<?php

require_once('../helpers/common.php');

use Apirone\Invoice\Utils;
use Apirone\Invoice\Model\Settings;

$path = '/var/www/storage/settings.json';
$action = $_GET['action'] ?? false;

switch ($action) {
    case 'create':
        require_once('../settings_create.php');
        break;
    case 'delete':
        if (file_exists($path)) {
            unlink($path);
        }
        break;
}

$content = file_exists($path) ? load_file_content($path,  false) : false;

Utils::send_json($content);
