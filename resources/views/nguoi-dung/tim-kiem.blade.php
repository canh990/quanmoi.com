@extends('layouts.app')

@section('title', 'Tìm kiếm địa điểm - Quán Mới')

@section('content')
<main class="pt-24 pb-20 max-w-6xl mx-auto px-4 md:px-8 flex-grow w-full">
    <div class="mb-8">
        <h1 class="text-3xl font-black tracking-tight text-gray-900">Tìm kiếm quán</h1>
        <p class="text-gray-500 mt-2">Tìm kiếm các quán ăn, quán nước, bida... lân cận bạn hoặc theo địa chỉ địa phương.</p>
    </div>

    <!-- Search options card -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-8">
        <div class="flex border-b border-gray-100 mb-6">
            <button onclick="switchTab('lan_can')" id="tab-lan_can" class="flex-1 pb-4 text-center font-bold text-sm border-b-2 border-primary text-primary transition-all">
                <span class="inline-flex items-center gap-2">
                    <span class="material-symbols-outlined">near_me</span>
                    Tìm lân cận (GPS)
                </span>
            </button>
            <button onclick="switchTab('dia_chi')" id="tab-dia_chi" class="flex-1 pb-4 text-center font-bold text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-900 transition-all">
                <span class="inline-flex items-center gap-2">
                    <span class="material-symbols-outlined">map</span>
                    Tìm theo địa chỉ
                </span>
            </button>
        </div>

        <!-- Near me search form -->
        <form id="form-lan_can" action="{{ route('tim-kiem.index') }}" method="GET" class="space-y-6">
            <input type="hidden" name="type" value="lan_can">
            <input type="hidden" name="latitude" id="input-lat" value="{{ request('latitude') }}">
            <input type="hidden" name="longitude" id="input-lng" value="{{ request('longitude') }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Bán kính tìm kiếm</label>
                    <select name="ban_kinh" class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                        <option value="1" {{ request('ban_kinh') == 1 ? 'selected' : '' }}>Trong vòng 1 km</option>
                        <option value="3" {{ request('ban_kinh', 3) == 3 ? 'selected' : '' }}>Trong vòng 3 km (Mặc định)</option>
                        <option value="5" {{ request('ban_kinh') == 5 ? 'selected' : '' }}>Trong vòng 5 km</option>
                        <option value="10" {{ request('ban_kinh') == 10 ? 'selected' : '' }}>Trong vòng 10 km</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Danh mục quán</label>
                    <select name="danh_muc_id" class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                        <option value="">Tất cả danh mục</option>
                        @foreach($danhMucs as $dm)
                            <option value="{{ $dm->id }}" {{ request('danh_muc_id') == $dm->id ? 'selected' : '' }}>{{ $dm->ten_danh_muc }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="button" onclick="getGPSAndSubmit()" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-2xl transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">my_location</span>
                        <span>Định vị & Tìm kiếm</span>
                    </button>
                </div>
            </div>
            <div id="gps-status" class="text-xs text-gray-500 hidden italic">Đang lấy tọa độ GPS của bạn...</div>
        </form>

        <!-- Address search form -->
        <form id="form-dia_chi" action="{{ route('tim-kiem.index') }}" method="GET" class="space-y-6 hidden">
            <input type="hidden" name="type" value="dia_chi">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Tỉnh / Thành phố</label>
                    <select name="tinh_thanh_id" id="select-tinh" onchange="loadDistrict(this.value)" class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm outline-none focus:border-primary bg-white" required>
                        <option value="">-- Chọn Tỉnh / Thành --</option>
                        @foreach($tinhThanhs as $tinh)
                            <option value="{{ $tinh['code'] }}" {{ request('tinh_thanh_id') == $tinh['code'] ? 'selected' : '' }}>{{ $tinh['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Quận / Huyện</label>
                    <select name="quan_huyen_id" id="select-huyen" onchange="loadWard(this.value)" class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                        <option value="">-- Chọn Quận / Huyện --</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Phường / Xã</label>
                    <select name="phuong_xa_id" id="select-xa" class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                        <option value="">-- Chọn Phường / Xã --</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Danh mục quán</label>
                    <select name="danh_muc_id" class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                        <option value="">Tất cả danh mục</option>
                        @foreach($danhMucs as $dm)
                            <option value="{{ $dm->id }}" {{ request('danh_muc_id') == $dm->id ? 'selected' : '' }}>{{ $dm->ten_danh_muc }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 px-8 rounded-2xl transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">search</span>
                    <span>Tìm kiếm</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Results section -->
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <span>Kết quả tìm kiếm</span>
            <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full font-bold">{{ $quans->count() }} quán</span>
        </h2>

        @if($quans->isEmpty())
            <div class="bg-white rounded-3xl border border-gray-100 p-12 text-center text-gray-500">
                <span class="material-symbols-outlined text-5xl mb-3 text-gray-300">location_off</span>
                <p class="font-medium text-lg">Chưa có kết quả tìm kiếm</p>
                <p class="text-sm text-gray-400 mt-1">Vui lòng chọn phương thức tìm kiếm và bấm nút để hiển thị các quán gần nhất.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($quans as $quan)
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between group hover:shadow-md transition-all">
                        <div>
                            <div class="relative h-48 bg-gray-100 overflow-hidden">
                                @if($quan->anh_bia)
                                    <img src="{{ $quan->anh_bia }}" alt="{{ $quan->ten_quan }}" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary/5 text-primary">
                                        <span class="material-symbols-outlined text-4xl">storefront</span>
                                    </div>
                                @endif
                                @if(isset($quan->khoang_cach))
                                    <span class="absolute top-4 right-4 bg-gray-900/80 backdrop-blur-sm text-white px-3 py-1.5 rounded-full text-xs font-bold">
                                        {{ number_format($quan->khoang_cach, 1) }} km lân cận
                                    </span>
                                @endif
                            </div>

                            <div class="p-6">
                                <span class="text-xs font-bold text-primary bg-primary/10 px-2.5 py-1 rounded-full uppercase tracking-wider">
                                    {{ $quan->danhMucQuan->ten_danh_muc ?? 'Chưa phân loại' }}
                                </span>
                                <h3 class="text-lg font-bold text-gray-900 mt-3">{{ $quan->ten_quan }}</h3>
                                <p class="text-sm text-gray-500 mt-2 flex items-start gap-1">
                                    <span class="material-symbols-outlined text-[18px] text-gray-400 mt-0.5">location_on</span>
                                    <span>{{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="p-6 pt-0">
                            <a href="{{ route('quan.detail', $quan->slug) }}" class="w-full inline-flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold py-3 px-4 rounded-2xl transition-all text-sm">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
<script>
    // Tab switching logic
    function switchTab(tab) {
        document.getElementById('tab-lan_can').className = tab === 'lan_can' 
            ? 'flex-1 pb-4 text-center font-bold text-sm border-b-2 border-primary text-primary transition-all'
            : 'flex-1 pb-4 text-center font-bold text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-900 transition-all';
        
        document.getElementById('tab-dia_chi').className = tab === 'dia_chi' 
            ? 'flex-1 pb-4 text-center font-bold text-sm border-b-2 border-primary text-primary transition-all'
            : 'flex-1 pb-4 text-center font-bold text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-900 transition-all';

        if (tab === 'lan_can') {
            document.getElementById('form-lan_can').classList.remove('hidden');
            document.getElementById('form-dia_chi').classList.add('hidden');
        } else {
            document.getElementById('form-lan_can').classList.add('hidden');
            document.getElementById('form-dia_chi').classList.remove('hidden');
        }
    }

    // Auto switch to active tab based on query params
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeType = urlParams.get('type') || 'lan_can';
        switchTab(activeType);

        if (activeType === 'dia_chi') {
            const currentTinh = "{{ request('tinh_thanh_id') }}";
            const currentHuyen = "{{ request('quan_huyen_id') }}";
            const currentXa = "{{ request('phuong_xa_id') }}";
            if (currentTinh) {
                loadDistrict(currentTinh, currentHuyen, currentXa);
            }
        }
    });

    // GPS Geolocating logic
    function getGPSAndSubmit() {
        const gpsStatus = document.getElementById('gps-status');
        gpsStatus.classList.remove('hidden');

        if (!navigator.geolocation) {
            alert('Trình duyệt của bạn không hỗ trợ định vị GPS.');
            gpsStatus.classList.add('hidden');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                document.getElementById('input-lat').value = position.coords.latitude;
                document.getElementById('input-lng').value = position.coords.longitude;
                gpsStatus.classList.add('hidden');
                document.getElementById('form-lan_can').submit();
            },
            (error) => {
                alert('Không thể lấy vị trí GPS: ' + error.message);
                gpsStatus.classList.add('hidden');
            },
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
    }

    // Address APIs fetching logic
    function loadDistrict(tinhCode, selectedHuyenCode = '', selectedXaCode = '') {
        const selectHuyen = document.getElementById('select-huyen');
        const selectXa = document.getElementById('select-xa');

        selectHuyen.innerHTML = '<option value="">-- Đang tải... --</option>';
        selectXa.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

        if (!tinhCode) {
            selectHuyen.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
            return;
        }

        fetch(`/api/dia-chi/quan-huyen/${tinhCode}`)
            .then(res => res.json())
            .then(data => {
                selectHuyen.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                data.forEach(huyen => {
                    const option = document.createElement('option');
                    option.value = huyen.code;
                    option.textContent = huyen.name;
                    if (selectedHuyenCode && selectedHuyenCode == huyen.code) {
                        option.selected = true;
                    }
                    selectHuyen.appendChild(option);
                });
                
                if (selectedHuyenCode) {
                    loadWard(selectedHuyenCode, selectedXaCode);
                }
            })
            .catch(() => {
                selectHuyen.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
            });
    }

    function loadWard(huyenCode, selectedXaCode = '') {
        const selectXa = document.getElementById('select-xa');
        selectXa.innerHTML = '<option value="">-- Đang tải... --</option>';

        if (!huyenCode) {
            selectXa.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            return;
        }

        fetch(`/api/dia-chi/phuong-xa/${huyenCode}`)
            .then(res => res.json())
            .then(data => {
                selectXa.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
                data.forEach(xa => {
                    const option = document.createElement('option');
                    option.value = xa.code;
                    option.textContent = xa.name;
                    if (selectedXaCode && selectedXaCode == xa.code) {
                        option.selected = true;
                    }
                    selectXa.appendChild(option);
                });
            })
            .catch(() => {
                selectXa.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
            });
    }
</script>
@endpush
