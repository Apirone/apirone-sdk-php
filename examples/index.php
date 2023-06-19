<?php

require_once('helpers/common.php');

?>
<html class="dark">
    <head>
        <title>Invoice Apirone library examples</title>
        <script src="//unpkg.com/alpinejs" defer></script>
        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
        <link rel="stylesheet" href="https://unpkg.com/@tailwindcss/typography@0.4.1/dist/typography.min.css">
        <link rel="stylesheet" href="https://unpkg.com/@highlightjs/cdn-assets@11.7.0/styles/github-dark.min.css">
        <script src="//unpkg.com/@highlightjs/cdn-assets@11.7.0/highlight.min.js"></script>
        <script>hljs.highlightAll();</script>
        <link rel="icon" href="/helpers/favicon.ico?v=0.0.1">
        <script src="/helpers/script.js"></script>
    </head>
    <body class="flex" x-data>
        <div class="container mx-auto max-w-5xl prose prose-base">
            <h1 class="pt-16">Apirone Invoice library examples</h1>
            <div>
                <h2>Install via composer</h2>
                <pre><code class="language-bash">composer require apirone-invoice-php</code></pre>
            </div>
            <div>
                <h2>Create database callback function</h2>
                <?php echo load_file_content('db.php'); ?>
            </div>
            <div x-data="table" x-init="load" id="step_2">
                <h2>Invoice data table</h2>
                <?php echo load_file_content('table.php'); ?>
                <button class="text-white font-simibold rounded-md w-48 bg-sky-500 hover:bg-sky-600 disabled:bg-gray-300 p-2" @click="doAction" x-text="label" :disabled="table"></button>
            </div>
            <div id="step_3">
                <h2>Settings</h2>
                <p>
                    When creating a settings object, you need to create an account, or you can use an existing one.
                    Also, all accessible cryptos of the service are automatically added to the settings object.
                    For invoice you can set only one destination address for each currency. You can set the destination address for each currency right away or do it later. 
                </p>
                <p>
                    You can store the settings in a file or get them as a JSON-object and save them in any way you like.
                    In this example, we will save the settings to a file.<br />
                </p>
                <?php echo load_file_content('settings.php'); ?>

                <div x-data="settings" x-init="load" class="mt-20">
                    <p>Settings config example</p>
                    <div class="relative">
                        <button x-show="file" class="absolute top-4 right-10 text-gray-200" @click="toggle" x-text="expand ? 'Collapse' : 'Expand'"></button>
                        <pre><code class="language-json" :class="{'' : expand, 'max-h-96' : !expand}" x-text="content"></code></pre>
                    </div>
                    <button class="text-white font-simibold rounded-md w-48 bg-sky-500 hover:bg-sky-700 p-2 disabled:bg-gray-300" @click="doAction" x-text="label" :disabled="file"></button>
                </div>

            </div>
            <div>
                <h2>Apirone callback handler</h2>
                <p>
                    Response processing is important for correct operation of the library.
                    To do this, you need to create a URL that will respond to requests from the apirone service. 
                    There is a special static method for this. You only need to register its call. 
                </p>
                <p>
                    If you want to process invoice statuses for your system, 
                    you need to create a callback function that will handle the changed invoice status.
                </p>
                <?php echo load_file_content('./callback.php'); ?>

            </div>
            <div>
                <h2>Create an invoice</h2>
                <?php echo load_file_content('./invoice.php'); ?>
            </div>
            <div>
                <h2>Show invoice</h2>
                <?php echo load_file_content('./render.php'); ?>
            </div>
            <div id="step_5">
                <h2>Playground</h2>
                <div x-show="!$store.table || !$store.settings">
                    Before creating an invoice you need make prev steps!
                </div>
                <div x-show="$store.table && $store.settings" class="pb-10">
                    <div class="my-8">
                        <form x-data="playground" @submit.prevent="create">
                            <div class="grid grid-cols-2 gap-6">
                                <label class="block">
                                    <span class="text-gray-700">Currency</span>
                                    <select x-model="data.currency" class="block w-full mt-1">
                                        <option value="">Select currency</option>
                                        <template x-if="$store.settings">
                                            <template x-for="currency in $store.settings.currencies">
                                                <option x-text="currency.abbr" :disabled="currency.address === null"></option>
                                            </template>
                                        </template>
                                    </select>
                                    <span class="inline-block mt-2 text-gray-300 text-sm">
                                        Currency type (any cryptocurrency supported by service). Required	
                                    </span>
                                </label>
                                <label class="block">
                                    <span class="text-gray-700">Amount</span>
                                    <input type="number" x-model="data.amount" min="0" class="mt-1 block w-full" placeholder="Enter amount value in minor units">
                                    <span class="inline-block mt-2 text-gray-300 text-sm">
                                        Amount for the checkout in the selected currency of the invoice object. Also you may create invoices without fixed amount. The amount is indicated in minor units	
                                    </span>
                                </label>
                                <label class="block">
                                    <span class="text-gray-700">Lifetime</span>
                                    <input x-model="data.lifetime" type="number" class="mt-1 block w-full" min="0" value="300">
                                    <span class="inline-block mt-2 text-gray-300 text-sm">
                                        Duration of invoice validity (indicated in seconds)	
                                    </span>
                                </label>
                                <label class="block">
                                    <span class="text-gray-700">Callback URL</span>
                                    <input x-model="data.callbackUrl" type="text" class="mt-1 block w-full" placeholder="https://yourhost.com/callback.php">
                                    <span class="inline-block mt-2 text-gray-300 text-sm">
                                        Enter the protocol and domain name or IP address of your host and add /callback.php for the example to work correctly.
                                        Your host must be accessible from the internet.
                                    </span>
                                </label>
                                <div>
                                    <button type="submit" :disabled="!data.currency" class="text-white font-simibold rounded-md w-48 bg-sky-500 hover:bg-sky-600 disabled:bg-gray-300 p-2" @click="$event.prevent; create" x-text="label"></button>
                                    <button type="button" x-show="invoice" class="text-white font-simibold rounded-md w-48 bg-sky-500 hover:bg-sky-600 disabled:bg-gray-300 p-2" @click="render">Show invoice</button>
                                </div>
                            </div>
                            <h3>Invoice details</h3>
                            <div class="relative">
                                <button x-show="invoice" class="absolute top-4 right-10 text-gray-200" @click.prevent="toggle" x-text="expand ? 'Collapse' : 'Expand'"></button>
                                <pre><code class="language-json" :class="{'' : expand, 'max-h-96' : !expand}" x-text="content"></code></pre>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>