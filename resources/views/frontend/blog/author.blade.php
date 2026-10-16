@extends('layouts.app')

@section('title', 'Tác giả ' . $author->ho_ten . ' - Hồ sơ cây bút ẩm thực | Quán Mới')
@section('description', 'Khám phá tất cả các bài viết review quán cà phê, quán ăn và cẩm nang ẩm thực đặc sắc từ tác giả ' . $author->ho_ten . ' trên Quán Mới.')

@push('styles')
<style>
    .author-master-card {
        background: linear-gradient(180deg, #ffffff 0%, #fffdfa 100%);
        border-radius: 1.5rem;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(226, 232, 240, 0.9);
        padding: 2rem;
        position: relative;
    }
    .author-hero-flex {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: 2rem;
        width: 100%;
    }
    .author-avatar-frame {
        width: 110px;
        height: 110px;
        min-width: 110px;
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 8px 20px -4px rgba(160, 65, 0, 0.12);
        border: 3px solid #ffffff;
        background: #ffdbcc;
        position: relative;
    }
    .author-body-col {
        flex: 1 1 0%;
        min-width: 0;
        width: 100%;
    }
    .author-kpi-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.875rem;
        width: 100%;
        margin-top: 1.25rem;
    }
    .author-action-col {
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
        width: 180px;
        min-width: 180px;
    }
    .article-cover-frame {
        position: relative;
        width: 100%;
        height: 205px;
        overflow: hidden;
        display: block;
        background-color: #f1f5f9;
        border-radius: 1rem 1rem 0 0;
    }
    .article-cover-frame img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.4s ease;
    }
    .article-zoom-card:hover .article-cover-frame img {
        transform: scale(1.06);
    }
    @media (max-width: 1024px) {
        .author-hero-flex {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .author-action-col {
            width: 100%;
            flex-direction: row;
            justify-content: center;
        }
        .author-kpi-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 640px) {
        .author-avatar-frame {
            width: 90px;
            height: 90px;
            min-width: 90px;
        }
        .author-master-card {
            padding: 1.25rem;
        }
        .article-cover-frame {
            height: 185px;
        }
    }
</style>
@endpush

@section('content')
<main class="min-h-screen pb-16 bg-slate-50/60">
    
    <!-- Clean & Light Top Navigation Bar -->
    <div class="max-w-[1200px] mx-auto px-container-margin pt-6 pb-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium overflow-x-auto whitespace-nowrap">
                <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('home') }}">
                    <span class="material-symbols-outlined text-[17px]">home</span>
                    <span>Trang chủ</span>
                </a>
                <span class="material-symbols-outlined text-[14px] text-slate-300">chevron_right</span>
                <a class="hover:text-primary transition-colors" href="{{ route('blog.index') }}">Blog Ẩm Thực</a>
                <span class="material-symbols-outlined text-[14px] text-slate-300">chevron_right</span>
                <span class="text-slate-800 font-bold">Tác giả {{ $author->ho_ten }}</span>
            </nav>

            <a href="{{ route('blog.index') }}" class="px-3.5 py-1.5 rounded-full bg-white hover:bg-slate-50 text-slate-700 hover:text-primary text-xs font-bold border border-slate-200 transition-all inline-flex items-center gap-1.5 shadow-2xs">
                <span class="material-symbols-outlined text-[15px]">arrow_back</span>
                <span>Về trang Blog</span>
            </a>
        </div>
    </div>

    <!-- Clean, Bright Author Profile Header Card -->
    <div class="max-w-[1200px] mx-auto px-container-margin mb-8">
        <div class="author-master-card">
            <div class="author-hero-flex">
                
                <!-- Avatar Column -->
                <div class="shrink-0 relative">
                    <div class="author-avatar-frame ring-4 ring-primary/10">
                        <img class="w-full h-full object-cover" 
                             src="{{ $author->anh_dai_dien_url }}" 
                             alt="{{ $author->ho_ten }}">
                    </div>
                    @if($author->hasRole('chu_quan') || $author->isAdmin())
                        <div class="absolute -bottom-2 -right-2 bg-blue-600 text-white rounded-full p-1 shadow-md ring-2 ring-white" title="Tác giả được xác thực & tín nhiệm">
                            <span class="material-symbols-outlined text-[18px] block" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                    @else
                        <div class="absolute -bottom-2 -right-2 bg-primary text-white rounded-full p-1 shadow-md ring-2 ring-white" title="Cây bút ẩm thực Quán Mới">
                            <span class="material-symbols-outlined text-[18px] block" style="font-variation-settings: 'FILL' 1;">editor_choice</span>
                        </div>
                    @endif
                </div>

                <!-- Identity & Info Column -->
                <div class="author-body-col">
                    <div class="flex flex-wrap items-center gap-2 mb-2 justify-center lg:justify-start">
                        <span class="px-3 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-primary text-white shadow-2xs">
                            {{ $author->ten_vai_tro_hien_thi ?? 'Tác giả Ẩm thực' }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Đang tích cực viết</span>
                        </span>
                        <span class="text-xs text-slate-500 flex items-center gap-1 bg-slate-100 px-2.5 py-0.5 rounded-full font-medium">
                            <span class="material-symbols-outlined text-[14px] text-slate-400">location_on</span>
                            <span>TP. Hồ Chí Minh</span>
                        </span>
                        <span class="text-xs text-slate-500 flex items-center gap-1 bg-slate-100 px-2.5 py-0.5 rounded-full font-medium">
                            <span class="material-symbols-outlined text-[14px] text-slate-400">calendar_today</span>
                            <span>Gia nhập: {{ $author->created_at ? $author->created_at->format('m/Y') : '2026' }}</span>
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-2">
                        {{ $author->ho_ten }}
                    </h1>

                    <!-- Author Editorial Philosophy -->
                    <div class="border-l-3 border-primary/50 bg-amber-50/50 rounded-r-2xl p-3.5 my-3 text-left">
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium italic">
                            @if($author->isAdmin())
                                "Mỗi bài viết là một góc nhìn độc lập, nghiêm túc và có trách nhiệm với độc giả. Tôi luôn nỗ lực thẩm định những quán ăn có bản sắc, mang đến thông tin ẩm thực trung thực và đáng tin cậy nhất cho cộng đồng Quán Mới."
                            @elseif($author->hasRole('chu_quan'))
                                "Là một người gắn bó với nghề dịch vụ ẩm thực, tôi tin rằng từng món ăn đều có một câu chuyện và linh hồn riêng. Tôi chia sẻ những bài viết này để kết nối thực khách với những hương vị chân thật nhất."
                            @else
                                "Niềm vui lớn nhất là được lang thang khắp các ngõ hẻm Sài Gòn, tìm kiếm những góc cà phê yên ả để làm việc và những món ngon dân dã ít người biết để chia sẻ lại cùng những tâm hồn đồng điệu."
                            @endif
                        </p>
                    </div>

                    <!-- 4 High-Impact KPI Counter Cards -->
                    <div class="author-kpi-row">
                        <div class="p-3 bg-white rounded-2xl border border-slate-200/80 text-center hover:border-primary/40 hover:shadow-2xs transition-all">
                            <div class="w-7 h-7 mx-auto mb-1 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[16px]">edit_note</span>
                            </div>
                            <div class="text-xl sm:text-2xl font-black text-primary">{{ number_format($totalBlogs) }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bài viết</div>
                        </div>

                        <div class="p-3 bg-white rounded-2xl border border-slate-200/80 text-center hover:border-amber-300 hover:shadow-2xs transition-all">
                            <div class="w-7 h-7 mx-auto mb-1 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-600">
                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                            </div>
                            <div class="text-xl sm:text-2xl font-black text-slate-800">{{ number_format($totalViews) }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lượt xem</div>
                        </div>

                        <div class="p-3 bg-white rounded-2xl border border-slate-200/80 text-center hover:border-blue-300 hover:shadow-2xs transition-all">
                            <div class="w-7 h-7 mx-auto mb-1 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-600">
                                <span class="material-symbols-outlined text-[16px]">forum</span>
                            </div>
                            <div class="text-xl sm:text-2xl font-black text-slate-800">{{ number_format($totalComments) }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bình luận</div>
                        </div>

                        <div class="p-3 bg-white rounded-2xl border border-slate-200/80 text-center hover:border-purple-300 hover:shadow-2xs transition-all" title="{{ $topCategory->name ?? 'Đa dạng' }}">
                            <div class="w-7 h-7 mx-auto mb-1 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-600">
                                <span class="material-symbols-outlined text-[16px]">workspace_premium</span>
                            </div>
                            <div class="text-xs sm:text-sm font-black text-slate-800 truncate">
                                {{ $topCategory->name ?? 'Đa dạng' }}
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Chuyên mục chính</div>
                        </div>
                    </div>
                </div>

                <!-- Actions Column -->
                <div class="author-action-col">
                    <button type="button" 
                            id="follow-author-btn" 
                            onclick="toggleFollowAuthor('{{ $author->id }}')" 
                            class="w-full px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/95 text-white font-bold text-xs shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-98">
                        <span class="material-symbols-outlined text-[17px]" id="follow-icon">bookmark_add</span>
                        <span id="follow-text">Theo dõi tác giả</span>
                    </button>

                    <button type="button" 
                            onclick="copyAuthorLink()" 
                            id="share-author-btn"
                            class="w-full px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-98">
                        <span class="material-symbols-outlined text-[17px] text-primary">share</span>
                        <span id="share-btn-text">Chia sẻ hồ sơ</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Main Content: Article Catalog & Curated Sidebar -->
    <div class="max-w-[1200px] mx-auto px-container-margin">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left: Articles Stream (3/4 column) -->
            <div class="lg:w-3/4">
                
                <!-- Filter Bar -->
                <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200/80 shadow-2xs mb-6">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                        
                        <!-- Category Chips -->
                        <div class="flex items-center gap-2 overflow-x-auto whitespace-nowrap scrollbar-hide pb-1 sm:pb-0">
                            <a href="{{ route('blog.author', array_filter(['id' => $author->id, 'sort' => request('sort'), 'search' => request('search')])) }}" 
                               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 {{ !request()->has('category') ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                <span class="material-symbols-outlined text-[15px]">grid_view</span>
                                <span>Tất cả ({{ $totalBlogs }})</span>
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ route('blog.author', array_filter(['id' => $author->id, 'category' => $cat->slug, 'sort' => request('sort'), 'search' => request('search')])) }}" 
                                   class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-1 {{ request('category') === $cat->slug ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    <span>{{ $cat->name }}</span>
                                    <span class="text-[10px] opacity-75 font-mono">({{ $cat->blogs_count }})</span>
                                </a>
                            @endforeach
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="flex items-center gap-2 shrink-0 justify-end">
                            <label for="author-sort-select" class="text-xs font-semibold text-slate-400 shrink-0 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">swap_vert</span>
                                <span class="hidden sm:inline">Sắp xếp:</span>
                            </label>
                            <select id="author-sort-select" 
                                    onchange="changeAuthorSort(this.value)" 
                                    class="bg-slate-100 text-slate-700 text-xs font-bold py-1.5 px-3 rounded-xl border-none outline-none focus:ring-2 focus:ring-primary cursor-pointer">
                                <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="views" {{ request('sort') === 'views' ? 'selected' : '' }}>Xem nhiều nhất</option>
                                <option value="comments" {{ request('sort') === 'comments' ? 'selected' : '' }}>Bình luận nhiều</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                            </select>
                        </div>
                    </div>

                    <!-- In-page Search Form -->
                    <div class="mt-3.5 pt-3 border-t border-slate-100">
                        <form action="{{ route('blog.author', $author->id) }}" method="GET" class="relative">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if(request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Tìm kiếm bài viết của tác giả {{ $author->ho_ten }}..." 
                                   class="w-full pl-10 pr-24 py-2 bg-slate-50 hover:bg-slate-100/70 focus:bg-white rounded-xl text-xs text-slate-800 placeholder:text-slate-400 outline-none border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                            
                            <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-3 py-1 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary/90 transition-all cursor-pointer">
                                Tìm kiếm
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Active Filter Alert Bar -->
                @if(request()->filled('search'))
                    <div class="mb-6 p-4 bg-amber-50/90 border border-amber-200/90 rounded-2xl flex items-center justify-between text-xs shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-amber-700">
                                <span class="material-symbols-outlined text-[18px]">manage_search</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Kết quả lọc bài viết cho:</span>
                                <strong class="text-slate-900 ml-1">"{{ request('search') }}"</strong>
                                <span class="bg-amber-100 text-amber-800 font-extrabold px-2 py-0.5 rounded-full ml-2">
                                    {{ $blogs->total() }} bài viết
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('blog.author', array_filter(['id' => $author->id, 'category' => request('category'), 'sort' => request('sort')])) }}" 
                           class="text-xs font-bold text-slate-500 hover:text-rose-600 flex items-center gap-1 px-2.5 py-1 rounded-lg hover:bg-white transition-all">
                            <span class="material-symbols-outlined text-sm">close</span>
                            <span>Xóa tìm kiếm</span>
                        </a>
                    </div>
                @endif

                <!-- Articles Grid (Curated Editorial Cards) -->
                @if($blogs->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($blogs as $blog)
                            <article class="article-zoom-card flex flex-col h-full bg-white rounded-2xl border border-slate-200/90 hover:border-primary/40 shadow-xs hover:shadow-xl transition-all duration-300 overflow-hidden group">
                                
                                <!-- Cover Image with Glass Tag Overlay -->
                                <a href="{{ route('blog.show', $blog->slug) }}" class="article-cover-frame">
                                    @if($blog->category)
                                        <div class="absolute top-3 left-3 z-10 px-2.5 py-1 rounded-lg bg-white/90 backdrop-blur-md text-slate-900 font-extrabold text-[10px] uppercase tracking-wider border border-white/50 shadow-sm">
                                            {{ $blog->category->name }}
                                        </div>
                                    @endif
                                    
                                    <div class="absolute top-3 right-3 z-10 px-2 py-0.5 rounded-md bg-black/50 backdrop-blur-md text-white font-semibold text-[10px] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">schedule</span>
                                        <span>{{ $blog->reading_time ?? '3' }} phút</span>
                                    </div>

                                    <img src="{{ $blog->cover_image_url ?? 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80' }}" 
                                         alt="{{ $blog->title }}"/>
                                </a>

                                <!-- Card Content -->
                                <div class="p-5 flex flex-col flex-grow">
                                    <a href="{{ route('blog.show', $blog->slug) }}" class="flex-grow block group/link">
                                        <h3 class="text-base font-bold text-slate-900 mb-2 line-clamp-2 group-hover/link:text-primary transition-colors leading-snug">
                                            {{ $blog->title }}
                                        </h3>
                                        <p class="text-slate-500 text-xs mb-4 line-clamp-3 leading-relaxed">
                                            {{ $blog->excerpt ?? Str::limit(strip_tags($blog->content), 110) }}
                                        </p>
                                    </a>

                                    <!-- Card Footer with Stats -->
                                    <div class="mt-auto pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                                        <span class="flex items-center gap-1 text-[11px] font-medium text-slate-500">
                                            <span class="material-symbols-outlined text-[14px] text-slate-400">calendar_today</span>
                                            <span>{{ $blog->published_at ? $blog->published_at->format('d/m/Y') : 'Mới' }}</span>
                                        </span>

                                        <div class="flex items-center gap-3">
                                            <span class="flex items-center gap-1 text-[11px]" title="Lượt xem">
                                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                                <span>{{ number_format($blog->view_count ?? 0) }}</span>
                                            </span>
                                            <span class="flex items-center gap-1 text-[11px]" title="Bình luận">
                                                <span class="material-symbols-outlined text-[14px]">forum</span>
                                                <span>{{ number_format($blog->comment_count ?? 0) }}</span>
                                            </span>
                                            <a href="{{ route('blog.show', $blog->slug) }}" class="w-6 h-6 rounded-full bg-slate-100 group-hover:bg-primary group-hover:text-white flex items-center justify-center transition-colors text-slate-400 ml-1">
                                                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Custom Styled Pagination -->
                    <div class="mt-12 flex justify-center">
                        {{ $blogs->links() }}
                    </div>
                @else
                    <!-- Elegant Empty State -->
                    <div class="text-center py-20 bg-white rounded-3xl border border-slate-200/80 shadow-xs px-6">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-3xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600 shadow-inner">
                            <span class="material-symbols-outlined text-4xl">menu_book</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Chưa tìm thấy bài viết phù hợp</h3>
                        <p class="text-slate-500 max-w-md mx-auto mb-6 text-xs sm:text-sm leading-relaxed">
                            Tác giả {{ $author->ho_ten }} chưa có bài viết nào khớp với tiêu chí tìm kiếm hoặc chuyên mục này. Hãy thử xóa bộ lọc để xem toàn bộ danh mục bài viết.
                        </p>
                        <a href="{{ route('blog.author', $author->id) }}" class="px-6 py-3 rounded-full bg-primary hover:bg-primary/90 text-white font-bold text-xs shadow-md transition-all inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">refresh</span>
                            <span>Xem tất cả bài viết của {{ $author->ho_ten }}</span>
                        </a>
                    </div>
                @endif

            </div>

            <!-- Right: Curated Sidebar (1/4 column) -->
            <aside class="lg:w-1/4 space-y-6">
                
                <!-- Creator Bio & Credentials Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[20px]">badge</span>
                        </div>
                        <h4 class="font-bold text-sm text-slate-900">Hồ sơ tác giả</h4>
                    </div>

                    <div class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-50">
                            <span class="text-slate-400 font-medium">Họ và tên:</span>
                            <span class="font-bold text-slate-800">{{ $author->ho_ten }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-50">
                            <span class="text-slate-400 font-medium">Chức danh:</span>
                            <span class="font-extrabold text-primary">{{ $author->ten_vai_tro_hien_thi }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-50">
                            <span class="text-slate-400 font-medium">Khu vực hoạt động:</span>
                            <span class="font-bold text-slate-800">Sài Gòn / TP.HCM</span>
                        </div>
                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-50">
                            <span class="text-slate-400 font-medium">Đánh giá độc giả:</span>
                            <span class="font-bold text-amber-600 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span>4.9 / 5.0</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Tình trạng:</span>
                            <span class="font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Tác giả thường trực</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown Card with Visual Progress Bars -->
                @if($categories->count() > 0)
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                    <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600">
                            <span class="material-symbols-outlined text-[20px]">donut_small</span>
                        </div>
                        <h4 class="font-bold text-sm text-slate-900">Chuyên mục nổi bật</h4>
                    </div>

                    <div class="space-y-4">
                        @foreach($categories as $c)
                            @php
                                $percent = $totalBlogs > 0 ? round(($c->blogs_count / $totalBlogs) * 100) : 0;
                            @endphp
                            <a href="{{ route('blog.author', ['id' => $author->id, 'category' => $c->slug]) }}" class="block group">
                                <div class="flex items-center justify-between text-xs mb-1.5 font-bold">
                                    <span class="text-slate-700 group-hover:text-primary transition-colors flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-primary/60 group-hover:bg-primary"></span>
                                        <span>{{ $c->name }}</span>
                                    </span>
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $c->blogs_count }} bài ({{ $percent }}%)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-primary h-1.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Newsletter Card Tailored to Author -->
                <div class="relative bg-gradient-to-br from-amber-500 via-primary to-orange-600 text-white p-6 sm:p-7 rounded-3xl shadow-lg overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center mb-3.5 border border-white/20">
                            <span class="material-symbols-outlined text-white text-[22px]">mark_email_read</span>
                        </div>
                        <h4 class="text-base font-extrabold mb-1">Đăng ký nhận bài viết mới</h4>
                        <p class="text-white/85 text-xs mb-4 leading-relaxed">
                            Cập nhật bài viết mới nhất và những phát hiện ẩm thực độc quyền từ tác giả {{ $author->ho_ten }} hàng tuần.
                        </p>

                        <form id="author-newsletter-form" action="{{ route('blog.newsletter.subscribe') }}" method="POST" onsubmit="handleAuthorNewsletterSubmit(event, this)" class="space-y-2.5">
                            @csrf
                            <input type="hidden" name="source" value="author_sidebar_{{ $author->id }}">
                            <div class="relative">
                                <input class="w-full px-4 py-2.5 bg-white text-slate-800 rounded-xl text-xs focus:ring-2 focus:ring-white outline-none placeholder:text-slate-400 font-medium" 
                                       name="email" 
                                       value="{{ auth()->check() ? auth()->user()->email : old('email') }}" 
                                       placeholder="Nhập email của bạn..." 
                                       type="email" 
                                       required/>
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-98">
                                <span class="btn-text">Đăng ký ngay</span>
                                <span class="material-symbols-outlined text-sm">send</span>
                            </button>
                            <div class="newsletter-feedback hidden text-xs font-semibold p-2.5 rounded-xl"></div>
                        </form>
                    </div>
                </div>

            </aside>

        </div>
    </div>

    <!-- Floating Toast Notification -->
    <div id="author-toast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 pointer-events-none transition-all duration-300 flex items-center gap-2.5 px-4 py-3 bg-slate-900 text-white text-xs font-bold rounded-2xl shadow-2xl border border-slate-700">
        <span class="material-symbols-outlined text-emerald-400 text-lg" id="toast-icon">check_circle</span>
        <span id="toast-msg">Đã sao chép link hồ sơ tác giả!</span>
    </div>

</main>
@endsection

@push('scripts')
<script>
    function showToast(msg, icon = 'check_circle') {
        const toast = document.getElementById('author-toast');
        const toastMsg = document.getElementById('toast-msg');
        const toastIcon = document.getElementById('toast-icon');
        if (!toast) return;

        toastMsg.textContent = msg;
        toastIcon.textContent = icon;
        toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');

        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
            toast.classList.remove('translate-y-0', 'opacity-100');
        }, 3000);
    }

    function copyAuthorLink() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            showToast('Đã sao chép đường dẫn hồ sơ tác giả!', 'link');
        }).catch(() => {
            prompt('Sao chép đường dẫn:', url);
        });
    }

    function toggleFollowAuthor(authorId) {
        const key = 'followed_author_' + authorId;
        const btn = document.getElementById('follow-author-btn');
        const btnText = document.getElementById('follow-text');
        const btnIcon = document.getElementById('follow-icon');
        const isFollowed = localStorage.getItem(key) === 'true';

        if (isFollowed) {
            localStorage.removeItem(key);
            btn.className = 'w-full px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/95 text-white font-bold text-xs shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-98';
            btnText.textContent = 'Theo dõi tác giả';
            btnIcon.textContent = 'bookmark_add';
            showToast('Đã bỏ theo dõi tác giả', 'notifications_off');
        } else {
            localStorage.setItem(key, 'true');
            btn.className = 'w-full px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-98';
            btnText.textContent = 'Đang theo dõi ✓';
            btnIcon.textContent = 'done_all';
            showToast('Đã theo dõi tác giả! Bạn sẽ nhận thông báo khi có bài viết mới.', 'favorite');
        }
    }

    // Init follow state
    document.addEventListener('DOMContentLoaded', () => {
        const authorId = '{{ $author->id }}';
        const key = 'followed_author_' + authorId;
        if (localStorage.getItem(key) === 'true') {
            const btn = document.getElementById('follow-author-btn');
            const btnText = document.getElementById('follow-text');
            const btnIcon = document.getElementById('follow-icon');
            if (btn) {
                btn.className = 'w-full px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-98';
                if (btnText) btnText.textContent = 'Đang theo dõi ✓';
                if (btnIcon) btnIcon.textContent = 'done_all';
            }
        }
    });

    function changeAuthorSort(sortVal) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortVal);
        window.location.href = url.toString();
    }

    function handleAuthorNewsletterSubmit(event, form) {
        event.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const btnText = btn ? btn.querySelector('.btn-text') : null;
        const originalText = btnText ? btnText.textContent : 'Đăng ký ngay';
        const feedback = form.querySelector('.newsletter-feedback');
        const emailInput = form.querySelector('input[name="email"]');
        const email = emailInput ? emailInput.value.trim() : '';

        if (!email) return;

        if (btn) {
            btn.disabled = true;
            if (btnText) btnText.textContent = 'Đang gửi...';
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
            if (!res.ok) throw new Error(data.message || 'Có lỗi xảy ra');
            return data;
        })
        .then(data => {
            if (feedback) {
                if (data.already_subscribed) {
                    feedback.className = 'newsletter-feedback block text-xs font-bold p-3 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 mt-2 text-left shadow-sm';
                    feedback.innerHTML = `<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-amber-600">info</span><span>${data.message}</span></span>`;
                } else {
                    feedback.className = 'newsletter-feedback block text-xs font-bold p-3 rounded-xl bg-emerald-100 text-emerald-900 border border-emerald-300 mt-2 text-left shadow-sm';
                    feedback.innerHTML = `<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-emerald-600">check_circle</span><span>${data.message}</span></span>`;
                    @guest
                    if (emailInput) emailInput.value = '';
                    @endguest
                    showToast('Đăng ký nhận tin ẩm thực thành công!', 'mark_email_read');
                }
            }
        })
        .catch(err => {
            if (feedback) {
                feedback.className = 'newsletter-feedback block text-xs font-bold p-3 rounded-xl bg-rose-100 text-rose-900 border border-rose-300 mt-2 text-left shadow-sm';
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
</script>
@endpush
