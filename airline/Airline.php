<?php

namespace Traveler\Airline;
class Airline {
    private array $airline_codes;

    public function __construct() {
        $str = file_get_contents('airlines.json');
        $json = json_decode($str, true);

        $this->airline_codes = array();
        foreach ($json as $item) {
            $this->airline_codes[$item['code']] = [$item['name'], $item['is_lowcost']];
        }
    }

    public function searchByCode(string $code) {
        return $this->airline_codes[$code];
    }
}