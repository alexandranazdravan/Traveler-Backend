<?php
namespace Traveler\IATA_Codes;
class IATA {

    private $city_codes_iata = array();
    private $airport_city_code = array();

    public function __construct() {
        $str = file_get_contents('city_codes.json');
        $json = json_decode($str, true);
        foreach ($json as $line) {
            $this->city_codes_iata[$line['code']] = $line['name'];
        }

        $str = file_get_contents('airports.json');
        $json = json_decode($str, true);
        foreach ($json as $line) {
            $this->airport_city_code[$line['code']] = $line['city_code'];
        }
    }

    public function searchByIATACityCode(string $iata_code): string {
        $return_city =  array_search($iata_code, array_flip($this->city_codes_iata));
        return ($return_city != "") ? $return_city : $this->findByAirportCode($iata_code);
    }

    public function searchByCityLocation(string $city): string {
        return array_search($city, $this->city_codes_iata);
    }

    private function findByAirportCode($iata_code) {
        return $this->city_codes_iata[$this->airport_city_code[$iata_code]];
    }
}