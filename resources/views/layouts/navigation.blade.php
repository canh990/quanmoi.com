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
<header class="hidden md:block fixed top-0 left-0 right-0 w-full z-50 bg-white border-b border-gray-100 shadow-sm transition-all duration-300" style="z-index: 9999;">
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
            <a class="nav-link py-2 text-[13px] lg:text-[15px] font-semibold transition-all duration-200 {{ request()->routeIs('ban-do') ? 'active text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }} flex items-center gap-1" href="{{ route('ban-do') }}">
                Bản đồ
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
                @php
                    $isAdmin = $navIsAdmin ?? (Auth::user()->isAdmin());
                    $navCount = $navUnreadCount ?? ($isAdmin ? (\App\Http\Controllers\Admin\ThongBaoAdminController::buildRealtimeNotificationData(Auth::user())['unread_count'] ?? 0) : Auth::user()->unreadNotifications()->count());
                    $navList = $navNotifications ?? ($isAdmin ? (\App\Http\Controllers\Admin\ThongBaoAdminController::buildRealtimeNotificationData(Auth::user())['all_notifications'] ?? []) : Auth::user()->notifications()->latest()->take(20)->get());
                @endphp
                {{-- Desktop Notification Button --}}
                <div class="relative" id="user-notif-wrapper">
                    <button id="user-notif-bell-btn" type="button" onclick="toggleUserNotifDropdown(event)" class="relative p-2.5 rounded-full hover:bg-primary/5 text-on-surface-variant hover:text-primary transition-all active:scale-90 group cursor-pointer" title="{{ $isAdmin ? 'Thông báo Quản trị' : 'Thông báo của bạn' }}" aria-expanded="false">
                        <span class="material-symbols-outlined text-[22px] group-hover:rotate-12 transition-transform">notifications</span>
                        <span id="user-notif-badge" class="{{ $navCount > 0 ? '' : 'hidden' }} absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-red-600 text-white text-[10px] font-extrabold rounded-full ring-2 ring-white flex items-center justify-center animate-pulse shadow-sm">{{ $navCount > 99 ? '99+' : $navCount }}</span>
                    </button>
                    @include('layouts.notification-dropdown', ['isMobile' => false, 'isAdmin' => $isAdmin, 'navUnreadCount' => $navCount, 'navNotifications' => $navList])
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
<header class="flex md:hidden justify-between items-center px-4 h-14 w-full fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-100 shadow-sm" style="z-index: 9999;">
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
            <div class="relative" id="user-mobile-notif-wrapper">
                <button id="user-mobile-notif-btn" type="button" onclick="toggleUserNotifDropdown(event)" class="relative p-2 rounded-full hover:bg-surface-container active:scale-90 transition-all text-on-surface-variant hover:text-primary cursor-pointer" title="{{ $isAdmin ? 'Thông báo Quản trị' : 'Thông báo' }}">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    <span id="user-mobile-notif-badge" class="{{ ($navCount ?? 0) > 0 ? '' : 'hidden' }} absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 bg-red-600 text-white text-[9px] font-extrabold rounded-full ring-2 ring-white flex items-center justify-center animate-pulse">{{ ($navCount ?? 0) > 99 ? '99+' : ($navCount ?? 0) }}</span>
                </button>
                @include('layouts.notification-dropdown', ['isMobile' => true, 'isAdmin' => $isAdmin, 'navUnreadCount' => $navCount, 'navNotifications' => $navList])
            </div>
        @endauth
        <a href="{{ route('ban-do') }}" class="p-2 rounded-full hover:bg-surface-container active:scale-90 transition-all text-on-surface-variant hover:text-primary" title="Bản đồ quán lân cận">
            <span class="material-symbols-outlined text-[22px]">map</span>
        </a>
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
                            <span class="absolute -top-1 -right-1.5 bg-white rounded-full flex items-center justify-center leading-none">
                                <span class="material-symbols-outlined text-tick-xanh text-[11px]" style="font-variation-settings: 'FILL' 1;">verified</span>
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
        const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        let currentNotifs = [];
        let isNotifLoaded = false;
        let lastSeenNotifId = null;
        let pollTimer = null;

        // Play gentle audio chime
        function playChime() {
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

            playChime();

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
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary">${isAdmin ? 'Thông báo Quản trị' : 'Thông báo mới'}</span>
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

        // Update badge counts on desktop and mobile
        function updateBadgeCount(count) {
            const badge = document.getElementById('user-notif-badge');
            const mobileBadge = document.getElementById('user-mobile-notif-badge');
            const pill = document.getElementById('user-notif-badge-pill');
            const mobilePill = document.getElementById('user-mobile-notif-badge-pill');

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
                if (mobilePill) {
                    mobilePill.textContent = count + ' mới';
                }
            } else {
                if (badge) badge.classList.add('hidden');
                if (mobileBadge) mobileBadge.classList.add('hidden');
                if (pill) pill.textContent = '0 mới';
                if (mobilePill) mobilePill.textContent = '0 mới';
            }
        }

        // Toggle dropdown
        window.toggleUserNotifDropdown = function(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const isMobile = window.innerWidth < 768;
            const targetDropdown = isMobile ? document.getElementById('user-mobile-notif-dropdown') : document.getElementById('user-notif-dropdown');
            const otherDropdown = isMobile ? document.getElementById('user-notif-dropdown') : document.getElementById('user-mobile-notif-dropdown');

            if (otherDropdown) otherDropdown.classList.add('hidden');
            if (!targetDropdown) return;

            const willBeVisible = targetDropdown.classList.contains('hidden');
            if (willBeVisible) {
                targetDropdown.classList.remove('hidden');
                if (!isNotifLoaded || !currentNotifs || currentNotifs.length === 0) {
                    loadAllNotifications();
                }
            } else {
                targetDropdown.classList.add('hidden');
            }
        };

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const desktopDropdown = document.getElementById('user-notif-dropdown');
            const mobileDropdown = document.getElementById('user-mobile-notif-dropdown');
            const desktopBell = document.getElementById('user-notif-bell-btn');
            const mobileBell = document.getElementById('user-mobile-notif-btn');
            const desktopWrapper = document.getElementById('user-notif-wrapper');
            const mobileWrapper = document.getElementById('user-mobile-notif-wrapper');

            if (desktopDropdown && !desktopDropdown.classList.contains('hidden')) {
                const isTrigger = (desktopBell && desktopBell.contains(e.target)) ||
                                  (desktopWrapper && desktopWrapper.contains(e.target));
                const isInside = desktopDropdown.contains(e.target);
                if (!isTrigger && !isInside) {
                    desktopDropdown.classList.add('hidden');
                }
            }

            if (mobileDropdown && !mobileDropdown.classList.contains('hidden')) {
                const isTrigger = (mobileBell && mobileBell.contains(e.target)) ||
                                  (mobileWrapper && mobileWrapper.contains(e.target));
                const isInside = mobileDropdown.contains(e.target);
                if (!isTrigger && !isInside) {
                    mobileDropdown.classList.add('hidden');
                }
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('user-notif-dropdown')?.classList.add('hidden');
                document.getElementById('user-mobile-notif-dropdown')?.classList.add('hidden');
            }
        });

        // Render notification list inside dropdown
        function renderDropdownContent(items) {
            const listEl = document.getElementById('user-notif-list');
            const mobileListEl = document.getElementById('user-mobile-notif-list');
            if (!listEl && !mobileListEl) return;

            if (!items || items.length === 0) {
                const emptyHtml = `
                    <div class="py-10 px-4 text-center text-gray-400 text-xs">
                        <span class="material-symbols-outlined text-4xl text-gray-300 block mb-2">notifications</span>
                        <p class="font-medium text-gray-600">${isAdmin ? 'Bạn chưa có thông báo quản trị nào' : 'Bạn chưa có thông báo nào'}</p>
                        <p class="text-[11px] text-gray-400 mt-1">${isAdmin ? 'Khi có quán, bài viết hoặc tài khoản cần duyệt, thông báo sẽ xuất hiện tại đây.' : 'Các thông báo duyệt quán, bài viết và đánh giá sẽ xuất hiện tại đây.'}</p>
                    </div>
                `;
                if (listEl) listEl.innerHTML = emptyHtml;
                if (mobileListEl) mobileListEl.innerHTML = emptyHtml;
                return;
            }

            if (isAdmin) {
                // Render Admin Items
                const html = items.map(item => {
                    const isUnread = !item.read;
                    const nId = item.id || '';
                    const nTitle = item.title || 'Thông báo hệ thống';
                    const nDesc = item.description || '';
                    const nUrl = item.url || '{{ route("admin.thong-bao.index") }}';
                    const nIcon = item.icon || 'notifications';
                    const nBadgeClass = item.badge_class || 'bg-amber-100 text-amber-700';
                    const nCatLabel = item.category_label || 'Hệ thống';
                    const nTimeAgo = item.time_ago || '';

                    return `
                        <div onclick="handleNotifClick('${nId}', '${nUrl}')" class="p-3.5 hover:bg-gray-50/80 transition-colors cursor-pointer flex items-start gap-3 relative ${isUnread ? 'bg-amber-50/30' : ''}">
                            <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                                <span class="material-symbols-outlined text-[18px]">${nIcon}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-1">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${nBadgeClass}">${nCatLabel}</span>
                                    <span class="text-[10px] text-gray-400 whitespace-nowrap">${nTimeAgo}</span>
                                </div>
                                <h4 class="text-xs ${isUnread ? 'font-black text-gray-900' : 'font-normal text-gray-600'} leading-tight truncate mb-1" title="${nTitle}">${nTitle}</h4>
                                <p class="text-[11px] ${isUnread ? 'text-gray-800 font-medium' : 'text-gray-500'} leading-relaxed line-clamp-2">${nDesc}</p>
                            </div>
                            ${isUnread ? '<span class="w-2.5 h-2.5 rounded-full bg-primary shrink-0 self-center ring-2 ring-white"></span>' : ''}
                        </div>
                    `;
                }).join('');

                if (listEl) listEl.innerHTML = html;
                if (mobileListEl) mobileListEl.innerHTML = html;
                return;
            }

            // Render User Items
            const html = items.map(item => {
                const isUnread = !item.read;
                const status = item.status || '';
                const action = item.action || '';

                let iconName = 'notifications';
                let iconClass = 'bg-primary/10 text-primary';
                let statusBadge = '';

                if (action === 'approved' || status === 'approved') {
                    iconName = 'check_circle';
                    iconClass = 'bg-emerald-100 text-emerald-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">' + (item.status_label || 'Đã duyệt') + '</span>';
                } else if (action === 'published' || status === 'published') {
                    iconName = 'check_circle';
                    iconClass = 'bg-emerald-100 text-emerald-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Đã xuất bản</span>';
                } else if (action === 'rejected' || status === 'rejected') {
                    iconName = 'cancel';
                    iconClass = 'bg-rose-100 text-rose-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">' + (item.status_label || 'Từ chối') + '</span>';
                } else if (action === 'submitted' || status === 'pending') {
                    iconName = 'schedule';
                    iconClass = 'bg-blue-100 text-blue-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">' + (item.status_label || 'Chờ duyệt') + '</span>';
                } else if (action === 'requested_revision' || status === 'need_revision') {
                    iconName = 'edit_note';
                    iconClass = 'bg-amber-100 text-amber-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Cần sửa</span>';
                } else if (action === 'hero') {
                    iconName = 'auto_awesome';
                    iconClass = 'bg-amber-100 text-amber-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Hero Blog</span>';
                } else if (action === 'featured_granted') {
                    iconName = 'hotel_class';
                    iconClass = 'bg-amber-100 text-amber-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Quán nổi bật</span>';
                } else if (action === 'verified_granted' || action === 'verified') {
                    iconName = 'verified';
                    iconClass = 'bg-emerald-100 text-emerald-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Đã xác thực</span>';
                } else if (action === 'verified_revoked' || action === 'verification_rejected') {
                    iconName = 'gpp_bad';
                    iconClass = 'bg-rose-100 text-rose-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Từ chối xác thực</span>';
                } else if (action === 'created') {
                    iconName = 'add_business';
                    iconClass = 'bg-indigo-100 text-indigo-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700">Khởi tạo</span>';
                } else if (action === 'updated') {
                    iconName = 'store';
                    iconClass = 'bg-amber-100 text-amber-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Cập nhật quán</span>';
                } else if (action === 'deleted' || action === 'force_deleted') {
                    iconName = 'delete_forever';
                    iconClass = 'bg-rose-100 text-rose-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">' + (item.status_label || 'Đã tạm xóa') + '</span>';
                } else if (action === 'restored') {
                    iconName = 'unarchive';
                    iconClass = 'bg-emerald-100 text-emerald-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Đã khôi phục</span>';
                } else if (action === 'new_review') {
                    iconName = 'rate_review';
                    iconClass = 'bg-amber-100 text-amber-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">' + (item.status_label || 'Đánh giá') + '</span>';
                } else if (action === 'broadcast') {
                    iconName = 'campaign';
                    iconClass = 'bg-purple-100 text-purple-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">Ban quản trị</span>';
                } else if (action === 'role_updated') {
                    iconName = 'admin_panel_settings';
                    iconClass = 'bg-purple-100 text-purple-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">Đổi vai trò</span>';
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
                } else if (action === 'status_updated') {
                    iconName = 'manage_accounts';
                    iconClass = 'bg-blue-100 text-blue-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">Đổi trạng thái</span>';
                } else if (action === 'review_reply') {
                    iconName = 'reply';
                    iconClass = 'bg-blue-100 text-blue-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">Đã phản hồi</span>';
                } else if (action === 'blog_comment') {
                    iconName = 'chat';
                    iconClass = 'bg-sky-100 text-sky-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-700">Bình luận</span>';
                } else if (action === 'comment_reply') {
                    iconName = 'quickreply';
                    iconClass = 'bg-teal-100 text-teal-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-700">Phản hồi</span>';
                } else if (action === 'menu_updated') {
                    iconName = 'restaurant_menu';
                    iconClass = 'bg-teal-100 text-teal-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-700">Thực đơn</span>';
                } else if (action === 'verification_requested') {
                    iconName = 'verified_user';
                    iconClass = 'bg-indigo-100 text-indigo-700';
                    statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700">Chờ xác thực</span>';
                }

                return `
                    <div onclick="handleNotifClick('${item.id}', '${item.link || ''}')" class="p-3.5 hover:bg-gray-50/80 transition-colors cursor-pointer flex items-start gap-3 relative ${isUnread ? 'bg-amber-50/30' : ''}">
                        <div class="w-9 h-9 rounded-xl ${iconClass} flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">${iconName}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                ${statusBadge}
                                <span class="text-[10px] text-gray-400 whitespace-nowrap">${item.time_ago || ''}</span>
                            </div>
                            <h4 class="text-xs ${isUnread ? 'font-black text-gray-900' : 'font-normal text-gray-600'} leading-tight truncate mb-1" title="${item.title || ''}">${item.title || 'Thông báo hệ thống'}</h4>
                            <p class="text-[11px] ${isUnread ? 'text-gray-800 font-medium' : 'text-gray-500'} leading-relaxed line-clamp-2">${item.message || ''}</p>
                            ${item.note ? `<div class="mt-1.5 p-2 rounded-lg bg-gray-100/80 text-[11px] text-gray-700 border-l-2 border-primary/50 italic line-clamp-2">"${item.note}"</div>` : ''}
                        </div>
                        ${isUnread ? '<span class="w-2.5 h-2.5 rounded-full bg-primary shrink-0 self-center ring-2 ring-white"></span>' : ''}
                    </div>
                `;
            }).join('');

            if (listEl) listEl.innerHTML = html;
            if (mobileListEl) mobileListEl.innerHTML = html;
        }

        // Fetch notifications list
        function loadAllNotifications() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || csrfToken;
            const url = isAdmin ? '{{ route("admin.realtime-check") }}' : '{{ route("nguoi-dung.thong-bao.index") }}';

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    currentNotifs = isAdmin ? (data.all_notifications || []) : (data.notifications || []);
                    let unreadCount = data.unread_count || 0;
                    if (window.__userJustMarkedAllReadAt && (Date.now() - window.__userJustMarkedAllReadAt < 4000)) {
                        unreadCount = 0;
                        currentNotifs.forEach(n => { n.read = true; });
                    }
                    updateBadgeCount(unreadCount);
                    renderDropdownContent(currentNotifs);
                    isNotifLoaded = true;
                }
            })
            .catch(() => {});
        }

        // Handle notification click: 1 click marks as read and redirects instantly
        window.handleNotifClick = function(id, link) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || csrfToken;

            // 1. Phản hồi giao diện tức thì (0ms Optimistic UI)
            const badge = document.getElementById('user-notif-badge');
            const mobileBadge = document.getElementById('user-mobile-notif-badge');
            let currentCount = parseInt(badge?.textContent || mobileBadge?.textContent || '0', 10);
            if (isNaN(currentCount)) currentCount = 0;
            if (currentCount > 0) {
                updateBadgeCount(Math.max(0, currentCount - 1));
            }

            // Đổi trạng thái thẻ nếu đang ở trang Trung tâm thông báo
            const card = document.getElementById(`notif-card-${id}`);
            if (card) {
                card.classList.remove('border-primary/40', 'bg-amber-50/20', 'bg-amber-50/25');
                card.classList.add('border-gray-100');
                const pulseBadge = card.querySelector('.animate-pulse, .animate-ping')?.closest('.inline-flex');
                if (pulseBadge) pulseBadge.remove();
                const markBtn = card.querySelector('button[onclick*="markSingleRead"]');
                if (markBtn) markBtn.remove();
            }

            const pageBadge = document.getElementById('page-unread-badge');
            if (pageBadge) {
                let pCount = parseInt(pageBadge.textContent || '0', 10);
                if (!isNaN(pCount) && pCount > 1) {
                    pageBadge.textContent = `${pCount - 1} chưa đọc`;
                } else {
                    pageBadge.remove();
                }
            }

            // 2. Gửi request lưu database ngầm không block bằng keepalive
            try {
                const markSingleUrl = isAdmin
                    ? `/admin/thong-bao/${encodeURIComponent(id)}/mark-read`
                    : `/thanh-vien/thong-bao/${encodeURIComponent(id)}/danh-dau-da-doc`;

                fetch(markSingleUrl, {
                    method: 'POST',
                    keepalive: true,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    }
                }).catch(() => {});
            } catch (e) {}

            // 3. Chuyển hướng ngay lập tức (1 lần nhấn duy nhất)
            if (link && link !== '#' && link !== 'javascript:void(0)') {
                const currentUrl = window.location.href.split('#')[0].split('?')[0];
                let isCurrentPage = false;
                try {
                    const parsedUrl = new URL(link, window.location.origin).href.split('#')[0].split('?')[0];
                    if (parsedUrl === currentUrl) isCurrentPage = true;
                } catch (e) {
                    if (link === window.location.pathname) isCurrentPage = true;
                }

                if (isCurrentPage && link.includes('#')) {
                    const hash = link.substring(link.indexOf('#'));
                    const targetEl = document.querySelector(hash);
                    if (targetEl) {
                        targetEl.scrollIntoView({ behavior: 'smooth' });
                        document.getElementById('user-notif-dropdown')?.classList.add('hidden');
                        document.getElementById('user-mobile-notif-dropdown')?.classList.add('hidden');
                        return;
                    }
                }
                window.location.href = link;
            }
        };

        // Aliases for global handlers
        window.handleUserNotifClick = window.handleNotifClick;
        window.handleAdminNotifClick = function(e, id, url) {
            if (e) { e.preventDefault(); }
            window.handleNotifClick(id, url);
        };

        // Mark all as read
        window.markAllNotifsRead = function() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || csrfToken;
            const markUrl = isAdmin ? '{{ route("admin.thong-bao.mark-all-read") }}' : '{{ route("nguoi-dung.thong-bao.mark-all-read") }}';

            updateBadgeCount(0);
            if (currentNotifs && Array.isArray(currentNotifs)) {
                currentNotifs.forEach(n => { n.read = true; });
                renderDropdownContent(currentNotifs);
            }

            fetch(markUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    updateBadgeCount(0);
                    loadAllNotifications();
                }
            })
            .catch(() => {});
        };

        window.markAllUserNotifsRead = window.markAllNotifsRead;
        window.markAllAdminNotifsReadDropdown = function(e) {
            if (e) e.preventDefault();
            window.markAllNotifsRead();
        };

        // Realtime check polling engine
        function checkRealtimeNotifications() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || csrfToken;

            if (isAdmin) {
                // Admin Real-time Polling
                fetch('{{ route("admin.realtime-check") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data || !data.success) return;

                    let unreadCount = data.unread_count || 0;
                    if (window.__userJustMarkedAllReadAt && (Date.now() - window.__userJustMarkedAllReadAt < 4000)) {
                        unreadCount = 0;
                    }
                    updateBadgeCount(unreadCount);

                    if (data.all_notifications && data.all_notifications.length > 0) {
                        const top = data.all_notifications[0];
                        if (lastSeenNotifId !== null && lastSeenNotifId !== top.id && !top.read) {
                            window.showUserToast(
                                top.title || 'Thông báo quản trị mới',
                                top.description || 'Có dữ liệu mới cần duyệt trong hệ thống.',
                                top.url || '{{ route("admin.thong-bao.index") }}',
                                'info',
                                top.icon || 'campaign'
                            );

                            window.dispatchEvent(new CustomEvent('admin:notifications-updated', { detail: data }));
                            window.dispatchEvent(new CustomEvent('quanmoi:notification-received', { detail: top }));

                            const desktopDropdown = document.getElementById('user-notif-dropdown');
                            const mobileDropdown = document.getElementById('user-mobile-notif-dropdown');
                            const isAnyOpen = (desktopDropdown && !desktopDropdown.classList.contains('hidden')) ||
                                              (mobileDropdown && !mobileDropdown.classList.contains('hidden'));
                            if (isAnyOpen) {
                                currentNotifs = data.all_notifications;
                                renderDropdownContent(currentNotifs);
                            }
                        }
                        lastSeenNotifId = top.id;
                    }
                })
                .catch(() => {});
                return;
            }

            // User / Restaurant Owner Real-time Polling
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

                let unreadCount = data.unread_count || 0;
                if (window.__userJustMarkedAllReadAt && (Date.now() - window.__userJustMarkedAllReadAt < 4000)) {
                    unreadCount = 0;
                }
                updateBadgeCount(unreadCount);

                if (data.latest) {
                    const top = data.latest;
                    if (lastSeenNotifId !== null && lastSeenNotifId !== top.id && !top.read) {
                        // New notification arrived!
                        const action = top.action || '';
                        let toastType = 'info';
                        let toastIcon = 'notifications';

                        if (['published', 'approved', 'verified', 'verified_granted', 'permission_granted', 'restored', 'featured_granted'].includes(action)) {
                            toastType = 'success';
                            toastIcon = action === 'featured_granted' ? 'hotel_class' : (['verified', 'verified_granted'].includes(action) ? 'verified' : (action === 'permission_granted' ? 'lock_open' : (action === 'restored' ? 'unarchive' : 'check_circle')));
                        } else if (['rejected', 'verification_rejected', 'permission_denied', 'deleted', 'force_deleted'].includes(action)) {
                            toastType = 'error';
                            toastIcon = action === 'permission_denied' ? 'lock' : (['deleted', 'force_deleted'].includes(action) ? 'delete_forever' : (action === 'verification_rejected' ? 'gpp_bad' : 'cancel'));
                        } else if (['requested_revision', 'need_revision', 'featured_revoked', 'verified_revoked'].includes(action)) {
                            toastType = 'warning';
                            toastIcon = action === 'featured_revoked' ? 'star' : 'edit_note';
                        } else if (action === 'new_review') {
                            toastType = 'info';
                            toastIcon = 'rate_review';
                        } else if (action === 'broadcast') {
                            toastType = 'info';
                            toastIcon = 'campaign';
                        } else if (action === 'role_updated') {
                            toastType = 'info';
                            toastIcon = 'admin_panel_settings';
                        } else if (action === 'submitted') {
                            toastType = 'info';
                            toastIcon = 'schedule';
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
                        if (top.action && (top.action.startsWith('permission_') || top.action === 'role_updated' || top.action === 'verified' || top.action === 'verification_rejected' || top.action === 'status_updated')) {
                            window.dispatchEvent(new CustomEvent('quanmoi:role-permission-updated', { detail: top }));
                        }
                        if (top.action === 'new_review') {
                            window.dispatchEvent(new CustomEvent('quanmoi:review-received', { detail: top }));
                        }
                        window.dispatchEvent(new CustomEvent('quanmoi:notification-received', { detail: top }));

                        // Refresh notification dropdown list if open
                        const desktopDropdown = document.getElementById('user-notif-dropdown');
                        const mobileDropdown = document.getElementById('user-mobile-notif-dropdown');
                        const isAnyOpen = (desktopDropdown && !desktopDropdown.classList.contains('hidden')) ||
                                          (mobileDropdown && !mobileDropdown.classList.contains('hidden'));
                        if (isAnyOpen) {
                            loadAllNotifications();
                        }
                    }
                    lastSeenNotifId = top.id;
                }
            })
            .catch(() => {});
        }

        // Initialize notifications immediately
        function initNotifications() {
            loadAllNotifications();
            checkRealtimeNotifications();
            if (!pollTimer) {
                pollTimer = setInterval(checkRealtimeNotifications, 2500);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initNotifications);
        } else {
            initNotifications();
        }
        document.addEventListener('turbo:load', initNotifications);

        // Preload when hovering over bell button
        const dBtn = document.getElementById('user-notif-bell-btn');
        if (dBtn) dBtn.addEventListener('mouseenter', () => { if (!isNotifLoaded) loadAllNotifications(); });
        const mBtn = document.getElementById('user-mobile-notif-btn');
        if (mBtn) mBtn.addEventListener('mouseenter', () => { if (!isNotifLoaded) loadAllNotifications(); });
    })();
</script>
@endauth

