@extends('layouts.app')

@section('title', 'Tat ca quan dang hoat dong | Quan Moi')

@section('content')
<main class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(255,107,0,0.12),_transparent_30%),linear-gradient(180deg,_#fff8f2_0%,_#ffffff_45%,_#fffaf6_100%)] pt-24 pb-20">
    <div class="max-w-7xl mx-auto px-4 md:px-8">
        <section class="rounded-[34px] border border-primary/10 bg-white/90 backdrop-blur-md shadow-[0_18px_48px_rgba(160,65,0,0.08)] overflow-hidden">
            <div class="p-6 md:p-8 lg:p-10 relative">
                <div class="absolute top-0 right-0 h-44 w-44 rounded-full bg-primary/10 blur-3xl"></div>
                <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-[12px] font-black uppercase tracking-[0.18em] text-primary">
                    <span class="material-symbols-outlined text-[18px]">explore</span>
                    Explore venues
                </span>
                <div class="mt-5 max-w-3xl">
                    <h1 class="text-3xl md:text-5xl font-black text-on-surface leading-tight">Tat ca quan dang hien cong khai</h1>
                    <p class="mt-3 text-[15px] md:text-[16px] leading-7 text-on-surface-variant">Loc theo khu vuc, loai hinh, khoang gia va trang thai dang mo cua de tim dia diem phu hop cho minh nhanh hon.</p>
                </div>

                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="rounded-2xl border border-primary/10 bg-[#fff7f1] px-4 py-4">
                        <p class="text-[11px] font-black uppercase tracking-[0.18em] text-primary/70">Ket qua</p>
                        <p class="mt-2 text-[15px] font-black text-on-surface">{{ $quans->total() }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white px-4 py-4">
                        <p class="text-[11px] font-black uppercase tracking-[0.18em] text-gray-400">Tinh / thanh</p>
                        <p class="mt-2 text-[15px] font-black text-on-surface">{{ $availableCities->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white px-4 py-4">
                        <p class="text-[11px] font-black uppercase tracking-[0.18em] text-gray-400">Loai hinh</p>
                        <p class="mt-2 text-[15px] font-black text-on-surface">{{ $availableTypes->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white px-4 py-4">
                        <p class="text-[11px] font-black uppercase tracking-[0.18em] text-gray-400">Dang loc</p>
                        <p class="mt-2 text-[15px] font-black text-on-surface">{{ request()->boolean('open_now') ? 'Mo cua' : 'Tat ca' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-6 rounded-[30px] border border-gray-100 bg-white p-4 md:p-5 shadow-[0_16px_40px_rgba(15,23,42,0.05)]">
            <form method="GET" action="{{ route('quan.index') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                <div class="relative xl:col-span-2">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" class="w-full rounded-2xl border border-gray-200 bg-white pl-10 pr-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Tim ten quan, loai hinh, dia chi...">
                </div>

                <select name="city" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10">
                    <option value="">Tat ca tinh thanh</option>
                    @foreach ($availableCities as $city)
                        <option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>
                    @endforeach
                </select>

                <select name="district" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10">
                    <option value="">Tat ca quan huyen</option>
                    @foreach ($availableDistricts as $district)
                        <option value="{{ $district }}" @selected(request('district') === $district)>{{ $district }}</option>
                    @endforeach
                </select>

                <select name="type" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10">
                    <option value="">Tat ca loai hinh</option>
                    @foreach ($availableTypes as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>

                <input type="number" name="min_price" min="0" step="1000" value="{{ request('min_price') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Gia tu">
                <input type="number" name="max_price" min="0" step="1000" value="{{ request('max_price') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10" placeholder="Gia den">

                <label class="flex items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3.5 text-sm font-bold text-gray-600">
                    <input type="checkbox" name="open_now" value="1" @checked(request()->boolean('open_now')) class="rounded border-gray-300 text-primary focus:ring-primary">
                    Chi hien quan dang mo cua
                </label>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 rounded-2xl bg-primary px-4 py-3.5 text-sm font-black text-white shadow-sm hover:bg-primary/90 transition-all">Ap dung loc</button>
                    <a href="{{ route('quan.index') }}" class="rounded-2xl border border-gray-200 bg-white px-4 py-3.5 text-sm font-black text-gray-700 hover:border-primary/30 hover:text-primary transition-all">Dat lai</a>
                </div>
            </form>
        </section>

        <section class="mt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($quans as $quan)
                @php
                    $coverImage = $quan->anh_bia ? asset('storage/' . $quan->anh_bia) : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=80';
                    $avatarImage = $quan->anh_dai_dien ? asset('storage/' . $quan->anh_dai_dien) : $coverImage;
                    $rating = number_format((float) ($quan->danh_gia_avg_so_sao ?? 0), 1);
                @endphp
                <article class="overflow-hidden rounded-[30px] border border-gray-100 bg-white shadow-[0_16px_40px_rgba(15,23,42,0.05)]">
                    <a href="{{ route('quan.detail', ['slug' => $quan->slug]) }}" class="group block">
                        <div class="relative">
                            <img src="{{ $coverImage }}" alt="{{ $quan->ten_quan }}" class="h-60 w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/15 to-transparent"></div>
                            <div class="absolute left-5 right-5 bottom-5 flex items-end justify-between gap-4">
                                <div class="min-w-0">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-[11px] font-black text-white backdrop-blur-md">
                                        <span class="material-symbols-outlined text-[14px]">star</span>
                                        {{ $rating }} ({{ $quan->danh_gia_count }})
                                    </span>
                                    <h2 class="mt-3 line-clamp-1 text-2xl font-black text-white">{{ $quan->ten_quan }}</h2>
                                    <p class="mt-1 line-clamp-1 text-sm text-white/80">{{ $quan->loai_hinh_kinh_doanh }} · {{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}</p>
                                </div>
                                <img src="{{ $avatarImage }}" alt="{{ $quan->ten_quan }}" class="h-16 w-16 rounded-[22px] border-4 border-white object-cover shadow-lg">
                            </div>
                        </div>

                        <div class="p-5">
                            <p class="line-clamp-2 text-sm leading-6 text-on-surface-variant">{{ strip_tags($quan->mo_ta ?: 'Kham pha thong tin quan, menu, review va huong dan di chuyen ngay.') }}</p>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-gray-400">Khoang gia</p>
                                    <p class="mt-1 text-sm font-black text-primary">{{ number_format((float) $quan->gia_nho_nhat, 0, ',', '.') }}d - {{ number_format((float) $quan->gia_lon_nhat, 0, ',', '.') }}d</p>
                                </div>
                                <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-gray-400">Mo cua</p>
                                    <p class="mt-1 text-sm font-black text-on-surface">{{ $quan->gio_mo_cua }} - {{ $quan->gio_dong_cua }}</p>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between text-sm">
                                <span class="text-gray-500">{{ $quan->nguoi_yeu_thich_count }} nguoi da luu</span>
                                <span class="font-black text-primary">Xem chi tiet</span>
                            </div>
                        </div>
                    </a>
                </article>
            @empty
                <div class="md:col-span-2 xl:col-span-3 rounded-[30px] border border-dashed border-gray-200 bg-white p-10 text-center shadow-sm">
                    <h2 class="text-2xl font-black text-on-surface">Khong tim thay quan phu hop</h2>
                    <p class="mt-2 text-gray-500">Thu doi bo loc hoac tu khoa de xem them dia diem.</p>
                </div>
            @endforelse
        </section>

        @if ($quans->hasPages())
            <div class="mt-8">
                {{ $quans->links() }}
            </div>
        @endif
    </div>
</main>
@endsection
