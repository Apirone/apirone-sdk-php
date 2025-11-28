# Local API

Provides methods to easily create the required endpoints.
Simply wrap the corresponding method calls based on your application's architecture.

## Properties

|Property|Type|Description|
|:---:|:---:|---|
|`$checkInterval`|int|Minimum interval for checking invoice status.|


## Methods

|Property|Return|Description|
|:---:|:---:|---|
|`checkInterval()`|void|Sets the `$checkInterval` value. Without parameters, sets the default value.|
|`invoices()`|never|Local API invoices entry point handler.|
|`wallets()`|never|Local API wallets entry point handler.|

- The `invoices()` method takes an invoice_id as its first parameter. The second, optional parameter
  can be the `$paymentProcessing` callback function from the `Invoice::callbackHandler()` [method](invoice#callback-handler).

- The `wallets()` method has no parameters.

## Implementation

Let's our `service_url` is `https://my_domain.net/api`.
To handle requests coming to the API `/api/invoices` and `/api/wallets`), we'll redirect them
to the `api_handler.php` file using `.htaccess`.


```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteRule . - [E=REWRITEBASE:/]
  RewriteRule ^api(?:/(.*))?$ %{ENV:REWRITEBASE}api_handler.php?url=$1 [QSA,L]
</IfModule>
```

```php
<?php
use Apirone\SDK\Service\Api;

// ...
$endpoint = $_REQUEST['url'];
switch ($endpoint) {
    // https://my_domain.net/api/invoices/{INVOICE_ID}
    case str_contains($endpoint, 'invoices'):
        $urlParts = explode('/', $endpoint);
        $invoice_id = end($urlParts);
        Api::invoices($invoice_id);
    // https://my_domain.net/api/wallets
    case 'wallets':
        Api::wallets();
}
// ...
```
