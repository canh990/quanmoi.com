<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quán Mới - Khám phá tinh hoa ẩm thực địa phương')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preload" href="{{ asset('fonts/material-symbols-outlined.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Raleway:wght@500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @stack('seo')
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col relative antialiased selection:bg-primary-fixed selection:text-on-primary-fixed pt-[72px]">
    <!-- Global Header -->
    @hasSection('header')
        @yield('header')
    @else
        @include('layouts.navigation')
    @endif

    <!-- Main Dynamic Content -->
   
    @yield('content')


    <!-- Global Footer -->
    @hasSection('footer')
        @yield('footer')
    @else
        @include('layouts.footer')
    @endif

    <!-- Location Search Modal Widget -->
    @include('layouts.location-modal')
    
    <!-- Logout Modal Widget -->
    @include('layouts.logout-modal')

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" 
        class="fixed bottom-6 right-6 z-50 p-3 bg-primary text-white rounded-full shadow-[0_4px_14px_rgba(160,65,0,0.25)] opacity-0 translate-y-10 pointer-events-none transition-all duration-300 hover:bg-primary/90 hover:-translate-y-1 hover:shadow-[0_8px_24px_rgba(160,65,0,0.35)] focus:outline-none flex items-center justify-center group"
        title="Lên đầu trang"
        aria-label="Lên đầu trang">
        <span class="material-symbols-outlined text-[24px] group-hover:-translate-y-0.5 transition-transform">arrow_upward</span>
    </button>

    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');
            
            if (scrollToTopBtn) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 300) {
                        scrollToTopBtn.classList.remove('opacity-0', 'translate-y-10', 'pointer-events-none');
                        scrollToTopBtn.classList.add('opacity-100', 'translate-y-0');
                    } else {
                        scrollToTopBtn.classList.add('opacity-0', 'translate-y-10', 'pointer-events-none');
                        scrollToTopBtn.classList.remove('opacity-100', 'translate-y-0');
                    }
                });

                scrollToTopBtn.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>
</body>
</html>
