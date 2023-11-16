<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'BioVillage | Доставка отборных продуктов')</title>
    <meta name="description" content="Biovillage — сервис доставки отборных продуктов в Москве. Бесплатная доставка от 2000 руб. Привозим свежие и полезные продукты! Помогаем освободить ваше время для семьи, увлечений и отдыха.">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" type="image/png" sizes="64x64" href="/img/favicon/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicon/apple-touch-icon-180x180.png">
    <meta name="theme-color" content="#FFB94B">
    <link rel="stylesheet" href="/css/main.min.css">
    @stack('head')
</head>
<body>
    @yield('content')
    @stack('end')
    <script async src="https://www.google.com/recaptcha/api.js?render=6LeOuBoaAAAAAF5c-wKstJqk3gKbXCI6wwmwOC4i"></script>
    <script src="/js/scripts.min.js"></script>
</body>
</html>
