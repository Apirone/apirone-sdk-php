<?php
require_once('../common/all.php');


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
