<?php

namespace Traveler\Main;
use Traveler\RequestController;
require_once "Controller/RequestController.php";


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
new RequestController\RequestController($_SERVER['REQUEST_METHOD'],$uri[2]);

