<?php

require_once('helpers/common.php');

?>
<html class="dark">
    <head>
        <title>Apirone SDK PHP examples</title>
        <script src="//unpkg.com/alpinejs" defer></script>
        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
        <link rel="stylesheet" href="https://unpkg.com/@tailwindcss/typography@0.4.1/dist/typography.min.css">
        <link rel="stylesheet" href="https://unpkg.com/@highlightjs/cdn-assets@11.7.0/styles/github-dark.min.css">
        <script src="//unpkg.com/@highlightjs/cdn-assets@11.7.0/highlight.min.js"></script>
        <script>hljs.highlightAll();</script>
        <link rel="icon" href="/helpers/favicon.ico?v=0.0.1">
        <script src="/helpers/script.js"></script>
        <style>
            pre strong.filename {display: block; background-color: #5d8ab9; margin: -12px -15px; padding: 8px 25px;}
            p i {font-family: monospace; font-style: normal; font-weight: bold;}
        </style>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body class="flex px-4" x-data>
        <div class="container mx-auto max-w-5xl prose prose-base">
            <h1 class="md:pt-16 pt-8 text-center md:text-left">Apirone SDK PHP examples</h1>
            <div>
                <h2>Install SDK</h2>
                <p>
                    The easiest way to install the library is via Composer.
                    Otherwise, you'll need to install the <a href="https://github.com/Apirone/apirone-api-php" target="_blank">apirone-api-php</a> library.
                </p>
                <pre><code class="language-bash">composer require apirone-sdk-php</code></pre>
            </div>
            <div>
                <h2>Database and Logging</h2>
                <p>
                    To interact with the database and logs, the library uses callback functions
                    that wrap calls to your methods for working with the database and logs.
                    For more information, see the documentation.
                </p>
                <h3>Database</h3>
                <p>Simple example with <i>SQLite</i> usage.</p>
                <?php echo load_file_content('db_sqlite.php'); ?>
                <h3>Logging</h3>
                <p>
                    Simple write log to file example, used as callback function.
                    Also you can use <code>PSR/LOG</code> or own implementation of <code>Psr\Log\LoggerInterface</code>.</p>
                <?php echo load_file_content('log.php'); ?>
            </div>
            <div x-data="table" x-init="load" id="step_2">
                <h2>Invoice data table</h2>
                <p>
                    To store invoice information, you need to create a table in your database.
                    The Db class contains special methods for this purpose.
                </p>
                <?php echo load_file_content('table.php'); ?>
                <button class="text-white rounded-md w-48 bg-[#5d8ab9] hover:opacity-80 disabled:opacity-80 p-2" @click="doAction" x-text="label" :disabled="table"></button>
            </div>
            <div id="step_3">
                <h2>Settings</h2>
                <p>
                    This is a special class that allows you to create a new account or use an existing one,
                    configure transfer addresses, or set up a pricing plan.
                    The class also allows you to store all the settings you need in one place.
                </p>
                <p>
                    You can store the settings in a file or get them as a JSON-object and save them in any way you like.
                    In this example, we will save the settings to a file.<br />
                </p>
                <?php echo load_file_content('settings.php'); ?>

                <div x-data="settings" x-init="load; $watch('file', value => hljs.highlightElement($refs.settingsJson))" class="mt-20">
                    <p>Settings config example</p>
                    <div class="relative">
                        <strong x-show="file" class="absolute top-[12px] right-10 !text-white cursor-pointer font-mono text-sm" @click="toggle" x-text="expand ? 'Collapse' : 'Expand'"></strong>
                        <pre><strong class="!text-white filename">settings.json</strong><code class="language-json mt-5" :class="{'' : expand, 'max-h-96' : !expand}" x-ref="settingsJson" x-text="content"></code></pre>
                    </div>
                    <button class="text-white rounded-md w-48 bg-[#5d8ab9] hover:opacity-80 disabled:opacity-80 p-2" @click="doAction" x-text="label" :disabled="file"></button>
                </div>

            </div>
            <div>
                <h2>Apirone API callbacks handler</h2>
                <p>
                    Receiving API callbacks is important for the correct processing of invoices.
                    To do this, you need to create a URL that will respond to requests from the apirone service.
                    There is a special static method for this. You only need to register its call.
                </p>
                <p>
                    The method supports two parameters for additional data processing in your system.
                    Both parameters must be callback functions. The first parameter, <code>$paymentProcessing</code>,
                    is used to process the payment in your system. The second parameter, <code>$callbackChecker</code>,
                    is used for preliminary validation/processing of input data.
                </p>
                <?php echo load_file_content('./callback.php'); ?>

            </div>
            <div>
                <h2>Create an invoice</h2>
                <?php echo load_file_content('./create-invoice.php'); ?>
            </div>
            <div>
                <h2>Show invoice</h2>

                <div class="border border-gray-200 bg-gray-200 rounded-md px-8 py-5">
                    <h3 class="!mt-2">Important!</h3>
                    <p>
                    Starting with <strong>SDK 2.0</strong>, server-side rendering is no longer supported. Instead, we now use
                    <a href="https://github.com/Apirone/invoice-app" target="_blank">Apirone Invoice App</a>.
                    This app, written in Vue, combines the ease of integration with the usability of modern front-end apps.
                    </p>
                </div>
                <h3>Invoice APP</h3>
                <p>
                    To display the invoice, you need to add two files: <code>script.min.js</code> and <code>style.min.css</code> from the <code>src/assets</code> directory.
                    All available config options see on <a href="https://github.com/Apirone/invoice-app" target="_blank">github</a>.
                </p>
                <?php echo load_file_content('./invoice.php'); ?>

                <h3>Local API for Invoice App</h3>
                <p>
                    By default, Invoice-App uses the <a href="https://apirone.com/docs" target="_blank">Apirone API</a> to retrieve data.
                    To fully implement <a href="https://ru.wikipedia.org/wiki/White_Label" target="_blank">White Label</a> on your server, you need to implement your own API with two endpoints: <code>invoices</code> and <code>wallets</code>.
                    You can see an example implementation in the <code>api.php</code> and <code>.htaccess</code> files.
                </p>
                <?php echo load_file_content('./api.php'); ?>
                <?php echo load_file_content('.htaccess'); ?>
            </div>
            <div id="step_5">
                <h2>Playground</h2>
                <div x-show="!$store.table || !$store.settings" class="pb-10">
                    <div class="border border-gray-200 bg-gray-200 rounded-md px-8 py-5">
                        <h3 class="!mt-2">Important!</h3>
                        <p>
                            Before creating an invoice you need create <a href="#step_2">data table</a> and <a href="#step_3">settings</a>!
                        </p>
                    </div>
                </div>
                <div x-show="$store.table && $store.settings" class="pb-10">
                    <div class="my-8">
                        <form x-data="playground" @submit.prevent="create" x-init="$watch('invoice', value => hljs.highlightElement($refs.invoiceJson))">
                            <div class="grid md:grid-cols-2 gap-6 grid-cols-1">
                                <label class="block">
                                    <span class="text-gray-700">Currency <span class="text-red-500">*</span></span>
                                    <select x-model="data.currency" class="block w-full mt-1" x-init="$watch('$store.settings', value => currencies())">
                                        <option value="" class="text-gray-400">Select currency</option>
                                        <template x-if="$store.settings">
                                            <template x-for="currency in $store.currencies">
                                                <template x-if="currency.address">
                                                    <option x-text="currency.alias" x-bind:value="currency.abbr"></option>
                                                </template>
                                            </template>
                                        </template>
                                        <template x-if="$store.settings">
                                            <template x-for="currency in $store.currencies">
                                                <template x-if="!currency.address">
                                                    <option x-text="currency.alias" x-bind:value="currency.abbr" disabled></option>
                                                </template>
                                            </template>
                                        </template>
                                    </select>
                                    <span class="inline-block mt-2 text-gray-400 text-sm">
                                        Currency type - any cryptocurrency supported by service.
                                        If no destination is set, currency is disabled. See settings.json above. Required
                                    </span>
                                </label>
                                <label class="block">
                                    <span class="text-gray-700">Amount</span>
                                    <input type="number" x-model="data.amount" class="mt-1 block w-full placeholder-gray-400" placeholder="Enter amount value in minor units">
                                    <span class="inline-block mt-2 text-gray-400 text-sm">
                                        Amount for the checkout in the selected currency of the invoice object. Also you may create invoices without fixed amount. The amount is indicated in minor units
                                    </span>
                                </label>
                                <label class="block">
                                    <span class="text-gray-700">Lifetime</span>
                                    <input x-model="data.lifetime" type="number" class="mt-1 block w-full">
                                    <span class="inline-block mt-2 text-gray-400 text-sm">
                                        Duration of invoice validity (indicated in seconds)
                                    </span>
                                </label>
                                <label class="block">
                                    <span class="text-gray-700">Expire</span>
                                    <input x-model="data.expire" type="text" class="mt-1 block w-full">
                                    <span class="inline-block mt-2 text-gray-400 text-sm no-prose">
                                        Invoice expiration time in <a href="https://www.iso.org/iso-8601-date-and-time-format.html" target="_blank" class="not-prose text-gray-400 hover:text-gray-500">ISO-8601</a> format, for example,
                                        <a href="#" @click.prevent="data.expire = $el.innerHTML" class="not-prose text-gray-400 hover:text-gray-500"><?php echo date('Y-m-d\TH:i:s', strtotime('1 day')); ?></a>.
                                        If both parameters are specified: lifetime and expire, then the parameter expire will take precedence
                                    </span>
                                </label>
                                <label class="block">
                                    <span class="text-gray-700">Callback URL</span>
                                    <input x-model="data.callbackUrl" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="https://yourhost.com/callback.php">
                                    <span class="inline-block mt-2 text-gray-400 text-sm">
                                        Enter the protocol and domain name or IP address of your host and add /callback.php for the example to work correctly.
                                        Your host must be accessible from the internet.
                                    </span>
                                </label>
                                <label class="block">
                                    <span class="text-gray-700">Linkback</span>
                                    <input x-model="data.linkback" type="text" class="mt-1 block w-full placeholder-gray-400" placeholder="https://linkback.com">
                                    <span class="inline-block mt-2 text-gray-400 text-sm">
                                        The customer will be redirected to this URL after the payment is completed
                                    </span>
                                </label>
                            </div>
                            <div>
                                <label _x-show="invoice">Add User Data<input class="mx-2 my-4 text-[#5d8ab9] ring-offset-[#5d8ab9]" type="checkbox" x-model="showUserData" /></label>
                                <div x-show="showUserData" x-transition class="" >
                                    <h3>Main section</h3>
                                    <div class="grid md:grid-cols-2 grid-cols-1 gap-6 mb-6">
                                        <label class="block">
                                            <span class="text-gray-700">Invoice title</span>
                                            <input x-model="userData.title" type="text" class="mt-1 block w-full">
                                            <span class="inline-block mt-2 text-gray-400 text-sm">Custom title for invoice</span>
                                        </label>
                                        <label class="block">
                                            <span class="text-gray-700">Merchant name</span>
                                            <input x-model="userData.merchant" type="text" class="mt-1 block w-full">
                                            <span class="inline-block mt-2 text-gray-400 text-sm">For example, the name of your store</span>
                                        </label>
                                        <label class="block">
                                            <span class="text-gray-700">Merchant url</span>
                                            <input x-model="userData.url" type="text" class="mt-1 block w-full">
                                            <span class="inline-block mt-2 text-gray-400 text-sm">URL to merchant's site</span>
                                        </label>
                                        <label class="block">
                                            <span class="text-gray-700">Price (total)</span>
                                            <input x-model="userData.price" type="text" class="mt-1 block w-full">
                                            <span class="inline-block mt-2 text-gray-400 text-sm">Displays the total price in fiat</span>
                                        </label>
                                        <label class="block">
                                            <span class="text-gray-700">Sub-price (sub total)</span>
                                            <input x-model="userData['sub-price']" type="text" class="mt-1 block w-full">
                                            <span class="inline-block mt-2 text-gray-400 text-sm">Displays amount in fiat before adding discount, tax or shipping charges</span>
                                        </label>
                                    </div>
                                    <h3>Items section</h3>
                                    <template x-for="(item, index) in userData.items">
                                        <div class="grid md:grid-cols-4 grid-cols-2 gap-6 mb-6">
                                            <label class="block">
                                                <span class="text-gray-700">Name</span>
                                                <input x-model="item.name" type="text" class="mt-1 block w-full">
                                                <span class="inline-block mt-2 text-gray-400 text-sm">Item name</span>
                                            </label>
                                            <label class="block">
                                                <span class="text-gray-700">Cost</span>
                                                <input x-model="item.cost" type="text" class="mt-1 block w-full">
                                                <span class="inline-block mt-2 text-gray-400 text-sm">Item cost</span>
                                            </label>
                                            <label class="block">
                                                <span class="text-gray-700">Qty</span>
                                                <input x-model="item.qty" type="text" class="mt-1 block w-full">
                                                <span class="inline-block mt-2 text-gray-400 text-sm">Items quantity</span>
                                            </label>
                                            <label class="block">
                                                <span class="text-gray-700">Total</span>
                                                <input x-model="item.total" type="text" class="mt-1 block w-full">
                                                <span class="inline-block mt-2 text-gray-400 text-sm">The total fiat price</span>
                                            </label>
                                        </div>
                                    </template>
                                    <button type="button" class="text-white rounded-md w-20 bg-[#5d8ab9] hover:opacity-80 disabled:opacity-80 p-2 mr-2 my-2" @click="addItem">Add</button>
                                    <button x-show="userData.items.length > 0" type="button" class="text-white rounded-md min-w-20 bg-[#5d8ab9] hover:opacity-80 disabled:opacity-80 p-2 mr-2 my-2" @click="userData.items=[]">Delete All</button>
                                    <h3>Extras section</h3>
                                    <template x-for="(item, index) in userData.extras">
                                        <div class="grid md:grid-cols-2 grid-cols-1 gap-6 mb-6">
                                            <label class="block">
                                                <span class="text-gray-700">Name</span>
                                                <input x-model="item.name" type="text" class="mt-1 block w-full">
                                                <span class="inline-block mt-2 text-gray-400 text-sm">Item name</span>
                                            </label>
                                            <label class="block">
                                                <span class="text-gray-700">Price</span>
                                                <input x-model="item.price" type="text" class="mt-1 block w-full">
                                                <span class="inline-block mt-2 text-gray-400 text-sm">Item price</span>
                                            </label>
                                        </div>
                                    </template>
                                    <button type="button" class="text-white rounded-md w-20  bg-[#5d8ab9] hover:opacity-80 disabled:opacity-80 p-2 mr-2 my-2" @click="addExtra">Add</button>
                                    <button x-show="userData.extras.length > 0" type="button" class="text-white rounded-md min-w-20  bg-[#5d8ab9] hover:opacity-80 disabled:opacity-80 p-2 mr-2 my-2" @click="userData.extras=[]">Delete All</button>
                                </div>
                            </div>
                            <div class="border-t-2 mt-6 pt-4">
                                <span class="block mb-8"><span class="text-red-500">*</span> - Required fields</span>
                                <button type="submit" :disabled="!data.currency" class="text-white rounded-md md:w-48 w-full bg-[#5d8ab9] hover:opacity-80 disabled:opacity-80 p-4 mr-2 my-2" @click="$event.prevent; create" x-text="label"></button>
                                <button type="button" x-show="invoice && !invoice.message" class="text-white rounded-md md:w-48 w-full bg-[#5d8ab9] hover:opacity-80 disabled:opacity-80 p-4 my-2" @click="front">Show invoice</button>
                                <label x-show="invoice && !invoice.message">QR Only<input class="mx-2" type="checkbox" x-model="qrOnly" /></label>
                            </div>

                            <h3>Invoice details</h3>
                            <div class="relative">
                                <strong x-show="invoice" class="absolute top-[12px] right-10 !text-white cursor-pointer font-mono text-sm" @click.prevent="toggle" x-text="expand ? 'Collapse' : 'Expand'"></strong>
                                <pre><strong x-show="invoice" class="!text-white filename">invoice.json</strong><code class="language-json mt-5" :class="{'' : expand, 'max-h-96' : !expand}" x-ref="invoiceJson" x-text="content"></code></pre>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
