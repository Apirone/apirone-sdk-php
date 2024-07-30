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

    /**
     * Invoice title
     *
     * @var string
     */
    private string  $title = '';

    /**
     * Merchant name
     *
     * @var string
     */
    private string  $merchant = '';

    /**
     * Merchant Url
     *
     * @var string
     */
    private string  $merchantUrl = '';

    /**
     * Invoice timeout
     *
     * @var int
     */
    private int     $timeout = 1800;

    /**
     * Price adjustment factor
     *
     * @var float
     */
    private float   $factor = 1;

    /**
     * Backlink
     *
     * @var string
     */
    private string  $backlink = '';

    /**
     * QR Template
     *
     * @var bool
     */
    private bool    $qrOnly = false;

    /**
     * Logo
     *
     * @var bool
     */
    private bool    $logo = true;

    /**
     * Debug
     *
     * @var bool
     */
    private bool    $debug = false;

    /**
     * Extra settings values object
     *
     * @var stdClass
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

        return $class->loadCurrencies();
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

        if (empty($class->currencies)) {
            $class->loadCurrencies();
        }

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
     * Convert instance to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if(empty($data['extra']) && gettype($data['extra']) == 'array') {
            $data['extra'] = new \stdClass();
        }

        return $data;
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
     * Reset settings to default values
     *
     * @return self
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
            $currency = Currency::fromJson($serviceItem);
            foreach ($accountInfo as $accountItem) {
                if ($accountItem->currency !== $serviceItem->abbr) {
                    continue;
                }
                $address = ($accountItem->destinations !== null) ? $accountItem->destinations[0]->address : null;
                $currency->setAddress($address);
                $currency->setPolicy($accountItem->{'processing-fee-policy'});
            }
            $this->currencies[] = $currency;
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
    public function getCurrency($abbr)
    {
        foreach($this->currencies as $currency) {
            if ($currency->abbr == $abbr) {
                return $currency;
            }
        }

        return false;
    }

    /**
     * Get the value of account
     * 
     * @return null|string 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Get the value of transferKey
     * 
     * @return null|string 
     */ 
    public function getTransferKey()
    {
        return $this->transferKey;
    }

    /**
     * Get the value of currencies
     * 
     * @return array 
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * Get the networks only
     * 
     * @return array 
     */
    public function getNetworks()
    {
        $networks = [];
        foreach ($this->currencies as $currency) {
            if (!$currency->isToken()) {
                $networks[] = $currency;
            }
        }
        return $networks;
    }

    /**
     * Get invoice title
     *
     * @return  string
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
     * @return  self
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
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Set the value of merchant
     *
     * @return  self
     */
    public function setMerchant($merchant)
    {
        $this->merchant = $merchant;

        return $this;
    }

    /**
     * Get merchant Url
     *
     * @return  string
     */
    public function getMerchantUrl()
    {
        return $this->merchantUrl;
    }

    /**
     * Set merchant Url
     *
     * @param  string  $merchantUrl  Merchant Url
     * @return  self
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
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set the value of timeout
     *
     * @return  self
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
     * @return  self
     */
    public function setFactor($factor)
    {
        $this->factor = $factor;

        return $this;
    }

    /**
     * Get the value of backlink
     */
    public function getBacklink()
    {
        return $this->backlink;
    }

    /**
     * Set the value of backlink
     *
     * @return  self
     */
    public function setBacklink($backlink)
    {
        $this->backlink = $backlink;

        return $this;
    }

    /**
     * Get the value of logo
     */
    public function getLogo(): bool
    {
        return $this->logo;
    }

    /**
     * Set the value of logo
     *
     * @return  self
     */
    public function setLogo(bool $logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get qR Template
     *
     * @return  bool
     */
    public function getQrOnly()
    {
        return $this->qrOnly;
    }

    /**
     * Set qR Template
     *
     * @param  bool  $qrOnly  QR Template
     *
     * @return  self
     */
    public function setQrOnly(bool $qrOnly)
    {
        $this->qrOnly = $qrOnly;

        return $this;
    }

    /**
     * Get debug
     *
     * @return  bool
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Set debug
     *
     * @param  bool  $debug  Debug
     *
     * @return  self
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
     * @return  self
     */
    public function setExtra(string $key, string $value)
    {
        $this->extra->{$key} = $value;

        return $this;
    }

    /**
     * Set the value of extra
     *
     * @return  self
     */
    public function setExtraObj(\stdClass $obj)
    {
        $this->extra = $obj;

        return $this;
    }

    /**
     * Currencies list parser
     *
     * @param mixed $json
     * @return array
     * @throws ReflectionException
     */
    public function parseCurrencies($json)
    {
        $items = [];
        foreach ($json as $item) {
            $items[] = Currency::fromJson($item);
        }

        return $items;
    }
}
