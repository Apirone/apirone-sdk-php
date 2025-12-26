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
use Apirone\SDK\Model\Settings\Network;
use ReflectionException;

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
    private array $currencies = [];

    /**
     * Metadata
     *
     * @var stdClass
     */
    private ?\stdClass $meta = null;

    private function __construct() {}

    public function __get($name) {

        if ($name == 'networks') {
            return $this->networks();
        }

        // Currencies lazy loading
        if ($name == 'currencies') {
            foreach (debug_backtrace(2) as $item) {
                $callstack[] = $item['function'];
            }
            if (in_array('toArray', $callstack)) {
                return $this->currencies;
            }
            if (empty($this->currencies) || get_class($this->currencies[array_key_first($this->currencies)]) == 'stdClass') {
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
     * @return static
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
     * @param mixed $absFilePath
     * @return self|null
     */
    public static function fromFile($absFilePath)
    {
        $json = @file_get_contents($absFilePath);

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
     */
    public static function fromExistingAccount($account, $transferKey)
    {
        $class = new static();

        $class->account = $account;
        $class->transferKey = $transferKey;

        return $class;
    }

    /**
     * Save settings to file
     *
     * @param mixed $absFilePath
     * @param 128 $flag
     * @return true|false
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
    public function toArray()
    {
        $settings = parent::toArray();

        foreach ($settings as $key => $val) {
            if (!in_array($key, ['account', 'transfer-key', 'meta'])) {
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
     * @return self
     */
    public function createAccount()
    {
        if (isset($this->account)) {
            return $this;
        }
        $account = Account::create();

        if ($account) {
            $this->account = $account->account;
            $this->transferKey = $account->{'transfer-key'};
        }

        return $this;
    }

    /**
     * Load currencies from an apirone service
     *
     * @return self
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
                $currency->changed(false);
            }
            $this->currencies[$serviceItem->abbr] = $currency;
        }

        return $this;
    }

    /**
     * Save networks currencies into apirone account
     *
     * @return array $errors Returns an array with errors, if this happens otherwise an empty array
     */
    public function saveNetworks()
    {
        $errors = [];

        foreach ($this->networks as $network) {
            $network->save($this->account, $this->transferKey);
            if ($network->error) {
                $errors[$network->abbr] = $network->error;
            }
        }

        return $errors;
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
        if (empty($this->currencies) || get_class($this->currencies[array_key_first($this->currencies)]) == 'stdClass') {
            $this->loadCurrencies();
        }

        $networks = [];
        foreach ($this->currencies as $currency) {
            if (!$currency->isToken()) {
                $networks[$currency->abbr] = Network::init($currency);
            }
        }
        foreach ($this->currencies as $currency) {
            if ($currency->isToken()) {
                $networks[$currency->network]->token($currency);
            }
        }

        return $networks;
    }
}
