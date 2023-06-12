<?php

require_once('common/all.php');

use Apirone\Invoice\Db\InvoiceDb;

InvoiceDb::setCallback($db_handler);

$result = InvoiceDb::install($table_prefix);

pa($result);