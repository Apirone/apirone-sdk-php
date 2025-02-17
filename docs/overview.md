# Overview

Despite the small size this library is a powerful SDK for integrating [Apirone Invoices](https://apirone.com/docs/invoices/) into your PHP application.
You can also work with the [Apirone API](https://apirone.com/docs/) using the [apirone-api-php](https://apirone.github.io/apirone-api-php/) library, which is also part of the SDK.

SDK simplifies work with requests to API and allows you to work with Invoice as a php class.
Also the library supports work with account settings, has tools for work with database and logging.

## Requirements

- PHP 7.4 or higher. Tested up to 8.4.
- cURL extension
- JSON extension

## Installation

Use [composer](https://getcomposer.org/) for library installation.

```bash
composer require apirone/apirone-sdk-php
```

You can also download or clone the library from our github [repository](https://github.com/Apirone/apirone-sdk-php).

## Quick structure overview


```
src/
├─ assets/
│  └─ ...
├─ Model/
│  ├─ Settings/
│  │  └─ Currency.php
│  ├─ Settings.php
│  ├─ UserData.php
│  └─ ...
└─ Service/
   ├─ InvoiceDb.php
   ├─ Render.php
   ├─ Utils.php
   └─ ...
```

- __assets__ - Contains js, css and image files for displaying the invoice.
- __Model__ - Contains classes for working with various data.
    - __Settings.php__ - Used to handle accounts, currencies, destinations, tariffs, synchronization and storage of settings.
        - __Currency.php__ - Contains all currency properties.
    - __UserData.php__ - Used to configure some additional information about the invoice (includes following fields: title, merchant,url, price, sub-price, items, extras, etc.)
- __Service__ - Contains service classes and invoice templates.
    - __InvoiceDb.php__ - Work with your Database.
    - __Render.php__ - Display the Invoice.
    - __Utils.php__ - Contain some useful methods.
- __Invoice.php__ - The main entry point to the library.

All other classes of the library are not used directly, but will be discussed in the next sections of the documentation.
