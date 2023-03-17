<?php
namespace Traveler\Register;

use Exception;
use Traveler\MySQL\Database;
use function Traveler\Security\_cleaninjections;
use function Traveler\Security\generate_csrf_token;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerExcp;

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

        $php_mailer = new PHPMailer(true);

        try {
            $php_mailer->isSMTP();
        } catch (Exception $e) {


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
}