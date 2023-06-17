<?php
require_once('helpers/common.php');
require_once('db.php');

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;
use Apirone\Invoice\Render;

// Config & DB
Invoice::db($db_handler, $table_prefix);
Invoice::config( Settings::fromFile('/var/www/storage/settings.json') );

if ($_GET['invoice']) {
    $id = $_GET['invoice'];
    $offset = $_GET['offset'] ?? false;

    $invoice = Invoice::getInvoice($id);
    $render = Render::init();
    $render->setTimeZoneByOffset($offset);
    // sleep(1);
    header("Content-Type: text/plain");
    if ($offset)
        $render->showInvoice($invoice);
    else
        echo $invoice->details->statusNum();
    exit;
}

$render = Render::init();
$render->setDataUrl('/invoice_render.php');

?>
<html>
    <head>
        <script src="/assets/js/script.js" type="text/javascript"></script>
        <link href="/assets/css/styles.css" rel="stylesheet">
    </head>
    <body style="margin: 0;">
        <?php 
            $render->showInvoice('aJi6bqNgfTvEgje8'); 
            // $invoice = Invoice::getInvoice('aJi6bqNgfTvEgje8');
            // pa($invoice->details->toJson());
        ?>
    </body>
</html>
