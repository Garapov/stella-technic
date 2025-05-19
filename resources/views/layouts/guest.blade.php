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
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&family=Roboto&display=swap" rel="stylesheet">
        <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
        <script src="{{ 'https://api-maps.yandex.ru/2.1/?apikey='. config('services.maps.key') . '&lang=ru_RU&suggest_apikey=' . config('services.maps.suggestion_key') }}"></script>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        
    </head>
    <body class="font-sans text-gray-900 antialiased">
        @livewire('general.header')
        <div class="bg-white dark:bg-gray-800">
            {{ $slot }}
        </div>

        @livewire('general.footer')
        @livewireScripts
    </body>
</html>
