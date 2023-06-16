<?php

require_once('../helpers/common.php');
require_once('../db_config_example.php');

use Apirone\Invoice\Utils;
use Apirone\Invoice\Db\InvoiceDb;

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
