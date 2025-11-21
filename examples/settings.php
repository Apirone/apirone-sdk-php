<?php

require_once('/var/www/vendor/autoload.php'); // Path/to/vendor/folder/autoload.php
require_once('log.php');

use Apirone\SDK\Model\Settings;

// Do not store settings file inside public html directory.
$path = '/var/www/storage/settings.json';

if (!file_exists($path)) {
    // Create settings object
    $settings = Settings::init();

    // Create a new account
    $settings->createAccount();
}
else {
    // Load saved config
    $settings = Settings::fromFile($path);
}

// You can save any property value, which will be stored
// in a special metaproperty and retrieved by its name.
// For example, save the processing-fee-plan in a settings object.
// To remove a parameter, simply set its value to empty via $settings->fee()
$settings->fee('percentage');

// Your transfer address for tbtc
$transfer_address = '2N186GvYp1gctUXMT4RXzAv3N5MB7wLncq7';

// Configure tbtc currency
$settings->currency('tbtc')
    ->address($transfer_address)
    ->policy($settings->fee);

// Save currencies settings into account
$settings->saveCurrencies();

// Save settings to file
$settings->toFile($path);
