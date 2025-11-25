# Settings class

The second most important class is the `Apirone\SDK\Model\Settings` class, which is used to handle the account, retrieve network and currency lists, set forwarding addresses and policy fees.
You can also use the class as a single place to store the parameters you need.

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

Use the `account` and `transferKey` as properties to get the values.

### Save and restore

Three methods of saving an object are available to you:

```php
// Save to file
$settings->toFile('/absolute/path/to/file.json');

// Get as stdClass object
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

The `Settings::currencies` property returns an associative array available currencies, with currency abbreviations as keys. This property is lazy and loads the list the first time it is accessed. The property is also loaded when the network property is accessed, if it hasn't been loaded previously.

The `Settings::networks` property returns an associative array of available networks and their tokens, if any, with currency abbreviations as keys.

> [!INFO]
> Differences between currency types: a network refers to a blockchain; for example, Bitcoin is both a currency and a network. Similarly, Tron is both a currency and a network. Meanwhile, USDT or USDC, which use the Tron network as their medium of exchange, are both a currency and a token.

> [!TIP]
> If the currency `abbr` property contains `@`, this is an indication that the currency is a token.
> For example, `trx` is a network, `usdt@trx` is a Tron-based token.

Also you can retrieve any available currency by its abbreviation using the `currency('abbr')` method.

The `loadCurrencies()` method retrieves currency settings from the account and sets them in the currency property.

To save currency changes to the account, use the `saveCurrencies()` method.

## Additional Parameters storage

### Meta property

The `meta` property is used to store additional parameters. It is an `stdClass` class with key-value parameters.

To get/set values, use the `meta` property or the `meta()` method with an `stdClass` as parameter.

To set and get individual parameters, also use the `property/method`, where name is the name of the desired parameter. To delete a parameter, call the method with the parameter name and an empty value.

For example:

```php
// store value into meta
$settings->myParameter('My parameter value');

// get value from meta
$param = $settings->myParameter;

// remove value from meta
$settings->myParameter();
```
