<?php

use Apirone\SDK\Service\Db;

// Configure SQLite PDO connection
$db_path = '/var/www/storage/sqlite.db';
$pdo = new \PDO('sqlite:' . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Database handler callback function
$db_handler = static function ($query) {
    global $pdo;

    $select = (str_contains(strtoupper($query), 'SELECT')) ? true : false;

    try {
        $result = $select ? $pdo->query($query) : $pdo->exec($query);
    }
    catch (\Exception $e) {
        return $e->getMessage();
    }

    if (!$select) {
        return (bool) $result;
    }

    return $result->fetchAll(PDO::FETCH_ASSOC);
};

// Configure SDK Db class
Db::handler($db_handler)
    ->adapter('sqlite') // Also available mysql & postgres. By default used `mysql` adapter
    ->prefix('prefix_') // By default prefix is empty string
    ->table('my_invoice_table'); // By defailt used `apirone_invoice`

