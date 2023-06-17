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
$is_ajax = (array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? true : false;

if ($id && $is_ajax) {
    $id = $_GET['invoice'];
    $offset = $_GET['offset'] ?? false;

    $invoice = Invoice::getInvoice($id);
    $render = Render::init();
    $render->setTimeZoneByOffset($offset);
    header("Content-Type: text/plain");
    if ($offset)
        $render->showInvoice($invoice);
    else
        echo $invoice->details->statusNum();
    exit;
}

$render = Render::init();
$render->setDataUrl('/render.php');

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