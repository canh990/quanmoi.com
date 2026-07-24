@extends('admin.layout')

@section('title', 'Quản lý SEO')
@section('page-title', 'Quản lý SEO')

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-lg font-bold text-gray-800">Danh sách dữ liệu SEO</h3>
            <a href="{{ route('admin.seo-datas.create') }}" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Thêm mới
            </a>
        </div>

        <div class="p-4 md:p-6 bg-gray-50 border-b border-gray-200">
            <form action="{{ route('admin.seo-datas.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tìm kiếm</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tên SEO hoặc URL..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:border-primary">
                </div>
                <div class="w-full md:w-48">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Loại trang</label>
                    <select name="page_type" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:border-primary bg-white">
                        <option value="">Tất cả</option>
                        <option value="type" {{ request('page_type') == 'type' ? 'selected' : '' }}>Theo loại hình</option>
                        <option value="location" {{ request('page_type') == 'location' ? 'selected' : '' }}>Theo tỉnh thành</option>
                        <option value="all" {{ request('page_type') == 'all' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-700 transition-colors">
                    Lọc
                </button>
                <a href="{{ route('admin.seo-datas.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-300 transition-colors">
                    Xóa lọc
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-bold border-b border-gray-200">Loại trang</th>
                        <th class="px-6 py-4 font-bold border-b border-gray-200">Tên SEO</th>
                        <th class="px-6 py-4 font-bold border-b border-gray-200">URL</th>
                        <th class="px-6 py-4 font-bold border-b border-gray-200">Meta Title</th>
                        <th class="px-6 py-4 font-bold border-b border-gray-200">Trạng thái</th>
                        <th class="px-6 py-4 font-bold border-b border-gray-200 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($seoDatas as $seo)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md text-xs font-semibold">
                                    {{ $seo->page_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $seo->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <a href="{{ $seo->link }}" target="_blank" class="text-primary hover:underline flex items-center gap-1">
                                    {{ $seo->link }}
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs" title="{{ $seo->meta_title }}">
                                {{ $seo->meta_title }}
                            </td>
                            <td class="px-6 py-4">
                                @if($seo->is_active)
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-md text-xs font-semibold border border-emerald-200">Kích hoạt</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 px-2.5 py-1 rounded-md text-xs font-semibold border border-gray-200">Đã tắt</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.seo-datas.edit', $seo->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form action="{{ route('admin.seo-datas.destroy', $seo->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa dữ liệu SEO này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Xóa">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">search_off</span>
                                <p>Không tìm thấy dữ liệu SEO nào.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($seoDatas->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $seoDatas->links() }}
            </div>
        @endif
    </div>
@endsection
