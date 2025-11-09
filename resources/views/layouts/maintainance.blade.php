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
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&family=Roboto&display=swap" rel="stylesheet">
        <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
        <script src="{{ 'https://api-maps.yandex.ru/2.1/?apikey='. config('services.maps.key') . '&lang=ru_RU&suggest_apikey=' . config('services.maps.suggestion_key') }}"></script>

        <!-- Alfa-Track Tag Manager Container -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-5NB54DPN');</script>
        <!-- End Alfa-Track Tag Manager Container -->
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="fixed inset-0 bg-gray-300 flex flex-col items-center justify-center gap-10">
            <svg class="w-40" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 48 48" enable-background="new 0 0 48 48" xml:space="preserve">
                <path fill="#607D8B" d="M12.3,40.8c0.1-0.1,1.3-1.4,3.2-3.4c2.8-2.9,15-15.9,15.2-16.1c0.9-0.9,5.2,2.4,8.8-1.2  c2.3-2.3,3.1-5.2,2.1-8.7L37,16c0,0-1.7,0-3.4-1.6C32,12.7,32,11,32,11l4.6-4.5c-3.3-1-6.5-0.2-8.8,2.1c-3.5,3.5-0.4,7.9-1.3,8.8  C26.3,17.5,9.2,33.8,9,34l0,0c-1.7,1.6-1.8,1.7-1.9,1.7C5.7,37.2,5.6,39.6,7,41C8.4,42.4,10.8,42.3,12.3,40.8z M10,36.5  c0.8,0,1.5,0.7,1.5,1.5s-0.7,1.5-1.5,1.5c-0.8,0-1.5-0.7-1.5-1.5S9.1,36.5,10,36.5z"/>
                <path fill="#B0BEC5" d="M40,37L14.5,11.7L12,8L7.4,6L6,7.4L8,12l3.7,2.5l25.5,25.3L40,37z"/>
                <path fill="#FFC107" d="M26.5,20.8c-2.1-5-3.3-2.3-5.7,0l0,0c-2.3,2.3-5,3.5,0,5.7c4.9,2.1,10.3,11.7,12.4,13.9  c2.1,2.1,4.7,2.3,7.1,0l0,0c2.3-2.3,2.1-4.9,0-7.1C38.2,31.2,28.6,25.8,26.5,20.8z"/>
                <path fill="#FF9800" d="M20.8,20.8c2.3-2.3,3.5-5,5.7,0l-5.7,5.7C15.9,24.4,18.5,23.2,20.8,20.8z"/>
            </svg>
            <div class="text-lg font-medium">Этот раздел находится в разработке. Мы стараемся запустить его как можно скорее.</div>
            
            <a href="{{ route('client.index') }}" class="text-blue-500" wire:navigate>На главную</a>
        </div>
        @livewireScripts
    </body>
</html>
