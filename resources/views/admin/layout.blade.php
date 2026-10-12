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

                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-white border border-slate-200/90 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-300 transition-all shadow-2xs group" title="Quay về trang chủ website">
                    <span class="material-symbols-outlined text-[18px] text-amber-500 group-hover:scale-110 transition-transform">home</span>
                    <span class="font-bold">Quay về trang chủ</span>
                </a>

                {{-- Real-time Admin Notification Bell --}}
                <div class="relative">
                    <button id="admin-notif-bell" onclick="toggleNotifDropdown()" class="relative p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">notifications</span>
                        <span id="realtime-badge-count" class="hidden absolute -top-1 -right-1 min-w-[20px] h-[20px] px-1 bg-rose-500 text-white text-[11px] font-bold rounded-full flex items-center justify-center border-2 border-white shadow-xs">0</span>
                    </button>

                    {{-- Notification Dropdown Menu --}}
                    <div id="admin-notif-dropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden transform transition-all">
                        <div class="px-4 py-3 bg-slate-900 text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-amber-400">notifications_active</span>
                                <h3 class="text-xs font-bold uppercase tracking-wider">Thông báo duyệt tức thời</h3>
                            </div>
                            <span id="realtime-badge-dropdown" class="px-2 py-0.5 text-[10px] font-bold bg-amber-500 text-slate-950 rounded-full">0 mới</span>
                        </div>
                        <div id="realtime-notif-list" class="max-h-80 overflow-y-auto divide-y divide-slate-100 p-2 text-xs">
                            <div class="p-4 text-center text-slate-400 font-medium">Đang kiểm tra thông báo tức thời...</div>
                        </div>
                        <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center">
                            <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="text-[11px] font-bold text-amber-700 hover:text-amber-800 transition-colors">Xem tất cả yêu cầu chờ duyệt →</a>
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

    {{-- Floating Toast Notification Container --}}
    <div id="realtime-toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-sm pointer-events-auto"></div>

    <script>
        // Real-time Notification Engine
        let lastSeenQuanKey = null;
        let lastSeenBlogKey = null;
        let isNotifInit = false;

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
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">🔔 Thông Báo Tức Thời</span>
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
                const list = document.getElementById('realtime-notif-list');

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

                if (list) {
                    let itemsHtml = '';

                    if (data.latest_quan) {
                        const ownerInfo = data.latest_quan.nguoi_dang 
                            ? `${data.latest_quan.nguoi_dang.ho_ten} (${data.latest_quan.nguoi_dang.email})` 
                            : 'Chưa xác định';

                        itemsHtml += `
                            <a href="${data.latest_quan.url}" class="flex items-start gap-3 p-2.5 hover:bg-slate-50 rounded-xl transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0 font-bold">
                                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="font-bold text-slate-800 truncate text-xs">Cơ sở quán mới chờ duyệt</p>
                                        <span class="text-[10px] text-slate-400">${data.latest_quan.time_ago}</span>
                                    </div>
                                    <p class="text-[11px] font-semibold text-amber-700 truncate">${data.latest_quan.ten_quan}</p>
                                    <p class="text-[10px] text-slate-500 truncate">Chủ quán: ${ownerInfo}</p>
                                </div>
                            </a>
                        `;
                    }

                    if (data.latest_blog) {
                        itemsHtml += `
                            <a href="${data.latest_blog.url}" class="flex items-start gap-3 p-2.5 hover:bg-slate-50 rounded-xl transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0 font-bold">
                                    <span class="material-symbols-outlined text-[18px]">article</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="font-bold text-slate-800 truncate text-xs">Bài viết Blog mới gửi duyệt</p>
                                        <span class="text-[10px] text-slate-400">${data.latest_blog.time_ago}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-600 truncate">${data.latest_blog.title}</p>
                                </div>
                            </a>
                        `;
                    }

                    if (data.latest_user) {
                        itemsHtml += `
                            <a href="${data.latest_user.url}" class="flex items-start gap-3 p-2.5 hover:bg-slate-50 rounded-xl transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center flex-shrink-0 font-bold">
                                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="font-bold text-slate-800 truncate text-xs">Thành viên mới đăng ký</p>
                                    </div>
                                    <p class="text-[11px] text-slate-600 truncate">${data.latest_user.ho_ten} (${data.latest_user.email})</p>
                                </div>
                            </a>
                        `;
                    }

                    if (!itemsHtml) {
                        itemsHtml = `<div class="p-4 text-center text-slate-400 font-medium text-xs">Không có thông báo chờ duyệt nào.</div>`;
                    }

                    list.innerHTML = itemsHtml;
                }

                if (data.latest_quan) {
                    const currentQuanKey = data.latest_quan.id + '_' + data.latest_quan.created_at;
                    if (isNotifInit && lastSeenQuanKey && lastSeenQuanKey !== currentQuanKey) {
                        const ownerText = data.latest_quan.nguoi_dang 
                            ? `Người đăng: ${data.latest_quan.nguoi_dang.ho_ten} (${data.latest_quan.nguoi_dang.email})`
                            : '';
                        showRealtimeToast('Quán Ăn Mới Cần Duyệt Gấp!', `Quán "${data.latest_quan.ten_quan}" vừa gửi hồ sơ duyệt mới. ${ownerText}`, data.latest_quan.url, 'storefront', 'bg-amber-400');
                    }
                    lastSeenQuanKey = currentQuanKey;

                    // If Admin is currently standing on the approval screen, inject row directly into DOM live if not present!
                    const tbody = document.getElementById('pending-quan-tbody');
                    if (tbody && !document.getElementById('quan-row-' + data.latest_quan.id)) {
                        const emptyRow = document.getElementById('pending-quan-empty-row');
                        if (emptyRow) emptyRow.remove();

                        const newRow = document.createElement('tr');
                        newRow.id = 'quan-row-' + data.latest_quan.id;
                        newRow.className = 'bg-amber-100/90 transition-all duration-1000 animate-pulse';
                        
                        const ownerName = data.latest_quan.nguoi_dang ? data.latest_quan.nguoi_dang.ho_ten : 'Chưa xác định';
                        const ownerEmail = data.latest_quan.nguoi_dang ? data.latest_quan.nguoi_dang.email : 'Chưa có Email';
                        const ownerPhone = data.latest_quan.nguoi_dang ? (data.latest_quan.nguoi_dang.so_dien_thoai || 'Chưa có SĐT') : '';
                        const quanAddr = data.latest_quan.dia_chi || 'Chưa cập nhật';
                        const loaiHinh = data.latest_quan.loai_hinh ? `<span class="text-[10px] text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded font-normal block w-max mt-0.5">${data.latest_quan.loai_hinh}</span>` : '';

                        newRow.innerHTML = `
                            <td class="p-3 font-bold text-slate-900">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                    ${data.latest_quan.ten_quan}
                                </div>
                                ${loaiHinh}
                            </td>
                            <td class="p-3 font-medium">
                                <span class="font-bold text-slate-900 block">${ownerName}</span>
                                <span class="text-[10px] text-slate-500 block">📧 ${ownerEmail}</span>
                                <span class="text-[10px] text-slate-500 block">📞 ${ownerPhone}</span>
                            </td>
                            <td class="p-3 text-slate-600">${quanAddr}</td>
                            <td class="p-3 text-emerald-600 font-bold">Vừa xong</td>
                            <td class="p-3 text-right">
                                <form action="/admin/duyet-xac-thuc/quan/${data.latest_quan.id}" method="POST" class="inline-block">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content || ''}">
                                    <input type="hidden" name="_method" value="PUT">
                                    <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                        Duyệt & Xác thực
                                    </button>
                                </form>
                            </td>
                        `;

                        tbody.insertBefore(newRow, tbody.firstChild);

                        setTimeout(() => {
                            newRow.classList.remove('bg-amber-100/90', 'animate-pulse');
                            newRow.classList.add('hover:bg-slate-50');
                        }, 5000);
                    }
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

                if (data.latest_blog) {
                    const currentBlogKey = data.latest_blog.id + '_' + data.latest_blog.created_at;
                    if (isNotifInit && lastSeenBlogKey && lastSeenBlogKey !== currentBlogKey) {
                        showRealtimeToast('Bài Viết Mới Gửi Duyệt!', `Bài viết "${data.latest_blog.title}" vừa được gửi đến Admin.`, data.latest_blog.url, 'article', 'bg-blue-400');
                    }
                    lastSeenBlogKey = currentBlogKey;
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
            }
        });

        document.getElementById('command-palette-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCommandPalette();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            checkRealtimeAdminNotifications();
            setInterval(checkRealtimeAdminNotifications, 3000);
        });
    </script>
    @stack('scripts')
</body>
</html>

