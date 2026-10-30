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

    <div class="grid grid-cols-1 md:grid-cols-12 gap-stack-lg">
        <!-- Sidebar Navigation -->
        @include('nguoi-dung.partials.sidebar')

        <!-- Main Content Area -->
        <div class="md:col-span-9 space-y-6">
            
            {{-- ============================================================== --}}
            {{-- 1. SỐ DƯ VÍ & NẠP TIỀN NHANH (GỌN GÀNG, TRỰC QUAN) --}}
            {{-- ============================================================== --}}
            <section id="khung-nap-tien" class="bg-surface-card rounded-2xl p-5 sm:p-7 shadow-[0px_4px_20px_rgba(0,0,0,0.03)] border border-surface-container space-y-6">
                {{-- Hàng trên: Tiêu đề + Số dư khả dụng --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-surface-container">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-on-surface tracking-tight">
                            Ví & Nạp tiền
                        </h1>
                        <p class="text-xs text-text-muted mt-0.5">
                            Nạp tiền tự động qua mã QR ngân hàng (SePay)
                        </p>
                    </div>

                    <div class="bg-surface-container-low/80 border border-surface-container px-4 py-3 rounded-xl flex items-center gap-3.5 shrink-0">
                        <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center shadow-xs shrink-0">
                            <span class="material-symbols-outlined text-[22px]">account_balance_wallet</span>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold text-text-muted uppercase tracking-wider block">Số dư khả dụng</span>
                            <div class="flex items-baseline gap-1">
                                <span class="text-xl sm:text-2xl font-extrabold text-on-surface tracking-tight font-mono" id="user-so-du-display">
                                    {{ number_format($user->so_du ?? 0, 0, ',', '.') }}
                                </span>
                                <span class="text-xs font-bold text-primary">đ</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Nạp tiền --}}
                <form id="form-nap-tien" action="{{ route('tai-khoan.nap-tien.checkout') }}" method="POST" novalidate class="space-y-5">
                    @csrf
                    <input type="hidden" name="phuong_thuc" value="SEPAY_GATEWAY" />

                    {{-- Client error alert --}}
                    <div id="deposit-client-error-banner" class="hidden p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] shrink-0 text-rose-600">error</span>
                        <span id="deposit-client-error-text" class="font-medium">Vui lòng kiểm tra lại số tiền nạp.</span>
                    </div>

                    {{-- Chọn mệnh giá nhanh --}}
                    <div class="space-y-2.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant">
                            Chọn nhanh mệnh giá
                        </label>
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                            <button type="button" onclick="selectPresetAmount(50000)" class="preset-btn py-2.5 px-2 rounded-xl border border-outline-variant/60 font-semibold text-xs text-on-surface hover:border-primary hover:text-primary hover:bg-primary/5 transition-all cursor-pointer text-center">
                                50.000đ
                            </button>
                            <button type="button" onclick="selectPresetAmount(100000)" class="preset-btn py-2.5 px-2 rounded-xl border-2 border-primary bg-primary/10 font-bold text-xs text-primary transition-all cursor-pointer text-center">
                                100.000đ
                            </button>
                            <button type="button" onclick="selectPresetAmount(200000)" class="preset-btn py-2.5 px-2 rounded-xl border border-outline-variant/60 font-semibold text-xs text-on-surface hover:border-primary hover:text-primary hover:bg-primary/5 transition-all cursor-pointer text-center">
                                200.000đ
                            </button>
                            <button type="button" onclick="selectPresetAmount(500000)" class="preset-btn py-2.5 px-2 rounded-xl border border-outline-variant/60 font-semibold text-xs text-on-surface hover:border-primary hover:text-primary hover:bg-primary/5 transition-all cursor-pointer text-center">
                                500.000đ
                            </button>
                            <button type="button" onclick="selectPresetAmount(1000000)" class="preset-btn py-2.5 px-2 rounded-xl border border-outline-variant/60 font-semibold text-xs text-on-surface hover:border-primary hover:text-primary hover:bg-primary/5 transition-all cursor-pointer text-center">
                                1.000.000đ
                            </button>
                            <button type="button" onclick="selectPresetAmount(2000000)" class="preset-btn py-2.5 px-2 rounded-xl border border-outline-variant/60 font-semibold text-xs text-on-surface hover:border-primary hover:text-primary hover:bg-primary/5 transition-all cursor-pointer text-center">
                                2.000.000đ
                            </button>
                        </div>
                    </div>

                    {{-- Ô nhập số tiền + Nút Nạp tiền trên cùng 1 hàng --}}
                    <div class="pt-1">
                        <label for="deposit-custom-amount" class="block text-xs font-medium text-text-muted mb-1.5">
                            Số tiền nạp (tối thiểu 10.000đ):
                        </label>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <div class="relative flex-1">
                                <input type="text" 
                                       id="deposit-custom-amount" 
                                       inputmode="numeric"
                                       placeholder="Tối thiểu 10.000"
                                       value="100.000"
                                       class="w-full pl-4 pr-14 py-3 bg-surface-container-low border border-outline-variant rounded-xl text-base font-bold text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-mono" />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-text-muted">VNĐ</span>
                            </div>
                            <input type="hidden" name="so_tien" id="deposit-real-amount" value="100000" />

                            <button type="submit" 
                                    id="btn-submit-deposit"
                                    class="px-7 py-3 rounded-xl bg-primary text-white font-bold text-sm shadow-md hover:bg-primary/95 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer shrink-0">
                                <span class="material-symbols-outlined text-[19px]">qr_code_scanner</span>
                                <span id="btn-deposit-label">NẠP TIỀN NGAY</span>
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            {{-- ============================================================== --}}
            {{-- 2. BẢNG LỊCH SỬ NẠP TIỀN --}}
            {{-- ============================================================== --}}
            <section id="lich-su-giao-dich" class="bg-surface-card rounded-2xl p-5 sm:p-7 shadow-[0px_4px_20px_rgba(0,0,0,0.03)] border border-surface-container space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-surface-container">
                    <span class="material-symbols-outlined text-primary text-[22px]">receipt_long</span>
                    <h2 class="text-base sm:text-lg font-bold text-on-surface">Lịch sử nạp tiền</h2>
                </div>

                @if(isset($lichSuNapTien) && $lichSuNapTien->count() > 0)
                    <div class="overflow-x-auto rounded-xl border border-outline-variant/30">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-wider font-semibold border-b border-outline-variant/30">
                                <tr>
                                    <th class="py-3 px-4">Mã giao dịch</th>
                                    <th class="py-3 px-4">Số tiền</th>
                                    <th class="py-3 px-4">Trạng thái</th>
                                    <th class="py-3 px-4 hidden sm:table-cell text-right">Thời gian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/20">
                                @foreach($lichSuNapTien as $tt)
                                    <tr class="hover:bg-surface-container-lowest/50 transition-colors">
                                        <td class="py-3 px-4 font-mono text-xs font-bold text-on-surface">
                                            <div>{{ $tt->ma_giao_dich }}</div>
                                            <div class="sm:hidden font-sans font-normal text-[11px] text-text-muted mt-0.5">
                                                {{ $tt->created_at ? $tt->created_at->format('d/m/Y H:i') : '—' }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 font-bold font-mono text-primary whitespace-nowrap">
                                            +{{ number_format($tt->so_tien, 0, ',', '.') }} đ
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
                                        <td class="py-3 px-4 text-xs text-text-muted hidden sm:table-cell text-right whitespace-nowrap">
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
                    <div class="py-10 text-center space-y-2">
                        <div class="w-12 h-12 mx-auto rounded-full bg-surface-container-low flex items-center justify-center text-text-muted">
                            <span class="material-symbols-outlined text-[26px]">receipt</span>
                        </div>
                        <p class="text-sm font-semibold text-on-surface">Chưa có giao dịch nạp tiền nào</p>
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
        if (window.showToast) {
            window.showToast({
                title: 'Số tiền không hợp lệ',
                message: msg,
                type: 'error'
            });
            return;
        }
        const banner = document.getElementById('deposit-client-error-banner');
        const text = document.getElementById('deposit-client-error-text');
        if (text) text.textContent = msg;
        if (banner) banner.classList.remove('hidden');
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
