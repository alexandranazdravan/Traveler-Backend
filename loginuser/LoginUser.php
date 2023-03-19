<?php

namespace Traveler\Login;

use Exception;
use Traveler\MySQL\Database;
use function Traveler\Security\_cleaninjections;
use function Traveler\Security\generate_csrf_token;
require_once dirname(__DIR__) . '/security.php';
require_once dirname(__DIR__) . '/MySQL/Database.php';


class LoginUser
{
    public function __construct()
    {
        //unset($_SESSION['token']);
        //unset($_SESSION['STATUS']);

        foreach ($_POST as $key => $value) {
            $_POST[$key] = _cleaninjections(trim($value));
        }

        $username = $_POST['username'];
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $_SESSION['STATUS']['login_status'] = 'Fields cannot be empty';
            header("Location: ../Traveler");
            exit();
        }

        $database = new Database();
        $conn = $database->getConn();
        $query = mysqli_query($conn, "select * from `users` where user_name='$username'");
        if (mysqli_num_rows($query) != 0) {
            $row = mysqli_fetch_array($query);

            if (!password_verify($password, $row['user_pass'])) {
                $_SESSION['ERRORS']['wrong_password'] = 'Wrong Password! Please, try again!';
                header("Location: ../Traveler");
                exit();
            } else {
                if (!isset($_SESSION)) {
                    session_start();
                }
                generate_csrf_token();
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
                $query =  mysqli_query($conn, "select * from `auth_details` where user_name='$username'");

                if (mysqli_num_rows($query) == 0) {
                    $query = "INSERT INTO auth_details (user_name, selector, token, expires_at) 
                            VALUES ('$username', '$selector', '$token',  '" . date('Y-m-d\TH:i:s', time() + 864000) . "');";
                } else if (mysqli_num_rows($query) == 1){
//                    $query = "UPDATE auth_details SET selector = '$selector', token = '$token',
//                            expires_at = '" . date('Y-m-d\TH:i:s', time() + 864000) . "'
//                            WHERE user_name = '$username';";
                    echo "User already logged in!";
                    return;
                }
                $conn->query($query);
            }
        } else {
            $_SESSION['ERRORS']['no_user_found'] = 'Username does not exist. Do you want to create an account?';
            header("Location: ../Traveler");
            //session_abort();
            exit();
        }
    }
}
