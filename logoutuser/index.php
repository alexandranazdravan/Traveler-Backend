<?php

use Traveler\MySQL\Database;

session_start();
require_once dirname(__DIR__) . '/MySQL/Database.php';

$gdfgdff = session_name();
if(isset($_COOKIE[session_name()])){
//    setcookie(session_name(), '', time()-7000000, '/');
    unset($_COOKIE[session_name()]);
}

if(isset($_COOKIE['loggedin'])) {
    unset($_COOKIE['loggedin']);
}

$database = new Database();
$conn = $database->getConn();
$user_name = $_SESSION['username'];
$query = "delete from `auth_details` where user_email= '$user_name';";
//$conn->query($query);
session_unset();
session_destroy();
