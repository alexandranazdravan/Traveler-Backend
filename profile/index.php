<?php
namespace Traveler\UserProfile;

use Traveler\MariaDB\Database;
require_once dirname(__DIR__) . '/MariaDB/Database.php';


$database = new Database();
$conn = $database->getConn();

require "UserProfile.php";
$user = new UserProfile();
$user->checkRequest($conn);


