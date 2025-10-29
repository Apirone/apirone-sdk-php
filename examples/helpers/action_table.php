<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('common.php');
require_once('../db.php');
require_once('../log.php');

use Apirone\SDK\Service\Utils;
use Apirone\SDK\Service\Db;

$table_query = sprintf("SELECT * FROM %s LIMIT 1", Db::tableName());
$table_exists  = gettype(Db::execute($table_query))  == 'array' ? true : false;

$action = $_GET['action'] ?? false;

switch ($action) {
    case 'create':
        Utils::sendJson(Db::install());
        break;
    case 'delete':
        Utils::sendJson(!Db::uninstall());
        break;
    default:
        Utils::sendJson($table_exists);
}
