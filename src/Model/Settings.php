<?php

namespace Apirone\Invoice\Model;

use Apirone\API\Endpoints\Account;
use Apirone\API\Endpoints\Service;
use Apirone\Invoice\Model\Settings\Currency;
use Apirone\Invoice\Model\Settings\Wallet;
class Settings extends AbstractModel
{
    private ?string $account = null;

    private ?string $transferKey = null;

    private array $currencies = [];

    private \stdClass $extras;

    private ?string $version = null;

    private function __construct()
    {
        $this->extras = new \stdClass;
    }
    public static function init()
    {
        $class = new static();

        $class->createAccount();
        $class->setDefaultValues();
        $class->currencies = $class->getCurrencies();

        return $class;
    }

    public static function fromJson($json)
    {
        $class = new static();
        
        $class->classLoader($json);

        if (empty($class->currencies)) {
            $class->currencies = $class->getCurrencies();
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

    public function __get($property)
    {
        $property = static::convertToCamelCase($property);
        if (\property_exists($this, $property)) {

            $class = new \ReflectionClass(static::class);

            $property = $class->getProperty($property);
            $property->setAccessible(true);

            if(!$property->isInitialized($this)) {
                return null;
            }

            return $property->getValue($this);
        }
        else {
            if (property_exists($this->extras, $property)) {
                return $this->extras->{$property};
            }
            return null;
        }
    }

    public function __set($prop, $value)
    {
        if (\property_exists($this, $prop)) {
            $this->{$prop} = $value;
        }
        else {
            $this->extras->{$prop} = $value;
        }
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
                $this->updateCurrencies();
            }
        }

        return $this;
    }

    public function setDefaultValues()
    {
        $this->extras->merchant = '';
        $this->extras->timeout = 1800;
        $this->extras->factor = 1;
        $this->extras->backlink = '';
        $this->extras->logo = true;

        return $this;
    }

    public function getCurrencies()
    {
        $serviceInfo = Service::account();
        $accountInfo = $this->getAccountSettings();

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

    public function updateCurrencies()
    {
        foreach ($this->currencies as $currency) {
            $currency->updateAccountSettings($this->account, $this->transferKey);
        }

        return $this;
    }

    private function getAccountSettings()
    {
        $settings = Account::init($this->account)->info();

        return $settings->info;
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

    public function parseCurrencies($json)
    {
        $items = [];
        foreach ($json as $item) {
            $items[] = Currency::fromJson($item);
        }

        return $items;
    }

    public function parseWallets($json)
    {
        $items = [];
        foreach ($json as $item) {
            $items[] = Wallet::fromJson($item);
        }

        return $items;    
    }

    public function toFile(string $dir, string $filename = 'invoice-config.json')
    {
        if (substr($dir, -1) !== DIRECTORY_SEPARATOR)
            $dir = $dir . DIRECTORY_SEPARATOR;
        $path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $dir . $filename;

        if (file_put_contents($path, json_encode($this->toJson(), JSON_PRETTY_PRINT))) {
            return true;
        }
        return false;
    }
}
