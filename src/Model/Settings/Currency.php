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

    private ?string $error = null;

    private function __construct()
    {
    }

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

        try {
            Account::init($account, $transferKey)->settings($this->abbr, $options);
        } catch(Exception $e) {
            $exception = json_decode($e->getMessage());
            $this->error = $exception->message;
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
        $this->address = $address;

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
}
