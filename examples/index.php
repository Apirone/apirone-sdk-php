<?php

require_once('common/all.php');

use Apirone\API\Endpoints\Account;
use Apirone\Invoice\Model\InvoiceDetails;
use Apirone\Invoice\Utils;
use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;


pa(Invoice::$settings->toJsonString(JSON_PRETTY_PRINT));

