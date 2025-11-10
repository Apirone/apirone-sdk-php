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
    public static function invoices(string $invoice, ?callable $paymentProcessing = null)
    {
        $invoice = Invoice::get($invoice);
        if ($invoice->id !== null) {
            try {
                $updated = $invoice->update();
                if ($updated && is_callable($paymentProcessing)) {
                    call_user_func($paymentProcessing, $invoice);
                }
                Utils::sendJson($invoice->details->toJson());
                exit;
            }
            catch (\Exception $e) {
                static::sendException($e);
            }
        }
        $json = json_decode('{"message": "Incorrect invoice id."}');

        Utils::sendJson($json, 404);
        exit;
    }

    public static function wallets()
    {
        try {
            Utils::sendJson(Service::wallet());
        }
        catch (\Exception $e) {
            static::sendException($e);
        }
        exit;
    }

    private static function sendException($e)
    {
        $json = json_decode(sprintf('{"message": "%s"}', $e->getMessage()));
        $code = $e->getCode();

        Utils::sendJson($json, $code ? $code : 503);
        exit;
    }
}
