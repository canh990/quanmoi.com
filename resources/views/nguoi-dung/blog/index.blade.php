@extends('layouts.app')
@section('title', 'Bài viết của tôi - Quán Mới')

@section('content')
<main class="max-w-[1200px] mx-auto px-container-margin py-stack-lg min-h-[819px] pt-6">
    @if(session('success'))
    <div id="toast-success" class="mb-6 flex items-center gap-3 bg-white border border-green-200 rounded-2xl px-5 py-4 shadow-lg shadow-green-100/50 animate-modal-in transition-opacity duration-300">
        <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-green-600" style="font-variation-settings:'FILL' 1">check_circle</span>
        </div>
        <div class="flex-1">
            <p class="font-bold text-gray-900 text-[15px]">Thành công</p>
            <p class="text-sm text-gray-600 mt-0.5">{{ session('success') }}</p>
        </div>
        <button onclick="document.getElementById('toast-success').remove()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
    </div>
    <script>
        setTimeout(function() {
            var el = document.getElementById('toast-success');
            if (el) {
                el.style.opacity = '0';
                setTimeout(function() { el.remove(); }, 300);
            }
        }, 2500);
    </script>
    @endif

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

            <!-- Status Filter Tabs & Search Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-outline-variant/40 mb-6">
                <!-- Status Filter Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0 scrollbar-hide">
                    <a href="{{ route('nguoi-dung.blog.index', array_filter(['search' => request('search')])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 {{ !request('status') ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                        <span>Tất cả</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ !request('status') ? 'bg-white/20 text-white' : 'bg-black/5 text-on-surface-variant' }}">{{ $totalCount ?? 0 }}</span>
                    </a>
                    
                    <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => 'draft', 'search' => request('search')])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 {{ request('status') === 'draft' ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                        <span class="material-symbols-outlined text-sm">edit_note</span>
                        <span>Nháp</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ request('status') === 'draft' ? 'bg-white/20 text-white' : 'bg-black/5 text-on-surface-variant' }}">{{ $statusCounts['draft'] ?? 0 }}</span>
                    </a>

                    <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => 'pending', 'search' => request('search')])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 {{ request('status') === 'pending' ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                        <span class="material-symbols-outlined text-sm">hourglass_empty</span>
                        <span>Chờ duyệt</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ request('status') === 'pending' ? 'bg-white/20 text-white' : 'bg-black/5 text-on-surface-variant' }}">{{ $statusCounts['pending'] ?? 0 }}</span>
                    </a>

                    <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => 'published', 'search' => request('search')])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 {{ request('status') === 'published' ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Đã xuất bản</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ request('status') === 'published' ? 'bg-white/20 text-white' : 'bg-black/5 text-on-surface-variant' }}">{{ $statusCounts['published'] ?? 0 }}</span>
                    </a>
                </div>

                <!-- Search Input in User Blog -->
                <form action="{{ route('nguoi-dung.blog.index') }}" method="GET" class="relative w-full sm:w-64 shrink-0">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm trong bài của bạn..." class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-primary focus:border-primary outline-none shadow-2xs">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
                    @if(request('search'))
                        <a href="{{ route('nguoi-dung.blog.index', array_filter(['status' => request('status')])) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5" title="Xóa tìm kiếm">
                            <span class="material-symbols-outlined text-base">cancel</span>
                        </a>
                    @endif
                </form>
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

            @if($blogs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-stack-lg mb-8">
                    @foreach($blogs as $blog)
                        @php
                            $statusConfig = [
                                'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Bản nháp', 'icon' => 'edit_note'],
                                'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Chờ duyệt', 'icon' => 'hourglass_empty'],
                                'need_revision' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Cần sửa', 'icon' => 'warning'],
                                'published' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Đã xuất bản', 'icon' => 'check_circle'],
                                'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Từ chối', 'icon' => 'cancel'],
                                'hidden' => ['bg' => 'bg-gray-200', 'text' => 'text-gray-500', 'label' => 'Đã ẩn', 'icon' => 'visibility_off'],
                            ];
                            $config = $statusConfig[$blog->status] ?? $statusConfig['draft'];
                        @endphp
                        
                        <article class="bg-surface-card rounded-2xl overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.04)] border border-outline-variant/30 flex flex-col group hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300">
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
                                <div class="absolute top-3 right-3 px-3 py-1.5 rounded-full flex items-center gap-1.5 text-xs font-bold shadow-sm backdrop-blur-md {{ $config['bg'] }} {{ $config['text'] }} bg-opacity-90">
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
                                
                                <div class="flex items-center justify-between text-xs text-on-surface-variant mb-4">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">calendar_today</span> {{ $blog->created_at->format('d/m/Y') }}</span>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">visibility</span> {{ number_format($blog->view_count ?? 0) }} view</span>
                                </div>
                                
                                <!-- Actions -->
                                <div class="pt-4 border-t border-surface-dim flex items-center justify-between">
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
</script>
@endpush
