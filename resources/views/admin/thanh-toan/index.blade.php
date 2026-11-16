@extends('admin.layout')

@section('title', 'Quản Lý Nạp Tiền & Doanh Thu Ví - Quán Mới Quản Trị')
@section('page-title', 'Quản Lý Nạp Tiền & Doanh Thu')

@section('content')
<div class="space-y-5">

    {{-- Header Actions Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-slate-200/80">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-[24px] text-emerald-600">account_balance_wallet</span>
                <span>Quản Lý Nạp Tiền & Doanh Thu Ví</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Đối soát lịch sử nạp tiền SePay / VietQR, duyệt đơn chuyển khoản thủ công và quản lý số dư ví thành viên.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="openAdjustBalanceModal()" class="btn-enterprise btn-enterprise-success">
                <span class="material-symbols-outlined">add_card</span>
                <span>Điều chỉnh / Nạp bù số dư</span>
            </button>

            <a href="{{ route('admin.thanh-toan.export', request()->query()) }}" class="btn-enterprise btn-enterprise-secondary" title="Xuất danh sách giao dịch ra file CSV/Excel">
                <span class="material-symbols-outlined">download</span>
                <span>Xuất CSV / Excel</span>
            </a>

            <a href="{{ route('admin.thong-ke.index') }}" class="btn-enterprise btn-enterprise-secondary" title="Xem biểu đồ thống kê tổng hợp">
                <span class="material-symbols-outlined">query_stats</span>
                <span>Biểu đồ thống kê</span>
            </a>
        </div>
    </div>

    {{-- 4 KPI Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- KPI 1: Tổng doanh thu thực nhận --}}
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200/90 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng Doanh Thu Nạp Ví</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/60">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-extrabold text-emerald-600 tracking-tight">
                    {{ number_format($stats['tong_doanh_thu'], 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">đ</span>
                </h3>
            </div>
            <div class="mt-2.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Tháng này: <strong class="text-slate-800">{{ number_format($stats['doanh_thu_thang_nay'], 0, ',', '.') }}đ</strong></span>
                <span>Hôm nay: <strong class="text-emerald-600">+{{ number_format($stats['doanh_thu_hom_nay'], 0, ',', '.') }}đ</strong></span>
            </div>
        </div>

        {{-- KPI 2: Giao dịch thành công --}}
        <a href="{{ route('admin.thanh-toan.index', ['trang_thai' => 'thanh_cong']) }}" class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200/90 shadow-xs hover:border-emerald-400 transition-all block group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider group-hover:text-emerald-700">Đơn Nạp Thành Công</span>
                <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200/60">
                    <span class="material-symbols-outlined text-[20px]">verified</span>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ number_format($stats['tong_don_thanh_cong']) }}</h3>
                <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">Đã cộng ví</span>
            </div>
            <div class="mt-2.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Tự động qua SePay & Admin</span>
                <span class="font-semibold text-blue-600 group-hover:underline">Lọc xem →</span>
            </div>
        </a>

        {{-- KPI 3: Đơn chờ thanh toán / xử lý --}}
        <a href="{{ route('admin.thanh-toan.index', ['trang_thai' => 'cho_thanh_toan']) }}" class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200/90 shadow-xs hover:border-amber-400 transition-all block group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider group-hover:text-amber-700">Đang Chờ Thanh Toán</span>
                <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/60">
                    <span class="material-symbols-outlined text-[20px]">hourglass_top</span>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <h3 class="text-2xl font-extrabold {{ $stats['tong_don_cho'] > 0 ? 'text-amber-600' : 'text-slate-900' }} tracking-tight">
                    {{ number_format($stats['tong_don_cho']) }}
                </h3>
                @if($stats['tong_don_cho'] > 0)
                    <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">Cần đối soát</span>
                @else
                    <span class="text-[11px] text-slate-400">Không có đơn treo</span>
                @endif
            </div>
            <div class="mt-2.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Có thể đồng bộ hoặc duyệt tay</span>
                <span class="font-semibold text-amber-600 group-hover:underline">Kiểm tra →</span>
            </div>
        </a>

        {{-- KPI 4: Tổng số dư ví người dùng --}}
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-slate-200/90 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng Số Dư Ví Hiện Hành</span>
                <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-200/60">
                    <span class="material-symbols-outlined text-[20px]">savings</span>
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-extrabold text-indigo-600 tracking-tight">
                    {{ number_format($stats['tong_so_du_vi'], 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">đ</span>
                </h3>
            </div>
            <div class="mt-2.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Đơn hủy / lỗi: <strong class="text-rose-600">{{ number_format($stats['tong_don_huy_loi']) }}</strong></span>
                <a href="{{ route('admin.nguoi-dung.index') }}" class="font-semibold text-indigo-600 hover:underline">Xem thành viên →</a>
            </div>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.thanh-toan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <div class="lg:col-span-4">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Tìm kiếm giao dịch</label>
                <div class="admin-search-wrapper w-full">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Mã NAP..., mã SePay, tên, email, SĐT khách hàng...">
                </div>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Trạng thái</label>
                <select name="trang_thai" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-amber-500">
                    <option value="all" {{ request('trang_thai', 'all') === 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                    <option value="thanh_cong" {{ request('trang_thai') === 'thanh_cong' ? 'selected' : '' }}>Thành công</option>
                    <option value="cho_thanh_toan" {{ request('trang_thai') === 'cho_thanh_toan' ? 'selected' : '' }}>Chờ thanh toán</option>
                    <option value="da_huy" {{ request('trang_thai') === 'da_huy' ? 'selected' : '' }}>Đã hủy</option>
                    <option value="that_bai" {{ request('trang_thai') === 'that_bai' ? 'selected' : '' }}>Thất bại</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Phương thức</label>
                <select name="phuong_thuc" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-amber-500">
                    <option value="all" {{ request('phuong_thuc', 'all') === 'all' ? 'selected' : '' }}>Tất cả phương thức</option>
                    <option value="vietqr" {{ request('phuong_thuc') === 'vietqr' ? 'selected' : '' }}>VietQR (SePay)</option>
                    <option value="sepay" {{ request('phuong_thuc') === 'sepay' ? 'selected' : '' }}>Cổng SePay PG</option>
                    <option value="admin_adjust" {{ request('phuong_thuc') === 'admin_adjust' ? 'selected' : '' }}>Admin điều chỉnh</option>
                </select>
            </div>

            <div class="lg:col-span-2 grid grid-cols-2 gap-1.5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Từ ngày</label>
                    <input type="date" name="tu_ngay" value="{{ request('tu_ngay') }}" class="w-full px-2 py-1.5 bg-white border border-slate-300 rounded-xl text-[11px] font-medium text-slate-800">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Đến ngày</label>
                    <input type="date" name="den_ngay" value="{{ request('den_ngay') }}" class="w-full px-2 py-1.5 bg-white border border-slate-300 rounded-xl text-[11px] font-medium text-slate-800">
                </div>
            </div>

            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="btn-enterprise bg-slate-900 hover:bg-slate-800 text-white flex-1">
                    <span class="material-symbols-outlined text-white">filter_alt</span>
                    <span>Lọc</span>
                </button>
                @if(request()->hasAny(['search', 'trang_thai', 'phuong_thuc', 'tu_ngay', 'den_ngay']))
                    <a href="{{ route('admin.thanh-toan.index') }}" class="btn-enterprise btn-enterprise-secondary" title="Xóa bộ lọc">
                        <span class="material-symbols-outlined">restart_alt</span>
                    </a>
                @endif
            </div>
        </form>

        @if(request()->hasAny(['search', 'trang_thai', 'phuong_thuc', 'tu_ngay', 'den_ngay']))
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-600">
                <div>
                    Kết quả lọc: <strong>{{ number_format($stats['filtered_count']) }}</strong> giao dịch
                </div>
                <div>
                    Tổng tiền thành công trong bộ lọc: <strong class="text-emerald-600">{{ number_format($stats['filtered_success_sum'], 0, ',', '.') }} VNĐ</strong>
                </div>
            </div>
        @endif
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Mã Giao Dịch</th>
                        <th class="py-3 px-4">Khách Hàng & Ví</th>
                        <th class="py-3 px-4">Số Tiền</th>
                        <th class="py-3 px-4">Phương Thức</th>
                        <th class="py-3 px-4">Trạng Thái</th>
                        <th class="py-3 px-4">Thời Gian</th>
                        <th class="py-3 px-4 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($giaoDichs as $item)
                        @php
                            $isPending = in_array($item->trang_thai, ['cho_thanh_toan', 'cho_xu_ly'], true);
                            $isSuccess = $item->trang_thai === 'thanh_cong';
                            $isDebit = str_starts_with((string) $item->ghi_chu, '[Trừ số dư]');
                            $detailPayload = [
                                'id' => $item->id,
                                'ma_giao_dich' => $item->ma_giao_dich,
                                'so_tien_formatted' => number_format((float) $item->so_tien, 0, ',', '.') . ' VNĐ',
                                'phuong_thuc' => strtoupper((string) $item->phuong_thuc),
                                'trang_thai' => $item->trang_thai,
                                'sepay_order_id' => $item->sepay_order_id ?? '—',
                                'sepay_transaction_id' => $item->sepay_transaction_id ?? '—',
                                'user_name' => $item->user->ho_ten ?? 'Khách vãng lai',
                                'user_email' => $item->user->email ?? '—',
                                'ghi_chu' => $item->ghi_chu ?? '—',
                                'created_at' => optional($item->created_at)->format('d/m/Y H:i:s'),
                                'updated_at' => optional($item->updated_at)->format('d/m/Y H:i:s'),
                                'du_lieu_ipn' => $item->du_lieu_ipn,
                            ];
                            $formattedAmountLabel = number_format((float) $item->so_tien, 0, ',', '.') . ' VNĐ';
                            $userNameLabel = $item->user->ho_ten ?? 'Khách hàng';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            {{-- Mã Giao Dịch --}}
                            <td class="py-3.5 px-4 align-top">
                                <div class="font-bold text-slate-900 font-mono text-xs flex items-center gap-1.5">
                                    <span>#{{ $item->ma_giao_dich }}</span>
                                </div>
                                @if($item->sepay_transaction_id)
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Mã NH/SePay: <span class="font-mono font-semibold text-slate-700">{{ $item->sepay_transaction_id }}</span>
                                    </div>
                                @endif
                                @if($item->ghi_chu)
                                    <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 max-w-[230px]" title="{{ $item->ghi_chu }}">
                                        {{ $item->ghi_chu }}
                                    </div>
                                @endif
                            </td>

                            {{-- Khách Hàng --}}
                            <td class="py-3.5 px-4 align-top">
                                @if($item->user)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-xs shrink-0 border border-indigo-200/60">
                                            {{ strtoupper(substr($item->user->ho_ten ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.nguoi-dung.edit', $item->user->id) }}" class="font-bold text-slate-900 hover:text-indigo-600 truncate block">
                                                {{ $item->user->ho_ten }}
                                            </a>
                                            <div class="text-[11px] text-slate-500 truncate">{{ $item->user->email }}</div>
                                            <div class="text-[10.5px] font-semibold text-emerald-700 mt-0.5">
                                                Ví hiện tại: {{ number_format((float) $item->user->so_du, 0, ',', '.') }}đ
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Tài khoản đã xóa</span>
                                @endif
                            </td>

                            {{-- Số Tiền --}}
                            <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                <div class="text-sm font-extrabold {{ $isDebit ? 'text-rose-600' : ($isSuccess ? 'text-emerald-600' : 'text-slate-900') }}">
                                    {{ $isDebit ? '-' : '+' }}{{ number_format((float) $item->so_tien, 0, ',', '.') }} đ
                                </div>
                                <div class="text-[10.5px] text-slate-400 mt-0.5">
                                    {{ $item->ten_goi_dich_vu ?? 'Nạp tiền ví Quán Mới' }}
                                </div>
                            </td>

                            {{-- Phương Thức --}}
                            <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                @if($item->phuong_thuc === 'vietqr')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="material-symbols-outlined text-[13px]">qr_code_2</span>
                                        VietQR SePay
                                    </span>
                                @elseif($item->phuong_thuc === 'admin_adjust')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <span class="material-symbols-outlined text-[13px]">admin_panel_settings</span>
                                        Admin điều chỉnh
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        <span class="material-symbols-outlined text-[13px]">credit_card</span>
                                        {{ strtoupper($item->phuong_thuc ?: 'SEPAY') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Trạng Thái --}}
                            <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                @if($isSuccess)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Thành công
                                    </span>
                                @elseif($isPending)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Chờ thanh toán
                                    </span>
                                @elseif($item->trang_thai === 'da_huy')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Đã hủy
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Thất bại
                                    </span>
                                @endif
                            </td>

                            {{-- Thời Gian --}}
                            <td class="py-3.5 px-4 align-top whitespace-nowrap text-slate-600">
                                <div class="font-semibold text-slate-800">{{ optional($item->created_at)->format('d/m/Y H:i') }}</div>
                                <div class="text-[10.5px] text-slate-400">{{ optional($item->created_at)->diffForHumans() }}</div>
                            </td>

                            {{-- Thao Tác --}}
                            <td class="py-3.5 px-4 align-top text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    {{-- Xem chi tiết --}}
                                    <button type="button"
                                        data-detail="{{ json_encode($detailPayload) }}"
                                        onclick="openPaymentDetailModal(JSON.parse(this.dataset.detail))"
                                        class="btn-enterprise btn-enterprise-secondary !px-2.5 !py-1"
                                        title="Xem chi tiết & dữ liệu Webhook SePay">
                                        <span class="material-symbols-outlined">visibility</span>
                                        <span>Chi tiết</span>
                                    </button>

                                    @if($isPending)
                                        {{-- Đồng bộ SePay --}}
                                        <form action="{{ route('admin.thanh-toan.sync', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn-enterprise btn-enterprise-secondary !px-2.5 !py-1" title="Kiểm tra & đồng bộ trạng thái từ cổng SePay">
                                                <span class="material-symbols-outlined">sync</span>
                                            </button>
                                        </form>

                                        {{-- Duyệt cộng tiền thủ công --}}
                                        <button type="button"
                                            data-id="{{ $item->id }}"
                                            data-code="{{ $item->ma_giao_dich }}"
                                            data-amount="{{ $formattedAmountLabel }}"
                                            data-user="{{ $userNameLabel }}"
                                            onclick="openApproveModal(this.dataset.id, this.dataset.code, this.dataset.amount, this.dataset.user)"
                                            class="btn-enterprise btn-enterprise-success !px-2.5 !py-1"
                                            title="Xác nhận đã nhận tiền và cộng vào ví">
                                            <span class="material-symbols-outlined">check_circle</span>
                                            <span>Duyệt</span>
                                        </button>

                                        {{-- Hủy đơn --}}
                                        <form action="{{ route('admin.thanh-toan.cancel', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn hủy đơn nạp tiền #{{ $item->ma_giao_dich }}?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn-enterprise btn-enterprise-soft-danger !px-2.5 !py-1" title="Hủy giao dịch chờ này">
                                                <span class="material-symbols-outlined">cancel</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-[42px] mb-2 text-slate-300">receipt_long</span>
                                    <p class="text-sm font-bold text-slate-600">Chưa có giao dịch nạp tiền nào khớp với bộ lọc</p>
                                    <p class="text-xs text-slate-400 mt-1">Các giao dịch nạp ví qua SePay / VietQR sẽ tự động hiển thị tại đây.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($giaoDichs->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $giaoDichs->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal 1: Xem chi tiết giao dịch & Log SePay --}}
<div id="payment-detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
        <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400">receipt_long</span>
                <h3 class="text-sm font-bold" id="detail-modal-title">Chi Tiết Giao Dịch</h3>
            </div>
            <button type="button" onclick="closePaymentDetailModal()" class="text-slate-400 hover:text-white">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-5 space-y-3 text-xs max-h-[75vh] overflow-y-auto">
            <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-200/80">
                <div>
                    <span class="text-slate-400 block text-[11px]">Mã giao dịch</span>
                    <span id="detail-code" class="font-mono font-bold text-slate-900 text-sm"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Số tiền</span>
                    <span id="detail-amount" class="font-extrabold text-emerald-600 text-sm"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Khách hàng</span>
                    <span id="detail-user" class="font-bold text-slate-800"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Email</span>
                    <span id="detail-email" class="text-slate-700"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Phương thức</span>
                    <span id="detail-method" class="font-semibold text-slate-800"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Mã GD SePay / Ngân hàng</span>
                    <span id="detail-sepay-tx" class="font-mono font-semibold text-slate-800"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Thời gian tạo</span>
                    <span id="detail-created" class="text-slate-700"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Cập nhật cuối</span>
                    <span id="detail-updated" class="text-slate-700"></span>
                </div>
            </div>

            <div>
                <span class="text-[11px] font-bold text-slate-600 uppercase">Ghi chú hệ thống:</span>
                <p id="detail-note" class="mt-1 p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700"></p>
            </div>

            <div>
                <span class="text-[11px] font-bold text-slate-600 uppercase">Dữ liệu gốc từ SePay Webhook / IPN (JSON):</span>
                <pre id="detail-ipn-json" class="mt-1 p-3 rounded-xl bg-slate-900 text-emerald-400 font-mono text-[11px] overflow-x-auto max-h-52"></pre>
            </div>
        </div>
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex justify-end">
            <button type="button" onclick="closePaymentDetailModal()" class="btn-enterprise btn-enterprise-secondary">Đóng</button>
        </div>
    </div>
</div>

{{-- Modal 2: Duyệt thủ công đơn nạp tiền --}}
<div id="approve-payment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="px-5 py-4 bg-emerald-700 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined">verified</span>
                <h3 class="text-sm font-bold">Xác Nhận Duyệt Nạp Tiền</h3>
            </div>
            <button type="button" onclick="closeApproveModal()" class="text-emerald-100 hover:text-white">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form id="approve-payment-form" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="p-5 space-y-3.5 text-xs">
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900">
                    Bạn đang duyệt đơn <strong id="approve-modal-code"></strong> của khách hàng <strong id="approve-modal-user"></strong> với số tiền <strong id="approve-modal-amount" class="text-emerald-700"></strong>.
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Mã tham chiếu ngân hàng / SePay (Tùy chọn)</label>
                    <input type="text" name="ma_tham_chieu" placeholder="VD: FT263019283..." class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:outline-none focus:border-emerald-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ghi chú đối soát</label>
                    <input type="text" name="ghi_chu_admin" placeholder="VD: Đã kiểm tra sao kê MBBank nhận đủ tiền" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:outline-none focus:border-emerald-600">
                </div>
            </div>
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeApproveModal()" class="btn-enterprise btn-enterprise-secondary">Hủy</button>
                <button type="submit" class="btn-enterprise btn-enterprise-success">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span>Xác nhận & Cộng tiền ví</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal 3: Điều chỉnh / Nạp bù số dư ví cho thành viên --}}
<div id="adjust-balance-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400">account_balance_wallet</span>
                <h3 class="text-sm font-bold">Điều Chỉnh / Nạp Bù Số Dư Ví</h3>
            </div>
            <button type="button" onclick="closeAdjustBalanceModal()" class="text-slate-400 hover:text-white">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.thanh-toan.adjust-balance') }}">
            @csrf
            <div class="p-5 space-y-3.5 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email, SĐT hoặc ID người dùng <span class="text-rose-500">*</span></label>
                    <input type="text" name="email_hoac_id" required placeholder="Nhập chính xác email hoặc số điện thoại thành viên..." class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Loại thao tác <span class="text-rose-500">*</span></label>
                        <select name="loai_dieu_chinh" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800">
                            <option value="cong_tien">+ Cộng tiền vào ví</option>
                            <option value="tru_tien">- Trừ tiền khỏi ví</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Số tiền (VNĐ) <span class="text-rose-500">*</span></label>
                        <input type="number" name="so_tien" min="1000" step="1000" required placeholder="VD: 50000" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-900">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lý do điều chỉnh (Lưu Audit Log & gửi thông báo) <span class="text-rose-500">*</span></label>
                    <input type="text" name="ghi_chu" required placeholder="VD: Nạp bù đơn chuyển khoản sai nội dung ngày 30/10" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:outline-none focus:border-amber-500">
                </div>
            </div>
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAdjustBalanceModal()" class="btn-enterprise btn-enterprise-secondary">Đóng</button>
                <button type="submit" class="btn-enterprise btn-enterprise-success">
                    <span class="material-symbols-outlined">save</span>
                    <span>Thực hiện điều chỉnh</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaymentDetailModal(data) {
        document.getElementById('detail-code').textContent = '#' + (data.ma_giao_dich || '');
        document.getElementById('detail-amount').textContent = data.so_tien_formatted || '';
        document.getElementById('detail-user').textContent = data.user_name || '';
        document.getElementById('detail-email').textContent = data.user_email || '';
        document.getElementById('detail-method').textContent = data.phuong_thuc || '';
        document.getElementById('detail-sepay-tx').textContent = data.sepay_transaction_id || '—';
        document.getElementById('detail-created').textContent = data.created_at || '—';
        document.getElementById('detail-updated').textContent = data.updated_at || '—';
        document.getElementById('detail-note').textContent = data.ghi_chu || '—';

        const jsonEl = document.getElementById('detail-ipn-json');
        if (data.du_lieu_ipn) {
            jsonEl.textContent = JSON.stringify(data.du_lieu_ipn, null, 2);
        } else {
            jsonEl.textContent = '// Chưa có dữ liệu Webhook / IPN trả về từ cổng SePay';
        }

        const modal = document.getElementById('payment-detail-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closePaymentDetailModal() {
        const modal = document.getElementById('payment-detail-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openApproveModal(id, code, amount, userName) {
        const form = document.getElementById('approve-payment-form');
        form.action = '/admin/thanh-toan/' + id + '/approve';
        document.getElementById('approve-modal-code').textContent = '#' + code;
        document.getElementById('approve-modal-amount').textContent = amount;
        document.getElementById('approve-modal-user').textContent = userName;

        const modal = document.getElementById('approve-payment-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeApproveModal() {
        const modal = document.getElementById('approve-payment-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openAdjustBalanceModal() {
        const modal = document.getElementById('adjust-balance-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAdjustBalanceModal() {
        const modal = document.getElementById('adjust-balance-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
