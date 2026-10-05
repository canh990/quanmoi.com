@extends('admin.layout')

@section('title', 'Quản Lý Quán Nổi Bật - Quán Mới Admin')
@section('page-title', 'Danh Sách Địa Điểm Quán Nổi Bật')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500 fill-1">star</span>
            Địa Điểm Quán Đang Được Đánh Dấu Nổi Bật ({{ $featuredQuan->total() }})
        </h3>
        <p class="text-xs text-slate-500 mb-6">Các quán được ghim nổi bật trên Slider trang chủ và vị trí ưu tiên tìm kiếm.</p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="p-3">Tên Quán</th>
                        <th class="p-3">Chủ Quán</th>
                        <th class="p-3">Địa Chỉ</th>
                        <th class="p-3 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($featuredQuan as $q)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-900 flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-500 fill-1 text-[18px]">star</span>
                                {{ $q->ten_quan }}
                            </td>
                            <td class="p-3 font-medium">{{ $q->chuQuan->ho_ten ?? 'N/A' }}</td>
                            <td class="p-3 text-slate-600">{{ Str::limit($q->dia_chi, 40) }}</td>
                            <td class="p-3 text-right">
                                <form action="{{ route('admin.quan-noi-bat.toggle', $q->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">star_half</span>
                                        Hủy Nổi Bật
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-xs text-slate-500">Chưa có quán nào được đánh dấu nổi bật.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $featuredQuan->links() }}</div>
    </div>
</div>
@endsection
