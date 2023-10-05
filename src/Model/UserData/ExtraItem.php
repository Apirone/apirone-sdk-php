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

class ExtraItem extends AbstractModel
{
    private ?string $name = null;

    private ?string $price = null;

    /**
     * Class constructor
     *
     * @param null|string $name
     * @param null|string $price
     * @return void
     */
    private function __construct(?string $name = null, ?string $price = null)
    {
        $this->name = $name;
        $this->price = $price;
    }

    /**
     * Create instance
     *
     * @param string $name
     * @param string $price
     * @return static
     */
    public static function init(string $name, string $price)
    {
        $class = new static($name, $price);

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
     *
     * @return null|string
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
