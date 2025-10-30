<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$host         = 'db_pgsql';
$user         = 'apirone';
$pass         = 'apirone';
$database     = 'apirone';

use Apirone\SDK\Service\Db;

// Establish a PDO connection
$dsn = "pgsql:host=$host;dbname=$database";
$pdo = new PDO($dsn, $user, $pass);

// Set error mode to exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// DB MySQL handler example
$db_handler = static function ($query) {
    global $pdo;

    $select = (str_contains(strtoupper($query), 'SELECT')) ? true : false;

    try {
        $result = $select ? $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC) : $pdo->exec($query);
    }
    catch (\Exception $e) {
        return $e->getMessage();
    }

    if (!$select) {
        return (bool) $result;
    }

    return $result;
};

// Configure SDK Db class
Db::handler($db_handler)
    ->adapter('postgres') // Also available sqlite & mysql. By default used `mysql` adapter
    ->prefix('prefix_') // By default prefix is empty string
    ->table('my_invoice_table'); // By defailt used `apirone_invoice`
