<?php
namespace Traveler\Register;

use \Traveler\Register;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(404);
    exit();
}

require "RegisterUser.php";
$register = new Register\RegisterUser();