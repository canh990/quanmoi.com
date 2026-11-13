@extends(request()->ajax() ? 'admin.ajax_layout' : 'admin.layout')

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

    /* Accordion Components */
    .tk-accordion-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 1rem !important;
        overflow: hidden !important;
        box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04) !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    }
    .tk-accordion-card:hover {
        border-color: #cbd5e1 !important;
    }
    .tk-accordion-header {
        width: 100% !important;
        padding: 0.875rem 1.25rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 0.75rem !important;
        background-color: #ffffff !important;
        border: none !important;
        cursor: pointer !important;
        text-align: left !important;
        transition: background-color 0.15s ease !important;
    }
    .tk-accordion-header:hover {
        background-color: #f8fafc !important;
    }
    .tk-accordion-card.is-open .tk-accordion-header {
        border-bottom: 1px solid #f1f5f9 !important;
        background-color: #fafbfc !important;
    }
    .tk-accordion-icon {
        width: 2.25rem !important;
        height: 2.25rem !important;
        border-radius: 0.625rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }
    .tk-accordion-badge {
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        background-color: #f1f5f9 !important;
        padding: 0.2rem 0.55rem !important;
        border-radius: 9999px !important;
        border: 1px solid #e2e8f0 !important;
    }
    .tk-accordion-chevron {
        transition: transform 0.25s ease !important;
    }
    .tk-accordion-card.is-open .tk-accordion-chevron {
        transform: rotate(180deg) !important;
    }
    .tk-accordion-body {
        display: block !important;
        padding: 1.25rem !important;
    }
    .tk-accordion-card:not(.is-open) .tk-accordion-body {
        display: none !important;
    }
    .tk-accordion-card .tk-ai-panel {
        border: none !important;
        padding: 0 !important;
        box-shadow: none !important;
        background: transparent !important;
    }

    /* AI CFO Panel - Clean Modern UI */
    .tk-ai-panel {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 1rem !important;
        padding: 1.25rem 1.5rem !important;
        box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04) !important;
    }
    /* ChatGPT-style Container & Stream */
    .tk-chat-container {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 1rem !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
        box-shadow: 0 4px 14px -4px rgba(15, 23, 42, 0.06) !important;
    }
    .tk-chat-stream {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%) !important;
        padding: 1.25rem !important;
        overflow-y: auto !important;
        max-height: 400px !important;
        min-height: 260px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 1.125rem !important;
        scroll-behavior: smooth !important;
    }
    .tk-chat-stream::-webkit-scrollbar {
        width: 6px;
    }
    .tk-chat-stream::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .tk-chat-stream::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    .tk-chat-stream::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .tk-ai-control-bar {
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 0.875rem 1rem !important;
    }
    .tk-ai-control-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 0.625rem !important;
    }
    @media (min-width: 768px) {
        .tk-ai-control-grid {
            grid-template-columns: 210px minmax(0, 1fr) auto !important;
            align-items: center !important;
        }
    }
    .tk-ai-summary-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    @media (min-width: 1024px) {
        .tk-ai-summary-grid {
            grid-template-columns: minmax(0, 1.85fr) minmax(0, 1.15fr) !important;
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
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 1.25rem !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1.25rem;
        min-width: 0;
    }
    .tk-ai-mini-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 0.625rem;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
    }
    @media (min-width: 640px) {
        .tk-ai-mini-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    .tk-ai-mini-card {
        background-color: #f8fafc !important;
        border: 1px solid #f1f5f9 !important;
        border-radius: 0.5rem !important;
        padding: 0.625rem 0.75rem !important;
        min-width: 0;
    }
    .tk-ai-forecast-box {
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 1.25rem !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
    }
    .tk-ai-forecast-value-box {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        padding: 0.75rem 1rem !important;
        margin: 0.625rem 0 !important;
        min-width: 0;
    }
    .tk-ai-highlight-box {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 1rem 1.25rem !important;
    }
    .tk-ai-warning-box {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 1rem 1.25rem !important;
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
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 1.125rem !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        transition: all 0.15s ease !important;
        box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.04) !important;
    }
    .tk-ai-strategy-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.08) !important;
    }
    .tk-ai-solution-callout {
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        padding: 0.625rem 0.75rem !important;
        color: #334155 !important;
        font-size: 0.75rem !important;
        line-height: 1.5 !important;
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

<div class="tk-page-wrap space-y-4">

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

            {{-- Accordion Master Controls (Mở tất cả / Thu gọn tất cả) --}}
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg border border-slate-200">
                <button type="button" onclick="toggleAllAccordions(true)" class="px-2 py-1 rounded-md text-slate-700 hover:bg-white hover:shadow-2xs font-medium text-[11px] transition-all flex items-center gap-1" title="Mở rộng tất cả các mục">
                    <span class="material-symbols-outlined text-[15px]">unfold_more</span>
                    <span class="hidden sm:inline">Mở tất cả</span>
                </button>
                <span class="text-slate-300 text-xs">|</span>
                <button type="button" onclick="toggleAllAccordions(false)" class="px-2 py-1 rounded-md text-slate-700 hover:bg-white hover:shadow-2xs font-medium text-[11px] transition-all flex items-center gap-1" title="Thu gọn tất cả các mục">
                    <span class="material-symbols-outlined text-[15px]">unfold_less</span>
                    <span class="hidden sm:inline">Thu gọn</span>
                </button>
            </div>
        </div>
    </div>

    {{-- MỤC 1 (ACCORDION): 4 KPI SUMMARY CARDS --}}
    <div class="tk-accordion-card" id="accordion-section-kpi">
        <button type="button" class="tk-accordion-header" onclick="toggleTkAccordion('accordion-section-kpi')">
            <div class="flex items-center gap-3 min-w-0">
                <span class="tk-accordion-icon bg-amber-50 text-amber-600 border border-amber-200">
                    <span class="material-symbols-outlined text-[20px]">bar_chart</span>
                </span>
                <div class="text-left min-w-0">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">
                        1. Chỉ Số Tổng Quan & Tăng Trưởng Kỳ Này
                    </h2>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">
                        +{{ number_format($quanStats['filtered']) }} Quán mới • +{{ number_format($userStats['filtered']) }} Thành viên mới • {{ number_format($revenueStats['filtered'], 0, ',', '.') }}đ Doanh thu
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="tk-accordion-badge">4 Chỉ số</span>
                <span class="material-symbols-outlined tk-accordion-chevron text-[20px] text-slate-400">expand_more</span>
            </div>
        </button>
        <div class="tk-accordion-body">
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
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MỤC 2 (ACCORDION): AI REVENUE INTELLIGENCE & PLATFORM GROWTH ENGINE (KIRA AI CFO) --}}
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
        $providerLabel = $aiData['provider_label'] ?? 'Google Gemini AI';
        $analyzedAt = $aiData['analyzed_at'] ?? now()->format('H:i d/m/Y');
        $activeCustomPrompt = $aiData['custom_prompt'] ?? '';
        $activeAnswer = $aiData['cau_tra_loi_cau_hoi'] ?? ($aiData['nhan_dinh_cfo'] ?? '');
    @endphp
    <div class="tk-accordion-card" id="accordion-section-ai">
        <button type="button" class="tk-accordion-header" onclick="toggleTkAccordion('accordion-section-ai')">
            <div class="flex items-center gap-3 min-w-0">
                <span class="tk-accordion-icon bg-indigo-50 text-indigo-600 border border-indigo-200">
                    <span class="material-symbols-outlined text-[20px]">smart_toy</span>
                </span>
                <div class="text-left min-w-0">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">
                        2. AI Cố Vấn Tăng Trưởng Doanh Thu (Kira AI CFO)
                    </h2>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">
                        {{ $providerLabel }} • Khung chat trực tuyến &amp; dự báo dòng tiền 30 ngày
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="tk-accordion-badge bg-emerald-50 text-emerald-700 border-emerald-200">AI Trực tuyến</span>
                <span class="material-symbols-outlined tk-accordion-chevron text-[20px] text-slate-400">expand_more</span>
            </div>
        </button>
        <div class="tk-accordion-body">
            <div id="admin-ai-revenue-section" class="tk-ai-panel space-y-6">
        {{-- ========================================================================= --}}
        {{-- KHUNG CHAT AI CFO CHUẨN CHATGPT (CHATGPT CONVERSATION INTERFACE) --}}
        {{-- ========================================================================= --}}
        <div class="tk-chat-container">
            {{-- Header Chat Box (ChatGPT Style) --}}
            <div class="p-3.5 sm:p-4 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-3 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-600 to-indigo-600 flex items-center justify-center text-white shadow-md">
                            <span class="material-symbols-outlined text-[20px]">smart_toy</span>
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-slate-900"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-sm sm:text-base text-white tracking-tight">Kira AI CFO</h3>
                            <span id="ai-provider-text" class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-indigo-950 text-indigo-300 border border-indigo-800">
                                {{ $providerLabel }}
                            </span>
                        </div>
                        <p class="text-[11px] text-emerald-400 flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Trực tuyến
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Hidden field lưu trọng tâm phân tích cho JS --}}
                    <input type="hidden" id="ai-focus-select" value="tong_hop">

                    {{-- Nút xóa chat / reset hội thoại --}}
                    <button type="button" onclick="clearAiChatHistory()" title="Làm mới chat" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-300 hover:text-white text-xs font-medium flex items-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-[17px]">restart_alt</span>
                        <span class="hidden sm:inline">Làm mới chat</span>
                    </button>
                </div>
            </div>

            {{-- Luồng tin nhắn Chat (Cuộn tin nhắn giống ChatGPT) --}}
            <div id="ai-chat-messages" class="tk-chat-stream">
                {{-- Tin nhắn chào mừng ban đầu từ AI --}}
                <div class="flex justify-start items-start gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                        <span class="material-symbols-outlined text-[18px]">smart_toy</span>
                    </div>
                    <div class="space-y-1 max-w-[90%] sm:max-w-[80%]">
                        <div class="p-3.5 rounded-2xl rounded-tl-xs bg-white border border-slate-200 text-slate-800 text-xs sm:text-sm shadow-2xs leading-relaxed">
                            Xin chào Admin! Tôi là Kira AI. Tôi có thể hỗ trợ bạn theo dõi dòng tiền SePay, giải phóng số dư ví và tối ưu hóa doanh thu toàn sàn. Bạn cần hỗ trợ gì hôm nay?
                        </div>
                        <p class="text-[10px] text-slate-400 pl-1">{{ now()->format('H:i') }}</p>
                    </div>
                </div>

                {{-- Nếu trước đó đã có câu hỏi người dùng, hiển thị tin nhắn đó và câu trả lời --}}
                @if(!empty($activeCustomPrompt))
                    <div class="flex justify-end items-end gap-2.5">
                        <div class="space-y-1 max-w-[85%] sm:max-w-[75%]">
                            <div class="p-3.5 rounded-2xl rounded-tr-xs bg-indigo-600 text-white text-xs sm:text-sm font-medium shadow-2xs leading-relaxed">
                                {{ $activeCustomPrompt }}
                            </div>
                            <p class="text-[10px] text-right text-slate-400 pr-1">{{ now()->format('H:i') }}</p>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-2xs">
                            <span class="material-symbols-outlined text-[17px]">person</span>
                        </div>
                    </div>

                    @if(!empty($activeAnswer))
                        <div class="flex justify-start items-start gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                <span class="material-symbols-outlined text-[18px]">smart_toy</span>
                            </div>
                            <div class="space-y-1 max-w-[90%] sm:max-w-[80%]">
                                <div class="p-3.5 rounded-2xl rounded-tl-xs bg-white border border-slate-200 text-slate-800 text-xs sm:text-sm shadow-2xs leading-relaxed whitespace-pre-line">
                                    {{ $activeAnswer }}
                                </div>
                                <p class="text-[10px] text-slate-400 pl-1">{{ now()->format('H:i') }}</p>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Vùng Nhập Tin Nhắn Ở ĐÁY Khung Chat (ChatGPT Style) --}}
            <div class="p-3 sm:p-4 bg-white border-t border-slate-200 space-y-2">
                {{-- Gợi ý mẫu tinh gọn --}}
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <button type="button" onclick="sendQuickAiMessage('Làm sao tăng doanh thu nạp ví SePay thêm 30 triệu trong tháng này?', 'kich_cau_nap_vi')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 border border-slate-200 text-slate-700 text-[11px] font-medium transition-all shadow-2xs">
                        💰 Tăng nạp ví +30tr
                    </button>
                    <button type="button" onclick="sendQuickAiMessage('Chiến lược thuyết phục các quán đang đông khách mua Gói Quán Nổi Bật?', 'goi_noi_bat')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 border border-slate-200 text-slate-700 text-[11px] font-medium transition-all shadow-2xs">
                        🔥 Kích cầu Gói Nổi Bật
                    </button>
                    <button type="button" onclick="sendQuickAiMessage('Làm sao giải phóng 25k số dư ví nhàn rỗi và giảm 22 đơn lỗi SePay?', 'chong_roi_bo')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 border border-slate-200 text-slate-700 text-[11px] font-medium transition-all shadow-2xs">
                        ⚡ Giảm lỗi SePay & giải phóng ví
                    </button>
                </div>

                {{-- Ô Nhập & Nút Gửi kiểu ChatGPT --}}
                <div class="relative flex items-center rounded-2xl bg-slate-50 border border-slate-300 focus-within:border-indigo-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-100 transition-all shadow-2xs p-1.5">
                    <input type="text" id="ai-custom-prompt"
                           onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();submitAiChatMessage();}"
                           placeholder="Hỏi AI CFO... (Enter để gửi)"
                           class="w-full bg-transparent pl-3 pr-2 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-400 outline-none">
                    <button type="button"
                            id="btn-refresh-ai-revenue"
                            onclick="submitAiChatMessage()"
                            class="w-9 h-9 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white flex items-center justify-center shrink-0 transition-all shadow-xs disabled:opacity-50">
                        <span id="icon-refresh-ai" class="material-symbols-outlined text-[19px]">arrow_upward</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- BẢNG PHÂN TÍCH TÀI CHÍNH ĐỒNG BỘ DƯỚI KHUNG CHAT --}}
        <div class="space-y-4 pt-1">
            <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2">
                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-indigo-600 text-[18px]">analytics</span>
                    Báo Cáo Phân Tích Tài Chính & Dự Báo Thời Gian Thực
                </span>
                <span class="text-[11px] text-slate-400" id="ai-analyzed-time">Cập nhật: {{ $analyzedAt }}</span>
            </div>

            {{-- Khối nội dung kết quả phân tích AI --}}
            <div id="ai-revenue-result-container" class="space-y-4">

            {{-- Row 1: CFO Executive Summary + 30-Day Revenue Forecast --}}
            <div class="tk-ai-summary-grid">
                {{-- Left: CFO Summary + Financial Sub-metrics --}}
                <div class="tk-ai-cfo-box">
                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <span class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[17px] text-slate-600">psychology</span>
                                Nhận Định Tài Chính & Dòng Tiền
                            </span>
                            <span class="text-[11px] text-slate-400" id="ai-analyzed-time">Cập nhật: {{ $aiData['analyzed_at'] ?? now()->format('H:i d/m/Y') }}</span>
                        </div>
                        <p id="ai-cfo-summary" class="text-xs sm:text-sm text-slate-700 leading-relaxed font-normal">
                            {{ $aiData['nhan_dinh_cfo'] ?? 'Đang tổng hợp dữ liệu dòng tiền toàn sàn...' }}
                        </p>
                    </div>

                    {{-- 3 Quick Financial Indicators --}}
                    <div class="tk-ai-mini-grid">
                        <div class="tk-ai-mini-card">
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Tăng trưởng</p>
                            <p class="text-sm sm:text-base font-bold mt-0.5 {{ $tangTruongPt >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $tangTruongPt >= 0 ? '+' : '' }}{{ $tangTruongPt }}% <span class="text-[11px] font-normal text-slate-400">vs kỳ trước</span>
                            </p>
                        </div>
                        <div class="tk-ai-mini-card">
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Số dư ví lưu hành</p>
                            <p class="text-sm sm:text-base font-bold text-slate-800 mt-0.5">
                                {{ number_format($tongSoDuVi, 0, ',', '.') }} đ
                            </p>
                        </div>
                        <div class="tk-ai-mini-card">
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Chưa ghim nổi bật</p>
                            <p class="text-sm sm:text-base font-bold text-amber-600 mt-0.5">
                                {{ number_format($quanChuaNoiBat) }} quán
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Right: 30-Day Revenue Forecast --}}
                <div class="tk-ai-forecast-box">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[17px] text-emerald-600 shrink-0">trending_up</span>
                                <span>Dự Báo Doanh Thu 30 Ngày</span>
                            </span>
                            <span class="px-2 py-0.5 rounded bg-white text-emerald-700 border border-slate-200 text-[10px] font-semibold whitespace-nowrap shrink-0">AI Forecast</span>
                        </div>
                        <div class="tk-ai-forecast-value-box">
                            <p class="text-[11px] font-medium text-slate-500">Ước tính dòng tiền nạp & gói dịch vụ:</p>
                            <h3 id="ai-forecast-range" class="text-base sm:text-lg font-extrabold text-emerald-600 mt-0.5 tracking-tight break-words">
                                {{ number_format($duBaoMin, 0, ',', '.') }}đ – {{ number_format($duBaoMax, 0, ',', '.') }}đ
                            </h3>
                        </div>
                        <p id="ai-forecast-note" class="text-xs text-slate-600 leading-relaxed font-normal">
                            {{ $aiData['nhan_xet_du_bao'] ?? '' }}
                        </p>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-normal">Đối soát dòng tiền SePay</span>
                        <a href="{{ route('admin.thanh-toan.index') }}" class="font-semibold text-emerald-700 hover:underline whitespace-nowrap">
                            Quản lý ví & Nạp tiền &rarr;
                        </a>
                    </div>
                </div>
            </div>

            {{-- Row 2: Highlights vs Risk Warnings --}}
            <div class="tk-ai-two-col-grid">
                {{-- Điểm sáng tăng trưởng --}}
                <div class="tk-ai-highlight-box">
                    <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5 mb-2.5">
                        <span class="material-symbols-outlined text-[17px] text-emerald-600">check_circle</span>
                        Điểm Sáng Tài Chính & Tăng Trưởng
                    </h4>
                    <ul id="ai-highlights-list" class="space-y-1.5 text-xs text-slate-700 font-normal">
                        @foreach(($aiData['diem_sang'] ?? []) as $hl)
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-500 font-bold">•</span>
                                <span>{{ $hl }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Cảnh báo rủi ro & Điểm nghẽn --}}
                <div class="tk-ai-warning-box">
                    <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5 mb-2.5">
                        <span class="material-symbols-outlined text-[17px] text-amber-500">warning</span>
                        Cảnh Báo Rủi Ro & Điểm Nghẽn Dòng Tiền
                    </h4>
                    <ul id="ai-warnings-list" class="space-y-1.5 text-xs text-slate-700 font-normal">
                        @foreach(($aiData['canh_bao_rui_ro'] ?? []) as $warn)
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 font-bold">•</span>
                                <span>{{ $warn }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Row 3: 3 Actionable Strategic Recommendations (1-Click Execution) --}}
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2.5">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[17px] text-slate-700">rocket_launch</span>
                        Gợi Ý Chiến Lược Tăng Doanh Thu Sàn (Thực Thi 1-Click)
                    </h3>
                    <span class="text-xs text-slate-400 font-normal">Bấm nút hành động trên từng thẻ để triển khai ngay</span>
                </div>

                <div id="ai-strategies-grid" class="tk-ai-strategy-grid">
                    @foreach(($aiData['khuyen_nghi_chien_luoc'] ?? []) as $idx => $st)
                        @php
                            $isHigh = ($st['uu_tien'] ?? 'cao') === 'cao';
                            $actionType = $st['hanh_dong_loai'] ?? 'broadcast';
                            $notifSample = $st['thong_bao_mau'] ?? null;
                        @endphp
                        <div class="tk-ai-strategy-card">
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $isHigh ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                        {{ $isHigh ? 'Ưu tiên cao' : 'Chiến lược' }}
                                    </span>
                                    @if(!empty($st['tac_dong_du_kien']))
                                        <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200/60">{{ $st['tac_dong_du_kien'] }}</span>
                                    @endif
                                </div>

                                <h4 class="font-bold text-sm text-slate-900 leading-snug">
                                    {{ $st['tieu_de'] ?? 'Chiến lược tăng trưởng doanh thu' }}
                                </h4>

                                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                                    <strong class="text-slate-800">Phân tích:</strong> {{ $st['phan_tich'] ?? '' }}
                                </p>

                                <div class="tk-ai-solution-callout">
                                    <strong class="text-slate-900">Giải pháp AI:</strong> {{ $st['giai_phap'] ?? '' }}
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col gap-2">
                                @if($notifSample)
                                    <button type="button"
                                            data-tieu-de="{{ $notifSample['tieu_de'] ?? '' }}"
                                            data-noi-dung="{{ $notifSample['noi_dung'] ?? '' }}"
                                            data-doi-tuong="{{ $notifSample['doi_tuong'] ?? 'chu_quan' }}"
                                            onclick="openAiBroadcastModalFromBtn(this)"
                                            class="btn-enterprise btn-enterprise-success w-full font-semibold text-xs">
                                        <span class="material-symbols-outlined text-[16px]">campaign</span>
                                        <span>{{ $st['nhan_nut'] ?? 'Phát sóng ưu đãi cho Chủ Quán' }}</span>
                                    </button>
                                @endif

                                @if($actionType === 'featured_venues')
                                    <a href="{{ route('admin.quan-noi-bat.index') }}"
                                       class="btn-enterprise btn-enterprise-secondary w-full font-semibold text-xs">
                                        <span class="material-symbols-outlined text-[16px]">local_fire_department</span>
                                        <span>Mở Quản Lý Quán Nổi Bật &rarr;</span>
                                    </a>
                                @elseif($actionType === 'ai_studio')
                                    <a href="{{ route('admin.khuyen-mai-ai.index') }}"
                                       class="btn-enterprise btn-enterprise-indigo w-full font-semibold text-xs">
                                        <span class="material-symbols-outlined text-[16px]">auto_fix_high</span>
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
            </div>
        </div>
    </div>

    {{-- MỤC 3 (ACCORDION): BIỂU ĐỒ XU HƯỚNG DOANH THU & TĂNG TRƯỞNG --}}
    <div class="tk-accordion-card" id="accordion-section-charts">
        <button type="button" class="tk-accordion-header" onclick="toggleTkAccordion('accordion-section-charts')">
            <div class="flex items-center gap-3 min-w-0">
                <span class="tk-accordion-icon bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <span class="material-symbols-outlined text-[20px]">insights</span>
                </span>
                <div class="text-left min-w-0">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">
                        3. Biểu Đồ Xu Hướng Doanh Thu &amp; Tăng Trưởng
                    </h2>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">
                        Doanh thu {{ number_format($revenueStats['filtered'], 0, ',', '.') }}đ • Tăng trưởng quán &amp; người dùng
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="tk-accordion-badge">2 Biểu đồ</span>
                <span class="material-symbols-outlined tk-accordion-chevron text-[20px] text-slate-400">expand_more</span>
            </div>
        </button>
        <div class="tk-accordion-body">
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
        </div>
    </div>

    {{-- MỤC 4 (ACCORDION): TOP QUÁN, PHÂN BỐ KHU VỰC & DOANH THU THEO GÓI --}}
    <div class="tk-accordion-card" id="accordion-section-top-venues">
        <button type="button" class="tk-accordion-header" onclick="toggleTkAccordion('accordion-section-top-venues')">
            <div class="flex items-center gap-3 min-w-0">
                <span class="tk-accordion-icon bg-amber-50 text-amber-600 border border-amber-200">
                    <span class="material-symbols-outlined text-[20px]">leaderboard</span>
                </span>
                <div class="text-left min-w-0">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">
                        4. Xếp Hạng Top Quán &amp; Phân Bố Khu Vực, Gói Dịch Vụ
                    </h2>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">
                        Top quán xem nhiều nhất • Thị phần tỉnh thành • Doanh thu theo gói
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="tk-accordion-badge">Cơ cấu &amp; Thứ hạng</span>
                <span class="material-symbols-outlined tk-accordion-chevron text-[20px] text-slate-400">expand_more</span>
            </div>
        </button>
        <div class="tk-accordion-body">
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
        </div>
    </div>

    {{-- MỤC 5 (ACCORDION): DANH SÁCH CHI TIẾT GIAO DỊCH TRONG KỲ --}}
    <div class="tk-accordion-card {{ (request()->filled('search') || request()->filled('trang_thai_tt') || request()->filled('page')) ? 'is-open' : '' }}" id="accordion-section-transactions">
        <button type="button" class="tk-accordion-header" onclick="toggleTkAccordion('accordion-section-transactions')">
            <div class="flex items-center gap-3 min-w-0">
                <span class="tk-accordion-icon bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                </span>
                <div class="text-left min-w-0">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">
                        5. Danh Sách Chi Tiết Giao Dịch Trong Kỳ
                    </h2>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">
                        {{ number_format($revenueStats['count_filtered']) }} giao dịch phát sinh từ {{ \Carbon\Carbon::parse($tuNgayStr)->format('d/m/Y') }} đến {{ \Carbon\Carbon::parse($denNgayStr)->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="tk-accordion-badge">{{ number_format($revenueStats['count_filtered']) }} Giao dịch</span>
                <span class="material-symbols-outlined tk-accordion-chevron text-[20px] text-slate-400">expand_more</span>
            </div>
        </button>
        <div class="tk-accordion-body">
            <div class="space-y-4">
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
    // =========================================================================
    // Accordion Toggle Handlers
    // =========================================================================
    window.toggleTkAccordion = function(id) {
        const card = document.getElementById(id);
        if (!card) return;
        const isOpen = card.classList.toggle('is-open');
        if (isOpen && id === 'accordion-section-charts') {
            setTimeout(() => {
                if (typeof renderAdminStatisticsCharts === 'function') {
                    renderAdminStatisticsCharts();
                }
                if (window.adminRevChartInstance) window.adminRevChartInstance.resize();
                if (window.adminGrowthChartInstance) window.adminGrowthChartInstance.resize();
            }, 60);
        }
    };

    window.toggleAllAccordions = function(open) {
        document.querySelectorAll('.tk-accordion-card').forEach(card => {
            if (open) {
                card.classList.add('is-open');
            } else {
                card.classList.remove('is-open');
            }
        });
        if (open) {
            setTimeout(() => {
                if (typeof renderAdminStatisticsCharts === 'function') {
                    renderAdminStatisticsCharts();
                }
                if (window.adminRevChartInstance) window.adminRevChartInstance.resize();
                if (window.adminGrowthChartInstance) window.adminGrowthChartInstance.resize();
            }, 60);
        }
    };

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
            const badgeCls = isHigh
                ? 'bg-amber-50 text-amber-700 border border-amber-200'
                : 'bg-slate-100 text-slate-700 border border-slate-200';
            const badgeTxt = isHigh ? 'Ưu tiên cao' : 'Chiến lược';
            const impactHtml = st.tac_dong_du_kien
                ? `<span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200/60">${window.escapeHtmlAi(st.tac_dong_du_kien)}</span>`
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
                            class="btn-enterprise btn-enterprise-success w-full font-semibold text-xs">
                        <span class="material-symbols-outlined text-[16px]">campaign</span>
                        <span>${window.escapeHtmlAi(st.nhan_nut || 'Phát sóng ưu đãi cho Chủ Quán')}</span>
                    </button>
                `;
            }

            if (st.hanh_dong_loai === 'featured_venues') {
                buttonsHtml += `
                    <a href="${featuredUrl}" class="btn-enterprise btn-enterprise-secondary w-full font-semibold text-xs">
                        <span class="material-symbols-outlined text-[16px]">local_fire_department</span>
                        <span>Mở Quản Lý Quán Nổi Bật &rarr;</span>
                    </a>
                `;
            } else if (st.hanh_dong_loai === 'ai_studio') {
                buttonsHtml += `
                    <a href="${aiStudioUrl}" class="btn-enterprise btn-enterprise-indigo w-full font-semibold text-xs">
                        <span class="material-symbols-outlined text-[16px]">auto_fix_high</span>
                        <span>${window.escapeHtmlAi(st.nhan_nut || 'Mở Khuyến Mãi AI Studio')} &rarr;</span>
                    </a>
                `;
            }

            return `
                <div class="tk-ai-strategy-card">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase ${badgeCls}">${badgeTxt}</span>
                            ${impactHtml}
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 leading-snug">${window.escapeHtmlAi(st.tieu_de || '')}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed font-normal"><strong class="text-slate-800">Phân tích:</strong> ${window.escapeHtmlAi(st.phan_tich || '')}</p>
                        <div class="tk-ai-solution-callout">
                            <strong class="text-slate-900">Giải pháp AI:</strong> ${window.escapeHtmlAi(st.giai_phap || '')}
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col gap-2">
                        ${buttonsHtml}
                    </div>
                </div>
            `;
        }).join('');
    };

    window.addChatMessageToStream = function(sender, text, providerLabel = '') {
        const stream = document.getElementById('ai-chat-messages');
        if (!stream) return;

        const timeStr = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        const isUser = sender === 'user';

        const msgHtml = isUser ? `
            <div class="flex justify-end items-end gap-2.5">
                <div class="space-y-1 max-w-[85%] sm:max-w-[75%]">
                    <div class="p-3.5 rounded-2xl rounded-tr-xs bg-indigo-600 text-white text-xs sm:text-sm font-medium shadow-2xs leading-relaxed">
                        ${window.escapeHtmlAi(text)}
                    </div>
                    <p class="text-[10px] text-right text-slate-400 pr-1">${timeStr}</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-2xs">
                    <span class="material-symbols-outlined text-[17px]">person</span>
                </div>
            </div>
        ` : `
            <div class="flex justify-start items-start gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">smart_toy</span>
                </div>
                <div class="space-y-1 max-w-[90%] sm:max-w-[80%]">
                    <div class="p-3.5 rounded-2xl rounded-tl-xs bg-white border border-slate-200 text-slate-800 text-xs sm:text-sm shadow-2xs leading-relaxed whitespace-pre-line">
                        ${window.escapeHtmlAi(text)}
                    </div>
                    <p class="text-[10px] text-slate-400 pl-1">${timeStr}</p>
                </div>
            </div>
        `;

        stream.insertAdjacentHTML('beforeend', msgHtml);
        stream.scrollTop = stream.scrollHeight;
    };

    window.showAiTypingIndicator = function() {
        const stream = document.getElementById('ai-chat-messages');
        if (!stream || document.getElementById('ai-chat-typing-indicator')) return;

        const indicatorHtml = `
            <div id="ai-chat-typing-indicator" class="flex justify-start items-start gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                    <span class="material-symbols-outlined text-[18px] animate-spin">sync</span>
                </div>
                <div class="p-3 rounded-2xl rounded-tl-xs bg-white border border-slate-200 text-slate-600 text-xs shadow-2xs flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-indigo-600 animate-ping"></span>
                    <span class="font-medium text-slate-700">AI đang suy nghĩ...</span>
                </div>
            </div>
        `;
        stream.insertAdjacentHTML('beforeend', indicatorHtml);
        stream.scrollTop = stream.scrollHeight;
    };

    window.hideAiTypingIndicator = function() {
        const el = document.getElementById('ai-chat-typing-indicator');
        if (el) el.remove();
    };

    window.clearAiChatHistory = function() {
        const stream = document.getElementById('ai-chat-messages');
        if (!stream) return;
        const timeStr = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        stream.innerHTML = `
            <div class="flex justify-start items-start gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">smart_toy</span>
                </div>
                <div class="space-y-1 max-w-[90%] sm:max-w-[80%]">
                    <div class="p-3.5 rounded-2xl rounded-tl-xs bg-white border border-slate-200 text-slate-800 text-xs sm:text-sm shadow-2xs leading-relaxed">
                        Đoạn chat đã được làm mới. Tôi sẵn sàng hỗ trợ bạn theo dõi doanh thu và tối ưu hóa sàn!
                    </div>
                    <p class="text-[10px] text-slate-400 pl-1">${timeStr}</p>
                </div>
            </div>
        `;
    };

    window.sendQuickAiMessage = function(questionText, focusMode) {
        const focusEl = document.getElementById('ai-focus-select');
        const promptEl = document.getElementById('ai-custom-prompt');
        if (focusEl && focusMode) focusEl.value = focusMode;
        if (promptEl) promptEl.value = questionText || '';
        window.submitAiChatMessage();
    };

    window.submitAiChatMessage = async function() {
        const promptEl = document.getElementById('ai-custom-prompt');
        const btn = document.getElementById('btn-refresh-ai-revenue');
        const icon = document.getElementById('icon-refresh-ai');
        const statusText = document.getElementById('ai-chat-status-text');
        const focusVal = document.getElementById('ai-focus-select')?.value || 'tong_hop';

        const messageText = (promptEl?.value || '').trim();
        if (!messageText) {
            promptEl?.focus();
            return;
        }

        // 1. Thêm tin nhắn của User vào khung chat (bên phải)
        window.addChatMessageToStream('user', messageText);
        promptEl.value = '';

        // 2. Chuyển trạng thái nút gửi và hiện typing indicator của AI (bên trái)
        if (btn) btn.disabled = true;
        if (icon) {
            icon.textContent = 'sync';
            icon.classList.add('animate-spin');
        }
        if (statusText) statusText.textContent = 'AI đang suy nghĩ...';
        window.showAiTypingIndicator();

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
                    custom_prompt: messageText
                })
            });

            const elapsed = Date.now() - startTime;
            if (elapsed < 800) {
                await new Promise(r => setTimeout(r, 800 - elapsed));
            }

            const json = await response.json();
            window.hideAiTypingIndicator();

            if (response.ok && json.success && json.data) {
                const d = json.data;
                const m = d.metrics || {};
                const aiReply = d.cau_tra_loi_cau_hoi || d.nhan_dinh_cfo || 'Đã phân tích xong câu hỏi của bạn.';

                // 3. Thêm tin nhắn phản hồi của AI vào khung chat
                window.addChatMessageToStream('ai', aiReply, d.provider_label || 'Google Gemini AI');

                // 4. Đồng bộ các thẻ phân tích tài chính bên dưới
                if (d.nhan_dinh_cfo) {
                    const cfoSum = document.getElementById('ai-cfo-summary');
                    if (cfoSum) cfoSum.textContent = d.nhan_dinh_cfo;
                }
                if (d.provider_label) {
                    const provEl = document.getElementById('ai-provider-text');
                    if (provEl) provEl.textContent = d.provider_label;
                }
                if (d.analyzed_at) {
                    const timeEl = document.getElementById('ai-analyzed-time');
                    if (timeEl) timeEl.textContent = 'Cập nhật: ' + d.analyzed_at;
                }
                if (m.diem_suc_khoe && m.nhan_suc_khoe) {
                    const healthEl = document.getElementById('ai-health-label');
                    if (healthEl) healthEl.textContent = `${m.diem_suc_khoe}/100 • ${m.nhan_suc_khoe}`;
                }
                if (m.du_bao_30_ngay) {
                    const minF = Number(m.du_bao_30_ngay.min || 0).toLocaleString('vi-VN') + 'đ';
                    const maxF = Number(m.du_bao_30_ngay.max || 0).toLocaleString('vi-VN') + 'đ';
                    const fRange = document.getElementById('ai-forecast-range');
                    if (fRange) fRange.textContent = `${minF} – ${maxF}`;
                }
                if (d.nhan_xet_du_bao) {
                    const fNote = document.getElementById('ai-forecast-note');
                    if (fNote) fNote.textContent = d.nhan_xet_du_bao;
                }

                if (Array.isArray(d.diem_sang)) {
                    const hlList = document.getElementById('ai-highlights-list');
                    if (hlList) {
                        hlList.innerHTML = d.diem_sang
                            .map(item => `<li class="flex items-start gap-2"><span class="text-emerald-600 font-bold">•</span><span>${window.escapeHtmlAi(item)}</span></li>`)
                            .join('');
                    }
                }

                if (Array.isArray(d.canh_bao_rui_ro)) {
                    const wnList = document.getElementById('ai-warnings-list');
                    if (wnList) {
                        wnList.innerHTML = d.canh_bao_rui_ro
                            .map(item => `<li class="flex items-start gap-2"><span class="text-amber-600 font-bold">•</span><span>${window.escapeHtmlAi(item)}</span></li>`)
                            .join('');
                    }
                }

                if (Array.isArray(d.khuyen_nghi_chien_luoc)) {
                    window.renderAiStrategiesGrid(d.khuyen_nghi_chien_luoc);
                }

                if (statusText) statusText.textContent = 'Đã phản hồi lúc ' + (d.analyzed_at || 'vừa xong');
            } else {
                window.addChatMessageToStream('ai', 'Rất tiếc, hệ thống đang bận. Vui lòng thử lại sau ít giây.', 'Lỗi');
                if (statusText) statusText.textContent = 'Không thể phản hồi';
            }
        } catch (err) {
            console.error('AI Analyze error:', err);
            window.hideAiTypingIndicator();
            window.addChatMessageToStream('ai', 'Lỗi kết nối mạng đến máy chủ. Vui lòng kiểm tra lại đường truyền.', 'Mạng');
            if (statusText) statusText.textContent = 'Lỗi kết nối';
        } finally {
            if (btn) btn.disabled = false;
            if (icon) {
                icon.classList.remove('animate-spin');
                icon.textContent = 'arrow_upward';
            }
            if (promptEl) promptEl.focus();
        }
    };

    window.refreshAdminAiRevenueInsights = function() {
        window.submitAiChatMessage();
    };

    window.applyQuickAiQuestion = function(focusMode, questionText) {
        window.sendQuickAiMessage(questionText, focusMode);
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
