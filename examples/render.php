<?php
require_once('helpers/common.php');
require_once('db.php');

use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

// Config & DB
Invoice::db($db_handler, $table_prefix);
Invoice::settings( Settings::fromFile('/var/www/storage/settings.json') );
$invoiceDataUrl = 'render_ajax_response.php';

if (array_key_exists('qr-only', $_GET)) {
    $invoiceDataUrl .= '?qr-only=' . $_GET['qr-only'];
}

Invoice::dataUrl($invoiceDataUrl);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="/assets/js/script.js" type="text/javascript"></script>
        <link href="/assets/css/styles.css" rel="stylesheet">
    </head>
    <body style="margin: 0;">
        <?php
            echo Invoice::renderLoader();
        ?>
    </body>
</html>
