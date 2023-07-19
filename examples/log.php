<?php

require_once('helpers/common.php');

use Apirone\SDK\Invoice;

$log_handler = static function($message) {
    $log_file ='/var/www/storage/log.txt';
    file_put_contents($log_file, $message . "\r\n", FILE_APPEND);
};

Invoice::log($log_handler, true);
