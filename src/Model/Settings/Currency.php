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
use Apirone\SDK\Service\Utils;
use ReflectionException;
use Exception;

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

    private bool $changed = false;

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
     * Load currency from account
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
    public function load($account)
    {
        $account = Account::init($account)->info($this->abbr);

        if ($account->info[0]->destinations != null) {
            $this->address = $account->info[0]->destinations[0]->address;
        }
        $this->policy = $account->info[0]->{'processing-fee-policy'};
        $this->changed = false;

        return $this;
    }

    /**
     * Save currency to account
     *
     * @param mixed $account
     * @param mixed $transferKey
     * @return $this
     */
    public function save($account, $transferKey)
    {
        if($this->changed) {
            $options = [];

            $options['destinations'] = ($this->address !== null) ? [['address' => $this->address]] : null;
            $options['processing-fee-policy'] = $this->policy;

            $this->error = null;
            try {
                Account::init($account, $transferKey)->settings($this->network, $options);
                $this->changed = false;
            }
            catch(Exception $e) {
                $this->error = $e->getMessage();
            }
        }

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
        $address = empty($address) ? null : trim($address);

        if ($address !== $this->address) {
            $this->address = $address;
            $this->changed = true;
        }

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
        $policy = strtolower(trim($policy));

        if (in_array($policy, ['fixed', 'percentage']) && $policy !== $this->policy ) {
            $this->policy = $policy;
            $this->changed = true;
        }

        return $this;
    }

    /**
     * Set currency alias depends on currency properties
     * @return $this;
     */
    private function alias()
    {

        $this->alias = Utils::getAlias($this->abbr, $this->name);

        return $this;
    }


    /**
     * Checks is the currency has an error
     *
     * @return bool
     */
    public function hasError()
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
        return Utils::isTestnet($this->abbr);
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
