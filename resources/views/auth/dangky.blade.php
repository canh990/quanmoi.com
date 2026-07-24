@extends('layouts.app')

@section('title', 'Quán Mới - Đăng ký tài khoản')

@section('content')
    <!-- Main Content Area -->
    <main class="flex-grow pt-20 pb-24 px-container-margin max-w-md mx-auto w-full">
        <!-- Registration Form (Initial State) -->
        <div class="space-y-stack-lg transition-opacity duration-300" id="registration-section">
            <div class="space-y-base">
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Tham gia cộng đồng</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Khám phá và đánh giá những quán ăn địa phương uy tín.</p>
            </div>
            <form class="space-y-stack-md" id="register-form" onsubmit="event.preventDefault(); submitRegistration();">
                <div class="space-y-base">
                    <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Họ và Tên</label>
                    <input id="ho_ten" name="ho_ten" class="w-full h-12 px-4 rounded-xl border border-outline-variant bg-surface focus:border-secondary outline-none transition-all font-body-lg text-body-lg text-main" placeholder="Nguyễn Văn A" required="" type="text"/>
                    <p class="text-error text-xs text-red-600 hidden" id="error-ho_ten"></p>
                </div>
                <div class="space-y-base">
                    <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Email</label>
                    <input id="email" name="email" class="w-full h-12 px-4 rounded-xl border border-outline-variant bg-surface focus:border-secondary outline-none transition-all font-body-lg text-body-lg text-main" placeholder="email@example.com" required="" type="email"/>
                    <p class="text-error text-xs text-red-600 hidden" id="error-email"></p>
                </div>
                <div class="space-y-base">
                    <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Mật khẩu</label>
                    <input id="mat_khau" name="mat_khau" class="w-full h-12 px-4 rounded-xl border border-outline-variant bg-surface focus:border-secondary outline-none transition-all font-body-lg text-body-lg text-main" placeholder="••••••••" required="" type="password"/>
                    <p class="text-error text-xs text-red-600 hidden" id="error-mat_khau"></p>
                </div>
                <div class="pt-2">
                    <p class="text-error text-sm text-red-600 hidden text-center" id="error-general"></p>
                </div>
                <div class="pt-4">
                    <button id="register-submit-btn" class="w-full h-14 bg-primary-container text-on-primary font-bold text-body-lg rounded-xl shadow-lg hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2" type="submit">
                        Đăng ký ngay
                    </button>
                </div>
            </form>
            <p class="text-center font-body-sm text-body-sm text-on-surface-variant">
                Bạn đã có tài khoản? <a class="text-primary font-bold hover:underline" href="{{ route('login') }}">Đăng nhập</a>
            </p>
        </div>

        <!-- OTP Overlay (Hidden by default) -->
        <div class="hidden fixed inset-0 z-[60] flex items-center justify-center px-container-margin" id="otp-section">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            <!-- OTP Modal -->
            <div class="relative bg-surface rounded-3xl w-full max-w-sm p-8 shadow-2xl space-y-stack-lg animate-in fade-in zoom-in duration-300">
                <div class="flex flex-col items-center text-center space-y-stack-sm">
                    <div class="w-16 h-16 bg-tertiary-container/20 rounded-full flex items-center justify-center mb-2">
                        <span class="material-symbols-outlined text-tertiary text-4xl" data-icon="verified" style="font-variation-settings: 'FILL' 1;">verified</span>
                    </div>
                    <h3 class="font-headline-lg text-headline-lg text-on-surface">Xác thực tick xanh</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant px-4">Mã OTP đã được gửi đến email của bạn, hiệu lực trong 3 phút</p>
                </div>
                <div class="flex justify-between gap-2 px-2">
                    <input class="otp-input w-11 h-14 text-center text-headline-lg font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-secondary outline-none transition-all text-main" maxlength="1" type="text"/>
                    <input class="otp-input w-11 h-14 text-center text-headline-lg font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-secondary outline-none transition-all text-main" maxlength="1" type="text"/>
                    <input class="otp-input w-11 h-14 text-center text-headline-lg font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-secondary outline-none transition-all text-main" maxlength="1" type="text"/>
                    <input class="otp-input w-11 h-14 text-center text-headline-lg font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-secondary outline-none transition-all text-main" maxlength="1" type="text"/>
                    <input class="otp-input w-11 h-14 text-center text-headline-lg font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-secondary outline-none transition-all text-main" maxlength="1" type="text"/>
                    <input class="otp-input w-11 h-14 text-center text-headline-lg font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-secondary outline-none transition-all text-main" maxlength="1" type="text"/>
                </div>
                <div class="space-y-stack-md">
                    <p class="text-error text-xs text-red-600 hidden text-center" id="error-otp"></p>
                    <button class="w-full h-14 bg-tertiary text-on-tertiary font-bold text-body-lg rounded-xl shadow-md hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2" id="verify-btn" onclick="submitOTPVerification()">
                        Xác thực ngay
                    </button>
                    <p class="text-center font-body-sm text-body-sm text-on-surface-variant" id="timer-container">
                        Gửi lại mã (sau <span class="font-bold" id="timer">60</span>s)
                    </p>
                </div>
                <button class="absolute top-4 right-4 p-1 rounded-full hover:bg-surface-container text-on-surface-variant" onclick="hideOTP()">
                    <span class="material-symbols-outlined" data-icon="close">close</span>
                </button>
            </div>
        </div>
    </main>
@endsection


@push('scripts')
    <script>
        let countdownTimer;

        // Clear error messages
        function clearErrors() {
            document.querySelectorAll('.text-error').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
        }

        // Show error message helper
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

        async function submitRegistration() {
            clearErrors();
            const submitBtn = document.getElementById('register-submit-btn');
            const originalBtnHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin" data-icon="progress_activity">progress_activity</span> Đang gửi...';

            const ho_ten = document.getElementById('ho_ten').value;
            const email = document.getElementById('email').value;
            const mat_khau = document.getElementById('mat_khau').value;

            try {
                const response = await fetch('/dangky', {
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

                // Show OTP modal on success
                showOTP(email);
            } catch (error) {
                showError('general', 'Không thể kết nối với hệ thống.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }

        function showOTP(email) {
            // Keep reference to email
            window.registrationEmail = email;
            const otpSection = document.getElementById('otp-section');
            otpSection.classList.remove('hidden');
            
            // Clear inputs
            inputs.forEach(input => input.value = '');
            if (inputs[0]) inputs[0].focus();
            
            startTimer();
        }

        function hideOTP() {
            const otpSection = document.getElementById('otp-section');
            otpSection.classList.add('hidden');
            
            // Re-enable register submit button
            const submitBtn = document.getElementById('register-submit-btn');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Đăng ký ngay';
        }

        async function submitOTPVerification() {
            const errorOtp = document.getElementById('error-otp');
            errorOtp.classList.add('hidden');
            
            const btn = document.getElementById('verify-btn');
            const originalBtnHtml = btn.innerHTML;

            // Gather OTP code
            let otp = '';
            inputs.forEach(input => {
                otp += input.value.trim();
            });

            if (otp.length !== 6) {
                errorOtp.textContent = 'Vui lòng nhập đủ 6 chữ số OTP.';
                errorOtp.classList.remove('hidden');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin" data-icon="progress_activity">progress_activity</span>';

            try {
                const response = await fetch('/dangky/xac-thuc', {
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

                // Successful verification
                btn.innerHTML = '<span class="material-symbols-outlined" data-icon="check_circle">check_circle</span> Thành công!';
                setTimeout(() => {
                    window.location.href = '/';
                }, 1000);
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
            timerContainer.innerHTML = 'Đang gửi lại...';

            try {
                const response = await fetch('/dangky/gui-lai', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        email: window.registrationEmail
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    errorOtp.textContent = data.message || 'Không thể gửi lại mã OTP.';
                    errorOtp.classList.remove('hidden');
                    timerContainer.innerHTML = '<a href="#" onclick="resendOTP(event)" class="text-primary font-bold">Gửi lại mã ngay</a>';
                    return;
                }

                // Start countdown again
                startTimer();
            } catch (error) {
                errorOtp.textContent = 'Lỗi kết nối máy chủ.';
                errorOtp.classList.remove('hidden');
                timerContainer.innerHTML = '<a href="#" onclick="resendOTP(event)" class="text-primary font-bold">Gửi lại mã ngay</a>';
            }
        }

        function startTimer() {
            if (countdownTimer) clearInterval(countdownTimer);
            
            let timeLeft = 60;
            const timerContainer = document.getElementById('timer-container');
            timerContainer.innerHTML = 'Gửi lại mã (sau <span class="font-bold" id="timer">60</span>s)';
            
            const timerEl = document.getElementById('timer');
            countdownTimer = setInterval(() => {
                timeLeft--;
                // Wait for DOM to ensure element is there
                const dynamicTimerEl = document.getElementById('timer');
                if (dynamicTimerEl) {
                    dynamicTimerEl.textContent = timeLeft;
                }
                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    timerContainer.innerHTML = '<a href="#" onclick="resendOTP(event)" class="text-primary font-bold">Gửi lại mã ngay</a>';
                }
            }, 1000);
        }

        // Auto-focus logic for OTP inputs
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
@endpush
