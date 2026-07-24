{{-- ╔══════════════════════════════════════════════════════════════╗ --}}
{{-- ║   DESKTOP — Edge-to-Edge Glassmorphic Header (Tràn viền)     ║ --}}
{{-- ╚══════════════════════════════════════════════════════════════╝ --}}
<header class="hidden md:block fixed top-0 left-0 right-0 w-full z-50 bg-white/75 dark:bg-on-surface-variant/10 backdrop-blur-md border-b border-white/40 dark:border-white/10 shadow-[0_4px_24px_rgba(160,65,0,0.03)] transition-all duration-300">
    {{-- Đã đổi sang w-full và tăng padding hai bên để tràn viền cực thoáng --}}
    <div class="w-full px-6 lg:px-10 h-18 py-3 flex justify-between items-center">
        
        {{-- Logo --}}
        <a href="/" class="flex items-center gap-2 group">
            <span class="material-symbols-outlined text-primary text-3xl group-hover:rotate-12 transition-transform duration-300" style="font-variation-settings: 'FILL' 1;">restaurant</span>
            <span class="font-display-lg text-[24px] text-primary tracking-tight font-extrabold transition-all duration-300 group-hover:opacity-95">Quán Mới</span>
        </a>

        {{-- Nav Links --}}
        <nav class="flex items-center gap-1 bg-surface-container-low/60 p-1 rounded-full border border-outline-variant/15">
            <a class="nav-link px-4.5 py-1.5 rounded-full text-[14px] font-semibold transition-all duration-200 {{ request()->is('/') ? 'active text-primary bg-white shadow-sm' : 'text-on-surface-variant hover:text-primary hover:bg-primary/5' }}" href="/">
                Khám phá
            </a>
            <a class="nav-link px-4.5 py-1.5 rounded-full text-[14px] font-semibold transition-all duration-200 text-on-surface-variant hover:text-primary hover:bg-primary/5" href="#">
                Blog
            </a>
            @auth
            <a class="nav-link px-4.5 py-1.5 rounded-full text-[14px] font-semibold transition-all duration-200 text-on-surface-variant hover:text-primary hover:bg-primary/5" href="#">
                Tài khoản
            </a>
            @endauth
        </nav>

        {{-- Right Actions --}}
        <div class="flex items-center gap-3">
            {{-- Search Bar --}}
            <div class="relative hidden lg:block group">
                <input class="pl-10 pr-4 py-2 rounded-full bg-surface-container-low/60 border border-outline-variant/30 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none w-52 focus:w-60 text-on-surface text-[14px] transition-all duration-300 placeholder:text-text-muted" placeholder="Tìm kiếm địa điểm..." type="text"/>
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-text-muted text-[18px] group-focus-within:text-primary transition-colors">search</span>
            </div>

            @auth
                {{-- Notifications --}}
                <button class="relative p-2.5 rounded-full hover:bg-primary/5 text-on-surface-variant hover:text-primary transition-all active:scale-90 group" title="Thông báo">
                    <span class="material-symbols-outlined text-[22px] group-hover:rotate-12 transition-transform">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full ring-2 ring-white animate-pulse"></span>
                </button>

                {{-- Divider --}}
                <div class="w-px h-6 bg-outline-variant/40 mx-1"></div>

                {{-- User Menu --}}
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-full hover:bg-primary/5 transition-all">
                        <img alt="Ảnh đại diện" class="w-8 h-8 rounded-full object-cover ring-2 ring-primary-fixed" src="{{ Auth::user()->anh_dai_dien ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->ho_ten) . '&background=ffdbcc&color=a04100&bold=true&size=128' }}"/>
                        <div class="flex flex-col text-left">
                            <span class="font-bold text-[12px] text-on-surface leading-tight max-w-[100px] truncate">{{ Auth::user()->ho_ten }}</span>
                            <span class="text-[10px] text-text-muted leading-tight">Thành viên</span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2.5 rounded-full hover:bg-red-50 text-on-surface-variant hover:text-error transition-all active:scale-95" title="Đăng xuất">
                            <span class="material-symbols-outlined text-[20px]">logout</span>
                        </button>
                    </form>
                </div>
            @else
                {{-- Guest Buttons --}}
                <a href="{{ route('login') }}" class="px-4.5 py-2 rounded-full text-[14px] font-semibold text-on-surface hover:text-primary hover:bg-primary/5 transition-all">Đăng nhập</a>
                <a href="{{ route('register') }}" class="px-5 py-2 rounded-full text-[14px] font-bold bg-primary text-white shadow-[0_4px_12px_rgba(160,65,0,0.2)] hover:shadow-[0_6px_20px_rgba(160,65,0,0.3)] hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">Đăng ký</a>
            @endauth
        </div>
    </div>
</header>


{{-- ╔══════════════════════════════════════════════════════════════╗ --}}
{{-- ║   MOBILE — Sticky TopAppBar                                  ║ --}}
{{-- ╚══════════════════════════════════════════════════════════════╝ --}}
<header class="flex md:hidden justify-between items-center px-4 h-14 w-full fixed top-0 left-0 right-0 z-50 bg-surface/80 backdrop-blur-md border-b border-surface-container-high/40">
    <button class="p-2 rounded-full hover:bg-surface-container active:scale-90 transition-all" aria-label="Menu">
        <span class="material-symbols-outlined text-on-surface-variant text-[22px]">menu</span>
    </button>
    <a href="/" class="flex items-center gap-1.5">
        <span class="material-symbols-outlined text-primary text-xl" style="font-variation-settings: 'FILL' 1;">restaurant</span>
        <span class="font-headline-lg-mobile text-headline-lg-mobile font-black text-primary tracking-tight">Quán Mới</span>
    </a>
    <button class="p-2 rounded-full hover:bg-surface-container active:scale-90 transition-all" aria-label="Search">
        <span class="material-symbols-outlined text-on-surface-variant text-[22px]">search</span>
    </button>
</header>


{{-- ╔══════════════════════════════════════════════════════════════╗ --}}
{{-- ║   MOBILE — Bottom Navigation Bar                             ║ --}}
{{-- ╚══════════════════════════════════════════════════════════════╝ --}}
<nav class="fixed bottom-0 left-0 w-full z-50 md:hidden">
    <div class="h-4 bg-gradient-to-t from-surface/90 to-transparent pointer-events-none"></div>
    <div class="bg-surface/90 backdrop-blur-md border-t border-surface-container-high/40 pb-safe h-[64px] flex justify-around items-center px-2">
        
        {{-- Khám phá --}}
        <a href="/" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group">
            <div class="px-5 py-1 rounded-full transition-all duration-300 {{ request()->is('/') ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high/50' }}">
                <span class="material-symbols-outlined text-[22px] block" style="font-variation-settings: 'FILL' {{ request()->is('/') ? '1' : '0' }};">explore</span>
            </div>
            <span class="text-[10px] font-bold mt-1 tracking-wide {{ request()->is('/') ? 'text-primary' : 'text-on-surface-variant' }}">Khám phá</span>
        </a>

        {{-- Tìm kiếm --}}
        <a href="#" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group">
            <div class="px-5 py-1 rounded-full transition-all duration-300 text-on-surface-variant hover:bg-surface-container-high/50">
                <span class="material-symbols-outlined text-[22px] block">search</span>
            </div>
            <span class="text-[10px] font-bold mt-1 tracking-wide text-on-surface-variant">Tìm kiếm</span>
        </a>

        {{-- Đã lưu --}}
        <a href="#" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group">
            <div class="px-5 py-1 rounded-full transition-all duration-300 text-on-surface-variant hover:bg-surface-container-high/50">
                <span class="material-symbols-outlined text-[22px] block">bookmark</span>
            </div>
            <span class="text-[10px] font-bold mt-1 tracking-wide text-on-surface-variant">Đã lưu</span>
        </a>

        {{-- Tài khoản --}}
        @auth
            <a href="#" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group">
                <div class="px-5 py-1 rounded-full transition-all duration-300 hover:bg-surface-container-high/50">
                    <img alt="Ảnh đại diện" class="w-[22px] h-[22px] rounded-full object-cover ring-2 ring-primary-fixed" src="{{ Auth::user()->anh_dai_dien ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->ho_ten) . '&background=ffdbcc&color=a04100&bold=true&size=64' }}"/>
                </div>
                <span class="text-[10px] font-bold mt-1 tracking-wide text-on-surface-variant">Tài khoản</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center group">
                <div class="px-5 py-1 rounded-full transition-all duration-300 {{ (request()->is('dangnhap') || request()->is('dangky')) ? 'bg-primary-fixed text-on-primary-fixed' : 'text-on-surface-variant hover:bg-surface-container-high/50' }}">
                    <span class="material-symbols-outlined text-[22px] block" style="font-variation-settings: 'FILL' {{ (request()->is('dangnhap') || request()->is('dangky')) ? '1' : '0' }};">person</span>
                </div>
                <span class="text-[10px] font-bold mt-1 tracking-wide {{ (request()->is('dangnhap') || request()->is('dangky')) ? 'text-primary' : 'text-on-surface-variant' }}">Tài khoản</span>
            </a>
        @endauth
    </div>
</nav>