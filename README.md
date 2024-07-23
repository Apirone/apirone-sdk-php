<img src="https://apirone.com/docs/logo.svg" width="200">

# Apirone SDK PHP <!-- omit in toc -->

[![GitHub license](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](https://raw.githubusercontent.com/Apirone/apirone-sdk-php/main/LICENSE)

This Library provides classes for working with [Apirone invoices](https://apirone.com/docs/invoices/).
Easy integration of cryptocurrency payments.

---

- [Requirements](#requirements)
- [Installation and preparation for use](#installation-and-preparation-for-use)
    - [Assets configure](#assets-configure)
    - [Database and Logs handlers](#database-and-logs-handlers)
- [Settings](#settings)
- [Create an Invoice](#create-an-invoice)
- [Get an existing invoice](#get-an-existing-invoice)
- [Callbacks handler](#callbacks-handler)
- [Show invoice](#show-invoice)
    - [Render loader](#render-loader)
    - [Render AJAX response](#render-ajax-response)
- [Examples and playground](#examples-and-playground)
- [Support](#support)
- [License](#license)

---

## Requirements

- PHP 7.4 or higher with cURL and JSON extentions. Tested up to 8.2.
- MySQL or MariaDB
- [Apirone API PHP](https://github.com/Apirone/apirone-api-php)

## Installation and preparation for use

Use [composer](https://getcomposer.org/) for library installation.

    composer require apirone/apirone-sdk-php

### Assets configure

Copy ```src/assets``` folder into your public html.
On the invoice display page you need to add ```css/styles.css``` and ``js/script.js`` from assets folder. Also, you can use minimized versions.

### Database and Logs handlers

To connect library to your database-engine you need to create two callback functions and pass them to the library.

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

For logging you can use a callback function or logger implementation [Psr/Log/LoggerInterface](https://packagist.org/packages/psr/log)

Log callback example:

```php

$logger = static function($level, $message, $context) {
    print_r([$level, $message, $context]);
};

```

Psr/Log example:

```php

$logger = new /Psr/Log/LoggerInterface();

```

Set handlers to the library:

```php

use Apirone\SDK\Invoice;

Invoice::db($db_handler, 'table_prefix_');
Invoice::setLogger($logger);

```

Create an invoice table:

```php
use Apirone\SDK\Service\InvoiceDb;

// Create the invoice table if it doesn't exist
InvoiceDb::install('table_prefix', $charset = 'urf8', $collate = 'utf_general_ci');
```

You can create a common file to set up an account class and include it in your code

```php
// Setup invoice handlers and Settings
Invoice::db($db_handler, $table_prefix);
Invoice::log($log_handler);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));
```

## Settings

The settings class is used to create an account, manage currency settings, and save and restore the settings object.

Create a new settings object:
```php
use Apirone\SDK\Model\Settings;

$settings = Settings::init();
$settings->createAccount();

// Save the settings object to the file
$settings->toFile('absolute/path/to/settings.json');

// Get settings as JSON-object to save it to the database
$json = $settings->toJson();

```

Load an existing settings object

```php
$fromFile = Settings::fromFile('/absolute/path/to/settings.json');

// For example you have get_option function
$json = get_option('apirone_settings');

$fromJson = Settings::fromJson($json);

```

## Create an Invoice

```php

use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

// Setup invoice handlers and Settings
Invoice::db($db_handler, $table_prefix);
Invoice::log($log_handler);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));

$invoice = Invoice::init('btc', 25000);

$invoice->callbackUrl('https://example.com/callback_page');
$invoice->lifetime(1800);

$invoice->crete();

// You can see the created invoice JSON data
$json = $invoice->toJson();
```

## Get an existing invoice

```php
use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

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
// Create an order process function
$order_status_handler = static function ($invoice) {
    // You need to set the order_id when creating an invoice.
    $order_id = $invoice->order();
    // ... Write here your business logic
}
// ... Setup invoice handlers and settings


Invoice::callbackHandler($order_status_handler);
```

## Show invoice

### Render loader

To display the invoice, use the ```renderLoader()``` function.
Also, a style file and js script should be added to the page template from the assets folder.

```php
use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

// Setup invoice handlers and Settings
Invoice::db($db_handler, $table_prefix);
Invoice::log($log_handler);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));

// Set invoice data url - render ajsx response page
Invoice::dataUrl('render_ajax_response.php');

// In case when $invoice_id does not set function try to find id from request params - $_GET['invoice']
$ = Invoice::renderLoader();

// If you got the required invoice ID any other way, just pass it to the function
$id = 'MyInvoiceId';
Invoice::renderLoader($id);
```

### Render AJAX response

To update the invoice data, you need to create a URL and add the renderAjaxResponse() function to the code.
This URL used as ```Invoice::dataUrl()``` parameter.

```php
use Apirone\SDK\Invoice;
use Apirone\SDK\Model\Settings;

// Setup invoice handlers and Settings
Invoice::db($db_handler, $table_prefix);
Invoice::log($log_handler);
Invoice::settings(Settings::fromFile('/absolute/path/to/settings.json'));

Invoice::renderAjax();
```

## Examples and playground

In the ```examples``` folder, you can find a ready-made implementation of the library usage.

You can also run the playground.
To run the playground, you need Docker and Docker Compose installed.

To do this, you need to copy the ```docker-compose.yml``` file from the ```docker``` folder to the library root folder and run the command:

    docker-compose up -d --build

After the start of the containers, the playground will be available in the browser at http://localhost

## Support

- https://github.com/Apirone/apirone-sdk-php/issues

- support@apirone.com

## License

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
SOFTWARE.
