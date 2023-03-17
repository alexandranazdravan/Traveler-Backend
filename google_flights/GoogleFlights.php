<?php

namespace Traveler\GoogleFlights;

use Traveler\Airline;
use Traveler\IATA_Codes;

class GoogleFlights
{
    private string $request_url = "https://travelpayouts-travelpayouts-flight-data-v1.p.rapidapi.com/v1/";
    private string $endpoint = 'prices/direct';

    /**
     * @param string $endpoint
     */
    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }
    public function createRequest(array $request_options): array {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->request_url . $this->endpoint. $this->assemblyOptions($request_options),
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
    public function analyzeResponse(array $response, string $currency, string $destination): array
    {
        $iata = new IATA_Codes\IATA();
        if ($response['data'] == array()) {
            print_r("Sorry, we could not find any flights which match your requirements.");
            return array();
        }
        if (in_array($this->endpoint,$this->with_iata_endpoints)) {
            return $this->processIATAEndpoint($response, $iata, $destination, $currency);
        } else if (in_array($this->endpoint,$this->with_date_endpoints)){
            return $this->processDateEndpoint($response, $currency);
        } else {
            return $this->processNoneEndpoint($response, $iata);
        }
    }

    private function assemblyOptions(array $request_options): string {
        $iata = new IATA_Codes\IATA();
        $options = "/?";

        foreach ($request_options as $key => $value) {
            if (in_array($key, $this->search_options)) {
                $options = $options . $key . '=' . $iata->searchByCityLocation($value) . '&';
            } else {
                $options = $options . $key . '=' . $value . '&';
            }
        }
        return substr($options, 0, -1);
    }

    private array $search_options = array('destination', 'origin');
    private array $with_iata_endpoints = array('prices/cheap', 'prices/direct', 'city-directions');
    private array $with_date_endpoints = array('prices/calendar', 'prices/monthly');

    private function processIATAEndpoint(array $response, IATA_Codes\IATA $iata, string $destination, string $currency): array {
        $flights_details = $response['data'][$iata->searchByCityLocation($destination)];
        $returned_details = array();
        $index = 0;

        foreach ($flights_details as $flight) {
            $departure = explode('T', $flight['departure_at']);
            $departure_time = explode("+", $departure[1])[0];
            $departure_day = $departure[0];

            $return = explode('T', $flight['return_at']);
            $return_time = explode("+", $return[1])[0];
            $return_day = $return[0];

            $airline = new Airline\Airline();
            $airline_details = $airline->searchByCode($flight['airline']);

            $returned_details[$index] = array(
                'Airline' => $airline_details[0],
                'Is it low cost?' => $airline_details[1] ? 'Yes' : 'No',
                'Price' => $flight['price'] . ' ' . strtoupper($currency),
                'Flight number' => $flight['flight_number'],
                'Departure day' => $departure_day,
                'Departure time' => $departure_time,
                'Return day' => $return_day,
                'Return time' => $return_time,
            );
            $index++;
        }
        return $returned_details;
    }

    private function processDateEndpoint(array $response, string $currency): array {
        $returned_details = array();
        $index = 0;
        foreach ($response['data'] as $flight) {
            $departure = explode('T', $flight['departure_at']);
            $departure_time = explode("+", $departure[1])[0];
            $departure_day = $departure[0];

            $return = explode('T', $flight['return_at']);
            $return_time = explode("+", $return[1])[0];
            $return_day = $return[0];

            $airline = new Airline\Airline();
            $airline_details = $airline->searchByCode($flight['airline']);

            $returned_details[$index] = array(
                'Airline' => $airline_details[0],
                'Is it low cost?' => $airline_details[1] ? 'Yes' : 'No',
                'Price' => $flight['price'] . ' ' . strtoupper($currency),
                'Flight number' => $flight['flight_number'],
                'Departure day' => $departure_day,
                'Departure time' => $departure_time,
                'Return day' => $return_day,
                'Return time' => $return_time,
                'Number of transfers' => $flight['transfers']
            );
            $index++;
        }
        return $returned_details;
    }

    private function processNoneEndpoint(array $response, IATA_Codes\IATA $iata): array {
        $returned_details = array();
        $index = 0;
        foreach ($response['data'] as $key => $value) {
            $splitted_key = explode('-',$key);
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
}