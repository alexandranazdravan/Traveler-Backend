<?php

namespace Traveler\UserProfile;


class UserProfile {
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

    public function getUserInfoById($id, $conn) {
        $query = mysqli_query($conn, "select * from `users` where user_id='$id';");
        $row = mysqli_fetch_array($query);
        $username = $row['user_name'];
        $query = mysqli_query($conn, "select * from `auth_details` where user_name='$username'");
        $row = mysqli_fetch_array($query);
        return $row['cookie'];
    }
}
