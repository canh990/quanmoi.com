<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quán Mới - Khám phá ẩm thực')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary-fixed": "#ffdbcc",
                        "on-error": "#ffffff",
                        "secondary": "#296767",
                        "on-tertiary-container": "#003a15",
                        "on-surface": "#191c1d",
                        "surface": "#f8f9fa",
                        "on-secondary-container": "#306e6d",
                        "on-secondary": "#ffffff",
                        "surface-tint": "#a04100",
                        "surface-container-lowest": "#ffffff",
                        "background": "#f8f9fa",
                        "surface-bright": "#f8f9fa",
                        "tick-xanh": "#22C55E",
                        "on-secondary-fixed": "#002020",
                        "text-muted": "#6B7280",
                        "tertiary-fixed-dim": "#4ae176",
                        "surface-container-low": "#f3f4f5",
                        "surface-container": "#edeeef",
                        "on-secondary-fixed-variant": "#044f4f",
                        "surface-card": "#FFFFFF",
                        "outline-variant": "#e2bfb0",
                        "on-tertiary-fixed-variant": "#005321",
                        "secondary-fixed": "#b0eeed",
                        "error": "#ba1a1a",
                        "on-primary-fixed-variant": "#7a3000",
                        "primary": "#a04100",
                        "surface-container-high": "#e7e8e9",
                        "inverse-on-surface": "#f0f1f2",
                        "on-surface-variant": "#5a4136",
                        "error-container": "#ffdad6",
                        "surface-dim": "#d9dadb",
                        "secondary-fixed-dim": "#94d1d1",
                        "text-main": "#111827",
                        "on-error-container": "#93000a",
                        "tertiary": "#006e2f",
                        "inverse-primary": "#ffb693",
                        "on-background": "#191c1d",
                        "surface-container-highest": "#e1e3e4",
                        "secondary-container": "#b0eeed",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed": "#6bff8f",
                        "on-primary-container": "#572000",
                        "on-primary-fixed": "#351000",
                        "outline": "#8e7164",
                        "tertiary-container": "#00b050",
                        "primary-fixed-dim": "#ffb693",
                        "primary-container": "#ff6b00",
                        "on-primary": "#ffffff",
                        "on-tertiary-fixed": "#002109",
                        "inverse-surface": "#2e3132",
                        "surface-variant": "#e1e3e4"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "gutter": "12px",
                        "container-margin": "16px",
                        "stack-sm": "8px",
                        "stack-md": "16px",
                        "stack-lg": "24px",
                        "base": "4px"
                    },
                    "fontFamily": {
                        "display-lg": ["Be Vietnam Pro"],
                        "label-md": ["Be Vietnam Pro"],
                        "headline-lg": ["Be Vietnam Pro"],
                        "title-md": ["Be Vietnam Pro"],
                        "label-sm": ["Be Vietnam Pro"],
                        "body-lg": ["Be Vietnam Pro"],
                        "headline-lg-mobile": ["Be Vietnam Pro"],
                        "body-sm": ["Be Vietnam Pro"]
                    },
                    "fontSize": {
                        "display-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "label-md": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600" }],
                        "headline-lg": ["24px", { "lineHeight": "32px", "fontWeight": "700" }],
                        "title-md": ["18px", { "lineHeight": "24px", "fontWeight": "600" }],
                        "label-sm": ["11px", { "lineHeight": "14px", "fontWeight": "500" }],
                        "body-lg": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "headline-lg-mobile": ["20px", { "lineHeight": "28px", "fontWeight": "700" }],
                        "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }]
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Be Vietnam Pro', sans-serif; }
        
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Đồng bộ Material Icons */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            vertical-align: middle;
        }

        /* Hiệu ứng trượt gạch chân cho link Desktop Nav */
        .nav-link {
            position: relative;
            padding-bottom: 4px;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2.5px;
            background: #a04100;
            border-radius: 2px;
            transition: width 0.3s ease, left 0.3s ease;
        }
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
            left: 0;
        }

        /* Responsive padding dốc (Safe area cho Mobile Bottom Nav) */
        @media (max-width: 767px) {
            body {
                padding-bottom: calc(64px + env(safe-area-inset-bottom, 0px));
            }
        }
    </style>
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
   <!-- Căn chỉnh lại khoảng cách trên để nội dung chính khớp sát mép thanh điều hướng mới -->

    @yield('content')


    <!-- Global Footer & Mobile Navigation -->
    @yield('footer')

    @stack('scripts')
</body>
</html>