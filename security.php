<?php

namespace Traveler\Security;
function _cleaninjections($test) {
    $pattern = '/[^a-zA-Z0-9\s.,!?;:\-()@]+/';
    $replacement = '';
    return preg_replace($pattern, $replacement, $test);
}
