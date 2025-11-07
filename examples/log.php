<?php

require_once('helpers/common.php');

use Apirone\SDK\Service\Logger;

$loggerCallback = static function ($level, $message, $context) {
    // No debug level logs
    if (strtoupper($level) == 'DEBUG') {
        return;
    }
    $log_file = '/var/www/storage/log.txt';
    $dt = new \DateTime();
    $context = ($context) ? ' CONTEXT: ' . json_encode($context) : '';
    $logdata = sprintf('%s %s %s%s', $dt->format("Y-m-d\TH:i:sP"), strtoupper($level), $message, $context);
    file_put_contents($log_file, $logdata. "\r\n", FILE_APPEND);
};

Logger::set($loggerCallback);
