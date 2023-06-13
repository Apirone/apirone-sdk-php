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

namespace Apirone\Invoice\Model;

use Apirone\Invoice\Model\AbstractModel;
use Apirone\Invoice\Model\UserData\PriceItem;
use Apirone\Invoice\Model\UserData\ExtraItem;
use Apirone\Invoice\Model\UserData\OrderItem;
use stdClass;

class UserData extends AbstractModel
{
    private ?string $title = null;

    // merchant	string	Merchant name. Used as the invoice title
    private ?string $merchant = null;

    // url	string	Merchant url
    private ?string $url = null;

    // price	object	Used in the invoice to display currency and amount in fiat
    private ?string $price = null;
    
    private ?string $subPrice = null;
    
    private ?array $items = null;
    
    private ?array $extras = null;

    private function __construct()
    {
    }

    public static function init() 
    {
        $class = new static();

        return $class;
    }

    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    public function title(?string $title = null)
    {
        $this->title = $title;

        return $this;
    }

    public function merchant(?string $merchant = null)
    {
        $this->merchant = $merchant;

        return $this;
    }

    public function url(?string $url = null)
    {
        $this->url = $url;

        return $this;
    }

    public function price(?string $value = null)
    {
        $this->price = $value;
        
        return $this;
    }

    public function subPrice(?string $value = null)
    {
        $this->subPrice = $value;
        
        return $this;
    }

    public function addOrderItem(string $item, string $cost, int $qty, string $total)
    {
        $this->items[] = OrderItem::init($item, $cost, $qty, $total);

        return $this;
    }

    public function addExtraItem(string $name, string $price)
    {
        $this->extras[] = ExtraItem::init($name, $price);

        return $this;
    }

    public function parseExtras($data)
    {
        $taxes = [];
        foreach ($data as $item) {
            $taxes[] = ExtraItem::fromJson($item);
        }

        return $taxes;
    }

    public function parseItems($data)
    {
        $items = [];
        foreach ($data as $item) {
            $items[] = OrderItem::fromJson($item);
        }

        return $items;
    }

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
     * Get the value of merchant
     */ 
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Get the value of url
     */ 
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the value of price
     */ 
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Get the value of subPrice
     */ 
    public function getSubPrice()
    {
        return $this->subPrice;
    }

    /**
     * Get the value of items
     */ 
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get the value of extras
     */ 
    public function getExtras()
    {
        return $this->extras;
    }
}
