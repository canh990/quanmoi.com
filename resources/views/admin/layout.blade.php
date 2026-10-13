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
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
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

        /* ==========================================================================
           ENTERPRISE DESIGN SYSTEM: BUTTONS & CONTROLS
           Chuẩn hóa doanh nghiệp B2B: Tương phản cao, chữ sắc nét, không bị chìm màu
           ========================================================================== */
        .btn-enterprise {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.375rem !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            line-height: 1rem !important;
            padding: 0.4375rem 0.875rem !important;
            border-radius: 0.5rem !important;
            white-space: nowrap !important;
            cursor: pointer !important;
            user-select: none !important;
            text-decoration: none !important;
            border: 1px solid transparent !important;
            transition: all 0.15s ease-in-out !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            flex-shrink: 0 !important;
        }
        .btn-enterprise:focus-visible {
            outline: 2px solid #0284c7 !important;
            outline-offset: 2px !important;
        }
        .btn-enterprise:disabled,
        .btn-enterprise[disabled] {
            opacity: 0.55 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            box-shadow: none !important;
            transform: none !important;
        }
        .btn-enterprise .material-symbols-outlined {
            font-size: 16px !important;
            line-height: 1 !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        /* 1. SUCCESS: Phê duyệt / Kích hoạt / Duyệt lại */
        .btn-enterprise-success {
            background-color: #15803d !important;
            border-color: #166534 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-success:hover {
            background-color: #166534 !important;
            border-color: #14532d !important;
            color: #ffffff !important;
        }
        .btn-enterprise-success:active {
            background-color: #14532d !important;
            border-color: #052e16 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-success .material-symbols-outlined {
            color: #ffffff !important;
        }

        /* 2. DANGER: Không duyệt / Từ chối / Xóa */
        .btn-enterprise-danger {
            background-color: #b91c1c !important;
            border-color: #991b1b !important;
            color: #ffffff !important;
        }
        .btn-enterprise-danger:hover {
            background-color: #991b1b !important;
            border-color: #7f1d1d !important;
            color: #ffffff !important;
        }
        .btn-enterprise-danger:active {
            background-color: #7f1d1d !important;
            border-color: #450a0a !important;
            color: #ffffff !important;
        }
        .btn-enterprise-danger .material-symbols-outlined {
            color: #ffffff !important;
        }

        /* 2b. SOFT DANGER: Không duyệt nhẹ nhàng */
        .btn-enterprise-soft-danger {
            background-color: #ffffff !important;
            border-color: #fca5a5 !important;
            color: #b91c1c !important;
        }
        .btn-enterprise-soft-danger:hover {
            background-color: #fef2f2 !important;
            border-color: #f87171 !important;
            color: #991b1b !important;
        }
        .btn-enterprise-soft-danger:active {
            background-color: #fee2e2 !important;
            border-color: #ef4444 !important;
            color: #7f1d1d !important;
        }
        .btn-enterprise-soft-danger .material-symbols-outlined {
            color: #b91c1c !important;
        }

        /* 3. SECONDARY: Nút phụ / Đóng / Quay lại */
        .btn-enterprise-secondary {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
        }
        .btn-enterprise-secondary:hover {
            background-color: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
        .btn-enterprise-secondary:active {
            background-color: #f1f5f9 !important;
            border-color: #64748b !important;
            color: #0f172a !important;
        }
        .btn-enterprise-secondary .material-symbols-outlined {
            color: #475569 !important;
        }
        .btn-enterprise-secondary:hover .material-symbols-outlined {
            color: #0f172a !important;
        }

        /* 4. PHOTO: Xem ảnh quán (tương phản cao) */
        .btn-enterprise-photo {
            background-color: #fffbeb !important;
            border-color: #fcd34d !important;
            color: #92400e !important;
        }
        .btn-enterprise-photo:hover {
            background-color: #fef3c7 !important;
            border-color: #f59e0b !important;
            color: #78350f !important;
        }
        .btn-enterprise-photo:active {
            background-color: #fde68a !important;
            border-color: #d97706 !important;
            color: #78350f !important;
        }
        .btn-enterprise-photo .material-symbols-outlined {
            color: #b45309 !important;
        }
        .btn-enterprise-photo:hover .material-symbols-outlined {
            color: #78350f !important;
        }

        /* 5. DARK: Trang quán / Xem ngoài web */
        .btn-enterprise-dark {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
            color: #ffffff !important;
        }
        .btn-enterprise-dark:hover {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-dark:active {
            background-color: #020617 !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }
        .btn-enterprise-dark .material-symbols-outlined {
            color: #ffffff !important;
        }

        /* 6. INDIGO: Xác thực tài khoản */
        .btn-enterprise-indigo {
            background-color: #4338ca !important;
            border-color: #3730a3 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-indigo:hover {
            background-color: #3730a3 !important;
            border-color: #312e81 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-indigo:active {
            background-color: #312e81 !important;
            border-color: #1e1b4b !important;
            color: #ffffff !important;
        }
        .btn-enterprise-indigo .material-symbols-outlined {
            color: #ffffff !important;
        }

        /* 7. PRIMARY / BRAND: Amber-600 */
        .btn-enterprise-primary {
            background-color: #d97706 !important;
            border-color: #b45309 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-primary:hover {
            background-color: #b45309 !important;
            border-color: #92400e !important;
            color: #ffffff !important;
        }
        .btn-enterprise-primary:active {
            background-color: #92400e !important;
            border-color: #78350f !important;
            color: #ffffff !important;
        }
        .btn-enterprise-primary .material-symbols-outlined {
            color: #ffffff !important;
        }

        /* 8. TABLE CELL ANTI-OVERFLOW & BOUNDARY ENFORCEMENT */
        .duyet-table-row td {
            vertical-align: middle;
        }
        .duyet-table-row td .truncate {
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            min-width: 0 !important;
        }
        .duyet-table-row td .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
            overflow-wrap: anywhere;
        }
    </style>
</head>
<body class="h-full text-slate-800 bg-[#f8fafc] font-sans antialiased flex flex-col md:flex-row min-h-screen overflow-x-hidden">

    {{-- Instant Loading Top Progress Bar --}}
    <div id="admin-top-progress-bar" class="fixed top-0 left-0 h-[3px] bg-amber-500 z-[9999] transition-all duration-150 pointer-events-none opacity-0 w-0 shadow-xs"></div>

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
                        <p class="text-[10px] text-slate-400 truncate leading-tight">Quản Trị Cấp Cao</p>
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
                    <span id="header-page-title" class="font-semibold text-slate-900">@yield('page-title', 'Bảng Điều Khiển')</span>
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

                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-white border border-slate-200/90 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-300 transition-all shadow-2xs group" title="Quay về trang chủ website">
                    <span class="material-symbols-outlined text-[18px] text-amber-500 group-hover:scale-110 transition-transform">home</span>
                    <span class="font-bold">Quay về trang chủ</span>
                </a>

                {{-- Real-time Admin Notification Bell --}}
                <div class="relative" id="admin-notif-container">
                    <button id="admin-notif-bell" onclick="toggleNotifDropdown()" class="relative p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">notifications</span>
                        <span id="realtime-badge-count" class="hidden absolute -top-1 -right-1 min-w-[20px] h-[20px] px-1 bg-rose-500 text-white text-[11px] font-bold rounded-full flex items-center justify-center border-2 border-white shadow-xs">0</span>
                    </button>

                    {{-- Notification Dropdown Menu --}}
                    <div id="admin-notif-dropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden transform transition-all">
                        <div class="px-4 py-3 bg-slate-900 text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-amber-400">notifications_active</span>
                                <h3 class="text-xs font-bold uppercase tracking-wider">Thông Báo Thời Gian Thực</h3>
                            </div>
                            <span id="realtime-badge-dropdown" class="px-2 py-0.5 text-[10px] font-bold bg-amber-500 text-slate-950 rounded-full">0 mới</span>
                        </div>

                        {{-- Quick Filter Buttons in Dropdown --}}
                        <div class="px-3 py-2 bg-slate-50 border-b border-slate-100 flex items-center gap-1 overflow-x-auto text-[11px]">
                            <button type="button" onclick="filterDropdownNotifs('all')" class="notif-tab-btn active px-2.5 py-1 rounded-lg font-bold bg-slate-900 text-white transition-colors" data-filter="all">Tất cả</button>
                            <button type="button" onclick="filterDropdownNotifs('quan')" class="notif-tab-btn px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:bg-slate-200 transition-colors" data-filter="quan">Quán</button>
                            <button type="button" onclick="filterDropdownNotifs('blog')" class="notif-tab-btn px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:bg-slate-200 transition-colors" data-filter="blog">Bài viết</button>
                            <button type="button" onclick="filterDropdownNotifs('user')" class="notif-tab-btn px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:bg-slate-200 transition-colors" data-filter="user">User</button>
                            <button type="button" onclick="filterDropdownNotifs('broadcast')" class="notif-tab-btn px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:bg-slate-200 transition-colors" data-filter="broadcast">Hệ thống</button>
                        </div>

                        <div id="realtime-notif-list" class="max-h-80 overflow-y-auto divide-y divide-slate-100 p-2 text-xs">
                            <div class="p-4 text-center text-slate-400 font-medium">Đang tải toàn bộ thông báo...</div>
                        </div>

                        <div class="p-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold">
                            <a href="{{ route('admin.thong-bao.index') }}" class="text-indigo-600 hover:text-indigo-800 transition-colors">Trung tâm thông báo →</a>
                            <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="text-amber-700 hover:text-amber-800 transition-colors">Xem chờ duyệt →</a>
                        </div>
                    </div>
                </div>

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
        <main id="admin-main-content" class="p-4 sm:p-6 flex-grow transition-opacity duration-150">
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
                <a href="{{ route('home') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-amber-50 hover:text-amber-700 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-amber-500">home</span>
                    <span>Quay Về Trang Chủ Website</span>
                </a>
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

    {{-- Enterprise Custom Confirmation Modal --}}
    <div id="admin-confirm-modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 transition-opacity duration-200">
        <div id="admin-confirm-box" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full p-6 transform transition-all scale-95 duration-200 text-center">
            <div id="confirm-modal-icon-bg" class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4 transition-colors bg-amber-100 text-amber-600">
                <span id="confirm-modal-icon" class="material-symbols-outlined text-[32px]">help</span>
            </div>
            <h3 id="confirm-modal-title" class="text-base font-bold text-slate-900 mb-1.5">Xác Nhận Thao Tác</h3>
            <div id="confirm-modal-message" class="text-xs text-slate-600 mb-6 leading-relaxed">Bạn có chắc chắn muốn thực hiện hành động này?</div>

            <div class="flex items-center justify-center gap-3">
                <button type="button" onclick="closeConfirmModal()" class="btn-enterprise btn-enterprise-secondary w-1/2 py-2.5 text-xs">
                    Hủy bỏ
                </button>
                <button type="button" id="confirm-modal-submit-btn" class="btn-enterprise btn-enterprise-success w-1/2 py-2.5 text-xs">
                    <span id="confirm-modal-btn-icon" class="material-symbols-outlined text-[16px]">check_circle</span>
                    <span id="confirm-modal-btn-text">Xác nhận</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Enterprise Venue Media & Detail Inspection Modal --}}
    <div id="venue-media-modal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 hidden flex items-center justify-center p-3 sm:p-5 transition-opacity duration-200">
        <div id="venue-media-box" class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 max-w-5xl w-full max-h-[94vh] flex flex-col overflow-hidden transform transition-all scale-95 duration-200">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80 shrink-0">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center border border-amber-200/50">
                        <span class="material-symbols-outlined text-[22px]">verified</span>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 id="vm-title" class="text-base font-black text-slate-900 leading-tight">Hồ Sơ & Thẩm Định Quán Ăn</h3>
                            <span id="vm-status-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Chờ duyệt</span>
                        </div>
                        <p id="vm-subtitle" class="text-xs text-slate-500 mt-0.5">Thẩm định tính xác thực của hình ảnh, địa điểm và thông tin chủ cơ sở</p>
                    </div>
                </div>
                <button type="button" onclick="closeVenueMediaModal()" class="w-9 h-9 rounded-xl bg-slate-200/70 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors cursor-pointer" title="Đóng cửa sổ">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- Body: 2 Columns on Desktop --}}
            <div class="p-6 overflow-y-auto flex-grow">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {{-- Left Col: Media Gallery (7 cols) --}}
                    <div class="lg:col-span-7 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-amber-500">photo_library</span>
                                Album Hình Ảnh Thẩm Định
                            </span>
                            <span id="vm-gallery-count" class="text-[11px] text-slate-500 font-semibold bg-slate-100 px-2 py-0.5 rounded-full">1 hình ảnh</span>
                        </div>

                        {{-- Main Big Photo Preview --}}
                        <div class="relative w-full h-72 sm:h-96 rounded-2xl overflow-hidden bg-slate-950 shadow-inner group border border-slate-200">
                            <img id="vm-main-img" src="" alt="Ảnh quán" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-102" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&auto=format&fit=crop&q=80';">
                            <div class="absolute bottom-3 left-3 bg-slate-950/80 backdrop-blur-md text-white text-[11px] font-semibold px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-md">
                                <span class="material-symbols-outlined text-[14px] text-amber-400">image</span>
                                <span id="vm-main-label">Ảnh Bìa Đại Diện</span>
                            </div>
                            <a id="vm-view-origin" href="#" target="_blank" class="absolute top-3 right-3 bg-white/90 hover:bg-white text-slate-800 text-xs font-bold px-3 py-1.5 rounded-xl shadow-md transition-all flex items-center gap-1 opacity-0 group-hover:opacity-100 cursor-pointer">
                                <span class="material-symbols-outlined text-[15px]">open_in_full</span>
                                <span>Xem ảnh gốc</span>
                            </a>
                        </div>

                        {{-- Gallery Thumbnails Strip --}}
                        <div id="vm-gallery-strip" class="flex items-center gap-2.5 overflow-x-auto pb-2 pt-1">
                            {{-- Injected dynamically --}}
                        </div>
                    </div>

                    {{-- Right Col: Inspection Dossier (5 cols) --}}
                    <div class="lg:col-span-5 space-y-4">
                        <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200/80 space-y-3">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[15px] text-indigo-500">description</span>
                                Hồ Sơ Đăng Ký Địa Điểm
                            </h4>
                            
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tên Địa Điểm</span>
                                <div id="vm-detail-name" class="font-black text-slate-900 text-base mt-0.5">--</div>
                                <div class="mt-1">
                                    <span id="vm-detail-type" class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/70">--</span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Địa Chỉ Thực Tế</span>
                                <div id="vm-detail-address" class="text-xs text-slate-700 font-medium mt-0.5 flex items-start gap-1">
                                    <span class="material-symbols-outlined text-[15px] text-rose-500 shrink-0 mt-0.5">location_on</span>
                                    <span>--</span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Chủ Quán / Người Đăng</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[15px]">person</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div id="vm-detail-owner" class="font-bold text-xs text-slate-900 truncate">--</div>
                                        <div id="vm-detail-contact" class="text-[11px] text-slate-500 space-y-0.5 mt-0.5">--</div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Giờ Mở Cửa & Khung Giá</span>
                                <div id="vm-detail-hours-price" class="text-xs text-slate-700 font-medium mt-0.5">--</div>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-400">
                                <span>Thời gian nộp duyệt:</span>
                                <span id="vm-detail-date" class="font-bold text-slate-600">--</span>
                            </div>
                        </div>

                        {{-- Quality Verification Checklist --}}
                        <div class="bg-amber-50/50 rounded-2xl p-3.5 border border-amber-200/50 text-[11px] space-y-1.5 text-amber-900">
                            <div class="font-bold flex items-center gap-1.5 text-amber-800">
                                <span class="material-symbols-outlined text-[16px] text-amber-600">fact_check</span>
                                Tiêu Chuẩn Thẩm Định Doanh Nghiệp
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-600">
                                <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
                                <span>Hình ảnh chụp thật, không chứa hình mờ hoặc vi phạm</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-600">
                                <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
                                <span>Địa chỉ rõ ràng, đúng khu vực đăng ký</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-600">
                                <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
                                <span>Thông tin liên hệ chủ cơ sở chính chủ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Action Buttons --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between shrink-0 flex-nowrap gap-3">
                <button type="button" onclick="closeVenueMediaModal()" class="btn-enterprise btn-enterprise-secondary">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    <span class="whitespace-nowrap">Đóng lại</span>
                </button>
                <div id="vm-actions" class="flex items-center gap-2 flex-nowrap shrink-0">
                    {{-- Approve & Reject buttons injected dynamically --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Floating Toast Notification Container --}}
    <div id="realtime-toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-sm pointer-events-auto"></div>

    <script>
        // Enterprise Confirmation Modal Engine
        let confirmModalCallback = null;

        function openConfirmModal(options) {
            const modal = document.getElementById('admin-confirm-modal');
            const box = document.getElementById('admin-confirm-box');
            const titleEl = document.getElementById('confirm-modal-title');
            const msgEl = document.getElementById('confirm-modal-message');
            const iconEl = document.getElementById('confirm-modal-icon');
            const iconBgEl = document.getElementById('confirm-modal-icon-bg');
            const submitBtn = document.getElementById('confirm-modal-submit-btn');
            const btnText = document.getElementById('confirm-modal-btn-text');
            const btnIcon = document.getElementById('confirm-modal-btn-icon');

            if (!modal) return;

            confirmModalCallback = options.onConfirm || null;

            titleEl.textContent = options.title || 'Xác Nhận Thao Tác';
            msgEl.innerHTML = options.message || 'Bạn có chắc chắn muốn thực hiện hành động này?';
            iconEl.textContent = options.icon || 'help';
            iconBgEl.className = 'w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4 ' + (options.iconBg || 'bg-amber-100 text-amber-600');
            btnText.textContent = options.confirmText || 'Xác nhận';
            btnIcon.textContent = options.confirmIcon || 'check_circle';
            submitBtn.className = 'btn-enterprise w-1/2 py-2.5 ' + (options.confirmClass || 'btn-enterprise-success');

            modal.classList.remove('hidden');
            setTimeout(() => {
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            }, 10);
        }

        function closeConfirmModal() {
            const modal = document.getElementById('admin-confirm-modal');
            const box = document.getElementById('admin-confirm-box');
            if (!modal) return;

            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                confirmModalCallback = null;
            }, 150);
        }

        document.getElementById('confirm-modal-submit-btn')?.addEventListener('click', function() {
            if (typeof confirmModalCallback === 'function') {
                const cb = confirmModalCallback;
                closeConfirmModal();
                cb();
            } else {
                closeConfirmModal();
            }
        });

        document.getElementById('venue-media-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeVenueMediaModal();
            }
        });

        function openVenueMediaModal(v) {
            if (!v) return;
            const modal = document.getElementById('venue-media-modal');
            const box = document.getElementById('venue-media-box');
            if (!modal) return;

            document.getElementById('vm-title').textContent = v.ten_quan || 'Hồ Sơ Quán Ăn';
            document.getElementById('vm-subtitle').textContent = (v.loai_hinh ? v.loai_hinh + ' • ' : '') + (v.dia_chi || '');
            
            // Status badge in modal header
            const statusBadge = document.getElementById('vm-status-badge');
            if (statusBadge) {
                if (v.trang_thai === 'da_duyet') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200/60';
                    statusBadge.textContent = 'Đang Hoạt Động';
                } else if (v.trang_thai === 'bi_khoa') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200/60';
                    statusBadge.textContent = 'Bị Từ Chối / Khóa';
                } else {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200/60';
                    statusBadge.textContent = 'Chờ Phê Duyệt';
                }
            }

            const mainImg = document.getElementById('vm-main-img');
            const viewOrigin = document.getElementById('vm-view-origin');
            const fallbackSrc = 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&auto=format&fit=crop&q=80';
            const defaultCover = v.anh_bia_url || fallbackSrc;
            
            mainImg.src = defaultCover;
            viewOrigin.href = defaultCover;
            document.getElementById('vm-main-label').textContent = 'Ảnh Bìa Đại Diện (1/' + ((v.gallery ? v.gallery.length : 0) + 1) + ')';

            // Gallery Strip
            const galleryStrip = document.getElementById('vm-gallery-strip');
            galleryStrip.innerHTML = '';
            
            const allImages = [];
            if (v.anh_bia_url) {
                allImages.push({ url: v.anh_bia_url, label: 'Ảnh Bìa Đại Diện' });
            }
            if (Array.isArray(v.gallery)) {
                v.gallery.forEach((gUrl, idx) => {
                    allImages.push({ url: gUrl, label: 'Hình Không Gian / Món ' + (idx + 1) });
                });
            }

            document.getElementById('vm-gallery-count').textContent = allImages.length > 0 ? (allImages.length + ' hình ảnh') : 'Chưa có ảnh phụ';

            if (allImages.length > 0) {
                allImages.forEach((imgObj, i) => {
                    const thumb = document.createElement('div');
                    thumb.className = 'w-16 h-16 rounded-xl overflow-hidden shrink-0 border-2 cursor-pointer transition-all ' + (i === 0 ? 'border-amber-500 scale-105 shadow-sm ring-2 ring-amber-300' : 'border-slate-200 hover:border-slate-400 opacity-70 hover:opacity-100');
                    thumb.innerHTML = `<img src="${imgObj.url}" alt="${imgObj.label}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='${fallbackSrc}';">`;
                    thumb.onclick = function() {
                        galleryStrip.querySelectorAll('div').forEach(d => {
                            d.className = 'w-16 h-16 rounded-xl overflow-hidden shrink-0 border-2 border-slate-200 hover:border-slate-400 opacity-70 hover:opacity-100 cursor-pointer transition-all';
                        });
                        thumb.className = 'w-16 h-16 rounded-xl overflow-hidden shrink-0 border-2 border-amber-500 scale-105 shadow-sm ring-2 ring-amber-300 cursor-pointer transition-all';
                        mainImg.src = imgObj.url;
                        viewOrigin.href = imgObj.url;
                        document.getElementById('vm-main-label').textContent = imgObj.label + ' (' + (i + 1) + '/' + allImages.length + ')';
                    };
                    galleryStrip.appendChild(thumb);
                });
            } else {
                galleryStrip.innerHTML = '<span class="text-xs text-slate-400 italic py-2">Quán chưa đăng tải thêm album hình ảnh phụ.</span>';
            }

            // Detail fields
            document.getElementById('vm-detail-name').textContent = v.ten_quan || 'Chưa cập nhật';
            document.getElementById('vm-detail-type').textContent = v.loai_hinh || 'Địa điểm ẩm thực';
            document.getElementById('vm-detail-address').textContent = (v.dia_chi || '') + (v.tinh_thanh ? ' (' + v.tinh_thanh + ')' : '');
            document.getElementById('vm-detail-owner').textContent = v.owner_name || (v.nguoi_dang ? v.nguoi_dang.ho_ten : 'Chưa xác định');
            
            const email = v.owner_email || (v.nguoi_dang ? v.nguoi_dang.email : '');
            const phone = v.owner_phone || (v.nguoi_dang ? v.nguoi_dang.so_dien_thoai : '');
            document.getElementById('vm-detail-contact').innerHTML = 
                (email ? `<div><a href="mailto:${email}" class="hover:text-primary underline">📧 ${email}</a></div>` : '') + 
                (phone ? `<div><a href="tel:${phone}" class="hover:text-primary font-bold">📞 ${phone}</a></div>` : '');

            const hours = (v.gio_mo_cua && v.gio_dong_cua) ? `${v.gio_mo_cua} - ${v.gio_dong_cua}` : 'Chưa thiết lập giờ';
            const price = (v.gia_nho_nhat && v.gia_lon_nhat) ? `${Number(v.gia_nho_nhat).toLocaleString()}đ - ${Number(v.gia_nho_nhat).toLocaleString()}đ` : 'Chưa cập nhật giá';
            document.getElementById('vm-detail-hours-price').textContent = `⏰ ${hours} • 💰 ${price}`;
            document.getElementById('vm-detail-date').textContent = v.created_at || (v.created_at_formatted || 'Gần đây');

            // Contextual Action Buttons based on status
            const actionsContainer = document.getElementById('vm-actions');
            const safeName = (v.ten_quan || '').replace(/'/g, "\\'");
            
            if (v.trang_thai === 'da_duyet') {
                actionsContainer.innerHTML = `
                    <a href="/admin/quan/${v.id}/edit" class="btn-enterprise btn-enterprise-dark">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        <span class="whitespace-nowrap">Quản Lý Hồ Sơ</span>
                    </a>
                `;
            } else if (v.trang_thai === 'bi_khoa') {
                actionsContainer.innerHTML = `
                    <button type="button" onclick="closeVenueMediaModal(); confirmReapproveQuan('${v.id}', '${safeName}')" class="btn-enterprise btn-enterprise-success">
                        <span class="material-symbols-outlined text-[16px]">published_with_changes</span>
                        <span class="whitespace-nowrap uppercase tracking-wider">Duyệt Lại Quán Này</span>
                    </button>
                    <button type="button" onclick="closeVenueMediaModal(); confirmDeleteRejectedQuan('${v.id}', '${safeName}')" class="btn-enterprise btn-enterprise-danger">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        <span class="whitespace-nowrap">Xóa Hồ Sơ</span>
                    </button>
                `;
            } else {
                actionsContainer.innerHTML = `
                    <button type="button" onclick="closeVenueMediaModal(); confirmApproveQuan('${v.id}', '${safeName}')" class="btn-enterprise btn-enterprise-success">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        <span class="whitespace-nowrap uppercase tracking-wider">Phê Duyệt Quán Này</span>
                    </button>
                    <button type="button" onclick="closeVenueMediaModal(); confirmRejectQuan('${v.id}', '${safeName}')" class="btn-enterprise btn-enterprise-danger">
                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                        <span class="whitespace-nowrap">Không Duyệt Quán</span>
                    </button>
                `;
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            }, 10);
        }

        function closeVenueMediaModal() {
            const modal = document.getElementById('venue-media-modal');
            const box = document.getElementById('venue-media-box');
            if (!modal) return;
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }

        // Global Action Helpers for Approval / Rejection
        function confirmApproveQuan(id, name) {
            openConfirmModal({
                title: 'Xác Nhận Duyệt Quán Ăn',
                message: `Bạn có chắc chắn muốn phê duyệt quán <b class="text-slate-900 font-bold">"${name}"</b> không?<br><span class="text-slate-500 text-[11px]">Quán sẽ được cấp quyền kinh doanh và hiển thị công khai ngay lập tức.</span>`,
                icon: 'task_alt',
                iconBg: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                confirmText: 'Đồng Ý Duyệt',
                confirmClass: 'btn-enterprise-success',
                confirmIcon: 'check_circle',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/quan/${id}`, 'PUT', 'quan-row-' + id)
            });
        }

        function confirmRejectQuan(id, name) {
            openConfirmModal({
                title: 'Từ Chối Duyệt Quán Ăn',
                message: `Bạn có chắc chắn muốn từ chối hồ sơ quán <b class="text-slate-900 font-bold">"${name}"</b> không?<br><span class="text-rose-600 text-[11px]">Hồ sơ quán này sẽ chuyển sang trạng thái bị từ chối / khóa.</span>`,
                icon: 'cancel',
                iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
                confirmText: 'Không Duyệt Quán',
                confirmClass: 'btn-enterprise-danger',
                confirmIcon: 'block',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/quan/${id}/reject`, 'PUT', 'quan-row-' + id)
            });
        }

        function confirmVerifyUser(id, name) {
            openConfirmModal({
                title: 'Xác Nhận Xác Thực Tài Khoản',
                message: `Bạn có chắc chắn muốn xác thực tài khoản cho người dùng <b class="text-slate-900 font-bold">"${name}"</b> không?<br><span class="text-slate-500 text-[11px]">Tài khoản sẽ được gắn huy hiệu đã xác thực đầy đủ.</span>`,
                icon: 'verified',
                iconBg: 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                confirmText: 'Xác Thực Ngay',
                confirmClass: 'btn-enterprise-indigo',
                confirmIcon: 'verified',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/user/${id}`, 'PUT', 'user-row-' + id)
            });
        }

        function confirmRejectUser(id, name) {
            openConfirmModal({
                title: 'Từ Chối Xác Thực Người Dùng',
                message: `Bạn có chắc chắn không duyệt xác thực cho người dùng <b class="text-slate-900 font-bold">"${name}"</b> không?`,
                icon: 'person_off',
                iconBg: 'bg-slate-200 text-slate-800 border border-slate-300',
                confirmText: 'Không Duyệt',
                confirmClass: 'btn-enterprise-danger',
                confirmIcon: 'block',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/user/${id}/reject`, 'PUT', 'user-row-' + id)
            });
        }

        function confirmReapproveQuan(id, name) {
            openConfirmModal({
                title: 'Phê Duyệt Lại Quán Ăn',
                message: `Bạn có muốn duyệt lại hồ sơ quán <b class="text-slate-900 font-bold">"${name}"</b> không?<br><span class="text-slate-500 text-[11px]">Quán sẽ được chuyển sang trạng thái đã duyệt và hiển thị công khai trên hệ thống.</span>`,
                icon: 'published_with_changes',
                iconBg: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                confirmText: 'Duyệt Lại Quán',
                confirmClass: 'btn-enterprise-success',
                confirmIcon: 'check_circle',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/quan/${id}`, 'PUT', 'rejected-row-' + id)
            });
        }

        function confirmDeleteRejectedQuan(id, name) {
            openConfirmModal({
                title: 'Xóa Hồ Sơ Quán Ăn',
                message: `Bạn có chắc chắn muốn xóa quán <b class="text-slate-900 font-bold">"${name}"</b> khỏi danh sách từ chối?<br><span class="text-rose-600 text-[11px]">Hồ sơ quán này sẽ được chuyển vào thùng rác hệ thống.</span>`,
                icon: 'delete_forever',
                iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
                confirmText: 'Xác Nhận Xóa',
                confirmClass: 'btn-enterprise-danger',
                confirmIcon: 'delete',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/quan/${id}`, 'DELETE', 'rejected-row-' + id)
            });
        }

        // Global synchronization for stats counters
        function updateAllStatsCounters(stats) {
            if (!stats) return;
            const fields = [
                ['stat-count-pending', 'tab-badge-pending', 'pane-count-pending', stats.pending_quan],
                ['stat-count-rejected', 'tab-badge-rejected', 'pane-count-rejected', stats.rejected_quan],
                ['stat-count-approved', 'tab-badge-approved', 'pane-count-approved', stats.approved_quan],
                ['stat-count-users', 'tab-badge-users', 'pane-count-users', stats.unverified_users],
            ];
            fields.forEach(([statId, tabId, paneId, val]) => {
                if (val !== undefined && val !== null) {
                    const elStat = document.getElementById(statId);
                    const elTab = document.getElementById(tabId);
                    const elPane = document.getElementById(paneId);
                    if (elStat) elStat.textContent = val;
                    if (elTab) elTab.textContent = val;
                    if (elPane) elPane.textContent = val;
                }
            });

            // Update topbar badges & sidebar badges if any
            const totalPending = ((stats.pending_quan || 0) + (stats.unverified_users || 0));
            const badgeCount = document.getElementById('realtime-badge-count');
            const sidebarBadge = document.getElementById('sidebar-notif-badge');
            if (badgeCount) {
                if (totalPending > 0) {
                    badgeCount.textContent = totalPending;
                    badgeCount.classList.remove('hidden');
                } else {
                    badgeCount.classList.add('hidden');
                }
            }
            if (sidebarBadge) {
                if (totalPending > 0) {
                    sidebarBadge.textContent = totalPending;
                    sidebarBadge.classList.remove('hidden');
                } else {
                    sidebarBadge.classList.add('hidden');
                }
            }
        }

        function executeActionAjax(url, method, rowId) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const tr = document.getElementById(rowId);

            const formData = new FormData();
            formData.append('_token', csrf);
            formData.append('_method', method);

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Hoàn Tất', data.message || 'Thao tác thành công', window.location.href, 'check_circle', 'bg-emerald-500');
                    }

                    if (data.stats) {
                        updateAllStatsCounters(data.stats);
                    }

                    if (tr) {
                        const tbody = tr.closest('tbody');
                        tr.style.transition = 'all 0.35s ease';
                        tr.style.opacity = '0';
                        tr.style.transform = 'translateX(20px)';
                        setTimeout(() => {
                            tr.remove();
                            if (tbody && tbody.querySelectorAll('tr').length === 0) {
                                let emptyMsg = 'Không có dữ liệu trong danh sách.';
                                let colSpan = 5;
                                if (tbody.id === 'pending-quan-tbody') {
                                    emptyMsg = 'Tuyệt vời! Hiện không còn quán nào đang chờ duyệt.';
                                } else if (tbody.id === 'rejected-quan-tbody') {
                                    emptyMsg = 'Không có quán nào trong danh sách bị từ chối / không duyệt.';
                                } else if (tbody.id === 'approved-quan-tbody') {
                                    emptyMsg = 'Chưa có quán nào được duyệt gần đây.';
                                } else if (tbody.id === 'unverified-users-tbody') {
                                    emptyMsg = 'Tất cả tài khoản người dùng đều đã xác thực.';
                                    colSpan = 4;
                                }
                                tbody.innerHTML = `<tr><td colspan="${colSpan}" class="p-8 text-center text-xs text-slate-500 font-medium">${emptyMsg}</td></tr>`;
                            }
                            if (typeof updateDuyetCounts === 'function') {
                                updateDuyetCounts();
                            }
                        }, 350);
                    }

                    if (typeof checkRealtimeAdminNotifications === 'function') {
                        checkRealtimeAdminNotifications();
                    }
                } else {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Thao Tác Thất Bại', data.message || 'Có lỗi xảy ra, vui lòng thử lại.', '', 'error', 'bg-rose-500');
                    } else {
                        alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                    }
                }
            })
            .catch(() => {
                if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Lỗi Kết Nối', 'Lỗi kết nối máy chủ, vui lòng kiểm tra mạng và thử lại!', '', 'wifi_off', 'bg-rose-500');
                } else {
                    alert('Lỗi kết nối máy chủ, vui lòng kiểm tra mạng và thử lại!');
                }
            });
        }
        // Real-time Notification Engine
        let lastSeenQuanKey = null;
        let lastSeenBlogKey = null;
        let isNotifInit = false;
        let currentDropdownFilter = 'all';
        window.__allNotifications = [];
        window.__lastSeenTopNotifId = null;

        function toggleNotifDropdown() {
            const dropdown = document.getElementById('admin-notif-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', function(e) {
            const bell = document.getElementById('admin-notif-bell');
            const dropdown = document.getElementById('admin-notif-dropdown');
            if (bell && dropdown && !bell.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function filterDropdownNotifs(filterType) {
            currentDropdownFilter = filterType;
            document.querySelectorAll('#admin-notif-dropdown .notif-tab-btn').forEach(btn => {
                if (btn.getAttribute('data-filter') === filterType) {
                    btn.className = 'notif-tab-btn active px-2.5 py-1 rounded-lg font-bold bg-slate-900 text-white transition-colors';
                } else {
                    btn.className = 'notif-tab-btn px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:bg-slate-200 transition-colors';
                }
            });
            renderDropdownList(window.__allNotifications);
        }

        function renderDropdownList(notifications) {
            const list = document.getElementById('realtime-notif-list');
            if (!list) return;

            const notifs = notifications || [];
            const filtered = (currentDropdownFilter === 'all')
                ? notifs
                : notifs.filter(n => n.type === currentDropdownFilter);

            if (!filtered || filtered.length === 0) {
                list.innerHTML = `
                    <div class="p-6 text-center text-slate-400 font-medium text-xs">
                        <span class="material-symbols-outlined text-2xl text-slate-300 block mb-1">notifications_off</span>
                        Không có thông báo nào trong mục này.
                    </div>
                `;
                return;
            }

            list.innerHTML = filtered.map(item => `
                <a href="${item.url}" class="flex items-start gap-3 p-2.5 hover:bg-slate-50 rounded-xl transition-colors border-b border-slate-50 last:border-0 group">
                    <div class="w-8 h-8 rounded-lg ${item.badge_class || 'bg-slate-100 text-slate-700'} flex items-center justify-center flex-shrink-0 font-bold group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-[18px]">${item.icon || 'notifications'}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">${item.category_label}</span>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap">${item.time_ago || ''}</span>
                        </div>
                        <p class="text-[11px] font-bold text-slate-800 truncate group-hover:text-amber-700">${item.title}</p>
                        <p class="text-[10px] text-slate-500 truncate">${item.description || ''}</p>
                    </div>
                </a>
            `).join('');
        }

        // Web Audio Synthesizer Chime
        function playChimeSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            } catch (e) {
                // Audio context not allowed until interaction
            }
        }

        function showRealtimeToast(title, body, url, iconName = 'notifications_active', badgeBg = 'bg-amber-500') {
            const container = document.getElementById('realtime-toast-container');
            if (!container) return;

            playChimeSound();

            const toast = document.createElement('div');
            toast.className = 'bg-slate-900 text-white rounded-2xl p-4 shadow-2xl border border-slate-700/80 flex items-start gap-3 transform translate-x-full transition-all duration-300 ease-out cursor-pointer hover:border-amber-400';
            toast.onclick = () => window.location.href = url;
            toast.innerHTML = `
                <div class="w-9 h-9 rounded-xl ${badgeBg} text-slate-950 flex items-center justify-center flex-shrink-0 font-bold">
                    <span class="material-symbols-outlined text-[20px]">${iconName}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">🔔 Thông Báo Mới</span>
                        <span class="text-[10px] text-slate-400">Vừa xong</span>
                    </div>
                    <h4 class="text-xs font-bold text-white truncate mt-0.5">${title}</h4>
                    <p class="text-[11px] text-slate-300 mt-0.5 line-clamp-2 leading-tight">${body}</p>
                </div>
                <button onclick="event.stopPropagation(); this.parentElement.remove()" class="text-slate-400 hover:text-white p-1">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            `;

            container.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-x-full'), 50);

            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 8000);
        }

        function checkRealtimeAdminNotifications() {
            fetch('{{ route("admin.realtime-check") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) return;

                const badgeCount = document.getElementById('realtime-badge-count');
                const badgeDropdown = document.getElementById('realtime-badge-dropdown');
                const sidebarBadge = document.getElementById('sidebar-notif-badge');

                if (badgeCount) {
                    if (data.total_pending > 0) {
                        badgeCount.textContent = data.total_pending;
                        badgeCount.classList.remove('hidden');
                    } else {
                        badgeCount.classList.add('hidden');
                    }
                }

                if (badgeDropdown) {
                    badgeDropdown.textContent = data.total_pending + ' mới';
                }

                if (sidebarBadge) {
                    if (data.total_pending > 0) {
                        sidebarBadge.textContent = data.total_pending;
                        sidebarBadge.classList.remove('hidden');
                    } else {
                        sidebarBadge.classList.add('hidden');
                    }
                }

                // Render toàn bộ thông báo vào dropdown bell
                window.__allNotifications = data.notifications || [];
                renderDropdownList(window.__allNotifications);

                // Phát tín hiệu cập nhật toàn bộ hệ thống cho các trang con (Duyệt xác thực, Thông báo...)
                window.dispatchEvent(new CustomEvent('admin:notifications-updated', { detail: data }));

                // Toast & chime notification nếu có item mới xuất hiện
                if (data.notifications && data.notifications.length > 0) {
                    const topNotif = data.notifications[0];
                    if (isNotifInit && window.__lastSeenTopNotifId && window.__lastSeenTopNotifId !== topNotif.id) {
                        showRealtimeToast(
                            topNotif.category_label + ': ' + topNotif.title,
                            topNotif.description || 'Có thông báo mới cần xử lý',
                            topNotif.url,
                            topNotif.icon || 'notifications_active',
                            topNotif.type === 'quan' ? 'bg-amber-400' : (topNotif.type === 'blog' ? 'bg-blue-400' : (topNotif.type === 'user' ? 'bg-indigo-400' : 'bg-emerald-400'))
                        );
                    }
                    window.__lastSeenTopNotifId = topNotif.id;
                }

                // Nếu admin đang ở trang Duyệt Quán, chèn quán mới trực tiếp vào DOM không reload
                const tbody = document.getElementById('pending-quan-tbody');
                if (tbody && data.latest_quan && !document.getElementById('quan-row-' + data.latest_quan.id)) {
                    const emptyRow = document.getElementById('pending-quan-empty-row');
                    if (emptyRow) emptyRow.remove();

                    const newRow = document.createElement('tr');
                    newRow.id = 'quan-row-' + data.latest_quan.id;
                    newRow.className = 'duyet-table-row bg-amber-100/90 transition-all duration-1000 animate-pulse';
                    
                    const qData = data.latest_quan;
                    window.__venueMediaData = window.__venueMediaData || {};
                    window.__venueMediaData[qData.id] = qData;

                    const ownerName = qData.owner_name || (qData.nguoi_dang ? qData.nguoi_dang.ho_ten : 'Chưa xác định');
                    const ownerEmail = qData.owner_email || (qData.nguoi_dang ? qData.nguoi_dang.email : 'Chưa có Email');
                    const ownerPhone = qData.owner_phone || (qData.nguoi_dang ? (qData.nguoi_dang.so_dien_thoai || 'Chưa có SĐT') : '');
                    const quanAddr = qData.dia_chi || 'Chưa cập nhật';
                    const anhBia = qData.anh_bia_url || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&auto=format&fit=crop&q=80';
                    const safeName = (qData.ten_quan || '').replace(/'/g, "\\'");
                    const galleryCount = (qData.gallery && qData.gallery.length > 0) ? (qData.gallery.length + 1) : 1;
                    const loaiHinh = (qData.loai_hinh_kinh_doanh || qData.loai_hinh) 
                        ? `<span class="text-[11px] text-slate-500 font-medium block mt-0.5">🏷️ ${qData.loai_hinh_kinh_doanh || qData.loai_hinh}</span>` 
                        : '';

                    newRow.setAttribute('data-search', `${qData.ten_quan} ${ownerName} ${quanAddr} ${qData.loai_hinh_kinh_doanh || ''}`.toLowerCase());

                    newRow.innerHTML = `
                        <td class="p-3.5 font-medium min-w-[320px] max-w-[360px] overflow-hidden">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div onclick="openVenueMediaModal(window.__venueMediaData['${qData.id}'])" class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200/90 cursor-pointer group relative shadow-2xs hover:ring-2 hover:ring-amber-500 hover:border-amber-400 transition-all" title="Bấm để xem ảnh và thẩm định chi tiết">
                                    <img src="${anhBia}" alt="${qData.ten_quan}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&auto=format&fit=crop&q=80';">
                                    <div class="absolute inset-0 bg-black/35 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white">
                                        <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                                    </div>
                                    <span class="absolute bottom-1 right-1 bg-black/70 backdrop-blur-xs text-white text-[9px] font-bold px-1.5 py-0.2 rounded-md">
                                        ${galleryCount}
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 overflow-hidden space-y-1">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping shrink-0"></span>
                                        <span class="font-bold text-slate-900 text-sm truncate max-w-[170px]" title="${safeName}">
                                            ${qData.ten_quan}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300 shrink-0 whitespace-nowrap">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Chờ duyệt</span>
                                        </span>
                                    </div>
                                    ${loaiHinh ? `<div class="mt-0.5"><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200 max-w-full truncate" title="${qData.loai_hinh_kinh_doanh || qData.loai_hinh}">🏷️ ${qData.loai_hinh_kinh_doanh || qData.loai_hinh}</span></div>` : ''}
                                    <div class="flex items-center gap-1.5 pt-0.5 flex-wrap">
                                        <button type="button" onclick="openVenueMediaModal(window.__venueMediaData['${qData.id}'])" class="btn-enterprise btn-enterprise-photo" title="Xem bộ sưu tập hình ảnh quán">
                                            <span class="material-symbols-outlined text-[15px]">photo_library</span>
                                            <span>Xem ảnh</span>
                                            <span class="btn-enterprise-photo-count">${galleryCount}</span>
                                        </button>
                                        ${qData.slug ? `
                                            <a href="/quan/${qData.slug}" class="btn-enterprise btn-enterprise-secondary" title="Xem trang quán thực tế ngoài website">
                                                <span>Trang quán</span>
                                                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                            </a>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3.5 min-w-[180px] max-w-[210px] overflow-hidden">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    ${ownerName ? ownerName.charAt(0).toUpperCase() : 'U'}
                                </div>
                                <div class="min-w-0 flex-1 space-y-0.5 overflow-hidden">
                                    <div class="font-bold text-slate-900 truncate text-xs block" title="${ownerName}">${ownerName}</div>
                                    <div class="text-[11px] text-slate-600 font-semibold truncate flex items-center gap-0.5" title="${ownerPhone}">
                                        <span class="material-symbols-outlined text-[12px] text-slate-400 shrink-0">call</span>
                                        <span class="truncate">${ownerPhone}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 truncate font-mono block" title="${ownerEmail}">📧 ${ownerEmail}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3.5 text-slate-600 min-w-[180px] max-w-[240px] overflow-hidden">
                            <div class="space-y-1 min-w-0">
                                <div class="font-bold text-slate-800 text-xs leading-snug line-clamp-2 break-words" title="${quanAddr}">
                                    ${quanAddr || 'Chưa cập nhật chi tiết'}
                                </div>
                                ${qData.ten_tinh_thanh ? `
                                    <div class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-md max-w-full overflow-hidden" title="${qData.ten_tinh_thanh}">
                                        <span class="material-symbols-outlined text-[13px] text-slate-500 shrink-0">location_on</span>
                                        <span class="truncate">${qData.ten_tinh_thanh}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </td>
                        <td class="p-3.5 text-emerald-600 font-bold whitespace-nowrap min-w-[110px] w-[110px]">
                            <div class="whitespace-nowrap font-bold text-xs">Vừa xong</div>
                            <div class="text-[10px] text-slate-400 font-normal whitespace-nowrap">Mới đăng</div>
                        </td>
                        <td class="p-3.5 text-right whitespace-nowrap min-w-[210px] w-[210px]">
                            <div class="flex items-center justify-end gap-2 flex-nowrap shrink-0">
                                <button type="button" onclick="confirmApproveQuan('${qData.id}', '${safeName}')" class="btn-enterprise btn-enterprise-success">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                    <span class="uppercase tracking-wider">Duyệt</span>
                                </button>
                                <button type="button" onclick="confirmRejectQuan('${qData.id}', '${safeName}')" class="btn-enterprise btn-enterprise-danger">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                    <span>Không duyệt</span>
                                </button>
                            </div>
                        </td>
                    `;

                    tbody.insertBefore(newRow, tbody.firstChild);

                    if (data.pending_quan !== undefined && typeof updateAllStatsCounters === 'function') {
                        updateAllStatsCounters({
                            pending_quan: data.pending_quan,
                            unverified_users: data.unverified_user
                        });
                    }

                    setTimeout(() => {
                        newRow.classList.remove('bg-amber-100/90', 'animate-pulse');
                        newRow.classList.add('hover:bg-slate-50/80');
                    }, 5000);
                }

                // Nếu admin đang ở trang Duyệt Xác Thực Người Dùng, chèn trực tiếp không reload
                const unverifiedTbody = document.getElementById('unverified-users-tbody');
                if (unverifiedTbody && data.unverified_users && data.unverified_users.length > 0) {
                    data.unverified_users.forEach(u => {
                        if (!document.getElementById('user-row-' + u.id)) {
                            const emptyRow = document.getElementById('unverified-users-empty-row');
                            if (emptyRow) emptyRow.remove();

                            const uRow = document.createElement('tr');
                            uRow.id = 'user-row-' + u.id;
                            uRow.className = 'bg-indigo-50/90 transition-all duration-1000 animate-pulse';
                            uRow.innerHTML = `
                                <td class="p-3 font-bold text-slate-900">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-ping"></span>
                                        ${u.ho_ten}
                                    </div>
                                </td>
                                <td class="p-3 text-slate-600">${u.email}</td>
                                <td class="p-3 text-slate-600">${u.so_dien_thoai || 'N/A'}</td>
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" onclick="confirmVerifyUser('${u.id}', '${(u.ho_ten || '').replace(/'/g, "\\'")}')" class="btn-enterprise btn-enterprise-indigo">
                                            <span class="material-symbols-outlined text-[16px]">verified</span>
                                            <span>Xác thực</span>
                                        </button>
                                        <button type="button" onclick="confirmRejectUser('${u.id}', '${(u.ho_ten || '').replace(/'/g, "\\'")}')" class="btn-enterprise btn-enterprise-danger">
                                            <span class="material-symbols-outlined text-[16px]">block</span>
                                            <span>Không duyệt</span>
                                        </button>
                                    </div>
                                </td>
                            `;
                            unverifiedTbody.insertBefore(uRow, unverifiedTbody.firstChild);
                            setTimeout(() => {
                                uRow.classList.remove('bg-indigo-50/90', 'animate-pulse');
                                uRow.classList.add('hover:bg-slate-50');
                            }, 5000);
                        }
                    });
                }

                // Update Dashboard KPI Cards Live
                const kpiTotalQuan = document.getElementById('kpi-total-quan');
                if (kpiTotalQuan && data.total_quan !== undefined) kpiTotalQuan.textContent = data.total_quan.toLocaleString();

                const kpiPendingQuan = document.getElementById('kpi-pending-quan');
                if (kpiPendingQuan && data.pending_quan !== undefined) kpiPendingQuan.textContent = data.pending_quan.toLocaleString();

                const kpiTotalUsers = document.getElementById('kpi-total-users');
                if (kpiTotalUsers && data.total_users !== undefined) kpiTotalUsers.textContent = data.total_users.toLocaleString();

                const kpiActiveUsers = document.getElementById('kpi-active-users');
                if (kpiActiveUsers && data.active_users !== undefined) kpiActiveUsers.textContent = data.active_users.toLocaleString() + ' người';

                // Inject Live User Row if standing on User Management screen
                const usersTbody = document.getElementById('users-tbody');
                if (usersTbody && data.latest_user && !document.getElementById('user-row-' + data.latest_user.id)) {
                    const uRow = document.createElement('tr');
                    uRow.id = 'user-row-' + data.latest_user.id;
                    uRow.className = 'hover:bg-gray-50/80 transition-colors bg-indigo-50/90 animate-pulse';
                    uRow.innerHTML = `
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-sm">
                                    ${(data.latest_user.ho_ten || 'U').substring(0, 1).toUpperCase()}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-ping"></span>
                                        ${data.latest_user.ho_ten}
                                    </p>
                                    <span class="text-xs text-gray-400">ID: ${data.latest_user.id.substring(0, 8)}...</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-medium text-gray-800">${data.latest_user.email}</p>
                            <p class="text-xs text-gray-400">${data.latest_user.so_dien_thoai || 'Chưa cập nhật SĐT'}</p>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">nguoi_dung</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Hoạt động</span>
                        </td>
                        <td class="py-3.5 px-4 text-center text-xs text-gray-400">0</td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="/admin/nguoi-dung/${data.latest_user.id}/edit" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-bold text-xs inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">edit</span> Chỉnh sửa
                            </a>
                        </td>
                    `;
                    usersTbody.insertBefore(uRow, usersTbody.firstChild);
                    setTimeout(() => uRow.classList.remove('bg-indigo-50/90', 'animate-pulse'), 5000);
                }

                isNotifInit = true;
            })
            .catch(() => {});
        }

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
                closeConfirmModal();
            }
        });

        document.getElementById('command-palette-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCommandPalette();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            updateSidebarActiveLinks(window.location.href);
            checkRealtimeAdminNotifications();
            setInterval(checkRealtimeAdminNotifications, 3000);
        });

        // ========================================================
        // INSTANT SPA TURBO NAVIGATION & HOVER PREFETCHING ENGINE
        // ========================================================
        const adminPageCache = new Map();
        let currentNavAbort = null;

        function prefetchUrl(url) {
            if (!url || adminPageCache.has(url)) return;
            try {
                const parsed = new URL(url, window.location.origin);
                if (parsed.origin !== window.location.origin || !parsed.pathname.startsWith('/admin')) return;

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => {
                        if (res.ok) return res.text();
                        return null;
                    })
                    .then(html => {
                        if (html) {
                            adminPageCache.set(url, html);
                            if (adminPageCache.size > 25) {
                                const firstKey = adminPageCache.keys().next().value;
                                adminPageCache.delete(firstKey);
                            }
                        }
                    })
                    .catch(() => {});
            } catch (e) {}
        }

        function instantNavigate(url, pushState = true) {
            const bar = document.getElementById('admin-top-progress-bar');
            if (bar) {
                bar.style.opacity = '1';
                bar.style.width = '35%';
            }

            if (currentNavAbort) {
                currentNavAbort.abort();
            }
            const controller = new AbortController();
            currentNavAbort = controller;

            const cachedHtml = adminPageCache.get(url);
            const fetchPromise = cachedHtml
                ? Promise.resolve(cachedHtml)
                : fetch(url, { signal: controller.signal, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => {
                        if (!res.ok) throw new Error('Network error');
                        return res.text();
                    })
                    .then(html => {
                        adminPageCache.set(url, html);
                        return html;
                    });

            if (bar) bar.style.width = '70%';

            fetchPromise.then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newMain = doc.querySelector('#admin-main-content');
                const currentMain = document.querySelector('#admin-main-content');

                if (newMain && currentMain) {
                    document.title = doc.title;

                    const newHeaderTitle = doc.querySelector('#header-page-title');
                    const currentHeaderTitle = document.querySelector('#header-page-title');
                    if (newHeaderTitle && currentHeaderTitle) {
                        currentHeaderTitle.innerHTML = newHeaderTitle.innerHTML;
                    }

                    currentMain.style.opacity = '0';
                    currentMain.style.transition = 'opacity 0.08s ease';

                    setTimeout(() => {
                        currentMain.innerHTML = newMain.innerHTML;
                        currentMain.className = newMain.className;

                        // Execute scripts in page content
                        doc.querySelectorAll('#admin-main-content script, main script').forEach(s => {
                            const newScript = document.createElement('script');
                            Array.from(s.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                            newScript.textContent = s.textContent;
                            document.body.appendChild(newScript);
                            newScript.remove();
                        });

                        // Re-run push scripts if any
                        doc.querySelectorAll('body > script:not([src])').forEach(s => {
                            if (!s.textContent.includes('Real-time Notification Engine') && 
                                !s.textContent.includes('INSTANT SPA TURBO NAVIGATION') &&
                                !s.textContent.includes('adminPageCache')) {
                                const newScript = document.createElement('script');
                                newScript.textContent = s.textContent;
                                document.body.appendChild(newScript);
                                newScript.remove();
                            }
                        });

                        currentMain.style.opacity = '1';
                        window.scrollTo({ top: 0, behavior: 'instant' });

                        updateSidebarActiveLinks(url);

                        if (pushState) {
                            window.history.pushState({ url }, '', url);
                        }

                        // Close mobile sidebar if open
                        const mobileSidebar = document.getElementById('admin-sidebar-mobile');
                        const backdrop = document.getElementById('mobile-sidebar-backdrop');
                        if (mobileSidebar && !mobileSidebar.classList.contains('-translate-x-full')) {
                            mobileSidebar.classList.add('-translate-x-full');
                            backdrop?.classList.add('hidden');
                        }

                        if (bar) {
                            bar.style.width = '100%';
                            setTimeout(() => {
                                bar.style.opacity = '0';
                                bar.style.width = '0';
                            }, 200);
                        }

                        if (typeof checkRealtimeAdminNotifications === 'function') {
                            checkRealtimeAdminNotifications();
                        }
                    }, 80);
                } else {
                    window.location.href = url;
                }
            })
            .catch(err => {
                if (err.name !== 'AbortError') {
                    window.location.href = url;
                }
            });
        }

        function updateSidebarActiveLinks(currentUrl) {
            try {
                const parsedUrl = new URL(currentUrl, window.location.origin);
                const parsedPath = parsedUrl.pathname.replace(/\/+$/, '') || '/';
                const items = document.querySelectorAll('.sidebar-item');
                if (!items || items.length === 0) return;

                let bestPath = null;
                let bestMatchLen = -1;

                // Vòng 1: Tìm duy nhất 1 đường dẫn khớp chính xác nhất (ưu tiên khớp hoàn toàn, hoặc prefix có dấu /)
                items.forEach(a => {
                    const aHref = a.getAttribute('href');
                    if (!aHref || aHref.startsWith('#') || aHref.startsWith('javascript:')) return;
                    const aPath = new URL(a.href, window.location.origin).pathname.replace(/\/+$/, '') || '/';

                    let isMatch = false;
                    if (aPath === parsedPath) {
                        isMatch = true;
                    } else if (aPath !== '/admin' && aPath !== '/' && parsedPath.startsWith(aPath + '/')) {
                        isMatch = true;
                    }

                    if (isMatch && aPath.length > bestMatchLen) {
                        bestPath = aPath;
                        bestMatchLen = aPath.length;
                    }
                });

                // Vòng 2: Duyệt qua tất cả items, CHỈ TÔ VÀNG DUY NHẤT ITEM KHỚP, TẤT CẢ Ô CÒN LẠI XÓA VÀNG
                items.forEach(a => {
                    const aHref = a.getAttribute('href');
                    if (!aHref || aHref.startsWith('#') || aHref.startsWith('javascript:')) return;
                    const aPath = new URL(a.href, window.location.origin).pathname.replace(/\/+$/, '') || '/';

                    if (bestPath && aPath === bestPath) {
                        a.classList.remove('text-slate-300', 'hover:bg-slate-800/80', 'hover:text-white');
                        a.classList.add('bg-[#F59E0B]', 'text-white', 'font-bold', 'shadow-md');
                    } else {
                        a.classList.remove('bg-[#F59E0B]', 'text-white', 'font-bold', 'shadow-md');
                        a.classList.add('text-slate-300', 'hover:bg-slate-800/80', 'hover:text-white');
                    }
                });
            } catch (e) {}
        }

        // Intercept clicks on admin internal links for instant 0-delay load
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
            if (link.getAttribute('target') === '_blank' || link.hasAttribute('download')) return;

            try {
                const parsed = new URL(link.href, window.location.origin);
                if (parsed.origin !== window.location.origin || !parsed.pathname.startsWith('/admin')) return;

                if (parsed.pathname === window.location.pathname) {
                    e.preventDefault();
                    return;
                }

                e.preventDefault();

                // Phản hồi giao diện tức thì (0ms latency):
                // Nếu click vào mục menu sidebar, chuyển highlight vàng duy nhất ô đó ngay lập tức!
                if (link.classList.contains('sidebar-item') || link.closest('.sidebar-item')) {
                    updateSidebarActiveLinks(parsed.href);
                }

                instantNavigate(parsed.href, true);
            } catch (e) {}
        });

        // Hover prefetch: loads page into RAM when cursor approaches (0ms latency on click)
        document.addEventListener('mouseover', function(e) {
            const link = e.target.closest('a');
            if (!link) return;
            const href = link.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                prefetchUrl(link.href);
            }
        }, { passive: true });

        document.addEventListener('touchstart', function(e) {
            const link = e.target.closest('a');
            if (!link) return;
            const href = link.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                prefetchUrl(link.href);
            }
        }, { passive: true });

        // Handle Browser Back / Forward buttons instantly from cache
        window.addEventListener('popstate', function() {
            updateSidebarActiveLinks(window.location.href);
            instantNavigate(window.location.href, false);
        });
    </script>
    @stack('scripts')
</body>
</html>

