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

namespace Apirone\SDK\Service;

use Apirone\SDK\Invoice;
use Apirone\SDK\Service\InvoiceDb;

class InvoiceQuery
{
    public const TABLE_INVOICE = 'apirone_invoice';

    /**
     * Return create invoice table SQL query
     *
     * @param string $prefix
     * @param string $charset
     * @param string $collate
     * @return string
     */
    public static function createInvoicesTable(
        string $prefix = '',
        string $charset = 'utf8',
        string $collate = 'utf8_general_ci'
    ): string
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
        ) ENGINE=InnoDB DEFAULT CHARACTER SET %s COLLATE %s;", self::getTable($prefix), $charset, $collate);
    }

    /**
     * Return drop invoices table SQL query
     *
     * @param string $prefix
     * @return string
     */
    public static function dropInvoicesTable(string $prefix = '')
    {
        return sprintf("DROP TABLE IF EXISTS `%s`;", self::getTable($prefix));
    }

    /**
     * Return select invoice query by invoice id
     *
     * @param null|string $invoice
     * @param string $prefix
     * @return string
     */
    public static function selectInvoice(?string $invoice, string $prefix = '')
    {
        return sprintf('SELECT * FROM `%s` WHERE `invoice` = "%s"', self::getTable($prefix), $invoice);
    }

    /**
     * Return select invoices query by order id
     *
     * @param string $order
     * @param string $prefix
     * @return string
     */
    public static function selectOrder(int $order, string $prefix = '')
    {
        return sprintf('SELECT * FROM `%s` WHERE `order` = %s order by time DESC', self::getTable($prefix), $order);
    }

    /**
     * Return create invoice query
     *
     * @param Invoice $invoice
     * @param string $prefix
     * @return string
     */
    public static function createInvoice(Invoice $invoice, string $prefix = '')
    {
        $invoice = $invoice->toJson();
        $invoice->order = $invoice->order ?? 0;
        $meta = property_exists($invoice, 'meta') ? sprintf("'%s'", json_encode($invoice->meta)) : "NULL";

        return "INSERT INTO `" . self::getTable($prefix) . "` " .
            "SET " .
            "`order` = " . (int) $invoice->order . "," .
            "`invoice` = '" . $invoice->invoice . "', " .
            "`status` = '" . $invoice->status . "', " .
            "`details` = '" . json_encode($invoice->details) . "', " .
            "`meta` = " . $meta . ";";
    }

    /**
     * Return update invoice query
     *
     * @param Invoice $invoice
     * @param string $prefix
     * @return string
     */
    public static function updateInvoice(Invoice $invoice, string $prefix = '')
    {
        $invoice = $invoice->toJson();
        $meta = property_exists($invoice, 'meta') ? sprintf("'%s'", json_encode($invoice->meta)) : "NULL";

        return "UPDATE `" . self::getTable($prefix) . "` " .
            "SET " .
            "`status` = '" . $invoice->status . "', " .
            "`details` = '" . json_encode($invoice->details) . "', " .
            "`meta` = " . $meta .
            " WHERE `invoice` = '" . $invoice->invoice . "';";
    }

    protected static function getTable($prefix = '')
    {
        $prefix = trim($prefix) !== '' ? $prefix : (string) InvoiceDb::$prefix;

        return trim($prefix) . self::TABLE_INVOICE;
    }
}
