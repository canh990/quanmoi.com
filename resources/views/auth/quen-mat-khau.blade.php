@extends('layouts.app')

@section('title', 'Quên mật khẩu | Quán Mới')

@push('styles')
    <meta name="description" content="Đặt lại mật khẩu tài khoản Quán Mới" />
    <style>
        @keyframes floatSlow {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20px, -15px) scale(1.04); }
        }
        .animate-float-slow { animation: floatSlow 10s ease-in-out infinite; }
        @keyframes successBounce {
            0% { transform: scale(0.5); opacity: 0; }
            60% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-success { animation: successBounce 0.5s ease-out; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .animate-spin { animation: spin 1s linear infinite; }
    </style>
@endpush

@section('content')
<main class="flex-grow pt-24 pb-16 flex items-center justify-center min-h-[85vh] px-4 relative overflow-hidden">
    {{-- Background --}}
    <div class="absolute inset-0 -z-10">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-fixed/20 via-background to-secondary-fixed/10"></div>
        <div class="absolute top-16 -left-16 w-64 h-64 bg-primary/8 rounded-full blur-3xl animate-float-slow"></div>
        <div class="absolute inset-0 opacity-[0.025]" style="background-image: radial-gradient(circle, #a04100 1px, transparent 1px); background-size: 28px 28px;"></div>
    </div>

    <div class="w-full max-w-[400px]">
        <div class="bg-white rounded-[28px] shadow-[0_20px_50px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-primary via-tertiary to-secondary"></div>
            <div class="p-7">

                {{-- STEP 1: Nhập Email --}}
                <div id="step-email">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary flex-shrink-0">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">lock_reset</span>
                        </div>
                        <div>
                            <h1 class="text-xl font-extrabold text-on-surface leading-tight">Quên mật khẩu</h1>
                            <p class="text-[13px] text-on-surface-variant">Nhập email để nhận link đặt lại</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label for="reset-email" class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Email tài khoản</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">mail</span>
                                <input id="reset-email" class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-gray-400" placeholder="email@example.com" type="email" required />
                            </div>
                            <p id="error-reset-email" class="text-xs text-red-600 hidden"></p>
                        </div>

                        <p id="error-reset-general" class="text-sm text-red-600 hidden text-center"></p>

                        <button id="send-btn" onclick="sendResetLink()" class="w-full h-[52px] bg-primary text-white font-bold text-[15px] rounded-2xl shadow-[0_4px_16px_rgba(160,65,0,0.25)] hover:shadow-[0_8px_24px_rgba(160,65,0,0.35)] hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">send</span>
                            Gửi link đặt lại mật khẩu
                        </button>

                        <p class="text-center text-[13px] text-on-surface-variant">
                            Nhớ mật khẩu rồi?
                            <a href="{{ route('login') }}" class="text-primary font-bold hover:underline ml-1">Đăng nhập</a>
                        </p>
                    </div>
                </div>

                {{-- STEP 2: Thành công --}}
                <div id="step-success" class="hidden">
                    <div class="flex flex-col items-center text-center space-y-4 py-4">
                        <div class="w-20 h-20 bg-tick-xanh/15 rounded-full flex items-center justify-center animate-success">
                            <span class="material-symbols-outlined text-tick-xanh text-5xl" style="font-variation-settings: 'FILL' 1;">mark_email_read</span>
                        </div>
                        <h2 class="text-xl font-black text-on-surface">Email đã được gửi! ✉️</h2>
                        <p class="text-[14px] text-on-surface-variant leading-relaxed max-w-xs" id="success-message-text">
                            Kiểm tra hộp thư email của bạn, bao gồm thư mục Spam. Link có hiệu lực trong <strong>60 phút</strong>.
                        </p>
                        <a href="{{ route('login') }}" class="mt-2 inline-flex items-center gap-2 text-primary font-bold text-[14px] hover:underline">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            Về trang đăng nhập
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    async function sendResetLink() {
        const emailEl = document.getElementById('reset-email');
        const errorEmail = document.getElementById('error-reset-email');
        const errorGeneral = document.getElementById('error-reset-general');
        const btn = document.getElementById('send-btn');
        const originalHtml = btn.innerHTML;

        // Clear errors
        errorEmail.classList.add('hidden'); errorEmail.textContent = '';
        errorGeneral.classList.add('hidden'); errorGeneral.textContent = '';

        if (!emailEl.value) {
            errorEmail.textContent = 'Vui lòng nhập địa chỉ email.';
            errorEmail.classList.remove('hidden');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Đang gửi...';

        try {
            const response = await fetch('/quen-mat-khau', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify({ email: emailEl.value })
            });
            const data = await response.json();

            if (!response.ok) {
                errorGeneral.textContent = data.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
                errorGeneral.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                return;
            }

            // Show success step
            document.getElementById('step-email').classList.add('hidden');
            document.getElementById('step-success').classList.remove('hidden');
        } catch (e) {
            errorGeneral.textContent = 'Không thể kết nối máy chủ.';
            errorGeneral.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    document.getElementById('reset-email').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') sendResetLink();
    });
</script>
@endpush
