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
        
        <!-- Comments Section -->
        <section class="mt-8 bg-surface-card rounded-xl p-6 md:p-stack-lg shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-outline-variant/30">
            <h3 class="font-title-md text-title-md text-on-background mb-stack-md flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">forum</span>
                Bình luận ({{ $blog->comments->count() }})
            </h3>
            
            @auth
                <!-- Comment Form -->
                <form action="#" method="POST" class="mb-8 flex gap-4 items-start">
                    @csrf
                    <img src="{{ auth()->user()->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->ho_ten).'&background=random' }}" class="w-10 h-10 rounded-full flex-shrink-0 shadow-sm border border-gray-100">
                    <div class="flex-1">
                        <textarea name="content" rows="3" placeholder="Viết bình luận của bạn..." class="w-full rounded-xl border border-surface-dim bg-surface-container-lowest focus:ring-1 focus:ring-primary focus:border-primary resize-none text-sm p-3"></textarea>
                        <div class="mt-2 flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-full font-label-md text-label-md hover:opacity-90 transition-opacity">Gửi bình luận</button>
                        </div>
                    </div>
                </form>
            @else
                <div class="p-6 bg-surface-container rounded-xl mb-8 text-center">
                    <p class="text-on-surface-variant font-label-md mb-4">Vui lòng đăng nhập để tham gia bình luận.</p>
                    <a href="{{ route('login') }}" class="inline-flex px-6 py-2 bg-primary text-white rounded-full font-label-md hover:opacity-90 transition-opacity">Đăng nhập</a>
                </div>
            @endauth
            
            <!-- Comment List -->
            <div class="space-y-6">
                @foreach($blog->comments as $comment)
                    <div class="flex gap-4 p-4 rounded-xl hover:bg-surface-container transition-colors">
                        <img src="{{ $comment->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->ho_ten).'&background=random' }}" class="w-10 h-10 rounded-full flex-shrink-0 object-cover">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-title-md text-[14px] text-on-background">{{ $comment->user->ho_ten }}</span>
                                <span class="text-xs text-on-surface-variant">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-on-surface font-body-sm whitespace-pre-line">{{ $comment->content }}</p>
                        </div>
                    </div>
                @endforeach
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
