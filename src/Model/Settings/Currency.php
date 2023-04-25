<?php

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
    
    private ?string $policy = null;

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

    public function getSettings($account)
    {
        $account = Account::init($account)->info($this->abbr);
        
        if ($account->info[0]->destinations != null) {
            $this->address = $account->info[0]->destinations[0]->address;
        }
        $this->policy = $account->info[0]->{'processing-fee-policy'};

        return $this;
    }

    public function updateAccountSettings($account, $transferKey)
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

    public function setAddress(?string $address = null)
    {
        $this->address = $address;

        return $this;
    }

    public function setPolicy(?string $policy = null)
    {
        $this->policy = $policy;

        return $this;
    }
}
