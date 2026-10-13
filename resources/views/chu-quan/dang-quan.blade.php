@extends('layouts.app')

@section('title', 'Đăng Tin - Quán Mới')

@push('styles')
    <meta name="description" content="Đăng tin địa điểm quán ăn, nhà hàng, cà phê mới lên hệ thống Quán Mới." />
    <style>
        .input-focus-effect:focus {
            border-color: #a04100;
            box-shadow: 0 0 0 2px rgba(160, 65, 0, 0.12);
        }
        .business-type-card.active {
            border-color: #ff6b00 !important;
            background-color: #ffdbcc !important;
            color: #a04100 !important;
        }
        .business-type-card.active .material-symbols-outlined {
            font-variation-settings: 'FILL' 1;
            color: #a04100 !important;
        }
    </style>
@endpush

@section('content')
<main class="pt-20 pb-24 px-4 md:px-12 max-w-[1280px] mx-auto">
    <form id="dang-quan-form" onsubmit="event.preventDefault(); submitForm();" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="loai_hinh_kinh_doanh" name="loai_hinh_kinh_doanh" value="Nhà hàng" />

        <div id="section-step-1">
            <div class="grid grid-cols-12 gap-6 lg:gap-8">
                {{-- Left Column: Form Fields --}}
                <section class="col-span-12 lg:col-span-7 flex flex-col gap-6">
                    


                    {{-- Business Type Selection --}}
                    <div class="bg-white p-6 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-gray-200 space-y-4">
                        <label class="text-[12px] font-bold text-gray-500 tracking-wider uppercase block">LOẠI HÌNH KINH DOANH <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-4" id="business-type-container">
                            {{-- 1. Nhà hàng --}}
                            <button type="button" class="business-type-card active flex flex-col items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-primary-container transition-all group active:scale-95 text-center cursor-pointer" onclick="selectType('Nhà hàng', this)">
                                <span class="material-symbols-outlined text-3xl text-gray-600 group-hover:text-primary">restaurant</span>
                                <span class="text-[14px] font-semibold">Nhà hàng</span>
                            </button>
                            {{-- 2. Cà phê --}}
                            <button type="button" class="business-type-card flex flex-col items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-primary-container transition-all group active:scale-95 text-center cursor-pointer" onclick="selectType('Cà phê & Trà', this)">
                                <span class="material-symbols-outlined text-3xl text-gray-600 group-hover:text-primary">local_cafe</span>
                                <span class="text-[14px] font-semibold">Cà phê & Trà</span>
                            </button>
                            {{-- 3. Billiards --}}
                            <button type="button" class="business-type-card flex flex-col items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-primary-container transition-all group active:scale-95 text-center cursor-pointer" onclick="selectType('Billiards & Giải trí', this)">
                                <span class="material-symbols-outlined text-3xl text-gray-600 group-hover:text-primary">sports_golf</span>
                                <span class="text-[14px] font-semibold">Billiards & Giải trí</span>
                            </button>
                            {{-- 4. Đồ ăn vặt --}}
                            <button type="button" class="business-type-card flex flex-col items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-primary-container transition-all group active:scale-95 text-center cursor-pointer" onclick="selectType('Đồ ăn vặt', this)">
                                <span class="material-symbols-outlined text-3xl text-gray-600 group-hover:text-primary">fastfood</span>
                                <span class="text-[14px] font-semibold">Đồ ăn vặt</span>
                            </button>
                            {{-- 5. Lẩu & Nướng --}}
                            <button type="button" class="business-type-card flex flex-col items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-primary-container transition-all group active:scale-95 text-center cursor-pointer" onclick="selectType('Lẩu & Nướng', this)">
                                <span class="material-symbols-outlined text-3xl text-gray-600 group-hover:text-primary">outdoor_grill</span>
                                <span class="text-[14px] font-semibold">Lẩu & Nướng</span>
                            </button>
                            {{-- 6. Quán Đêm 24/7 --}}
                            <button type="button" class="business-type-card flex flex-col items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-primary-container transition-all group active:scale-95 text-center cursor-pointer" onclick="selectType('Quán Đêm 24/7', this)">
                                <span class="material-symbols-outlined text-3xl text-gray-600 group-hover:text-primary">nightlife</span>
                                <span class="text-[14px] font-semibold">Quán Đêm 24/7</span>
                            </button>
                        </div>
                    </div>

                    {{-- Basic Info Form --}}
                    <div class="bg-white p-6 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-gray-200 flex flex-col gap-6">
                        <div>
                            <label for="ten_quan" class="text-[12px] font-bold text-gray-500 tracking-wider uppercase mb-2 block">TÊN CƠ SỞ KINH DOANH <span class="text-red-500">*</span></label>
                            <input id="ten_quan" name="ten_quan" required class="w-full px-4 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect text-[16px] font-normal" placeholder="Ví dụ: Phở Thìn Lò Đúc" type="text"/>
                            <p class="text-xs text-red-600 hidden mt-1" id="err-ten_quan"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="so_dien_thoai" class="text-[12px] font-bold text-gray-500 tracking-wider uppercase mb-2 block">SỐ ĐIỆN THOẠI <span class="text-red-500">*</span></label>
                                <input id="so_dien_thoai" name="so_dien_thoai" required class="w-full px-4 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect text-[16px] font-normal" placeholder="090 123 4567" type="tel"/>
                                <p class="text-xs text-red-600 hidden mt-1" id="err-so_dien_thoai"></p>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-[12px] font-bold text-gray-500 tracking-wider uppercase block">GIỜ HOẠT ĐỘNG <span class="text-red-500">*</span></label>
                                    <label class="flex items-center gap-1.5 cursor-pointer group">
                                        <input type="checkbox" id="is_24h" class="w-4 h-4 rounded text-primary border-gray-300 focus:ring-primary cursor-pointer" onchange="toggle24h(this.checked)">
                                        <span class="text-[12px] font-bold text-gray-500 group-hover:text-primary transition-colors select-none">MỞ 24/24</span>
                                    </label>
                                </div>
                                <div class="flex items-center gap-2 transition-opacity" id="time-select-container">
                                    <x-time-select name="gio_mo_cua" value="08:00" class="flex-1" />
                                    <span class="text-gray-400 font-bold">-</span>
                                    <x-time-select name="gio_dong_cua" value="22:00" class="flex-1" />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="gia_nho_nhat" class="text-[12px] font-bold text-gray-500 tracking-wider uppercase mb-2 block">GIÁ THẤP NHẤT (VNĐ)</label>
                                <input id="gia_nho_nhat" name="gia_nho_nhat" type="text" 
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');"
                                       class="w-full px-4 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect text-[16px]" placeholder="30.000"/>
                            </div>
                            <div>
                                <label for="gia_lon_nhat" class="text-[12px] font-bold text-gray-500 tracking-wider uppercase mb-2 block">GIÁ CAO NHẤT (VNĐ)</label>
                                <input id="gia_lon_nhat" name="gia_lon_nhat" type="text" 
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');"
                                       class="w-full px-4 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect text-[16px]" placeholder="150.000"/>
                            </div>
                        </div>

                        <div>
                            <label for="mo_ta" class="text-[12px] font-bold text-gray-500 tracking-wider uppercase mb-2 block">MÔ TẢ NGẮN</label>
                            <textarea id="mo_ta" name="mo_ta" class="w-full px-4 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect text-[16px] resize-none" placeholder="Chia sẻ về không gian, món đặc trưng hoặc ưu đãi của bạn..." rows="4"></textarea>
                        </div>
                        
                        <div>
                            <label for="tiktok_url" class="text-[12px] font-bold text-gray-500 tracking-wider uppercase mb-2 block flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16">
                                  <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"/>
                                </svg>
                                LINK TIKTOK REVIEW/QUÁN
                            </label>
                            <input id="tiktok_url" name="tiktok_url" class="w-full px-4 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect text-[16px] font-normal" placeholder="https://www.tiktok.com/@username/video/123456789" type="url"/>
                            <p class="text-xs text-red-600 hidden mt-1" id="err-tiktok_url"></p>
                        </div>
                    </div>

                    {{-- CTA Desktop --}}
                    <div class="flex justify-end pt-2">
                        <button id="submit-btn" type="submit" class="bg-primary text-white px-8 py-4 rounded-xl font-bold text-[16px] shadow-lg hover:shadow-xl hover:bg-primary/90 active:scale-[0.98] transition-all flex items-center gap-2 cursor-pointer">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            Hoàn tất & Chuyển sang Tạo thực đơn
                        </button>
                    </div>

                <p id="form-error-general" class="text-sm text-red-600 hidden text-center font-bold"></p>
            </section>

            {{-- Right Column: Media & Map --}}
            <section class="col-span-12 lg:col-span-5 flex flex-col gap-6">
                
                {{-- Image Upload Area --}}
                <div class="bg-white p-6 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <label class="text-[12px] font-bold text-gray-500 tracking-wider uppercase">HÌNH ẢNH KHÔNG GIAN &amp; MÓN ĂN</label>
                        <span class="text-primary text-[12px] font-bold">Tối đa 6 ảnh</span>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        {{-- Main Cover Photo Slot --}}
                        <div class="col-span-2 row-span-2 aspect-[4/3] rounded-lg overflow-hidden relative group border border-gray-200 bg-gray-50 cursor-pointer" onclick="document.getElementById('anh_bia').click()">
                            <input type="file" id="anh_bia" name="anh_bia" accept="image/*" class="hidden" onchange="previewCoverPhoto(this)" onclick="event.stopPropagation()" />
                            
                            <div id="cover-preview-container" class="hidden w-full h-full relative">
                                <img id="cover-preview-img" class="w-full h-full object-cover" src="" alt="Ảnh chính" />
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <button type="button" onclick="event.stopPropagation(); removeCoverPhoto();" class="p-2 bg-white rounded-full text-red-600 shadow-md"><span class="material-symbols-outlined">delete</span></button>
                                </div>
                            </div>

                            <div id="cover-upload-placeholder" class="w-full h-full flex flex-col items-center justify-center gap-2 text-gray-400 p-4 text-center">
                                <span class="material-symbols-outlined text-4xl text-primary">add_a_photo</span>
                                <span class="text-[13px] font-bold text-gray-700">Tải ảnh bìa chính</span>
                                <span class="text-[11px] text-gray-400">JPG, PNG, WEBP</span>
                            </div>

                            <div class="absolute top-2 left-2 px-2 py-1 bg-primary text-white text-[10px] font-bold rounded uppercase">Ảnh chính</div>
                        </div>

                        {{-- Gallery Thumbnails --}}
                        @for($i = 1; $i <= 5; $i++)
                        <div class="aspect-[4/3] rounded-lg border-2 border-dashed border-gray-200 flex flex-col items-center justify-center gap-1 text-gray-400 hover:border-primary hover:text-primary transition-all cursor-pointer relative group bg-gray-50" onclick="document.getElementById('gallery_{{ $i }}').click()">
                            <input type="file" id="gallery_{{ $i }}" name="danh_sach_anh[]" accept="image/*" multiple class="hidden" onchange="previewSingleGalleryPhoto(this, {{ $i }})" onclick="event.stopPropagation()" />
                            
                            <div id="gallery-preview-container-{{ $i }}" class="hidden absolute inset-0 w-full h-full">
                                <img id="gallery-preview-img-{{ $i }}" class="w-full h-full object-cover rounded-lg" src="" />
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" onclick="event.stopPropagation(); removeGalleryPhoto({{ $i }});" class="p-2 bg-white rounded-full text-red-600 shadow-md hover:bg-gray-100 flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                                </div>
                            </div>

                            <div id="gallery-placeholder-{{ $i }}" class="flex flex-col items-center justify-center w-full h-full p-2 text-center">
                                <span class="material-symbols-outlined text-xl mb-1">add_a_photo</span>
                                <span class="text-[10px] font-bold leading-tight">Thêm ảnh<br>phụ {{ $i }}</span>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                {{-- Interactive Address & Map Location --}}
                <div class="bg-white p-6 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-gray-200 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <label class="text-[12px] font-bold text-gray-500 tracking-wider uppercase">XÁC ĐỊNH VỊ TRÍ</label>
                        <div class="flex items-center gap-1 text-tick-xanh">
                            <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">verified</span>
                            <span class="text-[12px] font-bold">Định vị chính xác</span>
                        </div>
                    </div>

                    {{-- Admin Selects (Tỉnh/Thành ➔ Quận/Huyện ➔ Phường/Xã) --}}
                    <div class="grid grid-cols-1 gap-2.5">
                        <div>
                            <label class="text-[11px] font-bold text-gray-500 uppercase mb-1 block">Tỉnh / Thành phố <span class="text-red-500">*</span></label>
                            <select id="select-tinh" onchange="onTinhChange(this.value)" class="w-full h-11 px-3 rounded-lg border border-gray-200 outline-none input-focus-effect font-medium text-[14px]">
                                <option value="">-- Chọn Tỉnh / Thành phố --</option>
                            </select>
                            <input type="hidden" id="ten_tinh_thanh" name="ten_tinh_thanh" />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-[11px] font-bold text-gray-500 uppercase mb-1 block">Quận / Huyện <span class="text-red-500">*</span></label>
                                <select id="select-huyen" onchange="onHuyenChange(this.value)" disabled class="w-full h-11 px-3 rounded-lg border border-gray-200 outline-none input-focus-effect font-medium text-[14px] disabled:opacity-50">
                                    <option value="">-- Quận/Huyện --</option>
                                </select>
                                <input type="hidden" id="ten_quan_huyen" name="ten_quan_huyen" />
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-gray-500 uppercase mb-1 block">Phường / Xã</label>
                                <select id="select-xa" onchange="onXaChange(this.value)" disabled class="w-full h-11 px-3 rounded-lg border border-gray-200 outline-none input-focus-effect font-medium text-[14px] disabled:opacity-50">
                                    <option value="">-- Phường/Xã --</option>
                                </select>
                                <input type="hidden" id="ten_phuong_xa" name="ten_phuong_xa" />
                            </div>
                        </div>
                    </div>

                    {{-- Address Detail Input --}}
                    <div class="relative">
                        <label class="text-[11px] font-bold text-gray-500 uppercase mb-1 block">Số nhà & Tên đường <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input id="dia_chi_chi_tiet" name="dia_chi_chi_tiet" required onkeyup="updateAddressPreview()" class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect font-medium text-[14px]" placeholder="Ví dụ: 123 CAT HUNG..." type="text"/>
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        </div>
                    </div>

                    {{-- Map Preview Container --}}
                    <div class="relative h-[220px] rounded-lg overflow-hidden border border-gray-200 bg-gray-100 group">
                        <iframe 
                            id="map-iframe"
                            src="https://maps.google.com/maps?q={{ urlencode('Việt Nam') }}&t=&z=14&ie=UTF8&iwloc=&output=embed" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                        <div class="absolute bottom-3 left-3 right-3 bg-white/95 backdrop-blur-sm p-2.5 rounded-lg shadow-md border border-gray-200 pointer-events-none transition-opacity duration-300">
                            <div class="flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-secondary mt-0.5 text-[20px]">map</span>
                                <div>
                                    <p class="text-[12px] font-bold text-on-surface">Đang chọn vị trí:</p>
                                    <p class="text-[12px] text-gray-600 leading-tight" id="address-preview-text">Chưa chọn vị trí</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="kinh_do" name="kinh_do" value="106.7009" />
                    <input type="hidden" id="vi_do" name="vi_do" value="10.7769" />
                </div>
            </section>
        </div>
        </div>


    </form>
</main>
@endsection

@push('scripts')
<script>
    function selectType(typeVal, element, sync24h = true) {
        document.getElementById('loai_hinh_kinh_doanh').value = typeVal;

        document.querySelectorAll('.business-type-card').forEach(card => {
            card.classList.remove('active');
        });

        element.classList.add('active');
        
        if (sync24h) {
            const is24hCb = document.getElementById('is_24h');
            if (typeVal === 'Quán Đêm 24/7') {
                if (!is24hCb.checked) {
                    is24hCb.checked = true;
                    toggle24h(true, false);
                }
            }
        }
    }

    function toggle24h(is24h, syncCategory = true) {
        const moCua = document.querySelector('input[name="gio_mo_cua"]');
        const dongCua = document.querySelector('input[name="gio_dong_cua"]');
        const container = document.getElementById('time-select-container');

        if (is24h) {
            if (moCua) {
                moCua.value = '00:00';
                moCua.readOnly = true;
            }
            if (dongCua) {
                dongCua.value = '23:59';
                dongCua.readOnly = true;
            }
            container.classList.add('opacity-50', 'pointer-events-none');
            
            if (syncCategory) {
                const btn247 = Array.from(document.querySelectorAll('.business-type-card')).find(b => b.textContent.includes('24/7'));
                if (btn247 && !btn247.classList.contains('active')) {
                    selectType('Quán Đêm 24/7', btn247, false);
                }
            }
        } else {
            if (moCua) {
                moCua.value = '08:00';
                moCua.readOnly = false;
            }
            if (dongCua) {
                dongCua.value = '22:00';
                dongCua.readOnly = false;
            }
            container.classList.remove('opacity-50', 'pointer-events-none');
        }
    }

    async function loadTinhThanhList() {
        try {
            const res = await fetch('/api/dia-chi/tinh-thanh');
            const result = await res.json();
            if (result.success && Array.isArray(result.data)) {
                const select = document.getElementById('select-tinh');
                select.innerHTML = '<option value="">-- Chọn Tỉnh / Thành phố --</option>';
                result.data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.code;
                    opt.textContent = item.name;
                    select.appendChild(opt);
                });
            }
        } catch (e) {
            console.error('Lỗi nạp Tỉnh/Thành:', e);
        }
    }

    async function onTinhChange(tinhCode) {
        const selectHuyen = document.getElementById('select-huyen');
        const selectXa = document.getElementById('select-xa');
        const selectTinh = document.getElementById('select-tinh');

        document.getElementById('ten_tinh_thanh').value = selectTinh.options[selectTinh.selectedIndex]?.text || '';
        selectHuyen.innerHTML = '<option value="">Đang tải...</option>';
        selectHuyen.disabled = true;
        selectXa.innerHTML = '<option value="">Phường/Xã..</option>';
        selectXa.disabled = true;

        updateAddressPreview();

        if (!tinhCode) return;

        try {
            const res = await fetch(`/api/dia-chi/quan-huyen/${tinhCode}`);
            const result = await res.json();
            selectHuyen.innerHTML = '<option value="">-- Quận/Huyện --</option>';
            if (result.success && Array.isArray(result.data)) {
                result.data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.code;
                    opt.textContent = item.name;
                    selectHuyen.appendChild(opt);
                });
                selectHuyen.disabled = false;
            }
        } catch (e) {
            console.error('Lỗi nạp Quận/Huyện:', e);
        }
    }

    async function onHuyenChange(huyenCode) {
        const selectXa = document.getElementById('select-xa');
        const selectHuyen = document.getElementById('select-huyen');

        document.getElementById('ten_quan_huyen').value = selectHuyen.options[selectHuyen.selectedIndex]?.text || '';
        selectXa.innerHTML = '<option value="">Đang tải...</option>';
        selectXa.disabled = true;

        updateAddressPreview();

        if (!huyenCode) return;

        try {
            const res = await fetch(`/api/dia-chi/phuong-xa/${huyenCode}`);
            const result = await res.json();
            selectXa.innerHTML = '<option value="">-- Phường/Xã --</option>';
            if (result.success && Array.isArray(result.data)) {
                result.data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.code;
                    opt.textContent = item.name;
                    selectXa.appendChild(opt);
                });
                selectXa.disabled = false;
            }
        } catch (e) {
            console.error('Lỗi nạp Phường/Xã:', e);
        }
    }

    function onXaChange() {
        const selectXa = document.getElementById('select-xa');
        document.getElementById('ten_phuong_xa').value = selectXa.options[selectXa.selectedIndex]?.text || '';
        updateAddressPreview();
    }

    function updateAddressPreview() {
        const street = document.getElementById('dia_chi_chi_tiet').value;
        const ward = document.getElementById('ten_phuong_xa').value;
        const district = document.getElementById('ten_quan_huyen').value;
        const city = document.getElementById('ten_tinh_thanh').value;

        const parts = [street, ward, district, city].filter(Boolean);
        const full = parts.length > 0 ? parts.join(', ') : 'Chưa chọn vị trí';
        
        const previewEl = document.getElementById('address-preview-text');
        if (previewEl) previewEl.textContent = full;

        // Update Google Maps iframe
        const iframe = document.getElementById('map-iframe');
        if (iframe && parts.length > 0) {
            iframe.src = `https://maps.google.com/maps?q=${encodeURIComponent(full)}&t=&z=16&ie=UTF8&iwloc=&output=embed`;
        }
    }

    function previewCoverPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('cover-preview-img').src = e.target.result;
                document.getElementById('cover-preview-container').classList.remove('hidden');
                document.getElementById('cover-upload-placeholder').classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeCoverPhoto() {
        document.getElementById('anh_bia').value = '';
        document.getElementById('cover-preview-container').classList.add('hidden');
        document.getElementById('cover-upload-placeholder').classList.remove('hidden');
    }

    function previewSingleGalleryPhoto(input, clickedIndex) {
        if (!input.files || input.files.length === 0) return;
        
        let files = Array.from(input.files);
        
        let fileToSlotMap = [];
        fileToSlotMap.push({ file: files[0], slot: clickedIndex });
        
        let fileIndex = 1;
        for (let i = 1; i <= 5; i++) {
            if (fileIndex >= files.length) break;
            if (i === clickedIndex) continue;
            
            const targetInput = document.getElementById(`gallery_${i}`);
            if (!targetInput.files || targetInput.files.length === 0) {
                fileToSlotMap.push({ file: files[fileIndex], slot: i });
                fileIndex++;
            }
        }
        
        if (fileIndex < files.length) {
            alert(`Đã điền đầy các ô trống. Bỏ qua ${files.length - fileIndex} ảnh thừa.`);
        }
        
        fileToSlotMap.forEach(mapping => {
            const dt = new DataTransfer();
            dt.items.add(mapping.file);
            const targetInput = document.getElementById(`gallery_${mapping.slot}`);
            targetInput.files = dt.files;
            
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById(`gallery-preview-img-${mapping.slot}`).src = e.target.result;
                document.getElementById(`gallery-preview-container-${mapping.slot}`).classList.remove('hidden');
                document.getElementById(`gallery-placeholder-${mapping.slot}`).classList.add('hidden');
            };
            reader.readAsDataURL(mapping.file);
        });
    }

    function removeGalleryPhoto(index) {
        document.getElementById(`gallery_${index}`).value = '';
        document.getElementById(`gallery-preview-container-${index}`).classList.add('hidden');
        document.getElementById(`gallery-placeholder-${index}`).classList.remove('hidden');
        document.getElementById(`gallery-preview-img-${index}`).src = '';
    }

    async function submitForm() {
        const form = document.getElementById('dang-quan-form');
        const anhBiaInput = document.getElementById('anh_bia');
        
        if (!anhBiaInput.files || anhBiaInput.files.length === 0) {
            alert('Vui lòng thêm ảnh chính cho không gian quán!');
            return;
        }

        if (!form.reportValidity()) return;

        const formData = new FormData(form);
        
        // Format prices to remove dots before submitting
        let giaNho = formData.get('gia_nho_nhat');
        if (giaNho) formData.set('gia_nho_nhat', giaNho.replace(/\./g, ''));
        let giaLon = formData.get('gia_lon_nhat');
        if (giaLon) formData.set('gia_lon_nhat', giaLon.replace(/\./g, ''));

        const btn = document.getElementById('submit-btn');
        const errGen = document.getElementById('form-error-general');

        errGen.classList.add('hidden');
        const originalBtnText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Đang lưu...';

        try {
            const response = await fetch('/chu-quan/dang-quan', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: formData
            });

            const isJsonResponse = (response.headers.get('content-type') || '').includes('application/json');
            const data = isJsonResponse ? await response.json() : null;

            if (!response.ok) {
                if (data.errors) {
                    const firstErrKey = Object.keys(data.errors)[0];
                    errGen.textContent = data.errors[firstErrKey][0];
                } else {
                    errGen.textContent = data.message || 'Đã có lỗi xảy ra. Vui lòng kiểm tra lại.';
                }
                errGen.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalBtnText;
                return;
            }

            btn.innerHTML = '<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: \'FILL\' 1;">check_circle</span> Hoàn tất!';
            setTimeout(() => {
                window.location.href = data.redirect_to || '/';
            }, 1000);
        } catch (e) {
            errGen.textContent = 'Lỗi kết nối hệ thống.';
            errGen.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalBtnText;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadTinhThanhList();
    });
</script>
@endpush
