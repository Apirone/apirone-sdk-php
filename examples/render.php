<?php
require_once('helpers/common.php');
require_once('db.php');

use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

// Config & DB
Invoice::db($db_handler, $table_prefix);
Invoice::settings( Settings::fromFile('/var/www/storage/settings.json') );
$invoceDataUrl = 'render_ajax_response.php';

if (array_key_exists('qr-only', $_GET)) {
    $invoceDataUrl .= '?qr-only=' . $_GET['qr-only'];
}

Invoice::dataUrl($invoceDataUrl);
?>
<html>
    <head>
        <script src="/assets/js/script.js" type="text/javascript"></script>
        <link href="/assets/css/styles.css" rel="stylesheet">
    </head>
    <body style="margin: 0;">
        <?php
            echo Invoice::renderLoader();
        ?>
    </body>
</html>
