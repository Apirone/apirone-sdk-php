<?php

// DB settings
$host         = 'db';
$user         = 'root';
$pass         = 'apirone';
$database     = 'apirone';
$table_prefix = 'pfx_';

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
