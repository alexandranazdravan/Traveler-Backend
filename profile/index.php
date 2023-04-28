<?php
namespace Traveler\UserProfile;

use Traveler\MySQL\Database;
require_once dirname(__DIR__) . '/MySQL/Database.php';


$database = new Database();
$conn = $database->getConn();

require "UserProfile.php";
$user = new UserProfile();
$user->checkRequest($conn);