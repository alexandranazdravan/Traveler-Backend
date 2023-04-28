<?php

namespace Traveler\UserProfile;


class UserProfile {
    public function checkRequest($conn) {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->getUserInfoByCookie($_COOKIE['loggedin'], $conn);
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
                $username = $data['username'];
                $email = $data['email'];
                $fullname = $data['fullname'];
                $avatar = $data['avatar'];
                $query = "update `users` set user_name='$username', user_email='$email', user_fullname='$fullname', user_avatar='$avatar' where user_id = '$id';";
                $conn->query($query);
            }
        }
    }
    public function getUserInfoByCookie(string $cookie, $conn) {
        $query = mysqli_query($conn, "select user_name from `auth_details` where cookie='$cookie'");
        if(mysqli_num_rows($query) == 0) {
            $error_message = array('error' => 'User not logged in');
            http_response_code(400);
            echo json_encode($error_message);
            exit();
        }

        $row = mysqli_fetch_array($query);
        $username = $row['user_name'];
        $query = mysqli_query($conn, "select * from `users` where user_name='$username'");
        $row = mysqli_fetch_array($query);
        $data = array('user_id' => $row['user_id'], 'username' => $username, 'password' => $row['user_pass'], 'email' => $row['user_email'], 'fullname' => $row['user_fullname'], 'avatar' => $row['user_avatar']);
        echo json_encode($data);
        return $data;
    }
}
