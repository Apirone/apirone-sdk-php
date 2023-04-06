<?php

namespace Apirone\Invoice\Db;

class InvoiceQuery
{
    const TABLE_INVOICE = 'apirone_invoice';

    /**
     * Return create invoice table SQL query
     * 
     * @param string $prefix 
     * @param string $charset 
     * @param string $collate 
     * @return string 
     */
    public static function createInvoicesTable (
        string $prefix = '',
        string $charset = 'utf8',
        string $collate = 'utf8_general_ci'
    ): string {
        $table = $prefix . self::TABLE_INVOICE;

        $charset_collate = '';
        if ( ! empty( $charset ) ) {
            $charset_collate = "DEFAULT CHARACTER SET $charset";
        }
        if ( ! empty( $collate ) ) {
                $charset_collate .= " COLLATE $collate";
        }

        $query = "CREATE TABLE IF NOT EXISTS `$table` (
            `id` int NOT NULL AUTO_INCREMENT,
            `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `order_id` int NOT NULL DEFAULT '0',
            `account` varchar(64) NOT NULL,
            `invoice` varchar(64) NOT NULL,
            `status` varchar(10) NOT NULL,
            `details` text NULL,
            `meta` text NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `invoice` (`invoice`),
            KEY `order_id` (`order_id`)
        ) ENGINE=InnoDB $charset_collate;";

        return $query;
    }

    /**
     * Return drop invoices table SQL query
     * @param string $prefix 
     * @return string 
     */
    public static function dropInvoicesTable (string $prefix = '')
    {
        $table = $prefix . self::TABLE_INVOICE;

        $query = "DROP TABLE IF EXISTS `$table`;";

        return $query;
    }

    public static function selectInvoice (string $invoice, string $prefix = '')
    {
        $table = $prefix . self::TABLE_INVOICE;

        return "SELECT * FROM `$table` WHERE `invoice` = \"$invoice\"";
    }
}