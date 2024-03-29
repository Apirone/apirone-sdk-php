<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('../vendor/autoload.php');
require_once('db.php');
require_once('log.php');

use Apirone\SDK\Service\InvoiceDb;

// Just set callback & prefix. See db.php for details
InvoiceDb::setCallback($db_handler);
InvoiceDb::setPrefix($table_prefix);


// Call install method to create table
InvoiceDb::install();

// Call uninstall method to drop table
// WARNING! All table data will be lost
// InvoiceDb::uninstall();
