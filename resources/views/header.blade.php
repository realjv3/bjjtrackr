<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>BjjTrackr</title>

        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">

        <script type="application/javascript"> const CSRFToken = '{{ csrf_token() }}'; </script>
    </head>

    <body>
        <div id="body" style="height: 100%; width: 100%; background-color: #121212"></div>

        @yield('content')
    </body>

</html>
