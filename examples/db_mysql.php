<?php

$host         = 'db_mysql';
$user         = 'root';
$pass         = 'apirone';
$database     = 'apirone';

// MySQL connection example
$conn = new mysqli($host, $user, $pass, $database);
$conn->select_db($database);

// DB MySQL handler example
$db_handler = static function ($query) {
    global $conn;

    // Escape \u for utf8 encoding. mysqli->query remove all single backslashes
    $query = str_replace('\u', '\\\u', $query);
    $result = $conn->query($query, MYSQLI_STORE_RESULT);

    if (!$result) {
        return $conn->error;
    }
    if (gettype($result) == 'boolean') {
        return $result;
    }

    return $result->fetch_all(MYSQLI_ASSOC);
};

// Configure SDK Db class
Db::handler($db_handler)
    // ->adapter('mysql') // Also available sqlite & postgres. By default used `mysql` adapter
    ->prefix('prefix_') // By default prefix is empty string
    ->table('my_invoice_table'); // By defailt used `apirone_invoice`
