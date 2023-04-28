<?php
namespace Traveler\GoogleFlights;

use DateTime;
use Traveler\MySQL\Database;

require "IATA.php";
require "Airline.php";
require "GoogleFlights.php";
require_once dirname(__DIR__) . '/MySQL/Database.php';


$data = json_decode(file_get_contents('php://input'), true);
$database = new Database();
$conn = $database->getConn();
$google_flights = new GoogleFlights();
$google_flights->checkLoggedIn($conn, $data);
$google_flights->checkRequest($conn, $data);
