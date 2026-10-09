@extends('layouts.app')

@section('title', $quan->ten_quan . ' — Quản lý địa điểm | Quán Mới')

@section('content')
<main class="pt-6 pb-20 max-w-7xl mx-auto px-4 md:px-8 flex-grow">

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

    {{-- Breadcrumb --}}
    <div class="mb-5 flex items-center gap-1.5 text-[13px] font-bold uppercase tracking-wide">
        <a href="{{ route('chu-quan.quan.index') }}" class="text-gray-600 hover:text-primary transition-colors">QUẢN LÝ CỬA HÀNG</a>
        <span class="text-gray-300 mx-1.5">•</span>
        <span class="text-blue-600">{{ mb_strtoupper($quan->ten_quan, 'UTF-8') }}</span>
    </div>

    {{-- Header / Cover Photo & Basic Info --}}
    <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden mb-8">
        {{-- Cover Image --}}
        <div class="h-64 md:h-80 w-full bg-gray-100 relative group">
            <a href="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80' }}"
               data-fancybox="gallery"
               class="absolute inset-0 z-0 cursor-pointer">
                <img src="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80' }}" class="w-full h-full object-cover" alt="{{ $quan->ten_quan }}" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>
            </a>

            <div class="absolute bottom-6 left-6 right-6 flex flex-col md:flex-row md:items-end justify-between gap-4 text-white z-10">
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
                    <h1 class="text-2xl md:text-4xl font-black drop-shadow-md">{{ $quan->ten_quan }}</h1>
                    <p class="text-white/90 text-sm flex items-center gap-1 mt-1 drop-shadow-md">
                        <span class="material-symbols-outlined text-[18px]">location_on</span>
                        {{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 mt-4 md:mt-0">
                    {{-- Nút đổi ảnh bìa --}}
                    <label for="cover-upload-input"
                           class="px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-bold text-[13.5px] rounded-xl transition-all flex items-center gap-1.5 border border-white/30 cursor-pointer relative z-20">
                        <span class="material-symbols-outlined text-[18px]">add_photo_alternate</span> Đổi ảnh bìa
                    </label>
                    <input type="file" id="cover-upload-input" accept="image/*" class="hidden"
                           onchange="uploadCoverImage(this, '{{ $quan->slug }}')"/>

                    <a href="/quan/{{ $quan->slug }}" target="_blank"
                       class="px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-bold text-[13.5px] rounded-xl transition-all flex items-center gap-1.5 border border-white/30 cursor-pointer relative z-20 hidden md:flex">
                        <span class="material-symbols-outlined text-[18px]">visibility</span> Xem trang khách
                    </a>
                </div>
            </div>
        </div>

        {{-- Meta Stats Grid --}}
        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50/50 border-t border-gray-100 text-center relative">
            <div class="p-3 bg-white rounded-2xl border border-gray-100 relative">
                <span class="text-[12px] font-bold text-gray-400 uppercase tracking-wider block">Giờ mở cửa</span>
                <span class="text-[16px] font-black text-on-surface">{{ $quan->gio_mo_cua }} - {{ $quan->gio_dong_cua }}</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-gray-100 relative">
                <span class="text-[12px] font-bold text-gray-400 uppercase tracking-wider block">Số điện thoại</span>
                <span class="text-[16px] font-black text-primary">{{ $quan->so_dien_thoai }}</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-gray-100 relative">
                <span class="text-[12px] font-bold text-gray-400 uppercase tracking-wider block">Khoảng giá</span>
                <span class="text-[16px] font-black text-on-surface">{{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-gray-100 relative">
                <span class="text-[12px] font-bold text-gray-400 uppercase tracking-wider block">Lượt truy cập</span>
                <span class="text-[16px] font-black text-tertiary">{{ number_format($quan->luot_xem ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>


    </div>

    {{-- Content Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left: Details & Gallery --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-[24px] p-6 md:p-8 border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-extrabold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">photo_library</span>
                        Thư viện hình ảnh
                    </h3>
                    <button onclick="document.getElementById('gallery-upload-input').click()"
                            class="px-4 py-2 bg-primary/10 hover:bg-primary/20 text-primary font-bold text-[13px] rounded-xl transition-all flex items-center gap-1 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">add_a_photo</span> Thêm ảnh
                    </button>
                    <input type="file" id="gallery-upload-input" multiple accept="image/*" class="hidden"
                           onchange="uploadGalleryImages(this, '{{ $quan->id }}')"/>
                </div>

                <div id="gallery-grid" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @forelse($quan->hinhAnh as $img)
                        @php
                            $imgSrc = $img->duong_dan;
                        @endphp
                        {{-- Wrapper có nút xóa --}}
                        <div class="relative group rounded-xl overflow-hidden h-32 bg-gray-100 border border-gray-100"
                             id="img-{{ $img->id }}">
                            {{-- Xem ảnh lớn --}}
                            <a href="{{ $imgSrc }}" data-fancybox="gallery" class="block w-full h-full">
                                <img src="{{ $imgSrc }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="Ảnh quán"/>
                            </a>
                            {{-- Overlay mờ khi hover --}}
                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                            {{-- Nút Xóa --}}
                            <button onclick="deleteGalleryImage('{{ $img->id }}')"
                                    title="Xóa ảnh này"
                                    class="absolute top-1.5 right-1.5 w-7 h-7 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-md cursor-pointer z-10">
                                <span class="material-symbols-outlined text-[14px] font-black">close</span>
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-gray-400 text-sm" id="gallery-empty">
                            <span class="material-symbols-outlined text-4xl block mb-2 text-gray-300">photo_library</span>
                            Chưa có hình ảnh nào. Bấm "Thêm ảnh" để tải lên.
                        </div>
                    @endforelse
                </div>

                {{-- Thanh upload progress --}}
                <div id="gallery-uploading" class="hidden">
                    <div class="flex items-center gap-3 text-sm text-gray-500 bg-gray-50 rounded-xl px-4 py-3">
                        <div class="animate-spin w-4 h-4 border-2 border-primary border-t-transparent rounded-full flex-shrink-0"></div>
                        Đang tải ảnh lên...
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white rounded-[24px] p-6 md:p-8 border border-gray-100 shadow-sm space-y-4 group/desc relative">
                <button onclick="openEditModal()" class="absolute top-6 right-6 w-8 h-8 rounded-full bg-gray-50 border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-orange-500 hover:border-orange-200 opacity-0 group-hover/desc:opacity-100 transition-all cursor-pointer z-10" title="Chỉnh sửa mô tả">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                </button>
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-extrabold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">description</span>
                        Mô tả thông tin quán
                    </h3>
                </div>
                <p class="text-gray-600 leading-relaxed text-[15px]" id="current-mo-ta">
                    {{ $quan->mo_ta ?: 'Chưa có mô tả chi tiết cho quán ăn này.' }}
                </p>

                @if($quan->tiktok_url)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-500 mb-2 uppercase">Kênh TikTok:</h4>
                    <a href="{{ $quan->tiktok_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-primary hover:underline font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16">
                            <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"/>
                        </svg>
                        {{ Str::limit($quan->tiktok_url, 40) }}
                    </a>
                </div>
                @endif
                @if($quan->shopeefood_url)
                <div class="mt-3">
                    <a href="{{ $quan->shopeefood_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-green-700 hover:underline font-bold">
                        <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                        Đặt món trên ShopeeFood
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-6">

            {{-- Thao tác nhanh --}}
            <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm space-y-4">
                <h3 class="text-lg font-bold text-on-surface border-b border-gray-100 pb-3">Thao tác nhanh</h3>

                <div class="space-y-2">
                    {{-- Nút Chỉnh sửa thông tin --}}
                    <button onclick="openEditModal()"
                            class="w-full h-11 text-white font-bold text-[14px] rounded-xl flex items-center justify-center gap-2 shadow-sm hover:opacity-90 transition-all cursor-pointer" style="background-color: #f97316;">
                        <span class="material-symbols-outlined text-[18px]">edit</span> Chỉnh sửa thông tin
                    </button>
                    <a href="{{ route('chu-quan.quan.menu.edit', $quan->slug) }}" class="w-full h-11 bg-tertiary text-white font-bold text-[14px] rounded-xl flex items-center justify-center gap-2 shadow-sm hover:bg-tertiary/90 transition-all">
                        <span class="material-symbols-outlined text-[18px]">restaurant_menu</span> Quản lý / Thêm thực đơn
                    </a>
                    <a href="/chu-quan/dang-quan" class="w-full h-11 bg-primary text-white font-bold text-[14px] rounded-xl flex items-center justify-center gap-2 shadow-sm hover:bg-primary/90 transition-all">
                        <span class="material-symbols-outlined text-[18px]">add_location_alt</span> Đăng thêm quán mới
                    </a>
                    <a href="/" class="w-full h-11 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-[14px] rounded-xl flex items-center justify-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-[18px]">home</span> Trở về trang chủ
                    </a>
                </div>
            </div>

            {{-- ===== ANALYTICS ===== --}}
            <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-bold text-on-surface border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-500">bar_chart</span>
                    Thống kê
                </h3>
                <div class="grid grid-cols-3 gap-3">
                    {{-- Lượt xem --}}
                    <div class="bg-indigo-50 rounded-2xl p-3 text-center">
                        <span class="material-symbols-outlined text-indigo-500 text-2xl" style="font-variation-settings: 'FILL' 1;">visibility</span>
                        <p class="text-xl font-black text-indigo-700 mt-0.5">{{ number_format($quan->luot_xem ?? 0, 0, ',', '.') }}</p>
                        <p class="text-[11px] font-semibold text-indigo-400 uppercase tracking-wide mt-0.5">Lượt xem</p>
                    </div>
                    {{-- Lượt lưu --}}
                    <div class="bg-rose-50 rounded-2xl p-3 text-center">
                        <span class="material-symbols-outlined text-rose-500 text-2xl" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                        <p class="text-xl font-black text-rose-700 mt-0.5">{{ number_format($luotLuu, 0, ',', '.') }}</p>
                        <p class="text-[11px] font-semibold text-rose-400 uppercase tracking-wide mt-0.5">Lượt lưu</p>
                    </div>
                    {{-- Video Review --}}
                    <div class="bg-orange-50 rounded-2xl p-3 text-center">
                        <span class="material-symbols-outlined text-orange-500 text-2xl" style="font-variation-settings: 'FILL' 1;">videocam</span>
                        <p class="text-xl font-black text-orange-700 mt-0.5">{{ number_format($luotVideo, 0, ',', '.') }}</p>
                        <p class="text-[11px] font-semibold text-orange-400 uppercase tracking-wide mt-0.5">Video</p>
                    </div>
                </div>

                @if(($quan->luot_xem ?? 0) > 0 || $luotLuu > 0)
                <div class="mt-4 pt-3 border-t border-gray-100 text-xs text-gray-400 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">info</span>
                    Tỷ lệ lưu / xem:
                    <span class="font-bold text-gray-600">
                        {{ $quan->luot_xem > 0 ? round(($luotLuu / $quan->luot_xem) * 100, 1) : 0 }}%
                    </span>
                </div>
                @endif
            </div>

            {{-- ===== QR CODE ===== --}}
            <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-bold text-on-surface border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-gray-600">qr_code_2</span>
                    Mã QR quán
                </h3>
                <p class="text-xs text-gray-400 mb-4">In mã này và đặt tại bàn để khách quét xem thực đơn & review.</p>

                {{-- QR canvas container --}}
                <div class="flex justify-center mb-4">
                    <div class="bg-white p-3 rounded-2xl border-2 border-gray-200 shadow-inner inline-block">
                        <div id="qr-code-container"></div>
                    </div>
                </div>

                <p class="text-center text-[11px] text-gray-400 mb-4 truncate">{{ url('/quan/' . $quan->slug) }}</p>

                <button onclick="downloadQR()"
                        class="w-full h-10 bg-gray-800 hover:bg-gray-900 text-white font-bold text-[13px] rounded-xl flex items-center justify-center gap-2 transition-all">
                    <span class="material-symbols-outlined text-[18px]">download</span> Tải về PNG
                </button>
            </div>

        </div>{{-- end sidebar --}}
    </div>
</main>

{{-- ===================================================== --}}
{{-- MODAL: CHỈNH SỬA THÔNG TIN QUÁN                       --}}
{{-- ===================================================== --}}
<div id="edit-modal" class="fixed inset-0 z-[9999] hidden" aria-modal="true">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeEditModal()"></div>

    {{-- Modal Panel --}}
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-[28px] shadow-2xl w-full max-w-2xl max-h-[92vh] overflow-y-auto relative">

            {{-- Header --}}
            <div class="sticky top-0 bg-white rounded-t-[28px] px-7 pt-7 pb-5 border-b border-gray-100 flex items-center justify-between z-10">
                <div>
                    <h2 class="text-xl font-extrabold text-on-surface">Chỉnh sửa thông tin quán</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Cập nhật thông tin hiển thị cho khách hàng</p>
                </div>
                <button onclick="closeEditModal()" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition cursor-pointer">
                    <span class="material-symbols-outlined text-gray-600">close</span>
                </button>
            </div>

            {{-- Form --}}
            <form id="edit-form" data-update-url="{{ route('chu-quan.quan.update', $quan->slug) }}" class="px-7 py-6 space-y-5" onsubmit="submitEditForm(event)" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Số điện thoại --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Số điện thoại <span class="text-red-500">*</span></label>
                    <input type="tel" name="so_dien_thoai" id="edit-so-dien-thoai"
                           value="{{ $quan->so_dien_thoai }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm transition">
                </div>

                {{-- Mô tả --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Mô tả quán</label>
                    <textarea name="mo_ta" id="edit-mo-ta" rows="4"
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm resize-none transition">{{ $quan->mo_ta }}</textarea>
                </div>

                {{-- Giờ mở / đóng --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Giờ mở cửa <span class="text-red-500">*</span></label>
                        <x-time-select name="gio_mo_cua" value="{{ $quan->gio_mo_cua }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Giờ đóng cửa <span class="text-red-500">*</span></label>
                        <x-time-select name="gio_dong_cua" value="{{ $quan->gio_dong_cua }}" />
                    </div>
                </div>

                {{-- Khoảng giá --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Giá nhỏ nhất (đ)</label>
                        <input type="text" name="gia_nho_nhat" id="edit-gia-nho"
                               value="{{ number_format($quan->gia_nho_nhat, 0, '', '.') }}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Giá lớn nhất (đ)</label>
                        <input type="text" name="gia_lon_nhat" id="edit-gia-lon"
                               value="{{ number_format($quan->gia_lon_nhat, 0, '', '.') }}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm transition">
                    </div>
                </div>

                {{-- TikTok URL --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Link TikTok</label>
                    <input type="url" name="tiktok_url" id="edit-tiktok"
                           value="{{ $quan->tiktok_url }}"
                           placeholder="https://www.tiktok.com/@quan-cua-ban"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Link ShopeeFood</label>
                    <input type="url" name="shopeefood_url" id="edit-shopeefood"
                           value="{{ $quan->shopeefood_url }}"
                           placeholder="https://shopeefood.vn/..."
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm transition">
                </div>

                {{-- Ảnh bìa --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Ảnh bìa mới (tùy chọn)</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-orange-400 transition cursor-pointer" onclick="document.getElementById('edit-anh-bia').click()">
                        <span class="material-symbols-outlined text-3xl text-gray-300 block mb-1">add_photo_alternate</span>
                        <p class="text-xs text-gray-400">Nhấn để chọn ảnh mới (JPG, PNG, WebP – tối đa 5MB)</p>
                        <p id="edit-anh-bia-name" class="text-xs font-semibold text-orange-500 mt-1 hidden"></p>
                    </div>
                    <input type="file" name="anh_bia" id="edit-anh-bia" accept="image/*" class="hidden"
                           onchange="document.getElementById('edit-anh-bia-name').textContent = this.files[0]?.name; document.getElementById('edit-anh-bia-name').classList.remove('hidden')">
                </div>

                {{-- Toast inline --}}
                <div id="edit-toast" class="hidden text-sm font-semibold px-4 py-3 rounded-xl"></div>

                {{-- Submit --}}
                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="closeEditModal()"
                            class="flex-1 h-12 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">
                        Huỷ
                    </button>
                    <button type="submit" id="edit-submit-btn"
                            class="flex-1 h-12 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    /* =========================================================
       FANCYBOX
    ========================================================= */
    document.addEventListener("DOMContentLoaded", function () {
        Fancybox.bind('[data-fancybox="gallery"]', {
            Thumbs: { autoStart: true },
            Toolbar: {
                display: {
                    left: ["infobar"],
                    middle: ["zoomIn","zoomOut","toggle1to1","rotateCCW","rotateCW","flipX","flipY"],
                    right: ["slideshow","thumbs","close"],
                },
            },
        });

        // Render QR Code (dùng qrcodejs - tương thích browser)
        new QRCode(document.getElementById('qr-code-container'), {
            text: '{{ url('/quan/' . $quan->slug) }}',
            width: 160,
            height: 160,
            colorDark: '#1a1a1a',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    });

    /* =========================================================
       QR DOWNLOAD
    ========================================================= */
    function downloadQR() {
        // qrcodejs tạo ra một <canvas> hoặc <img> bên trong container
        const container = document.getElementById('qr-code-container');
        const canvas = container.querySelector('canvas');
        const img    = container.querySelector('img');

        const padding = 24;
        const labelHeight = 36;
        const qrSize = 160;
        const totalW = qrSize + padding * 2;
        const totalH = qrSize + padding * 2 + labelHeight;

        const newCanvas = document.createElement('canvas');
        newCanvas.width  = totalW;
        newCanvas.height = totalH;
        const ctx = newCanvas.getContext('2d');

        // Nền trắng
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, totalW, totalH);

        // Hàm vẽ QR sau khi có source
        const drawAndDownload = (src) => {
            const image = new Image();
            image.onload = () => {
                ctx.drawImage(image, padding, padding, qrSize, qrSize);
                ctx.fillStyle = '#374151';
                ctx.font = 'bold 13px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('{{ $quan->ten_quan }}', totalW / 2, qrSize + padding + 22);

                const link = document.createElement('a');
                link.download = 'qr-{{ $quan->slug }}.png';
                link.href = newCanvas.toDataURL('image/png');
                link.click();
            };
            image.src = src;
        };

        if (canvas) {
            drawAndDownload(canvas.toDataURL('image/png'));
        } else if (img) {
            drawAndDownload(img.src);
        }
    }

    /* =========================================================
       GALLERY UPLOAD
    ========================================================= */
    async function uploadGalleryImages(input, quanId) {
        if (!input.files || input.files.length === 0) return;

        const uploading = document.getElementById('gallery-uploading');
        uploading.classList.remove('hidden');

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
                // Thêm ảnh mới vào grid mà không reload
                const grid = document.getElementById('gallery-grid');
                const empty = document.getElementById('gallery-empty');
                if (empty) empty.remove();

                data.data.forEach(img => {
                    const div = document.createElement('div');
                    div.className = 'relative group rounded-xl overflow-hidden h-32 bg-gray-100 border border-gray-100';
                    div.id = 'img-' + img.id;
                    div.innerHTML = `
                        <a href="${img.duong_dan}" data-fancybox="gallery" class="block w-full h-full">
                            <img src="${img.duong_dan}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="Ảnh quán"/>
                        </a>
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                        <button onclick="deleteGalleryImage('${img.id}')"
                                title="Xóa ảnh này"
                                class="absolute top-1.5 right-1.5 w-7 h-7 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-md cursor-pointer z-10">
                            <span class="material-symbols-outlined text-[14px] font-black">close</span>
                        </button>`;
                    grid.appendChild(div);
                    // Rebind fancybox cho ảnh mới
                    if (window.Fancybox) Fancybox.bind('[data-fancybox="gallery"]');
                });
                input.value = '';
            } else {
                alert(data.message || 'Lỗi tải ảnh lên.');
            }
        } catch (e) {
            alert('Không thể kết nối máy chủ.');
        } finally {
            uploading.classList.add('hidden');
        }
    }

    /* =========================================================
       GALLERY DELETE
    ========================================================= */
    async function deleteGalleryImage(imgId) {
        if (!confirm('Bạn có chắc muốn xóa ảnh này không?')) return;

        const card = document.getElementById('img-' + imgId);
        if (card) {
            card.style.opacity = '0.4';
            card.style.pointerEvents = 'none';
        }

        try {
            const res = await fetch(`/chu-quan/hinh-anh/${imgId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            const data = await res.json();
            if (res.ok && data.success) {
                // Xóa card khỏi DOM
                if (card) card.remove();
                // Nếu không còn ảnh nào → hiện placeholder
                const grid = document.getElementById('gallery-grid');
                if (grid.children.length === 0) {
                    grid.innerHTML = `<div class="col-span-full py-8 text-center text-gray-400 text-sm" id="gallery-empty">
                        <span class="material-symbols-outlined text-4xl block mb-2 text-gray-300">photo_library</span>
                        Chưa có hình ảnh nào. Bấm "Thêm ảnh" để tải lên.
                    </div>`;
                }
            } else {
                alert(data.message || 'Không thể xóa ảnh.');
                if (card) { card.style.opacity = '1'; card.style.pointerEvents = 'auto'; }
            }
        } catch (e) {
            alert('Không thể kết nối máy chủ.');
            if (card) { card.style.opacity = '1'; card.style.pointerEvents = 'auto'; }
        }
    }

    /* =========================================================
       COVER IMAGE UPLOAD
    ========================================================= */
    async function uploadCoverImage(input, slug) {
        if (!input.files || input.files.length === 0) return;

        const label = document.querySelector('label[for="cover-upload-input"]');
        if (label) label.innerHTML = '<span class="animate-spin material-symbols-outlined text-[18px]">autorenew</span> Đang tải...';

        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('anh_bia', input.files[0]);
        // Giữ nguyên các trường bắt buộc khác
        formData.append('so_dien_thoai', '{{ $quan->so_dien_thoai }}');
        formData.append('gio_mo_cua',    '{{ $quan->gio_mo_cua }}');
        formData.append('gio_dong_cua',  '{{ $quan->gio_dong_cua }}');

        try {
            const res = await fetch(`/chu-quan/quan/${slug}`, {
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
                alert(data.message || 'Không thể cập nhật ảnh bìa.');
                if (label) label.innerHTML = '<span class="material-symbols-outlined text-[18px]">add_photo_alternate</span> Đổi ảnh bìa';
            }
        } catch (e) {
            alert('Không thể kết nối máy chủ.');
            if (label) label.innerHTML = '<span class="material-symbols-outlined text-[18px]">add_photo_alternate</span> Đổi ảnh bìa';
        }
    }

    /* =========================================================
       EDIT MODAL
    ========================================================= */
    function openEditModal() {
        const modal = document.getElementById('edit-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEditModal();
    });

    async function submitEditForm(e) {
        e.preventDefault();

        const btn   = document.getElementById('edit-submit-btn');
        const toast = document.getElementById('edit-toast');
        const form  = document.getElementById('edit-form');

        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin material-symbols-outlined text-[18px]">autorenew</span> Đang lưu...';
        toast.classList.add('hidden');

        const formData = new FormData(form);

        // Xóa dấu chấm (thousands separator) trước khi gửi lên server
        let giaNho = formData.get('gia_nho_nhat');
        if (giaNho) formData.set('gia_nho_nhat', giaNho.replace(/\./g, ''));
        
        let giaLon = formData.get('gia_lon_nhat');
        if (giaLon) formData.set('gia_lon_nhat', giaLon.replace(/\./g, ''));

        try {
            const res = await fetch(form.dataset.updateUrl, {
                method: 'POST', // Laravel cần POST + _method=PUT
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: formData
            });

            const data = await res.json();

            if (res.ok && data.success) {
                toast.textContent = '✅ ' + data.message;
                toast.className = 'text-sm font-semibold px-4 py-3 rounded-xl bg-green-50 text-green-700';
                setTimeout(() => window.location.reload(), 1000);
            } else {
                const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Có lỗi xảy ra.');
                toast.textContent = '❌ ' + msg;
                toast.className = 'text-sm font-semibold px-4 py-3 rounded-xl bg-red-50 text-red-700';
            }
        } catch (err) {
            toast.textContent = '❌ Không thể kết nối máy chủ.';
            toast.className = 'text-sm font-semibold px-4 py-3 rounded-xl bg-red-50 text-red-700';
        }

        toast.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">save</span> Lưu thay đổi';
    }
</script>
@endpush
