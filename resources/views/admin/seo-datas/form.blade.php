@extends('admin.layout')

@section('title', isset($seoData) ? 'Chỉnh sửa SEO Data' : 'Thêm mới SEO Data')
@section('page-title', isset($seoData) ? 'Chỉnh sửa SEO Data' : 'Thêm mới SEO Data')

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <form action="{{ isset($seoData) ? route('admin.seo-datas.update', $seoData->id) : route('admin.seo-datas.store') }}" method="POST" class="max-w-3xl">
            @csrf
            @if(isset($seoData))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <!-- Row 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Loại trang <span class="text-red-500">*</span></label>
                        <select name="page_type" class="w-full px-4 py-3 rounded-xl border {{ $errors->has('page_type') ? 'border-red-500' : 'border-gray-300' }} outline-none focus:border-primary" required>
                            <option value="type" {{ old('page_type', $seoData->page_type ?? '') == 'type' ? 'selected' : '' }}>Theo loại hình</option>
                            <option value="location" {{ old('page_type', $seoData->page_type ?? '') == 'location' ? 'selected' : '' }}>Theo tỉnh thành</option>
                            <option value="all" {{ old('page_type', $seoData->page_type ?? '') == 'all' ? 'selected' : '' }}>Tất cả</option>
                        </select>
                        @error('page_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tên SEO <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $seoData->name ?? '') }}" class="w-full px-4 py-3 rounded-xl border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} outline-none focus:border-primary" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Loại hình kinh doanh</label>
                        <input type="text" name="loai_hinh_kinh_doanh" value="{{ old('loai_hinh_kinh_doanh', $seoData->loai_hinh_kinh_doanh ?? '') }}" placeholder="Vd: Nhà hàng, Quán cà phê..." class="w-full px-4 py-3 rounded-xl border {{ $errors->has('loai_hinh_kinh_doanh') ? 'border-red-500' : 'border-gray-300' }} outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mã Tỉnh thành</label>
                        <input type="text" name="province_code" value="{{ old('province_code', $seoData->province_code ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mã Quận huyện</label>
                        <input type="text" name="district_code" value="{{ old('district_code', $seoData->district_code ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 outline-none focus:border-primary">
                    </div>
                </div>

                <!-- Row 3 -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">URL <span class="text-red-500">*</span></label>
                    <input type="text" name="link" value="{{ old('link', $seoData->link ?? '') }}" placeholder="Vd: /nha-hang-tai-ho-chi-minh" class="w-full px-4 py-3 rounded-xl border {{ $errors->has('link') ? 'border-red-500' : 'border-gray-300' }} outline-none focus:border-primary" required>
                    @error('link')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Row 4 -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Meta Title <span class="text-red-500">*</span></label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $seoData->meta_title ?? '') }}" class="w-full px-4 py-3 rounded-xl border {{ $errors->has('meta_title') ? 'border-red-500' : 'border-gray-300' }} outline-none focus:border-primary" required>
                    @error('meta_title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Row 5 -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description" rows="4" class="w-full px-4 py-3 rounded-xl border {{ $errors->has('meta_description') ? 'border-red-500' : 'border-gray-300' }} outline-none focus:border-primary">{{ old('meta_description', $seoData->meta_description ?? '') }}</textarea>
                </div>

                <!-- Row 6 -->
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $seoData->is_active ?? 1) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm font-bold text-gray-700">Kích hoạt</span>
                    </label>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-xl font-bold transition-colors">
                        {{ isset($seoData) ? 'Cập nhật' : 'Lưu dữ liệu' }}
                    </button>
                    <a href="{{ route('admin.seo-datas.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-3 rounded-xl font-bold transition-colors">
                        Hủy bỏ
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
