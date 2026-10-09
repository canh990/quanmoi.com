@extends('admin.layout')

@section('title', 'Quản Lý Quán - Quán Mới Admin')
@section('page-title', 'Quản Lý Địa Điểm Quán & Duyệt Bài')

@section('content')
<div class="space-y-6">
    {{-- Search & Filters (Report Matching) --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.quan.index') }}" class="flex flex-wrap items-center gap-3 w-full">
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Q   Tìm tên quán, địa chỉ..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/50">
                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
            </div>

            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:border-amber-500 bg-white text-slate-700">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="chua_duyet" {{ request('status') == 'chua_duyet' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="da_duyet" {{ request('status') == 'da_duyet' ? 'selected' : '' }}>Đã duyệt (Đang hoạt động)</option>
                <option value="bi_khoa" {{ request('status') == 'bi_khoa' ? 'selected' : '' }}>Bị khóa</option>
                <option value="soft_deleted" {{ request('status') == 'soft_deleted' ? 'selected' : '' }}>Đã xóa (Soft-delete)</option>
            </select>

            <select name="is_xac_thuc" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:border-amber-500 bg-white text-slate-700">
                <option value="">-- Tick xanh --</option>
                <option value="1" {{ request('is_xac_thuc') == '1' ? 'selected' : '' }}>Có Tick Xanh</option>
                <option value="0" {{ request('is_xac_thuc') == '0' ? 'selected' : '' }}>Chưa có Tick Xanh</option>
            </select>

            <button type="submit" class="bg-[#F59E0B] hover:bg-[#D97706] text-white px-5 py-2 rounded-xl font-bold text-xs transition-all shadow-xs flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">filter_alt</span> 
                <span>Lọc</span>
            </button>
        </form>
    </div>

    {{-- Venues Table (Report Matching) --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">TÊN QUÁN & LOẠI HÌNH</th>
                        <th class="py-3.5 px-4">CHỦ SỞ HỮU</th>
                        <th class="py-3.5 px-4">ĐỊA CHỈ</th>
                        <th class="py-3.5 px-4">TRẠNG THÁI</th>
                        <th class="py-3.5 px-4 text-right">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($quanList as $quan)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $quan->trashed() ? 'bg-rose-50/30' : '' }}">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=100&q=80' }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 flex-shrink-0" alt="" />
                                    <div>
                                        <p class="font-bold text-slate-900 leading-tight flex items-center gap-1">
                                            {{ $quan->ten_quan }}
                                            @if($quan->is_xac_thuc)
                                                <span class="material-symbols-outlined text-blue-500 text-[14px]" title="Có Tick Xanh" style="font-variation-settings: 'FILL' 1;">verified</span>
                                            @endif
                                        </p>
                                        <span class="inline-block bg-amber-50 text-amber-700 px-2 py-0.5 rounded text-[10px] font-semibold mt-0.5 border border-amber-200/60">
                                            {{ $quan->loai_hinh_kinh_doanh ?: 'Nhà hàng' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-800">{{ $quan->chuQuan?->ho_ten ?: 'Quản Trị Viên (Admin)' }}</p>
                                <p class="text-[11px] text-slate-400 font-mono leading-tight">{{ $quan->chuQuan?->email ?: 'admin@quanmoi.com' }}</p>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs text-slate-600">
                                <p class="font-medium truncate" title="{{ $quan->dia_chi_chi_tiet }}">{{ $quan->dia_chi_chi_tiet ?: '228 Love Creek Suite 319' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $quan->ten_quan_huyen ?: 'Quận Ba Đình' }}, {{ $quan->ten_tinh_thanh ?: 'Thành phố Hà Nội' }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($quan->trashed())
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200">Đã xóa</span>
                                @elseif($quan->trang_thai === 'chua_duyet')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">⏳ Chờ duyệt</span>
                                @elseif($quan->trang_thai === 'bi_khoa')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700 border border-slate-300">🔒 Bị khóa</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200/80">• Đã duyệt</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if(!$quan->trashed())
                                        <form action="{{ route('admin.quan.toggle-xac-thuc', $quan->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="p-1.5 rounded-lg transition-all {{ $quan->is_xac_thuc ? 'text-blue-500 hover:bg-blue-50' : 'text-slate-400 hover:text-blue-500 hover:bg-slate-100' }}" title="{{ $quan->is_xac_thuc ? 'Gỡ Tick Xanh' : 'Cấp Tick Xanh' }}">
                                                <span class="material-symbols-outlined text-[18px]" {!! $quan->is_xac_thuc ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>check_circle</span>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.quan.toggle-noi-bat', $quan->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="p-1.5 rounded-lg transition-all {{ $quan->is_noi_bat ? 'text-amber-500 hover:bg-amber-50' : 'text-slate-400 hover:text-amber-500 hover:bg-slate-100' }}" title="{{ $quan->is_noi_bat ? 'Gỡ khỏi danh sách nổi bật' : 'Đánh dấu nổi bật' }}">
                                                <span class="material-symbols-outlined text-[18px]" {!! $quan->is_noi_bat ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>star</span>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.quan.edit', $quan->id) }}" class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-slate-100 rounded-lg transition-all" title="Chỉnh sửa & Duyệt">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>

                                    @if($quan->trashed())
                                        <form action="{{ route('admin.quan.restore', $quan->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Khôi phục quán">
                                                <span class="material-symbols-outlined text-[18px]">restore_from_trash</span>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.quan.force-destroy', $quan->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa vĩnh viễn quán này khỏi cơ sở dữ liệu? Hành động này không thể khôi phục!');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-700 hover:bg-rose-100 rounded-lg transition-all" title="Xóa cứng (Vĩnh viễn)">
                                                <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.quan.destroy', $quan->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tạm quán này?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Xóa tạm (Soft-delete)">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Không tìm thấy địa điểm quán nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $quanList->links() }}
        </div>
    </div>
</div>
@endsection
