@props([
    'prefix' => 'nearby',
    'initialLat' => null,
    'initialLng' => null,
    'initialRadius' => 5,
    'danhMuc' => '',
])

@php
    $allowedRadii = [1, 3, 5, 10];
    $defaultRadius = in_array((int) $initialRadius, $allowedRadii, true) ? (int) $initialRadius : 5;
    $hasInitialGps = is_numeric($initialLat) && is_numeric($initialLng);
    $sectionId = $prefix === 'nearby' ? 'nearby-scanner-section' : $prefix . '-scanner-section';

    $mapCategories = [
        '' => 'Tất cả',
        'Nhà hàng' => 'Nhà hàng',
        'Cà phê & Trà' => 'Cà phê & Trà',
        'Lẩu & Nướng' => 'Lẩu & Nướng',
        'Ăn vặt' => 'Ăn vặt',
        'Billiards & Giải trí' => 'Billiards & Giải trí',
        'Quán Đêm 24/7' => 'Quán Đêm 24/7',
    ];
@endphp

@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        .qm-nearby-leaflet-map {
            z-index: 10 !important;
            font-family: inherit;
            width: 100%;
            height: 100%;
            min-height: 360px;
        }
        .qm-scanner-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .qm-scanner-map-box,
        .qm-scanner-list-box {
            height: 360px;
        }
        @media (min-width: 1024px) {
            .qm-scanner-grid {
                grid-template-columns: 7fr 5fr;
            }
            .qm-scanner-map-box,
            .qm-scanner-list-box {
                height: 450px;
            }
        }
        .qm-user-gps-dot {
            width: 18px;
            height: 18px;
            border-radius: 9999px;
            background: #2563eb;
            border: 3px solid #ffffff;
            box-shadow: 0 0 0 6px rgba(37, 99, 235, 0.25), 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        .qm-venue-marker-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 9999px;
            background: #ea580c;
            color: #ffffff;
            font-weight: 800;
            font-size: 11px;
            white-space: nowrap;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.4);
            transition: transform 0.18s ease, background-color 0.18s ease;
        }
        .qm-venue-marker-badge:hover,
        .qm-venue-marker-badge.is-active {
            background: #be123c;
            transform: scale(1.12) translateY(-2px);
            z-index: 999 !important;
        }
        .leaflet-popup-content-wrapper {
            border-radius: 16px !important;
            padding: 0 !important;
            overflow: hidden;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.18) !important;
        }
        .leaflet-popup-content {
            margin: 0 !important;
            width: 265px !important;
        }
    </style>
@endonce

<section id="{{ $sectionId }}" class="mb-8">
    {{-- Banner / Thanh kích hoạt Quét quán lân cận --}}
    <div class="bg-gradient-to-r from-orange-50 via-amber-50/70 to-rose-50 rounded-2xl border border-orange-200/80 p-4 sm:p-5 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-primary text-white flex items-center justify-center shrink-0 shadow-sm" style="background-color: #a04100;">
                    <span class="material-symbols-outlined text-[26px]">radar</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base sm:text-lg font-black text-on-surface">Bản đồ Quán ăn lân cận</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white text-primary border border-orange-200">
                            Dữ liệu Quán Mới đã xác thực
                        </span>
                    </div>
                    <p class="text-xs sm:text-[13.5px] text-on-surface-variant mt-0.5">
                        Định vị GPS hoặc nhấp chọn điểm bất kỳ trên bản đồ để tìm quán ăn quanh bạn trong bán kính 1 km – 10 km.
                    </p>
                </div>
            </div>

            {{-- Chọn bán kính + Nút Quét quán lân cận --}}
            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                <div class="inline-flex items-center bg-white rounded-xl p-1 border border-gray-200 shadow-2xs" role="group" aria-label="Chọn bán kính tìm kiếm">
                    @foreach($allowedRadii as $r)
                        <button type="button"
                                data-nearby-prefix="{{ $prefix }}"
                                data-nearby-radius="{{ $r }}"
                                onclick="window.QMNearbyScanner?.selectRadius('{{ $prefix }}', {{ $r }})"
                                class="nearby-radius-btn-{{ $prefix }} px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $defaultRadius === $r ? 'bg-primary text-white shadow-2xs' : 'text-gray-600 hover:bg-gray-100' }}"
                                style="{{ $defaultRadius === $r ? 'background-color:#a04100;color:#fff;' : '' }}">
                            {{ $r }} km
                        </button>
                    @endforeach
                </div>

                <button type="button"
                        id="{{ $prefix }}-btn-scan"
                        onclick="window.QMNearbyScanner?.scanFromGps('{{ $prefix }}')"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-[13.5px] text-white shadow-sm hover:brightness-110 active:scale-95 transition-all cursor-pointer"
                        style="background-color: #a04100; color: #ffffff;">
                    <span class="material-symbols-outlined text-[18px]" id="{{ $prefix }}-btn-scan-icon">my_location</span>
                    <span id="{{ $prefix }}-btn-scan-text">Quét quán lân cận</span>
                </button>

                <button type="button"
                        id="{{ $prefix }}-btn-toggle-panel"
                        onclick="window.QMNearbyScanner?.togglePanel('{{ $prefix }}')"
                        class="inline-flex items-center justify-center gap-1 px-3 py-2.5 rounded-xl font-bold text-xs bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[17px]" id="{{ $prefix }}-btn-toggle-icon">{{ $hasInitialGps ? 'expand_less' : 'map' }}</span>
                    <span id="{{ $prefix }}-btn-toggle-text">{{ $hasInitialGps ? 'Thu gọn' : 'Mở bản đồ' }}</span>
                </button>
            </div>
        </div>

        {{-- Khung hiển thị Bản đồ & Danh sách đồng bộ --}}
        <div id="{{ $prefix }}-scanner-panel" class="{{ $hasInitialGps ? '' : 'hidden' }} mt-4 pt-4 border-t border-orange-200/60 space-y-3">
            {{-- Thanh công cụ phụ: Chọn nhanh khu vực & Lọc danh mục ngay trên bản đồ --}}
            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-2.5 bg-white/80 rounded-xl p-2.5 border border-orange-100">
                {{-- Chọn nhanh khu vực (Hữu ích khi từ chối GPS hoặc muốn khám phá tỉnh thành khác) --}}
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mr-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px] text-orange-600">explore</span>
                        Vị trí nhanh:
                    </span>
                    <button type="button"
                            onclick="window.QMNearbyScanner?.scanFromGps('{{ $prefix }}')"
                            class="px-2.5 py-1 rounded-lg text-[11.5px] font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition cursor-pointer inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">my_location</span>
                        GPS của tôi
                    </button>
                    <button type="button"
                            onclick="window.QMNearbyScanner?.scanPresetLocation('{{ $prefix }}', 21.028511, 105.804817, 'Hà Nội (Ba Đình / Hoàn Kiếm)')"
                            class="px-2.5 py-1 rounded-lg text-[11.5px] font-semibold bg-gray-100 text-gray-700 hover:bg-orange-50 hover:text-orange-700 border border-gray-200 transition cursor-pointer">
                        Hà Nội
                    </button>
                    <button type="button"
                            onclick="window.QMNearbyScanner?.scanPresetLocation('{{ $prefix }}', 10.776889, 106.700806, 'TP. Hồ Chí Minh (Quận 1)')"
                            class="px-2.5 py-1 rounded-lg text-[11.5px] font-semibold bg-gray-100 text-gray-700 hover:bg-orange-50 hover:text-orange-700 border border-gray-200 transition cursor-pointer">
                        TP.HCM
                    </button>
                    <button type="button"
                            onclick="window.QMNearbyScanner?.scanPresetLocation('{{ $prefix }}', 16.054407, 108.202167, 'Đà Nẵng (Hải Châu)')"
                            class="px-2.5 py-1 rounded-lg text-[11.5px] font-semibold bg-gray-100 text-gray-700 hover:bg-orange-50 hover:text-orange-700 border border-gray-200 transition cursor-pointer">
                        Đà Nẵng
                    </button>
                </div>

                {{-- Lọc nhanh theo Danh mục trên Bản đồ --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 xl:pb-0">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mr-1 shrink-0">Danh mục:</span>
                    @foreach($mapCategories as $catValue => $catLabel)
                        @php $isActiveCat = ($danhMuc ?? '') === $catValue; @endphp
                        <button type="button"
                                data-nearby-cat="{{ $catValue }}"
                                onclick="window.QMNearbyScanner?.selectCategory('{{ $prefix }}', '{{ $catValue }}')"
                                class="nearby-cat-btn-{{ $prefix }} px-2.5 py-1 rounded-lg text-[11.5px] font-bold shrink-0 transition cursor-pointer {{ $isActiveCat ? 'bg-orange-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                            {{ $catLabel }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Thông báo trạng thái (Đang tìm / Lỗi GPS / Không có kết quả) --}}
            <div id="{{ $prefix }}-status-banner" class="hidden px-4 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold flex items-center justify-between gap-3"></div>

            <div class="qm-scanner-grid bg-white rounded-2xl border border-gray-200 p-3 sm:p-4 shadow-sm">
                {{-- Cột Bản đồ Leaflet (7/12 cột trên Desktop) --}}
                <div class="qm-scanner-map-box relative rounded-xl overflow-hidden border border-gray-200 bg-gray-100">
                    <div id="{{ $prefix }}-leaflet-map" class="w-full h-full qm-nearby-leaflet-map"></div>

                    {{-- Nút nổi "Quét khu vực đang xem" khi người dùng kéo/di chuyển bản đồ --}}
                    <button type="button"
                            id="{{ $prefix }}-btn-scan-area"
                            onclick="window.QMNearbyScanner?.scanCurrentMapView('{{ $prefix }}')"
                            style="z-index: 450;"
                            class="hidden absolute top-3 left-1/2 -translate-x-1/2 bg-white hover:bg-orange-50 text-gray-900 border border-orange-300 shadow-lg px-3.5 py-2 rounded-full text-xs font-extrabold items-center gap-1.5 transition cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-orange-600">travel_explore</span>
                        <span>Quét quán tại khu vực đang xem</span>
                    </button>

                    {{-- Gợi ý nhỏ ở góc dưới bản đồ --}}
                    <div style="z-index: 400;" class="absolute bottom-2.5 left-2.5 bg-white/90 px-2.5 py-1 rounded-lg border border-gray-200 text-[11px] text-gray-600 font-medium pointer-events-none hidden sm:flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-orange-600">touch_app</span>
                        <span>Mẹo: Nhấp vào điểm bất kỳ trên bản đồ để đổi tâm quét</span>
                    </div>

                    {{-- Lớp phủ Loading khi đang truy vấn --}}
                    <div id="{{ $prefix }}-map-loading" style="z-index: 500;" class="hidden absolute inset-0 bg-white/80 flex flex-col items-center justify-center gap-2">
                        <span class="material-symbols-outlined animate-spin text-3xl text-primary" style="color: #a04100;">progress_activity</span>
                        <p class="text-xs font-bold text-gray-700" id="{{ $prefix }}-loading-text">Đang quét quán ăn lân cận...</p>
                    </div>
                </div>

                {{-- Cột Danh sách quán đồng bộ với Marker (5/12 cột trên Desktop) --}}
                <div class="qm-scanner-list-box flex flex-col">
                    <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-gray-100">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-primary text-[19px]" style="color: #a04100;">storefront</span>
                            <h3 class="font-bold text-sm text-gray-800">
                                Kết quả quanh tâm (<span id="{{ $prefix }}-result-count">0</span>)
                            </h3>
                        </div>
                        <span class="text-[11px] font-semibold text-gray-500" id="{{ $prefix }}-radius-label">
                            Bán kính {{ $defaultRadius }} km
                        </span>
                    </div>

                    {{-- Danh sách cuộn --}}
                    <div id="{{ $prefix }}-venue-list" class="flex-1 overflow-y-auto space-y-2.5 pr-1">
                        <div class="h-full flex flex-col items-center justify-center text-center p-6 text-gray-400">
                            <span class="material-symbols-outlined text-4xl mb-2 text-orange-400">travel_explore</span>
                            <p class="text-xs font-semibold text-gray-600">Nhấn "Quét quán lân cận" hoặc chọn khu vực (Hà Nội, TP.HCM, Đà Nẵng) để xem các quán ăn trên bản đồ.</p>
                        </div>
                    </div>

                    {{-- Nút Xem thêm khi có nhiều trang --}}
                    <div id="{{ $prefix }}-load-more-wrap" class="hidden pt-2 mt-2 border-t border-gray-100">
                        <button type="button"
                                id="{{ $prefix }}-btn-load-more"
                                onclick="window.QMNearbyScanner?.loadMore('{{ $prefix }}')"
                                class="w-full py-2 px-3 rounded-xl text-xs font-bold bg-orange-50 hover:bg-orange-100 text-orange-800 border border-orange-200 transition cursor-pointer flex items-center justify-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">expand_more</span>
                            <span>Xem thêm kết quả</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@once
<script>
    window.QMNearbyScanner = (function () {
        const instances = {};

        function getInst(prefix) {
            if (!instances[prefix]) {
                instances[prefix] = {
                    prefix,
                    map: null,
                    userMarker: null,
                    radiusCircle: null,
                    venueLayerGroup: null,
                    markersById: {},
                    loadedVenues: [],
                    currentLat: null,
                    currentLng: null,
                    currentRadius: 5,
                    currentDanhMuc: '',
                    currentPage: 1,
                    lastPage: 1,
                    totalCount: 0,
                    isScanning: false,
                    suppressMoveEvent: false,
                };
            }
            return instances[prefix];
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str == null ? '' : String(str);
            return div.innerHTML;
        }

        function formatPrice(val) {
            const num = Number(val) || 0;
            return num.toLocaleString('vi-VN') + 'đ';
        }

        function buildDirectionsUrl(inst, venueLat, venueLng) {
            const dest = `${Number(venueLat).toFixed(6)},${Number(venueLng).toFixed(6)}`;
            if (Number.isFinite(inst.currentLat) && Number.isFinite(inst.currentLng)) {
                const origin = `${Number(inst.currentLat).toFixed(6)},${Number(inst.currentLng).toFixed(6)}`;
                return `https://www.google.com/maps/dir/?api=1&origin=${encodeURIComponent(origin)}&destination=${encodeURIComponent(dest)}`;
            }
            return `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(dest)}`;
        }

        function ensureMapInitialized(inst, lat, lng) {
            const mapId = `${inst.prefix}-leaflet-map`;
            const container = document.getElementById(mapId);
            if (!container || typeof L === 'undefined') return null;

            const startLat = Number.isFinite(lat) ? lat : 21.028511;
            const startLng = Number.isFinite(lng) ? lng : 105.804817;

            if (!inst.map) {
                inst.map = L.map(mapId, {
                    center: [startLat, startLng],
                    zoom: 14,
                    scrollWheelZoom: true,
                });

                L.tileLayer('https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['0', '1', '2', '3'],
                    attribution: '&copy; Google Maps',
                    keepBuffer: 8,
                    updateWhenIdle: false
                }).addTo(inst.map);

                inst.venueLayerGroup = L.layerGroup().addTo(inst.map);

                // Cho phép nhấp trực tiếp lên bản đồ để đặt tâm quét mới
                inst.map.on('click', function (e) {
                    if (inst.isScanning) return;
                    fetchNearbyVenues(inst.prefix, e.latlng.lat, e.latlng.lng, inst.currentRadius, 1, 'Điểm chọn trên bản đồ');
                });

                // Khi người dùng kéo bản đồ sang vùng khác, hiện nút "Quét quán tại khu vực đang xem"
                inst.map.on('moveend', function () {
                    if (inst.suppressMoveEvent || inst.isScanning || inst.currentLat === null) return;
                    const center = inst.map.getCenter();
                    const dLat = Math.abs(center.lat - inst.currentLat);
                    const dLng = Math.abs(center.lng - inst.currentLng);
                    const scanAreaBtn = document.getElementById(`${inst.prefix}-btn-scan-area`);
                    if (scanAreaBtn) {
                        if (dLat > 0.008 || dLng > 0.008) {
                            scanAreaBtn.classList.remove('hidden');
                            scanAreaBtn.classList.add('inline-flex');
                        } else {
                            scanAreaBtn.classList.add('hidden');
                            scanAreaBtn.classList.remove('inline-flex');
                        }
                    }
                });
            } else {
                setTimeout(() => inst.map.invalidateSize(), 100);
            }

            return inst.map;
        }

        function updateUserLocationMarker(inst, lat, lng, radiusKm, label = 'Vị trí tâm quét hiện tại') {
            if (!inst.map) return;

            inst.suppressMoveEvent = true;
            const latLng = [lat, lng];
            const userIcon = L.divIcon({
                className: 'qm-user-gps-wrapper',
                html: '<div class="qm-user-gps-dot" title="Tâm tìm kiếm hiện tại"></div>',
                iconSize: [18, 18],
                iconAnchor: [9, 9],
            });

            const popupContent = `<div class="p-2.5 text-center font-bold text-xs text-blue-700">📍 ${escapeHtml(label)}</div>`;

            if (inst.userMarker) {
                inst.userMarker.setLatLng(latLng).setPopupContent(popupContent);
            } else {
                inst.userMarker = L.marker(latLng, { icon: userIcon, zIndexOffset: 1000 })
                    .addTo(inst.map)
                    .bindPopup(popupContent);
            }

            if (inst.radiusCircle) {
                inst.radiusCircle.setLatLng(latLng);
                inst.radiusCircle.setRadius(radiusKm * 1000);
            } else {
                inst.radiusCircle = L.circle(latLng, {
                    radius: radiusKm * 1000,
                    color: '#ea580c',
                    weight: 1.5,
                    fillColor: '#f97316',
                    fillOpacity: 0.08,
                }).addTo(inst.map);
            }

            inst.map.fitBounds(inst.radiusCircle.getBounds(), { padding: [24, 24] });
            const scanAreaBtn = document.getElementById(`${inst.prefix}-btn-scan-area`);
            if (scanAreaBtn) {
                scanAreaBtn.classList.add('hidden');
                scanAreaBtn.classList.remove('inline-flex');
            }
            setTimeout(() => { inst.suppressMoveEvent = false; }, 350);
        }

        function showStatusBanner(inst, type, message, extraActionHtml = '') {
            const banner = document.getElementById(`${inst.prefix}-status-banner`);
            if (!banner) return;

            if (!message) {
                banner.classList.add('hidden');
                banner.innerHTML = '';
                return;
            }

            const styles = {
                info: 'bg-blue-50 text-blue-800 border border-blue-200',
                warning: 'bg-amber-50 text-amber-800 border border-amber-200',
                error: 'bg-rose-50 text-rose-800 border border-rose-200',
                success: 'bg-emerald-50 text-emerald-800 border border-emerald-200',
            };

            banner.className = `px-4 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold flex flex-wrap items-center justify-between gap-2 ${styles[type] || styles.info}`;
            banner.innerHTML = `<span>${escapeHtml(message)}</span>${extraActionHtml}`;
            banner.classList.remove('hidden');
        }

        function setLoading(inst, loading, text = 'Đang quét quán ăn lân cận...') {
            inst.isScanning = loading;
            const p = inst.prefix;
            const overlay = document.getElementById(`${p}-map-loading`);
            const loadingText = document.getElementById(`${p}-loading-text`);
            const btn = document.getElementById(`${p}-btn-scan`);
            const btnIcon = document.getElementById(`${p}-btn-scan-icon`);
            const btnText = document.getElementById(`${p}-btn-scan-text`);

            if (loadingText) loadingText.textContent = text;
            if (overlay) overlay.classList.toggle('hidden', !loading);

            if (btn) btn.disabled = loading;
            if (btnIcon) {
                btnIcon.textContent = loading ? 'progress_activity' : 'my_location';
                btnIcon.classList.toggle('animate-spin', loading);
            }
            if (btnText) {
                btnText.textContent = loading ? 'Đang quét...' : 'Quét quán lân cận';
            }
        }

        function buildPopupHtml(inst, quan) {
            const name = escapeHtml(quan.ten_quan);
            const addr = escapeHtml(quan.dia_chi || 'Đang cập nhật địa chỉ');
            const img = escapeHtml(quan.anh_dai_dien || 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=400&q=80');
            const url = escapeHtml(quan.url || `/quan/${quan.slug}`);
            const dist = Number(quan.khoang_cach_km).toFixed(2);
            const priceText = `${formatPrice(quan.gia_nho_nhat)} - ${formatPrice(quan.gia_lon_nhat)}`;
            const dirUrl = escapeHtml(buildDirectionsUrl(inst, quan.latitude, quan.longitude));
            const openBadge = quan.dang_mo_cua
                ? `<span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow">Đang mở cửa</span>`
                : `<span class="bg-gray-700 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow">Tạm đóng</span>`;

            return `
                <div class="bg-white overflow-hidden">
                    <div class="relative h-28 w-full bg-gray-100">
                        <img src="${img}" alt="${name}" class="w-full h-full object-cover" />
                        <div class="absolute top-2 left-2">${openBadge}</div>
                        <span class="absolute bottom-2 left-2 bg-orange-600 text-white text-[11px] font-extrabold px-2 py-0.5 rounded-lg shadow">
                            Cách ${dist} km
                        </span>
                    </div>
                    <div class="p-3 space-y-1.5">
                        <h4 class="font-bold text-[14px] text-gray-900 leading-snug line-clamp-1">${name}</h4>
                        <p class="text-[11.5px] text-gray-500 line-clamp-2">${addr}</p>
                        <div class="text-[12px] font-bold text-orange-700">${priceText}</div>
                        <div class="grid grid-cols-2 gap-1.5 pt-1">
                            <a href="${dirUrl}" target="_blank" rel="noopener noreferrer" class="py-1.5 px-2 rounded-lg text-[11px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 inline-flex items-center justify-center gap-1 transition">
                                <span class="material-symbols-outlined text-[14px]">directions</span>
                                <span>Chỉ đường</span>
                            </a>
                            <a href="${url}" class="py-1.5 px-2 rounded-lg text-[11px] font-bold text-white inline-flex items-center justify-center gap-1 transition hover:brightness-110" style="background-color:#a04100;color:#ffffff;">
                                <span>Chi tiết</span>
                                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderVenuesOnMapAndList(inst, quans, append = false) {
            const p = inst.prefix;
            const listEl = document.getElementById(`${p}-venue-list`);
            const countEl = document.getElementById(`${p}-result-count`);
            const radiusLabel = document.getElementById(`${p}-radius-label`);
            const loadMoreWrap = document.getElementById(`${p}-load-more-wrap`);

            if (!append) {
                inst.loadedVenues = Array.isArray(quans) ? [...quans] : [];
                if (inst.venueLayerGroup) {
                    inst.venueLayerGroup.clearLayers();
                }
                inst.markersById = {};
            } else {
                const existingIds = new Set(inst.loadedVenues.map(item => String(item.id)));
                (quans || []).forEach(q => {
                    if (!existingIds.has(String(q.id))) {
                        inst.loadedVenues.push(q);
                    }
                });
            }

            const allQuans = inst.loadedVenues;
            if (countEl) countEl.textContent = inst.totalCount || allQuans.length;
            if (radiusLabel) radiusLabel.textContent = `Bán kính ${inst.currentRadius} km`;

            if (loadMoreWrap) {
                loadMoreWrap.classList.toggle('hidden', !(inst.currentPage < inst.lastPage));
            }

            if (allQuans.length === 0) {
                if (listEl) {
                    listEl.innerHTML = `
                        <div class="h-full flex flex-col items-center justify-center text-center p-5 text-gray-500 space-y-2">
                            <span class="material-symbols-outlined text-4xl text-amber-500">location_off</span>
                            <p class="text-sm font-bold text-gray-800">Không tìm thấy quán ăn trong bán kính ${inst.currentRadius} km</p>
                            <p class="text-xs text-gray-500">Hãy thử chọn bán kính <strong>10 km</strong> hoặc nhấn nút <strong>Hà Nội</strong> ở thanh Vị trí nhanh phía trên.</p>
                            <button type="button"
                                    onclick="window.QMNearbyScanner?.scanPresetLocation('${p}', 21.028511, 105.804817, 'Hà Nội')"
                                    class="mt-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-orange-100 text-orange-800 hover:bg-orange-200 transition cursor-pointer">
                                Xem thử các quán tại Hà Nội
                            </button>
                        </div>
                    `;
                }
                showStatusBanner(
                    inst,
                    'warning',
                    `Không có quán ăn nào phù hợp trong phạm vi ${inst.currentRadius} km quanh tâm đã chọn.`,
                    `<button type="button" onclick="window.QMNearbyScanner?.selectRadius('${p}', 10)" class="underline font-extrabold text-xs cursor-pointer">Mở rộng lên 10 km</button>`
                );
                return;
            }

            showStatusBanner(inst, 'success', `Tìm thấy ${inst.totalCount || allQuans.length} quán ăn lân cận trong phạm vi ${inst.currentRadius} km.`);

            let listHtml = '';

            allQuans.forEach((quan, idx) => {
                const lat = parseFloat(quan.latitude);
                const lng = parseFloat(quan.longitude);
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

                const dist = Number(quan.khoang_cach_km).toFixed(2);

                if (!inst.markersById[quan.id]) {
                    const markerIcon = L.divIcon({
                        className: 'qm-venue-div-icon',
                        html: `<div class="qm-venue-marker-badge" id="${p}-badge-${escapeHtml(quan.id)}">
                                   <span class="material-symbols-outlined" style="font-size:13px;font-variation-settings:'FILL' 1;">restaurant</span>
                                   <span>${dist}km</span>
                               </div>`,
                        iconSize: [68, 28],
                        iconAnchor: [34, 14],
                    });

                    const marker = L.marker([lat, lng], { icon: markerIcon })
                        .bindPopup(buildPopupHtml(inst, quan), { maxWidth: 270 });

                    marker.on('click', () => {
                        highlightVenueCard(inst, quan.id, true);
                    });

                    inst.venueLayerGroup.addLayer(marker);
                    inst.markersById[quan.id] = marker;
                }

                const name = escapeHtml(quan.ten_quan);
                const addr = escapeHtml(quan.dia_chi || '');
                const img = escapeHtml(quan.anh_dai_dien || 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=300&q=80');
                const url = escapeHtml(quan.url || `/quan/${quan.slug}`);
                const cat = escapeHtml(quan.loai_hinh_kinh_doanh || 'Quán ăn');
                const dirUrl = escapeHtml(buildDirectionsUrl(inst, lat, lng));
                const verifiedHtml = quan.is_xac_thuc
                    ? `<span class="material-symbols-outlined text-[14px] text-blue-600 shrink-0" style="font-variation-settings:'FILL' 1;" title="Quán đã xác thực">verified</span>`
                    : '';
                const statusDot = quan.dang_mo_cua
                    ? `<span class="inline-flex items-center gap-1 text-[10.5px] font-bold text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Đang mở</span>`
                    : `<span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-gray-400"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Tạm đóng</span>`;

                listHtml += `
                    <div id="${p}-card-${escapeHtml(quan.id)}"
                         onclick="window.QMNearbyScanner?.focusVenue('${p}', '${escapeHtml(quan.id)}')"
                         class="nearby-venue-card-${p} group p-2.5 rounded-xl border border-gray-100 hover:border-orange-300 bg-gray-50/60 hover:bg-orange-50/40 transition-all cursor-pointer flex gap-3 items-center">
                        <div class="relative w-16 h-16 rounded-xl overflow-hidden bg-gray-200 shrink-0">
                            <img src="${img}" alt="${name}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                            <span class="absolute top-1 left-1 bg-black/75 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">
                                #${idx + 1}
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-1">
                                <div class="flex items-center gap-1 min-w-0">
                                    <h4 class="font-bold text-[13.5px] text-gray-900 truncate group-hover:text-primary">${name}</h4>
                                    ${verifiedHtml}
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 font-extrabold text-[11px] shrink-0">
                                    ${dist} km
                                </span>
                            </div>
                            <p class="text-[11.5px] text-gray-500 truncate mt-0.5">${addr}</p>
                            <div class="flex items-center justify-between mt-1.5 gap-1">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-[10.5px] font-medium text-gray-600 bg-white px-2 py-0.5 rounded border border-gray-200 truncate max-w-[105px]">
                                        ${cat}
                                    </span>
                                    ${statusDot}
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="${dirUrl}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       onclick="event.stopPropagation()"
                                       title="Chỉ đường bằng Google Maps"
                                       class="text-[11px] font-bold text-blue-600 hover:underline inline-flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[14px]">directions</span>
                                    </a>
                                    <a href="${url}"
                                       onclick="event.stopPropagation()"
                                       class="text-[11.5px] font-bold text-primary hover:underline inline-flex items-center gap-0.5"
                                       style="color:#a04100;">
                                        <span>Chi tiết</span>
                                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            if (listEl) {
                listEl.innerHTML = listHtml;
            }
        }

        function highlightVenueCard(inst, quanId, scrollCard = true) {
            const p = inst.prefix;
            document.querySelectorAll(`.nearby-venue-card-${p}`).forEach(el => {
                el.classList.remove('ring-2', 'ring-orange-500', 'bg-orange-50');
            });
            document.querySelectorAll('.qm-venue-marker-badge').forEach(el => {
                el.classList.remove('is-active');
            });

            const card = document.getElementById(`${p}-card-${quanId}`);
            if (card) {
                card.classList.add('ring-2', 'ring-orange-500', 'bg-orange-50');
                if (scrollCard) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }

            const badge = document.getElementById(`${p}-badge-${quanId}`);
            if (badge) {
                badge.classList.add('is-active');
            }
        }

        function focusVenue(prefix, quanId) {
            const inst = getInst(prefix);
            const marker = inst.markersById[quanId];
            if (!marker || !inst.map) return;

            inst.suppressMoveEvent = true;
            const latLng = marker.getLatLng();
            inst.map.flyTo(latLng, 16, { duration: 0.6 });
            marker.openPopup();
            highlightVenueCard(inst, quanId, false);
            setTimeout(() => { inst.suppressMoveEvent = false; }, 700);
        }

        function openPanel(inst) {
            const p = inst.prefix;
            const panel = document.getElementById(`${p}-scanner-panel`);
            const toggleIcon = document.getElementById(`${p}-btn-toggle-icon`);
            const toggleText = document.getElementById(`${p}-btn-toggle-text`);

            if (panel) panel.classList.remove('hidden');
            if (toggleIcon) toggleIcon.textContent = 'expand_less';
            if (toggleText) toggleText.textContent = 'Thu gọn';
        }

        function togglePanel(prefix) {
            const inst = getInst(prefix);
            const p = inst.prefix;
            const panel = document.getElementById(`${p}-scanner-panel`);
            const toggleIcon = document.getElementById(`${p}-btn-toggle-icon`);
            const toggleText = document.getElementById(`${p}-btn-toggle-text`);
            if (!panel) return;

            const isHidden = panel.classList.toggle('hidden');
            if (toggleIcon) toggleIcon.textContent = isHidden ? 'map' : 'expand_less';
            if (toggleText) toggleText.textContent = isHidden ? 'Mở bản đồ' : 'Thu gọn';

            if (!isHidden) {
                ensureMapInitialized(inst, inst.currentLat ?? 21.028511, inst.currentLng ?? 105.804817);
                setTimeout(() => inst.map?.invalidateSize(), 120);
            }
        }

        function selectRadius(prefix, radiusKm) {
            const inst = getInst(prefix);
            inst.currentRadius = Number(radiusKm) || 5;
            document.querySelectorAll(`.nearby-radius-btn-${inst.prefix}`).forEach(btn => {
                const isSelected = Number(btn.dataset.nearbyRadius) === inst.currentRadius;
                btn.className = `nearby-radius-btn-${inst.prefix} px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer ${isSelected ? 'bg-primary text-white shadow-2xs' : 'text-gray-600 hover:bg-gray-100'}`;
                btn.style.backgroundColor = isSelected ? '#a04100' : '';
                btn.style.color = isSelected ? '#ffffff' : '';
            });

            if (inst.currentLat !== null && inst.currentLng !== null) {
                fetchNearbyVenues(prefix, inst.currentLat, inst.currentLng, inst.currentRadius, 1);
            }
        }

        function selectCategory(prefix, danhMuc) {
            const inst = getInst(prefix);
            inst.currentDanhMuc = danhMuc || '';

            document.querySelectorAll(`.nearby-cat-btn-${inst.prefix}`).forEach(btn => {
                const isSelected = (btn.dataset.nearbyCat || '') === inst.currentDanhMuc;
                btn.className = `nearby-cat-btn-${inst.prefix} px-2.5 py-1 rounded-lg text-[11.5px] font-bold shrink-0 transition cursor-pointer ${isSelected ? 'bg-orange-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'}`;
            });

            if (inst.currentLat !== null && inst.currentLng !== null) {
                fetchNearbyVenues(prefix, inst.currentLat, inst.currentLng, inst.currentRadius, 1);
            }
        }

        function scanPresetLocation(prefix, lat, lng, label) {
            fetchNearbyVenues(prefix, lat, lng, getInst(prefix).currentRadius, 1, label);
        }

        function scanCurrentMapView(prefix) {
            const inst = getInst(prefix);
            if (!inst.map) return;
            const center = inst.map.getCenter();
            fetchNearbyVenues(prefix, center.lat, center.lng, inst.currentRadius, 1, 'Tâm bản đồ đang xem');
        }

        function loadMore(prefix) {
            const inst = getInst(prefix);
            if (inst.isScanning || inst.currentPage >= inst.lastPage) return;
            fetchNearbyVenues(prefix, inst.currentLat, inst.currentLng, inst.currentRadius, inst.currentPage + 1);
        }

        async function fetchNearbyVenues(prefix, lat, lng, radiusKm, page = 1, locationLabel = 'Vị trí đã chọn') {
            const inst = getInst(prefix);
            if (inst.isScanning) return;

            inst.currentLat = parseFloat(lat);
            inst.currentLng = parseFloat(lng);
            inst.currentRadius = Number(radiusKm) || inst.currentRadius;
            inst.currentPage = Number(page) || 1;

            openPanel(inst);
            ensureMapInitialized(inst, inst.currentLat, inst.currentLng);
            if (inst.currentPage === 1) {
                updateUserLocationMarker(inst, inst.currentLat, inst.currentLng, inst.currentRadius, locationLabel);
            }
            setLoading(inst, true, `Đang tìm quán ăn trong bán kính ${inst.currentRadius} km...`);

            try {
                const params = new URLSearchParams({
                    lat: inst.currentLat,
                    lng: inst.currentLng,
                    radius: inst.currentRadius,
                    page: inst.currentPage,
                    per_page: 24,
                });
                if (inst.currentDanhMuc) {
                    params.set('danh_muc', inst.currentDanhMuc);
                }

                const res = await fetch(`/api/ban-do/quan-lan-can?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    showStatusBanner(inst, 'error', json.message || 'Không thể tải danh sách quán ăn lân cận. Vui lòng thử lại.');
                    return;
                }

                inst.totalCount = json.meta?.total ?? (json.data?.length || 0);
                inst.lastPage = json.meta?.last_page ?? 1;

                renderVenuesOnMapAndList(inst, json.data || [], inst.currentPage > 1);
            } catch (err) {
                console.error('Nearby scan error:', err);
                showStatusBanner(inst, 'error', 'Lỗi kết nối máy chủ khi tải dữ liệu quán lân cận. Vui lòng kiểm tra mạng và thử lại.');
            } finally {
                setLoading(inst, false);
                if (inst.map) {
                    setTimeout(() => inst.map.invalidateSize(), 100);
                }
            }
        }

        function scanFromGps(prefix = 'nearby') {
            const inst = getInst(prefix);
            if (inst.isScanning) return;

            openPanel(inst);
            ensureMapInitialized(inst, inst.currentLat ?? 21.028511, inst.currentLng ?? 105.804817);

            if (!navigator.geolocation) {
                showStatusBanner(
                    inst,
                    'error',
                    'Trình duyệt của bạn không hỗ trợ định vị GPS. Bạn có thể nhấp trực tiếp lên bản đồ hoặc chọn vị trí nhanh bên trên.'
                );
                return;
            }

            setLoading(inst, true, 'Đang xin quyền truy cập vị trí GPS của bạn...');

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    setLoading(inst, false);
                    fetchNearbyVenues(prefix, pos.coords.latitude, pos.coords.longitude, inst.currentRadius, 1, 'Vị trí GPS của bạn');
                },
                (err) => {
                    setLoading(inst, false);
                    let msg = 'Không thể lấy vị trí GPS. Bạn có thể nhấp trực tiếp vào điểm bất kỳ trên bản đồ hoặc chọn nút "Hà Nội / TP.HCM" để xem quán.';
                    if (err && err.code === 1) {
                        msg = 'Bạn đã từ chối quyền GPS. Hãy nhấp trực tiếp lên bản đồ để đặt tâm quét hoặc chọn nhanh khu vực phía trên.';
                    }
                    showStatusBanner(
                        inst,
                        'warning',
                        msg,
                        `<button type="button" onclick="window.QMNearbyScanner?.scanPresetLocation('${prefix}', 21.028511, 105.804817, 'Hà Nội')" class="underline font-extrabold text-xs cursor-pointer shrink-0">Quét thử tại Hà Nội</button>`
                    );
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 }
            );
        }

        function init(config) {
            const prefix = config.prefix || 'nearby';
            const inst = getInst(prefix);
            inst.currentRadius = Number(config.radius) || 5;
            inst.currentDanhMuc = config.danhMuc || '';

            if (Number.isFinite(parseFloat(config.lat)) && Number.isFinite(parseFloat(config.lng))) {
                fetchNearbyVenues(prefix, parseFloat(config.lat), parseFloat(config.lng), inst.currentRadius, 1, 'Vị trí đã chọn');
            }
        }

        return {
            init,
            scanFromGps,
            selectRadius,
            selectCategory,
            scanPresetLocation,
            scanCurrentMapView,
            loadMore,
            fetchNearbyVenues,
            focusVenue,
            togglePanel,
        };
    })();
</script>
@endonce

<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.QMNearbyScanner?.init({
            prefix: @json($prefix),
            lat: @json($hasInitialGps ? (float) $initialLat : null),
            lng: @json($hasInitialGps ? (float) $initialLng : null),
            radius: @json($defaultRadius),
            danhMuc: @json($danhMuc ?? ''),
        });
    });
</script>
