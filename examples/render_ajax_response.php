<?php
require_once('helpers/common.php');
require_once('db.php');

use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;
use Apirone\SDK\Service\Render;

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
