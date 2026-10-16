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
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-surface-container overflow-hidden border border-gray-100">
                        <img class="w-full h-full object-cover" src="{{ $blog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($blog->user->ho_ten).'&background=random' }}" alt="{{ $blog->user->ho_ten }}"/>
                    </div>
                    <div>
                        <div class="font-title-md text-[14px] leading-[20px] text-on-background flex items-center gap-1 font-bold">
                            {{ $blog->user->ho_ten }}
                            @if($blog->user->hasRole('chu_quan'))
                                <span class="material-symbols-outlined text-tick-xanh text-[16px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                            @endif
                        </div>
                        <div class="font-label-sm text-label-sm text-on-surface-variant">
                            Tác giả
                        </div>
                    </div>
                </div>
                <div class="w-px h-6 bg-surface-dim"></div>
                <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                    {{ $blog->published_at ? $blog->published_at->format('d/m/Y') : 'Bản nháp' }}
                </div>
                <div class="w-px h-6 bg-surface-dim"></div>
                <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1.5 bg-surface-container/60 px-2.5 py-1 rounded-full">
                    <span class="material-symbols-outlined text-[16px] text-primary">visibility</span>
                    <span><strong id="blog-view-count" class="font-semibold text-on-surface">{{ number_format($blog->view_count ?? 0) }}</strong> lượt xem</span>
                </div>
                <div class="w-px h-6 bg-surface-dim"></div>
                <a href="#comments-section" class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-container/60 px-2.5 py-1 rounded-full">
                    <span class="material-symbols-outlined text-[16px] text-primary">forum</span>
                    <span><strong id="blog-header-comment-count" class="font-semibold text-on-surface">{{ number_format($blog->comment_count) }}</strong> bình luận</span>
                </a>
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
                                            <span class="material-symbols-outlined text-[16px] {{ $isLiked ? 'fill-current' : '' }}" id="like-icon-{{ $comment->id }}">{{ $isLiked ? 'favorite' : 'favorite_border' }}</span>
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
                                            <span class="material-symbols-outlined text-[16px]">favorite_border</span>
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
                                                    <span class="material-symbols-outlined text-[14px] {{ $isReplyLiked ? 'fill-current' : '' }}" id="like-icon-{{ $reply->id }}">{{ $isReplyLiked ? 'favorite' : 'favorite_border' }}</span>
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
                                                    <span class="material-symbols-outlined text-[14px]">favorite_border</span>
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
        
        <!-- Related Articles (if any) -->
        @if($relatedBlogs->count() > 0)
        <section class="mt-8">
            <h3 class="font-display-lg text-2xl text-on-background mb-6">Bài viết liên quan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach($relatedBlogs->take(4) as $related)
                    <a href="{{ route('blog.show', $related->slug) }}" class="bg-surface-card rounded-xl overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-outline-variant/30 group flex flex-col">
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="{{ $related->cover_image ? (Str::startsWith($related->cover_image, 'http') ? $related->cover_image : asset('storage/'.$related->cover_image)) : 'https://placehold.co/600x400/png' }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="{{ $related->title }}">
                        </div>
                        <div class="p-4 flex flex-col flex-1">
                            <h4 class="font-title-md text-[15px] leading-[22px] text-on-background mb-2 line-clamp-2 group-hover:text-primary transition-colors flex-1">{{ $related->title }}</h4>
                            <div class="text-[12px] text-on-surface-variant flex items-center justify-between mt-auto pt-2 border-t border-surface-dim">
                                <span>{{ $related->user->ho_ten }}</span>
                                <span>{{ $related->published_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
        @endif
        
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
                        <div class="font-label-sm text-label-sm text-on-surface-variant">{{ $popularBlog->published_at ? $popularBlog->published_at->format('d/m/Y') : 'Bản nháp' }}</div>
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
            <h3 class="font-title-md text-title-md mb-2 z-10 relative text-white">Đừng bỏ lỡ quán ngon!</h3>
            <p class="font-body-sm text-body-sm mb-4 z-10 relative opacity-90 text-white/90">Đăng ký để nhận danh sách review ẩm thực mới nhất mỗi tuần từ Quán Mới.</p>
            <form class="flex flex-col gap-2 z-10 relative">
                <input class="w-full rounded-lg border-none bg-surface-container-lowest focus:ring-2 focus:ring-on-primary-container px-4 py-3 font-body-sm text-body-sm text-on-background placeholder:text-text-muted outline-none" placeholder="Email của bạn" type="email" required/>
                <button class="w-full bg-on-primary-container text-surface-container-lowest rounded-lg py-3 font-label-md text-label-md font-bold hover:opacity-90 transition-opacity active:scale-[0.98]" type="submit" onclick="event.preventDefault(); alert('Cảm ơn bạn đã đăng ký!');">
                     Đăng ký ngay
                 </button>
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
                    icon.classList.add('fill-current');
                } else {
                    btn.className = 'flex items-center gap-1 transition-colors text-slate-500 hover:text-rose-600';
                    icon.textContent = 'favorite_border';
                    icon.classList.remove('fill-current');
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
                                        <span class="material-symbols-outlined text-[14px]" id="like-icon-${c.id}">favorite_border</span>
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
                                            <span class="material-symbols-outlined text-[16px]" id="like-icon-${c.id}">favorite_border</span>
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
</script>
@endpush
