<?php
require_once('../vendor/autoload.php');
require_once('db_config_example.php');

use Apirone\Invoice\Db\InvoiceDb;

// Just set callback & prefix
InvoiceDb::setCallback($db_handler); // See step 1
InvoiceDb::setPrefix($table_prefix); // See step 1


// Call install method to create table
InvoiceDb::install(); 

// Call uninstall method to drop table
// WARNING! All table data will be lost
InvoiceDb::uninstall();
