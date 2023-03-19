<?php
namespace Traveler\Register;

use Exception;
use PHPMailer\PHPMailer\OAuth;
use Traveler\MySQL\Database;
use function Traveler\Security\_cleaninjections;
use function Traveler\Security\generate_csrf_token;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerExcp;
use League\OAuth2\Client\Provider\Google;

date_default_timezone_set('Etc/UTC');

require 'vendor/autoload.php';
require_once dirname(__DIR__) . '/security.php';
require_once dirname(__DIR__) . '/MySQL/Database.php';

class RegisterUser {

    private string $username_regex = '/^[a-zA-Z0-9_.]+$/';
    private string $email_regex = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,}$/';

    public function __construct() {
        foreach($_POST as $key => $value){
            $_POST[$key] = _cleaninjections(trim($value));
        }

        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $repeat_password = $_POST['repeatpassword'];
        $full_name = $_POST['fullname'];

        if($username == null || $email == null || $password == null || $repeat_password == null) {
            $_SESSION['ERRORS']['register_params_error'] = 'Required fields cannot be empty!';
            header("Location: ../Traveler");
            exit();
        }

        if(!preg_match($this->username_regex, $username)) {
            $_SESSION['ERRORS']['register_username_error'] = 'Invalid username.
                                Accepted characters are alphanumeric, dot and underscore';
            header("Location: ../Traveler");
            exit();
        }

        if(!preg_match($this->email_regex, $email)) {
            $_SESSION['ERRORS']['register_email_error'] = 'Invalid email format';
            header("Location: ../Traveler");
            exit();
        }

        if(strcmp($password, $repeat_password) != 0) {
            $_SESSION['ERRORS']['register_pass_error'] = 'Passwords do not match';
            header("Location: ../Traveler");
            exit();
        }

        if($this->sendEmail($email, $full_name)) {
            if (!isset($_SESSION)) {
                session_start();
            }
            generate_csrf_token();

            $database = new Database();
            $conn = $database->getConn();
            $this->checkAccountAvailability($username, $email, $conn);

            $hash_pass = password_hash($password, PASSWORD_DEFAULT);
            if($full_name == null) {
                $insert_query = "insert into `users`(user_name, user_pass, user_email) 
                                values('$username','$hash_pass', '$email')";
            } else {
                $insert_query = "insert into `users`(user_name, user_pass, user_email, user_fullname) 
                                values('$username','$hash_pass', '$email', '$full_name')";
            }
            $conn->query($insert_query);
            $query =  mysqli_query($conn, "select * from `users` where user_name='$username'");
            $row = mysqli_fetch_array($query);
            $_SESSION['auth'] = 'loggedin';
            $_SESSION['id'] = $row['user_id'];
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $row['user_email'];
            $_SESSION['full_name'] = $row['user_fullname'];
            try {
                $selector = bin2hex(random_bytes(8));
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
            try {
                $token = random_bytes(32);
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }

            setcookie('loggedin', $selector . ':' . bin2hex($token), time() + 864000,
                '/', NULL, false, true);

            $token = password_hash($token, PASSWORD_DEFAULT);
            $query = "INSERT INTO `auth_details` (user_name, selector, token, expires_at) 
                            VALUES ('$username', '$selector', '$token',  '" . date('Y-m-d\TH:i:s', time() + 864000) . "');";
            $conn->query($query);
        }
        $gdfgdfdf = 9;
    }

    private function checkAccountAvailability(string $username, string $email, $conn): bool {
        $query = mysqli_query($conn, "select * from `users` where user_name='$username'");

        if(mysqli_num_rows($query) != 0) {
            $_SESSION['ERRORS']['register_unavailable_username_error'] = 'This username is already in use';
            header("Location: ../Traveler");
            exit();
        }

        $query = mysqli_query($conn, "select * from `users` where user_email='$email'");
        if(mysqli_num_rows($query) != 0) {
            $_SESSION['ERRORS']['register_unavailable_email_error'] = 'This email is already in use';
            header("Location: ../Traveler");
            exit();
        }
        return true;
    }

    /**
     * Template: https://github.com/PHPMailer/PHPMailer/blob/master/examples/gmail_xoauth.phps
     * @param mixed $email
     * @param mixed $full_name
     * @return void
     * @throws MailerExcp
     */
    private function sendEmail(mixed $email, mixed $full_name): bool {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;

        $mail->AuthType = 'XOAUTH2';
        $admin_email = 'phpmail2023@gmail.com';
        $clientId = '303867893610-psebe5317b8pvso46em4sfusai7pld13.apps.googleusercontent.com';
        $clientSecret = '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';
        $refreshToken = '1//09jDtiQPd2xDVCgYIARAAGAkSNwF-L9IrNsxz_egZksDZzelbBuqBQ6zYYUV72Xz5FPfhfUVTqGbWNqNGHKL3s1fbkwrqsfOrvSg';

        $provider = new Google(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
            ]
        );

        $mail->setOAuth(
            new OAuth(
                [
                    'provider' => $provider,
                    'clientId' => $clientId,
                    'clientSecret' => $clientSecret,
                    'refreshToken' => $refreshToken,
                    'userName' => $admin_email,
                ]
            )
        );

        try {
            $mail->setFrom($admin_email, 'First Last');
        } catch (MailerExcp $e) {
        }
        try {
            $mail->addAddress($email);
        } catch (MailerExcp $e) {
        }
        $mail->Subject = 'Traveler Registration';

        $message = 'Dear ' . $full_name . ',<br>';
        $message .= 'We are pleased to inform you that your registration to our app has been successful.<br>';
        $message .= 'Thank you for choosing our app!<br>';
        $message .= 'Please do not hesitate to contact us at ' . $admin_email . ' if you have any questions or feedback. We would be happy to assist you in any way we can.<br><br>';
        $message .= 'Best regards,<br>';
        $message .= 'The Traveler Team';
        $mail->Body = $message;

        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        } else {
            echo 'Message sent!';
            return true;
        }
    }
}