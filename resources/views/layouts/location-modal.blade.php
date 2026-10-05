{{-- ╔══════════════════════════════════════════════════════════════╗ --}}
{{-- ║   LOCATION SEARCH MODAL (Popup tìm kiếm theo khu vực)       ║ --}}
{{-- ╚══════════════════════════════════════════════════════════════╝ --}}
<div id="location-search-modal" class="fixed inset-0 z-[120] hidden flex items-center justify-center p-3 md:p-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/45 backdrop-blur-sm transition-opacity duration-300" onclick="closeLocationModal()"></div>

    {{-- Modal Body --}}
    <div class="relative bg-white rounded-2xl md:rounded-3xl w-full max-w-[560px] shadow-[0_20px_60px_rgba(0,0,0,0.2)] border border-gray-100 transform transition-all duration-300 scale-95 opacity-0 overflow-hidden flex flex-col max-h-[90vh]" id="location-modal-content">

        {{-- Top Input Header Bar --}}
        <div class="p-4 border-b border-gray-100 bg-white sticky top-0 z-20">
            <div class="flex items-center gap-3 relative">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                    <span class="material-symbols-outlined text-[22px]">search</span>
                </div>
                <input id="location-search-keyword" type="text" autocomplete="off" class="flex-grow h-11 px-2 text-[15px] font-medium text-gray-800 bg-transparent outline-none placeholder:text-gray-400" placeholder="Nhập tên quán, món ăn..." />
                <button onclick="closeLocationModal()" class="w-9 h-9 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center transition-all flex-shrink-0">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>

                {{-- Autocomplete Dropdown --}}
                <div id="autocomplete-results" class="absolute top-12 left-0 right-0 bg-white shadow-xl border border-gray-100 rounded-xl overflow-hidden hidden z-30 max-h-[300px] overflow-y-auto">
                    {{-- JS injected --}}
                </div>
            </div>
        </div>

        {{-- Tabs Bar --}}
        <div class="flex border-b border-gray-200 bg-gray-50/70 text-[14px] font-semibold">
            <button id="tab-btn-area" onclick="switchLocationTab('area')" class="flex-1 py-3 px-4 flex items-center justify-center gap-2 border-b-2 border-primary text-primary bg-white transition-all shadow-sm">
                <span class="material-symbols-outlined text-[18px]">map</span>
                Chọn khu vực
            </button>
            <button id="tab-btn-gps" onclick="switchLocationTab('gps')" class="flex-1 py-3 px-4 flex items-center justify-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-primary transition-all">
                <span class="material-symbols-outlined text-[18px]">near_me</span>
                Tìm kiếm theo vị trí
            </button>
        </div>

        {{-- Scrollable Content Body --}}
        <div class="p-5 md:p-6 overflow-y-auto flex-grow space-y-6">

            {{-- TAB 1: CHỌN KHU VỰC --}}
            <div id="location-tab-area" class="space-y-6">
                
                {{-- Section: Tỉnh/TP nổi bật --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-1 h-4 bg-primary rounded-full inline-block"></span>
                        <h4 class="font-bold text-[15px] text-gray-800">Tỉnh/TP nổi bật</h4>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        {{-- City 1: Hồ Chí Minh --}}
                        <div onclick="selectFeaturedCity('Hồ Chí Minh')" class="city-card group relative h-24 rounded-xl overflow-hidden cursor-pointer shadow-sm hover:shadow-md transition-all border border-gray-100">
                            <img src="https://images.unsplash.com/photo-1583417319070-4a69db38a482?auto=format&fit=crop&w=400&q=80" alt="Hồ Chí Minh" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                            <span class="absolute bottom-2.5 inset-x-2 text-center text-white font-bold text-[13px] drop-shadow-sm truncate">Hồ Chí Minh</span>
                        </div>

                        {{-- City 2: Hà Nội --}}
                        <div onclick="selectFeaturedCity('Hà Nội')" class="city-card group relative h-24 rounded-xl overflow-hidden cursor-pointer shadow-sm hover:shadow-md transition-all border border-gray-100">
                            <img src="https://images.unsplash.com/photo-1509030450996-939a26352156?auto=format&fit=crop&w=400&q=80" alt="Hà Nội" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                            <span class="absolute bottom-2.5 inset-x-2 text-center text-white font-bold text-[13px] drop-shadow-sm truncate">Hà Nội</span>
                        </div>

                        {{-- City 3: Đà Nẵng --}}
                        <div onclick="selectFeaturedCity('Đà Nẵng')" class="city-card group relative h-24 rounded-xl overflow-hidden cursor-pointer shadow-sm hover:shadow-md transition-all border border-gray-100">
                            <img src="https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=400&q=80" alt="Đà Nẵng" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                            <span class="absolute bottom-2.5 inset-x-2 text-center text-white font-bold text-[13px] drop-shadow-sm truncate">Đà Nẵng</span>
                        </div>

                        {{-- City 4: Thừa Thiên Huế --}}
                        <div onclick="selectFeaturedCity('Thừa Thiên Huế')" class="city-card group relative h-24 rounded-xl overflow-hidden cursor-pointer shadow-sm hover:shadow-md transition-all border border-gray-100">
                            <img src="https://images.unsplash.com/photo-1621245802011-85b1c97a53c9?auto=format&fit=crop&w=400&q=80" alt="Thừa Thiên Huế" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                            <span class="absolute bottom-2.5 inset-x-2 text-center text-white font-bold text-[13px] drop-shadow-sm truncate">Thừa Thiên Huế</span>
                        </div>
                    </div>
                </div>

                {{-- Section: Chọn khu vực cụ thể --}}
                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="w-1 h-4 bg-primary rounded-full inline-block"></span>
                        <h4 class="font-bold text-[15px] text-gray-800">Chọn khu vực cụ thể</h4>
                    </div>

                    <div class="space-y-3">
                        {{-- Select Tỉnh / TP --}}
                        <div class="relative">
                            <select id="select-province" onchange="onProvinceChange(this.value)" class="w-full h-12 px-4 pr-10 rounded-xl border border-gray-200 bg-white text-[14px] font-medium text-gray-700 appearance-none focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all cursor-pointer">
                                <option value="">Chọn Tỉnh/TP...</option>
                                <option value="HCM">Hồ Chí Minh</option>
                                <option value="HN">Hà Nội</option>
                                <option value="DN">Đà Nẵng</option>
                                <option value="HUE">Thừa Thiên Huế</option>
                                <option value="BD">Bình Dương</option>
                                <option value="DNai">Đồng Nai</option>
                                <option value="VT">Bà Rịa - Vũng Tàu</option>
                                <option value="CT">Cần Thơ</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-[20px]">arrow_drop_down</span>
                        </div>

                        {{-- Select Quận / Huyện --}}
                        <div class="relative">
                            <select id="select-district" onchange="onDistrictChange(this.value)" class="w-full h-12 px-4 pr-10 rounded-xl border border-gray-200 bg-white text-[14px] font-medium text-gray-700 appearance-none focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all cursor-pointer">
                                <option value="">Quận/Huyện...</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-[20px]">arrow_drop_down</span>
                        </div>

                        {{-- Select Phường / Xã --}}
                        <div class="relative">
                            <select id="select-ward" class="w-full h-12 px-4 pr-10 rounded-xl border border-gray-200 bg-white text-[14px] font-medium text-gray-700 appearance-none focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all cursor-pointer">
                                <option value="">Phường/Xã...</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-[20px]">arrow_drop_down</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- TAB 2: TÌM KIẾM THEO VỊ TRÍ (GPS) --}}
            <div id="location-tab-gps" class="space-y-6 hidden">
                <div class="bg-primary/5 rounded-2xl p-5 border border-primary/10 text-center space-y-3">
                    <div class="w-14 h-14 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto">
                        <span class="material-symbols-outlined text-3xl animate-pulse">my_location</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-[16px] text-gray-800">Tìm quán quanh vị trí của bạn</h4>
                        <p class="text-[13px] text-gray-500 mt-1" id="gps-status-text">Cho phép truy cập GPS để tự động định vị quán ăn gần nhất</p>
                    </div>
                    <button onclick="requestGpsLocation()" class="px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-[13px] hover:bg-primary/90 transition-all shadow-sm active:scale-95">
                        Lấy vị trí hiện tại
                    </button>
                </div>

                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-[14px] text-gray-700">Bán kính tìm kiếm</label>
                        <span class="text-primary font-bold text-[14px]" id="radius-val">3 km</span>
                    </div>
                    <input type="range" min="1" max="15" value="3" oninput="document.getElementById('radius-val').textContent = this.value + ' km'" class="w-full accent-primary h-2 bg-gray-200 rounded-lg cursor-pointer" />
                    <div class="flex justify-between text-[11px] text-gray-400 font-medium">
                        <span>1 km</span>
                        <span>5 km</span>
                        <span>10 km</span>
                        <span>15 km</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Bottom Actions Bar --}}
        <div class="p-4 border-t border-gray-100 bg-gray-50/60 flex items-center justify-between gap-3 sticky bottom-0 z-10">
            <button type="button" onclick="resetLocationForm()" class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-600 font-bold text-[13px] hover:bg-gray-50 active:scale-95 transition-all flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">refresh</span>
                Đặt lại
            </button>
            <button type="button" onclick="submitLocationFilter()" class="px-6 py-2.5 rounded-xl bg-primary text-white font-bold text-[14px] hover:bg-primary/90 active:scale-95 transition-all shadow-md flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">search</span>
                Tìm ngay
            </button>
        </div>

    </div>
</div>

@push('scripts')
    <script>
        const districtsData = {
            'HCM': ['Quận 1', 'Quận 3', 'Quận 5', 'Quận 7', 'Quận 10', 'Quận Bình Thạnh', 'Quận Tân Bình', 'TP. Thủ Đức'],
            'HN': ['Quận Hoàn Kiếm', 'Quận Ba Đình', 'Quận Đống Đa', 'Quận Cầu Giấy', 'Quận Hai Bà Trưng', 'Quận Tây Hồ'],
            'DN': ['Quận Hải Châu', 'Quận Thanh Khê', 'Quận Sơn Trà', 'Quận Ngũ Hành Sơn'],
            'HUE': ['TP. Huế', 'Huyện Hương Thủy', 'Huyện Hương Trà'],
            'BD': ['TP. Thủ Dầu Một', 'TP. Thuận An', 'TP. Dĩ An'],
            'DNai': ['TP. Biên Hòa', 'Huyện Long Thành'],
            'VT': ['TP. Vũng Tàu', 'TP. Bà Rịa'],
            'CT': ['Quận Ninh Kiều', 'Quận Bình Thủy']
        };

        const wardsData = {
            'Quận 1': ['Phường Bến Nghé', 'Phường Bến Thành', 'Phường Phạm Ngũ Lão', 'Phường Tân Định'],
            'Quận 3': ['Phường Võ Thị Sáu', 'Phường 1', 'Phường 2'],
            'TP. Thủ Đức': ['Phường Thảo Điền', 'Phường An Phú', 'Phường Linh Trung'],
            'Quận Hoàn Kiếm': ['Phường Hàng Bạc', 'Phường Hàng Bài', 'Phường Tràng Tiền']
        };

        function openLocationModal() {
            const modal = document.getElementById('location-search-modal');
            const content = document.getElementById('location-modal-content');
            if (!modal || !content) return;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            requestAnimationFrame(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            });
        }

        function closeLocationModal() {
            const modal = document.getElementById('location-search-modal');
            const content = document.getElementById('location-modal-content');
            if (!modal || !content) return;

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 250);
        }

        function switchLocationTab(tab) {
            const tabArea = document.getElementById('location-tab-area');
            const tabGps = document.getElementById('location-tab-gps');
            const btnArea = document.getElementById('tab-btn-area');
            const btnGps = document.getElementById('tab-btn-gps');

            if (tab === 'area') {
                tabArea.classList.remove('hidden');
                tabGps.classList.add('hidden');
                btnArea.className = 'flex-1 py-3 px-4 flex items-center justify-center gap-2 border-b-2 border-primary text-primary bg-white transition-all shadow-sm';
                btnGps.className = 'flex-1 py-3 px-4 flex items-center justify-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-primary transition-all';
            } else {
                tabGps.classList.remove('hidden');
                tabArea.classList.add('hidden');
                btnGps.className = 'flex-1 py-3 px-4 flex items-center justify-center gap-2 border-b-2 border-primary text-primary bg-white transition-all shadow-sm';
                btnArea.className = 'flex-1 py-3 px-4 flex items-center justify-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-primary transition-all';
            }
        }

        function selectFeaturedCity(cityName) {
            const provinceSelect = document.getElementById('select-province');
            if (!provinceSelect) return;

            const optionMap = {
                'Hồ Chí Minh': 'HCM',
                'Hà Nội': 'HN',
                'Đà Nẵng': 'DN',
                'Thừa Thiên Huế': 'HUE'
            };

            const code = optionMap[cityName] || '';
            provinceSelect.value = code;
            onProvinceChange(code);

            // Highlight selected city card
            document.querySelectorAll('.city-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-primary');
            });
            event.currentTarget.classList.add('ring-2', 'ring-primary');
        }

        function onProvinceChange(code) {
            const districtSelect = document.getElementById('select-district');
            const wardSelect = document.getElementById('select-ward');
            if (!districtSelect || !wardSelect) return;

            districtSelect.innerHTML = '<option value="">Quận/Huyện...</option>';
            wardSelect.innerHTML = '<option value="">Phường/Xã...</option>';

            if (code && districtsData[code]) {
                districtsData[code].forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d;
                    opt.textContent = d;
                    districtSelect.appendChild(opt);
                });
            }
        }

        function onDistrictChange(districtName) {
            const wardSelect = document.getElementById('select-ward');
            if (!wardSelect) return;

            wardSelect.innerHTML = '<option value="">Phường/Xã...</option>';

            if (districtName && wardsData[districtName]) {
                wardsData[districtName].forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w;
                    opt.textContent = w;
                    wardSelect.appendChild(opt);
                });
            } else if (districtName) {
                ['Phường 1', 'Phường 2', 'Phường 3', 'Phường Trung Tâm'].forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w;
                    opt.textContent = w;
                    wardSelect.appendChild(opt);
                });
            }
        }

        function resetLocationForm() {
            document.getElementById('location-search-keyword').value = '';
            document.getElementById('select-province').value = '';
            onProvinceChange('');
            document.querySelectorAll('.city-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-primary');
            });
        }

        function requestGpsLocation() {
            const statusText = document.getElementById('gps-status-text');
            if (navigator.geolocation) {
                statusText.textContent = 'Đang xác định vị trí...';
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        statusText.textContent = `Đã tìm thấy vị trí. Đang chuyển hướng...`;
                        window.location.href = `/kham-pha?lat=${lat}&lng=${lng}&sap_xep=near_me`;
                    },
                    (err) => {
                        statusText.textContent = 'Không thể định vị. Vui lòng cho phép truy cập vị trí.';
                    }
                );
            } else {
                statusText.textContent = 'Trình duyệt không hỗ trợ định vị GPS.';
            }
        }

        function submitLocationFilter() {
            const keyword = document.getElementById('location-search-keyword').value.trim();
            const provinceId = document.getElementById('select-province').value;
            const districtId = document.getElementById('select-district').value;

            const params = new URLSearchParams();
            if (keyword) params.set('tu_khoa', keyword);
            if (provinceId) params.set('tinh_thanh_id', provinceId);
            if (districtId) params.set('quan_huyen_id', districtId);

            closeLocationModal();

            if (params.toString()) {
                window.location.href = '/kham-pha?' + params.toString();
            } else {
                window.location.href = '/kham-pha';
            }
        }

        // Live Autocomplete logic
        let searchTimeout = null;
        const searchInput = document.getElementById('location-search-keyword');
        const resultsContainer = document.getElementById('autocomplete-results');

        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const q = e.target.value.trim();
            
            if (!q) {
                resultsContainer.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/api/search/suggest?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            renderAutocomplete(data.data, q);
                        }
                    });
            }, 300); // 300ms debounce
        });

        function renderAutocomplete(data, keyword) {
            resultsContainer.innerHTML = '';
            let html = '';

            if (data.mons && data.mons.length > 0) {
                html += `<div class="px-3 py-2 bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider">Món ăn gợi ý</div>`;
                data.mons.forEach(mon => {
                    html += `
                        <a href="/quan/${mon.quan_slug}" class="block px-4 py-2.5 hover:bg-primary/5 border-b border-gray-50">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-gray-400 text-[18px]">restaurant_menu</span>
                                <div>
                                    <div class="text-[14px] font-semibold text-gray-800">${mon.ten_mon}</div>
                                    <div class="text-[12px] text-gray-500">${mon.ten_quan}</div>
                                </div>
                            </div>
                        </a>
                    `;
                });
            }

            if (data.quans && data.quans.length > 0) {
                html += `<div class="px-3 py-2 bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider">Quán ăn</div>`;
                data.quans.forEach(quan => {
                    const img = quan.anh_bia || 'https://placehold.co/100x100?text=No+Image';
                    html += `
                        <a href="/quan/${quan.slug}" class="block px-4 py-2.5 hover:bg-primary/5 border-b border-gray-50">
                            <div class="flex items-center gap-3">
                                <img src="${img}" class="w-10 h-10 rounded-lg object-cover bg-gray-100 flex-shrink-0" />
                                <div class="overflow-hidden">
                                    <div class="text-[14px] font-semibold text-gray-800 truncate">${quan.ten_quan}</div>
                                    <div class="text-[12px] text-gray-500 truncate">${quan.dia_chi || quan.loai_hinh_kinh_doanh}</div>
                                </div>
                            </div>
                        </a>
                    `;
                });
            }

            if (!html) {
                html = `<div class="px-4 py-3 text-[14px] text-gray-500 text-center">Không tìm thấy "${keyword}"</div>`;
            } else {
                html += `<a href="/kham-pha?tu_khoa=${encodeURIComponent(keyword)}" class="block px-4 py-3 text-[14px] text-primary font-semibold text-center hover:bg-primary/5">Xem tất cả kết quả</a>`;
            }

            resultsContainer.innerHTML = html;
            resultsContainer.classList.remove('hidden');
        }

        // Ẩn dropdown khi click ngoài
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.classList.add('hidden');
            }
        });

        // Attach click triggers to search inputs
        document.addEventListener('DOMContentLoaded', () => {
            const headerSearch = document.querySelector('input[placeholder*="Tìm kiếm địa điểm"]');
            if (headerSearch) {
                headerSearch.addEventListener('click', (e) => {
                    e.preventDefault();
                    openLocationModal();
                });
            }

            const mobileSearchBtn = document.querySelector('button[aria-label="Search"]');
            if (mobileSearchBtn) {
                mobileSearchBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    openLocationModal();
                });
            }
        });
    </script>
@endpush
