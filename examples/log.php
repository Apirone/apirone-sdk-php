<?php

require_once('helpers/common.php');

use Apirone\SDK\Invoice;

$loggerCallback = static function ($level, $message, $context) {
    // if ($level = \Apirone\API\Log\LogLevel::INFO) {
    //     return;
    // }
    $log_file = '/var/www/storage/log.txt';
    $data = [$level, $message, $context];
    file_put_contents($log_file, print_r($data, true) . "\r\n", FILE_APPEND);
};

Invoice::setLogger($loggerCallback);
