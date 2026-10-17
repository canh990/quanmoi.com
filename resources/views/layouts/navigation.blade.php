@php
    $ownerNavUrl = $ownerNavUrl ?? route('chu-quan.dang-quan');
    $ownerNavLabel = $ownerNavLabel ?? 'Đăng quán';
    $isOwnerNav = $isOwnerNav ?? false;
@endphp
@push('styles')
<style>
    .material-symbols-filled { font-variation-settings: 'FILL' 1; }
</style>
@endpush

{{-- Desktop Header --}}
<header class="hidden md:block fixed top-0 left-0 right-0 w-full z-50 bg-white border-b border-gray-100 shadow-sm transition-all duration-300">
    <div class="w-full px-4 md:px-4 lg:px-10 h-18 py-3 flex justify-between items-center">
        <a href="/" class="flex items-center gap-1.5 group">
            <svg class="w-8 h-8 lg:w-10 lg:h-10 transition-transform duration-300 group-hover:scale-105 drop-shadow-sm" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M89.5 18C50.95 18 19.7 49.25 19.7 87.8C19.7 126.35 50.95 157.6 89.5 157.6C106.1 157.6 121.35 151.8 133.3 142.12L152.7 161.5L166.9 147.3L147.75 128.15C154.25 116.55 158 102.9 158 87.8C158 49.25 128.05 18 89.5 18ZM89.5 42.5C114.5 42.5 134.8 62.8 134.8 87.8C134.8 97.15 132 105.85 127.2 113.1L109.7 95.6H117V79H86.3V109.7H102.9V102.4L112.7 112.2C106.1 117.25 98.12 120.15 89.5 120.15C64.5 120.15 44.2 100 44.2 87.8C44.2 62.8 64.5 42.5 89.5 42.5Z" fill="#c97a3a" />
                <path d="M63.8 101.5V84.8H73.4V101.5H63.8ZM80.2 101.5V70.2H89.8V101.5H80.2ZM96.6 101.5V58.3H106.2V101.5H96.6Z" fill="#1a1a1a" />
                <path d="M61.2 72.7L77.5 60.1L88.1 67.4L106.8 48.6" stroke="#1a1a1a" stroke-width="5.4" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M99 48.6H106.8V56.4" stroke="#1a1a1a" stroke-width="5.4" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="text-[20px] lg:text-[26px] tracking-tight font-extrabold transition-all duration-300 group-hover:opacity-90" style="font-family: 'Raleway', sans-serif; color: #1a1a1a; line-height: 0.88; letter-spacing: -0.05em;">QuanMoi</span>
        </a>

        <nav class="flex items-center gap-3 md:gap-4 lg:gap-8">
            <a class="nav-link py-2 text-[13px] lg:text-[15px] font-semibold transition-all duration-200 {{ request()->is('/') ? 'active text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }}" href="/">
                Khám phá
            </a>
            <a class="nav-link py-2 text-[13px] lg:text-[15px] font-semibold transition-all duration-200 text-on-surface-variant hover:text-primary" href="#">
                Video review
            </a>
            <a class="nav-link py-2 text-[13px] lg:text-[15px] font-semibold transition-all duration-200 {{ request()->routeIs('quan-da-luu.index') ? 'active text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }} flex items-center gap-1" href="{{ route('quan-da-luu.index') }}">
                Đã lưu
            </a>
            <a class="nav-link py-2 text-[13px] lg:text-[15px] font-semibold transition-all duration-200 {{ request()->routeIs('blog.*') ? 'active text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }}" href="{{ route('blog.index') }}">
                Blog
            </a>
            @auth
                <a class="nav-link py-2 text-[13px] lg:text-[15px] font-semibold transition-all duration-200 {{ request()->is('tai-khoan*') ? 'active text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }}" href="{{ route('tai-khoan.index') }}">
                    Tài khoản
                </a>
            @endauth
        </nav>

        <div class="flex items-center gap-2 lg:gap-3">
            <a href="{{ $ownerNavUrl }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-gradient-to-r from-primary via-tertiary to-secondary text-white font-bold text-[13.5px] shadow-[0_4px_14px_rgba(160,65,0,0.25)] hover:shadow-[0_6px_20px_rgba(160,65,0,0.35)] hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200">
                <span class="material-symbols-outlined text-[18px]">{{ $ownerNavIcon ?? 'add_location_alt' }}</span>
                <span>{{ $ownerNavLabel }}</span>
            </a>

            @auth
                {{-- Desktop Notification Button & Dropdown --}}
                <div class="relative" id="user-notif-wrapper">
                    <button id="user-notif-bell-btn" type="button" onclick="toggleUserNotifDropdown()" class="relative p-2.5 rounded-full hover:bg-primary/5 text-on-surface-variant hover:text-primary transition-all active:scale-90 group cursor-pointer" title="Thông báo của bạn" aria-expanded="false">
                        <span class="material-symbols-outlined text-[22px] group-hover:rotate-12 transition-transform">notifications</span>
                        <span id="user-notif-badge" class="hidden absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-red-600 text-white text-[10px] font-extrabold rounded-full ring-2 ring-white flex items-center justify-center animate-pulse shadow-sm">0</span>
                    </button>

                    {{-- Dropdown Container --}}
                    <div id="user-notif-dropdown" class="hidden absolute right-0 top-full mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-[0_16px_40px_rgba(0,0,0,0.12)] border border-gray-100 overflow-hidden z-50 transition-all duration-200">
                        {{-- Header --}}
                        <div class="p-3.5 bg-slate-50/80 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-gray-900">Thông báo</span>
                                <span id="user-notif-badge-pill" class="text-[11px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full">0 mới</span>
                            </div>
                            <button type="button" onclick="markAllUserNotifsRead()" class="text-[11px] font-semibold text-primary hover:text-primary-hover transition-colors cursor-pointer flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">done_all</span>
                                <span>Đọc tất cả</span>
                            </button>
                        </div>

                        {{-- Notification List --}}
                        <div id="user-notif-list" class="max-h-[380px] overflow-y-auto divide-y divide-gray-100 custom-scrollbar">
                            <div class="p-8 text-center text-gray-400 text-xs">
                                <span class="material-symbols-outlined text-3xl text-gray-300 block mb-2">notifications_none</span>
                                Đang tải thông báo...
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="p-2.5 bg-gray-50/80 border-t border-gray-100 text-center">
                            <a href="{{ route('nguoi-dung.blog.index') }}" class="text-xs font-bold text-gray-700 hover:text-primary transition-colors flex items-center justify-center gap-1">
                                <span>Quản lý bài viết của tôi</span>
                                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="w-px h-6 bg-outline-variant/40 mx-1"></div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('tai-khoan.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-full hover:bg-primary/5 transition-all cursor-pointer">
                        <img alt="Ảnh đại diện" class="w-8 h-8 rounded-full object-cover ring-2 ring-primary-fixed" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->ho_ten) }}&background=ffdbcc&color=a04100&bold=true&size=128';" src="{{ Auth::user()->anh_dai_dien ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->ho_ten) . '&background=ffdbcc&color=a04100&bold=true&size=128' }}"/>
                        <div class="flex flex-col text-left">
                            <div class="flex items-center gap-1">
                                <span class="font-bold text-[12px] text-on-surface leading-tight max-w-[100px] truncate">{{ Auth::user()->ho_ten }}</span>
                                @if(Auth::user()->da_xac_thuc)
                                    <span class="material-symbols-outlined text-[14px] text-tick-xanh" title="Đã xác thực" style="font-variation-settings: 'FILL' 1;">verified</span>
                                @endif
                            </div>
                            <span class="text-[10px] text-text-muted leading-tight">{{ Auth::user()->ten_vai_tro_hien_thi }}</span>
                        </div>
                    </a>
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
        <svg class="w-8 h-8 drop-shadow-sm" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M89.5 18C50.95 18 19.7 49.25 19.7 87.8C19.7 126.35 50.95 157.6 89.5 157.6C106.1 157.6 121.35 151.8 133.3 142.12L152.7 161.5L166.9 147.3L147.75 128.15C154.25 116.55 158 102.9 158 87.8C158 49.25 128.05 18 89.5 18ZM89.5 42.5C114.5 42.5 134.8 62.8 134.8 87.8C134.8 97.15 132 105.85 127.2 113.1L109.7 95.6H117V79H86.3V109.7H102.9V102.4L112.7 112.2C106.1 117.25 98.12 120.15 89.5 120.15C64.5 120.15 44.2 100 44.2 87.8C44.2 62.8 64.5 42.5 89.5 42.5Z" fill="#c97a3a" />
            <path d="M63.8 101.5V84.8H73.4V101.5H63.8ZM80.2 101.5V70.2H89.8V101.5H80.2ZM96.6 101.5V58.3H106.2V101.5H96.6Z" fill="#1a1a1a" />
            <path d="M61.2 72.7L77.5 60.1L88.1 67.4L106.8 48.6" stroke="#1a1a1a" stroke-width="5.4" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M99 48.6H106.8V56.4" stroke="#1a1a1a" stroke-width="5.4" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span class="text-[22px] tracking-tight font-extrabold" style="font-family: 'Raleway', sans-serif; color: #1a1a1a; line-height: 0.88; letter-spacing: -0.05em;">QuanMoi</span>
    </a>
    <div class="flex items-center gap-1">
        @auth
            <button id="user-mobile-notif-btn" type="button" onclick="toggleUserNotifDropdown()" class="relative p-2 rounded-full hover:bg-surface-container active:scale-90 transition-all text-on-surface-variant hover:text-primary cursor-pointer" title="Thông báo">
                <span class="material-symbols-outlined text-[22px]">notifications</span>
                <span id="user-mobile-notif-badge" class="hidden absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-red-600 text-white text-[9px] font-extrabold rounded-full ring-2 ring-white flex items-center justify-center animate-pulse">0</span>
            </button>
        @endauth
        <a href="{{ route('kham-pha') }}" class="p-2 rounded-full hover:bg-surface-container active:scale-90 transition-all text-on-surface-variant hover:text-primary" title="Tìm món ăn">
            <span class="material-symbols-outlined text-[22px]">search</span>
        </a>
    </div>
</header>

{{-- Mobile Bottom Navigation --}}
<nav class="fixed bottom-0 left-0 w-full z-50 md:hidden">
    <div class="h-4 bg-gradient-to-t from-surface/90 to-transparent pointer-events-none"></div>
    <div class="bg-white border-t border-gray-100 pb-safe h-[64px] flex justify-around items-center px-1 shadow-lg">
        <a href="/" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group min-w-0">
            <div class="px-4 py-1 rounded-full transition-all duration-300 {{ request()->is('/') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high/50' }}">
                <span class="material-symbols-outlined text-[22px] block {{ request()->is('/') ? 'material-symbols-filled' : '' }}">explore</span>
            </div>
            <span class="text-[10px] font-bold mt-1 tracking-wide {{ request()->is('/') ? 'text-primary' : 'text-on-surface-variant' }} truncate w-full px-1">Khám phá</span>
        </a>

        <a href="{{ $ownerNavUrl }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group min-w-0">
            <div class="px-4 py-1 rounded-full transition-all duration-300 {{ request()->is('chu-quan/dang-quan') || request()->is('chu-quan/quan') || request()->is('chu-quan/quan/*') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-primary hover:bg-primary/5' }}">
                <span class="material-symbols-outlined text-[22px] block">{{ $ownerNavIcon ?? 'add_location_alt' }}</span>
            </div>
            <span class="text-[10px] font-bold mt-1 tracking-wide text-primary truncate w-full px-1">{{ $ownerNavLabel === 'Quản lý cửa hàng' ? 'Cửa hàng' : $ownerNavLabel }}</span>
        </a>

        <a href="{{ route('quan-da-luu.index') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group min-w-0">
            <div class="px-4 py-1 rounded-full transition-all duration-300 {{ request()->routeIs('quan-da-luu.index') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high/50' }}">
                <span class="material-symbols-outlined text-[22px] block {{ request()->routeIs('quan-da-luu.index') ? 'material-symbols-filled' : '' }}">favorite</span>
            </div>
            <span class="text-[10px] font-bold mt-1 tracking-wide {{ request()->routeIs('quan-da-luu.index') ? 'text-primary' : 'text-on-surface-variant' }} truncate w-full px-1">Đã lưu</span>
        </a>

        @auth
            <a href="{{ route('tai-khoan.index') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group min-w-0">
                <div class="px-4 py-1 rounded-full transition-all duration-300 {{ request()->is('tai-khoan*') ? 'bg-primary-fixed text-on-primary-fixed' : 'hover:bg-surface-container-high/50' }} inline-block">
                    <div class="relative inline-flex">
                        <img alt="Ảnh đại diện" class="w-[22px] h-[22px] rounded-full object-cover ring-2 ring-primary-fixed" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->ho_ten) }}&background=ffdbcc&color=a04100&bold=true&size=64';" src="{{ Auth::user()->anh_dai_dien ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->ho_ten) . '&background=ffdbcc&color=a04100&bold=true&size=64' }}"/>
                        @if(Auth::user()->da_xac_thuc)
                            <span class="absolute -top-1 -right-1.5 w-2.5 h-2.5 bg-tick-xanh rounded-full border border-white flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-[8px]" style="font-variation-settings: 'FILL' 1;">check</span>
                            </span>
                        @endif
                    </div>
                </div>
                <span class="text-[10px] font-bold mt-1 tracking-wide {{ request()->is('tai-khoan*') ? 'text-primary' : 'text-on-surface-variant' }} truncate w-full px-1">
                    Tài khoản
                </span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group min-w-0">
                <div class="px-4 py-1 rounded-full transition-all duration-300 {{ (request()->is('dang-nhap') || request()->is('dang-ky') || request()->is('dangnhap') || request()->is('dangky')) ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high/50' }}">
                    <span class="material-symbols-outlined text-[22px] block {{ (request()->is('dang-nhap') || request()->is('dang-ky') || request()->is('dangnhap') || request()->is('dangky')) ? 'material-symbols-filled' : '' }}">person</span>
                </div>
                <span class="text-[10px] font-bold mt-1 tracking-wide {{ (request()->is('dang-nhap') || request()->is('dang-ky') || request()->is('dangnhap') || request()->is('dangky')) ? 'text-primary' : 'text-on-surface-variant' }} truncate w-full px-1">Tài khoản</span>
            </a>
        @endauth
    </div>
</nav>

{{-- Floating Vietnamese Toast Container for Users --}}
<div id="user-floating-toast-container" class="fixed top-20 right-4 sm:right-6 z-[9999] flex flex-col gap-3 max-w-sm w-[calc(100vw-32px)] sm:w-96 pointer-events-none"></div>

@auth
<script>
    (function() {
        let userNotifs = [];
        let isUserNotifLoaded = false;
        let lastSeenNotifId = null;
        let pollTimer = null;

        // Play gentle audio chime
        function playUserChime() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(523.25, ctx.currentTime); // C5
                osc.frequency.exponentialRampToValueAtTime(783.99, ctx.currentTime + 0.15); // G5
                gain.gain.setValueAtTime(0.12, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            } catch (e) {
                // Audio context requires user interaction first
            }
        }

        // Show floating toast in Vietnamese
        window.showUserToast = function(title, message, link = '', type = 'info', icon = 'notifications') {
            const container = document.getElementById('user-floating-toast-container');
            if (!container) return;

            playUserChime();

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto bg-white rounded-2xl p-4 shadow-[0_12px_36px_rgba(0,0,0,0.14)] border border-gray-100 flex items-start gap-3.5 transform translate-x-full transition-all duration-300 ease-out cursor-pointer hover:shadow-xl hover:border-primary/30';
            if (link) {
                toast.onclick = () => window.location.href = link;
            }

            const iconColors = {
                'success': 'bg-emerald-100 text-emerald-600',
                'error': 'bg-rose-100 text-rose-600',
                'warning': 'bg-amber-100 text-amber-600',
                'info': 'bg-primary/10 text-primary',
            };
            const iconBg = iconColors[type] || iconColors.info;

            toast.innerHTML = `
                <div class="w-10 h-10 rounded-xl ${iconBg} flex items-center justify-center shrink-0 font-bold">
                    <span class="material-symbols-outlined text-[22px]">${icon}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1 mb-0.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary">Thông báo mới</span>
                        <span class="text-[10px] text-gray-400">Vừa xong</span>
                    </div>
                    <h4 class="text-xs font-bold text-gray-900 truncate">${title}</h4>
                    <p class="text-[11px] text-gray-600 mt-0.5 line-clamp-2 leading-relaxed">${message}</p>
                </div>
                <button type="button" onclick="event.stopPropagation(); this.closest('div.pointer-events-auto').remove()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors" title="Đóng">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            `;

            container.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-x-full'), 30);

            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 350);
            }, 8000);
        };

        // Toggle dropdown
        window.toggleUserNotifDropdown = function() {
            const dropdown = document.getElementById('user-notif-dropdown');
            if (!dropdown) return;
            const isHidden = dropdown.classList.contains('hidden');
            if (isHidden) {
                dropdown.classList.remove('hidden');
                loadUserNotifications();
            } else {
                dropdown.classList.add('hidden');
            }
        };

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('user-notif-wrapper');
            const mobileBtn = document.getElementById('user-mobile-notif-btn');
            const dropdown = document.getElementById('user-notif-dropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                if (wrapper && !wrapper.contains(e.target) && (!mobileBtn || !mobileBtn.contains(e.target))) {
                    dropdown.classList.add('hidden');
                }
            }
        });

        // Update badge counts on desktop and mobile
        function updateBadgeCount(count) {
            const badge = document.getElementById('user-notif-badge');
            const mobileBadge = document.getElementById('user-mobile-notif-badge');
            const pill = document.getElementById('user-notif-badge-pill');

            if (count > 0) {
                const text = count > 99 ? '99+' : count;
                if (badge) {
                    badge.textContent = text;
                    badge.classList.remove('hidden');
                }
                if (mobileBadge) {
                    mobileBadge.textContent = text;
                    mobileBadge.classList.remove('hidden');
                }
                if (pill) {
                    pill.textContent = count + ' mới';
                }
            } else {
                if (badge) badge.classList.add('hidden');
                if (mobileBadge) mobileBadge.classList.add('hidden');
                if (pill) pill.textContent = '0 mới';
            }
        }

        // Render notification list inside dropdown
        function renderUserNotifList(items) {
            const listEl = document.getElementById('user-notif-list');
            if (!listEl) return;

            if (!items || items.length === 0) {
                listEl.innerHTML = `
                    <div class="py-10 px-4 text-center text-gray-400 text-xs">
                        <span class="material-symbols-outlined text-4xl text-gray-300 block mb-2">notifications_none</span>
                        <p class="font-medium text-gray-600">Bạn chưa có thông báo nào</p>
                        <p class="text-[11px] text-gray-400 mt-1">Các thông báo duyệt bài viết sẽ xuất hiện tại đây.</p>
                    </div>
                `;
                return;
            }

            listEl.innerHTML = items.map(item => {
                const isUnread = !item.read;
                const status = item.status || '';
                const action = item.action || '';

                let iconName = 'notifications';
                let iconClass = 'bg-primary/10 text-primary';
                let statusBadge = '';

                if (action === 'published' || status === 'published') {
                    iconName = 'check_circle';
                    iconClass = 'bg-emerald-100 text-emerald-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Đã xuất bản</span>';
                } else if (action === 'rejected' || status === 'rejected') {
                    iconName = 'cancel';
                    iconClass = 'bg-rose-100 text-rose-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Từ chối</span>';
                } else if (action === 'requested_revision' || status === 'need_revision') {
                    iconName = 'edit_note';
                    iconClass = 'bg-amber-100 text-amber-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Cần sửa</span>';
                } else if (action === 'hidden' || status === 'hidden') {
                    iconName = 'visibility_off';
                    iconClass = 'bg-slate-200 text-slate-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">Đã ẩn</span>';
                } else if (action === 'role_updated') {
                    iconName = 'admin_panel_settings';
                    iconClass = 'bg-purple-100 text-purple-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">Đổi vai trò</span>';
                } else if (action === 'verified') {
                    iconName = 'verified';
                    iconClass = 'bg-emerald-100 text-emerald-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Đã xác thực</span>';
                } else if (action === 'verification_rejected') {
                    iconName = 'gpp_bad';
                    iconClass = 'bg-rose-100 text-rose-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Từ chối xác thực</span>';
                } else if (action === 'permission_granted') {
                    iconName = 'lock_open';
                    iconClass = 'bg-teal-100 text-teal-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-700">Cấp quyền</span>';
                } else if (action === 'permission_denied') {
                    iconName = 'lock';
                    iconClass = 'bg-rose-100 text-rose-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Tước quyền</span>';
                } else if (action === 'permission_reset') {
                    iconName = 'refresh';
                    iconClass = 'bg-sky-100 text-sky-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-700">Quyền mặc định</span>';
                }

                return `
                    <div onclick="handleUserNotifClick('${item.id}', '${item.link || ''}')" class="p-3.5 hover:bg-gray-50/80 transition-colors cursor-pointer flex items-start gap-3 relative ${isUnread ? 'bg-amber-50/30' : ''}">
                        <div class="w-9 h-9 rounded-xl ${iconClass} flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">${iconName}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                ${statusBadge}
                                <span class="text-[10px] text-gray-400 whitespace-nowrap">${item.time_ago || ''}</span>
                            </div>
                            <h4 class="text-xs font-bold text-gray-900 leading-tight truncate mb-1" title="${item.title || ''}">${item.title || 'Thông báo bài viết'}</h4>
                            <p class="text-[11px] text-gray-600 leading-relaxed line-clamp-2">${item.message || ''}</p>
                            ${item.note ? `<div class="mt-1.5 p-2 rounded-lg bg-gray-100/80 text-[11px] text-gray-700 border-l-2 border-primary/50 italic line-clamp-2">"${item.note}"</div>` : ''}
                        </div>
                        ${isUnread ? '<span class="w-2 h-2 rounded-full bg-primary shrink-0 self-center"></span>' : ''}
                    </div>
                `;
            }).join('');
        }

        // Fetch user notifications list
        function loadUserNotifications() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch('{{ route("nguoi-dung.thong-bao.index") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    userNotifs = data.notifications || [];
                    updateBadgeCount(data.unread_count || 0);
                    renderUserNotifList(userNotifs);
                    isUserNotifLoaded = true;
                }
            })
            .catch(() => {});
        }

        // Handle notification click: mark as read then redirect
        window.handleUserNotifClick = function(id, link) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch(`/thanh-vien/thong-bao/${id}/danh-dau-da-doc`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    updateBadgeCount(data.unread_count || 0);
                }
                if (link) {
                    window.location.href = link;
                }
            })
            .catch(() => {
                if (link) window.location.href = link;
            });
        };

        // Mark all as read
        window.markAllUserNotifsRead = function() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch('{{ route("nguoi-dung.thong-bao.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    updateBadgeCount(0);
                    loadUserNotifications();
                }
            })
            .catch(() => {});
        };

        // Realtime check polling engine
        function checkUserRealtimeNotifications() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch('{{ route("nguoi-dung.thong-bao.realtime-check") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) return;

                updateBadgeCount(data.unread_count || 0);

                if (data.latest) {
                    const top = data.latest;
                    if (lastSeenNotifId !== null && lastSeenNotifId !== top.id && !top.read) {
                        // New notification arrived!
                        const action = top.action || '';
                        let toastType = 'info';
                        let toastIcon = 'notifications';
                        if (action === 'published' || action === 'approved' || action === 'verified' || action === 'permission_granted') {
                            toastType = 'success';
                            toastIcon = action === 'verified' ? 'verified' : (action === 'permission_granted' ? 'lock_open' : 'check_circle');
                        } else if (action === 'role_updated') {
                            toastType = 'info';
                            toastIcon = 'admin_panel_settings';
                        } else if (action === 'rejected' || action === 'verification_rejected' || action === 'permission_denied') {
                            toastType = 'error';
                            toastIcon = action === 'permission_denied' ? 'lock' : 'cancel';
                        } else if (action === 'requested_revision' || action === 'need_revision') {
                            toastType = 'warning';
                            toastIcon = 'edit_note';
                        } else if (action === 'permission_reset') {
                            toastType = 'info';
                            toastIcon = 'refresh';
                        }

                        window.showUserToast(
                            top.title || (top.quan_id ? 'Thông báo cơ sở ẩm thực' : 'Thông báo bài viết'),
                            top.message || 'Trạng thái dữ liệu của bạn vừa được cập nhật.',
                            top.link || '',
                            toastType,
                            toastIcon
                        );

                        // Broadcast event so other components can sync instantly
                        if (top.blog_id) {
                            window.dispatchEvent(new CustomEvent('quanmoi:blog-status-updated', { detail: top }));
                        }
                        if (top.quan_id) {
                            window.dispatchEvent(new CustomEvent('quanmoi:quan-status-updated', { detail: top }));
                        }
                        if (top.action && (top.action.startsWith('permission_') || top.action === 'role_updated' || top.action === 'verified' || top.action === 'verification_rejected')) {
                            window.dispatchEvent(new CustomEvent('quanmoi:role-permission-updated', { detail: top }));
                        }
                        if (top.quan_id) {
                            window.dispatchEvent(new CustomEvent('quanmoi:quan-status-updated', { detail: top }));
                        }
                        window.dispatchEvent(new CustomEvent('quanmoi:notification-received', { detail: top }));

                        // Refresh notification dropdown list if open
                        const dropdown = document.getElementById('user-notif-dropdown');
                        if (dropdown && !dropdown.classList.contains('hidden')) {
                            loadUserNotifications();
                        }
                    }
                    lastSeenNotifId = top.id;
                }
            })
            .catch(() => {});
        }

        // Initialize polling
        document.addEventListener('DOMContentLoaded', function() {
            // Initial count check
            checkUserRealtimeNotifications();

            // Periodic poll every 2.5 seconds for instant bi-directional sync
            pollTimer = setInterval(checkUserRealtimeNotifications, 2500);
        });
    })();
</script>
@endauth

