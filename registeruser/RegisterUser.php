<?php
namespace Traveler\Register;

use Exception;
use PHPMailer\PHPMailer\OAuth;
use Traveler\MariaDB\Database;
use function Traveler\Security\_cleaninjections;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerExcp;
use League\OAuth2\Client\Provider\Google;

date_default_timezone_set('Etc/UTC');

require 'vendor/autoload.php';
require_once dirname(__DIR__) . '/security.php';
require_once dirname(__DIR__) . '/MariaDB/Database.php';

class RegisterUser {

    public function __construct() {
        $data = json_decode(file_get_contents('php://input'), true);

        foreach ($data as $key => $value) {
            if ($key != 'password') {
                $data[$key] = _cleaninjections(trim($value));
            }
        }

        $username = $data['username'];
        $email = $data['email'];
        $password = $data['password'];
        $full_name = $data['fullname'];


        if($this->sendEmail($email, $full_name)) {

            $database = new Database();
            $conn = $database->getConn();
            $this->checkAccountAvailability($username, $email, $conn);

            $hash_pass = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "insert into `users`(user_name, user_pass, user_email, user_fullname) 
                            values('$username','$hash_pass', '$email', '$full_name')";
            $conn->query($insert_query);

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

            $value = $selector . ':' . bin2hex($token);
            setcookie('loggedin', $value, time() + 864000,
                '/', NULL, false, true);
            $_COOKIE['loggedin'] = $value;

            $query = "INSERT INTO `auth_details` (user_name, cookie, expires_at) 
                            VALUES ('$username', '$value', '" . date('Y-m-d\TH:i:s', time() + 864000) . "');";
            $conn->query($query);

            $response = array('loggedin' => $_COOKIE['loggedin'], 'role' => 'user');
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }

    private function checkAccountAvailability(string $username, string $email, $conn): bool {
        $query = mysqli_query($conn, "select * from `users` where user_name='$username'");

        if(mysqli_num_rows($query) != 0) {
            $error_message = array('error' => 'Username already in use');
            http_response_code(400);
            echo json_encode($error_message);
            exit();
        }

        $query = mysqli_query($conn, "select * from `users` where user_email='$email'");
        if(mysqli_num_rows($query) != 0) {
            $error_message = array('error' => 'Email already in use');
            http_response_code(400);
            echo json_encode($error_message);
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
            return false;
        } else {
            return true;
        }
    }
}