@props([
    'prefix' => 'sb',
    'compact' => false
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
        <input type="hidden" name="lng" id="{{ $prefix }}-lng" value="{{ request('lng') }}">
        <input type="hidden" name="sap_xep" id="{{ $prefix }}-sap-xep" value="{{ request('sap_xep') }}">

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
                    placeholder="Tìm món ăn..." 
                />
                <button type="button" id="{{ $prefix }}-clear-btn" class="hidden text-gray-300 hover:text-gray-500 mr-2 flex-shrink-0 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                </button>
            </div>

            {{-- Location Picker Trigger (Middle) --}}
            <div class="relative w-full md:w-auto flex-shrink-0 mr-1.5">
                <button 
                    type="button" 
                    id="{{ $prefix }}-location-btn" 
                    class="w-full md:w-auto flex items-center justify-between md:justify-center gap-1.5 px-3.5 md:px-4 py-2 rounded-xl hover:bg-gray-50 text-gray-800 font-bold text-[14px] transition-all cursor-pointer border border-gray-200/90 shadow-2xs"
                >
                    <span class="material-symbols-outlined text-primary text-[20px] flex-shrink-0" style="color: #a04100; font-variation-settings: 'FILL' 1;">location_on</span>
                    <span id="{{ $prefix }}-location-label" class="truncate max-w-[130px] md:max-w-[140px]">
                        @if(request('quan_huyen_id'))
                            {{ request('quan_huyen_id') }}
                        @elseif(request('tinh_thanh_id'))
                            {{ request('tinh_thanh_id') }}
                        @elseif(request('lat') && request('lng'))
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
            {{-- Default Suggestions (Hiển thị khi ô tìm kiếm rỗng) --}}
            <div id="{{ $prefix }}-default-panel">
                <div class="flex items-center justify-between mb-3 px-1">
                    <h4 class="font-extrabold text-[15px] text-gray-800 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-primary rounded-full inline-block" style="background-color: #a04100;"></span>
                        Gợi ý cho bạn
                    </h4>
                    <span class="text-[12px] text-gray-400 font-medium">Món ngon nổi bật</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    {{-- 1. Phở bò (Có badge Đã xem gần đây) --}}
                    <div data-dish="Phở bò" class="suggestion-item flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-gray-100 hover:border-[#a04100]/30 transition-all cursor-pointer group bg-white shadow-2xs">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1582878826629-29b7ad1cb43f?auto=format&fit=crop&w=80&q=80" alt="Phở bò" class="w-full h-full object-cover">
                        </div>
                        <div class="overflow-hidden min-w-0">
                            <div class="font-bold text-[13px] md:text-[13.5px] text-gray-800 group-hover:text-primary truncate" style="--tw-text-opacity: 1;">Phở bò</div>
                            <div class="text-[10px] text-red-500 font-bold flex items-center gap-1 truncate"><span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>Đã xem gần đây</div>
                        </div>
                    </div>

                    {{-- 2. Cơm tấm --}}
                    <div data-dish="Cơm tấm" class="suggestion-item flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-gray-100 hover:border-[#a04100]/30 transition-all cursor-pointer group bg-white shadow-2xs">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1626804475297-41609ea064eb?auto=format&fit=crop&w=80&q=80" alt="Cơm tấm" class="w-full h-full object-cover">
                        </div>
                        <div class="overflow-hidden min-w-0">
                            <div class="font-bold text-[13px] md:text-[13.5px] text-gray-800 group-hover:text-primary truncate">Cơm tấm</div>
                            <div class="text-[11px] text-gray-400 truncate">Bữa chính đậm đà</div>
                        </div>
                    </div>

                    {{-- 3. Trà sữa --}}
                    <div data-dish="Trà sữa" class="suggestion-item flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-gray-100 hover:border-[#a04100]/30 transition-all cursor-pointer group bg-white shadow-2xs">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1558855567-1a4365318db5?auto=format&fit=crop&w=80&q=80" alt="Trà sữa" class="w-full h-full object-cover">
                        </div>
                        <div class="overflow-hidden min-w-0">
                            <div class="font-bold text-[13px] md:text-[13.5px] text-gray-800 group-hover:text-primary truncate">Trà sữa</div>
                            <div class="text-[11px] text-gray-400 truncate">Giải nhiệt cực đã</div>
                        </div>
                    </div>

                    {{-- 4. Bún đậu mắm tôm --}}
                    <div data-dish="Bún đậu mắm tôm" class="suggestion-item flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-gray-100 hover:border-[#a04100]/30 transition-all cursor-pointer group bg-white shadow-2xs">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1564834724105-918b73d1b9e0?auto=format&fit=crop&w=80&q=80" alt="Bún đậu mắm tôm" class="w-full h-full object-cover">
                        </div>
                        <div class="overflow-hidden min-w-0">
                            <div class="font-bold text-[13px] md:text-[13.5px] text-gray-800 group-hover:text-primary truncate">Bún đậu mắm tôm</div>
                            <div class="text-[11px] text-gray-400 truncate">Đậm đà giòn rụm</div>
                        </div>
                    </div>

                    {{-- 5. Cà phê --}}
                    <div data-dish="Cà phê" class="suggestion-item flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-gray-100 hover:border-[#a04100]/30 transition-all cursor-pointer group bg-white shadow-2xs">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=80&q=80" alt="Cà phê" class="w-full h-full object-cover">
                        </div>
                        <div class="overflow-hidden min-w-0">
                            <div class="font-bold text-[13px] md:text-[13.5px] text-gray-800 group-hover:text-primary truncate">Cà phê</div>
                            <div class="text-[11px] text-gray-400 truncate">Hẹn hò, trò chuyện</div>
                        </div>
                    </div>

                    {{-- 6. Lẩu & Nướng --}}
                    <div data-dish="Lẩu nướng" class="suggestion-item flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-gray-100 hover:border-[#a04100]/30 transition-all cursor-pointer group bg-white shadow-2xs">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1544025162-811114215438?auto=format&fit=crop&w=80&q=80" alt="Lẩu & Nướng" class="w-full h-full object-cover">
                        </div>
                        <div class="overflow-hidden min-w-0">
                            <div class="font-bold text-[13px] md:text-[13.5px] text-gray-800 group-hover:text-primary truncate">Lẩu & Nướng</div>
                            <div class="text-[11px] text-gray-400 truncate">Tiệc tùng tụ tập</div>
                        </div>
                    </div>

                    {{-- 7. Bánh mì --}}
                    <div data-dish="Bánh mì" class="suggestion-item flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-gray-100 hover:border-[#a04100]/30 transition-all cursor-pointer group bg-white shadow-2xs">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1509722747041-616f39b57569?auto=format&fit=crop&w=80&q=80" alt="Bánh mì" class="w-full h-full object-cover">
                        </div>
                        <div class="overflow-hidden min-w-0">
                            <div class="font-bold text-[13px] md:text-[13.5px] text-gray-800 group-hover:text-primary truncate">Bánh mì</div>
                            <div class="text-[11px] text-gray-400 truncate">Nhanh gọn nóng hổi</div>
                        </div>
                    </div>

                    {{-- 8. Đồ ăn vặt --}}
                    <div data-dish="Đồ ăn vặt" class="suggestion-item flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-gray-100 hover:border-[#a04100]/30 transition-all cursor-pointer group bg-white shadow-2xs">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1518013431119-2d4f2603893c?auto=format&fit=crop&w=80&q=80" alt="Đồ ăn vặt" class="w-full h-full object-cover">
                        </div>
                        <div class="overflow-hidden min-w-0">
                            <div class="font-bold text-[13px] md:text-[13.5px] text-gray-800 group-hover:text-primary truncate">Đồ ăn vặt</div>
                            <div class="text-[11px] text-gray-400 truncate">Ngon rẻ mê ly</div>
                        </div>
                    </div>

                    {{-- 9. Hải sản --}}
                    <div data-dish="Hải sản" class="suggestion-item flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-gray-100 hover:border-[#a04100]/30 transition-all cursor-pointer group bg-white shadow-2xs">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&fit=crop&w=80&q=80" alt="Hải sản" class="w-full h-full object-cover">
                        </div>
                        <div class="overflow-hidden min-w-0">
                            <div class="font-bold text-[13px] md:text-[13.5px] text-gray-800 group-hover:text-primary truncate">Hải sản</div>
                            <div class="text-[11px] text-gray-400 truncate">Tươi sống thả ga</div>
                        </div>
                    </div>
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
            class="absolute top-[calc(100%+10px)] right-0 md:right-16 bg-white rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.22)] border border-gray-100 p-5 hidden z-50 w-full sm:w-[360px] text-left transition-all duration-200"
        >
            <h4 class="font-black text-[16px] text-gray-800 text-center pb-3 border-b border-gray-100 mb-4">Khu vực</h4>
            
            <div class="space-y-4">
                {{-- Chọn tỉnh thành * --}}
                <div>
                    <label class="block text-[13px] font-bold text-gray-700 mb-1.5">
                        Chọn tỉnh thành <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="{{ $prefix }}-province-select" 
                            class="province-select-el w-full h-11 px-3.5 pr-9 rounded-xl border border-gray-200 bg-white text-[14px] font-medium text-gray-800 appearance-none focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all cursor-pointer"
                        >
                            <option value="">Toàn quốc</option>
                            <option value="HCM">Hồ Chí Minh</option>
                            <option value="HN">Hà Nội</option>
                            <option value="DN">Đà Nẵng</option>
                            <option value="CT">Cần Thơ</option>
                            <option value="BD">Bình Dương</option>
                            <option value="DNai">Đồng Nai</option>
                            <option value="VT">Bà Rịa - Vũng Tàu</option>
                            <option value="HUE">Thừa Thiên Huế</option>
                            <option value="HP">Hải Phòng</option>
                            <option value="KH">Khánh Hòa</option>
                            <option value="LD">Lâm Đồng</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-[20px]">arrow_drop_down</span>
                    </div>
                </div>

                {{-- Chọn quận huyện * --}}
                <div>
                    <label class="block text-[13px] font-bold text-gray-700 mb-1.5">
                        Chọn quận huyện <span class="text-red-500">*</span>
                    </label>
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

                {{-- Tìm kiếm gần bạn (Vị trí & GPS) --}}
                <div class="pt-2 border-t border-gray-100">
                    <button 
                        type="button" 
                        id="{{ $prefix }}-gps-toggle-btn" 
                        class="w-full flex items-center justify-between text-left py-1 text-gray-800 hover:text-primary transition-colors cursor-pointer"
                    >
                        <span class="flex items-center gap-1.5 font-bold text-[14px]">
                            <span class="material-symbols-outlined text-primary text-[19px]" style="color: #a04100;">near_me</span>
                            Tìm kiếm gần bạn
                        </span>
                        <span class="material-symbols-outlined text-[19px] text-gray-400 transition-transform duration-200" id="{{ $prefix }}-gps-chevron">expand_more</span>
                    </button>
                    
                    <div id="{{ $prefix }}-gps-controls" class="hidden mt-2.5 p-3.5 bg-primary/5 rounded-xl border border-primary/15 space-y-3">
                        <button 
                            type="button" 
                            id="{{ $prefix }}-gps-action-btn" 
                            class="w-full py-2.5 px-3 bg-white border border-primary/30 rounded-lg text-primary font-bold text-[12.5px] hover:bg-primary/10 flex items-center justify-center gap-1.5 shadow-2xs transition-all active:scale-98 cursor-pointer"
                            style="color: #a04100;"
                        >
                            <span class="material-symbols-outlined text-[17px] text-primary" style="color: #a04100;">my_location</span>
                            <span id="{{ $prefix }}-gps-btn-text">Lấy vị trí GPS hiện tại</span>
                        </button>
                        
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-[12px] font-semibold text-gray-600">
                                <span>Bán kính tìm kiếm:</span>
                                <span id="{{ $prefix }}-radius-display" class="font-extrabold text-primary" style="color: #a04100;">3 km</span>
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
                            <div class="flex justify-between text-[10px] text-gray-400 font-medium">
                                <span>1 km</span>
                                <span>5 km</span>
                                <span>10 km</span>
                                <span>15 km</span>
                            </div>
                        </div>
                    </div>
                </div>
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

        const districtsMap = {
            'HCM': ['Quận 1', 'Quận 3', 'Quận 4', 'Quận 5', 'Quận 7', 'Quận 10', 'Quận Bình Thạnh', 'Quận Tân Bình', 'Quận Gò Vấp', 'TP. Thủ Đức'],
            'HN': ['Quận Hoàn Kiếm', 'Quận Ba Đình', 'Quận Đống Đa', 'Quận Cầu Giấy', 'Quận Hai Bà Trưng', 'Quận Tây Hồ', 'Quận Thanh Xuân'],
            'DN': ['Quận Hải Châu', 'Quận Thanh Khê', 'Quận Sơn Trà', 'Quận Ngũ Hành Sơn', 'Quận Liên Chiểu'],
            'CT': ['Quận Ninh Kiều', 'Quận Cái Răng', 'Quận Bình Thủy'],
            'BD': ['TP. Thủ Dầu Một', 'TP. Thuận An', 'TP. Dĩ An'],
            'DNai': ['TP. Biên Hòa', 'Huyện Long Thành'],
            'VT': ['TP. Vũng Tàu', 'TP. Bà Rịa'],
            'HUE': ['TP. Huế', 'Huyện Hương Thủy'],
            'HP': ['Quận Hồng Bàng', 'Quận Ngô Quyền', 'Quận Lê Chân'],
            'KH': ['TP. Nha Trang', 'TP. Cam Ranh'],
            'LD': ['TP. Đà Lạt', 'TP. Bảo Lộc']
        };

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
        const hiddenSapXep = document.getElementById(prefix + '-sap-xep');

        const provinceSelect = document.getElementById(prefix + '-province-select');
        const districtSelect = document.getElementById(prefix + '-district-select');
        
        const gpsToggleBtn = document.getElementById(prefix + '-gps-toggle-btn');
        const gpsControls = document.getElementById(prefix + '-gps-controls');
        const gpsChevron = document.getElementById(prefix + '-gps-chevron');
        const gpsActionBtn = document.getElementById(prefix + '-gps-action-btn');
        const gpsBtnText = document.getElementById(prefix + '-gps-btn-text');
        const radiusInput = document.getElementById(prefix + '-radius-input');
        const radiusDisplay = document.getElementById(prefix + '-radius-display');

        const resetLocationBtn = document.getElementById(prefix + '-reset-location-btn');
        const applyLocationBtn = document.getElementById(prefix + '-apply-location-btn');

        let debounceTimer = null;

        function closeSuggestions() {
            suggestionsDropdown.classList.add('hidden');
        }

        function openSuggestions() {
            closeLocation();
            suggestionsDropdown.classList.remove('hidden');
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

        // Click on 9 dishes in Gợi ý cho bạn
        wrapper.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function() {
                const dish = this.getAttribute('data-dish');
                if (dish) {
                    searchInput.value = dish;
                    closeSuggestions();
                    form.submit();
                }
            });
        });

        function fetchSuggestions(keyword) {
            livePanel.innerHTML = '<div class="py-4 text-center text-gray-400 text-[13px] flex items-center justify-center gap-2"><span class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin" style="border-color: #a04100; border-top-color: transparent;"></span>Đang tìm gợi ý...</div>';

            fetch(`/api/search/suggest?q=${encodeURIComponent(keyword)}`)
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
                    html += `
                        <a href="/quan/${mon.quan_slug}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-transparent hover:border-[#a04100]/20 transition-all group">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                    <img src="${mon.hinh_anh || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=80&q=80'}" alt="${mon.ten_mon}" class="w-full h-full object-cover">
                                </div>
                                <div class="truncate">
                                    <div class="text-[13.5px] font-bold text-gray-800 group-hover:text-primary truncate" style="--tw-text-opacity: 1;">${mon.ten_mon}</div>
                                    <div class="text-[11.5px] text-gray-400 truncate">${mon.ten_quan}</div>
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
                    const img = quan.anh_bia || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=120&q=80';
                    html += `
                        <a href="/quan/${quan.slug}" class="flex items-center gap-2.5 p-2.5 rounded-xl hover:bg-[#a04100]/5 border border-transparent hover:border-[#a04100]/20 transition-all group">
                            <img src="${img}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0 bg-gray-100 border border-gray-100" alt="${quan.ten_quan}">
                            <div class="overflow-hidden min-w-0">
                                <div class="text-[13.5px] font-bold text-gray-800 group-hover:text-primary truncate">${quan.ten_quan}</div>
                                <div class="text-[11.5px] text-gray-400 truncate flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">location_on</span>
                                    ${quan.dia_chi || quan.loai_hinh_kinh_doanh || ''}
                                </div>
                            </div>
                        </a>
                    `;
                });
            }

            if (!data.mons?.length && !data.quans?.length) {
                html += `
                    <div class="py-6 text-center text-gray-500 text-[13.5px]">
                        Không tìm thấy món ăn hoặc quán nào khớp với "<strong>${keyword}</strong>"
                    </div>
                `;
            }

            // Footer action
            html += `
                <div class="pt-2 border-t border-gray-100 mt-2">
                    <button type="submit" class="w-full py-2.5 text-center text-[13px] font-bold text-primary hover:bg-[#a04100]/5 rounded-xl transition-all cursor-pointer" style="color: #a04100;">
                        Xem tất cả kết quả cho "${keyword}" →
                    </button>
                </div>
            `;

            livePanel.innerHTML = html;
        }

        // Location events
        locationBtn.addEventListener('click', toggleLocation);

        provinceSelect.addEventListener('change', function() {
            const provCode = this.value;
            districtSelect.innerHTML = '<option value="">Tất cả quận/huyện</option>';
            if (provCode && districtsMap[provCode]) {
                districtsMap[provCode].forEach(dist => {
                    const opt = document.createElement('option');
                    opt.value = dist;
                    opt.textContent = dist;
                    districtSelect.appendChild(opt);
                });
            }
        });

        gpsToggleBtn.addEventListener('click', function() {
            const isHidden = gpsControls.classList.contains('hidden');
            if (isHidden) {
                gpsControls.classList.remove('hidden');
                gpsChevron.classList.add('rotate-180');
            } else {
                gpsControls.classList.add('hidden');
                gpsChevron.classList.remove('rotate-180');
            }
        });

        radiusInput.addEventListener('input', function() {
            radiusDisplay.textContent = this.value + ' km';
        });

        gpsActionBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Trình duyệt không hỗ trợ định vị GPS');
                return;
            }

            gpsBtnText.textContent = 'Đang lấy vị trí...';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    hiddenLat.value = pos.coords.latitude;
                    hiddenLng.value = pos.coords.longitude;
                    hiddenSapXep.value = 'near_me';
                    
                    const radius = radiusInput.value;
                    locationLabel.textContent = `Gần bạn (${radius}km)`;
                    gpsBtnText.textContent = '✓ Đã định vị thành công';
                },
                (err) => {
                    gpsBtnText.textContent = 'Lấy vị trí GPS hiện tại';
                    alert('Vui lòng bật quyền truy cập vị trí trên thiết bị của bạn');
                }
            );
        });

        resetLocationBtn.addEventListener('click', function() {
            provinceSelect.value = '';
            districtSelect.innerHTML = '<option value="">Tất cả quận/huyện</option>';
            hiddenTinhThanh.value = '';
            hiddenQuanHuyen.value = '';
            hiddenLat.value = '';
            hiddenLng.value = '';
            hiddenSapXep.value = '';
            locationLabel.textContent = 'Chọn khu vực';
            gpsBtnText.textContent = 'Lấy vị trí GPS hiện tại';
            closeLocation();
        });

        applyLocationBtn.addEventListener('click', function() {
            const provVal = provinceSelect.value;
            const distVal = districtSelect.value;
            const hasGps = hiddenLat.value && hiddenLng.value;

            if (distVal) {
                hiddenQuanHuyen.value = distVal;
                hiddenTinhThanh.value = provVal;
                locationLabel.textContent = distVal;
            } else if (provVal) {
                hiddenTinhThanh.value = provVal;
                hiddenQuanHuyen.value = '';
                const selectedText = provinceSelect.options[provinceSelect.selectedIndex]?.text || provVal;
                locationLabel.textContent = selectedText;
            } else if (hasGps) {
                const radius = radiusInput.value;
                locationLabel.textContent = `Gần bạn (${radius}km)`;
            } else {
                hiddenTinhThanh.value = '';
                hiddenQuanHuyen.value = '';
                locationLabel.textContent = 'Chọn khu vực';
            }

            closeLocation();
        });

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
