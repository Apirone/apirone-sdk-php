<?php
/**
 * This file is part of the Apirone Invoice library.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Apirone\Invoice\Service;

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Service\Utils;

class Render
{
    private string $dataUrl = '';

    private string $backlink = '';

    private string $timeZone = 'UTC';

    private bool $qrOnly = false;
    
    private bool $logo = true;

    private function __construct($dataUrl = '', $qrOnly = false, $logo = true, $backlink = '')
    {
        $this->dataUrl = $dataUrl;
        $this->qrOnly = $qrOnly;
        $this->logo = $logo;
        $this->backlink = $backlink;
    }

    public static function init($dataUrl = '', $qrOnly = false, $logo = true, $backlink = '')
    {
        $class = new static($dataUrl, $qrOnly, $logo, $backlink);

        return $class;
    }

    public function setQrOnly($qrOnly = true)
    {
        $this->qrOnly = $qrOnly;

        return $this;
    }

    public function showLogo($show = true)
    {
        $this->logo = $show;

        return $this;
    }

    public function setBacklink($backlink = '')
    {
        $this->backlink = $backlink;

        return $this;
    }

    public function setTimeZone($timeZone = 'UTC')
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    public function setTimeZoneByOffset($offset = 0)
    {
        if ($offset == 0 || abs($offset >= 1140)) {
            $tz = 'UTC';
        }
        else {
            $abs = abs($offset);
            $t = sprintf('%02d:%02d', intdiv($abs, 60), fmod($abs, 60));
            $tz = 'GMT' . (($offset < 0) ? '+' : '-') . $t;
        }
        $this->timeZone = $tz;

        return $this;
    }

    public function setDataUrl($dataUrl)
    {
        $this->dataUrl = $dataUrl;

        return $this;
    }
    
    public function showInvoice($invoice, $echo = true)
    {   
        $show = ($invoice instanceof Invoice) ? true : false;
        $loading  = !$show;
        $id = ($show) ? $invoice->invoice : $invoice;

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
            // update invoice if page refreshed manually
            Invoice::getInvoice($id)->update();

            $invoice = $userData = $currency = null;
        }
        $statusLink = $this->dataUrl ? $this->dataUrl : '/';
        $status = $this->statusDescription(($show) ? $invoice->status : $id);
        $template = !$this->qrOnly ? 'full' : 'qr-only';

        // Draw output:
        list($t, $d, $c) = $this->helpers(); // translate, date, copy
        ob_start();
        include(__DIR__ . '/../Templates/' . $template . '.php');
        if (!$echo)
            return ob_get_clean();
        echo ob_get_clean();
    }

    private function statusDescription($ivoiceStatus)
    {
        $status = new \stdClass;
        switch ($ivoiceStatus) {
            case 'created':
            case 'partpaid':
            case 'paid':
            case 'overpaid':
                $status->title = 'Refresh';
                $status->description = 'waitingForPayment';
                break;
            case 'success':
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
    private function helpers()
    {
        // Localize callback
        $locales = $this->locales();
        $t = static function($key, $echo = true) use ($locales) {
            $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $locale = array_key_exists($locale, $locales) ? $locale : 'en';

            $result = array_key_exists($key, $locales[$locale]) ? $locales[$locale][$key] : $key;

            if (!$echo) {
                return $result;
            }
            echo $result;
        };

        // Locale date formatter callback
        $fmt = datefmt_create( substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2), 3, 2, $this->timeZone, 1);
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

    private function locales()
    {
        require(__DIR__ . '/../Templates/locales.php');

        return $locales;    
    }
}
