@php
    $user = Auth::user();
@endphp
<aside class="md:col-span-3 space-y-stack-sm">
    <div class="bg-surface-card p-stack-md rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container">
        <div class="flex flex-col items-center mb-stack-lg text-center">
            <form id="avatar-form" action="{{ route('tai-khoan.update') }}" method="POST" enctype="multipart/form-data" class="relative mb-stack-sm">
                @csrf
                @method('PUT')
                <input type="hidden" name="ho_ten" value="{{ $user->ho_ten }}">
                <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-primary-container p-0.5">
                    <img class="w-full h-full object-cover rounded-full" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($user->ho_ten) }}&background=ffdbcc&color=a04100&bold=true&size=256';" src="{{ $user->anh_dai_dien ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->ho_ten) . '&background=ffdbcc&color=a04100&bold=true&size=256' }}" />
                </div>
                <label for="anh_dai_dien" class="absolute bottom-0 right-0 bg-primary p-1.5 rounded-full text-white shadow-md hover:scale-110 transition-transform cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                </label>
                <input type="file" name="anh_dai_dien" id="anh_dai_dien" class="hidden" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="document.getElementById('avatar-form').submit()">
            </form>
            <h2 class="font-title-md text-title-md text-on-surface flex items-center gap-1">
                {{ $user->ho_ten }}
                @if($user->da_xac_thuc)
                    <span class="material-symbols-outlined text-tick-xanh" style="font-variation-settings: 'FILL' 1;">verified</span>
                @endif
            </h2>
            <p class="font-body-sm text-body-sm text-text-muted">Thành viên từ {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('m/Y') : 'N/A' }}</p>
        </div>
        <nav class="flex flex-col gap-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('tai-khoan.*') ? 'bg-primary-container/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }} transition-all" href="{{ route('tai-khoan.index') }}">
                <span class="material-symbols-outlined">person</span>
                <span class="font-body-lg text-body-lg">Hồ sơ cá nhân</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('nguoi-dung.blog.*') ? 'bg-primary-container/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }} transition-all" href="{{ route('nguoi-dung.blog.index') }}">
                <span class="material-symbols-outlined">article</span>
                <span class="font-body-lg text-body-lg">Quản lý bài viết</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container transition-all" href="#">
                <span class="material-symbols-outlined">settings</span>
                <span class="font-body-lg text-body-lg">Cài đặt</span>
            </a>
            <hr class="my-2 border-outline-variant opacity-50"/>
            <button type="button" onclick="openLogoutModal()" class="flex w-full items-center gap-3 px-4 py-3 rounded-lg text-error hover:bg-error-container transition-all">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-body-lg text-body-lg">Đăng xuất</span>
            </button>
        </nav>
    </div>
    <!-- Verification Status Card -->
    <div class="bg-tertiary-container/10 border border-tertiary-container/30 p-stack-md rounded-xl">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-tertiary" style="font-variation-settings: 'FILL' 1;">verified_user</span>
            <div>
                <p class="font-label-md text-label-md text-on-tertiary-container uppercase tracking-wider">Trạng thái tài khoản</p>
                <p class="font-title-md text-title-md text-tertiary">{{ $user->ten_vai_tro_hien_thi }}</p>
                <p class="font-body-sm text-body-sm text-on-tertiary-container/70 mt-1">Bạn có toàn quyền truy cập chức năng hệ thống theo vai trò của mình.</p>
            </div>
        </div>
    </div>
</aside>
