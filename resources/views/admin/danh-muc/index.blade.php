@extends('admin.layout')

@section('title', 'Quản lý Danh mục Quán')
@section('page-title', 'Quản lý Danh mục Quán')

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">Danh mục quán</h3>
            <a href="{{ route('admin.danh-muc.create') }}" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Thêm mới
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-bold border-b border-gray-200">Tên danh mục</th>
                        <th class="px-6 py-4 font-bold border-b border-gray-200">Slug</th>
                        <th class="px-6 py-4 font-bold border-b border-gray-200">Ngày tạo</th>
                        <th class="px-6 py-4 font-bold border-b border-gray-200 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($danhMucs as $dm)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $dm->ten_danh_muc }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $dm->slug }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $dm->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.danh-muc.edit', $dm->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form action="{{ route('admin.danh-muc.destroy', $dm->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
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
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">category</span>
                                <p>Không tìm thấy danh mục nào.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($danhMucs->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $danhMucs->links() }}
            </div>
        @endif
    </div>
@endsection
