@extends('admin.layout')

@section('title', 'Danh mục Blog - Quán Mới Admin')
@section('page-title', 'Danh Mục Blog')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Form Thêm mới -->
    <div class="md:col-span-1">
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Thêm danh mục</h3>
            <form action="{{ route('admin.blog.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full rounded-xl border border-gray-300 px-3 py-2 outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-gray-300 px-3 py-2 outline-none focus:border-primary resize-none"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="status" id="status" value="1" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                    <label for="status" class="text-sm text-gray-700">Kích hoạt</label>
                </div>
                <button type="submit" class="w-full py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-hover transition-colors">
                    Thêm mới
                </button>
            </form>
        </div>
    </div>

    <!-- Danh sách -->
    <div class="md:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-4">Tên danh mục</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Số bài viết</th>
                        <th class="px-6 py-4">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($categories as $category)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $category->slug }}</td>
                        <td class="px-6 py-4">{{ $category->blogs_count }}</td>
                        <td class="px-6 py-4">
                            @if($category->status)
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Hoạt động</span>
                            @else
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">Tạm ẩn</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-100">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
