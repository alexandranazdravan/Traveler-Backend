<?php

namespace Traveler\Login;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(404);
    echo http_response_code();
    exit();
}

require "Login.php";
new Login();


