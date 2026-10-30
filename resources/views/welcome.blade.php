@extends('layouts.app')

@php $seo = \App\Models\SeoSetting::forPage('home'); @endphp

@section('title', $seo->meta_title ?: 'Quán Mới - Khám phá tinh hoa ẩm thực địa phương')

@push('seo')
    @if($seo->meta_description)
    <meta name="description" content="{{ $seo->meta_description }}">
    @endif
    @if($seo->meta_keywords)
    <meta name="keywords" content="{{ $seo->meta_keywords }}">
    @endif
    <meta property="og:title" content="{{ $seo->og_title ?: $seo->meta_title ?: 'Quán Mới' }}">
    <meta property="og:description" content="{{ $seo->og_description ?: $seo->meta_description ?: '' }}">
    @if($seo->og_image)
    <meta property="og:image" content="{{ $seo->og_image }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
@endpush

@push('seo')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebSite",
  "name": "Quán Mới",
  "url": "{{ url('/') }}",
  "potentialAction": {
    "@@type": "SearchAction",
    "target": {
      "@@type": "EntryPoint",
      "urlTemplate": "{{ url('/kham-pha?tu_khoa={search_term_string}') }}"
    },
    "query-input": "required name=search_term_string"
  }
}
</script>
@endpush

@section('content')
    {{-- Unified Responsive Main Content (Đầy đủ 100% chức năng trên cả Desktop & Mobile) --}}
    <main class="flex-grow">
        {{-- Hero Section --}}
        <section class="relative w-full min-h-[420px] md:h-[480px] py-10 md:py-0 flex items-center justify-center z-30">
            {{-- Slider Track Container --}}
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div id="hero-slider-track" class="absolute inset-0 flex w-[300%] transition-transform duration-1000 ease-in-out" style="transform: translateX(0%);">
                    {{-- Slide 1: Bàn tiệc ẩm thực hấp dẫn --}}
                    <div class="w-1/3 h-full bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1920&q=80')">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/30"></div>
                    </div>
                    {{-- Slide 2: Không gian nhà hàng sang trọng --}}
                    <div class="w-1/3 h-full bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1920&q=80')">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/30"></div>
                    </div>
                    {{-- Slide 3: Ẩm thực nướng/lẩu sầm uất --}}
                    <div class="w-1/3 h-full bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=1920&q=80')">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/30"></div>
                    </div>
                </div>
            </div>
            <div class="relative z-20 text-center px-4 w-full max-w-4xl space-y-4 sm:space-y-6">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-3.5 sm:px-4 py-1.5 rounded-full text-white text-xs sm:text-sm font-semibold border border-white/30 mb-1">
                    <span class="material-symbols-outlined text-[18px] text-tick-xanh" style="font-variation-settings: 'FILL' 1;">verified</span>
                    Cộng đồng ẩm thực & giải trí hàng đầu
                </div>
                <h1 class="text-white font-display-lg text-3xl sm:text-4xl md:text-[52px] leading-tight md:leading-[62px] font-black drop-shadow-lg tracking-tight">
                    Khám phá <span class="text-primary-fixed">tinh hoa ẩm thực</span> địa phương
                </h1>
                <p class="text-white/90 text-sm sm:text-base md:text-lg max-w-2xl mx-auto font-normal drop-shadow">
                    Tìm kiếm hàng ngàn quán ăn, quán cà phê, tiệm trà sữa và địa điểm giải trí được yêu thích nhất gần bạn.
                </p>

                {{-- Hero Search Bar (Tìm món ăn, Gợi ý món ngon, Chọn khu vực) --}}
                <div class="mt-4">
                    <x-search-bar prefix="hero" />
                </div>
            </div>
        </section>

        {{-- Categories Section --}}
        @php
            $displayCategories = $danhMucList ?? collect();
            if ($displayCategories->isEmpty()) {
                $displayCategories = \App\Models\DanhMucQuan::hienThi()->noiBat()->orderBy('thu_tu')->orderBy('id')->get();
            }
            $totalCats = $displayCategories->count();
        @endphp
        <section class="max-w-[1240px] mx-auto py-10 md:py-16 px-container-margin relative z-10" id="home-categories-section">
            <div class="flex justify-between items-end gap-3 mb-6 md:mb-8">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-on-surface">Danh mục khám phá</h2>
                    <p class="text-text-muted text-xs sm:text-[15px] mt-1">Tìm địa điểm theo nhu cầu ẩm thực của bạn</p>
                </div>
                @if($totalCats > 6)
                    <button type="button" onclick="toggleMoreCategories()" class="inline-flex items-center gap-1 text-primary font-bold text-xs sm:text-[14px] hover:underline cursor-pointer group shrink-0">
                        <span id="headerToggleText">Xem thêm ({{ $totalCats - 6 }})</span>
                        <span id="headerToggleIcon" class="material-symbols-outlined text-[18px] group-hover:translate-y-0.5 transition-transform">expand_more</span>
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 sm:gap-5" id="categoriesGrid">
                @forelse($displayCategories as $index => $dm)
                    @php
                        $isExtra = $index >= 6;
                        
                        $scheme = match($dm->slug) {
                            'nha-hang'           => ['box' => 'bg-primary/10 text-primary group-hover:bg-primary', 'default_icon' => 'restaurant'],
                            'ca-phe-tra'         => ['box' => 'bg-secondary/10 text-secondary group-hover:bg-secondary', 'default_icon' => 'local_cafe'],
                            'billiards-giai-tri' => ['box' => 'bg-tertiary/10 text-tertiary group-hover:bg-tertiary', 'default_icon' => 'sports_esports'],
                            'do-an-vat'          => ['box' => 'bg-red-500/10 text-red-600 group-hover:bg-red-600', 'default_icon' => 'fastfood'],
                            'lau-nuong'          => ['box' => 'bg-amber-500/10 text-amber-600 group-hover:bg-amber-600', 'default_icon' => 'outdoor_grill'],
                            'quan-dem-24-7'      => ['box' => 'bg-purple-500/10 text-purple-600 group-hover:bg-purple-600', 'default_icon' => 'nightlife'],
                            'quan-nhau'          => ['box' => 'bg-purple-500/10 text-purple-600 group-hover:bg-purple-600', 'default_icon' => 'local_bar'],
                            'tra-sua-nuoc-ep'    => ['box' => 'bg-secondary/10 text-secondary group-hover:bg-secondary', 'default_icon' => 'emoji_food_beverage'],
                            'tiem-banh'          => ['box' => 'bg-amber-500/10 text-amber-600 group-hover:bg-amber-600', 'default_icon' => 'bakery_dining'],
                            'quan-an-binh-dan'   => ['box' => 'bg-primary/10 text-primary group-hover:bg-primary', 'default_icon' => 'lunch_dining'],
                            default => match($index % 6) {
                                0 => ['box' => 'bg-primary/10 text-primary group-hover:bg-primary', 'default_icon' => 'restaurant'],
                                1 => ['box' => 'bg-secondary/10 text-secondary group-hover:bg-secondary', 'default_icon' => 'local_cafe'],
                                2 => ['box' => 'bg-tertiary/10 text-tertiary group-hover:bg-tertiary', 'default_icon' => 'sports_esports'],
                                3 => ['box' => 'bg-red-500/10 text-red-600 group-hover:bg-red-600', 'default_icon' => 'fastfood'],
                                4 => ['box' => 'bg-amber-500/10 text-amber-600 group-hover:bg-amber-600', 'default_icon' => 'outdoor_grill'],
                                default => ['box' => 'bg-purple-500/10 text-purple-600 group-hover:bg-purple-600', 'default_icon' => 'nightlife'],
                            }
                        };
                        $boxClass = $scheme['box'];
                        $displayIcon = $dm->icon ?: $scheme['default_icon'];
                    @endphp
                    <a href="{{ route('danh-muc', $dm->slug) }}" title="{{ $dm->ten_danh_muc }} - {{ $dm->mo_ta }}" class="{{ $isExtra ? 'extra-category hidden opacity-0 translate-y-3' : '' }} block bg-surface-card rounded-2xl shadow-sm border border-surface-container hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer p-6 flex flex-col items-center text-center group">
                        <div class="w-16 h-16 rounded-2xl {{ $boxClass }} flex items-center justify-center mb-3 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">{{ $displayIcon }}</span>
                        </div>
                        <h3 class="font-bold text-[16px] text-on-surface">{{ $dm->ten_danh_muc }}</h3>
                        <p class="text-text-muted text-[13px] mt-1">{{ $dm->mo_ta }}</p>
                    </a>
                @empty
                    <div class="col-span-full py-8 text-center text-text-muted">Đang cập nhật danh mục ẩm thực...</div>
                @endforelse
            </div>

            @if($totalCats > 6)
                <div class="mt-8 text-center">
                    <button type="button" id="btnToggleCategories" onclick="toggleMoreCategories()" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-surface-card border border-surface-container hover:border-primary/40 hover:bg-primary/5 text-on-surface font-bold text-xs md:text-sm shadow-sm hover:shadow-md transition-all cursor-pointer group">
                        <span id="btnToggleCategoriesText">Xem thêm ({{ $totalCats - 6 }} danh mục khác)</span>
                        <span id="btnToggleCategoriesIcon" class="material-symbols-outlined text-[20px] text-primary group-hover:translate-y-0.5 transition-transform">expand_more</span>
                    </button>
                </div>
            @endif
        </section>

        {{-- SECTION 1: Trending Venues / Quán Ăn Nổi Bật --}}
        <section class="bg-surface-container-low py-16" id="home-featured-section">
            <div class="max-w-[1240px] mx-auto px-container-margin">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-primary text-xs font-bold uppercase tracking-wider mb-1">
                            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                            Hot nhất tuần
                        </div>
                        <h2 class="text-2xl font-black text-on-surface">Quán ăn nổi bật được đánh giá cao</h2>
                        <p class="text-text-muted text-[15px] mt-1">Những địa điểm nhận được nhiều phản hồi tích cực nhất từ cộng đồng</p>
                    </div>
                    <a class="text-primary font-bold text-[14px] flex items-center hover:underline gap-1" href="{{ route('quan-noi-bat') }}">
                        Xem tất cả <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>

                {{-- Grid Quán Nổi Bật --}}
                <div id="venues-grid-featured" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6 xl:gap-8">
                    @forelse($quanNoiBat as $quan)
                        <x-venue-card :quan="$quan" :saved-quan-ids="$savedQuanIds ?? []" />
                    @empty
                        <p class="col-span-full text-center text-gray-500 py-10">Chưa có quán nào nổi bật.</p>
                    @endforelse
                </div>

                @if($totalQuanNoiBat > 9)
                <div class="mt-10 flex justify-center">
                    <a href="{{ route('quan-noi-bat') }}" class="inline-flex items-center justify-center bg-white border-2 border-primary text-primary px-8 py-3.5 rounded-full font-bold text-[15px] hover:bg-primary hover:text-white transition-all shadow-sm group">
                        Xem thêm {{ $totalQuanNoiBat - 9 }} địa điểm khác
                        <span class="material-symbols-outlined ml-2 group-hover:translate-x-1 transition-transform text-[20px]">arrow_forward</span>
                    </a>
                </div>
                @endif
            </div>
        </section>

        {{-- SECTION 1.5: Quán Ăn Gần Bạn (Tách riêng nằm ở dưới Quán Nổi Bật) --}}
        <section class="bg-white py-16 border-t border-gray-100" id="home-nearby-section">
            <div class="max-w-[1240px] mx-auto px-container-margin">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-emerald-600 text-xs font-bold uppercase tracking-wider mb-1">
                            <span class="material-symbols-outlined text-[16px]">near_me</span>
                            <span>Gần bạn nhất (bán kính 10km)</span>
                        </div>
                        <h2 class="text-2xl font-black text-on-surface flex items-center gap-2">
                            <span>Quán ăn gần vị trí của bạn</span>
                        </h2>
                        <p class="text-text-muted text-[15px] mt-1" id="nearby-subtitle">
                            {{ ($hasGps && $quanGanBan->isNotEmpty()) ? 'Tự động sắp xếp theo khoảng cách gần nhất từ vị trí của bạn' : 'Khám phá các quán ăn ngon lân cận vị trí hiện tại của bạn' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" onclick="requestUserLocation(true)" class="text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3.5 py-2 rounded-xl flex items-center gap-1.5 transition-colors cursor-pointer shadow-xs">
                            <span class="material-symbols-outlined text-[16px]">my_location</span>
                            <span>Định vị lại</span>
                        </button>
                        <a href="{{ route('ban-do') }}" class="text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 px-3.5 py-2 rounded-xl flex items-center gap-1.5 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">map</span>
                            <span>Xem trên bản đồ</span>
                        </a>
                    </div>
                </div>

                {{-- Banner kêu gọi cấp quyền vị trí (chỉ hiển thị khi chưa có GPS) --}}
                <div id="nearby-permission-banner" class="{{ ($hasGps && $quanGanBan->isNotEmpty()) ? 'hidden' : 'block' }} bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-6 sm:p-8 text-center sm:text-left mb-6 shadow-xs">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                <span class="material-symbols-outlined text-[32px]">near_me</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-base sm:text-lg">Bật định vị để xem các quán ăn ngon quanh bạn</h3>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1 max-w-xl">Hệ thống sẽ tự động đo đạc khoảng cách và đề xuất những quán ăn được yêu thích nhất gần vị trí của bạn.</p>
                            </div>
                        </div>
                        <button type="button" onclick="requestUserLocation(true)" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm px-5 py-3 rounded-xl shadow-sm transition-all hover:scale-105 active:scale-95 cursor-pointer whitespace-nowrap">
                            <span class="material-symbols-outlined text-[18px]">near_me</span>
                            <span>Cho phép truy cập vị trí</span>
                        </button>
                    </div>
                </div>

                {{-- Grid Quán Gần Bạn --}}
                <div id="venues-grid-nearby" class="{{ ($hasGps && $quanGanBan->isNotEmpty()) ? 'grid' : 'hidden' }} grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6 xl:gap-8">
                    @if($hasGps && $quanGanBan->isNotEmpty())
                        @foreach($quanGanBan as $quan)
                            <x-venue-card :quan="$quan" :saved-quan-ids="$savedQuanIds ?? []" />
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

                
        {{-- SECTION: Mới từ cộng đồng (Giao diện cũ) --}}
        <section class="bg-surface-container-low py-10 border-t border-gray-100/60" id="home-community-section">
            <div class="max-w-[1240px] mx-auto px-container-margin">
                <div class="rounded-[24px] sm:rounded-[28px] bg-white border border-gray-100 shadow-sm p-4 sm:p-6 md:p-7">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4 mb-5 sm:mb-6">
                        <div>
                            <div class="inline-flex items-center gap-2 text-secondary text-xs font-bold uppercase tracking-wider mb-1">
                                <span class="material-symbols-outlined text-[16px]">new_releases</span>
                                Mới từ cộng đồng
                            </div>
                            <h3 class="text-xl md:text-2xl font-black text-on-surface">Quán mới vừa được thêm gần đây</h3>
                            <p class="text-text-muted text-xs sm:text-[15px] mt-1">Những địa điểm mới lên sóng để bạn khám phá sớm trước khi thành trend.</p>
                        </div>
                        <a class="text-primary font-bold text-xs sm:text-[14px] flex items-center gap-1 hover:underline" href="{{ route('chu-quan.dang-quan') }}">
                            Thêm quán của bạn <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                        @forelse($quanMoi as $quan)
                        <a href="{{ route('quan.detail', $quan->slug) }}" class="group rounded-2xl border border-gray-100 bg-white hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full">
                            <div class="relative w-full h-32 sm:h-44 flex-shrink-0 overflow-hidden">
                                <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                     src="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=600&q=80' }}" 
                                     alt="{{ $quan->ten_quan }}"
                                     loading="lazy" />
                                <div class="absolute top-2 left-2 bg-secondary text-white px-2 py-0.5 sm:py-1 rounded-lg text-[9.5px] sm:text-[10px] font-bold shadow-sm">Mới Mở</div>
                                <button type="button" 
                                        data-quan-id="{{ $quan->id }}" 
                                        data-saved="{{ in_array($quan->id, $savedQuanIds ?? []) ? '1' : '0' }}" 
                                        onclick="event.preventDefault(); toggleSave(this, '{{ $quan->id }}')" 
                                        class="absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 bg-white/90 backdrop-blur-md rounded-full flex items-center justify-center text-red-500 shadow-sm hover:scale-110 active:scale-90 transition-transform z-10" 
                                        title="{{ in_array($quan->id, $savedQuanIds ?? []) ? 'Bỏ lưu quán' : 'Lưu quán' }}">
                                    <span class="material-symbols-outlined text-[15px] sm:text-[18px]" style="font-variation-settings: 'FILL' {{ in_array($quan->id, $savedQuanIds ?? []) ? '1' : '0' }};">favorite</span>
                                </button>
                            </div>
                            <div class="p-2.5 sm:p-4 flex flex-col flex-1">
                                <div class="flex items-center gap-1 mb-1 sm:mb-1.5">
                                    <span class="material-symbols-outlined text-[12px] sm:text-[14px] text-primary">schedule</span>
                                    <span class="text-[10px] sm:text-[11px] text-primary font-bold truncate">{{ $quan->created_at ? $quan->created_at->diffForHumans() : 'Mới đây' }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-1 sm:gap-2">
                                    <h4 class="font-bold text-[13px] sm:text-[15px] text-on-surface group-hover:text-primary transition-colors truncate">{{ $quan->ten_quan }}</h4>
                                    @if($quan->is_xac_thuc)
                                        <span class="material-symbols-outlined text-tick-xanh text-[14px] sm:text-[16px] flex-shrink-0" style="font-variation-settings: 'FILL' 1;" title="Đã xác thực">verified</span>
                                    @endif
                                </div>
                                <p class="text-[11px] sm:text-[12px] text-text-muted mt-1 truncate flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px] sm:text-[14px]">location_on</span>
                                    <span class="truncate">{{ $quan->ten_quan_huyen ?? '' }}{{ $quan->ten_tinh_thanh ? ', ' . $quan->ten_tinh_thanh : '' }}</span>
                                </p>
                                <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-3 flex flex-wrap items-center justify-between border-t border-gray-100 gap-1 sm:gap-2 min-w-0">
                                    <div class="flex gap-1 sm:gap-1.5 items-center min-w-0 flex-shrink">
                                        <span class="hidden sm:inline-block bg-gray-50 text-gray-600 px-2 py-0.5 rounded-md text-[11px] font-medium whitespace-nowrap truncate max-w-[95px]" title="{{ $quan->danhMuc->ten_danh_muc ?? str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}">{{ $quan->danhMuc->ten_danh_muc ?? str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}</span>
                                        <span class="flex items-center gap-0.5 text-gray-500 text-[10.5px] sm:text-[11px] whitespace-nowrap flex-shrink-0"><span class="material-symbols-outlined text-[12px] sm:text-[13px]">visibility</span>{{ number_format($quan->luot_xem ?? 0) }}</span>
                                    </div>
                                    <span class="font-bold text-primary text-[11.5px] sm:text-[13px] whitespace-nowrap flex-shrink-0 ml-auto">{{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        </a>
                        @empty
                            <p class="sm:col-span-2 lg:col-span-4 text-center text-gray-500 py-10">Chưa có quán mới nào.</p>
                        @endforelse
                    </div>

                    @if($totalQuanMoi > 12)
                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-center">
                        <a href="{{ route('quan-moi') }}" class="inline-flex items-center justify-center bg-gray-50 border-2 border-gray-200 text-gray-700 px-8 py-3.5 rounded-full font-bold text-[15px] hover:bg-primary hover:border-primary hover:text-white transition-all shadow-sm group">
                            Khám phá thêm {{ $totalQuanMoi - 12 }} quán mới
                            <span class="material-symbols-outlined ml-2 group-hover:translate-x-1 transition-transform text-[20px]">arrow_forward</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </section>
{{-- SECTION 2: Video Reviews Carousel (Mới thêm) --}}
        <section class="max-w-[1240px] mx-auto py-16 px-container-margin">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <div class="inline-flex items-center gap-1.5 text-red-600 text-xs font-bold uppercase tracking-wider mb-1">
                        <span class="material-symbols-outlined text-[16px]">play_circle</span>
                        Video Review Thật
                    </div>
                    <h2 class="text-2xl font-black text-on-surface">Trải nghiệm thực tế qua Video Short</h2>
                    <p class="text-text-muted text-[15px] mt-1">Xem video đánh giá ngắn từ các Reviewer uy tín</p>
                </div>
                <a class="text-primary font-bold text-[14px] flex items-center hover:underline gap-1" href="{{ route('video-review.index') }}">
                    Xem tất cả video <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @forelse($videoShorts as $video)
                <a href="{{ route('video-review.index') }}" class="relative h-96 rounded-2xl overflow-hidden group cursor-pointer shadow-md hover:shadow-xl transition-all block bg-gray-900">
                    <img src="{{ $video->effective_thumbnail }}"
                         alt="Video review {{ $video->nguoi_dang }}"
                         loading="lazy"
                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=500&q=80';"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/20"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-14 h-14 rounded-full bg-white/30 backdrop-blur-md flex items-center justify-center text-white group-hover:scale-110 group-hover:bg-primary transition-all">
                            <span class="material-symbols-outlined text-3xl ml-1" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                        </div>
                    </div>
                    <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-full text-white text-[12px] font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">visibility</span> {{ number_format($video->luot_xem) }}
                    </div>
                    <div class="absolute bottom-4 inset-x-4 text-white space-y-1">
                        @if($video->quan)
                            <span class="bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded">Review Quán Ăn</span>
                        @else
                            <span class="bg-secondary text-white text-[10px] font-bold px-2 py-0.5 rounded">Short Video</span>
                        @endif
                        <h4 class="font-bold text-[15px] leading-tight line-clamp-2">{{ $video->tieu_de }}</h4>
                        <p class="text-white/80 text-[12px]">bởi <span class="font-bold text-white">{{ $video->nguoi_dang }}</span></p>
                    </div>
                </a>
                @empty
                    <div class="col-span-4 text-center text-gray-500 py-10">Đang cập nhật video review.</div>
                @endforelse
            </div>
        </section>

        {{-- SECTION 3: Community Collections / BST Bộ Thẩm Ẩm Thực --}}
        <section class="bg-gray-900 text-white py-16">
            <div class="max-w-[1240px] mx-auto px-container-margin">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <span class="text-primary-fixed text-xs font-bold uppercase tracking-wider block mb-1">Tuyển chọn đặc biệt</span>
                        <h2 class="text-2xl font-black text-white">Bộ sưu tập ẩm thực nổi bật</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="{{ route('kham-pha', ['tu_khoa' => 'Cà phê muối']) }}" class="block relative h-64 rounded-2xl overflow-hidden group cursor-pointer border border-white/10">
                        <img src="https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=600&q=80" alt="BST Cà Phê Muối" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                        <div class="absolute bottom-5 inset-x-5 space-y-1">
                            <span class="bg-primary/80 backdrop-blur-sm text-white text-[11px] font-bold px-2.5 py-1 rounded-md">15 Địa điểm</span>
                            <h3 class="font-bold text-[18px] text-white">BST Cà phê muối béo ngậy khó cưỡng</h3>
                            <p class="text-white/70 text-[13px]">Khám phá các quán cà phê muối ngon nhất thành phố</p>
                        </div>
                    </a>

                    <a href="{{ route('kham-pha', ['danh_muc' => 'Lẩu & Nướng']) }}" class="block relative h-64 rounded-2xl overflow-hidden group cursor-pointer border border-white/10">
                        <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&q=80" alt="BST Quán Nướng" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                        <div class="absolute bottom-5 inset-x-5 space-y-1">
                            <span class="bg-secondary/80 backdrop-blur-sm text-white text-[11px] font-bold px-2.5 py-1 rounded-md">24 Địa điểm</span>
                            <h3 class="font-bold text-[18px] text-white">Top Quán lẩu nướng sân vườn thoáng mát</h3>
                            <p class="text-white/70 text-[13px]">Lý tưởng cho những buổi tụ họp bạn bè cuối tuần</p>
                        </div>
                    </a>

                    <a href="{{ route('kham-pha', ['danh_muc' => 'Quán Đêm 24/7']) }}" class="block relative h-64 rounded-2xl overflow-hidden group cursor-pointer border border-white/10">
                        <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=600&q=80" alt="BST Đồ Ăn Đêm" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                        <div class="absolute bottom-5 inset-x-5 space-y-1">
                            <span class="bg-tertiary/80 backdrop-blur-sm text-white text-[11px] font-bold px-2.5 py-1 rounded-md">18 Địa điểm</span>
                            <h3 class="font-bold text-[18px] text-white">Quán ăn đêm 24/7 cho các "cú đêm"</h3>
                            <p class="text-white/70 text-[13px]">Tổng hợp quán ăn ngon mở muộn sau 00:00</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        {{-- SECTION 4: Community Impact Statistics --}}
        <section class="max-w-[1240px] mx-auto py-20 px-container-margin">
            <div class="bg-gradient-to-br from-primary-fixed via-amber-50 to-orange-100 rounded-3xl p-10 md:p-14 flex flex-col md:flex-row items-center justify-between shadow-sm relative overflow-hidden border border-amber-200">
                <div class="relative z-10 md:w-1/3 mb-8 md:mb-0 space-y-3">
                    <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold">Quán Mới Network</span>
                    <h2 class="font-black text-3xl text-on-primary-fixed leading-tight">Cộng đồng<br/>ẩm thực tin cậy</h2>
                    <p class="text-on-surface-variant text-[15px] leading-relaxed">Cùng nhau đóng góp đánh giá trung thực để xây dựng bản đồ ẩm thực chất lượng nhất.</p>
                </div>
                <div class="relative z-10 md:w-2/3 grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-2xl text-center border border-white/80 shadow-sm">
                        <div class="font-black text-4xl text-primary mb-1">1,200+</div>
                        <div class="font-bold text-[15px] text-on-surface">Quán ăn đã xác thực</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-2xl text-center border border-white/80 shadow-sm">
                        <div class="font-black text-4xl text-secondary mb-1">85,000+</div>
                        <div class="font-bold text-[15px] text-on-surface">Thành viên năng động</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-2xl text-center border border-white/80 shadow-sm">
                        <div class="font-black text-4xl text-tertiary mb-1">150,000+</div>
                        <div class="font-bold text-[15px] text-on-surface">Đánh giá thật</div>
                    </div>
            </div>
        </section>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('hero-slider-track');
            if (!track) return;
            
            let currentSlide = 0;
            const totalSlides = 3;
            
            setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                track.style.transform = `translateX(-${currentSlide * (100 / totalSlides)}%)`;
            }, 4000);
        });
    </script>

<script>
    function toggleMoreCategories() {
        const extraItems = document.querySelectorAll('.extra-category');
        if (!extraItems.length) return;

        const btnText = document.getElementById('btnToggleCategoriesText');
        const btnIcon = document.getElementById('btnToggleCategoriesIcon');
        const headerText = document.getElementById('headerToggleText');
        const headerIcon = document.getElementById('headerToggleIcon');
        const isCurrentlyHidden = extraItems[0].classList.contains('hidden');

        if (isCurrentlyHidden) {
            extraItems.forEach((el, idx) => {
                el.classList.remove('hidden');
                setTimeout(() => {
                    el.classList.remove('opacity-0', 'translate-y-3');
                }, idx * 35);
            });
            if (btnText) btnText.textContent = 'Thu gọn';
            if (btnIcon) btnIcon.textContent = 'expand_less';
            if (headerText) headerText.textContent = 'Thu gọn';
            if (headerIcon) headerIcon.textContent = 'expand_less';
        } else {
            extraItems.forEach(el => {
                el.classList.add('opacity-0', 'translate-y-3');
                setTimeout(() => {
                    el.classList.add('hidden');
                }, 200);
            });
            const remainingCount = extraItems.length;
            if (btnText) btnText.textContent = 'Xem thêm (' + remainingCount + ' danh mục khác)';
            if (btnIcon) btnIcon.textContent = 'expand_more';
            if (headerText) headerText.textContent = 'Xem thêm (' + remainingCount + ')';
            if (headerIcon) headerIcon.textContent = 'expand_more';

            const section = document.getElementById('home-categories-section');
            if (section) {
                const rect = section.getBoundingClientRect();
                if (rect.top < 0) {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        }
    }
</script>

@endsection

@push('scripts')
<script>
    async function toggleSave(btn, quanId) {
        const card = btn.closest('a, article, .group');
        const quanName = card?.querySelector('h3, h4')?.textContent?.trim() || '';
        const quanImg = card?.querySelector('img')?.getAttribute('src') || null;

        @guest
            if (window.showToast) {
                window.showToast({
                    eyebrow: 'Lưu địa điểm yêu thích',
                    title: 'Đăng nhập để lưu quán',
                    message: quanName ? `Đăng nhập ngay để lưu "${quanName}" vào bộ sưu tập của bạn.` : 'Vui lòng đăng nhập để lưu quán vào danh sách yêu thích.',
                    type: 'warning',
                    image: quanImg,
                    actionText: 'Đăng nhập ngay',
                    actionUrl: "{{ route('login') }}"
                });
            } else {
                window.location.href = "{{ route('login') }}";
            }
            return;
        @endguest

        if (btn.dataset.loading === 'true') return;

        // Find all save buttons for this quan on the page to sync state
        const matchingButtons = document.querySelectorAll(`button[data-quan-id="${quanId}"]`);
        const primaryIcon = btn.querySelector('.material-symbols-outlined');
        const isIconFilled = (el) => el && /['"]?FILL['"]?\s*1/i.test(el.style.fontVariationSettings || el.getAttribute('style') || '');
        const currentlySaved = btn.dataset.saved !== undefined
            ? btn.dataset.saved === '1'
            : isIconFilled(primaryIcon);
        const targetSaved = !currentlySaved;

        const applySaveState = (state) => {
            matchingButtons.forEach(b => {
                b.dataset.saved = state ? '1' : '0';
                b.title = state ? 'Bỏ lưu quán' : 'Lưu quán';
                const icon = b.querySelector('.material-symbols-outlined');
                if (icon) {
                    icon.style.fontVariationSettings = state ? "'FILL' 1" : "'FILL' 0";
                }
            });
        };

        // 1. OPTIMISTIC UI: Update visual state INSTANTLY (0ms delay)
        applySaveState(targetSaved);
        matchingButtons.forEach(b => {
            b.style.transform = 'scale(1.25)';
            setTimeout(() => { b.style.transform = 'scale(1)'; }, 180);
        });

        // 2. SHOW TOAST NOTIFICATION INSTANTLY
        if (window.showToast) {
            if (targetSaved) {
                window.showToast({
                    eyebrow: 'Bộ sưu tập yêu thích',
                    title: quanName ? `Đã lưu "${quanName}"` : 'Đã lưu quán thành công!',
                    message: 'Địa điểm đã được thêm vào danh sách quán yêu thích của bạn.',
                    type: 'saved',
                    image: quanImg,
                    actionText: 'Xem quán đã lưu',
                    actionUrl: "{{ route('quan-da-luu.index') }}"
                });
            } else {
                window.showToast({
                    eyebrow: 'Đã cập nhật danh sách',
                    title: quanName ? `Đã bỏ lưu "${quanName}"` : 'Đã bỏ lưu quán',
                    message: 'Địa điểm đã được gỡ khỏi danh sách yêu thích.',
                    type: 'unsaved',
                    image: quanImg
                });
            }
        }

        matchingButtons.forEach(b => b.dataset.loading = 'true');

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(`/quan-da-luu/${quanId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });

            if (res.status === 401) {
                // Revert icon and redirect to login
                applySaveState(currentlySaved);
                window.location.href = "{{ route('login') }}";
                return;
            }

            const data = await res.json();
            if (data.success) {
                const finalSaved = data.status === 'saved';
                applySaveState(finalSaved);
            } else {
                // Revert on failure
                applySaveState(currentlySaved);
                if (window.showToast) {
                    window.showToast({
                        title: 'Không thể thực hiện',
                        message: data.message || 'Đã có lỗi xảy ra, vui lòng thử lại.',
                        type: 'error'
                    });
                }
            }
        } catch (err) {
            console.error(err);
            // Revert on network error
            applySaveState(currentlySaved);
            if (window.showToast) {
                window.showToast({
                    title: 'Lỗi kết nối',
                    message: 'Không thể gửi yêu cầu đến máy chủ, vui lòng thử lại.',
                    type: 'error'
                });
            }
        } finally {
            matchingButtons.forEach(b => b.dataset.loading = 'false');
        }
    }

    // ==========================================
    // TỰ ĐỘNG YÊU CẦU TRUY CẬP VỊ TRÍ & QUÁN GẦN BẠN
    // ==========================================
    window.QUANMOI_SAVED_IDS = @json($savedQuanIds ?? []);

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showNearbyLoadingSkeleton() {
        const gridNearby = document.getElementById('venues-grid-nearby');
        const promptBanner = document.getElementById('nearby-permission-banner');
        if (promptBanner) promptBanner.classList.add('hidden');

        if (gridNearby) {
            gridNearby.dataset.loading = 'true';
            gridNearby.classList.remove('hidden');
            gridNearby.classList.add('grid');
            gridNearby.innerHTML = [1, 2, 3].map(() => `
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-pulse">
                    <div class="h-48 sm:h-52 bg-gray-200 w-full"></div>
                    <div class="p-4 sm:p-5 space-y-3">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                        <div class="h-8 bg-gray-100 rounded mt-4"></div>
                    </div>
                </div>
            `).join('');
        }
    }

    function generateVenueCardHtml(quan) {
        const isSaved = (window.QUANMOI_SAVED_IDS || []).includes(quan.id);
        const anhBia = quan.anh_dai_dien || 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80';
        const rating = (quan.so_sao_trung_binh && quan.so_sao_trung_binh > 0) ? quan.so_sao_trung_binh : '4.9';
        const loaiHinh = (quan.loai_hinh_kinh_doanh || 'Quán ăn').replace(/_/g, ' ');
        const giaNhoNhat = Number(quan.gia_nho_nhat || 0).toLocaleString('vi-VN');
        const giaLonNhat = Number(quan.gia_lon_nhat || 0).toLocaleString('vi-VN');
        const distanceKm = quan.khoang_cach_km !== undefined ? quan.khoang_cach_km : null;

        return `
        <a href="${quan.url}" class="qm-venue-card group cursor-pointer animate-fadeIn">
            <div class="qm-venue-thumb">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                     src="${anhBia}" 
                     alt="${escapeHtml(quan.ten_quan)}" 
                     loading="lazy" />
                
                <div class="absolute top-2 left-2 sm:top-3 sm:left-3 z-10">
                    <div class="qm-venue-badge-rating">
                        <span class="material-symbols-outlined text-[12px] sm:text-[14px] text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span>${rating}</span>
                    </div>
                </div>

                <div class="absolute top-2 right-2 sm:top-3 sm:right-3 flex items-center gap-1 sm:gap-1.5 z-10">
                    ${quan.dang_mo_cua ? `
                        <span class="qm-venue-badge-status bg-emerald-600/95 text-white">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            <span>Mở cửa</span>
                        </span>
                    ` : ''}
                    ${quan.is_noi_bat ? `
                        <span class="qm-venue-badge-hot" title="Quán nổi bật">
                            <span class="material-symbols-outlined text-[12px] sm:text-[14px]" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                        </span>
                    ` : ''}
                </div>

                <div class="absolute bottom-2 left-2 sm:bottom-3 sm:left-3 z-10 flex items-center gap-1 sm:gap-1.5 max-w-[calc(100%-44px)] sm:max-w-[calc(100%-56px)] pointer-events-none">
                    <span class="qm-venue-badge-category truncate" title="${loaiHinh}">
                        <span class="material-symbols-outlined text-[11px] sm:text-[13px] text-primary shrink-0">storefront</span>
                        <span class="truncate">${loaiHinh}</span>
                    </span>
                    ${distanceKm !== null ? `
                        <span class="qm-venue-badge-status bg-primary/95 text-white whitespace-nowrap" title="Cách vị trí của bạn ${distanceKm} km">
                            <span class="material-symbols-outlined text-[11px] sm:text-[12px]" style="font-variation-settings: 'FILL' 1;">near_me</span>
                            <span>${distanceKm} km</span>
                        </span>
                    ` : ''}
                </div>

                <button type="button" 
                        data-quan-id="${quan.id}" 
                        data-saved="${isSaved ? '1' : '0'}"
                        onclick="event.preventDefault(); toggleSave(this, '${quan.id}')" 
                        class="qm-venue-save-btn absolute bottom-2 right-2 sm:bottom-3 sm:right-3 z-10 cursor-pointer" 
                        title="${isSaved ? 'Bỏ lưu quán' : 'Lưu quán'}">
                    <span class="material-symbols-outlined text-[15px] sm:text-[18px]" style="font-variation-settings: 'FILL' ${isSaved ? '1' : '0'};">favorite</span>
                </button>
            </div>

            <div class="p-2.5 sm:p-4 flex flex-col flex-1 justify-between gap-2 sm:gap-3">
                <div class="space-y-1 sm:space-y-2">
                    <div class="flex items-start justify-between gap-1 sm:gap-2">
                        <h3 class="min-w-0 flex-1 font-bold text-[13px] sm:text-[16px] leading-snug text-slate-900 line-clamp-1 group-hover:text-primary transition-colors">${escapeHtml(quan.ten_quan)}</h3>
                        ${quan.is_xac_thuc ? `
                            <span class="inline-flex items-center justify-center w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-emerald-50 text-emerald-600 shrink-0 mt-0.5" title="Quán đã xác thực uy tín">
                                <span class="material-symbols-outlined text-[13px] sm:text-[15px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                            </span>
                        ` : ''}
                    </div>

                    <div class="flex items-start gap-1.5 sm:gap-2 text-slate-500 text-[11px] sm:text-[12.5px]">
                        <span class="qm-venue-loc-icon">
                            <span class="material-symbols-outlined text-[11px] sm:text-[13px]" style="font-variation-settings: 'FILL' 1;">location_on</span>
                        </span>
                        <span class="line-clamp-1 sm:line-clamp-2 leading-relaxed text-slate-600">${escapeHtml(quan.dia_chi || '')}</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between pt-2 sm:pt-2.5 border-t border-slate-100 gap-1 sm:gap-2 min-w-0 mt-auto">
                    <div class="flex items-center gap-1.5 sm:gap-2 min-w-0 flex-shrink">
                        <span class="inline-flex items-center gap-1 text-slate-500 text-[10.5px] sm:text-[12px] font-medium whitespace-nowrap flex-shrink-0">
                            <span class="material-symbols-outlined text-[12px] sm:text-[14px] text-slate-400">visibility</span>
                            <span>${Number(quan.luot_xem || 0).toLocaleString('vi-VN')}</span>
                        </span>
                    </div>

                    <span class="qm-venue-price-pill ml-auto">
                        <span>${giaNhoNhat}đ - ${giaLonNhat}đ</span>
                    </span>
                </div>
            </div>
        </a>
        `;
    }

    function generateMobileVenueItemHtml(quan) {
        const anhBia = quan.anh_dai_dien || 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=300&q=80';
        const giaNhoNhat = Number(quan.gia_nho_nhat || 0).toLocaleString('vi-VN');
        const giaLonNhat = Number(quan.gia_lon_nhat || 0).toLocaleString('vi-VN');

        return `
        <a href="${quan.url}" class="bg-white rounded-2xl p-3 flex gap-3 shadow-sm border border-gray-100 active:bg-gray-50 transition-colors animate-fadeIn">
            <div class="w-24 h-24 rounded-xl overflow-hidden flex-shrink-0 relative">
                <img class="w-full h-full object-cover" src="${anhBia}" loading="lazy"/>
                <div class="absolute bottom-1 right-1 bg-black/70 text-white px-1.5 py-0.5 rounded-md text-[10px] font-bold flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-[10px] text-amber-400" style="font-variation-settings: 'FILL' 1;">star</span> 4.9
                </div>
            </div>
            <div class="flex flex-col justify-between flex-1 py-0.5">
                <div>
                    <h3 class="font-bold text-[15px] text-on-surface leading-tight flex items-center gap-1">
                        ${escapeHtml(quan.ten_quan)}
                        ${quan.is_xac_thuc ? `<span class="material-symbols-outlined text-tick-xanh text-[15px]" style="font-variation-settings: 'FILL' 1;">verified</span>` : ''}
                    </h3>
                    <p class="text-[12px] text-text-muted mt-1 truncate">${escapeHtml(quan.dia_chi || '')}</p>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-primary">${giaNhoNhat}đ - ${giaLonNhat}đ</span>
                    <span class="text-[11px] text-emerald-600 font-bold flex items-center gap-0.5"><span class="material-symbols-outlined text-[12px]">near_me</span> ${quan.khoang_cach_km} km</span>
                </div>
            </div>
        </a>
        `;
    }

    function requestUserLocation(isUserInitiated = false) {
        if (!("geolocation" in navigator)) {
            if (isUserInitiated && window.showToast) {
                window.showToast({
                    title: 'Không hỗ trợ',
                    message: 'Trình duyệt của bạn không hỗ trợ định vị GPS.',
                    type: 'error'
                });
            }
            return;
        }

        // Tự động gọi trình duyệt yêu cầu truy cập vị trí
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // 1. Lưu tọa độ vào cookie (7 ngày) & sessionStorage để các trang khác và SSR sử dụng
                document.cookie = `user_lat=${lat}; path=/; max-age=604800; SameSite=Lax`;
                document.cookie = `user_lng=${lng}; path=/; max-age=604800; SameSite=Lax`;
                sessionStorage.setItem('quanmoi_geo_status', 'granted');

                // 2. Hiển thị trạng thái đang tải (skeleton)
                showNearbyLoadingSkeleton();

                // 3. Tải danh sách quán lân cận
                fetchNearbyVenues(lat, lng, isUserInitiated);
            },
            function(error) {
                // Người dùng từ chối cấp quyền hoặc đóng popup:
                // Giữ nguyên hiển thị mặc định như bình thường!
                console.log('Location permission was denied or dismissed:', error.code, error.message);
                sessionStorage.setItem('quanmoi_geo_status', 'denied');

                if (isUserInitiated && window.showToast) {
                    window.showToast({
                        title: 'Chưa cấp quyền',
                        message: 'Bạn đã từ chối chia sẻ vị trí. Bạn có thể bật lại bất cứ lúc nào.',
                        type: 'info'
                    });
                }
            },
            {
                enableHighAccuracy: false,
                timeout: 8000,
                maximumAge: 300000
            }
        );
    }

    async function fetchNearbyVenues(lat, lng, scrollOnSuccess = false) {
        const gridNearby = document.getElementById('venues-grid-nearby');
        const mobileNearby = document.getElementById('mobile-venues-nearby');
        const promptBanner = document.getElementById('nearby-permission-banner');

        // Thử bán kính 20km (bao quát đô thị TP.HCM / Hà Nội)
        try {
            let res = await fetch(`/api/ban-do/quan-lan-can?lat=${lat}&lng=${lng}&radius=20&per_page=9`);
            let data = await res.json();

            // Nếu 20km chưa có, tự động mở rộng quét 50km
            if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
                res = await fetch(`/api/ban-do/quan-lan-can?lat=${lat}&lng=${lng}&radius=50&per_page=9`);
                data = await res.json();
            }

            if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                if (promptBanner) promptBanner.classList.add('hidden');

                if (gridNearby) {
                    delete gridNearby.dataset.loading;
                    gridNearby.classList.remove('hidden');
                    gridNearby.classList.add('grid');
                    gridNearby.innerHTML = data.data.map(q => generateVenueCardHtml(q)).join('');
                }

                if (mobileNearby) {
                    mobileNearby.innerHTML = data.data.map(q => generateMobileVenueItemHtml(q)).join('');
                }

                const subtitle = document.getElementById('nearby-subtitle');
                if (subtitle) {
                    subtitle.textContent = `Tìm thấy ${data.data.length} quán ăn gần bạn nhất (sắp xếp theo khoảng cách)`;
                }

                const mobileSubtitle = document.getElementById('mobile-nearby-subtitle');
                if (mobileSubtitle) {
                    mobileSubtitle.textContent = `Tìm thấy ${data.data.length} quán ăn gần bạn nhất`;
                }

                if (scrollOnSuccess) {
                    document.getElementById('home-nearby-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                // Không có quán trong bán kính
                if (promptBanner) promptBanner.classList.add('hidden');

                if (gridNearby) {
                    delete gridNearby.dataset.loading;
                    gridNearby.classList.remove('hidden');
                    gridNearby.classList.add('grid');
                    gridNearby.innerHTML = `
                        <div class="col-span-full w-full max-w-2xl mx-auto bg-white rounded-2xl p-8 text-center border border-gray-100 shadow-sm flex flex-col items-center justify-center" style="grid-column: 1 / -1;">
                            <span class="material-symbols-outlined text-4xl text-amber-500 mb-2">location_off</span>
                            <h3 class="font-bold text-gray-900 text-base mb-1">Chưa có quán ăn trong bán kính lân cận</h3>
                            <p class="text-xs text-gray-500 max-w-md mx-auto">Hiện tại chưa có quán ăn nào thuộc hệ thống Quán Mới gần tọa độ của bạn. Bạn hãy khám phá các quán ăn nổi bật phía trên nhé!</p>
                        </div>
                    `;
                }
            }
        } catch (err) {
            console.error('Error fetching nearby venues:', err);
            if (gridNearby) {
                delete gridNearby.dataset.loading;
            }
        }
    }

    // Tự động kiểm tra và yêu cầu truy cập vị trí khi vào website
    document.addEventListener('DOMContentLoaded', function() {
        const geoStatus = sessionStorage.getItem('quanmoi_geo_status');
        const hasServerGps = {{ ($hasGps && $quanGanBan->isNotEmpty()) ? 'true' : 'false' }};

        if (hasServerGps) {
            // Server-Side Rendering đã tải sẵn quán gần bạn qua Cookie
            const promptBanner = document.getElementById('nearby-permission-banner');
            if (promptBanner) promptBanner.classList.add('hidden');
        } else if (geoStatus === 'granted') {
            // Đã cấp quyền trước đó nhưng chưa có dữ liệu SSR -> tự động tải
            requestUserLocation(false);
        } else if (geoStatus !== 'denied') {
            // Lần đầu vào trang: tự động xin quyền vị trí
            requestUserLocation(false);
        }
    });
    </script>
@endpush
