# Invoice class

All classes are well documented in the code, so let's consider only the advanced invoice configuration options.
Creating the logging, database and callbacks handlers was covered in [Five steps guide](/five-steps-guide), so we won't dwell on that here.
The main class of the library is `Apirone\SDK\Invoice`. Almost all work with invoices is done through it.

## Properties

All properties below, except the `meta` property, are read-only and cannot be edited directly.

|Property|Type|Description|
|:---:|:---:|---|
|`$id`|int|Table record ID (auto increment).|
|`$time`|int|Time of creation or last update.|
|`$order`|int|Your app OrderID. Set to 0 by default.|
|`$invoice`|string|Invoice identifier.|
|`$status`|string|Invoice current status.|
|`$details`|[InvoiceDetails](invoice-details)|Response from Apirone API with invoice details.|
|`$meta`|array|Array of [additional invoice parameters](invoice#meta-property-and-additional-parameters).|
|`$createParams`|array|An array of properties used to configure and create the invoice.|


## Main methods

|Method|Type|Description|
|:---:|:---:|---|
|`init()`|static|Initializes an invoice instance.|
|`get()`|static|Gets an invoice from storage and returns the Invoice class.|
|`getByOrder()`|static|Retrieves all invoices associated with this payment ID.|
|`fromJson()`|static|Creates an invoice instance from JSON or stdClass.|
|`toJson()`|object|Returns an invoice data as stdClass.|
|`toJsonString()`|object|Returns an invoice data as JSON string.|
|`update()`|object|Updates invoice data via the API. Saves it to storage if the data has been updated.|
|`save()`|object|Saves an invoice data into storage.|
|`info()`|object|Returns invoice info such as API invoice info endpoint.|

## Create invoice

You need to have an Apirone account to create an invoice. Use the Settings class to create and configure an account.

`Invoice::init($account, $abbr)` returns a new invoice instance for further configuration and creation.
Both parameters are required: account ID and cryptocurrency abbreviation abbreviation.

### Setting up invoice parameter methods

See [API docs](https://apirone.com/docs/invoices/#create-invoice) for a full description of the parameters.

|Method|API parameter|Description|
|:---:|:---:|---|
|`amount()`|`amount`|Amount for the checkout in the selected cryptocurrency in minor units.|
|`lifetime()`|`lifetime`|Duration of invoice validity (indicated in seconds). *|
|`expire()`|`expire`|Invoice expiration time in [ISO-8601](https://www.iso.org/iso-8601-date-and-time-format.html) format. *|
|`callbackUrl()`|`callback-url`|Used for invoice status updates notifications.|
|`linkback()`|`linkback`|Success payment redirect url.|
|`userData()`|`user-data`|[User-data](https://apirone.com/docs/invoices/#example-of-user-data-object) JSON-object or [UserData class](user-data) instance.|
|`order()`|-|PaymentID value in your application. Used in SDK only.|

`*` - If both parameters are specified, the `expire` parameter will take precedence.

All methods return a `$this` object and support method chain calls.

### User-data parameter

You can manually create this JSON object, but a more convenient way is to use the [UserData](/user-data) class. This class has all the necessary methods to set properties and its use guarantees correct syntax of the object.

Usage example:

```php
$userData = UserData::init()
    ->title('My invoice Title')
    ->merchant('Merchant name');
    ->price('$ 100');


$invoice->init($account_id, 'btc')->userData($userData);

```

### Create method

To create an invoice in the API, the `create()` method is used,after calling it you cannot change the invoice parameters.
You will only be able to update the status and history of the invoice.

> [!NOTE]
> Create an invoice only after all the necessary properties have been set.

Once the invoice has been successfully created, a record with the invoice data is added to the table.
The `invoice` property, which contains the invoice identifier, becomes available to you.

## Existing invoice & order invoices

|Class method|Return|Description|
|:---:|:---:|---|
|`get()`|Invoice|Gets invoice by ID.|
|`getByOrder()`|Array|Gets an array of invoice objects for an order with orderID.|
|`update()`|Boolean|Updates invoice data via the API. Saves it to storage if the data has been updated.|
|`save()`|Boolean|Saves an invoice data into storage.|
|`info()`|JSON|Returns invoice info such as API invoice info endpoint. To retrieve private information, set the optional parameter to true.|

To retrieve an invoice from the database, execute the static method `Invoice::get($invoice)`,
where the invoice ID is used as a parameter. Returns null if not found.


In addition, you can update the invoice information by calling the `update()` method on the invoice instance.

If the invoice data has changed, such as its status, it will be automatically updated in the database.

If you set the `order` property when creating an invoice, you can retrieve all invoices associated with that payment.

Use `Invoice::getByOrder($order)` method for this and get an array with invoices or an empty array if no invoices are found.

### Meta property and additional parameters

The `meta` property is used to store additional parameters and can be modified after the invoice is created. It is a `stdClass` object with key-value parameters.

To get/set values, use the `meta` property or the `meta()` method with an `stdClass` as parameter.

To set and get individual parameter, also use the `property/method`, where name is the name of the desired parameter. To delete a parameter, call the method with the parameter name and an empty value.

After adding or changing additional properties, you must call the `save()` method.

For example:

```php
// store value into meta
$invoice->myParameter('My parameter value');

// get value from meta
$param = $invoice->myParameter;

// remove value from meta
$invoice->myParameter();

// save invoice meta
$invoice->save();
```
## Callback handler


The Apirone service informs you of invoice events with callbacks.
Create a page that supports the POST method and add `Invoice::callbackHandler()` call to it.
You will use the URL of this page when creating the Invoice as a callback address.



Receiving API callbacks is important for the correct processing of invoices.
To do this, you need to create a URL that will respond to requests from the apirone service.
There is a special static method for this. You only need to register its call.

The method supports two parameters for additional data processing in your system.
Both parameters must be callback functions. The first parameter, `$paymentProcessing`, is used to process the payment in your system.
The second parameter, `$callbackChecker`, is used for preliminary `validation/processing` of input data.

```php
// Create checker function
$callbackChecker = static function($invoice) {
  // Your checker logic
}

// Create processing function
$paymentProcessing = static function($invoice) {
  // Your processing logic
}

//
Invoice::callbackHandler($paymentProcessing, $callbackChecker);

```

  > [!INFO]
> `$callbackChecker` function calls before `$paymentProcessing` function!
