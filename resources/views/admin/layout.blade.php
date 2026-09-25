<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản Trị Hệ Thống - Quán Mới')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Be Vietnam Pro', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .material-symbols-outlined.fill-1 { font-variation-settings: 'FILL' 1; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.6); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(51, 65, 85, 0.8); border-radius: 4px; }

        @media (min-width: 768px) {
            #admin-sidebar {
                display: flex !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 256px !important;
                height: 100vh !important;
                z-index: 30 !important;
            }
            .admin-main-wrapper {
                margin-left: 256px !important;
                width: calc(100% - 256px) !important;
                min-width: 0 !important;
            }
        }
    </style>
</head>
<body class="h-full text-slate-800 bg-slate-50 font-sans antialiased flex flex-col md:flex-row min-h-screen overflow-x-hidden">

    {{-- Overlay for Mobile Sidebar --}}
    <div id="mobile-sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden md:hidden transition-opacity"></div>

    {{-- Mobile Sidebar Drawer (< md) --}}
    <aside id="admin-sidebar-mobile" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex md:hidden flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl border-r border-slate-800">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-800/80 bg-slate-950/40">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-orange-500/20">
                        <span class="material-symbols-outlined text-white text-[24px]">storefront</span>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-lg text-white tracking-tight flex items-center gap-1.5">
                            Quán Mới <span class="bg-amber-500/20 text-amber-400 text-[10px] font-bold px-1.5 py-0.5 rounded border border-amber-500/30">ADMIN</span>
                        </h1>
                        <p class="text-[11px] text-slate-400 font-medium">Hệ thống quản trị trung tâm</p>
                    </div>
                </a>
                <button onclick="toggleSidebar()" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-6 custom-scrollbar">
                @include('admin.partials.sidebar-nav')
            </nav>

            <div class="p-4 border-t border-slate-800 bg-slate-950/60 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-amber-500 text-slate-950 font-black flex items-center justify-center flex-shrink-0 shadow-md">
                        {{ strtoupper(substr(auth()->user()->ho_ten ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->ho_ten ?? 'Admin' }}</p>
                        <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@quanmoi.com' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Đăng xuất" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Desktop Admin Sidebar (>= md) --}}
    <aside id="admin-sidebar" class="hidden md:flex flex-col justify-between fixed top-0 left-0 z-30 w-64 h-screen bg-slate-900 text-slate-300 border-r border-slate-800 flex-shrink-0">
        <div class="flex flex-col h-full">
            {{-- Brand Logo Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-800/80 bg-slate-950/40">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-orange-500/20 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-white text-[24px]">storefront</span>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-lg text-white tracking-tight flex items-center gap-1.5">
                            Quán Mới <span class="bg-amber-500/20 text-amber-400 text-[10px] font-bold px-1.5 py-0.5 rounded border border-amber-500/30">ADMIN</span>
                        </h1>
                        <p class="text-[11px] text-slate-400 font-medium">Hệ thống quản trị trung tâm</p>
                    </div>
                </a>
            </div>

            {{-- Sidebar Navigation --}}
            <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-6 custom-scrollbar">
                @include('admin.partials.sidebar-nav')
            </nav>

            {{-- User Profile Footer --}}
            <div class="p-4 border-t border-slate-800 bg-slate-950/60 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-amber-500 text-slate-950 font-black flex items-center justify-center flex-shrink-0 shadow-md">
                        {{ strtoupper(substr(auth()->user()->ho_ten ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->ho_ten ?? 'Admin' }}</p>
                        <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@quanmoi.com' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Đăng xuất" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content Wrapper --}}
    <div class="admin-main-wrapper flex-1 flex flex-col min-w-0 min-h-screen bg-slate-50 md:ml-64 w-full md:w-[calc(100%-16rem)]">
        
        {{-- Topbar Header --}}
        <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-slate-200 px-4 sm:px-6 py-3.5 flex items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-[24px]">menu</span>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        @yield('page-title', 'Dashboard')
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="/" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                    <span>Xem website</span>
                </a>
                
                <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-full text-xs font-bold border border-emerald-200/80 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="hidden sm:inline">Quyền</span> Admin
                </span>
            </div>
        </header>

        {{-- Alerts Section --}}
        <div class="px-4 sm:px-6 pt-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200/80 text-emerald-900 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-3 shadow-xs mb-4">
                    <span class="material-symbols-outlined text-emerald-600 flex-shrink-0">check_circle</span>
                    <div class="flex-1">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200/80 text-rose-900 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-3 shadow-xs mb-4">
                    <span class="material-symbols-outlined text-rose-600 flex-shrink-0">error</span>
                    <div class="flex-1">{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200/80 text-rose-900 px-4 py-3 rounded-xl text-sm font-semibold mb-4">
                    <div class="flex items-center gap-2 text-rose-700 font-bold mb-1">
                        <span class="material-symbols-outlined text-rose-600">warning</span>
                        <span>Đã xảy ra lỗi:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 text-rose-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Main Page Content --}}
        <main class="p-4 sm:p-6 flex-grow">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const mobileSidebar = document.getElementById('admin-sidebar-mobile');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            if (mobileSidebar) {
                mobileSidebar.classList.toggle('-translate-x-full');
            }
            if (backdrop) {
                backdrop.classList.toggle('hidden');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
