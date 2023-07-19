<?php
require_once('../vendor/autoload.php');
require_once('db.php');
require_once('log.php');

use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

// Setup DB and Settings into invoice object
Invoice::db($db_handler, $table_prefix);
Invoice::settings(Settings::fromFile('/var/www/storage/settings.json'));

// For example you need to pay in btc 25000 satoshi
$invoice = Invoice::init('btc', 25000);

// Set invoice lifetime 300s
$invoice->lifetime(300);

// Set callback URL
$invoice->callbackUrl('https://yourhost.com/invoice_callback.php');

// Create invoice
$invoice->create();

// Print invoice info
print_r($invoice->details->toJson());
