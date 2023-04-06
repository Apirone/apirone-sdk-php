<?php

namespace Apirone\Invoice\Model;

use Apirone\Invoice\Model\AbstractModel;
use Apirone\Invoice\Model\UserData\Price;
use Apirone\Invoice\Model\UserData\TaxItem;

class UserData extends AbstractModel
{
    // merchant	string	Merchant name. Used as the invoice title
    private ?string $merchant = null;

    // url	string	Merchant url
    private ?string $url = null;

    // price	object	Used in the invoice to display currency and amount in fiat
    private ?Price $price = null;
    
    private ?array $taxRates = null;

    private function __construct()
    {
    }

    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    public function parsePrice($data)
    {
        $userData = Price::fromJson($data);

        return $userData;
    }

    public function parseTaxRates($data)
    {
        $taxRates = [];
        foreach ($data as $item) {
            $taxRates[] = TaxItem::fromJson($item);
        }

        return $taxRates;
    }

}
