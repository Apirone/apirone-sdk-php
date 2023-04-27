<?php

require_once('./config.php');

use Apirone\API\Endpoints\Account;
use Apirone\Invoice\Model\InvoiceDetails;
use Apirone\Invoice\Utils;
use Apirone\Invoice\Invoice;
use Apirone\Invoice\Db\InvoiceDb;
use Apirone\Invoice\Model\Settings;

Apirone\API\Http\ErrorDispatcher::setCallback($log_handler);

InvoiceDb::setCallback($db_handler);
$settings = Settings::fromFile(__DIR__ . 'invoice-config.json');
$settings->setExtra('extraParameter', "Extra paraveter value");

$settings->toFile('');
pa($settings->toJsonString(JSON_PRETTY_PRINT));

