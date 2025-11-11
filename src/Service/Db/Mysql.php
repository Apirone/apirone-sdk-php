<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Apirone\SDK\Service\Db;

use Apirone\SDK\Invoice;
use Apirone\SDK\Service\Db;
use Apirone\SDK\Service\Db\AdapterInterface;

class Mysql implements AdapterInterface
{
    public static string $charset = 'utf8';

    public static string $collate = 'utf8_general_ci';


    /**
     * Return create invoice table SQL query
     *
     * @param string $prefix
     * @param string $charset
     * @param string $collate
     * @return string
     */
    public static function createTable()
    {
        return sprintf("CREATE TABLE IF NOT EXISTS `%s` (
            `id` int NOT NULL AUTO_INCREMENT,
            `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `order` int NOT NULL DEFAULT '0',
            `invoice` varchar(64) NOT NULL,
            `status` varchar(10) NOT NULL,
            `details` text NULL,
            `meta` text NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `invoice` (`invoice`),
            KEY `order` (`order`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET %s COLLATE %s;", Db::tableName(), static::$charset, static::$collate);
    }

    /**
     * Return drop invoices table SQL query
     *
     * @param string $prefix
     * @return string
     */
    public static function dropTable()
    {
        return sprintf("DROP TABLE IF EXISTS `%s`;", Db::tableName());
    }

    /**
     * Return select invoice query by invoice id
     *
     * @param null|string $invoice
     * @param string $prefix
     * @return string
     */
    public static function getInvoice(string $invoice)
    {
        return sprintf('SELECT * FROM `%s` WHERE `invoice` = "%s"', Db::tableName(), $invoice);
    }

    /**
     * Return select invoices query by order id
     *
     * @param string $order
     * @param string $prefix
     * @return string
     */
    public static function getOrderInvoices(int $order)
    {
        return sprintf('SELECT * FROM `%s` WHERE `order` = %s order by time DESC', Db::tableName(), $order);
    }

    /**
     * Return create invoice query
     *
     * @param Invoice $invoice
     * @param string $prefix
     * @return string
     */
    public static function createInvoice(Invoice $invoice)
    {
        $invoice = $invoice->toJson();
        $invoice->order = $invoice->order ?? 0;
        $meta = property_exists($invoice, 'meta') ? sprintf("'%s'", json_encode($invoice->meta)) : "NULL";

        return sprintf("INSERT INTO `%s` SET `order` = %s, `invoice` = '%s', `status` = '%s', `details` = '%s', `meta` = %s;",
            Db::tableName(),
            $invoice->order,
            $invoice->invoice,
            $invoice->status,
            json_encode($invoice->details),
            $meta
        );
    }

    /**
     * Return update invoice query
     *
     * @param Invoice $invoice
     * @param string $prefix
     * @return string
     */
    public static function updateInvoice(Invoice $invoice)
    {
        $invoice = $invoice->toJson();
        $meta = property_exists($invoice, 'meta') ? sprintf("'%s'", json_encode($invoice->meta)) : "NULL";

        return sprintf("UPDATE `%s`SET `time` = CURRENT_TIMESTAMP, `status` = '%s', `details` = '%s', `meta` = %s WHERE `invoice` = '%s';",
            Db::tableName(),
            $invoice->status,
            json_encode($invoice->details),
            $meta,
            $invoice->invoice);
    }
}
