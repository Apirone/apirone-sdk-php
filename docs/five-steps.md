# Five-steps integration guide

These five steps will help you easily integrate Apirone Payment Gateway into your application.

## Step 1. Configure logs and database

For compatibility with various classes and functions for working with databases or logs, we use callback functions.

### Creating a log handler

You need to create a static function and wrap your logging system as a callback. If you don't use logging, you can skip this step.

You can use the standard PSR-3: Logger Interface which has a `log()` method. or use your own function with the following parameters: `log($level, $message, $context)`

Simple log to file implementation

```php
$logger_handler = static function ($level, $message, $context) {
    $log_file = '/path/to/log_file.txt';
    $data = [$level, $message, $context];
    file_put_contents($log_file, print_r($data, true) . "\r\n", FILE_APPEND);
};

// Set logger handler via Invoice class.
Apirone\SDK\Invoice::logger($logger_handler);

// Or use LoggerWrapper class directly
Apirone\API\Log\LoggerWrapper::setLogger($logger_handler);

```

See `Apirone\API\Log\LoggerWrapper` for details.

### Database query handler

Just like with the logs, we need to wrap your database using your class or function that is used in your system to work with the DB.

The required parameter of the function is a string. In fact, this is a SQL-query that must be executed and return the query result.
For boolean results, return true or false. For data rows use fetch all result rows as an associative array.

Once you have created your database callback function, simply set it as a DB handler.

```php
$db_handler = static function ($query) {
    // Your implementation here;
}

Apirone\SDK\Service\InvoiceDB::setHandler($db_handler);

// Use Invoice::db() to set both db handler and table prefix if you need.
Apirone\SDK\Invoice::db($db_handler, 'pfx_');
```

### Invoice table creation

For normal operation, it is necessary to create an invoice table in the database.
Just execute the `InvoiceDB::install()` method once.

On this step you can set table prefix, charset and database collate.
Default values are: __$prefix__ is `empty`, __$charset__ is `utf8mb4`, __$collate__ is `utf8mb4_general_ci`.

```php
Apirone\SDK\Service\InvoiceDb::install();
```

If you need to delete a table, use `InvoiceDB::uninstall()` method.

## Step 2. Apirone API callback handler

### Create handler

Apirone API informs users about events via callbacks. To process them, you need to create a function that will be accessible from the Internet and support the POST request method. For example, at the address `https://my-domain.com/aporone-callback-handler.php`.

To correctly handle callbacks from the API, you must use the `Invoice::callbackHandler()` method. It must be the last one in the code.

### Process callback data

If you need to process a callback from API in your system, you need to create a static handler function and simply pass it to the method as a parameter.

```php
$my_apirone_callback_handler = static function ($invoice) {
    $my_system_payment_id = $invoice->order; // Set on Invoice create step

    // Process your business logic with payment_id here
};

Apirone\SDK\Invoice::callbackHandler($my_apirone_callback_handler);
```

## Step 3. Settings creating

For ease of working with SDK, there is a special Settings class, which allows you to work with accounts, currencies, invoice parameters, etc.

### Create new account

If you don't have an account yet or want to create a new one, just create a new Settings object and create an account. You can use arrow functions.

```php
    // Create a settings object and account.
    $settings = Apirone\SDK\Model\Settings::init();
    $settings->createAccount();

```

### Use existing account

If you already have an account, you can create a Settings object using the account ID and transfer key as parameters.

```php
    use Apirone\SDK\Model\Settings;

    // As an example, let's take the values from the Apirone docs
    // https://apirone.com/docs/account/#response-example
    $account = 'apr-f9e1211f4b52a50bcf3c36819fdc4ad3';
    $transferKey = '4sSm9aeXQiqMBmeEs42NTjZidJuGrqm7'
    
    // Create settings object with existing account
    $settings = Settings::fromExistingAccount($account, $transferKey);

```

### Setup transfer destination addresses

Destination addresses must be set before the invoice can be created.
The destination is set separately for each currency and can only be one.

__If no destination is specified, no automatic transfer will be made and the funds will accumulate in the account.__

```php
// Get btc currency and set transfer address
$settings->getCurrency('btc')
    ->setAddress('3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu');

// Saving destination and processing fee policy for the currency into account.
// Batch processing of all currencies.
// Can be called once after setting all desired values.
$settings->saveCurrencies();
```

These are not all the parameters that can be configured.
In addition to the basic parameters, the Settings object supports adding additional parameters that you can use as needed.
Read more about them below or explore Setting class for details.

### Save and restore settings

The easiest way to do this is to save the settings to a text file. In fact, it is a JSON object. It is also very easy to restore settings from the file.

A more complicated variant is that you implement the way of storing settings yourself. You can store them in a database or in any other way convenient for you. For this purpose you can unload settings into variable and restore them from it.

```php
use Apirone\SDK\Model\Settings;

// Save to file
$settings->toFile('/absolute/path/to/settings.json');

// Restore from file
$settings = Settings::fromFile('/absolute/path/to/settings.json');

// Get settings JSON object
$json = $settings->toJson();


// Restore from JSON
$settings = Settings::fromJson($json);
```

## Step 4. Invoice creating

A mandatory condition for creating an invoice is the presence of a database handler and a configured Settings object.
To add the above, use the static methods of the Invoice class before creating an invoice instance.

```php
// Setup DB and Settings into Invoice class
Invoice::db($db_handler, $table_prefix);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));
```

Let's create your first invoice. There are two ways to create an invoice - set an amount in crypto or create from fiat currency.

### Via crypto amount

The mandatory parameter for creating an invoice is the currency. All other parameters are optional.
Initialize the invoice with the desired currency and call the `Invoice::create()` method.
You can also set other parameters before you call the `Invoice::create()` method.

```php
$invoice_simple = Invoice::init('btc')->create();
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

If you have an amount in fiat currency, you can convert it to the desired crypto yourself or use another static method `Invoice::fromFiatAmount()`.
The amount specified in fiat will be converted to cryptocurrency automatically.
See the list of [supported currencies](https://apirone.com/docs/supported-currencies-and-networks/).

```php
// Create an invoice for 99.50 USD via BTC
$invoice_from_fiat = Invoice::fromFiatAmount(99.50, 'usd', 'btc');

// Instead of this comment you can set other parameters.

// Finally call create() method
$invoice_from_fiat->create();
```

## Step 5. Displaying an Invoice

A special Render class that will easily allow you to display an invoice in your app.

### Render invoice

Add a page, the result of which will be html. It can be a template from your app, or a separate page with its own markup.
Two files should be added to this page: `src/assets/css/styles.css` and `src/assets/js/script.js`.

On this page, you should have the DB handler added and the Settings object loaded just like in Step 4.
You should also customize the Render class by adding `Invoice Data URL` - a special address in your app
that will return invoice data via POST AJAX request.

You can add this parameter via the `Invoice::dataUrl()` method or directly, via the `Render::$dataUrl` property.

```php
use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

// Setup DB and Settings into Invoice class
Invoice::db($db_handler, $table_prefix);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));

// Setup Invoice Data Url via Invoice class
Invoice::dataUrl('https://my-domain.com/render-data-url.php');

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

### Render ajax response

As well as Apirone callback handler, this is a page accessible from the Internet and supports POST requests.

```php
// Setup DB and Settings into Invoice class
Invoice::db($db_handler, $table_prefix);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));

// Setup Invoice Data Url via Invoice class
Invoice::dataUrl('https://my-domain.com/render-data-url.php');

Invoice::renderAjax();
```
