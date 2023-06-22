<?php
require_once('../vendor/autoload.php');
require_once('db.php');

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;

/**
 * Your system Order status handler
 */
$order_handler = static function($invoice)
{
    $order_id = $invoice->order;

    // Process order by order_id
};

// Setup DB and Settinfs into invoice object
Invoice::db($db_handler, $table_prefix);
Invoice::settings(Settings::fromFile('/var/www/storage/settings.json'));


Invoice::callbackHandler($order_handler);
