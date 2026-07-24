@extends('layouts.app')

@section('title', 'Quán Mới - Đăng nhập & Đăng ký')

@section('header')
    {{-- ╔══════════════════════════════════════════════════════════════╗ --}}
    {{-- ║   DESKTOP — Edge-to-Edge Glassmorphic Header (Tràn viền)     ║ --}}
    {{-- ╚══════════════════════════════════════════════════════════════╝ --}}
    <header class="hidden md:block fixed top-0 left-0 right-0 w-full z-50 bg-white/75 dark:bg-on-surface-variant/10 backdrop-blur-md border-b border-white/40 dark:border-white/10 shadow-[0_4px_24px_rgba(160,65,0,0.03)] transition-all duration-300">
        <div class="w-full px-6 lg:px-10 h-18 py-3 flex justify-between items-center">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2 group">
                <span class="material-symbols-outlined text-primary text-3xl group-hover:rotate-12 transition-transform duration-300" style="font-variation-settings: 'FILL' 1;">restaurant</span>
                <span class="font-display-lg text-[24px] text-primary tracking-tight font-extrabold transition-all duration-300 group-hover:opacity-95">Quán Mới</span>
            </a>

            {{-- Nav Links --}}
            <nav class="flex items-center gap-1 bg-surface-container-low/60 p-1 rounded-full border border-outline-variant/15">
                <a class="nav-link px-4.5 py-1.5 rounded-full text-[14px] font-semibold transition-all duration-200 {{ request()->is('/') ? 'active text-primary bg-white shadow-sm' : 'text-on-surface-variant hover:text-primary hover:bg-primary/5' }}" href="/">
                    Khám phá
                </a>
                <a class="nav-link px-4.5 py-1.5 rounded-full text-[14px] font-semibold transition-all duration-200 text-on-surface-variant hover:text-primary hover:bg-primary/5" href="#">
                    Blog
                </a>
            </nav>

            {{-- Right Actions --}}
            <div class="flex items-center gap-3">
                <button onclick="showAuthModal('login')" class="px-4.5 py-2 rounded-full text-[14px] font-semibold text-primary bg-primary/5 hover:bg-primary/10 transition-all">Đăng nhập</button>
                <button onclick="showAuthModal('register')" class="px-5 py-2 rounded-full text-[14px] font-bold bg-primary text-white shadow-[0_4px_12px_rgba(160,65,0,0.2)] hover:shadow-[0_6px_20px_rgba(160,65,0,0.3)] transition-all">Đăng ký</button>
            </div>
        </div>
    </header>

    {{-- ╔══════════════════════════════════════════════════════════════╗ --}}
    {{-- ║   MOBILE — Sticky TopAppBar                                  ║ --}}
    {{-- ╚══════════════════════════════════════════════════════════════╝ --}}
    <header class="flex md:hidden justify-between items-center px-4 h-14 w-full fixed top-0 left-0 right-0 z-50 bg-surface/80 backdrop-blur-md border-b border-surface-container-high/40">
        <button class="p-2 rounded-full hover:bg-surface-container active:scale-90 transition-all" aria-label="Menu">
            <span class="material-symbols-outlined text-on-surface-variant text-[22px]">menu</span>
        </button>
        <a href="/" class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-primary text-xl" style="font-variation-settings: 'FILL' 1;">restaurant</span>
            <span class="font-headline-lg-mobile text-headline-lg-mobile font-black text-primary tracking-tight">Quán Mới</span>
        </a>
        <button class="p-2 rounded-full hover:bg-surface-container active:scale-90 transition-all" aria-label="Search">
            <span class="material-symbols-outlined text-on-surface-variant text-[22px]">search</span>
        </button>
    </header>
@endsection

@section('content')
    <main class="flex-grow pt-20 md:pt-28 pb-24 px-container-margin flex flex-col items-center justify-center min-h-[60vh]">
        {{-- Nút bấm mồi ở giữa trang dự phòng trường hợp modal đóng --}}
        <div class="text-center space-y-4">
            <h2 class="text-2xl font-black text-on-surface">Tham gia cùng Quán Mới</h2>
            <p class="text-on-surface-variant max-w-sm">Đăng nhập để lưu lại các quán ăn yêu thích và nhận nhiều ưu đãi hấp dẫn.</p>
            <div class="flex justify-center gap-4">
                <button onclick="showAuthModal('login')" class="px-6 py-3 rounded-full font-bold bg-primary text-white shadow-md hover:brightness-110 active:scale-95 transition-all">Đăng nhập ngay</button>
                <button onclick="showAuthModal('register')" class="px-6 py-3 rounded-full font-semibold border border-outline-variant hover:bg-surface-container-low active:scale-95 transition-all">Đăng ký tài khoản</button>
            </div>
        </div>

        {{-- ╔══════════════════════════════════════════════════════════════╗ --}}
        {{-- ║   SYSTEM PORTAL MODAL (Popup Đăng nhập / Đăng ký / OTP)      ║ --}}
        {{-- ╚══════════════════════════════════════════════════════════════╝ --}}
        <div id="auth-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
            {{-- Backdrop làm mờ hậu cảnh --}}
            <div class="absolute inset-0 bg-black/45 backdrop-blur-md transition-opacity duration-300" onclick="hideAuthModal()"></div>
            
            {{-- Thân Popup --}}
            <div class="relative bg-surface rounded-3xl w-full max-w-md p-6 md:p-8 shadow-2xl border border-outline-variant/20 transform transition-all duration-300 scale-95 opacity-0" id="auth-modal-content">
                
                {{-- Nút đóng popup --}}
                <button class="absolute top-4 right-4 p-2 rounded-full hover:bg-surface-container-high/60 text-on-surface-variant active:scale-90 transition-all" onclick="hideAuthModal()">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>

                {{-- ---------------- SECTION 1: ĐĂNG NHẬP ---------------- --}}
                <div id="section-login" class="space-y-6">
                    <div class="space-y-1">
                        <h2 class="font-headline-lg text-headline-lg text-on-surface font-extrabold">Chào mừng trở lại</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Khám phá tinh hoa ẩm thực địa phương cùng chúng tôi.</p>
                    </div>
                    <form class="space-y-4" onsubmit="event.preventDefault(); submitLogin();">
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-bold">Email</label>
                            <input id="login-email" class="w-full h-12 px-4 rounded-xl border border-outline-variant bg-surface focus:border-primary outline-none transition-all text-[15px]" placeholder="email@example.com" required type="email"/>
                            <p class="text-error text-xs text-red-600 hidden" id="error-email"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-bold">Mật khẩu</label>
                            <input id="login-password" class="w-full h-12 px-4 rounded-xl border border-outline-variant bg-surface focus:border-primary outline-none transition-all text-[15px]" placeholder="••••••••" required type="password"/>
                            <p class="text-error text-xs text-red-600 hidden" id="error-mat_khau"></p>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input id="login-remember" type="checkbox" class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary cursor-pointer"/>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Ghi nhớ đăng nhập</span>
                            </label>
                            <a href="#" class="text-primary font-bold text-body-sm hover:underline">Quên mật khẩu?</a>
                        </div>
                        <p class="text-error text-sm text-red-600 hidden text-center" id="error-general"></p>
                       <button id="register-submit-btn" class="w-full h-12 
                       bg-primary-container text-on-primary font-semibold 
                       text-body-md rounded-xl shadow-md hover:brightness-110
                        active:scale-95 transition-all flex items-center
                         justify-center gap-2" type="submit">
    Đăng nhập
</button>
                    </form>
                    <p class="text-center font-body-sm text-body-sm text-on-surface-variant">
                        Bạn chưa có tài khoản? <button class="text-primary font-bold hover:underline" onclick="switchSection('register')">Đăng ký ngay</button>
                    </p>
                </div>

                {{-- ---------------- SECTION 2: ĐĂNG KÝ ---------------- --}}
                <div id="section-register" class="space-y-6 hidden">
                    <div class="space-y-1">
                        <h2 class="font-headline-lg text-headline-lg text-on-surface font-extrabold">Tạo tài khoản mới</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Đăng ký thành viên chỉ trong vài bước đơn giản.</p>
                    </div>
                    <form class="space-y-4" onsubmit="event.preventDefault(); submitRegister();">
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-bold">Họ và tên</label>
                            <input id="register-name" class="w-full h-12 px-4 rounded-xl border border-outline-variant bg-surface focus:border-primary outline-none transition-all text-[15px]" placeholder="Nguyễn Văn A" required type="text"/>
                            <p class="text-error text-xs text-red-600 hidden" id="error-register-name"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-bold">Email</label>
                            <input id="register-email" class="w-full h-12 px-4 rounded-xl border border-outline-variant bg-surface focus:border-primary outline-none transition-all text-[15px]" placeholder="email@example.com" required type="email"/>
                            <p class="text-error text-xs text-red-600 hidden" id="error-register-email"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-bold">Mật khẩu</label>
                            <input id="register-password" class="w-full h-12 px-4 rounded-xl border border-outline-variant bg-surface focus:border-primary outline-none transition-all text-[15px]" placeholder="Tối thiểu 6 ký tự" required type="password"/>
                            <p class="text-error text-xs text-red-600 hidden" id="error-register-password"></p>
                        </div>
                        <p class="text-error text-sm text-red-600 hidden text-center" id="error-register-general"></p>
                    <button id="register-submit-btn" class="w-full h-12 bg-primary-container text-on-primary font-semibold text-body-md rounded-xl shadow-md hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2" type="submit">
    Đăng ký ngay
</button>
                    </form>
                    <p class="text-center font-body-sm text-body-sm text-on-surface-variant">
                        Đã có tài khoản? <button class="text-primary font-bold hover:underline" onclick="switchSection('login')">Đăng nhập</button>
                    </p>
                </div>

                {{-- ---------------- SECTION 3: XÁC THỰC OTP ---------------- --}}
                <div id="section-otp" class="space-y-6 hidden">
                    <div class="flex flex-col items-center text-center space-y-2">
                        <div class="w-14 h-14 bg-tertiary-container/20 rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-tertiary text-3xl" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                        <h3 class="font-headline-lg text-headline-lg text-on-surface font-extrabold">Kích hoạt tài khoản</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant px-4">Chúng tôi đã gửi mã xác thực 6 chữ số đến email của bạn.</p>
                    </div>
                    <div class="flex justify-between gap-2 px-1">
                        <input class="otp-input w-11 h-13 text-center text-xl font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-tertiary outline-none transition-all" maxlength="1" type="text"/>
                        <input class="otp-input w-11 h-13 text-center text-xl font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-tertiary outline-none transition-all" maxlength="1" type="text"/>
                        <input class="otp-input w-11 h-13 text-center text-xl font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-tertiary outline-none transition-all" maxlength="1" type="text"/>
                        <input class="otp-input w-11 h-13 text-center text-xl font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-tertiary outline-none transition-all" maxlength="1" type="text"/>
                        <input class="otp-input w-11 h-13 text-center text-xl font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-tertiary outline-none transition-all" maxlength="1" type="text"/>
                        <input class="otp-input w-11 h-13 text-center text-xl font-bold bg-surface-container-low border border-outline-variant rounded-xl focus:border-tertiary outline-none transition-all" maxlength="1" type="text"/>
                    </div>
                    <div class="space-y-4">
                        <p class="text-error text-xs text-red-600 hidden text-center" id="error-otp"></p>
                        <button class="w-full h-13 bg-tertiary text-on-tertiary font-bold rounded-xl shadow-md hover:brightness-110 active:scale-98 transition-all flex items-center justify-center gap-2" id="verify-btn" onclick="submitOTPVerification()">
                            Xác thực ngay
                        </button>
                        <p class="text-center font-body-sm text-body-sm text-on-surface-variant" id="timer-container">
                            Gửi lại mã (sau <span class="font-bold" id="timer">60</span>s)
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

@section('footer')
    {{-- ╔══════════════════════════════════════════════════════════════╗ --}}
    {{-- ║   MOBILE — Bottom Navigation Bar                             ║ --}}
    {{-- ╚══════════════════════════════════════════════════════════════╝ --}}
    <nav class="fixed bottom-0 left-0 w-full z-50 md:hidden">
        <div class="h-4 bg-gradient-to-t from-surface/90 to-transparent pointer-events-none"></div>
        <div class="bg-surface/90 backdrop-blur-md border-t border-surface-container-high/40 pb-safe h-[64px] flex justify-around items-center px-2">
            <a href="/" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center">
                <div class="px-5 py-1 rounded-full transition-all duration-300 text-on-surface-variant hover:bg-surface-container-high/50">
                    <span class="material-symbols-outlined text-[22px] block">explore</span>
                </div>
                <span class="text-[10px] font-bold mt-1 tracking-wide text-on-surface-variant">Khám phá</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center">
                <div class="px-5 py-1 rounded-full transition-all duration-300 text-on-surface-variant hover:bg-surface-container-high/50">
                    <span class="material-symbols-outlined text-[22px] block">search</span>
                </div>
                <span class="text-[10px] font-bold mt-1 tracking-wide text-on-surface-variant">Tìm kiếm</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center">
                <div class="px-5 py-1 rounded-full transition-all duration-300 text-on-surface-variant hover:bg-surface-container-high/50">
                    <span class="material-symbols-outlined text-[22px] block">bookmark</span>
                </div>
                <span class="text-[10px] font-bold mt-1 tracking-wide text-on-surface-variant">Đã lưu</span>
            </a>
            <button onclick="showAuthModal('login')" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center">
                <div class="px-5 py-1 rounded-full transition-all duration-300 bg-primary-fixed text-on-primary-fixed">
                    <span class="material-symbols-outlined text-[22px] block" style="font-variation-settings: 'FILL' 1;">person</span>
                </div>
                <span class="text-[10px] font-bold mt-1 tracking-wide text-primary">Tài khoản</span>
            </button>
        </div>
    </nav>
@endsection

@push('scripts')
    <script>
        let countdownTimer;
        const modal = document.getElementById('auth-modal');
        const modalContent = document.getElementById('auth-modal-content');

        // Khởi động hiển thị popup Đăng nhập mặc định khi vừa truy cập trang
        window.addEventListener('DOMContentLoaded', () => {
            showAuthModal('login');
        });

        // Hiển thị Popup
        function showAuthModal(section = 'login') {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Khóa cuộn trang nền
            
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 50);

            switchSection(section);
        }

        // Đóng Popup
        function hideAuthModal() {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = ''; // Mở lại cuộn trang
            }, 300);
        }

        // Chuyển đổi qua lại giữa các Section (Login, Register, OTP) bên trong modal
        function switchSection(sectionName) {
            clearErrors();
            
            // Ẩn tất cả section
            document.getElementById('section-login').classList.add('hidden');
            document.getElementById('section-register').classList.add('hidden');
            document.getElementById('section-otp').classList.add('hidden');

            // Hiển thị section được chỉ định
            if (sectionName === 'login') {
                document.getElementById('section-login').classList.remove('hidden');
            } else if (sectionName === 'register') {
                document.getElementById('section-register').classList.remove('hidden');
            } else if (sectionName === 'otp') {
                document.getElementById('section-otp').classList.remove('hidden');
            }
        }

        // Xóa thông báo lỗi cũ
        function clearErrors() {
            document.querySelectorAll('.text-error').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
        }

        // Ghi nhận lỗi cho form Đăng Nhập
        function showError(field, message) {
            const el = document.getElementById(`error-${field}`);
            if (el) {
                el.textContent = message;
                el.classList.remove('hidden');
            }
        }

        // Ghi nhận lỗi cho form Đăng Ký
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

        // ---------------- THỰC THI GỬI ĐĂNG NHẬP ----------------
        async function submitLogin() {
            clearErrors();
            const submitBtn = document.getElementById('login-submit-btn');
            const originalBtnHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">progress_activity</span>';

            const email = document.getElementById('login-email').value;
            const mat_khau = document.getElementById('login-password').value;
            const remember = document.getElementById('login-remember').checked;

            try {
                const response = await fetch('/dangnhap', {
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
                            showError(key, data.errors[key][0]);
                        });
                    } else {
                        showError('general', data.message || 'Email hoặc mật khẩu không chính xác.');
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                    return;
                }

                window.location.href = data.redirect_to || '/';
            } catch (error) {
                showError('general', 'Không thể kết nối máy chủ.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }

        // ---------------- THỰC THI GỬI ĐĂNG KÝ (Tải động qua API của bạn) ----------------
        async function submitRegister() {
            clearErrors();
            const submitBtn = document.getElementById('register-submit-btn');
            const originalBtnHtml = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">progress_activity</span>';

            const name = document.getElementById('register-name').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;

            try {
                // Sửa path API đăng ký của bạn nếu khác route này
                const response = await fetch('/dangky', {
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

                window.registrationEmail = email;
                switchSection('otp');
                startTimer();
            } catch (error) {
                showRegisterError('general', 'Không thể kết nối máy chủ.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }

        // ---------------- THỰC THI XÁC THỰC OTP ----------------
        async function submitOTPVerification() {
            const errorOtp = document.getElementById('error-otp');
            errorOtp.classList.add('hidden');
            
            const btn = document.getElementById('verify-btn');
            const originalBtnHtml = btn.innerHTML;

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
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin">progress_activity</span>';

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

                btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Thành công!';
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

        // ---------------- GỬI LẠI MÃ OTP ----------------
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
                    body: JSON.stringify({ email: window.registrationEmail })
                });

                const data = await response.json();

                if (!response.ok) {
                    errorOtp.textContent = data.message || 'Không thể gửi lại mã OTP.';
                    errorOtp.classList.remove('hidden');
                    timerContainer.innerHTML = '<a href="#" onclick="resendOTP(event)" class="text-primary font-bold">Gửi lại mã ngay</a>';
                    return;
                }

                startTimer();
            } catch (error) {
                errorOtp.textContent = 'Lỗi kết nối máy chủ.';
                errorOtp.classList.remove('hidden');
                timerContainer.innerHTML = '<a href="#" onclick="resendOTP(event)" class="text-primary font-bold">Gửi lại mã ngay</a>';
            }
        }

        // Bộ đếm lùi thời gian gửi lại OTP
        function startTimer() {
            if (countdownTimer) clearInterval(countdownTimer);
            
            let timeLeft = 60;
            const timerContainer = document.getElementById('timer-container');
            timerContainer.innerHTML = 'Gửi lại mã (sau <span class="font-bold" id="timer">60</span>s)';
            
            countdownTimer = setInterval(() => {
                timeLeft--;
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

        // Logic auto-focus ô nhập OTP
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