# Settings class

The second most important class is the `Apirone\SDK\Model\Settings` class, which is used to handle the account, retrieve network and currency lists, set forwarding addresses and policy fees, and store common invoice and SDK parameters such as `lifetime`, `title`, `debug`, etc.

## Creating and saving

There are several methods for creating an instance of a class.

### With new account

If you haven't already had an Apirone account, now is the time to create one.
Just create an instance of the class using the `Settings::init()` method and call the `createAccount()` method;

```php
use Apirone\SDK\Model\Settings;

$settings = Settings::init()->createAccount();
```

### Use existing account

If you have already had an [account](https://apirone.com/docs/account/#create-account)
and you want to create a Settings object, use special static function `fromExistingAccount()`
with the account ID and transfer key as parameters.

```php
$account = 'apr-f9e1211f4b52a50bcf3c36819fdc4ad3';
$transferKey = '4sSm9aeXQiqMBmeEs42NTjZidJuGrqm7';

$settings = Settings::fromExistingAccount($account, $transferKey);

```

### Account credentials

Use the `getAccount()` and `getTransferKey()` methods to get the values of `account` and `transfer-key`.

### Save and restore

Three methods of saving an object are available to you:

```php
// Save to file
$settings->toFile('/absolute/path/to/file.json');

// Get as JSON object
$json_settings = $settings->toJson();

// Get as string. Use JSON_PRETTY_PRINT or 128 as parameter to get pretty print.
$json_settings = $settings->toJsonString();
```


::: details Settings.json example

```json
<!--@include: /settings.json-->
```

:::

Use two methods for restore:

```php
// From file
$file_settings = Settings::fromFile('/absolute/path/to/file.json');

// From JSON-object or string
$json_settings = Settings::fromJson($settings);
```

## Currencies and networks

Currencies are all the cryptocurrencies that Apirone supports. The list of supported currencies
can be obtained via [API](https://apirone.com/docs/service/#service-info) or by using
the `Settings::currencies()` method.

A network refers to a blockchain, for example, Bitcoin is both a currency and a network.
Similarly, Tron is both a currency and a network. At the same time, USDT or USDC using
the Tron network as a carrier is both currency and token.
If you need a list of networks use the `Settings::networks()` method.

> [!INFO]
> If the currency `abbr` property contains `@`, this is an indication that the currency is a token.
> For example, `trx` is a network, `usdt@trx` is a Tron-based token.

As mentioned above, use the `currencies()` or `networks()` methods to get a list of currencies and return an array of [Currency](/currency) classes. You can also get any currency by its abbreviation using the `currency(‘abbr’)` method.

The `loadCurrencies()` method gets the currency settings from the account and updates the local values for each of them. You must then save the Settings object locally.

To save the local currency changes to the account, use the `saveCurrencies()` method;

## Additional Parameters storage

### Meta property

Use the `meta` property to store additional parameters. This is a `stdClass` with key-value parameters.

To add and update items, use `addMeta(‘key’, ‘value’)`.
To get or delete an item by key, use `getMeta(‘key’)` and `deleteMeta(‘key’)` respectively.

To get the entire object, use `$settings->getMeta()`.
The `$settings->meta($json)` method sets the entire object.

### Predefined and Extra props

> [!WARNING]
> These properties and methods will be deprecated and removed in future versions.

The class has predefined properties that are used for general invoice settings and display.
Working with parameters is done through getters and setters. For more details see properties and methods of the class.

Use the property name to get the value and call the property name as a method with the value to set.
You can use a call chain to set values.

|Property|Set value method|Get value method|Description|
|---|---|---|---|
|`title`|`setTitle()`|`getTitle()`|Invoice title|
|`merchant`|`setMerchant()`|`getMerchant()`|Merchant name|
|`merchantUrl`|`setMerchantUrl()`|`getMerchantUrl()`|Merchant store URL|
|`timeout`|`setTimeout()`|`getTimeout()`|Invoice lifetime|
|`factor`|`setFactor()`|`getFactor()`|Amount adjustment factor|
|`backLink`|`setBackLink()`|`setBackLink()`|Invoice backlink|
|`logo`|`setLogo()`|`getLogo()`|Show the logo on the invoice|
|`qrOnly`|`setQrOnly()`|`getQrOnly()`|Show qrCode template|
|`debug`|`setDebug()`|`getDebug()`|Debug mode|
|`extra`|`setExtraObj()`|`getExtra()`|Extra properties stdCLass|

In case you need to store additional parameters, use `setExtra(‘key’, ‘value’)` and `getExtra(‘key’)`.
