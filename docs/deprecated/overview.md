# SDK structure overview

Despite the small size of the library, it has all the necessary tools and methods to be fully functional and allows us to work with Apirone invoices with ease.

Let's break down the main classes and look at them in more detail. All classes inherited from AbstractModel can be converted to JSON (stdClass) and restored back. They can also be easily converted to text.

## Invoice

The main class of the library. Used to set static class properties such as database and logging handlers, Settings and Render objects.

Using special static methods, you can create instances of the class, set predefined parameters to create an invoice, update its status, and save additional parameters and their values if you need them. 

You can link an Invoice to a payment or any other entity in your system for further processing.

## Settings

It is used to create an Apron account, work with an existing account, set up transfer addresses, tariff policies, get available cryptocurrencies and synchronize parameters in API.

It is also used to configure and store invoice display settings. In addition, the class has built-in methods to save settings to a file and back to the class instance.

In addition to predefined values, it is possible to store additional parameters that you may need during the integration process.

## Currency

Apirone cryptocurrency class.

It contains all currency properties, allows you to set the transfer address and commission policy in the API. And also has a lot of additional useful methods for working with properties.

## InvoiceDetails

A class responsible for storing all invoice data. Loads the API response into the class instance, initializes additional nested classes such as UserData, etc., and provides property values by name. Also used for status updates.

## HistoryItem

Provides access to the properties of the history array elements.

## UserData

Used to configure some additional information about the invoice (includes following fields: title, merchant,url, price, sub-price, items, extras, etc.)

### OrderItem

Consists of objects with predefined fields: name, cost, qty (quantity), total

### ExtraItem

Additional elements on an invoice e.g fees, taxes or shipping price
