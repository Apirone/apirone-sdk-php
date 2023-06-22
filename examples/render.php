<?php
require_once('helpers/common.php');
require_once('db.php');

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;

// Config & DB
Invoice::db($db_handler, $table_prefix);
Invoice::settings( Settings::fromFile('/var/www/storage/settings.json') );
Invoice::dataUrl('/render_ajax_response.php');

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
