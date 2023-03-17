<?php
namespace Traveler\Register;

use \Traveler\Register;
//session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(404);
    echo http_response_code();
    exit();
}

require "RegisterUser.php";
$register = new Register\RegisterUser();