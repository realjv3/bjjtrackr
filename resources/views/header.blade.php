<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="overflow-y: auto">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>FlowRolled</title>
        <link rel="icon" href="https://flowrolled.nyc3.digitaloceanspaces.com/public/Icon%20Only.png">

        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">

        <script type="application/javascript"> const CSRFToken = '{{ csrf_token() }}'; </script>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-X21YE1QLR1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-X21YE1QLR1');
        </script>
    </head>

    <body style="background-color: #121212">
        <div id="body"></div>

        @yield('content')
    </body>

</html>
