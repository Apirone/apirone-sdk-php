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
use Apirone\SDK\Model\Settings\Currency;
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

    /**
     * Invoice title
     *
     * @var string
     * @deprecated Use meta object. The property will be removed in future versions.
     */
    private string  $title = '';

    /**
     * Merchant name
     *
     * @var string
     * @deprecated Use meta object. The property will be removed in future versions.
     */
    private string  $merchant = '';

    /**
     * Merchant Url
     *
     * @var string
     * @deprecated Use meta object. The property will be removed in future versions.
     */
    private string  $merchantUrl = '';

    /**
     * Invoice timeout
     *
     * @var int
     * @deprecated Use meta object. The property will be removed in future versions.
     */
    private int     $timeout = 1800;

    /**
     * Price adjustment factor
     *
     * @var float
     * @deprecated Use meta object. The property will be removed in future versions.
     */
    private float   $factor = 1;

    /**
     * Backlink
     *
     * @var string
     * @deprecated Use meta object. The property will be removed in future versions.
     */
    private string  $backlink = '';

    /**
     * QR Template
     *
     * @var bool
     * @deprecated Use meta object. The property will be removed in future versions.
     */
    private bool    $qrOnly = false;

    /**
     * Logo
     *
     * @var bool
     * @deprecated Use meta object. The property will be removed in future versions.
     */
    private bool    $logo = true;

    /**
     * Debug
     *
     * @var bool
     * @deprecated Use meta object. The property will be removed in future versions.
     */
    private bool    $debug = false;

    /**
     * Extra settings values object
     *
     * @var stdClass
     * @deprecated Use meta object. The property will be removed in future versions.
     */
    private \stdClass $extra;

    /**
     * Class constructor
     *
     * @return void
     */
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
        if (property_exists($this->meta, $name)) {
            return $this->meta->{$name};
        }
        return parent::__get($name);
    }

    public function __call($name, $value)
    {
        $name = static::convertToCamelCase($name);

        if (\property_exists($this, $name)) {

            $class = new \ReflectionClass(static::class);

            $property = $class->getProperty($name);
            $property->setAccessible(true);
            $property->setValue($this, $value[0]);

            return $this;
        }

        if (empty($value) || $value[0] == false) {
            unset($this->meta->{$name});
            return $this;
        }

        $this->meta->{$name} = $value[0];

        return $this;
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
    public function toFile($abspath)
    {
        if (file_put_contents($abspath, json_encode($this->toJson(), JSON_PRETTY_PRINT))) {
            return true;
        }

        return false;
    }

    /**
     * Convert instance to array and skip currency property
     *
     * @return array
     */
    public function toArray($unsetDeprecated = false): array
    {
        $settings = [];
        $class = new \ReflectionClass(static::class);

        foreach ($class->getProperties() as $property) {
            $prop = $property->getName();
            if ($prop == 'currency') {
                continue;
            }

            $type = gettype($this->{$prop});

            if ($type !== 'object' || $type !== 'array') {
                $settings[self::convertToSnakeCase($prop)] = $this->{$prop};
            }

            if ($type == 'object') {
                $settings[self::convertToSnakeCase($prop)] = (get_class($this->{$prop}) !== 'stdClass') ? $this->{$prop}->toArray() : json_decode(json_encode($this->{$prop}), true);

                continue;
            }
            if ($type == 'array') {
                $items = [];
                foreach($this->{$prop} as $key => $item) {
                    if(gettype($item) == 'object') {
                        $items[] = (get_class($item) !== 'stdClass') ? $item->toArray() : json_decode(json_encode($item), true);
                    }
                    else {
                        $items[$key] = $item;
                    }
                }
                $settings[self::convertToSnakeCase($prop)] = $items;
            }
        }

        // $settings = parent::toArray();

        if(empty($settings['extra'])) {
            unset($settings['extra']);
        }

        if(empty($settings['meta'])) {
            unset($settings['meta']);
        }

        if ($unsetDeprecated) {
            foreach ($settings as $key => $v) {
                if (in_array($key, ['account', 'transfer-key', 'meta'])) {
                    continue;
                }
                unset($settings[$key]);
            }
        }

        return $settings;
    }

    protected function classLoader($json)
    {
        $json = gettype($json) == 'string' ? json_decode($json) : $json;

        $class = new \ReflectionClass(static::class);

        foreach ($json as $key => $value) {
            $name = static::convertToCamelCase($key);
            if ($name == 'currencies')
                continue;

            if (\property_exists($this, $name)) {
                $property = $class->getProperty($name);
                $property->setAccessible(true);
                if (gettype($value) == 'object' || gettype($value) == 'array') {
                    $parser = 'parse' . ucfirst($name);
                    if ($class->hasMethod($parser)) {
                        $property->setValue($this, $this->$parser($value));
                    }
                    else {
                        $property->setValue($this, $value);
                    }
                }
                else {
                    $property->setValue($this, $value);
                }
            }
        }

        return $this;
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
        foreach($this->currencies as $currency) {
            if ($currency->abbr == $abbr) {
                return $currency;
            }
        }

        return false;
    }

    /**
     * Set meta
     * Use $class->meta() to clear all meta
     *
     * @param string $key
     * @return mixed
     */
    protected function meta($key = '{}') {
        $json = gettype($key) == 'string' ? json_decode($key) : $key;
        $this->meta = $json;

        return $this;
    }

    /**
     * Get the networks with tokens
     *
     * @return array
     * @deprecated Use as property $class->networks
     */
    public function networks()
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
     * Get the value of meta
     *
     * @param string|null $key
     * @return mixed
     * @deprecated Use $class->MyParamName directly
     */
    public function getMeta(string $key = null)
    {
        if ($key == null) {
            return $this->meta;
        }
        if (property_exists($this->meta, $key)) {
            return $this->meta->{$key};
        }

        return null;
    }

    /**
     * Add/edit meta item
     *
     * @return self
     * @deprecated Use $class->myParamName('My Param Value')
     */
    public function addMeta($key, $value)
    {
        $this->meta->{$key} = $value;

        return $this;
    }

    /**
     * Delete meta item
     *
     * @return self
     * @deprecated Use $class->meta('meta-key', null)
     */
    public function deleteMeta($key)
    {
        unset($this->meta->{$key});

        return $this;
    }

    /**
     * Reset settings to default values
     *
     * @return self
     * @deprecated The method will be removed in future versions.
     */
    public function restoreDefaults()
    {
        $this->merchantUrl = '';
        $this->merchant = '';
        $this->timeout = 1800;
        $this->factor = 1;
        $this->backlink = '';
        $this->logo = true;

        return $this;
    }

    /**
     * Get the value of account
     *
     * @return null|string
     * @deprecated Use $class->account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Get the value of transferKey
     *
     * @return null|string
     * @deprecated Use $class->transferKey
     */
    public function getTransferKey()
    {
        return $this->transferKey;
    }

    /**
     * Alias to currency()
     *
     * @param mixed $abbr
     * @return Currency | false
     * @deprecated Use $class->currency() method
     */
    public function getCurrency($abbr)
    {
        return $this->currency($abbr);
    }

    /**
     * Get the value of currencies
     *
     * @return array
     * @deprecated Use $class->currencies
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * Alias to networks()
     *
     * @return array
     * @deprecated Use $class->networks
     */
    public function getNetworks()
    {
        return $this->networks();
    }

    /**
     * Get invoice title
     *
     * @return string
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set invoice title
     *
     * @param  string  $title  Invoice title
     *
     * @return self
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of merchant
     *
     * @return string
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Set the value of merchant
     *
     * @return self
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function setMerchant($merchant)
    {
        $this->merchant = $merchant;

        return $this;
    }

    /**
     * Get merchant Url
     *
     * @return string
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function getMerchantUrl()
    {
        return $this->merchantUrl;
    }

    /**
     * Set merchant Url
     *
     * @param  string  $merchantUrl  Merchant Url
     * @return self
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function setMerchantUrl(string $merchantUrl)
    {
        $this->merchantUrl = $merchantUrl;

        return $this;
    }

    /**
     * Get the value of timeout
     *
     * @return int
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set the value of timeout
     *
     * @return self
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the value of factor
     *
     * @return float
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * Set the value of factor

     * If you want to add/subtract percent to/from the payment amount, use the following  price adjustment factor
     * multiplied by the amount.
     * For example:
     * 100% * 0.99 = 99%
     * 100% * 1.01 = 101%
     * @return self
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function setFactor($factor)
    {
        $this->factor = $factor;

        return $this;
    }

    /**
     * Get the value of backlink
     *
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function getBacklink()
    {
        return $this->backlink;
    }

    /**
     * Set the value of backlink
     *
     * @return self
     * @deprecated The method will be removed in future versions.
     */
    public function setBacklink($backlink)
    {
        $this->backlink = $backlink;

        return $this;
    }

    /**
     * Get the value of logo
     *
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function getLogo(): bool
    {
        return $this->logo;
    }

    /**
     * Set the value of logo
     *
     * @return self
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function setLogo(bool $logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get qR Template
     *
     * @return bool
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function getQrOnly()
    {
        return $this->qrOnly;
    }

    /**
     * Set qR Template
     *
     * @param  bool  $qrOnly  QR Template
     * @return self
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function setQrOnly(bool $qrOnly)
    {
        $this->qrOnly = $qrOnly;

        return $this;
    }

    /**
     * Get debug
     *
     * @return bool
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Set debug
     *
     * @param  bool  $debug Debug
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     * @return self
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Get the value of extra
     *
     * @param string|null $key
     * @return mixed
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function getExtra(string $key = null)
    {
        if ($key == null) {
            return $this->extra;
        }
        if (property_exists($this->extra, $key)) {
            return $this->extra->{$key};
        }

        return null;
    }

    /**
     * Set the value of extra
     *
     * @return self
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function setExtra(string $key, string $value)
    {
        $this->extra->{$key} = $value;

        return $this;
    }

    /**
     * Set the value of extra
     *
     * @return self
     * @deprecated Use $class->meta() method. The method will be removed in future versions.
     */
    public function setExtraObj(\stdClass $obj)
    {
        $this->extra = $obj;

        return $this;
    }
}
