@extends('layouts.app')

@section('title', 'Blog - Quán Mới | Khám phá ẩm thực địa phương')
@section('description', 'Chia sẻ những bài viết review, mẹo kinh doanh và trải nghiệm ẩm thực hấp dẫn nhất.')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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

    <!-- Flash Success / Error Messages in Vietnamese -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between shadow-sm animate-modal-in">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-600 text-2xl">check_circle</span>
                <div>
                    <h4 class="font-bold text-sm">Thành công!</h4>
                    <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold p-1">
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
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center overflow-hidden border border-white/30">
                    <img class="w-full h-full object-cover" src="{{ $heroBlog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($heroBlog->user->ho_ten).'&background=random' }}"/>
                </div>
                <div class="text-white">
                    <p class="font-label-md text-label-md font-semibold">Bởi {{ $heroBlog->user->ho_ten }}</p>
                    <p class="text-xs opacity-75">{{ $heroBlog->published_at ? $heroBlog->published_at->format('d/m/Y') : '' }} • {{ number_format($heroBlog->view_count ?? 0) }} lượt xem</p>
                </div>
            </div>
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
            <form action="{{ route('blog.index') }}" method="GET" class="relative flex-grow md:w-60">
                @if(request()->has('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request()->has('tag'))
                    <input type="hidden" name="tag" value="{{ request('tag') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm bài viết..." class="w-full pl-10 pr-4 py-2 bg-surface-container rounded-full text-body-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none border-none shadow-inner">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-muted text-[20px]">search</span>
            </form>

            @auth
                <button onclick="openCreateBlogModal()" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-gradient-to-r from-amber-600 via-primary to-orange-600 text-white text-xs font-bold hover:shadow-md active:scale-95 transition-all cursor-pointer whitespace-nowrap shadow-sm border border-white/20">
                    <span class="material-symbols-outlined text-[16px]">edit_square</span>
                    <span>Đăng bài</span>
                </button>
            @else
                <button onclick="openGuestModal()" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-gradient-to-r from-amber-600 via-primary to-orange-600 text-white text-xs font-bold hover:shadow-md active:scale-95 transition-all cursor-pointer whitespace-nowrap shadow-sm border border-white/20">
                    <span class="material-symbols-outlined text-[16px]">edit_square</span>
                    <span>Đăng bài</span>
                </button>
            @endauth
        </div>
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
                                    <div class="absolute top-3 left-3 z-10 bg-primary-container text-on-primary px-2 py-0.5 rounded-md font-label-md text-label-md uppercase font-bold">{{ $blog->category->name }}</div>
                                @endif
                                <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" 
                                     src="{{ $blog->cover_image_url ?? 'https://placehold.co/600x400/png' }}" alt="{{ $blog->title }}"/>
                            </a>
                            <div class="p-4 flex flex-col flex-grow">
                                <a href="{{ route('blog.show', $blog->slug) }}" class="flex-grow block">
                                    <h3 class="font-title-md text-title-md text-on-surface mb-2 line-clamp-2 group-hover:text-primary transition-colors font-bold">{{ $blog->title }}</h3>
                                    <p class="text-text-muted font-body-sm text-body-sm mb-4 line-clamp-3">{{ $blog->excerpt ?? Str::limit(strip_tags($blog->content), 120) }}</p>
                                </a>
                                <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                                    <span class="font-label-sm text-label-sm text-text-muted">{{ Str::limit($blog->user->ho_ten ?? 'Tác giả', 15) }} • {{ $blog->published_at ? $blog->published_at->format('d/m/Y') : '' }}</span>
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
                    <button onclick="openCreateBlogModal()" type="button" class="w-full py-2.5 bg-primary text-white font-bold text-xs rounded-xl shadow-sm hover:opacity-90 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">edit</span> Viết bài mới ngay
                    </button>
                @else
                    <button onclick="openGuestModal()" type="button" class="w-full py-2.5 bg-primary text-white font-bold text-xs rounded-xl shadow-sm hover:opacity-90 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">lock</span> Đăng nhập để viết bài
                    </button>
                @endauth
            </div>

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
                                <p class="text-[10px] text-text-muted mt-1 uppercase tracking-tighter">{{ number_format($popularBlog->view_count ?? 0) }} lượt xem</p>
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
<div id="createBlogModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
    <div class="relative w-full max-w-3xl my-8 bg-white rounded-3xl shadow-2xl border border-outline-variant overflow-hidden animate-modal-in flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-amber-600 via-primary to-orange-600 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30">
                    <span class="material-symbols-outlined text-2xl">edit_note</span>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-white">Tạo Bài Viết Blog Mới</h3>
                    <p class="text-xs text-white/80">Chia sẻ trải nghiệm & giới thiệu quán cho cộng đồng</p>
                </div>
            </div>
            <button onclick="closeCreateBlogModal()" type="button" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body Form -->
        <form action="{{ route('nguoi-dung.blog.store') }}" method="POST" enctype="multipart/form-data" id="modal-blog-form" class="p-6 overflow-y-auto space-y-6 flex-grow">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ route('blog.index') }}">
            <input type="hidden" name="action" id="modal-action-input" value="pending">
            <input type="hidden" name="content" id="modal-content-input">

            <!-- Client Validation Alert Box -->
            <div id="modal-error-alert" class="hidden p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-rose-600 text-lg">warning</span>
                <span>Vui lòng nhập đầy đủ Tiêu đề, chọn Danh mục và điền Nội dung bài viết trước khi gửi!</span>
            </div>

            <!-- Cover Image Upload -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2">Ảnh bìa bài viết</label>
                <div class="relative w-full aspect-[21/9] bg-surface-container rounded-2xl overflow-hidden border-2 border-dashed border-outline-variant flex items-center justify-center group cursor-pointer hover:border-primary transition-colors" id="modal-cover-container">
                    <input type="file" name="cover_image" id="modal_cover_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewModalCoverImage(this)">
                    
                    <div class="text-center p-4" id="modal-cover-placeholder">
                        <span class="material-symbols-outlined text-4xl text-primary mb-1">add_photo_alternate</span>
                        <p class="text-sm font-bold text-on-surface">Nhấn hoặc kéo thả để tải ảnh bìa bài viết</p>
                        <p class="text-xs text-text-muted mt-1">Định dạng JPG, PNG, WEBP (Khuyên dùng tỷ lệ 21:9, tối đa 5MB)</p>
                    </div>
                    
                    <img id="modal-cover-preview" src="" class="absolute inset-0 w-full h-full object-cover hidden">
                    
                    <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center z-20 pointer-events-none transition-opacity" id="modal-cover-overlay">
                        <span class="text-white font-bold text-sm bg-black/50 px-4 py-2 rounded-xl backdrop-blur-sm flex items-center gap-2"><span class="material-symbols-outlined text-lg">edit</span> Thay đổi ảnh</span>
                    </div>
                </div>
            </div>

            <!-- Title -->
            <div>
                <label for="modal_title" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2">Tiêu đề bài viết <span class="text-red-500">*</span></label>
                <input type="text" id="modal_title" name="title" value="{{ old('title') }}" required placeholder="Ví dụ: Review chi tiết món lẩu nấm cực ngon tại Quán Mới..." class="w-full rounded-2xl border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary text-base font-bold py-3 px-4 shadow-sm">
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

                $modalTags = \App\Models\BlogTag::all();
                if ($modalTags->isEmpty()) {
                    $defaultTagNames = ['ReviewQuan', 'MonNgon', 'KhamPha', 'GocChuQuan', 'AmThuc', 'KhuyenMai', 'MonMoi', 'TraiNghiem'];
                    foreach ($defaultTagNames as $name) {
                        \App\Models\BlogTag::firstOrCreate(
                            ['slug' => \Illuminate\Support\Str::slug($name)],
                            ['name' => $name]
                        );
                    }
                    $modalTags = \App\Models\BlogTag::all();
                }
            @endphp

            <!-- Category & Tags Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category with Live Autocomplete Suggestions -->
                <div>
                    <label for="modal_category_name" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2">Danh mục bài viết <span class="text-red-500">*</span></label>
                    <div class="relative autocomplete-wrapper">
                        <input type="text" 
                               id="modal_category_name" 
                               name="category_name" 
                               value="{{ old('category_name', old('new_category')) }}" 
                               placeholder="Gõ hoặc chọn danh mục (VD: Review Quán, Món Ngon)..." 
                               autocomplete="off"
                               required
                               class="w-full rounded-2xl border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary font-bold text-sm py-3 px-4 pr-10 shadow-sm"
                               onfocus="showCategorySuggestions('modal_cat_dropdown')"
                               oninput="filterCategorySuggestions(this, 'modal_cat_dropdown')">
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-text-muted pointer-events-none text-xl">expand_more</span>
                        
                        <!-- Suggestions Dropdown list attached right below input -->
                        <div id="modal_cat_dropdown" class="absolute left-0 right-0 top-full mt-1 bg-white border border-amber-300 rounded-2xl shadow-xl z-50 max-h-48 overflow-y-auto hidden divide-y divide-slate-100">
                            @foreach($modalCategories as $category)
                                <div class="cat-suggestion-item px-4 py-2.5 hover:bg-amber-50 cursor-pointer text-xs font-bold text-on-surface flex items-center justify-between transition-colors"
                                     data-name="{{ strtolower($category->name) }}"
                                     onclick="selectCategoryItem('modal_category_name', '{{ addslashes($category->name) }}', 'modal_cat_dropdown')">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-amber-600 text-sm">folder</span>
                                        <span>{{ $category->name }}</span>
                                    </span>
                                    <span class="text-[10px] text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full font-bold">Gợi ý danh mục</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Tags with Live Autocomplete Suggestions & Quick Badges -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="modal_custom_tags" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant">Thẻ bài viết (Tags)</label>
                        <span class="text-[11px] font-bold text-primary">Gõ trực tiếp hoặc chọn bên dưới</span>
                    </div>
                    <div class="relative autocomplete-wrapper">
                        <input type="text" 
                               id="modal_custom_tags" 
                               name="custom_tags" 
                               value="{{ old('custom_tags') }}" 
                               placeholder="Gõ tên thẻ (VD: Lẩu thái, Ăn đêm, Monngon)..." 
                               autocomplete="off"
                               class="w-full rounded-2xl border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary text-xs font-semibold py-2.5 px-4 pr-10 shadow-sm"
                               onfocus="showTagSuggestions('modal_tag_dropdown')"
                               oninput="filterTagSuggestions(this, 'modal_tag_dropdown')">
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-text-muted pointer-events-none text-lg">local_offer</span>
                        
                        <!-- Tag Suggestions Dropdown List attached right below input -->
                        <div id="modal_tag_dropdown" class="absolute left-0 right-0 top-full mt-1 bg-white border border-amber-300 rounded-2xl shadow-xl z-50 max-h-44 overflow-y-auto hidden divide-y divide-slate-100">
                            @foreach($modalTags as $tag)
                                <div class="tag-suggestion-item px-4 py-2 hover:bg-amber-50 cursor-pointer text-xs font-bold text-on-surface flex items-center justify-between transition-colors"
                                     data-name="{{ strtolower($tag->name) }}"
                                     onclick="selectTagItem('modal_custom_tags', '{{ addslashes($tag->name) }}', 'modal_tag_dropdown')">
                                    <span class="flex items-center gap-1.5">
                                        <span class="text-primary font-bold">#</span>
                                        <span>{{ $tag->name }}</span>
                                    </span>
                                    <span class="text-[10px] text-text-muted">Gợi ý thẻ</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Quick Tag Badges -->
                    <div class="flex flex-wrap gap-1.5 mt-2 max-h-20 overflow-y-auto p-2 bg-surface-container/50 rounded-xl border border-outline-variant/50">
                        @foreach($modalTags as $tag)
                            <button type="button" 
                                    onclick="addQuickTag('modal_custom_tags', '{{ addslashes($tag->name) }}')"
                                    class="px-2.5 py-1 text-[11px] rounded-full border border-outline-variant bg-white text-on-surface-variant font-bold hover:border-primary hover:text-primary transition-all flex items-center gap-1 cursor-pointer select-none">
                                <span>+#{{ $tag->name }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Excerpt -->
            <div>
                <label for="modal_excerpt" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2">Mô tả ngắn (Excerpt)</label>
                <textarea id="modal_excerpt" name="excerpt" rows="2" placeholder="Tóm tắt nội dung hấp dẫn nhất của bài viết..." class="w-full rounded-2xl border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary text-sm p-3 resize-none">{{ old('excerpt') }}</textarea>
            </div>

            <!-- Content Quill Editor -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2">Nội dung bài viết <span class="text-red-500">*</span></label>
                <div id="modal-editor-container">
                    {!! old('content') !!}
                </div>
            </div>

            <!-- Form Footer Buttons -->
            <div class="pt-4 border-t border-outline-variant flex flex-col sm:flex-row items-center justify-between gap-3 sticky bottom-0 bg-white z-20 py-2">
                <a href="{{ route('nguoi-dung.blog.create') }}" class="text-xs text-primary font-bold hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">open_in_new</span> Mở giao diện soạn thảo đầy đủ
                </a>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button type="button" onclick="submitModalBlogForm('draft')" class="w-1/2 sm:w-auto px-5 py-2.5 rounded-full font-bold text-xs text-on-surface-variant bg-surface-container hover:bg-surface-container-high transition-colors">
                        Lưu bản nháp
                    </button>
                    <button type="button" onclick="submitModalBlogForm('pending')" class="w-1/2 sm:w-auto px-6 py-2.5 rounded-full font-bold text-xs text-white bg-gradient-to-r from-amber-600 via-primary to-orange-600 hover:opacity-95 transition-all shadow-md flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-base">send</span> Gửi duyệt bài
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endauth

<!-- ==================== GUEST LOGIN MODAL ==================== -->
<div id="guestBlogModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
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

@endsection

@push('scripts')
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
        const modal = document.getElementById('createBlogModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            initModalQuill();
        }
    }

    function closeCreateBlogModal() {
        const modal = document.getElementById('createBlogModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
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

    function previewModalCoverImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('modal-cover-preview');
                const placeholder = document.getElementById('modal-cover-placeholder');
                const overlay = document.getElementById('modal-cover-overlay');
                
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if (placeholder) placeholder.classList.add('hidden');
                if (overlay) overlay.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function showCategorySuggestions(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.remove('hidden');
    }

    function filterCategorySuggestions(input, dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const query = input.value.trim().toLowerCase();
        dropdown.classList.remove('hidden');
        const items = dropdown.querySelectorAll('.cat-suggestion-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            if (!query || name.includes(query)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    function selectCategoryItem(inputId, value, dropdownId) {
        const input = document.getElementById(inputId);
        if (input) input.value = value;
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
    }

    function showTagSuggestions(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.remove('hidden');
    }

    function filterTagSuggestions(input, dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const terms = input.value.split(',');
        const currentTerm = terms[terms.length - 1].trim().toLowerCase();
        dropdown.classList.remove('hidden');
        const items = dropdown.querySelectorAll('.tag-suggestion-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            if (!currentTerm || name.includes(currentTerm)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    function selectTagItem(inputId, tagName, dropdownId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let terms = input.value.split(',').map(t => t.trim()).filter(t => t.length > 0);
        if (!terms.includes(tagName)) {
            if (terms.length > 0) terms.pop();
            terms.push(tagName);
        }
        input.value = terms.join(', ') + ', ';
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
    }

    function addQuickTag(inputId, tagName) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let terms = input.value.split(',').map(t => t.trim()).filter(t => t.length > 0);
        if (!terms.includes(tagName)) {
            terms.push(tagName);
        }
        input.value = terms.join(', ') + ', ';
        const dropdown = document.getElementById('modal_tag_dropdown');
        if (dropdown) dropdown.classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.autocomplete-wrapper')) {
            document.querySelectorAll('#modal_cat_dropdown, #modal_tag_dropdown, #create_cat_dropdown, #create_tag_dropdown, #edit_cat_dropdown, #edit_tag_dropdown').forEach(d => d.classList.add('hidden'));
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('#modal_cat_dropdown, #modal_tag_dropdown, #create_cat_dropdown, #create_tag_dropdown, #edit_cat_dropdown, #edit_tag_dropdown').forEach(d => d.classList.add('hidden'));
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
            } else {
                alert('Vui lòng nhập đầy đủ Tiêu đề, Danh mục và Nội dung bài viết!');
            }
            return;
        }

        if (alertBox) alertBox.classList.add('hidden');

        document.getElementById('modal-action-input').value = action;
        
        const form = document.getElementById('modal-blog-form');
        if (form) {
            form.submit();
        }
    }

    // Auto open modal if there are validation errors
    @if($errors->any() && auth()->check())
        document.addEventListener('DOMContentLoaded', function() {
            openCreateBlogModal();
        });
    @endif
</script>
@endpush
