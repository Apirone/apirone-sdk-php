<?php

namespace Apirone\Invoice\Model;

use Apirone\API\Endpoints\Account;
use Apirone\API\Endpoints\Service;
use Apirone\Invoice\Model\Settings\Currency;
class Settings extends AbstractModel
{
    private ?string $account = null;

    private ?string $transferKey = null;

    private array   $currencies = [];

    private string  $merchant = '';

    private int     $timeout = 1800;

    private float   $factor = 1;

    private string  $backlink = '';

    private bool    $logo = true;

    private ?string $version = null;
    
    private \stdClass $extra;

    private function __construct()
    {
        $this->extra = new \stdClass;
    }

    public static function init()
    {
        $class = new static();

        // $class->createAccount();
        // $class->setDefaultValues();
        $class->currencies = $class->loadCurrencies();

        return $class;
    }

    public static function fromJson($json)
    {
        $class = new static();
        
        $class->classLoader($json);

        if (empty($class->currencies)) {
            $class->currencies = $class->loadCurrencies();
        }

        return $class;
    }
    public static function fromFile($abspath)
    {
        $json = file_get_contents($abspath);
        if ($json) {
            return static::fromJson($json);
        }
        return false;
    }

    public static function fromExistingAccount($account, $transferKey) {
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

    public function toArray(): array {
        $data = parent::toArray();

        if(empty($data['extra']) && gettype($data['extra']) == 'array') {
            $data['extra'] = new \stdClass;
        }

        return $data;
    }

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

    public function restoreDefaults()
    {
        $this->merchant = '';
        $this->timeout = 1800;
        $this->factor = 1;
        $this->backlink = '';
        $this->logo = true;

        return $this;
    }

    public function loadCurrencies()
    {
        $serviceInfo = Service::account();
        $accountInfo = ($this->account) ? Account::init($this->account)->info()->info : [];

        $currencies = [];
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
            $currencies[] = $currency;
        }

        return $currencies;
    }

    public function saveCurrencies()
    {
        foreach ($this->currencies as $currency) {
            $currency->saveSettings($this->account, $this->transferKey);
        }

        return $this;
    }

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
     */ 
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Get the value of transferKey
     */ 
    public function getTransferKey()
    {
        return $this->transferKey;
    }

    /**
     * Get the value of currencies
     */ 
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * Get the value of merchant
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
     * Get the value of timeout
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
     */ 
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * Set the value of factor
     *
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
     * Get the value of version
     */ 
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Set the value of version
     *
     * @return  self
     */ 
    public function setVersion(string $version)
    {
        $this->version = $version;

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
        if (property_exists($this->extra, $key)){
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

    public function parseCurrencies($json)
    {
        $items = [];
        foreach ($json as $item) {
            $items[] = Currency::fromJson($item);
        }

        return $items;
    }
}
