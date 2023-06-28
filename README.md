<img src="https://apirone.com/docs/logo.svg" width="200">

# Apirone Invoice PHP | SDK

[![GitHub license](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](https://raw.githubusercontent.com/Apirone/apirone-invoce-php/main/LICENSE)

This Library provides classes for working with [Apirone invoices](https://apirone.com/docs/invoices/).
Easy integration of cryptocurrency payments.

## Requirements

- PHP 7.4 or higher with cURL and JSON extentions. Tested up to 8.2.
- MySQL or MariaDB
- [Apirone API PHP](https://github.com/Apirone/apirone-api-php)

## Installation and preparation for use

Use [composer](https://getcomposer.org/) for library installation.

    composer require apirone/apirone-invoice-php

### Assets configure

Copy ```src/assets``` folder into your public html.
On the invoice display page you need to add ```css/styles.css``` and ``js/script.js`` from asssets folder. Also you can use minimized versions.

### Database and Logs handlers

To connect library to your database-engine and log-engine you need to create two callback function and pass them to the library.

Db handler example for php MySQLi
```php

// Our MySQL engine example
$conn = new mysqli('host', 'user', 'pass', 'database');
$conn->select_db('database');

// DB MySQL handler example
$db_handler = static function($query) {
    global $conn;
    $result = $conn->query($query, MYSQLI_STORE_RESULT);

    if (!$result) {
        return $conn->error;
    }
    if (gettype($result) == 'boolean') {
        return $result;
    }
    return $result->fetch_all(MYSQLI_ASSOC);
};

```

Log handler example:

```php
$log_handler = static function($message) {
    // Handle message with yor log engine
    // Message is an associative array with two keys 'body' and 'details'
    // For example you have log function to provide logging
    // log($message['body'], $message['details']);

    print_r($message);
};
```

Set a handlers to the library:

```php
use Apirone\Invoice\Invoice;

Invoice::db($db_handler, 'table_prefix_');
Invoice::log($log_handler);
```

Create an invoce table:

```php
use Apirone\Invoice\Service\InvoiceDb;

// Create invoice table if not exists
InvoiceDb::install('tbale_prefix', $charset = 'urf8', $collate = 'utf_general_ci');
```

For more Datatbase details see [docs/Settings.md](./docs/Database.md)

Create common file for invoce class configure:

```php
// Setup invoice handlers and Settings
Invoice::db($db_handler, $table_prefix);
Invoice::log($log_handler);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));
```

## Settings

The settings class is used to create an account, manage currency settings, and save and restore the settings object.

Create new settings object:
```php
use Apirone\Invoice\Model\Settings;

$settings = Settings::init();
$settings->createAccount();

// Save settings object to file
$settings->toFile('absolute/path/to/settings.json');

// Get settings as JSON-object to save it to database
$json = $settings->toJson();

```

Load existing settings object

```php
$fromFile = Settings::fromFile('/absolute/path/to/settings.json');

// For example you have get_option function
$json = get_option('apirone_settings');

$fromJson = Settings::fromJson($json);

```

For more Settings class details see [docs/Settings.md](./docs/Settings.md)

## Create an Invoice

```php

use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;

// Setup invoice handlers and Settings
Invoice::db($db_handler, $table_prefix);
Invoice::log($log_handler);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));

$invoice = Invoice::init('btc', 25000);

$invoice->callbackUrl('https://example.com/callback_page');
$invoice->lifetime(1800);

$invoice->crete();

// You can see created invoice JSON data
$json = $invoice->toJson();
```

## Get existing invoice

```php
use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;

// Setup invoice handlers and Settings
Invoice::db($db_handler, $table_prefix);
Invoice::log($log_handler);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));

$id = '9qAs2OQao43VWz72';
$invoice = Invoice::getInvoice($id);

```

## Callbacks handler
Apirone service interacts with the library through [callbacks](https://apirone.com/docs/receiving-callbacks/#invoices).
To get invoice statuses, you need to create a URL and add a callback handler to your code.
This address must be set when creating a new invoice.

If you need to process the order status based on the status of the invoice, you can create a callback function to handle the status change.

```php
// Create oder process function
$order_status_handler = static function ($invoice) {
    // You need to set the order_id when creating an invoice.
    $order_id = $invoice->order();
    // ... Write here your business logic
}
// ... Setup invoice handlers and settings


Invoice::callbackHandler($order_status_handler);
```
## Show invoice

```php
use Apirone\Invoice\Invoice;
use Apirone\Invoice\Model\Settings;

// Setup invoice handlers and Settings
Invoice::db($db_handler, $table_prefix);
Invoice::log($log_handler);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));

// Set invoice data url - render ajsx response page
Invoice::dataUrl('render_ajax_response.php')

Invoice::renderLoader();
```
<!-- ## Invoice SDK usage

- [Database](./docs/Database.md)
  
- [Settings](./docs/Settings.md)

- [Invoice](./docs/Invoice.md)

- [Render](./docs/Render.md)

- [Utils](./docs/Utils.md)
## Support

* https://github.com/Apirone/apirone-invoice-php/issues  
* support@apirone.com -->

<!-- ## License

MIT License

Copyright (c) © 2017-2023. Apirone. All rights reserved.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE. -->