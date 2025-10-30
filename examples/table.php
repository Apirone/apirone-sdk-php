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
require_once('db_sqlite.php');

use Apirone\SDK\Service\Db;

// See db_sqlite.php for configure details


// Call install method to create table
Db::install();

// Call uninstall method to drop table
// WARNING! All table data will be lost
Db::uninstall();
