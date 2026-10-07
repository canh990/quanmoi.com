<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hệ Thống Quản Trị - Quán Mới Doanh Nghiệp')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        colors: {
                            primary: '#a04100',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .material-symbols-outlined.fill-1 { font-variation-settings: 'FILL' 1; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }

        @media (min-width: 768px) {
            #admin-sidebar {
                display: flex !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 260px !important;
                height: 100vh !important;
                z-index: 40 !important;
            }
            .admin-main-wrapper {
                margin-left: 260px !important;
                width: calc(100% - 260px) !important;
                min-width: 0 !important;
            }
        }
    </style>
</head>
<body class="h-full text-slate-800 bg-[#f8fafc] font-sans antialiased flex flex-col md:flex-row min-h-screen overflow-x-hidden">

    {{-- Backdrop Mobile --}}
    <div id="mobile-sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-40 hidden md:hidden transition-opacity"></div>

    {{-- Mobile Sidebar Drawer --}}
    <aside id="admin-sidebar-mobile" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex md:hidden flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out border-r border-slate-800 shadow-2xl">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800 bg-slate-950/60">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#F59E0B] flex items-center justify-center text-white shadow-sm font-bold">
                        <span class="material-symbols-outlined text-[20px]">space_dashboard</span>
                    </div>
                    <div>
                        <h1 class="font-bold text-sm text-white tracking-tight leading-none">QuanMoi Admin</h1>
                        <span class="text-[10px] text-slate-400 font-medium">trang quản trị hệ thống</span>
                    </div>
                </a>
                <button onclick="toggleSidebar()" class="text-slate-400 hover:text-white p-1 rounded-md">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5 custom-scrollbar">
                @include('admin.partials.sidebar-nav')
            </nav>

            <div class="p-3.5 border-t border-slate-800 bg-slate-950/40 flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-md bg-indigo-600 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->ho_ten ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-white truncate leading-tight">{{ auth()->user()->ho_ten ?? 'Quản Trị Viên' }}</p>
                        <p class="text-[10px] text-slate-400 truncate leading-tight">Quản Trị Cao Cấp</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Đăng xuất" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-md transition-colors">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Desktop Sidebar --}}
    <aside id="admin-sidebar" class="hidden md:flex flex-col justify-between fixed top-0 left-0 z-40 w-65 h-screen bg-slate-900 text-slate-300 border-r border-slate-800/80 flex-shrink-0">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800/80 bg-slate-950/60">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#F59E0B] flex items-center justify-center text-white shadow-sm font-bold">
                        <span class="material-symbols-outlined text-[20px]">space_dashboard</span>
                    </div>
                    <div>
                        <h1 class="font-bold text-sm text-white tracking-tight leading-none">QuanMoi Admin</h1>
                        <span class="text-[10px] text-slate-400 font-medium">trang quản trị hệ thống</span>
                    </div>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5 custom-scrollbar">
                @include('admin.partials.sidebar-nav')
            </nav>

            <div class="p-3.5 border-t border-slate-800 bg-slate-950/40 flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-md bg-indigo-600 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->ho_ten ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-white truncate leading-tight">{{ auth()->user()->ho_ten ?? 'Quản Trị Viên' }}</p>
                        <p class="text-[10px] text-slate-400 truncate leading-tight">Quản Trị Cao Cấp</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Đăng xuất" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-md transition-colors">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content Wrapper --}}
    <div class="admin-main-wrapper flex-1 flex flex-col min-w-0 min-h-screen bg-[#f8fafc] md:ml-65 w-full md:w-[calc(100%-16.25rem)]">
        
        {{-- Header Topbar --}}
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/90 px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-[22px]">menu</span>
                </button>
                
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                    <span>Quản trị</span>
                    <span class="material-symbols-outlined text-[14px] text-slate-300">chevron_right</span>
                    <span class="font-semibold text-slate-900">@yield('page-title', 'Bảng Điều Khiển')</span>
                </div>
            </div>

            {{-- Topbar Right Tools --}}
            <div class="flex items-center gap-3">
                {{-- Quick Search Button --}}
                <div onclick="openCommandPalette()" class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-slate-100/80 hover:bg-slate-100 border border-slate-200 rounded-lg text-xs text-slate-400 w-60 cursor-pointer transition-colors shadow-2xs">
                    <span class="material-symbols-outlined text-[16px] text-slate-400">search</span>
                    <span class="flex-1 truncate">Tìm kiếm lối tắt...</span>
                    <kbd class="px-1.5 py-0.5 text-[10px] font-semibold bg-white border border-slate-200 rounded text-slate-500">Ctrl K</kbd>
                </div>

                <a href="{{ route('admin.vai-tro.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200/80 hover:bg-blue-100 transition-colors shadow-2xs">
                    <span class="material-symbols-outlined text-[16px] text-blue-600">admin_panel_settings</span>
                    <span>Phân quyền</span>
                </a>

                <a href="/" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-xs">
                    <span class="material-symbols-outlined text-[16px] text-slate-500">open_in_new</span>
                    <span class="hidden sm:inline">Trang chủ web</span>
                </a>

                <div class="h-5 w-px bg-slate-200"></div>

                <div class="flex items-center gap-2 px-3 py-1 bg-emerald-50 border border-emerald-200/80 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-emerald-700">Quản Trị Viên Cao Cấp</span>
                </div>
            </div>
        </header>

        {{-- Flash Alerts --}}
        <div class="px-4 sm:px-6 pt-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 rounded-lg text-xs font-medium flex items-center gap-2.5 shadow-xs mb-3">
                    <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                    <div class="flex-1">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 rounded-lg text-xs font-medium flex items-center gap-2.5 shadow-xs mb-3">
                    <span class="material-symbols-outlined text-rose-600 text-[18px]">error</span>
                    <div class="flex-1">{{ session('error') }}</div>
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="p-4 sm:p-6 flex-grow">
            @yield('content')
        </main>
    </div>

    {{-- Enterprise Command Palette Modal (Ctrl + K) --}}
    <div id="command-palette-modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden flex items-start justify-center pt-20 px-4 transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden transform transition-all">
            <div class="flex items-center px-4 border-b border-slate-100 bg-slate-50/50">
                <span class="material-symbols-outlined text-slate-400">search</span>
                <input id="command-palette-input" onkeyup="filterCommandPalette()" type="text" placeholder="Gõ từ khóa để chuyển nhanh trang quản trị..." class="w-full px-3 py-3.5 text-xs text-slate-800 placeholder-slate-400 bg-transparent focus:outline-none font-medium">
                <button onclick="closeCommandPalette()" class="px-1.5 py-0.5 text-[10px] font-bold bg-slate-200/80 hover:bg-slate-200 text-slate-600 rounded">ESC</button>
            </div>
            <div id="command-palette-list" class="p-2 max-h-80 overflow-y-auto space-y-1 text-xs">
                <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Lối Tắt Truy Cập Nhanh</div>
                <a href="{{ route('admin.dashboard') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">space_dashboard</span>
                    <span>Bảng Điều Khiển Trung Tâm</span>
                </a>
                <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">fact_check</span>
                    <span>Duyệt Yêu Cầu Quán & Người Dùng</span>
                </a>
                <a href="{{ route('admin.quan.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                    <span>Quản Lý Danh Sách Quán</span>
                </a>
                <a href="{{ route('admin.nguoi-dung.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                    <span>Quản Lý Người Dùng & Tài Khoản</span>
                </a>
                <a href="{{ route('admin.thong-ke.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">query_stats</span>
                    <span>Báo Cáo & Thống Kê Doanh Thu</span>
                </a>
                <a href="{{ route('admin.audit-log.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">reorder</span>
                    <span>Nhật Ký Tác Vụ Hệ Thống (Audit Log)</span>
                </a>
                <a href="{{ route('admin.ai-tools.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">neurology</span>
                    <span>Trợ Lý Trí Tuệ Nhân Tạo AI</span>
                </a>
                <a href="{{ route('admin.bao-mat.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">shield</span>
                    <span>Cấu Hình Bảo Mật Hệ Thống</span>
                </a>
            </div>
        </div>
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

        function openCommandPalette() {
            const modal = document.getElementById('command-palette-modal');
            const input = document.getElementById('command-palette-input');
            if (modal) {
                modal.classList.remove('hidden');
                if (input) {
                    input.value = '';
                    filterCommandPalette();
                    setTimeout(() => input.focus(), 50);
                }
            }
        }

        function closeCommandPalette() {
            const modal = document.getElementById('command-palette-modal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function filterCommandPalette() {
            const query = (document.getElementById('command-palette-input')?.value || '').toLowerCase();
            const items = document.querySelectorAll('#command-palette-list .cmd-item');
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? 'flex' : 'none';
            });
        }

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                openCommandPalette();
            }
            if (e.key === 'Escape') {
                closeCommandPalette();
            }
        });

        document.getElementById('command-palette-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCommandPalette();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

