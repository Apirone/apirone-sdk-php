<?php

namespace Apirone\Invoice\Model\UserData;

use Apirone\Invoice\Model\AbstractModel;

class ExtraItem extends AbstractModel
{
    private ?string $name = null;

    private ?string $price = null;

    private function __construct(?string $name = null, ?string $price = null)
    {
        $this->name = $name;
        $this->price = $price;
    }

    public static function init(string $name, string $price)
    {
        $class = new static($name, $price);

        return $class;
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
