@extends('layouts.app')
@section('title', 'Bài viết đã lưu - Quán Mới')

@push('styles')
<style>
    .saved-card-cover-frame {
        position: relative;
        width: 100%;
        height: 205px;
        min-height: 205px;
        max-height: 205px;
        overflow: hidden;
        display: block;
        background-color: #f1f5f9;
    }
    .saved-card-cover-frame img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.5s ease;
    }
    .level-1-card:hover .saved-card-cover-frame img {
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
<main class="max-w-[1200px] mx-auto px-container-margin py-stack-lg min-h-[819px] pt-6">

    <div class="grid grid-cols-1 md:grid-cols-12 gap-stack-lg">
        @include('nguoi-dung.partials.sidebar')

        <div class="md:col-span-9 space-y-stack-lg">
            {{-- Breadcrumbs --}}
            <nav class="flex items-center gap-2 text-xs text-text-muted mb-2 font-medium" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">home</span>
                    <span>Trang chủ</span>
                </a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('tai-khoan.index') }}" class="hover:text-primary transition-colors">Tài khoản</a>
                <span class="text-slate-300">/</span>
                <span class="text-on-surface font-semibold">Bài viết đã lưu</span>
            </nav>

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-200/80 gap-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface tracking-tight">Bài viết đã lưu</h1>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ ($unreadCount ?? 0) > 0 ? 'bg-amber-100 text-amber-800 border border-amber-200/80' : 'bg-emerald-100 text-emerald-800 border border-emerald-200/80' }}" id="saved-blogs-counter">
                                    {{ ($unreadCount ?? 0) > 0 ? ($unreadCount . ' chưa đọc / ' . ($totalCount ?? 0) . ' bài') : (($totalCount ?? 0) . ' bài (Đã đọc hết)') }}
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-text-muted mt-0.5">Danh sách bài viết bạn đã đánh dấu đọc sau. Bấm vào xem sẽ tự động hiển thị "Đã đọc" và giảm số lượng ngay lập tức mà không cần tải lại trang.</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary/10 hover:bg-primary text-primary hover:text-white rounded-xl text-xs sm:text-sm font-bold transition-all shadow-xs group">
                        <span class="material-symbols-outlined text-base group-hover:rotate-45 transition-transform">explore</span>
                        <span>Khám phá Blog</span>
                    </a>
                </div>
            </div>

            {{-- Tab Phân Loại: Tất cả / Chưa đọc / Đã đọc --}}
            <div class="flex items-center gap-2 border-b border-slate-200/80 pb-1 overflow-x-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                <a href="{{ route('nguoi-dung.blog.saved', array_filter(['category' => request('category'), 'search' => request('search')])) }}" 
                   class="pb-2 px-3 text-xs sm:text-sm font-bold border-b-2 transition-all flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ !request('tab') ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    <span>Tất cả</span>
                    <span id="tab-count-all" class="px-1.5 py-0.5 rounded-full text-[11px] {{ !request('tab') ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">{{ $totalCount ?? 0 }}</span>
                </a>
                <a href="{{ route('nguoi-dung.blog.saved', array_filter(['tab' => 'unread', 'category' => request('category'), 'search' => request('search')])) }}" 
                   class="pb-2 px-3 text-xs sm:text-sm font-bold border-b-2 transition-all flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request('tab') === 'unread' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Chưa đọc</span>
                    <span id="tab-count-unread" class="px-1.5 py-0.5 rounded-full text-[11px] {{ request('tab') === 'unread' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">{{ $unreadCount ?? 0 }}</span>
                </a>
                <a href="{{ route('nguoi-dung.blog.saved', array_filter(['tab' => 'read', 'category' => request('category'), 'search' => request('search')])) }}" 
                   class="pb-2 px-3 text-xs sm:text-sm font-bold border-b-2 transition-all flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request('tab') === 'read' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    <span class="material-symbols-outlined text-xs text-emerald-600">done_all</span>
                    <span>Đã đọc</span>
                    <span id="tab-count-read" class="px-1.5 py-0.5 rounded-full text-[11px] {{ request('tab') === 'read' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $readCount ?? 0 }}</span>
                </a>
            </div>

            {{-- Bộ Lọc & Tìm Kiếm --}}
            <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 shadow-2xs space-y-3">
                <form action="{{ route('nguoi-dung.blog.saved') }}" method="GET" class="relative w-full">
                    @if(request('tab'))
                        <input type="hidden" name="tab" value="{{ request('tab') }}">
                    @endif
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Tìm trong bài viết đã lưu theo tiêu đề, tác giả..." 
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-50/80 hover:bg-white focus:bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none shadow-2xs transition-all">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
                    @if(request('search'))
                        <a href="{{ route('nguoi-dung.blog.saved', array_filter(['tab' => request('tab'), 'category' => request('category')])) }}" 
                           class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-600 p-0.5 transition-colors" 
                           title="Xóa tìm kiếm">
                            <span class="material-symbols-outlined text-base">cancel</span>
                        </a>
                    @endif
                </form>

                {{-- Phân loại theo Danh Mục (nếu có) --}}
                @if(isset($categories) && count($categories) > 0)
                    <div class="flex items-center gap-1.5 overflow-x-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden w-full pt-1">
                        <a href="{{ route('nguoi-dung.blog.saved', array_filter(['tab' => request('tab'), 'search' => request('search')])) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-semibold shrink-0 transition-all {{ !request('category') ? 'bg-amber-500 text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Tất cả danh mục
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('nguoi-dung.blog.saved', array_filter(['tab' => request('tab'), 'category' => $cat->slug, 'search' => request('search')])) }}" 
                               class="px-3 py-1.5 rounded-xl text-xs font-semibold shrink-0 transition-all {{ request('category') === $cat->slug ? 'bg-amber-500 text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Thông báo kết quả tìm kiếm / lọc --}}
            @if(request('search') || request('category') || request('tab'))
                <div class="flex items-center justify-between text-xs text-slate-600 bg-amber-50/70 border border-amber-200/70 px-4 py-2.5 rounded-xl">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="material-symbols-outlined text-amber-600 text-sm">filter_alt</span>
                        <span>Đang lọc:</span>
                        @if(request('tab'))
                            <span class="font-bold text-amber-800 bg-amber-100/90 px-2 py-0.5 rounded-md text-[11px]">
                                {{ request('tab') === 'unread' ? 'Chưa đọc' : 'Đã đọc' }}
                            </span>
                        @endif
                        @if(request('search'))
                            <span class="font-bold text-slate-800">"{{ request('search') }}"</span>
                        @endif
                        @if(request('category'))
                            <span class="font-bold text-amber-800 bg-amber-100/90 px-2 py-0.5 rounded-md text-[11px]">
                                Danh mục: {{ $categories->firstWhere('slug', request('category'))?->name ?? request('category') }}
                            </span>
                        @endif
                        <span class="text-slate-400">•</span>
                        <span>{{ $blogs->total() }} bài viết</span>
                    </div>
                    <a href="{{ route('nguoi-dung.blog.saved') }}" class="font-bold text-amber-700 hover:text-rose-600 hover:underline flex items-center gap-0.5 shrink-0">
                        <span class="material-symbols-outlined text-xs">close</span>
                        <span>Bỏ lọc</span>
                    </a>
                </div>
            @endif

            {{-- Danh Sách Bài Viết Đã Lưu --}}
            @if($blogs->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="saved-blogs-grid">
                    @foreach($blogs as $blog)
                        @php
                            $isRead = !empty($blog->pivot->da_doc);
                        @endphp
                        <article id="saved-card-{{ $blog->id }}" class="level-1-card flex flex-col h-full group border {{ $isRead ? 'border-slate-200 opacity-90' : 'border-amber-300 ring-1 ring-amber-300/40 shadow-xs' }} hover:border-amber-400 hover:ring-2 hover:ring-amber-400/20 hover:shadow-lg transition-all duration-300 relative bg-white rounded-2xl overflow-hidden">
                            <div class="saved-card-cover-frame">
                                <a href="{{ route('blog.show', $blog->slug) }}" 
                                   onclick="handleCardClickToRead(event, '{{ $blog->id }}')"
                                   class="block w-full h-full">
                                    <img src="{{ $blog->cover_image_url ?? 'https://placehold.co/600x400/png' }}" 
                                         alt="{{ $blog->title }}"
                                         loading="lazy"/>
                                </a>

                                {{-- Badges trên ảnh: Danh mục & Trạng thái đọc --}}
                                <div class="absolute top-3 left-3 z-10 flex flex-col gap-1 items-start max-w-[calc(100%-4rem)]">
                                    @if($blog->category)
                                        <div class="bg-black/60 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wide truncate shadow-sm">
                                            {{ $blog->category->name }}
                                        </div>
                                    @endif

                                    <div id="read-badge-{{ $blog->id }}">
                                        @if($isRead)
                                            <span class="inline-flex items-center gap-1 bg-emerald-600/90 text-white text-[9.5px] font-bold px-2 py-0.5 rounded-md backdrop-blur-md shadow-xs">
                                                <span class="material-symbols-outlined text-[11px]">check</span>
                                                <span>Đã đọc</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-amber-500 text-white text-[9.5px] font-bold px-2 py-0.5 rounded-md shadow-xs animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                <span>Chưa đọc</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Nút Bỏ Lưu Nhanh ở góc ảnh --}}
                                <button type="button" 
                                        onclick="handleRemoveSavedCard('{{ $blog->id }}', '{{ $blog->slug }}', '{{ addslashes($blog->title) }}')"
                                        data-blog-id="{{ $blog->id }}" 
                                        data-blog-slug="{{ $blog->slug }}" 
                                        class="absolute top-3 right-3 z-10 w-8 h-8 bg-white/95 hover:bg-rose-50 text-amber-500 hover:text-rose-600 backdrop-blur-md rounded-full flex items-center justify-center shadow-md hover:scale-110 active:scale-95 transition-all cursor-pointer" 
                                        title="Bỏ lưu bài viết này">
                                    <span class="material-symbols-outlined text-[19px]" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                                </button>

                                <div class="absolute bottom-2.5 right-2.5 z-10 px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white font-medium text-[10px] flex items-center gap-1 shadow-xs">
                                    <span class="material-symbols-outlined text-[12px] text-amber-300">schedule</span>
                                    <span>{{ ($blog->published_at ?? $blog->created_at)?->diffForHumans() ?? 'Vừa xong' }}</span>
                                </div>
                            </div>

                            <div class="p-4 flex flex-col flex-grow">
                                <a href="{{ route('blog.show', $blog->slug) }}" 
                                   onclick="handleCardClickToRead(event, '{{ $blog->id }}')"
                                   class="flex-grow block">
                                    <h3 id="card-title-{{ $blog->id }}" class="font-bold text-sm sm:text-base text-on-surface line-clamp-2 group-hover:text-amber-600 transition-colors leading-snug mb-2 {{ $isRead ? 'text-slate-700' : 'text-slate-900 font-extrabold' }}">
                                        {{ $blog->title }}
                                    </h3>
                                    <p class="text-text-muted text-xs line-clamp-2 leading-relaxed mb-3">
                                        {{ $blog->excerpt ?? Str::limit(strip_tags($blog->content), 100) }}
                                    </p>
                                </a>

                                <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-text-muted">
                                    <div class="flex items-center gap-2">
                                        @if($blog->user)
                                            <div class="w-6 h-6 rounded-full overflow-hidden bg-slate-200 shrink-0">
                                                <img src="{{ $blog->user->anh_dai_dien ?? 'https://ui-avatars.com/api/?name=' . urlencode($blog->user->ho_ten) . '&background=ffdbcc&color=a04100&bold=true&size=64' }}" 
                                                     alt="{{ $blog->user->ho_ten }}" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                            <span class="font-medium text-slate-700 truncate max-w-[90px] sm:max-w-[110px]">{{ $blog->user->ho_ten }}</span>
                                        @else
                                            <span class="font-medium text-slate-700">Tác giả</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-3">
                                        {{-- Nút Chuyển Đổi Trạng Thái Đã Đọc / Chưa Đọc --}}
                                        <button type="button" 
                                                onclick="handleToggleReadCard('{{ $blog->id }}')" 
                                                id="toggle-read-btn-{{ $blog->id }}"
                                                class="text-slate-400 hover:text-amber-600 p-1 rounded-md hover:bg-slate-100 transition-colors cursor-pointer" 
                                                title="{{ $isRead ? 'Đánh dấu là chưa đọc' : 'Đánh dấu là đã đọc' }}">
                                            <span class="material-symbols-outlined text-[17px]">{{ $isRead ? 'mark_chat_unread' : 'done_all' }}</span>
                                        </button>

                                        <a href="{{ route('blog.show', $blog->slug) }}" 
                                           onclick="handleCardClickToRead(event, '{{ $blog->id }}')"
                                           class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 hover:text-amber-700 hover:underline">
                                            <span>Đọc tiếp</span>
                                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Phân trang --}}
                <div class="mt-8 flex justify-center">
                    {{ $blogs->links() }}
                </div>
            @else
                {{-- Trạng thái trống (Empty State) --}}
                <div class="bg-white rounded-3xl border border-slate-200/90 p-8 sm:p-12 text-center shadow-xs flex flex-col items-center justify-center max-w-xl mx-auto my-6">
                    <div class="w-20 h-20 rounded-3xl bg-amber-50 text-amber-500 border border-amber-200/60 flex items-center justify-center mb-5 shadow-inner">
                        <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 0;">bookmark_add</span>
                    </div>

                    @if(request('search') || request('category') || request('tab'))
                        <h3 class="text-xl font-extrabold text-on-surface mb-2">Không tìm thấy bài viết</h3>
                        <p class="text-xs sm:text-sm text-text-muted mb-6 leading-relaxed max-w-md">
                            Không có bài viết đã lưu nào khớp với bộ lọc hoặc từ khóa tìm kiếm của bạn.
                        </p>
                        <a href="{{ route('nguoi-dung.blog.saved') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs sm:text-sm font-bold transition-all">
                            <span class="material-symbols-outlined text-base">refresh</span>
                            <span>Xem tất cả bài đã lưu</span>
                        </a>
                    @else
                        <h3 class="text-xl font-extrabold text-on-surface mb-2">Chưa có bài viết nào được lưu</h3>
                        <p class="text-xs sm:text-sm text-text-muted mb-6 leading-relaxed max-w-md">
                            Khi lướt xem các bài viết trên Blog Quán Mới, hãy bấm vào nút Bookmark <span class="inline-block text-amber-500">🔖</span> ở góc mỗi bài để lưu lại đọc sau nhé!
                        </p>
                        <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-full text-xs sm:text-sm font-bold hover:bg-primary-hover shadow-sm hover:scale-105 active:scale-95 transition-all">
                            <span class="material-symbols-outlined text-base">explore</span>
                            <span>Khám phá Blog Quán Mới</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    /**
     * Cập nhật tức thì giao diện card thành "ĐÃ ĐỌC" mà KHÔNG cần tải lại trang
     */
    function applyCardAsReadUI(blogId) {
        const badgeContainer = document.getElementById(`read-badge-${blogId}`);
        const toggleBtn = document.getElementById(`toggle-read-btn-${blogId}`);
        const card = document.getElementById(`saved-card-${blogId}`);
        const titleEl = document.getElementById(`card-title-${blogId}`);

        // Chỉ giảm số đếm nếu trước đó bài viết đang là CHƯA ĐỌC
        const isCurrentlyUnread = badgeContainer && badgeContainer.textContent.includes('Chưa đọc');

        if (badgeContainer) {
            badgeContainer.innerHTML = `<span class="inline-flex items-center gap-1 bg-emerald-600/90 text-white text-[9.5px] font-bold px-2 py-0.5 rounded-md backdrop-blur-md shadow-xs"><span class="material-symbols-outlined text-[11px]">check</span><span>Đã đọc</span></span>`;
        }
        if (toggleBtn) {
            toggleBtn.title = 'Đánh dấu là chưa đọc';
            toggleBtn.innerHTML = `<span class="material-symbols-outlined text-[17px]">mark_chat_unread</span>`;
        }
        if (card) {
            card.classList.remove('border-amber-300', 'ring-1', 'ring-amber-300/40');
            card.classList.add('border-slate-200', 'opacity-90');
        }
        if (titleEl) {
            titleEl.classList.remove('font-extrabold', 'text-slate-900');
            titleEl.classList.add('text-slate-700');
        }

        if (isCurrentlyUnread) {
            decrementUnreadCountUI();
        }
    }

    /**
     * Cập nhật tức thì giao diện card thành "CHƯA ĐỌC" mà KHÔNG cần tải lại trang
     */
    function applyCardAsUnreadUI(blogId) {
        const badgeContainer = document.getElementById(`read-badge-${blogId}`);
        const toggleBtn = document.getElementById(`toggle-read-btn-${blogId}`);
        const card = document.getElementById(`saved-card-${blogId}`);
        const titleEl = document.getElementById(`card-title-${blogId}`);

        if (badgeContainer) {
            badgeContainer.innerHTML = `<span class="inline-flex items-center gap-1 bg-amber-500 text-white text-[9.5px] font-bold px-2 py-0.5 rounded-md shadow-xs animate-pulse"><span class="w-1.5 h-1.5 rounded-full bg-white"></span><span>Chưa đọc</span></span>`;
        }
        if (toggleBtn) {
            toggleBtn.title = 'Đánh dấu là đã đọc';
            toggleBtn.innerHTML = `<span class="material-symbols-outlined text-[17px]">done_all</span>`;
        }
        if (card) {
            card.classList.remove('border-slate-200', 'opacity-90');
            card.classList.add('border-amber-300', 'ring-1', 'ring-amber-300/40');
        }
        if (titleEl) {
            titleEl.classList.add('font-extrabold', 'text-slate-900');
            titleEl.classList.remove('text-slate-700');
        }

        incrementUnreadCountUI();
    }

    /**
     * Giảm số đếm chưa đọc trên thanh sidebar, header và tabs ngay lập tức
     */
    function decrementUnreadCountUI() {
        const sidebarBadge = document.getElementById('sidebar-saved-blog-count');
        if (sidebarBadge) {
            const current = parseInt(sidebarBadge.textContent) || 0;
            const next = Math.max(0, current - 1);
            if (next > 0) {
                sidebarBadge.textContent = next > 99 ? '99+' : next;
                sidebarBadge.classList.remove('hidden');
            } else {
                sidebarBadge.classList.add('hidden');
            }
        }

        const unreadTab = document.getElementById('tab-count-unread');
        if (unreadTab) {
            const current = parseInt(unreadTab.textContent) || 0;
            unreadTab.textContent = Math.max(0, current - 1);
        }

        const readTab = document.getElementById('tab-count-read');
        if (readTab) {
            const current = parseInt(readTab.textContent) || 0;
            readTab.textContent = current + 1;
        }

        updateHeaderCounter();
    }

    /**
     * Tăng số đếm chưa đọc trên thanh sidebar, header và tabs ngay lập tức
     */
    function incrementUnreadCountUI() {
        const sidebarBadge = document.getElementById('sidebar-saved-blog-count');
        if (sidebarBadge) {
            const current = parseInt(sidebarBadge.textContent) || 0;
            const next = current + 1;
            sidebarBadge.textContent = next > 99 ? '99+' : next;
            sidebarBadge.classList.remove('hidden');
        }

        const unreadTab = document.getElementById('tab-count-unread');
        if (unreadTab) {
            const current = parseInt(unreadTab.textContent) || 0;
            unreadTab.textContent = current + 1;
        }

        const readTab = document.getElementById('tab-count-read');
        if (readTab) {
            const current = parseInt(readTab.textContent) || 0;
            readTab.textContent = Math.max(0, current - 1);
        }

        updateHeaderCounter();
    }

    /**
     * Đồng bộ số đếm header
     */
    function updateHeaderCounter() {
        const counter = document.getElementById('saved-blogs-counter');
        const unreadEl = document.getElementById('tab-count-unread');
        const allEl = document.getElementById('tab-count-all');
        if (counter && unreadEl && allEl) {
            const u = parseInt(unreadEl.textContent) || 0;
            const a = parseInt(allEl.textContent) || 0;
            if (u > 0) {
                counter.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200/80';
                counter.textContent = `${u} chưa đọc / ${a} bài`;
            } else {
                counter.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200/80';
                counter.textContent = `${a} bài (Đã đọc hết)`;
            }
        }
    }

    /**
     * Bấm vào bài viết để đọc: Chuyển ngay card sang "Đã đọc", giảm badge tức thì
     */
    function handleCardClickToRead(event, blogId) {
        // Cập nhật giao diện ngay lập tức trước khi chuyển trang / mở tab
        applyCardAsReadUI(blogId);

        // Gửi request mark-read trong nền với keepalive để đảm bảo lưu kể cả khi chuyển trang
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            fetch(`/thanh-vien/blog/${blogId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                keepalive: true
            }).catch(e => console.warn(e));
        }
    }

    /**
     * Gỡ bài viết đã lưu khỏi trang danh sách ngay tức thì với hiệu ứng fade mượt
     */
    function handleRemoveSavedCard(blogId, blogSlug, blogTitle) {
        if (!confirm(`Bạn có chắc muốn bỏ lưu bài viết "${blogTitle}"?`)) {
            return;
        }

        const card = document.getElementById(`saved-card-${blogId}`);
        if (card) {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';
        }

        // Cập nhật ngay bộ đếm trên trang
        const badgeContainer = document.getElementById(`read-badge-${blogId}`);
        const wasUnread = badgeContainer && badgeContainer.textContent.includes('Chưa đọc');
        const allTab = document.getElementById('tab-count-all');
        if (allTab) {
            allTab.textContent = Math.max(0, (parseInt(allTab.textContent) || 0) - 1);
        }
        if (wasUnread) {
            const unreadTab = document.getElementById('tab-count-unread');
            if (unreadTab) {
                unreadTab.textContent = Math.max(0, (parseInt(unreadTab.textContent) || 0) - 1);
            }
        } else {
            const readTab = document.getElementById('tab-count-read');
            if (readTab) {
                readTab.textContent = Math.max(0, (parseInt(readTab.textContent) || 0) - 1);
            }
        }
        updateHeaderCounter();

        // Gọi toggleBlogBookmark để đồng bộ localStorage và server
        const dummyBtn = document.createElement('button');
        dummyBtn.dataset.blogId = blogId;
        dummyBtn.dataset.blogSlug = blogSlug;
        dummyBtn.dataset.saved = '1';

        if (window.toggleBlogBookmark) {
            window.toggleBlogBookmark(dummyBtn, blogId, blogSlug, blogTitle);
        }

        setTimeout(() => {
            if (card) {
                card.remove();
            }

            // Kiểm tra xem còn card nào không, nếu hết thì reload để hiển thị empty state
            const remaining = document.querySelectorAll('#saved-blogs-grid article');
            if (!remaining || remaining.length === 0) {
                window.location.reload();
            }
        }, 320);
    }

    /**
     * Đánh dấu bài viết đã đọc / chưa đọc qua nút chuyển đổi nhanh (Không cần load lại trang)
     */
    async function handleToggleReadCard(blogId) {
        const badgeContainer = document.getElementById(`read-badge-${blogId}`);
        const isCurrentlyUnread = badgeContainer && badgeContainer.textContent.includes('Chưa đọc');

        // Optimistic UI update: Cập nhật giao diện ngay lập tức
        if (isCurrentlyUnread) {
            applyCardAsReadUI(blogId);
        } else {
            applyCardAsUnreadUI(blogId);
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        try {
            const res = await fetch(`/thanh-vien/blog/${blogId}/toggle-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data && data.success) {
                // Toast thông báo nhẹ
                if (window.showToast) {
                    window.showToast({
                        title: data.da_doc ? 'Đã đánh dấu đã đọc' : 'Đã đánh dấu chưa đọc',
                        message: data.da_doc ? 'Bài viết đã chuyển sang trạng thái đã đọc.' : 'Bài viết đã chuyển sang trạng thái chưa đọc.',
                        type: 'info',
                        duration: 2000
                    });
                }
            }
        } catch(e) {
            console.error('Error toggling read status:', e);
        }
    }

    // Tự động khôi phục / đồng bộ lại khi người dùng quay lại từ trang bài viết qua nút Back
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
@endpush
