@extends('layouts.app')
@section('title', 'Đang chuyển hướng đến cổng thanh toán SePay — Quán Mới')

@section('content')
<main class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-outline-variant/30 p-8 text-center space-y-6">
        {{-- Animated Logo / Spinner --}}
        <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
            <div class="absolute inset-0 rounded-full border-4 border-primary/20 animate-ping"></div>
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary shadow-inner">
                <span class="material-symbols-outlined text-[32px] animate-spin">progress_activity</span>
            </div>
        </div>

        <div class="space-y-2">
            <h2 class="text-xl font-bold text-on-surface">Đang kết nối cổng thanh toán SePay</h2>
            <p class="text-sm text-on-surface-variant leading-relaxed">
                Hệ thống đang bảo mật dữ liệu và chuyển bạn đến cổng thanh toán an toàn...
            </p>
        </div>

        <div class="bg-surface-container-low rounded-2xl p-4 text-left space-y-2 border border-outline-variant/20">
            <div class="flex justify-between items-center text-xs">
                <span class="text-on-surface-variant font-medium">Mã giao dịch:</span>
                <span class="font-mono font-bold text-on-surface">{{ $checkoutInfo['invoice_number'] }}</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-on-surface-variant font-medium">Số tiền nạp:</span>
                <span class="font-bold text-primary text-sm">{{ number_format($checkoutInfo['amount'], 0, ',', '.') }} VNĐ</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-on-surface-variant font-medium">Nội dung:</span>
                <span class="text-on-surface truncate max-w-[200px]" title="{{ $checkoutInfo['order_description'] }}">{{ $checkoutInfo['order_description'] }}</span>
            </div>
        </div>

        {{-- Form SePay tự động submit --}}
        <form id="sepay-checkout-form" method="POST" action="{{ $checkoutInfo['action_url'] }}" class="pt-2">
            @foreach($checkoutInfo['fields'] as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach

            
        </form>

        <div class="pt-1">
            <a href="{{ route('tai-khoan.nap-tien.huy', ['invoice' => $checkoutInfo['invoice_number']]) }}"
               id="btn-cancel-redirect"
               class="inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:underline transition-colors">
                <span class="material-symbols-outlined text-[16px]">cancel</span>
                <span>Hủy giao dịch và quay lại Ví</span>
            </a>
        </div>

        <p class="text-[11px] text-text-muted">
            Nếu trang không tự chuyển hướng sau vài giây, vui lòng bấm nút "Tiếp tục" ở trên.
        </p>
    </div>
</main>

@push('scripts')
<script>
    (function() {
        const invoiceNumber = @json($checkoutInfo['invoice_number']);
        const cancelUrl = @json(route('tai-khoan.nap-tien.huy', ['invoice' => $checkoutInfo['invoice_number']]));
        const redirectedKey = 'qm_sepay_redirected_' + invoiceNumber;
        let isCancelling = false;

        function handleBackCancel() {
            if (isCancelling) return;
            isCancelling = true;
            try {
                sessionStorage.removeItem('qm_pending_deposit_invoice');
                sessionStorage.removeItem(redirectedKey);
            } catch (e) {}
            window.location.replace(cancelUrl);
        }

        // Bắt sự kiện khi người dùng bấm nút Back trình duyệt từ cổng SePay quay lại trang này (bfcache)
        window.addEventListener('pageshow', function(event) {
            const navEntries = (window.performance && window.performance.getEntriesByType)
                ? window.performance.getEntriesByType('navigation')
                : [];
            const isBackForward = event.persisted || (navEntries.length > 0 && navEntries[0].type === 'back_forward');

            let alreadyRedirected = false;
            try {
                alreadyRedirected = sessionStorage.getItem(redirectedKey) === '1';
            } catch (e) {}

            if (isBackForward || alreadyRedirected) {
                handleBackCancel();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cancelBtn = document.getElementById('btn-cancel-redirect');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    isCancelling = true;
                    try {
                        sessionStorage.removeItem('qm_pending_deposit_invoice');
                        sessionStorage.removeItem(redirectedKey);
                    } catch (e) {}
                });
            }

            let alreadyRedirected = false;
            try {
                alreadyRedirected = sessionStorage.getItem(redirectedKey) === '1';
            } catch (e) {}

            if (alreadyRedirected) {
                handleBackCancel();
                return;
            }

            const form = document.getElementById('sepay-checkout-form');
            if (form) {
                setTimeout(function() {
                    if (isCancelling) return;
                    try {
                        sessionStorage.setItem('qm_pending_deposit_invoice', invoiceNumber);
                        sessionStorage.setItem(redirectedKey, '1');
                    } catch (e) {}
                    form.submit();
                }, 400);
            }
        });
    })();
</script>
@endpush
@endsection
