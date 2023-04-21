<?php
namespace Traveler\GoogleFlights;

require "IATA.php";
require "Airline.php";
require "GoogleFlights.php";

$gfdgdf = 9;
if (isset($_GET['item'])) {
    if ($_GET['item'] == 'airline') {
        $airline = new Airline();
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 10;
        $startIndex = ($page - 1) * $pageSize;
//        $endIndex = $startIndex + $pageSize;
        $lastCode = isset($_GET['lastCode']) ? $_GET['lastCode'] : null;
        if ($lastCode !== null) {
            $lastIndex = array_search($lastCode, array_keys($airline->getAirlineCodes()));
            $startIndex = $lastIndex + 1;
        }
        $subset = array_slice($airline->getAirlineCodes(), $startIndex, $pageSize, true);
        $return_array = array();
        foreach ($subset as $code => $details) {
            $return_array[] = array(
                'code' => $code,
                'name' => $details[0]
            );
        }
        $gfdgdf = 9;
        header('Content-Type: application/json');
        echo json_encode($return_array);

    }
}
else {
    $data = json_decode(file_get_contents('php://input'), true);
    $google_flights = new GoogleFlights();
    $google_flights->setEndpoint($data['uri']);
    if($data['uri'] == 'city-directions') {
        $request_options = array("origin" => $data['origin'], "currency" => $data['currency']);
        $response =  $google_flights->createRequest($request_options);
        $edited_response = $google_flights->analyzeResponse($response, $data['currency']);
        echo json_encode($edited_response);
    } else if($data['uri'] == 'airline-directions') {
        array_shift($data);
        $response = $google_flights->createRequest($data);
        $edited_response = $google_flights->analyzeResponse($response);
       echo json_encode($edited_response);
    } else if($data['uri'] == 'prices/direct' || $data['uri'] == 'prices/cheap') {
        array_shift($data);
        $data = array_filter($data, function($value) {
            return $value !== null;
        });
        $response = $google_flights->createRequest($data);
        if(isset($response['error'])) {
            $error_message = array('error' => $response['error']);
            http_response_code(400);
            echo json_encode($error_message);
            exit();
        }
        if($response['data'] == array()) {
            $error_message = array('error' => 'Sorry, there are no flights at the moment');
            http_response_code(400);
            echo json_encode($error_message);
            exit();
        }
        $edited_response = $google_flights->analyzeResponse($response, $data['currency'], $data['destination'], $data['origin']);
        echo json_encode($edited_response);
        $gdfg =1;
    }
}
