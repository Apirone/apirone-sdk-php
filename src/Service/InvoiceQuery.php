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
    ): string {
        $table = $prefix . self::TABLE_INVOICE;

        $charset_collate = '';
        if (! empty($charset)) {
            $charset_collate = "DEFAULT CHARACTER SET $charset";
        }
        if (! empty($collate)) {
            $charset_collate .= " COLLATE $collate";
        }

        $query = "CREATE TABLE IF NOT EXISTS `$table` (
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
        ) ENGINE=InnoDB $charset_collate;";

        return $query;
    }

    /**
     * Return drop invoices table SQL query
     *
     * @param string $prefix
     * @return string
     */
    public static function dropInvoicesTable(string $prefix = '')
    {
        $table = $prefix . self::TABLE_INVOICE;

        $query = "DROP TABLE IF EXISTS `$table`;";

        return $query;
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
        $table = $prefix . self::TABLE_INVOICE;

        return "SELECT * FROM `$table` WHERE `invoice` = \"$invoice\"";
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
        $table = $prefix . self::TABLE_INVOICE;

        return "SELECT * FROM `$table` WHERE `order` = \"$order\" order by time DESC";
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
        $table = $prefix . self::TABLE_INVOICE;

        $invoice = $invoice->toJson();

        $query = "INSERT INTO `" . $table . "` " .
            "SET " .
            "`order` = " . (int) $invoice->order . "," .
            "`invoice` = '" . $invoice->invoice . "', " .
            "`status` = '" . $invoice->status . "', " .
            "`details` = '" . json_encode($invoice->details) . "', " .
            "`meta` = '" . json_encode($invoice->meta) . "';";

        return $query;
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
        $table = $prefix . self::TABLE_INVOICE;

        $invoice = $invoice->toJson();

        $query = "UPDATE `" . $table . "` " .
            "SET " .
            "`status` = '" . $invoice->status . "', " .
            "`details` = '" . json_encode($invoice->details) . "', " .
            "`meta` = '" . json_encode($invoice->meta) . "' " .
            "WHERE `invoice` = '" . $invoice->invoice . "';";

        return $query;

    }
}
