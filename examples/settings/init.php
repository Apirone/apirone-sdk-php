<?php
// Init new settings object

require_once('../common/pa.php'); // Just for example output
require_once('../../vendor/autoload.php'); // Path/to/vender/folder/autoload.php

use Apirone\Invoice\Model\Settings;

// Do not store settings file inside public html directory.
$file = '/var/www/settings/settings.json';

if (!file_exists($file)) {
    // Create settings object
    $settings = Settings::init();

    // Create a new account
    $settings->createAccount();
}
else {
    // Load saved config
    $settings = Settings::fromFile($file);
}

// Set destination & fee policy for btc curency (for example)
$destination = '3KM9d68fLzSiukUFcx5vbiMHoLc6RvsRLy';
$fee = 'percentage';

$settings->getCurrency('btc')->setAddress($destination)->setPolicy($fee);

// Get & prinit all service cryptos
pa($settings->getCurrencies(), 'Service Currencies');

// Save currencies settings into account
$settings->saveCurrencies();

// Save settings to file
$settings->toFile($file);

// Restore saved settings from file
$saved = Settings::fromFile($file);

// Print saved as JSON
pa($saved->toJson(), 'Saved settings');
