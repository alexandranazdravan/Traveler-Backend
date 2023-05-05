<?php

namespace Traveler\Login;

use Exception;
use Traveler\MySQL\Database;
use function Traveler\Security\_cleaninjections;
require_once dirname(__DIR__) . '/security.php';
require_once dirname(__DIR__) . '/MySQL/Database.php';

class LoginUser
{
    public function __construct()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        foreach ($data as $key => $value) {
            if ($key != 'password') {
                $data[$key] = _cleaninjections(trim($value));
            }
        }

        $username = $data['username'];
        $password = $data['password'];

        if (empty($username) || empty($password)) {
            header("Location: ../Traveler");
            exit();
        }

        $database = new Database();
        $conn = $database->getConn();
        $query = mysqli_query($conn, "select * from `users` where user_name='$username'");
        if (mysqli_num_rows($query) != 0) {
            $row = mysqli_fetch_array($query);
            $role = $row['user_role'];
            if (!password_verify($password, $row['user_pass'])) {
                $error_message = array('error' => 'Invalid credentials');
                http_response_code(400);
                echo json_encode($error_message);
                exit();
            } else {
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
                setcookie('loggedin',$value, time() + 864000, '/', NULL, false, true);
                $_COOKIE['loggedin'] = $value;

                $query =  mysqli_query($conn, "select * from `auth_details` where user_name='$username'");
                $no_of_logins = mysqli_num_rows($query);
                if (mysqli_num_rows($query) == 0) {
                    $query = "INSERT INTO auth_details (user_name, cookie, expires_at) 
                            VALUES ('$username',  '$value', '" . date('Y-m-d\TH:i:s', time() + 864000) . "');";
                } else {
                    $comma_value = ',' . $value;
                    $row = mysqli_fetch_array($query);
                    $gfdgdf = (explode(",",$row['cookie']));
                    $gdfgdf = count($gfdgdf);
                    if(count(explode(",",$row['cookie'])) < 3) {
                        $old_cookies = $row['cookie'];
                        $query = "UPDATE auth_details SET cookie=concat('$old_cookies','$comma_value'),
                            expires_at = '" . date('Y-m-d\TH:i:s', time() + 864000) . "'
                            WHERE user_name = '$username';";
                    } else {
                        $error_message = array('error' => 'You have logged on too many devices. Only 3 allowed!');
                        http_response_code(400);
                        echo json_encode($error_message);
                        exit();
                    }
                }
                $conn->query($query);
                $response = array('loggedin' => $_COOKIE['loggedin'], 'role' => $role);
                header('Content-Type: application/json');
                echo json_encode($response);
            }
        } else {
            http_response_code(400);
            exit();
        }
    }
}
