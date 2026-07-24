@extends('admin.layout')

@section('title', isset($danhMuc) ? 'Chỉnh sửa Danh mục' : 'Thêm mới Danh mục')
@section('page-title', isset($danhMuc) ? 'Chỉnh sửa Danh mục' : 'Thêm mới Danh mục')

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <form action="{{ isset($danhMuc) ? route('admin.danh-muc.update', $danhMuc->id) : route('admin.danh-muc.store') }}" method="POST" class="max-w-xl">
            @csrf
            @if(isset($danhMuc))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tên danh mục <span class="text-red-500">*</span></label>
                    <input type="text" name="ten_danh_muc" value="{{ old('ten_danh_muc', $danhMuc->ten_danh_muc ?? '') }}" placeholder="Vd: Quán ăn, Quán nước, Bida..." class="w-full px-4 py-3 rounded-xl border {{ $errors->has('ten_danh_muc') ? 'border-red-500' : 'border-gray-300' }} outline-none focus:border-primary" required>
                    @error('ten_danh_muc')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-xl font-bold transition-colors">
                        {{ isset($danhMuc) ? 'Cập nhật' : 'Lưu danh mục' }}
                    </button>
                    <a href="{{ route('admin.danh-muc.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-3 rounded-xl font-bold transition-colors">
                        Hủy bỏ
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
