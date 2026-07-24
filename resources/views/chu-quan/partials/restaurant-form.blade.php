@php
    $isEditing = $isEditing ?? false;
    $pageTitle = $pageTitle ?? ($isEditing ? 'Cap nhat quan an cua ban' : 'Dang quan an moi');
    $pageSubtitle = $pageSubtitle ?? ($isEditing
        ? 'Cap nhat hinh anh, dia chi, menu va thong tin lien he de trang quan cua ban luon day du.'
        : 'Tao bai dang quan an voi giao dien dep, dung toa do, co thu vien anh va san sang cho duyet.');
    $heroBadge = $heroBadge ?? ($isEditing ? 'Owner update' : 'Owner onboard');
    $formAction = $formAction ?? route('chu-quan.quan.store');
    $formMethod = $formMethod ?? 'POST';
    $backUrl = $backUrl ?? route('chu-quan.quan.index');
    $submitPrimaryLabel = $submitPrimaryLabel ?? ($isEditing ? 'Cap nhat quán' : 'Dang bai');
    $draftLabel = $draftLabel ?? 'Luu nhap';
    $selectedType = old('loai_hinh_kinh_doanh', $quan->loai_hinh_kinh_doanh ?? ($businessTypes[0] ?? 'Quan an'));
    $coverPreview = old('anh_bia_existing', !empty($quan?->anh_bia) ? asset('storage/' . $quan->anh_bia) : null);
    $avatarPreview = old('anh_dai_dien_existing', !empty($quan?->anh_dai_dien) ? asset('storage/' . $quan->anh_dai_dien) : null);
    $existingGallery = $quan?->hinhAnh ?? collect();
    $selectedAmenities = collect($selectedAmenities ?? old('tien_ich', $quan?->tien_ich ?? []))
        ->filter(fn ($item) => filled($item))
        ->values()
        ->all();
@endphp

<div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(255,107,0,0.16),_transparent_34%),linear-gradient(180deg,_#fff7f1_0%,_#ffffff_38%,_#fff9f5_100%)]">
    <main class="pt-24 pb-20 px-4 md:px-8 max-w-7xl mx-auto">
        @if ($errors->any())
            <div class="mb-6 rounded-[28px] border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-2xl">error</span>
                    <div>
                        <p class="font-black text-[15px]">Thong tin chua hop le.</p>
                        <p class="mt-1">Kiem tra lai cac truong bat buoc va lien ket truoc khi luu.</p>
                    </div>
                </div>
            </div>
        @endif

        <section class="grid grid-cols-1 xl:grid-cols-[1.15fr_0.85fr] gap-6 xl:gap-8">
            <div class="space-y-6">
                <div class="rounded-[34px] border border-primary/10 bg-white/90 backdrop-blur-md shadow-[0_18px_48px_rgba(160,65,0,0.08)] overflow-hidden">
                    <div class="p-6 md:p-8 lg:p-10 relative">
                        <div class="absolute top-0 right-0 h-40 w-40 rounded-full bg-primary/10 blur-3xl"></div>
                        <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-[12px] font-black uppercase tracking-[0.18em] text-primary">
                            <span class="material-symbols-outlined text-[18px]">storefront</span>
                            {{ $heroBadge }}
                        </span>
                        <div class="mt-5 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                            <div class="max-w-2xl">
                                <h1 class="text-3xl md:text-5xl font-black text-on-surface leading-tight">{{ $pageTitle }}</h1>
                                <p class="mt-3 text-[15px] md:text-[16px] leading-7 text-on-surface-variant">{{ $pageSubtitle }}</p>
                            </div>
                            <a href="{{ $backUrl }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:border-primary/30 hover:text-primary transition-all">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                Quay lai quan ly
                            </a>
                        </div>

                        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="rounded-2xl bg-[#fff7f1] border border-primary/10 px-4 py-4">
                                <p class="text-[11px] font-black uppercase tracking-[0.18em] text-primary/70">Trang thai</p>
                                <p class="mt-2 text-[15px] font-black text-on-surface">
                                    @if (($quan->la_nhap ?? false))
                                        Ban nhap
                                    @elseif (($quan->trang_thai ?? null) === 'da_duyet')
                                        Da duyet
                                    @elseif (($quan->trang_thai ?? null) === 'bi_khoa')
                                        Bi khoa
                                    @else
                                        Cho duyet
                                    @endif
                                </p>
                            </div>
                            <div class="rounded-2xl bg-white border border-gray-100 px-4 py-4">
                                <p class="text-[11px] font-black uppercase tracking-[0.18em] text-gray-400">Anh thu vien</p>
                                <p class="mt-2 text-[15px] font-black text-on-surface">{{ $existingGallery->count() }}</p>
                            </div>
                            <div class="rounded-2xl bg-white border border-gray-100 px-4 py-4">
                                <p class="text-[11px] font-black uppercase tracking-[0.18em] text-gray-400">Loai bai dang</p>
                                <p class="mt-2 text-[15px] font-black text-on-surface">{{ $selectedType }}</p>
                            </div>
                            <div class="rounded-2xl bg-white border border-gray-100 px-4 py-4">
                                <p class="text-[11px] font-black uppercase tracking-[0.18em] text-gray-400">Toa do</p>
                                <p class="mt-2 text-[15px] font-black text-on-surface">
                                    {{ old('vi_do', $quan->vi_do ?? '10.7769') }},
                                    {{ old('kinh_do', $quan->kinh_do ?? '106.7009') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="dang-quan-form" action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @if (strtoupper($formMethod) !== 'POST')
                        @method($formMethod)
                    @endif

                    <input type="hidden" name="anh_bia_existing" value="{{ $coverPreview }}">
                    <input type="hidden" name="anh_dai_dien_existing" value="{{ $avatarPreview }}">
                    <input type="hidden" id="loai_hinh_kinh_doanh" name="loai_hinh_kinh_doanh" value="{{ $selectedType }}">

                    <section class="rounded-[30px] border border-gray-100 bg-white p-5 md:p-7 shadow-[0_16px_40px_rgba(15,23,42,0.05)]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                <span class="material-symbols-outlined">edit_square</span>
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-black text-on-surface">Thong tin co ban</h2>
                                <p class="text-sm text-on-surface-variant mt-1">Phan nay giup bai dang hien dung ten, loai hinh va khoang gia.</p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-6">
                            <div>
                                <label class="mb-3 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Danh muc quan</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="business-type-grid">
                                    @foreach ($businessTypes as $type)
                                        <button
                                            type="button"
                                            class="type-chip rounded-[24px] border px-4 py-4 text-left transition-all {{ $selectedType === $type ? 'border-primary bg-primary/10 text-primary shadow-sm' : 'border-gray-200 bg-white text-on-surface hover:border-primary/40 hover:bg-primary/5' }}"
                                            data-type="{{ $type }}"
                                        >
                                            <span class="block text-[13px] font-black">{{ $type }}</span>
                                            <span class="mt-1 block text-[12px] text-current/70">{{ $loop->first ? 'Pho bien' : 'Phu hop review cong dong' }}</span>
                                        </button>
                                    @endforeach
                                </div>
                                @error('loai_hinh_kinh_doanh') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="ten_quan" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Ten quan <span class="text-red-500">*</span></label>
                                    <input id="ten_quan" name="ten_quan" type="text" value="{{ old('ten_quan', $quan->ten_quan ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Vi du: Banh cuon Co Lan" required>
                                    @error('ten_quan') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="so_dien_thoai" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">So dien thoai <span class="text-red-500">*</span></label>
                                    <input id="so_dien_thoai" name="so_dien_thoai" type="text" value="{{ old('so_dien_thoai', $quan->so_dien_thoai ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="0901 234 567" required>
                                    @error('so_dien_thoai') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                                <div class="xl:col-span-2">
                                    <label for="email" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Email lien he</label>
                                    <input id="email" name="email" type="email" value="{{ old('email', $quan->email ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="owner@example.com">
                                    @error('email') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="gio_mo_cua" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Mo cua</label>
                                    <input id="gio_mo_cua" name="gio_mo_cua" type="time" value="{{ old('gio_mo_cua', $quan->gio_mo_cua ?? '08:00') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" required>
                                    @error('gio_mo_cua') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="gio_dong_cua" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Dong cua</label>
                                    <input id="gio_dong_cua" name="gio_dong_cua" type="time" value="{{ old('gio_dong_cua', $quan->gio_dong_cua ?? '22:00') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" required>
                                    @error('gio_dong_cua') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="gia_nho_nhat" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Gia tu</label>
                                    <input id="gia_nho_nhat" name="gia_nho_nhat" type="number" min="0" step="1000" value="{{ old('gia_nho_nhat', $quan->gia_nho_nhat ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="30000">
                                    @error('gia_nho_nhat') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="gia_lon_nhat" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Gia den</label>
                                    <input id="gia_lon_nhat" name="gia_lon_nhat" type="number" min="0" step="1000" value="{{ old('gia_lon_nhat', $quan->gia_lon_nhat ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="150000">
                                    @error('gia_lon_nhat') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[30px] border border-gray-100 bg-white p-5 md:p-7 shadow-[0_16px_40px_rgba(15,23,42,0.05)]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-secondary/10 text-secondary">
                                <span class="material-symbols-outlined">map</span>
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-black text-on-surface">Dia chi va ban do</h2>
                                <p class="text-sm text-on-surface-variant mt-1">Chon dung khu vuc, tu dong lay toa do va co the cham len ban do de tinh chinh.</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 lg:grid-cols-[0.95fr_1.05fr] gap-5">
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="select-tinh" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Tinh / thanh pho</label>
                                        <select id="select-tinh" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10">
                                            <option value="">Dang tai...</option>
                                        </select>
                                        <input type="hidden" id="tinh_thanh_id" name="tinh_thanh_id" value="{{ old('tinh_thanh_id', $quan->tinh_thanh_id ?? '') }}">
                                        <input type="hidden" id="ten_tinh_thanh" name="ten_tinh_thanh" value="{{ old('ten_tinh_thanh', $quan->ten_tinh_thanh ?? '') }}">
                                        @error('ten_tinh_thanh') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="select-huyen" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Quan / huyen</label>
                                        <select id="select-huyen" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" disabled>
                                            <option value="">Chon quan / huyen</option>
                                        </select>
                                        <input type="hidden" id="quan_huyen_id" name="quan_huyen_id" value="{{ old('quan_huyen_id', $quan->quan_huyen_id ?? '') }}">
                                        <input type="hidden" id="ten_quan_huyen" name="ten_quan_huyen" value="{{ old('ten_quan_huyen', $quan->ten_quan_huyen ?? '') }}">
                                        @error('ten_quan_huyen') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="select-xa" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Phuong / xa</label>
                                        <select id="select-xa" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" disabled>
                                            <option value="">Chon phuong / xa</option>
                                        </select>
                                        <input type="hidden" id="phuong_xa_id" name="phuong_xa_id" value="{{ old('phuong_xa_id', $quan->phuong_xa_id ?? '') }}">
                                        <input type="hidden" id="ten_phuong_xa" name="ten_phuong_xa" value="{{ old('ten_phuong_xa', $quan->ten_phuong_xa ?? '') }}">
                                        @error('ten_phuong_xa') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="dia_chi_chi_tiet" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">So nha, ten duong</label>
                                    <textarea id="dia_chi_chi_tiet" name="dia_chi_chi_tiet" rows="3" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Vi du: 19 Tran Hung Dao, gan cho trung tam">{{ old('dia_chi_chi_tiet', $quan->dia_chi_chi_tiet ?? '') }}</textarea>
                                    @error('dia_chi_chi_tiet') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="rounded-[24px] border border-gray-100 bg-[#fffaf6] p-4">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <p class="text-[12px] font-black uppercase tracking-[0.18em] text-primary/70">Dia chi hien thi</p>
                                            <p id="full-address-preview" class="mt-1 text-sm font-bold text-on-surface">Chua co dia chi day du.</p>
                                        </div>
                                        <button type="button" id="geocode-btn" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-primary px-4 py-3 text-sm font-black text-white shadow-sm transition-all hover:bg-primary/90">
                                            <span class="material-symbols-outlined text-[18px]">my_location</span>
                                            Lay toa do tu dia chi
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="vi_do" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Latitude</label>
                                        <input id="vi_do" name="vi_do" type="number" step="0.0000001" value="{{ old('vi_do', $quan->vi_do ?? '10.7769') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10">
                                        @error('vi_do') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="kinh_do" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Longitude</label>
                                        <input id="kinh_do" name="kinh_do" type="number" step="0.0000001" value="{{ old('kinh_do', $quan->kinh_do ?? '106.7009') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10">
                                        @error('kinh_do') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="rounded-[28px] border border-gray-100 overflow-hidden bg-white shadow-sm">
                                    <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                        <div>
                                            <p class="text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Map picker</p>
                                            <p class="text-xs text-gray-400 mt-1">Cham len ban do de cap nhat marker.</p>
                                        </div>
                                        <span class="rounded-full bg-primary/10 px-3 py-1 text-[11px] font-black text-primary">Leaflet + OpenStreetMap</span>
                                    </div>
                                    <div id="owner-map" class="h-[320px] w-full"></div>
                                </div>
                                <div class="rounded-[24px] border border-dashed border-primary/20 bg-primary/5 p-4 text-sm text-on-surface-variant">
                                    <p class="font-black text-on-surface">Mẹo:</p>
                                    <p class="mt-2">Neu ban da chon du tinh/thanh pho, quan/huyen va so nha, nut "Lay toa do tu dia chi" se geocode tu dong. Neu can chinh chuan hon, ban co the cham vao map de doi marker.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[30px] border border-gray-100 bg-white p-5 md:p-7 shadow-[0_16px_40px_rgba(15,23,42,0.05)]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                <span class="material-symbols-outlined">imagesmode</span>
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-black text-on-surface">Hinh anh noi bat</h2>
                                <p class="text-sm text-on-surface-variant mt-1">Tai anh bia, avatar va nhieu anh thu vien. Co preview truoc khi luu.</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 xl:grid-cols-[1.1fr_0.9fr] gap-5">
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label class="group rounded-[28px] border border-dashed border-primary/25 bg-[#fff8f2] p-4 cursor-pointer hover:border-primary/50 transition-all">
                                        <div class="mb-3 flex items-center justify-between">
                                            <span class="text-[12px] font-black uppercase tracking-[0.18em] text-primary/70">Anh bia</span>
                                            <span class="rounded-full bg-white px-3 py-1 text-[11px] font-black text-primary shadow-sm">16:9</span>
                                        </div>
                                        <div class="overflow-hidden rounded-[24px] bg-white border border-white/70 shadow-inner">
                                            <img id="cover-preview-image" src="{{ $coverPreview ?: 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=1000&q=80' }}" alt="Anh bia" class="h-56 w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
                                        </div>
                                        <input id="anh_bia" name="anh_bia" type="file" accept="image/*" class="hidden">
                                        <p class="mt-3 text-sm font-bold text-on-surface">Chon anh bia cho banner chi tiet</p>
                                        <p class="mt-1 text-xs text-gray-500">Nen dung anh ngang ro net, sang mau va co diem nhan.</p>
                                        @error('anh_bia') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </label>

                                    <label class="group rounded-[28px] border border-dashed border-gray-200 bg-white p-4 cursor-pointer hover:border-primary/40 transition-all">
                                        <div class="mb-3 flex items-center justify-between">
                                            <span class="text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Anh dai dien</span>
                                            <span class="rounded-full bg-primary/10 px-3 py-1 text-[11px] font-black text-primary">Avatar</span>
                                        </div>
                                        <div class="flex h-56 items-center justify-center rounded-[24px] border border-gray-100 bg-[#fffaf6]">
                                            <img id="avatar-preview-image" src="{{ $avatarPreview ?: 'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=700&q=80' }}" alt="Anh dai dien" class="h-36 w-36 rounded-full object-cover border-[6px] border-white shadow-lg">
                                        </div>
                                        <input id="anh_dai_dien" name="anh_dai_dien" type="file" accept="image/*" class="hidden">
                                        <p class="mt-3 text-sm font-bold text-on-surface">Dung cho avatar va card preview</p>
                                        <p class="mt-1 text-xs text-gray-500">Nen la logo, mat tien quan hoac anh nhan dien thuong hieu.</p>
                                        @error('anh_dai_dien') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label for="gallery_images" id="gallery-dropzone" class="block rounded-[28px] border border-dashed border-primary/30 bg-[#fff8f2] p-5 cursor-pointer transition-all hover:border-primary/60 hover:bg-primary/5">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-[12px] font-black uppercase tracking-[0.18em] text-primary/70">Thu vien anh</p>
                                            <h3 class="mt-2 text-xl font-black text-on-surface">Keo tha hoac chon nhieu anh</h3>
                                            <p class="mt-2 text-sm text-on-surface-variant">Co the tai toi da 8 anh moi trong mot lan. Cac anh nay se them vao gallery cua quan.</p>
                                        </div>
                                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-white shadow-sm">
                                            <span class="material-symbols-outlined text-[28px]">add_photo_alternate</span>
                                        </div>
                                    </div>
                                    <input id="gallery_images" name="gallery_images[]" type="file" accept="image/*" multiple class="hidden">
                                    @error('gallery_images') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    @error('gallery_images.*') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </label>

                                <div id="gallery-preview-grid" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @forelse ($existingGallery->take(6) as $image)
                                        <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-gray-50 aspect-square">
                                            <img src="{{ asset('storage/' . $image->duong_dan) }}" alt="Gallery" class="h-full w-full object-cover" loading="lazy">
                                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent px-3 py-2 text-[11px] font-black text-white">Anh da luu</div>
                                        </div>
                                    @empty
                                        <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">
                                            Chua co anh gallery nao. Cac anh ban tai len se preview tai day.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[30px] border border-gray-100 bg-white p-5 md:p-7 shadow-[0_16px_40px_rgba(15,23,42,0.05)]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-tertiary/10 text-tertiary">
                                <span class="material-symbols-outlined">article</span>
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-black text-on-surface">Mo ta va lien ket</h2>
                                <p class="text-sm text-on-surface-variant mt-1">Dien noi dung dang review, social link va nut affiliate dat mon.</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 xl:grid-cols-[1.05fr_0.95fr] gap-6">
                            <div>
                                <label class="mb-3 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Mo ta quan (rich text)</label>
                                <div class="overflow-hidden rounded-[28px] border border-gray-200">
                                    <div class="flex flex-wrap items-center gap-2 border-b border-gray-100 bg-[#fffaf6] px-4 py-3">
                                        <button type="button" class="editor-action rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-black text-gray-600 hover:border-primary hover:text-primary" data-command="bold">Bold</button>
                                        <button type="button" class="editor-action rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-black text-gray-600 hover:border-primary hover:text-primary" data-command="italic">Italic</button>
                                        <button type="button" class="editor-action rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-black text-gray-600 hover:border-primary hover:text-primary" data-command="insertUnorderedList">Bullet</button>
                                        <button type="button" class="editor-action rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-black text-gray-600 hover:border-primary hover:text-primary" data-command="insertOrderedList">Number</button>
                                        <button type="button" class="editor-action rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-black text-gray-600 hover:border-primary hover:text-primary" data-command="formatBlock" data-value="blockquote">Quote</button>
                                    </div>
                                    <div id="rich-editor" contenteditable="true" class="min-h-[220px] px-4 py-4 text-[15px] leading-7 text-on-surface focus:outline-none rich-content">{!! old('mo_ta', $quan->mo_ta ?? '<p>Hay gioi thieu diem manh cua quan: mon signature, khong gian, khach phu hop, uu dai...</p>') !!}</div>
                                    <textarea id="mo_ta" name="mo_ta" class="hidden">{{ old('mo_ta', $quan->mo_ta ?? '') }}</textarea>
                                </div>
                                @error('mo_ta') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="facebook_url" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Facebook</label>
                                        <input id="facebook_url" name="facebook_url" type="url" value="{{ old('facebook_url', $quan->facebook_url ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="https://facebook.com/ten-quan">
                                        @error('facebook_url') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="website_url" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Website</label>
                                        <input id="website_url" name="website_url" type="url" value="{{ old('website_url', $quan->website_url ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="https://tenquan.vn">
                                        @error('website_url') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="shopee_food_url" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Shopee Food affiliate link</label>
                                        <input id="shopee_food_url" name="shopee_food_url" type="url" value="{{ old('shopee_food_url', $quan->shopee_food_url ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="https://shopeefood.vn/...">
                                        @error('shopee_food_url') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="rounded-[28px] border border-gray-100 bg-[#fffaf6] p-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-[12px] font-black uppercase tracking-[0.18em] text-primary/70">Tien ich quan</p>
                                            <h3 class="mt-2 text-lg font-black text-on-surface">Danh dau nhung gi khach quan tam</h3>
                                        </div>
                                        <span class="rounded-full bg-white px-3 py-1 text-[11px] font-black text-primary shadow-sm">Optional</span>
                                    </div>
                                    <input type="hidden" name="tien_ich[]" value="">
                                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach ($amenityOptions as $amenity)
                                            <label class="flex items-center gap-3 rounded-2xl border px-4 py-3 transition-all {{ in_array($amenity, $selectedAmenities, true) ? 'border-primary bg-primary/10 text-primary' : 'border-gray-200 bg-white text-gray-700 hover:border-primary/30' }}">
                                                <input type="checkbox" name="tien_ich[]" value="{{ $amenity }}" class="rounded border-gray-300 text-primary focus:ring-primary" @checked(in_array($amenity, $selectedAmenities, true))>
                                                <span class="text-sm font-bold">{{ $amenity }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('tien_ich') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[30px] border border-gray-100 bg-white p-5 md:p-7 shadow-[0_16px_40px_rgba(15,23,42,0.05)]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-secondary/10 text-secondary">
                                <span class="material-symbols-outlined">travel_explore</span>
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-black text-on-surface">SEO co ban</h2>
                                <p class="text-sm text-on-surface-variant mt-1">Toi uu title va mo ta de link quan dep hon khi chia se.</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4">
                            <div>
                                <label for="meta_title" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Meta title</label>
                                <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title', $quan->meta_title ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Ten quan | mon signature | khu vuc">
                                @error('meta_title') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="meta_description" class="mb-2 block text-[12px] font-black uppercase tracking-[0.18em] text-gray-500">Meta description</label>
                                <textarea id="meta_description" name="meta_description" rows="3" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Mo ta ngan gon 140-160 ky tu cho trang chi tiet quan.">{{ old('meta_description', $quan->meta_description ?? '') }}</textarea>
                                @error('meta_description') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <div class="sticky bottom-4 z-20">
                        <div class="rounded-[28px] border border-primary/10 bg-white/95 backdrop-blur-md shadow-[0_18px_50px_rgba(15,23,42,0.12)] p-4 md:p-5">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-[12px] font-black uppercase tracking-[0.18em] text-primary/70">San sang xuat ban</p>
                                    <p class="mt-1 text-sm text-on-surface-variant">Ban co the luu nhap truoc, hoac gui duyet de quan hien tren trang cong khai sau khi admin kiem tra.</p>
                                </div>
                                <div class="flex flex-col-reverse sm:flex-row gap-3">
                                    <button type="submit" name="submit_action" value="draft" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-black text-gray-700 hover:border-primary/30 hover:text-primary transition-all">
                                        <span class="material-symbols-outlined text-[18px]">draft</span>
                                        {{ $draftLabel }}
                                    </button>
                                    <button type="submit" name="submit_action" value="publish" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3 text-sm font-black text-white shadow-sm hover:bg-primary/90 transition-all">
                                        <span class="material-symbols-outlined text-[18px]">publish</span>
                                        {{ $submitPrimaryLabel }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <aside class="space-y-6">
                <div class="rounded-[30px] border border-gray-100 bg-white p-5 md:p-6 shadow-[0_16px_40px_rgba(15,23,42,0.05)] xl:sticky xl:top-24">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <span class="material-symbols-outlined">insights</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-on-surface">Checklist bai dang</h2>
                            <p class="mt-1 text-sm text-on-surface-variant">Day la nhung muc nen co de bai len dep va day du.</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        @php
                            $tips = [
                                ['label' => 'Ten quan ro rang', 'done' => filled(old('ten_quan', $quan->ten_quan ?? ''))],
                                ['label' => 'Dia chi 3 cap + so nha', 'done' => filled(old('ten_tinh_thanh', $quan->ten_tinh_thanh ?? '')) && filled(old('ten_quan_huyen', $quan->ten_quan_huyen ?? '')) && filled(old('dia_chi_chi_tiet', $quan->dia_chi_chi_tiet ?? ''))],
                                ['label' => 'Co toa do ban do', 'done' => filled(old('vi_do', $quan->vi_do ?? '')) && filled(old('kinh_do', $quan->kinh_do ?? ''))],
                                ['label' => 'Co anh bia/anh avatar', 'done' => filled($coverPreview) && filled($avatarPreview)],
                                ['label' => 'Co mo ta va tien ich', 'done' => filled(old('mo_ta', $quan->mo_ta ?? '')) && !empty($selectedAmenities)],
                                ['label' => 'Co link lien he / dat mon', 'done' => filled(old('facebook_url', $quan->facebook_url ?? '')) || filled(old('website_url', $quan->website_url ?? '')) || filled(old('shopee_food_url', $quan->shopee_food_url ?? ''))],
                            ];
                        @endphp

                        @foreach ($tips as $tip)
                            <div class="flex items-center gap-3 rounded-2xl border px-4 py-3 {{ $tip['done'] ? 'border-emerald-100 bg-emerald-50' : 'border-gray-100 bg-gray-50' }}">
                                <span class="material-symbols-outlined {{ $tip['done'] ? 'text-emerald-600' : 'text-gray-400' }}">{{ $tip['done'] ? 'check_circle' : 'radio_button_unchecked' }}</span>
                                <span class="text-sm font-bold {{ $tip['done'] ? 'text-emerald-700' : 'text-gray-600' }}">{{ $tip['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($isEditing && isset($quan))
                        <div class="mt-6 rounded-[24px] border border-primary/10 bg-[#fff8f2] p-5">
                            <p class="text-[12px] font-black uppercase tracking-[0.18em] text-primary/70">Trang cong khai</p>
                            <p class="mt-2 text-sm text-on-surface-variant">
                                @if ($quan->trang_thai === 'da_duyet' && !$quan->la_nhap)
                                    Quan dang hien tren public. Ban co the mo trang chi tiet de xem.
                                @elseif ($quan->la_nhap)
                                    Quan dang o trang thai ban nhap, chi minh ban thay trong khu quan ly.
                                @else
                                    Quan dang cho duyet, chua hien tren trang cong khai.
                                @endif
                            </p>
                            <div class="mt-4 flex gap-3">
                                <a href="{{ route('chu-quan.quan.show', ['slug' => $quan->slug]) }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-3 text-sm font-black text-gray-700 border border-gray-200 hover:border-primary/30 hover:text-primary transition-all">
                                    <span class="material-symbols-outlined text-[18px]">dashboard</span>
                                    Quan ly
                                </a>
                                @if ($quan->trang_thai === 'da_duyet' && !$quan->la_nhap)
                                    <a href="{{ route('quan.detail', ['slug' => $quan->slug]) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-4 py-3 text-sm font-black text-white shadow-sm hover:bg-primary/90 transition-all">
                                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                        Xem public
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </aside>
        </section>
    </main>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const typeInput = document.getElementById('loai_hinh_kinh_doanh');
            const typeButtons = document.querySelectorAll('.type-chip');
            const richEditor = document.getElementById('rich-editor');
            const descriptionInput = document.getElementById('mo_ta');
            const galleryInput = document.getElementById('gallery_images');
            const galleryGrid = document.getElementById('gallery-preview-grid');
            const coverInput = document.getElementById('anh_bia');
            const avatarInput = document.getElementById('anh_dai_dien');
            const geocodeBtn = document.getElementById('geocode-btn');
            const previewAddress = document.getElementById('full-address-preview');
            const initialState = {
                provinceCode: document.getElementById('tinh_thanh_id').value,
                districtCode: document.getElementById('quan_huyen_id').value,
                wardCode: document.getElementById('phuong_xa_id').value,
                lat: parseFloat(document.getElementById('vi_do').value || '10.7769'),
                lng: parseFloat(document.getElementById('kinh_do').value || '106.7009'),
            };

            let map;
            let marker;

            const syncEditor = () => {
                descriptionInput.value = richEditor.innerHTML.trim();
            };

            const updateTypeStyles = (selectedType) => {
                typeButtons.forEach((button) => {
                    const active = button.dataset.type === selectedType;
                    button.classList.toggle('border-primary', active);
                    button.classList.toggle('bg-primary/10', active);
                    button.classList.toggle('text-primary', active);
                    button.classList.toggle('shadow-sm', active);
                    button.classList.toggle('border-gray-200', !active);
                    button.classList.toggle('bg-white', !active);
                    button.classList.toggle('text-on-surface', !active);
                });
            };

            typeButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    typeInput.value = button.dataset.type;
                    updateTypeStyles(button.dataset.type);
                });
            });

            document.querySelectorAll('.editor-action').forEach((button) => {
                button.addEventListener('click', () => {
                    const command = button.dataset.command;
                    const value = button.dataset.value || null;
                    richEditor.focus();
                    document.execCommand(command, false, value);
                    syncEditor();
                });
            });

            richEditor.addEventListener('input', syncEditor);
            syncEditor();
            updateTypeStyles(typeInput.value);

            const initMap = () => {
                map = L.map('owner-map', { zoomControl: true }).setView([initialState.lat, initialState.lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(map);

                marker = L.marker([initialState.lat, initialState.lng], { draggable: true }).addTo(map);

                const setCoordinates = (lat, lng, shouldPan = true) => {
                    document.getElementById('vi_do').value = lat.toFixed(7);
                    document.getElementById('kinh_do').value = lng.toFixed(7);
                    marker.setLatLng([lat, lng]);
                    if (shouldPan) {
                        map.panTo([lat, lng], { animate: true, duration: 0.5 });
                    }
                };

                map.on('click', (event) => {
                    setCoordinates(event.latlng.lat, event.latlng.lng);
                });

                marker.on('dragend', (event) => {
                    const position = event.target.getLatLng();
                    setCoordinates(position.lat, position.lng, false);
                });

                document.getElementById('vi_do').addEventListener('change', () => {
                    const lat = parseFloat(document.getElementById('vi_do').value);
                    const lng = parseFloat(document.getElementById('kinh_do').value);
                    if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
                        setCoordinates(lat, lng);
                    }
                });

                document.getElementById('kinh_do').addEventListener('change', () => {
                    const lat = parseFloat(document.getElementById('vi_do').value);
                    const lng = parseFloat(document.getElementById('kinh_do').value);
                    if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
                        setCoordinates(lat, lng);
                    }
                });
            };

            const readPreview = (input, targetId) => {
                const target = document.getElementById(targetId);
                if (!input.files || !input.files[0] || !target) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    target.src = event.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            };

            coverInput?.addEventListener('change', () => readPreview(coverInput, 'cover-preview-image'));
            avatarInput?.addEventListener('change', () => readPreview(avatarInput, 'avatar-preview-image'));

            const renderGalleryPreview = (files) => {
                const newPreviewWrapper = document.createElement('div');
                newPreviewWrapper.className = 'col-span-full grid grid-cols-2 md:grid-cols-3 gap-3';

                Array.from(files).forEach((file) => {
                    const card = document.createElement('div');
                    card.className = 'relative overflow-hidden rounded-2xl border border-primary/15 bg-[#fffaf6] aspect-square';

                    const image = document.createElement('img');
                    image.className = 'h-full w-full object-cover';
                    image.loading = 'lazy';

                    const badge = document.createElement('div');
                    badge.className = 'absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent px-3 py-2 text-[11px] font-black text-white';
                    badge.textContent = 'Anh moi';

                    const reader = new FileReader();
                    reader.onload = (event) => {
                        image.src = event.target.result;
                    };
                    reader.readAsDataURL(file);

                    card.appendChild(image);
                    card.appendChild(badge);
                    newPreviewWrapper.appendChild(card);
                });

                const emptyState = galleryGrid.querySelector('.col-span-full.rounded-2xl');
                if (emptyState) {
                    emptyState.remove();
                }

                const oldInjectedPreview = galleryGrid.querySelector('[data-preview-batch="true"]');
                oldInjectedPreview?.remove();

                newPreviewWrapper.dataset.previewBatch = 'true';
                galleryGrid.appendChild(newPreviewWrapper);
            };

            galleryInput?.addEventListener('change', () => {
                if (galleryInput.files?.length) {
                    renderGalleryPreview(galleryInput.files);
                }
            });

            const dropzone = document.getElementById('gallery-dropzone');
            ['dragenter', 'dragover'].forEach((eventName) => {
                dropzone?.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    dropzone.classList.add('border-primary', 'bg-primary/10');
                });
            });
            ['dragleave', 'drop'].forEach((eventName) => {
                dropzone?.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    dropzone.classList.remove('border-primary', 'bg-primary/10');
                });
            });
            dropzone?.addEventListener('drop', (event) => {
                if (!event.dataTransfer?.files?.length) {
                    return;
                }

                galleryInput.files = event.dataTransfer.files;
                renderGalleryPreview(event.dataTransfer.files);
            });

            const selectTinh = document.getElementById('select-tinh');
            const selectHuyen = document.getElementById('select-huyen');
            const selectXa = document.getElementById('select-xa');

            const updateAddressPreview = () => {
                const parts = [
                    document.getElementById('dia_chi_chi_tiet').value.trim(),
                    document.getElementById('ten_phuong_xa').value.trim(),
                    document.getElementById('ten_quan_huyen').value.trim(),
                    document.getElementById('ten_tinh_thanh').value.trim(),
                ].filter(Boolean);

                previewAddress.textContent = parts.length ? parts.join(', ') : 'Chua co dia chi day du.';
            };

            const fillSelect = (selectElement, items, placeholder, selectedCode) => {
                selectElement.innerHTML = `<option value="">${placeholder}</option>`;
                items.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = item.code;
                    option.textContent = item.name;
                    if (String(item.code) === String(selectedCode)) {
                        option.selected = true;
                    }
                    selectElement.appendChild(option);
                });
            };

            const loadWards = async (districtCode, selectedWard = null) => {
                if (!districtCode) {
                    selectXa.innerHTML = '<option value="">Chon phuong / xa</option>';
                    selectXa.disabled = true;
                    return;
                }

                const response = await fetch(`/api/dia-chi/phuong-xa/${districtCode}`);
                const result = await response.json();
                const wards = Array.isArray(result.data) ? result.data : [];
                fillSelect(selectXa, wards, 'Chon phuong / xa', selectedWard);
                selectXa.disabled = false;

                if (selectedWard) {
                    document.getElementById('phuong_xa_id').value = selectedWard;
                    document.getElementById('ten_phuong_xa').value = selectXa.options[selectXa.selectedIndex]?.text || '';
                }
            };

            const loadDistricts = async (provinceCode, selectedDistrict = null, selectedWard = null) => {
                if (!provinceCode) {
                    selectHuyen.innerHTML = '<option value="">Chon quan / huyen</option>';
                    selectHuyen.disabled = true;
                    selectXa.innerHTML = '<option value="">Chon phuong / xa</option>';
                    selectXa.disabled = true;
                    return;
                }

                const response = await fetch(`/api/dia-chi/quan-huyen/${provinceCode}`);
                const result = await response.json();
                const districts = Array.isArray(result.data) ? result.data : [];
                fillSelect(selectHuyen, districts, 'Chon quan / huyen', selectedDistrict);
                selectHuyen.disabled = false;

                if (selectedDistrict) {
                    document.getElementById('quan_huyen_id').value = selectedDistrict;
                    document.getElementById('ten_quan_huyen').value = selectHuyen.options[selectHuyen.selectedIndex]?.text || '';
                    await loadWards(selectedDistrict, selectedWard);
                }
            };

            const loadProvinces = async () => {
                const response = await fetch('/api/dia-chi/tinh-thanh');
                const result = await response.json();
                const provinces = Array.isArray(result.data) ? result.data : [];
                fillSelect(selectTinh, provinces, 'Chon tinh / thanh pho', initialState.provinceCode);

                if (initialState.provinceCode) {
                    document.getElementById('ten_tinh_thanh').value = selectTinh.options[selectTinh.selectedIndex]?.text || '';
                    await loadDistricts(initialState.provinceCode, initialState.districtCode, initialState.wardCode);
                }
            };

            selectTinh.addEventListener('change', async () => {
                document.getElementById('tinh_thanh_id').value = selectTinh.value;
                document.getElementById('ten_tinh_thanh').value = selectTinh.options[selectTinh.selectedIndex]?.text || '';
                document.getElementById('quan_huyen_id').value = '';
                document.getElementById('ten_quan_huyen').value = '';
                document.getElementById('phuong_xa_id').value = '';
                document.getElementById('ten_phuong_xa').value = '';
                updateAddressPreview();
                await loadDistricts(selectTinh.value);
            });

            selectHuyen.addEventListener('change', async () => {
                document.getElementById('quan_huyen_id').value = selectHuyen.value;
                document.getElementById('ten_quan_huyen').value = selectHuyen.options[selectHuyen.selectedIndex]?.text || '';
                document.getElementById('phuong_xa_id').value = '';
                document.getElementById('ten_phuong_xa').value = '';
                updateAddressPreview();
                await loadWards(selectHuyen.value);
            });

            selectXa.addEventListener('change', () => {
                document.getElementById('phuong_xa_id').value = selectXa.value;
                document.getElementById('ten_phuong_xa').value = selectXa.options[selectXa.selectedIndex]?.text || '';
                updateAddressPreview();
            });

            document.getElementById('dia_chi_chi_tiet').addEventListener('input', updateAddressPreview);

            geocodeBtn?.addEventListener('click', async () => {
                const address = previewAddress.textContent.trim();

                if (!address || address === 'Chua co dia chi day du.') {
                    previewAddress.textContent = 'Can chon dia chi truoc khi lay toa do.';
                    return;
                }

                const previousLabel = geocodeBtn.innerHTML;
                geocodeBtn.disabled = true;
                geocodeBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span> Dang tim toa do';

                try {
                    const response = await fetch(`/api/ban-do/geocode?address=${encodeURIComponent(address)}`);
                    const result = await response.json();

                    if (result.success) {
                        const lat = parseFloat(result.lat);
                        const lng = parseFloat(result.lng);
                        document.getElementById('vi_do').value = lat.toFixed(7);
                        document.getElementById('kinh_do').value = lng.toFixed(7);
                        marker.setLatLng([lat, lng]);
                        map.setView([lat, lng], 16);
                    }
                } catch (error) {
                    previewAddress.textContent = 'Khong the geocode luc nay. Ban co the cham map de chon tay.';
                } finally {
                    geocodeBtn.disabled = false;
                    geocodeBtn.innerHTML = previousLabel;
                }
            });

            document.getElementById('dang-quan-form').addEventListener('submit', syncEditor);

            updateAddressPreview();
            initMap();
            loadProvinces();
        });
    </script>
@endpush
