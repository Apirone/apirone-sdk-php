<?php
require_once('helpers/common.php');
require_once('db.php');

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;
use Apirone\Invoice\Service\Render;

// Config & DB
Invoice::db($db_handler, $table_prefix);
Invoice::settings( Settings::fromFile('/var/www/storage/settings.json') );
Invoice::dataUrl('/render_ajax_response.php');

Render::$qrOnly = true;
Invoice::renderAjax();
