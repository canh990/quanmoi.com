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

        <div class="grid grid-cols-12 gap-6 lg:gap-8">
            {{-- Left Column: Form Fields --}}
            <section class="col-span-12 lg:col-span-7 flex flex-col gap-6">
                
                {{-- Progress Stepper --}}
                <div class="flex items-center gap-4 mb-1 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">1</span>
                        <span class="text-[16px] font-bold text-primary">Thông tin cơ bản</span>
                    </div>
                    <div class="h-px bg-gray-300 flex-1"></div>
                    <div class="flex items-center gap-2 opacity-50">
                        <span class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-bold text-sm">2</span>
                        <span class="text-[16px] font-bold text-gray-600">Thực đơn</span>
                    </div>
                </div>

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
                            <label class="text-[12px] font-bold text-gray-500 tracking-wider uppercase mb-2 block">GIỜ MỞ CỬA <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2">
                                <input id="gio_mo_cua" name="gio_mo_cua" required class="flex-1 px-3 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect text-[16px] font-bold" type="time" value="08:00"/>
                                <span class="text-gray-400 font-bold">-</span>
                                <input id="gio_dong_cua" name="gio_dong_cua" required class="flex-1 px-3 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect text-[16px] font-bold" type="time" value="22:00"/>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="gia_nho_nhat" class="text-[12px] font-bold text-gray-500 tracking-wider uppercase mb-2 block">GIÁ THẤP NHẤT (VNĐ)</label>
                            <input id="gia_nho_nhat" name="gia_nho_nhat" type="number" min="0" step="1000" class="w-full px-4 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect text-[16px]" placeholder="30000"/>
                        </div>
                        <div>
                            <label for="gia_lon_nhat" class="text-[12px] font-bold text-gray-500 tracking-wider uppercase mb-2 block">GIÁ CAO NHẤT (VNĐ)</label>
                            <input id="gia_lon_nhat" name="gia_lon_nhat" type="number" min="0" step="1000" class="w-full px-4 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect text-[16px]" placeholder="150000"/>
                        </div>
                    </div>

                    <div>
                        <label for="mo_ta" class="text-[12px] font-bold text-gray-500 tracking-wider uppercase mb-2 block">MÔ TẢ NGẮN</label>
                        <textarea id="mo_ta" name="mo_ta" class="w-full px-4 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect text-[16px] resize-none" placeholder="Chia sẻ về không gian, món đặc trưng hoặc ưu đãi của bạn..." rows="4"></textarea>
                    </div>
                </div>

                {{-- CTA Desktop --}}
                <div class="flex justify-end pt-2">
                    <button id="submit-btn" type="submit" class="bg-primary text-white px-8 py-4 rounded-xl font-bold text-[16px] shadow-lg hover:shadow-xl hover:bg-primary/90 active:scale-[0.98] transition-all flex items-center gap-2 cursor-pointer">
                        Tiếp tục thiết lập thực đơn
                        <span class="material-symbols-outlined">arrow_forward</span>
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
                            <input type="file" id="anh_bia" name="anh_bia" accept="image/*" class="hidden" onchange="previewCoverPhoto(this)" />
                            
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
                        <div class="aspect-[4/3] rounded-lg border-2 border-dashed border-gray-200 flex flex-col items-center justify-center gap-1 text-gray-400 hover:border-primary hover:text-primary transition-all cursor-pointer" onclick="document.getElementById('anh_bia').click()">
                            <span class="material-symbols-outlined text-2xl">add_a_photo</span>
                            <span class="text-[11px] font-bold">Thêm ảnh</span>
                        </div>
                        <div class="aspect-[4/3] rounded-lg border border-gray-100 bg-gray-50/50 flex flex-col items-center justify-center text-gray-300">
                            <span class="material-symbols-outlined text-xl">image</span>
                        </div>
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
                            <input id="dia_chi_chi_tiet" name="dia_chi_chi_tiet" required onkeyup="updateAddressPreview()" class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 outline-none input-focus-effect font-medium text-[14px]" placeholder="Ví dụ: 123 Phố Huế..." type="text"/>
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        </div>
                    </div>

                    {{-- Map Preview Container --}}
                    <div class="relative h-[220px] rounded-lg overflow-hidden border border-gray-200 bg-gray-100 group">
                        <img class="w-full h-full object-cover grayscale-[0.2] group-hover:grayscale-0 transition-all duration-500" src="https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&w=800&q=80" alt="Bản đồ vị trí" />
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-12 h-12 text-primary animate-bounce">
                                <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1;">location_on</span>
                            </div>
                        </div>
                        <div class="absolute bottom-3 left-3 right-3 bg-white/95 backdrop-blur-sm p-2.5 rounded-lg shadow-md border border-gray-200">
                            <div class="flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-secondary mt-0.5 text-[20px]">map</span>
                                <div>
                                    <p class="text-[12px] font-bold text-on-surface">Đang chọn vị trí:</p>
                                    <p class="text-[12px] text-gray-600 leading-tight" id="address-preview-text">123 Phố Huế, Hai Bà Trưng, Hà Nội</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="kinh_do" name="kinh_do" value="106.7009" />
                    <input type="hidden" id="vi_do" name="vi_do" value="10.7769" />
                </div>
            </section>
        </div>
    </form>
</main>
@endsection

@push('scripts')
<script>
    function selectType(typeVal, element) {
        document.getElementById('loai_hinh_kinh_doanh').value = typeVal;

        document.querySelectorAll('.business-type-card').forEach(card => {
            card.classList.remove('active');
        });

        element.classList.add('active');
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
        const full = parts.length > 0 ? parts.join(', ') : '123 Phố Huế, Hai Bà Trưng, Hà Nội';
        
        const previewEl = document.getElementById('address-preview-text');
        if (previewEl) previewEl.textContent = full;
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

    async function submitForm() {
        const form = document.getElementById('dang-quan-form');
        const formData = new FormData(form);
        const btn = document.getElementById('submit-btn');
        const errGen = document.getElementById('form-error-general');

        errGen.classList.add('hidden');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Đang xử lý...';

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
                btn.innerHTML = 'Tiếp tục thiết lập thực đơn <span class="material-symbols-outlined">arrow_forward</span>';
                return;
            }

            btn.innerHTML = '<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: \'FILL\' 1;">check_circle</span> Đã Đăng Quán!';
            setTimeout(() => {
                window.location.href = data.redirect_to || '/';
            }, 1000);
        } catch (e) {
            errGen.textContent = 'Lỗi kết nối hệ thống.';
            errGen.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = 'Tiếp tục thiết lập thực đơn <span class="material-symbols-outlined">arrow_forward</span>';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadTinhThanhList();
    });
</script>
<script>
    async function submitForm() {
        const form = document.getElementById('dang-quan-form');
        const formData = new FormData(form);
        const btn = document.getElementById('submit-btn');
        const errGen = document.getElementById('form-error-general');
        const defaultBtnHtml = 'Tiáº¿p tá»¥c thiáº¿t láº­p thá»±c Ä‘Æ¡n <span class="material-symbols-outlined">arrow_forward</span>';

        errGen.classList.add('hidden');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Äang xá»­ lÃ½...';

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
                if (response.status === 401) {
                    errGen.textContent = 'Phiên đăng nhập đã hết. Đang chuyển bạn đến trang đăng nhập...';
                    errGen.classList.remove('hidden');
                    setTimeout(() => {
                        window.location.href = '{{ route('login') }}';
                    }, 1200);
                    return;
                }

                if (response.status === 419) {
                    errGen.textContent = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
                    errGen.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerHTML = defaultBtnHtml;
                    return;
                }

                if (data?.errors) {
                    const firstErrKey = Object.keys(data.errors)[0];
                    errGen.textContent = data.errors[firstErrKey][0];
                } else {
                    errGen.textContent = data?.message || 'Đã có lỗi xảy ra. Vui lòng kiểm tra lại.';
                }

                errGen.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = defaultBtnHtml;
                return;
            }

            if (!data) {
                throw new Error('Invalid JSON response');
            }

            btn.innerHTML = '<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: \'FILL\' 1;">check_circle</span> ÄÃ£ ÄÄƒng QuÃ¡n!';
            setTimeout(() => {
                window.location.href = data.redirect_to || '/';
            }, 1000);
        } catch (e) {
            errGen.textContent = 'Lỗi kết nối hệ thống.';
            errGen.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = defaultBtnHtml;
        }
    }
</script>
@endpush
