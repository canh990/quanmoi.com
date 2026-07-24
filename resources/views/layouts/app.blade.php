<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'QuÃ¡n Má»›i - KhÃ¡m phÃ¡ áº©m thá»±c')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    @stack('styles')
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col relative antialiased selection:bg-primary-fixed selection:text-on-primary-fixed">

    <!-- Global Header -->
    @hasSection('header')
        @yield('header')
    @else
        @include('layouts.navigation')
    @endif

    <!-- Main Dynamic Content -->
   <!-- CÄƒn chá»‰nh láº¡i khoáº£ng cÃ¡ch trÃªn Ä‘á»ƒ ná»™i dung chÃ­nh khá»›p sÃ¡t mÃ©p thanh Ä‘iá»u hÆ°á»›ng má»›i -->

    @yield('content')


    <!-- Global Footer -->
    @hasSection('footer')
        @yield('footer')
    @else
        @include('layouts.footer')
    @endif

    <!-- Location Search Modal Widget -->
    @include('layouts.location-modal')

    @stack('scripts')
</body>
</html>
