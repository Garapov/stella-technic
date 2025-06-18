<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <x-seo::meta />
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
        <script src="{{ 'https://api-maps.yandex.ru/2.1/?apikey='. config('services.maps.key') . '&lang=ru_RU&suggest_apikey=' . config('services.maps.suggestion_key') }}"></script>
        <!-- Scripts -->
        @filamentStyles
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="h-screen relative flex flex-col">
            @livewire('general.header-small')
            <div class="bg-gray-100 dark:bg-gray-800 grow">
                {{ $slot }}
            </div>
        </div>
        @filamentScripts
    </body>
</html>
