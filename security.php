<?php

namespace Traveler\Security;
function _cleaninjections($test) {
    $pattern = '/[^a-zA-Z0-9\s.,!?;:\-()]+/';
    $replacement = '';
    return preg_replace($pattern, $replacement, $test);
}

function generate_csrf_token() {
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
}
