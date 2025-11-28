# Database

A database is a wrapper class that uses a callback function to interact with your database or storage.
The callback function receives a prepared SQL query or data to execute and returns the result.

## Properties

|Property|Type|Description|
|:---:|:---:|---|
|`$handler`|`callable`|Callback handler for database execution. Required.|
|`$adapter`|`string`|[Adepter](database#adapter-class) name from the list `mysql`, `sqlite`, `postgres` or fully qualified class name. Default is `mysql`.|
|`$table`|`string`|Database table name. Default is `apirone_invoice`|
|`$prefix`|`string`|Database table prefix. Default is empty string.|

To set properties, you can use methods of the same name, using either static or non-static calls.
All methods return a `$this` object and support method chain calls.

```php
<?php
use Apirone\SDK\Service\Db;

$db_handler = static function ($query) {
  // Implements your logic
}

Db::handler($db_handler)
  ->adapter('sqlite')
  ->table('my_table_name')
  ->prefix('prefix_');

```

## Methods

|Method|Return|Description|
|:---:|:---:|---|
|`execute()`|`callable`|Passes a prepared query or data to the handler and returns the result.|
|`$install()`|`string`|Install invoice table into database|
|`$uninstall()`|`string`|Uninstall invoice table from database|
|`$saveInvoice()`|`string`|Saves created invoice or update existed.|
|`$getInvoice()`|`string`|Gets invoice from storage by invoice ID.|
|`$getOrderInvoices()`|`string`|Retrieves all invoices associated with this payment ID.|

For the library to function correctly, you need to create a table in the database or storage.
To do this, use the appropriate methods. Before this, the handler callback function must be set.

```php
// Call install method to create table
Db::install();
```
To delete table, use the `uninstall()` method. The remaining methods are not used directly.

```php
// Call uninstall method to drop table
// WARNING! All table data will be lost
Db::uninstall();
```

## Adapter class

The library supports __MySQL__, __SQLite__ and __PostgreSQL__ out of the box.
You can write your own adapter implementing the `AdapterInterface` methods
and use any storage you want, including API, microservices, or s3.

If the adapter uses additional properties, such as in `Mysql.php`, they can be set in the same way
as the main properties by calling a method with the name of the property being set and the value as a parameter.

For example, two additional properties are used in the MySQL adapter: `$charset` and `$collate`.
If the property does not exist in the adapter, PHP will return a notification.

```php
<?php
use Apirone\SDK\Service\Db;

Db::adapter('mysql');
Db::charset('utf8mb4')->collate('utf8mb4_0900_ai_ci');

```
```php
<?php
use Apirone\SDK\Service\Db;

Db::handler($handler)
  ->adapter('mysql');
  ->charset('utf8mb4') // Default is 'utf8'
  ->collate('utf8mb4_0900_ai_ci'); // Default is 'utf8_general_ci'

```
