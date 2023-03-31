<?php

namespace Traveler\RequestController;
class RequestController {
    public function __construct(string $method, string $uri)
    {
        $gdfgddfgdf = 8;
        if ($method == 'POST') {
            if ($uri == 'login') {
                require_once "../Traveler/loginuseradmin/index.php";
            }
            if ($uri == 'register') {
                require_once "../Traveler/registeruser/index.php";
            }
            if($uri == 'forgotpass') {
                require_once "../Traveler/resetpass/index.php";
            }
            if($uri == 'admin') {
                require_once "../Traveler/adminpage/index.php";
            }
        }
        if($method == 'GET') {
            if($uri == 'admin') {
                require_once "../Traveler/adminpage/index.php";
            }
            if($uri == 'logout') {
                require_once "../Traveler/logoutuser/index.php";
            }
        }
    }
}