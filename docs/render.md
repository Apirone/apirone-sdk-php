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
|`$template`|string|Absolute path to the template file.|
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

The methods for setting properties are not listed here.

## Custom locales & template

Starting with version 1.2, the library supports custom templates and localizations.\
To customize a template and/or locales, copy the files from `Apirone\SDK\Service\tpl`
directory to your project directory, make the necessary changes and set absolute paths
using `template()` methods for templates and `locales()` methods for locales.
If the template file does not exist, the library templates will be used as a fallback.

The second option for setting custom locales is to pass an array containing translations
to the `locales()` method. In case, if some value is missing,
the value from `en` locale will be used as a fallback value.

Officially, the library supports English `en`, Russian `ru`, Espanol `es`, French `fr`, German `de` and Turkish `tr`.
If you want to change eth from the list, create an array with locales, passing only the codes of the desired languages:

```php
$locales = [
    'en' => [],
    'ru' => [],
    'de' => [],
];
```

If you want to change only some keys of a language, add them to the required language array:

```php
$locales = [
    'en' => [
        'title' => 'Invoice EDITED',
        'from' => 'From EDITED',
        'remainsToPay' => 'Remains',
    ],
    'ru' => [],
    'de' => [],
];

```

To add a language that does not exist in the library, create an array with a two-letter language index,
put in all values from the array of the existing language and translate the values.
If a value is missing, it will be replaced by a value from the `en` locale.

## Required assets

As mentioned in [Step 4](/five-steps-guide#displaying-an-invoice), two files,
`/assets/css/styles.css` and `/assets/js/script.js` or their minimized versions,
need to be attached to display the invoice correctly.

Also in the `/assets/css/` folder you will find all the files you need to customize and modify the styles.
Use [Sass](https://sass-lang.com/) to create css styles and [Minify](https://www.npmjs.com/package/minify)
to create minimized versions.
