<?php

require_once('../vendor/autoload.php');
require_once('db_adapter.php');
require_once('log.php');

use Apirone\SDK\Invoice;

/**
 * callbackChecker
 */
$callbackChecker = static function (Invoice $invoice) {
    // your input validation/processing logic
};

/**
 * paymentProcessing
 */
$paymentProcessing = static function (Invoice $invoice) {
    // Your payment processing logic
};

Invoice::callbackHandler($paymentProcessing, $callbackChecker);
