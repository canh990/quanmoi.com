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

            <button type="submit" class="w-full py-3.5 px-6 rounded-2xl bg-primary text-white font-bold text-sm shadow-lg hover:shadow-primary/30 hover:bg-primary/95 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>Tiếp tục đến cổng thanh toán</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
        </form>

        <p class="text-[11px] text-text-muted">
            Nếu trang không tự chuyển hướng sau vài giây, vui lòng bấm nút "Tiếp tục" ở trên.
        </p>
    </div>
</main>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('sepay-checkout-form');
        if (form) {
            // Chờ 600ms tạo hiệu ứng chuyển tiếp mượt mà cho trải nghiệm người dùng
            setTimeout(function() {
                form.submit();
            }, 600);
        }
    });
</script>
@endpush
@endsection
