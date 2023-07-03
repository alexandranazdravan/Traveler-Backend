<?php

namespace Traveler\Security;
function _cleaninjections($str) {
    $pattern = '/[^a-zA-Z0-9\s.<>=,!?;:\-()@]+_/';
    $replacement = '';
    return preg_replace($pattern, $replacement, $str);
}

