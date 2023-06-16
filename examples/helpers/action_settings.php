<?php

require_once('../helpers/common.php');

use Apirone\Invoice\Utils;
use Apirone\Invoice\Model\Settings;

$path = '/var/www/storage/settings.json';
$file = (int) file_exists($path);

$action = $_GET['action'] ?? false;


switch ($action) {
    case 'create':
        require_once('../settings_create.php');
        break;
    case 'delete':
        if ($file) {
            unlink($path);
        }
        break;
}

$file = (int) file_exists($path);
$content = ($file) ? load_file_content($path) : false;
Utils::send_json($content);
