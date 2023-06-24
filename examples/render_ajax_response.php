<?php
require_once('helpers/common.php');
require_once('db.php');

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;
use Apirone\Invoice\Service\Render;

// Config & DB
Invoice::db($db_handler, $table_prefix);
Invoice::settings( Settings::fromFile('/var/www/storage/settings.json') );
Invoice::dataUrl($_SERVER['REQUEST_URI']);

// Override settings value qrOnly from GET-param
$qrOnly = $_GET['qr-only'] ?? false;
if ($qrOnly) {
    Render::$qrOnly = $qrOnly;
}

Invoice::renderAjax();
