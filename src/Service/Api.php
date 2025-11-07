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
class Api
{
    public static function invoices($id)
    {
        $invoice = Invoice::get($id);
        if ($invoice->id !== null) {
            Utils::sendJson($invoice->details->toJson());
            exit;
        }
        $json = json_decode('{"message": "Incorrect invoice id." }');

        Utils::sendJson($json, 400);
        exit;
    }

    public static function wallets()
    {
        Utils::sendJson(Service::wallet());
        exit;
    }
}
