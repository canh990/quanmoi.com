@props([
    'lat' => 10.7769,
    'lng' => 106.7009,
    'latName' => 'vi_do',
    'lngName' => 'kinh_do',
    'latId' => 'vi_do',
    'lngId' => 'kinh_do',
    'mapId' => 'qm-map-picker',
    'height' => 'h-[260px]',
    'showCoordInputs' => false,
    'addressSelectors' => ['#dia_chi_chi_tiet', '#ten_phuong_xa', '#ten_quan_huyen', '#ten_tinh_thanh'],
])

@php
    $initialLat = is_numeric($lat) ? (float) $lat : 10.7769;
    $initialLng = is_numeric($lng) ? (float) $lng : 106.7009;
    // Nếu dữ liệu cũ bị đảo ngược vi_do / kinh_do (VD: vi_do > 90), tự động chỉnh lại để hiển thị đúng ở Việt Nam
    if ($initialLat > 90 && $initialLng <= 90) {
        [$initialLat, $initialLng] = [$initialLng, $initialLat];
    }
    $selectorsJson = json_encode($addressSelectors);
@endphp

@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        .qm-leaflet-container {
            z-index: 10 !important;
            font-family: inherit;
        }
        .qm-pin-marker {
            display: flex;
            align-items: center;
            justify-content: center;
            filter: drop-shadow(0 6px 10px rgba(234, 88, 12, 0.45));
            transition: transform 0.15s ease;
        }
        .qm-pin-marker:hover {
            transform: scale(1.1) translateY(-2px);
        }
        .qm-pin-pulse {
            position: absolute;
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            background: rgba(249, 115, 22, 0.22);
            animation: qmPulse 1.8s infinite ease-out;
            pointer-events: none;
        }
        @keyframes qmPulse {
            0% { transform: scale(0.7); opacity: 0.9; }
            100% { transform: scale(1.8); opacity: 0; }
        }
    </style>
@endonce

<div class="space-y-3" data-qm-map-wrapper="{{ $mapId }}">
    {{-- Thanh công cụ định vị nhanh --}}
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-1.5 text-xs text-slate-600 font-medium">
            <span class="material-symbols-outlined text-orange-500 text-[18px]">touch_app</span>
            <span>Kéo thả ghim đỏ hoặc nhấp lên bản đồ để chọn đúng vị trí quán</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    onclick="window.QMMapPicker?.locateFromAddress('{{ $mapId }}')"
                    id="{{ $mapId }}-btn-geocode"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">travel_explore</span>
                <span>Tìm theo địa chỉ</span>
            </button>
            <button type="button"
                    onclick="window.QMMapPicker?.locateCurrentGps('{{ $mapId }}')"
                    id="{{ $mapId }}-btn-gps"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">my_location</span>
                <span>Vị trí của tôi</span>
            </button>
        </div>
    </div>

    {{-- Khung Bản Đồ Tương Tác --}}
    <div class="relative {{ $height }} w-full rounded-xl overflow-hidden border border-slate-200 bg-slate-100 shadow-inner" style="height: 260px; min-height: 240px;">
        <div id="{{ $mapId }}" class="w-full h-full qm-leaflet-container" style="width: 100%; height: 100%; min-height: 240px;"></div>

        {{-- Badge hiển thị trạng thái và tọa độ đang ghim --}}
        <div class="absolute bottom-3 left-3 right-3 bg-white/95 px-3 py-2 rounded-xl shadow-md border border-slate-200/80 flex items-center justify-between gap-2 pointer-events-none" style="z-index: 400;">
            <div class="flex items-center gap-2 min-w-0">
                <span class="material-symbols-outlined text-orange-500 text-[20px] shrink-0" style="font-variation-settings: 'FILL' 1;">location_on</span>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-slate-800 truncate" id="{{ $mapId }}-status-text">
                        Đã ghim vị trí trên bản đồ (có thể kéo thả ghim)
                    </p>
                    <p class="text-[11px] text-slate-500 font-mono truncate" id="{{ $mapId }}-coord-badge">
                        Vĩ độ: {{ number_format($initialLat, 6, '.', '') }} • Kinh độ: {{ number_format($initialLng, 6, '.', '') }}
                    </p>
                </div>
            </div>
            <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold shrink-0">
                Chuẩn GPS
            </span>
        </div>
    </div>

    {{-- Ô input tọa độ (Ẩn hoặc Hiện tùy màn hình Chủ quán / Admin) --}}
    @if($showCoordInputs)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kinh độ (Longitude)</label>
                <input type="number" step="any" id="{{ $lngId }}" name="{{ $lngName }}"
                       value="{{ number_format($initialLng, 6, '.', '') }}"
                       oninput="window.QMMapPicker?.syncFromInputs('{{ $mapId }}')"
                       placeholder="106.700900"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Vĩ độ (Latitude)</label>
                <input type="number" step="any" id="{{ $latId }}" name="{{ $latName }}"
                       value="{{ number_format($initialLat, 6, '.', '') }}"
                       oninput="window.QMMapPicker?.syncFromInputs('{{ $mapId }}')"
                       placeholder="10.776900"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
            </div>
        </div>
    @else
        <input type="hidden" id="{{ $lngId }}" name="{{ $lngName }}" value="{{ number_format($initialLng, 6, '.', '') }}" />
        <input type="hidden" id="{{ $latId }}" name="{{ $latName }}" value="{{ number_format($initialLat, 6, '.', '') }}" />
    @endif
</div>

@once
<script>
    window.QMMapPicker = (function () {
        const instances = {};
        const debounceTimers = {};

        function createPinIcon() {
            return L.divIcon({
                className: 'qm-custom-pin',
                html: `
                    <div style="position:relative;width:38px;height:38px;display:flex;align-items:center;justify-content:center;">
                        <span class="qm-pin-pulse"></span>
                        <div class="qm-pin-marker" style="position:relative;z-index:2;width:34px;height:34px;border-radius:9999px;background:linear-gradient(135deg,#ea580c,#f97316);border:2.5px solid #fff;color:#fff;">
                            <span class="material-symbols-outlined" style="font-size:20px;font-variation-settings:'FILL' 1;">restaurant</span>
                        </div>
                    </div>
                `,
                iconSize: [38, 38],
                iconAnchor: [19, 19],
            });
        }

        function init(config) {
            const { mapId, lat, lng, latId, lngId, addressSelectors } = config;
            const container = document.getElementById(mapId);
            if (!container || typeof L === 'undefined') return;

            if (instances[mapId]) {
                instances[mapId].map.invalidateSize();
                return instances[mapId];
            }

            const startLat = Number.isFinite(parseFloat(lat)) ? parseFloat(lat) : 10.7769;
            const startLng = Number.isFinite(parseFloat(lng)) ? parseFloat(lng) : 106.7009;

            const map = L.map(mapId, {
                center: [startLat, startLng],
                zoom: 16,
                scrollWheelZoom: true,
            });

            L.tileLayer('https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['0', '1', '2', '3'],
                attribution: '&copy; Google Maps',
                keepBuffer: 8,
                updateWhenIdle: false
            }).addTo(map);

            const marker = L.marker([startLat, startLng], {
                draggable: true,
                icon: createPinIcon(),
            }).addTo(map);

            const state = {
                mapId,
                map,
                marker,
                latId,
                lngId,
                addressSelectors: Array.isArray(addressSelectors) ? addressSelectors : [],
                userDraggedPin: false,
            };

            instances[mapId] = state;

            // Kéo thả ghim
            marker.on('dragend', function () {
                const pos = marker.getLatLng();
                state.userDraggedPin = true;
                updateCoordinates(mapId, pos.lat, pos.lng, 'Đã cập nhật tọa độ theo vị trí kéo ghim');
                map.panTo(pos);
            });

            // Nhấp chuột trực tiếp trên bản đồ để đặt ghim
            map.on('click', function (e) {
                state.userDraggedPin = true;
                marker.setLatLng(e.latlng);
                updateCoordinates(mapId, e.latlng.lat, e.latlng.lng, 'Đã đặt ghim tại điểm vừa chọn');
                map.panTo(e.latlng);
            });

            // Tự động làm mới kích thước khi nằm trong Modal hoặc Tab ẩn
            if (typeof ResizeObserver !== 'undefined') {
                const ro = new ResizeObserver(() => {
                    map.invalidateSize();
                });
                ro.observe(container);
            }

            // Lắng nghe thay đổi ở các ô địa chỉ để tự động định vị mượt mà
            bindAddressListeners(state);

            return state;
        }

        function updateCoordinates(mapId, lat, lng, statusMessage) {
            const inst = instances[mapId];
            if (!inst) return;

            const fixedLat = Number(lat).toFixed(6);
            const fixedLng = Number(lng).toFixed(6);

            const latInput = document.getElementById(inst.latId);
            const lngInput = document.getElementById(inst.lngId);
            if (latInput) latInput.value = fixedLat;
            if (lngInput) lngInput.value = fixedLng;

            const badge = document.getElementById(`${mapId}-coord-badge`);
            if (badge) {
                badge.textContent = `Vĩ độ: ${fixedLat} • Kinh độ: ${fixedLng}`;
            }

            const statusEl = document.getElementById(`${mapId}-status-text`);
            if (statusEl && statusMessage) {
                statusEl.textContent = statusMessage;
            }
        }

        function setPosition(mapId, lat, lng, zoom = 17, statusMessage = null) {
            const inst = instances[mapId];
            if (!inst) return;

            const numLat = parseFloat(lat);
            const numLng = parseFloat(lng);
            if (!Number.isFinite(numLat) || !Number.isFinite(numLng)) return;

            const latLng = L.latLng(numLat, numLng);
            inst.marker.setLatLng(latLng);
            inst.map.invalidateSize();
            inst.map.setView(latLng, zoom, { animate: true });
            updateCoordinates(mapId, numLat, numLng, statusMessage);
        }

        function syncFromInputs(mapId) {
            const inst = instances[mapId];
            if (!inst) return;

            const latVal = parseFloat(document.getElementById(inst.latId)?.value);
            const lngVal = parseFloat(document.getElementById(inst.lngId)?.value);

            if (Number.isFinite(latVal) && Number.isFinite(lngVal) && latVal >= -90 && latVal <= 90 && lngVal >= -180 && lngVal <= 180) {
                const latLng = L.latLng(latVal, lngVal);
                inst.marker.setLatLng(latLng);
                inst.map.panTo(latLng);
                updateCoordinates(mapId, latVal, lngVal, 'Đã đồng bộ ghim theo tọa độ nhập tay');
            }
        }

        function collectAddressString(inst) {
            const parts = [];
            const seen = new Set();
            inst.addressSelectors.forEach(selector => {
                const el = document.querySelector(selector);
                if (!el) return;
                let val = '';
                if (el.tagName === 'SELECT') {
                    const opt = el.options[el.selectedIndex];
                    val = (opt && opt.value) ? opt.text.trim() : '';
                } else {
                    val = (el.value || '').trim();
                }
                const lower = val.toLowerCase();
                if (val && !val.startsWith('--') && !seen.has(lower)) {
                    seen.add(lower);
                    parts.push(val);
                }
            });
            return parts.join(', ');
        }

        async function directGeocodeFallback(address) {
            if (!address || address.length < 3) return null;
            const rawParts = address.split(',').map(s => s.trim()).filter(Boolean);
            const candidates = [address];
            for (let i = 1; i < rawParts.length; i++) {
                const sub = rawParts.slice(i).join(', ');
                if (sub.length >= 3 && !candidates.includes(sub)) {
                    candidates.push(sub);
                }
            }

            for (let i = 0; i < candidates.length; i++) {
                const query = candidates[i];
                try {
                    const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1&countrycodes=vn`;
                    const res = await fetch(url, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const items = await res.json();
                        if (Array.isArray(items) && items.length > 0 && items[0].lat && items[0].lon) {
                            return {
                                lat: parseFloat(items[0].lat),
                                lng: parseFloat(items[0].lon),
                                isFallback: i > 0,
                                matchedQuery: query,
                                message: i > 0
                                    ? `Đã định vị theo khu vực: ${query} (Kéo ghim để chỉnh chính xác hơn)`
                                    : `Đã định vị: ${address} (Kéo ghim để chỉnh chính xác hơn)`,
                            };
                        }
                    }
                } catch (e) {}
            }
            return null;
        }

        async function locateFromAddress(mapId, silent = false) {
            const inst = instances[mapId];
            if (!inst) return;

            const address = collectAddressString(inst);
            if (!address || address.length < 3) {
                if (!silent) {
                    alert('Vui lòng nhập địa chỉ hoặc chọn Tỉnh/Thành, Quận/Huyện trước khi tìm trên bản đồ.');
                }
                return;
            }

            const btn = document.getElementById(`${mapId}-btn-geocode`);
            const statusEl = document.getElementById(`${mapId}-status-text`);
            const originalHtml = btn ? btn.innerHTML : '';

            if (btn && !silent) {
                btn.disabled = true;
                btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span><span>Đang tìm...</span>';
            }
            if (statusEl) {
                statusEl.textContent = `Đang định vị: ${address}...`;
            }

            try {
                let successHandled = false;
                const res = await fetch(`/api/ban-do/geocode?address=${encodeURIComponent(address)}`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (res.ok) {
                    const data = await res.json();
                    if (data && data.success && Number.isFinite(parseFloat(data.lat)) && Number.isFinite(parseFloat(data.lng))) {
                        const isFallback = !!data.is_fallback;
                        const zoomLevel = isFallback ? 16 : 17;
                        const msg = isFallback
                            ? `Đã định vị theo khu vực: ${data.matched_query || address} (Kéo ghim để chỉnh chính xác hơn)`
                            : `Đã định vị: ${address} (Kéo ghim để chỉnh chính xác hơn)`;

                        setPosition(
                            mapId,
                            parseFloat(data.lat),
                            parseFloat(data.lng),
                            zoomLevel,
                            msg
                        );
                        successHandled = true;
                    }
                }

                if (!successHandled) {
                    const directResult = await directGeocodeFallback(address);
                    if (directResult) {
                        setPosition(
                            mapId,
                            directResult.lat,
                            directResult.lng,
                            directResult.isFallback ? 16 : 17,
                            directResult.message
                        );
                        successHandled = true;
                    }
                }

                if (!successHandled && !silent) {
                    if (statusEl) statusEl.textContent = 'Không tìm thấy chính xác, hãy kéo ghim trực tiếp trên bản đồ.';
                }
            } catch (err) {
                console.warn('Geocode API error, trying direct browser fallback...', err);
                try {
                    const directResult = await directGeocodeFallback(address);
                    if (directResult) {
                        setPosition(
                            mapId,
                            directResult.lat,
                            directResult.lng,
                            directResult.isFallback ? 16 : 17,
                            directResult.message
                        );
                        return;
                    }
                } catch (e2) {}

                if (statusEl && !silent) {
                    statusEl.textContent = 'Không thể kết nối dịch vụ định vị. Hãy kéo ghim trực tiếp trên bản đồ.';
                }
            } finally {
                if (btn && !silent) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            }
        }

        function locateCurrentGps(mapId) {
            const inst = instances[mapId];
            if (!inst) return;

            if (!navigator.geolocation) {
                alert('Trình duyệt của bạn không hỗ trợ định vị GPS.');
                return;
            }

            const btn = document.getElementById(`${mapId}-btn-gps`);
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span><span>Đang lấy GPS...</span>';
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    }
                    inst.userDraggedPin = true;
                    setPosition(
                        mapId,
                        pos.coords.latitude,
                        pos.coords.longitude,
                        18,
                        'Đã ghim theo vị trí GPS hiện tại của bạn'
                    );
                },
                (err) => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    }
                    alert('Không thể lấy vị trí GPS. Vui lòng cấp quyền truy cập vị trí trên trình duyệt hoặc kéo ghim trực tiếp trên bản đồ.');
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }

        function scheduleAutoGeocode(mapId, delay = 850) {
            if (debounceTimers[mapId]) {
                clearTimeout(debounceTimers[mapId]);
            }
            debounceTimers[mapId] = setTimeout(() => {
                locateFromAddress(mapId, true);
            }, delay);
        }

        function bindAddressListeners(inst) {
            inst.addressSelectors.forEach(selector => {
                const el = document.querySelector(selector);
                if (!el || el.dataset.qmBound === '1') return;
                el.dataset.qmBound = '1';

                el.addEventListener('change', () => scheduleAutoGeocode(inst.mapId, 400));
                if (el.tagName === 'INPUT' && el.type !== 'hidden') {
                    el.addEventListener('input', () => scheduleAutoGeocode(inst.mapId, 900));
                }
            });
        }

        function refresh(mapId) {
            const inst = instances[mapId];
            if (inst && inst.map) {
                setTimeout(() => inst.map.invalidateSize(), 150);
            }
        }

        return {
            init,
            setPosition,
            syncFromInputs,
            locateFromAddress,
            locateCurrentGps,
            scheduleAutoGeocode,
            refresh,
        };
    })();
</script>
@endonce

<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.QMMapPicker?.init({
            mapId: @json($mapId),
            lat: @json($initialLat),
            lng: @json($initialLng),
            latId: @json($latId),
            lngId: @json($lngId),
            addressSelectors: {!! $selectorsJson !!},
        });
    });
</script>
