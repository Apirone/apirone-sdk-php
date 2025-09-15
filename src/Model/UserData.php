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

namespace Apirone\SDK\Model;

use Apirone\SDK\Model\AbstractModel;
use Apirone\SDK\Model\UserData\ExtraItem;
use Apirone\SDK\Model\UserData\OrderItem;
use ReflectionException;
use stdClass;

/**
 * @property-read string $title
 * @property-read string $merchant
 * @property-read string $url
 * @property-read string $price
 * @property-read string $subPrice
 * @property-read array  $items
 * @property-read array  $extras
 *
 * @method public title(string $title)
 * @method public merchant(string $merchant)
 * @method public url(string $url)
 * @method public price(string $price)
 * @method public subPrice(string $subPrice)
 * @method public items(array $items)
 * @method public extras(array $extras)
 */
class UserData extends AbstractModel
{
    private ?string $title = null;

    private ?string $merchant = null;

    private ?string $url = null;

    private ?string $price = null;

    private ?string $subPrice = null;

    private ?array $items = null;

    private ?array $extras = null;

    private function __construct() {}

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
     * @return static
     */
    public static function init()
    {
        $class = new static();

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
     * Add order item
     *
     * @param string $item
     * @param string $cost
     * @param int $qty
     * @param string $total
     * @return $this
     */
    public function addOrderItem(string $item, string $cost, int $qty, string $total)
    {
        $this->items[] = OrderItem::init($item, $cost, $qty, $total);

        return $this;
    }

    /**
     * Add extra item
     *
     * @param string $name
     * @param string $price
     * @return $this
     */
    public function addExtraItem(string $name, string $price)
    {
        $this->extras[] = ExtraItem::init($name, $price);

        return $this;
    }

    /**
     * Extras parser
     *
     * @param mixed $data
     * @return array
     * @throws ReflectionException
     */
    public function parseExtras($data)
    {
        $extras = [];
        foreach ($data as $item) {
            $extras[] = ExtraItem::fromJson($item);
        }

        return $extras;
    }

    /**
     * Items parser
     *
     * @param mixed $data
     * @return array
     * @throws ReflectionException
     */
    public function parseItems($data)
    {
        $items = [];
        foreach ($data as $item) {
            $items[] = OrderItem::fromJson($item);
        }

        return $items;
    }

    /**
     * Convert instance to JSON
     *
     * @return stdClass
     */
    public function toJson(): stdClass
    {
        $json = parent::toJson();

        foreach ($json as $key => $value) {
            if ($value === null) {
                unset($json->$key);
            }
        }

        return $json;
    }
}
