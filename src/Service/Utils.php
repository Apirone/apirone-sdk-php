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
use Apirone\API\Exceptions\RuntimeException;
use Apirone\API\Exceptions\ValidationFailedException;
use Apirone\API\Exceptions\UnauthorizedException;
use Apirone\API\Exceptions\ForbiddenException;
use Apirone\API\Exceptions\NotFoundException;
use Apirone\API\Exceptions\MethodNotAllowedException;
use Apirone\Lib\PhpQRCode\QRCode;
use Apirone\SDK\Model\Settings\Currency;

class Utils
{
    public const FROM = '?from=apirone';

    /**
     * Get apirone currency by abbreviation
     *
     * @param string $currency
     * @return mixed
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public static function currency(string $currency)
    {
        $info = Service::account();
        foreach($info->currencies as $item) {
            if ($item->abbr == $currency) {
                return $item;
            }
        }

        return null;
    }

    public static function getExplorerHref(Currency $currency, $type, $hash = '')
    {
        $explorer = 'blockchair.com';
        $currencyName = strtolower(str_replace([' ', '(', ')'], ['-', '/', ''], $currency->getName()));
        $from = '?from=apirone';
        if ($currency->getAbbr() == 'tbtc') {
            $currencyName = 'bitcoin/testnet';
            $from = '';
        }

        if (substr_count($currency->getAbbr(), 'trx') > 0 ){
            $explorer = $currency->isTestnet() ? 'shasta.tronscan.org' : 'tronscan.org';
            $currencyName = '#';
        }

        $href = sprintf('https://%s/%s/%s/%s', ...[$explorer, $currencyName, $type, $hash . $from]);

        return $href;
    }

    /**
     * Return transaction link to blockchair.com
     *
     * @param mixed $currency
     * @return string
     */
    public static function getTransactionLink($currency, $hash = '')
    {
        return self::getExplorerHref($currency, 'transaction', $hash);
    }

    /**
     * Return transaction link to blockchair.com
     *
     * @param mixed $currency
     * @return string
     */
    public static function getAddressLink($currency, $hash = '')
    {
        return self::getExplorerHref($currency, 'address', $hash);
    }

    /**
     * Return chart.googleapis.com QR-code link
     *
     * @param mixed $currency
     * @param mixed $input_address
     * @param mixed $amount
     * @return string
     */
    public static function getQrLink($currency, $input_address, $amount = null)
    {
        $prefix = (substr_count($input_address, ':') > 0) ? '' : strtolower(str_replace([' ', '(', ')'], ['-', '', ''], $currency->name)) . ':';
        $amount = ($amount !== null && $amount > 0) ? '?amount=' . $amount : '';

        return 'https://chart.googleapis.com/chart?chs=256x256&cht=qr&chld=H|0&chl=' . urlencode($prefix . $input_address . $amount);
    }

    /**
     * Return base64 encoded QR png
     *
     * @param mixed $currency
     * @param mixed $input_address
     * @param mixed $amount
     * @return string
     */
    public static function renderQr($currency, $input_address, $amount = null)
    {
        $prefix = (substr_count($input_address, ':') > 0) ? '' : strtolower(str_replace([' ', '(', ')'], ['-', '', ''], $currency->name)) . ':';
        $amount = ($amount !== null && $amount > 0) ? '?amount=' . $amount : '';

        return QRCode::init()->data($prefix . $input_address . $amount)->levelHigh()->quietZone(0)->base64();
    }

    /**
     * Return masked transaction hash
     *
     * @param mixed $hash
     * @return string
     */
    public static function maskTransactionHash($hash, $size = 8)
    {
        return substr($hash, 0, $size) . '......' . substr($hash, -$size);
    }

    /**
     * Convert to decimal and trim trailing zeros if $zeroTrim set true
     *
     * @param mixed $value
     * @param bool $zeroTrim (optional)
     * @return string
     */
    public static function exp2dec($value, $zeroTrim = false)
    {
        if ($zeroTrim) {
            return rtrim(rtrim(sprintf('%.8f', floatval($value)), 0), '.');
        }

        return sprintf('%.8f', floatval($value));
    }

    /**
     * Minor currency value to major
     *
     * @param mixed $value
     * @param mixed $unitsFactor
     * @return int|float
     */
    public static function min2cur($value, $unitsFactor)
    {
        return $value * $unitsFactor;
    }

    /**
     * Major currency value to minor
     *
     * @param mixed $value
     * @param mixed $unitsFactor
     * @return int
     */
    public static function cur2min($value, $unitsFactor)
    {
        return (int) round($value / $unitsFactor);
    }

    /**
     * Convert fiat value to crypto by request to apirone api
     *
     * @param mixed $value
     * @param string $from
     * @param string $to
     * @return mixed
     */
    public static function fiat2crypto($value, $from, $to)
    {
        return Service::fiat2crypto($value, $from, $to);
    }

    /**
     * Check is fiat supported by apirone
     *
     * @param mixed $fiat string
     * @return bool
     */
    public static function isFiatSupported($fiat)
    {
        $supported_currencies = Service::ticker();
        if (!$supported_currencies) {
            return false;
        }
        if(property_exists($supported_currencies, strtolower($fiat))) {
            return true;
        }

        return false;
    }

    /**
     * Sanitize text input to prevent XSS & SQL injection
     *
     * @param mixed $string
     * @return mixed
     */
    public static function sanitize($string)
    {
        if (is_object($string) || is_array($string)) {
            return '';
        }

        $string = trim(strip_tags($string));
        $string = preg_replace('/[\r\n\t ]+/', ' ', $string);

        $found = false;
        while (preg_match('/%[a-f0-9]{2}/i', $string, $match)) {
            $string = str_replace($match[0], '', $string);
            $found    = true;
        }

        if ($found) {
            $string = trim(preg_replace('/ +/', ' ', $string));
        }

        return $string;
    }

    /**
     * Send JSON response
     *
     * @param mixed $data
     * @param int $code
     * @return false|void
     */
    public static function send_json($data, $code = 200)
    {
        if (headers_sent()) {
            return false;
        }

        http_response_code($code);
        $json = json_encode($data);
        if ($json === false) {
            // Avoid echo of empty string (which is invalid JSON), and
            // JSONify the error message instead:
            $json = json_encode(["jsonError" => json_last_error_msg()]);
            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError":"unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo $json;
    }
}
