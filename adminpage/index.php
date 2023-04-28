<?php

namespace Traveler\Admin;

use Traveler\MySQL\Database;
require_once dirname(__DIR__) . '/MySQL/Database.php';
require "AdminGetUsers.php";


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$data = json_decode(file_get_contents('php://input'), true);

$database = new Database();
$conn = $database->getConn();

$admin = new AdminGetUsers();
$admin->setParams($uri, $data);
$admin->checkIfAdmin($conn);
$admin->checkRequest($conn);
