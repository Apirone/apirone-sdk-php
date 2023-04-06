<?php

namespace Apirone\Invoice\Model\UserData;

use Apirone\Invoice\Model\AbstractModel;

class Price extends AbstractModel
{
    // date	string	Invoice status change date
    private ?string $currency = null;

    // status	string	Invoice status
    private ?float $amount = null;

    private function __construct()
    {
    }
    
    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    public function toString()
    {
        return $this->amount . $this->currency;
    }

}
