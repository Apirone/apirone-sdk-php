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
                <pre><code class="language-php"><?php echo load_file_content('db_config_example.php'); ?></code></pre>
            </div>
            <div x-data="table" x-init="load" id="step_2">
                <h2>Invoice data table</h2>
                <pre><code class="language-php"><?php echo load_file_content('db_table_setup.php'); ?></code></pre>
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
                <pre><code class="language-php"><?php echo load_file_content('settings_create.php'); ?></code></pre>

                <div x-data="settings" x-init="load" class="mt-20">
                    <p>Settings config example</p>
                    <div class="relative">
                        <button x-show="file" class="absolute top-4 right-10 text-gray-200" @click="toggle" x-text="expand ? 'Collapse' : 'Expand'"></button>
                        <pre class="_relative"><code class="language-json" :class="{'' : expand, 'max-h-96' : !expand}" x-text="content"></code></pre>
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
                <pre><code class="language-php"><?php echo load_file_content('./invoice_callback.php'); ?></code></pre>

            </div>
            <div>
                <h2>Create an invoice</h2>
                <pre><code class="language-php"><?php echo load_file_content('./invoice_create.php'); ?></code></pre>
            </div>
            <div>
                <h2>Show invoice</h2>
                <pre><code class="language-php"><?php echo load_file_content('./invoice_render.php'); ?></code></pre>
                <pre><code class="language-php"><?php echo load_file_content('./invoice_render_json.php'); ?></code></pre>
            </div>
            <div id="step_5">
                <h2>Playground</h2>
                <div x-show="!$store.table || !$store.settings">
                    Before creating an invoice you need make prev steps!
                </div>
                <div x-show="$store.table && $store.settings">
                        <div class="mt-8 max-w-md">
                            <div class="grid grid-cols-1 gap-6">
                                <label class="block">
                                    <span class="text-gray-700">Full name</span>
                                    <input type="text" class="mt-1 block w-full" placeholder="">
                                </label>
                                <label class="block">
                                    <span class="text-gray-700">When is your event?</span>
                                    <input type="date" class="mt-1 block w-full">
                                </label>
                                <label class="block">
                                    <span class="text-gray-700">What type of event is it?</span>
                                    <select class="block w-full mt-1">
                                        <option>Corporate event</option>
                                        <option>Wedding</option>
                                        <option>Birthday</option>
                                        <option>Other</option>
                                    </select>
                                </label>
                                <div class="block">
                                    <div class="mt-2">
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" checked="">
                                                <span class="ml-2">Email me news and special offers</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>            </div>
        </div>
    </body>
</html>