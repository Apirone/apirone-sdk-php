<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('helpers/common.php');
require_once('db.php');
require_once('log.php');

use Apirone\SDK\Invoice;
use Apirone\SDK\Service\Db;
use Apirone\SDK\Service\WhiteLabelApi;
use Apirone\SDK\Service\Utils;

// Config & DB
Db::handler($db_handler)->prefix($table_prefix);

WhiteLabelApi::start();
