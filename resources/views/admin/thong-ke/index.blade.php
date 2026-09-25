@extends('admin.layout')

@section('title', 'Thống Kê Chi Tiết - Quán Mới Admin')
@section('page-title', 'Thống Kê Chi Tiết Hệ Thống')

@section('content')
<div class="space-y-6">

    {{-- Header & Date Filter Bar --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500">analytics</span>
                Báo Cáo & Thống Kê Chi Tiết
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Lọc dữ liệu hoạt động, tăng trưởng và doanh thu theo khoảng thời gian tùy chỉnh</p>
        </div>

        {{-- Filter & Export Actions --}}
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('admin.thong-ke.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-1.5 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">calendar_today</span>
                    <input type="date" name="tu_ngay" value="{{ $tuNgayStr }}" class="bg-transparent text-xs font-semibold text-slate-800 outline-none">
                    <span class="text-slate-400 text-xs font-bold">đến</span>
                    <input type="date" name="den_ngay" value="{{ $denNgayStr }}" class="bg-transparent text-xs font-semibold text-slate-800 outline-none">
                </div>
                
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Lọc dữ liệu</span>
                </button>
            </form>

            <a href="{{ route('admin.thong-ke.export', ['tu_ngay' => $tuNgayStr, 'den_ngay' => $denNgayStr]) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">download</span>
                <span>Xuất CSV / Excel</span>
            </a>
        </div>
    </div>

    {{-- Metrics Cards Filtered Range --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Card 1: Quán mới trong kỳ --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Quán mới trong kỳ</p>
                    <h3 class="text-2xl font-black text-amber-600 mt-1">+{{ number_format($quanStats['filtered']) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Tổng toàn hệ thống: <strong>{{ number_format($quanStats['total']) }}</strong></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[26px]">add_business</span>
                </div>
            </div>
        </div>

        {{-- Card 2: User mới trong kỳ --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">User mới trong kỳ</p>
                    <h3 class="text-2xl font-black text-indigo-600 mt-1">+{{ number_format($userStats['filtered']) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Tổng người dùng: <strong>{{ number_format($userStats['total']) }}</strong></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[26px]">person_add</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Doanh thu trong kỳ --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Doanh thu trong kỳ</p>
                    <h3 class="text-xl font-black text-emerald-600 mt-1">{{ number_format($revenueStats['filtered']) }} đ</h3>
                    <p class="text-[11px] text-slate-500 mt-1"><strong>{{ $revenueStats['count_filtered'] }}</strong> giao dịch thành công</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[26px]">payments</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Bài viết trong kỳ --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bài viết trong kỳ</p>
                    <h3 class="text-2xl font-black text-purple-600 mt-1">+{{ number_format($blogStats['filtered']) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Tổng bài viết: <strong>{{ number_format($blogStats['total']) }}</strong></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[26px]">newspaper</span>
                </div>
            </div>
        </div>

    </div>

    {{-- System Breakdown Summary Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Quán Breakdown --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-amber-500 text-[20px]">storefront</span>
                Phân loại địa điểm quán
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-100">
                    <span class="text-slate-600 font-medium">Đã duyệt (Hoạt động)</span>
                    <span class="font-extrabold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full">{{ number_format($quanStats['da_duyet']) }}</span>
                </div>
                <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-100">
                    <span class="text-slate-600 font-medium">Chờ kiểm duyệt</span>
                    <span class="font-extrabold text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-full">{{ number_format($quanStats['chua_duyet']) }}</span>
                </div>
                <div class="flex items-center justify-between text-xs py-1.5">
                    <span class="text-slate-600 font-medium">Được đánh dấu Nổi bật</span>
                    <span class="font-extrabold text-purple-600 bg-purple-50 px-2.5 py-0.5 rounded-full">{{ number_format($quanStats['noi_bat']) }}</span>
                </div>
            </div>
        </div>

        {{-- User Breakdown --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-indigo-600 text-[20px]">manage_accounts</span>
                Trạng thái tài khoản người dùng
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-100">
                    <span class="text-slate-600 font-medium">Đang hoạt động</span>
                    <span class="font-extrabold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full">{{ number_format($userStats['hoat_dong']) }}</span>
                </div>
                <div class="flex items-center justify-between text-xs py-1.5">
                    <span class="text-slate-600 font-medium">Tài khoản bị khóa</span>
                    <span class="font-extrabold text-rose-600 bg-rose-50 px-2.5 py-0.5 rounded-full">{{ number_format($userStats['bi_khoa']) }}</span>
                </div>
            </div>
        </div>

        {{-- Revenue Summary --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-emerald-600 text-[20px]">savings</span>
                Doanh thu dịch vụ (thanh_toan)
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-100">
                    <span class="text-slate-600 font-medium">Doanh thu trong kỳ lọc</span>
                    <span class="font-extrabold text-emerald-600 text-sm">{{ number_format($revenueStats['filtered']) }} đ</span>
                </div>
                <div class="flex items-center justify-between text-xs py-1.5">
                    <span class="text-slate-600 font-medium">Doanh thu tích lũy toàn thời gian</span>
                    <span class="font-extrabold text-slate-900 text-sm">{{ number_format($revenueStats['total']) }} đ</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Transactions Table in Range --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">receipt_long</span>
                    Danh sách giao dịch gói dịch vụ trong kỳ lọc
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Dữ liệu chi tiết từ bảng thanh_toan từ {{ \Carbon\Carbon::parse($tuNgayStr)->format('d/m/Y') }} đến {{ \Carbon\Carbon::parse($denNgayStr)->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 uppercase text-[11px] font-bold text-slate-400 border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4">Mã giao dịch</th>
                        <th class="py-3 px-4">Khách hàng</th>
                        <th class="py-3 px-4">Quán liên quan</th>
                        <th class="py-3 px-4">Gói dịch vụ</th>
                        <th class="py-3 px-4">Số tiền</th>
                        <th class="py-3 px-4">Phương thức</th>
                        <th class="py-3 px-4">Trạng thái</th>
                        <th class="py-3 px-4">Thời gian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $p)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $p->ma_giao_dich }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $p->user->ho_ten ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $p->quan->ten_quan ?? 'N/A' }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-700">{{ $p->ten_goi_dich_vu }}</td>
                            <td class="py-3 px-4 font-black text-emerald-600 text-sm">{{ number_format($p->so_tien) }} đ</td>
                            <td class="py-3 px-4 font-bold text-slate-600 uppercase">{{ $p->phuong_thuc ?? 'vnpay' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[10px] {{ $p->trang_thai === 'thanh_cong' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $p->trang_thai === 'thanh_cong' ? 'Thành công' : $p->trang_thai }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-400">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                Không tìm thấy giao dịch nào trong khoảng thời gian này.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->hasPages())
            <div class="mt-4 pt-4 border-t border-slate-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
