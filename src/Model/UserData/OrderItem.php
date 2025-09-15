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

/**
 * @property-read string $name
 * @property-read string $cost
 * @property-read string $qty
 * @property-read string $total
 *
 * @method public name(string $name)
 * @method public cost(string $cost)
 * @method public qty(string $qty)
 * @method public total(string $total)
 */
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

    public function __call($name, $value)
    {
        $name = static::convertToCamelCase($name);

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
}
