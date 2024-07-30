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

namespace Apirone\SDK\Model\Settings;

use Apirone\API\Endpoints\Account;
use Apirone\API\Exceptions\RuntimeException;
use Apirone\API\Exceptions\ValidationFailedException;
use Apirone\API\Exceptions\UnauthorizedException;
use Apirone\API\Exceptions\ForbiddenException;
use Apirone\API\Exceptions\NotFoundException;
use Apirone\API\Exceptions\MethodNotAllowedException;
use Apirone\SDK\Model\AbstractModel;
use Exception;
use ReflectionException;

class Currency extends AbstractModel
{
    private ?string $name = null;

    private ?string $abbr = null;

    private ?string $units = null;

    private ?float $unitsFactor = null;

    private ?int $dustRate = null;

    private ?string $address = null;

    private string $policy = 'fixed';

    private ?string $network = null;
    
    private ?string $token = null;

    private ?string $error = null;

    private function __construct() {}

    /**
     * Create a currency instance
     *
     * @return static
     */
    public static function init()
    {
        $class = new static();

        return $class;
    }

    /**
     * Restore currency instance from JSON
     *
     * @param mixed $json
     * @return $this
     * @throws ReflectionException
     */
    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    /**
     * Check is a test currency
     *
     * @return bool
     */
    public function isTestnet()
    {
        return (substr_count(strtolower($this->name), 'testnet') > 0) ? true : false;
    }

    /**
     * Load currency settings from account
     *
     * @param mixed $account
     * @return $this
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public function loadSettings($account)
    {
        $account = Account::init($account)->info($this->abbr);

        if ($account->info[0]->destinations != null) {
            $this->address = $account->info[0]->destinations[0]->address;
        }
        $this->policy = $account->info[0]->{'processing-fee-policy'};

        return $this;
    }

    /**
     * Cave currency settings to account
     *
     * @param mixed $account
     * @param mixed $transferKey
     * @return $this
     */
    public function saveSettings($account, $transferKey)
    {
        $options = [];

        $options['destinations'] = ($this->address !== null) ? [['address' => $this->address]] : null;
        $options['processing-fee-policy'] = $this->policy;

        $this->error = null;
        try {
            Account::init($account, $transferKey)->settings($this->network, $options);
        }
        catch(Exception $e) {
            $this->error = $e->getMessage();
        }

        return $this;
    }

    /**
     * Get the value of name
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of abbr
     *
     * @return null|string
     */
    public function getAbbr()
    {
        return $this->abbr;
    }

    /**
     * Get the value of units
     *
     * @return null|string
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Get the value of unitsFactor
     *
     * @return null|float
     */
    public function getUnitsFactor()
    {
        return $this->unitsFactor;
    }

    /**
     * Get the value of dustRate
     *
     * @return null|int
     */
    public function getDustRate()
    {
        return $this->dustRate;
    }

    /**
     * Get the currency destination address
     *
     * @return null|string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set currency destination address
     *
     * @param null|string $address
     * @return $this
     */
    public function setAddress(?string $address = null)
    {
        $this->address = empty($address) ? null : trim($address);

        return $this;
    }

    /**
     * Get the value of policy
     *
     * @return string
     */
    public function getPolicy()
    {
        return $this->policy;
    }

    /**
     * Set the value of policy
     *
     * @param null|string $policy
     * @return $this
     */
    public function setPolicy(?string $policy = null)
    {
        $this->policy = $policy;

        return $this;
    }

    /**
     * Get the value of error
     *
     * @return null|string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Is currency has an error
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->error ? true : false;
    }

    /**
     * Return network abbr if currency a network
     * 
     * @return null|string 
     */
    public function isNetwork()
    {
        return ($this->token == null) ? $this->network : null;
    }

    /**
     * Return network abbr if currency a token
     * 
     * @return null|string 
     */
    public function isToken()
    {
        return ($this->token !== null ) ? $this->network : null;
    }

    /**
     * Return array of currencies
     * @return array
     */
    public function getTokens(array $currencies)
    {
        if ($this->isToken()) {
            return [];
        }
        $tokens = [];
        
        foreach ($currencies as $currency) {
            if ($currency instanceof Currency && $currency->isToken() == $this->network) {
                $tokens[] = $currency;
            }
        }

        return $tokens;
    }

    /**
     * Parse currency abbr to set network & token
     *
     * @return self
     */
    public function parseAbbr()
    {
        $parts = explode('@', $this->abbr);
        switch (count($parts)) {
            case 1:
                $this->network = $parts[0];
                $this->token = null;
                break;
            case 2:
                $this->network = $parts[1];
                $this->token = $parts[0];
                break;
        }

        return $this;
    }
}
