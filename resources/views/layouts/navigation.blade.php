@php
    $ownerNavUrl = $ownerNavUrl ?? route('chu-quan.dang-quan');
    $ownerNavLabel = $ownerNavLabel ?? 'Đăng quán';
    $isOwnerNav = $isOwnerNav ?? false;
@endphp

{{-- Desktop Header --}}
<header class="hidden md:block fixed top-0 left-0 right-0 w-full z-50 bg-white border-b border-gray-100 shadow-sm transition-all duration-300">
    <div class="w-full px-6 lg:px-10 h-18 py-3 flex justify-between items-center">
        <a href="/" class="flex items-center gap-2 group">
            <span class="material-symbols-outlined text-primary text-3xl group-hover:rotate-12 transition-transform duration-300" style="font-variation-settings: 'FILL' 1;">restaurant</span>
            <span class="font-display-lg text-[24px] text-primary tracking-tight font-extrabold transition-all duration-300 group-hover:opacity-95">Quán Mới</span>
        </a>

        <nav class="flex items-center gap-6 md:gap-7 lg:gap-8">
            <a class="nav-link py-2 text-[15px] font-semibold transition-all duration-200 {{ request()->is('/') ? 'active text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }}" href="/">
                Khám phá
            </a>
            <a class="nav-link py-2 text-[15px] font-semibold transition-all duration-200 text-on-surface-variant hover:text-primary" href="#">
                Video review
            </a>
            <a class="nav-link py-2 text-[15px] font-semibold transition-all duration-200 {{ request()->routeIs('quan-da-luu.index') ? 'active text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }} flex items-center gap-1" href="{{ route('quan-da-luu.index') }}">
                Đã lưu
            </a>
            <a class="nav-link py-2 text-[15px] font-semibold transition-all duration-200 {{ request()->routeIs('blog.*') ? 'active text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }}" href="{{ route('blog.index') }}">
                Blog
            </a>
            @auth
                <a class="nav-link py-2 text-[15px] font-semibold transition-all duration-200 {{ request()->is('tai-khoan*') ? 'active text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }}" href="{{ route('tai-khoan.index') }}">
                    Tài khoản
                </a>
            @endauth
        </nav>

        <div class="flex items-center gap-3">
            <a href="{{ $ownerNavUrl }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-gradient-to-r from-primary via-tertiary to-secondary text-white font-bold text-[13.5px] shadow-[0_4px_14px_rgba(160,65,0,0.25)] hover:shadow-[0_6px_20px_rgba(160,65,0,0.35)] hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200">
                <span class="material-symbols-outlined text-[18px]">{{ $ownerNavIcon ?? 'add_location_alt' }}</span>
                <span>{{ $ownerNavLabel }}</span>
            </a>

            @auth
                <button class="relative p-2.5 rounded-full hover:bg-primary/5 text-on-surface-variant hover:text-primary transition-all active:scale-90 group" title="Thông báo">
                    <span class="material-symbols-outlined text-[22px] group-hover:rotate-12 transition-transform">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full ring-2 ring-white animate-pulse"></span>
                </button>

                <div class="w-px h-6 bg-outline-variant/40 mx-1"></div>

                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-full hover:bg-primary/5 transition-all">
                        <img alt="Ảnh đại diện" class="w-8 h-8 rounded-full object-cover ring-2 ring-primary-fixed" src="{{ Auth::user()->anh_dai_dien ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->ho_ten) . '&background=ffdbcc&color=a04100&bold=true&size=128' }}"/>
                        <div class="flex flex-col text-left">
                            <div class="flex items-center gap-1">
                                <span class="font-bold text-[12px] text-on-surface leading-tight max-w-[100px] truncate">{{ Auth::user()->ho_ten }}</span>
                                @if(Auth::user()->da_xac_thuc)
                                    <span class="material-symbols-outlined text-[14px] text-tick-xanh" title="Đã xác thực" style="font-variation-settings: 'FILL' 1;">verified</span>
                                @endif
                            </div>
                            <span class="text-[10px] text-text-muted leading-tight">{{ Auth::user()->ten_vai_tro_hien_thi }}</span>
                        </div>
                    </div>
                    <button type="button" onclick="openLogoutModal()" class="p-2.5 rounded-full hover:bg-red-50 text-on-surface-variant hover:text-error transition-all active:scale-95" title="Đăng xuất">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </button>
                </div>
            @else
                <div class="relative group/auth-menu">
                    <button type="button" class="w-10 h-10 rounded-full bg-primary/10 hover:bg-primary/15 text-primary flex items-center justify-center transition-all active:scale-95 border border-primary/20 shadow-sm" title="Tài khoản">
                        <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;">person</span>
                    </button>

                    <div class="absolute right-0 top-full mt-2 w-64 bg-white rounded-2xl shadow-[0_12px_36px_rgba(0,0,0,0.12)] border border-gray-100 p-4 opacity-0 invisible group-hover/auth-menu:opacity-100 group-hover/auth-menu:visible group-focus-within/auth-menu:opacity-100 group-focus-within/auth-menu:visible transition-all duration-200 z-50">
                        <div class="text-center space-y-1 mb-3 pb-2.5 border-b border-gray-100">
                            <h4 class="font-bold text-[14px] text-gray-800">Tài khoản</h4>
                            <p class="text-[12px] text-gray-500">Bạn muốn đăng nhập hay đăng ký?</p>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('login') }}" class="w-full h-10 bg-primary text-white font-bold text-[13px] rounded-xl flex items-center justify-center gap-2 hover:bg-primary/90 transition-all active:scale-95 shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">login</span>
                                Đăng nhập
                            </a>
                            <a href="{{ route('register') }}" class="w-full h-10 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-[13px] rounded-xl flex items-center justify-center gap-2 transition-all active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">person_add</span>
                                Đăng ký tài khoản
                            </a>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>

{{-- Mobile Top App Bar --}}
<header class="flex md:hidden justify-between items-center px-4 h-14 w-full fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-100 shadow-sm">
    <button class="p-2 rounded-full hover:bg-surface-container active:scale-90 transition-all" aria-label="Menu">
        <span class="material-symbols-outlined text-on-surface-variant text-[22px]">menu</span>
    </button>
    <a href="/" class="flex items-center gap-1.5">
        <span class="material-symbols-outlined text-primary text-xl" style="font-variation-settings: 'FILL' 1;">restaurant</span>
        <span class="font-headline-lg-mobile text-headline-lg-mobile font-black text-primary tracking-tight">Quán Mới</span>
    </a>
    <div class="w-8"></div>
</header>

{{-- Mobile Bottom Navigation --}}
<nav class="fixed bottom-0 left-0 w-full z-50 md:hidden">
    <div class="h-4 bg-gradient-to-t from-surface/90 to-transparent pointer-events-none"></div>
    <div class="bg-white border-t border-gray-100 pb-safe h-[64px] flex justify-around items-center px-2 shadow-lg">
        <a href="/" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group">
            <div class="px-5 py-1 rounded-full transition-all duration-300 {{ request()->is('/') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high/50' }}">
                <span class="material-symbols-outlined text-[22px] block" style="font-variation-settings: 'FILL' {{ request()->is('/') ? '1' : '0' }};">explore</span>
            </div>
            <span class="text-[10px] font-bold mt-1 tracking-wide {{ request()->is('/') ? 'text-primary' : 'text-on-surface-variant' }}">Khám phá</span>
        </a>

        <a href="{{ $ownerNavUrl }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group">
            <div class="px-5 py-1 rounded-full transition-all duration-300 {{ request()->is('chu-quan/dang-quan') || request()->is('chu-quan/quan') || request()->is('chu-quan/quan/*') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-primary hover:bg-primary/5' }}">
                <span class="material-symbols-outlined text-[22px] block">{{ $ownerNavIcon ?? 'add_location_alt' }}</span>
            </div>
            <span class="text-[10px] font-bold mt-1 tracking-wide text-primary">{{ $ownerNavLabel }}</span>
        </a>

        <a href="{{ route('quan-da-luu.index') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group">
            <div class="px-5 py-1 rounded-full transition-all duration-300 {{ request()->routeIs('quan-da-luu.index') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high/50' }}">
                <span class="material-symbols-outlined text-[22px] block" style="font-variation-settings: 'FILL' {{ request()->routeIs('quan-da-luu.index') ? '1' : '0' }};">favorite</span>
            </div>
            <span class="text-[10px] font-bold mt-1 tracking-wide {{ request()->routeIs('quan-da-luu.index') ? 'text-primary' : 'text-on-surface-variant' }}">Đã lưu</span>
        </a>

        @auth
            <a href="{{ route('tai-khoan.index') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group">
                <div class="px-5 py-1 rounded-full transition-all duration-300 {{ request()->is('tai-khoan*') ? 'bg-primary-fixed text-on-primary-fixed' : 'hover:bg-surface-container-high/50' }} relative inline-block">
                    <img alt="Ảnh đại diện" class="w-[22px] h-[22px] rounded-full object-cover ring-2 ring-primary-fixed" src="{{ Auth::user()->anh_dai_dien ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->ho_ten) . '&background=ffdbcc&color=a04100&bold=true&size=64' }}"/>
                    @if(Auth::user()->da_xac_thuc)
                        <span class="absolute top-0 right-4 w-2.5 h-2.5 bg-tick-xanh rounded-full border border-white flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-[8px]" style="font-variation-settings: 'FILL' 1;">check</span>
                        </span>
                    @endif
                </div>
                <span class="text-[10px] font-bold mt-1 tracking-wide {{ request()->is('tai-khoan*') ? 'text-primary' : 'text-on-surface-variant' }} flex items-center gap-0.5">
                    Tài khoản
                </span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group">
                <div class="px-5 py-1 rounded-full transition-all duration-300 {{ (request()->is('dang-nhap') || request()->is('dang-ky') || request()->is('dangnhap') || request()->is('dangky')) ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high/50' }}">
                    <span class="material-symbols-outlined text-[22px] block" style="font-variation-settings: 'FILL' {{ (request()->is('dang-nhap') || request()->is('dang-ky') || request()->is('dangnhap') || request()->is('dangky')) ? '1' : '0' }};">person</span>
                </div>
                <span class="text-[10px] font-bold mt-1 tracking-wide {{ (request()->is('dang-nhap') || request()->is('dang-ky') || request()->is('dangnhap') || request()->is('dangky')) ? 'text-primary' : 'text-on-surface-variant' }}">Tài khoản</span>
            </a>
        @endauth
    </div>
</nav>
