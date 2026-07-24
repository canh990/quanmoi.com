@extends('admin.layout')

@section('title', 'Chỉnh Sửa Người Dùng - Quán Mới Admin')
@section('page-title', 'Chỉnh Sửa Người Dùng')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white p-6 md:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <h3 class="font-bold text-lg text-gray-800">Cập nhật tài khoản: {{ $user->ho_ten }}</h3>
            <a href="{{ route('admin.nguoi-dung.index') }}" class="text-sm text-gray-500 hover:text-primary flex items-center gap-1">
                <span class="material-symbols-outlined text-lg">arrow_back</span> Quay lại
            </a>
        </div>

        <form action="{{ route('admin.nguoi-dung.update', $user->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Họ và tên <span class="text-red-500">*</span></label>
                <input type="text" name="ho_ten" value="{{ old('ho_ten', $user->ho_ten) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                @error('ho_ten') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Số điện thoại</label>
                <input type="text" name="so_dien_thoai" value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                @error('so_dien_thoai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Vai trò hệ thống</label>
                    <select name="vai_tro_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                        <option value="">-- Mặc định --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('vai_tro_id', $user->vai_tro_id) == $role->id ? 'selected' : '' }}>{{ $role->ten }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Trạng thái tài khoản</label>
                    <select name="trang_thai" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                        <option value="hoat_dong" {{ old('trang_thai', $user->trang_thai) === 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="bi_khoa" {{ old('trang_thai', $user->trang_thai) === 'bi_khoa' ? 'selected' : '' }}>Bị khóa</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Đặt lại mật khẩu mới (Bỏ trống nếu giữ nguyên)</label>
                <input type="password" name="mat_khau" placeholder="Nhập mật khẩu mới..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                @error('mat_khau') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.nguoi-dung.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-100 transition-all">Hủy</a>
                <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-primary/90 transition-all">Cập Nhật Người Dùng</button>
            </div>
        </form>
    </div>
</div>
@endsection
