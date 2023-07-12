<?php

require_once('common.php');
require_once('../db.php');

use Apirone\SDK\Invoice;
use Apirone\SDK\Service\Utils;
use Apirone\SDK\Model\Settings;
use Apirone\SDK\Model\UserData;

// Config & DB
Invoice::db($db_handler, $table_prefix);
Invoice::settings( Settings::fromFile('/var/www/storage/settings.json') );

$invoiceJson = json_decode(Utils::sanitize($_GET['data']));
$userDataJson = null;

if(isset($_GET['userData'])) {
    $userDataJson = json_decode(Utils::sanitize($_GET['userData']));
}
$invoice = Invoice::init($invoiceJson->currency, $invoiceJson->amount);

if ($invoiceJson->lifetime) {
    $invoice->lifetime($invoiceJson->lifetime);
}

if ($invoiceJson->expire) {
    $invoice->expire($invoiceJson->expire);
}

if ($invoiceJson->callbackUrl) {
    $invoice->callbackUrl($invoiceJson->callbackUrl);
}

if ($invoiceJson->linkback) {
    $invoice->linkback($invoiceJson->linkback);
}

if ($userDataJson) {
    $invoice->userData(UserData::fromJson($userDataJson));
}
try {
    $invoice->create();
}
catch (Exception $e) {
    echo $e->getMessage();
    exit;    
}

Utils::send_json($invoice->details->toJson());

