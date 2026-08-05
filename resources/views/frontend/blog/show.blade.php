@extends('layouts.app')

@section('title', $blog->seo_title ?? $blog->title)
@section('description', $blog->seo_description ?? Str::limit(strip_tags($blog->excerpt ?? $blog->content), 150))
@section('keywords', $blog->meta_keywords ?? '')

@section('content')
<div class="bg-surface-container-lowest min-h-screen pt-24 pb-20">
    <div class="max-w-[800px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex mb-6 text-sm text-on-surface-variant font-medium" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">home</span>
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-[18px] mx-1">chevron_right</span>
                        <a href="{{ route('blog.index') }}" class="hover:text-primary transition-colors">Blog</a>
                    </div>
                </li>
                @if($blog->category)
                <li>
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-[18px] mx-1">chevron_right</span>
                        <a href="{{ route('blog.index', ['category' => $blog->category->slug]) }}" class="hover:text-primary transition-colors">{{ $blog->category->name }}</a>
                    </div>
                </li>
                @endif
            </ol>
        </nav>

        <!-- Header -->
        <header class="mb-10">
            @if($blog->category)
                <a href="{{ route('blog.index', ['category' => $blog->category->slug]) }}" class="inline-block px-4 py-1.5 bg-primary/10 text-primary text-xs font-bold rounded-full mb-4 uppercase tracking-wider hover:bg-primary hover:text-white transition-colors">{{ $blog->category->name }}</a>
            @endif
            <h1 class="text-3xl md:text-5xl font-extrabold text-on-surface mb-6 leading-tight">{{ $blog->title }}</h1>
            
            @if($blog->excerpt)
                <p class="text-xl text-on-surface-variant mb-6 font-medium leading-relaxed">{{ $blog->excerpt }}</p>
            @endif
            
            <div class="flex items-center justify-between py-4 border-y border-outline-variant">
                <div class="flex items-center gap-4">
                    <img src="{{ $blog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($blog->user->ho_ten) }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-surface-container">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-on-surface">{{ $blog->user->ho_ten }}</span>
                            @if($blog->user->hasRole('chu_quan'))
                                <span class="material-symbols-outlined text-[16px] text-primary" title="Chủ quán">verified</span>
                            @endif
                        </div>
                        <div class="text-sm text-on-surface-variant flex items-center gap-2">
                            <span>{{ $blog->published_at ? $blog->published_at->format('d/m/Y H:i') : 'Bản nháp' }}</span>
                            <span>•</span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">visibility</span> {{ $blog->views_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Share Actions -->
                <div class="flex gap-2">
                    <button class="w-10 h-10 rounded-full flex items-center justify-center bg-surface-container hover:bg-surface-container-highest transition-colors text-on-surface-variant" title="Lưu bài viết">
                        <span class="material-symbols-outlined text-[20px]">bookmark</span>
                    </button>
                    <button class="w-10 h-10 rounded-full flex items-center justify-center bg-surface-container hover:bg-surface-container-highest transition-colors text-on-surface-variant" title="Chia sẻ" onclick="navigator.clipboard.writeText(window.location.href); alert('Đã copy link!');">
                        <span class="material-symbols-outlined text-[20px]">share</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Cover Image -->
        @if($blog->cover_image)
            <figure class="mb-12">
                <img src="{{ asset('storage/'.$blog->cover_image) }}" alt="{{ $blog->title }}" class="w-full rounded-2xl md:rounded-3xl shadow-lg">
            </figure>
        @endif

        <!-- Content -->
        <article class="prose prose-lg prose-headings:font-bold prose-headings:text-on-surface prose-p:text-on-surface prose-a:text-primary hover:prose-a:text-primary-hover max-w-none mb-12">
            {!! $blog->content !!}
        </article>

        <!-- Tags -->
        @if($blog->tags->count() > 0)
            <div class="flex flex-wrap gap-2 mb-12">
                @foreach($blog->tags as $tag)
                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="px-4 py-2 bg-surface-container rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container-highest transition-colors">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Comments Section -->
        <section class="mt-16 pt-10 border-t border-outline-variant">
            <h3 class="text-2xl font-bold text-on-surface mb-8">Bình luận ({{ $blog->comments->count() }})</h3>
            
            @auth
                <!-- Comment Form -->
                <form action="#" method="POST" class="mb-10 flex gap-4">
                    @csrf
                    <img src="{{ auth()->user()->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->ho_ten) }}" class="w-10 h-10 rounded-full flex-shrink-0">
                    <div class="flex-1">
                        <textarea name="content" rows="3" placeholder="Viết bình luận của bạn..." class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary resize-none"></textarea>
                        <div class="mt-2 flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-primary text-on-primary rounded-full font-bold hover:bg-primary-hover transition-colors">Gửi bình luận</button>
                        </div>
                    </div>
                </form>
            @else
                <div class="p-6 bg-surface-container rounded-2xl mb-10 text-center">
                    <p class="text-on-surface-variant mb-4">Vui lòng đăng nhập để tham gia bình luận.</p>
                    <a href="{{ route('login') }}" class="inline-flex px-6 py-2 bg-primary text-on-primary rounded-full font-bold hover:bg-primary-hover transition-colors">Đăng nhập</a>
                </div>
            @endauth
            
            <!-- Comment List -->
            <div class="space-y-6">
                @foreach($blog->comments as $comment)
                    <div class="flex gap-4 p-4 rounded-2xl hover:bg-surface-container-lowest transition-colors">
                        <img src="{{ $comment->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->ho_ten) }}" class="w-10 h-10 rounded-full flex-shrink-0">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-on-surface">{{ $comment->user->ho_ten }}</span>
                                <span class="text-xs text-on-surface-variant">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-on-surface whitespace-pre-line">{{ $comment->content }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        
    </div>
</div>

<!-- Related Articles -->
@if($relatedBlogs->count() > 0)
<div class="bg-surface-container py-16">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
        <h3 class="text-2xl font-bold text-on-surface mb-8">Bài viết liên quan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedBlogs as $related)
                <div class="flex flex-col bg-surface-container-lowest rounded-2xl overflow-hidden border border-outline-variant hover:shadow-xl transition-shadow group">
                    <a href="{{ route('blog.show', $related->slug) }}" class="relative aspect-video overflow-hidden">
                        <img src="{{ $related->cover_image ? asset('storage/'.$related->cover_image) : 'https://placehold.co/600x400/png' }}" 
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="{{ $related->title }}">
                    </a>
                    <div class="p-5 flex flex-col flex-1">
                        <a href="{{ route('blog.show', $related->slug) }}" class="flex-1">
                            <h4 class="text-lg font-bold text-on-surface mb-2 line-clamp-2 group-hover:text-primary transition-colors">{{ $related->title }}</h4>
                        </a>
                        <div class="mt-4 flex items-center gap-2 text-xs text-on-surface-variant">
                            <span class="font-semibold">{{ $related->user->ho_ten }}</span>
                            <span>•</span>
                            <span>{{ $related->published_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection
