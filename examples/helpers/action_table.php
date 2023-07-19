<?php

require_once('common.php');
require_once('../db.php');
require_once('../log.php');

use Apirone\SDK\Service\Utils;
use Apirone\SDK\Service\InvoiceDb;

InvoiceDb::$handler = $db_handler;

$table_query = sprintf("SELECT * FROM apirone.%sapirone_invoice LIMIT 1", $table_prefix);
$table_exists  = gettype(InvoiceDb::execute($table_query))  == 'array' ? true : false;

$action = $_GET['action'] ?? false;

switch ($action) {
    case 'create':
        Utils::send_json(InvoiceDb::install($table_prefix));
        break;
    case 'delete':
        Utils::send_json(!InvoiceDb::uninstall($table_prefix));
        break;
    default:
        Utils::send_json($table_exists);
}
