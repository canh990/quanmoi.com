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
        <span class="min-w-0 break-words text-blue-600 [overflow-wrap:anywhere]">{{ mb_strtoupper($quan->ten_quan, 'UTF-8') }}</span>
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
                <div class="min-w-0 flex-1">
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
                    <h1 class="text-2xl md:text-4xl font-black drop-shadow-md break-words [overflow-wrap:anywhere]">{{ $quan->ten_quan }}</h1>
                    <p class="text-white/90 text-sm flex items-start gap-1 mt-1 drop-shadow-md break-words [overflow-wrap:anywhere]">
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
            <nav id="owner-venue-tabs" class="flex border-b border-gray-200 gap-5 overflow-x-auto no-scrollbar" aria-label="Điều hướng chi tiết quán">
                <a href="#owner-menu-section" aria-current="location" class="tab-active py-3 text-xs sm:text-sm font-semibold whitespace-nowrap px-1">THỰC ĐƠN</a>
                <a href="#owner-promotions-section" class="text-gray-500 hover:text-primary py-3 text-xs sm:text-sm font-semibold whitespace-nowrap px-1 transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-amber-500 animate-pulse">auto_awesome</span>
                    KHUYẾN MÃI & AI (✨)
                </a>
                <a href="#owner-reviews-section" class="text-gray-500 hover:text-primary py-3 text-xs sm:text-sm font-semibold whitespace-nowrap px-1 transition-colors">ĐÁNH GIÁ</a>
                <a href="#owner-info-section" class="text-gray-500 hover:text-primary py-3 text-xs sm:text-sm font-semibold whitespace-nowrap px-1 transition-colors">THÔNG TIN CHI TIẾT</a>
                <a href="#owner-gallery-section" class="text-gray-500 hover:text-primary py-3 text-xs sm:text-sm font-semibold whitespace-nowrap px-1 transition-colors">HÌNH ẢNH</a>
            </nav>

            <div id="owner-gallery-section" hidden class="bg-white rounded-[24px] p-6 md:p-8 border border-gray-100 shadow-sm space-y-4">
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
            <div id="owner-info-section" hidden class="bg-white rounded-[24px] p-6 md:p-8 border border-gray-100 shadow-sm space-y-4 group/desc relative">
                <button onclick="openEditModal()" class="absolute top-6 right-6 w-8 h-8 rounded-full bg-gray-50 border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-orange-500 hover:border-orange-200 opacity-0 group-hover/desc:opacity-100 transition-all cursor-pointer z-10" title="Chỉnh sửa mô tả">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                </button>
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-extrabold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">description</span>
                        Mô tả thông tin quán
                    </h3>
                </div>
                <p class="text-gray-600 leading-relaxed text-[15px] break-words [overflow-wrap:anywhere]" id="current-mo-ta">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 border-t border-gray-100 pt-4 text-sm text-gray-600">
                    <p class="break-words [overflow-wrap:anywhere]"><strong class="text-gray-800">Địa chỉ:</strong> {{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_phuong_xa }}, {{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}</p>
                    <p><strong class="text-gray-800">Điện thoại:</strong> {{ $quan->so_dien_thoai }}</p>
                    @if($quan->email)
                        <p class="break-words [overflow-wrap:anywhere]"><strong class="text-gray-800">Email:</strong> {{ $quan->email }}</p>
                    @endif
                    <p><strong class="text-gray-800">Giờ mở cửa:</strong> {{ $quan->gio_mo_cua }} - {{ $quan->gio_dong_cua }}</p>
                </div>
            </div>
            {{-- Menu preview for the owner --}}
            <section class="bg-white rounded-[24px] p-6 md:p-8 border border-gray-100 shadow-sm space-y-6" id="owner-menu-section">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-xl font-extrabold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">restaurant_menu</span>
                        Thực đơn
                    </h3>
                    <a href="{{ route('chu-quan.quan.menu.edit', $quan->slug) }}" class="shrink-0 text-sm font-bold text-primary hover:underline">Quản lý thực đơn</a>
                </div>

                @forelse($quan->danhMucMenu as $danhMuc)
                    <div class="space-y-3">
                        <h4 class="text-base font-bold text-on-surface break-words [overflow-wrap:anywhere]">{{ $danhMuc->ten_danh_muc }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @forelse($danhMuc->monAn as $monAn)
                                <article class="flex min-w-0 gap-3 rounded-xl border border-gray-100 p-3">
                                    @if($monAn->hinh_anh_url)
                                        <img src="{{ $monAn->hinh_anh_url }}" alt="{{ $monAn->ten_mon }}" class="h-16 w-16 shrink-0 rounded-lg object-cover">
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <h5 class="font-bold text-sm break-words [overflow-wrap:anywhere]">{{ $monAn->ten_mon }}</h5>
                                        @if($monAn->mo_ta)
                                            <p class="mt-1 text-xs text-gray-500 break-words [overflow-wrap:anywhere]">{{ $monAn->mo_ta }}</p>
                                        @endif
                                        <p class="mt-2 font-bold text-primary">{{ number_format($monAn->gia, 0, ',', '.') }}đ</p>
                                    </div>
                                </article>
                            @empty
                                <p class="text-sm text-gray-500">Danh mục này chưa có món.</p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Quán chưa có thực đơn. Thêm món để khách có thể xem.</p>
                @endforelse
            </section>

            {{-- Read-only customer reviews for the owner --}}
            <section hidden class="bg-white rounded-[24px] p-6 md:p-8 border border-gray-100 shadow-sm space-y-4" id="owner-reviews-section">
                @php
                    $ownerReviews = $quan->danhGia->sortByDesc('updated_at');
                    $ownerReviewCount = $ownerReviews->count();
                    $ownerAverageRating = $ownerReviewCount ? $ownerReviews->avg('so_sao') : 0;
                @endphp
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-xl font-extrabold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500">star</span>
                        Đánh giá của khách ({{ $ownerReviewCount }})
                    </h3>
                    @if($ownerReviewCount)
                        <p class="font-bold text-amber-600">★ {{ number_format($ownerAverageRating, 1) }} / 5</p>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($ownerReviews as $danhGia)
                        <article class="rounded-xl border border-gray-100 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h4 class="font-bold text-sm break-words [overflow-wrap:anywhere]">{{ $danhGia->nguoiDung?->ho_ten ?? 'Thành viên' }}</h4>
                                <time class="text-xs text-gray-400" datetime="{{ $danhGia->updated_at?->toISOString() }}">
                                    {{ $danhGia->updated_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                                </time>
                            </div>
                            <p class="mt-1 text-amber-500" aria-label="{{ $danhGia->so_sao }} trên 5 sao">{{ str_repeat('★', (int) $danhGia->so_sao) }}{{ str_repeat('☆', 5 - (int) $danhGia->so_sao) }}</p>
                            @if($danhGia->binh_luan)
                                <p class="mt-2 text-sm text-gray-600 whitespace-pre-line break-words [overflow-wrap:anywhere]">{{ $danhGia->binh_luan }}</p>
                            @endif

                            @if($danhGia->phan_hoi)
                                <div class="mt-3 rounded-lg bg-orange-50 p-3">
                                    <p class="text-xs font-bold text-primary">Phản hồi của quán</p>
                                    <p class="mt-1 whitespace-pre-line break-words text-sm text-gray-700 [overflow-wrap:anywhere]">{{ $danhGia->phan_hoi }}</p>
                                    @if($danhGia->phan_hoi_luc)
                                        <time class="mt-1 block text-[11px] text-gray-400">{{ $danhGia->phan_hoi_luc->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</time>
                                    @endif
                                </div>
                            @endif

                            <form method="POST" action="{{ route('chu-quan.quan.danh-gia.reply', ['slug' => $quan->slug, 'reviewId' => $danhGia->id]) }}" class="mt-3 space-y-2">
                                @csrf
                                @method('PUT')
                                <label for="phan-hoi-{{ $danhGia->id }}" class="block text-xs font-bold text-gray-700">{{ $danhGia->phan_hoi ? 'Chỉnh sửa phản hồi' : 'Trả lời đánh giá này' }}</label>
                                <textarea id="phan-hoi-{{ $danhGia->id }}" name="phan_hoi_{{ $danhGia->id }}" rows="3" maxlength="2000" required class="w-full rounded-lg border border-gray-200 p-3 text-sm outline-none focus:border-primary" placeholder="Viết phản hồi cho khách...">{{ old('phan_hoi_'.$danhGia->id, $danhGia->phan_hoi) }}</textarea>
                                @error('phan_hoi_'.$danhGia->id)
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white hover:brightness-110">
                                    {{ $danhGia->phan_hoi ? 'Cập nhật phản hồi' : 'Gửi phản hồi' }}
                                </button>
                            </form>
                        </article>
                    @empty
                        <p class="text-sm text-gray-500">Quán chưa có đánh giá từ khách.</p>
                    @endforelse
                </div>
            </section>

            {{-- Promotions & Kira AI Marketing Section --}}
            <section hidden class="space-y-6" id="owner-promotions-section">
                {{-- Khung 1: Danh sách ưu đãi đang có của quán --}}
                <div class="bg-white rounded-[24px] p-6 md:p-8 border border-gray-100 shadow-sm space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h4 class="text-lg font-extrabold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">local_offer</span>
                            Chương trình ưu đãi của quán (<span id="promo-total-count">{{ $quan->khuyenMais->count() }}</span>)
                        </h4>
                        <a href="#kira-ai-form-box" onclick="document.getElementById('kira-ai-form-box').scrollIntoView({behavior: 'smooth'})"
                           class="px-3.5 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-xs rounded-xl transition flex items-center gap-1 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">add_circle</span>
                            Tạo ưu đãi mới với AI
                        </a>
                    </div>

                    {{-- Công cụ kiểm tra & xác thực mã voucher của khách đến quán --}}
                    <div class="p-4 rounded-2xl bg-gradient-to-r from-purple-50/70 to-indigo-50/50 border border-purple-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center text-sm font-bold shadow-xs shrink-0">🔍</span>
                            <div>
                                <h5 class="font-bold text-xs text-gray-900">Kiểm tra & xác thực voucher khách xuất trình</h5>
                                <p class="text-[11px] text-gray-500">Nhập mã khách đọc hoặc đưa trên điện thoại để kiểm tra tính hợp lệ tức thì</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="text" id="owner-verify-code-input" placeholder="VD: PHOBO20" class="px-3 py-2 bg-white border border-purple-200 rounded-xl font-mono text-xs uppercase font-bold text-purple-700 outline-none focus:border-purple-500 w-36 shadow-2xs" onkeypress="if(event.key === 'Enter') ownerVerifyVoucher()">
                            <button type="button" onclick="ownerVerifyVoucher()" class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1 shrink-0 cursor-pointer">
                                <span>Xác thực</span>
                            </button>
                        </div>
                    </div>
                    <div id="owner-verify-result-box" class="hidden text-xs p-3.5 rounded-2xl border space-y-1"></div>

                    <div id="owner-promo-list" class="space-y-3">
                        @forelse($quan->khuyenMais as $km)
                            <div class="p-4 rounded-2xl border border-gray-100 hover:border-purple-200 transition bg-white shadow-xs" id="km-card-{{ $km->id }}">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($km->ma_voucher)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 font-mono font-bold text-xs border border-dashed border-purple-300">
                                                <span class="material-symbols-outlined text-[13px]">confirmation_number</span>
                                                {{ $km->ma_voucher }}
                                            </span>
                                        @endif
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ $km->loai_label }} ({{ $km->gia_tri_giam ?: 'Ưu đãi' }})
                                        </span>
                                        @if($km->con_han)
                                            <span id="badge-status-{{ $km->id }}" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Đang hoạt động
                                            </span>
                                        @else
                                            <span id="badge-status-{{ $km->id }}" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">
                                                Tạm dừng / Hết hạn
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2">
                                        {{-- Toggle kích hoạt --}}
                                        <button type="button" onclick="toggleOwnerPromotion('{{ $km->id }}')"
                                                id="btn-toggle-{{ $km->id }}"
                                                title="{{ $km->trang_thai ? 'Bấm để tạm dừng' : 'Bấm để kích hoạt' }}"
                                                class="px-2.5 py-1 text-xs font-semibold rounded-lg transition {{ $km->trang_thai ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $km->trang_thai ? 'Bật' : 'Tắt' }}
                                        </button>
                                        {{-- Nút Xóa Voucher --}}
                                        <button type="button" onclick="deleteOwnerPromotion('{{ $km->id }}', '{{ $km->ma_voucher ?: $km->tieu_de }}')" title="Xóa voucher này"
                                                class="px-2.5 py-1 text-xs font-bold rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 flex items-center gap-1 transition cursor-pointer shadow-2xs">
                                            <span class="material-symbols-outlined text-[15px]">delete</span>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                </div>

                                <h5 class="font-bold text-sm text-gray-900">{{ $km->tieu_de }}</h5>
                                @if($km->slogan)
                                    <p class="text-xs italic text-gray-500 mt-0.5">"{{ $km->slogan }}"</p>
                                @endif

                                <div class="mt-2 text-xs text-gray-500 flex flex-wrap items-center gap-x-4 gap-y-1">
                                    <span>🍽️ <strong>Áp dụng:</strong> {{ $km->mon_ap_dung ?: 'Toàn bộ thực đơn' }}</span>
                                    @if($km->ngay_bat_dau || $km->ngay_ket_thuc)
                                        <span>⏰ <strong>Thời gian:</strong> {{ $km->ngay_bat_dau ? $km->ngay_bat_dau->format('d/m/Y') : 'Từ nay' }} - {{ $km->ngay_ket_thuc ? $km->ngay_ket_thuc->format('d/m/Y') : 'Vô thời hạn' }}</span>
                                    @endif
                                </div>

                                {{-- Nút xem bài viết chi tiết --}}
                                <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-xs">
                                    <button type="button" onclick="togglePromoContent('{{ $km->id }}')" class="font-bold text-purple-600 hover:text-purple-800 flex items-center gap-1 cursor-pointer">
                                        <span class="material-symbols-outlined text-[15px]">read_more</span>
                                        <span id="label-toggle-{{ $km->id }}">Xem nội dung bài viết</span>
                                    </button>
                                    <button type="button" onclick="copyExistingPromoPost('{{ $km->id }}')" class="font-semibold text-gray-500 hover:text-gray-800 flex items-center gap-1 cursor-pointer">
                                        <span class="material-symbols-outlined text-[15px]">content_copy</span>
                                        Sao chép bài viết
                                    </button>
                                </div>

                                <div id="promo-full-content-{{ $km->id }}" class="hidden mt-3 p-3 bg-gray-50 rounded-xl text-xs text-gray-700 leading-relaxed border border-gray-100 whitespace-pre-line font-sans">
                                    {{ $km->noi_dung }}
                                    @if($km->the_le)
                                        <div class="mt-2 pt-2 border-t border-gray-200 text-gray-500 font-sans">
                                            <strong>Thể lệ:</strong><br>{{ $km->the_le }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div id="owner-promo-empty" class="py-8 text-center text-gray-400 text-xs bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-gray-300">local_offer</span>
                                Quán chưa có ưu đãi nào đang diễn ra.<br>
                                Sử dụng công cụ Kira AI ngay bên dưới để tạo chiến dịch đầu tiên!
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Khung 2: BỘ TẠO KHUYẾN MÃI BẰNG KIRA AI --}}
                <div class="bg-white rounded-[24px] p-6 md:p-8 border border-purple-200/80 shadow-sm space-y-5" id="kira-ai-form-box">
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-purple-500/20">
                            <span class="material-symbols-outlined text-[20px]">psychology</span>
                        </div>
                        <div>
                            <h4 class="font-black text-gray-900 text-base flex items-center gap-2">
                                Kira AI F&B Promotion Studio
                            </h4>
                            <p class="text-xs text-gray-500">Tự động lấy dữ liệu món ăn và thông tin quán để sinh nội dung quảng cáo hoàn chỉnh.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Món ăn áp dụng --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px] text-amber-500">restaurant_menu</span>
                                Chọn món từ thực đơn quán
                            </label>
                            <select id="owner-ai-mon-an" class="w-full px-3.5 py-2.5 text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white font-medium">
                                <option value="Toàn bộ thực đơn quán">🍽️ Toàn bộ thực đơn quán</option>
                                @foreach($quan->danhMucMenu as $dm)
                                    <optgroup label="{{ $dm->ten_danh_muc }}">
                                        @foreach($dm->monAn as $mon)
                                            <option value="{{ $mon->ten_mon }}">
                                                {{ $mon->ten_mon }} ({{ number_format($mon->gia, 0, ',', '.') }}đ)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{-- Loại hình khuyến mãi --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px] text-rose-500">percent</span>
                                Hình thức khuyến mãi
                            </label>
                            <select id="owner-ai-loai-km" class="w-full px-3.5 py-2.5 text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white font-medium">
                                <option value="giam_phan_tram">Giảm theo % (VD: 20%, 30%)</option>
                                <option value="giam_tien">Giảm tiền trực tiếp (VD: 50.000đ)</option>
                                <option value="mua_1_tang_1">Mua 1 Tặng 1</option>
                                <option value="tang_mon">Tặng đồ uống / Tráng miệng</option>
                                <option value="combo">Combo giờ vàng giá sốc</option>
                                <option value="voucher">Voucher ưu đãi độc quyền</option>
                            </select>
                        </div>

                        {{-- Mức giảm / Quà tặng --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Mức giảm hoặc Quà tặng</label>
                            <input type="text" id="owner-ai-gia-tri" value="20%" placeholder="VD: 20% hoặc 50.000đ hoặc Tặng 1 trà tắc khổng lồ"
                                   class="w-full px-3.5 py-2.5 text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                        </div>

                        {{-- Dịp khuyến mãi --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Dịp khuyến mãi</label>
                            <input type="text" id="owner-ai-dip" value="Ưu đãi đón khách cuối tuần" placeholder="VD: Khai trương, Sinh nhật quán, Lễ hội ẩm thực..."
                                   class="w-full px-3.5 py-2.5 text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                        </div>

                        {{-- Phong cách văn phong --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Phong cách giọng văn</label>
                            <select id="owner-ai-phong-cach" class="w-full px-3.5 py-2.5 text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white font-medium">
                                <option value="hao_hung">🔥 Hào hứng, sôi nổi (Kích thích vị giác, đông vui)</option>
                                <option value="sang_trong">✨ Sang trọng, lịch thiệp (Nhà hàng cao cấp, tinh tế)</option>
                                <option value="hai_huoc">😂 Hài hước, Gen Z (Bắt trend cực chất)</option>
                                <option value="ngan_gon">⚡ Ngắn gọn, súc tích (Flash sale, chốt đơn nhanh)</option>
                            </select>
                        </div>

                        {{-- Thời hạn áp dụng --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Thời hạn áp dụng</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <input type="date" id="owner-ai-ngay-bat-dau" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" class="w-full px-2.5 py-2 text-xs border border-gray-200 rounded-xl outline-none transition focus:ring-2 focus:ring-purple-500">
                                    <span class="text-[10px] text-gray-400 block mt-0.5">Bắt đầu</span>
                                </div>
                                <div>
                                    <input type="date" id="owner-ai-ngay-ket-thuc" value="{{ date('Y-m-d', strtotime('+7 days')) }}" min="{{ date('Y-m-d') }}" class="w-full px-2.5 py-2 text-xs border border-gray-200 rounded-xl outline-none transition focus:ring-2 focus:ring-purple-500">
                                    <span class="text-[10px] text-gray-400 block mt-0.5">Kết thúc</span>
                                </div>
                            </div>
                            <p id="owner-promo-date-error" class="hidden text-[11px] font-bold text-rose-600 mt-1.5 flex items-center gap-1 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                                <span class="material-symbols-outlined text-[14px] shrink-0">error</span>
                                <span id="owner-promo-date-error-text"></span>
                            </p>
                        </div>

                        {{-- Yêu cầu thêm --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Yêu cầu bổ sung (Tùy chọn)</label>
                            <input type="text" id="owner-ai-yeu-cau" placeholder="VD: Áp dụng từ 11h-14h trưa, chỉ áp dụng ăn tại quán..."
                                   class="w-full px-3.5 py-2 text-xs border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                        </div>
                    </div>

                    {{-- Nút bấm tạo AI --}}
                    <div>
                        <button type="button" onclick="generateOwnerAiPromotion()" id="btn-owner-generate"
                                class="w-full py-3 bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-700 hover:from-purple-700 hover:to-indigo-700 text-white font-black text-xs rounded-xl shadow-md shadow-purple-500/20 transition flex items-center justify-center gap-2 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                            Kira AI Tạo Khuyến Mãi Ngay
                        </button>
                    </div>

                    {{-- Vùng Kết Quả AI Sinh Ra --}}
                    <div id="owner-ai-loading" class="hidden py-10 text-center">
                        <div class="w-10 h-10 border-3 border-purple-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                        <p class="font-bold text-purple-900 text-sm">Kira AI đang sáng tạo nội dung khuyến mãi...</p>
                        <p class="text-xs text-purple-600 mt-1">Đang chọn lọc từ ngữ kích thích vị giác và chốt đơn.</p>
                    </div>

                    <div id="owner-ai-result-box" class="hidden space-y-4 pt-4 border-t border-purple-100">
                        <div class="flex items-center justify-between">
                            <h5 class="font-bold text-sm text-purple-900 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-purple-600">check_circle</span>
                                Kết Quả Sáng Tạo Từ AI
                            </h5>
                            <span id="owner-res-source-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-800"></span>
                        </div>

                        {{-- Tiêu đề --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Tiêu đề chiến dịch</label>
                            <input type="text" id="owner-res-tieu-de" class="w-full font-bold text-sm px-3 py-2 bg-purple-50/50 border border-purple-200 rounded-xl outline-none">
                        </div>

                        {{-- Voucher & Slogan --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Mã Voucher gợi ý</label>
                                <div class="flex gap-1.5">
                                    <input type="text" id="owner-res-voucher" class="w-full font-mono font-bold text-purple-800 text-sm px-3 py-2 bg-purple-50 border border-purple-200 rounded-xl outline-none uppercase">
                                    <button type="button" onclick="copyOwnerGeneratedVoucher()" class="px-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[16px]">content_copy</span>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Slogan ngắn gọn</label>
                                <input type="text" id="owner-res-slogan" class="w-full italic text-xs text-gray-700 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                            </div>
                        </div>

                        {{-- Nội dung bài đăng --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-[11px] font-bold text-gray-500 uppercase">Bài viết mạng xã hội (Facebook / TikTok / Zalo)</label>
                                <button type="button" onclick="copyOwnerGeneratedPost()" class="text-xs font-bold text-purple-600 hover:text-purple-800 flex items-center gap-1 cursor-pointer">
                                    <span class="material-symbols-outlined text-[14px]">content_copy</span>
                                    Sao chép bài viết
                                </button>
                            </div>
                            <textarea id="owner-res-noi-dung" rows="7" class="w-full text-xs leading-relaxed text-gray-800 p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-sans"></textarea>
                        </div>

                        {{-- Thể lệ --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Thể lệ & Điều kiện áp dụng</label>
                            <textarea id="owner-res-the-le" rows="3" class="w-full text-xs text-gray-600 p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none font-sans"></textarea>
                        </div>

                        {{-- Hashtags --}}
                        <div>
                            <p id="owner-res-hashtags" class="text-xs font-mono text-purple-700 bg-purple-50 px-3 py-1.5 rounded-lg border border-purple-100"></p>
                        </div>

                        {{-- Nút lưu vào quán --}}
                        <div class="pt-3 flex flex-wrap items-center gap-2.5 border-t border-gray-100">
                            <button type="button" onclick="copyOwnerGeneratedPost()"
                                    class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">content_copy</span>
                                Sao chép bài viết
                            </button>

                            <button type="button" onclick="clearOwnerGeneratedPromo()"
                                    class="px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer"
                                    title="Xóa nội dung voucher vừa sinh ra">
                                <span class="material-symbols-outlined text-[16px]">delete_sweep</span>
                                <span>Xóa voucher vừa tạo</span>
                            </button>

                            <button type="button" onclick="saveOwnerGeneratedPromotion()" id="btn-owner-save"
                                    class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer ml-auto"
                                    style="background-color: #059669; color: #ffffff;">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                Lưu & Kích Hoạt Ưu Đãi Lên Quán Mới
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-6">

            {{-- Thao tác nhanh --}}
            <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm space-y-4">
                <h3 class="text-lg font-bold text-on-surface border-b border-gray-100 pb-3">Thao tác nhanh</h3>

                <div class="space-y-2">
                    {{-- Nút AI Tạo Khuyến Mãi --}}
                    <a href="#owner-promotions-section" onclick="setOwnerVenueTab('#owner-promotions-section')"
                       class="w-full h-11 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold text-[14px] rounded-xl flex items-center justify-center gap-2 shadow-sm hover:from-purple-700 hover:to-indigo-700 transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">auto_awesome</span> AI Tạo Khuyến Mãi (✨)
                    </a>

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
    const ownerVenueTabs = Array.from(document.querySelectorAll('#owner-venue-tabs a[href^="#owner-"]'));

    function setOwnerVenueTab(hash) {
        const requestedId = (hash || '').replace(/^#/, '');
        const activeId = ownerVenueTabs.some(tab => tab.hash === `#${requestedId}`)
            ? requestedId
            : 'owner-menu-section';

        ownerVenueTabs.forEach(tab => {
            const active = tab.hash === `#${activeId}`;
            tab.classList.toggle('tab-active', active);
            tab.classList.toggle('text-gray-500', !active);
            tab.classList.toggle('hover:text-primary', !active);
            if (active) tab.setAttribute('aria-current', 'location');
            else tab.removeAttribute('aria-current');

            const panel = document.querySelector(tab.hash);
            if (panel) panel.hidden = !active;
        });
    }

    ownerVenueTabs.forEach(tab => tab.addEventListener('click', event => {
        event.preventDefault();
        setOwnerVenueTab(tab.hash);
    }));
    window.addEventListener('hashchange', () => setOwnerVenueTab(window.location.hash));
    setOwnerVenueTab(window.location.hash);

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

    /* =========================================================
       KIRA AI PROMOTION STUDIO (CHỦ QUÁN)
    ========================================================= */
    function getOwnerTodayString() {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        const d = String(now.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function validateOwnerPromoDates() {
        const startInput = document.getElementById('owner-ai-ngay-bat-dau');
        const endInput = document.getElementById('owner-ai-ngay-ket-thuc');
        const errorP = document.getElementById('owner-promo-date-error');
        const errorText = document.getElementById('owner-promo-date-error-text');

        const startVal = startInput?.value;
        const endVal = endInput?.value;
        const todayStr = getOwnerTodayString();

        let errorMsg = '';
        let invalidInput = null;

        if (startVal && startVal < todayStr) {
            errorMsg = 'Ngày bắt đầu áp dụng không được ở trong quá khứ (phải từ hôm nay trở đi)!';
            invalidInput = startInput;
        } else if (endVal && endVal < todayStr) {
            errorMsg = 'Ngày kết thúc áp dụng không được ở trong quá khứ (phải từ hôm nay trở đi)!';
            invalidInput = endInput;
        } else if (startVal && endVal && endVal < startVal) {
            errorMsg = 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu!';
            invalidInput = endInput;
        }

        startInput?.classList.remove('border-rose-500', 'bg-rose-50/40');
        endInput?.classList.remove('border-rose-500', 'bg-rose-50/40');

        if (errorMsg) {
            if (invalidInput) {
                invalidInput.classList.add('border-rose-500', 'bg-rose-50/40');
            }
            if (errorP && errorText) {
                errorText.innerText = errorMsg;
                errorP.classList.remove('hidden');
            }
            return { valid: false, message: errorMsg, input: invalidInput };
        } else {
            if (errorP) errorP.classList.add('hidden');
            if (startVal && endInput) {
                endInput.min = startVal > todayStr ? startVal : todayStr;
            }
            return { valid: true };
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const startInput = document.getElementById('owner-ai-ngay-bat-dau');
        const endInput = document.getElementById('owner-ai-ngay-ket-thuc');
        startInput?.addEventListener('change', validateOwnerPromoDates);
        startInput?.addEventListener('input', validateOwnerPromoDates);
        endInput?.addEventListener('change', validateOwnerPromoDates);
        endInput?.addEventListener('input', validateOwnerPromoDates);
    });

    async function generateOwnerAiPromotion() {
        const monAn = document.getElementById('owner-ai-mon-an')?.value || '';
        const loaiKm = document.getElementById('owner-ai-loai-km')?.value || 'giam_phan_tram';
        const giaTri = document.getElementById('owner-ai-gia-tri')?.value || '20%';
        const dipKm = document.getElementById('owner-ai-dip')?.value || '';
        const phongCach = document.getElementById('owner-ai-phong-cach')?.value || 'hao_hung';
        const ngayBatDau = document.getElementById('owner-ai-ngay-bat-dau')?.value || '';
        const ngayKetThuc = document.getElementById('owner-ai-ngay-ket-thuc')?.value || '';
        const yeuCauThem = document.getElementById('owner-ai-yeu-cau')?.value || '';

        // KIỂM TRA LỖI: Thời hạn áp dụng không được ở trong quá khứ
        const dateCheck = validateOwnerPromoDates();
        if (!dateCheck.valid) {
            alert('⚠️ ' + dateCheck.message);
            if (dateCheck.input) dateCheck.input.focus();
            return;
        }

        const loading = document.getElementById('owner-ai-loading');
        const resultBox = document.getElementById('owner-ai-result-box');
        const btn = document.getElementById('btn-owner-generate');

        loading?.classList.remove('hidden');
        resultBox?.classList.add('hidden');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">refresh</span> Đang sinh nội dung bằng AI...';
        }

        try {
            const res = await fetch('{{ route("chu-quan.quan.khuyen-mai.ai-generate", $quan->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    mon_an: monAn,
                    loai_khuyen_mai: loaiKm,
                    gia_tri_giam: giaTri,
                    dip_khuyen_mai: dipKm,
                    phong_cach: phongCach,
                    ngay_bat_dau: ngayBatDau,
                    ngay_ket_thuc: ngayKetThuc,
                    yeu_cau_them: yeuCauThem
                })
            });

            const data = await res.json();

            if (res.ok && data.success && data.data) {
                const d = data.data;
                document.getElementById('owner-res-tieu-de').value = d.tieu_de || '';
                document.getElementById('owner-res-voucher').value = d.ma_voucher || '';
                document.getElementById('owner-res-slogan').value = d.slogan || '';
                document.getElementById('owner-res-noi-dung').value = d.noi_dung || '';
                document.getElementById('owner-res-the-le').value = d.the_le || '';
                document.getElementById('owner-res-hashtags').innerText = d.hashtags || '#QuanMoi #KhuyenMai';

                const badge = document.getElementById('owner-res-source-badge');
                if (badge) {
                    if (d.source === 'kira_ai') {
                        badge.innerText = '⚡ Tạo bởi Kira AI (' + (d.model || 'kira-3.5-flash') + ')';
                        badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
                    } else {
                        badge.innerText = '💡 Bộ Sinh Thông Minh Dự Phòng';
                        badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-800';
                    }
                }

                resultBox?.classList.remove('hidden');
                resultBox?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Không thể tạo nội dung khuyến mãi');
                alert('Có lỗi xảy ra: ' + errorMsg);
            }
        } catch (err) {
            alert('Lỗi kết nối tới máy chủ AI.');
        } finally {
            loading?.classList.add('hidden');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">auto_awesome</span> Kira AI Tạo Khuyến Mãi Ngay';
            }
        }
    }

    async function saveOwnerGeneratedPromotion() {
        const tieuDe = document.getElementById('owner-res-tieu-de')?.value || '';
        const maVoucher = document.getElementById('owner-res-voucher')?.value || '';
        const slogan = document.getElementById('owner-res-slogan')?.value || '';
        const noiDung = document.getElementById('owner-res-noi-dung')?.value || '';
        const theLe = document.getElementById('owner-res-the-le')?.value || '';
        const loaiKm = document.getElementById('owner-ai-loai-km')?.value || 'giam_phan_tram';
        const giaTri = document.getElementById('owner-ai-gia-tri')?.value || '';
        const monAn = document.getElementById('owner-ai-mon-an')?.value || '';
        const ngayBatDau = document.getElementById('owner-ai-ngay-bat-dau')?.value || '';
        const ngayKetThuc = document.getElementById('owner-ai-ngay-ket-thuc')?.value || '';

        if (!tieuDe || !noiDung) {
            alert('Vui lòng tạo nội dung khuyến mãi trước khi lưu!');
            return;
        }

        // KIỂM TRA LỖI: Thời hạn áp dụng không được ở trong quá khứ
        const dateCheck = validateOwnerPromoDates();
        if (!dateCheck.valid) {
            alert('⚠️ ' + dateCheck.message);
            if (dateCheck.input) dateCheck.input.focus();
            return;
        }

        const btn = document.getElementById('btn-owner-save');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">refresh</span> Đang lưu...';
        }

        try {
            const res = await fetch('{{ route("chu-quan.quan.khuyen-mai.store", $quan->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tieu_de: tieuDe,
                    ma_voucher: maVoucher,
                    slogan: slogan,
                    noi_dung: noiDung,
                    the_le: theLe,
                    loai_khuyen_mai: loaiKm,
                    gia_tri_giam: giaTri,
                    mon_ap_dung: monAn,
                    ngay_bat_dau: ngayBatDau || null,
                    ngay_ket_thuc: ngayKetThuc || null,
                    ai_provider: 'kira_ai'
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                alert('🎉 Tuyệt vời! ' + data.message);
                window.location.reload();
            } else {
                const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Không thể lưu ưu đãi');
                alert('Lỗi lưu: ' + errorMsg);
            }
        } catch (err) {
            alert('Lỗi kết nối máy chủ.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-[16px]">check_circle</span> Lưu & Kích Hoạt Ưu Đãi Lên Quán Mới';
                btn.style.backgroundColor = '#059669';
                btn.style.color = '#ffffff';
            }
        }
    }

    async function toggleOwnerPromotion(id) {
        try {
            const res = await fetch('/chu-quan/quan/{{ $quan->slug }}/khuyen-mai/' + id + '/toggle', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                const btn = document.getElementById('btn-toggle-' + id);
                const badge = document.getElementById('badge-status-' + id);
                if (data.trang_thai) {
                    if (btn) {
                        btn.className = 'px-2.5 py-1 text-xs font-semibold rounded-lg transition bg-emerald-100 text-emerald-800';
                        btn.innerText = 'Bật';
                    }
                    if (badge) {
                        badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
                        badge.innerText = 'Đang hoạt động';
                    }
                } else {
                    if (btn) {
                        btn.className = 'px-2.5 py-1 text-xs font-semibold rounded-lg transition bg-gray-100 text-gray-600';
                        btn.innerText = 'Tắt';
                    }
                    if (badge) {
                        badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500';
                        badge.innerText = 'Tạm dừng / Hết hạn';
                    }
                }
            } else {
                alert('Không thể thay đổi trạng thái.');
            }
        } catch (err) {
            alert('Lỗi kết nối.');
        }
    }

    function clearOwnerGeneratedPromo() {
        if (!confirm('Bạn có chắc chắn muốn xóa bản nháp voucher vừa tạo này không?')) return;
        document.getElementById('owner-res-tieu-de').value = '';
        document.getElementById('owner-res-voucher').value = '';
        document.getElementById('owner-res-slogan').value = '';
        document.getElementById('owner-res-noi-dung').value = '';
        document.getElementById('owner-res-the-le').value = '';
        document.getElementById('owner-res-hashtags').innerText = '';
        document.getElementById('owner-ai-result-box')?.classList.add('hidden');
    }

    async function deleteOwnerPromotion(id, title = '') {
        if (!confirm(`Bạn có chắc chắn muốn xóa vĩnh viễn voucher/ưu đãi "${title || 'này'}" không?`)) return;

        try {
            const res = await fetch('/chu-quan/quan/{{ $quan->slug }}/khuyen-mai/' + id, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                const card = document.getElementById('km-card-' + id);
                if (card) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => card.remove(), 300);
                }
                const countElem = document.getElementById('promo-total-count');
                if (countElem) {
                    const current = parseInt(countElem.innerText) || 1;
                    countElem.innerText = Math.max(0, current - 1);
                }
                alert('✅ Đã xóa voucher thành công!');
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể xóa ưu đãi.'));
            }
        } catch (err) {
            alert('Lỗi kết nối khi xóa.');
        }
    }

    function copyOwnerGeneratedVoucher() {
        const val = document.getElementById('owner-res-voucher')?.value;
        if (!val) return;
        navigator.clipboard.writeText(val).then(() => alert('Đã sao chép mã voucher: ' + val));
    }

    function copyOwnerGeneratedPost() {
        const tieuDe = document.getElementById('owner-res-tieu-de')?.value || '';
        const noiDung = document.getElementById('owner-res-noi-dung')?.value || '';
        const theLe = document.getElementById('owner-res-the-le')?.value || '';
        const hashtags = document.getElementById('owner-res-hashtags')?.innerText || '';

        const full = tieuDe + "\n\n" + noiDung + "\n\n📋 THỂ LỆ & ĐIỀU KIỆN:\n" + theLe + "\n\n" + hashtags;
        navigator.clipboard.writeText(full).then(() => alert('Đã sao chép toàn bộ bài đăng quảng bá mạng xã hội!'));
    }

    function copyExistingPromoPost(id) {
        const box = document.getElementById('promo-full-content-' + id);
        if (!box) return;
        navigator.clipboard.writeText(box.innerText.trim()).then(() => alert('Đã sao chép nội dung bài viết ưu đãi!'));
    }

    function togglePromoContent(id) {
        const box = document.getElementById('promo-full-content-' + id);
        const label = document.getElementById('label-toggle-' + id);
        if (!box) return;
        if (box.classList.contains('hidden')) {
            box.classList.remove('hidden');
            if (label) label.innerText = 'Thu gọn bài viết';
        } else {
            box.classList.add('hidden');
            if (label) label.innerText = 'Xem nội dung bài viết';
        }
    }

    async function ownerVerifyVoucher() {
        const input = document.getElementById('owner-verify-code-input');
        const code = input ? input.value.trim().toUpperCase() : '';
        const resBox = document.getElementById('owner-verify-result-box');

        if (!code) {
            if (resBox) {
                resBox.className = 'text-xs p-3.5 rounded-2xl border bg-amber-50 text-amber-800 border-amber-200 block';
                resBox.textContent = '⚠️ Vui lòng nhập mã voucher cần xác thực!';
            }
            return;
        }

        if (resBox) {
            resBox.className = 'text-xs p-3.5 rounded-2xl border bg-gray-50 text-gray-700 border-gray-200 block';
            resBox.textContent = '⏳ Đang kiểm tra mã...';
        }

        try {
            const res = await fetch('{{ route("chu-quan.quan.khuyen-mai.kiem-tra", $quan->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ma_voucher: code })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                const item = data.data;
                const statusHtml = item.trang_thai && !item.is_expired
                    ? '<span class="text-emerald-700 font-bold">✅ HỢP LỆ & ĐANG KÍCH HOẠT</span>'
                    : (item.is_expired ? '<span class="text-rose-600 font-bold">❌ ĐÃ HẾT HẠN</span>' : '<span class="text-amber-600 font-bold">⚠️ ĐANG TẠM DỪNG</span>');

                resBox.className = item.trang_thai && !item.is_expired
                    ? 'text-xs p-3.5 rounded-2xl border bg-emerald-50 text-emerald-900 border-emerald-200 block space-y-1 shadow-2xs'
                    : 'text-xs p-3.5 rounded-2xl border bg-rose-50 text-rose-900 border-rose-200 block space-y-1 shadow-2xs';

                resBox.innerHTML = `
                    <div class="flex items-center justify-between pb-1 border-b border-gray-200/50">
                        <span class="font-bold text-sm">Mã: <span class="font-mono">${item.ma_voucher}</span></span>
                        ${statusHtml}
                    </div>
                    <div class="pt-1 space-y-0.5 text-[11px]">
                        <p><strong>Ưu đãi:</strong> ${item.gia_tri_khuyen_mai} - ${item.tieu_de}</p>
                        <p><strong>Thời hạn:</strong> ${item.ngay_bat_dau || 'Từ nay'} đến ${item.ngay_ket_thuc}</p>
                        ${item.dieu_kien_ap_dung ? `<p><strong>Điều kiện:</strong> ${item.dieu_kien_ap_dung}</p>` : ''}
                    </div>
                `;
            } else {
                resBox.className = 'text-xs p-3.5 rounded-2xl border bg-rose-50 text-rose-800 border-rose-200 block';
                resBox.innerHTML = `❌ <strong>Không hợp lệ:</strong> ${data.message || 'Mã không tồn tại!'}`;
            }
        } catch (err) {
            console.error(err);
            if (resBox) {
                resBox.className = 'text-xs p-3.5 rounded-2xl border bg-rose-50 text-rose-800 border-rose-200 block';
                resBox.textContent = '❌ Lỗi kết nối khi xác thực.';
            }
        }
    }
</script>
@endpush
