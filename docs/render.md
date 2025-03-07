# Render class

The Render class is designed to set parameters for generating and outputting the html layout of an invoice.
You can also create your own custom template and localization file.

The class is designed with static methods and properties, but it is possible
to create an instance of the class for easier parameter customization.

## Class properies

|Property|Type|Description|
|---|---|---|
|`$idParam`|string|Invoice id param name. The default is `invoice`|
|`$dataUrl`|string|URL for ajax request to update invoice data.|
|`$backlink`|string|URL backlink to store.|
|`$timeZone`|string|Client time zone to generate the correct time. The default is UTC.|
|`$qrOnly`|bool|If set, the `qr-only.php` template is used.|
|`$logo`|bool|If set, the apirone logo will be shown.|
|`$template`|string|Absolute path to the template file. If the file does not exist, the library templates will be used as a fallback.|
|`$locales`|string|Absolute path to the locales file.|

All properties are available as `Render::$propetyName`.
For convenience, you can create an instance of the class and customize it with arrow functions
using the name of the porarameter as the function name:

```php
// Create an instance and configure
$render = Render::init()
    ->idParam('myParam')
    ->qrOnly(true)
    ->dataUrl('https://mysite.com/render-data-url');

// Call the method without parameter to set the default value
$render->dataUrl();

// To get a property value, use the obeject syntax
$dataUrl = $render->dataUrl;

```

## Class methods

|Method|Description|
|---|---|
|`init()`|Returns class instance|
|`fromJson()`|Returns an instance of the class with customized parameters from json.|
|`fromFile()`|Reads json from a file and returns a configured instance of the class.|
|`toJson()`|Returns a json object with class parameters.|
|`toJsonString()`|Returns a json object with class parameters as string.|
|`toFile()`|Saves class parameters to a file.|
|`timeZoneByOffset()`|Setting the $timeZone by local time zone with UTC offset.|
|`show()`|Render invoice html.|
|`isAjaxRequest()`|Checks the request headers and determines if it is an ajax request.|
|`getLocales()`|Returns an array of locales.|

## Custom locales & template

## Required assets
