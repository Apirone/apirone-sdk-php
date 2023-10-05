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
use Apirone\SDK\Model\Settings;
use Apirone\SDK\Service\Render;

// Config & DB
Invoice::db($db_handler, $table_prefix);
Invoice::settings(Settings::fromFile('/var/www/storage/settings.json'));
Invoice::dataUrl($_SERVER['REQUEST_URI']);

// Override settings value qrOnly from GET-param
$qrOnly = $_GET['qr-only'] ?? false;
if ($qrOnly) {
    Render::$qrOnly = $qrOnly;
}

Invoice::renderAjax();
