<?php

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;


require_once ('../vendor/autoload.php');

// DB settings
$host = 'db';
$user = 'root';
$pass = 'toor';
$db = 'apirone';
$table_prefix = 'pfx_';

$conn = new mysqli($host, $user, $pass, $db);
$conn->select_db($db);

// Log handler example
$log_handler = static function($message) {
    print_r($message);
};

// DB MySQL handler example
$db_handler = static function($query) {
    global $conn;
    $result = $conn->query($query, MYSQLI_STORE_RESULT);

    if (!$result) {
        return $conn->error;
    }
    if (gettype($result) == 'boolean') {
        return $result;
    }
    return $result->fetch_all(MYSQLI_ASSOC);
};

 // Config, DB & Logs
Invoice::config(Settings::fromFile(__DIR__ . '/settings/settings-example.json'));
Invoice::db($db_handler, $table_prefix);
Invoice::log($log_handler);
