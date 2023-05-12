<?php

namespace Apirone\Invoice\Tools;

use Apirone\API\Endpoints\Service;
use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\AbstractModel;
use Apirone\Invoice\Model\Settings;
use Apirone\Invoice\Model\Settings\Currency;
use DateTime;
use IntlDateFormatter;

class Template
{
    private ?Invoice $invoice;

    private string $backlink = '';

    private string $timeZone = 'UTC';

    private bool $qrOnly = false;
    
    private bool $showLogo = true;

    private bool $showEmpty = false;

    private function __construct($invoice = null, $qrOnly = false, $showLogo = true, $backlink = '')
    {
        $this->invoice = $invoice;
        $this->qrOnly = $qrOnly;
        $this->showLogo = $showLogo;
        $this->backlink = $backlink;
    }

    public static function init($invoice = null, $qrOnly = false, $showLogo = true, $backlink = '')
    {
        $class = new static($invoice, $qrOnly, $showLogo, $backlink);

        return $class;
    }

    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function setQrOnly($qrOnly = true)
    {
        $this->qrOnly = $qrOnly;

        return $this;
    }

    public function showLogo($showLogo = true)
    {
        $this->showLogo = $showLogo;

        return $this;
    }

    public function setBacklink($backlink = '')
    {
        $this->backlink = $backlink;

        return $this;
    }

    public function setTimeZone($timeZone = null)
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    public function showEmpty($empty = true)
    {
        $this->showEmpty = $empty;

        return $this;
    }


    public function render()
    {
        include(__DIR__ . '/Locales.php');

        // Localize callback
        $t = static function($key, $echo = true) use ($locales) {
            return static::localize($key, $echo, $locales);
        };
    
        $invoice = $this->invoice->details;
        $userData = $invoice->getUserData();
        $currency = Currency::fromJson(Utils::currency($invoice->getCurrency()));
        $loading = $this->showEmpty;
        $template = !$this->qrOnly ? 'FullView' : 'QrView';
        pa($loading);
        // Draw output
        ob_start();
        include(__DIR__ . '/Views/' . $template . '.php');
        return ob_get_clean();
    }

    public function datefmt($date)
    {
        $fmt = datefmt_create(
            substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2),
            IntlDateFormatter::SHORT,
            IntlDateFormatter::MEDIUM,
            $this->timeZone,
            IntlDateFormatter::GREGORIAN
        );

        return datefmt_format($fmt, new DateTime($date));
    }

    public static function localize($key, $echo, $langs = [])
    {
        $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $locale = array_key_exists($locale, $langs) ? $locale : 'en';

        $current = $langs[$locale];
        $keys = explode('.', $key);
        $result = $key;
        if (array_key_exists($keys[0], $current)) {
            if (is_array($current[$keys[0]]) && array_key_exists($keys[1], $current[$keys[0]])) {
                $result = $current[$keys[0]][$keys[1]];
            }
            else {
                $result = $langs[$locale][$keys[0]];
            }
        }
        if (!$echo)
            return $result;
        echo $result;
    }

    public function toArray()
    {
        $output = [];
        $output['qrOnly'] = $this->qrOnly;
        $output['showLogo'] = $this->showLogo;
        $output['backlink'] = $this->backlink;

        return $output;
    }

    public function toJson()
    {
        return json_decode(json_encode($this->toArray()));
    }

}
