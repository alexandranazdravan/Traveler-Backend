<?php

use Traveler\MySQL\Database;

require_once dirname(__DIR__) . '/MySQL/Database.php';

if(isset($_COOKIE['loggedin'])) {
    $cookie = $_COOKIE['loggedin'];
    unset($_COOKIE['loggedin']);
}

$database = new Database();
$conn = $database->getConn();
$query = "delete from `auth_details` where cookie= '$cookie';";
$conn->query($query);