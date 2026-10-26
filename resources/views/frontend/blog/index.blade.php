@extends('layouts.app')

@section('title', 'Blog - Quán Mới | Khám phá ẩm thực địa phương')
@section('description', 'Chia sẻ những bài viết review, mẹo kinh doanh và trải nghiệm ẩm thực hấp dẫn nhất.')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<style>
    .level-1-card { background: white; border-radius: 12px; box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05); transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .level-1-card:hover { transform: translateY(-4px); box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.08); }
    .blog-card-cover-frame {
        position: relative;
        width: 100%;
        height: 205px;
        overflow: hidden;
        display: block;
        border-radius: 12px 12px 0 0;
        background-color: #f1f5f9;
    }
    .blog-card-cover-frame img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.5s ease;
    }
    .level-1-card:hover .blog-card-cover-frame img {
        transform: scale(1.05);
    }
    .active-tab { color: #a04100; font-weight: 700; border-bottom: 2px solid #a04100; padding-bottom: 4px; }
    
    /* Scrollbar hide for categories */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Modal Quill Editor custom styles */
    #modal-editor-container {
        min-height: 220px;
        font-family: 'Be Vietnam Pro', sans-serif;
        font-size: 15px;
        border-radius: 0 0 1rem 1rem;
    }
    .ql-toolbar.ql-snow {
        border-radius: 1rem 1rem 0 0;
        border-color: #e5e7eb !important;
        background: #f9fafb;
    }
    .ql-container.ql-snow {
        border-color: #e5e7eb !important;
    }
    
    /* Smooth modal animations */
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .animate-modal-in {
        animation: modalFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endpush

@section('content')
<!-- Main Content -->
<main class="mt-6 md:mt-10 max-w-[1200px] mx-auto px-container-margin pb-16">

    @if(session('success'))
        <div id="blog-success-banner" class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-sm animate-modal-in flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-emerald-900">Thành công!</h4>
                    <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('blog-success-banner')?.remove()" class="p-1.5 text-emerald-500 hover:text-emerald-700 hover:bg-emerald-100 rounded-lg transition-colors cursor-pointer" title="Đóng">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-sm animate-modal-in">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-rose-600">error</span>
                <h4 class="font-bold text-sm">Đã có lỗi xảy ra khi lưu bài viết:</h4>
            </div>
            <ul class="list-disc list-inside text-xs text-rose-700 space-y-0.5 pl-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Featured Post Hero -->
    @if(!request()->has('category') && !request()->has('tag') && !request()->has('search') && $heroBlog)
    <section class="relative w-full h-[480px] rounded-2xl overflow-hidden mb-stack-lg group shadow-lg">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" 
             style="background-image: url('{{ $heroBlog->cover_image_url ?? 'https://placehold.co/1200x500/png' }}')">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-8 md:p-12 max-w-3xl">
            @if($heroBlog->category)
                <span class="bg-primary-container text-on-primary px-3 py-1 rounded-lg font-label-md text-label-md mb-4 inline-block uppercase tracking-wider font-bold">{{ $heroBlog->category->name }}</span>
            @endif
            <a href="{{ route('blog.show', $heroBlog->slug) }}" class="block">
                <h1 class="font-display-lg text-white text-3xl md:text-4xl lg:text-5xl mb-4 leading-tight hover:text-primary-fixed transition-colors font-extrabold">{{ $heroBlog->title }}</h1>
            </a>
            <p class="text-white/90 font-body-lg text-body-lg mb-6 line-clamp-2">{{ $heroBlog->excerpt }}</p>
            <a href="{{ $heroBlog->user ? route('blog.author', $heroBlog->user->id) : '#' }}" class="flex items-center gap-4 group/hero-author hover:opacity-95 transition-opacity inline-flex" title="Xem thông tin tác giả {{ $heroBlog->user?->ho_ten }}">
                <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center overflow-hidden border border-white/30 ring-2 ring-transparent group-hover/hero-author:ring-white transition-all">
                    <img class="w-full h-full object-cover group-hover/hero-author:scale-105 transition-transform" src="{{ $heroBlog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($heroBlog->user->ho_ten ?? 'User').'&background=random' }}"/>
                </div>
                <div class="text-white">
                    <p class="font-label-md text-label-md font-semibold flex items-center gap-1 group-hover/hero-author:underline">
                        <span>Bởi {{ $heroBlog->user->ho_ten ?? 'Tác giả' }}</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </p>
                    <p class="text-xs opacity-75 flex items-center gap-2 flex-wrap">
                        <span>{{ $heroBlog->published_at ? $heroBlog->published_at->format('d/m/Y') : '' }}</span>
                        <span>•</span>
                        <span class="blog-view-count-wrap inline-flex items-center gap-1 font-medium px-2 py-0.5 rounded-full border border-transparent hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 transition-all cursor-pointer" data-blog-slug="{{ $heroBlog->slug }}" data-blog-id="{{ $heroBlog->id }}" title="Lượt xem bài viết">
                            <span class="material-symbols-outlined text-[15px] text-amber-300">visibility</span>
                            <span class="blog-view-count-text">{{ number_format($heroBlog->view_count ?? 0) }} lượt xem</span>
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1 font-medium" title="Bình luận bài viết">
                            <span class="material-symbols-outlined text-[15px]">forum</span>
                            <span>{{ number_format($heroBlog->comment_count ?? 0) }} bình luận</span>
                        </span>
                    </p>
                </div>
            </a>
        </div>
    </section>
    @endif

    <!-- Category Navigation & Search & Create Action -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-stack-lg border-b border-outline-variant pb-3 gap-4">
        <nav class="flex items-center gap-6 md:gap-8 overflow-x-auto whitespace-nowrap scrollbar-hide w-full md:w-auto pb-1">
            <a class="font-title-md text-title-md {{ !request()->has('category') && !request()->has('tag') && !request()->has('search') ? 'active-tab' : 'text-text-muted hover:text-primary transition-colors' }}" href="{{ route('blog.index') }}">Tất cả</a>
            @foreach($categories as $cat)
                <a class="font-title-md text-title-md {{ request('category') === $cat->slug ? 'active-tab' : 'text-text-muted hover:text-primary transition-colors' }}" href="{{ route('blog.index', ['category' => $cat->slug]) }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </nav>
        
        <div class="flex items-center gap-3 w-full md:w-auto shrink-0 justify-end">
            <form action="{{ route('blog.index') }}" method="GET" class="relative flex-grow md:w-80 lg:w-96" id="blog-search-form">
                @if(request()->has('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request()->has('tag'))
                    <input type="hidden" name="tag" value="{{ request('tag') }}">
                @endif
                <input type="text" 
                       id="blog-search-input"
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Tìm kiếm bài viết..." 
                       autocomplete="off"
                       oninput="handleBlogSearchInput(this)"
                       class="w-full pl-10 pr-9 py-2.5 bg-surface-container rounded-full text-body-sm focus:ring-2 focus:ring-primary focus:bg-white focus:border-primary outline-none border border-slate-200/50 shadow-inner transition-all">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-text-muted text-[20px] pointer-events-none">search</span>
                
                @if(request('search'))
                    <a href="{{ route('blog.index', array_filter(['category' => request('category'), 'tag' => request('tag')])) }}" 
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 p-0.5 rounded-full hover:bg-slate-200/60 transition-colors"
                       title="Xóa tìm kiếm">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                    </a>
                @endif

                <!-- Live Suggestions Dropdown -->
                <div id="blog-search-suggestions" class="absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200/80 overflow-hidden z-50 hidden divide-y divide-slate-100">
                    <div id="blog-suggestions-list" class="max-h-80 overflow-y-auto divide-y divide-slate-100"></div>
                    <div class="p-2.5 bg-slate-50 text-center">
                        <button type="submit" class="text-xs font-bold text-primary hover:underline flex items-center justify-center gap-1 w-full">
                            <span>Xem tất cả kết quả</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="flex flex-col lg:flex-row gap-gutter">
        
        <!-- Article Grid -->
        <div class="lg:w-3/4">
            @if(request()->filled('search'))
                <div class="mb-6 p-4 bg-amber-50/90 border border-amber-200/90 rounded-2xl flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-amber-600 text-xl">search</span>
                        <div>
                            <span class="text-xs text-slate-500 font-medium">Kết quả tìm kiếm cho:</span>
                            <span class="font-extrabold text-sm text-slate-900 ml-1">"{{ request('search') }}"</span>
                            <span class="text-xs font-bold text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full ml-2">
                                {{ $blogs->total() }} bài viết
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('blog.index', array_filter(['category' => request('category'), 'tag' => request('tag')])) }}" class="text-xs font-bold text-slate-500 hover:text-red-600 flex items-center gap-1 py-1 px-3 rounded-xl hover:bg-white transition-all">
                        <span class="material-symbols-outlined text-base">close</span>
                        <span>Xóa tìm kiếm</span>
                    </a>
                </div>
            @endif

            @if($blogs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
                    @foreach($blogs as $blog)
                        <!-- Blog Card -->
                        <article class="level-1-card flex flex-col h-full group border border-outline-variant hover:border-amber-400/80 hover:ring-2 hover:ring-amber-400/30 hover:shadow-lg transition-all duration-300">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="blog-card-cover-frame">
                                @if($blog->category)
                                    <div class="absolute top-3 left-3 z-10 bg-primary-container text-on-primary px-2 py-0.5 rounded-md font-label-md text-label-md uppercase font-bold max-w-[calc(100%-1.5rem)] truncate">{{ $blog->category->name }}</div>
                                @endif
                                <div class="absolute bottom-3 right-3 z-10 px-2.5 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white font-semibold text-[10px] flex items-center gap-1 shadow-sm" title="Thời gian đăng: {{ ($blog->published_at ?? $blog->created_at)?->format('d/m/Y H:i') }}">
                                    <span class="material-symbols-outlined text-[12px] text-amber-300">schedule</span>
                                    <span>{{ ($blog->published_at ?? $blog->created_at)?->diffForHumans() ?? 'Vừa xong' }}</span>
                                </div>
                                <img src="{{ $blog->cover_image_url ?? 'https://placehold.co/600x400/png' }}" alt="{{ $blog->title }}"/>
                            </a>
                            <div class="p-4 flex flex-col flex-grow">
                                <a href="{{ route('blog.show', $blog->slug) }}" class="flex-grow block">
                                    <h3 class="font-title-md text-title-md text-on-surface mb-2 line-clamp-2 group-hover:text-primary transition-colors font-bold">{{ $blog->title }}</h3>
                                    <p class="text-text-muted font-body-sm text-body-sm mb-4 line-clamp-3">{{ $blog->excerpt ?? Str::limit(strip_tags($blog->content), 120) }}</p>
                                </a>
                                <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                                    <span class="font-label-sm text-label-sm text-text-muted flex items-center gap-1">
                                        @if($blog->user)
                                            <a href="{{ route('blog.author', $blog->user->id) }}" class="hover:text-primary transition-colors font-medium hover:underline" title="Xem bài viết của tác giả {{ $blog->user->ho_ten }}">
                                                {{ Str::limit($blog->user->ho_ten, 15) }}
                                            </a>
                                        @else
                                            <span>Tác giả</span>
                                        @endif
                                        <span>•</span>
                                        <span>{{ ($blog->published_at ?? $blog->created_at)?->format('d/m/Y') ?? 'Mới' }}</span>
                                    </span>
                                    <div class="font-label-sm text-label-sm text-text-muted flex items-center gap-2">
                                        <span class="blog-view-count-wrap flex items-center gap-1 font-semibold text-slate-600 hover:text-amber-700 transition-all px-2 py-0.5 rounded-lg border border-transparent hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 hover:shadow-xs cursor-pointer" data-blog-slug="{{ $blog->slug }}" data-blog-id="{{ $blog->id }}" title="{{ number_format($blog->view_count ?? 0) }} lượt xem">
                                            <span class="material-symbols-outlined text-[15px] text-amber-600">visibility</span>
                                            <span class="blog-view-count-text">{{ number_format($blog->view_count ?? 0) }} lượt xem</span>
                                        </span>
                                        <span class="flex items-center gap-1 font-semibold text-slate-600 hover:text-primary transition-all px-2 py-0.5 rounded-lg border border-transparent hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 hover:shadow-xs cursor-pointer" title="{{ number_format($blog->comment_count ?? 0) }} bình luận">
                                            <span class="material-symbols-outlined text-[15px] text-primary">forum</span>
                                            <span>{{ number_format($blog->comment_count ?? 0) }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                
                <div class="mt-12 flex justify-center">
                    {{ $blogs->links() }}
                </div>
            @else
                @if(request()->filled('search'))
                    <div class="text-center py-16 bg-white rounded-3xl border border-outline-variant shadow-sm px-4">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                            <span class="material-symbols-outlined text-3xl">search_off</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-on-surface mb-2">Không tìm thấy bài viết nào</h3>
                        <p class="text-on-surface-variant max-w-md mx-auto mb-6 text-sm">Không có bài viết nào khớp với từ khóa "<span class="font-bold text-slate-900">{{ request('search') }}</span>". Hãy thử từ khóa khác hoặc quay lại danh sách bài viết.</p>
                        <a href="{{ route('blog.index') }}" class="px-6 py-2.5 rounded-full bg-primary text-white font-bold text-xs shadow-md hover:bg-primary/90 transition-all inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">refresh</span> Xem tất cả bài viết
                        </a>
                    </div>
                @else
                    <div class="text-center py-20 bg-white rounded-2xl border border-outline-variant shadow-sm">
                        <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">edit_note</span>
                        <h3 class="text-xl font-bold text-on-surface mb-2">Chưa có bài viết nào</h3>
                        <p class="text-on-surface-variant max-w-md mx-auto mb-6">Hãy trở thành người đầu tiên đăng bài viết review hoặc chia sẻ không gian quán của bạn!</p>
                        @auth
                            <button onclick="openCreateBlogModal()" type="button" class="px-6 py-3 rounded-full bg-primary text-white font-bold text-sm shadow-md hover:bg-primary/90 transition-all inline-flex items-center gap-2">
                                <span class="material-symbols-outlined">add</span> Đăng bài viết ngay
                            </button>
                        @else
                            <button onclick="openGuestModal()" type="button" class="px-6 py-3 rounded-full bg-primary text-white font-bold text-sm shadow-md hover:bg-primary/90 transition-all inline-flex items-center gap-2">
                                <span class="material-symbols-outlined">login</span> Đăng nhập để đăng bài
                            </button>
                        @endauth
                    </div>
                @endif
            @endif
        </div>
        
        <!-- Sidebar -->
        <aside class="lg:w-1/4 space-y-stack-lg">
            
            <!-- Quick Action Box for Owners -->
            <div class="bg-gradient-to-br from-amber-500/10 to-primary/10 p-6 rounded-2xl border border-primary/20 shadow-sm">
                <div class="flex items-center gap-2 text-primary font-bold text-sm mb-2">
                    <span class="material-symbols-outlined text-[20px]">campaign</span>
                    <span>Đăng bài truyền thông</span>
                </div>
                <h4 class="font-bold text-on-surface text-base mb-2">Tạo bài viết cho quán</h4>
                <p class="text-on-surface-variant text-xs mb-4">Chia sẻ menu mới, không gian quán đẹp hoặc thông tin khuyến mãi để thu hút thực khách.</p>
                @auth
                    <button onclick="openCreateBlogModal()" type="button" class="w-full py-2.5 bg-primary text-white font-bold text-xs rounded-xl shadow-sm hover:opacity-90 border border-transparent hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">edit</span> Viết bài mới ngay
                    </button>
                @else
                    <button onclick="openGuestModal()" type="button" class="w-full py-2.5 bg-primary text-white font-bold text-xs rounded-xl shadow-sm hover:opacity-90 border border-transparent hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">lock</span> Đăng nhập để viết bài
                    </button>
                @endauth
            </div>

            <!-- Newsletter Signup -->
            <div class="bg-primary-fixed p-6 rounded-xl border border-primary-container/20">
                <div class="flex items-center gap-2 mb-2 text-on-primary-container">
                    <span class="material-symbols-outlined text-[22px]">mark_email_read</span>
                    <h4 class="font-title-md text-title-md font-bold">Nhận tin ẩm thực</h4>
                </div>
                <p class="text-on-primary-fixed-variant font-body-sm text-body-sm mb-4">Đừng bỏ lỡ các review quán mới và công thức nấu ăn độc quyền hàng tuần.</p>

                @if(session('newsletter_success'))
                    <div class="mb-3 p-3 bg-emerald-100/90 text-emerald-900 border border-emerald-300 rounded-xl text-xs font-bold flex items-center gap-2 shadow-2xs">
                        <span class="material-symbols-outlined text-base text-emerald-600">check_circle</span>
                        <span>{{ session('newsletter_success') }}</span>
                    </div>
                @elseif(session('newsletter_info'))
                    <div class="mb-3 p-3 bg-amber-100/90 text-amber-900 border border-amber-300 rounded-xl text-xs font-bold flex items-center gap-2 shadow-2xs">
                        <span class="material-symbols-outlined text-base text-amber-600">info</span>
                        <span>{{ session('newsletter_info') }}</span>
                    </div>
                @endif

                <form id="blog-index-newsletter-form" action="{{ route('blog.newsletter.subscribe') }}" method="POST" onsubmit="handleNewsletterSubmit(event, this)" class="space-y-3">
                    @csrf
                    <input type="hidden" name="source" value="blog_index">
                    <div class="relative">
                        <input class="w-full px-4 py-3 bg-white border border-outline-variant rounded-xl text-body-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-text-muted text-slate-800" 
                               name="email" 
                               value="{{ auth()->check() ? auth()->user()->email : old('email') }}" 
                               placeholder="Email của bạn..." 
                               type="email" 
                               required/>
                    </div>
                    <button type="submit" class="w-full py-3 bg-primary-container text-on-primary font-title-md text-title-md rounded-xl shadow-sm hover:opacity-90 border border-transparent hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 active:scale-[0.98] transition-all flex items-center justify-center gap-2 font-bold cursor-pointer">
                        <span class="btn-text">Đăng ký ngay</span>
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </button>
                    <div class="newsletter-feedback hidden text-xs font-semibold p-2.5 rounded-xl"></div>
                </form>
            </div>

            <!-- Popular Posts -->
            <div class="level-1-card p-6">
                <h4 class="font-title-md text-title-md text-on-surface mb-4 pb-2 border-b border-outline-variant flex items-center gap-2 font-bold">
                    <span class="material-symbols-outlined text-primary">trending_up</span>
                    Bài viết phổ biến
                </h4>
                <div class="space-y-6">
                    @foreach(\App\Models\Blog::where('status', 'published')->orderBy('view_count', 'desc')->limit(5)->get() as $index => $popularBlog)
                        <a class="flex gap-3 group" href="{{ route('blog.show', $popularBlog->slug) }}">
                            <span class="text-2xl font-bold text-primary-container/30 group-hover:text-primary transition-colors">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h5 class="font-label-md text-label-md text-on-surface group-hover:text-primary transition-colors line-clamp-2 font-semibold">{{ $popularBlog->title }}</h5>
                                <p class="blog-view-count-wrap text-[11px] text-slate-500 font-semibold mt-1 flex items-center gap-1 px-1.5 py-0.5 rounded-md border border-transparent hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 transition-all inline-flex cursor-pointer" data-blog-slug="{{ $popularBlog->slug }}" data-blog-id="{{ $popularBlog->id }}" title="{{ number_format($popularBlog->view_count ?? 0) }} lượt xem">
                                    <span class="material-symbols-outlined text-[13px] text-amber-600">visibility</span>
                                    <span class="blog-view-count-text">{{ number_format($popularBlog->view_count ?? 0) }} lượt xem</span>
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            
            <!-- Hot Topics Tag Cloud -->
            <div class="level-1-card p-6">
                <h4 class="font-title-md text-title-md text-on-surface mb-4 flex items-center gap-2 font-bold">
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
                <h4 class="font-title-md text-title-md text-on-surface mb-4 font-bold">Cộng tác viên</h4>
                <div class="space-y-4">
                    @foreach(\App\Models\User::whereHas('blogs', function($q){ $q->where('status', 'published'); })->withCount(['blogs' => function($q){ $q->where('status', 'published'); }])->orderBy('blogs_count', 'desc')->limit(4)->get() as $author)
                        <div class="flex items-center gap-3">
                            <img class="w-10 h-10 rounded-full object-cover border border-outline-variant" src="{{ $author->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($author->ho_ten).'&background=random' }}"/>
                            <div>
                                <p class="font-label-md text-label-md text-on-surface flex items-center gap-1 font-semibold">
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




<!-- ==================== CREATE BLOG MODAL ==================== -->
@auth
<div id="createBlogModal" class="fixed inset-0 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-md p-3 sm:p-6 lg:p-8 pt-6 sm:pt-8" style="z-index: 100000;">
    <div class="flex min-h-full items-center justify-center p-0 text-center">
        <form action="{{ route('nguoi-dung.blog.store') }}" method="POST" enctype="multipart/form-data" id="modal-blog-form" onsubmit="event.preventDefault(); submitModalBlogForm('pending');" class="relative w-full max-w-3xl my-auto max-h-[86vh] bg-white rounded-3xl shadow-2xl border border-slate-200/80 overflow-hidden animate-modal-in flex flex-col text-left">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ route('blog.index') }}">
            <input type="hidden" name="action" id="modal-action-input" value="pending">
            <input type="hidden" name="content" id="modal-content-input">
            
            <!-- Modal Header (Pinned at Top) -->
            <div style="background: linear-gradient(135deg, #b45309 0%, #d97706 50%, #ea580c 100%); color: #ffffff;" class="px-5 sm:px-6 py-4 flex items-center justify-between shrink-0 shadow-sm relative z-20">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 text-white shrink-0 shadow-xs">
                        <span class="material-symbols-outlined text-2xl">edit_note</span>
                    </div>
                    <div class="leading-tight min-w-0">
                        <h3 class="text-base sm:text-lg font-extrabold text-white tracking-wide truncate">Tạo Bài Viết Blog Mới</h3>
                        <p class="text-xs text-amber-100/90 font-medium mt-0.5 truncate">Chia sẻ trải nghiệm & giới thiệu quán cho cộng đồng</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-3 shrink-0 ml-2">
                    <span id="modal-draft-indicator" class="text-xs font-semibold text-white/90 bg-black/20 px-2.5 py-1 rounded-full opacity-0 transition-opacity hidden sm:flex items-center"></span>
                    <button onclick="closeCreateBlogModal()" type="button" class="w-9 h-9 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition-all hover:scale-105 active:scale-95 cursor-pointer shadow-xs" title="Đóng cửa sổ (Esc)">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>
            </div>

            <!-- Scrollable Body (Only this part scrolls!) -->
            <div id="modal-scrollable-body" class="p-5 sm:p-6 overflow-y-auto space-y-4 flex-1 min-h-0">
                <!-- Draft Restored Banner in Modal -->
                <div id="modal-draft-alert-banner" class="hidden p-3 rounded-2xl border border-amber-300 bg-amber-50/95 text-xs text-amber-950 flex items-center justify-between gap-2 shadow-2xs animate-modal-in">
                    <div class="flex items-center gap-2 overflow-hidden">
                        <span class="material-symbols-outlined text-amber-600 text-lg shrink-0">history_edu</span>
                        <span class="truncate font-bold">Đã tự động khôi phục bài viết đang soạn dở của bạn!</span>
                        <span class="text-slate-500 font-medium text-[11px] hidden sm:inline" id="qm-draft-time-label"></span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" onclick="dismissDraftBanner()" class="px-2.5 py-1 text-[11px] font-bold text-amber-800 hover:bg-amber-100 rounded-lg transition-colors cursor-pointer">
                            Tiếp tục
                        </button>
                        <button type="button" onclick="discardSavedDraft()" class="px-2.5 py-1 text-[11px] font-bold text-rose-700 hover:bg-rose-100 rounded-lg transition-colors cursor-pointer flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-xs">delete</span> Xóa nháp
                        </button>
                    </div>
                </div>

                <!-- Client Validation Alert Box -->
                <div id="modal-error-alert" class="hidden p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-2xs">
                    <span class="material-symbols-outlined text-rose-600 text-lg">warning</span>
                    <span>Vui lòng nhập đầy đủ Tiêu đề, chọn Danh mục và điền Nội dung bài viết trước khi gửi!</span>
                </div>

                {{-- AI Content Writer Assistant Toolbar --}}
                @include('nguoi-dung.blog.partials.ai-toolbar', ['context' => 'modal'])

                <!-- Cover Image Upload & Editor -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Ảnh bìa bài viết</label>
                        <span id="modal-cover-badge" class="hidden text-[11px] font-bold text-amber-700 bg-amber-100/90 px-2.5 py-0.5 rounded-full border border-amber-300 flex items-center gap-1 shadow-2xs">
                            <span class="material-symbols-outlined text-xs">aspect_ratio</span> Tỷ lệ 21:9 chuẩn
                        </span>
                    </div>

                    <div class="relative w-full h-44 sm:h-48 min-h-[160px] bg-amber-50/40 rounded-2xl overflow-hidden border-2 border-dashed border-amber-300/80 hover:border-amber-500 hover:bg-amber-50/80 transition-all duration-200 flex flex-col items-center justify-center text-center p-2 group shadow-2xs" id="modal-cover-container">
                        <input type="file" name="cover_image" id="modal_cover_image" accept="image/jpeg,image/png,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewModalCoverImage(this)">
                        
                        <!-- Placeholder khi chưa có ảnh -->
                        <div class="text-center p-2 select-none pointer-events-none" id="modal-cover-placeholder">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-1.5 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-xl">add_photo_alternate</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800">Nhấn hoặc kéo thả để tải ảnh bìa bài viết</p>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Định dạng JPG, PNG, WEBP (Tỷ lệ 21:9, tối đa 5MB)</p>
                            <span class="inline-flex items-center gap-1 mt-1.5 text-[10px] font-bold text-amber-700 bg-amber-100/90 px-2.5 py-0.5 rounded-full border border-amber-200/80">
                                <span class="material-symbols-outlined text-xs">crop</span> Có hỗ trợ cắt & chỉnh sửa ảnh
                            </span>
                        </div>
                        
                        <!-- Preview ảnh -->
                        <img id="modal-cover-preview" src="" alt="Ảnh bìa bài viết" class="absolute inset-0 w-full h-full object-cover hidden">
                        
                        <!-- Overlay & Actions Bar khi đã có ảnh -->
                        <div id="modal-cover-actions-bar" class="absolute inset-0 bg-slate-950/45 backdrop-blur-[1px] opacity-0 group-hover:opacity-100 transition-opacity duration-200 hidden flex flex-col items-center justify-center gap-2 p-3 z-20">
                            <div class="flex items-center gap-2 flex-wrap justify-center">
                                <!-- Nút Chỉnh sửa ảnh -->
                                <button type="button" onclick="editModalCoverImage(event)" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg flex items-center gap-1.5 transition-all transform hover:scale-105 active:scale-95 cursor-pointer">
                                    <span class="material-symbols-outlined text-base">crop</span>
                                    <span>Chỉnh sửa & Cắt ảnh</span>
                                </button>
                                <!-- Nút Đổi ảnh -->
                                <button type="button" onclick="changeModalCoverImage(event)" class="px-3 py-2 rounded-xl bg-slate-900/85 hover:bg-slate-900 text-white font-bold text-xs backdrop-blur-md shadow-md flex items-center gap-1.5 transition-all hover:scale-105 active:scale-95 cursor-pointer border border-white/10">
                                    <span class="material-symbols-outlined text-base">sync</span>
                                    <span>Đổi ảnh</span>
                                </button>
                                <!-- Nút Xóa ảnh -->
                                <button type="button" onclick="removeModalCoverImage(event)" class="p-2 rounded-xl bg-rose-600/90 hover:bg-rose-600 text-white font-bold text-xs backdrop-blur-md shadow-md flex items-center justify-center transition-all hover:scale-105 active:scale-95 cursor-pointer border border-white/10" title="Xóa ảnh bìa">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </div>
                            <p class="text-[11px] text-white/95 font-medium bg-black/50 px-3 py-0.5 rounded-full backdrop-blur-sm pointer-events-none">
                                Nhấn <b>Chỉnh sửa & Cắt ảnh</b> để căn chỉnh góc đẹp nhất
                            </p>
                        </div>

                        <!-- Nút Chỉnh sửa cố định góc dưới khi có ảnh (để mobile không cần hover cũng thấy) -->
                        <div id="modal-cover-fixed-edit-btn" class="absolute bottom-2 right-2 hidden z-20 group-hover:hidden">
                            <button type="button" onclick="editModalCoverImage(event)" class="px-3 py-1.5 rounded-xl bg-slate-900/85 hover:bg-slate-900 text-amber-300 font-bold text-xs backdrop-blur-md shadow-md border border-slate-700 flex items-center gap-1 transition-all cursor-pointer">
                                <span class="material-symbols-outlined text-sm text-amber-400">crop</span>
                                <span>Chỉnh sửa ảnh</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="modal_title" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Tiêu đề bài viết <span class="text-red-500">*</span></label>
                        <button type="button" onclick="openAiTitleSuggestModal('modal')" class="text-[11px] font-bold text-purple-600 hover:text-purple-800 flex items-center gap-1 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-sm">auto_fix_high</span> Gợi ý tiêu đề AI
                        </button>
                    </div>
                    <input type="text" id="modal_title" name="title" value="{{ old('title') }}" required placeholder="Ví dụ: Review chi tiết món lẩu nấm cực ngon tại Quán Mới..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 text-xs font-bold text-slate-900 py-2.5 px-4 shadow-2xs transition-all outline-none placeholder:font-normal placeholder:text-slate-400">
                </div>

                @php
                    $modalCategories = count($categories ?? []) > 0 ? $categories : \App\Models\BlogCategory::all();
                    if ($modalCategories->isEmpty()) {
                        $defaultCatNames = ['Review Quán', 'Món Ngon Địa Phương', 'Góc Chủ Quán', 'Khám Phá Ẩm Thực', 'Khuyến Mãi & Ưu Đãi'];
                        foreach ($defaultCatNames as $name) {
                            \App\Models\BlogCategory::firstOrCreate(
                                ['slug' => \Illuminate\Support\Str::slug($name)],
                                ['name' => $name, 'status' => true]
                            );
                        }
                        $modalCategories = \App\Models\BlogCategory::all();
                    }

                    $allModalTags = \App\Models\BlogTag::all();
                    if ($allModalTags->isEmpty()) {
                        $defaultTagNames = ['ReviewQuan', 'MonNgon', 'AmThuc', 'KhongGianChill', 'DiaDiemAnUong', 'KhamPha', 'GocChuQuan', 'KhuyenMai'];
                        foreach ($defaultTagNames as $name) {
                            \App\Models\BlogTag::firstOrCreate(
                                ['slug' => \Illuminate\Support\Str::slug($name)],
                                ['name' => $name]
                            );
                        }
                        $allModalTags = \App\Models\BlogTag::all();
                    }
                    // Chỉ hiển thị vài thẻ phổ biến nhất ban đầu
                    $popularTags = $allModalTags->take(8);
                @endphp

                <!-- Category & Tags Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Category with Live Autocomplete Suggestions -->
                    <div>
                        <label for="modal_category_name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Danh mục bài viết <span class="text-red-500">*</span></label>
                        <div class="relative autocomplete-wrapper">
                            <input type="text" 
                                   id="modal_category_name" 
                                   name="category_name" 
                                   list="modal_category_datalist"
                                   value="{{ old('category_name', old('new_category')) }}" 
                                   placeholder="Gõ hoặc chọn danh mục..." 
                                   autocomplete="off"
                                   required
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 font-bold text-xs text-slate-900 py-2.5 px-4 pr-10 shadow-2xs transition-all outline-none placeholder:font-normal placeholder:text-slate-400"
                                   onclick="toggleCategoryDropdown('modal_cat_dropdown')"
                                   oninput="filterCategorySuggestions(this, 'modal_cat_dropdown')">
                            <button type="button" 
                                    onclick="toggleCategoryDropdown('modal_cat_dropdown')"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-amber-600 p-1 rounded-lg transition-colors flex items-center justify-center cursor-pointer"
                                    title="Mở danh mục">
                                <span class="material-symbols-outlined text-lg">expand_more</span>
                            </button>
                            
                            <!-- Suggestions Dropdown list attached right below input -->
                            <div id="modal_cat_dropdown" class="cat-dropdown-menu absolute left-0 right-0 top-full mt-1 bg-white border border-amber-200 rounded-2xl shadow-xl z-50 max-h-48 overflow-y-auto hidden divide-y divide-slate-100">
                                @foreach($modalCategories as $category)
                                    <div class="cat-suggestion-item px-4 py-2.5 hover:bg-amber-50/80 cursor-pointer text-xs font-bold text-slate-800 flex items-center justify-between transition-colors"
                                         data-name="{{ strtolower($category->name) }}"
                                         onclick="selectCategoryItem('modal_category_name', '{{ addslashes($category->name) }}', 'modal_cat_dropdown')">
                                        <span class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-amber-600 text-sm">folder</span>
                                            <span>{{ $category->name }}</span>
                                        </span>
                                        <span class="text-[10px] text-amber-700 bg-amber-100/70 px-2 py-0.5 rounded-full font-bold">Danh mục có sẵn</span>
                                    </div>
                                @endforeach
                                <div class="cat-custom-option hidden px-4 py-2.5 bg-amber-50 hover:bg-amber-100 cursor-pointer text-xs font-bold text-amber-900 flex items-center justify-between transition-colors border-t border-amber-200"
                                     onclick="selectCustomCategoryItem('modal_category_name', 'modal_cat_dropdown')">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-amber-700 text-sm">add_circle</span>
                                        <span>Dùng danh mục mới: "<span class="cat-custom-text"></span>"</span>
                                    </span>
                                    <span class="text-[10px] text-white bg-amber-600 px-2 py-0.5 rounded-full font-bold">Mới</span>
                                </div>
                            </div>

                            <datalist id="modal_category_datalist">
                                @foreach($modalCategories as $category)
                                    <option value="{{ $category->name }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>
                    
                    <!-- Tags with Live Autocomplete Suggestions & Quick Badges -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="modal_custom_tags" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Thẻ bài viết (Tags)</label>
                            <span class="text-[11px] font-bold text-amber-700">Gõ thẻ mới (nhấn Enter) hoặc chọn</span>
                        </div>
                        <div class="relative autocomplete-wrapper">
                            <input type="text" 
                                   id="modal_custom_tags" 
                                   name="custom_tags" 
                                   list="modal_tag_datalist"
                                   value="{{ old('custom_tags') }}" 
                                   placeholder="Gõ tên thẻ mới (nhấn Enter hoặc dấu phẩy để thêm)..." 
                                   autocomplete="off"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 text-xs font-semibold text-slate-900 py-2.5 px-4 pr-10 shadow-2xs transition-all outline-none placeholder:font-normal placeholder:text-slate-400"
                                   onkeydown="handleTagInputKeydown(event, this, 'modal_tag_dropdown')"
                                   onclick="toggleTagDropdown('modal_tag_dropdown')"
                                   oninput="filterTagSuggestions(this, 'modal_tag_dropdown'); syncQuickTagStates('modal_custom_tags');">
                            <button type="button" 
                                    onclick="toggleTagDropdown('modal_tag_dropdown')"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-amber-600 p-1 rounded-lg transition-colors flex items-center justify-center cursor-pointer"
                                    title="Mở gợi ý thẻ">
                                <span class="material-symbols-outlined text-base">local_offer</span>
                            </button>
                            
                            <!-- Tag Suggestions Dropdown List -->
                            <div id="modal_tag_dropdown" class="tag-dropdown-menu absolute left-0 right-0 top-full mt-1 bg-white border border-amber-200 rounded-2xl shadow-xl z-50 max-h-48 overflow-y-auto hidden divide-y divide-slate-100">
                                @foreach($allModalTags as $tag)
                                    <div class="tag-suggestion-item px-4 py-2 hover:bg-amber-50/80 cursor-pointer text-xs font-bold text-slate-800 flex items-center justify-between transition-colors"
                                         data-name="{{ strtolower($tag->name) }}"
                                         onclick="selectTagItem('modal_custom_tags', '{{ addslashes($tag->name) }}', 'modal_tag_dropdown')">
                                        <span class="flex items-center gap-1.5">
                                            <span class="text-amber-600 font-bold">#</span>
                                            <span>{{ $tag->name }}</span>
                                        </span>
                                        <span class="text-[10px] text-slate-400">Gợi ý thẻ</span>
                                    </div>
                                @endforeach
                                <div class="tag-custom-option hidden px-4 py-2.5 bg-amber-50 hover:bg-amber-100 cursor-pointer text-xs font-bold text-amber-900 flex items-center justify-between transition-colors border-t border-amber-200"
                                     onclick="selectCustomTagItem('modal_custom_tags', 'modal_tag_dropdown')">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-amber-700 text-sm">add_circle</span>
                                        <span>Thêm thẻ mới: #<span class="tag-custom-text font-bold text-amber-800"></span></span>
                                    </span>
                                    <span class="text-[10px] text-white bg-amber-600 px-2 py-0.5 rounded-full font-bold">Thêm mới</span>
                                </div>
                            </div>

                            <datalist id="modal_tag_datalist">
                                @foreach($allModalTags as $tag)
                                    <option value="{{ $tag->name }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        
                        <!-- Quick Tag Badges with interactive toggle state -->
                        <div class="quick-tags-container flex flex-wrap gap-1.5 mt-2 max-h-24 overflow-y-auto p-2 bg-slate-50/60 rounded-xl border border-slate-200/60" 
                             data-target="modal_custom_tags"
                             data-popular='@json($popularTags->pluck("name"))'>
                            @foreach($popularTags as $tag)
                                <button type="button" 
                                        data-tag="{{ strtolower($tag->name) }}"
                                        onclick="addQuickTag('modal_custom_tags', '{{ addslashes($tag->name) }}')"
                                        class="quick-tag-btn px-2.5 py-1 text-[11px] rounded-full border border-amber-200/80 bg-amber-50/60 text-amber-800 font-medium hover:bg-amber-100 hover:border-amber-300 transition-all flex items-center gap-1 cursor-pointer select-none active:scale-95">
                                    <span class="material-symbols-outlined text-xs tag-icon">add</span>
                                    <span>#{{ $tag->name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Excerpt -->
                <div>
                    <label for="modal_excerpt" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Mô tả ngắn (Excerpt)</label>
                    <textarea id="modal_excerpt" name="excerpt" rows="2" placeholder="Tóm tắt nội dung hấp dẫn nhất của bài viết..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 text-xs font-medium text-slate-800 p-2.5 shadow-2xs transition-all outline-none resize-none placeholder:text-slate-400">{{ old('excerpt') }}</textarea>
                </div>

                <!-- Content Quill Editor -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Nội dung bài viết <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openAiOutlineModal('modal')" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-sm">article</span> AI Dàn ý
                            </button>
                            <span class="text-slate-300">•</span>
                            <button type="button" onclick="runAiSpellCheck('modal')" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-800 flex items-center gap-1 transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-sm">spellcheck</span> AI Sửa chính tả
                            </button>
                        </div>
                    </div>
                    <div id="modal-editor-container" class="bg-white rounded-xl">
                        {!! old('content') !!}
                    </div>
                </div>
            </div>

            <!-- Form Footer Buttons (Pinned at Bottom) -->
            <div class="shrink-0 bg-slate-50 border-t border-slate-200/80 px-6 py-3.5 flex items-center justify-between gap-3 relative z-30">
                <a href="{{ route('nguoi-dung.blog.create') }}" onclick="transferDraftToFullEditor(event)" class="text-xs font-bold text-amber-700 hover:text-amber-800 flex items-center gap-1 shrink-0 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-base">open_in_new</span>
                    <span class="hidden sm:inline">Mở giao diện soạn thảo đầy đủ</span>
                    <span class="sm:hidden">Soạn thảo</span>
                </a>
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <button type="button" onclick="submitModalBlogForm('draft')" style="background-color: #ffffff; color: #334155; border: 1px solid #cbd5e1;" class="px-4 sm:px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-slate-100 active:scale-95 transition-all shadow-2xs whitespace-nowrap cursor-pointer">
                        Lưu bản nháp
                    </button>
                    <button type="button" onclick="submitModalBlogForm('pending')" style="background-color: #d97706; color: #ffffff;" class="px-5 sm:px-6 py-2.5 rounded-xl font-bold text-xs hover:bg-amber-700 active:scale-95 transition-all shadow-md flex items-center justify-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <span class="material-symbols-outlined text-base text-white">send</span>
                        <span class="text-white font-extrabold">Gửi duyệt bài</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endauth

<!-- ==================== GUEST LOGIN MODAL ==================== -->
<div id="guestBlogModal" class="fixed inset-0 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="z-index: 100000;">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-outline-variant p-6 text-center animate-modal-in">
        <div class="w-16 h-16 rounded-2xl bg-amber-100 text-primary flex items-center justify-center mx-auto mb-4 border border-amber-200">
            <span class="material-symbols-outlined text-3xl">rate_review</span>
        </div>
        <h3 class="text-xl font-extrabold text-on-surface mb-2">Đăng bài viết trên Quán Mới</h3>
        <p class="text-sm text-text-muted mb-6">Bạn cần đăng nhập tài khoản (Chủ quán hoặc Thành viên) để có thể tạo bài viết blog và chia sẻ review ẩm thực.</p>
        <div class="flex flex-col gap-3">
            <a href="{{ route('login') }}" class="w-full py-3 bg-primary text-white font-bold rounded-2xl shadow-md hover:bg-primary/90 transition-all flex items-center justify-center gap-2 text-sm">
                <span class="material-symbols-outlined text-lg">login</span> Đăng nhập ngay
            </a>
            <a href="{{ route('register') }}" class="w-full py-3 bg-surface-container text-on-surface font-bold rounded-2xl hover:bg-surface-container-high transition-all text-sm">
                Tạo tài khoản mới
            </a>
            <button onclick="closeGuestModal()" type="button" class="mt-2 text-xs text-text-muted hover:text-on-surface font-medium">
                Hủy bỏ
            </button>
        </div>
    </div>
</div>

{{-- AI Assistant Modals & Script --}}
@include('nguoi-dung.blog.partials.ai-assistant')

{{-- Cover Image Cropper & Editor Modal --}}
@include('nguoi-dung.blog.partials.image-cropper-modal')

{{-- Blog Draft Auto-save & Restoration Manager --}}
@include('nguoi-dung.blog.partials.draft-manager', ['context' => 'modal'])

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    let modalQuill = null;

    document.addEventListener('DOMContentLoaded', function() {
        initModalQuill();
        
        const catSelect = document.getElementById('modal_category_id');
        if (catSelect) {
            catSelect.addEventListener('change', function() {
                const wrapper = document.getElementById('modal_new_category_wrapper');
                const newCatInput = document.getElementById('modal_new_category');
                if (this.value === 'new') {
                    if (wrapper) wrapper.classList.remove('hidden');
                    if (newCatInput) newCatInput.focus();
                } else {
                    if (wrapper) wrapper.classList.add('hidden');
                }
            });
        }

        // Hỗ trợ Kéo & Thả ảnh bìa
        const coverContainer = document.getElementById('modal-cover-container');
        if (coverContainer) {
            ['dragenter', 'dragover'].forEach(eventName => {
                coverContainer.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    coverContainer.classList.add('border-amber-500', 'bg-amber-100/50');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                coverContainer.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    coverContainer.classList.remove('border-amber-500', 'bg-amber-100/50');
                }, false);
            });

            coverContainer.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt ? dt.files : null;
                if (files && files.length > 0) {
                    const input = document.getElementById('modal_cover_image');
                    if (input) {
                        try {
                            const transfer = new DataTransfer();
                            transfer.items.add(files[0]);
                            input.files = transfer.files;
                            previewModalCoverImage(input);
                        } catch(err) {
                            console.error(err);
                        }
                    }
                }
            }, false);
        }
    });

    function initModalQuill() {
        const container = document.getElementById('modal-editor-container');
        if (container && !modalQuill) {
            modalQuill = new Quill('#modal-editor-container', {
                theme: 'snow',
                placeholder: 'Hãy viết nội dung bài viết hấp dẫn tại đây...',
                modules: {
                    toolbar: [
                        [{ 'header': [2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['blockquote', 'link', 'image'],
                        ['clean']
                    ]
                }
            });
        }
    }

    function openCreateBlogModal() {
        try {
            sessionStorage.setItem('qm_blog_modal_open', '1');
            sessionStorage.removeItem('qm_blog_modal_closed');
            if (window.location.hash !== '#tao-bai-viet') {
                history.replaceState(null, null, '#tao-bai-viet');
            }
        } catch(e) {}

        const modal = document.getElementById('createBlogModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('block');
            document.body.classList.add('overflow-hidden');
            modal.scrollTop = 0;
            const scrollBody = document.getElementById('modal-scrollable-body');
            if (scrollBody) scrollBody.scrollTop = 0;
            initModalQuill();
            syncQuickTagStates('modal_custom_tags');
            if (window.restoreBlogDraft) {
                setTimeout(window.restoreBlogDraft, 60);
            }
        }
    }

    function closeCreateBlogModal() {
        try {
            sessionStorage.removeItem('qm_blog_modal_open');
            sessionStorage.setItem('qm_blog_modal_closed', '1');
            if (window.location.hash === '#tao-bai-viet') {
                history.replaceState(null, null, window.location.pathname + window.location.search);
            }
        } catch(e) {}

        const modal = document.getElementById('createBlogModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('block');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function openGuestModal() {
        const modal = document.getElementById('guestBlogModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeGuestModal() {
        const modal = document.getElementById('guestBlogModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateBlogModal();
            closeGuestModal();
        }
    });


    function previewModalCoverImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!file.type.match(/^image\//)) {
                if (typeof showAiToast === 'function') {
                    showAiToast('Vui lòng chọn tệp hình ảnh hợp lệ (JPG, PNG, WEBP).', 'error');
                } else if (typeof showToast === 'function') {
                    showToast({ title: 'Định dạng không hỗ trợ', message: 'Vui lòng chọn ảnh JPG, PNG hoặc WEBP.', type: 'error' });
                }
                input.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                if (typeof showAiToast === 'function') {
                    showAiToast('Dung lượng ảnh không được vượt quá 5MB.', 'error');
                } else if (typeof showToast === 'function') {
                    showToast({ title: 'Ảnh quá lớn', message: 'Dung lượng ảnh không được vượt quá 5MB.', type: 'error' });
                }
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const imageSrc = e.target.result;
                const preview = document.getElementById('modal-cover-preview');
                const placeholder = document.getElementById('modal-cover-placeholder');
                const actionsBar = document.getElementById('modal-cover-actions-bar');
                const fixedBtn = document.getElementById('modal-cover-fixed-edit-btn');
                const badge = document.getElementById('modal-cover-badge');
                const container = document.getElementById('modal-cover-container');

                if (preview) {
                    preview.src = imageSrc;
                    preview.classList.remove('hidden');
                }
                if (placeholder) placeholder.classList.add('hidden');
                if (actionsBar) actionsBar.classList.remove('hidden');
                if (fixedBtn) fixedBtn.classList.remove('hidden');
                if (badge) badge.classList.remove('hidden');
                if (container) {
                    container.classList.remove('border-dashed');
                    container.classList.add('border-solid', 'border-amber-400');
                }

                input.classList.add('pointer-events-none');

                // Tự động mở trình chỉnh sửa ảnh để người dùng cắt cúp theo chuẩn 21:9
                setTimeout(() => {
                    openCoverImageCropper(imageSrc, 'modal');
                }, 120);
            };
            reader.readAsDataURL(file);
        }
    }

    function editModalCoverImage(event) {
        if (event) event.stopPropagation();
        const preview = document.getElementById('modal-cover-preview');
        if (preview && preview.src && !preview.classList.contains('hidden')) {
            openCoverImageCropper(preview.src, 'modal');
        } else {
            const input = document.getElementById('modal_cover_image');
            if (input) {
                input.classList.remove('pointer-events-none');
                input.click();
            }
        }
    }

    function changeModalCoverImage(event) {
        if (event) event.stopPropagation();
        const input = document.getElementById('modal_cover_image');
        if (input) {
            input.classList.remove('pointer-events-none');
            input.click();
        }
    }

    function removeModalCoverImage(event) {
        if (event) event.stopPropagation();
        const input = document.getElementById('modal_cover_image');
        const preview = document.getElementById('modal-cover-preview');
        const placeholder = document.getElementById('modal-cover-placeholder');
        const actionsBar = document.getElementById('modal-cover-actions-bar');
        const fixedBtn = document.getElementById('modal-cover-fixed-edit-btn');
        const badge = document.getElementById('modal-cover-badge');
        const container = document.getElementById('modal-cover-container');

        if (input) {
            input.value = '';
            input.classList.remove('pointer-events-none');
        }
        if (preview) {
            preview.src = '';
            preview.classList.add('hidden');
        }
        if (placeholder) placeholder.classList.remove('hidden');
        if (actionsBar) actionsBar.classList.add('hidden');
        if (fixedBtn) fixedBtn.classList.add('hidden');
        if (badge) badge.classList.add('hidden');
        if (container) {
            container.classList.add('border-dashed');
            container.classList.remove('border-solid', 'border-amber-400');
        }
    }


    function toggleCategoryDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const isHidden = dropdown.classList.contains('hidden');
        document.querySelectorAll('.cat-dropdown-menu, .tag-dropdown-menu').forEach(d => {
            if (d.id !== dropdownId) d.classList.add('hidden');
        });
        if (isHidden) {
            dropdown.querySelectorAll('.cat-suggestion-item').forEach(item => item.classList.remove('hidden'));
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }

    function filterCategorySuggestions(input, dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const query = input.value.trim().toLowerCase();
        dropdown.classList.remove('hidden');
        const items = dropdown.querySelectorAll('.cat-suggestion-item');
        let exactMatch = false;
        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            if (!query || name.includes(query)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
            if (query && name === query) {
                exactMatch = true;
            }
        });

        const customOption = dropdown.querySelector('.cat-custom-option');
        if (customOption) {
            if (query && !exactMatch) {
                customOption.classList.remove('hidden');
                const textSpan = customOption.querySelector('.cat-custom-text');
                if (textSpan) textSpan.textContent = input.value.trim();
            } else {
                customOption.classList.add('hidden');
            }
        }
    }

    function selectCategoryItem(inputId, value, dropdownId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.value = value;
            input.dispatchEvent(new Event('change'));
        }
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
    }

    function selectCustomCategoryItem(inputId, dropdownId) {
        const input = document.getElementById(inputId);
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
        if (input) input.focus();
    }

    function toggleTagDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const isHidden = dropdown.classList.contains('hidden');
        document.querySelectorAll('.tag-dropdown-menu, .cat-dropdown-menu').forEach(d => {
            if (d.id !== dropdownId) d.classList.add('hidden');
        });
        if (isHidden) {
            dropdown.querySelectorAll('.tag-suggestion-item').forEach(item => item.classList.remove('hidden'));
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }

    function filterTagSuggestions(input, dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const rawTerms = input.value.split(',');
        const currentTerm = rawTerms[rawTerms.length - 1].trim().toLowerCase().replace(/^#/, '');
        dropdown.classList.remove('hidden');
        const items = dropdown.querySelectorAll('.tag-suggestion-item');
        let exactMatch = false;
        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase().replace(/^#/, '');
            if (!currentTerm || name.includes(currentTerm)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
            if (currentTerm && name === currentTerm) {
                exactMatch = true;
            }
        });

        const customOption = dropdown.querySelector('.tag-custom-option');
        if (customOption) {
            const rawCurrent = rawTerms[rawTerms.length - 1].trim().replace(/^#/, '');
            if (rawCurrent && !exactMatch) {
                customOption.classList.remove('hidden');
                const textSpan = customOption.querySelector('.tag-custom-text');
                if (textSpan) textSpan.textContent = rawCurrent;
            } else {
                customOption.classList.add('hidden');
            }
        }
    }

    function handleTagInputKeydown(event, input, dropdownId) {
        if (event.key === 'Enter') {
            event.preventDefault();
            commitCurrentTag(input.id, dropdownId);
        }
    }

    function commitCurrentTag(inputId, dropdownId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const rawTerms = input.value.split(',');
        const currentTerm = rawTerms[rawTerms.length - 1].trim().replace(/^#/, '');
        if (currentTerm) {
            let terms = rawTerms.map(t => t.trim().replace(/^#/, '')).filter(t => t.length > 0);
            let uniqueTerms = [];
            terms.forEach(t => {
                if (!uniqueTerms.includes(t)) uniqueTerms.push(t);
            });
            input.value = uniqueTerms.join(', ') + ', ';
        } else if (input.value.trim().length > 0 && !input.value.trim().endsWith(',')) {
            input.value = input.value.trim() + ', ';
        }
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
        syncQuickTagStates(inputId);
    }

    function selectCustomTagItem(inputId, dropdownId) {
        commitCurrentTag(inputId, dropdownId);
        const input = document.getElementById(inputId);
        if (input) input.focus();
    }

    function selectTagItem(inputId, tagName, dropdownId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        tagName = tagName.replace(/^#/, '');
        let terms = input.value.split(',').map(t => t.trim().replace(/^#/, '')).filter(t => t.length > 0);
        if (!terms.includes(tagName)) {
            if (terms.length > 0) terms.pop();
            terms.push(tagName);
        }
        input.value = terms.join(', ') + ', ';
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
        syncQuickTagStates(inputId);
    }

    function addQuickTag(inputId, tagName) {
        const input = document.getElementById(inputId);
        if (!input) return;
        tagName = tagName.replace(/^#/, '').trim();
        let terms = input.value.split(',').map(t => t.trim().replace(/^#/, '')).filter(t => t.length > 0);
        const lowerList = terms.map(t => t.toLowerCase());
        const index = lowerList.indexOf(tagName.toLowerCase());
        if (index >= 0) {
            // Already present -> Toggle off (remove)
            terms.splice(index, 1);
        } else {
            // Not present -> Toggle on (add)
            terms.push(tagName);
        }
        input.value = terms.length > 0 ? terms.join(', ') + ', ' : '';
        input.dispatchEvent(new Event('input', { bubbles: true }));
        syncQuickTagStates(inputId);
    }

    function syncQuickTagStates(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        const container = document.querySelector(`.quick-tags-container[data-target="${inputId}"]`);
        if (!container) return;

        // Lấy danh sách thẻ người dùng đang nhập / vừa sử dụng
        const rawTerms = input.value.split(',').map(t => t.trim().replace(/^#/, '')).filter(t => t.length > 0);
        const currentTerms = [];
        rawTerms.forEach(t => {
            if (!currentTerms.some(c => c.toLowerCase() === t.toLowerCase())) {
                currentTerms.push(t);
            }
        });

        // Lấy danh sách thẻ phổ biến mặc định từ data-popular
        let popularList = [];
        try {
            popularList = JSON.parse(container.getAttribute('data-popular') || '[]');
        } catch(e) {}
        if (!popularList || popularList.length === 0) {
            popularList = ['ReviewQuan', 'MonNgon', 'AmThuc', 'KhongGianChill', 'DiaDiemAnUong', 'KhamPha', 'GocChuQuan', 'KhuyenMai'];
        }

        let html = '';

        // 1. Thẻ vừa dùng / đang dùng (luôn hiện lên đầu với màu cam nổi bật & dấu tích ✓)
        currentTerms.forEach(term => {
            const safeTerm = term.replace(/'/g, "\\'");
            html += `
                <button type="button" 
                        data-tag="${term.toLowerCase()}"
                        onclick="addQuickTag('${inputId}', '${safeTerm}')"
                        class="quick-tag-btn px-2.5 py-1 text-[11px] rounded-full border border-amber-600 bg-amber-600 text-white font-bold transition-all flex items-center gap-1 cursor-pointer select-none shadow-2xs active:scale-95"
                        title="Bấm để bỏ thẻ #${term}">
                    <span class="material-symbols-outlined text-xs tag-icon">check</span>
                    <span>#${term}</span>
                </button>
            `;
        });

        // 2. Vài thẻ phổ biến chưa dùng (chỉ hiện tối đa 6 gợi ý để thanh thẻ luôn gọn gàng)
        let unselectedCount = 0;
        popularList.forEach(popTag => {
            const isAlreadySelected = currentTerms.some(c => c.toLowerCase() === popTag.toLowerCase());
            if (!isAlreadySelected && unselectedCount < 6) {
                unselectedCount++;
                const safeTag = popTag.replace(/'/g, "\\'");
                html += `
                    <button type="button" 
                            data-tag="${popTag.toLowerCase()}"
                            onclick="addQuickTag('${inputId}', '${safeTag}')"
                            class="quick-tag-btn px-2.5 py-1 text-[11px] rounded-full border border-amber-200/80 bg-amber-50/60 text-amber-800 font-medium hover:bg-amber-100 hover:border-amber-300 transition-all flex items-center gap-1 cursor-pointer select-none active:scale-95"
                            title="Bấm để thêm nhanh #${popTag}">
                        <span class="material-symbols-outlined text-xs tag-icon">add</span>
                        <span>#${popTag}</span>
                    </button>
                `;
            }
        });

        container.innerHTML = html;
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.autocomplete-wrapper')) {
            document.querySelectorAll('.cat-dropdown-menu, .tag-dropdown-menu').forEach(d => d.classList.add('hidden'));
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.cat-dropdown-menu, .tag-dropdown-menu').forEach(d => d.classList.add('hidden'));
        }
    });

    function submitModalBlogForm(action) {
        const titleInput = document.getElementById('modal_title');
        const catInput = document.getElementById('modal_category_name');
        
        const title = titleInput ? titleInput.value.trim() : '';
        const catVal = catInput ? catInput.value.trim() : '';

        let html = '';

        if (modalQuill) {
            html = modalQuill.root.innerHTML;
            if (html === '<p><br></p>') html = '';
            document.getElementById('modal-content-input').value = html;
        }

        const alertBox = document.getElementById('modal-error-alert');

        if (!title || !catVal || !html.trim()) {
            if (alertBox) {
                alertBox.classList.remove('hidden');
                alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            if (typeof showAiToast === 'function') {
                showAiToast('Vui lòng nhập đầy đủ Tiêu đề, Danh mục và Nội dung bài viết!', 'warning');
            } else if (typeof showToast === 'function') {
                showToast({ title: 'Thiếu thông tin', message: 'Vui lòng nhập đầy đủ Tiêu đề, Danh mục và Nội dung bài viết!', type: 'warning' });
            }
            return;
        }

        if (alertBox) alertBox.classList.add('hidden');

        // Clear local draft when submitting
        if (window.clearDraftOnSubmit) {
            window.clearDraftOnSubmit();
        }

        document.getElementById('modal-action-input').value = action;
        
        const form = document.getElementById('modal-blog-form');
        if (form) {
            form.submit();
        }
    }

    // Live Blog Search Suggestions
    let searchDebounceTimer = null;
    function handleBlogSearchInput(input) {
        clearTimeout(searchDebounceTimer);
        const query = input.value.trim();
        const suggestionsBox = document.getElementById('blog-search-suggestions');
        const listContainer = document.getElementById('blog-suggestions-list');

        if (!suggestionsBox || !listContainer) return;

        if (query.length < 1) {
            suggestionsBox.classList.add('hidden');
            listContainer.innerHTML = '';
            return;
        }

        searchDebounceTimer = setTimeout(() => {
            fetch(`/blog?suggest=1&search=${encodeURIComponent(query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                listContainer.innerHTML = '';
                if (data && data.length > 0) {
                    data.forEach(item => {
                        const row = document.createElement('a');
                        row.href = item.url;
                        row.className = 'flex items-center gap-3 p-3 hover:bg-amber-50/80 transition-colors group cursor-pointer';
                        row.innerHTML = `
                            <img src="${item.cover_url}" class="w-12 h-10 object-cover rounded-lg shrink-0 border border-slate-100" />
                            <div class="flex-1 min-w-0">
                                <h5 class="text-xs font-bold text-slate-800 group-hover:text-amber-700 truncate">${item.title}</h5>
                                <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-400">
                                    ${item.category ? `<span class="text-amber-700 font-semibold">${item.category}</span>` : ''}
                                    ${item.published_date ? `<span>• ${item.published_date}</span>` : ''}
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-sm text-slate-300 group-hover:text-amber-600 shrink-0">chevron_right</span>
                        `;
                        listContainer.appendChild(row);
                    });
                    suggestionsBox.classList.remove('hidden');
                } else {
                    listContainer.innerHTML = `
                        <div class="p-4 text-center text-xs text-slate-400 flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-slate-300">search_off</span>
                            <span>Không có bài viết khớp</span>
                        </div>
                    `;
                    suggestionsBox.classList.remove('hidden');
                }
            })
            .catch(() => {
                suggestionsBox.classList.add('hidden');
            });
        }, 250);
    }

    document.addEventListener('click', function(e) {
        const searchForm = document.getElementById('blog-search-form');
        const suggestionsBox = document.getElementById('blog-search-suggestions');
        if (suggestionsBox && searchForm && !searchForm.contains(e.target)) {
            suggestionsBox.classList.add('hidden');
        }
    });

    // Chỉ tự động mở lại modal tạo bài viết khi có lỗi validation từ server
    @if($errors->any() && auth()->check())
    document.addEventListener('DOMContentLoaded', function() {
        openCreateBlogModal();
    });
    @else
    document.addEventListener('DOMContentLoaded', function() {
        try {
            sessionStorage.removeItem('qm_blog_modal_open');
        } catch(e) {}
    });
    @endif

    // Luôn làm mới dữ liệu lượt xem khi quay lại trang bằng nút Back để đồng bộ với trang chi tiết
    window.addEventListener('pageshow', function(event) {
        const isBack = event.persisted || (window.performance && (
            (window.performance.navigation && window.performance.navigation.type === 2) ||
            (window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0] && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')
        ));
        if (isBack) {
            window.location.reload();
        }
    });

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

    // Tự động đồng bộ số lượt xem theo thời gian thực mà KHÔNG CẦN TẢI LẠI TRANG
    (function() {
        function updateBlogViewInDom(data) {
            if (!data || (!data.slug && !data.blogId)) return;
            
            const selectors = [];
            if (data.slug) selectors.push(`[data-blog-slug="${data.slug}"]`);
            if (data.blogId) selectors.push(`[data-blog-id="${data.blogId}"]`);
            
            const containers = document.querySelectorAll(selectors.join(', '));
            if (!containers || containers.length === 0) return;

            containers.forEach(wrap => {
                const textEl = wrap.querySelector('.blog-view-count-text') || wrap;
                if (textEl) {
                    textEl.textContent = `${data.formatted} lượt xem`;
                }
                if (wrap.hasAttribute('title')) {
                    wrap.setAttribute('title', `${data.formatted} lượt xem`);
                }
                // Hiệu ứng viền sáng amber nhấp nháy báo hiệu cập nhật trực tiếp thành công
                wrap.classList.add('border-amber-400', 'ring-2', 'ring-amber-400/50', 'bg-amber-50/60', 'transition-all', 'duration-500');
                setTimeout(() => {
                    wrap.classList.remove('border-amber-400', 'ring-2', 'ring-amber-400/50', 'bg-amber-50/60');
                }, 1800);
            });
        }

        // 1. Nhận qua BroadcastChannel giữa các tab
        if ('BroadcastChannel' in window) {
            try {
                const bc = new BroadcastChannel('qm_blog_views_sync');
                bc.onmessage = function(ev) {
                    if (ev.data) updateBlogViewInDom(ev.data);
                };
            } catch(e) {}
        }

        // 2. Nhận qua storage event
        window.addEventListener('storage', function(e) {
            if (e.key === 'qm_latest_blog_view' && e.newValue) {
                try {
                    const data = JSON.parse(e.newValue);
                    updateBlogViewInDom(data);
                } catch(err) {}
            }
        });

        // 3. Khi bấm Back từ trang chi tiết về trang blog (BFCache / pageshow)
        window.addEventListener('pageshow', function() {
            try {
                const raw = localStorage.getItem('qm_latest_blog_view');
                if (raw) {
                    const data = JSON.parse(raw);
                    if (data && (Date.now() - (data.timestamp || 0)) < 3600000) {
                        updateBlogViewInDom(data);
                    }
                }
            } catch(err) {}
        });
    })();
</script>
@endpush
