@extends('layouts.app')

@section('title', 'Đăng nhập & Đăng ký tài khoản | Quán Mới')

@push('styles')
    {{-- SEO Meta tags & OpenGraph --}}
    <meta name="description" content="Đăng nhập tài khoản Quán Mới để lưu các quán ăn yêu thích, nhận ưu đãi hấp dẫn và đóng góp đánh giá ẩm thực cùng cộng đồng." />
    <meta name="keywords" content="đăng nhập quán mới, đăng ký tài khoản quán mới, review quán ăn, địa điểm ẩm thực" />
    <meta property="og:title" content="Đăng nhập & Đăng ký tài khoản - Quán Mới" />
    <meta property="og:description" content="Khám phá tinh hoa ẩm thực địa phương, lưu địa điểm yêu thích và chia sẻ trải nghiệm của bạn." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />

    <style>
        .auth-section {
            animation: sectionFadeIn 0.3s ease-out;
        }
        @keyframes sectionFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

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

        @keyframes successBounce {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.1); }
            70% { transform: scale(0.95); }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-success-bounce { animation: successBounce 0.6s ease-out; }

        .otp-input:not(:placeholder-shown):not(:focus) {
            border-color: rgba(0, 110, 47, 0.4);
            background-color: rgba(0, 110, 47, 0.03);
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
@endpush

@section('content')
    <main class="flex-grow pt-20 md:pt-24 pb-24 flex flex-col items-center justify-center min-h-[85vh] relative overflow-hidden px-4">

        {{-- Animated Background --}}
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-fixed/30 via-background to-secondary-fixed/20"></div>
            <div class="absolute top-20 -left-20 w-72 h-72 bg-primary/8 rounded-full blur-3xl animate-float-slow"></div>
            <div class="absolute bottom-20 -right-20 w-80 h-80 bg-tertiary/6 rounded-full blur-3xl animate-float-slow-reverse"></div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, #a04100 1px, transparent 1px); background-size: 32px 32px;"></div>
        </div>

        {{-- Header Intro Text for SEO & UX --}}
        <div class="text-center space-y-2 mb-6 max-w-md">
            <div class="inline-flex items-center gap-1.5 bg-primary/10 text-primary px-3.5 py-1 rounded-full text-xs font-bold">
                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">restaurant</span>
                Cộng đồng ẩm thực Quán Mới
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-on-surface">Đăng nhập tài khoản</h1>
            <p class="text-on-surface-variant text-[14px]">Khám phá, đánh giá và lưu các quán ăn yêu thích</p>
        </div>

        {{-- ╔══════════════════════════════════════════════════════════════╗ --}}
        {{-- ║   STANDALONE AUTH CARD (Được hiển thị trực tiếp ngoài SEO)   ║ --}}
        {{-- ╚══════════════════════════════════════════════════════════════╝ --}}
        <div class="w-full max-w-[420px] relative z-10">
            <div class="bg-white rounded-[28px] shadow-[0_20px_50px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden">

                {{-- Decorative top gradient bar --}}
                <div class="h-1.5 bg-gradient-to-r from-primary via-tertiary to-secondary"></div>

                <div class="p-6 md:p-8">

                    {{-- ================ SECTION 1: ĐĂNG NHẬP ================ --}}
                    <div id="section-login" class="auth-section">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-11 h-11 bg-primary/10 rounded-2xl flex items-center justify-center text-primary flex-shrink-0">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">waving_hand</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-extrabold text-on-surface leading-tight">Chào mừng trở lại</h2>
                                <p class="text-[13px] text-on-surface-variant">Nhập thông tin để đăng nhập</p>
                            </div>
                        </div>

                        <form class="space-y-4" onsubmit="event.preventDefault(); submitLogin();" method="POST" action="/dang-nhap">
                            @csrf
                            {{-- Email --}}
                            <div class="space-y-1.5">
                                <label for="login-email" class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Email</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">mail</span>
                                    <input id="login-email" name="email" class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-gray-400" placeholder="email@example.com" required type="email"/>
                                </div>
                                <p class="text-error text-xs text-red-600 hidden" id="error-login-email"></p>
                            </div>

                            {{-- Password --}}
                            <div class="space-y-1.5">
                                <label for="login-password" class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Mật khẩu</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">lock</span>
                                    <input id="login-password" name="mat_khau" class="w-full h-12 pl-11 pr-12 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-gray-400" placeholder="••••••••" required type="password"/>
                                    <button type="button" onclick="togglePassword('login-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                </div>
                                <p class="text-error text-xs text-red-600 hidden" id="error-login-password"></p>
                            </div>

                            {{-- Remember / Forgot --}}
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 cursor-pointer select-none group">
                                    <input id="login-remember" name="remember" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer"/>
                                    <span class="text-[13px] text-on-surface-variant group-hover:text-on-surface transition-colors">Ghi nhớ đăng nhập</span>
                                </label>
                                <a href="#" class="text-primary font-bold text-[13px] hover:underline">Quên mật khẩu?</a>
                            </div>

                            {{-- General error --}}
                            <p class="text-error text-sm text-red-600 hidden text-center" id="error-login-general"></p>

                            {{-- Submit --}}
                            <button id="login-submit-btn" class="w-full h-[52px] bg-primary text-white font-bold text-[15px] rounded-2xl shadow-[0_4px_16px_rgba(160,65,0,0.25)] hover:shadow-[0_8px_24px_rgba(160,65,0,0.35)] hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2" type="submit">
                                <span class="material-symbols-outlined text-[20px]">login</span>
                                Đăng nhập
                            </button>
                        </form>

                        {{-- Divider --}}
                        <div class="flex items-center gap-3 my-5">
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <span class="text-[11px] text-gray-400 uppercase tracking-widest font-bold">hoặc</span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>

                        <p class="text-center text-[14px] text-on-surface-variant">
                            Bạn chưa có tài khoản?
                            <button class="text-primary font-bold hover:underline ml-1" onclick="switchSection('register')">Đăng ký ngay</button>
                        </p>
                    </div>

                    {{-- ================ SECTION 2: ĐĂNG KÝ ================ --}}
                    <div id="section-register" class="auth-section hidden">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-11 h-11 bg-tertiary/10 rounded-2xl flex items-center justify-center text-tertiary flex-shrink-0">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">how_to_reg</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-extrabold text-on-surface leading-tight">Tạo tài khoản mới</h2>
                                <p class="text-[13px] text-on-surface-variant">Bắt đầu chỉ trong vài giây</p>
                            </div>
                        </div>

                        {{-- Progress steps --}}
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-primary text-white text-[11px] font-bold flex items-center justify-center">1</div>
                                <span class="text-[11px] font-bold text-primary">Thông tin</span>
                            </div>
                            <div class="w-8 h-px bg-gray-200"></div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-gray-200 text-gray-400 text-[11px] font-bold flex items-center justify-center">2</div>
                                <span class="text-[11px] font-medium text-gray-400">Xác thực</span>
                            </div>
                        </div>

                        <form class="space-y-3.5" onsubmit="event.preventDefault(); submitRegister();" method="POST" action="/dang-ky">
                            @csrf
                            {{-- Name --}}
                            <div class="space-y-1.5">
                                <label for="register-name" class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Họ và tên</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">person</span>
                                    <input id="register-name" name="ho_ten" class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-gray-400" placeholder="Nguyễn Văn A" required type="text"/>
                                </div>
                                <p class="text-error text-xs text-red-600 hidden" id="error-register-name"></p>
                            </div>

                            {{-- Email --}}
                            <div class="space-y-1.5">
                                <label for="register-email" class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Email</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">mail</span>
                                    <input id="register-email" name="email" class="w-full h-12 pl-11 pr-4 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-gray-400" placeholder="email@example.com" required type="email"/>
                                </div>
                                <p class="text-error text-xs text-red-600 hidden" id="error-register-email"></p>
                            </div>

                            {{-- Password --}}
                            <div class="space-y-1.5">
                                <label for="register-password" class="text-[12px] font-bold text-on-surface-variant uppercase tracking-wider">Mật khẩu</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/50 group-focus-within:text-primary transition-colors">lock</span>
                                    <input id="register-password" name="mat_khau" class="w-full h-12 pl-11 pr-12 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-[15px] placeholder:text-gray-400" placeholder="Tối thiểu 6 ký tự" required type="password" minlength="6"/>
                                    <button type="button" onclick="togglePassword('register-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                </div>
                                <p class="text-error text-xs text-red-600 hidden" id="error-register-password"></p>
                            </div>

                            {{-- General error --}}
                            <p class="text-error text-sm text-red-600 hidden text-center" id="error-register-general"></p>

                            {{-- Submit --}}
                            <button id="register-submit-btn" class="w-full h-[52px] bg-primary text-white font-bold text-[15px] rounded-2xl shadow-[0_4px_16px_rgba(160,65,0,0.25)] hover:shadow-[0_8px_24px_rgba(160,65,0,0.35)] hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 mt-2" type="submit">
                                <span class="material-symbols-outlined text-[20px]">person_add</span>
                                Đăng ký ngay
                            </button>
                        </form>

                        {{-- Divider --}}
                        <div class="flex items-center gap-3 my-5">
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <span class="text-[11px] text-gray-400 uppercase tracking-widest font-bold">hoặc</span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>

                        <p class="text-center text-[14px] text-on-surface-variant">
                            Đã có tài khoản?
                            <button class="text-primary font-bold hover:underline ml-1" onclick="switchSection('login')">Đăng nhập</button>
                        </p>
                    </div>

                    {{-- ================ SECTION 3: XÁC THỰC OTP ================ --}}
                    <div id="section-otp" class="auth-section hidden">
                        {{-- Progress steps --}}
                        <div class="flex items-center justify-center gap-2 mb-5">
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
                            <div class="w-16 h-16 bg-tertiary/10 rounded-2xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-tertiary text-4xl" style="font-variation-settings: 'FILL' 1;">verified</span>
                            </div>
                            <h3 class="text-xl font-extrabold text-on-surface">Kích hoạt tài khoản</h3>
                            <p class="text-[13px] text-on-surface-variant leading-relaxed">
                                Chúng tôi đã gửi mã xác thực <span class="font-bold text-on-surface">6 chữ số</span> đến email
                                <span class="font-bold text-primary block" id="otp-email-display"></span>
                            </p>
                        </div>

                        {{-- OTP Inputs --}}
                        <div class="flex justify-center gap-2 mt-6 mb-2">
                            <input class="otp-input w-11 h-13 text-center text-xl font-black bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-tertiary focus:ring-2 focus:ring-tertiary/10 outline-none transition-all" maxlength="1" type="text" inputmode="numeric"/>
                            <input class="otp-input w-11 h-13 text-center text-xl font-black bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-tertiary focus:ring-2 focus:ring-tertiary/10 outline-none transition-all" maxlength="1" type="text" inputmode="numeric"/>
                            <input class="otp-input w-11 h-13 text-center text-xl font-black bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-tertiary focus:ring-2 focus:ring-tertiary/10 outline-none transition-all" maxlength="1" type="text" inputmode="numeric"/>
                            <input class="otp-input w-11 h-13 text-center text-xl font-black bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-tertiary focus:ring-2 focus:ring-tertiary/10 outline-none transition-all" maxlength="1" type="text" inputmode="numeric"/>
                            <input class="otp-input w-11 h-13 text-center text-xl font-black bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-tertiary focus:ring-2 focus:ring-tertiary/10 outline-none transition-all" maxlength="1" type="text" inputmode="numeric"/>
                            <input class="otp-input w-11 h-13 text-center text-xl font-black bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-tertiary focus:ring-2 focus:ring-tertiary/10 outline-none transition-all" maxlength="1" type="text" inputmode="numeric"/>
                        </div>

                        <div class="space-y-3 mt-4">
                            <p class="text-error text-xs text-red-600 hidden text-center" id="error-otp"></p>
                            <button class="w-full h-[52px] bg-tertiary text-white font-bold text-[15px] rounded-2xl shadow-md hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-2" id="verify-btn" onclick="submitOTPVerification()">
                                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                                Xác thực ngay
                            </button>
                            <p class="text-center text-[13px] text-on-surface-variant" id="timer-container">
                                Gửi lại mã (sau <span class="font-bold text-on-surface" id="timer">60</span>s)
                            </p>
                        </div>
                    </div>

                    {{-- ================ SECTION 4: THÀNH CÔNG ================ --}}
                    <div id="section-success" class="auth-section hidden">
                        <div class="flex flex-col items-center text-center space-y-4 py-6">
                            <div class="w-20 h-20 bg-tick-xanh/15 rounded-full flex items-center justify-center animate-success-bounce">
                                <span class="material-symbols-outlined text-tick-xanh text-5xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            </div>
                            <h3 class="text-2xl font-black text-on-surface">Chào mừng bạn! 🎉</h3>
                            <p class="text-[14px] text-on-surface-variant leading-relaxed max-w-xs">
                                Tài khoản đã được kích hoạt thành công. Đang chuyển bạn đến trang chủ...
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
        let countdownTimer;

        // Swapping sections logic
        function switchSection(sectionName) {
            clearErrors();
            document.querySelectorAll('.auth-section').forEach(s => s.classList.add('hidden'));

            const target = document.getElementById(`section-${sectionName}`);
            if (target) {
                target.classList.remove('hidden');
            }

            if (sectionName === 'login') {
                setTimeout(() => document.getElementById('login-email')?.focus(), 100);
            } else if (sectionName === 'register') {
                setTimeout(() => document.getElementById('register-name')?.focus(), 100);
            } else if (sectionName === 'otp') {
                const emailDisplay = document.getElementById('otp-email-display');
                if (emailDisplay && window.registrationEmail) {
                    emailDisplay.textContent = window.registrationEmail;
                }
                setTimeout(() => {
                    const firstInput = document.querySelector('#section-otp .otp-input');
                    if (firstInput) firstInput.focus();
                }, 100);
            }
        }

        function clearErrors() {
            document.querySelectorAll('.text-error').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
        }

        function showLoginError(field, message) {
            const el = document.getElementById(`error-login-${field}`);
            if (el) {
                el.textContent = message;
                el.classList.remove('hidden');
            }
        }

        function showRegisterError(field, message) {
            const el = document.getElementById(`error-register-${field}`);
            if (el) {
                el.textContent = message;
                el.classList.remove('hidden');
            }
        }

        function getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('.material-symbols-outlined');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        async function submitLogin() {
            clearErrors();
            const submitBtn = document.getElementById('login-submit-btn');
            const originalBtnHtml = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Đang xử lý...';

            const email = document.getElementById('login-email').value;
            const mat_khau = document.getElementById('login-password').value;
            const remember = document.getElementById('login-remember').checked;

            try {
                const response = await fetch('/dang-nhap', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ email, mat_khau, remember })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 403 && data.needs_verification) {
                        window.registrationEmail = data.email;
                        switchSection('otp');
                        startTimer();
                    } else if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            showLoginError(key, data.errors[key][0]);
                        });
                    } else {
                        showLoginError('general', data.message || 'Email hoặc mật khẩu không chính xác.');
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                    return;
                }

                submitBtn.innerHTML = '<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: \'FILL\' 1;">check_circle</span> Thành công!';
                submitBtn.classList.remove('bg-primary');
                submitBtn.classList.add('bg-tick-xanh');
                setTimeout(() => {
                    window.location.href = data.redirect_to || '/';
                }, 500);
            } catch (error) {
                showLoginError('general', 'Không thể kết nối máy chủ.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }

        async function submitRegister() {
            clearErrors();
            const submitBtn = document.getElementById('register-submit-btn');
            const originalBtnHtml = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Đang tạo tài khoản...';

            const name = document.getElementById('register-name').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;

            try {
                const response = await fetch('/dang-ky', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ ho_ten: name, email: email, mat_khau: password })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        if (data.errors.ho_ten) showRegisterError('name', data.errors.ho_ten[0]);
                        if (data.errors.email) showRegisterError('email', data.errors.email[0]);
                        if (data.errors.mat_khau) showRegisterError('password', data.errors.mat_khau[0]);
                    } else {
                        showRegisterError('general', data.message || 'Gặp lỗi trong quá trình tạo tài khoản.');
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                    return;
                }

                submitBtn.innerHTML = '<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: \'FILL\' 1;">check_circle</span> Thành công!';
                submitBtn.classList.remove('bg-primary');
                submitBtn.classList.add('bg-tick-xanh');

                switchSection('success');
                setTimeout(() => {
                    window.location.href = data.redirect_to || '/';
                }, 1200);
            } catch (error) {
                showRegisterError('general', 'Không thể kết nối máy chủ.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }

        async function submitOTPVerification() {
            const errorOtp = document.getElementById('error-otp');
            errorOtp.classList.add('hidden');

            const btn = document.getElementById('verify-btn');
            const originalBtnHtml = btn.innerHTML;

            let otp = '';
            otpInputs.forEach(input => { otp += input.value.trim(); });

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

                switchSection('success');
                setTimeout(() => { window.location.href = '/'; }, 2000);
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
            timerContainer.innerHTML = '<span class="text-gray-400 animate-pulse">Đang gửi lại...</span>';

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

                otpInputs.forEach(input => input.value = '');
                if (otpInputs[0]) otpInputs[0].focus();
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

        const otpInputs = document.querySelectorAll('#section-otp .otp-input');
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