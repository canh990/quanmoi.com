@extends('layouts.app')

@php
    $danhMuc      = $danhMuc ?? request('danh_muc', '');
    $tuKhoa       = $tuKhoa ?? request('tu_khoa', '');
    $tinhThanhId  = $tinhThanhId ?? request('tinh_thanh_id', '');
    $quanHuyenId  = $quanHuyenId ?? request('quan_huyen_id', '');
    $mucGia       = $mucGia ?? request('muc_gia', '');
    $sapXep       = $sapXep ?? request('sap_xep', '');
    $isXacThuc    = $isXacThuc ?? request()->boolean('is_xac_thuc');
    $isNoiBat     = $isNoiBat ?? ($danhMuc === 'Quán Nổi Bật' || request()->boolean('is_noi_bat'));
    $dangMoCua    = $dangMoCua ?? request()->boolean('dang_mo_cua');
    $coShopee     = $coShopee ?? request()->boolean('co_shopeefood');
    $tenTinhThanh = $tenTinhThanh ?? '';
    $quansGoiY    = $quansGoiY ?? collect();
    $monAns       = $monAns ?? collect();

    $seoSlug = match($danhMuc) {
        'Quán Nổi Bật' => 'quan-noi-bat',
        'Quán Mới'     => 'quan-moi',
        default        => 'kham-pha',
    };
    $seo = \App\Models\SeoSetting::forPage($seoSlug);

    $isSpecialPage = in_array($danhMuc, ['Quán Nổi Bật', 'Quán Mới']);

    $hasActiveFilters = !empty($tuKhoa) 
        || (!empty($danhMuc) && !$isSpecialPage)
        || !empty($tinhThanhId) 
        || !empty($quanHuyenId) 
        || !empty($mucGia) 
        || !empty($isXacThuc) 
        || (!empty($isNoiBat) && !$isSpecialPage)
        || !empty($dangMoCua) 
        || !empty($coShopee);

    $isAllActive = (empty($danhMuc) || $isSpecialPage) && empty($mucGia) && empty($isXacThuc) && empty($dangMoCua) && empty($coShopee);
    $isDangMoCuaActive = !empty($dangMoCua);
    $isUnder50kActive = ($mucGia === 'duoi_50k');
    $isXacThucActive = !empty($isXacThuc);
    $isShopeeActive = !empty($coShopee);
    $isLauNuongActive = ($danhMuc === 'Lẩu & Nướng');
    $isCaPheActive = in_array($danhMuc, ['Cà phê', 'Cà phê & Trà']);
@endphp

@section('title', $seo->meta_title ?: (($danhMuc ? $danhMuc . ' - ' : '') . 'Khám phá quán ngon | Quán Mới'))

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
@endpush

@section('content')
<main class="pb-24 bg-surface-container-lowest min-h-screen">
    
    {{-- Header Banner --}}
    <div class="bg-primary/5 border-b border-primary/10">
        <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-10 pt-8 pb-6 md:pt-10 md:pb-7">
            <h1 class="text-3xl md:text-4xl font-black text-on-surface mb-2.5 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-4xl">travel_explore</span>
                @if(isset($tuKhoa) && $tuKhoa !== '')
                    Kết quả tìm kiếm: "{{ $tuKhoa }}"
                @else
                    {{ $danhMuc ? 'Khám phá: ' . $danhMuc : 'Khám phá tất cả quán ngon' }}
                @endif
            </h1>
            <p class="text-on-surface-variant text-base md:text-lg mb-5">
                Tìm kiếm những địa điểm ẩm thực và giải trí tuyệt vời nhất.
            </p>

            {{-- Search Bar --}}
            <div class="max-w-3xl">
                <x-search-bar prefix="kp" />
            </div>

            {{-- Hàng Filter Chips nhanh (Quick Filter Pills) nằm ngang --}}
            <div class="mt-4 sm:mt-5 w-full">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1 scroll-smooth">
                    {{-- [Tất cả] --}}
                    <a href="{{ route('kham-pha') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isAllActive ? 'bg-primary text-white border-primary shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-primary hover:bg-white border-gray-200/90 hover:border-primary/40 font-semibold' }}">
                        <span class="material-symbols-outlined text-[17px]">apps</span>
                        <span>Tất cả</span>
                    </a>

                    {{-- [Đang mở cửa 🟢] --}}
                    <a href="{{ $isDangMoCuaActive ? request()->fullUrlWithQuery(['dang_mo_cua' => null]) : request()->fullUrlWithQuery(['dang_mo_cua' => 1]) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isDangMoCuaActive ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-emerald-700 hover:bg-white border-gray-200/90 hover:border-emerald-300 font-semibold' }}">
                        <span class="w-2.5 h-2.5 rounded-full {{ $isDangMoCuaActive ? 'bg-white' : 'bg-emerald-500 animate-pulse' }}"></span>
                        <span>Đang mở cửa</span>
                    </a>

                    {{-- [Dưới 50k] --}}
                    <a href="{{ $isUnder50kActive ? request()->fullUrlWithQuery(['muc_gia' => null]) : request()->fullUrlWithQuery(['muc_gia' => 'duoi_50k']) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isUnder50kActive ? 'bg-primary text-white border-primary shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-primary hover:bg-white border-gray-200/90 hover:border-primary/40 font-semibold' }}">
                        <span class="material-symbols-outlined text-[17px] {{ $isUnder50kActive ? 'text-white' : 'text-amber-600' }}">payments</span>
                        <span>Dưới 50k</span>
                    </a>

                    {{-- [Tích xanh ✅] --}}
                    <a href="{{ $isXacThucActive ? request()->fullUrlWithQuery(['is_xac_thuc' => null]) : request()->fullUrlWithQuery(['is_xac_thuc' => 1]) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isXacThucActive ? 'bg-sky-600 text-white border-sky-600 shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-sky-600 hover:bg-white border-gray-200/90 hover:border-sky-300 font-semibold' }}">
                        <span class="material-symbols-outlined text-[17px] {{ $isXacThucActive ? 'text-white' : 'text-sky-500' }}" style="font-variation-settings: 'FILL' 1;">verified</span>
                        <span>Tích xanh</span>
                    </a>

                    {{-- [ShopeeFood] --}}
                    <a href="{{ $isShopeeActive ? request()->fullUrlWithQuery(['co_shopeefood' => null]) : request()->fullUrlWithQuery(['co_shopeefood' => 1]) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isShopeeActive ? 'bg-[#EE4D2D] text-white border-[#EE4D2D] shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-[#EE4D2D] hover:bg-white border-gray-200/90 hover:border-orange-300 font-semibold' }}">
                        <span class="material-symbols-outlined text-[17px] {{ $isShopeeActive ? 'text-white' : 'text-[#EE4D2D]' }}">moped</span>
                        <span>ShopeeFood</span>
                    </a>

                    {{-- [Lẩu & Nướng] --}}
                    <a href="{{ $isLauNuongActive ? request()->fullUrlWithQuery(['danh_muc' => null]) : request()->fullUrlWithQuery(['danh_muc' => 'Lẩu & Nướng']) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isLauNuongActive ? 'bg-primary text-white border-primary shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-primary hover:bg-white border-gray-200/90 hover:border-primary/40 font-semibold' }}">
                        <span class="material-symbols-outlined text-[17px] {{ $isLauNuongActive ? 'text-white' : 'text-rose-500' }}">skillet</span>
                        <span>Lẩu & Nướng</span>
                    </a>

                    {{-- [Cà phê] --}}
                    <a href="{{ $isCaPheActive ? request()->fullUrlWithQuery(['danh_muc' => null]) : request()->fullUrlWithQuery(['danh_muc' => 'Cà phê']) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isCaPheActive ? 'bg-primary text-white border-primary shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-primary hover:bg-white border-gray-200/90 hover:border-primary/40 font-semibold' }}">
                        <span class="material-symbols-outlined text-[17px] {{ $isCaPheActive ? 'text-white' : 'text-amber-700' }}">local_cafe</span>
                        <span>Cà phê</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-10 mt-5 sm:mt-6">

        {{-- Thanh hiển thị bộ lọc đang áp dụng (Active Filter Tags) --}}
        @if($hasActiveFilters)
            <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-gray-100 shadow-2xs mb-6 flex flex-wrap items-center gap-2 sm:gap-2.5">
                <div class="flex items-center gap-1.5 text-gray-500 font-bold text-[13px] sm:text-[13.5px] mr-1">
                    <span class="material-symbols-outlined text-[18px] text-primary" style="color: #a04100;">tune</span>
                    <span>Đang lọc theo:</span>
                </div>

                {{-- Từ khóa --}}
                @if(!empty($tuKhoa))
                    <a href="{{ request()->fullUrlWithQuery(['tu_khoa' => null]) }}" 
                       class="inline-flex items-center gap-1.5 bg-primary/10 hover:bg-primary/20 text-primary border border-primary/25 rounded-full px-3 py-1 text-[13px] font-bold transition-all group"
                       title="Bỏ lọc từ khóa">
                        <span>Từ khóa: "{{ $tuKhoa }}"</span>
                        <span class="material-symbols-outlined text-[15px] group-hover:rotate-90 transition-transform">close</span>
                    </a>
                @endif

                {{-- Tỉnh thành --}}
                @if(!empty($tinhThanhId))
                    <a href="{{ request()->fullUrlWithQuery(['tinh_thanh_id' => null, 'quan_huyen_id' => null]) }}" 
                       class="inline-flex items-center gap-1.5 bg-primary/10 hover:bg-primary/20 text-primary border border-primary/25 rounded-full px-3 py-1 text-[13px] font-bold transition-all group"
                       title="Bỏ lọc khu vực">
                        <span class="material-symbols-outlined text-[15px]">location_on</span>
                        <span>{{ $tenTinhThanh ?: $tinhThanhId }}</span>
                        <span class="material-symbols-outlined text-[15px] group-hover:rotate-90 transition-transform">close</span>
                    </a>
                @endif

                {{-- Quận huyện --}}
                @if(!empty($quanHuyenId))
                    <a href="{{ request()->fullUrlWithQuery(['quan_huyen_id' => null]) }}" 
                       class="inline-flex items-center gap-1.5 bg-primary/10 hover:bg-primary/20 text-primary border border-primary/25 rounded-full px-3 py-1 text-[13px] font-bold transition-all group"
                       title="Bỏ lọc quận huyện">
                        <span>{{ $quanHuyenId }}</span>
                        <span class="material-symbols-outlined text-[15px] group-hover:rotate-90 transition-transform">close</span>
                    </a>
                @endif

                {{-- Danh mục --}}
                @if(!empty($danhMuc) && !$isSpecialPage)
                    <a href="{{ request()->fullUrlWithQuery(['danh_muc' => null]) }}" 
                       class="inline-flex items-center gap-1.5 bg-primary/10 hover:bg-primary/20 text-primary border border-primary/25 rounded-full px-3 py-1 text-[13px] font-bold transition-all group"
                       title="Bỏ lọc danh mục">
                        <span>{{ $danhMuc }}</span>
                        <span class="material-symbols-outlined text-[15px] group-hover:rotate-90 transition-transform">close</span>
                    </a>
                @endif

                {{-- Đang mở cửa --}}
                @if(!empty($dangMoCua))
                    <a href="{{ request()->fullUrlWithQuery(['dang_mo_cua' => null]) }}" 
                       class="inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full px-3 py-1 text-[13px] font-bold transition-all group"
                       title="Bỏ lọc đang mở cửa">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Đang mở cửa</span>
                        <span class="material-symbols-outlined text-[15px] group-hover:rotate-90 transition-transform">close</span>
                    </a>
                @endif

                {{-- Mức giá --}}
                @if($mucGia === 'duoi_50k')
                    <a href="{{ request()->fullUrlWithQuery(['muc_gia' => null]) }}" 
                       class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-full px-3 py-1 text-[13px] font-bold transition-all group"
                       title="Bỏ lọc mức giá">
                        <span>Dưới 50k</span>
                        <span class="material-symbols-outlined text-[15px] group-hover:rotate-90 transition-transform">close</span>
                    </a>
                @endif

                {{-- Tích xanh --}}
                @if(!empty($isXacThuc))
                    <a href="{{ request()->fullUrlWithQuery(['is_xac_thuc' => null]) }}" 
                       class="inline-flex items-center gap-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 rounded-full px-3 py-1 text-[13px] font-bold transition-all group"
                       title="Bỏ lọc tích xanh">
                        <span>Đã xác thực ✅</span>
                        <span class="material-symbols-outlined text-[15px] group-hover:rotate-90 transition-transform">close</span>
                    </a>
                @endif

                {{-- ShopeeFood --}}
                @if(!empty($coShopee))
                    <a href="{{ request()->fullUrlWithQuery(['co_shopeefood' => null]) }}" 
                       class="inline-flex items-center gap-1.5 bg-orange-50 hover:bg-orange-100 text-[#EE4D2D] border border-orange-200 rounded-full px-3 py-1 text-[13px] font-bold transition-all group"
                       title="Bỏ lọc ShopeeFood">
                        <span>ShopeeFood 🛵</span>
                        <span class="material-symbols-outlined text-[15px] group-hover:rotate-90 transition-transform">close</span>
                    </a>
                @endif

                {{-- Nổi bật --}}
                @if(!empty($isNoiBat) && !$isSpecialPage)
                    <a href="{{ request()->fullUrlWithQuery(['is_noi_bat' => null]) }}" 
                       class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-full px-3 py-1 text-[13px] font-bold transition-all group"
                       title="Bỏ lọc nổi bật">
                        <span>Nổi bật ⭐</span>
                        <span class="material-symbols-outlined text-[15px] group-hover:rotate-90 transition-transform">close</span>
                    </a>
                @endif

                {{-- Nút Xóa tất cả bộ lọc --}}
                <a href="{{ route('kham-pha') }}" 
                   class="inline-flex items-center gap-1 text-[12.5px] font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-full transition-all ml-auto cursor-pointer border border-red-200 active:scale-95"
                   title="Xoá tất cả điều kiện lọc">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    <span>Xoá tất cả bộ lọc</span>
                </a>
            </div>
        @endif

        {{-- Sort Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 sm:mb-6">
            <p class="text-on-surface-variant font-medium text-[15px]">
                Tìm thấy <span class="font-bold text-on-surface">{{ $quans->total() }}</span> kết quả
            </p>
            <div class="flex items-center gap-3">
                <label for="sap_xep" class="text-[14px] text-gray-500 font-medium whitespace-nowrap">Sắp xếp:</label>
                <select id="sap_xep" class="border border-gray-200 rounded-xl px-3 py-2 text-[14px] bg-white outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 text-gray-700 font-medium cursor-pointer transition-all" onchange="const url = new URL(window.location.href); url.searchParams.set('sap_xep', this.value); window.location.href = url.toString();">
                    <option value="" {{ empty($sapXep) ? 'selected' : '' }}>Độ liên quan / Mặc định</option>
                    <option value="near_me" {{ ($sapXep ?? '') === 'near_me' ? 'selected' : '' }}>Gần tôi nhất</option>
                    <option value="view_desc" {{ ($sapXep ?? '') === 'view_desc' ? 'selected' : '' }}>Xem nhiều nhất</option>
                    <option value="created_desc" {{ ($sapXep ?? '') === 'created_desc' ? 'selected' : '' }}>Mới đăng gần đây</option>
                </select>
            </div>
        </div>

        {{-- Món ăn được tìm thấy (khi có từ khóa tìm kiếm) --}}
        @if(isset($monAns) && $monAns->isNotEmpty())
            <section class="mb-12" id="ket-qua-mon-an">
                <div class="flex items-end justify-between mb-5">
                    <div>
                        <h2 class="text-xl md:text-2xl font-black text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[26px]">restaurant_menu</span>
                            Món ăn được tìm thấy
                        </h2>
                        <p class="text-text-muted text-[14px] mt-1">
                            {{ $monAns->count() }} món khớp với "<span class="font-semibold text-on-surface">{{ $tuKhoa }}</span>"
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    @foreach($monAns as $mon)
                        @php $quanCuaMon = $mon->danhMuc?->quan; @endphp
                        @continue(!$quanCuaMon)
                        <a href="{{ route('quan.detail', $quanCuaMon->slug) }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col">
                            <div class="relative h-32 w-full overflow-hidden bg-gray-100">
                                <img
                                    src="{{ $mon->hinh_anh ?: ($quanCuaMon->anh_bia ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80') }}"
                                    alt="{{ $mon->ten_mon }}"
                                    loading="lazy"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                />
                                @if(!$mon->con_hang)
                                    <span class="absolute top-2 left-2 bg-black/70 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">Hết món</span>
                                @endif
                                @if($mon->gia)
                                    <span class="absolute bottom-2 right-2 bg-primary text-white text-[12px] font-bold px-2 py-0.5 rounded-lg shadow-sm">
                                        {{ number_format($mon->gia, 0, ',', '.') }}đ
                                    </span>
                                @endif
                            </div>
                            <div class="p-3 flex flex-col flex-grow">
                                <h3 class="font-bold text-[14px] text-on-surface line-clamp-2 group-hover:text-primary transition-colors">{{ $mon->ten_mon }}</h3>
                                <p class="mt-auto pt-2 text-[12px] text-text-muted flex items-center gap-1 truncate">
                                    <span class="material-symbols-outlined text-[14px] text-primary">storefront</span>
                                    <span class="truncate">{{ $quanCuaMon->ten_quan }}</span>
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            <h2 class="text-xl md:text-2xl font-black text-on-surface flex items-center gap-2 border-t border-gray-200" style="margin-top: 48px; padding-top: 32px; margin-bottom: 20px;">
                <span class="material-symbols-outlined text-primary text-[26px]">storefront</span>
                Quán ăn liên quan
            </h2>
        @endif

        {{-- Danh sách quán hoặc Xử lý trạng thái trống (Smart Empty State) --}}
        @if($quans->isEmpty())
            <div class="py-8">
                <div class="max-w-xl mx-auto text-center bg-white rounded-3xl border border-gray-100 shadow-sm p-8 sm:p-12">
                    <div class="w-20 h-20 mx-auto rounded-3xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary mb-5 shadow-inner" style="color: #a04100;">
                        <span class="material-symbols-outlined text-4xl">search_off</span>
                    </div>
                    
                    <h3 class="text-xl sm:text-2xl font-black text-on-surface mb-2.5">Không tìm thấy quán nào</h3>
                    <p class="text-on-surface-variant text-[14.5px] leading-relaxed mb-6">
                        @if($hasActiveFilters)
                            Rất tiếc, các tiêu chí lọc hiện tại không có kết quả phù hợp. Bạn hãy thử xóa bớt điều kiện lọc hoặc tham khảo các quán nổi bật bên dưới nhé.
                        @else
                            Hiện tại chưa có quán nào trong danh mục này.
                        @endif
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ route('kham-pha') }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-white font-bold text-[14px] px-6 py-3 rounded-xl hover:brightness-110 shadow-md transition-all active:scale-95 cursor-pointer"
                           style="background-color: #a04100; color: #ffffff;">
                            <span class="material-symbols-outlined text-[18px]">filter_alt_off</span>
                            <span>Xóa bộ lọc để xem các quán khác</span>
                        </a>
                    </div>
                </div>

                {{-- Gợi ý các quán nổi bật tương tự (Smart Recommendations) --}}
                @if(isset($quansGoiY) && $quansGoiY->isNotEmpty())
                    <div class="mt-16 pt-10 border-t border-gray-200/80">
                        <div class="flex items-end justify-between mb-6">
                            <div>
                                <h2 class="text-xl sm:text-2xl font-black text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-amber-500 text-[28px]" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                                    Gợi ý quán nổi bật dành cho bạn
                                </h2>
                                <p class="text-text-muted text-[13.5px] sm:text-[14px] mt-1">
                                    Những địa điểm ẩm thực được cộng đồng đánh giá cao nhất
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($quansGoiY as $quan)
                                <a href="{{ route('quan.detail', $quan->slug) }}" class="block bg-white rounded-2xl shadow-sm overflow-hidden group cursor-pointer hover:shadow-xl transition-all border border-gray-100 flex flex-col h-full">
                                    <div class="relative h-48 w-full overflow-hidden flex-shrink-0">
                                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $quan->anh_bia ? $quan->anh_bia : 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $quan->ten_quan }}"/>
                                        
                                        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-2.5 py-1 rounded-xl text-primary font-bold text-xs flex items-center gap-1 shadow-sm">
                                            <span class="material-symbols-outlined text-[15px] text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span> 4.9
                                        </div>

                                        @if($quan->is_noi_bat)
                                            <div class="absolute top-3 right-3 bg-amber-500 text-white font-bold text-[11px] px-2 py-0.5 rounded-lg shadow-sm flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[13px]" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                                                Nổi bật
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-5 flex flex-col flex-grow">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="font-bold text-[17px] text-on-surface truncate group-hover:text-primary transition-colors pr-2">{{ $quan->ten_quan }}</h3>
                                            @if($quan->is_xac_thuc)
                                                <span class="material-symbols-outlined text-tick-xanh text-[18px] flex-shrink-0" style="font-variation-settings: 'FILL' 1;" title="Đã xác thực">verified</span>
                                            @endif
                                        </div>
                                        <p class="text-text-muted text-[13px] flex items-start gap-1.5 mb-4 flex-grow">
                                            <span class="material-symbols-outlined text-[16px] text-gray-400 mt-0.5">location_on</span>
                                            <span class="line-clamp-2 leading-relaxed">{{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_phuong_xa }}, {{ $quan->ten_quan_huyen }}</span>
                                        </p>
                                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto gap-2 min-w-0">
                                            <div class="flex items-center gap-1.5 min-w-0 flex-shrink">
                                                <span class="bg-gray-100 px-2 py-0.5 rounded-md text-gray-600 text-[11px] font-medium whitespace-nowrap truncate max-w-[100px]" title="{{ str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}">
                                                    {{ str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}
                                                </span>
                                                @if(!empty($quan->shopeefood_url))
                                                    <span class="bg-orange-50 text-[#EE4D2D] border border-orange-200/60 px-1.5 py-0.5 rounded text-[10.5px] font-bold whitespace-nowrap flex items-center gap-0.5" title="Có đặt trên ShopeeFood">
                                                        <span class="material-symbols-outlined text-[12px]">moped</span>
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-primary font-bold text-[12.5px] sm:text-[13px] whitespace-nowrap flex-shrink-0 ml-auto">
                                                {{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            {{-- Grid quán ăn --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($quans as $quan)
                    <a href="{{ route('quan.detail', $quan->slug) }}" class="block bg-white rounded-2xl shadow-sm overflow-hidden group cursor-pointer hover:shadow-xl transition-all border border-gray-100 flex flex-col h-full">
                        <div class="relative h-48 w-full overflow-hidden flex-shrink-0">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $quan->anh_bia ? $quan->anh_bia : 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $quan->ten_quan }}"/>
                            
                            {{-- Rating badge --}}
                            <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-2.5 py-1 rounded-xl text-primary font-bold text-xs flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-[15px] text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span> 4.9
                            </div>

                            {{-- Open badge / ShopeeFood badge --}}
                            <div class="absolute top-3 right-3 flex items-center gap-1">
                                @if(method_exists($quan, 'isDangMoCua') && $quan->isDangMoCua())
                                    <span class="bg-emerald-600/90 backdrop-blur-md text-white px-2 py-0.5 rounded-lg text-[10.5px] font-bold flex items-center gap-1 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        Mở cửa
                                    </span>
                                @endif
                                @if($quan->is_noi_bat)
                                    <span class="bg-amber-500 text-white font-bold text-[10.5px] px-2 py-0.5 rounded-lg shadow-sm flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[12px]" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-bold text-[17px] text-on-surface truncate group-hover:text-primary transition-colors pr-2">{{ $quan->ten_quan }}</h3>
                                @if($quan->is_xac_thuc)
                                    <span class="material-symbols-outlined text-tick-xanh text-[18px] flex-shrink-0" style="font-variation-settings: 'FILL' 1;" title="Đã xác thực">verified</span>
                                @endif
                            </div>
                            <p class="text-text-muted text-[13px] flex items-start gap-1.5 mb-4 flex-grow">
                                <span class="material-symbols-outlined text-[16px] text-gray-400 mt-0.5">location_on</span>
                                <span class="line-clamp-2 leading-relaxed">{{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_phuong_xa }}, {{ $quan->ten_quan_huyen }}</span>
                            </p>
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto gap-2 min-w-0">
                                <div class="flex items-center gap-1.5 min-w-0 flex-shrink">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded-md text-gray-600 text-[11px] font-medium whitespace-nowrap truncate max-w-[100px]" title="{{ str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}">
                                        {{ str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}
                                    </span>
                                    @if(!empty($quan->shopeefood_url))
                                        <span class="bg-orange-50 text-[#EE4D2D] border border-orange-200/60 px-1.5 py-0.5 rounded text-[10.5px] font-bold whitespace-nowrap flex items-center gap-0.5" title="Có đặt trên ShopeeFood">
                                            <span class="material-symbols-outlined text-[12px]">moped</span>
                                        </span>
                                    @endif
                                </div>
                                <span class="text-primary font-bold text-[12.5px] sm:text-[13px] whitespace-nowrap flex-shrink-0 ml-auto">
                                    {{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12 flex justify-center">
                {{ $quans->withQueryString()->links() }}
            </div>
        @endif

    </div>
</main>
@endsection
