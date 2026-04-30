<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apirone\SDK\Service\Db;

use Apirone\SDK\Invoice;

interface AdapterInterface
{
    public static function createTable();

    public static function dropTable();

    public static function getInvoice(string $invoice);

    public static function getOrderInvoices(int $order);

    public static function createInvoice(Invoice $invoice);

    public static function updateInvoice(Invoice $invoice);
}
