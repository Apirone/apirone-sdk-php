<?php

namespace Apirone\Invoice\Model\UserData;

use Apirone\Invoice\Model\AbstractModel;

class TaxItem extends AbstractModel
{
    // date	string	Invoice status change date
    private ?string $name = null;

    // status	string	Invoice status
    private ?float $price = null;

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
        return $this->name . $this->price;
    }

}
