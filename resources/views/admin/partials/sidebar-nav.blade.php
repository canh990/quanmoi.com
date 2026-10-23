{{-- Group 1: TÀI KHOẢN & ĐỊA ĐIỂM --}}
<div>
    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Tài Khoản & Địa Điểm</p>
    <div class="space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.dashboard*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">space_dashboard</span>
            <span>Bảng Điều Khiển</span>
        </a>
        <a href="{{ route('admin.nguoi-dung.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.nguoi-dung.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
            <span>Quản Lý Người Dùng</span>
        </a>
        <a href="{{ route('admin.quan.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ (request()->routeIs('admin.quan.*') && !request()->routeIs('admin.quan-noi-bat.*')) ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">storefront</span>
            <span>Quản Lý Địa Điểm Quán</span>
        </a>
        <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="sidebar-item flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.duyet-xac-thuc.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[18px]">fact_check</span>
                <span>Quán Chờ Duyệt</span>
            </div>
            <span class="sidebar-quan-pending-badge {{ ($sidebarPendingQuanCount ?? 0) > 0 ? '' : 'hidden' }} px-1.5 py-0.5 text-[10px] font-bold bg-amber-500/20 text-amber-300 rounded border border-amber-500/30 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="badge-text">{{ ($sidebarPendingQuanCount ?? 0) > 0 ? ($sidebarPendingQuanCount . ' cần duyệt') : 'Cần duyệt' }}</span>
            </span>
        </a>
        <a href="{{ route('admin.quan-noi-bat.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.quan-noi-bat.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
            <span>Quản Lý Quán Nổi Bật</span>
        </a>
        <a href="{{ route('admin.video-shorts.index') }}" class="sidebar-item flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.video-shorts.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[18px]">smart_display</span>
                <span>Video TikTok / Shorts</span>
            </div>
            @php $pendingVideosCount = \App\Models\VideoShort::where('trang_thai', 'cho_duyet')->count(); @endphp
            @if($pendingVideosCount > 0)
                <span class="px-1.5 py-0.5 text-[10px] font-bold bg-amber-500/20 text-amber-300 rounded border border-amber-500/30">{{ $pendingVideosCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.seo.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.seo.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">travel_explore</span>
            <span>Quản Lý SEO</span>
        </a>
    </div>
</div>

{{-- Group 2: BLOG & TIN TỨC --}}
<div>
    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Blog & Tin Tức</p>
    <div class="space-y-1">
        <a href="{{ route('admin.blog.dashboard') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.blog.dashboard') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">space_dashboard</span>
            <span>Tổng Quan Blog</span>
        </a>
        <a href="{{ route('admin.blog.posts.pending') }}" class="sidebar-item flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.blog.posts.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[18px]">article</span>
                <span>Duyệt Bài Viết</span>
            </div>
            <span class="sidebar-blog-pending-badge {{ ($sidebarPendingBlogCount ?? 0) > 0 ? '' : 'hidden' }} px-1.5 py-0.5 text-[10px] font-bold bg-amber-500/20 text-amber-300 rounded border border-amber-500/30 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="badge-text">{{ ($sidebarPendingBlogCount ?? 0) > 0 ? ($sidebarPendingBlogCount . ' cần duyệt') : 'Cần duyệt' }}</span>
            </span>
        </a>
        <a href="{{ route('admin.blog.categories.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.blog.categories.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">category</span>
            <span>Danh Mục Blog</span>
        </a>
        <a href="{{ route('admin.blog.tags.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.blog.tags.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
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
        <a href="{{ route('admin.thong-ke.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.thong-ke.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">query_stats</span>
            <span>Thống Kê Doanh Thu</span>
        </a>
        <a href="{{ route('admin.vai-tro.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.vai-tro.*') || request()->routeIs('admin.phan-quyen.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
            <span>Vai Trò & Phân Quyền</span>
        </a>
        <a href="{{ route('admin.khuyen-mai-ai.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.khuyen-mai-ai.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">loyalty</span>
            <span>Khuyến Mãi & Voucher AI</span>
        </a>
        <a href="{{ route('admin.thong-bao.index') }}" class="sidebar-item flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.thong-bao.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[18px]">campaign</span>
                <span>Thông Báo Hệ Thống</span>
            </div>
            <span class="sidebar-notif-badge {{ ($sidebarUnreadCount ?? 0) > 0 ? '' : 'hidden' }} px-1.5 py-0.5 text-[10px] font-bold bg-rose-500 text-white rounded-full">{{ ($sidebarUnreadCount ?? 0) > 99 ? '99+' : ($sidebarUnreadCount ?? 0) }}</span>
        </a>
        <a href="{{ route('admin.audit-log.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.audit-log.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">reorder</span>
            <span>Nhật Ký Tác Vụ</span>
        </a>
        <a href="{{ route('admin.ai-tools.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.ai-tools.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">neurology</span>
            <span>Trợ Lý Trí Tuệ AI</span>
        </a>
        <a href="{{ route('admin.bao-mat.index') }}" class="sidebar-item flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('admin.bao-mat.*') ? 'bg-[#F59E0B] text-white font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <span class="material-symbols-outlined text-[18px]">shield</span>
            <span>Cấu Hình Bảo Mật</span>
        </a>
    </div>
</div>

{{-- Quay về trang chủ website --}}
<div class="pt-3 border-t border-slate-800">
    <a href="{{ route('home') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 transition-all shadow-xs group">
        <div class="flex items-center gap-2.5">
            <span class="material-symbols-outlined text-[18px] text-amber-400 group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
            <span>Quay về trang chủ</span>
        </div>
        <span class="material-symbols-outlined text-[16px] text-amber-400/70">home</span>
    </a>
</div>
