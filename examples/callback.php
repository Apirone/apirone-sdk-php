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

use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

/**
 * Your system Order status handler
 */
$order_handler = static function ($invoice) {
    $order_id = $invoice->order;

    // Process order by order_id
};

// Setup DB and Settings into invoice object
Invoice::db($db_handler, $table_prefix);
Invoice::settings(Settings::fromFile('/var/www/storage/settings.json'));

Invoice::callbackHandler($order_handler);
