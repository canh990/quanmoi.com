@extends('layouts.app')
@section('title', 'Ví tiền & Nạp tiền SePay — Quán Mới')

@push('seo')
    <meta name="description" content="Quản lý ví tiền, nạp tiền an toàn qua Cổng SePay Gateway tại Quán Mới.">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <link rel="canonical" href="{{ route('tai-khoan.vi-tien') }}">
@endpush

@section('content')
<main class="max-w-[1200px] mx-auto px-container-margin py-stack-lg min-h-[819px] pt-6">
    {{-- Breadcrumb Navigation Chuẩn SEO --}}
    <nav aria-label="Breadcrumb" class="mb-4">
        <ol class="flex items-center gap-2 text-xs font-medium text-text-muted">
            <li>
                <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">home</span>
                    <span>Trang chủ</span>
                </a>
            </li>
            <li><span class="text-outline-variant">/</span></li>
            <li>
                <a href="{{ route('tai-khoan.index') }}" class="hover:text-primary transition-colors">Tài khoản</a>
            </li>
            <li><span class="text-outline-variant">/</span></li>
            <li class="text-on-surface font-semibold" aria-current="page">Ví & Nạp tiền</li>
        </ol>
    </nav>

    {{-- Session Flash Notifications --}}
    @if(session('success'))
    <div id="toast-success" class="mb-6 flex items-start sm:items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4 shadow-sm" role="alert">
        <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0 text-emerald-600">
            <span class="material-symbols-outlined text-[22px]">check_circle</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-emerald-900 text-sm">Thao tác thành công!</p>
            <p class="text-emerald-800 text-xs mt-0.5">{{ session('success') }}</p>
        </div>
        <button type="button" onclick="document.getElementById('toast-success').remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div id="toast-error" class="mb-6 flex items-start sm:items-center gap-3 bg-rose-50 border border-rose-200 rounded-2xl px-5 py-4 shadow-sm" role="alert">
        <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0 text-rose-600">
            <span class="material-symbols-outlined text-[22px]">error</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-rose-900 text-sm">Lỗi thanh toán</p>
            <p class="text-rose-800 text-xs mt-0.5">{{ session('error') }}</p>
        </div>
        <button type="button" onclick="document.getElementById('toast-error').remove()" class="text-rose-500 hover:text-rose-700 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>
    @endif

    @if(session('info'))
    <div id="toast-info" class="mb-6 flex items-start sm:items-center gap-3 bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 shadow-sm" role="alert">
        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0 text-blue-600">
            <span class="material-symbols-outlined text-[22px]">info</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-blue-900 text-sm">Thông báo</p>
            <p class="text-blue-800 text-xs mt-0.5">{{ session('info') }}</p>
        </div>
        <button type="button" onclick="document.getElementById('toast-info').remove()" class="text-blue-500 hover:text-blue-700 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-stack-lg">
        <!-- Sidebar Navigation -->
        @include('nguoi-dung.partials.sidebar')

        <!-- Main Content Area -->
        <div class="md:col-span-9 space-y-6">
            
            {{-- ============================================================== --}}
            {{-- 1. PAGE HEADER & SỐ DƯ TÀI KHOẢN (THIẾT KẾ CLEAN, TRANG NHÃ) --}}
            {{-- ============================================================== --}}
            <header class="bg-surface-card p-6 md:p-8 rounded-2xl border border-surface-container shadow-[0px_4px_20px_rgba(0,0,0,0.03)]">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-[11px] font-bold tracking-wide uppercase">
                                Ví Quán Mới
                            </span>
                            <span class="text-text-muted text-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-emerald-600">verified</span>
                                <span>Cổng SePay bảo mật</span>
                            </span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface tracking-tight flex items-center gap-2.5 pt-1">
                            <span>Quản lý Ví tiền & Nạp tiền</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-text-muted">
                            Sử dụng số dư ví để thanh toán nhanh các dịch vụ, gói ưu đãi và đặt bàn tại Quán Mới.
                        </p>
                    </div>

                    {{-- Khối Số Dư Nổi Bật --}}
                    <div class="bg-surface-container-low/70 border border-surface-container p-4 sm:p-5 rounded-2xl flex items-center gap-4 shrink-0">
                        <div class="w-12 h-12 rounded-xl bg-primary text-white flex items-center justify-center shadow-md shadow-primary/20 shrink-0">
                            <span class="material-symbols-outlined text-[26px]">account_balance_wallet</span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider block">Số dư khả dụng</span>
                            <div class="flex items-baseline gap-1.5 mt-0.5">
                                <span class="text-2xl sm:text-3xl font-extrabold text-on-surface tracking-tight font-mono" id="user-so-du-display">
                                    {{ number_format($user->so_du ?? 0, 0, ',', '.') }}
                                </span>
                                <span class="text-xs sm:text-sm font-bold text-primary">VNĐ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- ============================================================== --}}
            {{-- 2. KHUNG NẠP TIỀN QUA CỔNG SEPAY GATEWAY --}}
            {{-- ============================================================== --}}
            <section id="khung-nap-tien" class="bg-surface-card rounded-2xl p-6 md:p-8 shadow-[0px_4px_20px_rgba(0,0,0,0.03)] border border-surface-container space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-surface-container">
                    <div class="space-y-0.5">
                        <h2 class="text-lg md:text-xl font-bold text-on-surface flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                            <span>Nạp tiền vào ví</span>
                        </h2>
                        <p class="text-xs text-text-muted">Chọn số tiền nạp và thanh toán nhanh qua Cổng SePay</p>
                    </div>
                    <div class="hidden sm:flex items-center gap-1.5 text-xs text-text-muted bg-surface-container-low px-3 py-1.5 rounded-lg border border-surface-container">
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">security</span>
                        <span>Chuẩn PCI-DSS quốc tế</span>
                    </div>
                </div>

                <form id="form-nap-tien" action="{{ route('tai-khoan.nap-tien.checkout') }}" method="POST" novalidate class="space-y-6">
                    @csrf
                    <input type="hidden" name="phuong_thuc" value="SEPAY_GATEWAY" />

                    {{-- Client error alert --}}
                    <div id="deposit-client-error-banner" class="hidden p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] shrink-0 text-rose-600">error</span>
                        <span id="deposit-client-error-text" class="font-medium">Vui lòng kiểm tra lại số tiền nạp.</span>
                    </div>

                    {{-- Bước 1: Chọn số tiền nhanh --}}
                    <div class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant">
                            1. Chọn số tiền muốn nạp
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
                            <button type="button" onclick="selectPresetAmount(50000)" class="preset-btn py-3 px-3 rounded-xl border border-outline-variant/60 font-semibold text-xs text-on-surface hover:border-primary hover:text-primary hover:bg-primary/5 transition-all cursor-pointer text-center">
                                50.000 đ
                            </button>
                            <button type="button" onclick="selectPresetAmount(100000)" class="preset-btn py-3 px-3 rounded-xl border-2 border-primary bg-primary/10 font-bold text-xs text-primary transition-all cursor-pointer text-center">
                                100.000 đ
                            </button>
                            <button type="button" onclick="selectPresetAmount(200000)" class="preset-btn py-3 px-3 rounded-xl border border-outline-variant/60 font-semibold text-xs text-on-surface hover:border-primary hover:text-primary hover:bg-primary/5 transition-all cursor-pointer text-center">
                                200.000 đ
                            </button>
                            <button type="button" onclick="selectPresetAmount(500000)" class="preset-btn py-3 px-3 rounded-xl border border-outline-variant/60 font-semibold text-xs text-on-surface hover:border-primary hover:text-primary hover:bg-primary/5 transition-all cursor-pointer text-center">
                                500.000 đ
                            </button>
                            <button type="button" onclick="selectPresetAmount(1000000)" class="preset-btn py-3 px-3 rounded-xl border border-outline-variant/60 font-semibold text-xs text-on-surface hover:border-primary hover:text-primary hover:bg-primary/5 transition-all cursor-pointer text-center">
                                1.000.000 đ
                            </button>
                            <button type="button" onclick="selectPresetAmount(2000000)" class="preset-btn py-3 px-3 rounded-xl border border-outline-variant/60 font-semibold text-xs text-on-surface hover:border-primary hover:text-primary hover:bg-primary/5 transition-all cursor-pointer text-center">
                                2.000.000 đ
                            </button>
                        </div>

                        {{-- Tự nhập số tiền --}}
                        <div class="pt-2">
                            <label for="deposit-custom-amount" class="block text-xs font-medium text-text-muted mb-1.5">
                                Hoặc tự nhập số tiền khác (VNĐ):
                            </label>
                            <div class="relative max-w-sm">
                                <input type="text" 
                                       id="deposit-custom-amount" 
                                       inputmode="numeric"
                                       placeholder="Tối thiểu 10.000 VNĐ"
                                       value="100.000"
                                       class="w-full pl-4 pr-16 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl text-base font-bold text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-mono" />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-text-muted">VNĐ</span>
                            </div>
                            <input type="hidden" name="so_tien" id="deposit-real-amount" value="100000" />
                            <p class="text-[11px] text-text-muted mt-1.5">Hạn mức nạp: Tối thiểu 10.000 VNĐ — Tối đa 50.000.000 VNĐ mỗi lần.</p>
                        </div>
                    </div>

                    {{-- Bước 2: Phương thức thanh toán --}}
                    <div class="space-y-3 pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant">
                            2. Phương thức thanh toán
                        </label>

                        <div class="p-4 sm:p-5 rounded-2xl border border-primary/30 bg-primary/[0.03] flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[24px]">account_balance</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-on-surface text-sm">Cổng thanh toán SePay</span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-bold">Tự động cộng tiền</span>
                                </div>
                                <p class="text-xs text-text-muted mt-1 leading-relaxed">
                                    Hỗ trợ quét <strong>Mã VietQR 24/7</strong> mọi ngân hàng, <strong>Thẻ ATM nội địa (Napas)</strong> và <strong>Thẻ Visa / Mastercard</strong>.
                                </p>
                                <div class="flex items-center gap-4 mt-2.5 flex-wrap text-[11px]">
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                                        <span class="material-symbols-outlined text-[14px]">bolt</span>
                                        Tiền vào ví tức thì
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-text-muted">
                                        <span class="material-symbols-outlined text-[14px] text-primary">verified_user</span>
                                        Không thu phí giao dịch
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Nút Xác nhận nạp tiền --}}
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-surface-container">
                        <div class="text-xs text-text-muted flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">lock</span>
                            <span>Giao dịch an toàn được mã hóa SSL 256-bit</span>
                        </div>

                        <button type="submit" 
                                id="btn-submit-deposit"
                                class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-primary text-white font-bold text-sm shadow-md hover:bg-primary/95 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">credit_card</span>
                            <span id="btn-deposit-label">TIẾN HÀNH NẠP TIỀN</span>
                        </button>
                    </div>
                </form>
            </section>

            {{-- ============================================================== --}}
            {{-- 3. BẢNG LỊCH SỬ NẠP TIỀN --}}
            {{-- ============================================================== --}}
            <section id="lich-su-giao-dich" class="bg-surface-card rounded-2xl p-6 md:p-8 shadow-[0px_4px_20px_rgba(0,0,0,0.03)] border border-surface-container space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-surface-container">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[24px]">receipt_long</span>
                        <h2 class="text-lg md:text-xl font-bold text-on-surface">Lịch sử nạp tiền</h2>
                    </div>
                    <span class="text-xs text-text-muted">Cập nhật theo thời gian thực</span>
                </div>

                @if(isset($lichSuNapTien) && $lichSuNapTien->count() > 0)
                    <div class="overflow-x-auto rounded-xl border border-outline-variant/30">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-wider font-semibold border-b border-outline-variant/30">
                                <tr>
                                    <th class="py-3 px-4">Mã giao dịch</th>
                                    <th class="py-3 px-4">Số tiền</th>
                                    <th class="py-3 px-4 hidden sm:table-cell">Phương thức</th>
                                    <th class="py-3 px-4">Trạng thái</th>
                                    <th class="py-3 px-4 hidden md:table-cell">Thời gian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/20">
                                @foreach($lichSuNapTien as $tt)
                                    <tr class="hover:bg-surface-container-lowest/50 transition-colors">
                                        <td class="py-3 px-4 font-mono text-xs font-bold text-on-surface">
                                            {{ $tt->ma_giao_dich }}
                                        </td>
                                        <td class="py-3 px-4 font-bold font-mono text-primary">
                                            +{{ number_format($tt->so_tien, 0, ',', '.') }} đ
                                        </td>
                                        <td class="py-3 px-4 text-xs text-on-surface-variant hidden sm:table-cell">
                                            <span class="inline-flex items-center gap-1 font-medium text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                                                <span class="material-symbols-outlined text-[13px]">credit_card</span> Cổng SePay
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($tt->trang_thai === 'thanh_cong')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Thành công
                                                </span>
                                            @elseif($tt->trang_thai === 'cho_thanh_toan')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Chờ nạp
                                                </span>
                                            @elseif($tt->trang_thai === 'that_bai')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Thất bại
                                                </span>
                                            @elseif($tt->trang_thai === 'da_huy')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Đã hủy
                                                </span>
                                            @else
                                                <span class="text-xs text-text-muted">{{ $tt->trang_thai }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-xs text-text-muted hidden md:table-cell">
                                            {{ $tt->created_at ? $tt->created_at->format('d/m/Y H:i') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang nếu có --}}
                    @if(method_exists($lichSuNapTien, 'hasPages') && $lichSuNapTien->hasPages())
                        <div class="pt-2">
                            {{ $lichSuNapTien->links() }}
                        </div>
                    @endif
                @else
                    <div class="py-12 text-center space-y-3">
                        <div class="w-14 h-14 mx-auto rounded-full bg-surface-container-low flex items-center justify-center text-text-muted">
                            <span class="material-symbols-outlined text-[32px]">receipt</span>
                        </div>
                        <p class="text-sm font-semibold text-on-surface">Chưa có giao dịch nạp tiền nào</p>
                        <p class="text-xs text-text-muted max-w-sm mx-auto">
                            Khi bạn thực hiện nạp tiền vào ví qua Cổng SePay, chi tiết các giao dịch sẽ hiển thị đầy đủ tại đây.
                        </p>
                    </div>
                @endif
            </section>

        </div>
    </div>
</main>

@push('scripts')
<script>
    function formatNumber(n) {
        return new Intl.NumberFormat('vi-VN').format(n);
    }

    function selectPresetAmount(amount) {
        document.getElementById('deposit-real-amount').value = amount;
        document.getElementById('deposit-custom-amount').value = formatNumber(amount);
        hideError();

        // Update preset button active states
        document.querySelectorAll('.preset-btn').forEach(btn => {
            btn.classList.remove('border-2', 'border-primary', 'bg-primary/10', 'text-primary', 'font-bold');
            btn.classList.add('border', 'border-outline-variant/60', 'text-on-surface', 'font-semibold');
            if (btn.textContent.includes(formatNumber(amount))) {
                btn.classList.remove('border', 'border-outline-variant/60', 'text-on-surface', 'font-semibold');
                btn.classList.add('border-2', 'border-primary', 'bg-primary/10', 'text-primary', 'font-bold');
            }
        });
    }

    function showError(msg) {
        const banner = document.getElementById('deposit-client-error-banner');
        const text = document.getElementById('deposit-client-error-text');
        text.textContent = msg;
        banner.classList.remove('hidden');
    }

    function hideError() {
        const banner = document.getElementById('deposit-client-error-banner');
        banner.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputAmount = document.getElementById('deposit-custom-amount');
        const hiddenAmount = document.getElementById('deposit-real-amount');
        const form = document.getElementById('form-nap-tien');

        inputAmount.addEventListener('input', function(e) {
            let raw = this.value.replace(/[^0-9]/g, '');
            if (!raw) {
                hiddenAmount.value = '0';
                return;
            }
            let val = parseInt(raw, 10);
            if (val > 50000000) val = 50000000;
            hiddenAmount.value = val;
            this.value = formatNumber(val);
            hideError();

            // Highlight selected preset if matches
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.classList.remove('border-2', 'border-primary', 'bg-primary/10', 'text-primary', 'font-bold');
                btn.classList.add('border', 'border-outline-variant/60', 'text-on-surface', 'font-semibold');
                if (btn.textContent.includes(formatNumber(val))) {
                    btn.classList.remove('border', 'border-outline-variant/60', 'text-on-surface', 'font-semibold');
                    btn.classList.add('border-2', 'border-primary', 'bg-primary/10', 'text-primary', 'font-bold');
                }
            });
        });

        form.addEventListener('submit', function(e) {
            const amount = parseInt(hiddenAmount.value, 10);
            if (!amount || amount < 10000) {
                e.preventDefault();
                showError('Số tiền nạp tối thiểu là 10.000 VNĐ.');
                inputAmount.focus();
                return;
            }

            const btn = document.getElementById('btn-submit-deposit');
            btn.disabled = true;
            btn.innerHTML = '<span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span> Đang chuyển hướng SePay...';
        });
    });
</script>
@endpush
@endsection
