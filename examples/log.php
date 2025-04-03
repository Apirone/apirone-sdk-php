<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('helpers/common.php');

use Apirone\SDK\Invoice;

$loggerCallback = static function ($level, $message, $context) {
    $log_file = '/var/www/storage/log.txt';
    $dt = new \DateTime();
    $context = ($context) ? ' CONTEXT: ' . json_encode($context) : '';
    $logdata = sprintf('%s %s %s%s', $dt->format("Y-m-d\TH:i:sP"), strtoupper($level), $message, $context);
    file_put_contents($log_file, $logdata. "\r\n", FILE_APPEND);
};

Invoice::logger($loggerCallback);
