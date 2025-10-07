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


use Apirone\API\Endpoints\Service;
use Apirone\SDK\Invoice;
use Apirone\SDK\Service\Utils;
class InvoiceApi
{
    public static function invoice($id)
    {
        $invoice = Invoice::get($invoice);
        if ($invoice) {
            Utils::sendJson($invoice->details);
            exit;
        }
        $json = '{"message": "Incorrect invoice id." }';

        Utils::sendJson($json, 400);
        exit;
    }

    public static function wallets()
    {
        return Service::wallet();
    }
}
