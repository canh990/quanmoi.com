@extends('admin.layout')

@section('title', 'Bảng Điều Khiển Quản Trị - Quán Mới Doanh Nghiệp')
@section('page-title', 'Bảng Điều Khiển Trung Tâm')

@section('content')
<div class="space-y-6">

    {{-- Header Actions Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1 border-b border-slate-200/80">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Tổng Quan Hệ Thống
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Cập nhật chỉ số hoạt động quán, người dùng và doanh thu dịch vụ theo thời gian thực.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            {{-- Date Range Selector --}}
            <div class="relative inline-block text-left">
                <select class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors shadow-xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="30">30 ngày gần đây</option>
                    <option value="7">7 ngày gần đây</option>
                    <option value="90">Quý này</option>
                    <option value="365">Năm 2026</option>
                </select>
            </div>

            <a href="{{ route('admin.thong-ke.export') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
                <span class="material-symbols-outlined text-[16px] text-slate-500">download</span>
                <span>Xuất báo cáo</span>
            </a>

            <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition-colors shadow-xs">
                <span class="material-symbols-outlined text-[16px]">add_task</span>
                <span>Duyệt yêu cầu</span>
            </a>
        </div>
    </div>

    {{-- KPI Metric Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- KPI 1: Total Venues --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng số quán</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <h3 class="text-2xl font-bold text-slate-900 tracking-tight">{{ number_format($stats['total_quan']) }}</h3>
                <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-600">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +12.4%
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Quán nổi bật:</span>
                <span class="font-semibold text-slate-800">{{ number_format($stats['featured_quan']) }} địa điểm</span>
            </div>
        </div>

        {{-- KPI 2: Pending Approval --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Chờ duyệt xác thực</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">pending_actions</span>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <h3 class="text-2xl font-bold {{ $stats['pending_quan'] > 0 ? 'text-amber-600' : 'text-slate-900' }} tracking-tight">
                    {{ number_format($stats['pending_quan']) }}
                </h3>
                @if($stats['pending_quan'] > 0)
                    <span class="inline-flex items-center text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                        Cần duyệt
                    </span>
                @else
                    <span class="text-xs font-medium text-slate-400">Đã sạch hàng đợi</span>
                @endif
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Thao tác nhanh:</span>
                <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="font-semibold text-indigo-600 hover:underline">Xử lý ngay &rarr;</a>
            </div>
        </div>

        {{-- KPI 3: Total Users --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Người dùng hệ thống</span>
                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">group</span>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <h3 class="text-2xl font-bold text-slate-900 tracking-tight">{{ number_format($stats['total_users']) }}</h3>
                <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-600">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +8.2%
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Tài khoản hoạt động:</span>
                <span class="font-semibold text-slate-800">{{ number_format($stats['active_users']) }} người</span>
            </div>
        </div>

        {{-- KPI 4: Package Revenue --}}
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-xs hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Doanh thu dịch vụ</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <h3 class="text-xl font-bold text-slate-900 tracking-tight">
                    {{ number_format($stats['doanh_thu_goi']) }} <span class="text-xs font-medium text-slate-500">VNĐ</span>
                </h3>
                <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-600">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +15.8%
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Dữ liệu thực tế:</span>
                <span class="font-semibold text-emerald-600">Thanh toán thành công</span>
            </div>
        </div>

    </div>

    {{-- Interactive Analytics Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Chart 1: Registration Growth (2 Columns wide) --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200/90 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-indigo-600">show_chart</span>
                        Tăng Trưởng Quán & Người Dùng
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Biểu đồ đối chiếu số lượng quán mới và tài khoản mới tạo 6 tháng qua</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-medium text-slate-600">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Quán mới
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span> Người dùng mới
                    </span>
                </div>
            </div>

            <div class="relative w-full h-[280px]">
                <canvas id="registrationChart"></canvas>
            </div>
        </div>

        {{-- Chart 2: Revenue Trend (1 Column wide) --}}
        <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-0.5">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">bar_chart</span>
                    Doanh Thu Gói Theo Tháng
                </h3>
                <p class="text-xs text-slate-500 mb-3">Tổng giá trị giao dịch thành công</p>
            </div>

            <div class="relative w-full h-[220px]">
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                <span>Tổng thu hiện tại:</span>
                <span class="font-bold text-emerald-600 text-xs">{{ number_format($stats['doanh_thu_goi']) }} VNĐ</span>
            </div>
        </div>

    </div>

    {{-- Modern Data Tables & Recent Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Recent Users Table --}}
        <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-indigo-600">person_add</span>
                    Người dùng vừa đăng ký
                </h3>
                <a href="{{ route('admin.nguoi-dung.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Quản lý &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($stats['recent_users'] as $u)
                    <div class="py-2.5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-md bg-slate-100 font-bold text-slate-700 flex items-center justify-center text-xs flex-shrink-0">
                                {{ strtoupper(substr($u->ho_ten, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-800 truncate leading-tight">{{ $u->ho_ten }}</p>
                                <p class="text-[10px] text-slate-400 truncate leading-tight">{{ $u->email }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded flex-shrink-0 {{ $u->isAdmin() ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/60' : 'bg-slate-100 text-slate-600' }}">
                            {{ $u->ten_vai_tro_hien_thi }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Chưa có dữ liệu</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Venues Table --}}
        <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-amber-600">storefront</span>
                    Quán vừa tạo
                </h3>
                <a href="{{ route('admin.quan.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Xem tất cả &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($stats['recent_quan'] as $q)
                    <div class="py-2.5 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate leading-tight">{{ $q->ten_quan }}</p>
                            <p class="text-[10px] text-slate-400 truncate leading-tight">{{ $q->ten_quan_huyen ?? 'Khu vực chưa rõ' }}</p>
                        </div>
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded flex-shrink-0 {{ $q->trang_thai === 'da_duyet' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-amber-50 text-amber-700 border border-amber-200/60' }}">
                            {{ $q->trang_thai === 'da_duyet' ? 'Đã duyệt' : 'Chờ duyệt' }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Chưa có dữ liệu</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Audit Logs Table --}}
        <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">reorder</span>
                    Nhật ký hệ thống gần nhất
                </h3>
                <a href="{{ route('admin.audit-log.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Xem nhật ký &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($stats['recent_logs'] as $log)
                    <div class="py-2.5 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate leading-tight">{{ $log->hanh_dong ?? 'Thao tác Admin' }}</p>
                            <p class="text-[10px] text-slate-400 truncate leading-tight">IP: {{ $log->ip_address ?? '127.0.0.1' }} • {{ $log->created_at ? $log->created_at->format('H:i d/m') : '' }}</p>
                        </div>
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-600 flex-shrink-0">
                            {{ $log->admin->ho_ten ?? 'Quản trị viên' }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Chưa ghi nhận log</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const labels = @json($chartData['labels']);
        const quanData = @json($chartData['quan']);
        const userData = @json($chartData['users']);
        const revenueData = @json($chartData['revenue']);

        // 1. Chart: Registration Growth
        const ctxReg = document.getElementById('registrationChart').getContext('2d');
        
        const gradientQuan = ctxReg.createLinearGradient(0, 0, 0, 260);
        gradientQuan.addColorStop(0, 'rgba(245, 158, 11, 0.2)');
        gradientQuan.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

        const gradientUser = ctxReg.createLinearGradient(0, 0, 0, 260);
        gradientUser.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
        gradientUser.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

        new Chart(ctxReg, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Số quán mới',
                        data: quanData,
                        borderColor: '#f59e0b',
                        backgroundColor: gradientQuan,
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#f59e0b',
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Số user mới',
                        data: userData,
                        borderColor: '#4f46e5',
                        backgroundColor: gradientUser,
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#4f46e5',
                        pointHoverRadius: 5
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
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { precision: 0, font: { family: 'Inter', size: 11 }, color: '#64748b' }
                    }
                }
            }
        });

        // 2. Chart: Monthly Revenue
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        const gradientRev = ctxRev.createLinearGradient(0, 0, 0, 220);
        gradientRev.addColorStop(0, 'rgba(16, 185, 129, 0.7)');
        gradientRev.addColorStop(1, 'rgba(16, 185, 129, 0.15)');

        new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData,
                    backgroundColor: gradientRev,
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false
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
                                return 'Doanh thu: ' + context.parsed.y.toLocaleString('vi-VN') + ' VNĐ';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 10 }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000).toFixed(1) + 'M';
                            },
                            font: { family: 'Inter', size: 10 },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });
</script>
