<?php

require_once('../vendor/autoload.php');
require_once('db_adapter.php');
require_once('log.php');

use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

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

// Setup settings to invoice class
Invoice::settings(Settings::fromFile('/var/www/storage/settings.json'));

Invoice::callbackHandler($paymentProcessing, $callbackChecker);
