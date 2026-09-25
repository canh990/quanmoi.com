{{-- Main Core Management --}}
<div>
    <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tổng Quan & Thống Kê</p>
    <div class="space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.dashboard') ? 'fill-1' : '' }}">dashboard</span>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.thong-ke.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.thong-ke.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.thong-ke.*') ? 'fill-1' : '' }}">analytics</span>
            <span>Thống kê</span>
        </a>
    </div>
</div>

{{-- User & Security Management --}}
<div>
    <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tài Khoản & Phân Quyền</p>
    <div class="space-y-1">
        <a href="{{ route('admin.nguoi-dung.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.nguoi-dung.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px]">group</span>
            <span>Người dùng</span>
        </a>
        <a href="{{ route('admin.vai-tro.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.vai-tro.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
            <span>Vai trò</span>
        </a>
        <a href="{{ route('admin.phan-quyen.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.phan-quyen.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px]">lock_person</span>
            <span>Phân quyền (Overrides)</span>
        </a>
    </div>
</div>

{{-- Venue Management --}}
<div>
    <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Địa Điểm Quán</p>
    <div class="space-y-1">
        <a href="{{ route('admin.quan.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.quan.*') && !request()->routeIs('admin.quan-noi-bat.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px]">storefront</span>
            <span>Danh sách quán</span>
        </a>
        <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.duyet-xac-thuc.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px]">verified_user</span>
            <span>Duyệt xác thực</span>
        </a>
        <a href="{{ route('admin.quan-noi-bat.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.quan-noi-bat.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px]">star</span>
            <span>Quán nổi bật</span>
        </a>
    </div>
</div>

{{-- System & Security --}}
<div>
    <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Hệ Thống & Vận Hành</p>
    <div class="space-y-1">
        <a href="{{ route('admin.thong-bao.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.thong-bao.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px]">notifications</span>
            <span>Thông báo</span>
        </a>
        <a href="{{ route('admin.bao-mat.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.bao-mat.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px]">security</span>
            <span>Bảo mật</span>
        </a>
        <a href="{{ route('admin.audit-log.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.audit-log.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px]">history</span>
            <span>Audit Log</span>
        </a>
        <a href="{{ route('admin.ai-tools.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.ai-tools.*') ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold shadow-md shadow-purple-600/30' : 'hover:bg-slate-800/80 hover:text-white text-purple-300' }}">
            <span class="material-symbols-outlined text-[20px]">psychology</span>
            <span>AI Tools</span>
        </a>
    </div>
</div>

{{-- Content & SEO --}}
<div>
    <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nội Dung & SEO</p>
    <div class="space-y-1">
        <a href="{{ route('admin.seo.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.seo.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px]">search</span>
            <span>Cấu hình SEO</span>
        </a>
        <a href="{{ route('admin.blog.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.blog.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[20px]">article</span>
            <span>Blog & Tin tức</span>
        </a>
    </div>
</div>
