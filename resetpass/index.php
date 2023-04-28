<?php

namespace Traveler\ForgotPass;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(404);
    exit();
}

require "ResetPass.php";
new ResetPass();