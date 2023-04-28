<?php

namespace Traveler\Security;
function _cleaninjections($test) {
    $pattern = '/[^a-zA-Z0-9\s.,!?;:\-()@]+_/';
    $replacement = '';
    return preg_replace($pattern, $replacement, $test);
}
