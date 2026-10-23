@extends('layouts.app')

@section('title', 'Quán đã lưu | Quán Mới')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    /* Scoped styling cho trang Quán đã lưu */
    .saved-page-wrap {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 16px 64px;
        font-family: 'Be Vietnam Pro', ui-sans-serif, system-ui, sans-serif;
    }

    .saved-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border-radius: 9999px;
        font-size: 12.5px;
        font-weight: 700;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #4b5563;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.18s ease;
    }

    .saved-filter-pill:hover {
        background: #f9fafb;
        color: #111827;
        border-color: #d1d5db;
    }

    .saved-filter-pill.is-active {
        background: #ea580c;
        color: #ffffff;
        border-color: #ea580c;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.25);
    }

    .saved-view-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #374151;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .saved-view-toggle-btn.is-active {
        background: #111827;
        color: #ffffff;
        border-color: #111827;
    }

    .saved-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .saved-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        border-color: #fed7aa;
    }

    .saved-card-thumb-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 10;
        background: #f1f5f9;
        overflow: hidden;
    }

    .saved-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .saved-card:hover .saved-card-img {
        transform: scale(1.05);
    }

    .saved-heart-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 38px;
        height: 38px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ef4444;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        z-index: 20;
        transition: transform 0.18s ease, background 0.18s ease;
    }

    .saved-heart-btn:hover {
        transform: scale(1.1);
        background: #ffffff;
    }

    .saved-heart-btn:active {
        transform: scale(0.9);
    }

    /* Leaflet map of saved places */
    #saved-places-map-container {
        width: 100%;
        height: 600px;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
        background: #e2e8f0;
    }

    .qm-saved-marker-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 9999px;
        background: #ea580c;
        color: #ffffff;
        font-weight: 800;
        font-size: 11.5px;
        white-space: nowrap;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.4);
        cursor: pointer;
        transition: transform 0.18s ease, background 0.18s ease;
    }

    .qm-saved-marker-badge:hover {
        background: #be123c;
        transform: scale(1.15) translateY(-2px);
    }

    .leaflet-popup-content-wrapper {
        border-radius: 16px !important;
        padding: 0 !important;
        overflow: hidden;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.2) !important;
    }

    .leaflet-popup-content {
        margin: 0 !important;
        line-height: inherit;
    }
</style>
@endpush

@section('content')
<main class="saved-page-wrap">
    <div class="w-full">
        {{-- HEADER: TIÊU ĐỀ + CHẾ ĐỘ XEM (LƯỚI / BẢN ĐỒ) --}}
        <header class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 pb-4 border-b border-gray-100">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">Bộ sưu tập cá nhân</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Quán đã lưu</h1>
                <p class="text-sm text-gray-500 mt-1">Khám phá lại những địa điểm yêu thích của bạn.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Toggle Chế độ xem: Lưới (Grid) vs Bản đồ (Map) --}}
                <div class="inline-flex p-1 bg-gray-100 rounded-xl border border-gray-200">
                    <button type="button" 
                            id="saved-view-btn-grid" 
                            onclick="switchSavedView('grid')" 
                            class="saved-view-toggle-btn is-active" 
                            title="Chế độ xem dạng lưới">
                        <span class="material-symbols-outlined text-[17px]">grid_view</span>
                        <span>Danh sách (<span id="saved-total-count">{{ $quanDaLuu->count() }}</span>)</span>
                    </button>
                    <button type="button" 
                            id="saved-view-btn-map" 
                            onclick="switchSavedView('map')" 
                            class="saved-view-toggle-btn" 
                            title="Xem tất cả quán trên bản đồ">
                        <span class="material-symbols-outlined text-[17px]">map</span>
                        <span>Bản đồ</span>
                    </button>
                </div>

                {{-- Nút chuyển sang trang Bản đồ lân cận --}}
                <a href="{{ route('ban-do') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary/10 hover:bg-primary/20 text-primary font-bold text-xs rounded-xl transition-all" title="Khám phá quán quanh bạn theo GPS">
                    <span class="material-symbols-outlined text-[17px]">near_me</span>
                    <span>Quét quán gần bạn</span>
                </a>
            </div>
        </header>

        @if($quanDaLuu->count() > 0)
        {{-- THANH TÌM KIẾM & BỘ LỌC THÔNG MINH --}}
        <div class="mb-6 space-y-3" id="saved-filter-toolbar">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                {{-- Ô tìm kiếm tức thì theo tên/địa chỉ quán đã lưu --}}
                <div class="relative flex-1 max-w-md">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[19px]">search</span>
                    <input type="text" 
                           id="saved-search-input" 
                           oninput="filterSavedPlaces()" 
                           placeholder="Tìm trong danh sách đã lưu..." 
                           class="w-full h-10 pl-10 pr-9 rounded-xl border border-gray-200 bg-white text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                    <button type="button" id="saved-search-clear" onclick="clearSavedSearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>

                {{-- Bộ lọc mở cửa & Sắp xếp --}}
                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <button type="button" 
                            id="saved-filter-open-btn" 
                            onclick="toggleFilterOpenNow()" 
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-[15px]">schedule</span>
                        <span>Đang mở cửa</span>
                    </button>

                    <select id="saved-sort-select" onchange="sortSavedPlaces(this.value)" class="h-10 px-3 rounded-xl border border-gray-200 bg-white text-xs font-bold text-gray-700 focus:outline-none focus:border-primary">
                        <option value="newest">Mới lưu nhất</option>
                        <option value="rating_desc">Đánh giá cao nhất</option>
                        <option value="price_asc">Giá: Thấp đến cao</option>
                        <option value="name_asc">Tên quán (A - Z)</option>
                    </select>
                </div>
            </div>

            {{-- Danh mục ẩm thực thực tế từ các quán người dùng đã lưu --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar pt-1">
                <button type="button" 
                        onclick="filterByCategory('')" 
                        data-category="" 
                        class="saved-filter-pill is-active saved-cat-btn">
                    Tất cả ({{ $quanDaLuu->count() }})
                </button>
                @foreach($categories as $cat)
                    @php
                        $catCount = $quanDaLuu->where('loai_hinh_kinh_doanh', $cat)->count();
                    @endphp
                    <button type="button" 
                            onclick="filterByCategory('{{ addslashes($cat) }}')" 
                            data-category="{{ $cat }}" 
                            class="saved-filter-pill saved-cat-btn">
                        <span>{{ $cat }}</span>
                        <span class="text-[11px] opacity-70">({{ $catCount }})</span>
                    </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- 1. CHẾ ĐỘ XEM DẠNG LƯỚI (GRID VIEW) --}}
        <div id="saved-view-grid-wrap">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6" id="saved-places-grid-desktop">
                @forelse($quanDaLuu as $quan)
                @php
                    $primaryImg = $quan->hinhAnh->first()->url ?? ($quan->anh_bia_url ?? 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80');
                    $isOpen = method_exists($quan, 'isDangMoCua') ? $quan->isDangMoCua() : true;
                    $avgRating = round((float)($quan->danh_gia_avg_so_sao ?? 0), 1);
                    $ratingCount = (int)($quan->danh_gia_count ?? 0);
                    $priceText = ($quan->gia_nho_nhat && $quan->gia_lon_nhat)
                        ? number_format($quan->gia_nho_nhat, 0, ',', '.') . 'đ - ' . number_format($quan->gia_lon_nhat, 0, ',', '.') . 'đ'
                        : ($quan->gia_nho_nhat ? 'Từ ' . number_format($quan->gia_nho_nhat, 0, ',', '.') . 'đ' : 'Đang cập nhật giá');
                    
                    $addressFull = trim(implode(', ', array_filter([
                        $quan->dia_chi_chi_tiet,
                        $quan->ten_phuong_xa,
                        $quan->ten_quan_huyen,
                        $quan->ten_tinh_thanh
                    ])));

                    $directionsUrl = ($quan->vi_do && $quan->kinh_do)
                        ? 'https://www.google.com/maps/dir/?api=1&destination=' . $quan->vi_do . ',' . $quan->kinh_do
                        : 'https://maps.google.com/?q=' . urlencode($quan->ten_quan . ', ' . $addressFull);
                @endphp
                <article id="quan-card-desktop-{{ $quan->id }}" 
                         data-id="{{ $quan->id }}"
                         data-name="{{ Str::lower($quan->ten_quan) }}"
                         data-address="{{ Str::lower($addressFull) }}"
                         data-category="{{ $quan->loai_hinh_kinh_doanh ?? '' }}"
                         data-is-open="{{ $isOpen ? '1' : '0' }}"
                         data-rating="{{ $avgRating }}"
                         data-price="{{ (int)($quan->gia_nho_nhat ?? 0) }}"
                         data-lat="{{ $quan->vi_do }}"
                         data-lng="{{ $quan->kinh_do }}"
                         class="saved-card saved-item-element group">
                    
                    {{-- Hình ảnh thumbnail --}}
                    <div class="saved-card-thumb-wrap">
                        <img class="saved-card-img" src="{{ $primaryImg }}" alt="{{ $quan->ten_quan }}" loading="lazy" />
                        
                        {{-- Nút Trái tim thả/bỏ lưu --}}
                        <button type="button" 
                                onclick="toggleSave(this, '{{ $quan->id }}')" 
                                class="saved-heart-btn" 
                                title="Bỏ lưu quán">
                            <span class="material-symbols-outlined text-[21px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                        </button>

                        {{-- Link phủ card --}}
                        <a href="{{ route('quan.detail', $quan->slug) }}" class="absolute inset-0 z-10" aria-label="Xem chi tiết {{ $quan->ten_quan }}"></a>

                        {{-- Badge Trạng thái mở cửa --}}
                        <div class="absolute top-3 left-3 z-10 pointer-events-none">
                            @if($isOpen)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-600/90 backdrop-blur-md text-white text-[11px] font-extrabold shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    Đang mở cửa
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-900/80 backdrop-blur-md text-white text-[11px] font-extrabold shadow-sm">
                                    Tạm đóng cửa
                                </span>
                            @endif
                        </div>

                        {{-- Badge Đánh giá sao thực tế --}}
                        <div class="absolute bottom-3 left-3 px-2.5 py-1 bg-black/70 backdrop-blur-md text-white rounded-lg text-xs font-bold flex items-center gap-1 z-10 pointer-events-none">
                            <span class="material-symbols-outlined text-[15px] text-amber-400" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span>{{ $avgRating > 0 ? $avgRating : 'Mới' }}</span>
                            @if($ratingCount > 0)
                                <span class="text-white/70 font-normal">({{ $ratingCount }})</span>
                            @endif
                        </div>
                    </div>

                    {{-- Nội dung thông tin quán --}}
                    <div class="p-4 flex flex-col flex-1 justify-between gap-3">
                        <div>
                            {{-- Danh mục & Loại hình --}}
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-[11px] font-extrabold text-primary uppercase tracking-wider">
                                    {{ $quan->loai_hinh_kinh_doanh ?: 'Quán ăn' }}
                                </span>
                                @if($quan->is_xac_thuc)
                                    <span class="inline-flex items-center gap-0.5 text-blue-600 text-[11px] font-bold" title="Quán đã xác thực">
                                        <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                                        Đã xác thực
                                    </span>
                                @endif
                            </div>

                            {{-- Tên quán --}}
                            <h3 class="text-base font-bold text-gray-900 group-hover:text-primary transition-colors line-clamp-1 mb-1.5">
                                <a href="{{ route('quan.detail', $quan->slug) }}" class="hover:underline">
                                    {{ $quan->ten_quan }}
                                </a>
                            </h3>

                            {{-- Địa chỉ đầy đủ --}}
                            <p class="text-xs text-gray-500 line-clamp-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px] text-gray-400 flex-shrink-0">location_on</span>
                                <span>{{ $addressFull ?: 'Đang cập nhật địa chỉ' }}</span>
                            </p>
                        </div>

                        {{-- Footer Card: Giá cả & Nút chỉ đường --}}
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                            <div class="font-extrabold text-gray-800">
                                {{ $priceText }}
                            </div>

                            <div class="flex items-center gap-3">
                                <a href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-700 font-bold inline-flex items-center gap-0.5 hover:underline" title="Chỉ đường trên Google Maps">
                                    <span class="material-symbols-outlined text-[14px]">directions</span>
                                    <span>Chỉ đường</span>
                                </a>
                                <a href="{{ route('quan.detail', $quan->slug) }}" class="text-primary hover:text-primary/80 font-extrabold inline-flex items-center">
                                    <span>Xem</span>
                                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
                @empty
                {{-- TRẠNG THÁI TRỐNG (Bắt buộc giữ chuỗi "Bạn chưa lưu địa điểm nào." để thỏa mãn test) --}}
                <div class="col-span-full py-16 px-4 text-center empty-state-desktop">
                    <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-4 shadow-xs">
                        <span class="material-symbols-outlined text-[32px]">favorite</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Bạn chưa lưu địa điểm nào.</h3>
                    <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">Hãy dạo quanh Khám phá hoặc Bản đồ để thả tim lưu lại những quán ăn bạn muốn ghé thăm.</p>
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ route('kham-pha') }}" class="px-5 py-2.5 rounded-xl bg-primary text-white font-bold text-xs shadow-sm hover:brightness-105 active:scale-95 transition-all">
                            Khám phá món ngon ngay
                        </a>
                        <a href="{{ route('ban-do') }}" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold text-xs transition-all">
                            Tìm quán quanh bạn
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Thông báo khi tìm kiếm không khớp --}}
            <div id="saved-no-search-results" class="hidden py-16 text-center">
                <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">search_off</span>
                <h4 class="font-bold text-sm text-gray-700 mb-1">Không tìm thấy quán đã lưu phù hợp</h4>
                <p class="text-xs text-gray-500">Thử tìm kiếm với từ khóa khác hoặc đặt lại bộ lọc.</p>
            </div>
        </div>

        {{-- 2. CHẾ ĐỘ XEM TRÊN BẢN ĐỒ (MAP VIEW) --}}
        <div id="saved-view-map-wrap" class="hidden">
            <div id="saved-places-map-container"></div>
            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 px-1">
                <span>📍 Hiển thị tất cả quán đã lưu có tọa độ GPS</span>
                <span id="saved-map-count-badge" class="font-bold text-primary"></span>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function() {
    // Lưu trữ dữ liệu các quán đã lưu (pre-computed từ backend an toàn và sạch sẽ)
    const savedState = {
        currentCategory: '',
        searchKeyword: '',
        filterOpenOnly: false,
        currentSort: 'newest',
        viewMode: 'grid',
        map: null,
        markersLayer: null,
        placesData: @json($placesPayload ?? [])
    };

    // Chuyển đổi giữa chế độ Lưới (Grid) và Bản đồ (Map)
    window.switchSavedView = function(mode) {
        savedState.viewMode = mode;
        const gridWrap = document.getElementById('saved-view-grid-wrap');
        const mapWrap = document.getElementById('saved-view-map-wrap');
        const btnGrid = document.getElementById('saved-view-btn-grid');
        const btnMap = document.getElementById('saved-view-btn-map');

        if (mode === 'grid') {
            gridWrap.classList.remove('hidden');
            mapWrap.classList.add('hidden');
            btnGrid.classList.add('is-active');
            btnMap.classList.remove('is-active');
        } else {
            gridWrap.classList.add('hidden');
            mapWrap.classList.remove('hidden');
            btnGrid.classList.remove('is-active');
            btnMap.classList.add('is-active');

            initSavedPlacesMap();
        }
    };

    // Khởi tạo bản đồ các quán đã lưu
    function initSavedPlacesMap() {
        if (savedState.map) {
            setTimeout(() => savedState.map.invalidateSize(), 100);
            return;
        }

        const validPlaces = savedState.placesData.filter(p => p.lat && p.lng);
        const mapCountBadge = document.getElementById('saved-map-count-badge');
        if (mapCountBadge) {
            mapCountBadge.textContent = `${validPlaces.length} / ${savedState.placesData.length} quán có vị trí GPS`;
        }

        const defaultCenter = validPlaces.length > 0
            ? [validPlaces[0].lat, validPlaces[0].lng]
            : [21.028511, 105.854200];

        savedState.map = L.map('saved-places-map-container', {
            center: defaultCenter,
            zoom: 13,
            zoomControl: true
        });

        // Sử dụng Google Maps CDN siêu tốc
        L.tileLayer('https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['0', '1', '2', '3'],
            attribution: '&copy; Google Maps',
            keepBuffer: 6,
            updateWhenIdle: false
        }).addTo(savedState.map);

        savedState.markersLayer = L.layerGroup().addTo(savedState.map);

        const bounds = [];
        validPlaces.forEach(p => {
            const shortName = p.name.length > 15 ? p.name.substring(0, 14) + '…' : p.name;
            const markerHtml = `
                <div class="qm-saved-marker-badge">
                    <span class="material-symbols-outlined" style="font-size: 13px;">favorite</span>
                    <span>${escapeHtml(shortName)}</span>
                </div>
            `;

            const icon = L.divIcon({
                className: 'custom-saved-pin-container',
                html: markerHtml,
                iconSize: [null, null],
                iconAnchor: [30, 15]
            });

            const marker = L.marker([p.lat, p.lng], { icon: icon }).addTo(savedState.markersLayer);

            const openStatusBadge = p.isOpen
                ? `<span style="background: rgba(16, 185, 129, 0.9); color: white; padding: 2px 7px; border-radius: 9999px; font-size: 10px; font-weight: 700;">Đang mở cửa</span>`
                : `<span style="background: rgba(107, 114, 128, 0.9); color: white; padding: 2px 7px; border-radius: 9999px; font-size: 10px; font-weight: 700;">Tạm đóng</span>`;

            const popupContent = `
                <div style="width: 260px; background: white; font-family: 'Be Vietnam Pro', sans-serif;">
                    <div style="position: relative; width: 100%; height: 120px; overflow: hidden;">
                        <img src="${p.image}" style="width: 100%; height: 100%; object-fit: cover;" alt="${escapeHtml(p.name)}" />
                        <div style="position: absolute; top: 8px; left: 8px;">${openStatusBadge}</div>
                    </div>
                    <div style="padding: 12px;">
                        <h4 style="font-size: 14px; font-weight: 800; color: #111827; margin: 0 0 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(p.name)}</h4>
                        <div style="font-size: 11px; color: #6b7280; margin-bottom: 6px;">${escapeHtml(p.category)} • ★ ${p.rating > 0 ? p.rating : 'Mới'}</div>
                        <div style="font-size: 11px; color: #4b5563; margin-bottom: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(p.address || 'Địa chỉ')}</div>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 8px; border-top: 1px solid #f3f4f6;">
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${p.lat},${p.lng}" target="_blank" rel="noopener noreferrer" style="font-size: 11px; font-weight: 700; color: #2563eb; text-decoration: none;">Chỉ đường ↗</a>
                            <a href="${p.url}" style="font-size: 11px; font-weight: 700; color: white; background: #ea580c; padding: 4px 10px; border-radius: 6px; text-decoration: none;">Xem quán ➔</a>
                        </div>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent, { maxWidth: 280 });
            bounds.push([p.lat, p.lng]);
        });

        if (bounds.length > 0) {
            savedState.map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
        }

        setTimeout(() => savedState.map.invalidateSize(), 200);
    }

    // Lọc theo Danh mục
    window.filterByCategory = function(category) {
        savedState.currentCategory = category;
        document.querySelectorAll('.saved-cat-btn').forEach(btn => {
            if (btn.dataset.category === category) {
                btn.classList.add('is-active');
            } else {
                btn.classList.remove('is-active');
            }
        });
        filterSavedPlaces();
    };

    // Toggle lọc đang mở cửa
    window.toggleFilterOpenNow = function() {
        savedState.filterOpenOnly = !savedState.filterOpenOnly;
        const btn = document.getElementById('saved-filter-open-btn');
        if (btn) {
            if (savedState.filterOpenOnly) {
                btn.classList.add('bg-emerald-50', 'text-emerald-700', 'border-emerald-300');
            } else {
                btn.classList.remove('bg-emerald-50', 'text-emerald-700', 'border-emerald-300');
            }
        }
        filterSavedPlaces();
    };

    // Sắp xếp
    window.sortSavedPlaces = function(sortOrder) {
        savedState.currentSort = sortOrder;
        const container = document.getElementById('saved-places-grid-desktop');
        if (!container) return;

        const cards = Array.from(container.querySelectorAll('.saved-item-element'));
        cards.sort((a, b) => {
            if (sortOrder === 'rating_desc') {
                return (parseFloat(b.dataset.rating) || 0) - (parseFloat(a.dataset.rating) || 0);
            }
            if (sortOrder === 'price_asc') {
                const priceA = parseInt(a.dataset.price) || 999999999;
                const priceB = parseInt(b.dataset.price) || 999999999;
                return priceA - priceB;
            }
            if (sortOrder === 'name_asc') {
                return (a.dataset.name || '').localeCompare(b.dataset.name || '');
            }
            // default 'newest': giữ nguyên thứ tự ban đầu của DOM
            return 0;
        });

        cards.forEach(c => container.appendChild(c));
    };

    // Lọc tìm kiếm thời gian thực
    window.filterSavedPlaces = function() {
        const input = document.getElementById('saved-search-input');
        const clearBtn = document.getElementById('saved-search-clear');
        const query = (input ? input.value : '').trim().toLowerCase();
        savedState.searchKeyword = query;

        if (clearBtn) clearBtn.classList.toggle('hidden', query.length === 0);

        const cards = document.querySelectorAll('.saved-item-element');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.dataset.name || '';
            const address = card.dataset.address || '';
            const category = card.dataset.category || '';
            const isOpen = card.dataset.isOpen === '1';

            const matchesSearch = query === '' || name.includes(query) || address.includes(query);
            const matchesCategory = savedState.currentCategory === '' || category === savedState.currentCategory;
            const matchesOpen = !savedState.filterOpenOnly || isOpen;

            if (matchesSearch && matchesCategory && matchesOpen) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noResults = document.getElementById('saved-no-search-results');
        if (noResults) {
            noResults.classList.toggle('hidden', visibleCount > 0 || cards.length === 0);
        }
    };

    window.clearSavedSearch = function() {
        const input = document.getElementById('saved-search-input');
        if (input) input.value = '';
        filterSavedPlaces();
    };

    // Xử lý Bỏ lưu / Thả tim (Optimistic UI)
    window.toggleSave = async function(btn, quanId) {
        if (btn.dataset.loading === 'true') return;
        btn.dataset.loading = 'true';

        const card = document.getElementById(`quan-card-desktop-${quanId}`);
        const quanName = card?.querySelector('h3')?.textContent?.trim() || 'Quán ăn';
        const quanImg = card?.querySelector('img')?.getAttribute('src') || null;

        // 1. Toast thông báo lập tức
        if (window.showToast) {
            window.showToast({
                eyebrow: 'Đã cập nhật danh sách',
                title: `Đã bỏ lưu "${quanName}"`,
                message: 'Địa điểm đã được gỡ khỏi danh sách yêu thích của bạn.',
                type: 'unsaved',
                image: quanImg
            });
        }

        // 2. Hiệu ứng gỡ card mượt mà
        if (card) {
            card.style.transition = 'all 0.28s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.88)';

            setTimeout(() => {
                card.remove();

                // Cập nhật số lượng
                const remainingCards = document.querySelectorAll('.saved-item-element').length;
                const totalCountElem = document.getElementById('saved-total-count');
                if (totalCountElem) totalCountElem.textContent = remainingCards;

                // Nếu hết quán, hiện empty state chuẩn
                const grid = document.getElementById('saved-places-grid-desktop');
                if (grid && remainingCards === 0) {
                    grid.innerHTML = `
                        <div class="col-span-full py-16 px-4 text-center empty-state-desktop">
                            <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-4 shadow-xs">
                                <span class="material-symbols-outlined text-[32px]">favorite</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Bạn chưa lưu địa điểm nào.</h3>
                            <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">Hãy dạo quanh Khám phá hoặc Bản đồ để thả tim lưu lại những quán ăn bạn muốn ghé thăm.</p>
                            <div class="flex flex-wrap items-center justify-center gap-3">
                                <a href="{{ route('kham-pha') }}" class="px-5 py-2.5 rounded-xl bg-primary text-white font-bold text-xs shadow-sm hover:brightness-105 active:scale-95 transition-all">
                                    Khám phá món ngon ngay
                                </a>
                                <a href="{{ route('ban-do') }}" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold text-xs transition-all">
                                    Tìm quán quanh bạn
                                </a>
                            </div>
                        </div>
                    `;
                    const toolbar = document.getElementById('saved-filter-toolbar');
                    if (toolbar) toolbar.remove();
                }
            }, 280);
        }

        // 3. Gửi request backend
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
                window.location.href = "{{ route('login') }}";
                return;
            }

            const data = await res.json();
            if (!data.success && window.showToast) {
                window.showToast({
                    title: 'Không thể thực hiện',
                    message: data.message || 'Đã có lỗi xảy ra.',
                    type: 'error'
                });
            }
        } catch (err) {
            console.error('Lỗi toggle save:', err);
        } finally {
            btn.dataset.loading = 'false';
        }
    };

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
})();
</script>
@endpush
