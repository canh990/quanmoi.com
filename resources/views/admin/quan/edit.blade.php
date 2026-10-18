@extends('admin.layout')

@section('title', 'Chỉnh Sửa Quán - Quán Mới Admin')
@section('page-title', 'Chỉnh Sửa & Duyệt Bài Địa Điểm Quán')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white p-6 md:p-8 rounded-2xl border border-slate-200/90 shadow-2xs space-y-6">
        {{-- Header & Quick Meta --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-lg text-slate-800">{{ $quan->ten_quan }}</h3>
                    @if($quan->is_xac_thuc)
                        <span class="material-symbols-outlined text-blue-500 text-[18px] shrink-0" title="Đã có Tick Xanh xác thực" style="font-variation-settings: 'FILL' 1;">verified</span>
                    @endif
                    @if($quan->is_noi_bat)
                        <span class="material-symbols-outlined text-amber-500 text-[18px] shrink-0" title="Quán nổi bật" style="font-variation-settings: 'FILL' 1;">star</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Mã quán: <span class="font-mono text-slate-600 font-semibold">{{ $quan->id }}</span> • Slug: <span class="font-mono text-amber-600">{{ $quan->slug }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('quan.detail', ['slug' => $quan->slug]) }}" target="_blank" class="btn-enterprise btn-enterprise-secondary text-slate-700 hover:text-amber-600" title="Xem trang quán thực tế trên website">
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                    <span>Xem Trên Web</span>
                </a>
                <a href="{{ route('admin.quan.index') }}" class="btn-enterprise btn-enterprise-secondary">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Quay lại</span>
                </a>
            </div>
        </div>

        {{-- Statistics Bar --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50/70 p-3.5 rounded-xl border border-slate-200/60">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-amber-600 text-xl bg-amber-100 p-1.5 rounded-lg">visibility</span>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Lượt xem</span>
                    <span class="text-xs font-bold text-slate-800">{{ number_format($quan->luot_xem ?? 0) }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-emerald-600 text-xl bg-emerald-100 p-1.5 rounded-lg">photo_library</span>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Hình ảnh</span>
                    <span class="text-xs font-bold text-slate-800">{{ $quan->hinhAnh->count() }} ảnh</span>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-indigo-600 text-xl bg-indigo-100 p-1.5 rounded-lg">restaurant_menu</span>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Thực đơn</span>
                    <span class="text-xs font-bold text-slate-800">{{ $quan->danhMucMenu->count() }} danh mục</span>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-rose-600 text-xl bg-rose-100 p-1.5 rounded-lg">rate_review</span>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Đánh giá</span>
                    <span class="text-xs font-bold text-slate-800">{{ $quan->danhGia->count() }} ({{ $quan->danhGia->avg('so_sao') ? round($quan->danhGia->avg('so_sao'), 1) . '★' : '0★' }})</span>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.quan.update', $quan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Block 1: Thông tin cơ bản & Chủ sở hữu --}}
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-amber-500">storefront</span>
                    1. THÔNG TIN CƠ BẢN & CHỦ SỞ HỮU
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tên quán <span class="text-rose-500">*</span></label>
                        <input type="text" name="ten_quan" value="{{ old('ten_quan', $quan->ten_quan) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                        @error('ten_quan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Loại hình kinh doanh <span class="text-rose-500">*</span></label>
                        <select name="loai_hinh_kinh_doanh" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:border-amber-500 bg-white text-slate-800">
                            @foreach($businessTypes as $type)
                                <option value="{{ $type }}" {{ old('loai_hinh_kinh_doanh', $quan->loai_hinh_kinh_doanh) == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                            @if(!in_array($quan->loai_hinh_kinh_doanh, $businessTypes) && !empty($quan->loai_hinh_kinh_doanh))
                                <option value="{{ $quan->loai_hinh_kinh_doanh }}" selected>{{ $quan->loai_hinh_kinh_doanh }}</option>
                            @endif
                        </select>
                        @error('loai_hinh_kinh_doanh') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Chủ sở hữu quán (Tài khoản phụ trách)</label>
                    <select name="chu_quan_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-white text-slate-800">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('chu_quan_id', $quan->chu_quan_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->ho_ten }} ({{ $user->email }}) - {{ $user->vaiTro?->ten ?: 'Người dùng' }}
                            </option>
                        @endforeach
                    </select>
                    @error('chu_quan_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Block 2: Liên hệ --}}
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-amber-500">contact_phone</span>
                    2. THÔNG TIN LIÊN HỆ
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Số điện thoại liên hệ <span class="text-rose-500">*</span></label>
                        <input type="text" name="so_dien_thoai" value="{{ old('so_dien_thoai', $quan->so_dien_thoai) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                        @error('so_dien_thoai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email quán</label>
                        <input type="email" name="email" value="{{ old('email', $quan->email) }}" placeholder="VD: quan@example.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                        @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Block 3: Địa chỉ & Vị trí --}}
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-amber-500">pin_drop</span>
                    3. ĐỊA CHỈ & VỊ TRÍ
                </h4>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Địa chỉ chi tiết <span class="text-rose-500">*</span></label>
                    <input type="text" name="dia_chi_chi_tiet" value="{{ old('dia_chi_chi_tiet', $quan->dia_chi_chi_tiet) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                    @error('dia_chi_chi_tiet') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tỉnh / Thành phố</label>
                        <input type="text" name="ten_tinh_thanh" value="{{ old('ten_tinh_thanh', $quan->ten_tinh_thanh) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Quận / Huyện</label>
                        <input type="text" name="ten_quan_huyen" value="{{ old('ten_quan_huyen', $quan->ten_quan_huyen) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Phường / Xã</label>
                        <input type="text" name="ten_phuong_xa" value="{{ old('ten_phuong_xa', $quan->ten_phuong_xa) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kinh độ (Longitude)</label>
                        <input type="number" step="any" name="kinh_do" value="{{ old('kinh_do', $quan->kinh_do) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Vĩ độ (Latitude)</label>
                        <input type="number" step="any" name="vi_do" value="{{ old('vi_do', $quan->vi_do) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                    </div>
                </div>
            </div>

            {{-- Block 4: Hoạt động & Bảng giá --}}
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-amber-500">schedule</span>
                    4. THỜI GIAN HOẠT ĐỘNG & BẢNG GIÁ
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Giờ mở cửa</label>
                        <x-time-select name="gio_mo_cua" value="{{ old('gio_mo_cua', $quan->gio_mo_cua) }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Giờ đóng cửa</label>
                        <x-time-select name="gio_dong_cua" value="{{ old('gio_dong_cua', $quan->gio_dong_cua) }}" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Giá tối thiểu (VNĐ)</label>
                        <input type="number" name="gia_nho_nhat" value="{{ old('gia_nho_nhat', (int)$quan->gia_nho_nhat) }}" min="0" step="1000" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                        @error('gia_nho_nhat') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Giá tối đa (VNĐ)</label>
                        <input type="number" name="gia_lon_nhat" value="{{ old('gia_lon_nhat', (int)$quan->gia_lon_nhat) }}" min="0" step="1000" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                        @error('gia_lon_nhat') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Block 5: Trạng thái duyệt & Huy hiệu --}}
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-amber-500">verified</span>
                    5. TRẠNG THÁI & HUY HIỆU
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Trạng thái duyệt <span class="text-rose-500">*</span></label>
                        <select name="trang_thai" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:border-amber-500 bg-white text-slate-800">
                            <option value="da_duyet" {{ old('trang_thai', $quan->trang_thai) === 'da_duyet' ? 'selected' : '' }}>✅ Đã duyệt (Đang hoạt động)</option>
                            <option value="chua_duyet" {{ old('trang_thai', $quan->trang_thai) === 'chua_duyet' ? 'selected' : '' }}>⏳ Chờ duyệt bài</option>
                            <option value="bi_khoa" {{ old('trang_thai', $quan->trang_thai) === 'bi_khoa' ? 'selected' : '' }}>🔒 Bị khóa</option>
                        </select>
                        @error('trang_thai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="relative flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="is_xac_thuc" value="1" {{ old('is_xac_thuc', $quan->is_xac_thuc) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <span class="text-xs font-bold text-slate-700 flex items-center gap-1">
                                <span class="material-symbols-outlined text-blue-500 text-[18px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                Cấp Tick Xanh xác thực
                            </span>
                        </label>
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="relative flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="is_noi_bat" value="1" {{ old('is_noi_bat', $quan->is_noi_bat) ? 'checked' : '' }} class="w-4 h-4 text-amber-600 border-slate-300 rounded focus:ring-amber-500">
                            <span class="text-xs font-bold text-slate-700 flex items-center gap-1">
                                <span class="material-symbols-outlined text-amber-500 text-[18px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                Đánh dấu Quán Nổi Bật
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Block 6: Kênh mạng xã hội & Đặt món --}}
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-amber-500">link</span>
                    6. LIÊN KẾT MẠNG XÃ HỘI & GIAO HÀNG
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Đường dẫn TikTok Video / Kênh</label>
                        <input type="url" name="tiktok_url" value="{{ old('tiktok_url', $quan->tiktok_url) }}" placeholder="https://www.tiktok.com/@quanmoi" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                        @error('tiktok_url') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Đường dẫn ShopeeFood / Đặt món</label>
                        <input type="url" name="shopeefood_url" value="{{ old('shopeefood_url', $quan->shopeefood_url) }}" placeholder="https://shopeefood.vn/ha-noi/..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">
                        @error('shopeefood_url') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Block 7: Ảnh bìa & Giới thiệu --}}
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-amber-500">image</span>
                    7. HÌNH ẢNH BÌA & MÔ TẢ
                </h4>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ảnh đại diện / Ảnh bìa quán</label>
                    <div class="flex items-center gap-4">
                        <div id="image-preview-container" class="w-24 h-24 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0">
                            <img src="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=100&q=80' }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=100&q=80';" class="w-full h-full object-cover rounded-xl" alt="{{ $quan->ten_quan }}">
                        </div>
                        <div class="flex-1">
                            <input type="file" name="anh_bia" id="anh_bia_input" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 file:cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-1">Tải ảnh mới lên nếu muốn thay đổi ảnh bìa hiện tại (JPG, PNG, WEBP, tối đa 5MB).</p>
                        </div>
                    </div>
                    @error('anh_bia') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Gallery Preview if any --}}
                @if($quan->hinhAnh->count() > 0)
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Bộ sưu tập hình ảnh quán ({{ $quan->hinhAnh->count() }} ảnh)</label>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach($quan->hinhAnh->take(6) as $img)
                                <img src="{{ $img->duong_dan_url ?: $img->duong_dan }}" onerror="this.style.display='none'" class="w-16 h-16 rounded-lg object-cover border border-slate-200" alt="Gallery">
                            @endforeach
                            @if($quan->hinhAnh->count() > 6)
                                <div class="w-16 h-16 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-500">
                                    +{{ $quan->hinhAnh->count() - 6 }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mô tả quán ăn</label>
                    <textarea name="mo_ta" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/40">{{ old('mo_ta', $quan->mo_ta) }}</textarea>
                    @error('mo_ta') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.quan.index') }}" class="btn-enterprise btn-enterprise-secondary">Hủy</a>
                <button type="submit" class="bg-[#F59E0B] hover:bg-[#D97706] text-white px-6 py-2.5 rounded-xl font-bold text-xs transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Lưu Cập Nhật Quán</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('anh_bia_input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewContainer = document.getElementById('image-preview-container');
        if (file && previewContainer) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                previewContainer.innerHTML = `<img src="${evt.target.result}" class="w-full h-full object-cover rounded-xl" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
