<?php

namespace Traveler\ContactUs;
use League\OAuth2\Client\Provider\Google;
use PHPMailer\PHPMailer\Exception as MailerExcp;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';
class ContactUs {

    public function __construct() {
        $data = json_decode(file_get_contents('php://input'), true);

        $from = $data['from'];
        $subject = $data['subject'];
        $message = $data['body'];

        $subject .= '        ';
        $subject .= "From: '$from'";

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
            $mail->addAddress('phpmail2023@gmail.com');
        } catch (MailerExcp $e) {
        }
        $mail->Subject = $subject;
        $mail->Body = $message;

        if (!$mail->send()) {
            return false;
        } else {
            return true;
        }


    }
}
