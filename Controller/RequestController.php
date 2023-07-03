<?php

namespace Traveler\RequestController;
class RequestController {
    public function __construct(string $method, string $uri)
    {
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
            if($uri == 'contact') {
                require_once "../Traveler/contactus/index.php";
            }
            if($uri == 'userprofile') {
                require_once "../Traveler/profile/index.php";
            }
            if($uri == 'dashboard') {
                require_once "../Traveler/travelapi/index.php";
            }
            if($uri == 'wishlist') {
                require_once "../Traveler/userwishlist/index.php";
            }
        }
        if($method == 'GET') {
            if($uri == 'admin') {
                require_once "../Traveler/adminpage/index.php";
            }
            if($uri == 'logout') {
                require_once "../Traveler/logoutuser/index.php";
            }
            if($uri == 'userprofile') {
                require_once "../Traveler/profile/index.php";
            }
            if($uri == 'dashboard') {
                require_once "../Traveler/travelapi/index.php";
            }
            if($uri == 'wishlist') {
                require_once "../Traveler/userwishlist/index.php";
            }
        }
        if($method == 'DELETE') {
            if($uri == 'wishlist') {
                require_once "../Traveler/userwishlist/index.php";
            }
        }
    }
}