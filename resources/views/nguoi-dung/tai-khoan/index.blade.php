@extends('layouts.app')
@section('title', 'Quản lý tài khoản — Quán Mới')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(8px);
    }
</style>
@endpush

@section('content')
<main class="max-w-[1200px] mx-auto px-container-margin py-stack-lg min-h-[819px] pt-6">
    @if(session('success'))
    <div id="toast-success" class="relative overflow-hidden mb-6 flex items-center gap-3 bg-white border border-green-200 rounded-2xl px-5 py-4 shadow-lg shadow-green-100/50 transition-all duration-500 ease-out">
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
        <button onclick="dismissToastSuccess()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100 cursor-pointer" title="Đóng ngay">
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

    @if(session('error_conflict') || session('error'))
    <div id="toast-conflict" class="mb-6 flex items-start sm:items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 shadow-lg shadow-amber-100/50">
        <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0 text-amber-600">
            <span class="material-symbols-outlined text-[22px]">warning</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-amber-900 text-sm">Xung đột dữ liệu!</p>
            <p class="text-amber-800 text-xs mt-0.5">{{ session('error_conflict') ?? session('error') }}</p>
        </div>
        <a href="{{ route('tai-khoan.index') }}" class="shrink-0 px-3.5 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
            <span class="material-symbols-outlined text-[16px]">refresh</span>
            <span>Tải lại trang</span>
        </a>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-stack-lg">
        <!-- Sidebar Navigation -->
        @include('nguoi-dung.partials.sidebar')

        <!-- Main Content Area -->
        <div class="md:col-span-9 space-y-stack-lg">
            <!-- Profile Information Section -->
            <section class="bg-surface-card p-stack-lg rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container">
                <form id="form-update-profile" action="{{ route('tai-khoan.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="last_updated_at" value="{{ $user->updated_at?->toISOString() }}">
                    <div class="flex justify-between items-center mb-stack-lg">
                        <h3 class="font-headline-lg text-headline-lg text-on-surface">Thông tin cá nhân</h3>
                        <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-xl font-label-md text-label-md active:scale-95 transition-all shadow-lg hover:shadow-primary/20 flex items-center justify-center gap-2 min-w-[140px]">
                            <span class="btn-text">LƯU THAY ĐỔI</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md">
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Họ và tên</label>
                            <input name="ho_ten" class="w-full px-4 py-3 bg-surface-container-low border {{ $errors->has('ho_ten') ? 'border-error' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" type="text" value="{{ old('ho_ten', $user->ho_ten) }}"/>
                            @error('ho_ten')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Địa chỉ Email</label>
                            <input class="w-full px-4 py-3 bg-surface-container-highest border border-outline-variant rounded-xl text-text-muted cursor-not-allowed" disabled="" type="email" value="{{ $user->email }}"/>
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Giới tính</label>
                            <select name="gioi_tinh" class="w-full px-4 py-3 bg-surface-container-low border {{ $errors->has('gioi_tinh') ? 'border-error' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary outline-none appearance-none">
                                <option value="nữ" {{ old('gioi_tinh', $user->gioi_tinh) == 'nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="nam" {{ old('gioi_tinh', $user->gioi_tinh) == 'nam' ? 'selected' : '' }}>Nam</option>
                                <option value="khác" {{ old('gioi_tinh', $user->gioi_tinh) == 'khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Ngày sinh</label>
                            <input name="ngay_sinh" class="w-full px-4 py-3 bg-surface-container-low border {{ $errors->has('ngay_sinh') ? 'border-error' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary outline-none" type="date" value="{{ old('ngay_sinh', $user->ngay_sinh ? \Carbon\Carbon::parse($user->ngay_sinh)->format('Y-m-d') : '') }}"/>
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Số điện thoại</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-3.5 text-on-surface-variant">call</span>
                                <input name="so_dien_thoai" class="w-full pl-10 pr-4 py-3 bg-surface-container-low border {{ $errors->has('so_dien_thoai') ? 'border-error' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all" type="tel" value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}"/>
                            </div>
                            @error('so_dien_thoai')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Địa chỉ hiện tại</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-3.5 text-on-surface-variant">location_on</span>
                                <input name="dia_chi" class="w-full pl-10 pr-4 py-3 bg-surface-container-low border {{ $errors->has('dia_chi') ? 'border-error' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all" type="text" value="{{ old('dia_chi', $user->dia_chi) }}"/>
                            </div>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Password Update Section -->
            <section class="bg-surface-card p-stack-lg rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container">
                <form id="form-update-password" action="{{ route('tai-khoan.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="flex justify-between items-center mb-stack-lg">
                        <h3 class="font-headline-lg text-headline-lg text-on-surface">Bảo mật & Mật khẩu</h3>
                        <button type="submit" class="bg-surface-container-highest text-on-surface-variant hover:text-primary px-6 py-2.5 rounded-xl font-label-md text-label-md active:scale-95 transition-all shadow-sm flex items-center justify-center gap-2 min-w-[180px]">
                            <span class="btn-text">CẬP NHẬT MẬT KHẨU</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md">
                        <div class="md:col-span-2 space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Mật khẩu hiện tại</label>
                            <input name="current_password" class="w-full px-4 py-3 bg-surface-container-low border {{ $errors->has('current_password') ? 'border-error' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all" type="password" required/>
                            @error('current_password')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Mật khẩu mới</label>
                            <input name="password" class="w-full px-4 py-3 bg-surface-container-low border {{ $errors->has('password') ? 'border-error' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all" type="password" required/>
                            @error('password')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Xác nhận mật khẩu mới</label>
                            <input name="password_confirmation" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all" type="password" required/>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Saved Places Section (Bố cục chuẩn: 1 thẻ lớn + 4 thẻ nhỏ, nếu thừa có nút Xem tất cả / Xem thêm) -->
            <section class="space-y-4">
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <h3 class="font-headline-lg text-headline-lg text-on-surface">Địa điểm đã lưu</h3>
                        <p class="font-body-sm text-body-sm text-text-muted">Danh sách các nhà hàng và quán yêu thích của bạn</p>
                    </div>
                    @if(isset($savedQuan) && $savedQuan->isNotEmpty())
                        <a class="font-label-md text-label-md text-primary font-bold hover:underline inline-flex items-center gap-1" href="{{ route('quan-da-luu.index') }}">
                            <span>Xem tất cả</span>
                            @if($savedQuan->count() > 5)
                                <span>({{ $savedQuan->count() }})</span>
                            @endif
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                    @endif
                </div>

                @if(isset($savedQuan) && $savedQuan->isNotEmpty())
                    @php
                        $firstQuan = $savedQuan->first();
                        $remainingQuan = $savedQuan->slice(1, 4);
                        $totalSaved = $savedQuan->count();
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter" id="saved-places-grid">
                        <!-- Card 1: Thẻ lớn nổi bật (Chiếm 2 cột) -->
                        @php
                            $firstImg = $firstQuan->hinhAnh->first()->url ?? ($firstQuan->anh_bia_url ?? 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80');
                            $firstRating = $firstQuan->danh_gia_avg_so_sao ? number_format($firstQuan->danh_gia_avg_so_sao, 1) : '4.9';
                            $firstLoc = $firstQuan->ten_quan_huyen ? ($firstQuan->ten_quan_huyen . ($firstQuan->ten_tinh_thanh ? ', ' . Str::replace(['Thành phố ', 'Tỉnh '], '', $firstQuan->ten_tinh_thanh) : '')) : ($firstQuan->dia_chi_chi_tiet ?? 'Việt Nam');
                        @endphp
                        <div id="saved-card-{{ $firstQuan->id }}" class="saved-quan-card {{ $remainingQuan->isEmpty() ? 'md:col-span-3' : 'md:col-span-2' }} relative group rounded-2xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.06)] border border-surface-container active:scale-[0.99] transition-all hover:shadow-xl cursor-pointer">
                            <div class="h-64 md:h-72 w-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('{{ $firstImg }}')"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent pointer-events-none"></div>
                            <a href="{{ route('quan.detail', $firstQuan->slug) }}" class="absolute inset-0 z-10" aria-label="{{ $firstQuan->ten_quan }}"></a>
                            <div class="absolute bottom-0 p-stack-md w-full z-20 pointer-events-none">
                                <div class="flex justify-between items-end gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="bg-primary text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">ĐÁNH GIÁ CAO</span>
                                            <span class="flex items-center text-white text-xs font-bold gap-1 bg-black/40 backdrop-blur-sm px-2 py-0.5 rounded-full">
                                                <span class="material-symbols-outlined text-[14px] text-amber-400" style="font-variation-settings: 'FILL' 1;">star</span>
                                                <span>{{ $firstRating }}</span>
                                            </span>
                                        </div>
                                        <h4 class="text-white font-title-lg text-lg md:text-xl font-bold truncate group-hover:text-primary-fixed transition-colors">{{ $firstQuan->ten_quan }}</h4>
                                        <p class="text-white/85 text-body-sm flex items-center gap-1 truncate mt-0.5">
                                            <span class="material-symbols-outlined text-[16px] text-primary-fixed">location_on</span>
                                            <span>{{ $firstLoc }}</span>
                                        </p>
                                    </div>
                                    <button type="button" onclick="event.stopPropagation(); toggleSavePlace(this, '{{ $firstQuan->id }}')" class="pointer-events-auto bg-white/20 hover:bg-white/40 backdrop-blur-md p-2.5 rounded-full text-white border border-white/30 transition-all cursor-pointer shadow-md active:scale-90 shrink-0" title="Bỏ lưu địa điểm" aria-label="Bỏ lưu địa điểm">
                                        <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Các thẻ nhỏ còn lại (Tối đa 4 thẻ) -->
                        @foreach($remainingQuan as $quan)
                            @php
                                $quanImg = $quan->hinhAnh->first()->url ?? ($quan->anh_bia_url ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=500&q=80');
                                $isOpen = $quan->isDangMoCua();
                                $locText = $quan->ten_quan_huyen ? ($quan->ten_quan_huyen . ($quan->ten_tinh_thanh ? ', ' . Str::replace(['Thành phố ', 'Tỉnh '], '', $quan->ten_tinh_thanh) : '')) : ($quan->dia_chi_chi_tiet ?? 'Việt Nam');
                            @endphp
                            <div id="saved-card-{{ $quan->id }}" class="saved-quan-card group bg-surface-card rounded-2xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container active:scale-[0.98] transition-all hover:-translate-y-1 hover:shadow-lg cursor-pointer relative flex flex-col justify-between">
                                <div class="h-44 w-full relative overflow-hidden bg-surface-container-low">
                                    <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="{{ $quanImg }}" alt="{{ $quan->ten_quan }}"/>
                                    <a href="{{ route('quan.detail', $quan->slug) }}" class="absolute inset-0 z-10" aria-label="{{ $quan->ten_quan }}"></a>
                                    <button type="button" onclick="event.stopPropagation(); toggleSavePlace(this, '{{ $quan->id }}')" class="absolute top-3 right-3 z-20 bg-white/90 hover:bg-white backdrop-blur-sm p-1.5 rounded-full text-primary shadow-sm hover:scale-110 active:scale-95 transition-all cursor-pointer" title="Bỏ lưu địa điểm" aria-label="Bỏ lưu địa điểm">
                                        <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                                    </button>
                                </div>
                                <div class="p-4 space-y-1.5 relative">
                                    <a href="{{ route('quan.detail', $quan->slug) }}" class="absolute inset-0 z-0" aria-label="{{ $quan->ten_quan }}"></a>
                                    <h4 class="font-title-md text-sm md:text-base font-bold text-on-surface truncate group-hover:text-primary transition-colors relative z-10 pointer-events-none">{{ $quan->ten_quan }}</h4>
                                    <div class="flex items-center justify-between text-xs relative z-10 pointer-events-none pt-0.5">
                                        <span class="text-text-muted truncate max-w-[65%]" title="{{ $locText }}">{{ $locText }}</span>
                                        @if($isOpen)
                                            <span class="text-tick-xanh font-extrabold text-[11px] tracking-wide shrink-0 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>ĐANG MỞ</span>
                                            </span>
                                        @else
                                            <span class="text-error font-extrabold text-[11px] tracking-wide shrink-0">ĐÓNG CỬA</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Nút Xem thêm nếu có trên 5 địa điểm đã lưu --}}
                    @if($totalSaved > 5)
                    <div class="pt-4 text-center">
                        <a href="{{ route('quan-da-luu.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl border border-primary/20 bg-primary/5 hover:bg-primary hover:text-white text-primary text-xs md:text-sm font-bold shadow-xs hover:shadow-md transition-all group">
                            <span>Xem thêm {{ $totalSaved - 5 }} địa điểm đã lưu</span>
                            <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                    @endif
                @else
                    {{-- Trạng thái trống khi chưa lưu địa điểm nào --}}
                    <div class="bg-surface-card rounded-2xl border border-surface-container p-8 md:p-12 text-center space-y-3">
                        <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto">
                            <span class="material-symbols-outlined text-3xl">bookmark_border</span>
                        </div>
                        <h4 class="text-base md:text-lg font-bold text-on-surface">Bạn chưa lưu địa điểm nào</h4>
                        <p class="text-xs md:text-sm text-text-muted max-w-md mx-auto">Khám phá các nhà hàng, quán ăn ngon và bấm vào biểu tượng lưu để xem lại nhanh chóng tại đây bất cứ lúc nào.</p>
                        <div class="pt-2">
                            <a href="{{ route('kham-pha') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-md shadow-primary/20 transition-all">
                                <span class="material-symbols-outlined text-[18px]">travel_explore</span>
                                <span>Khám phá quán ngon ngay</span>
                            </a>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        ['form-update-profile', 'form-update-password'].forEach(function(formId) {
            const form = document.getElementById(formId);
            if (!form) return;

            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    return;
                }

                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    if (submitBtn.dataset.submitting === 'true') {
                        e.preventDefault();
                        return false;
                    }
                    submitBtn.dataset.submitting = 'true';
                    submitBtn.style.pointerEvents = 'none';

                    submitBtn.innerHTML = `
                        <svg class="animate-spin h-4 w-4 text-current inline-block mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Đang lưu...</span>
                    `;

                    setTimeout(function() {
                        submitBtn.disabled = true;
                    }, 50);
                }
            });
        });
    });

    function toggleSavePlace(btn, quanId) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) return;

        const card = document.getElementById(`saved-card-${quanId}`);
        const quanName = card?.querySelector('h4')?.textContent?.trim() || '';
        let quanImg = card?.querySelector('img')?.getAttribute('src') || null;
        if (!quanImg && card) {
            const bgDiv = card.querySelector('[style*="background-image"]');
            const match = bgDiv?.style?.backgroundImage?.match(/url\(["']?([^"']+)["']?\)/);
            if (match && match[1]) quanImg = match[1];
        }

        // 1. SHOW TOAST NOTIFICATION IMMEDIATELY (0ms delay)
        if (window.showToast) {
            window.showToast({
                eyebrow: 'Đã cập nhật danh sách',
                title: quanName ? `Đã bỏ lưu "${quanName}"` : 'Đã bỏ lưu quán',
                message: 'Địa điểm đã được gỡ khỏi danh sách yêu thích của bạn.',
                type: 'unsaved',
                image: quanImg
            });
        }

        // 2. OPTIMISTIC UI: Remove card instantly
        if (card) {
            card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.88)';
            setTimeout(() => {
                card.remove();
                const remaining = document.querySelectorAll('.saved-quan-card');
                if (remaining.length === 0) {
                    window.location.reload();
                }
            }, 280);
        }

        fetch(`/quan-da-luu/${quanId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                if (window.showToast) {
                    window.showToast({
                        title: 'Không thể thực hiện',
                        message: data.message || 'Đã có lỗi xảy ra, vui lòng tải lại trang.',
                        type: 'error'
                    });
                }
            }
        })
        .catch(err => {
            console.error(err);
            if (window.showToast) {
                window.showToast({
                    title: 'Lỗi kết nối',
                    message: 'Không thể gửi yêu cầu đến máy chủ.',
                    type: 'error'
                });
            }
        });
    }
</script>
@endsection
