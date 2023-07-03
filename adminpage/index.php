<?php

namespace Traveler\Admin;

use Traveler\MariaDB\Database;
require_once dirname(__DIR__) . '/MariaDB/Database.php';
require "AdminPage.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$data = json_decode(file_get_contents('php://input'), true);

$database = new Database();
$conn = $database->getConn();

$admin = new AdminPage();
$admin->setParams($uri, $data);
$admin->checkIfAdmin($conn);
$admin->checkRequest($conn);

