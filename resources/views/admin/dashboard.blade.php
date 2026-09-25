@extends('admin.layout')

@section('title', 'Dashboard Quản Trị - Quán Mới')
@section('page-title', 'Tổng Quan Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 md:p-8 text-white shadow-xl border border-slate-800">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-40 -top-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 mb-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    Hệ thống đang hoạt động ổn định
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Xin chào, {{ auth()->user()->ho_ten ?? 'Admin' }}! 👋
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-xl">
                    Chào mừng trở lại bảng điều khiển quản trị Quán Mới. Theo dõi chỉ số tăng trưởng quán, người dùng và doanh thu dịch vụ theo thời gian thực.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.thong-ke.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/20 transition-all hover:scale-[1.02]">
                    <span class="material-symbols-outlined text-[18px]">analytics</span>
                    <span>Thống kê chi tiết</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 5 Overview Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        {{-- Card 1: Tổng số quán --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Tổng số quán</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_quan']) }}</h3>
                    <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]">verified</span>
                        {{ $stats['featured_quan'] }} quán nổi bật
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[26px]">storefront</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Quán chờ duyệt --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Quán chờ duyệt</p>
                    <h3 class="text-2xl font-black {{ $stats['pending_quan'] > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-1">
                        {{ number_format($stats['pending_quan']) }}
                    </h3>
                    <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="text-[11px] font-bold text-amber-600 hover:underline mt-1 inline-flex items-center gap-0.5">
                        Xử lý ngay &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[26px]">pending_actions</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Tổng người dùng --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Tổng người dùng</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_users']) }}</h3>
                    <p class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]">check_circle</span>
                        {{ $stats['active_users'] }} hoạt động
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[26px]">group</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Doanh thu gói dịch vụ (từ bảng thanh_toan) --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Doanh thu gói</p>
                    <h3 class="text-xl font-black text-emerald-600 mt-1 truncate" title="{{ number_format($stats['doanh_thu_goi']) }} VNĐ">
                        {{ number_format($stats['doanh_thu_goi']) }} <span class="text-xs">đ</span>
                    </h3>
                    <p class="text-[11px] font-semibold text-slate-500 mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px] text-emerald-500">payments</span>
                        Bảng thanh_toan
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[26px]">account_balance_wallet</span>
                </div>
            </div>
        </div>

        {{-- Card 5: Số bài viết --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Số bài viết</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_blogs']) }}</h3>
                    <p class="text-[11px] font-semibold text-purple-600 mt-1">Nội dung truyền thông</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[26px]">article</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Interactive Chart.js Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Registration Growth Chart (2 Columns wide) --}}
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500">show_chart</span>
                        Tăng trưởng Quán & User theo tháng
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Biểu đồ so sánh số lượng quán mới đăng ký và người dùng tạo tài khoản mới 6 tháng qua</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span> Quán mới
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                        <span class="w-3 h-3 rounded-full bg-indigo-600"></span> User mới
                    </span>
                </div>
            </div>

            <div class="relative w-full h-[320px]">
                <canvas id="registrationChart"></canvas>
            </div>
        </div>

        {{-- Monthly Revenue Trend Chart (1 Column wide) --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-emerald-600">monetization_on</span>
                    Doanh thu gói theo tháng
                </h3>
                <p class="text-xs text-slate-500 mb-4">Biểu đồ tổng giá trị giao dịch dịch vụ thành công</p>
            </div>

            <div class="relative w-full h-[250px]">
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Tổng doanh thu hiện tại:</span>
                <span class="font-extrabold text-emerald-600 text-sm">{{ number_format($stats['doanh_thu_goi']) }} đ</span>
            </div>
        </div>

    </div>

    {{-- Data Grids & Recent Logs --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Recent Users --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-600 text-[20px]">person_add</span>
                    Người dùng mới nhất
                </h3>
                <a href="{{ route('admin.nguoi-dung.index') }}" class="text-xs font-bold text-amber-600 hover:underline">Xem tất cả</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($stats['recent_users'] as $u)
                    <div class="py-2.5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-indigo-50 font-bold text-indigo-700 flex items-center justify-center text-xs flex-shrink-0">
                                {{ strtoupper(substr($u->ho_ten, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $u->ho_ten }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ $u->email }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0 {{ $u->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $u->ten_vai_tro_hien_thi }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Chưa có người dùng nào</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Venues --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-600 text-[20px]">storefront</span>
                    Quán vừa đăng ký
                </h3>
                <a href="{{ route('admin.quan.index') }}" class="text-xs font-bold text-amber-600 hover:underline">Xem tất cả</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($stats['recent_quan'] as $q)
                    <div class="py-2.5 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $q->ten_quan }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ $q->ten_quan_huyen ?? 'Chưa rõ địa chỉ' }}</p>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0 {{ $q->trang_thai === 'da_duyet' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $q->trang_thai === 'da_duyet' ? 'Đã duyệt' : 'Chờ duyệt' }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Chưa có quán nào</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Payment Transactions --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">receipt_long</span>
                    Giao dịch gói dịch vụ mới
                </h3>
                <a href="{{ route('admin.thong-ke.index') }}" class="text-xs font-bold text-amber-600 hover:underline">Xem thống kê</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($stats['recent_payments'] as $p)
                    <div class="py-2.5 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $p->ten_goi_dich_vu }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ $p->user->ho_ten ?? 'Khách hàng' }} • {{ strtoupper($p->phuong_thuc ?? 'VNPay') }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-black text-emerald-600">+{{ number_format($p->so_tien) }} đ</p>
                            <p class="text-[10px] text-slate-400">{{ $p->created_at->format('d/m/H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Chưa có giao dịch thanh toán nào</p>
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
        
        const gradientQuan = ctxReg.createLinearGradient(0, 0, 0, 300);
        gradientQuan.addColorStop(0, 'rgba(245, 158, 11, 0.35)');
        gradientQuan.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

        const gradientUser = ctxReg.createLinearGradient(0, 0, 0, 300);
        gradientUser.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
        gradientUser.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

        new Chart(ctxReg, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Số quán đăng ký',
                        data: quanData,
                        borderColor: '#f59e0b',
                        backgroundColor: gradientQuan,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#f59e0b',
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Số user mới',
                        data: userData,
                        borderColor: '#4f46e5',
                        backgroundColor: gradientUser,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#4f46e5',
                        pointHoverRadius: 6
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
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { precision: 0, font: { family: 'Plus Jakarta Sans', size: 11 }, color: '#64748b' }
                    }
                }
            }
        });

        // 2. Chart: Monthly Revenue
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        const gradientRev = ctxRev.createLinearGradient(0, 0, 0, 250);
        gradientRev.addColorStop(0, 'rgba(16, 185, 129, 0.8)');
        gradientRev.addColorStop(1, 'rgba(16, 185, 129, 0.2)');

        new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (đ)',
                    data: revenueData,
                    backgroundColor: gradientRev,
                    borderColor: '#10b981',
                    borderWidth: 1.5,
                    borderRadius: 8,
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
                                return 'Doanh thu: ' + context.parsed.y.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 10 }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000).toFixed(1) + 'M';
                            },
                            font: { family: 'Plus Jakarta Sans', size: 10 },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });
</script>
