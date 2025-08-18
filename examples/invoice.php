<?php

// require_once('helpers/common.php');
// require_once('db.php');

// use Apirone\SDK\Invoice;
// use Apirone\SDK\Model\Settings;

// // Config & DB
// Invoice::db($db_handler, $table_prefix);
// Invoice::settings(Settings::fromFile('/var/www/storage/settings.json'));
// $invoiceDataUrl = 'render_ajax_response.php';

// if (array_key_exists('qr-only', $_GET)) {
//     $invoiceDataUrl .= '?qr-only=' . $_GET['qr-only'];
//     Render::$qrOnly = $_GET['qr-only'];
// }

// Invoice::dataUrl($invoiceDataUrl);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" href="/assets/favicon.ico">
    <title>Invoice</title>
  <script type="module" crossorigin src="/assets/script.min.js"></script>
  <link rel="stylesheet" crossorigin href="/assets/style.min.css">
</head>
<body>
<div id="app"></div>
</body>
</html>
