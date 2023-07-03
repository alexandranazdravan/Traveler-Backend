<?php

namespace Traveler\MariaDB;
class Database {
    private $user = 'root';
    private $pass = 'pass';
    private $db = 'traveler';

    private $conn;

    /**
     * @return mixed
     */
    public function getConn()
    {
        return $this->conn;
    }

    public function __construct() {
        $this->connectToDatabse();
    }

    private function connectToDatabse() {
        $this->conn = new \mysqli('localhost', $this->user, $this->pass, $this->db);
    }
}
