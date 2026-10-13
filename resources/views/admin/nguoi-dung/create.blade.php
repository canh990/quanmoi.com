@extends('admin.layout')

@section('title', 'Tạo Mới Người Dùng - Quán Mới Admin')
@section('page-title', 'Tạo Mới Người Dùng')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white p-6 md:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <h3 class="font-bold text-lg text-gray-800">Thêm người dùng mới</h3>
            <a href="{{ route('admin.nguoi-dung.index') }}" class="text-sm text-gray-500 hover:text-primary flex items-center gap-1">
                <span class="material-symbols-outlined text-lg">arrow_back</span> Quay lại
            </a>
        </div>

        <form action="{{ route('admin.nguoi-dung.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Avatar Upload Section --}}
            <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 border border-gray-200">
                <div class="relative shrink-0">
                    <img id="avatar-preview" src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80" class="w-16 h-16 rounded-full object-cover border border-gray-300" alt="Avatar preview">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ảnh Đại Diện</label>
                    <input type="file" name="anh_dai_dien" id="avatar-input" accept="image/*" class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary/90 cursor-pointer">
                    <p class="text-[11px] text-gray-400 mt-1">Định dạng JPG, PNG, WEBP. Tối đa 5MB.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" name="ho_ten" value="{{ old('ho_ten') }}" required placeholder="Nhập họ và tên..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                    @error('ho_ten') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="Nhập địa chỉ email..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="mat_khau" required placeholder="Tối thiểu 6 ký tự..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                    @error('mat_khau') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Số điện thoại</label>
                    <input type="text" name="so_dien_thoai" value="{{ old('so_dien_thoai') }}" placeholder="Nhập số điện thoại..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                    @error('so_dien_thoai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Giới tính</label>
                    <select name="gioi_tinh" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                        <option value="">-- Chưa chọn --</option>
                        <option value="nam" {{ old('gioi_tinh') === 'nam' ? 'selected' : '' }}>Nam</option>
                        <option value="nu" {{ old('gioi_tinh') === 'nu' ? 'selected' : '' }}>Nữ</option>
                        <option value="khac" {{ old('gioi_tinh') === 'khac' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Ngày sinh</label>
                    <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Địa chỉ</label>
                    <input type="text" name="dia_chi" value="{{ old('dia_chi') }}" placeholder="Nhập địa chỉ..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Vai trò hệ thống</label>
                    <select name="vai_tro_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                        <option value="">-- Mặc định (Thành viên) --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('vai_tro_id') == $role->id ? 'selected' : '' }}>{{ $role->ten }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Trạng thái tài khoản</label>
                    <select name="trang_thai" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                        <option value="hoat_dong" {{ old('trang_thai', 'hoat_dong') === 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="bi_khoa" {{ old('trang_thai') === 'bi_khoa' ? 'selected' : '' }}>Bị khóa</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Cấp Tick Xanh</label>
                    <div class="flex items-center h-[42px] px-4 rounded-xl border border-gray-200 bg-gray-50">
                        <label class="flex items-center gap-2 cursor-pointer w-full">
                            <input type="hidden" name="da_xac_thuc" value="0">
                            <input type="checkbox" name="da_xac_thuc" value="1" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary" {{ old('da_xac_thuc') ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-700 flex items-center gap-1">Xác thực ngay <span class="material-symbols-outlined text-blue-500 text-[16px]" style="font-variation-settings: 'FILL' 1;">verified</span></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.nguoi-dung.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-100 transition-all">Hủy</a>
                <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-primary/90 transition-all flex items-center gap-1">
                    <span class="material-symbols-outlined text-lg">add</span> Tạo Người Dùng
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('avatar-input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('avatar-preview').src = evt.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
