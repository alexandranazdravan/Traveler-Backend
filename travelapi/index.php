<?php
namespace Traveler\TravelApi;

use DateTime;
use Traveler\MariaDB\Database;

require "IATACitiesAirports.php";
require "IATAAirlines.php";
require "TravelApi.php";
require_once dirname(__DIR__) . '/MariaDB/Database.php';


$data = json_decode(file_get_contents('php://input'), true);
$database = new Database();
$conn = $database->getConn();
$travelapi = new TravelApi();
$travelapi->checkLoggedIn($conn, $data);
$travelapi->checkRequest($conn, $data);
