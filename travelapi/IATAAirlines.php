<?php

namespace Traveler\TravelApi;
class IATAAirlines {
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
        return $this->airline_codes[$code] ?? null;
    }

    /**
     * @return array
     */
    public function getAirlineCodes(): array
    {
        return $this->airline_codes;
    }
}