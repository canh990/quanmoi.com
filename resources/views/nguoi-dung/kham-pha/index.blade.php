@extends('layouts.app')

@php
    $danhMuc      = $danhMuc ?? request('danh_muc', '');
    $tuKhoa       = $tuKhoa ?? request('tu_khoa', '');
    $tinhThanhId  = $tinhThanhId ?? request('tinh_thanh_id', '');
    $quanHuyenId  = $quanHuyenId ?? request('quan_huyen_id', '');
    $mucGia       = $mucGia ?? request('muc_gia', '');
    $sapXep       = $sapXep ?? request('sap_xep', '');
    $lat          = $lat ?? request('lat', '');
    $lon          = $lon ?? request('lon', request('lng', ''));
    $banKinh      = $banKinh ?? request('ban_kinh', '');
    $isXacThuc    = $isXacThuc ?? request()->boolean('is_xac_thuc');
    $isNoiBat     = $isNoiBat ?? ($danhMuc === 'Quán Nổi Bật' || request()->boolean('is_noi_bat'));
    $dangMoCua    = $dangMoCua ?? request()->boolean('dang_mo_cua');
    $coShopee     = $coShopee ?? request()->boolean('co_shopeefood');
    $coVideo      = $coVideo ?? request()->boolean('co_video');
    $tenTinhThanh = $tenTinhThanh ?? '';
    $quansGoiY    = $quansGoiY ?? collect();
    $monAns       = $monAns ?? collect();

    $hasGps = !empty($lat) && !empty($lon);

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
        || !empty($coShopee)
        || !empty($coVideo)
        || ($hasGps && ($sapXep === 'near_me' || !empty($banKinh)));

    $isNearMeActive = ($sapXep === 'near_me' && $hasGps);
    $isAllActive = !$hasActiveFilters && empty($danhMuc);
    $isDangMoCuaActive = !empty($dangMoCua);
    $isPriceActive = !empty($mucGia);
    $activePriceShortLabel = match($mucGia) {
        'duoi_50k'  => 'Dưới 50k',
        '50k_150k'  => '50k - 150k',
        '150k_300k' => '150k - 300k',
        'tren_300k' => 'Trên 300k',
        default     => 'Khoảng giá',
    };
    $isXacThucActive = !empty($isXacThuc);
    $isShopeeActive = !empty($coShopee);
    $isVideoActive = !empty($coVideo);
    $isCategoryActive = !empty($danhMuc) && !$isSpecialPage;
    $activeCategoryLabel = $isCategoryActive ? $danhMuc : 'Danh mục quán';
    $danhMucList = [
        ['ten' => 'Lẩu & Nướng', 'icon' => 'skillet', 'desc' => 'Buffet nướng, lẩu thái, nướng than'],
        ['ten' => 'Cà phê & Trà', 'icon' => 'local_cafe', 'desc' => 'Cafe chill, trà sữa, acoustic'],
        ['ten' => 'Ăn vặt', 'icon' => 'fastfood', 'desc' => 'Bánh tráng, xiên que, trà chanh'],
        ['ten' => 'Nhà hàng', 'icon' => 'dinner_dining', 'desc' => 'Gia đình, sang trọng, tiệc tùng'],
        ['ten' => 'Quán Đêm 24/7', 'icon' => 'nightlife', 'desc' => 'Mở xuyên đêm, ăn khuya'],
        ['ten' => 'Billiards & Giải trí', 'icon' => 'sports_esports', 'desc' => 'Bida, board game, giải trí'],
    ];
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
                       class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-full text-[13px] sm:text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isAllActive ? 'bg-primary text-white border-primary shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-primary hover:bg-white border-gray-200/90 hover:border-primary/40 font-semibold' }}">
                        <span class="material-symbols-outlined text-[17px]">apps</span>
                        <span>Tất cả</span>
                    </a>

                    {{-- [Gần tôi 📍 (GPS)] --}}
                    @if($isNearMeActive)
                        <a href="{{ request()->fullUrlWithQuery(['sap_xep' => null, 'lat' => null, 'lon' => null, 'lng' => null, 'ban_kinh' => null]) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-full text-[13px] sm:text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-sm bg-rose-600 text-white border-rose-600 font-bold"
                           title="Đang tìm gần bạn. Nhấp để bỏ lọc.">
                            <span class="material-symbols-outlined text-[17px] animate-pulse">my_location</span>
                            <span>Gần tôi{{ !empty($banKinh) ? " (< {$banKinh}km)" : '' }}</span>
                            <span class="material-symbols-outlined text-[15px]">close</span>
                        </a>
                    @else
                        <button type="button" 
                                onclick="triggerNearMe()" 
                                class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-full text-[13px] sm:text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs bg-white/95 text-gray-700 hover:text-rose-600 hover:bg-white border-gray-200/90 hover:border-rose-300 font-semibold">
                            <span class="material-symbols-outlined text-[17px] text-rose-500">near_me</span>
                            <span>Gần tôi</span>
                        </button>
                    @endif

                    {{-- [Đang mở cửa 🟢] --}}
                    <a href="{{ $isDangMoCuaActive ? request()->fullUrlWithQuery(['dang_mo_cua' => null]) : request()->fullUrlWithQuery(['dang_mo_cua' => 1]) }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-full text-[13px] sm:text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isDangMoCuaActive ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-emerald-700 hover:bg-white border-gray-200/90 hover:border-emerald-300 font-semibold' }}">
                        <span class="w-2.5 h-2.5 rounded-full {{ $isDangMoCuaActive ? 'bg-white' : 'bg-emerald-500 animate-pulse' }}"></span>
                        <span>Đang mở cửa</span>
                    </a>

                    {{-- [Khoảng giá ▾] Dropdown --}}
                    @if($isPriceActive)
                        <div class="inline-flex items-center rounded-full bg-primary text-white border border-primary shadow-sm text-[13px] sm:text-[13.5px] font-bold">
                            <button type="button" 
                                    id="price-dropdown-btn"
                                    onclick="togglePriceDropdown(event)"
                                    class="inline-flex items-center gap-1.5 pl-3.5 sm:pl-4 pr-1 py-2 whitespace-nowrap cursor-pointer hover:brightness-105 transition-all">
                                <span class="material-symbols-outlined text-[17px]">payments</span>
                                <span>{{ $activePriceShortLabel }}</span>
                                <span class="material-symbols-outlined text-[17px]">arrow_drop_down</span>
                            </button>
                            <a href="{{ request()->fullUrlWithQuery(['muc_gia' => null]) }}" 
                               class="pr-3 pl-1 py-2 hover:opacity-80 transition-opacity flex items-center" 
                               title="Bỏ lọc mức giá">
                                <span class="material-symbols-outlined text-[15px]">close</span>
                            </a>
                        </div>
                    @else
                        <button type="button" 
                                id="price-dropdown-btn"
                                onclick="togglePriceDropdown(event)"
                                class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-full text-[13px] sm:text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs bg-white/95 text-gray-700 hover:text-amber-800 hover:bg-white border-gray-200/90 hover:border-amber-300 font-semibold">
                            <span class="material-symbols-outlined text-[17px] text-amber-600">payments</span>
                            <span>Khoảng giá</span>
                            <span class="material-symbols-outlined text-[18px] text-gray-400">arrow_drop_down</span>
                        </button>
                    @endif

                    {{-- [Danh mục quán ▾] (Gộp Lẩu Nướng, Cà phê & Trà, Ăn vặt, Nhà hàng...) --}}
                    @if($isCategoryActive)
                        <div class="inline-flex items-center rounded-full bg-primary text-white border border-primary shadow-sm text-[13px] sm:text-[13.5px] font-bold">
                            <button type="button" 
                                    id="category-dropdown-btn"
                                    onclick="toggleCategoryDropdown(event)"
                                    class="inline-flex items-center gap-1.5 pl-3.5 sm:pl-4 pr-1 py-2 whitespace-nowrap cursor-pointer hover:brightness-105 transition-all">
                                <span class="material-symbols-outlined text-[17px]">restaurant_menu</span>
                                <span>{{ $activeCategoryLabel }}</span>
                                <span class="material-symbols-outlined text-[17px]">arrow_drop_down</span>
                            </button>
                            <a href="{{ request()->fullUrlWithQuery(['danh_muc' => null]) }}" 
                               class="pr-3 pl-1 py-2 hover:opacity-80 transition-opacity flex items-center" 
                               title="Bỏ lọc danh mục">
                                <span class="material-symbols-outlined text-[15px]">close</span>
                            </a>
                        </div>
                    @else
                        <button type="button" 
                                id="category-dropdown-btn"
                                onclick="toggleCategoryDropdown(event)"
                                class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-full text-[13px] sm:text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs bg-white/95 text-gray-700 hover:text-primary hover:bg-white border-gray-200/90 hover:border-primary/40 font-semibold">
                            <span class="material-symbols-outlined text-[17px] text-amber-700">restaurant_menu</span>
                            <span>Danh mục quán</span>
                            <span class="material-symbols-outlined text-[18px] text-gray-400">arrow_drop_down</span>
                        </button>
                    @endif

                    {{-- [Video review 🎬] --}}
                    <a href="{{ $isVideoActive ? request()->fullUrlWithQuery(['co_video' => null]) : request()->fullUrlWithQuery(['co_video' => 1]) }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-full text-[13px] sm:text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isVideoActive ? 'bg-purple-600 text-white border-purple-600 shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-purple-600 hover:bg-white border-gray-200/90 hover:border-purple-300 font-semibold' }}">
                        <span class="material-symbols-outlined text-[17px] {{ $isVideoActive ? 'text-white' : 'text-purple-600' }}">play_circle</span>
                        <span>Video review</span>
                    </a>

                    {{-- [Tích xanh ✅] --}}
                    <a href="{{ $isXacThucActive ? request()->fullUrlWithQuery(['is_xac_thuc' => null]) : request()->fullUrlWithQuery(['is_xac_thuc' => 1]) }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-full text-[13px] sm:text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isXacThucActive ? 'bg-sky-600 text-white border-sky-600 shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-sky-600 hover:bg-white border-gray-200/90 hover:border-sky-300 font-semibold' }}">
                        <span class="material-symbols-outlined text-[17px] {{ $isXacThucActive ? 'text-white' : 'text-sky-500' }}" style="font-variation-settings: 'FILL' 1;">verified</span>
                        <span>Tích xanh</span>
                    </a>

                    {{-- [ShopeeFood] --}}
                    <a href="{{ $isShopeeActive ? request()->fullUrlWithQuery(['co_shopeefood' => null]) : request()->fullUrlWithQuery(['co_shopeefood' => 1]) }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-full text-[13px] sm:text-[13.5px] whitespace-nowrap transition-all duration-200 cursor-pointer border select-none active:scale-95 shadow-2xs {{ $isShopeeActive ? 'bg-[#EE4D2D] text-white border-[#EE4D2D] shadow-sm font-bold' : 'bg-white/95 text-gray-700 hover:text-[#EE4D2D] hover:bg-white border-gray-200/90 hover:border-orange-300 font-semibold' }}">
                        <span class="material-symbols-outlined text-[17px] {{ $isShopeeActive ? 'text-white' : 'text-[#EE4D2D]' }}">moped</span>
                        <span>ShopeeFood</span>
                    </a>
                </div>

                {{-- Popover Dropdown Mức giá (Nằm ngoài overflow-x-auto) --}}
                <div id="price-dropdown-menu" class="hidden fixed z-[99999] w-64 bg-white rounded-2xl shadow-[0_16px_50px_rgba(0,0,0,0.2)] border border-gray-100 p-2 text-left">
                    <div class="px-3 py-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Khoảng giá</span>
                        @if($isPriceActive)
                            <a href="{{ request()->fullUrlWithQuery(['muc_gia' => null]) }}" class="text-[11px] text-red-500 hover:underline lowercase font-semibold">xoá lọc</a>
                        @endif
                    </div>
                    <a href="{{ request()->fullUrlWithQuery(['muc_gia' => null]) }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] transition-colors {{ empty($mucGia) ? 'bg-amber-50 text-amber-900 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                        <span>Tất cả mức giá</span>
                        @if(empty($mucGia))
                            <span class="material-symbols-outlined text-[16px] text-primary">check</span>
                        @endif
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['muc_gia' => 'duoi_50k']) }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] transition-colors {{ $mucGia === 'duoi_50k' ? 'bg-amber-50 text-amber-900 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                        <div>
                            <div class="font-bold">Bình dân</div>
                            <div class="text-[11.5px] text-gray-400">&lt; 50.000đ</div>
                        </div>
                        @if($mucGia === 'duoi_50k')
                            <span class="material-symbols-outlined text-[16px] text-primary">check</span>
                        @endif
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['muc_gia' => '50k_150k']) }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] transition-colors {{ $mucGia === '50k_150k' ? 'bg-amber-50 text-amber-900 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                        <div>
                            <div class="font-bold">Tầm trung</div>
                            <div class="text-[11.5px] text-gray-400">50.000đ - 150.000đ</div>
                        </div>
                        @if($mucGia === '50k_150k')
                            <span class="material-symbols-outlined text-[16px] text-primary">check</span>
                        @endif
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['muc_gia' => '150k_300k']) }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] transition-colors {{ $mucGia === '150k_300k' ? 'bg-amber-50 text-amber-900 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                        <div>
                            <div class="font-bold">Tiệc tùng / Nướng lẩu</div>
                            <div class="text-[11.5px] text-gray-400">150.000đ - 300.000đ</div>
                        </div>
                        @if($mucGia === '150k_300k')
                            <span class="material-symbols-outlined text-[16px] text-primary">check</span>
                        @endif
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['muc_gia' => 'tren_300k']) }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] transition-colors {{ $mucGia === 'tren_300k' ? 'bg-amber-50 text-amber-900 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                        <div>
                            <div class="font-bold">Cao cấp</div>
                            <div class="text-[11.5px] text-gray-400">&gt; 300.000đ</div>
                        </div>
                        @if($mucGia === 'tren_300k')
                            <span class="material-symbols-outlined text-[16px] text-primary">check</span>
                        @endif
                    </a>
                </div>

                {{-- Popover Dropdown Danh mục quán (Nằm ngoài overflow-x-auto) --}}
                <div id="category-dropdown-menu" class="hidden fixed z-[99999] w-72 bg-white rounded-2xl shadow-[0_16px_50px_rgba(0,0,0,0.2)] border border-gray-100 p-2 text-left">
                    <div class="px-3 py-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider flex items-center justify-between">
                        <span>Danh mục quán</span>
                        @if($isCategoryActive)
                            <a href="{{ request()->fullUrlWithQuery(['danh_muc' => null]) }}" class="text-[11px] text-red-500 hover:underline lowercase font-semibold">xoá lọc</a>
                        @endif
                    </div>
                    <a href="{{ request()->fullUrlWithQuery(['danh_muc' => null]) }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] transition-colors {{ empty($danhMuc) ? 'bg-amber-50 text-amber-900 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[18px] text-gray-400">apps</span>
                            <span class="font-medium">Tất cả danh mục</span>
                        </div>
                        @if(empty($danhMuc))
                            <span class="material-symbols-outlined text-[16px] text-primary">check</span>
                        @endif
                    </a>
                    @foreach($danhMucList as $dm)
                        @php 
                            $isSelected = ($danhMuc === $dm['ten']) || ($dm['ten'] === 'Cà phê & Trà' && in_array($danhMuc, ['Cà phê', 'Cà phê & Trà']));
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['danh_muc' => $dm['ten']]) }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-[13px] transition-colors {{ $isSelected ? 'bg-amber-50 text-amber-900 font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="material-symbols-outlined text-[19px] {{ $isSelected ? 'text-primary' : 'text-gray-500' }}">{{ $dm['icon'] }}</span>
                                <div class="truncate">
                                    <div class="font-bold truncate">{{ $dm['ten'] }}</div>
                                    <div class="text-[11px] text-gray-400 truncate">{{ $dm['desc'] }}</div>
                                </div>
                            </div>
                            @if($isSelected)
                                <span class="material-symbols-outlined text-[16px] text-primary flex-shrink-0 ml-1">check</span>
                            @endif
                        </a>
                    @endforeach
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

                {{-- Vị trí GPS & Bán kính --}}
                @if($hasGps && ($sapXep === 'near_me' || !empty($banKinh)))
                    <div class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-800 border border-rose-200 rounded-full pl-3 pr-1.5 py-1 text-[13px] font-bold">
                        <span class="material-symbols-outlined text-[16px] text-rose-600 animate-pulse">my_location</span>
                        <span>Gần bạn</span>
                        <div class="flex items-center gap-1 ml-1 bg-white rounded-full px-1.5 py-0.5 border border-rose-200 text-[11.5px]">
                            <a href="{{ request()->fullUrlWithQuery(['ban_kinh' => 3]) }}" class="px-1.5 py-0.5 rounded-full transition-all {{ $banKinh == 3 ? 'bg-rose-600 text-white font-extrabold' : 'text-rose-700 hover:bg-rose-100' }}">&lt; 3km</a>
                            <a href="{{ request()->fullUrlWithQuery(['ban_kinh' => 5]) }}" class="px-1.5 py-0.5 rounded-full transition-all {{ $banKinh == 5 ? 'bg-rose-600 text-white font-extrabold' : 'text-rose-700 hover:bg-rose-100' }}">&lt; 5km</a>
                            <a href="{{ request()->fullUrlWithQuery(['ban_kinh' => 10]) }}" class="px-1.5 py-0.5 rounded-full transition-all {{ $banKinh == 10 ? 'bg-rose-600 text-white font-extrabold' : 'text-rose-700 hover:bg-rose-100' }}">&lt; 10km</a>
                            <a href="{{ request()->fullUrlWithQuery(['ban_kinh' => null]) }}" class="px-1.5 py-0.5 rounded-full transition-all {{ empty($banKinh) ? 'bg-rose-600 text-white font-extrabold' : 'text-rose-700 hover:bg-rose-100' }}">Tất cả</a>
                        </div>
                        <a href="{{ request()->fullUrlWithQuery(['sap_xep' => null, 'lat' => null, 'lon' => null, 'lng' => null, 'ban_kinh' => null]) }}" class="ml-1 text-rose-500 hover:text-rose-700 hover:rotate-90 transition-transform p-0.5" title="Bỏ lọc vị trí">
                            <span class="material-symbols-outlined text-[15px]">close</span>
                        </a>
                    </div>
                @endif

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
                @if(!empty($mucGia))
                    @php
                        $mucGiaLabel = match($mucGia) {
                            'duoi_50k'  => 'Bình dân (< 50k)',
                            '50k_150k'  => 'Tầm trung (50k - 150k)',
                            '150k_300k' => 'Nướng lẩu / Tiệc (150k - 300k)',
                            'tren_300k' => 'Cao cấp (> 300k)',
                            default     => $mucGia,
                        };
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['muc_gia' => null]) }}" 
                       class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-full px-3 py-1 text-[13px] font-bold transition-all group"
                       title="Bỏ lọc mức giá">
                        <span class="material-symbols-outlined text-[15px]">payments</span>
                        <span>{{ $mucGiaLabel }}</span>
                        <span class="material-symbols-outlined text-[15px] group-hover:rotate-90 transition-transform">close</span>
                    </a>
                @endif

                {{-- Video review --}}
                @if(!empty($coVideo))
                    <a href="{{ request()->fullUrlWithQuery(['co_video' => null]) }}" 
                       class="inline-flex items-center gap-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 rounded-full px-3 py-1 text-[13px] font-bold transition-all group"
                       title="Bỏ lọc video review">
                        <span class="material-symbols-outlined text-[15px]">play_circle</span>
                        <span>Có Video Review 🎬</span>
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
                <select id="sap_xep" class="border border-gray-200 rounded-xl px-3 py-2 text-[14px] bg-white outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 text-gray-700 font-medium cursor-pointer transition-all" onchange="handleSortSelectChange(this)">
                    <option value="" {{ empty($sapXep) ? 'selected' : '' }}>Độ liên quan / Mặc định</option>
                    <option value="near_me" {{ ($sapXep ?? '') === 'near_me' ? 'selected' : '' }}>Gần tôi nhất (GPS)</option>
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
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $quan->ten_quan }}"/>
                            
                            {{-- Rating badge & Distance badge --}}
                            <div class="absolute top-3 left-3 flex items-center gap-1.5 z-10">
                                <div class="bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-xl text-primary font-bold text-xs flex items-center gap-1 shadow-sm">
                                    <span class="material-symbols-outlined text-[15px] text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span> 4.9
                                </div>
                                @if(isset($quan->khoang_cach_km) && $quan->khoang_cach_km !== null)
                                    <div class="bg-primary/95 text-white backdrop-blur-md px-2.5 py-1 rounded-xl font-bold text-xs flex items-center gap-1 shadow-sm" title="Cách vị trí của bạn {{ $quan->khoang_cach_km }} km">
                                        <span class="material-symbols-outlined text-[14px]">near_me</span>
                                        {{ $quan->khoang_cach_km }} km
                                    </div>
                                @endif
                            </div>

                            {{-- Badges: Video Review, Open, Hot --}}
                            <div class="absolute top-3 right-3 flex items-center gap-1 z-10">
                                @php
                                    $hasVideo = $quan->co_video_review ?? ($quan->relationLoaded('videos') ? $quan->videos->isNotEmpty() : (method_exists($quan, 'videos') && $quan->videos()->exists()));
                                @endphp
                                @if($hasVideo)
                                    <span class="bg-purple-600/95 backdrop-blur-md text-white px-2 py-0.5 rounded-lg text-[10.5px] font-bold flex items-center gap-0.5 shadow-sm" title="Có video review">
                                        <span class="material-symbols-outlined text-[13px]">play_circle</span>
                                        Video
                                    </span>
                                @endif
                                @if(method_exists($quan, 'isDangMoCua') && $quan->isDangMoCua())
                                    <span class="bg-emerald-600/90 backdrop-blur-md text-white px-2 py-0.5 rounded-lg text-[10.5px] font-bold flex items-center gap-1 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        Mở cửa
                                    </span>
                                @endif
                                @if($quan->is_noi_bat)
                                    <span class="bg-amber-500 text-white font-bold text-[10.5px] px-2 py-0.5 rounded-lg shadow-sm flex items-center gap-0.5" title="Quán nổi bật">
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

@push('scripts')
<script>
    function triggerNearMe(radius) {
        if (!navigator.geolocation) {
            alert('Trình duyệt của bạn không hỗ trợ định vị GPS');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const url = new URL(window.location.href);
                url.searchParams.set('sap_xep', 'near_me');
                url.searchParams.set('lat', pos.coords.latitude);
                url.searchParams.set('lon', pos.coords.longitude);
                if (radius) {
                    url.searchParams.set('ban_kinh', radius);
                }
                window.location.href = url.toString();
            },
            function(err) {
                alert('Vui lòng bật quyền truy cập vị trí trên trình duyệt hoặc thiết bị để tìm quán gần bạn');
            },
            { timeout: 10000, enableHighAccuracy: true }
        );
    }

    function handleSortSelectChange(select) {
        if (select.value === 'near_me') {
            triggerNearMe();
            return;
        }
        const url = new URL(window.location.href);
        url.searchParams.set('sap_xep', select.value);
        window.location.href = url.toString();
    }

    function togglePriceDropdown(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const btn = (e && (e.currentTarget || e.target.closest('button'))) || document.getElementById('price-dropdown-btn');
        const menu = document.getElementById('price-dropdown-menu');
        const catMenu = document.getElementById('category-dropdown-menu');
        
        if (catMenu) catMenu.classList.add('hidden');
        if (!menu || !btn) return;

        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            const rect = btn.getBoundingClientRect();
            const menuWidth = 260;
            let left = rect.left;
            if (left + menuWidth > window.innerWidth - 16) {
                left = window.innerWidth - menuWidth - 16;
            }
            if (left < 16) left = 16;

            menu.style.top = (rect.bottom + 8) + 'px';
            menu.style.left = left + 'px';
            menu.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
        }
    }

    function toggleCategoryDropdown(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const btn = (e && (e.currentTarget || e.target.closest('button'))) || document.getElementById('category-dropdown-btn');
        const menu = document.getElementById('category-dropdown-menu');
        const priceMenu = document.getElementById('price-dropdown-menu');
        
        if (priceMenu) priceMenu.classList.add('hidden');
        if (!menu || !btn) return;

        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            const rect = btn.getBoundingClientRect();
            const menuWidth = 288;
            let left = rect.left;
            if (left + menuWidth > window.innerWidth - 16) {
                left = window.innerWidth - menuWidth - 16;
            }
            if (left < 16) left = 16;

            menu.style.top = (rect.bottom + 8) + 'px';
            menu.style.left = left + 'px';
            menu.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
        }
    }

    document.addEventListener('click', function(e) {
        const priceMenu = document.getElementById('price-dropdown-menu');
        const priceBtn = document.getElementById('price-dropdown-btn');
        if (priceMenu && !priceMenu.classList.contains('hidden')) {
            if (!priceMenu.contains(e.target) && (!priceBtn || !priceBtn.contains(e.target))) {
                priceMenu.classList.add('hidden');
            }
        }

        const catMenu = document.getElementById('category-dropdown-menu');
        const catBtn = document.getElementById('category-dropdown-btn');
        if (catMenu && !catMenu.classList.contains('hidden')) {
            if (!catMenu.contains(e.target) && (!catBtn || !catBtn.contains(e.target))) {
                catMenu.classList.add('hidden');
            }
        }
    });

    window.addEventListener('resize', function() {
        const priceMenu = document.getElementById('price-dropdown-menu');
        const catMenu = document.getElementById('category-dropdown-menu');
        if (priceMenu) priceMenu.classList.add('hidden');
        if (catMenu) catMenu.classList.add('hidden');
    });

    window.addEventListener('scroll', function() {
        const priceMenu = document.getElementById('price-dropdown-menu');
        const catMenu = document.getElementById('category-dropdown-menu');
        if (priceMenu && !priceMenu.classList.contains('hidden')) priceMenu.classList.add('hidden');
        if (catMenu && !catMenu.classList.contains('hidden')) catMenu.classList.add('hidden');
    }, { passive: true });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const priceMenu = document.getElementById('price-dropdown-menu');
            const catMenu = document.getElementById('category-dropdown-menu');
            if (priceMenu) priceMenu.classList.add('hidden');
            if (catMenu) catMenu.classList.add('hidden');
        }
    });
</script>
@endpush
