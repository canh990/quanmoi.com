{{-- Group 1: TÀI KHOẢN & ĐỊA ĐIỂM --}}
<div>
    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Tài Khoản & Địa Điểm</p>
    <div class="space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">space_dashboard</span>
            <span>Bảng Điều Khiển</span>
        </a>
        <a href="{{ route('admin.vai-tro.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.vai-tro.*') || request()->routeIs('admin.phan-quyen.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
            <span>Phân Quyền & Vai Trò</span>
        </a>
        <a href="{{ route('admin.nguoi-dung.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.nguoi-dung.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
            <span>Quản Lý Người Dùng</span>
        </a>
        <a href="{{ route('admin.quan.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.quan.*') && !request()->routeIs('admin.quan-noi-bat.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">storefront</span>
            <span>Quản Lý Địa Điểm Quán</span>
        </a>
        <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.duyet-xac-thuc.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[18px]">fact_check</span>
                <span>Quán Chờ Duyệt</span>
            </div>
            <span class="px-1.5 py-0.5 text-[10px] font-bold bg-amber-500/20 text-amber-300 rounded border border-amber-500/30">Cần duyệt</span>
        </a>
        <a href="{{ route('admin.quan-noi-bat.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.quan-noi-bat.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
            <span>Quản Lý Quán Nổi Bật</span>
        </a>
        <a href="{{ route('admin.seo.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.seo.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">travel_explore</span>
            <span>Quản Lý SEO</span>
        </a>
    </div>
</div>

{{-- Group 2: BLOG & TIN TỨC --}}
<div>
    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Blog & Tin Tức</p>
    <div class="space-y-1">
        <a href="{{ route('admin.blog.dashboard') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.blog.dashboard') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">space_dashboard</span>
            <span>Tổng Quan Blog</span>
        </a>
        <a href="{{ route('admin.blog.posts.pending') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.blog.posts.pending') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">fact_check</span>
            <span>Duyệt Bài Viết</span>
        </a>
        <a href="{{ route('admin.blog.categories.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.blog.categories.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">category</span>
            <span>Danh Mục Blog</span>
        </a>
        <a href="{{ route('admin.blog.tags.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.blog.tags.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">tag</span>
            <span># Thẻ (Tags)</span>
        </a>
        <a href="/" target="_blank" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all">
            <span class="material-symbols-outlined text-[18px]">open_in_new</span>
            <span>Xem Trang Chủ Quán Mới</span>
        </a>
    </div>
</div>

{{-- Group 3: VẬN HÀNH & PHÂN QUYỀN --}}
<div>
    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Vận Hành & Phân Quyền</p>
    <div class="space-y-1">
        <a href="{{ route('admin.thong-ke.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.thong-ke.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">query_stats</span>
            <span>Thống Kê Doanh Thu</span>
        </a>
        <a href="{{ route('admin.vai-tro.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.vai-tro.*') || request()->routeIs('admin.phan-quyen.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
            <span>Vai Trò & Phân Quyền</span>
        </a>
        <a href="{{ route('admin.audit-log.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.audit-log.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">reorder</span>
            <span>Nhật Ký Tác Vụ</span>
        </a>
        <a href="{{ route('admin.ai-tools.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.ai-tools.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">neurology</span>
            <span>Trợ Lý Trí Tuệ AI</span>
        </a>
        <a href="{{ route('admin.bao-mat.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.bao-mat.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">shield</span>
            <span>Cấu Hình Bảo Mật</span>
        </a>
    </div>
</div>

