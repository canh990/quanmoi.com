@extends('layouts.app')

@section('title', 'Quán Mới - Đăng ký tài khoản')

@section('content')
    <main class="flex-grow pt-20 pb-24 flex flex-col items-center justify-center min-h-screen relative overflow-hidden">

        {{-- Animated Background --}}
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-tertiary/5 via-background to-primary-fixed/20"></div>
            <div class="absolute top-10 -right-20 w-72 h-72 bg-tertiary/6 rounded-full blur-3xl animate-float-slow"></div>
            <div class="absolute bottom-20 -left-20 w-80 h-80 bg-primary/6 rounded-full blur-3xl animate-float-slow-reverse"></div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, #006e2f 1px, transparent 1px); background-size: 32px 32px;"></div>
        </div>

        {{-- Registration Card --}}
        <div class="w-full max-w-[420px] px-4 relative z-10">
            <div class="bg-surface rounded-[28px] shadow-[0_25px_60px_rgba(0,0,0,0.1)] border border-white/60 overflow-hidden">
                {{-- Top gradient --}}
                <div class="h-1 bg-gradient-to-r from-tertiary via-primary to-secondary"></div>

                <div class="p-6 md:p-8">
                    {{-- Registration Form --}}
                    <div id="registration-section" class="transition-opacity duration-300">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-tertiary/10 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-tertiary text-xl" style="font-variation-settings: 'FILL' 1;">how_to_reg</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-extrabold text-on-surface leading-tight">Tham gia cộng đồng</h2>
                                <p class="text-[13px] text-on-surface-variant">Khám phá và đánh giá quán ăn uy tín</p>
                            </div>
                        </div>

                        {{-- Progress indicator --}}
                        <div class="flex items-center justify-center gap-2 mb-5">
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-primary text-white text-[11px] font-bold flex items-center justify-center">1</div>
                                <span class="text-[11px] font-bold text-primary">Thông tin</span>
                            </div>
                            <div class="w-8 h-px bg-outline-variant/40"></div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-outline-variant/30 text-on-surface-variant/60 text-[11px] font-bold flex items-center justify-center">2</div>
                                <span class="text-[11px] font-medium text-on-surface-variant/60">Xác thực</span>
                            </div>
                        </div>

                        <form class="space-y-3.5" id="register-form" onsubmit="event.preventDefault(); submitRegistration();">
                            <div class="space-y-1.5">
                                <label class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Họ và Tên</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">person</span>
                                    <input id="ho_ten" name="ho_ten" class="w-full h-12 pl-11 pr-4 rounded-xl border border-outline-variant/60 bg-surface-container-lowest focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-on-surface-variant/40" placeholder="Nguyễn Văn A" required type="text"/>
                                </div>
                                <p class="text-error text-xs text-red-600 hidden" id="error-ho_ten"></p>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Email</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">mail</span>
                                    <input id="email" name="email" class="w-full h-12 pl-11 pr-4 rounded-xl border border-outline-variant/60 bg-surface-container-lowest focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-on-surface-variant/40" placeholder="email@example.com" required type="email"/>
                                </div>
                                <p class="text-error text-xs text-red-600 hidden" id="error-email"></p>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Mật khẩu</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">lock</span>
                                    <input id="mat_khau" name="mat_khau" class="w-full h-12 pl-11 pr-12 rounded-xl border border-outline-variant/60 bg-surface-container-lowest focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-on-surface-variant/40" placeholder="Tối thiểu 8 ký tự" required type="password" minlength="8"/>
                                    <button type="button" onclick="togglePw()" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-full hover:bg-surface-container text-on-surface-variant/50 hover:text-on-surface-variant transition-all">
                                        <span class="material-symbols-outlined text-[20px]" id="pw-toggle-icon">visibility</span>
                                    </button>
                                </div>
                                <p class="text-error text-xs text-red-600 hidden" id="error-mat_khau"></p>
                            </div>
                            <p class="text-error text-sm text-red-600 hidden text-center" id="error-general"></p>
                            <div class="pt-1">
                                <button id="register-submit-btn" class="w-full h-[52px] bg-primary text-white font-bold text-[15px] rounded-2xl shadow-[0_4px_16px_rgba(160,65,0,0.25)] hover:shadow-[0_8px_24px_rgba(160,65,0,0.35)] hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2" type="submit">
                                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                                    Đăng ký ngay
                                </button>
                            </div>
                        </form>

                        <div class="flex items-center gap-3 my-5">
                            <div class="flex-1 h-px bg-outline-variant/30"></div>
                            <span class="text-[11px] text-on-surface-variant/50 uppercase tracking-widest font-bold">hoặc</span>
                            <div class="flex-1 h-px bg-outline-variant/30"></div>
                        </div>

                        {{-- Google Login --}}
                        <a href="{{ route('login.google') }}" class="w-full h-[52px] bg-white text-on-surface font-bold text-[15px] rounded-2xl border border-outline-variant/40 shadow-sm hover:bg-gray-50 hover:shadow active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-3 mb-5">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/><path d="M1 1h22v22H1z" fill="none"/></svg>
                            Tiếp tục với Google
                        </a>

                        <p class="text-center text-[14px] text-on-surface-variant">
                            Bạn đã có tài khoản? <a class="text-primary font-bold hover:underline ml-1" href="{{ route('login') }}">Đăng nhập</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- OTP Overlay --}}
        <div class="hidden fixed inset-0 z-[60] flex items-center justify-center px-4" id="otp-section">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-md"></div>
            <div class="relative bg-surface rounded-[28px] w-full max-w-[400px] shadow-[0_25px_60px_rgba(0,0,0,0.15)] border border-white/60 overflow-hidden">
                <div class="h-1 bg-gradient-to-r from-tertiary to-tick-xanh"></div>
                <div class="p-6 md:p-8 space-y-6">
                    {{-- Progress --}}
                    <div class="flex items-center justify-center gap-2">
                        <div class="flex items-center gap-1.5">
                            <div class="w-6 h-6 rounded-full bg-tick-xanh text-white text-[11px] font-bold flex items-center justify-center">
                                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'wght' 700;">check</span>
                            </div>
                            <span class="text-[11px] font-bold text-tick-xanh">Thông tin</span>
                        </div>
                        <div class="w-8 h-px bg-tick-xanh"></div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-6 h-6 rounded-full bg-tertiary text-white text-[11px] font-bold flex items-center justify-center animate-pulse">2</div>
                            <span class="text-[11px] font-bold text-tertiary">Xác thực</span>
                        </div>
                    </div>

                    <div class="flex flex-col items-center text-center space-y-3">
                        <div class="w-16 h-16 bg-gradient-to-br from-tertiary/15 to-tick-xanh/10 rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-tertiary text-4xl" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-on-surface">Xác thực tài khoản</h3>
                        <p class="text-[13px] text-on-surface-variant leading-relaxed">Mã OTP đã được gửi đến email, hiệu lực trong <span class="font-bold text-on-surface">3 phút</span></p>
                    </div>
                    <div class="flex justify-center gap-2.5 px-2">
                        <input class="otp-input w-12 h-14 text-center text-xl font-black bg-surface-container-lowest border-2 border-outline-variant/40 rounded-xl focus:border-tertiary focus:ring-4 focus:ring-tertiary/10 outline-none transition-all text-on-surface" maxlength="1" type="text" inputmode="numeric"/>
                        <input class="otp-input w-12 h-14 text-center text-xl font-black bg-surface-container-lowest border-2 border-outline-variant/40 rounded-xl focus:border-tertiary focus:ring-4 focus:ring-tertiary/10 outline-none transition-all text-on-surface" maxlength="1" type="text" inputmode="numeric"/>
                        <input class="otp-input w-12 h-14 text-center text-xl font-black bg-surface-container-lowest border-2 border-outline-variant/40 rounded-xl focus:border-tertiary focus:ring-4 focus:ring-tertiary/10 outline-none transition-all text-on-surface" maxlength="1" type="text" inputmode="numeric"/>
                        <div class="w-3 flex items-center justify-center"><div class="w-1.5 h-1.5 rounded-full bg-outline-variant/40"></div></div>
                        <input class="otp-input w-12 h-14 text-center text-xl font-black bg-surface-container-lowest border-2 border-outline-variant/40 rounded-xl focus:border-tertiary focus:ring-4 focus:ring-tertiary/10 outline-none transition-all text-on-surface" maxlength="1" type="text" inputmode="numeric"/>
                        <input class="otp-input w-12 h-14 text-center text-xl font-black bg-surface-container-lowest border-2 border-outline-variant/40 rounded-xl focus:border-tertiary focus:ring-4 focus:ring-tertiary/10 outline-none transition-all text-on-surface" maxlength="1" type="text" inputmode="numeric"/>
                        <input class="otp-input w-12 h-14 text-center text-xl font-black bg-surface-container-lowest border-2 border-outline-variant/40 rounded-xl focus:border-tertiary focus:ring-4 focus:ring-tertiary/10 outline-none transition-all text-on-surface" maxlength="1" type="text" inputmode="numeric"/>
                    </div>
                    <div class="space-y-3">
                        <p class="text-error text-xs text-red-600 hidden text-center" id="error-otp"></p>
                        <button class="w-full h-[52px] bg-tertiary text-on-tertiary font-bold text-[15px] rounded-2xl shadow-[0_4px_16px_rgba(0,110,47,0.25)] hover:shadow-[0_8px_24px_rgba(0,110,47,0.35)] hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2" id="verify-btn" onclick="submitOTPVerification()">
                            <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                            Xác thực ngay
                        </button>
                        <button class="w-full h-[40px] bg-transparent text-on-surface-variant font-bold text-[14px] rounded-2xl hover:bg-surface-container-highest active:scale-[0.98] transition-all duration-200 flex items-center justify-center" onclick="window.location.href = '/'">
                            Bỏ qua, tôi sẽ xác thực sau
                        </button>
                        <p class="text-center text-[13px] text-on-surface-variant" id="timer-container">
                            Gửi lại mã (sau <span class="font-bold text-on-surface" id="timer">60</span>s)
                        </p>
                    </div>
                    <button class="absolute top-5 right-5 p-2 rounded-full hover:bg-surface-container-high/60 text-on-surface-variant active:scale-90 transition-all" onclick="hideOTP()">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('styles')
    <style>
        @keyframes floatSlow {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -20px) scale(1.05); }
            66% { transform: translate(-15px, 15px) scale(0.95); }
        }
        @keyframes floatSlowReverse {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(-25px, 15px) scale(0.95); }
            66% { transform: translate(20px, -25px) scale(1.05); }
        }
        .animate-float-slow { animation: floatSlow 12s ease-in-out infinite; }
        .animate-float-slow-reverse { animation: floatSlowReverse 15s ease-in-out infinite; }
        .animate-spin { animation: spin 1s linear infinite; }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let countdownTimer;

        function clearErrors() {
            document.querySelectorAll('.text-error').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
        }

        function showError(field, message) {
            const el = document.getElementById(`error-${field}`);
            if (el) {
                el.textContent = message;
                el.classList.remove('hidden');
            }
        }

        function getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function togglePw() {
            const input = document.getElementById('mat_khau');
            const icon = document.getElementById('pw-toggle-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        async function submitRegistration() {
            clearErrors();
            const submitBtn = document.getElementById('register-submit-btn');
            const originalBtnHtml = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Đang tạo tài khoản...';

            const ho_ten = document.getElementById('ho_ten').value;
            const email = document.getElementById('email').value;
            const mat_khau = document.getElementById('mat_khau').value;

            try {
                const response = await fetch('/dang-ky', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ ho_ten, email, mat_khau })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            showError(key, data.errors[key][0]);
                        });
                    } else {
                        showError('general', data.message || 'Đã xảy ra lỗi, vui lòng thử lại.');
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                    return;
                }

                showOTP(email);
            } catch (error) {
                showError('general', 'Không thể kết nối với hệ thống.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }

        function showOTP(email) {
            window.registrationEmail = email;
            const otpSection = document.getElementById('otp-section');
            otpSection.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            const inputs = otpSection.querySelectorAll('.otp-input');
            inputs.forEach(input => input.value = '');
            if (inputs[0]) inputs[0].focus();

            startTimer();
        }

        function hideOTP() {
            const otpSection = document.getElementById('otp-section');
            otpSection.classList.add('hidden');
            document.body.style.overflow = '';

            const submitBtn = document.getElementById('register-submit-btn');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-[20px]">person_add</span> Đăng ký ngay';
        }

        async function submitOTPVerification() {
            const errorOtp = document.getElementById('error-otp');
            errorOtp.classList.add('hidden');

            const btn = document.getElementById('verify-btn');
            const originalBtnHtml = btn.innerHTML;

            const otpSection = document.getElementById('otp-section');
            const inputs = otpSection.querySelectorAll('.otp-input');

            let otp = '';
            inputs.forEach(input => { otp += input.value.trim(); });

            if (otp.length !== 6) {
                errorOtp.textContent = 'Vui lòng nhập đủ 6 chữ số OTP.';
                errorOtp.classList.remove('hidden');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Đang xác thực...';

            try {
                const response = await fetch('/dang-ky/xac-thuc', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        email: window.registrationEmail,
                        otp: otp
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    errorOtp.textContent = data.message || 'Mã OTP không chính xác.';
                    errorOtp.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                    return;
                }

                btn.classList.remove('bg-tertiary');
                btn.classList.add('bg-tick-xanh');
                btn.innerHTML = '<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: \'FILL\' 1;">check_circle</span> Thành công!';
                setTimeout(() => { window.location.href = '/'; }, 1200);
            } catch (error) {
                errorOtp.textContent = 'Lỗi kết nối máy chủ.';
                errorOtp.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
            }
        }

        async function resendOTP(event) {
            if (event) event.preventDefault();

            const errorOtp = document.getElementById('error-otp');
            errorOtp.classList.add('hidden');

            const timerContainer = document.getElementById('timer-container');
            timerContainer.innerHTML = '<span class="animate-pulse">Đang gửi lại...</span>';

            try {
                const response = await fetch('/dang-ky/gui-lai', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ email: window.registrationEmail })
                });

                const data = await response.json();

                if (!response.ok) {
                    errorOtp.textContent = data.message || 'Không thể gửi lại mã OTP.';
                    errorOtp.classList.remove('hidden');
                    timerContainer.innerHTML = '<a href="#" onclick="resendOTP(event)" class="text-primary font-bold hover:underline">Gửi lại mã ngay</a>';
                    return;
                }

                startTimer();
            } catch (error) {
                errorOtp.textContent = 'Lỗi kết nối máy chủ.';
                errorOtp.classList.remove('hidden');
                timerContainer.innerHTML = '<a href="#" onclick="resendOTP(event)" class="text-primary font-bold hover:underline">Gửi lại mã ngay</a>';
            }
        }

        function startTimer() {
            if (countdownTimer) clearInterval(countdownTimer);

            let timeLeft = 60;
            const timerContainer = document.getElementById('timer-container');
            timerContainer.innerHTML = 'Gửi lại mã (sau <span class="font-bold text-on-surface" id="timer">60</span>s)';

            countdownTimer = setInterval(() => {
                timeLeft--;
                const dynamicTimerEl = document.getElementById('timer');
                if (dynamicTimerEl) dynamicTimerEl.textContent = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    timerContainer.innerHTML = '<a href="#" onclick="resendOTP(event)" class="text-primary font-bold hover:underline inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">refresh</span>Gửi lại mã ngay</a>';
                }
            }, 1000);
        }

        // OTP auto-focus logic
        const otpInputs = document.querySelectorAll('#otp-section .otp-input');
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                if (index === otpInputs.length - 1 && e.target.value.length === 1) {
                    let otp = '';
                    otpInputs.forEach(inp => otp += inp.value);
                    if (otp.length === 6) submitOTPVerification();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                    otpInputs[index - 1].select();
                }
            });
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                if (pasteData.length >= 6) {
                    otpInputs.forEach((inp, i) => { inp.value = pasteData[i] || ''; });
                    otpInputs[Math.min(5, pasteData.length - 1)].focus();
                    if (pasteData.length >= 6) submitOTPVerification();
                }
            });
            input.addEventListener('focus', () => input.select());
        });
    </script>
@endpush
