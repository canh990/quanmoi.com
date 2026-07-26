@extends('layouts.app')

@section('title', 'Quản lý cửa hàng | Quán Mới')

@section('content')
<main class="pt-24 pb-20 max-w-7xl mx-auto px-4 md:px-8 flex-grow">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <span class="inline-flex items-center gap-2 bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold">Chủ quán workspace</span>
            <h1 class="text-3xl md:text-4xl font-black text-on-surface mt-3">Quản lý cửa hàng của bạn</h1>
            <p class="text-gray-500 mt-2">Xem tất cả quán đã đăng và mở nhanh trang quản lý chi tiết của từng quán.</p>
        </div>
        <a href="{{ route('chu-quan.dang-quan') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-primary text-white font-bold shadow-sm hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined">add_location_alt</span>
            Đăng thêm quán mới
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($quanList as $quan)
            <article class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                <img src="{{ $quan->anh_bia ? asset('storage/' . $quan->anh_bia) : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $quan->ten_quan }}" class="w-full h-52 object-cover">
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-black text-on-surface line-clamp-1">{{ $quan->ten_quan }}</h2>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $quan->trang_thai === 'da_duyet' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' }}">
                            {{ $quan->trang_thai === 'da_duyet' ? 'Đã duyệt' : 'Chờ duyệt' }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-500 mt-4 line-clamp-2">{{ $quan->mo_ta ?: 'Cập nhật mô tả để cửa hàng nổi bật hơn trên hệ thống.' }}</p>

                    <div class="mt-5 flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ $quan->gio_mo_cua }} - {{ $quan->gio_dong_cua }}</span>
                        <span class="font-bold text-primary">{{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ</span>
                    </div>

                    <div class="mt-5">
                        <a href="{{ route('chu-quan.quan.show', ['slug' => $quan->slug]) }}" class="h-11 rounded-2xl bg-primary text-white font-bold text-sm flex items-center justify-center gap-2 hover:bg-primary/90 transition-all">
                            <span class="material-symbols-outlined text-[18px]">storefront</span>
                            Quản lý cửa hàng
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-[28px] border border-dashed border-gray-200 bg-white p-10 text-center">
                <h2 class="text-2xl font-black text-on-surface">Bạn chưa có cửa hàng nào</h2>
                <p class="text-gray-500 mt-2">Đăng quán đầu tiên để bắt đầu sử dụng khu quản lý cửa hàng.</p>
                <a href="{{ route('chu-quan.dang-quan') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-3 rounded-2xl bg-primary text-white font-bold shadow-sm hover:bg-primary/90 transition-all">
                    <span class="material-symbols-outlined">add_location_alt</span>
                    Đăng quán ngay
                </a>
            </div>
        @endforelse
    </div>
</main>
@endsection
