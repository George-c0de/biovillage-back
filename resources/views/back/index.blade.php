<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Администрирование | BioVillage</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="{{ boasset('css/app.css') }}" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="/back/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/back/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/back/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="/back/img/favicon/site.webmanifest">
    <link rel="mask-icon" href="/back/img/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/back/img/favicon/favicon.ico">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-config" content="/back/img/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    {{-- Глобальные переменные js --}}
    <script>
        window.settings = @json($settings ?? new stdClass);
    </script>

</head>
<body>
    <div id="app"></div>
    <script src="{{ boasset('js/app.js') }}"></script>
</body>
</html>
