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
}
