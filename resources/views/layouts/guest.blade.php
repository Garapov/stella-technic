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
        <!-- Alfa-Track Tag Manager Container (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5NB54DPN" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Alfa-Track Tag Manager Container (noscript) -->
        @livewire('general.header')
        <div class="bg-white dark:bg-gray-800">
            {{ $slot }}
        </div>

        @livewire('general.footer')
        @livewire('general.forms.callback')
        @livewire('general.forms.buyoneclick')
        @livewireScripts
    </body>
</html>
