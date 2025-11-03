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
class InvoiceAppApi
{
    public static function start()
    {
        $endpoint = Utils::sanitize($_REQUEST['url']);

        switch ($endpoint) {
            // Invoice request URl looks like this: https://your-white-label-api-root/invoices/{INVOICE_ID}
            // We need to get last part from URL
            case str_contains($endpoint, 'invoices'):
                $urlParts = explode('/', $endpoint);
                $invoice_id = end($urlParts);
                static::invoices($invoice_id);
            // Wallets request URl looks like this: https://your-white-label-api-root/wallets
            case 'wallets':
                static::wallets();
        }

        Utils::sendJson('Not Found', 404);

    }
    private static function invoices($id)
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

    private static function wallets()
    {
        Utils::sendJson(Service::wallet());
        exit;
    }
}
