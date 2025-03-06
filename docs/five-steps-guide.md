# Five-steps integration guide

These five steps will help you easily integrate Apirone Payment Gateway into your application.

## Step 1. Configure logs and database

We use callback functions for compatibility with different code to work with databases or logs.

### Create a log handler

Create a static function and wrap the logging system in it as a callback.
If you do not use logging, you can skip this step.
Use the standard PSR-3: Logger Interface, which has a `log()` method, 
or use your own function with the following parameters: `log($level, $message, $context)`

Simple "log to file" implementation:

```php
<?php
use Apirone\SDK\Invoice;

$logger_handler = static function ($level, $message, $context) {
    $log_file = '/path/to/log_file.txt';
    $data = [$level, $message, $context];
    file_put_contents($log_file, print_r($data, true) . "\r\n", FILE_APPEND);
};

// Set logger handler via Invoice class
Invoice::logger($logger_handler);

```

### Create database handler

As with logs, wrap the DB class or function used to manipulate the database
into a callback function that is passed an SQL query as a parameter.

Once you have created your database callback function, simply set it as a DB handler.

```php
$db_handler = static function ($query) {
    // Your implementation here;
    // Just run a sql query and return the result
}

Invoice::db($db_handler);
```

### Invoice table

After installing the DB-handler, create an invoice table in the database.
To do this, just execute the `InvoiceDB::install()` method once.

```php
<?php
use Apirone\SDK\Service\InvoiceDb;

// Create an invoice table
InvoiceDb::install();
```

## Step 2. Working with Apirone callbacks

### Create callbacks handler

The Apirone service informs you of invoice events with callbacks.
Create a page that supports the POST method and add `Invoice::callbackHandler()` call to it.
You will use the URL of this page when creating the Invoice as a callback address.

### Process callback data

When the callback is received you can process its data in your app.
Create a handler and simply add it as a parameter to the `Invoice::callbackHandler()` function.
To authenticate the order in your app, set its ID to the `Invoice::$order` property
at the creation stage and get it from the invoice data at the callback processing stage.

```php
$my_apirone_callback_handler = static function ($invoice) {
    $my_app_order_id = $invoice->order;

    // Process your business logic with $my_app_order_id here
};

Invoice::callbackHandler($my_apirone_callback_handler);
```

## Step 3. Apirone account

A special [Settings](/settings) class that allows you to work with accounts, currencies, invoice parameters, fees, etc.

### New or existing account

If you don't have an account yet or want to create a new one, just init a new Settings object and call `createAccount()` method.

```php
<?php
use Apirone\SDK\Model\Settings;

// Create a settings object and account.
$settings = Settings::init()->createAccount();

```

If you already have an account, you [can create](settings#use-existing-account) a Settings object using the account ID and transfer key as parameters.

```php
$settings = Settings::fromExistingAccount('account-id', 'transfer-key');

```

### Setting up a forwarding

Destination addresses must be set before the invoice can be created.
The destination is set separately for each currency and can only be one.

> [!WARNING]
> If you do not specify a destination, no automatic forwarding will take place and funds will accumulate in the account.

```php
// Set the btc currency and the transfer address for forwarding
$settings->getCurrency('btc')->setAddress('3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu');

// Save currency settings into account
$settings->saveCurrencies();
```

### Save and restore Settings object

The easiest way to do this is to save the settings to a text file. It is also very easy to restore settings from the file. A more complicated variant is that you implement the way of storing settings yourself.

```php

// Save to file
$settings->toFile('/absolute/path/to/settings.json');

// Restore from file
$settings = Settings::fromFile('/absolute/path/to/settings.json');

```

## Step 4. Invoice creating

A mandatory condition for creating an invoice is the presence of a database handler and a configured Settings object.
To add the above, use the static methods of the Invoice class before creating an invoice instance.

```php
// Setup DB and Settings into Invoice class
Invoice::db($db_handler, $table_prefix);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));
```

---

Unlike APIs, the library has two ways to create an invoice -
set the amount in cryptocurrency or create from fiat currency.
Let's look at both methods in more detail.

### Via crypto amount

The mandatory parameter for creating an invoice is the currency. All other parameters are optional.
Initialize the invoice with the desired currency and call the `Invoice::create()` method.
You can also set other parameters before you call the `Invoice::create()` method.

```php
$simplest_invoice = Invoice::init('btc')->create();
```

If you have created the database function and the Settings object correctly, the result of the execution
will be a created invoice and a record will be added to the invoice table.
The created invoice will wait for any amount to be paid, after which its [status](https://apirone.com/docs/invoices/#invoice-status) will change to `completed`.

To create a fixed amount invoice, you can set it up by calling the `Invoice::init()` method or using the `Invoice::amount()` method.
The amount is indicated in minor units (e.g. 0.005 BTC shall be specified as 500000, e.g. for usdt@trx: 50 usd shall be specified as 50000000).

```php
// 0.005 btc
$invoice_btc = Invoice::init('btc', 500000)->create();

// 50 usd via usdt@trx
$invoice_usdt = Invoice::init('usdt@trx')
    ->amount(50000000)
    ->create();
```

### Via fiat amount

If you have an amount in fiat currency, the first thing you need to do is convert it to cryptocurrency,
as the API only supports creating an account in cryptocurrency.

But the library has a special method `Invoice::fromFiatAmount()` that will do it for you.
The amount specified in fiat will be converted to cryptocurrency automatically.

```php
// Create an invoice for 99.50 USD via BTC
$invoice_from_fiat = Invoice::fromFiatAmount(99.50, 'usd', 'btc');

// Instead of this comment, you can set other invoice parameters.

// Finally call create() method
$invoice_from_fiat->create();
```

See the list of [supported fiat currencies](https://apirone.com/docs/supported-currencies-and-networks/).

## Step 5. Displaying an Invoice

The library provides methods that will allow you to add an invoice to your html.
A special [Render](/render) class that will allow you to do this.

### Dynamic data update

Create a page that supports the POST method, set its address using the `Invoice::dataUrl()` method,
and then add a call to the `Invoice::renderAjax()` method.

```php
// Set invoice dataUrl
Invoice::dataUrl('https://my-domain.com/render-invoice-data.php');

// Return invoice data
Invoice::renderAjax();
```

### Displaying an invoice

Add `src/assets/css/styles.css` and `src/assets/js/script.js` to the page where you want to display the invoice.

In the right place in the html markup, add a call to the `Invoice::renderLoader()` method.
On this page, you should have the DB handler added and the Settings object loaded just like in [Step 4](#step-4-invoice-creating).
See the code below for an example.

```php
use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

// Setup DB and Settings
Invoice::db($db_handler, $table_prefix);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));

// Setup Invoice Data Url
Invoice::dataUrl('https://my-domain.com/render-invoice-data.php');

?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="/assets/js/script.min.js" type="text/javascript"></script>
        <link href="/assets/css/styles.min.css" rel="stylesheet">
    </head>
    <body style="margin: 0;">
        <?php echo Invoice::renderLoader(); ?>
    </body>
</html>

```

## What's Next?

[Dive deeper](/invoice) - by learning the classes of the library,
you will be able to control all available parameters more flexibly
and make more fine-tuning and use the full power of the library!
