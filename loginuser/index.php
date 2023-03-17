<?php

namespace Traveler\Login;
//require "../autoload.php";
//autoload(__DIR__);


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(404);
    echo http_response_code();
    exit();
}
require "LoginUser.php";
$login = new LoginUser();
