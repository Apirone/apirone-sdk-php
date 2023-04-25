<?php

namespace Apirone\Invoice\Model\Settings;

use Apirone\Invoice\Model\AbstractModel;
use Apirone\APi\Endpoints\Account;

class Wallet extends AbstractModel
{
    private ?string $currency = null;

    private ?string $processingFeePolicy = null;

    private ?string $address = null;
    
    // private ?string $addressValid = null;

    private function __construct(?string $currency = null, ?string $address = null, ?string $processingFeePolicy = null)
    {
        $this->currency = $currency;

        $this->address = $address;

        $this->processingFeePolicy = $processingFeePolicy === null ? 'percentage' : $processingFeePolicy;
    }

    public static function init(?string $currency, ?string $address, ?string $processingFeePolicy)
    {
        $class = new static($currency, $address, $processingFeePolicy);

        return $class;
    }
    
    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

}
