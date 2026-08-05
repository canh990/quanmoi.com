<footer class="bg-surface-container-lowest border-t border-outline-variant/30 text-on-surface pt-12 pb-24 md:pb-12 mt-auto">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        {{-- Top Grid Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-12 pb-12 border-b border-outline-variant/20">

            {{-- Column 1: Brand Info (Spans 2 cols on lg) --}}
            <div class="lg:col-span-2 space-y-4">
                <a href="/" class="inline-flex items-center gap-1.5 group">
                    <svg class="w-10 h-10 transition-transform duration-300 group-hover:scale-105 drop-shadow-sm" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M89.5 18C50.95 18 19.7 49.25 19.7 87.8C19.7 126.35 50.95 157.6 89.5 157.6C106.1 157.6 121.35 151.8 133.3 142.12L152.7 161.5L166.9 147.3L147.75 128.15C154.25 116.55 158 102.9 158 87.8C158 49.25 128.05 18 89.5 18ZM89.5 42.5C114.5 42.5 134.8 62.8 134.8 87.8C134.8 97.15 132 105.85 127.2 113.1L109.7 95.6H117V79H86.3V109.7H102.9V102.4L112.7 112.2C106.1 117.25 98.12 120.15 89.5 120.15C64.5 120.15 44.2 100 44.2 87.8C44.2 62.8 64.5 42.5 89.5 42.5Z" fill="#c97a3a" />
                        <path d="M63.8 101.5V84.8H73.4V101.5H63.8ZM80.2 101.5V70.2H89.8V101.5H80.2ZM96.6 101.5V58.3H106.2V101.5H96.6Z" fill="#1a1a1a" />
                        <path d="M61.2 72.7L77.5 60.1L88.1 67.4L106.8 48.6" stroke="#1a1a1a" stroke-width="5.4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M99 48.6H106.8V56.4" stroke="#1a1a1a" stroke-width="5.4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="text-[28px] tracking-tight font-extrabold transition-all duration-300 group-hover:opacity-90" style="font-family: 'Raleway', sans-serif; color: #1a1a1a; line-height: 0.88; letter-spacing: -0.05em;">QuanMoi</span>
                </a>
                <p class="text-on-surface-variant text-[14px] leading-relaxed max-w-sm">
                    Cộng đồng khám phá, đánh giá và chia sẻ những địa điểm ẩm thực chất lượng, uy tín nhất tại địa phương của bạn.
                </p>

                {{-- Social Icons --}}
                <div class="flex items-center gap-3 pt-2">
                    <a href="#" class="w-9 h-9 rounded-full bg-surface-container-low hover:bg-primary hover:text-white text-on-surface-variant flex items-center justify-center transition-all duration-200" title="Facebook">
                        <span class="material-symbols-outlined text-[18px]">public</span>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full bg-surface-container-low hover:bg-primary hover:text-white text-on-surface-variant flex items-center justify-center transition-all duration-200" title="Instagram">
                        <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full bg-surface-container-low hover:bg-primary hover:text-white text-on-surface-variant flex items-center justify-center transition-all duration-200" title="Youtube">
                        <span class="material-symbols-outlined text-[18px]">smart_display</span>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full bg-surface-container-low hover:bg-primary hover:text-white text-on-surface-variant flex items-center justify-center transition-all duration-200" title="Email">
                        <span class="material-symbols-outlined text-[18px]">mail</span>
                    </a>
                </div>
            </div>

            {{-- Column 2: Khám phá --}}
            <div class="space-y-3">
                <h4 class="font-bold text-[15px] text-on-surface uppercase tracking-wider">Khám phá</h4>
                <ul class="space-y-2.5 text-[14px]">
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Quán mới mở</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Top đánh giá cao</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Món ngon đường phố</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Lẩu & Nướng</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Cà phê & Trà sữa</a></li>
                </ul>
            </div>

            {{-- Column 3: Về Quán Mới --}}
            <div class="space-y-3">
                <h4 class="font-bold text-[15px] text-on-surface uppercase tracking-wider">Về Quán Mới</h4>
                <ul class="space-y-2.5 text-[14px]">
                    <li><a href="{{ route('about') }}" class="text-on-surface-variant hover:text-primary transition-colors">Giới thiệu về chúng tôi</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Blog ẩm thực</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Dành cho chủ quán</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Tuyển dụng</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Liên hệ hợp tác</a></li>
                </ul>
            </div>

            {{-- Column 4: Hỗ trợ --}}
            <div class="space-y-3">
                <h4 class="font-bold text-[15px] text-on-surface uppercase tracking-wider">Hỗ trợ</h4>
                <ul class="space-y-2.5 text-[14px]">
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Trung tâm trợ giúp</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Quy định sử dụng</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Chính sách bảo mật</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Giải quyết tranh chấp</a></li>
                    <li><a href="#" class="text-on-surface-variant hover:text-primary transition-colors">Hotline: 1900 1234</a></li>
                </ul>
            </div>

        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-[13px] text-on-surface-variant">
            <p>© 2026 Quán Mới. Tất cả quyền được bảo lưu.</p>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-primary transition-colors">Bảo mật</a>
                <a href="#" class="hover:text-primary transition-colors">Điều khoản</a>
                <a href="#" class="hover:text-primary transition-colors">Cookie</a>
            </div>
        </div>
    </div>
</footer>
