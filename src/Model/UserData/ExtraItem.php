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

/**
 * @property-read string $name
 * @property-read string $price
 *
 * @method public name(string $name)
 * @method public price(string $price)
 */

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

    public function __call($name, $value)
    {
        if (\property_exists($this, $name)) {

            $class = new \ReflectionClass(static::class);

            $property = $class->getProperty($name);
            $property->setAccessible(true);

            $property->setValue($this, $value[0]);

            return $this;
        }
        $trace = \debug_backtrace();
        \trigger_error(
            'Call to undefined method ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            \E_USER_ERROR
        );
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
}
