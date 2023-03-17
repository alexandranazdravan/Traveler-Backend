<?php

namespace Traveler\Main;
use Traveler\GoogleFlights;
use Traveler\Login;
use Traveler\MySQL;
use Traveler\RequestController;
require_once "Controller/RequestController.php";

session_start();
session_unset();
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
new RequestController\RequestController($_SERVER['REQUEST_METHOD'],$uri[2]);
//header("Location: login");
//require "autoload.php";
//autoload(__DIR__);
//session_start();
//if (isset($_SESSION['auth'])) {
//
//    header("Location: ");
//    exit();
//}
//else {
//
////    header("Location: login");
//    require_once "loginuser/index.php";
////    autoload(__DIR__);
//    exit();
//}
//$database = new MySQL\Database();
//$conn = $database->getConn();

//switch ($uri[2]) {
//    case "login":
//        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
//            http_response_code(404);
//            echo http_response_code();
//        }
//        $login = new Login\LoginUser($conn);
//        break;
//    case "logout":
//}
$fsdfsd = 9;



/**
 * More details can be found here: https://rapidapi.com/Travelpayouts/api/flight-data
*/
/** full possible req:
 * https://travelpayouts-travelpayouts-flight-data-v1.p.rapidapi.com/v1/prices/direct/?destination=LED&origin=MOW"
 * https://api.travelpayouts.com/v1/prices/cheap?origin=MOW&destination=HKT&depart_date=2023-02&return_date=2023-03&currency=EUR"
 * https://travelpayouts-travelpayouts-flight-data-v1.p.rapidapi.com/data/en-GB/airlines.json"
 */

//pe astea le iau eu de undeva
//$currency = 'EUR';
//$destination = 'St. Petersburg';
//$origin = 'Moscow';
//$request_op = array(
//    "destination" => $destination,
//    "origin" => $origin,
//    "currency" => $currency,
//    "depart_date" => "2023-03",
//    //"return_date" => "2023-04"
//);
//$request_opp = array(
//    "airline_code" => 'SU',
//    "limit" => 10
//);
//$google_flights = new GoogleFlights\GoogleFlights();
////$response = $google_flights->createRequest($request_op);
////$google_flights->setEndpoint('prices/cheap');
////$google_flights->setEndpoint('prices/calendar');
//$google_flights->setEndpoint('airline-directions');
//$response = $google_flights->createRequest($request_opp);
//$flights_found = $google_flights->analyzeResponse($response, $currency, $destination);
//$gxfgdf =9;

/** response:
 * {
        "success": true,
        "data": {
            "LED": {
                "0": {
                    "price": 3390,
                    "airline": "UT",
                    "flight_number": 381,
                    "departure_at": "2023-03-13T19:10:00+03:00",
                    "return_at": "2023-03-22T21:30:00+03:00",
                    "expires_at": "2023-03-06T21:15:35Z"
                }
            }
        },
        "currency": "rub"
    }
 * sau, cand nu se gaseste:
 * {
 *      "success":true,
 *      "data": {
 *      },
 *      "currency":"EUR"
 * }
*/
