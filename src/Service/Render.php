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
use ReflectionClass;
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
     * URL for ajax request to update invoice data
     *
     * @var string
     */
    public static string $dataUrl = '';

    /**
     * Invoice backlink to store
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
     * Absolute path to the custom template file.
     * @var string
     */
    public static string $template = '';

    /**
     * Absolute path to the custom locales file.
     * @var string
     */
    public static string $locales = '';

    private function __construct() {}

    /**
     * Magic method for getting values of a private props by its name
     *
     * @param string $name 
     * @return mixed 
     */
    public function __get($name)
    {
        if ($name == 'locales') {
            return self::getLocales();
        }
        if (\property_exists($this, $name)) {
            $class = new \ReflectionClass(static::class);
            
            return $class->getStaticProperties()[$name];
        }

        $trace = \debug_backtrace();
        \trigger_error(
            'Undefined property ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            \E_USER_ERROR
        );
    }

    /**
     * Returns an instance of a class for customizing static props using arrow function syntax.
     *
     * @return Render
     */
    public static function init()
    {
        return new static();
    }

    public static function fromJson($json)
    {
        $render =  new static();

            $json = gettype($json) == 'string' ? json_decode($json) : $json;
            $class = new \ReflectionClass(static::class);

            foreach ($class->getStaticProperties() as $key => $val) {
                if (property_exists($json, $key)) {
                    $render->$key($json->$key);
                }
            }

        return $render;
    }

    /**
     * Restore paremeters from file
     *
     * @param mixed $abspath 
     * @return static|null 
     */
    public static function fromFile($abspath)
    {
        $json = @file_get_contents($abspath);

        if ($json) {
            return static::fromJson($json);
        }

        return null;
    }

    /**
     * 
     * @return \stdClass 
     */
    public static function toJson()
    {
        $class = new \ReflectionClass(static::class);
        $json = new \stdClass;
        foreach ($class->getStaticProperties() as $key => $val) {
            $json->$key = $val;
        }

        return $json;
    }

    /**
     * Save paremeters to file
     *
     * @param string $abspath
     * @param string $filename
     * @return bool
     */
    public function toFile($abspath)
    {
        if (file_put_contents($abspath, json_encode($this->toJson(), JSON_PRETTY_PRINT))) {
            return true;
        }

        return false;
    }

    /**
     * Convert class data to json string
     *
     * @param int $flag - second param for json_encode. For example - JSON_PRETTY_PRINT or 128
     * @return string
     */
    public function toJsonString($flag = 0): string
    {
        return json_encode(Render::toJson(), $flag);
    }

    public function idParam($param = "invoice")
    {
        $this::$idParam = $param;

        return $this;
    }

    public function dataUrl($url = "")
    {
        $this::$dataUrl = $url;

        return $this;
    }

    public function backlink($url = "")
    {
        $this::$backlink = $url;

        return $this;
    }

    public function timeZone($tz): self
    {
        $this::$timeZone = 'UTC';
        if(preg_match('/(^GMT[+-][0-9]{2}:[0-9]{2}\b$)|(^UTC)/', $tz)) {
            $this::$timeZone = $tz;
        }

        return $this;
    }

    /**
     * Setting the $timeZone by local time zone with UTC offset
     *
     * @param int $offset
     * @return void
     * @throws DivisionByZeroError
     * @throws ArithmeticError
     */
    public static function timeZoneByOffset(int $offset = 0)
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

    public function qrOnly($qrOnly = false)
    {
        $this::$qrOnly = $qrOnly;

        return $this;
    }

    public function logo($logo = true)
    {
        $this::$logo = $logo;

        return $this;
    }

    public function template($absolutePath = '')
    {
        $this::$template = $absolutePath;

        return $this;
    }

    public function locales($absolutePth = '')
    {
        self::$locales = $absolutePth;

        return $this;
    }

    /**
     * Setting the $timeZone by local time zone with UTC offset
     *
     * @param int $offset
     * @return void
     * @throws DivisionByZeroError
     * @throws ArithmeticError
     * @deprecated Use timezoneByOffset()
     */
    public static function setTimeZoneByOffset(int $offset = 0)
    {
        self::timeZoneByOffset($offset);
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
                $amount = Utils::humanizeAmount($amount, $currency);

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

        // Set template
        if (!empty(self::$template) && file_exists(self::$template)) {
            $template = self::$template;
        }
        else {
            $name = !self::$qrOnly ? 'full' : 'qr-only';
            $template = __DIR__ . '/tpl/' . $name . '.php';
        }

        // Draw output:
        [$t, $d, $c, $l] = self::helpers(); // translate, date, copy, link
        ob_start();
        include($template);

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
        $locales = self::getLocales();
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
     * Return locales for template. Sets custom locales via locales array.
     * 
     * @param array $custom 
     * @return array 
     */
    public static function getLocales(array $custom = [])
    {
        $default = require(__DIR__ . '/tpl/locales.php');
        if (empty(self::$locales)) {
            $result = $default;
        }
        if (empty($custom)) {
            $custom = require(self::$locales);
        }

        $fallback['en'] = $default['en'];
        $result = array_replace_recursive($fallback, $custom);

        return $result;
    }
}
