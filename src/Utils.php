<?php

namespace Apirone\Invoice;

use Apirone\API\Endpoints\Service;

class Utils
{
    public static function currency(string $currency) {
        $info = Service::account();
        foreach($info->currencies as $item) {
            if ($item->abbr == $currency) {
                return $item;
            }
        }
        return null;
    }
    /**
     * Returnt transaction link to blockchair.com
     * 
     * @param mixed $currency
     * @return string 
     */
    public static function getTransactionLink($currency, $transaction = '') {
        if ($currency->abbr == 'tbtc') 
            return 'https://blockchair.com/bitcoin/testnet/transaction/' . $transaction;
        
        return sprintf('https://blockchair.com/%s/transactions/', strtolower(str_replace([' ', '(', ')'], ['-', '/', ''],  $currency->name))) . $transaction;
    }

    /**
     * Returnt transaction link to blockchair.com
     * 
     * @param mixed $currency
     * @return string 
     */
    public static function getAddressLink($currency, $address = '') {
        if ($currency->abbr == 'tbtc') 
            return 'https://blockchair.com/bitcoin/testnet/address/' . $address;
        
        return sprintf('https://blockchair.com/%s/address/', strtolower(str_replace([' ', '(', ')'], ['-', '/', ''],  $currency->name))) . $address;
    }

    /**
     * Return img tag with QR-code link
     * 
     * @param mixed $currency 
     * @param mixed $input_address 
     * @param mixed $remains 
     * @return void 
     */
    public static function getQrLink($currency, $input_address, $remains) {
        $prefix = (substr_count($input_address, ':') > 0 ) ? '' : strtolower(str_replace([' ', '(', ')'], ['-', '', ''],  $currency->name)) . ':';

        return 'https://chart.googleapis.com/chart?chs=225x225&cht=qr&chl=' . urlencode($prefix . $input_address . "?amount=" . $remains);
    }

    /**
     * Return masked transaction hash
     * 
     * @param mixed $hash 
     * @return string 
     */
    public static function maskTransactionHash ($hash, $size = 8) {
        return substr($hash, 0, $size) . '......' . substr($hash, -$size);
    }

    /**
     * Convert to decimal and trim trailing zeros if $zeroTrim set true
     * 
     * @param mixed $value 
     * @param bool $zeroTrim (optional)
     * @return string 
     */
    public static function exp2dec($value, $zeroTrim = false) {
        if ($zeroTrim)
            return rtrim(rtrim(sprintf('%.8f', floatval($value)), 0), '.');
        
        return sprintf('%.8f', floatval($value));
    }

    public static function min2cur($value, $unitsFactor) {
        return $value * $unitsFactor;
    }

    public static function cur2min($value, $unitsFactor) {
        return $value / $unitsFactor;
    }

    /**
     * Convert fiat value to cripto by request to apirone api
     * 
     * @param mixed $value 
     * @param string $from 
     * @param string $to 
     * @return mixed 
     */
    public static function fiat2crypto($value, $from='usd', $to = 'btc') {
        if ($from == 'btc') {
            return $value;
        }

        $endpoint = '/v1/to' . strtolower($to);
        $params = array(
            'currency' => trim(strtolower($from)),
            'value' => trim(strtolower($value))
        );
        $result = Request::execute('get', $endpoint, $params );

        if (Request::isResponseError($result)) {
            Log::debug($result);
            return false;
        }
        else
            return (float) $result;
    }

    /**
     * Check is fiat supported
     * 
     * @param mixed $fiat string
     * @return bool 
     */
    public static function isFiatSupported($fiat) {
        $supported_currencies = self::ticker();
        if (!$supported_currencies) {
            return false;
        }
        if(property_exists($supported_currencies, strtolower($fiat))) {
            return true;
        }
        return false;

    }

        /**
     * @param string $date DateTime string
     *
     * @return string
     */
    public static function convertToIso8601(string $date): string
    {
        $date = new \DateTime($date);
        $date->setTimezone(new \DateTimeZone(\date_default_timezone_get()));

        return $date->format(\DateTime::ATOM);
    }
}
