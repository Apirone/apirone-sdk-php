<?php
require_once('common/all.php');

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;

// Config, DB & Logs
Invoice::config( Settings::fromFile('/var/www/settings/invoice-config.json') );
Invoice::db($db_handler, 'pfx_');
Invoice::log($log_handler);

if ($_GET['invoice']) {
    $id = (int) $_GET['invoice'];
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
