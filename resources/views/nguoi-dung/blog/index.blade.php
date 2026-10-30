@extends('layouts.app')
@section('title', 'Bài viết của tôi - Quán Mới')

@section('content')
<main class="max-w-[1200px] mx-auto px-container-margin py-stack-lg min-h-[819px] pt-6">
    {{-- Breadcrumb Navigation Chuẩn SEO --}}
    <nav aria-label="Breadcrumb" class="mb-4">
        <ol class="flex items-center gap-2 text-xs font-medium text-text-muted">
            <li>
                <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">home</span>
                    <span>Trang chủ</span>
                </a>
            </li>
            <li><span class="text-outline-variant">/</span></li>
            <li>
                <a href="{{ route('tai-khoan.index') }}" class="hover:text-primary transition-colors">Tài khoản</a>
            </li>
            <li><span class="text-outline-variant">/</span></li>
            <li class="text-on-surface font-semibold" aria-current="page">Quản lý bài viết</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-stack-lg">
        @include('nguoi-dung.partials.sidebar')

        <div class="md:col-span-9 space-y-stack-lg">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-on-surface">Bài viết của tôi</h1>
                    <p class="text-on-surface-variant mt-2">Quản lý và theo dõi các bài review, chia sẻ của bạn.</p>
                </div>
                <a href="{{ route('nguoi-dung.blog.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-full font-bold hover:bg-primary-hover transition-colors shadow-sm">
                    <span class="material-symbols-outlined">add</span>
                    Viết bài mới
                </a>
            </div>

            <!-- Thanh Công Cụ: Thanh Tìm Kiếm Lên Trên & Các Nút Nằm Cùng 1 Hàng -->
            <div class="bg-white p-3.5 sm:p-4.5 rounded-2xl border border-slate-200/90 shadow-xs mb-6 space-y-3">
                {{-- Hàng 1: Thanh tìm kiếm nằm lên trên rộng rãi 100% --}}
                <form action="{{ route('nguoi-dung.blog.index') }}" method="GET" class="relative w-full">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Tìm trong bài của bạn..." 
                           class="w-full pl-10 pr-9 py-2.5 bg-slate-50/80 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-primary focus:border-primary outline-none shadow-2xs transition-all">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
                    @if(request('search'))
                        <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => request('status')])) }}" 
                           class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-600 p-0.5 transition-colors" 
                           title="Xóa tìm kiếm">
                            <span class="material-symbols-outlined text-base">cancel</span>
                        </a>
                    @endif
                </form>

                {{-- Hàng 2: Toàn bộ 6 nút trạng thái nằm cùng 1 hàng duy nhất (flex-nowrap không bao giờ rớt dòng) --}}
                <div class="flex items-center gap-1.5 sm:gap-2 flex-nowrap overflow-x-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden w-full pt-2.5 border-t border-slate-100 py-0.5">
                    {{-- 1. Tất cả --}}
                    <a href="{{ route('nguoi-dung.blog.index', array_filter(['search' => request('search')])) }}" 
                       class="px-2.5 sm:px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ !request('status') ? 'bg-primary text-white shadow-sm ring-1 ring-primary' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span>Tất cả</span>
                        <span id="user-blog-count-total" class="px-1.5 py-0.5 rounded-full text-[10px] {{ !request('status') ? 'bg-white/20 text-white' : 'bg-white text-slate-700 shadow-2xs' }}">{{ $totalCount ?? 0 }}</span>
                    </a>
                    
                    {{-- 2. Nháp --}}
                    <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => 'draft', 'search' => request('search')])) }}" 
                       class="px-2.5 sm:px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request('status') === 'draft' ? 'bg-slate-800 text-white shadow-sm ring-1 ring-slate-800' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <span class="material-symbols-outlined text-[15px]">edit_note</span>
                        <span>Nháp</span>
                        <span id="user-blog-count-draft" class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') === 'draft' ? 'bg-white/20 text-white' : 'bg-white text-slate-700 shadow-2xs' }}">{{ $statusCounts['draft'] ?? 0 }}</span>
                    </a>

                    {{-- 3. Chờ duyệt --}}
                    <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => 'pending', 'search' => request('search')])) }}" 
                       class="px-2.5 sm:px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-sm ring-1 ring-amber-600' : 'bg-amber-50/70 text-amber-800 hover:bg-amber-100 border border-amber-200/60' }}">
                        <span class="material-symbols-outlined text-[15px]">hourglass_empty</span>
                        <span>Chờ duyệt</span>
                        <span id="user-blog-count-pending" class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') === 'pending' ? 'bg-white/20 text-white' : 'bg-white text-amber-800 shadow-2xs' }}">{{ $statusCounts['pending'] ?? 0 }}</span>
                    </a>

                    {{-- 4. Đã xuất bản --}}
                    <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => 'published', 'search' => request('search')])) }}" 
                       class="px-2.5 sm:px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request('status') === 'published' ? 'bg-emerald-600 text-white shadow-sm ring-1 ring-emerald-600' : 'bg-emerald-50/70 text-emerald-800 hover:bg-emerald-100 border border-emerald-200/60' }}">
                        <span class="material-symbols-outlined text-[15px]">check_circle</span>
                        <span>Đã xuất bản</span>
                        <span id="user-blog-count-published" class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') === 'published' ? 'bg-white/20 text-white' : 'bg-white text-emerald-800 shadow-2xs' }}">{{ $statusCounts['published'] ?? 0 }}</span>
                    </a>

                    {{-- 5. Cần sửa --}}
                    <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => 'need_revision', 'search' => request('search')])) }}" 
                       class="px-2.5 sm:px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request('status') === 'need_revision' ? 'bg-orange-600 text-white shadow-sm ring-1 ring-orange-600' : 'bg-orange-50/70 text-orange-800 hover:bg-orange-100 border border-orange-200/60' }}">
                        <span class="material-symbols-outlined text-[15px]">warning</span>
                        <span>Cần sửa</span>
                        <span id="user-blog-count-revision" class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') === 'need_revision' ? 'bg-white/30 text-white' : 'bg-white text-orange-800 shadow-2xs' }}">{{ $statusCounts['need_revision'] ?? 0 }}</span>
                    </a>

                    {{-- 6. Từ chối --}}
                    <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => 'rejected', 'search' => request('search')])) }}" 
                       class="px-2.5 sm:px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request('status') === 'rejected' ? 'bg-rose-600 text-white shadow-sm ring-1 ring-rose-600' : 'bg-rose-50/70 text-rose-800 hover:bg-rose-100 border border-rose-200/60' }}">
                        <span class="material-symbols-outlined text-[15px]">cancel</span>
                        <span>Từ chối</span>
                        <span id="user-blog-count-rejected" class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') === 'rejected' ? 'bg-white/30 text-white' : 'bg-white text-rose-800 shadow-2xs' }}">{{ $statusCounts['rejected'] ?? 0 }}</span>
                    </a>
                </div>
            </div>

            @if(request()->filled('search'))
                <div class="mb-6 p-3.5 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600 text-lg">search</span>
                        <span class="text-xs text-slate-700">Kết quả tìm kiếm cho: <b class="text-slate-900">"{{ request('search') }}"</b> ({{ $blogs->total() }} bài viết)</span>
                    </div>
                    <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => request('status')])) }}" class="text-xs font-bold text-slate-500 hover:text-red-600 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">close</span> Xóa tìm kiếm
                    </a>
                </div>
            @endif

            <div id="user-blog-container">
            @if($blogs->count() > 0)
                <div id="user-blog-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-stack-lg mb-8">
                    @foreach($blogs as $blog)
                        @php
                            $statusConfig = [
                                'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Bản nháp', 'icon' => 'edit_note'],
                                'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Chờ duyệt', 'icon' => 'pending_actions'],
                                'need_revision' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Cần sửa', 'icon' => 'rule'],
                                'published' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Đã xuất bản', 'icon' => 'check_circle'],
                                'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Từ chối', 'icon' => 'cancel'],
                                'hidden' => ['bg' => 'bg-gray-200', 'text' => 'text-gray-500', 'label' => 'Đã ẩn', 'icon' => 'visibility_off'],
                            ];
                            $config = $statusConfig[$blog->status] ?? $statusConfig['draft'];
                        @endphp
                        
                        <article id="user-blog-card-{{ $blog->id }}" class="bg-surface-card rounded-2xl overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.04)] border border-outline-variant/30 flex flex-col group hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300">
                            <!-- Image & Status -->
                            <div class="relative aspect-[4/3] bg-surface-container overflow-hidden">
                                @if($blog->cover_image_url)
                                    <img src="{{ $blog->cover_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-on-surface-variant/50">
                                        <span class="material-symbols-outlined text-5xl">image</span>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div id="user-blog-badge-{{ $blog->id }}" class="absolute top-3 right-3 px-3 py-1.5 rounded-full flex items-center gap-1.5 text-xs font-bold shadow-sm backdrop-blur-md {{ $config['bg'] }} {{ $config['text'] }} bg-opacity-90">
                                    <span class="material-symbols-outlined text-[14px]">{{ $config['icon'] }}</span>
                                    {{ $config['label'] }}
                                </div>
                                
                                <!-- Category Badge -->
                                @if($blog->category)
                                    <div class="absolute bottom-3 left-3 px-3 py-1 rounded-lg bg-black/60 backdrop-blur-md text-white text-xs font-medium">
                                        {{ $blog->category->name }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="font-title-md text-[16px] leading-[22px] text-on-surface mb-3 line-clamp-2 group-hover:text-primary transition-colors flex-1" title="{{ $blog->title }}">
                                    {{ $blog->title }}
                                </h3>
                                
                                <div class="flex items-center justify-between text-xs text-on-surface-variant mb-4 flex-wrap gap-2">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">calendar_today</span> {{ $blog->created_at->format('d/m/Y') }}</span>
                                    <div class="flex items-center gap-3">
                                        <span class="flex items-center gap-1" title="Lượt xem"><span class="material-symbols-outlined text-[16px]">visibility</span> {{ number_format($blog->view_count ?? 0) }}</span>
                                        <span class="flex items-center gap-1" title="Bình luận"><span class="material-symbols-outlined text-[16px]">forum</span> {{ number_format($blog->comment_count ?? 0) }}</span>
                                    </div>
                                </div>

                                {{-- Moderation feedback block if rejected or need revision --}}
                                @php
                                    $latestLog = $blog->moderationLogs ? $blog->moderationLogs->first() : null;
                                @endphp
                                <div id="user-blog-note-{{ $blog->id }}">
                                    @if(in_array($blog->status, ['rejected', 'need_revision']) && $latestLog && $latestLog->note)
                                        <div class="mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200/70 text-xs text-amber-950 flex items-start gap-2 shadow-2xs">
                                            <span class="material-symbols-outlined text-[16px] text-amber-600 shrink-0 mt-0.5">feedback</span>
                                            <div class="min-w-0 flex-1">
                                                <span class="font-bold text-[11px] uppercase tracking-wider block text-amber-800">
                                                    {{ $blog->status === 'rejected' ? 'Lý do từ chối duyệt:' : 'Yêu cầu chỉnh sửa:' }}
                                                </span>
                                                <p class="text-[11px] text-slate-700 mt-0.5 leading-relaxed font-medium">
                                                    {{ $latestLog->note }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Actions -->
                                <div id="user-blog-actions-{{ $blog->id }}" class="pt-4 border-t border-surface-dim flex items-center justify-between">
                                    @if(in_array($blog->status, ['draft', 'need_revision', 'rejected', 'published']))
                                        <a href="{{ route('nguoi-dung.blog.edit', $blog) }}" class="flex-1 flex items-center justify-center gap-2 py-2 rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-white font-label-md transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">edit</span> Sửa bài
                                        </a>
                                    @else
                                        <div class="flex-1 text-center py-2 text-xs text-text-muted font-medium bg-surface-container rounded-xl cursor-not-allowed">
                                            Đang chờ duyệt
                                        </div>
                                    @endif
                                    
                                    @if($blog->status === 'published')
                                        <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="w-10 h-10 ml-3 rounded-xl flex items-center justify-center bg-surface-container text-on-surface hover:bg-primary-fixed hover:text-primary-fixed-variant transition-colors" title="Xem bài viết">
                                            <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                                        </a>
                                    @endif

                                    <button type="button" 
                                            onclick="confirmDeleteBlog('{{ route('nguoi-dung.blog.destroy', $blog) }}', '{{ addslashes($blog->title) }}')"
                                            class="w-10 h-10 ml-2 rounded-xl flex items-center justify-center bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all cursor-pointer shadow-2xs active:scale-95" 
                                            title="Xóa bài viết">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                
                <div class="flex justify-center">
                    {{ $blogs->links() }}
                </div>
            @else
                @if(request()->filled('search'))
                    <div class="text-center py-16 bg-surface-container-lowest rounded-3xl border border-outline-variant px-4">
                        <span class="material-symbols-outlined text-5xl text-amber-500 mb-3">search_off</span>
                        <h3 class="text-lg font-bold text-on-surface mb-1">Không tìm thấy bài viết</h3>
                        <p class="text-on-surface-variant text-xs mb-4">Không có bài viết nào khớp với từ khóa "{{ request('search') }}".</p>
                        <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => request('status')])) }}" class="inline-flex items-center gap-1.5 px-5 py-2 bg-primary text-on-primary rounded-full text-xs font-bold hover:bg-primary-hover transition-colors">
                            <span class="material-symbols-outlined text-base">refresh</span> Xóa bộ lọc tìm kiếm
                        </a>
                    </div>
                @else
                    <div class="text-center py-20 bg-surface-container-lowest rounded-3xl border border-outline-variant">
                        <span class="material-symbols-outlined text-6xl text-on-surface-variant mb-4">edit_document</span>
                        <h3 class="text-xl font-bold text-on-surface mb-2">Bạn chưa có bài viết nào</h3>
                        <p class="text-on-surface-variant mb-6">Hãy chia sẻ trải nghiệm và câu chuyện của bạn với mọi người.</p>
                        <a href="{{ route('nguoi-dung.blog.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-full font-bold hover:bg-primary-hover transition-colors">
                            <span class="material-symbols-outlined">edit</span> Viết bài ngay
                        </a>
                    </div>
                @endif
            @endif
            </div>
        </div>
    </div>
</main>

<!-- Modal Xác Nhận Xóa Bài Viết Hiện Đại -->
<div id="deleteConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 transition-all">
    <div class="relative w-full max-w-md bg-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-100 transform transition-all scale-95 opacity-0 duration-200" id="deleteConfirmModalBox">
        <button type="button" onclick="closeDeleteModal()" class="absolute right-4 top-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-100 transition-colors cursor-pointer">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>

        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-red-50 border border-red-100 flex items-center justify-center text-red-600 shadow-inner">
                <span class="material-symbols-outlined text-3xl">delete_forever</span>
            </div>

            <h3 class="text-xl font-extrabold text-slate-900 mb-1.5">Xác nhận xóa bài viết</h3>
            <p class="text-slate-500 text-xs sm:text-sm mb-3">Bạn có chắc chắn muốn xóa bài viết này không?</p>
            
            <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-bold text-slate-800 line-clamp-2 mb-3 text-left flex items-start gap-2">
                <span class="material-symbols-outlined text-amber-600 text-base shrink-0 mt-0.5">article</span>
                <span id="deleteModalBlogTitle" class="line-clamp-2">Tên bài viết</span>
            </div>
            
            <p class="text-[11px] text-red-500 font-semibold mb-6 flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-sm">warning</span> Bài viết sau khi xóa sẽ được chuyển vào thùng rác.
            </p>

            <form id="deleteConfirmForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" 
                            onclick="closeDeleteModal()" 
                            style="background-color: #ffffff; color: #334155; border: 1px solid #cbd5e1;" 
                            class="w-full py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm hover:bg-slate-50 active:scale-95 transition-all cursor-pointer">
                        Hủy bỏ
                    </button>
                    <button type="submit" 
                            style="background-color: #dc2626 !important; color: #ffffff !important; border: 1px solid #dc2626 !important;" 
                            class="w-full py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm shadow-md flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-lg" style="color: #ffffff !important;">delete</span>
                        <span style="color: #ffffff !important; font-weight: 700;">Xác nhận xóa</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDeleteBlog(deleteUrl, blogTitle) {
        const modal = document.getElementById('deleteConfirmModal');
        const modalBox = document.getElementById('deleteConfirmModalBox');
        const form = document.getElementById('deleteConfirmForm');
        const titleSpan = document.getElementById('deleteModalBlogTitle');

        if (!modal || !form) return;

        form.action = deleteUrl;
        if (titleSpan) titleSpan.textContent = blogTitle || 'Bài viết này';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            modalBox.classList.remove('scale-95', 'opacity-0');
            modalBox.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteConfirmModal');
        const modalBox = document.getElementById('deleteConfirmModalBox');

        if (!modal || !modalBox) return;

        modalBox.classList.remove('scale-100', 'opacity-100');
        modalBox.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 150);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });

    document.addEventListener('click', function(e) {
        const modal = document.getElementById('deleteConfirmModal');
        if (e.target === modal) {
            closeDeleteModal();
        }
    });

    // Lắng nghe sự kiện duyệt / cập nhật bài viết thời gian thực từ hệ thống
    window.addEventListener('quanmoi:blog-status-updated', function(e) {
        const notif = e.detail;
        if (!notif) return;

        // 1. Cập nhật số lượng trên các tab lọc
        fetch('{{ route("nguoi-dung.blog.index") }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                const counts = data.status_counts || {};
                const total = data.total_count || 0;
                const elTotal = document.getElementById('user-blog-count-total');
                const elDraft = document.getElementById('user-blog-count-draft');
                const elPending = document.getElementById('user-blog-count-pending');
                const elPublished = document.getElementById('user-blog-count-published');
                const elRev = document.getElementById('user-blog-count-revision');
                const elRej = document.getElementById('user-blog-count-rejected');
                if (elTotal) elTotal.textContent = total;
                if (elDraft) elDraft.textContent = counts.draft || 0;
                if (elPending) elPending.textContent = counts.pending || 0;
                if (elPublished) elPublished.textContent = counts.published || 0;
                if (elRev) elRev.textContent = counts.need_revision || 0;
                if (elRej) elRej.textContent = counts.rejected || 0;
            }
        })
        .catch(() => {});

        // 2. Nếu thẻ bài viết đang hiển thị trên giao diện, cập nhật ngay lập tức badge và nút bấm
        const blogId = notif.blog_id;
        if (!blogId) return;

        const card = document.getElementById('user-blog-card-' + blogId);
        const urlParams = new URLSearchParams(window.location.search);
        const currentFilterStatus = urlParams.get('status');

        if (card) {
            const badge = document.getElementById('user-blog-badge-' + blogId);
            const actions = document.getElementById('user-blog-actions-' + blogId);
            const noteBox = document.getElementById('user-blog-note-' + blogId);

            // Highlight thẻ bài viết
            card.classList.add('ring-2', 'ring-primary', 'transition-all', 'duration-500');
            setTimeout(() => card.classList.remove('ring-2', 'ring-primary'), 4000);

            // Cập nhật badge
            if (badge && notif.status) {
                const statusMap = {
                    'published': { bg: 'bg-green-100', text: 'text-green-700', label: 'Đã xuất bản', icon: 'check_circle' },
                    'rejected': { bg: 'bg-red-100', text: 'text-red-700', label: 'Từ chối', icon: 'cancel' },
                    'need_revision': { bg: 'bg-orange-100', text: 'text-orange-700', label: 'Cần sửa', icon: 'rule' },
                    'hidden': { bg: 'bg-gray-200', text: 'text-gray-500', label: 'Đã ẩn', icon: 'visibility_off' },
                    'pending': { bg: 'bg-amber-100', text: 'text-amber-700', label: 'Chờ duyệt', icon: 'pending_actions' },
                };
                const cfg = statusMap[notif.status];
                if (cfg) {
                    badge.className = `absolute top-3 right-3 px-3 py-1.5 rounded-full flex items-center gap-1.5 text-xs font-bold shadow-sm backdrop-blur-md ${cfg.bg} ${cfg.text} bg-opacity-90 transition-all`;
                    badge.innerHTML = `<span class="material-symbols-outlined text-[14px]">${cfg.icon}</span> ${cfg.label}`;
                }
            }

            // Cập nhật ghi chú của quản trị viên nếu có
            if (noteBox) {
                if (notif.note && (notif.status === 'rejected' || notif.status === 'need_revision')) {
                    noteBox.innerHTML = `
                        <div class="mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200/70 text-xs text-amber-950 flex items-start gap-2 shadow-2xs">
                            <span class="material-symbols-outlined text-[16px] text-amber-600 shrink-0 mt-0.5">feedback</span>
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-[11px] uppercase tracking-wider block text-amber-800">
                                    ${notif.status === 'rejected' ? 'Lý do từ chối duyệt:' : 'Yêu cầu chỉnh sửa:'}
                                </span>
                                <p class="text-[11px] text-slate-700 mt-0.5 leading-relaxed font-medium">${notif.note}</p>
                            </div>
                        </div>
                    `;
                } else if (notif.status === 'published') {
                    noteBox.innerHTML = '';
                }
            }

            // Cập nhật action buttons
            if (actions) {
                const editUrl = `/thanh-vien/blog/${blogId}/sua`;
                const deleteUrl = `/thanh-vien/blog/${blogId}`;
                const publicUrl = notif.link || `/blog/${notif.slug || blogId}`;
                const titleSafe = (notif.title || '').replace(/'/g, "\\'");

                if (notif.status === 'published') {
                    actions.innerHTML = `
                        <a href="${editUrl}" class="flex-1 flex items-center justify-center gap-2 py-2 rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-white font-label-md transition-colors">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Sửa bài
                        </a>
                        <a href="${publicUrl}" target="_blank" class="w-10 h-10 ml-3 rounded-xl flex items-center justify-center bg-surface-container text-on-surface hover:bg-primary-fixed hover:text-primary-fixed-variant transition-colors" title="Xem bài viết">
                            <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                        </a>
                        <button type="button" onclick="confirmDeleteBlog('${deleteUrl}', '${titleSafe}')" class="w-10 h-10 ml-2 rounded-xl flex items-center justify-center bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all cursor-pointer shadow-2xs active:scale-95" title="Xóa bài viết">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    `;
                } else if (notif.status === 'rejected' || notif.status === 'need_revision') {
                    actions.innerHTML = `
                        <a href="${editUrl}" class="flex-1 flex items-center justify-center gap-2 py-2 rounded-xl bg-primary text-white hover:bg-primary-hover font-label-md transition-colors shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Chỉnh sửa & Gửi lại
                        </a>
                        <button type="button" onclick="confirmDeleteBlog('${deleteUrl}', '${titleSafe}')" class="w-10 h-10 ml-2 rounded-xl flex items-center justify-center bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all cursor-pointer shadow-2xs active:scale-95" title="Xóa bài viết">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    `;
                }
            }

            // Nếu đang xem tab lọc riêng mà bài viết đã đổi sang trạng thái khác, mờ dần sau 3 giây
            if (currentFilterStatus && currentFilterStatus !== notif.status) {
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => card.remove(), 500);
                }, 3000);
            }
        } else {
            // Nếu thẻ bài viết chưa có trên giao diện (ví dụ đang xem tab Đã xuất bản mà bài vừa được duyệt sang)
            if (!currentFilterStatus || currentFilterStatus === notif.status) {
                fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('user-blog-container');
                    const currentContainer = document.getElementById('user-blog-container');
                    if (newContainer && currentContainer) {
                        currentContainer.innerHTML = newContainer.innerHTML;
                    }
                })
                .catch(() => {});
            }
        }
    });
</script>
@endpush
