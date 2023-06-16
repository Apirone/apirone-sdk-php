<?php
require_once('../vendor/autoload.php');
require_once('db_config_example.php');

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;

// Setup DB and Settinfs into invoice object
Invoice::db($db_handler, $table_prefix);
Invoice::config(Settings::fromFile('/var/www/storage/settings.json'));

