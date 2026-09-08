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
    <div id="toast-success" class="mb-6 flex items-center gap-3 bg-white border border-green-200 rounded-2xl px-5 py-4 shadow-lg shadow-green-100/50">
        <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-green-600" style="font-variation-settings:'FILL' 1">check_circle</span>
        </div>
        <div class="flex-1">
            <p class="font-bold text-gray-900 text-sm">Cập nhật thành công!</p>
            <p class="text-gray-500 text-xs mt-0.5">{{ session('success') }}</p>
        </div>
        <button onclick="document.getElementById('toast-success').remove()" class="text-gray-400 hover:text-gray-600 transition-colors">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-stack-lg">
        <!-- Sidebar Navigation -->
        @include('nguoi-dung.partials.sidebar')

        <!-- Main Content Area -->
        <div class="md:col-span-9 space-y-stack-lg">
            <!-- Profile Information Section -->
            <section class="bg-surface-card p-stack-lg rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container">
                <form action="{{ route('tai-khoan.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="flex justify-between items-center mb-stack-lg">
                        <h3 class="font-headline-lg text-headline-lg text-on-surface">Thông tin cá nhân</h3>
                        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-xl font-label-md text-label-md active:scale-95 transition-all shadow-lg hover:shadow-primary/20">LƯU THAY ĐỔI</button>
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
                <form action="{{ route('tai-khoan.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="flex justify-between items-center mb-stack-lg">
                        <h3 class="font-headline-lg text-headline-lg text-on-surface">Bảo mật & Mật khẩu</h3>
                        <button type="submit" class="bg-surface-container-highest text-on-surface-variant hover:text-primary px-6 py-2 rounded-xl font-label-md text-label-md active:scale-95 transition-all shadow-sm">CẬP NHẬT MẬT KHẨU</button>
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

            <!-- Saved Places Section (Static Mockup from User) -->
            <section>
                <div class="flex justify-between items-end mb-stack-md">
                    <div>
                        <h3 class="font-headline-lg text-headline-lg text-on-surface">Địa điểm đã lưu</h3>
                        <p class="font-body-sm text-body-sm text-text-muted">Danh sách các nhà hàng và quán yêu thích của bạn</p>
                    </div>
                    <a class="font-label-md text-label-md text-primary hover:underline" href="#">Xem tất cả</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                    <!-- Card 1 -->
                    <div class="md:col-span-2 relative group rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container active:scale-[0.98] transition-transform cursor-pointer">
                        <div class="h-64 w-full bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuB3jqwH813tiSNEvFGv_EaYpRbcPOShZZtE2YqrBax43KOL1RBtHwwZ-ygBaADC8pOWQxovokZTFf5QUtpygXzcfr--UFqZQC0ju7oviYRmiVVnZ0GpAW45jP4fJJ4ODvYyegbPDL-iA2vKexecE3-uGd41fQzVL5VRuwCBWU2kNeL0zDd5_2rw7pOtpotKX3EwxfqrzWjzg_-4RV96KSZvfGA85rBDWWgxX53V9XJo00KS3YZDUfe6bY17z1-efbGzO2pHDSSH1Hw')"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-0 p-stack-md w-full">
                            <div class="flex justify-between items-end">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Đánh giá cao</span>
                                        <span class="flex items-center text-white text-label-sm"><span class="material-symbols-outlined text-sm mr-1" style="font-variation-settings: 'FILL' 1;">star</span> 4.9</span>
                                    </div>
                                    <h4 class="text-white font-title-md text-title-md">Phở Gia Truyền - Bát Đàn</h4>
                                    <p class="text-white/80 text-body-sm flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">location_on</span> Hoàn Kiếm, Hà Nội</p>
                                </div>
                                <button class="bg-white/20 backdrop-blur-md p-2 rounded-full text-white border border-white/30">
                                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="group bg-surface-card rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container active:scale-[0.98] transition-transform cursor-pointer">
                        <div class="h-40 w-full relative">
                            <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDhXp2aELz0P35wu9Gs_jLu_fZHZObzZKDRlbQMwQuhPnFA60JER_sxAGnBQrH5ajRz_WPnpgn1xhVTL1JK_bN010gUh5qhnG_rkFgBdzfGQpKaKnawowrS4R3ShYqvnFKSirJpu4DC_eX1pDexpyMNTmD5a4jr-_s5zHqZWKPNci4vcaFh83bV1M2VGK4klUvq0CJJoGvWGSDA3l4994sQ2HlmsI6TJrXYuXfEFV12ck--GufY9hkU6kN4M2JFyQpGNkpxxiwiF3w"/>
                            <button class="absolute top-3 right-3 bg-white/80 backdrop-blur-sm p-1.5 rounded-full text-primary shadow-sm">
                                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                            </button>
                        </div>
                        <div class="p-stack-sm space-y-1">
                            <h4 class="font-title-md text-title-md text-on-surface truncate">The Workshop Coffee</h4>
                            <div class="flex items-center justify-between">
                                <span class="text-body-sm text-text-muted">Quận 1, HCM</span>
                                <span class="text-tick-xanh font-bold text-label-sm">ĐANG MỞ</span>
                            </div>
                        </div>
                    </div>
                    <!-- Card 3 -->
                    <div class="group bg-surface-card rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container active:scale-[0.98] transition-transform cursor-pointer">
                        <div class="h-40 w-full relative">
                            <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDq5dCIh8Av4FojFGW4EV28OX4d-O0cxOWyKChOthbzav-vrZSTmJjWEK68TbmyZyjqrrMyqR8MfoGbjM2SWchLMLbjSBAcDGeTZ7Q9LHhN5WyaktILdP7IeJlA2GuEkmvY_dr1xmiaOZUmOiYRx0scHd1EMYGg51ezJq175ovPCHpn8tJ030JZIuB-wcYOC0FfiEtXxSnfL_XvXo07m23iRYBSwB5QXABRMLiAaCSMrSogP2UgHfJIYpHtuHV97B0faMgwnrCEg-s"/>
                            <button class="absolute top-3 right-3 bg-white/80 backdrop-blur-sm p-1.5 rounded-full text-primary shadow-sm">
                                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                            </button>
                        </div>
                        <div class="p-stack-sm space-y-1">
                            <h4 class="font-title-md text-title-md text-on-surface truncate">Bếp Mẹ Ỉn</h4>
                            <div class="flex items-center justify-between">
                                <span class="text-body-sm text-text-muted">Lê Thánh Tôn, HCM</span>
                                <span class="text-error font-bold text-label-sm">ĐÓNG CỬA</span>
                            </div>
                        </div>
                    </div>
                    <!-- Card 4 -->
                    <div class="group bg-surface-card rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container active:scale-[0.98] transition-transform cursor-pointer">
                        <div class="h-40 w-full relative">
                            <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCZCzphAGbwOAFrMJki4D9PSCH_OB5VhtWdhKOGTVdcSTPDY38IUFHjTnBvkUjGCSed0TsbmLMd_JU97pll9OwAqdXRj2pROKMFcGK0YNDbjQ7eAtFg0b_VHj0UIeIG4H5M2lNmhCGlZzAUuFy5q2mjn80rsiHyMA5kwcsM3PQZEHJVSt4nc5rGF8O9RxxX34-QRn6G-2xbDB0NmDptfHE_9e3NpjQN3KrpkLTw55wlzdITHe5tM8xoy-XXGQF-XeNIaPV7GWGwnlM"/>
                            <button class="absolute top-3 right-3 bg-white/80 backdrop-blur-sm p-1.5 rounded-full text-primary shadow-sm">
                                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                            </button>
                        </div>
                        <div class="p-stack-sm space-y-1">
                            <h4 class="font-title-md text-title-md text-on-surface truncate">Sushi Tony</h4>
                            <div class="flex items-center justify-between">
                                <span class="text-body-sm text-text-muted">Quận 7, HCM</span>
                                <span class="text-tick-xanh font-bold text-label-sm">ĐANG MỞ</span>
                            </div>
                        </div>
                    </div>
                    <!-- Card 5 -->
                    <div class="group bg-surface-card rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container active:scale-[0.98] transition-transform cursor-pointer">
                        <div class="h-40 w-full relative">
                            <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAFhuaCGLfF8hdgQN0B2sir216mHF3CQSFxxxM3CFWMgW9HISDEaenLCaFC6BaU13-mT669wBM_EpORfmxic64eYnVSdVLrUQrh2WqLCrEDdOeXiKHaknOmRcuBkSZ3TaNXtXxukreQNLbsyp7ZcyEq_M3i2Hf7fUAAYHWQTBTRn9WW4s4oc6H1LVCdkr6U5WWlxNqMONJzYdWxcJ-XG-q40UY68qd2fM07D3Ey8d02eMxY_Avj1wlPOicpzamqcfoF0D_uwJKsj40"/>
                            <button class="absolute top-3 right-3 bg-white/80 backdrop-blur-sm p-1.5 rounded-full text-primary shadow-sm">
                                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                            </button>
                        </div>
                        <div class="p-stack-sm space-y-1">
                            <h4 class="font-title-md text-title-md text-on-surface truncate">Maison Marou</h4>
                            <div class="flex items-center justify-between">
                                <span class="text-body-sm text-text-muted">Hà Nội & HCM</span>
                                <span class="text-tick-xanh font-bold text-label-sm">ĐANG MỞ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>
@endsection
