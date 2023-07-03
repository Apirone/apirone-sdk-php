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
use DivisionByZeroError;
use ArithmeticError;
use Closure;
use stdClass;

class Render
{
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
        if ($offset == 0 || abs($offset >= 1140)) {
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
    public static function show(Invoice $invoice)
    {
        $show = ($invoice->invoice) ? true : false;
        $loading  = !$show;
        $id = $invoice->invoice;
        $status = self::statusDescription($invoice);
        $statusLink = self::$dataUrl ? self::$dataUrl : '/';
        $backlink = Invoice::$settings->getBacklink();
        $template = !self::$qrOnly ? 'full' : 'qr-only';

        if ($show) {
            $invoice = $invoice->details;
            $userData = $invoice->getUserData();
            $currency = Invoice::$settings->getCurrency($invoice->getCurrency());
            $statusNum = $invoice->statusNum();
            if ($invoice->amount !== null) {
                $remains = $invoice->amount;
                foreach ($invoice->history as $item) {
                    if (property_exists($item, 'amount')) {
                        $remains = $remains - $item->amount;
                    }
                }
                $amount = ($remains <= 0) ? $invoice->amount : $remains;
                $amount = Utils::exp2dec($amount * $currency->getUnitsFactor());
            }
            else {
                $amount = null;
            }
        }
        else {
            $invoice = $userData = $currency = null;
            $loading = true;
        }

        // Draw output:
        list($t, $d, $c) = self::helpers(); // translate, date, copy
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
    private function statusDescription(Invoice $invoice): \stdClass
    {

        $status = new \stdClass;
        switch ($invoice->status) {
            case 'created':
            case 'partpaid':
            case 'paid':
            case 'overpaid':
                $status->title = 'Refresh';
                $status->description = 'waitingForPayment';
                break;
            case 'completed':
                $status->title = 'Success';
                $status->description = 'paymentAccepted';
                break;
            case 'expired':
                $status->title = 'Expired';
                $status->description = 'paymentExpired';
                break;
            case null:
                $status->title = 'Warning';
                $status->description = 'invalidInvoiceId';
                break;
            default:
                $status->title = 'Loading';
                $status->description = '';
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
        $t = static function($key, $echo = true) use ($locales) {
            $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $locale = array_key_exists($locale, $locales) ? $locale : 'en';

            $result = array_key_exists($key, $locales[$locale]) ? $locales[$locale][$key] : $locales['en'][$key];

            if (!$echo) {
                return $result;
            }
            echo $result;
        };

        // Locale date formatter callback
        $fmt = datefmt_create( substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2), 3, 2, self::$timeZone, 1);
        $d = static function($date, $echo = true) use ($fmt) {
            $result = datefmt_format($fmt, new \DateTime($date));

            if (!$echo) {
                return $result;
            }
            echo $result;
        };

        // Copy button
        $c = static function($value, $style = '', $echo = true) {
            $style = ($style) ? 'style="' . $style . '"' : '';
            $tpl = '<button class="btn__copy hovered"%s><input type="hidden" readonly value="%s"></button>';
            $result = sprintf($tpl, $style, $value);
            
            if(!$echo) {
                return $result;
            }
            echo $result;
        };

        return [$t, $d, $c];    
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
