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
use Apirone\SDK\Service\Utils;
use Apirone\API\Log\LoggerWrapper;
use DivisionByZeroError;
use ArithmeticError;
use Closure;
use Exception;
use stdClass;

class Render
{
    /**
     * Invoice id param name
     *
     * @var string
     */
    public static string $idParam = 'invoice';

    /**
     * Invoice data url for ajax request
     *
     * @var string
     */
    public static string $dataUrl = '';

    /**
     * Invoice store back link
     *
     * @var string
     */
    public static string $backlink = '';

    /**
     * Invoice timezone
     *
     * @var string
     */
    public static string $timeZone = 'UTC';

    /**
     * Show qr template only
     *
     * @var bool
     */
    public static bool $qrOnly = false;

    /**
     * Show apirone logo on invoice template
     *
     * @var bool
     */
    public static bool $logo = true;

    /**
     * Set render options
     *
     * @param string $dataUrl
     * @param bool $qrOnly
     * @param bool $logo
     * @param string $backlink
     * @return void
     */
    public static function init($dataUrl = '', $qrOnly = false, $logo = true, $backlink = '')
    {
        self::$dataUrl = $dataUrl;
        self::$qrOnly = $qrOnly;
        self::$logo = $logo;
        self::$backlink = $backlink;
    }

    /**
     * Set timezone by local timezone to UTC offset
     *
     * @param int $offset
     * @return void
     * @throws DivisionByZeroError
     * @throws ArithmeticError
     */
    public static function setTimeZoneByOffset(int $offset = 0)
    {
        if ($offset == 0 || abs($offset) >= 1140) {
            $tz = 'UTC';
        }
        else {
            $abs = abs($offset);
            $t = sprintf('%02d:%02d', intdiv($abs, 60), fmod($abs, 60));
            $tz = 'GMT' . (($offset < 0) ? '+' : '-') . $t;
        }
        self::$timeZone = $tz;
    }

    /**
     * Render invoice html
     *
     * @param Invoice $invoice
     * @return string|false
     */
    public static function show(?Invoice $invoice = null)
    {
        if($invoice instanceof Invoice && $invoice->id !== null) {
            $show = true;
            $id = $invoice->invoice;
            try {
                $invoice->update();
            }
            catch (Exception $e) {
                LoggerWrapper::error($e->getMessage(), [$invoice->invoice]);
            }
        }
        else {
            $show = false;
            $id = array_key_exists(Render::$idParam, $_GET) ? Utils::sanitize($_GET[Render::$idParam]) : '';
        }
        $loading  = !$show;

        $status = self::statusDescription($invoice);

        $statusLink = self::$dataUrl ? self::$dataUrl : '/';
        $backlink = !empty(self::$backlink) ? self::$backlink : Invoice::$settings->getBacklink();
        $logo = Invoice::$settings->getLogo();
        $template = !self::$qrOnly ? 'full' : 'qr-only';
        $note = null;
        $amount = null;

        if ($show) {
            $details = $invoice->details;
            $userData = $details->getUserData();
            $currency = Invoice::$settings->getCurrency($details->getCurrency());

            if ($details->amount !== null) {
                $overpaid = false;
                $remains = $details->amount;
                foreach ($details->history as $item) {
                    if (property_exists($item, 'amount')) {
                        $remains = $remains - $item->amount;
                    }
                    if ($overpaid == null && $item->status == 'overpaid') {
                        $overpaid = true;
                    }
                }
                $amount = ($remains <= 0) ? $details->amount : $remains;
                $amount = Utils::exp2dec($amount * $currency->getUnitsFactor());

                if (($details->status == 'created' || $details->status == 'partpaid') && !$details->isExpired()) {
                    $note = 'notePayment';
                }
                $note = ($overpaid) ? 'noteOverpaid' : $note;
            }
        }
        else {
            $details = $userData = $currency = null;
            $loading = true;
        }

        // Draw output:
        [$t, $d, $c, $l] = self::helpers(); // translate, date, copy, link
        ob_start();
        include(__DIR__ . '/tpl/' . $template . '.php');

        return ob_get_clean();
    }

    /**
     * Check request headers
     *
     * @return bool
     */
    public static function isAjaxRequest(): bool
    {
        return (array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER)
            && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? true : false;
    }

    /**
     * Return status description by invoice status
     *
     * @param Invoice $invoice
     * @return stdClass
     */
    private static function statusDescription(?Invoice $invoice = null): \stdClass
    {
        $status = new \stdClass();
        $status->title = 'Loading';
        $status->description = '';

        if ($invoice == null) {
            return $status;
        }
        
        switch ($invoice->status) {
            case 'created':
            case 'partpaid':
                if ($invoice->details->isExpired()) {
                    $status->title = 'Expired';
                    $status->description = 'paymentExpired';
                }
                else {
                    $status->title = 'Refresh';
                    $status->description = 'waitingForPayment';
                }

                break;
            case 'paid':
            case 'overpaid':
            case 'completed':
                $status->title = 'Success';
                $status->description = 'paymentAccepted';

                break;
            case 'expired':
                $status->title = 'Expired';
                $status->description = 'paymentExpired';

                break;
            default:
                $status->title = 'Warning';
                $status->description = 'invalidInvoiceId';

                break;
        }

        return $status;
    }

    /**
     * Return template helpers closures array
     *
     * @return (
        Closure(mixed $key, bool $echo = true): string|void|
        Closure(mixed $date, bool $echo = true): string|false|void|
        Closure(mixed $value, string $style = '', bool $echo = true): string|void)[]
     */
    private static function helpers()
    {
        // Localize callback
        $locales = self::locales();
        $t = static function ($key, $echo = true) use ($locales) {
            if(empty($key)) {
                $result = '';
            }
            else {
                $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                $locale = array_key_exists($locale, $locales) ? $locale : 'en';

                $result = array_key_exists($key, $locales[$locale]) ? $locales[$locale][$key] : $locales['en'][$key];
            }

            if (!$echo) {
                return $result;
            }
            echo $result;
        };

        // Locale date formatter callback
        $fmt = datefmt_create(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2), 3, 2, self::$timeZone, 1);
        $d = static function ($date, $echo = true) use ($fmt) {
            $result = datefmt_format($fmt, new \DateTime($date . 'Z'));

            if (!$echo) {
                return $result;
            }
            echo $result;
        };

        // Copy button
        $c = static function ($value, $style = '', $echo = true) {
            $style = ($style) ? 'style="' . $style . '"' : '';
            $tpl = '<button class="btn__copy hovered"%s><input type="hidden" readonly value="%s"></button>';
            $result = sprintf($tpl, $style, $value);

            if(!$echo) {
                return $result;
            }
            echo $result;
        };

        // Add invoice id to linkback URL
        $l = static function ($url, $id) {
            if (empty($id)) {
                return $url;
            }
            $glue = (parse_url($url, PHP_URL_QUERY)) ? '&' : '?';

            return $url . $glue . 'invoice=' . $id;
        };

        return [$t, $d, $c, $l];
    }

    /**
     * Return locales for template
     *
     * @return string[][]
     */
    private static function locales()
    {
        require(__DIR__ . '/tpl/locales.php');

        return $locales;
    }
}
