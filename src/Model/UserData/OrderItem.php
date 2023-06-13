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

class OrderItem extends AbstractModel
{
    // date	string	Invoice status change date
    private ?string $name = null;

    // status	string	Invoice status
    private ?string $cost = null;

    private ?int $qty = null;

    private ?string $total = null;

    private function __construct(
        ?string $name = null,
        ?string $cost = null,
        ?int $qty = null,
        ?string $total = null
    ) {
        $this->name = $name;
        $this->cost = $cost;
        $this->qty = $qty;
        $this->total = $total;
    }

    public static function init(string $name, string $cost, int $qty, string $total)
    {
        $class = new static($name, $cost, $qty, $total);

        return $class;
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
     * Get the value of cost
     */ 
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set the value of cost
     *
     * @return  self
     */ 
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get the value of qty
     */ 
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set the value of qty
     *
     * @return  self
     */ 
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get the value of total
     */ 
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set the value of total
     *
     * @return  self
     */ 
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }
}
