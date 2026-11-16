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
            <li class="text-on-surface font-semibold" aria-current="page">Hồ sơ cá nhân</li>
        </ol>
    </nav>

    {{-- Banner cảnh báo xung đột đa tab (Optimistic Locking) --}}
    @if(session('error_conflict') || session('error'))
        <div class="mb-6 flex items-start sm:items-center gap-3 bg-rose-50 border border-rose-200 rounded-2xl px-5 py-4 shadow-lg shadow-rose-100/50" role="alert">
            <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0 text-rose-600">
                <span class="material-symbols-outlined text-[22px]">error</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-rose-900 text-sm">{{ session('error_conflict') ?: session('error') }}</p>
            </div>
            <a href="{{ route('tai-khoan.index') }}" class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shrink-0 transition-colors">
                Tải lại trang
            </a>
        </div>
    @endif

    {{-- Banner báo lỗi giao diện Web ở phía Client (Không dùng popup HTML trình duyệt) --}}
    <div id="client-error-banner" class="hidden mb-6 flex items-start sm:items-center gap-3 bg-rose-50 border border-rose-200 rounded-2xl px-5 py-4 shadow-lg shadow-rose-100/50" role="alert">
        <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0 text-rose-600">
            <span class="material-symbols-outlined text-[22px]">error</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-rose-900 text-sm">Vui lòng kiểm tra lại thông tin:</p>
            <ul id="client-error-list" class="list-disc list-inside text-rose-800 text-xs mt-1 space-y-0.5"></ul>
        </div>
        <button type="button" onclick="document.getElementById('client-error-banner').classList.add('hidden')" class="text-rose-500 hover:text-rose-700 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-stack-lg">
        <!-- Sidebar Navigation -->
        @include('nguoi-dung.partials.sidebar')

        <!-- Main Content Area -->
        <div class="md:col-span-9 space-y-stack-lg">
            <!-- Profile Information Section -->
            <section class="bg-surface-card p-stack-lg rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container">
                <form id="form-update-profile" action="{{ route('tai-khoan.update') }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="last_updated_at" value="{{ $user->updated_at?->toISOString() }}">
                    <div class="flex justify-between items-center mb-stack-lg">
                        <h3 class="font-headline-lg text-headline-lg text-on-surface">Thông tin cá nhân</h3>
                        <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-xl font-label-md text-label-md active:scale-95 transition-all shadow-lg hover:shadow-primary/20 flex items-center justify-center gap-2 min-w-[140px]">
                            <span class="btn-text">LƯU THAY ĐỔI</span>
                        </button>
                    </div>

                    {{-- Banner báo lỗi cho thông tin cá nhân --}}
                    <div id="profile-client-error-banner" class="hidden mb-4 flex items-start sm:items-center gap-3 bg-rose-50 border border-rose-200 rounded-2xl px-4 py-3 shadow-xs" role="alert">
                        <div class="w-7 h-7 rounded-lg bg-rose-100 flex items-center justify-center shrink-0 text-rose-600">
                            <span class="material-symbols-outlined text-[18px]">error</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-rose-900 text-xs">Vui lòng kiểm tra lại thông tin:</p>
                            <ul id="profile-client-error-list" class="list-disc list-inside text-rose-800 text-[11px] mt-0.5 space-y-0.5"></ul>
                        </div>
                        <button type="button" onclick="document.getElementById('profile-client-error-banner').classList.add('hidden')" class="text-rose-500 hover:text-rose-700 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md">
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Họ và tên <span class="text-rose-500">*</span></label>
                            <input name="ho_ten"
                                   id="ho_ten"
                                   class="w-full px-4 py-3 bg-surface-container-low border {{ $errors->has('ho_ten') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                   type="text"
                                   value="{{ old('ho_ten', $user->ho_ten) }}"/>
                            <p id="client-error-ho_ten" class="text-rose-600 text-xs mt-1.5 font-medium items-center gap-1 hidden"></p>
                            @error('ho_ten')
                                <p class="text-rose-600 text-xs mt-1.5 font-medium flex items-center gap-1 server-error-ho_ten">
                                    <span class="material-symbols-outlined text-[15px] shrink-0">error</span>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
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
                            @error('gioi_tinh')
                                <p class="text-rose-600 text-xs mt-1.5 font-medium flex items-center gap-1 server-error-gioi_tinh">
                                    <span class="material-symbols-outlined text-[15px] shrink-0">error</span>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Ngày sinh</label>
                            <input name="ngay_sinh"
                                   id="ngay_sinh"
                                   class="w-full px-4 py-3 bg-surface-container-low border {{ $errors->has('ngay_sinh') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all"
                                   type="date"
                                   value="{{ old('ngay_sinh', $user->ngay_sinh ? \Carbon\Carbon::parse($user->ngay_sinh)->format('Y-m-d') : '') }}"/>
                            <p id="client-error-ngay_sinh" class="text-rose-600 text-xs mt-1.5 font-medium items-center gap-1 hidden"></p>
                            @error('ngay_sinh')
                                <p class="text-rose-600 text-xs mt-1.5 font-medium flex items-center gap-1 server-error-ngay_sinh">
                                    <span class="material-symbols-outlined text-[15px] shrink-0">error</span>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Số điện thoại</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-3.5 text-on-surface-variant">call</span>
                                <input name="so_dien_thoai"
                                       id="so_dien_thoai"
                                       maxlength="11"
                                       inputmode="numeric"
                                       class="w-full pl-10 pr-4 py-3 bg-surface-container-low border {{ $errors->has('so_dien_thoai') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all"
                                       type="tel"
                                       placeholder="Ví dụ: 0912345678"
                                       value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}"/>
                            </div>
                            <p id="client-error-so_dien_thoai" class="text-rose-600 text-xs mt-1.5 font-medium items-center gap-1 hidden"></p>
                            @error('so_dien_thoai')
                                <p class="text-rose-600 text-xs mt-1.5 font-medium flex items-center gap-1 server-error-so_dien_thoai">
                                    <span class="material-symbols-outlined text-[15px] shrink-0">error</span>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Địa chỉ hiện tại</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-3.5 text-on-surface-variant pointer-events-none">location_on</span>
                                @php
                                    $currentDiaChi = trim((string) old('dia_chi', $user->dia_chi));
                                    $matchedTinh = false;
                                @endphp
                                <select name="dia_chi"
                                        id="dia_chi"
                                        class="w-full pl-10 pr-10 py-3 bg-surface-container-low border {{ $errors->has('dia_chi') ? 'border-error' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary outline-none appearance-none transition-all cursor-pointer">
                                    <option value="">-- Chọn Tỉnh / Thành phố --</option>
                                    @if(!empty($tinhThanhs) && is_array($tinhThanhs))
                                        @foreach($tinhThanhs as $tinh)
                                            @php
                                                $tinhName = $tinh['name'] ?? '';
                                                $isSelected = ($currentDiaChi !== '' && $currentDiaChi === $tinhName);
                                                if ($isSelected) {
                                                    $matchedTinh = true;
                                                }
                                            @endphp
                                            @if($tinhName !== '')
                                                <option value="{{ $tinhName }}" {{ $isSelected ? 'selected' : '' }}>
                                                    {{ $tinhName }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($currentDiaChi !== '' && !$matchedTinh)
                                        <option value="{{ $currentDiaChi }}" selected>{{ $currentDiaChi }}</option>
                                    @endif
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-3.5 text-on-surface-variant pointer-events-none">expand_more</span>
                            </div>
                            @error('dia_chi')
                                <p class="text-rose-600 text-xs mt-1.5 font-medium flex items-center gap-1 server-error-dia_chi">
                                    <span class="material-symbols-outlined text-[15px] shrink-0">error</span>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>
                </form>
            </section>

            <!-- Password Update Section -->
            <section class="bg-surface-card p-stack-lg rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container">
                <form id="form-update-password" action="{{ route('tai-khoan.update-password') }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="last_updated_at" value="{{ $user->updated_at?->toISOString() }}">
                    <div class="flex justify-between items-center mb-stack-lg">
                        <h3 class="font-headline-lg text-headline-lg text-on-surface">Bảo mật & Mật khẩu</h3>
                        <button type="submit" class="bg-surface-container-highest text-on-surface-variant hover:text-primary px-6 py-2.5 rounded-xl font-label-md text-label-md active:scale-95 transition-all shadow-sm flex items-center justify-center gap-2 min-w-[180px]">
                            <span class="btn-text">CẬP NHẬT MẬT KHẨU</span>
                        </button>
                    </div>

                    {{-- Banner báo lỗi cho đổi mật khẩu --}}
                    <div id="pwd-client-error-banner" class="hidden mb-4 flex items-start sm:items-center gap-3 bg-rose-50 border border-rose-200 rounded-2xl px-4 py-3 shadow-xs" role="alert">
                        <div class="w-7 h-7 rounded-lg bg-rose-100 flex items-center justify-center shrink-0 text-rose-600">
                            <span class="material-symbols-outlined text-[18px]">error</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-rose-900 text-xs">Vui lòng kiểm tra lại thông tin mật khẩu:</p>
                            <ul id="pwd-client-error-list" class="list-disc list-inside text-rose-800 text-[11px] mt-0.5 space-y-0.5"></ul>
                        </div>
                        <button type="button" onclick="document.getElementById('pwd-client-error-banner').classList.add('hidden')" class="text-rose-500 hover:text-rose-700 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-stack-md">
                        <div class="md:col-span-2 space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Mật khẩu hiện tại <span class="text-rose-500">*</span></label>
                            <input name="current_password"
                                   id="current_password"
                                   class="w-full px-4 py-3 bg-surface-container-low border {{ $errors->has('current_password') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all"
                                   type="password"/>
                            <p id="client-error-current_password" class="text-rose-600 text-xs mt-1.5 font-medium items-center gap-1 hidden"></p>
                            @error('current_password')
                                <p class="text-rose-600 text-xs mt-1.5 font-medium flex items-center gap-1 server-error-current_password">
                                    <span class="material-symbols-outlined text-[15px] shrink-0">error</span>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Mật khẩu mới <span class="text-rose-500">*</span></label>
                            <input name="password"
                                   id="password"
                                   class="w-full px-4 py-3 bg-surface-container-low border {{ $errors->has('password') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-outline-variant' }} rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all"
                                   type="password"/>
                            <p id="client-error-password" class="text-rose-600 text-xs mt-1.5 font-medium items-center gap-1 hidden"></p>
                            @error('password')
                                <p class="text-rose-600 text-xs mt-1.5 font-medium flex items-center gap-1 server-error-password">
                                    <span class="material-symbols-outlined text-[15px] shrink-0">error</span>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-md text-label-md text-on-surface-variant block">Xác nhận mật khẩu mới <span class="text-rose-500">*</span></label>
                            <input name="password_confirmation"
                                   id="password_confirmation"
                                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all"
                                   type="password"/>
                            <p id="client-error-password_confirmation" class="text-rose-600 text-xs mt-1.5 font-medium items-center gap-1 hidden"></p>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Saved Places Section (Bố cục chuẩn: 1 thẻ lớn + 4 thẻ nhỏ, nếu thừa có nút Xem tất cả / Xem thêm) -->
            <section class="space-y-4" id="saved-places-section">
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <h3 class="font-headline-lg text-headline-lg text-on-surface">Địa điểm đã lưu</h3>
                        <p class="font-body-sm text-body-sm text-text-muted">Danh sách các nhà hàng và quán yêu thích của bạn</p>
                    </div>
                    <a id="saved-view-all-link"
                       class="font-label-md text-label-md text-primary font-bold hover:underline inline-flex items-center gap-1 {{ isset($savedQuan) && $savedQuan->isNotEmpty() ? '' : 'hidden' }}"
                       href="{{ route('quan-da-luu.index') }}">
                        <span>Xem tất cả</span>
                        <span id="saved-view-all-count" class="{{ isset($savedQuan) && $savedQuan->count() > 5 ? '' : 'hidden' }}">({{ isset($savedQuan) ? $savedQuan->count() : 0 }})</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>

                @php
                    $savedPlacesPayload = collect($savedQuan ?? [])->values()->map(function ($quan, $index) {
                        $fallbackImg = $index === 0
                            ? 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80'
                            : 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=500&q=80';
                        $img = $quan->hinhAnh->first()->url ?? ($quan->anh_bia_url ?? $fallbackImg);
                        $rating = $quan->danh_gia_avg_so_sao ? number_format($quan->danh_gia_avg_so_sao, 1) : '4.9';
                        $loc = $quan->ten_quan_huyen
                            ? ($quan->ten_quan_huyen . ($quan->ten_tinh_thanh ? ', ' . Str::replace(['Thành phố ', 'Tỉnh '], '', $quan->ten_tinh_thanh) : ''))
                            : ($quan->dia_chi_chi_tiet ?? 'Việt Nam');

                        return [
                            'id' => (string) $quan->id,
                            'name' => (string) $quan->ten_quan,
                            'url' => route('quan.detail', $quan->slug),
                            'image' => (string) $img,
                            'rating' => (string) $rating,
                            'location' => (string) $loc,
                            'isOpen' => (bool) (method_exists($quan, 'isDangMoCua') ? $quan->isDangMoCua() : false),
                        ];
                    })->all();
                @endphp

                <div id="saved-places-body">
                    @if(isset($savedQuan) && $savedQuan->isNotEmpty())
                        @php
                            $firstItem = $savedPlacesPayload[0];
                            $remainingItems = array_slice($savedPlacesPayload, 1, 4);
                            $totalSaved = count($savedPlacesPayload);
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter" id="saved-places-grid">
                            <!-- Card 1: Thẻ lớn nổi bật (Chiếm 2 cột) -->
                            <div id="saved-card-{{ $firstItem['id'] }}" data-quan-id="{{ $firstItem['id'] }}" class="saved-quan-card {{ empty($remainingItems) ? 'md:col-span-3' : 'md:col-span-2' }} relative group rounded-2xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.06)] border border-surface-container transition-all hover:shadow-xl cursor-pointer">
                                <div class="h-64 md:h-72 w-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('{{ $firstItem['image'] }}')"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent pointer-events-none"></div>
                                <a href="{{ $firstItem['url'] }}" class="absolute inset-0 z-10" aria-label="{{ $firstItem['name'] }}"></a>
                                <div class="absolute bottom-0 p-stack-md w-full z-20 pointer-events-none">
                                    <div class="flex justify-between items-end gap-3">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="bg-primary text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">ĐÁNH GIÁ CAO</span>
                                                <span class="flex items-center text-white text-xs font-bold gap-1 bg-black/40 backdrop-blur-sm px-2 py-0.5 rounded-full">
                                                    <span class="material-symbols-outlined text-[14px] text-amber-400" style="font-variation-settings: 'FILL' 1;">star</span>
                                                    <span>{{ $firstItem['rating'] }}</span>
                                                </span>
                                            </div>
                                            <h4 class="text-white font-title-lg text-lg md:text-xl font-bold truncate group-hover:text-primary-fixed transition-colors">{{ $firstItem['name'] }}</h4>
                                            <p class="text-white/85 text-body-sm flex items-center gap-1 truncate mt-0.5">
                                                <span class="material-symbols-outlined text-[16px] text-primary-fixed">location_on</span>
                                                <span>{{ $firstItem['location'] }}</span>
                                            </p>
                                        </div>
                                        <button type="button"
                                                data-save-btn="1"
                                                onmousedown="event.stopPropagation()"
                                                ontouchstart="event.stopPropagation()"
                                                onclick="event.preventDefault(); event.stopPropagation(); toggleSavePlace(this, '{{ $firstItem['id'] }}')"
                                                class="relative z-30 pointer-events-auto bg-white/20 hover:bg-white/40 backdrop-blur-md w-10 h-10 flex items-center justify-center rounded-full text-red-500 border border-white/30 transition-all cursor-pointer shadow-md active:scale-90 shrink-0"
                                                title="Bỏ lưu địa điểm"
                                                aria-label="Bỏ lưu địa điểm">
                                            <span class="material-symbols-outlined text-[20px] pointer-events-none" style="font-variation-settings: 'FILL' 1;">favorite</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Các thẻ nhỏ còn lại (Tối đa 4 thẻ) -->
                            @foreach($remainingItems as $item)
                                <div id="saved-card-{{ $item['id'] }}" data-quan-id="{{ $item['id'] }}" class="saved-quan-card group bg-surface-card rounded-2xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container transition-all hover:-translate-y-1 hover:shadow-lg cursor-pointer relative flex flex-col justify-between">
                                    <div class="h-44 w-full relative overflow-hidden bg-surface-container-low">
                                        <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="{{ $item['image'] }}" alt="{{ $item['name'] }}"/>
                                        <a href="{{ $item['url'] }}" class="absolute inset-0 z-10" aria-label="{{ $item['name'] }}"></a>
                                        <button type="button"
                                                data-save-btn="1"
                                                onmousedown="event.stopPropagation()"
                                                ontouchstart="event.stopPropagation()"
                                                onclick="event.preventDefault(); event.stopPropagation(); toggleSavePlace(this, '{{ $item['id'] }}')"
                                                class="absolute top-3 right-3 z-30 pointer-events-auto bg-white/90 hover:bg-white backdrop-blur-sm w-9 h-9 flex items-center justify-center rounded-full text-red-500 shadow-sm hover:scale-110 active:scale-95 transition-all cursor-pointer"
                                                title="Bỏ lưu địa điểm"
                                                aria-label="Bỏ lưu địa điểm">
                                            <span class="material-symbols-outlined text-[20px] pointer-events-none" style="font-variation-settings: 'FILL' 1;">favorite</span>
                                        </button>
                                    </div>
                                    <div class="p-4 space-y-1.5 relative">
                                        <a href="{{ $item['url'] }}" class="absolute inset-0 z-0" aria-label="{{ $item['name'] }}"></a>
                                        <h4 class="font-title-md text-sm md:text-base font-bold text-on-surface truncate group-hover:text-primary transition-colors relative z-10 pointer-events-none">{{ $item['name'] }}</h4>
                                        <div class="flex items-center justify-between text-xs relative z-10 pointer-events-none pt-0.5">
                                            <span class="text-text-muted truncate max-w-[65%]" title="{{ $item['location'] }}">{{ $item['location'] }}</span>
                                            @if($item['isOpen'])
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
                        <div id="saved-load-more-wrap" class="pt-4 text-center {{ $totalSaved > 5 ? '' : 'hidden' }}">
                            <a href="{{ route('quan-da-luu.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl border border-primary/20 bg-primary/5 hover:bg-primary hover:text-white text-primary text-xs md:text-sm font-bold shadow-xs hover:shadow-md transition-all group">
                                <span id="saved-load-more-text">Xem thêm {{ max(0, $totalSaved - 5) }} địa điểm đã lưu</span>
                                <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        </div>
                    @else
                        {{-- Trạng thái trống khi chưa lưu địa điểm nào --}}
                        <div class="bg-surface-card rounded-2xl border border-surface-container p-8 md:p-12 text-center space-y-3">
                            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto">
                                <span class="material-symbols-outlined text-3xl">favorite</span>
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
                </div>
            </section>
        </div>
    </div>
</main>

<script>
    // =========================================================================
    // 1. CLIENT VALIDATION CHO HỒ SƠ VÀ MẬT KHẨU
    // =========================================================================
    function showFieldError(fieldName, message) {
        const input = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
        if (!input) return;
        input.classList.remove('border-outline-variant', 'border-error');
        input.classList.add('border-rose-500', 'ring-1', 'ring-rose-500');

        const serverError = document.querySelector(`.server-error-${fieldName}`);
        if (serverError) serverError.classList.add('hidden');

        let errorEl = document.getElementById(`client-error-${fieldName}`);
        if (!errorEl) {
            errorEl = document.createElement('p');
            errorEl.id = `client-error-${fieldName}`;
            errorEl.className = 'text-rose-600 text-xs mt-1.5 font-medium flex items-center gap-1';
            const parent = input.closest('.space-y-1') || input.parentElement;
            parent.appendChild(errorEl);
        }
        errorEl.innerHTML = `<span class="material-symbols-outlined text-[15px] shrink-0">error</span><span>${message}</span>`;
        errorEl.classList.remove('hidden');
        errorEl.classList.add('flex');
    }

    function clearFieldError(fieldName) {
        const input = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.classList.remove('border-rose-500', 'ring-1', 'ring-rose-500', 'border-error');
            input.classList.add('border-outline-variant');
        }
        const errorEl = document.getElementById(`client-error-${fieldName}`);
        if (errorEl) {
            errorEl.classList.add('hidden');
            errorEl.classList.remove('flex');
            errorEl.innerHTML = '';
        }
        const serverError = document.querySelector(`.server-error-${fieldName}`);
        if (serverError) {
            serverError.classList.add('hidden');
        }
    }

    function showFormErrorBanner(bannerId, listId, messages) {
        const banner = document.getElementById(bannerId);
        const list = document.getElementById(listId);
        if (!banner || !list) return;

        list.innerHTML = '';
        messages.forEach(function(msg) {
            const li = document.createElement('li');
            li.textContent = msg;
            list.appendChild(li);
        });
        banner.classList.remove('hidden');
    }

    function hideFormErrorBanner(bannerId) {
        const banner = document.getElementById(bannerId);
        if (banner) {
            banner.classList.add('hidden');
        }
    }

    // =========================================================================
    // 2. BỎ LƯU QUÁN ĂN & ĐỒNG BỘ GIAO DIỆN DANH SÁCH ĐÃ LƯU
    // =========================================================================
    let savedPlacesState = @json($savedPlacesPayload ?? []);
    const toggleSaveUrlTemplate = @json(route('quan-da-luu.toggle', ['quanId' => '__QUAN_ID__']));
    const khamPhaUrl = @json(route('kham-pha'));
    const quanDaLuuIndexUrl = @json(route('quan-da-luu.index'));
    const loginUrl = @json(route('login'));
    const inFlightSavedIds = new Set();

    function escapeSavedHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeCssUrl(url) {
        return String(url || '').replace(/['"\\()\r\n]/g, '');
    }

    function setSavedCardPending(quanId, isPending) {
        const card = document.getElementById(`saved-card-${quanId}`);
        if (!card) return;
        const btn = card.querySelector('button[data-save-btn="1"]');
        if (isPending) {
            card.classList.add('opacity-50', 'pointer-events-none');
            if (btn) {
                btn.dataset.loading = 'true';
                btn.disabled = true;
            }
        } else {
            card.classList.remove('opacity-50', 'pointer-events-none');
            card.style.opacity = '';
            card.style.transform = '';
            if (btn) {
                btn.dataset.loading = 'false';
                btn.disabled = false;
            }
        }
    }

    function buildSavedCardHtml(item, isFeatured, isSingle) {
        const id = escapeSavedHtml(item.id);
        const name = escapeSavedHtml(item.name);
        const url = escapeSavedHtml(item.url);
        const imgHtml = escapeSavedHtml(item.image);
        const bgUrl = escapeCssUrl(item.image);
        const rating = escapeSavedHtml(item.rating || '4.9');
        const location = escapeSavedHtml(item.location || 'Việt Nam');
        const isPending = inFlightSavedIds.has(String(item.id));
        const pendingClasses = isPending ? ' opacity-50 pointer-events-none' : '';
        const btnLoadingAttr = isPending ? ' data-loading="true" disabled' : '';

        if (isFeatured) {
            const colSpanClass = isSingle ? 'md:col-span-3' : 'md:col-span-2';
            return `
                <div id="saved-card-${id}" data-quan-id="${id}" class="saved-quan-card ${colSpanClass} relative group rounded-2xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.06)] border border-surface-container transition-all hover:shadow-xl cursor-pointer${pendingClasses}">
                    <div class="h-64 md:h-72 w-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('${bgUrl}')"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent pointer-events-none"></div>
                    <a href="${url}" class="absolute inset-0 z-10" aria-label="${name}"></a>
                    <div class="absolute bottom-0 p-stack-md w-full z-20 pointer-events-none">
                        <div class="flex justify-between items-end gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="bg-primary text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">ĐÁNH GIÁ CAO</span>
                                    <span class="flex items-center text-white text-xs font-bold gap-1 bg-black/40 backdrop-blur-sm px-2 py-0.5 rounded-full">
                                        <span class="material-symbols-outlined text-[14px] text-amber-400" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>${rating}</span>
                                    </span>
                                </div>
                                <h4 class="text-white font-title-lg text-lg md:text-xl font-bold truncate group-hover:text-primary-fixed transition-colors">${name}</h4>
                                <p class="text-white/85 text-body-sm flex items-center gap-1 truncate mt-0.5">
                                    <span class="material-symbols-outlined text-[16px] text-primary-fixed">location_on</span>
                                    <span>${location}</span>
                                </p>
                            </div>
                            <button type="button"
                                    data-save-btn="1"${btnLoadingAttr}
                                    onmousedown="event.stopPropagation()"
                                    ontouchstart="event.stopPropagation()"
                                    onclick="event.preventDefault(); event.stopPropagation(); toggleSavePlace(this, '${id}')"
                                    class="relative z-30 pointer-events-auto bg-white/20 hover:bg-white/40 backdrop-blur-md w-10 h-10 flex items-center justify-center rounded-full text-red-500 border border-white/30 transition-all cursor-pointer shadow-md active:scale-90 shrink-0"
                                    title="Bỏ lưu địa điểm"
                                    aria-label="Bỏ lưu địa điểm">
                                <span class="material-symbols-outlined text-[20px] pointer-events-none" style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        const statusBadge = item.isOpen
            ? `<span class="text-tick-xanh font-extrabold text-[11px] tracking-wide shrink-0 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>ĐANG MỞ</span>
               </span>`
            : `<span class="text-error font-extrabold text-[11px] tracking-wide shrink-0">ĐÓNG CỬA</span>`;

        return `
            <div id="saved-card-${id}" data-quan-id="${id}" class="saved-quan-card group bg-surface-card rounded-2xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container transition-all hover:-translate-y-1 hover:shadow-lg cursor-pointer relative flex flex-col justify-between${pendingClasses}">
                <div class="h-44 w-full relative overflow-hidden bg-surface-container-low">
                    <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="${imgHtml}" alt="${name}"/>
                    <a href="${url}" class="absolute inset-0 z-10" aria-label="${name}"></a>
                    <button type="button"
                            data-save-btn="1"${btnLoadingAttr}
                            onmousedown="event.stopPropagation()"
                            ontouchstart="event.stopPropagation()"
                            onclick="event.preventDefault(); event.stopPropagation(); toggleSavePlace(this, '${id}')"
                            class="absolute top-3 right-3 z-30 pointer-events-auto bg-white/90 hover:bg-white backdrop-blur-sm w-9 h-9 flex items-center justify-center rounded-full text-red-500 shadow-sm hover:scale-110 active:scale-95 transition-all cursor-pointer"
                            title="Bỏ lưu địa điểm"
                            aria-label="Bỏ lưu địa điểm">
                        <span class="material-symbols-outlined text-[20px] pointer-events-none" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    </button>
                </div>
                <div class="p-4 space-y-1.5 relative">
                    <a href="${url}" class="absolute inset-0 z-0" aria-label="${name}"></a>
                    <h4 class="font-title-md text-sm md:text-base font-bold text-on-surface truncate group-hover:text-primary transition-colors relative z-10 pointer-events-none">${name}</h4>
                    <div class="flex items-center justify-between text-xs relative z-10 pointer-events-none pt-0.5">
                        <span class="text-text-muted truncate max-w-[65%]" title="${location}">${location}</span>
                        ${statusBadge}
                    </div>
                </div>
            </div>
        `;
    }

    function buildSavedEmptyHtml() {
        return `
            <div class="bg-surface-card rounded-2xl border border-surface-container p-8 md:p-12 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto">
                    <span class="material-symbols-outlined text-3xl">favorite</span>
                </div>
                <h4 class="text-base md:text-lg font-bold text-on-surface">Bạn chưa lưu địa điểm nào</h4>
                <p class="text-xs md:text-sm text-text-muted max-w-md mx-auto">Khám phá các nhà hàng, quán ăn ngon và bấm vào biểu tượng lưu để xem lại nhanh chóng tại đây bất cứ lúc nào.</p>
                <div class="pt-2">
                    <a href="${escapeSavedHtml(khamPhaUrl)}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-md shadow-primary/20 transition-all">
                        <span class="material-symbols-outlined text-[18px]">travel_explore</span>
                        <span>Khám phá quán ngon ngay</span>
                    </a>
                </div>
            </div>
        `;
    }

    function renderSavedPlacesSection() {
        const viewAllLink = document.getElementById('saved-view-all-link');
        const viewAllCount = document.getElementById('saved-view-all-count');
        const body = document.getElementById('saved-places-body');
        if (!body) return;

        const total = savedPlacesState.length;

        if (viewAllLink) {
            viewAllLink.classList.toggle('hidden', total === 0);
        }
        if (viewAllCount) {
            viewAllCount.textContent = `(${total})`;
            viewAllCount.classList.toggle('hidden', total <= 5);
        }

        if (total === 0) {
            body.innerHTML = buildSavedEmptyHtml();
            return;
        }

        const visibleItems = savedPlacesState.slice(0, 5);
        const isSingle = visibleItems.length === 1;
        const cardsHtml = visibleItems
            .map((item, idx) => buildSavedCardHtml(item, idx === 0, isSingle))
            .join('');

        const extraCount = Math.max(0, total - 5);
        const loadMoreClass = extraCount > 0 ? '' : ' hidden';

        body.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter" id="saved-places-grid">
                ${cardsHtml}
            </div>
            <div id="saved-load-more-wrap" class="pt-4 text-center${loadMoreClass}">
                <a href="${escapeSavedHtml(quanDaLuuIndexUrl)}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl border border-primary/20 bg-primary/5 hover:bg-primary hover:text-white text-primary text-xs md:text-sm font-bold shadow-xs hover:shadow-md transition-all group">
                    <span id="saved-load-more-text">Xem thêm ${extraCount} địa điểm đã lưu</span>
                    <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>
        `;
    }

    async function refreshSavedPlacesFromServer() {
        if (!window.QuanMoiSavedSync) return;
        try {
            const syncData = await window.QuanMoiSavedSync.fetchSyncState({ withPlaces: true });
            if (syncData?.authenticated && Array.isArray(syncData.places)) {
                savedPlacesState = syncData.places;
                renderSavedPlacesSection();
            }
        } catch (_) {}
    }

    window.onQuanMoiSavedPlacesSync = function(msg) {
        if (!msg || !msg.quanId) return;
        const id = String(msg.quanId);

        if (!msg.saved) {
            const beforeLen = savedPlacesState.length;
            savedPlacesState = savedPlacesState.filter(p => String(p.id) !== id);
            if (savedPlacesState.length !== beforeLen) {
                renderSavedPlacesSection();
            }
            return;
        }

        if (!savedPlacesState.some(p => String(p.id) === id)) {
            refreshSavedPlacesFromServer();
        }
    };

    window.onQuanMoiSavedPlacesFullSync = function() {
        refreshSavedPlacesFromServer();
    };

    window.toggleSavePlace = async function(btn, quanId) {
        const id = String(quanId || '');
        if (!id || inFlightSavedIds.has(id) || btn?.dataset.loading === 'true') return;

        const card = document.getElementById(`saved-card-${id}`);
        const item = savedPlacesState.find(p => String(p.id) === id);
        const quanName = item?.name || card?.querySelector('h4')?.textContent?.trim() || '';
        let quanImg = item?.image || card?.querySelector('img')?.getAttribute('src') || null;
        if (!quanImg && card) {
            const bgDiv = card.querySelector('[style*="background-image"]');
            const match = bgDiv?.style?.backgroundImage?.match(/url\(["']?([^"']+)["']?\)/);
            if (match && match[1]) quanImg = match[1];
        }

        inFlightSavedIds.add(id);
        setSavedCardPending(id, true);

        try {
            const result = window.QuanMoiSavedSync
                ? await window.QuanMoiSavedSync.sendToggleRequest(id, 'unsave')
                : { ok: false, data: null };

            if (result.unauthenticated) {
                inFlightSavedIds.delete(id);
                setSavedCardPending(id, false);
                if (typeof window.showToast === 'function') {
                    window.showToast({
                        title: 'Phiên đăng nhập đã hết hạn',
                        message: 'Vui lòng đăng nhập lại để tiếp tục.',
                        type: 'warning',
                        actionText: 'Đăng nhập ngay',
                        actionUrl: loginUrl
                    });
                } else {
                    window.location.href = loginUrl;
                }
                return;
            }

            if (result.csrfExpired) {
                inFlightSavedIds.delete(id);
                setSavedCardPending(id, false);
                if (typeof window.showToast === 'function') {
                    window.showToast({
                        title: 'Phiên làm việc đã hết hạn',
                        message: 'Vui lòng tải lại trang để cập nhật mã bảo mật và thử lại.',
                        type: 'warning',
                        actionText: 'Tải lại trang',
                        actionUrl: window.location.href
                    });
                }
                return;
            }

            if (!result.ok) {
                inFlightSavedIds.delete(id);
                if (result.data?.already_unsaved) {
                    savedPlacesState = savedPlacesState.filter(p => String(p.id) !== id);
                    renderSavedPlacesSection();
                } else {
                    setSavedCardPending(id, false);
                }
                if (typeof window.showToast === 'function') {
                    window.showToast({
                        title: result.data?.already_unsaved ? 'Xóa không hợp lệ' : 'Không thể thực hiện',
                        message: result.data?.message || 'Đã có lỗi xảy ra, vui lòng thử lại.',
                        type: 'error'
                    });
                }
                return;
            }

            inFlightSavedIds.delete(id);
            savedPlacesState = savedPlacesState.filter(p => String(p.id) !== id);
            window.QuanMoiSavedSync?.broadcastChange(id, false, result.data?.saved_count);

            if (typeof window.showToast === 'function') {
                window.showToast({
                    eyebrow: 'Đã cập nhật danh sách',
                    title: quanName ? `Đã bỏ lưu "${quanName}"` : 'Đã bỏ lưu quán',
                    message: 'Địa điểm đã được gỡ khỏi danh sách yêu thích của bạn.',
                    type: 'unsaved',
                    image: quanImg
                });
            }

            const currentCard = document.getElementById(`saved-card-${id}`);
            if (currentCard) {
                currentCard.style.transition = 'all 0.24s cubic-bezier(0.4, 0, 0.2, 1)';
                currentCard.style.opacity = '0';
                currentCard.style.transform = 'scale(0.92)';
                setTimeout(renderSavedPlacesSection, 240);
            } else {
                renderSavedPlacesSection();
            }
        } catch (err) {
            console.error(err);
            inFlightSavedIds.delete(id);
            setSavedCardPending(id, false);
            if (typeof window.showToast === 'function') {
                window.showToast({
                    title: 'Lỗi kết nối',
                    message: 'Không thể gửi yêu cầu đến máy chủ.',
                    type: 'error'
                });
            }
        }
    };
</script>
@endsection
