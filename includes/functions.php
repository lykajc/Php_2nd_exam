<?php


function clean_input($data) {
    return trim($data);
}

function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}