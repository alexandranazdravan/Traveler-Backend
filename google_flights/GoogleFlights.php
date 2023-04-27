<?php

namespace Traveler\GoogleFlights;

use DateTime;

/**
 * More details can be found here: https://rapidapi.com/Travelpayouts/api/flight-data
 */
/** full possible req:
 * https://travelpayouts-travelpayouts-flight-data-v1.p.rapidapi.com/v1/prices/direct/?destination=LED&origin=MOW"
 * https://api.travelpayouts.com/v1/prices/cheap?origin=MOW&destination=HKT&depart_date=2023-02&return_date=2023-03&currency=EUR"
 * https://travelpayouts-travelpayouts-flight-data-v1.p.rapidapi.com/data/en-GB/airlines.json"
 */

/** response:
 * {
 * "success": true,
 * "data": {
 * "LED": {
 * "0": {
 * "price": 3390,
 * "airline": "UT",
 * "flight_number": 381,
 * "departure_at": "2023-03-13T19:10:00+03:00",
 * "return_at": "2023-03-22T21:30:00+03:00",
 * "expires_at": "2023-03-06T21:15:35Z"
 * }
 * }
 * },
 * "currency": "rub"
 * }
 * sau, cand nu se gaseste:
 * {
 *      "success":true,
 *      "data": {
 *      },
 *      "currency":"EUR"
 * }
 */
class GoogleFlights
{
    private string $request_url = "https://travelpayouts-travelpayouts-flight-data-v1.p.rapidapi.com/";
    private string $endpoint = 'v1/prices/direct';

    /**
     * @param string $endpoint
     */
    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function createRequest(array $request_options): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->request_url . $this->endpoint . $this->assemblyOptions($request_options),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-Access-Token: 05f40c220e8193fc8297804b069de4d8",
                "X-RapidAPI-Host: travelpayouts-travelpayouts-flight-data-v1.p.rapidapi.com",
                "X-RapidAPI-Key: 54db60a185mshe7972e9ad846b31p1ee7b6jsnc676d9a1406e"
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    public function analyzeResponse(array $response, string $currency = '', string $destination = '', $origin = ''): array
    {
        $iata = new IATA();
        if (in_array($this->endpoint, $this->with_iata_endpoints)) {
            return $this->processIATAEndpoint($response, $iata, $destination, $origin, $currency);
        } else if (in_array($this->endpoint, $this->with_date_endpoints)) {
            return $this->processDateEndpoint($response, $currency, $origin, $destination);
        } else {
            return $this->processNoneEndpoint($response, $iata);
        }
    }

    private function assemblyOptions(array $request_options): string
    {
        $iata = new IATA();
        $options = "?";

        foreach ($request_options as $key => $value) {
            if (in_array($key, $this->search_options)) {
                $opt = $iata->searchByCityLocation($value);
                if ($opt != '') {
                    $options = $options . $key . '=' . $iata->searchByCityLocation($value) . '&';
                }
                else {
                    $error_message = array('error' => 'There is no city with such name');
                    http_response_code(400);
                    echo json_encode($error_message);
                    exit();
                }
            } else  if (in_array($key, $this->date_options)) {
                $departure = explode('T', $value);
                $departure_day = $departure[0];
                $options = $options . $key . '=' . $departure_day . '&';
            } else {
                $options = $options . $key . '=' . $value . '&';
            }
        }
        return substr($options, 0, -1);
    }

    private function processIATAEndpoint(array $response, IATA $iata, string $destination, string $origin, string $currency): array
    {
        $returned_details = array();
        $index = 0;
        if($destination !== '') {
            $flights_details = $response['data'][$iata->searchByCityLocation($destination)];

            foreach ($flights_details as $flight) {
                $departure = explode('T', $flight['departure_at']);
                $departure_time = explode("+", $departure[1])[0];
                $date = new DateTime($departure[0]);
                $departure_day = $date->format('d-m-Y');

                $return = explode('T', $flight['return_at']);
                $return_time = explode("+", $return[1])[0];
                $date = new DateTime($return[0]);
                $return_day = $date->format('d-m-Y');

                $airline = new Airline();
                $airline_details = $airline->searchByCode($flight['airline']);

                $returned_details[$index] = array(
                    'origin' => $origin,
                    'destination' => $destination,
                    'airline' => $airline_details != null ? $airline_details[0] : null,
                    'is_lowcost' => $airline_details != null ? ($airline_details[1] ? 'Yes' : 'No') : 'NA',
                    'price' => $flight['price'] . ' ' . strtoupper($currency),
                    'flight_number' => $flight['flight_number'],
                    'departure_at' => $departure_day,
                    'departure_time' => $departure_time,
                    'return_at' => $return_day,
                    'return_time' => $return_time,
                    'is_favourite' => false
                );
                $index++;
            }
        } else {
            foreach ($response['data'] as $key => $flight) {
                $departure = explode('T', $flight['departure_at']);
                $departure_time = explode("+", $departure[1])[0];
                $date = new DateTime($departure[0]);
                $departure_day = $date->format('d-m-Y');

                $return = explode('T', $flight['return_at']);
                $return_time = explode("+", $return[1])[0];
                $date = new DateTime($return[0]);
                $return_day = $date->format('d-m-Y');

                $airline = new Airline();
                $airline_details = $airline->searchByCode($flight['airline']);

                $returned_details[$index] = array(
                    'origin' => $iata->searchByIATACityCode($flight['origin']),
                    'destination' => $iata->searchByIATACityCode($key),
                    'airline' => $airline_details != null ? $airline_details[0] : null,
                    'is_lowcost' => $airline_details != null ? ($airline_details[1] ? 'Yes' : 'No') : 'NA',
                    'price' => $flight['price'] . ' ' . strtoupper($currency),
                    'flight_number' => $flight['flight_number'],
                    'departure_at' => $departure_day,
                    'departure_time' => $departure_time,
                    'return_at' => $return_day,
                    'return_time' => $return_time,
                    'is_favourite' => false
                );
                $index++;
            }
        }
        return $returned_details;
    }

    private function processDateEndpoint(array $response, string $currency, string $origin, string $destination): array
    {
        $returned_details = array();
        $index = 0;
        foreach ($response['data'] as $flight) {
            $returned_details[$index] = array(
                'origin' => $origin,
                'destination' => $destination,
                'class' => ($flight['trip_class'] === 0) ? 'Economy Class' : (($flight['trip_class'] === 1) ? 'Business Class' : 'First Class'),
                'price' => $flight['value'] . ' ' . strtoupper($currency),
                'no_of_changes' => $flight['number_of_changes'],
                'duration' => $flight['duration'] .  ' min',
                'distance' => $flight['distance'] .  'km',
                'departure_at' => $flight['depart_date'],
                'return_at' => $flight['return_date'],
                'is_favourite' => false
            );
            $index++;
        }
        return $returned_details;
    }

    private function processNoneEndpoint(array $response, IATA $iata): array
    {
        $returned_details = array();
        $index = 0;
        foreach ($response['data'] as $key => $value) {
            $splitted_key = explode('-', $key);
            $origin = $splitted_key[0];
            $destination = $splitted_key[1];

            $returned_details[$index] = array(
                'Departure City' => $iata->searchByIATACityCode($origin),
                'Destination City' => $iata->searchByIATACityCode($destination),
                'Popularity' => $value
            );
            $index++;
        }
        return $returned_details;
    }

    private array $search_options = array('destination', 'origin');
    private array $date_options = array('return_date', 'depart_date');
    private array $with_iata_endpoints = array('v1/prices/cheap', 'v1/prices/direct', 'v1/city-directions');
    private array $with_date_endpoints = array('v2/prices/month-matrix');
}