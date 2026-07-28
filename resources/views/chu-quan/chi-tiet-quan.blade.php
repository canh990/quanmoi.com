@extends('layouts.app')

@section('title', $quan->ten_quan . ' — Quản lý địa điểm | Quán Mới')

@section('content')
<main class="pt-24 pb-20 max-w-7xl mx-auto px-4 md:px-8 flex-grow">

    {{-- Banner Toast Notification --}}
    @if(session('success'))
        <div class="mb-6 bg-tick-xanh/15 border border-tick-xanh/30 text-tick-xanh px-5 py-3.5 rounded-2xl flex items-center gap-3">
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            <span class="font-bold text-[15px]">{{ session('success') }}</span>
        </div>
    @endif

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    @endpush

    {{-- Header / Cover Photo & Basic Info --}}
    <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden mb-8">
        {{-- Cover Image --}}
        <div class="h-64 md:h-80 w-full bg-gray-100 relative group cursor-pointer" data-fancybox="gallery" data-src="{{ $quan->anh_bia ? $quan->anh_bia : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80' }}">
            <img src="{{ $quan->anh_bia ? $quan->anh_bia : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80' }}" class="w-full h-full object-cover" alt="{{ $quan->ten_quan }}" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent z-10 pointer-events-none"></div>
            
            <div class="absolute bottom-6 left-6 right-6 flex flex-col md:flex-row md:items-end justify-between gap-4 text-white z-20">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        @if($quan->trang_thai === 'da_duyet')
                            <div class="inline-flex items-center gap-1.5 bg-tick-xanh text-white px-3 py-1 rounded-full text-xs font-bold">
                                <span class="material-symbols-outlined text-[14px]">verified</span>
                                Đã duyệt & Đang hoạt động
                            </div>
                        @elseif($quan->trang_thai === 'chua_duyet')
                            <div class="inline-flex items-center gap-1.5 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                <span class="material-symbols-outlined text-[14px]">pending</span>
                                Đang chờ duyệt
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                <span class="material-symbols-outlined text-[14px]">cancel</span>
                                Bị từ chối / Khóa
                            </div>
                        @endif
                        <div class="inline-flex items-center gap-1 bg-white/20 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs font-bold border border-white/30">
                            <span class="material-symbols-outlined text-[14px]">category</span>
                            {{ $quan->loai_hinh_kinh_doanh ?? 'Quán ăn' }}
                        </div>
                    </div>
                    <h1 class="text-2xl md:text-4xl font-black">{{ $quan->ten_quan }}</h1>
                    <p class="text-white/80 text-sm flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-[18px]">location_on</span>
                        {{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="/quan/{{ $quan->slug }}" target="_blank" class="px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-bold text-[13.5px] rounded-xl transition-all flex items-center gap-1.5 border border-white/30">
                        <span class="material-symbols-outlined text-[18px]">visibility</span> Xem trang khách hàng
                    </a>
                </div>
            </div>
        </div>

        {{-- Meta Stats Grid --}}
        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50/50 border-t border-gray-100 text-center">
            <div class="p-3 bg-white rounded-2xl border border-gray-100">
                <span class="text-[12px] font-bold text-gray-400 uppercase tracking-wider block">Giờ mở cửa</span>
                <span class="text-[16px] font-black text-on-surface">{{ $quan->gio_mo_cua }} - {{ $quan->gio_dong_cua }}</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-gray-100">
                <span class="text-[12px] font-bold text-gray-400 uppercase tracking-wider block">Số điện thoại</span>
                <span class="text-[16px] font-black text-primary">{{ $quan->so_dien_thoai }}</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-gray-100">
                <span class="text-[12px] font-bold text-gray-400 uppercase tracking-wider block">Khoảng giá</span>
                <span class="text-[16px] font-black text-on-surface">{{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-gray-100">
                <span class="text-[12px] font-bold text-gray-400 uppercase tracking-wider block">Lượt truy cập</span>
                <span class="text-[16px] font-black text-tertiary">1,250+</span>
            </div>
        </div>
    </div>

    {{-- Content Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left: Details & Gallery --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Gallery Upload --}}
            <div class="bg-white rounded-[24px] p-6 md:p-8 border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-extrabold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">photo_library</span>
                        Thư viện hình ảnh
                    </h3>
                    <button onclick="document.getElementById('gallery-upload-input').click()" class="px-4 py-2 bg-primary/10 hover:bg-primary/20 text-primary font-bold text-[13px] rounded-xl transition-all flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">add_a_photo</span> Thêm ảnh
                    </button>
                    <input type="file" id="gallery-upload-input" multiple accept="image/*" class="hidden" onchange="uploadGalleryImages(this, '{{ $quan->id }}')" />
                </div>

                <div id="gallery-grid" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @forelse($quan->hinhAnh as $img)
                        @php
                            $imgSrc = Str::startsWith($img->duong_dan, 'http') ? $img->duong_dan : asset('storage/' . $img->duong_dan);
                        @endphp
                        <a href="{{ $imgSrc }}" data-fancybox="gallery" class="relative group rounded-xl overflow-hidden h-32 bg-gray-100 border border-gray-100 block">
                            <img src="{{ $imgSrc }}" class="w-full h-full object-cover" alt="Ảnh quán" />
                        </a>
                    @empty
                        <div class="col-span-full py-8 text-center text-gray-400 text-sm">
                            Chưa có hình ảnh nào trong thư viện quán.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white rounded-[24px] p-6 md:p-8 border border-gray-100 shadow-sm space-y-4">
                <h3 class="text-xl font-extrabold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">description</span>
                    Mô tả thông tin quán
                </h3>
                <p class="text-gray-600 leading-relaxed text-[15px]">
                    {{ $quan->mo_ta ?: 'Chưa có mô tả chi tiết cho quán ăn này.' }}
                </p>
            </div>
        </div>

        {{-- Right Sidebar Actions --}}
        <div class="space-y-6">
            <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm space-y-4">
                <h3 class="text-lg font-bold text-on-surface border-b border-gray-100 pb-3">Thao tác nhanh</h3>
                
                <div class="space-y-2">
                    <a href="/chu-quan/dang-quan" class="w-full h-11 bg-primary text-white font-bold text-[14px] rounded-xl flex items-center justify-center gap-2 shadow-sm hover:bg-primary/90 transition-all">
                        <span class="material-symbols-outlined text-[18px]">add_location_alt</span> Đăng thêm quán mới
                    </a>
                    <a href="/" class="w-full h-11 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-[14px] rounded-xl flex items-center justify-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-[18px]">home</span> Trở về trang chủ
                    </a>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Fancybox.bind('[data-fancybox="gallery"]', {
            Thumbs: {
                autoStart: true,
            },
            Toolbar: {
                display: {
                    left: ["infobar"],
                    middle: [
                        "zoomIn",
                        "zoomOut",
                        "toggle1to1",
                        "rotateCCW",
                        "rotateCW",
                        "flipX",
                        "flipY",
                    ],
                    right: ["slideshow", "thumbs", "close"],
                },
            },
        });
    });

    async function uploadGalleryImages(input, quanId) {
        if (!input.files || input.files.length === 0) return;

        const formData = new FormData();
        for (let i = 0; i < input.files.length; i++) {
            formData.append('hinh_anh[]', input.files[i]);
        }

        try {
            const res = await fetch(`/chu-quan/quan/${quanId}/hinh-anh`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: formData
            });
            const data = await res.json();
            if (res.ok && data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Lỗi tải ảnh lên.');
            }
        } catch (e) {
            alert('Không thể kết nối máy chủ.');
        }
    }
</script>
@endpush
