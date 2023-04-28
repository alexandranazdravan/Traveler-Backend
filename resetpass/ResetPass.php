<?php

namespace Traveler\ForgotPass;
use League\OAuth2\Client\Provider\Google;
use PHPMailer\PHPMailer\Exception as MailerExcp;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use Traveler\MySQL\Database;
use function Traveler\Security\_cleaninjections;

require 'vendor/autoload.php';
require_once dirname(__DIR__) . '/security.php';
require_once dirname(__DIR__) . '/MySQL/Database.php';

class ResetPass {
    public function  __construct() {
        $data = json_decode(file_get_contents('php://input'), true);
        foreach ($data as $key => $value) {
            $data[$key] = _cleaninjections(trim($value));
        }

        $username = $data['username'];
        $email = $data['email'];

        $database = new Database();
        $conn = $database->getConn();
        $query = mysqli_query($conn, "select * from `users` where user_name='$username' and user_email='$email';");

        if (mysqli_num_rows($query) == 0) {
            http_response_code(404);
            exit();
        }

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
        $mail->Subject = 'Traveler - Reset Password';

        $password = $this->getRandomPass(10);
        $message = 'Your new password is: ' . $password;
        $message .= '<br >If you encounter any issues with your new password, do not hesitate to contact us at ' . $admin_email . '.';
        $message .= '<br> <br>';
        $message .= 'The Traveler Team';
        $mail->Body = $message;

        if (!$mail->send()) {
            return false;
        } else {
            $hash_pass = password_hash($password, PASSWORD_DEFAULT);
            $update_query = "update `users` set user_pass='$hash_pass' where user_name='$username';";
            $conn->query($update_query);
            return true;
        }
    }
    private function getRandomPass($n) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!&%^(){}$#@';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}
