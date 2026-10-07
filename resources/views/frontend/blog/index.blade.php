@extends('layouts.app')

@section('title', 'Blog - Quán Mới | Khám phá ẩm thực địa phương')
@section('description', 'Chia sẻ những bài viết review, mẹo kinh doanh và trải nghiệm ẩm thực hấp dẫn nhất.')

@push('styles')
<style>
    .level-1-card { background: white; border-radius: 12px; box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05); transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .level-1-card:hover { transform: translateY(-4px); box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.08); }
    .active-tab { color: #a04100; font-weight: 700; border-bottom: 2px solid #a04100; padding-bottom: 4px; }
    
    /* Scrollbar hide for categories */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush

@section('content')
<!-- Main Content -->
<main class="mt-8 md:mt-12 max-w-[1200px] mx-auto px-container-margin pb-16">
    
    <!-- Featured Post Hero -->
    @if(!request()->has('category') && !request()->has('tag') && $heroBlog)
    <section class="relative w-full h-[480px] rounded-xl overflow-hidden mb-stack-lg group">
        <img class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105"
             src="{{ $heroBlog->cover_image ? (Str::startsWith($heroBlog->cover_image, 'http') ? $heroBlog->cover_image : asset('storage/'.$heroBlog->cover_image)) : 'https://placehold.co/1200x500/png' }}"
             alt="{{ $heroBlog->title }}">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-8 md:p-12 max-w-3xl">
            @if($heroBlog->category)
                <span class="bg-primary-container text-on-primary px-3 py-1 rounded-lg font-label-md text-label-md mb-4 inline-block uppercase tracking-wider">{{ $heroBlog->category->name }}</span>
            @endif
            <a href="{{ route('blog.show', $heroBlog->slug) }}" class="block">
                <h1 class="font-display-lg text-white text-4xl md:text-5xl mb-4 leading-tight hover:text-primary-fixed transition-colors">{{ $heroBlog->title }}</h1>
            </a>
            <p class="text-white/90 font-body-lg text-body-lg mb-6 line-clamp-2">{{ $heroBlog->excerpt }}</p>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center overflow-hidden border border-white/30">
                    <img class="w-full h-full object-cover" src="{{ $heroBlog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($heroBlog->user->ho_ten).'&background=random' }}"/>
                </div>
                <div class="text-white">
                    <p class="font-label-md text-label-md">Bởi {{ $heroBlog->user->ho_ten }}</p>
                    <p class="text-xs opacity-75">{{ $heroBlog->published_at->format('d/m/Y') }} • {{ number_format($heroBlog->view_count ?? 0) }} lượt xem</p>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Category Navigation & Search -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-stack-lg border-b border-outline-variant pb-2 gap-4">
        <nav class="flex items-center gap-8 overflow-x-auto whitespace-nowrap scrollbar-hide w-full md:w-auto pb-1">
            <a class="font-title-md text-title-md {{ !request()->has('category') && !request()->has('tag') && !request()->has('search') ? 'active-tab' : 'text-text-muted hover:text-primary transition-colors' }}" href="{{ route('blog.index') }}">Tất cả</a>
            @foreach($categories as $cat)
                <a class="font-title-md text-title-md {{ request('category') === $cat->slug ? 'active-tab' : 'text-text-muted hover:text-primary transition-colors' }}" href="{{ route('blog.index', ['category' => $cat->slug]) }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </nav>
        
        <form action="{{ route('blog.index') }}" method="GET" class="relative w-full md:w-72 flex-shrink-0">
            @if(request()->has('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request()->has('tag'))
                <input type="hidden" name="tag" value="{{ request('tag') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm bài viết..." class="w-full pl-10 pr-4 py-2 bg-surface-container rounded-full text-body-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none border-none shadow-inner">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-muted text-[20px]">search</span>
        </form>
    </div>
    
    <div class="flex flex-col lg:flex-row gap-gutter">
        
        <!-- Article Grid -->
        <div class="lg:w-3/4">
            @if($blogs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
                    @foreach($blogs as $blog)
                        <!-- Blog Card -->
                        <article class="level-1-card flex flex-col h-full group">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="relative aspect-[4/3] rounded-t-xl overflow-hidden block">
                                @if($blog->category)
                                    <div class="absolute top-3 left-3 z-10 bg-primary-container text-on-primary px-2 py-0.5 rounded-md font-label-md text-label-md uppercase">{{ $blog->category->name }}</div>
                                @endif
                                <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" 
                                     src="{{ $blog->cover_image ? (Str::startsWith($blog->cover_image, 'http') ? $blog->cover_image : asset('storage/'.$blog->cover_image)) : 'https://placehold.co/600x400/png' }}" alt="{{ $blog->title }}"/>
                            </a>
                            <div class="p-4 flex flex-col flex-grow">
                                <a href="{{ route('blog.show', $blog->slug) }}" class="flex-grow block">
                                    <h3 class="font-title-md text-title-md text-on-surface mb-2 line-clamp-2 group-hover:text-primary transition-colors">{{ $blog->title }}</h3>
                                    <p class="text-text-muted font-body-sm text-body-sm mb-4 line-clamp-3">{{ $blog->excerpt ?? Str::limit(strip_tags($blog->content), 120) }}</p>
                                </a>
                                <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                                    <span class="font-label-sm text-label-sm text-text-muted">{{ Str::limit($blog->user->ho_ten, 15) }} • {{ $blog->published_at->format('d/m/Y') }}</span>
                                    <span class="font-label-sm text-label-sm text-text-muted flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span> {{ number_format($blog->view_count ?? 0) }}
                                    </span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                
                <div class="mt-12 flex justify-center">
                    {{ $blogs->links() }}
                </div>
            @else
                <div class="text-center py-20 bg-white rounded-xl border border-outline-variant shadow-sm">
                    <span class="material-symbols-outlined text-5xl text-gray-400 mb-4">article</span>
                    <h3 class="text-xl font-bold text-on-surface mb-2">Chưa có bài viết nào</h3>
                    <p class="text-on-surface-variant">Hãy trở thành người đầu tiên chia sẻ câu chuyện của bạn.</p>
                </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <aside class="lg:w-1/4 space-y-stack-lg">
            
            <!-- Newsletter Signup -->
            <div class="bg-primary-fixed p-6 rounded-xl border border-primary-container/20">
                <h4 class="font-title-md text-title-md text-on-primary-container mb-2">Nhận tin ẩm thực</h4>
                <p class="text-on-primary-fixed-variant font-body-sm text-body-sm mb-4">Đừng bỏ lỡ các review quán mới và công thức nấu ăn độc quyền hàng tuần.</p>
                <form class="space-y-3" onsubmit="event.preventDefault(); alert('Cảm ơn bạn đã đăng ký!');">
                    <input class="w-full px-4 py-3 bg-white border border-outline-variant rounded-xl text-body-sm focus:ring-primary focus:border-primary outline-none" placeholder="Email của bạn..." type="email" required/>
                    <button type="submit" class="w-full py-3 bg-primary-container text-on-primary font-title-md text-title-md rounded-xl shadow-sm hover:opacity-90 active:scale-[0.98] transition-all">Đăng ký ngay</button>
                </form>
            </div>
            
            <!-- Popular Posts -->
            <div class="level-1-card p-6">
                <h4 class="font-title-md text-title-md text-on-surface mb-4 pb-2 border-b border-outline-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">trending_up</span>
                    Bài viết phổ biến
                </h4>
                <div class="space-y-6">
                    @foreach(\App\Models\Blog::where('status', 'published')->orderBy('view_count', 'desc')->limit(5)->get() as $index => $popularBlog)
                        <a class="flex gap-3 group" href="{{ route('blog.show', $popularBlog->slug) }}">
                            <span class="text-2xl font-bold text-primary-container/30 group-hover:text-primary transition-colors">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h5 class="font-label-md text-label-md text-on-surface group-hover:text-primary transition-colors line-clamp-2">{{ $popularBlog->title }}</h5>
                                <p class="text-[10px] text-text-muted mt-1 uppercase tracking-tighter">{{ number_format($popularBlog->view_count ?? 0) }} lượt xem</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            
            <!-- Hot Topics Tag Cloud -->
            <div class="level-1-card p-6">
                <h4 class="font-title-md text-title-md text-on-surface mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">local_fire_department</span>
                    Chủ đề hot
                </h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <a class="px-3 py-1 bg-surface-container text-on-surface-variant font-label-sm text-label-sm rounded-full hover:bg-primary-fixed hover:text-primary transition-all {{ request('tag') === $tag->slug ? 'bg-primary text-white' : '' }}" href="{{ route('blog.index', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a>
                    @endforeach
                </div>
            </div>
            
            <!-- Verified Authors -->
            <div class="level-1-card p-6">
                <h4 class="font-title-md text-title-md text-on-surface mb-4">Cộng tác viên</h4>
                <div class="space-y-4">
                    @foreach(\App\Models\User::whereHas('blogs', function($q){ $q->where('status', 'published'); })->withCount(['blogs' => function($q){ $q->where('status', 'published'); }])->orderBy('blogs_count', 'desc')->limit(4)->get() as $author)
                        <div class="flex items-center gap-3">
                            <img class="w-10 h-10 rounded-full object-cover border border-outline-variant" src="{{ $author->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($author->ho_ten).'&background=random' }}"/>
                            <div>
                                <p class="font-label-md text-label-md text-on-surface flex items-center gap-1">
                                    {{ Str::limit($author->ho_ten, 20) }}
                                    <span class="material-symbols-outlined text-[14px] text-tick-xanh" style="font-variation-settings: 'FILL' 1;">verified</span>
                                </p>
                                <p class="text-[10px] text-text-muted">{{ $author->blogs_count }} bài viết</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
        </aside>
    </div>
</main>
@endsection
