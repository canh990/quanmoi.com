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
                <div class="w-px h-6 bg-surface-dim hidden md:block"></div>
                <div class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1 hidden md:flex">
                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                    {{ number_format($blog->view_count ?? 0) }} lượt xem
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
        
        <!-- Comments Section with AI Moderation -->
        <section class="mt-8 bg-surface-card rounded-xl p-6 md:p-stack-lg shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-outline-variant/30">
            <div class="flex items-center justify-between mb-6 flex-wrap gap-3 pb-4 border-b border-surface-dim">
                <h3 class="font-title-md text-title-md text-on-background flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">forum</span>
                    <span>Bình luận (<span id="comments-count-badge">{{ $blog->comments->count() }}</span>)</span>
                </h3>
                
                {{-- AI Moderation Shield Badge --}}
                <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold shadow-2xs">
                    <span class="material-symbols-outlined text-sm text-emerald-600">verified_user</span>
                    <span>Bảo vệ bởi AI Moderation (Tự động lọc spam & ngôn từ độc hại)</span>
                </div>
            </div>

            {{-- Dynamic AI Moderation Alert Banner --}}
            <div id="comment-alert-box" class="hidden mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs">
                <span class="material-symbols-outlined text-xl shrink-0" id="comment-alert-icon">info</span>
                <div class="flex-1">
                    <h5 class="font-extrabold text-sm mb-0.5" id="comment-alert-title">Thông báo kiểm duyệt</h5>
                    <p id="comment-alert-msg" class="leading-relaxed"></p>
                </div>
                <button type="button" onclick="document.getElementById('comment-alert-box').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
            
            @auth
                <!-- Comment Form -->
                <form id="blog-comment-form" action="{{ route('blog.comment.store', $blog->slug) }}" method="POST" onsubmit="handleCommentSubmit(event)" class="mb-8 flex gap-4 items-start">
                    @csrf
                    <img src="{{ auth()->user()->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->ho_ten).'&background=random' }}" class="w-10 h-10 rounded-full flex-shrink-0 shadow-sm border border-gray-100 object-cover">
                    <div class="flex-1 space-y-2">
                        <textarea id="comment-content-input" name="content" rows="3" required placeholder="Chia sẻ suy nghĩ hoặc đánh giá của bạn về bài viết này..." class="w-full rounded-xl border border-surface-dim bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary resize-none text-sm p-3.5 outline-none transition-all"></textarea>
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <span class="text-[11px] text-text-muted flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs text-emerald-600">smart_toy</span>
                                Bình luận được quét & kiểm duyệt tự động trước khi hiển thị
                            </span>
                            <button type="submit" id="comment-submit-btn" class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-full font-label-md text-label-md transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                                <span class="material-symbols-outlined text-base">send</span>
                                <span id="comment-submit-text">Gửi bình luận</span>
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="p-6 bg-surface-container rounded-2xl mb-8 text-center border border-outline-variant/30">
                    <p class="text-on-surface-variant font-label-md mb-3">Vui lòng đăng nhập tài khoản để tham gia bình luận bài viết.</p>
                    <a href="{{ route('login') }}" class="inline-flex px-6 py-2 bg-primary text-white rounded-full font-label-md hover:opacity-90 transition-opacity">Đăng nhập</a>
                </div>
            @endauth
            
            <!-- Comment List -->
            <div id="comments-list-container" class="space-y-4">
                @forelse($blog->comments as $comment)
                    <div class="comment-item flex gap-4 p-4 rounded-2xl bg-white hover:bg-surface-container/40 transition-colors border border-surface-dim/50">
                        <img src="{{ $comment->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->ho_ten).'&background=random' }}" class="w-10 h-10 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs">
                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-title-md text-[14px] font-bold text-on-background">{{ $comment->user->ho_ten }}</span>
                                    <span class="text-xs text-on-surface-variant">• {{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-0.5 shadow-2xs">
                                    <span class="material-symbols-outlined text-[12px] text-emerald-600">verified</span>
                                    <span>AI Đã Kiểm Duyệt</span>
                                </span>
                            </div>
                            <p class="text-on-surface font-body-sm whitespace-pre-line text-sm leading-relaxed">{{ $comment->content }}</p>
                        </div>
                    </div>
                @empty
                    <div id="comments-empty-state" class="p-8 text-center rounded-2xl bg-surface-container/20 border border-dashed border-outline-variant/40">
                        <span class="material-symbols-outlined text-3xl text-text-muted mb-2">forum</span>
                        <p class="text-xs font-bold text-slate-600">Chưa có bình luận nào cho bài viết này</p>
                        <p class="text-[11px] text-text-muted mt-0.5">Hãy là người đầu tiên chia sẻ cảm nghĩ và nhận xét của bạn!</p>
                    </div>
                @endforelse
            </div>
        </section>
        
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
    function handleCommentSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('blog-comment-form');
        const textarea = document.getElementById('comment-content-input');
        const btn = document.getElementById('comment-submit-btn');
        const btnText = document.getElementById('comment-submit-text');
        const alertBox = document.getElementById('comment-alert-box');
        const alertTitle = document.getElementById('comment-alert-title');
        const alertMsg = document.getElementById('comment-alert-msg');
        const alertIcon = document.getElementById('comment-alert-icon');
        const listContainer = document.getElementById('comments-list-container');
        const emptyState = document.getElementById('comments-empty-state');
        const counterBadge = document.getElementById('comments-count-badge');

        const content = textarea.value.trim();
        if (!content) return;

        // Loading state
        btn.disabled = true;
        btnText.textContent = 'AI đang quét kiểm duyệt...';
        btn.classList.add('opacity-75');

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ content: content })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            btn.disabled = false;
            btnText.textContent = 'Gửi bình luận';
            btn.classList.remove('opacity-75');

            if (status === 200 && body.success) {
                // Success: clean & verified
                alertBox.className = 'mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs bg-emerald-50 border border-emerald-200 text-emerald-800';
                alertIcon.className = 'material-symbols-outlined text-xl text-emerald-600 shrink-0';
                alertIcon.textContent = 'verified_user';
                alertTitle.textContent = 'Kiểm duyệt an toàn thành công!';
                alertMsg.textContent = body.message;
                alertBox.classList.remove('hidden');

                // Clear textarea
                textarea.value = '';

                // Remove empty state if present
                if (emptyState) emptyState.remove();

                // Prepend new comment
                const newCommentHtml = `
                    <div class="comment-item flex gap-4 p-4 rounded-2xl bg-white hover:bg-surface-container/40 transition-colors border border-emerald-200/80 shadow-xs">
                        <img src="${body.comment.user_avatar}" class="w-10 h-10 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs">
                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-title-md text-[14px] font-bold text-on-background">${body.comment.user_name}</span>
                                    <span class="text-xs text-on-surface-variant">• ${body.comment.created_at_human}</span>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full flex items-center gap-0.5 shadow-2xs">
                                    <span class="material-symbols-outlined text-[12px] text-emerald-600">verified</span>
                                    <span>AI Đã Kiểm Duyệt</span>
                                </span>
                            </div>
                            <p class="text-on-surface font-body-sm whitespace-pre-line text-sm leading-relaxed">${body.comment.content}</p>
                        </div>
                    </div>
                `;
                listContainer.insertAdjacentHTML('afterbegin', newCommentHtml);

                if (counterBadge && body.comment_count !== undefined) {
                    counterBadge.textContent = body.comment_count;
                }
            } else {
                // Moderation blocked or error
                alertBox.className = 'mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs bg-rose-50 border border-rose-200 text-rose-800';
                alertIcon.className = 'material-symbols-outlined text-xl text-rose-600 shrink-0';
                alertIcon.textContent = 'gpp_bad';
                alertTitle.textContent = 'Bình luận bị AI chặn kiểm duyệt!';
                alertMsg.textContent = body.message || 'Bình luận của bạn không đáp ứng tiêu chuẩn an toàn cộng đồng.';
                alertBox.classList.remove('hidden');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.textContent = 'Gửi bình luận';
            btn.classList.remove('opacity-75');

            alertBox.className = 'mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs bg-rose-50 border border-rose-200 text-rose-800';
            alertIcon.className = 'material-symbols-outlined text-xl text-rose-600 shrink-0';
            alertIcon.textContent = 'error';
            alertTitle.textContent = 'Lỗi gửi bình luận';
            alertMsg.textContent = 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.';
            alertBox.classList.remove('hidden');
        });
    }
</script>
@endpush
