@extends('layouts.app')

@section('title', $blog->seo_title ?? $blog->title)
@section('description', $blog->seo_description ?? Str::limit(strip_tags($blog->excerpt ?? $blog->content), 150))
@section('keywords', $blog->meta_keywords ?? '')

@push('styles')
<style>
    .prose-custom {
        font-size: 1.125rem;
        line-height: 1.8;
    }
    .prose-custom h2, .prose-custom h3 {
        color: var(--color-on-background);
        font-weight: 700;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
    }
    .prose-custom h2 {
        font-size: 1.5rem;
        color: var(--color-primary);
    }
    .prose-custom h3 {
        font-size: 1.25rem;
    }
    .prose-custom p {
        margin-bottom: 1.25em;
        color: var(--color-on-background);
    }
    .prose-custom img {
        border-radius: 0.75rem;
        margin: 1.5em 0;
    }
    .prose-custom ul {
        list-style-type: disc;
        padding-left: 1.5em;
        margin-bottom: 1.5em;
    }
    .prose-custom li {
        margin-bottom: 0.5em;
    }
</style>
@endpush

@section('content')
<!-- Main Content -->
<main class="max-w-[1200px] mx-auto px-container-margin pt-12 md:pt-20 pb-stack-lg flex flex-col md:flex-row gap-stack-lg">
    
    <!-- Article Section (Left Column) -->
    <div class="w-full md:w-2/3">
        <article class="bg-surface-card rounded-xl p-6 md:p-stack-lg shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-outline-variant/30">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 mb-stack-sm text-on-surface-variant font-label-md text-label-md overflow-x-auto whitespace-nowrap pb-2">
                <a class="hover:text-primary transition-colors flex items-center" href="{{ route('home') }}">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                </a>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <a class="hover:text-primary transition-colors" href="{{ route('blog.index') }}">Blog</a>
                @if($blog->category)
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    <a class="hover:text-primary transition-colors" href="{{ route('blog.index', ['category' => $blog->category->slug]) }}">{{ $blog->category->name }}</a>
                @endif
            </div>
            
            <h1 class="font-display-lg text-3xl md:text-[40px] md:leading-[48px] text-on-background mb-stack-md font-bold">
                {{ $blog->title }}
            </h1>
            
            <div class="flex items-center gap-4 mb-stack-lg border-b border-surface-dim pb-stack-sm">
                <a href="{{ $blog->user ? route('blog.author', $blog->user->id) : '#' }}" class="flex items-center gap-3 group/author hover:opacity-95 transition-opacity" title="Xem thông tin tác giả {{ $blog->user?->ho_ten }}">
                    <div class="w-10 h-10 rounded-full bg-surface-container overflow-hidden border border-gray-100 ring-2 ring-transparent group-hover/author:ring-primary/40 transition-all">
                        <img class="w-full h-full object-cover group-hover/author:scale-105 transition-transform" src="{{ $blog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($blog->user->ho_ten ?? 'User').'&background=random' }}" alt="{{ $blog->user->ho_ten ?? 'Tác giả' }}"/>
                    </div>
                    <div>
                        <div class="font-title-md text-[14px] leading-[20px] text-on-background flex items-center gap-1 font-bold group-hover/author:text-primary transition-colors">
                            {{ $blog->user->ho_ten ?? 'Tác giả' }}
                            @if($blog->user && ($blog->user->hasRole('chu_quan') || $blog->user->isAdmin()))
                                <span class="material-symbols-outlined text-tick-xanh text-[16px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                            @endif
                        </div>
                        <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-0.5 group-hover/author:text-primary transition-colors">
                            <span>Tác giả</span>
                            <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                        </div>
                    </div>
                </a>
                <div class="w-px h-6 bg-surface-dim"></div>
                <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1" title="Đăng lúc {{ ($blog->published_at ?? $blog->created_at)?->format('d/m/Y H:i') }}">
                    <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                    <span>{{ ($blog->published_at ?? $blog->created_at)?->format('d/m/Y') ?? 'Mới' }}</span>
                    <span class="text-xs text-slate-400">({{ ($blog->published_at ?? $blog->created_at)?->diffForHumans() ?? 'Vừa xong' }})</span>
                </div>
                <div class="w-px h-6 bg-surface-dim"></div>
                <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1.5 bg-surface-container/60 px-2.5 py-1 rounded-full border border-transparent hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 hover:shadow-xs transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[16px] text-amber-600">visibility</span>
                    <span><strong id="blog-view-count" class="font-semibold text-on-surface">{{ number_format($blog->view_count ?? 0) }}</strong> lượt xem</span>
                </div>
                <div class="w-px h-6 bg-surface-dim"></div>
                <a href="#comments-section" class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-container/60 px-2.5 py-1 rounded-full border border-transparent hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 hover:shadow-xs">
                    <span class="material-symbols-outlined text-[16px] text-primary">forum</span>
                    <span><strong id="blog-header-comment-count" class="font-semibold text-on-surface">{{ number_format($blog->comment_count) }}</strong> bình luận</span>
                </a>
            </div>

            <!-- AI Summarizer Feature Section -->
            <div class="mb-stack-lg border border-primary/25 bg-gradient-to-br from-amber-500/5 via-primary/5 to-purple-500/5 rounded-2xl p-4 md:p-5 transition-all shadow-xs relative overflow-hidden" id="ai-summarizer-card">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background: linear-gradient(135deg, #a04100 0%, #ea580c 100%); color: #ffffff;">
                            <span class="material-symbols-outlined text-xl" style="color: #ffffff;">auto_awesome</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-title-md text-sm md:text-base font-bold text-on-background">AI Tóm tắt bài viết</h3>
                                <span class="bg-primary/10 text-primary text-[10px] font-extrabold px-2 py-0.5 rounded-full border border-primary/20">5s nắm trọn cốt lõi</span>
                            </div>
                            <p class="text-xs text-on-surface-variant mt-0.5">Rút gọn 4 ý chính: Địa chỉ, món nên thử, mức giá & ưu/nhược điểm trong 5 giây.</p>
                        </div>
                    </div>
                    <button type="button" onclick="toggleAiSummary()" id="ai-summary-btn" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-bold shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">bolt</span>
                        <span id="ai-summary-btn-text">Tóm tắt bằng AI</span>
                    </button>
                </div>

                <!-- AI Summary Loading Spinner / Shimmer -->
                <div id="ai-summary-loading" class="hidden mt-4 pt-4 border-t border-primary/10 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-primary animate-pulse">
                        <span class="material-symbols-outlined text-base animate-spin">progress_activity</span>
                        <span>AI đang đọc và tổng hợp nội dung bài viết...</span>
                    </div>
                    <div class="h-3.5 bg-slate-200/80 rounded-full animate-pulse w-3/4"></div>
                    <div class="h-3.5 bg-slate-200/80 rounded-full animate-pulse w-5/6"></div>
                </div>

                <!-- AI Summary Content Details (Expands smoothly) -->
                <div id="ai-summary-content" class="hidden mt-4 pt-4 border-t border-primary/15">
                    <!-- Card Overview TL;DR -->
                    <div class="p-3.5 bg-white/90 backdrop-blur-sm rounded-xl border border-primary/10 mb-3.5 shadow-2xs">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] font-extrabold text-primary flex items-center gap-1 uppercase tracking-wider">
                                <span class="material-symbols-outlined text-[14px]">psychology</span> Tóm lược nhanh (TL;DR)
                            </span>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="speakAiSummary()" id="ai-tts-btn" class="text-[11px] font-semibold text-slate-500 hover:text-primary flex items-center gap-1 px-2 py-0.5 rounded-md hover:bg-slate-100 transition-colors cursor-pointer" title="Đọc to tóm tắt">
                                    <span class="material-symbols-outlined text-[14px]" id="ai-tts-icon">volume_up</span>
                                    <span id="ai-tts-text">Nghe đọc</span>
                                </button>
                                <button type="button" onclick="copyAiSummary()" class="text-[11px] font-semibold text-slate-500 hover:text-primary flex items-center gap-1 px-2 py-0.5 rounded-md hover:bg-slate-100 transition-colors cursor-pointer" title="Sao chép tóm tắt">
                                    <span class="material-symbols-outlined text-[14px]">content_copy</span>
                                    <span>Sao chép</span>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-on-surface leading-relaxed font-medium" id="ai-summary-overview"></p>
                    </div>

                    <!-- 4 Key Pillars Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Pillar 1: Địa chỉ -->
                        <div class="p-3 bg-white/95 rounded-xl border border-outline-variant/30 flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight block">1. Địa chỉ & Khu vực</span>
                                <p class="text-xs font-bold text-slate-800 mt-0.5 leading-snug" id="ai-summary-address"></p>
                            </div>
                        </div>

                        <!-- Pillar 2: Món nên thử -->
                        <div class="p-3 bg-white/95 rounded-xl border border-outline-variant/30 flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[16px]">restaurant_menu</span>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight block">2. Món nên thử (Must-try)</span>
                                <div class="flex flex-wrap gap-1 mt-1" id="ai-summary-musttry"></div>
                            </div>
                        </div>

                        <!-- Pillar 3: Mức giá -->
                        <div class="p-3 bg-white/95 rounded-xl border border-outline-variant/30 flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[16px]">payments</span>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight block">3. Mức giá & Phân khúc</span>
                                <p class="text-xs font-bold text-slate-800 mt-0.5 leading-snug" id="ai-summary-price"></p>
                            </div>
                        </div>

                        <!-- Pillar 4: Ưu & Nhược điểm -->
                        <div class="p-3 bg-white/95 rounded-xl border border-outline-variant/30 flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[16px]">balance</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight block">4. Ưu & Nhược điểm</span>
                                <ul class="text-xs space-y-1 mt-1 text-slate-700" id="ai-summary-proscons"></ul>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Footer -->
                    <div class="mt-3 flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                        <span id="ai-summary-saved-time">Tiết kiệm 95% thời gian đọc (nắm trọn trong 5 giây)</span>
                        <button type="button" onclick="toggleAiSummary(true)" class="hover:text-primary font-medium flex items-center gap-0.5 cursor-pointer">
                            <span>Thu gọn</span>
                            <span class="material-symbols-outlined text-[14px]">expand_less</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hero Image -->
            @if($blog->cover_image)
            <div class="w-full h-[300px] md:h-[400px] rounded-xl overflow-hidden mb-stack-lg shadow-[0_4px_20px_rgba(0,0,0,0.05)]">
                <img class="w-full h-full object-cover" src="{{ Str::startsWith($blog->cover_image, 'http') ? $blog->cover_image : asset('storage/'.$blog->cover_image) }}" alt="{{ $blog->title }}"/>
            </div>
            @endif

            <!-- Article Content -->
            @if($blog->excerpt)
                <p class="text-lg font-medium text-on-surface-variant mb-6 leading-relaxed italic border-l-4 border-primary pl-4">{{ $blog->excerpt }}</p>
            @endif
            
            <div class="prose-custom max-w-none">
                {!! $blog->content !!}
            </div>

            <!-- Tags -->
            @if($blog->tags->count() > 0)
            <div class="mt-stack-lg pt-stack-md border-t border-surface-dim flex flex-wrap gap-2">
                @foreach($blog->tags as $tag)
                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="px-3 py-1 bg-surface-container rounded-full font-label-md text-label-md text-on-surface-variant hover:bg-outline-variant hover:text-on-primary-container cursor-pointer transition-colors">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
            @endif
        </article>
        
        <!-- Comments Section -->
        <section id="comments-section" class="mt-8 bg-surface-card rounded-2xl p-6 md:p-stack-lg shadow-[0_4px_24px_rgba(0,0,0,0.06)] border border-outline-variant/30">
            <div class="flex items-center justify-between mb-6 flex-wrap gap-3 pb-4 border-b border-surface-dim">
                <h3 class="font-title-md text-title-md text-on-background flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-2xl">forum</span>
                    <span>Bình luận (<span id="comments-count-badge">{{ number_format($blog->comment_count) }}</span>)</span>
                </h3>
            </div>

            {{-- Dynamic Alert Banner --}}
            <div id="comment-alert-box" class="hidden mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs">
                <span class="material-symbols-outlined text-xl shrink-0" id="comment-alert-icon">info</span>
                <div class="flex-1">
                    <h5 class="font-extrabold text-sm mb-0.5" id="comment-alert-title">Thông báo</h5>
                    <p id="comment-alert-msg" class="leading-relaxed"></p>
                </div>
                <button type="button" onclick="document.getElementById('comment-alert-box').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
            
            @if(! $blog->allow_comments)
                <div class="p-6 bg-surface-container rounded-2xl mb-6 text-center border border-outline-variant/30">
                    <span class="material-symbols-outlined text-3xl text-text-muted mb-2">lock</span>
                    <p class="text-on-surface-variant font-medium text-sm">Tác giả đã tạm khóa tính năng bình luận cho bài viết này.</p>
                </div>
            @else
                @auth
                    <!-- Main Comment Form -->
                    <form id="blog-comment-form" action="{{ route('blog.comment.store', $blog->slug) }}" method="POST" onsubmit="handleCommentSubmit(event, 'blog-comment-form')" class="mb-8 flex gap-4 items-start">
                        @csrf
                        <img src="{{ auth()->user()->anh_dai_dien_url }}" class="w-10 h-10 rounded-full flex-shrink-0 shadow-sm border border-gray-100 object-cover" alt="{{ auth()->user()->ho_ten }}">
                        <div class="flex-1 space-y-2">
                            <div class="relative">
                                <textarea id="comment-content-input" name="content" rows="3" required placeholder="Chia sẻ suy nghĩ hoặc nhận xét của bạn về bài viết này..." oninput="updateCharCount(this, 'main-char-count')" class="w-full rounded-2xl border border-surface-dim bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary resize-none text-sm p-3.5 outline-none transition-all"></textarea>
                            </div>
                            <div class="flex items-center justify-between flex-wrap gap-2 text-xs">
                                <div class="flex items-center gap-3 text-[11px] text-text-muted">
                                    <span id="main-char-count" class="text-slate-400">0/1000 ký tự</span>
                                    <span class="hidden sm:inline text-slate-400">• Ctrl + Enter để gửi</span>
                                </div>
                                <button type="submit" id="comment-submit-btn" class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-full font-label-md text-label-md transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                                    <span class="material-symbols-outlined text-base">send</span>
                                    <span id="comment-submit-text">Gửi bình luận</span>
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="p-6 bg-surface-container rounded-2xl mb-8 text-center border border-outline-variant/30">
                        <span class="material-symbols-outlined text-3xl text-primary mb-2">account_circle</span>
                        <p class="text-on-surface-variant font-medium text-sm mb-3">Vui lòng đăng nhập tài khoản để tham gia bình luận bài viết.</p>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-6 py-2.5 bg-primary text-white rounded-full font-label-md hover:bg-primary/90 transition-all shadow-xs">
                            <span class="material-symbols-outlined text-base">login</span>
                            Đăng nhập ngay
                        </a>
                    </div>
                @endauth
            @endif
            
            <!-- Comment List -->
            <div id="comments-list-container" class="space-y-4">
                @forelse($blog->rootComments as $comment)
                    <div id="comment-block-{{ $comment->id }}" class="comment-item p-4 rounded-2xl bg-white hover:bg-surface-container/20 transition-all border border-surface-dim/60 shadow-2xs">
                        <div class="flex gap-3.5 items-start">
                            <img src="{{ $comment->user->anh_dai_dien_url }}" class="w-10 h-10 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs" alt="{{ $comment->user->ho_ten }}">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1 flex-wrap">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-title-md text-[14px] font-bold text-on-background">{{ $comment->user->ho_ten }}</span>
                                        
                                        @if($comment->user_id === $blog->user_id)
                                            <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5 border border-primary/20">
                                                <span class="material-symbols-outlined text-[12px]">edit_note</span>
                                                Tác giả
                                            </span>
                                        @endif

                                        @if($comment->user->isAdmin())
                                            <span class="bg-purple-50 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5 border border-purple-200">
                                                <span class="material-symbols-outlined text-[12px]">shield_person</span>
                                                Quản trị viên
                                            </span>
                                        @endif

                                        <span class="text-xs text-on-surface-variant">• {{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <p class="text-on-surface font-body-sm whitespace-pre-line text-sm leading-relaxed">{{ $comment->content }}</p>
                                
                                <!-- Comment Actions: Like, Reply, Delete -->
                                <div class="flex items-center gap-4 mt-2.5 pt-2 text-xs text-on-surface-variant font-medium">
                                    @auth
                                        @php $isLiked = $comment->isLikedBy(auth()->user()); @endphp
                                        <button type="button" onclick="handleLikeComment({{ $comment->id }})" id="like-btn-{{ $comment->id }}" class="flex items-center gap-1 transition-colors {{ $isLiked ? 'text-rose-600 font-bold' : 'text-slate-500 hover:text-rose-600' }}">
                                            <span class="material-symbols-outlined text-[16px] {{ $isLiked ? 'fill-1' : '' }}" style="font-variation-settings: 'FILL' {{ $isLiked ? '1' : '0' }};" id="like-icon-{{ $comment->id }}">favorite</span>
                                            <span id="like-count-{{ $comment->id }}">{{ $comment->likes_count > 0 ? $comment->likes_count : 'Thích' }}</span>
                                        </button>

                                        @if($blog->allow_comments)
                                            <button type="button" onclick="toggleReplyBox({{ $comment->id }}, '{{ addslashes($comment->user->ho_ten) }}')" class="flex items-center gap-1 text-slate-500 hover:text-primary transition-colors">
                                                <span class="material-symbols-outlined text-[16px]">reply</span>
                                                <span>Trả lời</span>
                                            </button>
                                        @endif

                                        @if(auth()->id() === $comment->user_id || auth()->id() === $blog->user_id || auth()->user()->isAdmin())
                                            <button type="button" onclick="openDeleteModal({{ $comment->id }})" class="flex items-center gap-1 text-slate-400 hover:text-rose-600 transition-colors ml-auto">
                                                <span class="material-symbols-outlined text-[15px]">delete</span>
                                                <span>Xóa</span>
                                            </button>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="flex items-center gap-1 text-slate-500 hover:text-rose-600 transition-colors">
                                            <span class="material-symbols-outlined text-[16px]">favorite</span>
                                            <span>{{ $comment->likes_count > 0 ? $comment->likes_count : 'Thích' }}</span>
                                        </a>
                                        <a href="{{ route('login') }}" class="flex items-center gap-1 text-slate-500 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-[16px]">reply</span>
                                            <span>Trả lời</span>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>

                        <!-- Inline Reply Box for this Comment -->
                        @auth
                            <div id="reply-box-{{ $comment->id }}" class="hidden mt-3 pl-10 pr-2 pt-2 border-t border-surface-dim">
                                <form id="reply-form-{{ $comment->id }}" action="{{ route('blog.comment.store', $blog->slug) }}" method="POST" onsubmit="handleCommentSubmit(event, 'reply-form-{{ $comment->id }}', {{ $comment->id }})" class="flex gap-3 items-start">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <img src="{{ auth()->user()->anh_dai_dien_url }}" class="w-8 h-8 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs" alt="{{ auth()->user()->ho_ten }}">
                                    <div class="flex-1 space-y-2">
                                        <textarea id="reply-input-{{ $comment->id }}" name="content" rows="2" required placeholder="Trả lời @{{ $comment->user->ho_ten }}..." oninput="updateCharCount(this, 'char-count-{{ $comment->id }}')" class="w-full rounded-xl border border-surface-dim bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary resize-none text-xs p-3 outline-none transition-all"></textarea>
                                        <div class="flex items-center justify-between flex-wrap gap-2 text-xs">
                                            <span id="char-count-{{ $comment->id }}" class="text-[11px] text-slate-400">0/1000 ký tự</span>
                                            <div class="flex items-center gap-2">
                                                <button type="button" onclick="toggleReplyBox({{ $comment->id }})" class="px-3 py-1.5 rounded-full text-slate-500 hover:bg-slate-100 font-medium transition-colors cursor-pointer text-xs">
                                                    Hủy
                                                </button>
                                                <button type="submit" id="reply-btn-{{ $comment->id }}" class="px-4 py-1.5 bg-primary hover:bg-primary/90 text-white rounded-full font-label-sm transition-all shadow-xs flex items-center gap-1 cursor-pointer text-xs">
                                                    <span class="material-symbols-outlined text-sm">send</span>
                                                    <span id="reply-btn-text-{{ $comment->id }}">Gửi phản hồi</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endauth

                        <!-- Nested Replies List -->
                        <div id="replies-list-{{ $comment->id }}" class="space-y-3 mt-3 pl-8 sm:pl-10 {{ $comment->replies->count() > 0 ? 'pt-2' : '' }}">
                            @foreach($comment->replies as $reply)
                                <div id="comment-block-{{ $reply->id }}" class="reply-item p-3.5 rounded-xl bg-surface-container/30 border border-surface-dim/70 flex gap-3 items-start transition-all">
                                    <img src="{{ $reply->user->anh_dai_dien_url }}" class="w-8 h-8 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs" alt="{{ $reply->user->ho_ten }}">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2 mb-1 flex-wrap">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-title-md text-[13px] font-bold text-on-background">{{ $reply->user->ho_ten }}</span>
                                                
                                                @if($reply->user_id === $blog->user_id)
                                                    <span class="bg-primary/10 text-primary text-[9px] font-bold px-1.5 py-0.2 rounded-full flex items-center gap-0.5 border border-primary/20">
                                                        <span class="material-symbols-outlined text-[11px]">edit_note</span>
                                                        Tác giả
                                                    </span>
                                                @endif

                                                @if($reply->user->isAdmin())
                                                    <span class="bg-purple-50 text-purple-700 text-[9px] font-bold px-1.5 py-0.2 rounded-full flex items-center gap-0.5 border border-purple-200">
                                                        <span class="material-symbols-outlined text-[11px]">shield_person</span>
                                                        Quản trị viên
                                                    </span>
                                                @endif

                                                <span class="text-[11px] text-on-surface-variant">• {{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <p class="text-on-surface font-body-sm whitespace-pre-line text-xs leading-relaxed">{{ $reply->content }}</p>
                                        
                                        <!-- Reply Actions -->
                                        <div class="flex items-center gap-3 mt-2 text-[11px] text-on-surface-variant font-medium">
                                            @auth
                                                @php $isReplyLiked = $reply->isLikedBy(auth()->user()); @endphp
                                                <button type="button" onclick="handleLikeComment({{ $reply->id }})" id="like-btn-{{ $reply->id }}" class="flex items-center gap-0.5 transition-colors {{ $isReplyLiked ? 'text-rose-600 font-bold' : 'text-slate-500 hover:text-rose-600' }}">
                                                    <span class="material-symbols-outlined text-[14px] {{ $isReplyLiked ? 'fill-1' : '' }}" style="font-variation-settings: 'FILL' {{ $isReplyLiked ? '1' : '0' }};" id="like-icon-{{ $reply->id }}">favorite</span>
                                                    <span id="like-count-{{ $reply->id }}">{{ $reply->likes_count > 0 ? $reply->likes_count : 'Thích' }}</span>
                                                </button>

                                                @if($blog->allow_comments)
                                                    <button type="button" onclick="toggleReplyBox({{ $comment->id }}, '{{ addslashes($reply->user->ho_ten) }}')" class="flex items-center gap-0.5 text-slate-500 hover:text-primary transition-colors">
                                                        <span class="material-symbols-outlined text-[14px]">reply</span>
                                                        <span>Trả lời</span>
                                                    </button>
                                                @endif

                                                @if(auth()->id() === $reply->user_id || auth()->id() === $blog->user_id || auth()->user()->isAdmin())
                                                    <button type="button" onclick="openDeleteModal({{ $reply->id }})" class="flex items-center gap-0.5 text-slate-400 hover:text-rose-600 transition-colors ml-auto">
                                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                                        <span>Xóa</span>
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}" class="flex items-center gap-0.5 text-slate-500 hover:text-rose-600 transition-colors">
                                                    <span class="material-symbols-outlined text-[14px]">favorite</span>
                                                    <span>{{ $reply->likes_count > 0 ? $reply->likes_count : 'Thích' }}</span>
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div id="comments-empty-state" class="p-8 text-center rounded-2xl bg-surface-container/20 border border-dashed border-outline-variant/40">
                        <span class="material-symbols-outlined text-4xl text-text-muted mb-2">forum</span>
                        <p class="text-sm font-bold text-slate-700">Chưa có bình luận nào cho bài viết này</p>
                        <p class="text-xs text-text-muted mt-1">Hãy là người đầu tiên chia sẻ cảm nghĩ và nhận xét của bạn!</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Delete Comment Confirmation Modal -->
        <div id="delete-comment-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs hidden transition-all">
            <div class="bg-surface-card rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-outline-variant/40 animate-in fade-in zoom-in-95 duration-200">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mb-4 mx-auto">
                    <span class="material-symbols-outlined text-2xl">delete_forever</span>
                </div>
                <h4 class="font-title-md text-center text-on-background text-lg font-bold mb-2">Xác nhận xóa bình luận?</h4>
                <p class="text-xs text-on-surface-variant text-center mb-6 leading-relaxed">
                    Bình luận này sẽ bị xóa vĩnh viễn khỏi bài viết cùng với các phản hồi liên quan. Hành động này không thể hoàn tác.
                </p>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 py-2.5 rounded-full border border-surface-dim font-label-md text-xs font-semibold text-slate-700 hover:bg-surface-container transition-colors cursor-pointer">
                        Hủy bỏ
                    </button>
                    <button type="button" id="confirm-delete-btn" onclick="executeDeleteComment()" class="flex-1 py-2.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white font-label-md text-xs font-semibold transition-all shadow-xs cursor-pointer">
                        Xóa bình luận
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Related Articles -->
        <section class="mt-12 pt-8 border-t border-surface-dim">
            <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">auto_stories</span>
                    </div>
                    <div>
                        <h3 class="font-display-lg text-xl sm:text-2xl text-on-background font-bold">Bài viết liên quan</h3>
                        <p class="text-xs text-on-surface-variant mt-0.5">Khám phá các bài viết cùng chủ đề ẩm thực hấp dẫn</p>
                    </div>
                </div>
                @if($relatedBlogs->count() > 0)
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-surface-container text-slate-600 border border-outline-variant/30">
                        {{ $relatedBlogs->count() }} bài gợi ý
                    </span>
                @endif
            </div>

            @if($relatedBlogs->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                    @foreach($relatedBlogs as $related)
                        <a href="{{ route('blog.show', $related->slug) }}" class="bg-surface-card rounded-2xl overflow-hidden shadow-xs hover:shadow-xl border border-outline-variant/30 hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 transition-all duration-300 hover:-translate-y-1.5 flex flex-col group">
                            {{-- Cover Image --}}
                            <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                                <img src="{{ $related->cover_image ? (Str::startsWith($related->cover_image, 'http') ? $related->cover_image : asset('storage/'.$related->cover_image)) : 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80' }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" 
                                     alt="{{ $related->title }}"
                                     loading="lazy"
                                     onerror="this.src='https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80'">
                                
                                {{-- Category Badge --}}
                                @if($related->category)
                                    <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md text-amber-700 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-xs border border-amber-200/60 uppercase tracking-wider">
                                        {{ $related->category->name }}
                                    </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between gap-3">
                                <div>
                                    <h4 class="font-title-md text-[15px] sm:text-[16px] leading-snug text-on-background font-bold line-clamp-2 group-hover:text-primary transition-colors">
                                        {{ $related->title }}
                                    </h4>
                                    @if($related->excerpt)
                                        <p class="text-xs text-on-surface-variant line-clamp-2 leading-relaxed mt-2 text-slate-500 font-normal">
                                            {{ $related->excerpt }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Meta footer: Author, Date, Views --}}
                                <div class="pt-3 border-t border-surface-dim/70 flex items-center justify-between text-xs text-on-surface-variant gap-2 flex-wrap">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <img class="w-6 h-6 rounded-full object-cover border border-gray-100 shrink-0" 
                                             src="{{ $related->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($related->user->ho_ten ?? 'User').'&background=random' }}" 
                                             alt="{{ $related->user->ho_ten ?? 'Tác giả' }}">
                                        <span class="font-semibold text-slate-700 truncate max-w-[110px] text-xs">
                                            {{ $related->user->ho_ten ?? 'Tác giả' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0 text-[11px] text-slate-400 font-medium">
                                        <span title="{{ ($related->published_at ?? $related->created_at)?->format('d/m/Y H:i') }}">
                                            {{ ($related->published_at ?? $related->created_at)?->diffForHumans() ?? 'Mới' }}
                                        </span>
                                        <span class="flex items-center gap-0.5 text-slate-500" title="{{ number_format($related->view_count ?? 0) }} lượt xem">
                                            <span class="material-symbols-outlined text-[13px] text-amber-500">visibility</span>
                                            <span>{{ number_format($related->view_count ?? 0) }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center rounded-2xl bg-surface-container/20 border border-dashed border-outline-variant/40">
                    <span class="material-symbols-outlined text-3xl text-text-muted mb-1">auto_stories</span>
                    <p class="text-xs text-on-surface-variant">Hiện chưa có thêm bài viết liên quan nào khác.</p>
                </div>
            @endif
        </section>
        
    </div>
    
    <!-- Sidebar (Right Column) -->
    <aside class="w-full md:w-1/3 space-y-stack-lg">
        
        <!-- Popular Articles -->
        <div class="bg-surface-card rounded-xl p-stack-md shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-outline-variant/30">
            <h3 class="font-title-md text-title-md text-on-background mb-stack-md border-l-4 border-primary pl-2">Bài viết phổ biến</h3>
            
            @foreach(\App\Models\Blog::where('status', 'published')->orderBy('view_count', 'desc')->limit(5)->get() as $popularBlog)
                <a href="{{ route('blog.show', $popularBlog->slug) }}" class="flex gap-3 mb-stack-md group cursor-pointer last:mb-0">
                    <div class="w-20 h-20 rounded-lg overflow-hidden shrink-0">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="{{ $popularBlog->cover_image ? (Str::startsWith($popularBlog->cover_image, 'http') ? $popularBlog->cover_image : asset('storage/'.$popularBlog->cover_image)) : 'https://placehold.co/100x100/png' }}" alt="{{ $popularBlog->title }}"/>
                    </div>
                    <div>
                        <h4 class="font-title-md text-[14px] leading-[20px] text-on-background group-hover:text-primary transition-colors line-clamp-2 mb-1">
                            {{ $popularBlog->title }}
                        </h4>
                        <div class="font-label-sm text-label-sm text-on-surface-variant">{{ ($popularBlog->published_at ?? $popularBlog->created_at)?->format('d/m/Y') ?? 'Mới' }}</div>
                    </div>
                </a>
            @endforeach
        </div>
        
        <!-- Hot Topics -->
        <div class="bg-surface-card rounded-xl p-stack-md shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-outline-variant/30">
            <h3 class="font-title-md text-title-md text-on-background mb-stack-md border-l-4 border-primary pl-2">Chủ đề hot</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $idx => $tag)
                    @if($idx % 4 == 0)
                        <a class="px-3 py-1 bg-secondary-fixed text-on-secondary-fixed-variant rounded-full font-label-md text-label-md hover:bg-secondary hover:text-on-secondary transition-colors" href="{{ route('blog.index', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a>
                    @elseif($idx % 4 == 1)
                        <a class="px-3 py-1 bg-primary-fixed text-on-primary-fixed-variant rounded-full font-label-md text-label-md hover:bg-primary hover:text-on-primary transition-colors" href="{{ route('blog.index', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a>
                    @else
                        <a class="px-3 py-1 bg-surface-container rounded-full font-label-md text-label-md text-on-surface-variant hover:bg-outline-variant hover:text-on-primary-container transition-colors" href="{{ route('blog.index', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        
        <!-- Newsletter -->
        <div class="bg-primary-container rounded-xl p-stack-md shadow-[0_4px_20px_rgba(0,0,0,0.05)] text-on-primary-container text-center relative overflow-hidden">
            <!-- Decorative abstract background pattern -->
            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0); background-size: 16px 16px;"></div>
            <span class="material-symbols-outlined text-[40px] mb-2 z-10 relative text-white">mail</span>
            <h3 class="font-title-md text-title-md mb-2 z-10 relative text-white font-bold">Đừng bỏ lỡ quán ngon!</h3>
            <p class="font-body-sm text-body-sm mb-4 z-10 relative opacity-90 text-white/90">Đăng ký để nhận danh sách review ẩm thực mới nhất mỗi tuần từ Quán Mới.</p>

            @if(session('newsletter_success'))
                <div class="mb-3 p-3 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-xl text-xs font-bold flex items-center gap-2 text-left z-10 relative shadow-2xs">
                    <span class="material-symbols-outlined text-base text-emerald-600">check_circle</span>
                    <span>{{ session('newsletter_success') }}</span>
                </div>
            @elseif(session('newsletter_info'))
                <div class="mb-3 p-3 bg-amber-100 text-amber-900 border border-amber-300 rounded-xl text-xs font-bold flex items-center gap-2 text-left z-10 relative shadow-2xs">
                    <span class="material-symbols-outlined text-base text-amber-600">info</span>
                    <span>{{ session('newsletter_info') }}</span>
                </div>
            @endif

            <form id="blog-show-newsletter-form" action="{{ route('blog.newsletter.subscribe') }}" method="POST" onsubmit="handleNewsletterSubmit(event, this)" class="flex flex-col gap-2.5 z-10 relative">
                @csrf
                <input type="hidden" name="source" value="blog_show">
                <input class="w-full rounded-lg border-none bg-surface-container-lowest focus:ring-2 focus:ring-white px-4 py-3 font-body-sm text-body-sm text-on-background placeholder:text-text-muted outline-none text-slate-800" 
                       name="email" 
                       value="{{ auth()->check() ? auth()->user()->email : old('email') }}" 
                       placeholder="Email của bạn..." 
                       type="email" 
                       required/>
                <button class="w-full bg-white text-primary rounded-lg py-3 font-label-md text-label-md font-bold hover:bg-slate-100 transition-all active:scale-[0.98] shadow-sm flex items-center justify-center gap-1.5 cursor-pointer" type="submit">
                     <span class="btn-text">Đăng ký ngay</span>
                     <span class="material-symbols-outlined text-base">send</span>
                </button>
                <div class="newsletter-feedback hidden text-xs font-semibold p-2.5 rounded-xl text-left"></div>
            </form>
        </div>
        
    </aside>
</main>
@endsection

@push('scripts')
<script>
    let activeDeleteCommentId = null;

    // Character counter helper
    function updateCharCount(el, targetId) {
        const len = el.value.length;
        const target = document.getElementById(targetId);
        if (target) {
            target.textContent = `${len}/1000 ký tự`;
            if (len > 900) {
                target.classList.add('text-amber-600', 'font-bold');
            } else {
                target.classList.remove('text-amber-600', 'font-bold');
            }
        }
    }

    // Toggle reply form visibility
    function toggleReplyBox(commentId, authorName = '') {
        const box = document.getElementById(`reply-box-${commentId}`);
        if (!box) return;
        
        const isHidden = box.classList.contains('hidden');
        // Hide all other open reply boxes
        document.querySelectorAll('[id^="reply-box-"]').forEach(b => {
            if (b.id !== `reply-box-${commentId}`) b.classList.add('hidden');
        });

        if (isHidden) {
            hideAlertBox();
            box.classList.remove('hidden');
            const input = document.getElementById(`reply-input-${commentId}`);
            if (input) {
                if (authorName && !input.value.trim()) {
                    input.value = `@${authorName} `;
                }
                input.focus();
                // Move cursor to end
                input.setSelectionRange(input.value.length, input.value.length);
            }
        } else {
            box.classList.add('hidden');
        }
    }

    // Auto-dismiss alert banner when user focuses or types new input
    function hideAlertBox() {
        const alertBox = document.getElementById('comment-alert-box');
        if (alertBox && !alertBox.classList.contains('hidden')) {
            alertBox.classList.add('hidden');
        }
    }

    // Dismiss old notifications immediately when typing or clicking into any comment/reply input
    document.addEventListener('input', function(e) {
        if (e.target && e.target.tagName === 'TEXTAREA') {
            hideAlertBox();
        }
    });

    document.addEventListener('focusin', function(e) {
        if (e.target && e.target.tagName === 'TEXTAREA') {
            hideAlertBox();
        }
    });

    // Open & Close Delete Confirmation Modal
    function openDeleteModal(commentId) {
        hideAlertBox();
        activeDeleteCommentId = commentId;
        const modal = document.getElementById('delete-comment-modal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        activeDeleteCommentId = null;
        const modal = document.getElementById('delete-comment-modal');
        if (modal) modal.classList.add('hidden');
    }

    // Execute Delete
    function executeDeleteComment() {
        if (!activeDeleteCommentId) return;
        const targetCommentId = activeDeleteCommentId;

        const btn = document.getElementById('confirm-delete-btn');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Đang xóa...';
        }

        const deleteUrl = "{{ url('/blog/' . $blog->slug . '/binh-luan') }}/" + targetCommentId;

        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Xóa bình luận';
            }
            closeDeleteModal();

            if (data.success) {
                const el = document.getElementById(`comment-block-${targetCommentId}`);
                if (el) {
                    el.style.transition = 'all 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-8px) scale(0.96)';
                    el.style.maxHeight = el.scrollHeight + 'px';
                    setTimeout(() => {
                        el.style.maxHeight = '0px';
                        el.style.paddingTop = '0px';
                        el.style.paddingBottom = '0px';
                        el.style.marginTop = '0px';
                        el.style.marginBottom = '0px';
                        el.style.overflow = 'hidden';
                    }, 50);
                    setTimeout(() => {
                        el.remove();
                        // Check if list is empty
                        const list = document.getElementById('comments-list-container');
                        if (list && list.querySelectorAll('.comment-item').length === 0) {
                            list.innerHTML = `
                                <div id="comments-empty-state" class="p-8 text-center rounded-2xl bg-surface-container/20 border border-dashed border-outline-variant/40 animate-in fade-in duration-300">
                                    <span class="material-symbols-outlined text-4xl text-text-muted mb-2">forum</span>
                                    <p class="text-sm font-bold text-slate-700">Chưa có bình luận nào cho bài viết này</p>
                                    <p class="text-xs text-text-muted mt-1">Hãy là người đầu tiên chia sẻ cảm nghĩ và nhận xét của bạn!</p>
                                </div>
                            `;
                        }
                    }, 350);
                }

                const badge = document.getElementById('comments-count-badge');
                if (badge && data.comment_count !== undefined) {
                    badge.textContent = data.comment_count;
                }
                const headerBadge = document.getElementById('blog-header-comment-count');
                if (headerBadge && data.comment_count !== undefined) {
                    headerBadge.textContent = data.comment_count;
                }

                showAlert('info', 'Thông báo', 'Đã xóa bình luận thành công.');
            } else {
                showAlert('error', 'Lỗi xóa bình luận', data.message || 'Không thể xóa bình luận.');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Xóa bình luận';
            }
            closeDeleteModal();
            showAlert('error', 'Lỗi hệ thống', 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.');
        });
    }

    // Toggle Like on Comment
    function handleLikeComment(commentId) {
        const likeUrl = "{{ url('/blog/' . $blog->slug . '/binh-luan') }}/" + commentId + "/thich";
        const btn = document.getElementById(`like-btn-${commentId}`);
        const icon = document.getElementById(`like-icon-${commentId}`);
        const countSpan = document.getElementById(`like-count-${commentId}`);

        fetch(likeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.liked) {
                    btn.className = 'flex items-center gap-1 transition-colors text-rose-600 font-bold';
                    icon.textContent = 'favorite';
                    icon.classList.add('fill-1');
                    icon.style.fontVariationSettings = "'FILL' 1";
                } else {
                    btn.className = 'flex items-center gap-1 transition-colors text-slate-500 hover:text-rose-600';
                    icon.textContent = 'favorite';
                    icon.classList.remove('fill-1');
                    icon.style.fontVariationSettings = "'FILL' 0";
                }
                countSpan.textContent = data.likes_count > 0 ? data.likes_count : 'Thích';
            }
        })
        .catch(err => {
            console.error('Like error:', err);
        });
    }

    // Alert display helper
    // Alert display helper
    function showAlert(type, title, message) {
        const alertBox = document.getElementById('comment-alert-box');
        const alertTitle = document.getElementById('comment-alert-title');
        const alertMsg = document.getElementById('comment-alert-msg');
        const alertIcon = document.getElementById('comment-alert-icon');
        if (!alertBox) return;

        if (type === 'success') {
            alertBox.className = 'mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs bg-emerald-50 border border-emerald-200 text-emerald-800 animate-in fade-in duration-300';
            alertIcon.className = 'material-symbols-outlined text-xl text-emerald-600 shrink-0';
            alertIcon.textContent = 'check_circle';
        } else if (type === 'moderated') {
            alertBox.className = 'mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs bg-rose-50 border border-rose-200 text-rose-800 animate-in shake duration-300';
            alertIcon.className = 'material-symbols-outlined text-xl text-rose-600 shrink-0';
            alertIcon.textContent = 'error';
        } else {
            alertBox.className = 'mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs bg-amber-50 border border-amber-200 text-amber-800 animate-in fade-in duration-300';
            alertIcon.className = 'material-symbols-outlined text-xl text-amber-600 shrink-0';
            alertIcon.textContent = 'info';
        }

        alertTitle.textContent = title;
        alertMsg.textContent = message;
        alertBox.classList.remove('hidden');

        alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Handle Comment / Reply Submission
    function handleCommentSubmit(e, formId, parentId = null) {
        e.preventDefault();
        hideAlertBox();
        const form = document.getElementById(formId);
        if (!form) return;

        const isReply = (parentId !== null);
        const textarea = isReply 
            ? document.getElementById(`reply-input-${parentId}`)
            : document.getElementById('comment-content-input');
        const btn = isReply 
            ? document.getElementById(`reply-btn-${parentId}`)
            : document.getElementById('comment-submit-btn');
        const btnText = isReply 
            ? document.getElementById(`reply-btn-text-${parentId}`)
            : document.getElementById('comment-submit-text');
        const counterBadge = document.getElementById('comments-count-badge');
        const emptyState = document.getElementById('comments-empty-state');
        const listContainer = document.getElementById('comments-list-container');

        const content = textarea.value.trim();
        if (!content) return;

        // Loading state
        btn.disabled = true;
        const originalText = btnText.textContent;
        btnText.textContent = 'Đang gửi...';
        btn.classList.add('opacity-75');

        const payload = { content: content };
        if (parentId) {
            payload.parent_id = parentId;
        }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => {
            return res.json().then(data => ({ status: res.status, body: data })).catch(() => ({
                status: res.status,
                body: { success: false, message: 'Phản hồi từ máy chủ không hợp lệ (' + res.status + ')' }
            }));
        })
        .then(({ status, body }) => {
            btn.disabled = false;
            btnText.textContent = originalText;
            btn.classList.remove('opacity-75');

            if (status === 200 && body.success) {
                showAlert('success', 'Thành công', body.message || 'Bình luận đã được đăng thành công!');
                textarea.value = '';

                // Reset char counter
                if (!isReply) {
                    const charSpan = document.getElementById('main-char-count');
                    if (charSpan) charSpan.textContent = '0/1000 ký tự';
                }

                // Remove empty state if visible
                if (emptyState) emptyState.remove();

                const c = body.comment;
                const authorBadge = c.is_author 
                    ? `<span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5 border border-primary/20"><span class="material-symbols-outlined text-[12px]">edit_note</span>Tác giả</span>`
                    : '';
                const adminBadge = c.is_admin 
                    ? `<span class="bg-purple-50 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5 border border-purple-200"><span class="material-symbols-outlined text-[12px]">shield_person</span>Quản trị viên</span>`
                    : '';

                if (isReply) {
                    // Hide reply form
                    const replyBox = document.getElementById(`reply-box-${parentId}`);
                    if (replyBox) replyBox.classList.add('hidden');

                    // Append reply
                    const repliesList = document.getElementById(`replies-list-${parentId}`);
                    const newReplyHtml = `
                        <div id="comment-block-${c.id}" class="reply-item p-3.5 rounded-xl bg-surface-container/30 border border-surface-dim/70 flex gap-3 items-start transition-all animate-in fade-in duration-300">
                            <img src="${c.user_avatar}" class="w-8 h-8 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs" alt="${c.user_name}">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1 flex-wrap">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-title-md text-[13px] font-bold text-on-background">${c.user_name}</span>
                                        ${authorBadge}
                                        ${adminBadge}
                                        <span class="text-[11px] text-on-surface-variant">• ${c.created_at_human}</span>
                                    </div>
                                </div>
                                <p class="text-on-surface font-body-sm whitespace-pre-line text-xs leading-relaxed">${c.content}</p>
                                <div class="flex items-center gap-3 mt-2 text-[11px] text-on-surface-variant font-medium">
                                    <button type="button" onclick="handleLikeComment(${c.id})" id="like-btn-${c.id}" class="flex items-center gap-0.5 transition-colors text-slate-500 hover:text-rose-600">
                                        <span class="material-symbols-outlined text-[14px]" id="like-icon-${c.id}">favorite</span>
                                        <span id="like-count-${c.id}">Thích</span>
                                    </button>
                                    <button type="button" onclick="toggleReplyBox(${parentId}, '${c.user_name}')" class="flex items-center gap-0.5 text-slate-500 hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[14px]">reply</span>
                                        <span>Trả lời</span>
                                    </button>
                                    <button type="button" onclick="openDeleteModal(${c.id})" class="flex items-center gap-0.5 text-slate-400 hover:text-rose-600 transition-colors ml-auto">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        <span>Xóa</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    if (repliesList) {
                        repliesList.insertAdjacentHTML('beforeend', newReplyHtml);
                    }
                } else {
                    // Prepend root comment
                    const newCommentHtml = `
                        <div id="comment-block-${c.id}" class="comment-item p-4 rounded-2xl bg-white hover:bg-surface-container/20 transition-all border border-surface-dim/60 shadow-2xs animate-in fade-in duration-300">
                            <div class="flex gap-3.5 items-start">
                                <img src="${c.user_avatar}" class="w-10 h-10 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs" alt="${c.user_name}">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1 flex-wrap">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-title-md text-[14px] font-bold text-on-background">${c.user_name}</span>
                                            ${authorBadge}
                                            ${adminBadge}
                                            <span class="text-xs text-on-surface-variant">• ${c.created_at_human}</span>
                                        </div>
                                    </div>
                                    <p class="text-on-surface font-body-sm whitespace-pre-line text-sm leading-relaxed">${c.content}</p>
                                    <div class="flex items-center gap-4 mt-2.5 pt-2 text-xs text-on-surface-variant font-medium">
                                        <button type="button" onclick="handleLikeComment(${c.id})" id="like-btn-${c.id}" class="flex items-center gap-1 transition-colors text-slate-500 hover:text-rose-600">
                                            <span class="material-symbols-outlined text-[16px]" id="like-icon-${c.id}">favorite</span>
                                            <span id="like-count-${c.id}">Thích</span>
                                        </button>
                                        <button type="button" onclick="toggleReplyBox(${c.id}, '${c.user_name}')" class="flex items-center gap-1 text-slate-500 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-[16px]">reply</span>
                                            <span>Trả lời</span>
                                        </button>
                                        <button type="button" onclick="openDeleteModal(${c.id})" class="flex items-center gap-1 text-slate-400 hover:text-rose-600 transition-colors ml-auto">
                                            <span class="material-symbols-outlined text-[15px]">delete</span>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Inline Reply Box -->
                            <div id="reply-box-${c.id}" class="hidden mt-3 pl-10 pr-2 pt-2 border-t border-surface-dim">
                                <form id="reply-form-${c.id}" action="{{ route('blog.comment.store', $blog->slug) }}" method="POST" onsubmit="handleCommentSubmit(event, 'reply-form-${c.id}', ${c.id})" class="flex gap-3 items-start">
                                    <input type="hidden" name="parent_id" value="${c.id}">
                                    <img src="${c.user_avatar}" class="w-8 h-8 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs">
                                    <div class="flex-1 space-y-2">
                                        <textarea id="reply-input-${c.id}" name="content" rows="2" required placeholder="Trả lời @${c.user_name}..." oninput="updateCharCount(this, 'char-count-${c.id}')" class="w-full rounded-xl border border-surface-dim bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary resize-none text-xs p-3 outline-none transition-all"></textarea>
                                        <div class="flex items-center justify-between flex-wrap gap-2 text-xs">
                                            <span id="char-count-${c.id}" class="text-[11px] text-slate-400">0/1000 ký tự</span>
                                            <div class="flex items-center gap-2">
                                                <button type="button" onclick="toggleReplyBox(${c.id})" class="px-3 py-1.5 rounded-full text-slate-500 hover:bg-slate-100 font-medium transition-colors cursor-pointer text-xs">
                                                    Hủy
                                                </button>
                                                <button type="submit" id="reply-btn-${c.id}" class="px-4 py-1.5 bg-primary hover:bg-primary/90 text-white rounded-full font-label-sm transition-all shadow-xs flex items-center gap-1 cursor-pointer text-xs">
                                                    <span class="material-symbols-outlined text-sm">send</span>
                                                    <span id="reply-btn-text-${c.id}">Gửi phản hồi</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div id="replies-list-${c.id}" class="space-y-3 mt-3 pl-8 sm:pl-10"></div>
                        </div>
                    `;
                    listContainer.insertAdjacentHTML('afterbegin', newCommentHtml);
                }

                if (counterBadge && body.comment_count !== undefined) {
                    counterBadge.textContent = body.comment_count;
                }
                const headerBadge = document.getElementById('blog-header-comment-count');
                if (headerBadge && body.comment_count !== undefined) {
                    headerBadge.textContent = body.comment_count;
                }
            } else {
                showAlert('moderated', 'Từ không hợp lệ', body.message || 'Từ không hợp lệ.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.textContent = originalText;
            btn.classList.remove('opacity-75');
            showAlert('error', 'Lỗi gửi bình luận', 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.');
        });
    }

    // Ctrl + Enter shortcut to quickly submit comment
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            const activeEl = document.activeElement;
            if (activeEl && activeEl.tagName === 'TEXTAREA') {
                const form = activeEl.closest('form');
                if (form) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }
        }
    });

    // ─── AI Summarizer Logic ───────────────────────────────────────────────
    let currentSummaryData = null;
    let isSpeaking = false;

    function toggleAiSummary(forceClose = false) {
        const card = document.getElementById('ai-summary-content');
        const btnText = document.getElementById('ai-summary-btn-text');
        const loading = document.getElementById('ai-summary-loading');

        if (forceClose || !card.classList.contains('hidden')) {
            card.classList.add('hidden');
            btnText.textContent = 'Tóm tắt bằng AI';
            if (window.speechSynthesis) window.speechSynthesis.cancel();
            return;
        }

        if (currentSummaryData) {
            card.classList.remove('hidden');
            btnText.textContent = 'Thu gọn tóm tắt';
            return;
        }

        // Fetch summary via AJAX
        loading.classList.remove('hidden');
        btnText.textContent = 'Đang tóm tắt...';

        fetch("{{ route('blog.ai.summarize', $blog->slug) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            loading.classList.add('hidden');
            if (data.success) {
                currentSummaryData = data;
                renderAiSummary(data);
                card.classList.remove('hidden');
                btnText.textContent = 'Thu gọn tóm tắt';
            } else {
                alert('Không thể tạo tóm tắt lúc này. Vui lòng thử lại sau.');
                btnText.textContent = 'Tóm tắt bằng AI';
            }
        })
        .catch(err => {
            loading.classList.add('hidden');
            btnText.textContent = 'Tóm tắt bằng AI';
            console.error('Error fetching AI summary:', err);
        });
    }

    function renderAiSummary(data) {
        document.getElementById('ai-summary-overview').textContent = data.overview || '';
        document.getElementById('ai-summary-address').textContent = data.address || 'Đang cập nhật địa chỉ';
        document.getElementById('ai-summary-price').textContent = data.price_range || 'Mức giá hợp lý';

        // Must try dishes
        const mustTryContainer = document.getElementById('ai-summary-musttry');
        mustTryContainer.innerHTML = '';
        (data.must_try || []).forEach(dish => {
            const span = document.createElement('span');
            span.className = 'px-2 py-0.5 bg-amber-50 text-amber-900 border border-amber-200 text-[11px] font-bold rounded-md';
            span.textContent = dish;
            mustTryContainer.appendChild(span);
        });

        // Pros & Cons
        const prosConsList = document.getElementById('ai-summary-proscons');
        prosConsList.innerHTML = '';
        (data.pros || []).forEach(pro => {
            const li = document.createElement('li');
            li.className = 'flex items-start gap-1 text-emerald-700 font-medium';
            li.innerHTML = `<span class="material-symbols-outlined text-[14px] text-emerald-600 shrink-0 mt-0.5">check_circle</span> <span>${pro}</span>`;
            prosConsList.appendChild(li);
        });
        (data.cons || []).forEach(con => {
            const li = document.createElement('li');
            li.className = 'flex items-start gap-1 text-slate-500 font-medium';
            li.innerHTML = `<span class="material-symbols-outlined text-[14px] text-amber-500 shrink-0 mt-0.5">info</span> <span>${con}</span>`;
            prosConsList.appendChild(li);
        });

        document.getElementById('ai-summary-saved-time').textContent = data.reading_time_saved || 'Tiết kiệm 95% thời gian đọc (nắm trọn trong 5 giây)';
    }

    function copyAiSummary() {
        if (!currentSummaryData) return;
        const text = `✨ TÓM TẮT BÀI VIẾT BẰNG AI: ${currentSummaryData.title}\n\n` +
                     `📍 Địa chỉ: ${currentSummaryData.address}\n` +
                     `🍲 Món nên thử: ${(currentSummaryData.must_try || []).join(', ')}\n` +
                     `💵 Mức giá: ${currentSummaryData.price_range}\n` +
                     `📝 Tóm lược: ${currentSummaryData.overview}`;
        navigator.clipboard.writeText(text).then(() => {
            showAlert('success', 'Đã sao chép', 'Đã sao chép tóm tắt bài viết vào bộ nhớ tạm!');
        });
    }

    function speakAiSummary() {
        if (!('speechSynthesis' in window) || !currentSummaryData) return;
        if (isSpeaking) {
            window.speechSynthesis.cancel();
            isSpeaking = false;
            document.getElementById('ai-tts-icon').textContent = 'volume_up';
            document.getElementById('ai-tts-text').textContent = 'Nghe đọc';
            return;
        }

        const textToRead = `${currentSummaryData.title}. Tóm tắt nhanh: ${currentSummaryData.overview}. Địa chỉ: ${currentSummaryData.address}. Món nên thử: ${(currentSummaryData.must_try || []).join(', ')}. Mức giá: ${currentSummaryData.price_range}.`;
        const utterance = new SpeechSynthesisUtterance(textToRead);
        utterance.lang = 'vi-VN';
        utterance.rate = 1.0;
        utterance.onstart = () => {
            isSpeaking = true;
            document.getElementById('ai-tts-icon').textContent = 'stop_circle';
            document.getElementById('ai-tts-text').textContent = 'Dừng đọc';
        };
        utterance.onend = utterance.onerror = () => {
            isSpeaking = false;
            document.getElementById('ai-tts-icon').textContent = 'volume_up';
            document.getElementById('ai-tts-text').textContent = 'Nghe đọc';
        };
        window.speechSynthesis.speak(utterance);
    }

    // Newsletter Ajax Submit
    function handleNewsletterSubmit(event, form) {
        event.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const btnText = btn ? btn.querySelector('.btn-text') : null;
        const originalText = btnText ? btnText.textContent : 'Đăng ký ngay';
        const feedback = form.querySelector('.newsletter-feedback');
        const emailInput = form.querySelector('input[name="email"]');
        const email = emailInput ? emailInput.value.trim() : '';

        if (!email) {
            if (feedback) {
                feedback.className = 'newsletter-feedback block text-xs font-bold p-2.5 rounded-xl bg-rose-100 text-rose-800 border border-rose-300 shadow-2xs mt-2';
                feedback.innerHTML = '<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">error</span>Vui lòng nhập email của bạn.</span>';
            }
            return;
        }

        if (btn) {
            btn.disabled = true;
            if (btnText) btnText.textContent = 'Đang xử lý...';
        }

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
            }
            return data;
        })
        .then(data => {
            if (feedback) {
                if (data.already_subscribed) {
                    feedback.className = 'newsletter-feedback block text-xs font-bold p-3 rounded-xl bg-amber-50 text-amber-900 border border-amber-300 shadow-2xs mt-2 text-left';
                    feedback.innerHTML = `<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-amber-600">info</span><span>${data.message}</span></span>`;
                } else {
                    feedback.className = 'newsletter-feedback block text-xs font-bold p-3 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-300 shadow-2xs mt-2 text-left';
                    feedback.innerHTML = `<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-emerald-600">check_circle</span><span>${data.message}</span></span>`;
                    @guest
                    if (emailInput) emailInput.value = '';
                    @endguest
                }
            }
        })
        .catch(err => {
            if (feedback) {
                feedback.className = 'newsletter-feedback block text-xs font-bold p-3 rounded-xl bg-rose-50 text-rose-800 border border-rose-300 shadow-2xs mt-2 text-left';
                feedback.innerHTML = `<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-rose-600">error</span><span>${err.message}</span></span>`;
            }
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                if (btnText) btnText.textContent = originalText;
            }
        });
    }

    // Tự động đồng bộ lượt xem sang trang Blog Index và Author Profile mà không cần tải lại trang
    (function() {
        const blogViewPayload = {
            slug: @json($blog->slug),
            blogId: @json($blog->id),
            viewCount: {{ (int) ($blog->view_count ?? 0) }},
            formatted: @json(number_format($blog->view_count ?? 0)),
            authorId: @json($blog->user_id ?? null),
            timestamp: Date.now()
        };

        try {
            localStorage.setItem('qm_latest_blog_view', JSON.stringify(blogViewPayload));
        } catch(e) {}

        try {
            if ('BroadcastChannel' in window) {
                const bc = new BroadcastChannel('qm_blog_views_sync');
                bc.postMessage(blogViewPayload);
                setTimeout(() => bc.close(), 1000);
            }
        } catch(e) {}
    })();
</script>
@endpush
