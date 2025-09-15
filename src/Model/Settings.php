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

namespace Apirone\SDK\Model;

use Apirone\API\Endpoints\Account;
use Apirone\API\Endpoints\Service;
use Apirone\API\Exceptions\RuntimeException;
use Apirone\API\Exceptions\ValidationFailedException;
use Apirone\API\Exceptions\UnauthorizedException;
use Apirone\API\Exceptions\ForbiddenException;
use Apirone\API\Exceptions\NotFoundException;
use Apirone\API\Exceptions\MethodNotAllowedException;
use Apirone\SDK\Model\Settings\Coin;
use Apirone\SDK\Model\Settings\Currency;
use Apirone\SDK\Model\Settings\Network;
use ReflectionException;
use stdClass;

/**
 * @property-read string $account
 * @property-read string $transferKey
 * @property-read array  $currencies
 * @property-read array  $networks
 * @property-read stdClass $meta
 */
class Settings extends AbstractModel
{
    /**
     * Invoice account
     *
     * @var null|string
     */
    private ?string $account = null;

    /**
     * Account transfer key
     *
     * @var null|string
     */
    private ?string $transferKey = null;

    /**
     * Currencies
     *
     * @var array
     */
    private array   $currencies = [];

    private array   $coins = [];

    /**
     * Metadata
     *
     * @var stdClass
     */
    private stdClass   $meta;

    private function __construct()
    {
        $this->extra = new \stdClass();
        $this->meta = new \stdClass();
    }

    public function __get($name) {

        if ($name == 'networks') {
            return $this->networks();
        }

        // Currencies lazy loading
        if ($name == 'currencies') {
            if(empty($this->currencies)) {
                $this->loadCurrencies();
            }
            return $this->currencies;
        }

        return parent::__get($name);
    }

    /**
     * Create instance
     *
     * @return self
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws ReflectionException
     */
    public static function init()
    {
        $class = new static();

        return $class;
    }

    /**
     * Restore settings from JSON
     *
     * @param mixed $json
     * Get the value of transferKey
     * @return self
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public static function fromJson($json)
    {
        $class = new static();
        $class->classLoader($json);

        return $class;
    }

    /**
     * Restore settings from file
     *
     * @param mixed $abspath
     * @return self|null
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
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
     * Create instance for existing apirone account
     *
     * @param mixed $account
     * @param mixed $transferKey
     * @return self
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws ReflectionException
     */
    public static function fromExistingAccount($account, $transferKey)
    {
        $class = new static();

        $class->account = $account;
        $class->transferKey = $transferKey;
        $class->loadCurrencies();

        return $class;
    }

    /**
     * Save settings to file
     *
     * @param string $abspath
     * @param string $filename
     * @return bool
     */
    public function toFile($absFilePath, $flag = JSON_PRETTY_PRINT)
    {
        if (file_put_contents($absFilePath, json_encode($this->toJson(), $flag))) {
            return true;
        }

        return false;
    }

    /**
     * Convert instance to array and skip currency property
     *
     * @return array
     */
    public function toArray(): array
    {
        $settings = parent::toArray();

        if(empty($settings['extra'])) {
            unset($settings['extra']);
        }

        if(empty($settings['meta'])) {
            unset($settings['meta']);
        }

        foreach ($settings as $key => $val) {
            if (!in_array($key, ['account', 'transfer-key', 'coins', 'meta'])) {
                unset($settings[$key]);
            }
        }
        return $settings;
    }

    protected function classLoader($json)
    {
        return parent::classLoader($json);
    }

    /**
     * Create a new apirone account
     *
     * @param bool $renew
     * @return self
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public function createAccount($renew = false)
    {
        if ($renew == false && isset($this->account)) {
            return $this;
        }
        $account = Account::create();

        if ($account) {
            $this->account = $account->account;
            $this->transferKey = $account->{'transfer-key'};
            if ($renew) {
                $this->saveCurrencies();
            }
        }

        return $this;
    }

    /**
     * Load currencies from an apirone service
     *
     * @return self
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws ReflectionException
     */
    public function loadCurrencies()
    {
        $serviceInfo = Service::account();
        $accountInfo = ($this->account) ? Account::init($this->account)->info()->info : [];

        $this->currencies = [];
        foreach($serviceInfo->currencies as $serviceItem) {
            $currency = Currency::init($serviceItem);
            foreach ($accountInfo as $accountItem) {
                if ($accountItem->currency !== $serviceItem->abbr) {
                    continue;
                }
                $address = ($accountItem->destinations !== null) ? $accountItem->destinations[0]->address : null;
                $currency->address($address);
                $currency->policy($accountItem->{'processing-fee-policy'});
            }
            $this->currencies[$serviceItem->abbr] = $currency;
        }

        return $this;
    }

    /**
     * Save currencies into apirone account
     *
     * @return self
     */
    public function saveCurrencies()
    {
        foreach ($this->currencies as $currency) {
            $currency->saveSettings($this->account, $this->transferKey);
        }

        return $this;
    }

    /**
     * Get currency object by it abbreviation
     *
     * @param mixed $abbr
     * @return Currency | false
     */
    public function currency($abbr)
    {
        if (empty($this->currencies)) {
            $this->loadCurrencies();
        }

        return array_key_exists($abbr, $this->currencies) ? $this->currencies[$abbr] : false;
    }


    /**
     * Get the networks with tokens
     *
     * @return array
     */
    private function networks()
    {
        if (empty($this->currencies)) {
            $this->loadCurrencies();
        }
        $networks = [];
        foreach ($this->currencies as $currency) {
            if (!$currency->isToken()) {
                $networks[$currency->abbr] = \Apirone\SDK\Model\Settings\Network::init($currency);
            }
        }
        foreach ($this->currencies as $currency) {
            if ($currency->isToken()) {
                $networks[$currency->network]->token($currency);
            }
        }
        return $networks;
    }

    /**
     * Coins list parser
     *
     * @param mixed $array Array of json coin objects
     * @return array
     * @throws ReflectionException
     */
    public function parseCoins($array)
    {
        $items = [];
        foreach ($array as $item) {
            $items[] = Coin::init($item);
        }

        return $items;
    }
}
