# Invoice class

The main class of the library is `Apirone\SDK\Invoice`. Almost all work with invoices is done through it.
It is also used to configure database handlers, logging and rendering parameters.

## Static class methods

Creating the logging, database and callbacks handlers was covered in [Five steps guide](/five-steps-guide), so we won't dwell on that here.

Use these methods for general class settings. Set these methods before working with invoices. Only the installation of the database handler is mandatory.

- `Invoice::logger($logger)` sets the [logging handler](/five-steps-guide#creating-a-log-handler).
- `Invoice::db($db_handler, $table_prefix)` sets the [database handler](/five-steps-guide#create-database-handler).
- `Invoice::callbackHandler($callbackHandler)` sets Apirone API [callbacks handler](/five-steps-guide#create-callbacks-handler).
- `Invoice::settings($settings)` sets the static property $settings, which contains an instance of the class [Settings](/settings).

Methods used to work with invoice output. Use on the page displaying the invoice.

- `Invoice::dataUrl($url)` sets the static property Render::$dataUrl to the [Render](/render) class.
  Added for convenience. Can be set directly to the Render class.
- `Invoice::renderLoader()` Outputs the invoice display loader.
- `Invoice::renderAjax()` Used to update invoice data. Must be called on the page specified in the `Invoice::dataUrl($dataUrl)` method.
  Returns html containing invoice markup or its numeric status.

Creating an invoice instance

- `Invoice::init()` returns a new invoice instance for further configuration and creation.
- `Invoice::fromFiatAmount()` returns a new invoice instance with an already calculated amount from fiat currency to the specified cryptocurrency

Get existed invoice

- 