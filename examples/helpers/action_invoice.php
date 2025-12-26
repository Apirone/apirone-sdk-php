<?php

require_once('common.php');
require_once('../db_adapter.php');
require_once('../log.php');

use Apirone\SDK\Invoice;
use Apirone\SDK\Service\Db;
use Apirone\SDK\Service\Utils;
use Apirone\SDK\Model\Settings;
use Apirone\SDK\Model\UserData;

// Config
$settings = Settings::fromFile('/var/www/storage/settings.json');

$invoiceJson = json_decode(Utils::sanitize($_POST['data']));
$userDataJson = null;

if(isset($_POST['userData'])) {
    $userDataJson = json_decode(Utils::sanitize($_POST['userData']));
}
$invoice = Invoice::init($settings->account, $invoiceJson->currency);

if ($invoiceJson->amount) {
    $invoice->amount($invoiceJson->amount);
}

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

Utils::sendJson($invoice->details->toJson());
