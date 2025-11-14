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
use stdClass;

class Utils
{
    public const THRESHOLD = 1.0E-8;

    public const SUFFIX = '0000000000';

    public static array $cryptos = [];

    public static function loadCryptos()
    {
        $info = Service::account();
        $cryptos = [];

        foreach ($info->currencies as $item) {
            $crypto = new \stdClass;

            $crypto->abbr = $item->abbr;
            $crypto->name = $item->name;
            $crypto->alias = Utils::getAlias($item->abbr,$item->name);
            $crypto->unitsFactor = $item->{'units-factor'};
            $parts = Utils::getNetworkAndToken($item->abbr);
            $crypto->network = $parts->network;
            $crypto->token = $parts->token;
            $crypto->test = Utils::isTestnet($item->abbr);

            static::$cryptos[$item->abbr] = $crypto;
        }

        return static::$cryptos;
    }

    /**
     * Get cryptocurrency parameters by abbreviation
     *
     * @param string $abbr
     * @return mixed
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public static function getCrypto(string $abbr)
    {
        // Loading cryptocurrency mapping prod api on first launch
        if (static::$cryptos == null) {
            require_once(__DIR__ . '/cryptos.php');
            static::$cryptos = (array) json_decode($cryptos);
        }

        if (!array_key_exists($abbr, static::$cryptos)) {
            Utils::loadCryptos();
        }
        if (array_key_exists($abbr, static::$cryptos)) {
            if (!property_exists(static::$cryptos[$abbr], 'alias')) {
                $parts = Utils::getNetworkAndToken($abbr);

                static::$cryptos[$abbr]->abbr = $abbr;
                static::$cryptos[$abbr]->alias = Utils::getAlias($abbr, static::$cryptos[$abbr]->name);
                static::$cryptos[$abbr]->network = $parts->network;
                static::$cryptos[$abbr]->token = $parts->token;
                static::$cryptos[$abbr]->test = Utils::isTestnet($abbr);
            }

            return static::$cryptos[$abbr];
        }

        $parts = Utils::getNetworkAndToken($abbr);

        $crypto = new stdClass;
        $crypto->name =  ucfirst($abbr);
        $crypto->unitsFactor = 1.0E-8;
        $crypto->abbr = $abbr;
        $crypto->alias = $abbr;
        $crypto->network = $parts->network;
        $crypto->token = $parts->token;
        $crypto->testnet = true;

        return static::$cryptos[$abbr] = $crypto;
    }

    public static function getExplorerHref($abbr, $type, $hash = '')
    {
        $crypto = static::getCrypto($abbr);
        // Explorer switch
        switch ($crypto->abbr) {
            case (substr_count($crypto->abbr, 'trx') > 0 ):
                $explorer = $crypto->isTestnet() ? 'shasta.tronscan.org' : 'tronscan.org';
                $path = implode('/', ['#', $type, $hash]);
                break;
            case (substr_count($crypto->abbr, 'eth') > 0 ):
                $explorer = $crypto->isTestnet() ? 'sepolia.etherscan.io' : 'etherscan.io';
                $type = ($type == 'transaction') ? 'tx' : $type;
                $path = implode('/', [$type, $hash]);
                break;
            case (substr_count($crypto->abbr, 'bnb') > 0 ):
                $explorer = $crypto->isTestnet() ? 'testnet.bscscan.com' : 'bscscan.com';
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
                $currencyName = strtolower(str_replace([' ', '(', ')'], ['-', '/', ''], $crypto->name));
                $from = '?from=apirone';
                if ($crypto->abbr == 'tbtc') {
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
     * @param string $abbr
     * @return string
     */
    public static function getTransactionLink($abbr, $hash = '')
    {
        return self::getExplorerHref($abbr, 'transaction', $hash);
    }

    /**
     * Return transaction link to blockchair.com
     *
     * @param string $abbr
     * @return string
     */
    public static function getAddressLink($abbr, $hash = '')
    {
        return self::getExplorerHref($abbr, 'address', $hash);
    }

    /**
     * Make currency alias by abbr & name
     *
     * @param string $abbr
     * @param mixed $name
     * @return mixed
     */
    public static function getAlias(string $abbr, $name)
    {
        $parts = Utils::getNetworkAndToken($abbr);

        if ($parts->token) {
            preg_match('#\((.*?)\)#', $name, $match);
            $suffix = count($match) > 0 ? $match[1] : '';

            $format = Utils::isTestnet($abbr) ? '%s (%s - testnet)' : '%s (%s)';

            return strtoupper(sprintf($format, $parts->token, $suffix));
        }

        return $name;
    }

    /**
     * Determine currency network & token by abbr
     *
     * @param string $abbr
     * @return stdClass
     */
    public static function getNetworkAndToken(string $abbr)
    {
        $parts = explode('@', $abbr, 2);
        $class = new stdClass;
        $class->network = count($parts) == 1 ? $parts[0] : $parts[1];
        $class->token = count($parts) == 2 ? $parts[0] : null;

        return $class;
    }


    public static function estimate($account, $amount, $fiat, $currencies, $fee = false, $factor = 1)
    {
        $path = sprintf('v2/accounts/%s/tocrypto', $account);
        $options['amount'] = $amount;
        $options['fiat'] = $fiat;
        $options['currencies'] = (is_array($currencies) ? implode(',', $currencies) : $currencies);

        if ($fee !== false) {
            $options['fee'] = (bool) $fee;
        }
        if ($factor !== 1) {
            $options['factor'] = $factor;
        }

        return Request::get($path, $options);
    }

    /**
     * Convert to decimal and trim trailing zeros if $zeroTrim set true
     *
     * @param int $amount
     * @param string $abbr
     * @param bool $zeroTrim (optional)
     * @return string
     */
    public static function humanizeAmount($amount, $abbr, $zeroTrim = true)
    {
        $crypto = Utils::getCrypto($abbr);

        $amount = Utils::min2cur($amount, $crypto->unitsFactor);
        $suffix = ($crypto->unitsFactor < static::THRESHOLD) ? static::SUFFIX : '';
        if($crypto->isStablecoin()) {
            $decimals = 2;
        }
        else {
            $unitsFactor = ($crypto->unitsFactor < static::THRESHOLD) ? static::THRESHOLD : $crypto->unitsFactor;
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
     * Check is fiat supported by apirone
     *
     * @param string $fiat
     * @return bool
     */
    public static function isFiatSupported(string $fiat)
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
     * Returns whether the currency is a stablecoin
     *
     * @return bool
     */
    public static function isStablecoin(string $abbr)
    {
        return substr_count(strtolower($abbr), 'usd') > 0 ? true : false;
    }

    /**
     * Checks if a cryptocurrency is a testnet
     * @param string $abbr
     * @return bool
     */
    public static function isTestnet(string $abbr)
    {
        $parts = explode('@', $abbr, 2);
        $network = count($parts) == 1 ? $parts[0] : $parts[1];

        return (substr($network, 0, 1) == 't' && strlen($network) > 3) ? true : false;
    }

    /**
     * Sanitize text input to prevent XSS & SQL injection
     *
     * @param mixed $string
     * @return mixed
     */
    public static function sanitize($string = '')
    {

        if (empty($string) || is_object($string) || is_array($string)) {
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

    /**
     * Send exception method & code as JSON
     *
     * @param mixed $e
     * @return never
     */
    public static function sendException($e)
    {
        $json = json_decode(sprintf('{"message": "%s"}', $e->getMessage()));
        $code = $e->getCode();

        Utils::sendJson($json, $code ? $code : 503);
        exit;
    }
}
