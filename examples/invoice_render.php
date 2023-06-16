<?php
require_once('helpers/common.php');

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;
use Apirone\Invoice\Render;

// Config, DB & Logs
Invoice::config( Settings::fromFile('/var/www/storage/settings.json') );
Invoice::db($db_handler, $table_prefix);
Invoice::log($log_handler);

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
            // $render->showInvoice($invoice); 
            // $render->showInvoice('79Ry0vxkPp8Adt2b'); 
            $render->showInvoice('QeqskSWixgoa7m95'); 
            // $render->showInvoice(null);
            // $render->showInvoice('fPlb8egiPSqqXgcb');
        ?>
    </body>
</html>
