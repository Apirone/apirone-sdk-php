<?php
require_once('../common/all.php');

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;
use Apirone\Invoice\Render;

// Config, DB & Logs
Invoice::config( Settings::fromFile(__DIR__ . '/invoice-config.json') );
Invoice::db($db_handler, 'pfx_');
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
// $invoice = Invoice::getInvoice('79Ry0vxkPp8Adt2b'); // expired
// $invoice = Invoice::getInvoice('fPlb8egiPSqqXgcb'); // created

// pa($invoice::$settings->getCurrency('btc'));
$render = Render::init();
// $render->setTimeZoneByOffset(-240);
// $render->showInvoice('79Ry0vxkPp8Adt2b');
// $render->showInvoice($invoice);
// $render->showInvoice(null);

?>
<html>
    <head>
        <script src="/UI/js/script.js" type="text/javascript"></script>
        <link href="/UI/css/styles.css" rel="stylesheet">
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

