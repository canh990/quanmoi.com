<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản Trị Hệ Thống - Quán Mới')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Raleway:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body class="text-on-surface bg-gray-50 flex min-h-screen font-sans antialiased">

    {{-- Admin Sidebar --}}
    <aside class="w-64 bg-slate-900 text-white flex-shrink-0 flex flex-col justify-between p-4 hidden md:flex">
        <div>
            <div class="flex items-center gap-3 px-3 py-4 border-b border-slate-800 mb-6">
                <svg class="w-9 h-9" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M89.5 18C50.95 18 19.7 49.25 19.7 87.8C19.7 126.35 50.95 157.6 89.5 157.6C106.1 157.6 121.35 151.8 133.3 142.12L152.7 161.5L166.9 147.3L147.75 128.15C154.25 116.55 158 102.9 158 87.8C158 49.25 128.05 18 89.5 18ZM89.5 42.5C114.5 42.5 134.8 62.8 134.8 87.8C134.8 97.15 132 105.85 127.2 113.1L109.7 95.6H117V79H86.3V109.7H102.9V102.4L112.7 112.2C106.1 117.25 98.12 120.15 89.5 120.15C64.5 120.15 44.2 100 44.2 87.8C44.2 62.8 64.5 42.5 89.5 42.5Z" fill="#c97a3a" />
                    <path d="M63.8 101.5V84.8H73.4V101.5H63.8ZM80.2 101.5V70.2H89.8V101.5H80.2ZM96.6 101.5V58.3H106.2V101.5H96.6Z" fill="#ffffff" />
                    <path d="M61.2 72.7L77.5 60.1L88.1 67.4L106.8 48.6" stroke="#ffffff" stroke-width="5.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M99 48.6H106.8V56.4" stroke="#ffffff" stroke-width="5.4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div>
                    <h1 class="font-extrabold text-lg text-white tracking-tight" style="font-family: 'Raleway', sans-serif;">QuanMoi Admin</h1>
                    <p class="text-xs text-slate-400">Trang Quản Trị Hệ Thống</p>
                </div>
            </div>

            <nav class="space-y-1.5">
                <a href="{{ route('admin.nguoi-dung.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('admin.nguoi-dung.*') ? 'bg-primary text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                    Quản Lý Người Dùng
                </a>
                <a href="{{ route('admin.quan.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('admin.quan.*') && !request()->has('status') && !request()->has('is_noi_bat') ? 'bg-primary text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]">storefront</span>
                    Quản Lý Địa Điểm Quán
                </a>
                <a href="{{ route('admin.quan.index', ['status' => 'chua_duyet']) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('admin.quan.*') && request('status') === 'chua_duyet' ? 'bg-amber-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                    Quán Chờ Duyệt
                </a>
                <a href="{{ route('admin.quan.index', ['is_noi_bat' => 1]) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('admin.quan.*') && request('is_noi_bat') == 1 ? 'bg-yellow-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]">star</span>
                    Quản Lý Quán Nổi Bật
                </a>
                <a href="{{ route('admin.seo.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('admin.seo.*') ? 'bg-emerald-700 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                    Quản Lý SEO
                </a>
                
                <div class="pt-4 mt-4 border-t border-slate-800">
                    <p class="px-4 text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Blog & Tin tức</p>
                    <a href="{{ route('admin.blog.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('admin.blog.dashboard') ? 'bg-primary text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">dashboard</span>
                        Tổng Quan Blog
                    </a>
                    <a href="{{ route('admin.blog.posts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('admin.blog.posts.*') ? 'bg-primary text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">article</span>
                        Duyệt Bài Viết
                    </a>
                    <a href="{{ route('admin.blog.categories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('admin.blog.categories.*') ? 'bg-primary text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">category</span>
                        Danh Mục Blog
                    </a>
                    <a href="{{ route('admin.blog.tags.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('admin.blog.tags.*') ? 'bg-primary text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">tag</span>
                        Thẻ (Tags)
                    </a>
                </div>
                <a href="/" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                    Xem Trang Chủ Quán Mới
                </a>
            </nav>
        </div>

        {{-- Logged in user info --}}
        <div class="border-t border-slate-800 pt-4 px-3 flex items-center justify-between">
            <div class="flex items-center gap-3 truncate">
                <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center font-black">A</div>
                <div class="truncate">
                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->ho_ten ?? 'Admin' }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@quanmoi.com' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Đăng xuất" class="text-slate-400 hover:text-red-400 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content Area --}}
    <div class="flex-grow flex flex-col min-w-0">
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shadow-sm">
            <h2 class="text-xl font-black text-gray-800">@yield('page-title', 'Dashboard')</h2>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Quyền Admin Cao Cấp
                </span>
            </div>
        </header>

        {{-- Alerts --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-red-600">error</span>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <main class="p-6 flex-grow">
            @yield('content')
        </main>
    </div>

</body>
</html>
