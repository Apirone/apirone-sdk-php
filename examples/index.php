<?php

require_once('./config.php');

use Apirone\API\Endpoints\Account;
use Apirone\Invoice\Model\InvoiceDetails;
use Apirone\Invoice\Utils;
use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;


Invoice::log($log_handler);
Invoice::config(Settings::fromFile(__DIR__ . 'invoice-config.json'));


pa(Invoice::$settings->toJsonString(JSON_PRETTY_PRINT));

