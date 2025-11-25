# Getting started

The main idea of the library is to simplify integration into your PHP application,
and provide convenient methods for working with data coming the Apirone API.
It also solves the problems of sending API requests, handling errors and exceptions,
logging, working with the database, and actually displaying the invoice on your pages.
All this has already been implemented in the SDK and works out of the box.

Despite the small size this library is a powerful SDK for integrating [Apirone Invoices](https://apirone.com/docs/invoices/) into your PHP application.
The SDK simplifies work with requests to API and allows you to work with Invoice as a PHP class.

The library does not use dependencies from external developers. Only two dependencies are used here,
also developed by Apirone. This gives us extra control over the library, makes sure that
nothing will break due to external dependencies, and ensures that the library is small.

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

## File structure

Let's take a look at the structure of the library and highlight the main files and folders you will have to work with when using the library.

```
src/
├─ assets/
│  └─ ...
├─ Model/
│  ├─ Settings/
│  |  ├─ Currency.php
│  |  └─ Network.php
│  ├─ Settings.php
│  ├─ UserData.php
│  └─ ...
├─ Service/
|  ├─ Api.php
|  ├─ Db.php
|  ├─ Logger.php
│  ├─ Utils.php
│  └─ ...
└─ Invoice.php
```

- __assets__ - Contains JS, CSS and image files for displaying the Invoice.
- __Model__ - Contains classes for working with various data.
  - __Settings/Currency.php__ - Contains all currency properties and methods.
  - __Settings/Network.php__ - Contains additional methods when currency is network not a token.
  - __Settings.php__ - Used to handle accounts, currencies, destinations, tariffs, synchronization and storage of settings.
  - __UserData.php__ - Used to configure some additional information about the Invoice (includes following fields: `title`, `merchant`, `URL`, `price`, `sub-price`, `items`, `extras`, etc.)
- __Service__ - Contains service classes.
  - __Api.php__ - Local white label API implementation.
  - __Db.php__ - Database wrapper class.
  - __Logger.php__ - Logger wrapper class.
  - __Utils.php__ - Contain some useful methods.
- __Invoice.php__ - The main entry point to the library.

> [!NOTE]
> All other classes of the library are not used directly, but will be described in the next sections of the documentation.

## What's Next?

- To find out what the Apirone Payment Gateway is, you can go to the [official website](https://apirone.com) or go to the [API documentation](https://apirone.com/docs).

- To understand how to integrate the library into your project, read this short [five-steps guide](/five-steps-guide).

- To learn more about what properties and methods are available in the library's classes, go to [Dive Deeper](/invoice).

- To learn about real-world usage of the library or to see a live example locally, go to the [Usage examples](/usage-examples) section.
