@extends('layouts.app')

@section('title', 'Blog & Review - Quán Mới')
@section('description', 'Chia sẻ những bài viết review, mẹo kinh doanh và trải nghiệm ẩm thực hấp dẫn nhất.')

@section('content')
<div class="bg-surface-container-lowest min-h-screen pt-24 pb-12">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-10 text-center md:text-left">
            <h1 class="text-3xl md:text-5xl font-extrabold text-on-surface mb-4">Blog & Review</h1>
            <p class="text-on-surface-variant text-lg">Khám phá những trải nghiệm ẩm thực đặc sắc và câu chuyện thú vị.</p>
        </div>
        
        <!-- Categories Filter -->
        <div class="flex overflow-x-auto gap-3 pb-4 mb-8 hide-scrollbar">
            <a href="{{ route('blog.index') }}" 
               class="px-5 py-2 rounded-full whitespace-nowrap font-medium transition-all {{ !request()->has('category') ? 'bg-primary text-on-primary shadow-md' : 'bg-surface-container hover:bg-surface-container-highest text-on-surface' }}">
                Tất cả
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat->slug]) }}" 
                   class="px-5 py-2 rounded-full whitespace-nowrap font-medium transition-all {{ request('category') === $cat->slug ? 'bg-primary text-on-primary shadow-md' : 'bg-surface-container hover:bg-surface-container-highest text-on-surface' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        @if(!request()->has('category') && !request()->has('tag') && $heroBlog)
            <!-- Hero Article -->
            <a href="{{ route('blog.show', $heroBlog->slug) }}" class="group block mb-16 relative rounded-3xl overflow-hidden shadow-lg aspect-[16/9] md:aspect-[21/9]">
                <img src="{{ $heroBlog->cover_image ? asset('storage/'.$heroBlog->cover_image) : 'https://placehold.co/1200x500/png' }}" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="{{ $heroBlog->title }}">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 md:p-10 text-white w-full md:w-3/4 lg:w-2/3">
                    @if($heroBlog->category)
                        <span class="inline-block px-3 py-1 bg-primary text-white text-xs font-bold rounded-full mb-4">{{ $heroBlog->category->name }}</span>
                    @endif
                    <h2 class="text-2xl md:text-4xl font-bold mb-3 leading-tight group-hover:text-primary-fixed transition-colors">{{ $heroBlog->title }}</h2>
                    <p class="text-gray-200 line-clamp-2 md:line-clamp-3 mb-6 hidden md:block">{{ $heroBlog->excerpt }}</p>
                    
                    <div class="flex items-center gap-3">
                        <img src="{{ $heroBlog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($heroBlog->user->ho_ten) }}" class="w-10 h-10 rounded-full border-2 border-white object-cover">
                        <div>
                            <p class="font-semibold text-sm">{{ $heroBlog->user->ho_ten }}</p>
                            <p class="text-xs text-gray-300">{{ $heroBlog->published_at->format('d/m/Y') }} • {{ $heroBlog->views_count ?? 0 }} lượt xem</p>
                        </div>
                    </div>
                </div>
            </a>
        @endif

        <!-- Blog Grid -->
        @if($blogs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($blogs as $blog)
                    <div class="flex flex-col bg-surface-container-lowest rounded-2xl overflow-hidden border border-outline-variant hover:shadow-xl transition-shadow group">
                        <a href="{{ route('blog.show', $blog->slug) }}" class="relative aspect-video overflow-hidden">
                            <img src="{{ $blog->cover_image ? asset('storage/'.$blog->cover_image) : 'https://placehold.co/600x400/png' }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="{{ $blog->title }}">
                            @if($blog->category)
                                <span class="absolute top-4 right-4 px-3 py-1 bg-white/90 backdrop-blur-md text-primary font-bold text-[10px] uppercase tracking-wider rounded-full shadow-sm">{{ $blog->category->name }}</span>
                            @endif
                        </a>
                        <div class="p-6 flex flex-col flex-1">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="flex-1">
                                <h3 class="text-xl font-bold text-on-surface mb-3 line-clamp-2 group-hover:text-primary transition-colors">{{ $blog->title }}</h3>
                                <p class="text-on-surface-variant text-sm line-clamp-3 mb-4">{{ $blog->excerpt ?? Str::limit(strip_tags($blog->content), 120) }}</p>
                            </a>
                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-outline-variant">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $blog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($blog->user->ho_ten) }}" class="w-8 h-8 rounded-full object-cover">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-on-surface leading-none">{{ Str::limit($blog->user->ho_ten, 15) }}</span>
                                        <span class="text-[10px] text-on-surface-variant mt-1">{{ $blog->published_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div class="flex gap-3 text-on-surface-variant text-xs">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">visibility</span> {{ $blog->views_count ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-12 flex justify-center">
                {{ $blogs->links() }}
            </div>
        @else
            <div class="text-center py-20 bg-surface-container rounded-3xl">
                <span class="material-symbols-outlined text-6xl text-on-surface-variant mb-4">article</span>
                <h3 class="text-xl font-bold text-on-surface mb-2">Chưa có bài viết nào</h3>
                <p class="text-on-surface-variant">Hãy trở thành người đầu tiên chia sẻ câu chuyện của bạn.</p>
                @auth
                    <a href="{{ route('nguoi-dung.blog.create') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-full font-bold hover:bg-primary-hover transition-colors">
                        <span class="material-symbols-outlined">edit_square</span> Viết bài ngay
                    </a>
                @endauth
            </div>
        @endif
        
    </div>
</div>

<style>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
@endsection
