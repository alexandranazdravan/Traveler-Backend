<?php

namespace Traveler\Main;
use Traveler\RequestController;
require_once "Controller/RequestController.php";

//$xdebug_session = 'PHPSTORM'; // replace with your preferred xdebug session name
//$cookie_header = 'XDEBUG_SESSION=' . $xdebug_session;
//header($cookie_header);

//$headers = apache_request_headers();
//$cookies = explode('; ', $headers['Cookies']);
//
//foreach ($cookies as $cookie) {
//    list($name, $value) = explode('=', $cookie, 2);
//    $_COOKIE[$name] = $value;
//    setcookie($name, $value);
//}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
new RequestController\RequestController($_SERVER['REQUEST_METHOD'],$uri[2]);

