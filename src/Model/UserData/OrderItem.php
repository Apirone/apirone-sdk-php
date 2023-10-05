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

namespace Apirone\SDK\Model\UserData;

use Apirone\SDK\Model\AbstractModel;
use ReflectionException;

class OrderItem extends AbstractModel
{
    private ?string $name = null;

    private ?string $cost = null;

    private ?int $qty = null;

    private ?string $total = null;

    /**
     * Class constructor
     *
     * @param null|string $name
     * @param null|string $cost
     * @param null|int $qty
     * @param null|string $total
     * @return void
     */
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

    /**
     * Create instance
     *
     * @param string $name
     * @param string $cost
     * @param int $qty
     * @param string $total
     * @return static
     */
    public static function init(string $name, string $cost, int $qty, string $total)
    {
        $class = new static($name, $cost, $qty, $total);

        return $class;
    }

    /**
     * Restore instance from JSON
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
     * Convert instance to string
     *
     * @return string
     */
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
     *
     * @return null|int
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
     *
     * @return null|string
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
