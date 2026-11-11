@extends('layouts.app')

@section('title', $quan->ten_quan . ' — Quản lý địa điểm | Quán Mới')

@section('content')
<main class="pt-6 pb-20 max-w-7xl mx-auto px-4 md:px-8 flex-grow">


    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
        <style>
            .tab-active {
                border-bottom: 2px solid #ea580c;
                color: #ea580c !important;
                font-weight: 700;
                margin-bottom: -1px;
            }
            .owner-confirm-btn-danger {
                background-color: #dc2626 !important;
                background-image: linear-gradient(135deg, #ef4444, #dc2626) !important;
                color: #ffffff !important;
                border: 1px solid #dc2626 !important;
            }
            .owner-confirm-btn-danger:hover {
                background-color: #b91c1c !important;
                background-image: linear-gradient(135deg, #dc2626, #b91c1c) !important;
                color: #ffffff !important;
            }
            .owner-confirm-btn-cancel {
                background-color: #ffffff !important;
                color: #4b5563 !important;
                border: 1px solid #e5e7eb !important;
            }
            .owner-confirm-btn-cancel:hover {
                background-color: #f9fafb !important;
                color: #1f2937 !important;
                border-color: #d1d5db !important;
            }
        </style>
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

                    {{-- Nút Xem trang khách: Chuyển hướng trực tiếp trong cùng tab, không mở tab mới, không hiện khung popup --}}
                    <a href="{{ route('quan.detail', ['slug' => $quan->slug]) }}"
                       class="px-3.5 py-2 sm:px-4 sm:py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-bold text-xs sm:text-[13.5px] rounded-xl transition-all flex items-center gap-1.5 border border-white/30 cursor-pointer relative z-20 shadow-2xs"
                       title="Xem trực tiếp giao diện của thực khách">
                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                        <span>Xem trang khách</span>
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
            <nav id="owner-venue-tabs" class="flex border-b border-gray-200 gap-3 sm:gap-4 md:gap-5 overflow-x-auto no-scrollbar" aria-label="Điều hướng chi tiết quán">
                <a href="#owner-menu-section" aria-current="location" class="tab-active py-3 text-xs sm:text-sm font-semibold whitespace-nowrap px-1">THỰC ĐƠN</a>
                <a href="#owner-promotions-section" class="text-gray-500 hover:text-primary py-3 text-xs sm:text-sm font-semibold whitespace-nowrap px-1 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] text-amber-500">auto_awesome</span>
                    ƯU ĐÃI & AI
                </a>
                <a href="#owner-videos-section" class="text-gray-500 hover:text-primary py-3 text-xs sm:text-sm font-semibold whitespace-nowrap px-1 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] text-orange-500">smart_display</span>
                    VIDEO REVIEW ({{ $videos->count() }})
                </a>
                <a href="#owner-reviews-section" class="text-gray-500 hover:text-primary py-3 text-xs sm:text-sm font-semibold whitespace-nowrap px-1 transition-colors flex items-center gap-1.5" onclick="markOwnerReviewsAsSeen('{{ $quan->slug }}')">
                    <span class="material-symbols-outlined text-[16px] text-amber-500">rate_review</span>
                    <span>ĐÁNH GIÁ (<span id="owner-reviews-total-count">{{ $ownerReviewCount ?? 0 }}</span>)</span>
                    <span id="owner-reviews-tab-badge" class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 {{ ($ownerUnrepliedCount ?? 0) > 0 ? '' : 'hidden' }}" title="Số đánh giá chưa phản hồi">
                        <span id="owner-reviews-unreplied-count">{{ $ownerUnrepliedCount ?? 0 }}</span> chưa trả lời
                    </span>
                </a>
                <a href="#owner-info-section" class="text-gray-500 hover:text-primary py-3 text-xs sm:text-sm font-semibold whitespace-nowrap px-1 transition-colors">THÔNG TIN</a>
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
            <section hidden class="bg-white rounded-[24px] p-6 md:p-8 border border-gray-100 shadow-sm space-y-6" id="owner-reviews-section">
                @php
                    $ownerReviews = $quan->danhGia->sortByDesc('updated_at');
                    $ownerReviewCount = $ownerReviews->count();
                    $ownerAverageRating = $ownerReviewCount ? $ownerReviews->avg('so_sao') : 0;
                    $summaryData = $aiReviewSummary ?? null;
                    $hasAiSummary = !empty($summaryData) && !empty($summaryData['eligible']);
                @endphp
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-xl font-extrabold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500">star</span>
                        <span>Đánh giá của khách</span>
                        (<span id="owner-reviews-header-count">{{ $ownerReviewCount }}</span>)
                    </h3>
                    <p id="owner-reviews-avg-rating" class="font-bold text-amber-600 {{ $ownerReviewCount ? '' : 'hidden' }}">
                        ★ {{ number_format($ownerAverageRating, 1) }} / 5
                    </p>
                </div>

                {{-- KHUNG AI TÓM TẮT & PHÂN TÍCH ĐÁNH GIÁ DÀNH CHO CHỦ QUÁN --}}
                <div id="ai-review-summary-card" class="rounded-3xl border border-purple-100 bg-gradient-to-br from-purple-50/60 via-white to-indigo-50/40 p-5 md:p-6 shadow-xs relative overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-gradient-to-br from-purple-400/10 to-indigo-500/10 blur-2xl pointer-events-none"></div>

                    @if(!$hasAiSummary)
                        {{-- Chưa đủ đánh giá để phân tích --}}
                        <div id="ai-summary-empty-state" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <span class="material-symbols-outlined text-xl">auto_awesome</span>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-bold text-sm text-gray-900">Kira AI • Trợ lý Phân tích Đánh giá</h4>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">Dành riêng cho Chủ quán</span>
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed">
                                        {{ $summaryData['message'] ?? 'Khi quán có từ 2 đánh giá trở lên, Kira AI sẽ tự động phân tích khẩu vị thực khách, thống kê tỷ lệ hài lòng và đề xuất các giải pháp nâng cao chất lượng dịch vụ.' }}
                                    </p>
                                </div>
                            </div>
                            @if($ownerReviewCount >= 2)
                                <button type="button" onclick="refreshAiReviewSummary('{{ $quan->slug }}')"
                                        id="btn-trigger-ai-summary"
                                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 shrink-0 cursor-pointer">
                                    <span class="material-symbols-outlined text-sm">auto_awesome</span>
                                    <span>Kích hoạt phân tích AI</span>
                                </button>
                            @endif
                        </div>
                    @else
                        {{-- Đã có dữ liệu phân tích --}}
                        <div id="ai-summary-content" class="space-y-5">
                            {{-- Header của Báo cáo AI --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-purple-100/80">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center shadow-sm shrink-0">
                                        <span class="material-symbols-outlined text-xl">smart_toy</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-extrabold text-sm sm:text-base text-gray-900">Báo cáo Phân tích Trải nghiệm Khách hàng</h4>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                                Nội bộ Chủ quán
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1.5">
                                            <span>Tổng hợp từ <strong>{{ $summaryData['review_count'] ?? $ownerReviewCount }}</strong> đánh giá</span>
                                            <span>•</span>
                                            <span>Cập nhật: <span id="ai-summary-time">{{ $summaryData['analyzed_at'] ?? 'Mới nhất' }}</span></span>
                                        </p>
                                    </div>
                                </div>
                                <button type="button" onclick="refreshAiReviewSummary('{{ $quan->slug }}')"
                                        id="btn-refresh-ai-summary"
                                        title="Quét và phân tích lại với các đánh giá mới nhất"
                                        class="px-3.5 py-1.5 bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 text-xs font-bold rounded-xl shadow-2xs transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
                                    <span class="material-symbols-outlined text-sm" id="icon-refresh-ai">sync</span>
                                    <span>Phân tích lại</span>
                                </button>
                            </div>

                            {{-- Tổng quan vận hành --}}
                            <div class="p-3.5 rounded-2xl bg-white/80 border border-purple-100 shadow-2xs">
                                <p class="text-xs font-bold text-purple-900 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm text-purple-600">summarize</span>
                                    Tổng kết vận hành:
                                </p>
                                <p id="ai-summary-tong-quan" class="text-xs sm:text-sm text-gray-700 leading-relaxed font-medium">
                                    {{ $summaryData['tong_quan'] ?? '' }}
                                </p>
                            </div>

                            {{-- Tỉ lệ hài lòng --}}
                            @php
                                $tiLe = $summaryData['ti_le_hai_long'] ?? ['tich_cuc' => 80, 'trung_tinh' => 15, 'can_cai_thien' => 5];
                                $tichCuc = $tiLe['tich_cuc'] ?? 80;
                                $trungTinh = $tiLe['trung_tinh'] ?? 15;
                                $canCaiThien = $tiLe['can_cai_thien'] ?? 5;
                            @endphp
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-gray-700 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm text-indigo-600">pie_chart</span>
                                        Tỉ lệ cảm xúc khách hàng:
                                    </span>
                                    <div class="flex items-center gap-3 text-[11px]">
                                        <span class="text-emerald-700 font-bold">● {{ $tichCuc }}% Hài lòng</span>
                                        <span class="text-amber-700 font-bold">● {{ $trungTinh }}% Bình thường</span>
                                        <span class="text-rose-700 font-bold">● {{ $canCaiThien }}% Góp ý/Phàn nàn</span>
                                    </div>
                                </div>
                                <div class="w-full h-3 rounded-full bg-gray-100 overflow-hidden flex shadow-inner">
                                    <div style="width: {{ $tichCuc }}%;" class="bg-emerald-500 h-full transition-all duration-500" title="{{ $tichCuc }}% Hài lòng"></div>
                                    <div style="width: {{ $trungTinh }}%;" class="bg-amber-400 h-full transition-all duration-500" title="{{ $trungTinh }}% Bình thường"></div>
                                    <div style="width: {{ $canCaiThien }}%;" class="bg-rose-500 h-full transition-all duration-500" title="{{ $canCaiThien }}% Cần cải thiện"></div>
                                </div>
                            </div>

                            {{-- Lưới 2 cột: Khách khen & Khách góp ý --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Điểm khen --}}
                                <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-2">
                                    <h5 class="text-xs font-bold text-emerald-900 flex items-center gap-1.5 uppercase tracking-wide">
                                        <span class="material-symbols-outlined text-sm text-emerald-600">thumb_up</span>
                                        Điểm khách khen nhiều nhất:
                                    </h5>
                                    <ul id="ai-summary-diem-khen" class="space-y-1.5 text-xs text-gray-700">
                                        @foreach($summaryData['diem_khen'] ?? [] as $khen)
                                            <li class="flex items-start gap-1.5">
                                                <span class="text-emerald-600 font-bold mt-0.5">✓</span>
                                                <span>{{ $khen }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- Điểm cần khắc phục --}}
                                <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-100 space-y-2">
                                    <h5 class="text-xs font-bold text-amber-900 flex items-center gap-1.5 uppercase tracking-wide">
                                        <span class="material-symbols-outlined text-sm text-amber-600">warning</span>
                                        Vấn đề khách góp ý / phàn nàn:
                                    </h5>
                                    <ul id="ai-summary-diem-che" class="space-y-1.5 text-xs text-gray-700">
                                        @foreach($summaryData['diem_can_khac_phuc'] ?? [] as $che)
                                            <li class="flex items-start gap-1.5">
                                                <span class="text-amber-600 font-bold mt-0.5">⚠</span>
                                                <span>{{ $che }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            {{-- Lưới 2 khối: Top món ăn & Đề xuất hành động --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                {{-- Món ăn nổi bật --}}
                                <div class="p-4 rounded-2xl bg-white border border-gray-100 shadow-2xs space-y-2.5">
                                    <h5 class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-sm text-orange-500">restaurant</span>
                                        Món được khen nhiều (Nên đẩy mạnh):
                                    </h5>
                                    <div id="ai-summary-top-mon" class="flex flex-wrap gap-1.5">
                                        @forelse($summaryData['top_mon_an'] ?? [] as $mon)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-100">
                                                <span>🍲</span>
                                                <span>{{ $mon }}</span>
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400">Chưa đủ dữ liệu tên món</span>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Đề xuất hành động --}}
                                <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-100 space-y-2.5">
                                    <h5 class="text-xs font-bold text-purple-900 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-sm text-purple-600">lightbulb</span>
                                        Đề xuất hành động:
                                    </h5>
                                    <ul id="ai-summary-khuyen-nghi" class="space-y-1.5 text-xs text-gray-700">
                                        @foreach($summaryData['khuyen_nghi_chuyen_gia'] ?? [] as $index => $action)
                                            <li class="flex items-start gap-1.5">
                                                <span class="w-4 h-4 rounded-full bg-purple-200 text-purple-800 text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">
                                                    {{ $index + 1 }}
                                                </span>
                                                <span class="font-medium">{{ $action }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div id="owner-reviews-list-container" class="space-y-3">
                    @include('chu-quan.partials.reviews-list', ['ownerReviews' => $ownerReviews, 'quan' => $quan])
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
                        <button type="button" onclick="document.getElementById('kira-ai-form-box')?.scrollIntoView({behavior: 'smooth'})"
                           class="px-3.5 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-xs rounded-xl transition flex items-center gap-1 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">add_circle</span>
                            Tạo ưu đãi mới với AI
                        </button>
                    </div>

                    {{-- Banner Giải Thích Quy Trình Phê Duyệt Voucher --}}
                    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 text-xl shrink-0 mt-0.5">verified_user</span>
                        <div class="text-xs text-amber-900 leading-relaxed">
                            <p class="font-bold mb-0.5">Quy trình kiểm duyệt voucher & khuyến mãi:</p>
                            <p>Khi bạn tạo voucher hoặc ưu đãi mới, chương trình sẽ ở trạng thái <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-bold text-[11px]"><span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Chờ duyệt</span>. <strong>Ban quản trị (Admin) sẽ kiểm tra nội dung và phê duyệt</strong> trước khi mã voucher được kích hoạt công khai cho thực khách trên trang chi tiết quán.</p>
                        </div>
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
                                        @if($km->trang_thai_duyet === 'cho_duyet')
                                            <span id="badge-status-{{ $km->id }}" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                🟡 Chờ Admin duyệt
                                            </span>
                                        @elseif($km->trang_thai_duyet === 'tu_choi')
                                            <span id="badge-status-{{ $km->id }}" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1" title="{{ $km->ly_do_tu_choi }}">
                                                <span class="material-symbols-outlined text-[13px]">block</span>
                                                🔴 Bị từ chối
                                            </span>
                                        @elseif($km->con_han)
                                            <span id="badge-status-{{ $km->id }}" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                🟢 Đã duyệt (Đang hoạt động)
                                            </span>
                                        @else
                                            <span id="badge-status-{{ $km->id }}" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">
                                                Đã duyệt (Tạm dừng / Hết hạn)
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2" id="owner-actions-{{ $km->id }}">
                                        @if($km->trang_thai_duyet === 'cho_duyet')
                                            <span class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-lg font-medium italic flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[13px]">hourglass_top</span>
                                                Đang chờ duyệt
                                            </span>
                                        @elseif($km->trang_thai_duyet === 'tu_choi')
                                            <span class="text-[11px] text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-lg font-medium italic flex items-center gap-1" title="{{ $km->ly_do_tu_choi }}">
                                                <span class="material-symbols-outlined text-[13px]">info</span>
                                                Bị từ chối
                                            </span>
                                        @else
                                            {{-- Toggle kích hoạt --}}
                                            <button type="button" onclick="toggleOwnerPromotion('{{ $km->id }}')"
                                                    id="btn-toggle-{{ $km->id }}"
                                                    title="{{ $km->trang_thai ? 'Bấm để tạm dừng' : 'Bấm để kích hoạt' }}"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg transition {{ $km->trang_thai ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $km->trang_thai ? 'Bật' : 'Tắt' }}
                                            </button>
                                        @endif
                                        {{-- Nút Xóa Voucher --}}
                                        <button type="button" onclick="deleteOwnerPromotion('{{ $km->id }}', '{{ $km->ma_voucher ?: $km->tieu_de }}')" title="Xóa voucher này"
                                                class="px-2.5 py-1 text-xs font-bold rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 flex items-center gap-1 transition cursor-pointer shadow-2xs">
                                            <span class="material-symbols-outlined text-[15px]">delete</span>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                </div>

                                <div id="km-reject-reason-wrap-{{ $km->id }}">
                                    @if($km->trang_thai_duyet === 'tu_choi' && $km->ly_do_tu_choi)
                                        <div class="mb-2 p-2 rounded-xl bg-rose-50 text-rose-700 text-xs border border-rose-100 flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[14px] shrink-0 text-rose-600">info</span>
                                            <span><strong>Lý do từ chối:</strong> {{ $km->ly_do_tu_choi }}</span>
                                        </div>
                                    @endif
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
                                    class="px-5 py-2.5 bg-orange-500 text-white font-extrabold text-xs rounded-xl shadow-md flex items-center gap-1.5 cursor-pointer ml-auto">
                                <span class="material-symbols-outlined text-[16px]">send</span>
                                Gửi Phê Duyệt Voucher Lên Admin
                            </button>
                        </div>
                    </div>
            </section>

            {{-- Owner TikTok Videos Review Section --}}
            <section hidden class="space-y-6" id="owner-videos-section">
                <div class="bg-white rounded-[24px] p-6 md:p-8 border border-gray-100 shadow-sm space-y-5">
                    {{-- Section Header --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center shadow-2xs">
                                <span class="material-symbols-outlined text-xl">smart_display</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-extrabold text-on-surface flex items-center gap-2">
                                    Video Review TikTok Của Quán
                                    <span class="text-xs bg-orange-100 text-orange-700 font-bold px-2.5 py-0.5 rounded-full" id="owner-video-count-badge">{{ $videos->count() }} video</span>
                                </h4>
                                <p class="text-xs text-gray-400 mt-0.5">Quản lý và đăng tải các video review thực tế cho {{ $quan->ten_quan }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="openCreateVideoModal()"
                                class="px-4 py-2.5 bg-gradient-to-r from-orange-500 to-rose-500 hover:from-orange-600 hover:to-rose-600 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">add_circle</span>
                            Đăng video review mới
                        </button>
                    </div>

                    {{-- Banner Giải Thích Quy Trình Phê Duyệt --}}
                    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 text-xl shrink-0 mt-0.5">verified_user</span>
                        <div class="text-xs text-amber-900 leading-relaxed">
                            <p class="font-bold mb-0.5">Quy trình kiểm duyệt video review:</p>
                            <p>Khi bạn đăng video TikTok mới, video sẽ ở trạng thái <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-bold text-[11px]"><span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Chờ duyệt</span>. <strong>Ban quản trị (Admin) sẽ kiểm tra nội dung và phê duyệt</strong> trước khi video được hiển thị công khai cho khách hàng trên trang chi tiết quán và trang Video Review chung.</p>
                        </div>
                    </div>

                    {{-- Danh sách Video --}}
                    <div id="owner-videos-container">
                        @if($videos->isEmpty())
                            <div class="py-12 text-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                <div class="w-16 h-16 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center mx-auto mb-3">
                                    <span class="material-symbols-outlined text-3xl">smart_display</span>
                                </div>
                                <h5 class="text-sm font-bold text-gray-800">Quán chưa có video review nào</h5>
                                <p class="text-xs text-gray-500 max-w-md mx-auto mt-1 mb-4">Dán đường dẫn video TikTok review về quán để tự động tải video sạch, tăng lượng tương tác và thu hút thực khách đến quán!</p>
                                <button type="button" onclick="openCreateVideoModal()"
                                        class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Đăng video review đầu tiên
                                </button>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="owner-videos-grid">
                                @foreach($videos as $v)
                                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-2xs hover:shadow-md transition flex flex-col h-full" id="owner-video-card-{{ $v->id }}">
                                        {{-- Video Card Header Thumbnail --}}
                                        <div class="relative w-full aspect-[9/13] shrink-0 bg-gray-900 overflow-hidden group cursor-pointer" style="aspect-ratio: 9/13;" onclick="openPreviewVideoModal('{{ $v->clean_video_url ?: $v->embed_url }}', '{{ $v->video_id }}', '{{ addslashes($v->tieu_de) }}')">
                                            <img src="{{ $v->effective_thumbnail }}"
                                                 alt="{{ $v->tieu_de }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=400&q=80';">
                                            
                                            <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                                <div class="w-11 h-11 rounded-full bg-white/90 text-gray-900 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                                    <span class="material-symbols-outlined text-2xl">play_arrow</span>
                                                </div>
                                            </div>

                                            {{-- Badge Trạng Thái Phê Duyệt --}}
                                            <div class="absolute top-2.5 left-2.5" id="owner-video-badge-wrap-{{ $v->id }}">
                                                @if($v->trang_thai === 'da_duyet')
                                                    <span class="inline-flex items-center gap-1 bg-emerald-600/95 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                                                        <span class="material-symbols-outlined text-xs">check_circle</span>
                                                        Đã duyệt
                                                    </span>
                                                @elseif($v->trang_thai === 'cho_duyet')
                                                    <span class="inline-flex items-center gap-1 bg-amber-500/95 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                                        Chờ duyệt
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 bg-gray-600/95 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                                                        <span class="material-symbols-outlined text-xs">visibility_off</span>
                                                        Đang ẩn
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Lượt xem --}}
                                            @if($v->luot_xem > 0)
                                                <div class="absolute top-2.5 right-2.5">
                                                    <span class="inline-flex items-center gap-1 bg-black/60 backdrop-blur-xs text-white text-[10px] font-medium px-2 py-0.5 rounded-full">
                                                        <span class="material-symbols-outlined text-xs">visibility</span>
                                                        {{ number_format($v->luot_xem) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Video Card Content --}}
                                        <div class="p-3.5 flex flex-col justify-between flex-1 min-h-[110px]">
                                            <div>
                                                <h5 class="font-bold text-xs text-gray-900 line-clamp-2 leading-snug h-8 sm:h-9 overflow-hidden mb-1.5" title="{{ $v->tieu_de }}">{{ $v->tieu_de }}</h5>
                                                <div class="flex items-center gap-1.5 text-[11px] text-gray-500 mb-2">
                                                    <span class="font-medium truncate">{{ $v->nguoi_dang ?: '@reviewer' }}</span>
                                                    <span>•</span>
                                                    <span>{{ $v->created_at ? $v->created_at->format('d/m/Y') : 'Mới đây' }}</span>
                                                </div>
                                            </div>

                                            {{-- Nút Thao tác --}}
                                            <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-2 mt-auto">
                                                <button type="button" onclick="openPreviewVideoModal('{{ $v->clean_video_url ?: $v->embed_url }}', '{{ $v->video_id }}', '{{ addslashes($v->tieu_de) }}')"
                                                        class="text-xs font-bold text-primary hover:underline flex items-center gap-1 cursor-pointer">
                                                    <span class="material-symbols-outlined text-[15px]">play_circle</span>
                                                    Xem thử
                                                </button>

                                                <button type="button" onclick="deleteOwnerVideo('{{ $v->id }}', '{{ addslashes($v->tieu_de) }}')"
                                                        class="text-xs font-bold text-red-500 hover:text-red-700 flex items-center gap-1 p-1 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Xóa video này">
                                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                                    Xóa
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-6">

            {{-- Thao tác nhanh --}}
            <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">bolt</span>
                        </span>
                        Thao tác nhanh
                    </h3>
                    <span class="text-[11px] text-gray-400 font-medium">Lối tắt quản lý</span>
                </div>

                <div class="space-y-2">
                    {{-- 1. Đăng video TikTok review --}}
                    <a href="#owner-videos-section" onclick="setOwnerVenueTab('#owner-videos-section')"
                       class="group p-3 rounded-2xl border border-orange-100 bg-orange-50/40 hover:bg-orange-50 hover:border-orange-200 transition-all flex items-center gap-3 cursor-pointer">
                        <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-lg">smart_display</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <h4 class="font-bold text-xs text-gray-900 group-hover:text-orange-600 transition-colors">Đăng video review</h4>
                                <span class="bg-orange-100 text-orange-700 text-[9px] font-extrabold px-1.5 py-0.5 rounded-full uppercase">Mới</span>
                            </div>
                            <p class="text-[11px] text-gray-500 truncate">Dán link TikTok hoặc file MP4</p>
                        </div>
                        <span class="material-symbols-outlined text-gray-400 text-base group-hover:text-orange-600 group-hover:translate-x-0.5 transition-all">chevron_right</span>
                    </a>

                    {{-- 2. AI Tạo Khuyến Mãi --}}
                    <a href="#owner-promotions-section" onclick="setOwnerVenueTab('#owner-promotions-section')"
                       class="group p-3 rounded-2xl border border-purple-100 bg-purple-50/30 hover:bg-purple-50 hover:border-purple-200 transition-all flex items-center gap-3 cursor-pointer">
                        <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-lg">auto_awesome</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-xs text-gray-900 group-hover:text-purple-600 transition-colors">Kira AI Tạo ưu đãi</h4>
                            <p class="text-[11px] text-gray-500 truncate">Sinh voucher & bài viết tự động</p>
                        </div>
                        <span class="material-symbols-outlined text-gray-400 text-base group-hover:text-purple-600 group-hover:translate-x-0.5 transition-all">chevron_right</span>
                    </a>

                    {{-- 3. Quản lý / Thêm thực đơn --}}
                    <a href="{{ route('chu-quan.quan.menu.edit', $quan->slug) }}"
                       class="group p-3 rounded-2xl border border-gray-100 bg-white hover:bg-gray-50 hover:border-gray-200 transition-all flex items-center gap-3 cursor-pointer">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-lg">restaurant_menu</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-xs text-gray-900 group-hover:text-emerald-600 transition-colors">Quản lý thực đơn</h4>
                            <p class="text-[11px] text-gray-500 truncate">Thêm món ăn, đổi giá và ảnh món</p>
                        </div>
                        <span class="material-symbols-outlined text-gray-400 text-base group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all">chevron_right</span>
                    </a>

                    {{-- 4. Chỉnh sửa thông tin quán --}}
                    <button type="button" onclick="openEditModal()"
                            class="w-full text-left group p-3 rounded-2xl border border-gray-100 bg-white hover:bg-gray-50 hover:border-gray-200 transition-all flex items-center gap-3 cursor-pointer">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-xs text-gray-900 group-hover:text-blue-600 transition-colors">Chỉnh sửa thông tin</h4>
                            <p class="text-[11px] text-gray-500 truncate">Tên quán, giờ mở cửa, địa chỉ</p>
                        </div>
                        <span class="material-symbols-outlined text-gray-400 text-base group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all">chevron_right</span>
                    </button>

                    {{-- 5. Đăng thêm quán mới --}}
                    <a href="/chu-quan/dang-quan"
                       class="group p-3 rounded-2xl border border-gray-100 bg-white hover:bg-gray-50 hover:border-gray-200 transition-all flex items-center gap-3 cursor-pointer">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-lg">add_location_alt</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-xs text-gray-900 group-hover:text-amber-600 transition-colors">Đăng thêm cơ sở mới</h4>
                            <p class="text-[11px] text-gray-500 truncate">Mở thêm chi nhánh hoặc quán mới</p>
                        </div>
                        <span class="material-symbols-outlined text-gray-400 text-base group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all">chevron_right</span>
                    </a>
                </div>

                {{-- Nút Trở về trang chủ nhẹ nhàng ở dưới --}}
                <div class="pt-2 border-t border-gray-100">
                    <a href="/" class="w-full py-2.5 px-3 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-600 hover:text-gray-900 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-base">home</span>
                        <span>Trở về trang chủ</span>
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
                        <span class="material-symbols-outlined text-rose-500 text-2xl" style="font-variation-settings: 'FILL' 1;">favorite</span>
                        <p class="text-xl font-black text-rose-700 mt-0.5">{{ number_format($luotLuu, 0, ',', '.') }}</p>
                        <p class="text-[11px] font-semibold text-rose-400 uppercase tracking-wide mt-0.5">Lượt lưu</p>
                    </div>
                    {{-- Video Review --}}
                    <a href="#owner-videos-section" onclick="setOwnerVenueTab('#owner-videos-section')" class="bg-orange-50 hover:bg-orange-100 transition-colors rounded-2xl p-3 text-center cursor-pointer block">
                        <span class="material-symbols-outlined text-orange-500 text-2xl" style="font-variation-settings: 'FILL' 1;">videocam</span>
                        <p class="text-xl font-black text-orange-700 mt-0.5">{{ number_format($luotVideo, 0, ',', '.') }}</p>
                        <p class="text-[11px] font-semibold text-orange-400 uppercase tracking-wide mt-0.5">Video</p>
                    </a>
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

            {{-- ===== QR CODE SHOPEEFOOD ===== --}}
            <div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <h3 class="text-lg font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-500">qr_code_2</span>
                        Mã QR ShopeeFood
                    </h3>
                    @if($quan->shopeefood_url)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> ShopeeFood
                        </span>
                    @else
                        <button type="button" onclick="openEditModal()" class="text-[11px] font-bold text-orange-600 hover:underline cursor-pointer">
                            + Thêm link
                        </button>
                    @endif
                </div>

                @if(filled($quan->shopeefood_url))
                    <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                        In mã này và đặt tại bàn để khách quét đặt món trực tiếp trên ShopeeFood.
                    </p>

                    {{-- QR canvas container --}}
                    <div class="flex justify-center mb-4">
                        <div class="bg-white p-3 rounded-2xl border-2 border-orange-200 shadow-inner inline-block">
                            <div id="qr-code-container"></div>
                        </div>
                    </div>

                    <div class="text-center mb-4 px-2">
                        <a href="{{ $quan->shopeefood_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[11px] text-orange-600 hover:text-orange-700 font-semibold truncate max-w-full hover:underline" title="{{ $quan->shopeefood_url }}">
                            <span class="material-symbols-outlined text-[13px] shrink-0">shopping_bag</span>
                            <span class="truncate">{{ $quan->shopeefood_url }}</span>
                        </a>
                    </div>

                    <button onclick="downloadQR()"
                            class="w-full h-10 bg-gray-800 hover:bg-gray-900 text-white font-bold text-[13px] rounded-xl flex items-center justify-center gap-2 transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">download</span> Tải về PNG
                    </button>
                @else
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800 leading-relaxed flex items-start gap-2">
                        <span class="material-symbols-outlined text-[16px] text-amber-600 shrink-0 mt-0.5">info</span>
                        <div>
                            <span>Quán chưa có link ShopeeFood nên chưa có mã QR. Bấm </span>
                            <button type="button" onclick="openEditModal()" class="font-bold underline text-amber-900 cursor-pointer">Chỉnh sửa</button>
                            <span> để cập nhật link ShopeeFood tạo mã QR đặt món.</span>
                        </div>
                    </div>
                @endif
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
                           inputmode="numeric" pattern="0[0-9]{9,10}" maxlength="11"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);"
                           title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm transition">
                </div>

                {{-- Địa chỉ chi tiết & Vị trí GPS trên bản đồ --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Số nhà, Tên đường</label>
                    <input type="text" name="dia_chi_chi_tiet" id="edit-dia-chi-chi-tiet"
                           value="{{ $quan->dia_chi_chi_tiet }}"
                           placeholder="VD: 123 Nguyễn Huệ"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm transition">
                    <input type="hidden" id="edit-ten-phuong-xa" value="{{ $quan->ten_phuong_xa }}">
                    <input type="hidden" id="edit-ten-quan-huyen" value="{{ $quan->ten_quan_huyen }}">
                    <input type="hidden" id="edit-ten-tinh-thanh" value="{{ $quan->ten_tinh_thanh }}">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Vị trí ghim trên bản đồ (Hiển thị trên Quét quán lân cận)</label>
                    <x-map-picker
                        :lat="$quan->vi_do ?? 10.7769"
                        :lng="$quan->kinh_do ?? 106.7009"
                        mapId="owner-edit-map-picker"
                        latId="edit-vi-do"
                        lngId="edit-kinh-do"
                        height="h-[230px]"
                        :showCoordInputs="true"
                        :addressSelectors="['#edit-dia-chi-chi-tiet', '#edit-ten-phuong-xa', '#edit-ten-quan-huyen', '#edit-ten-tinh-thanh']"
                    />
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
                           pattern="https?:\/\/(.*shopeefood\.vn|.*shopee\.vn)(\/.*)?"
                           title="Đường dẫn ShopeeFood phải thuộc tên miền shopeefood.vn hoặc shopee.vn (ví dụ: https://shopeefood.vn/...)"
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
    function showNotify(message, type = 'warning', title = null) {
        if (typeof window.showToast === 'function') {
            const defaultTitle = type === 'error' ? 'Có lỗi xảy ra' : (type === 'success' ? 'Thành công' : (type === 'info' ? 'Thông báo' : 'Lưu ý'));
            window.showToast({
                title: title || defaultTitle,
                message: message,
                type: type
            });
        } else {
            console.warn(message);
        }
    }

    const ownerVenueTabs = Array.from(document.querySelectorAll('#owner-venue-tabs a[href^="#owner-"]'));

    function setOwnerVenueTab(hash) {
        const rawHash = (hash || '').trim();
        const requestedId = rawHash.replace(/^#/, '');

        let targetTab = ownerVenueTabs.find(tab => tab.hash === `#${requestedId}`);

        // Nếu hash trỏ tới 1 element bên trong tab (ví dụ: #kira-ai-form-box nằm trong tab #owner-promotions-section)
        if (!targetTab && requestedId) {
            const el = document.getElementById(requestedId);
            if (el) {
                const parentSection = el.closest('section[id^="owner-"], div[id^="owner-"]');
                if (parentSection && parentSection.id) {
                    targetTab = ownerVenueTabs.find(tab => tab.hash === `#${parentSection.id}`);
                }
            }
        }

        // Nếu không khớp và trang đang có tab active, giữ nguyên tab hiện tại không được tự chuyển sang thực đơn
        let activeId = 'owner-menu-section';
        if (targetTab) {
            activeId = targetTab.hash.replace(/^#/, '');
        } else {
            const currentActiveTab = ownerVenueTabs.find(tab => tab.classList.contains('tab-active'));
            if (currentActiveTab) {
                activeId = currentActiveTab.hash.replace(/^#/, '');
            }
        }

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

        if (activeId === 'owner-reviews-section') {
            if (typeof window.markOwnerReviewsAsSeen === 'function') {
                window.markOwnerReviewsAsSeen('{{ $quan->slug }}');
            }
        }
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

        // Render QR Code (chỉ tạo mã QR khi quán đã nhập link ShopeeFood)
        const qrTargetUrl = @json($quan->shopeefood_url ?: null);
        const qrContainer = document.getElementById('qr-code-container');
        if (qrContainer && qrTargetUrl) {
            new QRCode(qrContainer, {
                text: qrTargetUrl,
                width: 160,
                height: 160,
                colorDark: '#1a1a1a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    });

    /* =========================================================
       QR DOWNLOAD
    ========================================================= */
    function downloadQR() {
        // qrcodejs tạo ra một <canvas> hoặc <img> bên trong container
        const container = document.getElementById('qr-code-container');
        if (!container) return;
        const canvas = container.querySelector('canvas');
        const img    = container.querySelector('img');

        const padding = 24;
        const labelHeight = 44;
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

        // Viền bo nhẹ
        ctx.strokeStyle = '#f3f4f6';
        ctx.lineWidth = 2;
        ctx.strokeRect(1, 1, totalW - 2, totalH - 2);

        // Hàm vẽ QR sau khi có source
        const drawAndDownload = (src) => {
            const image = new Image();
            image.onload = () => {
                ctx.drawImage(image, padding, padding, qrSize, qrSize);
                
                // Tên quán
                ctx.fillStyle = '#111827';
                ctx.font = 'bold 13px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('{{ addslashes($quan->ten_quan) }}', totalW / 2, qrSize + padding + 18);

                // Dòng phụ chú ShopeeFood
                const hasShopee = Boolean(@json(!empty($quan->shopeefood_url)));
                ctx.fillStyle = hasShopee ? '#ea580c' : '#6b7280';
                ctx.font = '600 11px sans-serif';
                ctx.fillText(hasShopee ? 'Đặt món trên ShopeeFood' : 'Quét mã xem quán', totalW / 2, qrSize + padding + 34);

                const link = document.createElement('a');
                link.download = hasShopee ? 'shopeefood-qr-{{ $quan->slug }}.png' : 'qr-{{ $quan->slug }}.png';
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
                showNotify(data.message || 'Lỗi tải ảnh lên.', 'error', 'Tải ảnh thất bại');
            }
        } catch (e) {
            showNotify('Không thể kết nối máy chủ. Vui lòng thử lại sau.', 'error', 'Lỗi kết nối');
        } finally {
            uploading.classList.add('hidden');
        }
    }

    /* =========================================================
       GALLERY DELETE
    ========================================================= */
    async function deleteGalleryImage(imgId) {
        openOwnerConfirmModal({
            title: 'Xác Nhận Xóa Ảnh',
            message: 'Bạn có chắc chắn muốn xóa hình ảnh này khỏi không gian quán không?',
            icon: 'delete',
            iconClass: 'bg-red-100 text-red-600',
            confirmText: 'Xác Nhận Xóa',
            confirmIcon: 'delete',
            confirmClass: 'owner-confirm-btn-danger',
            onConfirm: async () => {
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
                        if (grid && grid.children.length === 0) {
                            grid.innerHTML = `<div class="col-span-full py-8 text-center text-gray-400 text-sm" id="gallery-empty">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-gray-300">photo_library</span>
                                Chưa có hình ảnh nào. Bấm "Thêm ảnh" để tải lên.
                            </div>`;
                        }
                        if (window.showToast) {
                            window.showToast({
                                title: 'Đã xóa ảnh',
                                message: 'Hình ảnh đã được gỡ bỏ khỏi quán thành công.',
                                type: 'success'
                            });
                        }
                    } else {
                        if (window.showToast) {
                            window.showToast({
                                title: 'Không thể xóa ảnh',
                                message: data.message || 'Không thể xóa ảnh.',
                                type: 'error'
                            });
                        }
                        if (card) { card.style.opacity = '1'; card.style.pointerEvents = 'auto'; }
                    }
                } catch (e) {
                    if (window.showToast) {
                        window.showToast({
                            title: 'Lỗi kết nối',
                            message: 'Không thể kết nối máy chủ.',
                            type: 'error'
                        });
                    }
                    if (card) { card.style.opacity = '1'; card.style.pointerEvents = 'auto'; }
                }
            }
        });
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
                showNotify('Cập nhật ảnh bìa thành công!', 'success', 'Thành công');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showNotify(data.message || 'Không thể cập nhật ảnh bìa.', 'error', 'Lỗi cập nhật');
                if (label) label.innerHTML = '<span class="material-symbols-outlined text-[18px]">add_photo_alternate</span> Đổi ảnh bìa';
            }
        } catch (e) {
            showNotify('Không thể kết nối máy chủ. Vui lòng thử lại sau.', 'error', 'Lỗi kết nối');
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
            showNotify(dateCheck.message, 'warning', 'Thời gian không hợp lệ');
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
                showNotify(errorMsg, 'error', 'Không thể tạo ưu đãi');
            }
        } catch (err) {
            showNotify('Lỗi kết nối tới máy chủ AI. Vui lòng thử lại sau.', 'error', 'Lỗi kết nối AI');
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
            showNotify('Vui lòng tạo nội dung khuyến mãi trước khi lưu!', 'warning', 'Chưa có nội dung');
            return;
        }

        // KIỂM TRA LỖI: Thời hạn áp dụng không được ở trong quá khứ
        const dateCheck = validateOwnerPromoDates();
        if (!dateCheck.valid) {
            showNotify(dateCheck.message, 'warning', 'Thời gian không hợp lệ');
            if (dateCheck.input) dateCheck.input.focus();
            return;
        }

        const btn = document.getElementById('btn-owner-save');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">refresh</span> Đang gửi duyệt...';
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
                const km = data.data;
                showNotify(data.message || 'Đã gửi phê duyệt voucher lên Admin thành công!', 'success', 'Gửi duyệt thành công');

                // 1. Chèn thẻ voucher mới ngay lập tức vào danh sách (KHÔNG LOAD TRANG, KHÔNG ĐỔI TAB)
                if (km && km.id) {
                    const list = document.getElementById('owner-promo-list');
                    const emptyAlert = document.getElementById('owner-promo-empty');
                    if (emptyAlert) emptyAlert.remove();

                    const newCard = document.createElement('div');
                    newCard.id = `km-card-${km.id}`;
                    newCard.className = 'p-4 rounded-2xl border border-amber-300 ring-2 ring-amber-400/40 bg-amber-50/20 transition bg-white shadow-xs';

                    const voucherCodeHtml = km.ma_voucher ? `
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 font-mono font-bold text-xs border border-dashed border-purple-300">
                            <span class="material-symbols-outlined text-[13px]">confirmation_number</span>
                            ${km.ma_voucher}
                        </span>
                    ` : '';

                    const discountLabel = (km.gia_tri_giam || 'Ưu đãi');
                    const promoTypeLabel = km.loai_label || 'Ưu đãi';

                    const safeTitle = (km.tieu_de || '').replace(/"/g, '&quot;');
                    const safeSlogan = km.slogan ? `<p class="text-xs italic text-gray-500 mt-0.5">"${(km.slogan || '').replace(/"/g, '&quot;')}"</p>` : '';
                    const safeDish = km.mon_ap_dung || 'Toàn bộ thực đơn';
                    const datesStr = (km.ngay_bat_dau || km.ngay_ket_thuc)
                        ? `<span>⏰ <strong>Thời gian:</strong> ${km.ngay_bat_dau ? km.ngay_bat_dau.substring(0, 10) : 'Từ nay'} đến ${km.ngay_ket_thuc ? km.ngay_ket_thuc.substring(0, 10) : 'Vô thời hạn'}</span>`
                        : '';

                    newCard.innerHTML = `
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2">
                            <div class="flex flex-wrap items-center gap-2">
                                ${voucherCodeHtml}
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    ${promoTypeLabel} (${discountLabel})
                                </span>
                                <span id="badge-status-${km.id}" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    🟡 Chờ Admin duyệt
                                </span>
                            </div>

                            <div class="flex items-center gap-2" id="owner-actions-${km.id}">
                                <span class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-lg font-medium italic flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">hourglass_top</span>
                                    Đang chờ duyệt
                                </span>
                                <button type="button" onclick="deleteOwnerPromotion('${km.id}', '${(km.ma_voucher || km.tieu_de || '').replace(/'/g, "\\'")}')" title="Xóa voucher này"
                                        class="px-2.5 py-1 text-xs font-bold rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 flex items-center gap-1 transition cursor-pointer shadow-2xs">
                                    <span class="material-symbols-outlined text-[15px]">delete</span>
                                    <span>Xóa</span>
                                </button>
                            </div>
                        </div>

                        <div id="km-reject-reason-wrap-${km.id}"></div>

                        <h5 class="font-bold text-sm text-gray-900">${safeTitle}</h5>
                        ${safeSlogan}

                        <div class="mt-2 text-xs text-gray-500 flex flex-wrap items-center gap-x-4 gap-y-1">
                            <span>🍽️ <strong>Áp dụng:</strong> ${safeDish}</span>
                            ${datesStr}
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-xs">
                            <button type="button" onclick="togglePromoContent('${km.id}')" class="font-bold text-purple-600 hover:text-purple-800 flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-[15px]">read_more</span>
                                <span id="label-toggle-${km.id}">Xem nội dung bài viết</span>
                            </button>
                            <button type="button" onclick="copyExistingPromoPost('${km.id}')" class="font-semibold text-gray-500 hover:text-gray-800 flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-[15px]">content_copy</span>
                                Sao chép bài viết
                            </button>
                        </div>

                        <div id="promo-full-content-${km.id}" class="hidden mt-3 p-3 bg-gray-50 rounded-xl text-xs text-gray-700 leading-relaxed border border-gray-100 whitespace-pre-line font-sans">
                            ${(km.noi_dung || '')}
                            ${km.the_le ? `<div class="mt-2 pt-2 border-t border-gray-200 text-gray-500 font-sans"><strong>Thể lệ:</strong><br>${km.the_le}</div>` : ''}
                        </div>
                    `;

                    if (list) {
                        list.prepend(newCard);
                    }

                    // 2. Tăng số lượng tổng voucher
                    const countElem = document.getElementById('promo-total-count');
                    if (countElem) {
                        const cur = parseInt(countElem.innerText) || 0;
                        countElem.innerText = cur + 1;
                    }

                    // 3. Đưa vào danh sách theo dõi trạng thái realtime
                    if (typeof lastVoucherStatuses !== 'undefined') {
                        lastVoucherStatuses[km.id] = 'cho_duyet';
                    }

                    // 4. Ẩn khung kết quả và cuộn mượt tới voucher vừa tạo
                    const resBox = document.getElementById('owner-ai-result-box');
                    if (resBox) resBox.classList.add('hidden');
                    newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Không thể lưu ưu đãi');
                showNotify(errorMsg, 'error', 'Lỗi lưu ưu đãi');
            }
        } catch (err) {
            showNotify('Lỗi kết nối máy chủ. Vui lòng thử lại sau.', 'error', 'Lỗi kết nối');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-[16px]">send</span> Gửi Phê Duyệt Voucher Lên Admin';
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
                showNotify('Không thể thay đổi trạng thái lúc này.', 'error', 'Lỗi thao tác');
            }
        } catch (err) {
            showNotify('Lỗi kết nối máy chủ.', 'error', 'Lỗi kết nối');
        }
    }

    function clearOwnerGeneratedPromo() {
        openOwnerConfirmModal({
            title: 'Hủy Bản Nháp Voucher',
            message: 'Bạn có chắc chắn muốn xóa bản nháp voucher vừa tạo này không?',
            icon: 'delete_sweep',
            iconClass: 'bg-amber-100 text-amber-600',
            confirmText: 'Xóa bản nháp',
            confirmIcon: 'delete',
            confirmClass: 'owner-confirm-btn-danger',
            onConfirm: () => {
                document.getElementById('owner-res-tieu-de').value = '';
                document.getElementById('owner-res-voucher').value = '';
                document.getElementById('owner-res-slogan').value = '';
                document.getElementById('owner-res-noi-dung').value = '';
                document.getElementById('owner-res-the-le').value = '';
                document.getElementById('owner-res-hashtags').innerText = '';
                document.getElementById('owner-ai-result-box')?.classList.add('hidden');
            }
        });
    }

    async function deleteOwnerPromotion(id, title = '') {
        openOwnerConfirmModal({
            title: 'Xác Nhận Xóa Voucher',
            message: `Bạn có chắc chắn muốn xóa vĩnh viễn voucher "<strong>${(title || 'này').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</strong>" khỏi quán không?`,
            icon: 'delete',
            iconClass: 'bg-red-100 text-red-600',
            confirmText: 'Xác Nhận Xóa',
            confirmIcon: 'delete',
            confirmClass: 'owner-confirm-btn-danger',
            onConfirm: async () => {
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
                        if (window.showToast) {
                            window.showToast({
                                title: 'Đã xóa voucher',
                                message: 'Đã xóa ưu đãi khỏi quán thành công!',
                                type: 'success'
                            });
                        }
                    } else {
                        if (window.showToast) {
                            window.showToast({
                                title: 'Không thể xóa',
                                message: data.message || 'Không thể xóa ưu đãi.',
                                type: 'error'
                            });
                        }
                    }
                } catch (err) {
                    if (window.showToast) {
                        window.showToast({
                            title: 'Lỗi kết nối',
                            message: 'Không thể kết nối máy chủ để xóa voucher.',
                            type: 'error'
                        });
                    }
                }
            }
        });
    }

    function copyOwnerGeneratedVoucher() {
        const val = document.getElementById('owner-res-voucher')?.value;
        if (!val) return;
        navigator.clipboard.writeText(val).then(() => {
            if (window.showToast) {
                window.showToast({
                    title: 'Đã sao chép',
                    message: 'Mã voucher: ' + val,
                    type: 'success'
                });
            }
        });
    }

    function copyOwnerGeneratedPost() {
        const tieuDe = document.getElementById('owner-res-tieu-de')?.value || '';
        const noiDung = document.getElementById('owner-res-noi-dung')?.value || '';
        const theLe = document.getElementById('owner-res-the-le')?.value || '';
        const hashtags = document.getElementById('owner-res-hashtags')?.innerText || '';

        const full = tieuDe + "\n\n" + noiDung + "\n\n📋 THỂ LỆ & ĐIỀU KIỆN:\n" + theLe + "\n\n" + hashtags;
        navigator.clipboard.writeText(full).then(() => {
            if (window.showToast) {
                window.showToast({
                    title: 'Đã sao chép bài đăng',
                    message: 'Đã sao chép toàn bộ bài đăng quảng bá mạng xã hội!',
                    type: 'success'
                });
            }
        });
    }

    function copyExistingPromoPost(id) {
        const box = document.getElementById('promo-full-content-' + id);
        if (!box) return;
        navigator.clipboard.writeText(box.innerText.trim()).then(() => {
            if (window.showToast) {
                window.showToast({
                    title: 'Đã sao chép',
                    message: 'Đã sao chép nội dung bài viết ưu đãi!',
                    type: 'success'
                });
            }
        });
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

    /* =========================================================
       ĐỒNG BỘ REALTIME TRẠNG THÁI VOUCHER KHI ADMIN DUYỆT / TỪ CHỐI
    ========================================================= */
    const ownerVenueId = '{{ $quan->id }}';

    function handleVoucherRealtimeUpdate(payload) {
        if (!payload || !payload.voucher_id) return;
        if (payload.quan_id && payload.quan_id !== ownerVenueId) return;

        const id = payload.voucher_id;
        const card = document.getElementById('km-card-' + id);
        const badge = document.getElementById('badge-status-' + id);
        const actions = document.getElementById('owner-actions-' + id);
        const rejectWrap = document.getElementById('km-reject-reason-wrap-' + id);

        if (!card && !badge) {
            return;
        }

        if (payload.status === 'da_duyet' || payload.action === 'voucher_approved') {
            // 1. Cập nhật Badge Trạng Thái
            if (badge) {
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 animate-pulse';
                badge.innerHTML = '🟢 Đã duyệt (Đang hoạt động)';
                badge.title = '';
            }

            // 2. Chuyển nút từ "Đang chờ duyệt" / "Bị từ chối" sang nút Bật/Tắt
            if (actions) {
                const existingToggle = document.getElementById('btn-toggle-' + id);
                if (!existingToggle) {
                    const statusSpan = actions.querySelector('span.italic');
                    if (statusSpan) {
                        statusSpan.outerHTML = `
                            <button type="button" onclick="toggleOwnerPromotion('${id}')"
                                    id="btn-toggle-${id}"
                                    title="Bấm để tạm dừng"
                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg transition bg-emerald-100 text-emerald-800">
                                Bật
                            </button>
                        `;
                    }
                }
            }

            // 3. Xóa hộp lý do từ chối (nếu có trước đó)
            if (rejectWrap) {
                rejectWrap.innerHTML = '';
            }

            // 4. Hiệu ứng viền xanh nhấp nháy cho card
            if (card) {
                card.classList.remove('ring-2', 'ring-rose-400', 'bg-rose-50/20');
                card.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50/20');
                setTimeout(() => {
                    card.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/20');
                }, 4000);
            }

            // 5. Toast thông báo realtime cho chủ quán
            if (window.showToast) {
                window.showToast({
                    title: '🎉 Voucher Đã Được Phê Duyệt!',
                    message: `Ban quản trị đã phê duyệt voucher "${payload.ma_voucher || payload.tieu_de || 'của quán'}". Ưu đãi đã kích hoạt công khai!`,
                    type: 'success'
                });
            }
        } else if (payload.status === 'tu_choi' || payload.action === 'voucher_rejected') {
            // 1. Cập nhật Badge Trạng Thái
            if (badge) {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1';
                badge.title = payload.ly_do || 'Chưa đạt tiêu chuẩn';
                badge.innerHTML = `
                    <span class="material-symbols-outlined text-[13px]">block</span>
                    🔴 Bị từ chối
                `;
            }

            // 2. Chuyển nút sang "Bị từ chối"
            if (actions) {
                const existingToggle = document.getElementById('btn-toggle-' + id);
                const rejectReason = payload.ly_do || '';
                if (existingToggle) {
                    existingToggle.outerHTML = `
                        <span class="text-[11px] text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-lg font-medium italic flex items-center gap-1" title="${rejectReason}">
                            <span class="material-symbols-outlined text-[13px]">info</span>
                            Bị từ chối
                        </span>
                    `;
                }
            }

            // 3. Thêm hộp thông báo lý do từ chối
            if (rejectWrap) {
                const rejectReason = payload.ly_do || 'Chưa đạt tiêu chuẩn hệ thống';
                rejectWrap.innerHTML = `
                    <div class="mb-2 p-2 rounded-xl bg-rose-50 text-rose-700 text-xs border border-rose-100 flex items-center gap-1.5 animate-fadeIn">
                        <span class="material-symbols-outlined text-[14px] shrink-0 text-rose-600">info</span>
                        <span><strong>Lý do từ chối:</strong> ${rejectReason}</span>
                    </div>
                `;
            }

            // 4. Hiệu ứng viền đỏ
            if (card) {
                card.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/20');
                card.classList.add('ring-2', 'ring-rose-400', 'bg-rose-50/20');
                setTimeout(() => {
                    card.classList.remove('ring-2', 'ring-rose-400', 'bg-rose-50/20');
                }, 4000);
            }

            // 5. Toast thông báo realtime cho chủ quán
            if (window.showToast) {
                window.showToast({
                    title: '⚠️ Voucher Bị Từ Chối Duyệt',
                    message: `Voucher "${payload.ma_voucher || payload.tieu_de || ''}" bị từ chối. Lý do: ${payload.ly_do || 'Chưa đạt tiêu chuẩn'}`,
                    type: 'error'
                });
            }
        }
    }

    // 1. Lắng nghe qua BroadcastChannel (tức thì 0ms giữa các tab cùng trình duyệt)
    if (typeof BroadcastChannel !== 'undefined') {
        try {
            const voucherChannel = new BroadcastChannel('quanmoi_voucher_sync');
            voucherChannel.onmessage = function(e) {
                if (e.data) handleVoucherRealtimeUpdate(e.data);
            };
        } catch(e) {}
    }

    // 2. Lắng nghe qua Storage Event (dự phòng cho cross-tab trong trình duyệt)
    window.addEventListener('storage', function(e) {
        if (e.key === 'quanmoi_voucher_event' && e.newValue) {
            try {
                const data = JSON.parse(e.newValue);
                handleVoucherRealtimeUpdate(data);
            } catch(err) {}
        }
    });

    // 3. Polling ngầm mỗi 3.5 giây (hỗ trợ trường hợp khác trình duyệt/thiết bị khác)
    let lastVoucherStatuses = {};
    document.querySelectorAll('[id^="km-card-"]').forEach(card => {
        const id = card.id.replace('km-card-', '');
        const badge = document.getElementById('badge-status-' + id);
        if (badge) {
            const txt = badge.innerText || '';
            lastVoucherStatuses[id] = txt.includes('Đã duyệt') ? 'da_duyet' : (txt.includes('từ chối') ? 'tu_choi' : 'cho_duyet');
        }
    });

    async function checkOwnerPromotionsRealtime() {
        if (document.hidden) return;
        try {
            const res = await fetch('{{ route("chu-quan.quan.khuyen-mai.realtime-status", $quan->slug) }}', {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const json = await res.json();
            if (json.success && Array.isArray(json.data)) {
                json.data.forEach(item => {
                    const prev = lastVoucherStatuses[item.id];
                    if (prev && prev !== item.trang_thai_duyet) {
                        lastVoucherStatuses[item.id] = item.trang_thai_duyet;
                        handleVoucherRealtimeUpdate({
                            voucher_id: item.id,
                            quan_id: ownerVenueId,
                            status: item.trang_thai_duyet,
                            ly_do: item.ly_do_tu_choi,
                            ma_voucher: item.ma_voucher,
                            tieu_de: item.tieu_de,
                            trang_thai: item.trang_thai
                        });
                    } else if (!prev) {
                        lastVoucherStatuses[item.id] = item.trang_thai_duyet;
                    }
                });
            }
        } catch(err) {}
    }

    setInterval(checkOwnerPromotionsRealtime, 3500);

    /* =========================================================
       XỬ LÝ VIDEO TIKTOK CỦA CHỦ QUÁN
    ========================================================= */
    function openCreateVideoModal() {
        const modal = document.getElementById('modal-create-video');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeCreateVideoModal() {
        const modal = document.getElementById('modal-create-video');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            resetCreateVideoForm();
        }
    }

    function resetCreateVideoForm() {
        const form = document.getElementById('owner-video-form');
        if (form) form.reset();
        clearOwnerTikTokPreview();
        clearOwnerVideoFile();
        clearOwnerThumbnail();
        const btn = document.getElementById('btn-submit-owner-video');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-base">send</span><span>Gửi video phê duyệt</span>`;
        }
    }

    function clearOwnerTikTokPreview() {
        const input = document.getElementById('owner_video_input');
        const previewBox = document.getElementById('owner-tiktok-preview-box');
        if (input) input.value = '';
        if (previewBox) {
            previewBox.classList.add('hidden');
            const thumb = document.getElementById('owner-preview-thumb');
            if (thumb) thumb.src = '';
            const title = document.getElementById('owner-preview-title');
            if (title) title.textContent = '';
            const author = document.getElementById('owner-preview-author');
            if (author) author.textContent = '';
            const views = document.getElementById('owner-preview-views');
            if (views) views.textContent = '';
        }
    }

    function handleOwnerVideoFileChange(input) {
        const fileBox = document.getElementById('owner-file-selected-box');
        const fileNameSpan = document.getElementById('owner-file-selected-name');
        const titleInput = document.getElementById('owner_video_title');

        if (input.files && input.files.length > 0) {
            const file = input.files[0];
            if (file.size > 200 * 1024 * 1024) {
                if (window.showToast) {
                    window.showToast({
                        title: 'File quá lớn',
                        message: `File video "${file.name}" (${(file.size / (1024 * 1024)).toFixed(1)}MB) vượt quá giới hạn 200MB. Vui lòng chọn file dung lượng nhỏ hơn hoặc dán link TikTok.`,
                        type: 'error'
                    });
                }
                input.value = '';
                if (fileBox) fileBox.classList.add('hidden');
                return;
            }

            // Tự động xóa TikTok preview để tránh nhầm lẫn giữa 2 nguồn
            clearOwnerTikTokPreview();

            if (fileBox && fileNameSpan) {
                fileNameSpan.textContent = `${file.name} (${(file.size / (1024 * 1024)).toFixed(1)}MB)`;
                fileBox.classList.remove('hidden');
            }

            // Tự động điền tiêu đề từ tên file nếu tiêu đề đang trống
            if (titleInput && !titleInput.value.trim()) {
                const nameWithoutExt = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
                titleInput.value = nameWithoutExt;
            }
        } else {
            if (fileBox) fileBox.classList.add('hidden');
        }
    }

    function clearOwnerVideoFile() {
        const fileInput = document.getElementById('owner_video_file');
        const fileBox = document.getElementById('owner-file-selected-box');
        if (fileInput) fileInput.value = '';
        if (fileBox) fileBox.classList.add('hidden');
    }

    function handleOwnerThumbnailChange(input) {
        const preview = document.getElementById('owner-thumb-preview');
        const previewBox = document.getElementById('owner-thumb-preview-box');
        if (input.files && input.files.length > 0) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                if (preview) preview.src = e.target.result;
                if (previewBox) previewBox.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            clearOwnerThumbnail();
        }
    }

    function clearOwnerThumbnail() {
        const fileInput = document.getElementById('owner_thumbnail_file');
        const previewBox = document.getElementById('owner-thumb-preview-box');
        const preview = document.getElementById('owner-thumb-preview');
        if (fileInput) fileInput.value = '';
        if (previewBox) previewBox.classList.add('hidden');
        if (preview) preview.src = '';
    }

    // Lắng nghe khi xóa sạch ô nhập TikTok thì tự ẩn box preview
    document.addEventListener('DOMContentLoaded', function() {
        const tiktokInput = document.getElementById('owner_video_input');
        if (tiktokInput) {
            tiktokInput.addEventListener('input', function() {
                if (!this.value.trim()) {
                    const previewBox = document.getElementById('owner-tiktok-preview-box');
                    if (previewBox) previewBox.classList.add('hidden');
                }
            });
        }
    });

    async function fetchOwnerTikTokInfo() {
        const input = document.getElementById('owner_video_input');
        const btn = document.getElementById('btn-fetch-tiktok-info');
        const previewBox = document.getElementById('owner-tiktok-preview-box');
        const titleInput = document.getElementById('owner_video_title');

        if (!input || !input.value.trim()) {
            if (window.showToast) {
                window.showToast({
                    title: 'Chưa nhập liên kết',
                    message: 'Vui lòng dán đường dẫn link video TikTok trước khi lấy thông tin.',
                    type: 'warning'
                });
            }
            return;
        }

        // Tự động clear file video nếu trước đó đã chọn file
        clearOwnerVideoFile();

        btn.disabled = true;
        const oldBtnText = btn.innerHTML;
        btn.innerHTML = `<span class="animate-spin material-symbols-outlined text-base">sync</span> Đang tải...`;

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch("{{ route('chu-quan.quan.videos.fetch-info', $quan->slug) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ url: input.value.trim() })
            });

            const data = await res.json();
            if (data.success && data.data) {
                const info = data.data;
                if (info.title && titleInput && !titleInput.value) {
                    titleInput.value = info.title;
                }
                if (previewBox) {
                    previewBox.classList.remove('hidden');
                    document.getElementById('owner-preview-thumb').src = info.cover_url || 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400';
                    document.getElementById('owner-preview-title').textContent = info.title || 'Video Review TikTok';
                    document.getElementById('owner-preview-author').textContent = info.author_name || '@tiktok_creator';
                    document.getElementById('owner-preview-views').textContent = `${Number(info.views || 0).toLocaleString('vi-VN')} lượt xem • ${Number(info.likes || 0).toLocaleString('vi-VN')} thích`;
                }
                if (window.showToast) {
                    window.showToast({
                        title: 'Đã nhận diện video',
                        message: 'Đã tải thông tin video TikTok thành công!',
                        type: 'success'
                    });
                }
            } else {
                if (window.showToast) {
                    window.showToast({
                        title: 'Thông báo TikTok',
                        message: data.message || 'Không thể tự động lấy thông tin video. Bạn vẫn có thể gửi link này để Ban quản trị kiểm duyệt.',
                        type: 'info'
                    });
                }
            }
        } catch (err) {
            if (window.showToast) {
                window.showToast({
                    title: 'Lỗi kết nối',
                    message: 'Không thể kết nối máy chủ khi lấy thông tin video TikTok.',
                    type: 'error'
                });
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = oldBtnText;
        }
    }

    async function handleOwnerVideoFormSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const fileInput = document.getElementById('owner_video_file');
        const tiktokInput = document.getElementById('owner_video_input');

        const hasTiktok = tiktokInput && tiktokInput.value.trim().length > 0;
        const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

        if (!hasTiktok && !hasFile) {
            if (window.showAlertDialog) {
                window.showAlertDialog({
                    title: 'Thiếu thông tin video',
                    message: 'Vui lòng dán link video TikTok hoặc chọn file video MP4 từ máy tính.',
                    type: 'warning'
                });
            } else if (window.showToast) {
                window.showToast({
                    title: 'Thiếu thông tin video',
                    message: 'Vui lòng dán link video TikTok hoặc chọn file video MP4 từ máy tính.',
                    type: 'warning'
                });
            }
            return;
        }

        if (hasFile) {
            const file = fileInput.files[0];
            if (file.size > 200 * 1024 * 1024) {
                const sizeMb = (file.size / (1024 * 1024)).toFixed(1);
                if (window.showAlertDialog) {
                    window.showAlertDialog({
                        title: 'File quá lớn',
                        message: `File video "${file.name}" (${sizeMb}MB) vượt quá giới hạn 200MB. Vui lòng chọn file dung lượng nhỏ hơn.`,
                        type: 'error'
                    });
                } else if (window.showToast) {
                    window.showToast({
                        title: 'File quá lớn',
                        message: `File video "${file.name}" vượt quá 200MB.`,
                        type: 'error'
                    });
                }
                return;
            }
        }

        const btn = document.getElementById('btn-submit-owner-video');
        const oldBtnHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span class="animate-spin material-symbols-outlined text-base">sync</span><span>Đang tải lên và gửi video...</span>`;
        }

        try {
            const formData = new FormData(form);
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await res.json().catch(() => ({}));

            if (res.ok && data.success) {
                closeCreateVideoModal();
                resetCreateVideoForm();

                // Hiển thị hộp thoại màn hình thông báo đã gửi video thành công
                if (window.showAlertDialog) {
                    window.showAlertDialog({
                        title: 'Gửi video thành công!',
                        message: data.message || 'Video review của bạn đã được gửi thành công đến Ban Quản Trị và đang ở trạng thái 🟡 "Chờ duyệt". Video sẽ được hiển thị công khai ngay sau khi Admin duyệt!',
                        type: 'success',
                        onOk: () => {
                            syncOwnerVideosGrid();
                        }
                    });
                } else if (window.showToast) {
                    window.showToast({
                        title: 'Gửi video thành công!',
                        message: data.message || 'Video đang ở trạng thái 🟡 "Chờ duyệt".',
                        type: 'success',
                        duration: 6000
                    });
                }

                // Cập nhật lại giao diện danh sách video của quán
                syncOwnerVideosGrid(data.data?.id);
            } else {
                const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Không thể gửi video review. Vui lòng kiểm tra lại đường dẫn TikTok hoặc file tải lên.');
                if (window.showAlertDialog) {
                    window.showAlertDialog({
                        title: 'Không thể gửi video',
                        message: errorMsg,
                        type: 'error'
                    });
                } else if (window.showToast) {
                    window.showToast({
                        title: 'Lỗi gửi video',
                        message: errorMsg,
                        type: 'error',
                        duration: 6000
                    });
                }
            }
        } catch (err) {
            console.error('Submit video error:', err);
            if (window.showAlertDialog) {
                window.showAlertDialog({
                    title: 'Lỗi kết nối',
                    message: 'Không thể kết nối máy chủ để tải video lên. Vui lòng thử lại sau giây lát.',
                    type: 'error'
                });
            } else if (window.showToast) {
                window.showToast({
                    title: 'Lỗi kết nối',
                    message: 'Không thể tải video lên lúc này.',
                    type: 'error'
                });
            }
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = oldBtnHtml || `<span class="material-symbols-outlined text-base">send</span><span>Gửi video phê duyệt</span>`;
            }
        }
    }

    // Enterprise Confirmation Modal Engine cho Chủ Quán
    let ownerConfirmCallback = null;

    function openOwnerConfirmModal(options) {
        const modal = document.getElementById('owner-confirm-modal');
        const box = document.getElementById('owner-confirm-box');
        const titleEl = document.getElementById('owner-confirm-title');
        const msgEl = document.getElementById('owner-confirm-message');
        const iconEl = document.getElementById('owner-confirm-icon');
        const iconWrap = document.getElementById('owner-confirm-icon-wrap');
        const submitBtn = document.getElementById('owner-confirm-submit-btn');
        const btnText = document.getElementById('owner-confirm-btn-text');
        const btnIcon = document.getElementById('owner-confirm-btn-icon');

        if (!modal) return;

        ownerConfirmCallback = options.onConfirm || null;
        titleEl.textContent = options.title || 'Xác nhận thao tác';
        msgEl.innerHTML = options.message || 'Bạn có chắc chắn muốn thực hiện hành động này?';
        iconEl.textContent = options.icon || 'help';
        btnText.textContent = options.confirmText || 'Xác nhận';
        btnIcon.textContent = options.confirmIcon || 'check';

        const cClass = (options.confirmClass || '').toLowerCase();
        const isDanger = cClass.includes('danger') || cClass.includes('red') || cClass.includes('delete') || (options.confirmIcon === 'delete');

        if (isDanger) {
            iconWrap.className = 'w-14 h-14 rounded-2xl mx-auto flex items-center justify-center ' + (options.iconClass || '');
            iconWrap.style.backgroundColor = '#fee2e2';
            iconWrap.style.color = '#dc2626';

            submitBtn.className = 'owner-confirm-btn-danger flex-1 py-2.5 px-4 rounded-xl font-bold text-xs shadow-md shadow-red-500/25 transition flex items-center justify-center gap-1.5 cursor-pointer ' + (options.confirmClass || '');
            submitBtn.style.backgroundColor = '#dc2626';
            submitBtn.style.color = '#ffffff';
        } else {
            iconWrap.className = 'w-14 h-14 rounded-2xl mx-auto flex items-center justify-center ' + (options.iconClass || '');
            iconWrap.style.backgroundColor = options.iconBgColor || '#fef3c7';
            iconWrap.style.color = options.iconColor || '#d97706';

            submitBtn.className = 'flex-1 py-2.5 px-4 rounded-xl font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5 cursor-pointer ' + (options.confirmClass || '');
            submitBtn.style.backgroundColor = options.bgColor || '#059669';
            submitBtn.style.color = options.textColor || '#ffffff';
        }

        submitBtn.style.display = 'inline-flex';

        modal.classList.remove('hidden');
        setTimeout(() => {
            box.classList.remove('scale-95');
            box.classList.add('scale-100');
        }, 10);
    }

    function closeOwnerConfirmModal() {
        const modal = document.getElementById('owner-confirm-modal');
        const box = document.getElementById('owner-confirm-box');
        ownerConfirmCallback = null;
        if (!modal) return;

        box.classList.remove('scale-100');
        box.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 150);
    }

    let isOwnerConfirmSubmitting = false;

    function handleOwnerConfirmSubmit() {
        if (isOwnerConfirmSubmitting) return;
        if (typeof ownerConfirmCallback === 'function') {
            isOwnerConfirmSubmitting = true;
            const cb = ownerConfirmCallback;
            ownerConfirmCallback = null;
            closeOwnerConfirmModal();
            try {
                cb();
            } finally {
                setTimeout(() => {
                    isOwnerConfirmSubmitting = false;
                }, 600);
            }
        } else {
            closeOwnerConfirmModal();
        }
    }

    const ownerDeletingVideos = new Set();
    async function deleteOwnerVideo(id, title) {
        if (ownerDeletingVideos.has(id)) return;
        openOwnerConfirmModal({
            title: 'Xác Nhận Xóa Video',
            message: `Bạn có chắc chắn muốn xóa video "<strong>${(title || 'này').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</strong>" khỏi quán không?<br><span class="text-xs text-red-500 font-medium">Video sẽ bị xóa vĩnh viễn và không còn xuất hiện trên quán của bạn.</span>`,
            icon: 'delete',
            iconClass: 'bg-red-100 text-red-600',
            confirmText: 'Xác Nhận Xóa',
            confirmIcon: 'delete',
            confirmClass: 'owner-confirm-btn-danger',
            onConfirm: async () => {
                if (ownerDeletingVideos.has(id)) return;
                ownerDeletingVideos.add(id);
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch(`/chu-quan/quan/{{ $quan->slug }}/videos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();
                    if (data.success) {
                        const card = document.getElementById(`owner-video-card-${id}`);
                        if (card) {
                            card.style.transition = 'all 0.3s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                card.remove();
                                const container = document.getElementById('owner-videos-grid');
                                if (container && container.querySelectorAll('[id^="owner-video-card-"]').length === 0) {
                                    window.location.reload();
                                }
                            }, 300);
                        }
                        if (window.showToast) {
                            window.showToast({
                                title: 'Đã xóa video',
                                message: 'Video review đã được gỡ bỏ khỏi quán thành công.',
                                type: 'success'
                            });
                        }
                    } else {
                        if (window.showToast) {
                            window.showToast({
                                title: 'Không thể xóa',
                                message: data.message || 'Có lỗi xảy ra khi xóa video.',
                                type: 'error'
                            });
                        }
                    }
                } catch (e) {
                    if (window.showToast) {
                        window.showToast({
                            title: 'Lỗi kết nối',
                            message: 'Không thể kết nối máy chủ để xóa video.',
                            type: 'error'
                        });
                    }
                } finally {
                    setTimeout(() => {
                        ownerDeletingVideos.delete(id);
                    }, 1200);
                }
            }
        });
    }

    // ========================================================
    // TỰ ĐỘNG ĐỒNG BỘ REALTIME KHI ADMIN DUYỆT HOẶC TỪ CHỐI VIDEO
    // ========================================================
    let isSyncingOwnerVideos = false;

    function syncOwnerVideosGrid(highlightVideoId = null) {
        if (isSyncingOwnerVideos) return;
        isSyncingOwnerVideos = true;

        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newContainer = doc.getElementById('owner-videos-container');
            const curContainer = document.getElementById('owner-videos-container');
            if (newContainer && curContainer) {
                curContainer.innerHTML = newContainer.innerHTML;
            }

            const newBadge = doc.getElementById('owner-video-count-badge');
            const curBadge = document.getElementById('owner-video-count-badge');
            if (newBadge && curBadge) {
                curBadge.innerHTML = newBadge.innerHTML;
            }

            if (highlightVideoId) {
                const card = document.getElementById(`owner-video-card-${highlightVideoId}`);
                if (card) {
                    card.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50/30');
                    setTimeout(() => {
                        card.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/30');
                    }, 4000);
                }
            }
        })
        .catch(err => console.warn('Lỗi đồng bộ video:', err))
        .finally(() => {
            isSyncingOwnerVideos = false;
        });
    }

    // Lắng nghe sự kiện Realtime khi Admin duyệt hoặc từ chối video
    window.addEventListener('quanmoi:video-status-updated', function(e) {
        if (!e.detail) return;
        const detail = e.detail;

        // 1. Nếu video được Admin phê duyệt -> Chuyển ngay badge thành "Đã duyệt" không cần load trang
        if (detail.action === 'video_approved' || detail.status === 'approved') {
            const videoId = detail.video_db_id;
            let updated = false;

            if (videoId) {
                const badgeWrap = document.getElementById(`owner-video-badge-wrap-${videoId}`);
                if (badgeWrap) {
                    badgeWrap.innerHTML = `
                        <span class="inline-flex items-center gap-1 bg-emerald-600/95 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                            <span class="material-symbols-outlined text-xs">check_circle</span>
                            Đã duyệt
                        </span>
                    `;
                    const card = document.getElementById(`owner-video-card-${videoId}`);
                    if (card) {
                        card.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50/30');
                        setTimeout(() => {
                            card.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/30');
                        }, 4000);
                    }
                    updated = true;
                }
            }

            // Fallback nếu không có ID: tìm thẻ theo tiêu đề video
            if (!updated && detail.video_title) {
                const cards = document.querySelectorAll('[id^="owner-video-card-"]');
                cards.forEach(card => {
                    const titleText = card.querySelector('h5')?.textContent?.trim() || '';
                    if (titleText && (titleText === detail.video_title.trim() || titleText.includes(detail.video_title.trim()) || detail.video_title.trim().includes(titleText))) {
                        const badgeWrap = card.querySelector('[id^="owner-video-badge-wrap-"]') || card.querySelector('.absolute.top-2\\.5.left-2\\.5');
                        if (badgeWrap) {
                            badgeWrap.innerHTML = `
                                <span class="inline-flex items-center gap-1 bg-emerald-600/95 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                                    <span class="material-symbols-outlined text-xs">check_circle</span>
                                    Đã duyệt
                                </span>
                            `;
                            card.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50/30');
                            setTimeout(() => {
                                card.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/30');
                            }, 4000);
                        }
                    }
                });
            }

            // Đồng bộ ngầm toàn bộ lại sau 800ms
            setTimeout(() => syncOwnerVideosGrid(videoId), 800);
        }

        // 2. Nếu video của quán bị Admin từ chối -> Gỡ bỏ card khỏi giao diện ngay lập tức
        if (detail.action === 'video_rejected' || detail.status === 'rejected') {
            const videoId = detail.video_db_id;
            let targetCard = null;

            if (videoId) {
                targetCard = document.getElementById(`owner-video-card-${videoId}`);
            }

            if (!targetCard && detail.video_title) {
                const cards = document.querySelectorAll('[id^="owner-video-card-"]');
                cards.forEach(card => {
                    const titleText = card.querySelector('h5')?.textContent?.trim() || '';
                    if (titleText && (titleText === detail.video_title.trim() || titleText.includes(detail.video_title.trim()) || detail.video_title.trim().includes(titleText))) {
                        targetCard = card;
                    }
                });
            }

            if (targetCard) {
                targetCard.style.transition = 'all 0.4s ease';
                targetCard.style.opacity = '0';
                targetCard.style.transform = 'scale(0.85)';
                setTimeout(() => {
                    targetCard.remove();
                    const container = document.getElementById('owner-videos-grid');
                    if (container && container.querySelectorAll('[id^="owner-video-card-"]').length === 0) {
                        syncOwnerVideosGrid();
                    }
                }, 400);
            }
        }
    });

    // Tự động kiểm tra đồng bộ ngầm trạng thái video mỗi 4 giây khi tab đang mở
    setInterval(function() {
        if (!document.hidden && document.getElementById('owner-videos-container')) {
            syncOwnerVideosGrid();
        }
    }, 4000);

    function openPreviewVideoModal(videoUrl, videoId, title) {
        const modal = document.getElementById('modal-owner-preview-player');
        const container = document.getElementById('owner-player-container');
        if (!modal || !container) return;

        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.classList.add('overflow-hidden');
        container.innerHTML = '';

        const playerV1Url = `https://www.tiktok.com/player/v1/${videoId}?music_info=1&description=1`;
        const watchUrl = `https://www.tiktok.com/@creator/video/${videoId}`;

        if (videoUrl && (videoUrl.endsWith('.mp4') || videoUrl.includes('/storage/videos/') || videoUrl.includes('mime_type=video_mp4'))) {
            const video = document.createElement('video');
            video.src = videoUrl;
            video.controls = true;
            video.autoplay = true;
            video.playsInline = true;
            video.className = 'w-full h-full object-contain';
            video.onerror = () => {
                container.innerHTML = `
                    <iframe src="${playerV1Url}"
                            style="width:100%;height:100%;border:none;background:#000;"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; fullscreen; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen></iframe>
                    <div style="position:absolute;bottom:12px;left:0;right:0;text-align:center;pointer-events:auto;z-index:20;">
                        <a href="${watchUrl}" target="_blank" rel="noopener noreferrer"
                           style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:rgba(0,0,0,0.8);color:#fbbf24;font-size:11px;font-weight:bold;border-radius:9999px;border:1px solid rgba(245,158,11,0.4);text-decoration:none;">
                            <span>Mở xem trên TikTok</span>
                            <span class="material-symbols-outlined" style="font-size:14px;">open_in_new</span>
                        </a>
                    </div>
                `;
            };
            container.appendChild(video);
            video.play().catch(e => console.log('Autoplay muted'));
        } else {
            container.innerHTML = `
                <iframe src="${playerV1Url}"
                        style="width:100%;height:100%;border:none;background:#000;"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; fullscreen; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen></iframe>
                <div style="position:absolute;bottom:12px;left:0;right:0;text-align:center;pointer-events:auto;z-index:20;">
                    <a href="${watchUrl}" target="_blank" rel="noopener noreferrer"
                       style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:rgba(0,0,0,0.8);color:#fbbf24;font-size:11px;font-weight:bold;border-radius:9999px;border:1px solid rgba(245,158,11,0.4);text-decoration:none;">
                        <span>Mở xem trên TikTok</span>
                        <span class="material-symbols-outlined" style="font-size:14px;">open_in_new</span>
                    </a>
                </div>
            `;
        }
    }

    function closePreviewVideoModal() {
        const modal = document.getElementById('modal-owner-preview-player');
        const container = document.getElementById('owner-player-container');
        if (container) container.innerHTML = '';
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePreviewVideoModal();
        }
    });

    /* =========================================================
       KIRA AI REVIEW SUMMARY & REPLY (DÀNH CHO CHỦ QUÁN)
    ========================================================= */
    async function refreshAiReviewSummary(slug, isSilent = false) {
        const btn = document.getElementById('btn-refresh-ai-summary') || document.getElementById('btn-trigger-ai-summary');
        const icon = document.getElementById('icon-refresh-ai');
        if (!isSilent) {
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
            }
            if (icon) {
                icon.classList.add('animate-spin');
            }
        }

        try {
            const res = await fetch(`/chu-quan/quan/${slug}/danh-gia/ai-summary`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ refresh: true })
            });

            const data = await res.json();
            if (data.success && data.data && data.data.eligible) {
                renderAiReviewSummaryCard(data.data, slug);
                if (!isSilent) {
                    if (typeof showToast === 'function') {
                        showToast('Đã phân tích lại đánh giá của khách bằng AI thành công!', 'success');
                    } else if (window.showToast) {
                        window.showToast({
                            title: 'Thành công',
                            message: 'Đã phân tích lại đánh giá của khách bằng AI.',
                            type: 'success'
                        });
                    }
                }
            } else if (!isSilent && data.data && !data.data.eligible) {
                showNotify(data.data.message || 'Quán chưa có đủ đánh giá để AI phân tích.', 'info', 'AI Tóm Tắt Đánh Giá');
            }
        } catch (err) {
            console.error('AI summary error:', err);
            if (!isSilent) {
                showNotify('Không thể kết nối đến máy chủ AI lúc này. Vui lòng thử lại sau giây lát.', 'error', 'Lỗi kết nối AI');
            }
        } finally {
            if (!isSilent) {
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
                if (icon) {
                    icon.classList.remove('animate-spin');
                }
            }
        }
    }

    function renderAiReviewSummaryCard(data, slug) {
        const card = document.getElementById('ai-review-summary-card');
        if (!card) return;

        const tiLe = data.ti_le_hai_long || { tich_cuc: 80, trung_tinh: 15, can_cai_thien: 5 };
        const tichCuc = tiLe.tich_cuc || 0;
        const trungTinh = tiLe.trung_tinh || 0;
        const canCaiThien = tiLe.can_cai_thien || 0;

        const diemKhenHtml = (data.diem_khen || []).map(k => `
            <li class="flex items-start gap-1.5">
                <span class="text-emerald-600 font-bold mt-0.5">✓</span>
                <span>${k}</span>
            </li>
        `).join('');

        const diemCheHtml = (data.diem_can_khac_phuc || []).map(c => `
            <li class="flex items-start gap-1.5">
                <span class="text-amber-600 font-bold mt-0.5">⚠</span>
                <span>${c}</span>
            </li>
        `).join('');

        const topMonHtml = (data.top_mon_an || []).map(m => `
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-100">
                <span>🍲</span>
                <span>${m}</span>
            </span>
        `).join('');

        const khuyenNghiHtml = (data.khuyen_nghi_chuyen_gia || []).map((action, i) => `
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-purple-200 text-purple-800 text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">
                    ${i + 1}
                </span>
                <span class="font-medium">${action}</span>
            </li>
        `).join('');

        card.innerHTML = `
            <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-gradient-to-br from-purple-400/10 to-indigo-500/10 blur-2xl pointer-events-none"></div>
            <div id="ai-summary-content" class="space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-purple-100/80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center shadow-sm shrink-0">
                            <span class="material-symbols-outlined text-xl">smart_toy</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-extrabold text-sm sm:text-base text-gray-900">Kira AI • Báo cáo Phân tích Trải nghiệm Khách hàng</h4>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                    Nội bộ Chủ quán
                                </span>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1.5">
                                <span>Tổng hợp từ <strong>${data.review_count || ''}</strong> đánh giá</span>
                                <span>•</span>
                                <span>Cập nhật: <span id="ai-summary-time">${data.analyzed_at || 'Mới nhất'}</span></span>
                            </p>
                        </div>
                    </div>
                    <button type="button" onclick="refreshAiReviewSummary('${slug}')"
                            id="btn-refresh-ai-summary"
                            title="Quét và phân tích lại với các đánh giá mới nhất"
                            class="px-3.5 py-1.5 bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 text-xs font-bold rounded-xl shadow-2xs transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
                        <span class="material-symbols-outlined text-sm" id="icon-refresh-ai">sync</span>
                        <span>Phân tích lại</span>
                    </button>
                </div>

                <div class="p-3.5 rounded-2xl bg-white/80 border border-purple-100 shadow-2xs">
                    <p class="text-xs font-bold text-purple-900 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm text-purple-600">summarize</span>
                        Tổng kết vận hành:
                    </p>
                    <p id="ai-summary-tong-quan" class="text-xs sm:text-sm text-gray-700 leading-relaxed font-medium">
                        ${data.tong_quan || ''}
                    </p>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span class="text-gray-700 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm text-indigo-600">pie_chart</span>
                            Tỉ lệ cảm xúc khách hàng:
                        </span>
                        <div class="flex items-center gap-3 text-[11px]">
                            <span class="text-emerald-700 font-bold">● ${tichCuc}% Hài lòng</span>
                            <span class="text-amber-700 font-bold">● ${trungTinh}% Bình thường</span>
                            <span class="text-rose-700 font-bold">● ${canCaiThien}% Góp ý/Phàn nàn</span>
                        </div>
                    </div>
                    <div class="w-full h-3 rounded-full bg-gray-100 overflow-hidden flex shadow-inner">
                        <div style="width: ${tichCuc}%;" class="bg-emerald-500 h-full transition-all duration-500" title="${tichCuc}% Hài lòng"></div>
                        <div style="width: ${trungTinh}%;" class="bg-amber-400 h-full transition-all duration-500" title="${trungTinh}% Bình thường"></div>
                        <div style="width: ${canCaiThien}%;" class="bg-rose-500 h-full transition-all duration-500" title="${canCaiThien}% Cần cải thiện"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-2">
                        <h5 class="text-xs font-bold text-emerald-900 flex items-center gap-1.5 uppercase tracking-wide">
                            <span class="material-symbols-outlined text-sm text-emerald-600">thumb_up</span>
                            Điểm khách khen nhiều nhất:
                        </h5>
                        <ul id="ai-summary-diem-khen" class="space-y-1.5 text-xs text-gray-700">
                            ${diemKhenHtml}
                        </ul>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-100 space-y-2">
                        <h5 class="text-xs font-bold text-amber-900 flex items-center gap-1.5 uppercase tracking-wide">
                            <span class="material-symbols-outlined text-sm text-amber-600">warning</span>
                            Vấn đề khách góp ý / phàn nàn:
                        </h5>
                        <ul id="ai-summary-diem-che" class="space-y-1.5 text-xs text-gray-700">
                            ${diemCheHtml}
                        </ul>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                    <div class="p-4 rounded-2xl bg-white border border-gray-100 shadow-2xs space-y-2.5">
                        <h5 class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-orange-500">restaurant</span>
                            Món được khen nhiều (Nên đẩy mạnh):
                        </h5>
                        <div id="ai-summary-top-mon" class="flex flex-wrap gap-1.5">
                            ${topMonHtml || '<span class="text-xs text-gray-400">Chưa đủ dữ liệu tên món</span>'}
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-100 space-y-2.5">
                        <h5 class="text-xs font-bold text-purple-900 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-purple-600">lightbulb</span>
                            Đề xuất hành động từ Chuyên gia F&B AI:
                        </h5>
                        <ul id="ai-summary-khuyen-nghi" class="space-y-1.5 text-xs text-gray-700">
                            ${khuyenNghiHtml}
                        </ul>
                    </div>
                </div>
            </div>
        `;
    }

    /* =========================================================
       AI REVIEW REPLY SUGGESTIONS (ĐA PHONG CÁCH & TRÁNH TRÙNG LẶP)
    ========================================================= */
    window.reviewAiSeeds = window.reviewAiSeeds || {};

    window.applyQuickReply = function(reviewId, text, label) {
        const textarea = document.getElementById(`phan-hoi-${reviewId}`);
        if (!textarea) return;
        textarea.value = text;
        textarea.classList.add('ring-2', 'ring-amber-500', 'bg-amber-50/30');
        setTimeout(() => {
            textarea.classList.remove('ring-2', 'ring-amber-500', 'bg-amber-50/30');
        }, 1200);

        const hint = document.getElementById(`applied-hint-${reviewId}`);
        if (hint) {
            hint.querySelector('.hint-text').textContent = `Đã áp dụng mẫu: ${label}`;
            hint.classList.remove('hidden');
        }

        textarea.focus();
    };

    window.closeAiSuggestions = function(reviewId) {
        const box = document.getElementById(`ai-suggestions-box-${reviewId}`);
        if (box) {
            box.classList.add('hidden');
        }
    };

    window.filterSuggestionsTone = function(reviewId, tone, btn) {
        const grid = document.getElementById(`ai-suggestions-grid-${reviewId}`);
        const box = document.getElementById(`ai-suggestions-box-${reviewId}`);
        if (!grid || !box) return;

        // Cập nhật tab active
        box.querySelectorAll('.suggestion-tab-btn').forEach(b => {
            b.classList.remove('active', 'bg-purple-700', 'text-white');
            b.classList.add('bg-white', 'text-slate-700');
        });
        btn.classList.add('active', 'bg-purple-700', 'text-white');
        btn.classList.remove('bg-white', 'text-slate-700');

        // Lọc card
        grid.querySelectorAll('.ai-suggestion-card').forEach(card => {
            const cardTone = card.getAttribute('data-tone');
            if (tone === 'all' || cardTone === tone) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    };

    window.copyAiReply = function(e, btn, text) {
        e.stopPropagation();
        if (!text) return;

        const doCopy = () => {
            const original = btn.innerHTML;
            btn.innerHTML = `<span class="material-symbols-outlined text-[13px] text-emerald-600">check</span><span class="text-emerald-700 font-bold">Đã chép</span>`;
            setTimeout(() => {
                btn.innerHTML = original;
            }, 1800);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(doCopy).catch(() => {});
        } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy');
                doCopy();
            } catch (err) {}
            document.body.removeChild(ta);
        }
    };

    window.applyAiSuggestionItem = function(reviewId, el) {
        const text = el.getAttribute('data-reply');
        const label = el.getAttribute('data-label') || 'Gợi ý AI';
        const textarea = document.getElementById(`phan-hoi-${reviewId}`);
        if (!textarea || !text) return;

        textarea.value = text;
        textarea.classList.add('ring-2', 'ring-purple-600', 'bg-purple-50/40');
        setTimeout(() => {
            textarea.classList.remove('ring-2', 'ring-purple-600', 'bg-purple-50/40');
        }, 1200);

        // Đánh dấu card đang được chọn
        const grid = document.getElementById(`ai-suggestions-grid-${reviewId}`);
        if (grid) {
            grid.querySelectorAll('.ai-suggestion-card').forEach(c => {
                c.classList.remove('ring-2', 'ring-purple-600', 'border-purple-600', 'bg-purple-50/25');
                const badge = c.querySelector('.selected-check-badge');
                if (badge) badge.classList.add('hidden');
            });
            el.classList.add('ring-2', 'ring-purple-600', 'border-purple-600', 'bg-purple-50/25');
            const currentBadge = el.querySelector('.selected-check-badge');
            if (currentBadge) currentBadge.classList.remove('hidden');
        }

        const hint = document.getElementById(`applied-hint-${reviewId}`);
        if (hint) {
            hint.querySelector('.hint-text').textContent = `Đã chọn phương án: ${label}`;
            hint.classList.remove('hidden');
        }

        textarea.focus();
    };

    window.ownerAiReviewData = window.ownerAiReviewData || {};
    window.ownerAiCurrentIndex = window.ownerAiCurrentIndex || {};

    /**
     * Tự động áp dụng câu trả lời theo chỉ số phong cách vào textarea
     */
    window.applyAiSuggestionToTextarea = function(reviewId, index) {
        const data = window.ownerAiReviewData[reviewId];
        if (!data || !data.suggestions || !data.suggestions[index]) return;

        window.ownerAiCurrentIndex[reviewId] = index;
        const currentItem = data.suggestions[index];
        const total = data.suggestions.length;
        const humanIndex = index + 1;

        const textarea = document.getElementById(`phan-hoi-${reviewId}`);
        if (textarea) {
            textarea.value = currentItem.reply;
            textarea.focus();

            // Hiệu ứng highlight viền sáng thông báo đã điền tự động
            textarea.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-50/20');
            void textarea.offsetWidth; // Force repaint
            textarea.classList.add('ring-2', 'ring-purple-500', 'bg-purple-50/20');
            setTimeout(() => {
                textarea.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-50/20');
            }, 1400);
        }

        // Cập nhật nhãn phong cách đang kích hoạt
        const activeLabel = document.getElementById(`ai-active-style-label-${reviewId}`);
        if (activeLabel) {
            activeLabel.innerHTML = `Kiểu ${humanIndex}/${total}: <span class="text-purple-900 font-extrabold">${currentItem.label}</span>`;
        }

        // Cập nhật số đếm trên nút Đổi câu khác
        const cycleCount = document.getElementById(`cycle-count-${reviewId}`);
        if (cycleCount) {
            cycleCount.textContent = `Kiểu ${humanIndex}/${total}`;
        }

        // Cập nhật trạng thái active của các pills
        const pillsContainer = document.getElementById(`ai-styles-pills-${reviewId}`);
        if (pillsContainer) {
            pillsContainer.querySelectorAll('.ai-style-pill').forEach((pill, idx) => {
                if (idx === index) {
                    pill.className = 'ai-style-pill px-2.5 py-1 rounded-lg text-[11px] font-bold bg-purple-700 text-white border border-purple-700 shadow-2xs flex items-center gap-1 shrink-0 transition cursor-pointer';
                } else {
                    pill.className = 'ai-style-pill px-2.5 py-1 rounded-lg text-[11px] font-medium bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 flex items-center gap-1 shrink-0 transition cursor-pointer';
                }
            });
        }

        // Cập nhật thông báo dưới textarea
        const hint = document.getElementById(`applied-hint-${reviewId}`);
        if (hint) {
            hint.querySelector('.hint-text').textContent = `Đã áp dụng: Kiểu ${humanIndex}/${total} • ${currentItem.label} (Bấm "Đổi câu khác" để thử phong cách khác)`;
            hint.classList.remove('hidden');
        }
    };

    /**
     * Xoay vòng đổi sang câu trả lời phong cách tiếp theo (Không cần reload hay chờ mạng)
     */
    window.cycleNextAiReply = function(reviewId) {
        const data = window.ownerAiReviewData[reviewId];
        if (!data || !data.suggestions || data.suggestions.length === 0) return;

        const currentIndex = window.ownerAiCurrentIndex[reviewId] || 0;
        const nextIndex = (currentIndex + 1) % data.suggestions.length;
        window.applyAiSuggestionToTextarea(reviewId, nextIndex);
    };

    /**
     * Hiển thị thanh trạng thái AI Thông minh (Tự hiểu Khen/Chê, vấn đề thực tế)
     */
    window.renderAiSmartStatusBar = function(reviewId) {
        const data = window.ownerAiReviewData[reviewId];
        if (!data) return;

        const box = document.getElementById(`ai-smart-status-${reviewId}`);
        if (!box) return;

        // 1. Phân loại khen / chê / góp ý
        const sentimentBadge = document.getElementById(`ai-sentiment-badge-${reviewId}`);
        if (sentimentBadge) {
            if (data.phanLoai === 'khen') {
                sentimentBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs';
                sentimentBadge.innerHTML = '<span class="material-symbols-outlined text-[14px] text-emerald-600">thumb_up</span><span>🌟 Khách khen ngợi</span>';
            } else if (data.phanLoai === 'che') {
                sentimentBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 shadow-2xs';
                sentimentBadge.innerHTML = '<span class="material-symbols-outlined text-[14px] text-rose-600">warning</span><span>⚠️ Khách phản ánh / chê</span>';
            } else {
                sentimentBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 shadow-2xs';
                sentimentBadge.innerHTML = '<span class="material-symbols-outlined text-[14px] text-amber-600">lightbulb</span><span>💡 Đóng góp ý kiến</span>';
            }
        }

        // 2. Tóm tắt vấn đề AI tự hiểu
        const issueText = document.getElementById(`ai-issue-text-${reviewId}`);
        if (issueText) {
            issueText.textContent = data.vanDe;
        }

        // 3. Render danh sách các viên thuốc phong cách (Pills)
        const pillsContainer = document.getElementById(`ai-styles-pills-${reviewId}`);
        if (pillsContainer) {
            const activeIdx = window.ownerAiCurrentIndex[reviewId] || 0;
            pillsContainer.innerHTML = data.suggestions.map((item, idx) => `
                <button type="button"
                        onclick="window.applyAiSuggestionToTextarea('${reviewId}', ${idx})"
                        id="ai-pill-${reviewId}-${idx}"
                        class="ai-style-pill px-2.5 py-1 rounded-lg text-[11px] font-medium transition cursor-pointer border flex items-center gap-1 shrink-0 ${idx === activeIdx ? 'bg-purple-700 text-white border-purple-700 shadow-2xs font-bold' : 'bg-white text-slate-700 hover:bg-slate-100 border-slate-200'}">
                    <span>${item.label}</span>
                </button>
            `).join('');
        }

        box.classList.remove('hidden');
    };

    /**
     * Bấm AI Gợi ý: Tự động phân tích Khen/Chê, tạo ít nhất 4 kiểu không trùng lặp, và điền ngay lập tức vào textarea!
     */
    window.suggestAiReply = async function(slug, reviewId, btn, isRefresh = false) {
        const textarea = document.getElementById(`phan-hoi-${reviewId}`);
        if (!textarea) return;

        // Quản lý seed để mỗi lần bấm sinh bộ mới là ra các câu hoàn toàn khác biệt, không trùng lặp
        if (isRefresh) {
            window.reviewAiSeeds[reviewId] = (window.reviewAiSeeds[reviewId] || 0) + 1;
        } else if (window.reviewAiSeeds[reviewId] === undefined) {
            window.reviewAiSeeds[reviewId] = 0;
        }
        const currentSeed = window.reviewAiSeeds[reviewId];

        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `
            <span class="material-symbols-outlined text-xs animate-spin">sync</span>
            <span>${isRefresh ? 'Đang tạo bộ mới...' : 'AI đang phân tích & điền...'}</span>
        `;

        try {
            const res = await fetch(`/chu-quan/quan/${slug}/danh-gia/${reviewId}/ai-suggest-reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    seed: currentSeed
                })
            });

            const data = await res.json();
            if (data.success && (data.suggestions || data.reply)) {
                const suggestions = data.suggestions && data.suggestions.length > 0 ? data.suggestions : [
                    {
                        tone: 'lich_su',
                        label: 'Lịch sự & Chuẩn mực',
                        icon: 'workspace_premium',
                        reply: data.reply
                    }
                ];

                // Lưu dữ liệu vào state client
                window.ownerAiReviewData[reviewId] = {
                    phanLoai: data.phan_loai || 'khen',
                    vanDe: data.van_de_nhan_dien || 'Trải nghiệm của khách',
                    suggestions: suggestions
                };

                // BƯỚC QUAN TRỌNG: Tự động điền NGAY câu trả lời vào textarea mà không bắt người dùng phải chọn kiểu!
                window.applyAiSuggestionToTextarea(reviewId, 0);

                // Cập nhật giao diện thanh trạng thái AI Thông minh
                window.renderAiSmartStatusBar(reviewId);

                // Kích hoạt nút "Đổi câu khác"
                const cycleBtn = document.getElementById(`btn-cycle-reply-${reviewId}`);
                if (cycleBtn) {
                    cycleBtn.classList.remove('hidden');
                }
            } else {
                showNotify('Không thể tạo phản hồi lúc này. Vui lòng thử lại.', 'error', 'AI Gợi Ý Trả Lời');
            }
        } catch (err) {
            console.error('Suggest reply error:', err);
            showNotify('Lỗi kết nối. Vui lòng thử lại sau.', 'error', 'Lỗi kết nối');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    };

    /* =========================================================
       REALTIME REVIEW SYNCING (TỰ ĐỘNG CẬP NHẬT ĐÁNH GIÁ MỚI KHÔNG CẦN F5)
    ========================================================= */
    window.ownerReviewsState = {
        slug: '{{ $quan->slug }}',
        lastId: '{{ $ownerReviews->first()?->id ?? "" }}',
        lastCount: {{ (int) ($ownerReviewCount ?? 0) }},
        lastUpdated: '{{ $ownerReviews->first()?->updated_at?->toISOString() ?? "" }}',
        isSyncing: false
    };

    async function checkOwnerReviewsUpdates() {
        if (window.ownerReviewsState.isSyncing) return;
        if (document.hidden) return; // Tiết kiệm băng thông và CPU khi tab không active

        window.ownerReviewsState.isSyncing = true;
        const state = window.ownerReviewsState;

        try {
            const url = new URL(`/chu-quan/quan/${state.slug}/danh-gia/realtime-check`, window.location.origin);
            url.searchParams.set('last_id', state.lastId || '');
            url.searchParams.set('last_count', state.lastCount);
            url.searchParams.set('last_updated', state.lastUpdated || '');

            const res = await fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) return;
            const data = await res.json();

            if (data.has_updates) {
                // 1. Cập nhật HTML danh sách đánh giá (chỉ cập nhật khi chủ quán không đang gõ phản hồi dở dang)
                const listContainer = document.getElementById('owner-reviews-list-container');
                const isUserTyping = listContainer && listContainer.contains(document.activeElement);
                if (listContainer && data.html && !isUserTyping) {
                    listContainer.innerHTML = data.html;
                }

                // 2. Cập nhật số lượng trên Section Header
                const headerCount = document.getElementById('owner-reviews-header-count');
                if (headerCount) {
                    headerCount.textContent = data.total_count;
                }

                // 3. Cập nhật số tổng trên Tab
                const totalCountEl = document.getElementById('owner-reviews-total-count');
                if (totalCountEl) {
                    totalCountEl.textContent = data.total_count;
                }

                // Cập nhật Badge số đánh giá chưa trả lời
                const tabBadge = document.getElementById('owner-reviews-tab-badge');
                const unrepliedCountEl = document.getElementById('owner-reviews-unreplied-count');
                const unreplied = typeof data.unreplied_count !== 'undefined' ? data.unreplied_count : 0;
                if (tabBadge && unrepliedCountEl) {
                    unrepliedCountEl.textContent = unreplied;
                    const isReviewsTabActive = document.querySelector('#owner-venue-tabs a[href="#owner-reviews-section"]')?.classList.contains('tab-active');
                    tabBadge.classList.toggle('hidden', unreplied === 0 || isReviewsTabActive);
                }

                // 4. Cập nhật Điểm đánh giá trung bình
                const avgRatingEl = document.getElementById('owner-reviews-avg-rating');
                if (avgRatingEl) {
                    if (data.total_count > 0 && data.average_rating > 0) {
                        avgRatingEl.textContent = `★ ${parseFloat(data.average_rating).toFixed(1)} / 5`;
                        avgRatingEl.classList.remove('hidden');
                    } else {
                        avgRatingEl.classList.add('hidden');
                    }
                }

                // 5. Hiệu ứng highlight thẻ đánh giá mới nhất và Toast thông báo
                if (data.latest_review && data.latest_review.id) {
                    const latestCard = document.getElementById(`owner-review-item-${data.latest_review.id}`);
                    if (latestCard) {
                        latestCard.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50/60', 'shadow-md');
                        setTimeout(() => {
                            latestCard.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/60', 'shadow-md');
                        }, 5000);
                    }

                    // Hiển thị thông báo nổi Toast cho chủ quán
                    showOwnerNewReviewToast(data.latest_review);
                }

                // 6. Tự động kích hoạt AI phân tích cập nhật lại (ngầm)
                if (typeof refreshAiReviewSummary === 'function') {
                    refreshAiReviewSummary(state.slug, true);
                }

                // Cập nhật state nội bộ
                state.lastCount = data.total_count;
                if (data.latest_review) {
                    state.lastId = data.latest_review.id;
                    state.lastUpdated = data.latest_review.updated_at || '';
                }
            }
        } catch (err) {
            console.debug('Error polling owner reviews:', err);
        } finally {
            window.ownerReviewsState.isSyncing = false;
        }
    }

    /**
     * Xử lý gửi phản hồi đánh giá bằng AJAX mượt mà, không giật tab hay tải lại trang
     */
    window.handleOwnerReplySubmit = async function(event, slug, reviewId) {
        event.preventDefault();
        const form = event.target;
        const textarea = document.getElementById(`phan-hoi-${reviewId}`);
        const btn = document.getElementById(`btn-submit-reply-${reviewId}`);
        const btnText = document.getElementById(`btn-submit-reply-text-${reviewId}`);
        const labelMode = document.getElementById(`label-reply-mode-${reviewId}`);

        if (!textarea || !textarea.value.trim()) {
            if (window.showToast) {
                window.showToast({
                    title: 'Thiếu nội dung',
                    message: 'Vui lòng nhập nội dung phản hồi cho khách hàng.',
                    type: 'warning'
                });
            }
            if (textarea) textarea.focus();
            return;
        }

        const oldHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            if (btnText) btnText.textContent = 'Đang lưu phản hồi...';
        }

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const formData = new FormData(form);
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                // 1. Cập nhật hoặc hiển thị khung phản hồi của quán ngay trên thẻ đánh giá
                const displayWrap = document.getElementById(`owner-reply-display-wrap-${reviewId}`);
                const replyTextEl = document.getElementById(`owner-reply-text-${reviewId}`);
                const replyTimeEl = document.getElementById(`owner-reply-time-${reviewId}`);
                const wasPreviouslyReplied = displayWrap && !displayWrap.classList.contains('hidden');

                if (displayWrap && replyTextEl) {
                    replyTextEl.textContent = data.reply;
                    if (replyTimeEl && data.reply_time) {
                        replyTimeEl.textContent = data.reply_time;
                    }
                    displayWrap.classList.remove('hidden');
                }

                // 2. Cập nhật nhãn và nút
                if (labelMode) {
                    labelMode.textContent = 'Chỉnh sửa câu trả lời của quán';
                }
                if (btnText) {
                    btnText.textContent = 'Cập nhật phản hồi';
                }

                // 3. Ẩn hint đã áp dụng mẫu
                const hint = document.getElementById(`applied-hint-${reviewId}`);
                if (hint) hint.classList.add('hidden');

                // 4. Cập nhật badge số chưa trả lời (nếu vừa trả lời lần đầu)
                const unrepliedEl = document.getElementById('owner-reviews-unreplied-count');
                const tabBadge = document.getElementById('owner-reviews-tab-badge');
                if (unrepliedEl && !wasPreviouslyReplied) {
                    let curCount = parseInt(unrepliedEl.textContent) || 0;
                    curCount = Math.max(0, curCount - 1);
                    unrepliedEl.textContent = curCount;
                    if (curCount === 0 && tabBadge) tabBadge.classList.add('hidden');
                }

                // 5. Cập nhật timestamp state nội bộ
                if (window.ownerReviewsState) {
                    window.ownerReviewsState.lastUpdated = new Date().toISOString();
                }

                if (window.showToast) {
                    window.showToast({
                        title: 'Đã lưu phản hồi!',
                        message: data.message || 'Phản hồi đánh giá đã được lưu thành công.',
                        type: 'success'
                    });
                }
            } else {
                if (window.showToast) {
                    window.showToast({
                        title: 'Không thể lưu phản hồi',
                        message: data.message || 'Vui lòng kiểm tra lại nội dung phản hồi.',
                        type: 'error'
                    });
                }
            }
        } catch (err) {
            console.error('Submit review reply error:', err);
            if (window.showToast) {
                window.showToast({
                    title: 'Lỗi kết nối',
                    message: 'Không thể kết nối máy chủ để lưu phản hồi.',
                    type: 'error'
                });
            }
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = oldHtml || `<span class="material-symbols-outlined text-[15px]">send</span><span>Cập nhật phản hồi</span>`;
            }
        }
    };

    // Polling định kỳ mỗi 4 giây (chỉ chạy khi tab đang mở)
    setInterval(checkOwnerReviewsUpdates, 4000);

    /**
     * Toast thông báo realtime khi có khách đánh giá mới
     */
    function showOwnerNewReviewToast(review) {
        let toastContainer = document.getElementById('owner-review-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'owner-review-toast-container';
            toastContainer.className = 'fixed bottom-5 right-5 z-[9999] flex flex-col gap-3 max-w-sm w-full px-4 sm:px-0 pointer-events-none';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto bg-white/95 backdrop-blur-md border border-emerald-400 rounded-2xl p-4 shadow-2xl flex items-start gap-3.5 transform transition-all duration-300 translate-y-4 opacity-0';
        
        const starsText = '★'.repeat(review.so_sao || 5) + '☆'.repeat(5 - (review.so_sao || 5));
        
        toast.innerHTML = `
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                <span class="material-symbols-outlined text-xl">star</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Đánh giá mới vừa đến</span>
                    <button type="button" onclick="this.closest('.pointer-events-auto').remove()" class="text-gray-400 hover:text-gray-600 text-sm p-0.5 rounded cursor-pointer leading-none">
                        ✕
                    </button>
                </div>
                <h5 class="font-bold text-xs text-gray-900 mt-1 truncate">${review.user_name || 'Khách hàng'}</h5>
                <p class="text-amber-500 text-xs font-bold">${starsText}</p>
                <p class="text-xs text-gray-600 line-clamp-2 mt-1 italic font-serif">"${review.binh_luan || 'Đã để lại đánh giá sao'}"</p>
                <div class="mt-2.5 flex items-center gap-2">
                    <button type="button" onclick="focusOnReview('${review.id}')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold rounded-lg shadow-2xs transition flex items-center gap-1 cursor-pointer">
                        <span>Xem & Trả lời ngay</span>
                        <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </button>
                </div>
            </div>
        `;

        toastContainer.appendChild(toast);
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        });

        // Tự động đóng sau 9 giây
        setTimeout(() => {
            if (toast && toast.parentNode) {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }
        }, 9000);
    }

    function focusOnReview(reviewId) {
        // Chuyển sang tab Đánh giá
        if (typeof setOwnerVenueTab === 'function') {
            setOwnerVenueTab('owner-reviews-section');
        } else {
            const reviewTab = document.querySelector('a[href="#owner-reviews-section"]');
            if (reviewTab) reviewTab.click();
        }

        setTimeout(() => {
            const card = document.getElementById(`owner-review-item-${reviewId}`);
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.classList.add('ring-4', 'ring-emerald-400', 'bg-emerald-50/70');
                const textarea = document.getElementById(`phan-hoi-${reviewId}`);
                if (textarea) textarea.focus();
                setTimeout(() => {
                    card.classList.remove('ring-4', 'ring-emerald-400', 'bg-emerald-50/70');
                }, 4000);
            }
        }, 200);
    }
</script>

{{-- Modal Đăng Video Review Mới Cho Chủ Quán --}}
<div id="modal-create-video" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden transition-opacity" onclick="if(event.target === this) closeCreateVideoModal()">
    <div class="bg-white rounded-3xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-gray-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        {{-- Modal Header --}}
        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-rose-50/50 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-xs">
                    <span class="material-symbols-outlined text-lg">smart_display</span>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base leading-tight">Đăng Video Review TikTok Mới</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">Quán: {{ $quan->ten_quan }}</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateVideoModal()" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer shadow-2xs">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('chu-quan.quan.videos.store', $quan->slug) }}" enctype="multipart/form-data" id="owner-video-form" onsubmit="handleOwnerVideoFormSubmit(event)" class="p-5 overflow-y-auto space-y-4 text-xs">
            @csrf

            {{-- Thông báo kiểm duyệt --}}
            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-800 text-[11px] leading-relaxed flex items-start gap-2">
                <span class="material-symbols-outlined text-amber-600 text-base shrink-0 mt-0.5">info</span>
                <span>Video sau khi gửi sẽ ở trạng thái <strong>🟡 Chờ duyệt</strong>. Admin sẽ phê duyệt trước khi hiển thị cho khách xem.</span>
            </div>

            {{-- Nhập link TikTok --}}
            <div>
                <label for="owner_video_input" class="block font-bold text-gray-700 mb-1.5">
                    Đường dẫn video TikTok (Khuyên dùng)
                </label>
                <div class="flex gap-2">
                    <input type="text" id="owner_video_input" name="video_input"
                           placeholder="https://www.tiktok.com/@username/video/123456789..."
                           class="flex-1 px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 outline-none focus:border-orange-500 focus:bg-white transition">
                    <button type="button" id="btn-fetch-tiktok-info" onclick="fetchOwnerTikTokInfo()"
                            class="px-3.5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition flex items-center gap-1 shrink-0 cursor-pointer shadow-xs">
                        <span class="material-symbols-outlined text-base">sync</span>
                        <span>Kiểm tra</span>
                    </button>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Dán link video TikTok review về quán bạn (hỗ trợ cả link app và trình duyệt).</p>
            </div>

            {{-- Khung preview khi bóc tách được info (Có nút Xóa/Hủy) --}}
            <div id="owner-tiktok-preview-box" class="hidden p-3 bg-gray-50 rounded-2xl border border-gray-200 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <img id="owner-preview-thumb" src="" class="w-14 h-18 object-cover rounded-xl bg-gray-200 shrink-0 shadow-xs">
                    <div class="min-w-0 flex-1">
                        <p id="owner-preview-title" class="font-bold text-xs text-gray-900 line-clamp-2"></p>
                        <p id="owner-preview-author" class="text-[11px] text-orange-600 font-semibold mt-0.5"></p>
                        <p id="owner-preview-views" class="text-[10px] text-gray-400 mt-0.5"></p>
                    </div>
                </div>
                <button type="button" onclick="clearOwnerTikTokPreview()" class="px-2.5 py-1.5 rounded-xl text-xs font-bold text-red-600 hover:text-red-700 hover:bg-red-50 border border-red-200 flex items-center gap-1 shrink-0 transition cursor-pointer" title="Hủy bỏ video TikTok này để chọn nguồn khác">
                    <span class="material-symbols-outlined text-base">delete</span>
                    <span>Xóa</span>
                </button>
            </div>

            {{-- Tiêu đề video --}}
            <div>
                <label for="owner_video_title" class="block font-bold text-gray-700 mb-1.5">
                    Tiêu đề hiển thị (Tùy chọn)
                </label>
                <input type="text" id="owner_video_title" name="tieu_de" maxlength="250"
                       placeholder="Để trống hệ thống sẽ tự lấy tiêu đề từ video TikTok"
                       class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 outline-none focus:border-orange-500 focus:bg-white transition">
            </div>

            {{-- Hoặc tải file MP4 --}}
            <div class="pt-3 border-t border-gray-100">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="owner_video_file" class="block font-bold text-gray-700">
                        Hoặc tải file video từ máy tính
                    </label>
                    <span class="text-[10px] text-gray-400">MP4, MOV, WebM (tối đa 200MB)</span>
                </div>
                <input type="file" id="owner_video_file" name="video_file" accept="video/mp4,video/quicktime,video/webm"
                       onchange="handleOwnerVideoFileChange(this)"
                       class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">

                {{-- Thông báo file đã chọn --}}
                <div id="owner-file-selected-box" class="hidden mt-2 p-2.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between gap-2 text-emerald-800">
                    <div class="flex items-center gap-2 min-w-0 truncate">
                        <span class="material-symbols-outlined text-base text-emerald-600 shrink-0">check_circle</span>
                        <span id="owner-file-selected-name" class="font-bold text-xs truncate"></span>
                    </div>
                    <button type="button" onclick="clearOwnerVideoFile()" class="text-xs text-red-600 hover:text-red-800 font-bold px-2 py-1 rounded-lg hover:bg-red-50 transition cursor-pointer shrink-0">
                        Hủy file
                    </button>
                </div>
            </div>

            {{-- Ảnh bìa tùy chọn (Thumbnail) --}}
            <div class="pt-2 border-t border-gray-100">
                <label for="owner_thumbnail_file" class="block font-bold text-gray-700 mb-1">
                    Ảnh bìa / Thumbnail đại diện (Tùy chọn)
                </label>
                <input type="file" id="owner_thumbnail_file" name="thumbnail_file" accept="image/jpeg,image/png,image/webp"
                       onchange="handleOwnerThumbnailChange(this)"
                       class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                <p class="text-[10px] text-gray-400 mt-0.5">Tải ảnh bìa tùy chỉnh dạng JPG, PNG nếu bạn đăng video từ máy tính.</p>

                <div id="owner-thumb-preview-box" class="hidden mt-2 flex items-center gap-2.5 p-2 bg-gray-50 border border-gray-200 rounded-xl">
                    <img id="owner-thumb-preview" src="" class="w-12 h-16 object-cover rounded-lg bg-gray-200">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-700">Xem trước ảnh bìa tùy chọn</p>
                    </div>
                    <button type="button" onclick="clearOwnerThumbnail()" class="text-xs text-red-600 hover:text-red-800 font-bold px-2 py-1 hover:bg-red-50 rounded-lg transition cursor-pointer">
                        Hủy ảnh
                    </button>
                </div>
            </div>

            {{-- Nút hành động --}}
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeCreateVideoModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition cursor-pointer">
                    Hủy bỏ
                </button>
                <button type="submit" id="btn-submit-owner-video" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-base">send</span>
                    <span>Gửi video phê duyệt</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Xem Thử Video Review Cho Chủ Quán --}}
<div id="modal-owner-preview-player" class="fixed inset-0 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md hidden transition-opacity" style="z-index: 100000;" onclick="if(event.target === this) closePreviewVideoModal()">
    <button type="button" onclick="closePreviewVideoModal()" class="fixed top-5 right-5 text-white hover:text-gray-300 w-11 h-11 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition cursor-pointer" style="z-index: 100001;" title="Đóng (Esc)">
        <span class="material-symbols-outlined text-2xl font-black">close</span>
    </button>
    <div class="rounded-3xl overflow-hidden shadow-2xl bg-black border border-white/10 flex items-center justify-center" style="width: min(480px, calc(86vh * 9 / 16), 94vw); height: min(780px, 86vh); position: relative;">
        <div id="owner-player-container" class="w-full h-full flex items-center justify-center bg-black"></div>
    </div>
</div>

<script>
    window.markOwnerReviewsAsSeen = function(slug) {
        const badge = document.getElementById('owner-reviews-tab-badge');
        if (badge) {
            badge.classList.add('hidden');
        }
        try {
            localStorage.setItem('owner_reviews_seen_' + slug, Date.now().toString());
        } catch (e) {}
    };

    // Realtime detection: when admin rejects or deletes this restaurant, alert owner and redirect to /chu-quan
    window.addEventListener('quanmoi:quan-status-updated', function(e) {
        const notif = e.detail;
        if (!notif) return;

        const currentQuanId = '{{ $quan->id }}';
        const currentQuanSlug = '{{ $quan->slug }}';

        if (String(notif.quan_id) === currentQuanId || notif.slug === currentQuanSlug) {
            const isRemoved = ['rejected', 'deleted', 'force_deleted', 'khong_duyet'].includes(notif.action) ||
                              ['bi_khoa', 'tu_choi'].includes(notif.status);

            if (isRemoved) {
                const noteInfo = notif.note ? `<br><br><span class="text-xs text-rose-600 font-semibold">Ghi chú từ BQT: ${notif.note}</span>` : '';
                if (typeof window.showAlertDialog === 'function') {
                    window.showAlertDialog({
                        title: 'Cơ sở ẩm thực đã bị gỡ',
                        message: `Cơ sở ẩm thực "${notif.title || '{{ $quan->ten_quan }}'}" vừa bị Ban quản trị từ chối duyệt hoặc xóa khỏi hệ thống.${noteInfo}<br><br>Hệ thống sẽ chuyển bạn về trang quản lý cửa hàng.`,
                        type: 'error',
                        onOk: () => {
                            window.location.href = '{{ route('chu-quan.quan.index') }}';
                        }
                    });
                } else {
                    alert('Cơ sở ẩm thực đã bị Ban quản trị từ chối duyệt hoặc xóa khỏi hệ thống.');
                    window.location.href = '{{ route('chu-quan.quan.index') }}';
                }

                setTimeout(() => {
                    window.location.href = '{{ route('chu-quan.quan.index') }}';
                }, 2500);
            }
        }
    });
</script>

{{-- MODAL XÁC NHẬN CHUNG DÀNH CHO CHỦ QUÁN (THAY THẾ TOÀN BỘ CONFIRM NATIVE) --}}
<div id="owner-confirm-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div id="owner-confirm-box" class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4 transform transition-transform scale-95 duration-200">
        <div id="owner-confirm-icon-wrap" class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center bg-red-100 text-red-600" style="background-color: #fee2e2; color: #dc2626;">
            <span id="owner-confirm-icon" class="material-symbols-outlined text-3xl">delete</span>
        </div>
        <div class="space-y-1.5">
            <h3 id="owner-confirm-title" class="text-base font-bold text-gray-900">Xác Nhận Thao Tác</h3>
            <div id="owner-confirm-message" class="text-xs text-gray-600 leading-relaxed"></div>
        </div>
        <div class="pt-2 flex items-center justify-center gap-2.5">
            <button type="button" onclick="closeOwnerConfirmModal()" class="owner-confirm-btn-cancel flex-1 py-2.5 px-4 rounded-xl border border-gray-200 text-gray-600 font-bold text-xs hover:bg-gray-50 transition cursor-pointer" style="background-color: #ffffff; color: #4b5563; border: 1px solid #e5e7eb;">
                Hủy bỏ
            </button>
            <button type="button" id="owner-confirm-submit-btn" onclick="handleOwnerConfirmSubmit()"
                    style="background-color: #dc2626 !important; color: #ffffff !important; border: 1px solid #dc2626 !important;"
                    class="owner-confirm-btn-danger flex-1 py-2.5 px-4 rounded-xl font-bold text-xs shadow-md shadow-red-500/25 transition flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95">
                <span id="owner-confirm-btn-icon" class="material-symbols-outlined text-base" style="color: #ffffff !important;">delete</span>
                <span id="owner-confirm-btn-text" style="color: #ffffff !important; font-weight: 700;">Xác Nhận Xóa</span>
            </button>
        </div>
    </div>
</div>
@endpush
