<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('/var/www/vendor/autoload.php'); // Path/to/vender/folder/autoload.php
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

// Set destination & fee policy for tbtc currency (for example)
$destination = '2N186GvYp1gctUXMT4RXzAv3N5MB7wLncq7';
$fee = 'percentage';

$settings->currency('btc')->address();
$settings->currency('tbtc')->address($destination)->policy($fee);

// Save currencies settings into account
$settings->saveCurrencies();

// Save settings to file
$settings->toFile($path);
