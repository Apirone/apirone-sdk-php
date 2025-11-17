<?php

require_once('common.php');

use Apirone\SDK\Service\Utils;
use Apirone\SDK\Model\Settings;

$path = '/var/www/storage/settings.json';
$content = file_exists($path) ? load_file_content($path, false) : false;

if ($content) {
    foreach (Settings::fromJson(json_decode($content))->currencies as $currency) {
        $currencies[] = $currency->toJson();
    }
    Utils::sendJson(json_encode($currencies, JSON_PRETTY_PRINT));
    exit;

}

Utils::sendJson($content);
