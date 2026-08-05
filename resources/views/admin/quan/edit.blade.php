@extends('admin.layout')

@section('title', 'Chỉnh Sửa Quán - Quán Mới Admin')
@section('page-title', 'Chỉnh Sửa & Duyệt Bài Địa Điểm Quán')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white p-6 md:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div>
                <h3 class="font-bold text-lg text-gray-800">Quán: {{ $quan->ten_quan }}</h3>
                <p class="text-xs text-gray-400">Chủ sở hữu: {{ $quan->chuQuan?->ho_ten ?? 'Không rõ' }} ({{ $quan->chuQuan?->email }})</p>
            </div>
            <a href="{{ route('admin.quan.index') }}" class="text-sm text-gray-500 hover:text-primary flex items-center gap-1">
                <span class="material-symbols-outlined text-lg">arrow_back</span> Quay lại
            </a>
        </div>

        <form action="{{ route('admin.quan.update', $quan->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tên quán <span class="text-red-500">*</span></label>
                    <input type="text" name="ten_quan" value="{{ old('ten_quan', $quan->ten_quan) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                    @error('ten_quan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Loại hình kinh doanh <span class="text-red-500">*</span></label>
                    <input type="text" name="loai_hinh_kinh_doanh" value="{{ old('loai_hinh_kinh_doanh', $quan->loai_hinh_kinh_doanh) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                    @error('loai_hinh_kinh_doanh') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Số điện thoại liên hệ <span class="text-red-500">*</span></label>
                    <input type="text" name="so_dien_thoai" value="{{ old('so_dien_thoai', $quan->so_dien_thoai) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Email quán</label>
                    <input type="email" name="email" value="{{ old('email', $quan->email) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Địa chỉ chi tiết <span class="text-red-500">*</span></label>
                <input type="text" name="dia_chi_chi_tiet" value="{{ old('dia_chi_chi_tiet', $quan->dia_chi_chi_tiet) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tỉnh / Thành phố</label>
                    <input type="text" name="ten_tinh_thanh" value="{{ old('ten_tinh_thanh', $quan->ten_tinh_thanh) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Quận / Huyện</label>
                    <input type="text" name="ten_quan_huyen" value="{{ old('ten_quan_huyen', $quan->ten_quan_huyen) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Phường / Xã</label>
                    <input type="text" name="ten_phuong_xa" value="{{ old('ten_phuong_xa', $quan->ten_phuong_xa) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Trạng thái duyệt quán <span class="text-red-500">*</span></label>
                    <select name="trang_thai" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white font-bold">
                        <option value="chua_duyet" {{ old('trang_thai', $quan->trang_thai) === 'chua_duyet' ? 'selected' : '' }}>⏳ Chờ duyệt bài</option>
                        <option value="da_duyet" {{ old('trang_thai', $quan->trang_thai) === 'da_duyet' ? 'selected' : '' }}>✅ Đã duyệt (Đang hoạt động)</option>
                        <option value="bi_khoa" {{ old('trang_thai', $quan->trang_thai) === 'bi_khoa' ? 'selected' : '' }}>🔒 Bị khóa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Giờ mở cửa</label>
                    <x-time-select name="gio_mo_cua" value="{{ old('gio_mo_cua', $quan->gio_mo_cua) }}" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Giờ đóng cửa</label>
                    <x-time-select name="gio_dong_cua" value="{{ old('gio_dong_cua', $quan->gio_dong_cua) }}" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mô tả quán</label>
                <textarea name="mo_ta" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">{{ old('mo_ta', $quan->mo_ta) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.quan.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-100 transition-all">Hủy</a>
                <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-primary/90 transition-all">Lưu Cập Nhật Quán</button>
            </div>
        </form>
    </div>
</div>
@endsection
