<?php
require_once('helpers/common.php');
require_once('db.php');

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;
use Apirone\Invoice\Render;

// Config & DB
Invoice::db($db_handler, $table_prefix);
Invoice::config( Settings::fromFile('/var/www/storage/settings.json') );

$id = array_key_exists('invoice', $_GET) ? $_GET['invoice'] : null ;
$is_ajax = (array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) 
    && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? true : false;

$id = $_GET['invoice'];
$offset = $_GET['offset'] ?? false;

$render = Render::init();
$render->setDataUrl('/render.php');
$render->setTimeZoneByOffset($offset);

if ($id && $is_ajax) {
    $invoice = Invoice::getInvoice($id);
    $render = Render::init();
    header("Content-Type: text/plain");
    if ($offset)
        $render->showInvoice($invoice);
    else
        echo $invoice->details->statusNum();
    exit;
}
?>
<html>
    <head>
        <script src="/assets/js/script.js" type="text/javascript"></script>
        <link href="/assets/css/styles.css" rel="stylesheet">
    </head>
    <body style="margin: 0;">
        <?php 
            $render->showInvoice($id); 
        ?>
    </body>
</html>
