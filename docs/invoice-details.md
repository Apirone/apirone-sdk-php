# InvoiceDetails class

After the invoice is created, the service returns a JSON object with information about it.
For easier handling of the data, the `InvoiceDetails` class loads the data and provides easy access to the properties.

## Class properties

|Property|Description|
|---|---|
|`$account`|Account identifier|
|`$invoice`|Invoice identifier|
|`$created`|Invoice creation date|
|`$currency`|Currency abbreviation|
|`$address`|The generated cryptocurrency address to receive a payment from a customer|
|`$expire`|Invoice expiration time in ISO-8601 format, for example, 2022-02-22T09:00:30|
|`$currency`|Amount in the selected currency|
|`$userData`|[UserData](user-data) class|
|`$status`|Invoice current status|
|`$history`|Invoice status change history|
|`$linkback`|The customer will be redirected to this URL after the payment is completed|
|`$callbackUrl`|Callback URL to receive data about the payment|
|`$invoiceUrl`|Link to the Apirone invoice web view - apirone.com/invoice|

All properties also available via `getPropertyName()` functions. Deprecated.

## Additional methods

|Method|Description|
|---|---|
|`update()`|Returns updated invoice data from the API|
|`info()`|Returns public or private invoice info|
|`isExpired()`|Checks if the invoice has not expired|
|`timeToExpire()`|Returns the number of seconds until an invoice expires. If the invoice has status `paid`, `overpaid`, `completed` or `expired` returns `-1`|
|`showLinkback()`*|Shows linkback if set and invoice status is paid or overpaid|
|`statusNum()`*|Returns count of history items. In case when the invoice completed or expired return zero value|

`*` - Used to render the invoice data.
