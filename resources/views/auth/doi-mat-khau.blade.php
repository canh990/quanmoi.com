@extends('layouts.app')

@section('title', 'Đổi mật khẩu | Quán Mới')

@section('content')
@auth
<main class="flex-grow pt-24 pb-16 flex items-center justify-center min-h-[85vh] px-4">
    <div class="w-full max-w-[420px]">
        <div class="bg-white rounded-[28px] shadow-[0_20px_50px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-primary via-tertiary to-secondary"></div>
            <div class="p-7">

                {{-- FORM ĐỔI MẬT KHẨU --}}
                <div id="step-form">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary flex-shrink-0">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">manage_accounts</span>
                        </div>
                        <div>
                            <h1 class="text-xl font-extrabold text-on-surface leading-tight">Đổi mật khẩu</h1>
                            <p class="text-[13px] text-on-surface-variant">Cập nhật mật khẩu bảo mật của bạn</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        {{-- Mật khẩu hiện tại --}}
                        <div class="space-y-1.5">
                            <label for="current-pw" class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Mật khẩu hiện tại</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">lock</span>
                                <input id="current-pw" class="w-full h-12 pl-11 pr-12 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-gray-400" placeholder="Nhập mật khẩu hiện tại" type="password" />
                                <button type="button" onclick="togglePw('current-pw', this)" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-full hover:bg-gray-100 text-gray-400 transition-all">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <p id="error-current-pw" class="text-xs text-red-600 hidden"></p>
                        </div>

                        {{-- Mật khẩu mới --}}
                        <div class="space-y-1.5">
                            <label for="new-pw" class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Mật khẩu mới</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">key</span>
                                <input id="new-pw" class="w-full h-12 pl-11 pr-12 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-gray-400" placeholder="Tối thiểu 6 ký tự" type="password" minlength="6" />
                                <button type="button" onclick="togglePw('new-pw', this)" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-full hover:bg-gray-100 text-gray-400 transition-all">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <p id="error-new-pw" class="text-xs text-red-600 hidden"></p>
                        </div>

                        {{-- Xác nhận mật khẩu mới --}}
                        <div class="space-y-1.5">
                            <label for="confirm-pw" class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Xác nhận mật khẩu mới</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">lock_open</span>
                                <input id="confirm-pw" class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-gray-400" placeholder="Nhập lại mật khẩu mới" type="password" />
                            </div>
                            <p id="error-confirm-pw" class="text-xs text-red-600 hidden"></p>
                        </div>

                        <p id="error-general" class="text-sm text-red-600 hidden text-center"></p>

                        <button id="submit-btn" onclick="submitChangePassword()" class="w-full h-[52px] bg-primary text-white font-bold text-[15px] rounded-2xl shadow-[0_4px_16px_rgba(160,65,0,0.25)] hover:shadow-[0_8px_24px_rgba(160,65,0,0.35)] hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">save</span>
                            Lưu mật khẩu mới
                        </button>

                        <a href="/" class="block text-center text-[13px] text-on-surface-variant hover:text-primary transition-colors">← Quay lại trang chủ</a>
                    </div>
                </div>

                {{-- THÀNH CÔNG --}}
                <div id="step-success" class="hidden">
                    <div class="flex flex-col items-center text-center space-y-4 py-6">
                        <div class="w-20 h-20 bg-tick-xanh/15 rounded-full flex items-center justify-center" style="animation: successBounce 0.5s ease-out">
                            <span class="material-symbols-outlined text-tick-xanh text-5xl" style="font-variation-settings: 'FILL' 1;">shield_check</span>
                        </div>
                        <h2 class="text-xl font-black text-on-surface">Đổi mật khẩu thành công! 🔐</h2>
                        <p class="text-[14px] text-on-surface-variant max-w-xs">Mật khẩu của bạn đã được cập nhật. Bảo mật tài khoản đã được tăng cường.</p>
                        <a href="/" class="mt-2 inline-flex items-center gap-2 bg-primary text-white font-bold text-[14px] px-5 py-2.5 rounded-xl hover:bg-primary/90 transition-all">
                            <span class="material-symbols-outlined text-[18px]">home</span>
                            Về trang chủ
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
@else
<main class="flex-grow pt-24 pb-16 flex items-center justify-center min-h-[85vh] px-4">
    <div class="text-center space-y-4">
        <span class="material-symbols-outlined text-primary text-6xl" style="font-variation-settings: 'FILL' 1;">lock_person</span>
        <h1 class="text-2xl font-black text-on-surface">Bạn chưa đăng nhập</h1>
        <p class="text-on-surface-variant">Vui lòng đăng nhập để thay đổi mật khẩu.</p>
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-primary text-white font-bold px-6 py-3 rounded-xl hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined text-[18px]">login</span>Đăng nhập
        </a>
    </div>
</main>
@endauth
@endsection

@push('scripts')
<style>
    @keyframes successBounce { 0% { transform: scale(0.5); opacity: 0; } 60% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .animate-spin { animation: spin 1s linear infinite; }
</style>
<script>
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

    async function submitChangePassword() {
        const currentPw = document.getElementById('current-pw').value;
        const newPw = document.getElementById('new-pw').value;
        const confirmPw = document.getElementById('confirm-pw').value;
        const btn = document.getElementById('submit-btn');
        const originalHtml = btn.innerHTML;

        ['current-pw', 'new-pw', 'confirm-pw', 'general'].forEach(key => {
            const el = document.getElementById(`error-${key}`);
            if (el) { el.classList.add('hidden'); el.textContent = ''; }
        });

        if (!currentPw) { showError('current-pw', 'Vui lòng nhập mật khẩu hiện tại.'); return; }
        if (!newPw || newPw.length < 6) { showError('new-pw', 'Mật khẩu mới phải ít nhất 6 ký tự.'); return; }
        if (newPw !== confirmPw) { showError('confirm-pw', 'Xác nhận mật khẩu không khớp.'); return; }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Đang lưu...';

        try {
            const response = await fetch('/doi-mat-khau', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify({ mat_khau_hien_tai: currentPw, mat_khau_moi: newPw, mat_khau_xac_nhan: confirmPw })
            });
            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    if (data.errors.mat_khau_hien_tai) showError('current-pw', data.errors.mat_khau_hien_tai[0]);
                    if (data.errors.mat_khau_moi) showError('new-pw', data.errors.mat_khau_moi[0]);
                } else {
                    showError('general', data.message || 'Có lỗi xảy ra.');
                }
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                return;
            }

            document.getElementById('step-form').classList.add('hidden');
            document.getElementById('step-success').classList.remove('hidden');
        } catch (e) {
            showError('general', 'Không thể kết nối máy chủ.');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    function showError(key, message) {
        const el = document.getElementById(`error-${key}`);
        if (el) { el.textContent = message; el.classList.remove('hidden'); }
    }
</script>
@endpush
