<?php
require_once('../vendor/autoload.php');
require_once('db_config_example.php');

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;

// Setup DB and Settinfs into invoice object
Invoice::db($db_handler, $table_prefix);
Invoice::config(Settings::fromFile('/var/www/storage/settings.json'));

// For example you neet to pay in btc 25000 satoshi
$invoice = Invoice::init('btc', 25000);

// Set invoice lifetime 300s
$invoice->lifetime(300);

// Set callback URL
$invoice->callbackUrl('https://yourhost.com/invoice_callback.php');

// Create invoice
$invoice->create();

// Print invoce info
print_r($invoice->details->toJson());
