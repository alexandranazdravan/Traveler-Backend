<?php
namespace Traveler\GoogleFlights;

use DateTime;

require "IATA.php";
require "Airline.php";
require "GoogleFlights.php";


if (isset($_GET['item'])) {
    if ($_GET['item'] == 'airline') {
        $airline = new Airline();
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 10;
        $startIndex = ($page - 1) * $pageSize;

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

        header('Content-Type: application/json');
        echo json_encode($return_array);

    }
}
else {
    $data = json_decode(file_get_contents('php://input'), true);
    $google_flights = new GoogleFlights();
    $google_flights->setEndpoint($data['uri']);
    if($data['uri'] == 'v1/city-directions') {
        $request_options = array("origin" => $data['origin'], "currency" => $data['currency']);
        $response =  $google_flights->createRequest($request_options);
        $edited_response = $google_flights->analyzeResponse($response, $data['currency']);
    } else if($data['uri'] == 'v1/airline-directions') {
        array_shift($data);
        $response = $google_flights->createRequest($data);
        $edited_response = $google_flights->analyzeResponse($response);
    } else if($data['uri'] == 'v1/prices/direct' || $data['uri'] == 'v1/prices/cheap') {
        array_shift($data);

        if(isset($data['depart_date'])) {
            $data = array_filter($data, function ($value) {
                return $value !== null;
            });


            $response_1 = $google_flights->createRequest($data);
            if (isset($response_1['error'])) {
                $error_message = array('error' => $response_1['error']);
                http_response_code(400);
                echo json_encode($error_message);
                exit();
            }
            if ($response_1['data'] == array()) {
                $error_message = array('error' => 'Sorry, there are no flights at the moment');
                http_response_code(400);
                echo json_encode($error_message);
                exit();
            }

            foreach ($data as $key => $value) {
                if ($key == 'depart_date' || $key == 'return_date') {
                    $date = new DateTime($value);
                    $data[$key] = $date->format('Y-m');
                }
            }

            $response_2 = $google_flights->createRequest($data);
            if (isset($response_2['error'])) {
                $error_message = array('error' => $response_2['error']);
                http_response_code(400);
                echo json_encode($error_message);
                exit();
            }
            if ($response_2['data'] == array()) {
                $error_message = array('error' => 'Sorry, there are no flights at the moment');
                http_response_code(400);
                echo json_encode($error_message);
                exit();
            }
            foreach ($response_2['data'] as $city => $value) {
                foreach ($value as $flight) {
                    array_push($response_1['data'][$city], $flight);
                }
            }
            $response = $response_1;
        } else {
            $now = new DateTime();
            $month = $now->format('m');
            $year = $now->format('Y');
            $now_date = $year . '-' . $month;
            $data["depart_date"] = $now_date;
            $response = $google_flights->createRequest($data);

            for ($i = 1; $i < 4; $i++) {
                $now->modify('+1 month');
                $month = $now->format('m');
                $year = $now->format('Y');
                $now_date = $year . '-' . $month;
                $data["depart_date"] = $now_date;
                $one_flight = $google_flights->createRequest($data);
                foreach ($one_flight['data'] as $city => $value) {
                    foreach ($value as $flight) {
                        array_push($response['data'][$city], $flight);
                    }
                }
            }
        }

        $edited_response = $google_flights->analyzeResponse($response, $data['currency'], $data['destination'], $data['origin']);
    } else if($data['uri'] == 'v2/prices/month-matrix') {
        array_shift($data);
        $response = $google_flights->createRequest($data);
        $edited_response = $google_flights->analyzeResponse($response, $data['currency'], $data['destination'], $data['origin']);
    }
    echo json_encode($edited_response);
}
