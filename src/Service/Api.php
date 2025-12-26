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
    /**
     * Minimum interval for checking invoice status
     *
     * @var int
     */
    public static int $checkInterval = 0;

    /**
     * Set $checkInterval via method
     *
     * @param int $interval
     * @return void
     */
    public static function checkInterval($interval = 0)
    {
        static::$checkInterval = $interval;
    }

    /**
     * Local API invoices entry point handler
     *
     * @param string $invoice
     * @param null|callable $paymentProcessing callback function
     * @return never
     */
    public static function invoices(string $invoice, ?callable $paymentProcessing = null)
    {
        $invoice = Invoice::get($invoice);
        if ($invoice !== null) {
            try {
                $updated = $invoice->update(static::$checkInterval);
                if ($updated && is_callable($paymentProcessing)) {
                    call_user_func($paymentProcessing, $invoice);
                }
                Utils::sendJson($invoice->info());
                exit;
            }
            catch (\Exception $e) {
                Utils::sendException($e);
            }
        }
        $json = json_decode('{"message": "Incorrect invoice id."}');

        Utils::sendJson($json, 404);
        exit;
    }

    /**
     * Local API wallets entry point handler
     *
     * @return never
     */
    public static function wallets()
    {
        try {
            Utils::sendJson(Service::wallet());
        }
        catch (\Exception $e) {
            Utils::sendException($e);
        }
        exit;
    }
}
