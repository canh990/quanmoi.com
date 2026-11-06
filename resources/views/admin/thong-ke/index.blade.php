@extends('admin.layout')

@section('title', 'Báo Cáo & Thống Kê Chi Tiết - Quán Mới Admin')
@section('page-title', 'Báo Cáo & Thống Kê Chi Tiết')

@section('content')
<style>
    /* ==========================================================================
       ENTERPRISE SCOPED STYLES FOR /admin/thong-ke
       Đảm bảo 100% tương phản cao, bố cục chuẩn xác, không phụ thuộc JIT build
       ========================================================================== */
    .tk-page-wrap {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .tk-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.875rem !important;
        padding: 1.25rem !important;
        box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.04) !important;
    }
    .tk-kpi-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1rem;
    }
    @media (min-width: 640px) {
        .tk-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        .tk-kpi-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .tk-kpi-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 1.125rem 1.25rem !important;
        box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.04) !important;
        transition: all 0.15s ease-in-out !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .tk-kpi-card:hover {
        border-color: #94a3b8 !important;
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.08) !important;
    }

    /* Preset Filter Pills */
    .tk-preset-group {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.25rem;
        background-color: #f1f5f9;
        padding: 0.25rem;
        border-radius: 0.625rem;
        border: 1px solid #e2e8f0;
    }
    .tk-preset-pill {
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .tk-preset-pill:hover {
        color: #0f172a;
        background-color: rgba(255, 255, 255, 0.6);
    }
    .tk-preset-pill.active {
        background-color: #ffffff !important;
        color: #4338ca !important;
        font-weight: 700 !important;
        box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08) !important;
    }

    /* AI CFO Panel */
    .tk-ai-panel {
        background-color: #ffffff !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 1rem !important;
        padding: 1.25rem 1.5rem !important;
        box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.06) !important;
    }
    .tk-ai-control-bar {
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 1rem !important;
        overflow: hidden !important;
    }
    .tk-ai-control-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
        align-items: end !important;
    }
    @media (min-width: 1024px) {
        .tk-ai-control-grid {
            grid-template-columns: 270px minmax(0, 1fr) auto !important;
        }
    }
    .tk-ai-summary-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    @media (min-width: 1024px) {
        .tk-ai-summary-grid {
            grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) !important;
        }
    }
    .tk-ai-two-col-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    @media (min-width: 768px) {
        .tk-ai-two-col-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }
    .tk-main-two-one-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1.25rem !important;
    }
    @media (min-width: 1024px) {
        .tk-main-two-one-grid {
            grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) !important;
        }
    }
    .tk-ai-cfo-box {
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-left: 4px solid #4f46e5 !important;
        border-radius: 0.75rem !important;
        padding: 1.125rem 1.25rem !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1rem;
        min-width: 0;
    }
    .tk-ai-mini-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e2e8f0;
    }
    @media (min-width: 640px) {
        .tk-ai-mini-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    .tk-ai-mini-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.625rem !important;
        padding: 0.75rem 0.875rem !important;
        box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.03) !important;
        min-width: 0;
    }
    .tk-ai-forecast-box {
        background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%) !important;
        border: 1px solid #a7f3d0 !important;
        border-radius: 0.75rem !important;
        padding: 1.125rem 1.25rem !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
        overflow: hidden;
    }
    .tk-ai-forecast-value-box {
        background-color: #ffffff !important;
        border: 1px solid #a7f3d0 !important;
        border-radius: 0.625rem !important;
        padding: 0.75rem 0.875rem !important;
        margin: 0.625rem 0 !important;
        min-width: 0;
    }
    .tk-ai-highlight-box {
        background-color: #f0fdf4 !important;
        border: 1px solid #bbf7d0 !important;
        border-radius: 0.75rem !important;
        padding: 1rem 1.125rem !important;
    }
    .tk-ai-warning-box {
        background-color: #fffbeb !important;
        border: 1px solid #fde68a !important;
        border-radius: 0.75rem !important;
        padding: 1rem 1.125rem !important;
    }
    .tk-ai-strategy-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1rem;
    }
    @media (min-width: 1024px) {
        .tk-ai-strategy-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    .tk-ai-strategy-card {
        background-color: #ffffff !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 0.75rem !important;
        padding: 1.125rem !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        transition: all 0.15s ease !important;
        box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04) !important;
    }
    .tk-ai-strategy-card.priority-high {
        border-color: #fcd34d !important;
        border-top: 3px solid #f59e0b !important;
    }
    .tk-ai-strategy-card.priority-normal {
        border-color: #c7d2fe !important;
        border-top: 3px solid #4f46e5 !important;
    }
    .tk-ai-strategy-card:hover {
        box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.1) !important;
        transform: translateY(-1px);
    }
    .tk-ai-solution-callout {
        background-color: #fffbeb !important;
        border: 1px solid #fde68a !important;
        border-radius: 0.5rem !important;
        padding: 0.625rem 0.75rem !important;
        color: #1e293b !important;
        font-size: 0.75rem !important;
        line-height: 1.45 !important;
    }
    .tk-chip-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 600;
        background-color: #ffffff;
        color: #334155;
        border: 1px solid #cbd5e1;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .tk-chip-btn:hover {
        background-color: #fffbeb;
        color: #b45309;
        border-color: #f59e0b;
    }
    .tk-progress-track {
        width: 100%;
        height: 0.5rem;
        background-color: #f1f5f9;
        border-radius: 9999px;
        overflow: hidden;
    }
    .tk-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #4f46e5, #6366f1);
        border-radius: 9999px;
        transition: width 0.4s ease;
    }
</style>

<div class="tk-page-wrap">

    {{-- 1. HEADER & FILTER BAR --}}
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-[24px] text-amber-500">analytics</span>
                <span>Báo Cáo & Thống Kê Chi Tiết</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Phân tích chuyên sâu hoạt động, lượt xem, tăng trưởng địa điểm và cố vấn doanh thu dịch vụ AI theo thời gian thực.
            </p>
        </div>

        {{-- Filter Actions & Date Presets --}}
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Quick Presets --}}
            <div class="tk-preset-group">
                <a href="{{ route('admin.thong-ke.index', ['preset' => 'today']) }}"
                   class="tk-preset-pill {{ $preset === 'today' ? 'active' : '' }}">
                    Hôm nay
                </a>
                <a href="{{ route('admin.thong-ke.index', ['preset' => '7days']) }}"
                   class="tk-preset-pill {{ $preset === '7days' ? 'active' : '' }}">
                    7 ngày qua
                </a>
                <a href="{{ route('admin.thong-ke.index', ['preset' => '30days']) }}"
                   class="tk-preset-pill {{ ($preset === '30days' || (!$preset && !request('tu_ngay'))) ? 'active' : '' }}">
                    30 ngày
                </a>
                <a href="{{ route('admin.thong-ke.index', ['preset' => 'this_month']) }}"
                   class="tk-preset-pill {{ $preset === 'this_month' ? 'active' : '' }}">
                    Tháng này
                </a>
                <a href="{{ route('admin.thong-ke.index', ['preset' => 'this_year']) }}"
                   class="tk-preset-pill {{ $preset === 'this_year' ? 'active' : '' }}">
                    Năm {{ date('Y') }}
                </a>
            </div>

            {{-- Custom Date Form --}}
            <form action="{{ route('admin.thong-ke.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-slate-300 shadow-2xs">
                    <span class="material-symbols-outlined text-slate-400 text-[16px]">calendar_today</span>
                    <input type="date" name="tu_ngay" value="{{ $tuNgayStr }}" class="bg-transparent text-xs font-semibold text-slate-800 outline-none">
                    <span class="text-slate-400 text-xs font-bold">→</span>
                    <input type="date" name="den_ngay" value="{{ $denNgayStr }}" class="bg-transparent text-xs font-semibold text-slate-800 outline-none">
                </div>

                <button type="submit" class="btn-enterprise btn-enterprise-dark">
                    <span class="material-symbols-outlined">filter_alt</span>
                    <span>Lọc dữ liệu</span>
                </button>
            </form>

            {{-- Export Button --}}
            <a href="{{ route('admin.thong-ke.export', array_filter(['tu_ngay' => $tuNgayStr, 'den_ngay' => $denNgayStr, 'preset' => $preset])) }}"
               class="btn-enterprise btn-enterprise-success" title="Tải xuống báo cáo CSV / Excel">
                <span class="material-symbols-outlined">download</span>
                <span>Xuất CSV / Excel</span>
            </a>
        </div>
    </div>

    {{-- 2. 4 KPI SUMMARY CARDS (Đồng bộ chuẩn Enterprise với Dashboard & Thanh Toán) --}}
    <div class="tk-kpi-grid">
        {{-- Card 1: Quán mới --}}
        <div class="tk-kpi-card">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Quán Mới Trong Kỳ</span>
                    <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200">
                        <span class="material-symbols-outlined text-[20px]">add_business</span>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">+{{ number_format($quanStats['filtered']) }}</h3>
                    <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                        {{ number_format($quanStats['da_duyet']) }} đã duyệt
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Tổng toàn hệ thống: <strong class="text-slate-800">{{ number_format($quanStats['total']) }}</strong></span>
                <a href="{{ route('admin.quan.index') }}" class="font-semibold text-amber-600 hover:underline">Quản lý →</a>
            </div>
        </div>

        {{-- Card 2: Người dùng mới --}}
        <div class="tk-kpi-card">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Người Dùng Mới</span>
                    <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-200">
                        <span class="material-symbols-outlined text-[20px]">person_add</span>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">+{{ number_format($userStats['filtered']) }}</h3>
                    <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                        {{ number_format($userStats['hoat_dong']) }} hoạt động
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Tổng tài khoản: <strong class="text-slate-800">{{ number_format($userStats['total']) }}</strong></span>
                <a href="{{ route('admin.nguoi-dung.index') }}" class="font-semibold text-indigo-600 hover:underline">Thành viên →</a>
            </div>
        </div>

        {{-- Card 3: Doanh thu trong kỳ --}}
        <div class="tk-kpi-card">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Doanh Thu Gói Trong Kỳ</span>
                    <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200">
                        <span class="material-symbols-outlined text-[20px]">payments</span>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between gap-2">
                    <h3 class="text-2xl font-extrabold text-emerald-600 tracking-tight">
                        {{ number_format($revenueStats['filtered'], 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">đ</span>
                    </h3>
                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        {{ number_format($revenueStats['count_filtered']) }} đơn
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Tổng lũy kế: <strong class="text-slate-800">{{ number_format($revenueStats['total'], 0, ',', '.') }}đ</strong></span>
                <a href="{{ route('admin.thanh-toan.index') }}" class="font-semibold text-emerald-600 hover:underline">Đối soát ví →</a>
            </div>
        </div>

        {{-- Card 4: Tổng lượt xem quán --}}
        <div class="tk-kpi-card">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng Lượt Xem Quán</span>
                    <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200">
                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ number_format($quanStats['total_views']) }}</h3>
                    <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                        {{ number_format($quanStats['noi_bat']) }} nổi bật
                    </span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Quán tích xanh: <strong class="text-slate-800">{{ number_format($quanStats['xac_thuc']) }}</strong></span>
                <a href="{{ route('admin.quan-noi-bat.index') }}" class="font-semibold text-blue-600 hover:underline">Quán nổi bật →</a>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- 3. AI REVENUE INTELLIGENCE & PLATFORM GROWTH ENGINE (SPEC 2: ADMIN AI CFO) --}}
    {{-- ========================================================================= --}}
    @php
        $aiData = $aiRevenueInsights ?? [];
        $aiMetrics = $aiData['metrics'] ?? [];
        $healthScore = $aiMetrics['diem_suc_khoe'] ?? 82;
        $healthLabel = $aiMetrics['nhan_suc_khoe'] ?? 'Tài chính khả quan';
        $duBaoMin = $aiMetrics['du_bao_30_ngay']['min'] ?? 0;
        $duBaoMax = $aiMetrics['du_bao_30_ngay']['max'] ?? 0;
        $tongSoDuVi = $aiMetrics['tong_so_du_vi'] ?? 0;
        $quanChuaNoiBat = $aiMetrics['so_quan_chua_mua_goi_noi_bat'] ?? 0;
        $tangTruongPt = $aiMetrics['tang_truong_pt'] ?? 0;
    @endphp
    <div id="admin-ai-revenue-section" class="tk-ai-panel space-y-4">
        {{-- Top Header Bar --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3.5 border-b border-slate-200">
            <div class="flex items-start sm:items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">auto_awesome</span>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900">
                            AI Phân Tích Doanh Thu & Chiến Lược Tăng Trưởng Toàn Sàn
                        </h2>
                        <span id="ai-provider-badge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span id="ai-provider-text">{{ $aiData['provider_label'] ?? 'Google Gemini AI' }}</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Cố vấn Tài chính ảo (Virtual CFO) tự động phân tích dòng tiền SePay, nghĩa vụ số dư ví lưu hành, cảnh báo rời bỏ và gợi ý chiến dịch 1-chạm.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <div class="px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">verified_user</span>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-500 font-bold leading-none">Điểm sức khỏe tài chính</p>
                        <p class="text-xs font-extrabold text-emerald-800 mt-0.5" id="ai-health-label">{{ $healthScore }}/100 • {{ $healthLabel }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Thanh điều khiển Tương tác AI CFO --}}
        <div class="tk-ai-control-bar space-y-2.5">
            <div class="tk-ai-control-grid">
                <div class="min-w-0">
                    <label for="ai-focus-select" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                        1. Chọn trọng tâm AI phân tích
                    </label>
                    <select id="ai-focus-select" onchange="refreshAdminAiRevenueInsights()" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-bold text-slate-800 outline-none focus:border-amber-500 cursor-pointer" style="height: 38px;">
                        <option value="tong_hop">📊 Tổng quan dòng tiền & Dự báo 30 ngày</option>
                        <option value="kich_cau_nap_vi">💰 Chiến lược Kích cầu nạp ví SePay</option>
                        <option value="goi_noi_bat">🔥 Chuyển đổi Quán mua Gói Nổi Bật & Tích Xanh</option>
                        <option value="chong_roi_bo">🛡️ Cảnh báo rời bỏ (Churn Risk) & Giữ chân VIP</option>
                    </select>
                </div>

                <div class="min-w-0">
                    <label for="ai-custom-prompt" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                        2. Đặt câu hỏi hoặc mục tiêu doanh thu cho AI CFO (Tùy chọn)
                    </label>
                    <input type="text" id="ai-custom-prompt"
                           onkeydown="if(event.key==='Enter'){event.preventDefault();refreshAdminAiRevenueInsights();}"
                           placeholder="VD: Làm sao tăng doanh thu nạp ví thêm 30 triệu trong tháng này?"
                           class="w-full px-3 py-2 rounded-lg bg-white border border-slate-300 text-xs font-medium text-slate-900 outline-none focus:border-amber-500" style="height: 38px;">
                </div>

                <button type="button"
                        id="btn-refresh-ai-revenue"
                        onclick="refreshAdminAiRevenueInsights()"
                        class="btn-enterprise btn-enterprise-primary px-4 shrink-0" style="height: 38px;">
                    <span id="icon-refresh-ai" class="material-symbols-outlined">auto_awesome</span>
                    <span id="text-refresh-ai">Kira AI Phân Tích Ngay</span>
                </button>
            </div>

            {{-- Gợi ý câu hỏi nhanh 1-chạm --}}
            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                <span class="text-[11px] font-bold text-slate-500 mr-1">Gợi ý câu hỏi nhanh:</span>
                <button type="button" onclick="applyQuickAiQuestion('kich_cau_nap_vi', 'Làm sao tăng doanh thu nạp ví SePay thêm 30 triệu trong tháng này?')" class="tk-chip-btn">
                    <span>💰 Tăng +30tr nạp ví tháng này</span>
                </button>
                <button type="button" onclick="applyQuickAiQuestion('goi_noi_bat', 'Chiến lược thuyết phục các quán đang đông khách mua Gói Quán Nổi Bật?')" class="tk-chip-btn">
                    <span>🔥 Kích cầu mua Gói Quán Nổi Bật</span>
                </button>
                <button type="button" onclick="applyQuickAiQuestion('chong_roi_bo', 'Làm sao kích hoạt số dư ví đang nhàn rỗi và giảm tỷ lệ lỗi thanh toán QR?')" class="tk-chip-btn">
                    <span>⚡ Giải phóng số dư ví nhàn rỗi</span>
                </button>
            </div>
        </div>

        {{-- Thông báo trạng thái sau khi AI phân tích xong --}}
        <div id="ai-analysis-toast" class="hidden rounded-xl p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">task_alt</span>
                <span id="ai-analysis-toast-text">Kira AI đã phân tích xong dữ liệu doanh thu và cập nhật khuyến nghị chiến lược mới!</span>
            </div>
            <span class="text-[11px] font-semibold text-emerald-700" id="ai-analysis-toast-time">Vừa xong</span>
        </div>

        {{-- Trạng thái AI Đang Suy Nghĩ & Quét Số Liệu (Loading State) --}}
        <div id="ai-revenue-loading-box" class="hidden rounded-xl p-6 bg-amber-50 border border-amber-200 text-center space-y-2">
            <div class="w-10 h-10 rounded-xl bg-white border border-amber-300 text-amber-600 flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-[24px] animate-spin">sync</span>
            </div>
            <h4 class="font-bold text-slate-900 text-sm" id="ai-loading-step-title">
                Kira AI CFO đang quét dữ liệu giao dịch SePay & số dư ví toàn sàn...
            </h4>
            <p class="text-xs text-slate-600 max-w-lg mx-auto" id="ai-loading-step-desc">
                Đang đối chiếu tốc độ tăng trưởng kỳ này, tỷ lệ chuyển đổi Gói Quán Nổi Bật và tính toán 3 chiến dịch hành động 1-Click tối ưu nhất.
            </p>
        </div>

        {{-- Khối nội dung kết quả phân tích AI --}}
        <div id="ai-revenue-result-container" class="space-y-4">
            {{-- Row 1: CFO Executive Summary + 30-Day Revenue Forecast --}}
            <div class="tk-ai-summary-grid">
                {{-- Left 2 cols: CFO Summary + Financial Sub-metrics --}}
                <div class="tk-ai-cfo-box">
                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-indigo-600">psychology</span>
                                Nhận Định Giám Đốc Tài Chính AI (CFO Executive Summary)
                            </span>
                            <span class="text-[11px] font-semibold text-slate-500" id="ai-analyzed-time">Cập nhật: {{ $aiData['analyzed_at'] ?? now()->format('H:i d/m/Y') }}</span>
                        </div>
                        <p id="ai-cfo-summary" class="text-xs sm:text-sm text-slate-800 leading-relaxed font-medium">
                            {{ $aiData['nhan_dinh_cfo'] ?? 'Đang tổng hợp dữ liệu dòng tiền toàn sàn...' }}
                        </p>
                    </div>

                    {{-- 3 Quick Financial Indicators --}}
                    <div class="tk-ai-mini-grid">
                        <div class="tk-ai-mini-card">
                            <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Tăng trưởng kỳ này</p>
                            <p class="text-base font-extrabold mt-1 {{ $tangTruongPt >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $tangTruongPt >= 0 ? '+' : '' }}{{ $tangTruongPt }}% <span class="text-[11px] font-medium text-slate-500">vs kỳ trước</span>
                            </p>
                        </div>
                        <div class="tk-ai-mini-card">
                            <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Số dư ví lưu hành (Nợ ví)</p>
                            <p class="text-base font-extrabold text-indigo-600 mt-1">
                                {{ number_format($tongSoDuVi, 0, ',', '.') }} đ
                            </p>
                        </div>
                        <div class="tk-ai-mini-card">
                            <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Mỏ vàng chưa khai thác</p>
                            <p class="text-base font-extrabold text-amber-600 mt-1">
                                {{ number_format($quanChuaNoiBat) }} quán <span class="text-[11px] font-medium text-slate-500">chưa mua gói</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Right 1 col: 30-Day Revenue Forecast --}}
                <div class="tk-ai-forecast-box">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-900 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-emerald-600 shrink-0">trending_up</span>
                                <span>Dự Báo Doanh Thu 30 Ngày Tới</span>
                            </span>
                            <span class="px-2 py-0.5 rounded bg-white text-emerald-800 border border-emerald-200 text-[10px] font-bold whitespace-nowrap shrink-0">AI Forecast</span>
                        </div>
                        <div class="tk-ai-forecast-value-box">
                            <p class="text-[11px] font-semibold text-slate-500">Ước tính dòng tiền nạp & mua gói dịch vụ:</p>
                            <h3 id="ai-forecast-range" class="text-base sm:text-lg font-extrabold text-emerald-600 mt-0.5 tracking-tight break-words">
                                {{ number_format($duBaoMin, 0, ',', '.') }}đ – {{ number_format($duBaoMax, 0, ',', '.') }}đ
                            </h3>
                        </div>
                        <p id="ai-forecast-note" class="text-xs text-slate-700 leading-relaxed font-medium">
                            {{ $aiData['nhan_xet_du_bao'] ?? '' }}
                        </p>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-emerald-200 flex items-center justify-between text-xs">
                        <span class="text-slate-600 font-medium">Đối soát dòng tiền SePay</span>
                        <a href="{{ route('admin.thanh-toan.index') }}" class="font-bold text-emerald-700 hover:underline whitespace-nowrap">
                            Quản lý ví & Nạp tiền &rarr;
                        </a>
                    </div>
                </div>
            </div>

            {{-- Row 2: Highlights vs Risk Warnings --}}
            <div class="tk-ai-two-col-grid">
                {{-- Điểm sáng tăng trưởng --}}
                <div class="tk-ai-highlight-box">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-900 flex items-center gap-1.5 mb-2">
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                        Điểm Sáng Tài Chính & Tăng Trưởng
                    </h4>
                    <ul id="ai-highlights-list" class="space-y-1.5 text-xs text-slate-800 font-medium">
                        @foreach(($aiData['diem_sang'] ?? []) as $hl)
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">•</span>
                                <span>{{ $hl }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Cảnh báo rủi ro & Điểm nghẽn --}}
                <div class="tk-ai-warning-box">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900 flex items-center gap-1.5 mb-2">
                        <span class="material-symbols-outlined text-[18px] text-amber-600">warning</span>
                        Cảnh Báo Rủi Ro & Điểm Nghẽn Dòng Tiền
                    </h4>
                    <ul id="ai-warnings-list" class="space-y-1.5 text-xs text-slate-800 font-medium">
                        @foreach(($aiData['canh_bao_rui_ro'] ?? []) as $warn)
                            <li class="flex items-start gap-2">
                                <span class="text-amber-600 font-bold">•</span>
                                <span>{{ $warn }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Row 3: 3 Actionable Strategic Recommendations (1-Click Execution) --}}
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-3">
                    <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-amber-500">rocket_launch</span>
                        Gợi Ý Chiến Lược Tăng Doanh Thu Sàn (Thực Thi 1-Click)
                    </h3>
                    <span class="text-xs text-slate-500 font-medium">Bấm nút hành động trên từng thẻ để triển khai chiến dịch ngay</span>
                </div>

                <div id="ai-strategies-grid" class="tk-ai-strategy-grid">
                    @foreach(($aiData['khuyen_nghi_chien_luoc'] ?? []) as $idx => $st)
                        @php
                            $isHigh = ($st['uu_tien'] ?? 'cao') === 'cao';
                            $actionType = $st['hanh_dong_loai'] ?? 'broadcast';
                            $notifSample = $st['thong_bao_mau'] ?? null;
                        @endphp
                        <div class="tk-ai-strategy-card {{ $isHigh ? 'priority-high' : 'priority-normal' }}">
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $isHigh ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-indigo-100 text-indigo-800 border border-indigo-200' }}">
                                        {{ $isHigh ? '🔥 Ưu tiên cao' : '💡 Chiến lược' }}
                                    </span>
                                    @if(!empty($st['tac_dong_du_kien']))
                                        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">{{ $st['tac_dong_du_kien'] }}</span>
                                    @endif
                                </div>

                                <h4 class="font-bold text-sm text-slate-900 leading-snug">
                                    {{ $st['tieu_de'] ?? 'Chiến lược tăng trưởng doanh thu' }}
                                </h4>

                                <p class="text-xs text-slate-600 leading-relaxed">
                                    <strong class="text-slate-900">Phân tích:</strong> {{ $st['phan_tich'] ?? '' }}
                                </p>

                                <div class="tk-ai-solution-callout">
                                    <strong class="text-amber-800">Giải pháp AI đề xuất:</strong> {{ $st['giai_phap'] ?? '' }}
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col gap-2">
                                @if($notifSample)
                                    <button type="button"
                                            data-tieu-de="{{ $notifSample['tieu_de'] ?? '' }}"
                                            data-noi-dung="{{ $notifSample['noi_dung'] ?? '' }}"
                                            data-doi-tuong="{{ $notifSample['doi_tuong'] ?? 'chu_quan' }}"
                                            onclick="openAiBroadcastModalFromBtn(this)"
                                            class="btn-enterprise btn-enterprise-success w-full">
                                        <span class="material-symbols-outlined">campaign</span>
                                        <span>{{ $st['nhan_nut'] ?? 'Phát sóng ưu đãi cho Chủ Quán' }}</span>
                                    </button>
                                @endif

                                @if($actionType === 'featured_venues')
                                    <a href="{{ route('admin.quan-noi-bat.index') }}"
                                       class="btn-enterprise btn-enterprise-secondary w-full">
                                        <span class="material-symbols-outlined">local_fire_department</span>
                                        <span>Mở Quản Lý Quán Nổi Bật &rarr;</span>
                                    </a>
                                @elseif($actionType === 'ai_studio')
                                    <a href="{{ route('admin.khuyen-mai-ai.index') }}"
                                       class="btn-enterprise btn-enterprise-indigo w-full">
                                        <span class="material-symbols-outlined">auto_fix_high</span>
                                        <span>{{ $st['nhan_nut'] ?? 'Mở Khuyến Mãi AI Studio' }} &rarr;</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- 4. CHART SECTION (2 Major Charts) --}}
    <div class="tk-main-two-one-grid">
        {{-- Chart 1: Revenue & Transaction Trend --}}
        <div class="tk-card flex flex-col justify-between min-w-0">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-emerald-600">show_chart</span>
                        Xu Hướng Doanh Thu Gói Dịch Vụ
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Biểu đồ tổng thu nhập phát sinh từ giao dịch thành công theo khoảng ngày đã lọc</p>
                </div>
                <div class="flex items-center gap-3 text-xs font-semibold text-slate-600">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        Tổng kỳ: {{ number_format($revenueStats['filtered'], 0, ',', '.') }} đ
                    </span>
                </div>
            </div>

            <div style="position: relative; width: 100%; height: 280px;">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        {{-- Chart 2: Registration & Venue Growth --}}
        <div class="tk-card flex flex-col justify-between min-w-0">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-0.5">
                    <span class="material-symbols-outlined text-[20px] text-indigo-600">query_stats</span>
                    Tăng Trưởng Hệ Thống
                </h3>
                <p class="text-xs text-slate-500 mb-3">Đối chiếu quán mới (+{{ number_format($quanStats['filtered']) }}) & người dùng mới (+{{ number_format($userStats['filtered']) }})</p>
            </div>

            <div style="position: relative; width: 100%; height: 230px;">
                <canvas id="growthTrendChart"></canvas>
            </div>

            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                <span class="flex items-center gap-1.5 text-amber-700 font-semibold">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Quán mới (+{{ number_format($quanStats['filtered']) }})
                </span>
                <span class="flex items-center gap-1.5 text-indigo-700 font-semibold">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span> Người dùng mới (+{{ number_format($userStats['filtered']) }})
                </span>
            </div>
        </div>
    </div>

    {{-- 5. SYSTEM BREAKDOWN: TOP VENUES, REGIONAL DISTRIBUTION & PACKAGE REVENUE --}}
    <div class="tk-main-two-one-grid">
        {{-- Top Most Viewed Venues --}}
        <div class="tk-card min-w-0">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500 text-[20px]">workspace_premium</span>
                        Top Quán Được Xem Nhiều Nhất
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Danh sách các quán thu hút nhiều lượt truy cập nhất hệ thống</p>
                </div>
                <a href="{{ route('admin.quan.index') }}" class="btn-enterprise btn-enterprise-secondary">
                    <span>Quản lý Quán</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Thứ hạng / Quán</th>
                            <th class="py-2.5 px-3">Chủ sở hữu</th>
                            <th class="py-2.5 px-3">Khu vực</th>
                            <th class="py-2.5 px-3 text-right">Lượt xem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topVenues as $idx => $tv)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-6 h-6 rounded-full font-bold text-[11px] flex items-center justify-center shrink-0 {{ $idx === 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : ($idx === 1 ? 'bg-slate-200 text-slate-700' : ($idx === 2 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-500')) }}">
                                            #{{ $idx + 1 }}
                                        </span>
                                        <div>
                                            <p class="font-bold text-slate-900 leading-tight">{{ $tv->ten_quan }}</p>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="text-[10px] px-1.5 py-0.5 rounded font-semibold {{ $tv->trang_thai === 'da_duyet' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                    {{ $tv->trang_thai === 'da_duyet' ? 'Đã duyệt' : 'Chờ duyệt' }}
                                                </span>
                                                @if($tv->is_noi_bat)
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">🔥 Nổi bật</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 font-medium text-slate-700">
                                    {{ $tv->chuQuan->ho_ten ?? 'Khách vãng lai / Đã xóa' }}
                                </td>
                                <td class="py-3 px-3 text-slate-600">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-medium">
                                        <span class="material-symbols-outlined text-[13px] text-slate-400">location_on</span>
                                        {{ $tv->ten_tinh_thanh ?? 'Chưa rõ' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-extrabold text-slate-900 text-sm">
                                    {{ number_format($tv->luot_xem ?? 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-slate-400">Chưa có dữ liệu lượt xem quán ăn nào trong hệ thống.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Regional Distribution & Package Revenue Breakdown --}}
        <div class="space-y-5">
            {{-- Regional Distribution --}}
            <div class="tk-card">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 mb-3 pb-2.5 border-b border-slate-100">
                    <span class="material-symbols-outlined text-indigo-600 text-[20px]">location_on</span>
                    Phân Phối Quán Theo Khu Vực
                </h3>
                <div class="space-y-3">
                    @php
                        $totalRegionalQuan = $regionStats->sum('total_quan') ?: 1;
                        $maxQuan = $regionStats->max('total_quan') ?: 1;
                    @endphp
                    @forelse($regionStats as $reg)
                        @php
                            $barPercent = max(6, round(($reg->total_quan / $maxQuan) * 100));
                            $sharePercent = round(($reg->total_quan / $totalRegionalQuan) * 100);
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-slate-800">{{ $reg->ten_tinh_thanh }}</span>
                                <span class="font-bold text-indigo-600">{{ number_format($reg->total_quan) }} quán <span class="text-[10px] text-slate-400 font-medium">({{ $sharePercent }}%)</span></span>
                            </div>
                            <div class="tk-progress-track">
                                <div class="tk-progress-fill" style="width: {{ $barPercent }}%;" data-width-percent="{{ $barPercent }}"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Chưa ghi nhận thông tin địa bàn hoạt động của các quán ăn.</p>
                    @endforelse
                </div>
            </div>

            {{-- Package Revenue Breakdown --}}
            <div class="tk-card">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 mb-3 pb-2.5 border-b border-slate-100">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">inventory_2</span>
                    Doanh Thu Theo Gói Dịch Vụ
                </h3>
                <div class="space-y-2.5">
                    @forelse($packageStats as $pkg)
                        <div class="flex items-center justify-between gap-2 text-xs py-2 border-b border-slate-100 last:border-0">
                            <div class="min-w-0">
                                <p class="font-bold text-slate-900 truncate">{{ $pkg->ten_goi_dich_vu ?? 'Gói mặc định' }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ number_format($pkg->total_count) }} giao dịch thành công</p>
                            </div>
                            <span class="font-extrabold text-emerald-600 text-sm shrink-0">{{ number_format($pkg->total_revenue, 0, ',', '.') }} đ</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Chưa phát sinh doanh thu từ gói dịch vụ nào trong kỳ lọc.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- 6. TRANSACTIONS TABLE IN RANGE WITH FILTER & SEARCH --}}
    <div class="tk-card space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">receipt_long</span>
                    Danh Sách Giao Dịch Trong Kỳ Lọc
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Dữ liệu chi tiết giao dịch từ <strong>{{ \Carbon\Carbon::parse($tuNgayStr)->format('d/m/Y') }}</strong> đến <strong>{{ \Carbon\Carbon::parse($denNgayStr)->format('d/m/Y') }}</strong>
                </p>
            </div>

            {{-- Filter & Search --}}
            <form action="{{ route('admin.thong-ke.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="tu_ngay" value="{{ $tuNgayStr }}">
                <input type="hidden" name="den_ngay" value="{{ $denNgayStr }}">
                @if($preset)<input type="hidden" name="preset" value="{{ $preset }}">@endif

                <select name="trang_thai_tt" onchange="this.form.submit()" class="px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 outline-none focus:border-amber-500 cursor-pointer">
                    <option value="">Tất cả trạng thái</option>
                    <option value="thanh_cong" {{ $statusFilter === 'thanh_cong' ? 'selected' : '' }}>Thành công</option>
                    <option value="cho_xu_ly" {{ $statusFilter === 'cho_xu_ly' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="that_bai" {{ $statusFilter === 'that_bai' ? 'selected' : '' }}>Thất bại / Đã hủy</option>
                </select>

                <div class="admin-search-wrapper w-56 sm:w-64">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" name="search" value="{{ $searchKey }}" placeholder="Tìm mã GD, tên khách, quán...">
                </div>

                <button type="submit" class="btn-enterprise btn-enterprise-secondary">
                    <span>Tìm</span>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 uppercase text-[11px] font-bold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Mã giao dịch</th>
                        <th class="py-3 px-4">Khách hàng</th>
                        <th class="py-3 px-4">Quán liên quan</th>
                        <th class="py-3 px-4">Nội dung / Gói dịch vụ</th>
                        <th class="py-3 px-4 text-right">Số tiền</th>
                        <th class="py-3 px-4">Phương thức</th>
                        <th class="py-3 px-4">Trạng thái</th>
                        <th class="py-3 px-4 text-right">Thời gian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $p)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $p->ma_giao_dich }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $p->user->ho_ten ?? 'Khách vãng lai / Đã xóa' }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $p->quan->ten_quan ?? 'Ví cá nhân' }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-700">{{ $p->ten_goi_dich_vu }}</td>
                            <td class="py-3 px-4 text-right font-extrabold text-emerald-600 text-sm">{{ number_format($p->so_tien, 0, ',', '.') }} đ</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 font-bold text-[10px] text-slate-700 uppercase">
                                    {{ $p->phuong_thuc ?? 'SEPAY' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($p->trang_thai === 'thanh_cong')
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-emerald-100 text-emerald-800 border border-emerald-200">Thành công</span>
                                @elseif(in_array($p->trang_thai, ['cho_xu_ly', 'cho_thanh_toan']))
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-amber-100 text-amber-800 border border-amber-200">Chờ xử lý</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-rose-100 text-rose-800 border border-rose-200">Thất bại</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right text-slate-500 font-medium">{{ $p->created_at ? $p->created_at->format('d/m/Y H:i') : '' }}</td>
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

{{-- Modal 1-Click Phát Sóng Chiến Dịch Thông Báo Cho Chủ Quán Từ AI --}}
<div id="ai-broadcast-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <span class="material-symbols-outlined">campaign</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Phát Sóng Chiến Dịch Ưu Đãi (1-Click AI)</h3>
                    <p class="text-xs text-slate-500">Nội dung đã được AI CFO soạn sẵn, gửi trực tiếp tới các Chủ Quán</p>
                </div>
            </div>
            <button type="button" onclick="closeAiBroadcastModal()" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="ai-broadcast-form" onsubmit="submitAiBroadcast(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nhóm nhận thông báo</label>
                <select id="ai-bc-doi-tuong" name="doi_tuong" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs font-bold text-slate-800 outline-none focus:border-emerald-500">
                    <option value="chu_quan">Toàn bộ Chủ Quán trên hệ thống</option>
                    <option value="tat_ca">Tất cả thành viên & Chủ Quán</option>
                    <option value="nguoi_dung">Thực khách / Người dùng</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tiêu đề chiến dịch</label>
                <input type="text" id="ai-bc-tieu-de" name="tieu_de" required maxlength="255"
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs font-bold text-slate-900 outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nội dung thông báo chi tiết</label>
                <textarea id="ai-bc-noi-dung" name="noi_dung" rows="4" required
                          class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-800 leading-relaxed outline-none focus:border-emerald-500"></textarea>
            </div>

            <div id="ai-bc-feedback" class="hidden rounded-xl p-3 text-xs font-bold"></div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeAiBroadcastModal()" class="btn-enterprise btn-enterprise-secondary">
                    <span>Hủy bỏ</span>
                </button>
                <button type="submit" id="btn-submit-ai-bc" class="btn-enterprise btn-enterprise-success">
                    <span class="material-symbols-outlined">send</span>
                    <span id="text-submit-ai-bc">Xác nhận & Phát sóng ngay</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script type="application/json" id="admin-statistics-chart-data">@json($chartData)</script>
<script>
    window.escapeHtmlAi = function(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    window.applyQuickAiQuestion = function(focusMode, questionText) {
        const focusEl = document.getElementById('ai-focus-select');
        const promptEl = document.getElementById('ai-custom-prompt');
        if (focusEl && focusMode) focusEl.value = focusMode;
        if (promptEl) promptEl.value = questionText || '';
        window.refreshAdminAiRevenueInsights();
    };

    window.openAiBroadcastModalFromBtn = function(btn) {
        if (!btn) return;
        window.openAiBroadcastModal(
            btn.getAttribute('data-tieu-de') || '',
            btn.getAttribute('data-noi-dung') || '',
            btn.getAttribute('data-doi-tuong') || 'chu_quan'
        );
    };

    window.openAiBroadcastModal = function(tieuDe, noiDung, doiTuong = 'chu_quan') {
        const modal = document.getElementById('ai-broadcast-modal');
        document.getElementById('ai-bc-tieu-de').value = tieuDe || '';
        document.getElementById('ai-bc-noi-dung').value = noiDung || '';
        document.getElementById('ai-bc-doi-tuong').value = doiTuong || 'chu_quan';
        const fb = document.getElementById('ai-bc-feedback');
        fb.classList.add('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.closeAiBroadcastModal = function() {
        const modal = document.getElementById('ai-broadcast-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    window.submitAiBroadcast = async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-submit-ai-bc');
        const txt = document.getElementById('text-submit-ai-bc');
        const fb = document.getElementById('ai-bc-feedback');

        btn.disabled = true;
        txt.textContent = 'Đang phát sóng...';
        fb.classList.add('hidden');

        try {
            const response = await fetch('{{ route("admin.thong-bao.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tieu_de: document.getElementById('ai-bc-tieu-de').value,
                    noi_dung: document.getElementById('ai-bc-noi-dung').value,
                    doi_tuong: document.getElementById('ai-bc-doi-tuong').value
                })
            });

            const res = await response.json();
            fb.classList.remove('hidden', 'bg-rose-50', 'text-rose-700', 'bg-emerald-50', 'text-emerald-700');

            if (response.ok && res.success) {
                fb.classList.add('bg-emerald-50', 'text-emerald-700');
                fb.textContent = '✅ ' + (res.message || 'Đã gửi thông báo chiến dịch tới các Chủ Quán thành công!');
                setTimeout(() => window.closeAiBroadcastModal(), 1500);
            } else {
                fb.classList.add('bg-rose-50', 'text-rose-700');
                fb.textContent = '⚠️ ' + (res.message || 'Không thể gửi thông báo, vui lòng thử lại.');
            }
        } catch (err) {
            fb.classList.remove('hidden');
            fb.classList.add('bg-rose-50', 'text-rose-700');
            fb.textContent = '⚠️ Lỗi kết nối mạng khi gửi thông báo.';
        } finally {
            btn.disabled = false;
            txt.textContent = 'Xác nhận & Phát sóng ngay';
        }
    };

    window.renderAiStrategiesGrid = function(strategies) {
        const grid = document.getElementById('ai-strategies-grid');
        if (!grid || !Array.isArray(strategies)) return;

        const featuredUrl = '{{ route("admin.quan-noi-bat.index") }}';
        const aiStudioUrl = '{{ route("admin.khuyen-mai-ai.index") }}';

        grid.innerHTML = strategies.map(st => {
            const isHigh = (st.uu_tien || 'cao') === 'cao';
            const priorityCls = isHigh ? 'priority-high' : 'priority-normal';
            const badgeCls = isHigh
                ? 'bg-amber-100 text-amber-800 border border-amber-300'
                : 'bg-indigo-100 text-indigo-800 border border-indigo-200';
            const badgeTxt = isHigh ? '🔥 Ưu tiên cao' : '💡 Chiến lược';
            const impactHtml = st.tac_dong_du_kien
                ? `<span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">${window.escapeHtmlAi(st.tac_dong_du_kien)}</span>`
                : '';

            const notif = st.thong_bao_mau || null;
            let buttonsHtml = '';

            if (notif) {
                buttonsHtml += `
                    <button type="button"
                            data-tieu-de="${window.escapeHtmlAi(notif.tieu_de || '')}"
                            data-noi-dung="${window.escapeHtmlAi(notif.noi_dung || '')}"
                            data-doi-tuong="${window.escapeHtmlAi(notif.doi_tuong || 'chu_quan')}"
                            onclick="openAiBroadcastModalFromBtn(this)"
                            class="btn-enterprise btn-enterprise-success w-full">
                        <span class="material-symbols-outlined">campaign</span>
                        <span>${window.escapeHtmlAi(st.nhan_nut || 'Phát sóng ưu đãi cho Chủ Quán')}</span>
                    </button>
                `;
            }

            if (st.hanh_dong_loai === 'featured_venues') {
                buttonsHtml += `
                    <a href="${featuredUrl}" class="btn-enterprise btn-enterprise-secondary w-full">
                        <span class="material-symbols-outlined">local_fire_department</span>
                        <span>Mở Quản Lý Quán Nổi Bật &rarr;</span>
                    </a>
                `;
            } else if (st.hanh_dong_loai === 'ai_studio') {
                buttonsHtml += `
                    <a href="${aiStudioUrl}" class="btn-enterprise btn-enterprise-indigo w-full">
                        <span class="material-symbols-outlined">auto_fix_high</span>
                        <span>${window.escapeHtmlAi(st.nhan_nut || 'Mở Khuyến Mãi AI Studio')} &rarr;</span>
                    </a>
                `;
            }

            return `
                <div class="tk-ai-strategy-card ${priorityCls}">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase ${badgeCls}">${badgeTxt}</span>
                            ${impactHtml}
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 leading-snug">${window.escapeHtmlAi(st.tieu_de || '')}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed"><strong class="text-slate-900">Phân tích:</strong> ${window.escapeHtmlAi(st.phan_tich || '')}</p>
                        <div class="tk-ai-solution-callout">
                            <strong class="text-amber-800">Giải pháp AI đề xuất:</strong> ${window.escapeHtmlAi(st.giai_phap || '')}
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col gap-2">
                        ${buttonsHtml}
                    </div>
                </div>
            `;
        }).join('');
    };

    window.refreshAdminAiRevenueInsights = async function() {
        const btn = document.getElementById('btn-refresh-ai-revenue');
        const icon = document.getElementById('icon-refresh-ai');
        const text = document.getElementById('text-refresh-ai');
        const loadingBox = document.getElementById('ai-revenue-loading-box');
        const resultContainer = document.getElementById('ai-revenue-result-container');
        const toastBox = document.getElementById('ai-analysis-toast');
        const stepTitle = document.getElementById('ai-loading-step-title');

        const focusVal = document.getElementById('ai-focus-select')?.value || 'tong_hop';
        const customPromptVal = document.getElementById('ai-custom-prompt')?.value || '';

        btn.disabled = true;
        icon.textContent = 'sync';
        icon.classList.add('animate-spin');
        text.textContent = 'Kira AI đang phân tích...';

        if (toastBox) toastBox.classList.add('hidden');
        if (loadingBox) loadingBox.classList.remove('hidden');
        if (resultContainer) resultContainer.style.opacity = '0.35';

        if (stepTitle) {
            stepTitle.textContent = 'Bước 1/3: Đang quét lịch sử giao dịch SePay & đối soát số dư ví lưu hành...';
            setTimeout(() => {
                if (btn.disabled && stepTitle) {
                    stepTitle.textContent = 'Bước 2/3: Đang phân tích ma trận khu vực, gói Quán Nổi Bật & tính toán dự báo 30 ngày...';
                }
            }, 380);
            setTimeout(() => {
                if (btn.disabled && stepTitle) {
                    stepTitle.textContent = 'Bước 3/3: Kira AI CFO đang tổng hợp nhận định & khởi tạo 3 chiến dịch hành động 1-Click...';
                }
            }, 750);
        }

        const startTime = Date.now();

        try {
            const response = await fetch('{{ route("admin.thong-ke.ai-analyze") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tu_ngay: '{{ $tuNgayStr }}',
                    den_ngay: '{{ $denNgayStr }}',
                    focus: focusVal,
                    custom_prompt: customPromptVal
                })
            });

            const elapsed = Date.now() - startTime;
            if (elapsed < 900) {
                await new Promise(r => setTimeout(r, 900 - elapsed));
            }

            const json = await response.json();
            if (response.ok && json.success && json.data) {
                const d = json.data;
                const m = d.metrics || {};

                if (d.nhan_dinh_cfo) {
                    document.getElementById('ai-cfo-summary').textContent = d.nhan_dinh_cfo;
                }
                if (d.provider_label) {
                    document.getElementById('ai-provider-text').textContent = d.provider_label;
                }
                if (d.analyzed_at) {
                    document.getElementById('ai-analyzed-time').textContent = 'Cập nhật: ' + d.analyzed_at;
                }
                if (m.diem_suc_khoe && m.nhan_suc_khoe) {
                    document.getElementById('ai-health-label').textContent = `${m.diem_suc_khoe}/100 • ${m.nhan_suc_khoe}`;
                }
                if (m.du_bao_30_ngay) {
                    const minF = Number(m.du_bao_30_ngay.min || 0).toLocaleString('vi-VN') + 'đ';
                    const maxF = Number(m.du_bao_30_ngay.max || 0).toLocaleString('vi-VN') + 'đ';
                    document.getElementById('ai-forecast-range').textContent = `${minF} – ${maxF}`;
                }
                if (d.nhan_xet_du_bao) {
                    document.getElementById('ai-forecast-note').textContent = d.nhan_xet_du_bao;
                }

                if (Array.isArray(d.diem_sang)) {
                    document.getElementById('ai-highlights-list').innerHTML = d.diem_sang
                        .map(item => `<li class="flex items-start gap-2"><span class="text-emerald-600 font-bold">•</span><span>${window.escapeHtmlAi(item)}</span></li>`)
                        .join('');
                }

                if (Array.isArray(d.canh_bao_rui_ro)) {
                    document.getElementById('ai-warnings-list').innerHTML = d.canh_bao_rui_ro
                        .map(item => `<li class="flex items-start gap-2"><span class="text-amber-600 font-bold">•</span><span>${window.escapeHtmlAi(item)}</span></li>`)
                        .join('');
                }

                if (Array.isArray(d.khuyen_nghi_chien_luoc)) {
                    window.renderAiStrategiesGrid(d.khuyen_nghi_chien_luoc);
                }

                if (toastBox) {
                    const focusNames = {
                        tong_hop: 'Tổng quan dòng tiền & Dự báo 30 ngày',
                        kich_cau_nap_vi: 'Chiến lược Kích cầu nạp ví SePay',
                        goi_noi_bat: 'Chuyển đổi Gói Nổi Bật & Tích Xanh',
                        chong_roi_bo: 'Cảnh báo rời bỏ (Churn Risk) & Giữ chân VIP'
                    };
                    document.getElementById('ai-analysis-toast-text').textContent =
                        'Kira AI đã phân tích xong [' + (focusNames[focusVal] || 'Doanh thu toàn sàn') + '] và cập nhật khuyến nghị mới!';
                    document.getElementById('ai-analysis-toast-time').textContent = 'Lúc ' + (d.analyzed_at || 'vừa xong');
                    toastBox.classList.remove('hidden');
                }
            }
        } catch (err) {
            console.error('AI Analyze error:', err);
        } finally {
            if (loadingBox) loadingBox.classList.add('hidden');
            if (resultContainer) resultContainer.style.opacity = '1';
            btn.disabled = false;
            icon.classList.remove('animate-spin');
            icon.textContent = 'auto_awesome';
            text.textContent = 'Kira AI Phân Tích Ngay';
        }
    };

    function renderAdminStatisticsCharts() {
        const dataEl = document.getElementById('admin-statistics-chart-data');
        if (!dataEl) return;

        let parsedData = {};
        try {
            parsedData = JSON.parse(dataEl.textContent);
        } catch (e) {
            return;
        }

        const { labels = [], revenue: revenueData = [], quan: quanData = [], users: userData = [] } = parsedData;

        // 1. Chart: Revenue Trend
        const canvasRev = document.getElementById('revenueTrendChart');
        if (canvasRev && window.Chart) {
            if (window.adminRevChartInstance) {
                window.adminRevChartInstance.destroy();
            }
            const ctxRev = canvasRev.getContext('2d');
            const gradientRev = ctxRev.createLinearGradient(0, 0, 0, 260);
            gradientRev.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
            gradientRev.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

            window.adminRevChartInstance = new Chart(ctxRev, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: revenueData,
                        borderColor: '#10b981',
                        backgroundColor: gradientRev,
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1.5,
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
                                    return 'Doanh thu: ' + Number(context.parsed.y || 0).toLocaleString('vi-VN') + ' đ';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { maxTicksLimit: 10, font: { family: 'Inter', size: 11 }, color: '#64748b' }
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMax: 100000,
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
        const canvasGrowth = document.getElementById('growthTrendChart');
        if (canvasGrowth && window.Chart) {
            if (window.adminGrowthChartInstance) {
                window.adminGrowthChartInstance.destroy();
            }
            const ctxGrowth = canvasGrowth.getContext('2d');
            window.adminGrowthChartInstance = new Chart(ctxGrowth, {
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
                            ticks: { maxTicksLimit: 8, font: { family: 'Inter', size: 10 }, color: '#64748b' }
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMax: 5,
                            grid: { color: '#f1f5f9' },
                            ticks: { precision: 0, font: { family: 'Inter', size: 10 }, color: '#64748b' }
                        }
                    }
                }
            });
        }
    }

    function initAdminStatisticsPage() {
        if (window.Chart) {
            renderAdminStatisticsCharts();
        } else {
            let s = document.querySelector('script[src*="chart.js"]');
            if (!s) {
                s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                s.onload = renderAdminStatisticsCharts;
                document.head.appendChild(s);
            } else {
                s.addEventListener('load', renderAdminStatisticsCharts);
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminStatisticsPage);
    } else {
        initAdminStatisticsPage();
    }
</script>
@endsection
