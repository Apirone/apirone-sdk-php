<!doctype html>
<html lang="en">
  <head>
      <title>Invoice</title>
      <link rel="icon" href="/assets/favicon.ico">
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
      <!-- Customize Invoice App congig if need: -->
      <script>
          window.apirone_config = {
            service_url: 'http://localhost/invoice-app-api/',
            images_relative_path: '/assets/img',
            invoice_id_key: 'id',
          };
      </script>
      <!-- Add Invoice App script & styles -->
      <script type="module" crossorigin src="/assets/script.min.js"></script>
      <link rel="stylesheet" crossorigin href="/assets/style.min.css">
  </head>
  <body>
    <!-- Add Invoce App container -->
    <div id="app"></div>
  </body>
</html>
