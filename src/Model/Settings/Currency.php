<?php
/**
 * This file is part of the Apirone Invoice library.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Apirone\Invoice\Model\Settings;

use Apirone\API\Endpoints\Account;
use Apirone\Invoice\Model\AbstractModel;
use Exception;

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

    public static function init()
    {
        $class = new static();

        return $class;
    }
    
    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    public function isTestnet()
    {
        return (substr_count(strtolower($this->name), 'testnet') > 0) ? true : false;
    }

    public function loadSettings($account)
    {
        $account = Account::init($account)->info($this->abbr);
        
        if ($account->info[0]->destinations != null) {
            $this->address = $account->info[0]->destinations[0]->address;
        }
        $this->policy = $account->info[0]->{'processing-fee-policy'};

        return $this;
    }

    public function saveSettings($account, $transferKey)
    {
        $options = [];

        $options['destinations'] = ($this->address !== null) ? [['address' => $this->address]] : null;
        $options['processing-fee-policy'] = $this->policy;

        try {
            Account::init($account, $transferKey)->settings($this->abbr, $options);
        }
        catch(Exception $e) {
            $exception = json_decode($e->getMessage());
            $this->error = $exception->message;
        }

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of abbr
     */ 
    public function getAbbr()
    {
        return $this->abbr;
    }

    /**
     * Get the value of units
     */ 
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Get the value of unitsFactor
     */ 
    public function getUnitsFactor()
    {
        return $this->unitsFactor;
    }

    /**
     * Get the value of dustRate
     */ 
    public function getDustRate()
    {
        return $this->dustRate;
    }

    /**
     * Get the value of address
     */ 
    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress(?string $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of policy
     */ 
    public function getPolicy()
    {
        return $this->policy;
    }

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
     * Is currency have an error
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->error ? true : false;
    }
}
