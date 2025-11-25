# Invoice class

All classes are well documented in the code, so let's consider only the advanced invoice configuration options.
Creating the logging, database and callbacks handlers was covered in [Five steps guide](/five-steps-guide), so we won't dwell on that here.
The main class of the library is `Apirone\SDK\Invoice`. Almost all work with invoices is done through it.

## Main invoice properties

|Property|Type|Description|
|:---:|---|---|
|`$id`|int|table record ID (auto increment)|
|`$order`|int|Your app OrderID. Set to 0 by default|
|`$invoice`|string|Invoice Identifier|
|`$status`|string|Invoice current status|
|`$details`|[InvoiceDetails](invoice-details)|Response from Apirone API with invoice details.|
|`$meta`|array|Array for additional invoice parameters|

A static property `$settings` is also available, which can contain an instance of [Settings](settings) class.

## Create invoice

You need to have an Apirone account to create an invoice. Use the Settings class to create and configure an account.

`Invoice::init()` returns a new invoice instance for further configuration and creation.
The currency abbreviation is a required parameter, and the amount is optional.
However, you can change the currency value before calling the `create()` method.

### Creation parameters

See [API docs](https://apirone.com/docs/invoices/#create-invoice) for a full description of the parameters.

|Class method|API parameter|Description|
|---|---|---|
|`currency()`|`currency`|Currency type supported by service. Required|
|`amount()`|`amount`|Amount for the checkout in the selected currency|
|`lifetime()`|`lifetime`|Duration of invoice validity (indicated in seconds) *|
|`expire()`|`expire`|Invoice expiration time in [ISO-8601](https://www.iso.org/iso-8601-date-and-time-format.html) format *|
|`callbackUrl()`|`callback-url`|Used for invoice status updates notifications|
|`linkback()`|`linkback`|Success payment redirect url|
|`userData()`|`user-data`|[User-data](https://apirone.com/docs/invoices/#example-of-user-data-object) JSON-object or [UserData class](user-data) instance|
|`order()`|-|PaymentID value in your application. Used in SDK only|

`*` - If both parameters are specified, the `expire` parameter will take precedence.

All methods return a `$this` object and support method chain calls.

### User-data parameter

You can manually create this JSON object, but a more convenient way is to use the [UserData](/user-data) class. This class has all the necessary methods to set properties and its use guarantees correct syntax of the object.

Usage example:

```php
$invoice->init('btc');
// ...
$invoiceUserData = UserData::init()
    ->title('My invoice Title')
    ->merchant('Merchant name');
    ->price('$ 100');

$invoice->userData($invoiceUserData);
// ...
```

### Create method

To create an invoice in the API, the `create()` method is used,after calling it you cannot change the invoice parameters.
You will only be able to update the status and history of the invoice.

> [!NOTE]
> Create an invoice only after all the necessary properties have been set.

Once the invoice has been successfully created, a record with the invoice data is added to the table.
The `invoice` property, which contains the invoice identifier, becomes available to you.

## Existing invoice & order invoices

To retrieve an invoice from the database, execute the static method `Invoice::get($invoice)`, where the invoice ID is used as a parameter.

In addition, you can update the invoice information by calling the `update()` method on the invoice instance.
If the invoice data has changed, such as its status, it will be automatically updated in the database.

Methods for saving meta information on the invoice are also available to you if you need them.
Use the `setMeta($key, $value )` method to set and the `getMeta($key)` method to get the value.

If you set the `order` property when creating an invoice, you can retrieve all invoices associated with that payment.

Use `Invoice::getByOrder($order)` method for this and get an array with invoices or an empty array if no invoices are found.

### Meta property

The `meta` property is used to store additional parameters and can be modified after the invoice is created. It is a `stdClass` class with key-value parameters.

To get/set values, use the `meta` property or the `meta()` method with an `stdClass` as parameter.

To set and get individual parameter, also use the `property/method`, where name is the name of the desired parameter. To delete a parameter, call the method with the parameter name and an empty value.

For example:

```php
// store value into meta
$invoice->myParameter('My parameter value');

// get value from meta
$param = $invoice->myParameter;

// remove value from meta
$invoice->myParameter();
```
