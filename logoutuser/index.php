<?php

use Traveler\MySQL\Database;

require_once dirname(__DIR__) . '/MySQL/Database.php';

if(isset($_COOKIE['loggedin'])) {
    $cookie = $_COOKIE['loggedin'];
    unset($_COOKIE['loggedin']);
}

$database = new Database();
$conn = $database->getConn();
$query = mysqli_query($conn, "select * from `auth_details`");
$rows = [];
while($row = mysqli_fetch_array($query)){
    if($row['user_name'] !== 'admin') {
        $rows[] = $row;
    }
}
$all_cookies = [];
foreach ($rows as $row) {
    $all_cookies[$row['user_name']] = explode(",", $row['cookie']);
}
foreach ($all_cookies as $username => $cookies) {
    if(in_array($cookie, $cookies)) {
        $all_cookies[$username] = array_diff($cookies, [$cookie]);
        break;
    } else {
        return;
    }
}
if(count($all_cookies[$username]) == 0) {
    $query = "delete from `auth_details` where cookie= '$cookie';";
} else {
    $new_cookie = implode(",", $all_cookies[$username]);
    $query = "UPDATE auth_details SET cookie='$new_cookie'
                            WHERE user_name = '$username';";
}
$conn->query($query);