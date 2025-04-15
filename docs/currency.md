# Currency class

The currency class is designed to work with Apirone currencies and contains all necessary data and methods.
It is used to synchronize settings between the API and local settings,
allows you to set the transfer address and processing fees in the API.

## Create an instance

To create an instance, call the `Currency::init()` method and pass the json currency object
received from the API [account info](https://apirone.com/docs/account/#account-info) method as a parameter.

> [!INFO]
> If you work with currencies via the [Settings](/settings) class, you do not have to initialize the Currency class yourself.

## Properties

|Property|Description|
|---|---|
|`name`|The full name of the cryptocurrency|
|`abbr`|Cryptocurrency abbreviation|
|`units`|The minimal unit used for cryptocurrency|
|`unitsFactor`|Factor for minimal unit|
|`dustRate`|Fee free threshold. We don't charge the service fee for payments under this amount|
|`address`|Forwarding destination address|
|`policy`|Processing fee policies|
|`error`|Returns error property if set|

Almost all properties are read-only. Two properties `address` and `policy` are available for modification.
To do this, use `$class->address()` and `$class->policy()` methods.

## Sinchronize with account

|Method|Description|
|---|---|
|`loadSettings()`|Load currency settings from account|
|`saveSettings()`|Save currency settings to account|
|`hasError()`|Checks is the currency has an error|

## Additional methods

|Method|Description|
|---|---|
|`isTestnet()`|Checks whether the currency is a testnet. For example tbtc.|
|`isNetwork()`|Is the token over network (blockchain)|
|`isToken()`|Is a network-based token.|
|`isStablecoin()`|Returns whether the currency is a stablecoin|
|`getTokens()`|Returns an array with network tokens. An array of all currencies is used as a parameter.|
