<?php

namespace Traveler\ContactUs;
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(404);
    exit();
}

require "ContactUs.php";
new ContactUs();


