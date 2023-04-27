<?php
namespace Traveler\UserProfile;

use Traveler\MySQL\Database;
require_once dirname(__DIR__) . '/MySQL/Database.php';


$database = new Database();
$conn = $database->getConn();

require "UserProfile.php";
$user = new UserProfile();
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user->getUserInfoByCookie($_COOKIE['loggedin'], $conn);
} else {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['actualoldpassword'])) {
        if(!password_verify($data['oldpassword'], $data['actualoldpassword'])) {
            http_response_code(400);
        } else {
            $hash_pass = password_hash($data['newpassword'], PASSWORD_DEFAULT);
            $username = $data['username'];
            $query = "update `users` set user_pass='$hash_pass' where user_name = '$username';";
             $conn->query($query);
        }
    } else if (isset($data['id'])) {
        $id = $data['id'];
        $cookie = $user->getUserInfoById($id, $conn);
        $username = $data['username'];
        $email = $data['email'];
        $fullname = $data['fullname'];
        $avatar = $data['avatar'];
        $query = "update `users` set user_name='$username', user_email='$email', user_fullname='$fullname', user_avatar='$avatar' where user_id = '$id';";
        $conn->query($query);
        $query = "update `auth_details` set user_name='$username' where cookie = '$cookie';";
        $conn->query($query);
    }
}