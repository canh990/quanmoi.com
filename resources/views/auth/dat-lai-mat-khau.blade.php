@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu | Quán Mới')

@push('styles')
    <style>
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .animate-spin { animation: spin 1s linear infinite; }
        @keyframes successBounce {
            0% { transform: scale(0.5); opacity: 0; }
            60% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-success { animation: successBounce 0.5s ease-out; }
    </style>
@endpush

@section('content')
<main class="flex-grow pt-24 pb-16 flex items-center justify-center min-h-[85vh] px-4 relative overflow-hidden">
    <div class="absolute inset-0 -z-10">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-fixed/20 via-background to-secondary-fixed/10"></div>
        <div class="absolute inset-0 opacity-[0.025]" style="background-image: radial-gradient(circle, #a04100 1px, transparent 1px); background-size: 28px 28px;"></div>
    </div>

    <div class="w-full max-w-[400px]">
        <div class="bg-white rounded-[28px] shadow-[0_20px_50px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-primary via-tertiary to-secondary"></div>
            <div class="p-7">

                {{-- FORM ĐẶT LẠI MẬT KHẨU --}}
                <div id="step-form">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary flex-shrink-0">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">key</span>
                        </div>
                        <div>
                            <h1 class="text-xl font-extrabold text-on-surface leading-tight">Đặt mật khẩu mới</h1>
                            <p class="text-[13px] text-on-surface-variant">Cho tài khoản {{ $email }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        {{-- Mật khẩu mới --}}
                        <div class="space-y-1.5">
                            <label for="new-password" class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Mật khẩu mới</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">lock</span>
                                <input id="new-password" class="w-full h-12 pl-11 pr-12 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-gray-400" placeholder="Tối thiểu 6 ký tự" type="password" minlength="6" />
                                <button type="button" onclick="togglePw('new-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-full hover:bg-gray-100 text-gray-400 transition-all">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <p id="error-new-password" class="text-xs text-red-600 hidden"></p>
                        </div>

                        {{-- Xác nhận mật khẩu --}}
                        <div class="space-y-1.5">
                            <label for="confirm-password" class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Xác nhận mật khẩu</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">lock_open</span>
                                <input id="confirm-password" class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-gray-400" placeholder="Nhập lại mật khẩu mới" type="password" />
                            </div>
                            <p id="error-confirm-password" class="text-xs text-red-600 hidden"></p>
                        </div>

                        <p id="error-general" class="text-sm text-red-600 hidden text-center"></p>

                        <button id="reset-btn" onclick="submitReset()" class="w-full h-[52px] bg-primary text-white font-bold text-[15px] rounded-2xl shadow-[0_4px_16px_rgba(160,65,0,0.25)] hover:shadow-[0_8px_24px_rgba(160,65,0,0.35)] hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            Xác nhận đặt lại mật khẩu
                        </button>
                    </div>
                </div>

                {{-- THÀNH CÔNG --}}
                <div id="step-success" class="hidden">
                    <div class="flex flex-col items-center text-center space-y-4 py-4">
                        <div class="w-20 h-20 bg-tick-xanh/15 rounded-full flex items-center justify-center animate-success">
                            <span class="material-symbols-outlined text-tick-xanh text-5xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        </div>
                        <h2 class="text-xl font-black text-on-surface">Thành công!</h2>
                        <p class="text-[14px] text-on-surface-variant leading-relaxed max-w-xs">
                            Mật khẩu của bạn đã được đặt lại thành công. Đang chuyển bạn đến trang đăng nhập...
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    const TOKEN = '{{ $token }}';
    const EMAIL = '{{ $email }}';

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('.material-symbols-outlined');
        if (input.type === 'password') { input.type = 'text'; icon.textContent = 'visibility_off'; }
        else { input.type = 'password'; icon.textContent = 'visibility'; }
    }

    async function submitReset() {
        const newPw = document.getElementById('new-password').value;
        const confirmPw = document.getElementById('confirm-password').value;
        const errorNew = document.getElementById('error-new-password');
        const errorConfirm = document.getElementById('error-confirm-password');
        const errorGeneral = document.getElementById('error-general');
        const btn = document.getElementById('reset-btn');
        const originalHtml = btn.innerHTML;

        [errorNew, errorConfirm, errorGeneral].forEach(el => { el.classList.add('hidden'); el.textContent = ''; });

        if (!newPw || newPw.length < 6) {
            errorNew.textContent = 'Mật khẩu phải có ít nhất 6 ký tự.';
            errorNew.classList.remove('hidden');
            return;
        }
        if (newPw !== confirmPw) {
            errorConfirm.textContent = 'Xác nhận mật khẩu không khớp.';
            errorConfirm.classList.remove('hidden');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Đang xử lý...';

        try {
            const response = await fetch('/dat-lai-mat-khau', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify({ email: EMAIL, token: TOKEN, mat_khau: newPw, mat_khau_xac_nhan: confirmPw })
            });
            const data = await response.json();

            if (!response.ok) {
                errorGeneral.textContent = data.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
                errorGeneral.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                return;
            }

            document.getElementById('step-form').classList.add('hidden');
            document.getElementById('step-success').classList.remove('hidden');
            setTimeout(() => { window.location.href = '/dang-nhap'; }, 2000);
        } catch (e) {
            errorGeneral.textContent = 'Không thể kết nối máy chủ.';
            errorGeneral.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }
</script>
@endpush
