@extends('layouts.app')

@section('title', 'Cài đặt tài khoản & Tùy chọn cá nhân — Quán Mới')

@push('seo')
    <meta name="description" content="Tùy chỉnh cài đặt cá nhân, tùy chọn tìm kiếm ẩm thực, thông báo email và quản lý bảo mật tài khoản người dùng tại Quán Mới.">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <link rel="canonical" href="{{ route('tai-khoan.cai-dat') }}">
    <meta property="og:title" content="Cài đặt tài khoản — Quán Mới">
    <meta property="og:description" content="Tùy chỉnh cài đặt cá nhân, thông báo email và quản lý bảo mật tài khoản.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('tai-khoan.cai-dat') }}">

    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@@type": "ListItem",
          "position": 1,
          "name": "Trang chủ",
          "item": "{{ url('/') }}"
        },
        {
          "@@type": "ListItem",
          "position": 2,
          "name": "Hồ sơ cá nhân",
          "item": "{{ route('tai-khoan.index') }}"
        },
        {
          "@@type": "ListItem",
          "position": 3,
          "name": "Cài đặt",
          "item": "{{ route('tai-khoan.cai-dat') }}"
        }
      ]
    }
    </script>
@endpush

@push('styles')
<style>
    .toggle-checkbox:checked {
        right: 0;
        border-color: #a04100;
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #a04100;
    }
</style>
@endpush

@section('content')
<main class="max-w-[1200px] mx-auto px-container-margin py-stack-lg min-h-[850px] pt-6">

    {{-- Breadcrumb Navigation Chuẩn SEO --}}
    <nav aria-label="Breadcrumb" class="mb-4">
        <ol class="flex items-center gap-2 text-xs font-medium text-text-muted">
            <li>
                <a href="{{ url('/') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">home</span>
                    <span>Trang chủ</span>
                </a>
            </li>
            <li><span class="text-outline-variant">/</span></li>
            <li>
                <a href="{{ route('tai-khoan.index') }}" class="hover:text-primary transition-colors">Tài khoản</a>
            </li>
            <li><span class="text-outline-variant">/</span></li>
            <li class="text-on-surface font-semibold" aria-current="page">Cài đặt</li>
        </ol>
    </nav>

    {{-- Alert Cập nhật thành công với đếm ngược 10s --}}
    @if(session('success'))
    <div id="toast-success" class="relative overflow-hidden mb-6 flex items-center gap-3 bg-white border border-green-200 rounded-2xl px-5 py-4 shadow-lg shadow-green-100/50 transition-all duration-500 ease-out" role="alert" aria-live="polite">
        <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-green-600" style="font-variation-settings:'FILL' 1">check_circle</span>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <p class="font-bold text-gray-900 text-sm">Cập nhật thành công!</p>
                <span id="toast-countdown-badge" class="text-[11px] font-semibold text-green-700 bg-green-50 px-2 py-0.5 rounded-full border border-green-200/70 inline-flex items-center gap-1" title="Tự động đóng sau 10 giây">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                    <span id="toast-countdown-seconds">10s</span>
                </span>
            </div>
            <p class="text-gray-500 text-xs mt-0.5">{{ session('success') }}</p>
        </div>
        <button onclick="dismissToastSuccess()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100 cursor-pointer" title="Đóng ngay" aria-label="Đóng thông báo">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>

        {{-- Thanh tiến trình đếm ngược 10 giây --}}
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-green-100 overflow-hidden">
            <div id="toast-progress-bar" class="h-full bg-green-500 transition-all duration-1000 ease-linear" style="width: 100%;"></div>
        </div>
    </div>

    <script>
        function dismissToastSuccess() {
            const toast = document.getElementById('toast-success');
            if (!toast) return;
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                toast.remove();
            }, 500);
        }

        (function() {
            let timeLeft = 10;
            const secondsEl = document.getElementById('toast-countdown-seconds');
            const progressEl = document.getElementById('toast-progress-bar');

            const timer = setInterval(() => {
                timeLeft--;
                if (secondsEl) {
                    secondsEl.textContent = timeLeft + 's';
                }
                if (progressEl) {
                    progressEl.style.width = (timeLeft * 10) + '%';
                }
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    dismissToastSuccess();
                }
            }, 1000);
        })();
    </script>
    @endif

    {{-- Alert xung đột dữ liệu Optimistic Locking --}}
    @if(session('error_conflict') || session('error'))
    <div id="toast-conflict" class="mb-6 flex items-start sm:items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 shadow-lg shadow-amber-100/50" role="alert">
        <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0 text-amber-600">
            <span class="material-symbols-outlined text-[22px]">warning</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-amber-900 text-sm">Xung đột dữ liệu!</p>
            <p class="text-amber-800 text-xs mt-0.5">{{ session('error_conflict') ?? session('error') }}</p>
        </div>
        <a href="{{ route('tai-khoan.cai-dat') }}" class="shrink-0 px-3.5 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
            <span class="material-symbols-outlined text-[16px]">refresh</span>
            <span>Tải lại trang</span>
        </a>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-stack-lg">
        <!-- Sidebar Navigation (Col 3) -->
        @include('nguoi-dung.partials.sidebar')

        <!-- Main Content Area (Col 9) -->
        <div class="md:col-span-9 space-y-6">

            <!-- Page Header Chuẩn SEO với thẻ H1 duy nhất -->
            <header class="bg-surface-card p-6 rounded-2xl border border-surface-container shadow-[0px_4px_20px_rgba(0,0,0,0.03)] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface tracking-tight flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-primary text-3xl">tune</span>
                        <span>Cài đặt tài khoản</span>
                    </h1>
                    <p class="text-sm text-text-muted mt-1">Tùy biến trải nghiệm khám phá ẩm thực, cấu hình thông báo và quản lý an toàn tài khoản của bạn.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('tai-khoan.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-on-surface-variant hover:text-primary bg-surface-container/60 hover:bg-surface-container rounded-xl transition-all">
                        <span class="material-symbols-outlined text-[18px]">account_circle</span>
                        <span>Xem hồ sơ</span>
                    </a>
                </div>
            </header>

            <form id="form-settings" action="{{ route('tai-khoan.cai-dat.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="last_updated_at" value="{{ $user->updated_at?->toISOString() }}">

                <!-- Section 1: Tùy chọn ẩm thực & Tìm kiếm -->
                <section class="bg-surface-card p-6 md:p-8 rounded-2xl border border-surface-container shadow-[0px_4px_20px_rgba(0,0,0,0.03)] space-y-6">
                    <div class="border-b border-surface-container pb-4">
                        <h2 class="text-lg md:text-xl font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">restaurant</span>
                            <span>Tùy chọn ẩm thực & Khám phá địa phương</span>
                        </h2>
                        <p class="text-xs md:text-sm text-text-muted mt-0.5">Giúp Quán Mới cá nhân hóa kết quả gợi ý quán ngon phù hợp với khẩu vị và vị trí của bạn.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Khu vực mặc định -->
                        <div>
                            <label for="khu_vuc_mac_dinh" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2">
                                Tỉnh / Thành phố mặc định
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-muted text-[20px] pointer-events-none">location_on</span>
                                <select id="khu_vuc_mac_dinh" name="khu_vuc_mac_dinh" class="w-full pl-10 pr-4 py-2.5 bg-background border border-surface-container rounded-xl text-sm font-medium text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                    <option value="">-- Tự động theo vị trí hiện tại --</option>
                                    @php
                                        $savedKhuVuc = $user->getSetting('khu_vuc_mac_dinh', '');
                                    @endphp
                                    @if(is_array($tinhThanhs))
                                        @foreach($tinhThanhs as $tinh)
                                            <option value="{{ $tinh['name'] ?? '' }}" {{ $savedKhuVuc == ($tinh['name'] ?? '') ? 'selected' : '' }}>
                                                {{ $tinh['name'] ?? '' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <p class="text-[11px] text-text-muted mt-1.5">Ưu tiên hiển thị các bài viết và địa điểm ẩm thực tại khu vực này khi bạn mở trang chủ.</p>
                        </div>

                        <!-- Bán kính tìm kiếm (Slider) -->
                        <div>
                            @php
                                $savedRadius = (int) $user->getSetting('ban_kinh_tim_kiem', 5);
                            @endphp
                            <div class="flex items-center justify-between mb-2">
                                <label for="ban_kinh_tim_kiem" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant">
                                    Bán kính tìm kiếm ẩm thực
                                </label>
                                <span id="radius-value-display" class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-extrabold">{{ $savedRadius }} km</span>
                            </div>
                            <div class="pt-2">
                                <input type="range" id="ban_kinh_tim_kiem" name="ban_kinh_tim_kiem" min="1" max="30" step="1" value="{{ $savedRadius }}"
                                    class="w-full h-2 bg-surface-container rounded-lg appearance-none cursor-pointer accent-primary focus:outline-none"
                                    oninput="document.getElementById('radius-value-display').innerText = this.value + ' km'">
                                <div class="flex justify-between text-[11px] text-text-muted mt-1.5 font-medium">
                                    <span>Gần (1 km)</span>
                                    <span>Trung bình (10 km)</span>
                                    <span>Rộng (30 km)</span>
                                </div>
                            </div>
                            <p class="text-[11px] text-text-muted mt-1.5">Bộ lọc bản đồ sẽ tự động đặt bán kính quét mặc định theo mức này.</p>
                        </div>
                    </div>

                    <!-- Sở thích ẩm thực (Food Preference Tags) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-2.5">
                            Món ngon yêu thích (Chọn các món bạn quan tâm nhất)
                        </label>
                        @php
                            $userFavorites = (array) $user->getSetting('so_thich_am_thuc', ['pho_bun', 'tra_sua_cafe']);
                            $foodTags = [
                                'pho_bun' => ['label' => 'Phở & Bún bò', 'icon' => 'ramen_dining'],
                                'com_tam' => ['label' => 'Cơm tấm & Cơm trưa', 'icon' => 'rice_bowl'],
                                'tra_sua_cafe' => ['label' => 'Trà sữa & Cà phê', 'icon' => 'local_cafe'],
                                'do_nuong_lau' => ['label' => 'Lẩu & Đồ nướng BBQ', 'icon' => 'skillet'],
                                'an_vat' => ['label' => 'Ăn vặt & Đường phố', 'icon' => 'bakery_dining'],
                                'hai_san' => ['label' => 'Hải sản tươi sống', 'icon' => 'set_meal'],
                                'do_chay' => ['label' => 'Món chay thanh tịnh', 'icon' => 'eco'],
                                'banh_mi' => ['label' => 'Bánh mì & Đồ ăn nhanh', 'icon' => 'fastfood'],
                            ];
                        @endphp
                        <div class="flex flex-wrap gap-2.5">
                            @foreach($foodTags as $tagKey => $tag)
                                @php
                                    $isSelected = in_array($tagKey, $userFavorites);
                                @endphp
                                <label for="food_{{ $tagKey }}" class="food-tag-label inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border text-xs font-semibold cursor-pointer transition-all select-none {{ $isSelected ? 'bg-primary-container/20 border-primary text-primary font-bold shadow-xs' : 'bg-surface-card border-surface-container text-on-surface-variant hover:border-outline' }}">
                                    <input type="checkbox" id="food_{{ $tagKey }}" name="so_thich_am_thuc[]" value="{{ $tagKey }}" class="hidden" {{ $isSelected ? 'checked' : '' }} onchange="toggleFoodChip(this)">
                                    <span class="material-symbols-outlined text-[18px]">{{ $tag['icon'] }}</span>
                                    <span>{{ $tag['label'] }}</span>
                                    <span class="chip-check material-symbols-outlined text-[16px] {{ $isSelected ? 'inline-block' : 'hidden' }}">check</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </section>

                <!-- Section 2: Cấu hình thông báo -->
                <section class="bg-surface-card p-6 md:p-8 rounded-2xl border border-surface-container shadow-[0px_4px_20px_rgba(0,0,0,0.03)] space-y-6">
                    <div class="border-b border-surface-container pb-4">
                        <h2 class="text-lg md:text-xl font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">notifications_active</span>
                            <span>Tùy chọn thông báo & Email</span>
                        </h2>
                        <p class="text-xs md:text-sm text-text-muted mt-0.5">Kiểm soát tần suất và nội dung thông báo gửi về hòm thư của bạn.</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Item 1: Quán mới nổi bật -->
                        <div class="flex items-center justify-between p-4 rounded-xl border border-surface-container bg-surface-container/20 hover:bg-surface-container/30 transition-all">
                            <div class="space-y-0.5 pr-4">
                                <label for="toggle_email_quan_moi" class="text-sm font-bold text-on-surface block cursor-pointer">
                                    Bản tin quán mới mở & Địa điểm ẩm thực hot tuần
                                </label>
                                <p class="text-xs text-text-muted">Nhận email gợi ý các quán ăn được đánh giá cao gần bạn vào mỗi sáng thứ Bảy.</p>
                            </div>
                            <div class="relative inline-block w-11 h-6 align-middle select-none shrink-0">
                                <input type="checkbox" id="toggle_email_quan_moi" name="thong_bao_email_quan_moi" value="1"
                                    {{ $user->getSetting('thong_bao_email_quan_moi', true) ? 'checked' : '' }}
                                    class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-2 border-outline-variant appearance-none cursor-pointer transition-transform duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20" />
                                <label for="toggle_email_quan_moi" class="toggle-label block overflow-hidden h-6 rounded-full bg-outline-variant/50 cursor-pointer transition-colors duration-200 ease-in-out"></label>
                            </div>
                        </div>

                        <!-- Item 2: Tương tác bài viết & đánh giá -->
                        <div class="flex items-center justify-between p-4 rounded-xl border border-surface-container bg-surface-container/20 hover:bg-surface-container/30 transition-all">
                            <div class="space-y-0.5 pr-4">
                                <label for="toggle_email_tuong_tac" class="text-sm font-bold text-on-surface block cursor-pointer">
                                    Tương tác bài viết & Phản hồi đánh giá
                                </label>
                                <p class="text-xs text-text-muted">Thông báo khi chủ quán hoặc thực khách khác phản hồi bình luận, bài review của bạn.</p>
                            </div>
                            <div class="relative inline-block w-11 h-6 align-middle select-none shrink-0">
                                <input type="checkbox" id="toggle_email_tuong_tac" name="thong_bao_email_tuong_tac" value="1"
                                    {{ $user->getSetting('thong_bao_email_tuong_tac', true) ? 'checked' : '' }}
                                    class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-2 border-outline-variant appearance-none cursor-pointer transition-transform duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20" />
                                <label for="toggle_email_tuong_tac" class="toggle-label block overflow-hidden h-6 rounded-full bg-outline-variant/50 cursor-pointer transition-colors duration-200 ease-in-out"></label>
                            </div>
                        </div>

                        <!-- Item 3: Khuyến mãi và ưu đãi độc quyền -->
                        <div class="flex items-center justify-between p-4 rounded-xl border border-surface-container bg-surface-container/20 hover:bg-surface-container/30 transition-all">
                            <div class="space-y-0.5 pr-4">
                                <label for="toggle_email_khuyen_mai" class="text-sm font-bold text-on-surface block cursor-pointer">
                                    Voucher & Ưu đãi độc quyền từ quán đối tác
                                </label>
                                <p class="text-xs text-text-muted">Nhận mã giảm giá và vé mời tham dự sự kiện khai trương ẩm thực đặc biệt.</p>
                            </div>
                            <div class="relative inline-block w-11 h-6 align-middle select-none shrink-0">
                                <input type="checkbox" id="toggle_email_khuyen_mai" name="thong_bao_khuyen_mai" value="1"
                                    {{ $user->getSetting('thong_bao_khuyen_mai', true) ? 'checked' : '' }}
                                    class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-2 border-outline-variant appearance-none cursor-pointer transition-transform duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20" />
                                <label for="toggle_email_khuyen_mai" class="toggle-label block overflow-hidden h-6 rounded-full bg-outline-variant/50 cursor-pointer transition-colors duration-200 ease-in-out"></label>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Quyền riêng tư & Hiển thị -->
                <section class="bg-surface-card p-6 md:p-8 rounded-2xl border border-surface-container shadow-[0px_4px_20px_rgba(0,0,0,0.03)] space-y-6">
                    <div class="border-b border-surface-container pb-4">
                        <h2 class="text-lg md:text-xl font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">visibility</span>
                            <span>Quyền riêng tư & Hiển thị đánh giá</span>
                        </h2>
                        <p class="text-xs md:text-sm text-text-muted mt-0.5">Quyết định cách người dùng khác nhìn thấy danh tính và hoạt động của bạn trên Quán Mới.</p>
                    </div>

                    <div class="space-y-5">
                        <!-- Chế độ tên khi viết đánh giá -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">
                                Chế độ hiển thị tên khi viết đánh giá quán
                            </label>
                            @php
                                $reviewMode = $user->getSetting('che_do_danh_gia', 'cong_khai');
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Card Công khai -->
                                <label for="mode_cong_khai" class="relative flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all select-none {{ $reviewMode === 'cong_khai' ? 'bg-primary-container/10 border-primary ring-1 ring-primary/20' : 'bg-surface-card border-surface-container hover:border-outline' }}">
                                    <input type="radio" id="mode_cong_khai" name="che_do_danh_gia" value="cong_khai" class="mt-1 text-primary focus:ring-primary" {{ $reviewMode === 'cong_khai' ? 'checked' : '' }}>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5 font-bold text-sm text-on-surface">
                                            <span class="material-symbols-outlined text-primary text-[18px]">verified</span>
                                            <span>Công khai họ tên & ảnh đại diện</span>
                                        </div>
                                        <p class="text-xs text-text-muted leading-relaxed">Được cộng đồng tin tưởng hơn, giúp bạn thăng hạng cấp độ Food Reviewer uy tín.</p>
                                    </div>
                                </label>

                                <!-- Card Ẩn danh -->
                                <label for="mode_an_danh" class="relative flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all select-none {{ $reviewMode === 'an_danh' ? 'bg-primary-container/10 border-primary ring-1 ring-primary/20' : 'bg-surface-card border-surface-container hover:border-outline' }}">
                                    <input type="radio" id="mode_an_danh" name="che_do_danh_gia" value="an_danh" class="mt-1 text-primary focus:ring-primary" {{ $reviewMode === 'an_danh' ? 'checked' : '' }}>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5 font-bold text-sm text-on-surface">
                                            <span class="material-symbols-outlined text-text-muted text-[18px]">visibility_off</span>
                                            <span>Đăng ẩn danh</span>
                                        </div>
                                        <p class="text-xs text-text-muted leading-relaxed">Chỉ hiển thị là "Thực khách ẩn danh". Bảo mật tuyệt đối danh tính cá nhân.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Công khai quán đã lưu -->
                        <div class="flex items-center justify-between p-4 rounded-xl border border-surface-container bg-surface-container/20 hover:bg-surface-container/30 transition-all">
                            <div class="space-y-0.5 pr-4">
                                <label for="toggle_cong_khai_quan_da_luu" class="text-sm font-bold text-on-surface block cursor-pointer">
                                    Công khai bộ sưu tập quán đã lưu
                                </label>
                                <p class="text-xs text-text-muted">Cho phép bạn bè và khách xem danh sách các quán bạn đã lưu vào mục yêu thích khi vào trang cá nhân của bạn.</p>
                            </div>
                            <div class="relative inline-block w-11 h-6 align-middle select-none shrink-0">
                                <input type="checkbox" id="toggle_cong_khai_quan_da_luu" name="cong_khai_quan_da_luu" value="1"
                                    {{ $user->getSetting('cong_khai_quan_da_luu', true) ? 'checked' : '' }}
                                    class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-2 border-outline-variant appearance-none cursor-pointer transition-transform duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20" />
                                <label for="toggle_cong_khai_quan_da_luu" class="toggle-label block overflow-hidden h-6 rounded-full bg-outline-variant/50 cursor-pointer transition-colors duration-200 ease-in-out"></label>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 4: Thiết bị & Phiên đăng nhập gần đây -->
                <section class="bg-surface-card p-6 md:p-8 rounded-2xl border border-surface-container shadow-[0px_4px_20px_rgba(0,0,0,0.03)] space-y-6">
                    <div class="border-b border-surface-container pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h2 class="text-lg md:text-xl font-bold text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">security</span>
                                <span>Bảo mật & Thiết bị đăng nhập gần đây</span>
                            </h2>
                            <p class="text-xs md:text-sm text-text-muted mt-0.5">Theo dõi các thiết bị và vị trí IP đang duy trì phiên làm việc trên tài khoản.</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 self-start sm:self-auto">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>{{ count($sessions) }} phiên hoạt động</span>
                        </span>
                    </div>

                    <!-- Danh sách phiên đăng nhập -->
                    <div class="space-y-3">
                        @forelse($sessions as $session)
                            <div class="flex items-center justify-between p-3.5 rounded-xl border {{ $session['is_current'] ? 'border-primary/40 bg-primary-container/5' : 'border-surface-container bg-surface-card' }} transition-all">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl {{ $session['is_current'] ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant' }} flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[22px]">{{ $session['device_icon'] }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-on-surface truncate">{{ $session['device_name'] }}</p>
                                            @if($session['is_current'])
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-bold shrink-0">Thiết bị hiện tại</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-text-muted mt-0.5">
                                            <span>IP: {{ $session['ip_address'] ?? '127.0.0.1' }}</span>
                                            <span>•</span>
                                            <span>Hoạt động: {{ $session['last_activity'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="material-symbols-outlined text-text-muted text-[18px]">verified_user</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-text-muted italic py-2">Không có phiên đăng nhập nào khác.</p>
                        @endforelse
                    </div>

                    <!-- Trạng thái liên kết & xác thực -->
                    <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl border border-surface-container bg-surface-container/20 flex items-center gap-3">
                            <span class="material-symbols-outlined text-emerald-600 text-2xl" style="font-variation-settings: 'FILL' 1;">mark_email_read</span>
                            <div>
                                <p class="text-xs font-bold text-on-surface">Email xác thực</p>
                                <p class="text-[11px] text-text-muted">{{ $user->email }} (Đã kích hoạt)</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl border border-surface-container bg-surface-container/20 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary text-2xl">key</span>
                                <div>
                                    <p class="text-xs font-bold text-on-surface">Mật khẩu bảo vệ</p>
                                    <p class="text-[11px] text-text-muted">Đổi mật khẩu định kỳ để an toàn</p>
                                </div>
                            </div>
                            <a href="{{ route('tai-khoan.index') }}#change-password" class="text-xs font-bold text-primary hover:underline">
                                Đổi mật khẩu
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Section 5: Thao tác an toàn & Quản lý tài khoản -->
                <section class="bg-surface-card p-6 md:p-8 rounded-2xl border border-surface-container shadow-[0px_4px_20px_rgba(0,0,0,0.03)] space-y-4">
                    <div class="border-b border-surface-container pb-4">
                        <h2 class="text-lg md:text-xl font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">manage_accounts</span>
                            <span>Khu vực chủ quán & Nâng cấp quyền hạn</span>
                        </h2>
                        <p class="text-xs md:text-sm text-text-muted mt-0.5">Các dịch vụ mở rộng và quản trị liên quan tới quyền tài khoản của bạn.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-primary-container/10 border border-primary-container/30">
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-on-surface flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-primary text-[18px]">storefront</span>
                                <span>Bạn có quán ăn hoặc muốn đăng ký địa điểm mới?</span>
                            </p>
                            <p class="text-xs text-text-muted">Tham gia hệ sinh thái Quán Mới để tiếp cận hàng ngàn thực khách sành ăn mỗi ngày hoàn toàn miễn phí.</p>
                        </div>
                        <a href="{{ route('chu-quan.quan.index') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-sm transition-all shrink-0">
                            <span class="material-symbols-outlined text-[18px]">add_business</span>
                            <span>Kênh Chủ Quán</span>
                        </a>
                    </div>
                </section>

                <!-- Action Footer: Lưu cài đặt với Chống Double Submit -->
                <div class="sticky bottom-4 z-20 bg-surface-card/95 backdrop-blur-md p-4 md:p-5 rounded-2xl border border-surface-container shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-text-muted">
                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">verified</span>
                        <span>Mọi thay đổi sẽ được áp dụng ngay lập tức cho tài khoản của bạn.</span>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a href="{{ route('tai-khoan.index') }}" class="flex-1 sm:flex-none text-center px-5 py-2.5 rounded-xl border border-surface-container text-xs font-bold text-on-surface-variant hover:bg-surface-container transition-all">
                            Hủy bỏ
                        </a>

                        <button type="submit" id="btn-save-settings" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-md shadow-primary/20 transition-all cursor-pointer">
                            <span id="btn-save-icon" class="material-symbols-outlined text-[18px]">save</span>
                            <span id="btn-save-text">LƯU CÀI ĐẶT</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    // Xử lý hiệu ứng chọn chips sở thích món ăn
    function toggleFoodChip(checkbox) {
        const label = checkbox.closest('.food-tag-label');
        const checkIcon = label.querySelector('.chip-check');
        if (checkbox.checked) {
            label.classList.add('bg-primary-container/20', 'border-primary', 'text-primary', 'font-bold', 'shadow-xs');
            label.classList.remove('bg-surface-card', 'border-surface-container', 'text-on-surface-variant');
            if (checkIcon) checkIcon.classList.remove('hidden');
        } else {
            label.classList.remove('bg-primary-container/20', 'border-primary', 'text-primary', 'font-bold', 'shadow-xs');
            label.classList.add('bg-surface-card', 'border-surface-container', 'text-on-surface-variant');
            if (checkIcon) checkIcon.classList.add('hidden');
        }
    }

    // Chống double submit cho nút Lưu Cài Đặt
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-settings');
        const saveBtn = document.getElementById('btn-save-settings');
        const saveIcon = document.getElementById('btn-save-icon');
        const saveText = document.getElementById('btn-save-text');

        if (form && saveBtn) {
            form.addEventListener('submit', function() {
                saveBtn.disabled = true;
                saveBtn.classList.add('opacity-75', 'cursor-not-allowed');
                if (saveIcon) {
                    saveIcon.textContent = 'progress_activity';
                    saveIcon.classList.add('animate-spin');
                }
                if (saveText) {
                    saveText.textContent = 'Đang lưu cài đặt...';
                }
            });
        }
    });
</script>
@endsection
