<?php

namespace Traveler\Admin;

use Traveler\MySQL\Database;
require_once dirname(__DIR__) . '/MySQL/Database.php';


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$data = json_decode(file_get_contents('php://input'), true);

if (isset($_COOKIE['loggedin'])) {
    $cookie = $_COOKIE['loggedin'];
} else {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['cookie'])) {
        $cookie = $data['cookie'];
    } else {
        echo json_encode(array('isAdmin' => false));
        exit();
    }
}

$database = new Database();
$conn = $database->getConn();
$query = mysqli_query($conn, "select * from `auth_details` where user_name='admin'");

if (mysqli_num_rows($query) == 0) {
    echo json_encode(array('isAdmin' => false));
    exit();
} else {
    $row = mysqli_fetch_array($query);
    $admin_cookie = $row['cookie'];
    if ($admin_cookie == $cookie) {
        echo json_encode(array('isAdmin' => true));

    } else {
        echo json_encode(array('isAdmin' => false));
        exit();
    }
}

if (isset($uri[3])) {
    if ($uri[3] == 'read') {
        require "AdminGetUsers.php";
        $login = new AdminGetUsers($conn);
    } else if ($uri[3] == 'delete') {
        $id = $data['user_id'];
        $query = mysqli_query($conn, "select * from `users` where user_id='$id'");
        $row = mysqli_fetch_array($query);
        $user_name = $row['user_name'];
        $query = "delete from `auth_details` where `user_name`='$user_name';";
        $conn->query($query);
        $query = "delete from `users` where `user_id`='$id';";
        $conn->query($query);
    } else if ($uri[3] == 'create') {
        $user_name = $data['user_name'];
        $user_fullname = isset($data['user_fullname']) ? $data['user_fullname'] : '';
        $user_email = $data['user_email'];
        $pass = $data['user_pass'];
        $user_pass = password_hash($pass, PASSWORD_DEFAULT);
        $query = "insert into `users` (user_name, user_email, user_fullname, user_pass) values ('$user_name', '$user_email', '$user_fullname', '$user_pass');";
        $conn->query($query);
    } else if ($uri[3] == 'update') {
        $user_name = $data['user_name'];
        $user_fullname = $data['user_fullname'];
        $user_email = $data['user_email'];
        $user_id = $data['user_id'];
        $query = "update `users` set user_name='$user_name', user_email='$user_email', user_fullname='$user_fullname' where user_id = '$user_id';";
        $conn->query($query);
    }
}