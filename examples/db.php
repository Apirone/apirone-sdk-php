<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Apirone\SDK\Service\Db;
$db_path = '/var/www/storage/sqlite.db';
// $sqlite = new SQLite3($db_path);

// $db_handler = static function ($query) {
//     global $sqlite;

//     $select = (str_contains(strtoupper($query), 'SELECT')) ? true : false;

//     try {
//         $result = $select ? $sqlite->query($query) : $sqlite->exec($query);
//     }
//     catch (\Exception $e) {
//         return $e->getMessage();
//     }

//     if (!$select) {
//         return (bool) $result;
//     }

//     while ($rows[] = $result->fetchArray(SQLITE3_ASSOC)) {}
//     unset($rows[count($rows)]);

//     return $rows;
// };

$pdo = new \PDO('sqlite:' . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

Db::handler($db_handler)
    ->adapter('sqlite') // Also available mysql & postgres. By default used `mysql` adapter
    ->prefix('prefix_') // By default prefix is empty string
    ->table('my_invoice_table'); // By defailt used `apirone_invoice`

