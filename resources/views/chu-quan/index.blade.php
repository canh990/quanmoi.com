@extends('layouts.app')

@section('title', 'Quản lý quán của bạn - Quán Mới')

@section('content')
@php
    $statusMeta = [
        'draft' => [
            'label' => 'Bản nháp', 
            'class' => 'bg-slate-100 text-slate-700 border-slate-200',
            'dot' => 'bg-slate-400'
        ],
        'chua_duyet' => [
            'label' => 'Chờ duyệt', 
            'class' => 'bg-amber-50 text-amber-700 border-amber-200/60',
            'dot' => 'bg-amber-500 animate-pulse'
        ],
        'da_duyet' => [
            'label' => 'Hoạt động', 
            'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
            'dot' => 'bg-emerald-500'
        ],
        'bi_khoa' => [
            'label' => 'Bị khóa', 
            'class' => 'bg-rose-50 text-rose-700 border-rose-200/60',
            'dot' => 'bg-rose-500'
        ],
    ];
@endphp

<main class="min-h-screen bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-orange-50/60 via-gray-50 to-white pt-24 pb-20">
    <div class="max-w-7xl mx-auto px-4 md:px-8">
        {{-- Session Notice --}}
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50/90 backdrop-blur-sm p-4 text-sm text-emerald-800 shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600 text-2xl">check_circle</span>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        {{-- Hero Header --}}
        <section class="relative rounded-3xl border border-orange-100 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white p-6 md:p-10 shadow-xl overflow-hidden">
            {{-- Background decorative elements --}}
            <div class="absolute -right-10 -bottom-10 h-64 w-64 rounded-full bg-primary/20 blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 top-0 h-48 w-48 rounded-full bg-amber-500/10 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-md px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-orange-300 border border-white/10 mb-4">
                        <span class="material-symbols-outlined text-[16px]">storefront</span>
                        Khu vực Chủ Quán
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white leading-tight">
                        Quản lý các địa điểm quán
                    </h1>
                    <p class="mt-2.5 text-sm md:text-base text-slate-300 leading-relaxed">
                        Theo dõi trạng thái phê duyệt, cập nhật menu, hình ảnh và phản hồi đánh giá từ thực khách cho tất cả chi nhánh của bạn.
                    </p>
                </div>

                <div class="flex-shrink-0">
                    <a href="{{ route('chu-quan.dang-quan') }}" class="inline-flex items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-r from-primary to-orange-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                        <span class="material-symbols-outlined text-[20px]">add_location_alt</span>
                        <span>Đăng quán mới</span>
                    </a>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-4 hover:bg-white/10 transition-colors">
                    <div class="flex items-center justify-between text-slate-400 mb-1">
                        <span class="text-xs font-bold uppercase tracking-wider">Tổng số quán</span>
                        <span class="material-symbols-outlined text-primary text-xl">store</span>
                    </div>
                    <p class="text-2xl font-black text-white">{{ $quanList->total() }}</p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-4 hover:bg-white/10 transition-colors">
                    <div class="flex items-center justify-between text-slate-400 mb-1">
                        <span class="text-xs font-bold uppercase tracking-wider">Đang hoạt động</span>
                        <span class="material-symbols-outlined text-emerald-400 text-xl">check_circle</span>
                    </div>
                    <p class="text-2xl font-black text-white">{{ $quanList->getCollection()->where('trang_thai', 'da_duyet')->where('la_nhap', false)->count() }}</p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-4 hover:bg-white/10 transition-colors">
                    <div class="flex items-center justify-between text-slate-400 mb-1">
                        <span class="text-xs font-bold uppercase tracking-wider">Chờ duyệt</span>
                        <span class="material-symbols-outlined text-amber-400 text-xl">hourglass_top</span>
                    </div>
                    <p class="text-2xl font-black text-white">{{ $quanList->getCollection()->where('trang_thai', 'chua_duyet')->where('la_nhap', false)->count() }}</p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-4 hover:bg-white/10 transition-colors">
                    <div class="flex items-center justify-between text-slate-400 mb-1">
                        <span class="text-xs font-bold uppercase tracking-wider">Bản nháp</span>
                        <span class="material-symbols-outlined text-slate-400 text-xl">draft</span>
                    </div>
                    <p class="text-2xl font-black text-white">{{ $quanList->getCollection()->where('la_nhap', true)->count() }}</p>
                </div>
            </div>
        </section>

        {{-- Filter & Search Section --}}
        <section class="mt-6 rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('chu-quan.quan.index') }}" class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-grow">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 pl-11 pr-4 py-3 text-sm outline-none transition-all focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Tìm kiếm tên quán, địa chỉ, quận huyện...">
                </div>

                <div class="w-full md:w-56">
                    <select name="status" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm outline-none transition-all focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10">
                        <option value="">Tất cả trạng thái</option>
                        <option value="draft" @selected(request('status') === 'draft')>Bản nháp</option>
                        <option value="chua_duyet" @selected(request('status') === 'chua_duyet')>Chờ duyệt</option>
                        <option value="da_duyet" @selected(request('status') === 'da_duyet')>Đã duyệt (Hoạt động)</option>
                        <option value="bi_khoa" @selected(request('status') === 'bi_khoa')>Bị khóa</option>
                    </select>
                </div>

                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-6 py-3 text-sm font-bold text-white hover:bg-black transition-colors flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    <span>Lọc kết quả</span>
                </button>

                @if(request('q') || request('status'))
                    <a href="{{ route('chu-quan.quan.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors flex-shrink-0">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                        <span>Xóa lọc</span>
                    </a>
                @endif
            </form>
        </section>

        {{-- Quan List Grid --}}
        <section class="mt-8 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($quanList as $quan)
                @php
                    $statusKey = $quan->la_nhap ? 'draft' : $quan->trang_thai;
                    $status = $statusMeta[$statusKey] ?? $statusMeta['chua_duyet'];
                    $coverImage = $quan->anh_bia ? asset('storage/' . $quan->anh_bia) : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=80';
                    $avatarImage = $quan->anh_dai_dien ? asset('storage/' . $quan->anh_dai_dien) : $coverImage;
                @endphp
                
                <article class="group rounded-3xl border border-gray-200/80 bg-white shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col justify-between">
                    <div>
                        {{-- Image Banner & Badges --}}
                        <div class="relative h-52 w-full overflow-hidden bg-gray-100">
                            <img src="{{ $coverImage }}" alt="{{ $quan->ten_quan }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            
                            {{-- Top Status Badge --}}
                            <div class="absolute top-4 right-4 z-10">
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold shadow-sm backdrop-blur-md {{ $status['class'] }}">
                                    <span class="w-2 h-2 rounded-full {{ $status['dot'] }}"></span>
                                    {{ $status['label'] }}
                                </span>
                            </div>

                            {{-- Bottom Info Overlay --}}
                            <div class="absolute left-4 right-4 bottom-4 flex items-end justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <span class="inline-block rounded-md bg-primary/90 backdrop-blur-md px-2.5 py-0.5 text-[11px] font-bold text-white uppercase tracking-wider mb-1">
                                        {{ $quan->loai_hinh_kinh_doanh }}
                                    </span>
                                    <h2 class="text-xl font-bold text-white tracking-tight line-clamp-1 group-hover:text-amber-300 transition-colors">
                                        {{ $quan->ten_quan }}
                                    </h2>
                                    <p class="text-xs text-white/80 line-clamp-1 flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[14px]">location_on</span>
                                        <span>{{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}</span>
                                    </p>
                                </div>
                                <img src="{{ $avatarImage }}" alt="{{ $quan->ten_quan }}" class="h-12 w-12 rounded-xl border-2 border-white object-cover shadow-md flex-shrink-0">
                            </div>
                        </div>

                        {{-- Card Content --}}
                        <div class="p-5">
                            {{-- Quick Stats Row --}}
                            <div class="grid grid-cols-3 gap-2 bg-gray-50/80 rounded-2xl p-2.5 border border-gray-100 text-center">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block">Thực đơn</span>
                                    <span class="text-sm font-extrabold text-gray-800 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] text-primary">restaurant_menu</span>
                                        {{ $quan->danh_muc_menu_count }}
                                    </span>
                                </div>
                                <div class="border-x border-gray-200/60">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block">Đánh giá</span>
                                    <span class="text-sm font-extrabold text-amber-500 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] fill-current">star</span>
                                        {{ number_format((float) ($quan->danh_gia_avg_so_sao ?? 0), 1) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block">Lượt lưu</span>
                                    <span class="text-sm font-extrabold text-rose-500 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">favorite</span>
                                        {{ $quan->nguoi_yeu_thich_count }}
                                    </span>
                                </div>
                            </div>

                            {{-- Description Excerpt --}}
                            <p class="mt-3.5 text-xs leading-relaxed text-gray-600 line-clamp-2">
                                {{ strip_tags($quan->mo_ta ?: 'Chưa có mô tả. Hãy cập nhật mô tả và hình ảnh để thu hút thêm thực khách.') }}
                            </p>

                            {{-- Opening Hours & Price --}}
                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                                <span class="text-gray-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px] text-gray-400">schedule</span>
                                    {{ $quan->gio_mo_cua }} - {{ $quan->gio_dong_cua }}
                                </span>
                                <span class="font-extrabold text-primary">
                                    {{ number_format((float) $quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format((float) $quan->gia_lon_nhat, 0, ',', '.') }}đ
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="p-5 pt-0 grid grid-cols-2 gap-2.5">
                        <a href="{{ route('chu-quan.quan.show', ['slug' => $quan->slug]) }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-3 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-primary-dark transition-colors">
                            <span class="material-symbols-outlined text-[16px]">dashboard</span>
                            <span>Quản lý</span>
                        </a>
                        <a href="{{ route('chu-quan.quan.edit', ['slug' => $quan->slug]) }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs font-bold text-gray-700 hover:bg-gray-100 hover:border-gray-300 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                            <span>Chỉnh sửa</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="md:col-span-2 xl:col-span-3 rounded-3xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 text-primary flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-3xl">storefront</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Bạn chưa có quán nào</h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">Đăng ký quán đầu tiên của bạn để đưa địa điểm lên hệ thống Quán Mới và tiếp cận hàng nghìn thực khách.</p>
                    <a href="{{ route('chu-quan.dang-quan') }}" class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark transition-all">
                        <span class="material-symbols-outlined text-[18px]">add_location_alt</span>
                        <span>Đăng quán ngay</span>
                    </a>
                </div>
            @endforelse
        </section>

        {{-- Pagination --}}
        @if ($quanList->hasPages())
            <div class="mt-8">
                {{ $quanList->links() }}
            </div>
        @endif
    </div>
</main>
@endsection
