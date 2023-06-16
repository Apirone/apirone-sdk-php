<?php
// Advanced invoice settings

require_once('helpers/pa.php'); // Just for example output
require_once('../vendor/autoload.php'); // Path/to/vender/folder/autoload.php

use Apirone\Invoice\Model\Settings;

$file = '/var/www/storage/settings.json';

$settings = Settings::fromFile($file);

// Merchant name
$settings->setMerchant('Merchant name');

// Timeout, sec. Default 1800 (30 min)
$settings->setTimeout(300);

// Price adjustment factor
// If you want to add/substract percent to/from the payment amount,
// use the following  price adjustment factor multiplied by the amount.
// For example: <br />100% * 0.99 = 99% <br />100% * 1.01 = 101%
$settings->setFactor(1.01);

// Backlink
// Backlink button to some url in invoice footer
$settings->setBacklink('https://example.org');

// Logo
// Show apirone logo in invoice footer. Default true
$settings->setLogo(false);

// Extra
// If you need store additional params you can use extra
$settings->setExtra('key1', 'value 1');
$settings->setExtra('key2', 'value 2');

// Also you can use stdClass to set extra
$my_extra = new \stdClass;
$my_extra->some_value = 'Some value';
$my_extra->other_value = 'Oher value';
$settings->setExtraObj($my_extra);

// Save settings
$settings->toFile($file);

// Print settings
pa($settings->toJson());
