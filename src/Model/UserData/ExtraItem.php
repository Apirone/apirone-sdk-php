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


    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of price
     */ 
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */ 
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }
}
