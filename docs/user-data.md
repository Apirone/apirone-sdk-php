# UserData class

This class allows you to configure Invoice user-data property using setters to set values rather than json format.

See [API docs](https://apirone.com/docs/invoices/#create-invoice) for a full description of the parameters.
|Class method|API parameter|Description|
|---|---|---|
|`setTitle()`|`title`|Invoice title|
|`setMerchant()`|`merchant`|Merchant name|
|`setUrl()`|`url`|Merchant url|
|`setPrice()`|`price`|Displays the total price in fiat|
|`setSubPrice()`|`sub-price`|Displays amount in fiat before adding discount, tax or shipping charges|
|`setItems()`|`items`|Consists of objects with predefined fields: name, cost, qty (quantity), total|
|`setExtras()`|`extras`|Additional elements on an invoice e.g fees, taxes or shipping price|

The `addOrderItem()` and `addExtraItem()` functions are used to add items to the `items` and `extras` arrays respectively.
Add instances of the `OrderItem` and `ExtraItem` classes into arrays.

Getters are also available for all properties. The arrays `items` and `extras` can be obtained using `getItems()` and `getExtras()`.

Example of user-data object as JSON:

```json
"user-data": {
    "title": "Invoice for shop",
    "merchant": "SHOP",
    "url": "http://exampleshop.com",
    "price": "$170",
    "sub-price": "$160",
    "items": [
        {
            "name": "box",
            "cost": "$10",
            "qty": 10,
            "total": "$100"
        },
        {
            "name": "hat",
            "cost": "$5",
            "qty": 10,
            "total": "$50"
        },
        {
            "name": "cap",
            "cost": "$1",
            "qty": 10,
            "total": "$10"
        }
    ],
    "extras": [
        {
            "name": "Shipping",
            "price": "$5"
        },
        {
            "name": "Tax name",
            "price": "$5"
        }
    ]
}
```

Same data via UserData class:

```php
$userData = UserData::init()
    ->setTitle('Invoice for shop')
    ->setMerchant('SHOP')
    ->setUrl('http://exampleshop.com')
    ->setPrice("$170")
    ->setSubPrice("$160")
    ->addOrderItem("box", "$10", 10, "$100")
    ->addOrderItem("hat", "$5", 10, "$50")
    ->addOrderItem("cap", "$1", 10, "$10")
    ->addExtraItem("Shipping","$5")
    ->addExtraItem("Tax name","$5");

// Set to invoice
$invoice->userData($userData);

```

User-data example result:

![User-data invoice result](/user-data-invoice-example.png)

Thus, the UserData class makes it easy to add custom data to an invoice and influence its appearance.
