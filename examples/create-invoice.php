<?php

require_once('../vendor/autoload.php');
require_once('db_adapter.php');
require_once('log.php');

use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

// Get settings
$settings = Settings::fromFile('/var/www/storage/settings.json');

// For example you need to pay in btc 2500 satoshi
$invoice = Invoice::init($settings->account, 'btc')->amount(2500);

// Set invoice lifetime 300s (5 min)
$invoice->lifetime(300);

// Set callback URL
$invoice->callbackUrl('https://yourhost.com/invoice_callback.php');

// Create invoice
$invoice->create();

// Print invoice info
print_r($invoice->details->toJson());
