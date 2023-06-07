<?php

// DB settings
$host = 'db';
$user = 'root';
$pass = 'toor';
$db = 'apirone';
$table_prefix = 'pfx_';

$conn = new mysqli($host, $user, $pass, $db);
$conn->select_db($db);

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
