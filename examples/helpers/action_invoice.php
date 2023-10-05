<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('common.php');
require_once('../db.php');
require_once('../log.php');

use Apirone\SDK\Invoice;
use Apirone\SDK\Service\Utils;
use Apirone\SDK\Model\Settings;
use Apirone\SDK\Model\UserData;

// Config & DB
Invoice::db($db_handler, $table_prefix);
Invoice::settings(Settings::fromFile('/var/www/storage/settings.json'));

$invoiceJson = json_decode(Utils::sanitize($_POST['data']));
$userDataJson = null;

if(isset($_POST['userData'])) {
    $userDataJson = json_decode(Utils::sanitize($_POST['userData']));
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
