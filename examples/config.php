<?php

use Apirone\Invoice\Db\InvoiceDb;

define('ROOT', __DIR__);
define('SLASH', DIRECTORY_SEPARATOR);

// PrintArray - used for fast debug output
function pa($mixed, $title = false)
{
    if ($title) {
        echo $title . ':';
    }
    echo '<pre>';
    if (gettype($mixed) == 'boolean') {
        print_r($mixed ? 'true' : 'false');
    }
    else {
        print_r(!is_null($mixed) ? $mixed : 'NULL');
    }
    echo '</pre>';
}

// Autoload emulation for development
spl_autoload_register(function ($className) 
{
    $fileName = str_replace('Apirone/Invoice/', '', sprintf("%s%s%s.php", ROOT, SLASH, str_replace("\\", "/", $className)));

    if (file_exists($fileName)) {
        require_once ($fileName);
    } 
    else {
        echo "file not found {$fileName}";
    }
});

require_once ('../vendor/autoload.php');

// DB settings
$host = 'db';
$user = 'root';
$pass = 'toor';
$db = 'apirone';

$conn = new mysqli($host, $user, $pass, $db);
$conn->select_db($db);

// Log handler example
$log_handler = static function($message) {
    pa($message);
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

// Set table prefix example
InvoiceDb::setPrefix('pfx_');
