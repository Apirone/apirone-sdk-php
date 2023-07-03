<?php

require_once('common.php');
require_once('../db.php');

use Apirone\SDK\Invoice;
use Apirone\SDK\Service\Utils;
use Apirone\SDK\Model\Settings;

// Config & DB
Invoice::db($db_handler, $table_prefix);
Invoice::settings( Settings::fromFile('/var/www/storage/settings.json') );

$params = json_decode($_GET['data']);

$invoice = Invoice::init($params->currency, $params->amount);

if ($params->lifetime) {
    $invoice->lifetime($params->lifetime);
}

if ($params->callbackUrl) {
    $invoice->callbackUrl($params->callbackUrl);
}

$invoice->create();

Utils::send_json($invoice->details->toJson());

