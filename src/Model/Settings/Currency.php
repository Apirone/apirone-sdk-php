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

/**
 * Apirone crypto currency
 *
 * @property-read string $name
 * @property-read string $abbr
 * @property-read string $alias
 * @property-read string $units
 * @property-read string $unitsFactor
 * @property-read int    $dustRate
 * @property-read string $address
 * @property-read string $policy
 * @property-read string $network
 * @property-read string $token
 * @property-read string $error
 *
 * @method public address(string $address)
 * @method public policy(string $policyType) 'fixed' or 'percentage'
 */
class Currency extends AbstractModel
{
    private ?string $name = null;

    private ?string $abbr = null;

    private ?string $alias = null;

    private ?string $units = null;

    private ?float $unitsFactor = null;

    private ?int $dustRate = null;

    private ?string $address = null;

    private string $policy = 'percentage';

    private ?string $network = null;

    private ?string $token = null;

    private ?string $error = null;

    private function __construct() {}

    /**
     * Create a currency instance

     * @param mixed $json
     * @return $this
     * @return static
     */
    public static function init($json)
    {
        $class = new static();

        return $class->classLoader($json)->alias();
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
     * Save currency settings to account
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

        Account::init($account, $transferKey)->settings($this->network, $options);

        return $this;
    }

    /**
     * Set currency destination address
     *
     * @param null|string $address
     * @return $this
     */
    public function address(?string $address = null)
    {
        $this->address = empty($address) ? null : trim($address);

        return $this;
    }

    /**
     * Set the value of policy
     *
     * @param string $policy `fixed` or `percentage`
     * @return $this
     */
    public function policy(string $policy)
    {
        $this->policy = $policy;

        return $this;
    }

    /**
     * Set currency alias depends on currency properties
     * @return $this;
     */
    private function alias()
    {
        if ($this->token == null) {
            $this->alias = $this->name;
            return $this;
        }

        preg_match('#\((.*?)\)#', $this->name, $match);
        $suffix = count($match) > 0 ? $match[1] : '';

        $format = ($this->isTestnet()) ? '%s (%s - testnet)' : '%s (%s)';

        $this->alias = strtoupper(sprintf($format, $this->token, $suffix));

        return $this;
    }


    /**
     * Checks is the currency has an error
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->error ? true : false;
    }

    /**
     * Check is a test currency
     *
     * @return bool
     */
    public function isTestnet()
    {
        if ($this->isToken()) {
            return (substr($this->network, 0, 1) == 't' && strlen($this->network) > 3) ? true : false;
        }

        return (substr_count(strtolower($this->name), 'testnet') > 0) ? true : false;
    }

    /**
     * Returns network abbr if currency a network
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
     * Returns whether the currency is a stablecoin
     *
     * @return bool
     */
    public function isStablecoin()
    {
        return (substr_count(strtolower($this->name), 'usd') > 0) ? true : false;
    }

    /**
     * Returns array of currencies
     *
     * @param array $currencies
     * @return \Apirone\SDK\Model\Settings\Currency[]
     */
    public function tokens(array $currencies)
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
     * Create currency instance from JSON. Alias to init()
     *
     * @param mixed $json
     * @return $this
     * @throws ReflectionException
     */
    public static function fromJson($json)
    {
        return self::init($json);
    }
}
