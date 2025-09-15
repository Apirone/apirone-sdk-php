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
use Apirone\API\Http\Request;
use Apirone\Lib\PhpQRCode\QRCode;
use Apirone\SDK\Model\Settings\Currency;

class Utils
{
    public const FROM = '?from=apirone';

    public const THRESHOLD = 1.0E-8;

    public const SUFFIX = '0000000000';

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
        // Explorer switch
        switch ($currency->abbr) {
            case (substr_count($currency->abbr, 'trx') > 0 ):
                $explorer = $currency->isTestnet() ? 'shasta.tronscan.org' : 'tronscan.org';
                $path = implode('/', ['#', $type, $hash]);
                break;
            case (substr_count($currency->abbr, 'eth') > 0 ):
                $explorer = $currency->isTestnet() ? 'sepolia.etherscan.io' : 'etherscan.io';
                $type = ($type == 'transaction') ? 'tx' : $type;
                $path = implode('/', [$type, $hash]);
                break;
            case (substr_count($currency->abbr, 'bnb') > 0 ):
                $explorer = $currency->isTestnet() ? 'testnet.bscscan.com' : 'bscscan.com';
                $type = ($type == 'transaction') ? 'tx' : $type;
                $path = implode('/', [$type, $hash]);
                break;
            case 'btc':
                $explorer = 'explorer.apirone.com';
                $type = ($type == 'transaction') ? 'tx' : $type;
                $path = implode('/', [$type, $hash]);
                break;
            default:
                $explorer = 'blockchair.com';
                $currencyName = strtolower(str_replace([' ', '(', ')'], ['-', '/', ''], $currency->name));
                $from = '?from=apirone';
                if ($currency->abbr == 'tbtc') {
                    $currencyName = 'bitcoin/testnet';
                    $from = '';
                }
                $path = implode('/', [$currencyName, $type, $hash . $from]);
                break;
        }

        $href = sprintf('https://%s/%s', ...[$explorer, $path]);

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
     * Return masked transaction hash
     *
     * @param mixed $hash
     * @return string
     */
    public static function maskTransactionHash($hash, $size = 8)
    {
        return substr($hash, 0, $size) . '.....' . substr($hash, -$size);
    }

    public static function estimate($account, $amount, $fiat, $currencies)
    {
        $path = sprintf('v2/accounts/%s/fwd-fee', $account);
        $options['amount'] = $amount;
        $options['fiat'] = $fiat;
        $options['currencies'] = (is_array($currencies) ? implode(',', $currencies) : $currencies);

        return Request::get($path, $options);
    }

    /**
     * Convert to decimal and trim trailing zeros if $zeroTrim set true
     *
     * @param int $amount
     * @param Currency $currency
     * @param bool $zeroTrim (optional)
     * @return string
     */
    public static function humanizeAmount($amount, $currency, $zeroTrim = true)
    {
        $amount = Utils::min2cur($amount, $currency->unitsFactor);
        $suffix = ($currency->unitsFactor < static::THRESHOLD) ? static::SUFFIX : '';
        if($currency->isStablecoin()) {
            $decimals = 2;
        }
        else {
            $unitsFactor = ($currency->unitsFactor < static::THRESHOLD) ? static::THRESHOLD : $currency->unitsFactor;
            $decimals = substr((string) $unitsFactor, strpos((string) $unitsFactor, "-") + 1);
        }

        $amount = sprintf('%.' . $decimals . 'f', floatval($amount)) . $suffix;

        if ($zeroTrim) {
            return rtrim(rtrim($amount, '0'), '.');
        }

        return $amount;
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
        if ($unitsFactor < static::THRESHOLD) {
            return number_format(floatval($value / static::THRESHOLD), 0, '.', '') . static::SUFFIX;
        }

        return number_format(floatval($value / $unitsFactor), 0, '.', '');
    }

    /**
     * Convert fiat amount to crypto
     *
     * @param mixed $fiatAmount
     * @param mixed $fiatCode
     * @param mixed $crypto
     * @return float
     * @throws \Apirone\API\Exceptions\RuntimeException
     * @throws \Apirone\API\Exceptions\ValidationFailedException
     * @throws \Apirone\API\Exceptions\UnauthorizedException
     * @throws \Apirone\API\Exceptions\ForbiddenException
     * @throws \Apirone\API\Exceptions\NotFoundException
     * @throws \Apirone\API\Exceptions\MethodNotAllowedException
     */
    public static function fiat2crypto(float $fiatAmount, string $fiatCode, $currency): float
    {
        // Fallback for support currency abbr
        if (gettype($currency) == 'string') {
            $json = Utils::currency(strtolower($currency));
            $currency = Currency::init($json);
        }
        $fiatCode = strtolower($fiatCode);

        $result = static::fiat2cryptos($fiatAmount, $fiatCode, [$currency]);

        if ($result) {
            return (float)$result[$currency->abbr];
        }

        return $result;
    }

    /**
     * Converts fiat amount to crypto for array of cryptos
     *
     * @param float $fiatAmount
     * @param string $fiatCode
     * @param Currency[] $currencies
     * @return array<mixed, int|float>
     * @throws \Apirone\API\Exceptions\RuntimeException
     * @throws \Apirone\API\Exceptions\ValidationFailedException
     * @throws \Apirone\API\Exceptions\UnauthorizedException
     * @throws \Apirone\API\Exceptions\ForbiddenException
     * @throws \Apirone\API\Exceptions\NotFoundException
     * @throws \Apirone\API\Exceptions\MethodNotAllowedException
     */
    public static function fiat2cryptos(float $fiatAmount, string $fiatCode, array $currencies)
    {
        $fiatCode = strtolower($fiatCode);
        $to = [];
        $currencies = array_values($currencies);
        foreach ($currencies as $currency) {
            $to[] = $currency->abbr;
        }
        $rates = Service::ticker(implode(',', $to), $fiatCode);
        $amounts = [];

        if (property_exists($rates, $fiatCode)) {
            $amount =  floatval($fiatAmount / (float) $rates->$fiatCode);
            $decimals = substr((string)$currencies[0]->unitsFactor, strpos((string)$currencies[0]->unitsFactor, "-") + 1);
            $amounts[$currencies[0]->abbr] = floatval(sprintf('%.' . $decimals . 'f', $amount));
        }
        else {
            foreach ($currencies as $currency) {
                if (property_exists($rates, $currency->abbr)) {
                    $amount =  floatval($fiatAmount / (float) $rates->{$currency->abbr}->$fiatCode);
                    $decimals = substr((string)$currency->unitsFactor, strpos((string)$currency->unitsFactor, "-") + 1);
                    $amounts[$currency->abbr] = floatval(sprintf('%.' . $decimals . 'f', $amount));
                }
            }
        }

        return $amounts;
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
    public static function sendJson($data, $code = 200)
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
