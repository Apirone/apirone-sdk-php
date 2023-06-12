<?php

if (!function_exists('pa')) {
    require_once('pa.php');
}

// Log handler example
$log_handler = static function($message) {
    pa($message);
};
