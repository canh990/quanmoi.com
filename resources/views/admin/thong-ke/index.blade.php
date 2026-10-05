@extends('admin.layout')

@section('title', 'Báo Cáo & Thống Kê Chi Tiết - Quán Mới Admin')
@section('page-title', 'Báo Cáo & Thống Kê Chi Tiết')

@section('content')
<div class="space-y-6">

    {{-- Header & Quick Presets Date Filter Bar --}}
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500">analytics</span>
                Báo Cáo & Thống Kê Chi Tiết
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Phân tích chuyên sâu hoạt động, lượt xem, tăng trưởng địa điểm và doanh thu dịch vụ</p>
        </div>

        {{-- Filter Actions & Date Presets --}}
        <div class="flex flex-wrap items-center gap-3">
            
            {{-- Quick Presets --}}
            <div class="flex items-center bg-slate-100 p-1 rounded-2xl border border-slate-200/80 text-xs font-semibold text-slate-600">
                <a href="{{ route('admin.thong-ke.index', ['preset' => 'today']) }}" 
                   class="px-3 py-1.5 rounded-xl transition-all {{ $preset === 'today' ? 'bg-white text-indigo-700 shadow-xs font-bold' : 'hover:text-slate-900' }}">
                    Hôm nay
                </a>
                <a href="{{ route('admin.thong-ke.index', ['preset' => '7days']) }}" 
                   class="px-3 py-1.5 rounded-xl transition-all {{ $preset === '7days' ? 'bg-white text-indigo-700 shadow-xs font-bold' : 'hover:text-slate-900' }}">
                    7 ngày qua
                </a>
                <a href="{{ route('admin.thong-ke.index', ['preset' => '30days'] + ($preset ? [] : (request('tu_ngay') ? [] : ['preset' => '30days']))) }}" 
                   class="px-3 py-1.5 rounded-xl transition-all {{ ($preset === '30days' || (!$preset && !request('tu_ngay'))) ? 'bg-white text-indigo-700 shadow-xs font-bold' : 'hover:text-slate-900' }}">
                    30 ngày
                </a>
                <a href="{{ route('admin.thong-ke.index', ['preset' => 'this_month']) }}" 
                   class="px-3 py-1.5 rounded-xl transition-all {{ $preset === 'this_month' ? 'bg-white text-indigo-700 shadow-xs font-bold' : 'hover:text-slate-900' }}">
                    Tháng này
                </a>
                <a href="{{ route('admin.thong-ke.index', ['preset' => 'this_year']) }}" 
                   class="px-3 py-1.5 rounded-xl transition-all {{ $preset === 'this_year' ? 'bg-white text-indigo-700 shadow-xs font-bold' : 'hover:text-slate-900' }}">
                    Năm {{ date('Y') }}
                </a>
            </div>

            {{-- Custom Date Form --}}
            <form action="{{ route('admin.thong-ke.index') }}" method="GET" class="flex items-center gap-2">
                <div class="flex items-center gap-1.5 bg-slate-100 px-3 py-1.5 rounded-2xl border border-slate-200">
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">calendar_today</span>
                    <input type="date" name="tu_ngay" value="{{ $tuNgayStr }}" class="bg-transparent text-xs font-semibold text-slate-800 outline-none">
                    <span class="text-slate-400 text-xs font-bold">đến</span>
                    <input type="date" name="den_ngay" value="{{ $denNgayStr }}" class="bg-transparent text-xs font-semibold text-slate-800 outline-none">
                </div>
                
                <button type="submit" class="px-4 py-2 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Lọc</span>
                </button>
            </form>

            {{-- Export Button --}}
            <a href="{{ route('admin.thong-ke.export', array_filter(['tu_ngay' => $tuNgayStr, 'den_ngay' => $denNgayStr, 'preset' => $preset])) }}" 
               class="px-4 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">download</span>
                <span>Xuất CSV / Excel</span>
            </a>
        </div>
    </div>

    {{-- Metrics Cards Filtered Range --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Card 1: Quán mới --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:border-amber-300 transition-all">
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

        {{-- Card 2: User mới --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:border-indigo-300 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Người dùng mới</p>
                    <h3 class="text-2xl font-black text-indigo-600 mt-1">+{{ number_format($userStats['filtered']) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Tổng người dùng: <strong>{{ number_format($userStats['total']) }}</strong></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[26px]">person_add</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Doanh thu trong kỳ --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Doanh thu gói trong kỳ</p>
                    <h3 class="text-xl font-black text-emerald-600 mt-1">{{ number_format($revenueStats['filtered']) }} đ</h3>
                    <p class="text-[11px] text-slate-500 mt-1"><strong>{{ $revenueStats['count_filtered'] }}</strong> giao dịch thành công</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[26px]">payments</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Tổng lượt xem quán --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:border-purple-300 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tổng lượt xem quán</p>
                    <h3 class="text-2xl font-black text-purple-600 mt-1">{{ number_format($quanStats['total_views']) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Lượt tương tác xem chi tiết quán</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[26px]">visibility</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Chart Section (2 Major Charts) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Chart 1: Revenue & Transaction Trend --}}
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-emerald-600">show_chart</span>
                        Xu Hướng Doanh Thu Gói Dịch Vụ
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Biểu đồ tổng thu nhập phát sinh từ giao dịch thành công theo khoảng ngày đã lọc</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold text-slate-600">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Doanh thu (VNĐ)
                    </span>
                </div>
            </div>

            <div class="relative w-full h-[280px]">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        {{-- Chart 2: Registration & Venue Growth --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-0.5">
                    <span class="material-symbols-outlined text-[20px] text-indigo-600">query_stats</span>
                    Tăng Trưởng Hệ Thống
                </h3>
                <p class="text-xs text-slate-500 mb-4">Đối chiếu quán mới & tài khoản đăng ký</p>
            </div>

            <div class="relative w-full h-[220px]">
                <canvas id="growthTrendChart"></canvas>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                <span class="flex items-center gap-1 text-amber-600 font-semibold"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Quán mới</span>
                <span class="flex items-center gap-1 text-indigo-600 font-semibold"><span class="w-2 h-2 rounded-full bg-indigo-600"></span> Người dùng mới</span>
            </div>
        </div>

    </div>

    {{-- System Breakdown Summary Cards & Distribution --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Top Most Viewed Venues --}}
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500 text-[20px]">workspace_premium</span>
                        Top Quán Được Xem Nhiều Nhất
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Danh sách các quán thu hút nhiều lượt truy cập nhất hệ thống</p>
                </div>
                <a href="{{ route('admin.quan.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Quản lý Quán &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase border-b border-slate-100">
                        <tr>
                            <th class="py-2.5 px-3">Thứ hạng / Quán</th>
                            <th class="py-2.5 px-3">Chủ sở hữu</th>
                            <th class="py-2.5 px-3">Khu vực</th>
                            <th class="py-2.5 px-3 text-right">Lượt xem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topVenues as $idx => $tv)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-6 h-6 rounded-full font-black text-[11px] flex items-center justify-center flex-shrink-0 {{ $idx === 0 ? 'bg-amber-100 text-amber-800' : ($idx === 1 ? 'bg-slate-200 text-slate-700' : ($idx === 2 ? 'bg-orange-100 text-orange-800' : 'bg-slate-100 text-slate-500')) }}">
                                            #{{ $idx + 1 }}
                                        </span>
                                        <div>
                                            <p class="font-bold text-slate-900 leading-tight">{{ $tv->ten_quan }}</p>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-[10px] px-1.5 py-0.2 rounded font-semibold {{ $tv->trang_thai === 'da_duyet' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                                    {{ $tv->trang_thai === 'da_duyet' ? 'Đã duyệt' : 'Chờ duyệt' }}
                                                </span>
                                                @if($tv->is_noi_bat)
                                                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-purple-50 text-purple-700 font-bold">Nổi bật</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 font-medium text-slate-700">
                                    {{ $tv->chuQuan->ho_ten ?? 'Chưa gắn chủ' }}
                                </td>
                                <td class="py-3 px-3 text-slate-500">
                                    {{ $tv->ten_tinh_thanh ?? 'Chưa rõ' }}
                                </td>
                                <td class="py-3 px-3 text-right font-black text-slate-900 text-sm">
                                    {{ number_format($tv->luot_xem ?? 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-slate-400">Chưa có dữ liệu lượt xem quán</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Regional Distribution & Package Revenue Breakdown --}}
        <div class="space-y-6">
            
            {{-- Regional Distribution --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-indigo-600 text-[20px]">location_on</span>
                    Phân Phối Quán Theo Khu Vực
                </h3>
                <div class="space-y-3">
                    @php $maxQuan = $regionStats->max('total_quan') ?: 1; @endphp
                    @forelse($regionStats as $reg)
                        @php $percent = round(($reg->total_quan / $maxQuan) * 100); @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-slate-800">{{ $reg->ten_tinh_thanh }}</span>
                                <span class="font-extrabold text-indigo-600">{{ number_format($reg->total_quan) }} quán</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full bg-indigo-600 transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Chưa ghi nhận địa bàn quán</p>
                    @endforelse
                </div>
            </div>

            {{-- Package Revenue Breakdown --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">inventory_2</span>
                    Doanh Thu Theo Gói Dịch Vụ
                </h3>
                <div class="space-y-3">
                    @forelse($packageStats as $pkg)
                        <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-100 last:border-0">
                            <div>
                                <p class="font-bold text-slate-900">{{ $pkg->ten_goi_dich_vu ?? 'Gói mặc định' }}</p>
                                <p class="text-[10px] text-slate-400">{{ number_format($pkg->total_count) }} lượt mua thành công</p>
                            </div>
                            <span class="font-black text-emerald-600 text-sm">{{ number_format($pkg->total_revenue) }} đ</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Chưa phát sinh doanh thu gói</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    {{-- Transactions Table in Range with Filter & Search --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">receipt_long</span>
                    Danh Sách Giao Dịch Trong Kỳ Lọc
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Dữ liệu chi tiết giao dịch gói dịch vụ từ {{ \Carbon\Carbon::parse($tuNgayStr)->format('d/m/Y') }} đến {{ \Carbon\Carbon::parse($denNgayStr)->format('d/m/Y') }}</p>
            </div>

            {{-- Filter & Search --}}
            <form action="{{ route('admin.thong-ke.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="tu_ngay" value="{{ $tuNgayStr }}">
                <input type="hidden" name="den_ngay" value="{{ $denNgayStr }}">
                @if($preset)<input type="hidden" name="preset" value="{{ $preset }}">@endif

                <select name="trang_thai_tt" onchange="this.form.submit()" class="px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none">
                    <option value="">Tất cả trạng thái</option>
                    <option value="thanh_cong" {{ $statusFilter === 'thanh_cong' ? 'selected' : '' }}>Thành công</option>
                    <option value="cho_xu_ly" {{ $statusFilter === 'cho_xu_ly' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="that_bai" {{ $statusFilter === 'that_bai' ? 'selected' : '' }}>Thất bại</option>
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ $searchKey }}" placeholder="Tìm mã GD, user, quán..." class="pl-8 pr-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 outline-none w-48 sm:w-60 focus:ring-2 focus:ring-indigo-500">
                    <span class="material-symbols-outlined text-slate-400 text-[18px] absolute left-2.5 top-2">search</span>
                </div>
            </form>
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
                            <td class="py-3 px-4 font-bold text-slate-600 uppercase">{{ $p->phuong_thuc ?? 'VNPAY' }}</td>
                            <td class="py-3 px-4">
                                @if($p->trang_thai === 'thanh_cong')
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[10px] bg-emerald-100 text-emerald-700">Thành công</span>
                                @elseif($p->trang_thai === 'cho_xu_ly')
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[10px] bg-amber-100 text-amber-700">Chờ xử lý</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[10px] bg-rose-100 text-rose-700">Thất bại</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-400">{{ $p->created_at ? $p->created_at->format('d/m/Y H:i') : '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                Không tìm thấy giao dịch nào phù hợp với bộ lọc trong khoảng thời gian này.
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const labels = @json($chartData['labels']);
        const revenueData = @json($chartData['revenue']);
        const quanData = @json($chartData['quan']);
        const userData = @json($chartData['users']);

        // 1. Chart: Revenue Trend
        const ctxRev = document.getElementById('revenueTrendChart')?.getContext('2d');
        if (ctxRev) {
            const gradientRev = ctxRev.createLinearGradient(0, 0, 0, 260);
            gradientRev.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
            gradientRev.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            new Chart(ctxRev, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: revenueData,
                        borderColor: '#10b981',
                        backgroundColor: gradientRev,
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#10b981',
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Doanh thu: ' + context.parsed.y.toLocaleString('vi-VN') + ' đ';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                                    return value;
                                },
                                font: { family: 'Inter', size: 11 },
                                color: '#64748b'
                            }
                        }
                    }
                }
            });
        }

        // 2. Chart: Growth Trend (Quán & Users)
        const ctxGrowth = document.getElementById('growthTrendChart')?.getContext('2d');
        if (ctxGrowth) {
            new Chart(ctxGrowth, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Quán mới',
                            data: quanData,
                            backgroundColor: '#f59e0b',
                            borderRadius: 4
                        },
                        {
                            label: 'Người dùng mới',
                            data: userData,
                            backgroundColor: '#4f46e5',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 10 }, color: '#64748b' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { precision: 0, font: { family: 'Inter', size: 10 }, color: '#64748b' }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

