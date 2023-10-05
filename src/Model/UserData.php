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
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title value
     *
     * @param null|string $title
     * @return $this
     */
    public function setTitle(?string $title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Set the merchant value
     *
     * @param null|string $merchant
     * @return $this
     */
    public function setMerchant(?string $merchant = null)
    {
        $this->merchant = $merchant;

        return $this;
    }

    /**
     * Get the value of url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the url value
     *
     * @param null|string $url
     * @return $this
     */
    public function setUrl(?string $url = null)
    {
        $this->url = $url;

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
     * Set the price value
     *
     * @param null|string $value
     * @return $this
     */
    public function setPrice(?string $value = null)
    {
        $this->price = $value;

        return $this;
    }

    /**
     * Get the value of subPrice
     */
    public function getSubPrice()
    {
        return $this->subPrice;
    }

    /**
     * Set the subPrice value
     *
     * @param null|string $value
     * @return $this
     */
    public function setSubPrice(?string $value = null)
    {
        $this->subPrice = $value;

        return $this;
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
        $taxes = [];
        foreach ($data as $item) {
            $taxes[] = ExtraItem::fromJson($item);
        }

        return $taxes;
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

    /**
     * Get the value of items
     *
     * @return null|array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get the value of extras
     *
     * @return null|array
     */
    public function getExtras()
    {
        return $this->extras;
    }
}
