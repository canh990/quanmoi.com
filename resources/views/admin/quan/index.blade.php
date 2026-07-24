@extends('admin.layout')

@section('title', 'Quản Lý Quán - Quán Mới Admin')
@section('page-title', 'Quản Lý Địa Điểm Quán & Duyệt Bài')

@section('content')
<div class="space-y-6">
    {{-- Search & Filters --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.quan.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-72">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tên quán, địa chỉ..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
            </div>

            <select name="status" class="px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="chua_duyet" {{ request('status') == 'chua_duyet' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="da_duyet" {{ request('status') == 'da_duyet' ? 'selected' : '' }}>Đã duyệt (Đang hoạt động)</option>
                <option value="bi_khoa" {{ request('status') == 'bi_khoa' ? 'selected' : '' }}>Bị khóa</option>
                <option value="soft_deleted" {{ request('status') == 'soft_deleted' ? 'selected' : '' }}>Đã xóa (Soft-delete)</option>
            </select>

            <button type="submit" class="bg-primary text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-primary/90 transition-all flex items-center gap-1">
                <span class="material-symbols-outlined text-lg">filter_alt</span> Lọc
            </button>
        </form>
    </div>

    {{-- Venues Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase">
                        <th class="py-3.5 px-4">Tên Quán & Loại Hình</th>
                        <th class="py-3.5 px-4">Chủ Sở Hữu</th>
                        <th class="py-3.5 px-4">Địa Chỉ</th>
                        <th class="py-3.5 px-4">Trạng Thái</th>
                        <th class="py-3.5 px-4 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($quanList as $quan)
                        <tr class="hover:bg-gray-50/80 transition-colors {{ $quan->trashed() ? 'bg-red-50/30' : '' }}">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $quan->anh_bia ? asset('storage/' . $quan->anh_bia) : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=100&q=80' }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200" alt="" />
                                    <div>
                                        <p class="font-bold text-gray-900 leading-snug">{{ $quan->ten_quan }}</p>
                                        <span class="inline-block bg-orange-100 text-primary px-2 py-0.5 rounded text-[11px] font-bold mt-0.5">
                                            {{ $quan->loai_hinh_kinh_doanh ?: 'Quán ăn' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-medium text-gray-800">{{ $quan->chuQuan?->ho_ten ?: 'Ẩn / Không rõ' }}</p>
                                <p class="text-xs text-gray-400">{{ $quan->chuQuan?->email }}</p>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs truncate">
                                <p class="text-gray-700 truncate" title="{{ $quan->dia_chi_chi_tiet }}">{{ $quan->dia_chi_chi_tiet }}</p>
                                <p class="text-xs text-gray-400">{{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($quan->trashed())
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Đã xóa (Soft Deleted)</span>
                                @elseif($quan->trang_thai === 'chua_duyet')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">⏳ Chờ duyệt</span>
                                @elseif($quan->trang_thai === 'bi_khoa')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-700">🔒 Bị khóa</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">✅ Đã duyệt</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.quan.edit', $quan->id) }}" class="p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-all" title="Chỉnh sửa & Duyệt">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>

                                    @if($quan->trashed())
                                        <form action="{{ route('admin.quan.restore', $quan->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Khôi phục quán">
                                                <span class="material-symbols-outlined text-lg">restore_from_trash</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.quan.destroy', $quan->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tạm quán này?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Xóa tạm (Soft-delete)">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">Không tìm thấy địa điểm quán nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $quanList->links() }}
        </div>
    </div>
</div>
@endsection
