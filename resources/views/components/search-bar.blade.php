@props([
    'prefix' => 'sb',
    'compact' => false,
    'placeholder' => 'Tìm món ăn, tìm quán...'
])

{{-- ╔══════════════════════════════════════════════════════════════════╗ --}}
{{-- ║  QUANMOI SEARCH BAR COMPONENT                                   ║ --}}
{{-- ║  Tông màu nâu truyền thống (#a04100 / primary)                   ║ --}}
{{-- ╚══════════════════════════════════════════════════════════════════╝ --}}
<div class="relative w-full max-w-3xl mx-auto z-40 search-bar-wrapper" id="{{ $prefix }}-wrapper">
    <form action="{{ route('kham-pha') }}" method="GET" id="{{ $prefix }}-form" class="relative">
        {{-- Hidden location filter inputs --}}
        <input type="hidden" name="tinh_thanh_id" id="{{ $prefix }}-tinh-thanh-id" value="{{ request('tinh_thanh_id') }}">
        <input type="hidden" name="quan_huyen_id" id="{{ $prefix }}-quan-huyen-id" value="{{ request('quan_huyen_id') }}">
        <input type="hidden" name="lat" id="{{ $prefix }}-lat" value="{{ request('lat') }}">
        <input type="hidden" name="lng" id="{{ $prefix }}-lng" value="{{ request('lng', request('lon')) }}">
        <input type="hidden" name="ban_kinh" id="{{ $prefix }}-ban-kinh" value="{{ request('ban_kinh') }}">
        <input type="hidden" name="sap_xep" id="{{ $prefix }}-sap-xep" value="{{ request('sap_xep') }}">

        {{-- Giữ lại các bộ lọc nhanh đang bật khi tìm thêm từ khóa trên trang Khám phá --}}
        @foreach(['danh_muc', 'muc_gia', 'dang_mo_cua', 'co_video', 'is_xac_thuc', 'co_shopeefood', 'is_noi_bat'] as $extraFilter)
            @if(request()->filled($extraFilter))
                <input type="hidden" name="{{ $extraFilter }}" value="{{ request($extraFilter) }}">
            @endif
        @endforeach

        {{-- Main Container Bar --}}
        <div class="bg-white rounded-2xl md:rounded-full p-1.5 md:p-2 shadow-[0_8px_30px_rgba(0,0,0,0.15)] border border-gray-100 flex flex-col md:flex-row items-center gap-2 md:gap-0 transition-all duration-300 hover:shadow-[0_12px_40px_rgba(0,0,0,0.2)]">
            
            {{-- Search Input (Left) --}}
            <div class="flex-1 flex items-center w-full px-2 relative min-w-0">
                <span class="material-symbols-outlined text-gray-400 text-[24px] ml-2 mr-2 flex-shrink-0">search</span>
                <input 
                    type="text" 
                    id="{{ $prefix }}-input" 
                    name="tu_khoa" 
                    value="{{ request('tu_khoa') }}" 
                    autocomplete="off" 
                    class="w-full bg-transparent border-none focus:ring-0 text-gray-800 text-[15px] md:text-[16px] font-medium outline-none placeholder:text-gray-400 py-2.5" 
                    placeholder="{{ $placeholder }}" 
                />
                <button type="button" id="{{ $prefix }}-clear-btn" class="{{ request()->filled('tu_khoa') ? '' : 'hidden' }} text-gray-300 hover:text-gray-500 mr-2 flex-shrink-0 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                </button>
            </div>

            {{-- Location Picker Trigger (Middle) --}}
            <div class="relative w-full md:w-auto flex-shrink-0 mr-1.5">
                <button 
                    type="button" 
                    id="{{ $prefix }}-location-btn" 
                    style="border: 1px solid #e5e7eb;"
                    class="w-full md:w-auto flex items-center justify-between md:justify-center gap-1.5 px-3.5 md:px-4 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-800 font-bold text-[14px] transition-all cursor-pointer border border-gray-200 outline-none focus:outline-none"
                >
                    <span class="material-symbols-outlined text-primary text-[20px] flex-shrink-0" style="color: #a04100; font-variation-settings: 'FILL' 1;">location_on</span>
                    <span id="{{ $prefix }}-location-label" class="truncate max-w-[130px] md:max-w-[140px]">
                        @if(request('quan_huyen_id') || request('tinh_thanh_id'))
                            Đang tải...
                        @elseif(request('lat') && (request('lng') || request('lon')))
                            Gần bạn
                        @else
                            Chọn khu vực
                        @endif
                    </span>
                    <span class="material-symbols-outlined text-gray-400 text-[20px] transition-transform duration-200 flex-shrink-0" id="{{ $prefix }}-location-arrow">arrow_drop_down</span>
                </button>
            </div>

            {{-- Submit Button (Right) - Tông màu nâu QuanMoi (#a04100) --}}
            <button 
                type="submit" 
                style="background-color: #a04100; color: #ffffff;"
                class="w-full md:w-auto active:scale-95 text-white font-bold text-[14.5px] px-6 md:px-7 py-2.5 rounded-xl shadow-md hover:brightness-110 transition-all flex items-center justify-center gap-1.5 flex-shrink-0 cursor-pointer"
            >
                <span>Tìm kiếm</span>
            </button>
        </div>

        {{-- ╔══════════════════════════════════════════════════════════════╗ --}}
        {{-- ║  DROPDOWN 1: GỢI Ý CHO BẠN & AUTOCOMPLETE                     ║ --}}
        {{-- ╚══════════════════════════════════════════════════════════════╝ --}}
        <div 
            id="{{ $prefix }}-suggestions-dropdown" 
            class="absolute top-[calc(100%+8px)] left-0 right-0 w-full bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.18)] border border-gray-100 p-4 md:p-5 hidden z-50 text-left transition-all duration-200 overflow-hidden"
        >
            {{-- AI Suggestions (Hiển thị khi ô tìm kiếm rỗng - tự động gợi ý theo nhu cầu & ngữ cảnh) --}}
            <div id="{{ $prefix }}-default-panel">
                {{-- Header với AI context label --}}
                <div class="flex items-center justify-between mb-2.5 px-1">
                    <h4 class="font-extrabold text-[15px] text-gray-800 flex items-center gap-2">
                        <span class="w-1.5 h-4 rounded-full inline-block" style="background-color: #a04100;"></span>
                        <span id="{{ $prefix }}-ai-label">Gợi ý món ngon cho bạn</span>
                    </h4>
                    <div class="flex items-center gap-2">
                        <span id="{{ $prefix }}-ai-context" class="text-[11.5px] font-semibold px-2.5 py-0.5 rounded-full flex items-center gap-1.5 transition-all duration-300" style="color: #a04100; background-color: rgba(160,65,0,0.08);">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#a04100] animate-pulse"></span>
                            <span>AI đang phân tích...</span>
                        </span>
                    </div>
                </div>

                {{-- Nhu cầu của bạn (AI tự động chọn, có thể đổi theo ý muốn) --}}
                <div class="mb-3 px-0.5">
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-[11.5px] select-none" id="{{ $prefix }}-need-pills">
                        <button type="button" data-need="auto" class="ai-need-pill active-need px-2.5 py-1 rounded-full font-bold transition-all border shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>✨</span>
                            <span>AI Tự động</span>
                        </button>
                        <button type="button" data-need="bua_chinh" class="ai-need-pill px-2.5 py-1 rounded-full font-medium transition-all border shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🍱</span>
                            <span>Bữa no chính</span>
                        </button>
                        <button type="button" data-need="nhanh_gon" class="ai-need-pill px-2.5 py-1 rounded-full font-medium transition-all border shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>⚡</span>
                            <span>Nhanh gọn</span>
                        </button>
                        <button type="button" data-need="thanh_dam" class="ai-need-pill px-2.5 py-1 rounded-full font-medium transition-all border shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🥗</span>
                            <span>Thanh đạm</span>
                        </button>
                        <button type="button" data-need="an_vat" class="ai-need-pill px-2.5 py-1 rounded-full font-medium transition-all border shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🧋</span>
                            <span>Ăn vặt & Nước</span>
                        </button>
                        <button type="button" data-need="lau_nuong" class="ai-need-pill px-2.5 py-1 rounded-full font-medium transition-all border shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🍲</span>
                            <span>Lẩu & Nướng</span>
                        </button>
                        <button type="button" data-need="tiet_kiem" class="ai-need-pill px-2.5 py-1 rounded-full font-medium transition-all border shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>💰</span>
                            <span>Tiết kiệm</span>
                        </button>
                    </div>
                </div>

                {{-- Loading skeleton --}}
                <div id="{{ $prefix }}-ai-loading" class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    @for ($i = 0; $i < 6; $i++)
                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl border border-gray-100 bg-white shadow-2xs animate-pulse">
                        <div class="w-10 h-10 rounded-xl bg-gray-200 flex-shrink-0"></div>
                        <div class="flex-1 space-y-1.5">
                            <div class="h-3 bg-gray-200 rounded-full w-3/4"></div>
                            <div class="h-2 bg-gray-100 rounded-full w-1/2"></div>
                        </div>
                    </div>
                    @endfor
                </div>

                {{-- AI Suggestions Grid (JS rendered) --}}
                <div id="{{ $prefix }}-ai-grid" class="hidden grid grid-cols-2 sm:grid-cols-3 gap-2.5"></div>

                {{-- Footer trợ lý AI --}}
                <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400 px-1">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-[#a04100]">auto_awesome</span>
                        <span>Kira AI tự động gợi ý theo nhu cầu & thời gian</span>
                    </span>
                    <button type="button" onclick="if (typeof toggleAiChatbot === 'function') { toggleAiChatbot(true); }" class="font-semibold text-[#a04100] hover:underline flex items-center gap-0.5 cursor-pointer">
                        <span>Hỏi Kira AI</span>
                        <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                    </button>
                </div>
            </div>

            {{-- Live Search Autocomplete (Hiển thị khi người dùng gõ từ khóa) --}}
            <div id="{{ $prefix }}-live-panel" class="hidden max-h-[360px] overflow-y-auto space-y-2">
                {{-- JS injected --}}
            </div>
        </div>

        {{-- ╔══════════════════════════════════════════════════════════════╗ --}}
        {{-- ║  DROPDOWN 2: CHỌN KHU VỰC (POPOVER THEO MẪU ẢNH)             ║ --}}
        {{-- ╚══════════════════════════════════════════════════════════════╝ --}}
        <div 
            id="{{ $prefix }}-location-panel" 
            class="absolute top-[calc(100%+10px)] right-0 md:right-16 bg-white rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.22)] border border-gray-100 p-5 hidden z-50 w-full sm:w-[380px] text-left transition-all duration-200"
        >
            <h4 class="font-black text-[16px] text-gray-800 text-center pb-3 border-b border-gray-100 mb-4">Khu vực tìm kiếm</h4>

            {{-- Tabs: 2 chế độ loại trừ nhau --}}
            <div class="grid grid-cols-2 gap-1 p-1 bg-gray-100 rounded-xl mb-4" role="tablist">
                <button type="button" role="tab" id="{{ $prefix }}-tab-area" data-mode="area"
                        class="loc-tab flex items-center justify-center gap-1.5 py-2 rounded-lg text-[13px] font-bold transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">map</span>
                    Chọn khu vực
                </button>
                <button type="button" role="tab" id="{{ $prefix }}-tab-gps" data-mode="gps"
                        class="loc-tab flex items-center justify-center gap-1.5 py-2 rounded-lg text-[13px] font-bold transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">near_me</span>
                    Gần bạn
                </button>
            </div>

            {{-- Pane 1: Chọn tỉnh thành / quận huyện --}}
            <div id="{{ $prefix }}-pane-area" role="tabpanel" class="space-y-4">
                <div>
                    <label for="{{ $prefix }}-province-select" class="block text-[13px] font-bold text-gray-700 mb-1.5">Tỉnh / thành phố</label>
                    <div class="relative">
                        <select 
                            id="{{ $prefix }}-province-select" 
                            class="province-select-el w-full h-11 px-3.5 pr-9 rounded-xl border border-gray-200 bg-white text-[14px] font-medium text-gray-800 appearance-none focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all cursor-pointer"
                        >
                            <option value="">Toàn quốc</option>
                            {{-- Nạp động từ provinces.open-api.vn qua /api/dia-chi/tinh-thanh --}}
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-[20px]">arrow_drop_down</span>
                    </div>
                </div>

                <div>
                    <label for="{{ $prefix }}-district-select" class="block text-[13px] font-bold text-gray-700 mb-1.5">Quận / huyện</label>
                    <div class="relative">
                        <select 
                            id="{{ $prefix }}-district-select" 
                            class="district-select-el w-full h-11 px-3.5 pr-9 rounded-xl border border-gray-200 bg-white text-[14px] font-medium text-gray-800 appearance-none focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all cursor-pointer"
                        >
                            <option value="">Tất cả quận/huyện</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-[20px]">arrow_drop_down</span>
                    </div>
                </div>
            </div>

            {{-- Pane 2: Tìm kiếm gần bạn (GPS) --}}
            <div id="{{ $prefix }}-pane-gps" role="tabpanel" class="hidden space-y-3.5">
                <div id="{{ $prefix }}-gps-status" class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 bg-gray-50 text-[12.5px] text-gray-600">
                    <span class="material-symbols-outlined text-[20px] text-gray-400" id="{{ $prefix }}-gps-status-icon">location_searching</span>
                    <span id="{{ $prefix }}-gps-status-text">Chưa lấy vị trí của bạn</span>
                </div>

                <button 
                    type="button" 
                    id="{{ $prefix }}-gps-action-btn" 
                    class="w-full py-2.5 px-3 bg-white border border-primary/30 rounded-lg text-primary font-bold text-[13px] hover:bg-primary/10 flex items-center justify-center gap-1.5 shadow-2xs transition-all active:scale-98 cursor-pointer"
                    style="color: #a04100;"
                >
                    <span class="material-symbols-outlined text-[17px]" style="color: #a04100;">my_location</span>
                    <span id="{{ $prefix }}-gps-btn-text">Lấy vị trí GPS hiện tại</span>
                </button>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-[12px] font-semibold text-gray-600">
                        <label for="{{ $prefix }}-radius-input">Bán kính tìm kiếm:</label>
                        <span id="{{ $prefix }}-radius-display" class="font-extrabold" style="color: #a04100;">3 km</span>
                    </div>
                    <input 
                        type="range" 
                        min="1" 
                        max="15" 
                        value="3" 
                        id="{{ $prefix }}-radius-input" 
                        style="accent-color: #a04100;"
                        class="w-full h-1.5 bg-gray-200 rounded-lg cursor-pointer"
                    >
                    <div class="flex items-center justify-between gap-1 pt-1">
                        <button type="button" class="radius-preset-pill px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-[#a04100]/10 hover:text-primary text-[11px] font-semibold text-gray-600 transition-all cursor-pointer" data-km="1">1 km</button>
                        <button type="button" class="radius-preset-pill px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-[#a04100]/10 hover:text-primary text-[11px] font-semibold text-gray-600 transition-all cursor-pointer" data-km="3">3 km</button>
                        <button type="button" class="radius-preset-pill px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-[#a04100]/10 hover:text-primary text-[11px] font-semibold text-gray-600 transition-all cursor-pointer" data-km="5">5 km</button>
                        <button type="button" class="radius-preset-pill px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-[#a04100]/10 hover:text-primary text-[11px] font-semibold text-gray-600 transition-all cursor-pointer" data-km="10">10 km</button>
                        <button type="button" class="radius-preset-pill px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-[#a04100]/10 hover:text-primary text-[11px] font-semibold text-gray-600 transition-all cursor-pointer" data-km="15">15 km</button>
                    </div>
                </div>

                <p class="text-[11.5px] text-gray-400 leading-relaxed flex gap-1.5">
                    <span class="material-symbols-outlined text-[15px] flex-shrink-0">info</span>
                    Kết quả được sắp xếp từ gần đến xa. Bộ lọc tỉnh/quận sẽ không áp dụng ở chế độ này.
                </p>
            </div>

            {{-- Buttons Action Footer --}}
            <div class="flex items-center gap-2.5 pt-4 mt-4 border-t border-gray-100">
                <button 
                    type="button" 
                    id="{{ $prefix }}-reset-location-btn" 
                    class="flex-1 py-2.5 px-4 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-bold text-[13.5px] transition-all cursor-pointer text-center"
                >
                    Xoá lọc
                </button>
                <button 
                    type="button" 
                    id="{{ $prefix }}-apply-location-btn" 
                    style="background-color: #a04100; color: #ffffff;"
                    class="flex-1 py-2.5 px-4 rounded-xl hover:brightness-110 text-white font-bold text-[13.5px] transition-all shadow-md active:scale-95 cursor-pointer text-center"
                >
                    Áp dụng
                </button>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
    (function() {
        const prefix = '{{ $prefix }}';
        const wrapper = document.getElementById(prefix + '-wrapper');
        if (!wrapper) return;

        // Mã hành chính lấy từ provinces.open-api.vn (v1) qua API nội bộ có cache
        const API_TINH_THANH = '/api/dia-chi/tinh-thanh';
        const API_QUAN_HUYEN = (code) => `/api/dia-chi/quan-huyen/${encodeURIComponent(code)}`;
        const LEGACY_TINH = { HN: '1', HCM: '79', DN: '48', CT: '92', BD: '74', DNAI: '75', VT: '77', HUE: '46', HP: '31', KH: '56', LD: '68' };
        const districtCache = {};
        let provincesPromise = null;

        const form = document.getElementById(prefix + '-form');
        const searchInput = document.getElementById(prefix + '-input');
        const clearBtn = document.getElementById(prefix + '-clear-btn');
        const suggestionsDropdown = document.getElementById(prefix + '-suggestions-dropdown');
        const defaultPanel = document.getElementById(prefix + '-default-panel');
        const livePanel = document.getElementById(prefix + '-live-panel');
        
        const locationBtn = document.getElementById(prefix + '-location-btn');
        const locationPanel = document.getElementById(prefix + '-location-panel');
        const locationArrow = document.getElementById(prefix + '-location-arrow');
        const locationLabel = document.getElementById(prefix + '-location-label');
        
        const hiddenTinhThanh = document.getElementById(prefix + '-tinh-thanh-id');
        const hiddenQuanHuyen = document.getElementById(prefix + '-quan-huyen-id');
        const hiddenLat = document.getElementById(prefix + '-lat');
        const hiddenLng = document.getElementById(prefix + '-lng');
        const hiddenBanKinh = document.getElementById(prefix + '-ban-kinh');
        const hiddenSapXep = document.getElementById(prefix + '-sap-xep');

        const provinceSelect = document.getElementById(prefix + '-province-select');
        const districtSelect = document.getElementById(prefix + '-district-select');
        
        const tabArea = document.getElementById(prefix + '-tab-area');
        const tabGps = document.getElementById(prefix + '-tab-gps');
        const paneArea = document.getElementById(prefix + '-pane-area');
        const paneGps = document.getElementById(prefix + '-pane-gps');
        const gpsStatus = document.getElementById(prefix + '-gps-status');
        const gpsStatusIcon = document.getElementById(prefix + '-gps-status-icon');
        const gpsStatusText = document.getElementById(prefix + '-gps-status-text');
        const gpsActionBtn = document.getElementById(prefix + '-gps-action-btn');
        const gpsBtnText = document.getElementById(prefix + '-gps-btn-text');
        const radiusInput = document.getElementById(prefix + '-radius-input');
        const radiusDisplay = document.getElementById(prefix + '-radius-display');

        const resetLocationBtn = document.getElementById(prefix + '-reset-location-btn');
        const applyLocationBtn = document.getElementById(prefix + '-apply-location-btn');

        let debounceTimer = null;
        let activeTab = 'area';
        let currentLat = null;
        let currentLng = null;

        function switchTab(mode) {
            activeTab = mode;
            if (mode === 'area') {
                if (tabArea) {
                    tabArea.className = 'loc-tab flex items-center justify-center gap-1.5 py-2 rounded-lg text-[13px] transition-all cursor-pointer bg-white text-primary shadow-xs font-extrabold border border-gray-200/60';
                    tabArea.style.color = '#a04100';
                }
                if (tabGps) {
                    tabGps.className = 'loc-tab flex items-center justify-center gap-1.5 py-2 rounded-lg text-[13px] transition-all cursor-pointer text-gray-500 hover:text-gray-900 font-semibold border border-transparent';
                    tabGps.style.color = '';
                }
                if (paneArea) paneArea.classList.remove('hidden');
                if (paneGps) paneGps.classList.add('hidden');
            } else {
                if (tabGps) {
                    tabGps.className = 'loc-tab flex items-center justify-center gap-1.5 py-2 rounded-lg text-[13px] transition-all cursor-pointer bg-white text-rose-600 shadow-xs font-extrabold border border-gray-200/60';
                    tabGps.style.color = '';
                }
                if (tabArea) {
                    tabArea.className = 'loc-tab flex items-center justify-center gap-1.5 py-2 rounded-lg text-[13px] transition-all cursor-pointer text-gray-500 hover:text-gray-900 font-semibold border border-transparent';
                    tabArea.style.color = '';
                }
                if (paneGps) paneGps.classList.remove('hidden');
                if (paneArea) paneArea.classList.add('hidden');
            }
        }

        if (tabArea) tabArea.addEventListener('click', () => switchTab('area'));
        if (tabGps) tabGps.addEventListener('click', () => switchTab('gps'));

        function closeSuggestions() {
            suggestionsDropdown.classList.add('hidden');
            if (typeof stopAiAutoRotate === 'function') {
                stopAiAutoRotate();
            }
        }

        function openSuggestions() {
            closeLocation();
            suggestionsDropdown.classList.remove('hidden');
            // Lazy-load AI suggestions lần đầu tiên hoặc tự động xoay đổi món
            if (typeof loadAiSuggestions === 'function') {
                if (!window['_aiLoaded_' + prefix]) {
                    window['_aiLoaded_' + prefix] = true;
                    loadAiSuggestions();
                } else if (typeof onAiDropdownOpened === 'function') {
                    onAiDropdownOpened();
                }
            }
        }

        function closeLocation() {
            locationPanel.classList.add('hidden');
            locationArrow.classList.remove('rotate-180');
        }

        function toggleLocation(e) {
            e.stopPropagation();
            closeSuggestions();
            const isHidden = locationPanel.classList.contains('hidden');
            if (isHidden) {
                locationPanel.classList.remove('hidden');
                locationArrow.classList.add('rotate-180');
            } else {
                closeLocation();
            }
        }

        // Suggestions events
        searchInput.addEventListener('focus', openSuggestions);
        searchInput.addEventListener('click', openSuggestions);

        searchInput.addEventListener('input', function(e) {
            const val = e.target.value.trim();
            if (val.length > 0) {
                clearBtn.classList.remove('hidden');
                defaultPanel.classList.add('hidden');
                livePanel.classList.remove('hidden');

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchSuggestions(val);
                }, 280);
            } else {
                clearBtn.classList.add('hidden');
                livePanel.classList.add('hidden');
                defaultPanel.classList.remove('hidden');
            }
        });

        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            clearBtn.classList.add('hidden');
            livePanel.classList.add('hidden');
            defaultPanel.classList.remove('hidden');
            searchInput.focus();
        });

        function escapeHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function cleanEmptyInputs() {
            form.querySelectorAll('input[type="hidden"], input[name="tu_khoa"]').forEach(inp => {
                if (!inp.value || inp.value.trim() === '') {
                    inp.disabled = true;
                }
            });
        }

        form.addEventListener('submit', function() {
            cleanEmptyInputs();
        });

        function submitSearchForm() {
            // Lưu từ khóa vào lịch sử tìm kiếm
            const currentVal = searchInput ? searchInput.value.trim() : '';
            if (currentVal && typeof saveSearchToHistory === 'function') {
                saveSearchToHistory(currentVal);
            }
            cleanEmptyInputs();
            form.submit();
        }

        // ════════════════════════════════════════════════════════════
        // AI FOOD SUGGESTIONS - Tự động gợi ý thông minh theo nhu cầu & thời gian
        // ════════════════════════════════════════════════════════════
        const aiLabel       = document.getElementById(prefix + '-ai-label');
        const aiContext     = document.getElementById(prefix + '-ai-context');
        const aiLoading     = document.getElementById(prefix + '-ai-loading');
        const aiGrid        = document.getElementById(prefix + '-ai-grid');
        const aiNeedPills   = document.getElementById(prefix + '-need-pills');

        let currentAiNeed = 'auto';
        const AI_SUGGESTIONS_KEY = 'qm_ai_suggestions_v3_' + prefix;
        const AI_SUGGESTIONS_TTL = 3 * 60 * 1000; // 3 phút cache pool
        const HISTORY_KEY        = 'qm_search_history';

        // Quản lý pool và tự động đổi gợi ý
        let aiSuggestionsPool   = [];
        let aiCurrentSliceIndex = 0;
        let aiAutoRotateTimer   = null;
        let lastAiContextData   = null;
        const AI_DISPLAY_COUNT  = 9;
        const AI_AUTO_ROTATE_INTERVAL = 20000; // Tự động xoay đổi món mỗi 20 giây khi search bar mở

        // Lấy lịch sử tìm kiếm từ localStorage
        function getSearchHistory() {
            try {
                const raw = localStorage.getItem(HISTORY_KEY);
                return raw ? JSON.parse(raw) : [];
            } catch { return []; }
        }

        // Lưu lịch sử tìm kiếm
        function saveSearchToHistory(term) {
            if (!term || term.trim().length < 2) return;
            try {
                let history = getSearchHistory();
                history = [term.trim(), ...history.filter(h => h !== term.trim())].slice(0, 15);
                localStorage.setItem(HISTORY_KEY, JSON.stringify(history));
            } catch { /* ignore */ }
        }

        // Xử lý sự kiện bấm chọn nhu cầu ẩm thực
        if (aiNeedPills) {
            aiNeedPills.querySelectorAll('.ai-need-pill').forEach(pill => {
                pill.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const need = this.getAttribute('data-need') || 'auto';
                    if (need === currentAiNeed) return;

                    currentAiNeed = need;

                    // Cập nhật giao diện pill active
                    aiNeedPills.querySelectorAll('.ai-need-pill').forEach(p => {
                        p.classList.remove('active-need', 'bg-[#a04100]', 'text-white', 'border-[#a04100]', 'shadow-xs', 'font-bold');
                        p.classList.add('bg-gray-50', 'text-gray-700', 'border-gray-200', 'font-medium');
                    });

                    this.classList.remove('bg-gray-50', 'text-gray-700', 'border-gray-200', 'font-medium');
                    this.classList.add('active-need', 'bg-[#a04100]', 'text-white', 'border-[#a04100]', 'shadow-xs', 'font-bold');

                    aiCurrentSliceIndex = 0;
                    loadAiSuggestions(true);
                });
            });
        }

        // Render một item gợi ý món ăn
        function renderAiSuggestionItem(item) {
            const el = document.createElement('div');
            el.className = 'ai-suggestion-item flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-gray-100 hover:border-[#a04100]/30 transition-all cursor-pointer group bg-white shadow-2xs relative';
            el.setAttribute('data-search', item.search_term || item.dish);
            el.setAttribute('data-slug', item.quan_slug || '');

            const defaultImg = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=160&q=80';
            const img = item.image || defaultImg;
            const dishName = escapeHtml(item.dish || '');
            const subtitle = escapeHtml(item.subtitle || 'Quán ngon chuẩn vị');
            const priceText = item.price_formatted ? escapeHtml(item.price_formatted) : '';
            const reason = escapeHtml(item.reason || 'Món ngon nổi bật');
            const badge = escapeHtml(item.badge || 'AI');

            el.innerHTML = `
                <div class="w-11 h-11 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform bg-gray-100 relative">
                    <img src="${escapeHtml(img)}" alt="${dishName}"
                         class="w-full h-full object-cover"
                         loading="lazy"
                         onerror="this.src='${defaultImg}'">
                </div>
                <div class="overflow-hidden min-w-0 flex-1 pr-1">
                    <div class="flex items-center justify-between gap-1">
                        <div class="font-bold text-[13px] md:text-[13.5px] text-gray-800 group-hover:text-primary truncate" style="--tw-text-opacity:1;">${dishName}</div>
                        <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded-md bg-orange-50 text-[#a04100] shrink-0 border border-orange-100/60 leading-none">${badge}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-gray-500 truncate mt-0.5">
                        ${priceText ? `<span class="font-bold text-[#a04100]">${priceText}</span><span class="text-gray-300">·</span>` : ''}
                        <span class="truncate">${subtitle}</span>
                    </div>
                    <div class="text-[10px] text-gray-400 truncate mt-0.5 flex items-center gap-1">
                        <span class="truncate">${reason}</span>
                    </div>
                </div>
            `;

            el.addEventListener('click', function () {
                const searchTerm = this.getAttribute('data-search');
                if (searchTerm) {
                    searchInput.value = searchTerm;
                    saveSearchToHistory(searchTerm);
                    closeSuggestions();
                    submitSearchForm();
                }
            });

            return el;
        }

        // Render các items vào grid
        function renderAiGridItems(items) {
            if (!aiGrid) return;
            aiGrid.innerHTML = '';
            (items || []).forEach(item => {
                aiGrid.appendChild(renderAiSuggestionItem(item));
            });
        }

        // AI tự động đổi món từ pool
        function autoRotateAiDishes(withEffect = true) {
            if (!aiSuggestionsPool || aiSuggestionsPool.length <= 6) return;

            // Xoay vòng index lát cắt
            aiCurrentSliceIndex = (aiCurrentSliceIndex + 6) % aiSuggestionsPool.length;

            const count = Math.min(AI_DISPLAY_COUNT, aiSuggestionsPool.length);
            const rotated = [];
            for (let i = 0; i < count; i++) {
                const idx = (aiCurrentSliceIndex + i) % aiSuggestionsPool.length;
                rotated.push(aiSuggestionsPool[idx]);
            }

            if (withEffect && aiGrid) {
                aiGrid.style.transition = 'opacity 0.25s ease';
                aiGrid.style.opacity = '0.35';
                setTimeout(() => {
                    renderAiGridItems(rotated);
                    aiGrid.style.opacity = '1';
                }, 200);

                // Hiệu ứng thông báo AI tự đổi món
                if (aiContext && lastAiContextData) {
                    aiContext.innerHTML = '<span class="shrink-0 animate-pulse text-[12px]">✨</span><span>AI tự đổi món mới</span>';
                    setTimeout(() => {
                        if (lastAiContextData && aiContext) {
                            aiContext.innerHTML = `<span class="shrink-0">${lastAiContextData.icon || '✨'}</span><span>${escapeHtml(lastAiContextData.label || 'Gợi ý cho bạn')}</span>`;
                        }
                    }, 1800);
                }
            } else {
                renderAiGridItems(rotated);
            }
        }

        // Bắt đầu chu kỳ AI tự động đổi món
        function startAiAutoRotate() {
            stopAiAutoRotate();
            aiAutoRotateTimer = setInterval(() => {
                autoRotateAiDishes(true);
            }, AI_AUTO_ROTATE_INTERVAL);
        }

        // Dừng chu kỳ AI tự đổi món khi đóng dropdown
        function stopAiAutoRotate() {
            if (aiAutoRotateTimer) {
                clearInterval(aiAutoRotateTimer);
                aiAutoRotateTimer = null;
            }
        }

        // Kích hoạt khi người dùng mở lại dropdown tìm kiếm
        function onAiDropdownOpened() {
            if (aiSuggestionsPool && aiSuggestionsPool.length > 6) {
                autoRotateAiDishes(false);
            }
            startAiAutoRotate();
        }

        // Load AI suggestions từ server
        function loadAiSuggestions(force = false) {
            const cacheKey = AI_SUGGESTIONS_KEY + '_' + currentAiNeed;

            // Kiểm tra cache localStorage nếu không phải force
            if (!force) {
                try {
                    const cached = localStorage.getItem(cacheKey);
                    if (cached) {
                        const parsed = JSON.parse(cached);
                        const age = Date.now() - (parsed.timestamp || 0);
                        if (age < AI_SUGGESTIONS_TTL && Array.isArray(parsed.suggestions) && parsed.suggestions.length > 0) {
                            aiSuggestionsPool   = parsed.suggestions;
                            lastAiContextData   = parsed.context;
                            aiCurrentSliceIndex = 0;
                            renderAiSuggestions(parsed);
                            startAiAutoRotate();
                            return;
                        }
                    }
                } catch { /* ignore cache error */ }
            }

            // Hiện loading skeleton, ẩn grid
            if (aiLoading) aiLoading.classList.remove('hidden');
            if (aiGrid) aiGrid.classList.add('hidden');
            if (aiContext) {
                aiContext.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-[#a04100] animate-pulse"></span> AI đang phân tích...';
            }

            // Gọi API lấy pool 18 món
            const now = new Date();
            const params = new URLSearchParams({
                need: currentAiNeed,
                hour: now.getHours(),
                day_of_week: now.getDay(),
                limit: 18,
            });

            // Thêm lịch sử tìm kiếm
            const history = getSearchHistory();
            if (history.length > 0) {
                params.set('history', JSON.stringify(history.slice(0, 10)));
            }

            // Thêm location nếu người dùng đã chọn
            if (hiddenTinhThanh && hiddenTinhThanh.value) {
                params.set('location', hiddenTinhThanh.value);
            }

            fetch(`/api/ai-food-suggestions?${params.toString()}`)
                .then(res => res.json())
                .then(res => {
                    if (!res.success) return;

                    aiSuggestionsPool   = Array.isArray(res.suggestions) ? res.suggestions : [];
                    lastAiContextData   = res.context;
                    aiCurrentSliceIndex = 0;

                    // Lưu cache pool
                    try {
                        localStorage.setItem(cacheKey, JSON.stringify({
                            timestamp: Date.now(),
                            suggestions: aiSuggestionsPool,
                            context: res.context,
                        }));
                    } catch { /* ignore */ }

                    renderAiSuggestions({ suggestions: aiSuggestionsPool, context: res.context });
                    startAiAutoRotate();
                })
                .catch(() => {
                    if (aiLoading) aiLoading.classList.add('hidden');
                    if (aiGrid) aiGrid.classList.remove('hidden');
                    if (aiContext) aiContext.textContent = 'Gợi ý nổi bật';
                });
        }

        // Render kết quả AI suggestions vào grid
        function renderAiSuggestions(data) {
            if (!aiGrid) return;

            // Cập nhật label context
            if (aiContext && data.context) {
                lastAiContextData = data.context;
                aiContext.innerHTML = `<span class="shrink-0">${data.context.icon || '✨'}</span><span>${escapeHtml(data.context.label || 'Gợi ý cho bạn')}</span>`;
            }
            if (aiLabel && data.context && data.context.message) {
                aiLabel.textContent = data.context.message;
            }

            // Render batch món đầu tiên
            const suggestions = Array.isArray(data.suggestions) ? data.suggestions : [];
            renderAiGridItems(suggestions.slice(0, AI_DISPLAY_COUNT));

            // Ẩn loading skeleton, hiện grid
            if (aiLoading) aiLoading.classList.add('hidden');
            aiGrid.classList.remove('hidden');
        }


        function fetchSuggestions(keyword) {
            livePanel.innerHTML = '<div class="py-4 text-center text-gray-400 text-[13px] flex items-center justify-center gap-2"><span class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin" style="border-color: #a04100; border-top-color: transparent;"></span>Đang tìm gợi ý...</div>';

            const params = new URLSearchParams({ q: keyword });
            if (hiddenTinhThanh && hiddenTinhThanh.value) params.set('tinh_thanh_id', hiddenTinhThanh.value);
            if (hiddenQuanHuyen && hiddenQuanHuyen.value) params.set('quan_huyen_id', hiddenQuanHuyen.value);
            if (hiddenLat && hiddenLat.value) params.set('lat', hiddenLat.value);
            if (hiddenLng && hiddenLng.value) params.set('lng', hiddenLng.value);
            if (hiddenBanKinh && hiddenBanKinh.value) params.set('ban_kinh', hiddenBanKinh.value);

            fetch(`/api/search/suggest?${params.toString()}`)
                .then(res => res.json())
                .then(res => {
                    if (!res.success) return;
                    renderLiveSuggestions(res.data, keyword);
                })
                .catch(() => {
                    livePanel.innerHTML = `<div class="p-3 text-center text-gray-400 text-[13px]">Không thể tải gợi ý.</div>`;
                });
        }

        function renderLiveSuggestions(data, keyword) {
            let html = '';
            const safeKeyword = escapeHtml(keyword);

            // 1. Món ăn gợi ý
            if (data.mons && data.mons.length > 0) {
                html += `
                    <div class="px-2 py-1 text-[11px] font-extrabold text-primary uppercase tracking-wider flex items-center gap-1.5" style="color: #a04100;">
                        <span class="material-symbols-outlined text-[15px]">restaurant_menu</span>
                        Món ăn phù hợp
                    </div>
                `;
                data.mons.forEach(mon => {
                    const priceFormatted = mon.gia ? new Intl.NumberFormat('vi-VN').format(mon.gia) + 'đ' : '';
                    const tenMon = escapeHtml(mon.ten_mon);
                    const tenQuan = escapeHtml(mon.ten_quan);
                    const quanSlug = encodeURIComponent(mon.quan_slug || '');
                    const img = escapeHtml(mon.hinh_anh || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=80&q=80');
                    html += `
                        <a href="/quan/${quanSlug}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-transparent hover:border-[#a04100]/20 transition-all group">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                    <img src="${img}" alt="${tenMon}" class="w-full h-full object-cover">
                                </div>
                                <div class="truncate">
                                    <div class="text-[13.5px] font-bold text-gray-800 group-hover:text-primary truncate" style="--tw-text-opacity: 1;">${tenMon}</div>
                                    <div class="text-[11.5px] text-gray-400 truncate">${tenQuan}</div>
                                </div>
                            </div>
                            ${priceFormatted ? `<span class="text-[12px] font-extrabold text-primary ml-2 flex-shrink-0 bg-primary/10 px-2 py-0.5 rounded-md" style="color: #a04100; background-color: rgba(160,65,0,0.1);">${priceFormatted}</span>` : ''}
                        </a>
                    `;
                });
            }

            // 2. Quán ăn gợi ý
            if (data.quans && data.quans.length > 0) {
                html += `
                    <div class="px-2 pt-2.5 pb-1 text-[11px] font-extrabold text-primary uppercase tracking-wider flex items-center gap-1.5" style="color: #a04100;">
                        <span class="material-symbols-outlined text-[15px]">storefront</span>
                        Quán ăn liên quan
                    </div>
                `;
                data.quans.forEach(quan => {
                    const img = escapeHtml(quan.anh_bia || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=120&q=80');
                    const tenQuan = escapeHtml(quan.ten_quan);
                    const subInfo = escapeHtml(quan.dia_chi || quan.loai_hinh_kinh_doanh || '');
                    const slug = encodeURIComponent(quan.slug || '');
                    html += `
                        <a href="/quan/${slug}" class="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-transparent hover:border-[#a04100]/20 transition-all group">
                            <img src="${img}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0 bg-gray-100 border border-gray-100" alt="${tenQuan}">
                            <div class="overflow-hidden min-w-0">
                                <div class="text-[13.5px] font-bold text-gray-800 group-hover:text-primary truncate">${tenQuan}</div>
                                <div class="text-[11.5px] text-gray-400 truncate flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">location_on</span>
                                    ${subInfo}
                                </div>
                            </div>
                        </a>
                    `;
                });
            }

            if (!data.mons?.length && !data.quans?.length) {
                html += `
                    <div class="py-6 text-center text-gray-500 text-[13.5px]">
                        Không tìm thấy món ăn hoặc quán nào khớp với "<strong>${safeKeyword}</strong>"
                    </div>
                `;
            }

            // Footer action
            html += `
                <div class="pt-2 border-t border-gray-100 mt-2">
                    <button type="submit" class="w-full py-2.5 text-center text-[13px] font-bold text-primary hover:bg-[#a04100]/5 rounded-xl transition-all cursor-pointer" style="color: #a04100;">
                        Xem tất cả kết quả cho "${safeKeyword}" →
                    </button>
                </div>
            `;

            livePanel.innerHTML = html;
        }

        // Location events
        locationBtn.addEventListener('click', function(e) {
            toggleLocation(e);
            loadProvinces();
        });

        function setOptions(select, placeholder, items, selected) {
            select.innerHTML = '';
            const first = document.createElement('option');
            first.value = '';
            first.textContent = placeholder;
            select.appendChild(first);
            items.forEach(item => {
                const opt = document.createElement('option');
                opt.value = String(item.code);
                opt.textContent = item.name;
                if (selected !== undefined && selected !== '' && Number(selected) === Number(item.code)) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        }

        function loadProvinces(selected) {
            if (!provincesPromise) {
                provinceSelect.disabled = true;
                provincesPromise = fetch(API_TINH_THANH)
                    .then(res => res.json())
                    .then(res => (res.success && Array.isArray(res.data)) ? res.data : [])
                    .catch(() => [])
                    .finally(() => { provinceSelect.disabled = false; });
            }
            return provincesPromise.then(list => {
                if (provinceSelect.options.length <= 1 && list.length) {
                    setOptions(provinceSelect, 'Toàn quốc', list, selected ?? hiddenTinhThanh.value);
                }
                return list;
            });
        }

        function loadDistricts(provCode, selected) {
            if (!provCode) {
                setOptions(districtSelect, 'Tất cả quận/huyện', []);
                return Promise.resolve([]);
            }
            districtSelect.disabled = true;
            districtSelect.innerHTML = '<option value="">Đang tải...</option>';
            if (!districtCache[provCode]) {
                districtCache[provCode] = fetch(API_QUAN_HUYEN(provCode))
                    .then(res => res.json())
                    .then(res => (res.success && Array.isArray(res.data)) ? res.data : [])
                    .catch(() => { delete districtCache[provCode]; return []; });
            }
            return districtCache[provCode].then(list => {
                setOptions(districtSelect, 'Tất cả quận/huyện', list, selected);
                districtSelect.disabled = false;
                return list;
            });
        }

        provinceSelect.addEventListener('change', function() {
            loadDistricts(this.value);
        });

        // Khôi phục lựa chọn + nhãn khi trang được tải với bộ lọc khu vực
        (function restoreLocation() {
            const hasGps = hiddenLat.value && (hiddenLng.value || '{{ request('lon') }}');
            let tinh = hiddenTinhThanh.value;
            const huyen = hiddenQuanHuyen.value;

            if (hasGps) {
                currentLat = hiddenLat.value;
                currentLng = hiddenLng.value || '{{ request('lon') }}';
                const r = hiddenBanKinh?.value || '3';
                locationLabel.textContent = `Gần bạn (< ${r}km)`;

                if (gpsStatus) gpsStatus.className = 'flex items-center gap-2.5 p-3 rounded-xl border border-emerald-200 bg-emerald-50 text-[12.5px] text-emerald-800 font-medium';
                if (gpsStatusIcon) {
                    gpsStatusIcon.className = 'material-symbols-outlined text-[20px] text-emerald-600';
                    gpsStatusIcon.textContent = 'my_location';
                }
                if (gpsBtnText) gpsBtnText.textContent = 'Lấy lại vị trí';

                syncGpsRadius(r);
                switchTab('gps');
            } else if (tinh) {
                if (LEGACY_TINH[tinh.toUpperCase()]) {
                    tinh = LEGACY_TINH[tinh.toUpperCase()];
                    hiddenTinhThanh.value = tinh;
                }
                loadProvinces(tinh).then(list => {
                    const p = list.find(x => Number(x.code) === Number(tinh));
                    if (!huyen) {
                        locationLabel.textContent = p ? p.name : 'Khu vực đã chọn';
                        return loadDistricts(tinh);
                    }
                    return loadDistricts(tinh, huyen).then(dl => {
                        const d = dl.find(x => Number(x.code) === Number(huyen));
                        locationLabel.textContent = d ? d.name : (p ? p.name : 'Khu vực đã chọn');
                    });
                });
                switchTab('area');
            } else {
                syncGpsRadius('3');
                switchTab('area');
            }
        })();

        // Hàm đồng bộ bán kính tìm kiếm: slider, số hiển thị, nút chọn nhanh và hộp trạng thái GPS
        function syncGpsRadius(val) {
            const r = String(val);
            if (radiusInput && radiusInput.value !== r) {
                radiusInput.value = r;
            }
            if (radiusDisplay) {
                radiusDisplay.textContent = r + ' km';
            }

            // Đồng bộ trực tiếp dòng trạng thái GPS: "Đang lọc gần bạn (X km)"
            const hasCoords = currentLat || (hiddenLat && hiddenLat.value);
            if (hasCoords && gpsStatusText) {
                gpsStatusText.textContent = `Đang lọc gần bạn (${r} km)`;
            }

            // Đồng bộ trạng thái active của các nút chọn nhanh 1km, 3km, 5km...
            wrapper.querySelectorAll('.radius-preset-pill').forEach(btn => {
                if (btn.getAttribute('data-km') === r) {
                    btn.classList.add('bg-[#a04100]', 'text-white', 'font-extrabold', 'shadow-xs');
                    btn.classList.remove('bg-gray-100', 'text-gray-600');
                } else {
                    btn.classList.remove('bg-[#a04100]', 'text-white', 'font-extrabold', 'shadow-xs');
                    btn.classList.add('bg-gray-100', 'text-gray-600');
                }
            });
        }

        // Bắt sự kiện kéo thanh trượt bán kính
        if (radiusInput) {
            radiusInput.addEventListener('input', function() {
                syncGpsRadius(this.value);
            });
        }

        // Bắt sự kiện bấm các nút chọn nhanh 1km, 3km, 5km, 10km, 15km
        wrapper.querySelectorAll('.radius-preset-pill').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const km = this.getAttribute('data-km');
                if (km) {
                    syncGpsRadius(km);
                }
            });
        });

        if (gpsActionBtn) {
            gpsActionBtn.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Trình duyệt không hỗ trợ định vị GPS');
                    return;
                }

                if (gpsBtnText) gpsBtnText.textContent = 'Đang lấy vị trí GPS...';
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        currentLat = pos.coords.latitude;
                        currentLng = pos.coords.longitude;
                        if (gpsStatus) gpsStatus.className = 'flex items-center gap-2.5 p-3 rounded-xl border border-emerald-200 bg-emerald-50 text-[12.5px] text-emerald-800 font-medium';
                        if (gpsStatusIcon) {
                            gpsStatusIcon.className = 'material-symbols-outlined text-[20px] text-emerald-600';
                            gpsStatusIcon.textContent = 'my_location';
                        }
                        if (gpsBtnText) gpsBtnText.textContent = 'Lấy lại vị trí';
                        syncGpsRadius(radiusInput ? radiusInput.value : '3');
                    },
                    (err) => {
                        if (gpsBtnText) gpsBtnText.textContent = 'Lấy vị trí GPS hiện tại';
                        alert('Vui lòng bật quyền truy cập vị trí trên thiết bị của bạn');
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            });
        }

        if (resetLocationBtn) {
            resetLocationBtn.addEventListener('click', function() {
                // Xoá toàn bộ cả 2 chế độ
                provinceSelect.value = '';
                districtSelect.innerHTML = '<option value="">Tất cả quận/huyện</option>';
                hiddenTinhThanh.value = '';
                hiddenQuanHuyen.value = '';

                hiddenLat.value = '';
                hiddenLng.value = '';
                if (hiddenBanKinh) hiddenBanKinh.value = '';
                if (hiddenSapXep.value === 'near_me') hiddenSapXep.value = '';
                currentLat = null;
                currentLng = null;

                if (gpsStatus) gpsStatus.className = 'flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 bg-gray-50 text-[12.5px] text-gray-600';
                if (gpsStatusIcon) {
                    gpsStatusIcon.className = 'material-symbols-outlined text-[20px] text-gray-400';
                    gpsStatusIcon.textContent = 'location_searching';
                }
                if (gpsStatusText) gpsStatusText.textContent = 'Chưa lấy vị trí của bạn';
                if (gpsBtnText) gpsBtnText.textContent = 'Lấy vị trí GPS hiện tại';
                syncGpsRadius('3');

                locationLabel.textContent = 'Chọn khu vực';
                switchTab('area');
                closeLocation();

                if (prefix === 'kp') {
                    submitSearchForm();
                }
            });
        }

        if (applyLocationBtn) {
            applyLocationBtn.addEventListener('click', function() {
                if (activeTab === 'area') {
                    // ÁP DỤNG KHU VỰC HÀNH CHÍNH -> XÓA SẠCH GPS ĐỂ TÁCH BIỆT HOÀN TOÀN
                    const provVal = provinceSelect.value;
                    const distVal = districtSelect.value;

                    hiddenLat.value = '';
                    hiddenLng.value = '';
                    if (hiddenBanKinh) hiddenBanKinh.value = '';
                    if (hiddenSapXep.value === 'near_me') hiddenSapXep.value = '';
                    currentLat = null;
                    currentLng = null;

                    if (distVal) {
                        hiddenQuanHuyen.value = distVal;
                        hiddenTinhThanh.value = provVal;
                        locationLabel.textContent = districtSelect.options[districtSelect.selectedIndex]?.text || distVal;
                    } else if (provVal) {
                        hiddenTinhThanh.value = provVal;
                        hiddenQuanHuyen.value = '';
                        locationLabel.textContent = provinceSelect.options[provinceSelect.selectedIndex]?.text || provVal;
                    } else {
                        hiddenTinhThanh.value = '';
                        hiddenQuanHuyen.value = '';
                        locationLabel.textContent = 'Chọn khu vực';
                    }
                } else {
                    // ÁP DỤNG GPS GẦN BẠN -> XÓA SẠCH TỈNH / HUYỆN ĐỂ TÁCH BIỆT HOÀN TOÀN
                    const lat = currentLat || hiddenLat.value;
                    const lng = currentLng || hiddenLng.value;

                    if (!lat || !lng) {
                        alert('Vui lòng bấm "Lấy vị trí GPS hiện tại" trước khi áp dụng');
                        return;
                    }

                    hiddenTinhThanh.value = '';
                    hiddenQuanHuyen.value = '';
                    provinceSelect.value = '';
                    districtSelect.innerHTML = '<option value="">Tất cả quận/huyện</option>';

                    hiddenLat.value = lat;
                    hiddenLng.value = lng;
                    const radius = radiusInput?.value || '3';
                    if (hiddenBanKinh) hiddenBanKinh.value = radius;
                    hiddenSapXep.value = 'near_me';

                    locationLabel.textContent = `Gần bạn (< ${radius}km)`;
                }

                closeLocation();

                if (prefix === 'kp') {
                    submitSearchForm();
                }
            });
        }

        // Click outside listener
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                closeSuggestions();
                closeLocation();
            }
        });

        // ESC key listener
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuggestions();
                closeLocation();
            }
        });
    })();
</script>
@endpush
