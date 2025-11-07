<?php

require_once('helpers/common.php');
require_once('db_adapter.php');
require_once('log.php');

use Apirone\SDK\Service\Api;
use Apirone\SDK\Service\Utils;

$endpoint = Utils::sanitize($_REQUEST['url']);

switch ($endpoint) {
    // Invoice request URl looks like this: https://your-white-label-api-root/invoices/{INVOICE_ID}
    // We need to get last part from URL
    case str_contains($endpoint, 'invoices'):
        $urlParts = explode('/', $endpoint);
        $invoice_id = end($urlParts);
        Api::invoices($invoice_id);
    // Wallets request URl looks like this: https://your-white-label-api-root/wallets
    case 'wallets':
        Api::wallets();
}

Utils::sendJson('Not Found', 404);
