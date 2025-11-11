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

class Postgres implements AdapterInterface
{
    /**
     * Return create invoice table SQL query
     *
     * @return string
     */
    public static function createTable()
    {
        return sprintf('CREATE TABLE IF NOT EXISTS %1$s (
                "id" SERIAL PRIMARY KEY,
                "time" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                "order" INTEGER NOT NULL DEFAULT 0,
                "invoice" VARCHAR(64) NOT NULL,
                "status" VARCHAR(10) NOT NULL,
                "details" TEXT,
                "meta" TEXT,
                UNIQUE (invoice)
            );', Db::tableName());
    }

    /**
     * Return drop invoices table SQL query
     *
     * @return string
     */
    public static function dropTable()
    {
        return sprintf("DROP TABLE IF EXISTS %s;", Db::tableName());
    }

    /**
     * Return select invoice query by invoice id
     *
     * @param string $invoice
     * @return string
     */
    public static function getInvoice(string $invoice)
    {
        return sprintf('SELECT * FROM %s WHERE "invoice" = \'%s\'', Db::tableName(), $invoice);
    }

    /**
     * Return select invoices query by order id
     *
     * @param int $order
     * @return string
     */
    public static function getOrderInvoices(int $order)
    {
        return sprintf('SELECT * FROM %s WHERE "order" = %s order by time DESC', Db::tableName(), $order);
    }

    /**
     * Return create invoice query
     *
     * @param Invoice $invoice
     * @return string
     */
    public static function createInvoice(Invoice $invoice)
    {
        $invoice = $invoice->toJson();
        $invoice->order = $invoice->order ?? 0;
        $meta = property_exists($invoice, 'meta') ? sprintf("'%s'", json_encode($invoice->meta)) : "NULL";

        return sprintf('INSERT INTO %s ("order", "invoice", "status", "details", "meta") VALUES (%s, \'%s\', \'%s\', \'%s\', %s);',
                Db::tableName(),
                (int) $invoice->order,
                $invoice->invoice,
                $invoice->status,
                json_encode($invoice->details),
                $meta);
    }

    /**
     * Return update invoice query
     *
     * @param Invoice $invoice
     * @return string
     */
    public static function updateInvoice(Invoice $invoice)
    {
        $invoice = $invoice->toJson();
        $meta = property_exists($invoice, 'meta') ? sprintf("'%s'", json_encode($invoice->meta)) : "NULL";

        return sprintf('UPDATE %s SET "time" = CURRENT_TIMESTAMP, "status" = \'%s\', "details" = \'%s\', "meta" = %s WHERE "invoice" = \'%s\';',
            Db::tableName(),
            $invoice->status,
            json_encode($invoice->details),
            $meta,
            $invoice->invoice);

    }
}
