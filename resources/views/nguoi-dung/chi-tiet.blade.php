@extends('layouts.app')

@section('title', $quan->ten_quan . ' - ' . $quan->dia_chi_chi_tiet . ' | Quán Mới')

@push('styles')
    {{-- SEO Meta Tags & OpenGraph --}}
    <meta name="description" content="{{ $quan->ten_quan }} tại {{ $quan->dia_chi_chi_tiet }}. {{ $quan->mo_ta }}" />
    <meta name="keywords" content="{{ Str::slug($quan->ten_quan, ', ') }}, quán ngon {{ $quan->ten_quan_huyen }}, địa điểm ăn uống" />
    <link rel="canonical" href="{{ url()->current() }}" />

    {{-- OpenGraph Meta Tags --}}
    <meta property="og:title" content="{{ $quan->ten_quan }} - Quán Mới" />
    <meta property="og:description" content="{{ $quan->mo_ta }}" />
    <meta property="og:type" content="restaurant" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80' }}" />

    <style>
        /* Dynamic Gallery Styles */
        .gallery-hero-single {
            width: 100%;
            aspect-ratio: 16 / 9;
            max-height: 440px;
        }
        @media (min-width: 640px) {
            .gallery-hero-single {
                aspect-ratio: 21 / 9;
            }
        }

        .gallery-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            aspect-ratio: 16 / 9;
            gap: 8px;
        }
        @media (min-width: 640px) {
            .gallery-grid-2 {
                aspect-ratio: 2.2 / 1;
            }
        }

        .gallery-grid-3 {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            grid-template-rows: repeat(2, minmax(0, 1fr));
            aspect-ratio: 16 / 9;
            gap: 8px;
        }
        @media (min-width: 640px) {
            .gallery-grid-3 {
                aspect-ratio: 2.6 / 1;
            }
        }
        .gallery-grid-3-item-1 { grid-row: span 2; }
        .gallery-grid-3-item-2 { grid-row: span 1; }
        .gallery-grid-3-item-3 { grid-row: span 1; }

        .bento-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(2, minmax(0, 1fr));
            aspect-ratio: 16 / 9;
            gap: 8px;
        }
        @media (min-width: 640px) {
            .bento-grid {
                aspect-ratio: 3.05 / 1;
            }
        }
        .bento-item-1 { grid-column: span 2; grid-row: span 2; }
        .bento-item-2 { grid-column: span 2; grid-row: span 1; }
        .bento-item-3 { grid-column: span 1; grid-row: span 1; }
        .bento-item-4 { grid-column: span 1; grid-row: span 1; }

        .sticky-sidebar {
            position: sticky;
            top: 80px;
            height: fit-content;
        }
        .tab-active {
            border-bottom: 2px solid #a04100;
            color: #a04100;
        }

        .venue-page-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 20px;
        }

        .venue-content-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 14px;
            min-width: 0;
        }

        .venue-menu-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .venue-menu-card {
            display: flex;
            align-items: center;
            min-width: 0;
            gap: 12px;
            padding: 10px;
            min-height: 92px;
        }

        .venue-menu-image {
            width: 72px;
            height: 72px;
            flex: 0 0 72px;
            overflow: hidden;
            border-radius: 6px;
        }

        .venue-menu-order-icon {
            display: flex;
            width: 24px;
            height: 24px;
            flex: 0 0 24px;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            background: #ffdbcc;
            color: #a04100;
        }

        .rating-choice {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: -1px;
            overflow: hidden;
            clip-path: inset(50%);
            white-space: nowrap;
        }

        .rating-choice:focus-visible + label {
            outline: 2px solid #a04100;
            outline-offset: 3px;
            border-radius: 2px;
        }

        .review-comment-text {
            overflow-wrap: anywhere;
            word-break: normal;
        }

        @media (max-width: 340px) {
            .venue-menu-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .venue-section-anchor {
            scroll-margin-top: 92px;
        }

        @media (min-width: 480px) {
            .venue-content-layout {
                grid-template-columns: minmax(0, 2.1fr) minmax(145px, 1fr);
                align-items: start;
                gap: 14px;
            }

            .venue-sidebar {
                min-width: 0;
            }

            .venue-sidebar .venue-side-card {
                padding: 12px;
            }

            .venue-map-frame {
                height: 120px;
            }
        }

    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
@endpush

@push('seo')
    @php
        $breadcrumbItems = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Trang chủ',
                'item' => route('home'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Khám phá',
                'item' => route('kham-pha'),
            ],
        ];
        $pos = 3;
        if (!empty($quan->ten_tinh_thanh)) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'name' => $quan->ten_tinh_thanh,
                'item' => route('kham-pha', ['tinh_thanh_id' => $quan->tinh_thanh_id]),
            ];
        }
        if (!empty($quan->loai_hinh_kinh_doanh)) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'name' => str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh)),
                'item' => route('kham-pha', ['danh_muc' => $quan->loai_hinh_kinh_doanh]),
            ];
        }
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $pos,
            'name' => $quan->ten_quan,
            'item' => url()->current(),
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')
    <main class="venue-page-layout mt-3 sm:mt-4 max-w-7xl mx-auto px-3 sm:px-4 md:px-8 pb-20 flex-grow">

        {{-- Breadcrumb Navigation --}}
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-on-surface-variant text-[13.5px] sm:text-[14px] overflow-x-auto no-scrollbar whitespace-nowrap -mb-1">
            <a class="hover:text-primary transition-colors flex items-center text-gray-500 hover:text-primary" href="{{ route('home') }}" title="Trang chủ">
                <span class="material-symbols-outlined text-[19px]">home</span>
            </a>
            <span class="material-symbols-outlined text-[16px] text-gray-400 select-none">chevron_right</span>
            <a class="hover:text-primary transition-colors text-gray-600 hover:text-primary font-medium" href="{{ route('kham-pha') }}">Khám phá</a>

            @if(!empty($quan->ten_tinh_thanh))
                <span class="material-symbols-outlined text-[16px] text-gray-400 select-none">chevron_right</span>
                <a class="hover:text-primary transition-colors text-gray-600 hover:text-primary font-medium" href="{{ route('kham-pha', ['tinh_thanh_id' => $quan->tinh_thanh_id]) }}">
                    {{ $quan->ten_tinh_thanh }}
                </a>
            @endif

            @if(!empty($quan->loai_hinh_kinh_doanh))
                <span class="material-symbols-outlined text-[16px] text-gray-400 select-none">chevron_right</span>
                <a class="hover:text-primary transition-colors text-gray-600 hover:text-primary font-medium" href="{{ route('kham-pha', ['danh_muc' => $quan->loai_hinh_kinh_doanh]) }}">
                    {{ str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh)) }}
                </a>
            @endif

            <span class="material-symbols-outlined text-[16px] text-gray-400 select-none">chevron_right</span>
            <span class="text-on-surface font-bold text-gray-900 truncate max-w-[200px] sm:max-w-[320px] md:max-w-[450px]" title="{{ $quan->ten_quan }}">
                {{ $quan->ten_quan }}
            </span>
        </nav>

        {{-- Preview Banner for Unapproved / Pending Venues --}}
        @if($quan->trang_thai !== 'da_duyet')
            <div class="my-3 p-4 rounded-2xl border border-amber-300 bg-amber-50 text-amber-900 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-[22px]">pending_actions</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm text-amber-950">Chế độ xem trước thẩm định hồ sơ</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $quan->trang_thai === 'chua_duyet' ? 'bg-amber-200 text-amber-900' : 'bg-rose-100 text-rose-800' }}">
                                <span class="material-symbols-outlined text-[13px]">{{ $quan->trang_thai === 'chua_duyet' ? 'schedule' : 'lock' }}</span>
                                {{ $quan->trang_thai === 'chua_duyet' ? 'Chờ phê duyệt' : 'Bị khóa / Từ chối' }}
                            </span>
                        </div>
                        <p class="text-xs text-amber-800 mt-0.5">Trang quán này chưa được công khai cho khách. Chỉ Quản trị viên và Chủ sở hữu quán mới có thể xem trước nội dung này.</p>
                    </div>
                </div>
                @if(auth()->check() && auth()->user()->isAdmin())
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition-all">
                            <span class="material-symbols-outlined text-[16px]">verified</span>
                            <span>Quay lại duyệt quán</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Hero Gallery Section -->
        <section id="gallery-section" class="venue-section-anchor mb-5">
            @php
                $hinhAnhs = $quan->hinhAnh ?? collect();
                $galleryList = collect();

                // 1. Ảnh bìa (Cover)
                if (!empty($quan->anh_bia_url)) {
                    $galleryList->push([
                        'url' => $quan->anh_bia_url,
                        'caption' => $quan->ten_quan . ' - Ảnh bìa'
                    ]);
                }

                // 2. Các ảnh trong bộ sưu tập thực tế (Gallery) - Tuyệt đối không chèn ảnh stock giả mạo
                foreach ($hinhAnhs as $idx => $ha) {
                    $imgUrl = $ha->duong_dan ?? null;
                    if (!empty($imgUrl) && $imgUrl !== $quan->anh_bia_url) {
                        $galleryList->push([
                            'url' => $imgUrl,
                            'caption' => $ha->tieu_de ?: ($quan->ten_quan . ' - Ảnh ' . ($idx + 1))
                        ]);
                    }
                }

                // Nếu hoàn toàn không có ảnh nào, dùng 1 ảnh nhà hàng dự phòng chuẩn chất lượng cao
                if ($galleryList->isEmpty()) {
                    $galleryList->push([
                        'url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80',
                        'caption' => $quan->ten_quan
                    ]);
                }

                $totalImages = $galleryList->count();
            @endphp

            @if($totalImages === 1)
                {{-- TRƯỜNG HỢP 1: DUY NHẤT 1 ẢNH (HERO BANNER TOÀN CẢNH CINEMATIC) --}}
                <div class="gallery-hero-single rounded-2xl overflow-hidden relative group shadow-sm bg-gray-100 border border-gray-100">
                    <a href="{{ $galleryList[0]['url'] }}" data-fancybox="gallery" data-caption="{{ $galleryList[0]['caption'] }}" class="block w-full h-full cursor-zoom-in relative">
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-all z-10 pointer-events-none"></div>
                        <img src="{{ $galleryList[0]['url'] }}" 
                             alt="{{ $quan->ten_quan }}" 
                             class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out" />
                    </a>

                    {{-- Nút xem toàn màn hình --}}
                    <div class="absolute bottom-4 right-4 z-20 flex items-center gap-2">
                        <a href="{{ $galleryList[0]['url'] }}" data-fancybox="gallery" data-caption="{{ $galleryList[0]['caption'] }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-black/60 hover:bg-black/80 backdrop-blur-md text-white text-xs font-bold transition-all shadow-md">
                            <span class="material-symbols-outlined text-[17px]">fullscreen</span>
                            <span>Xem ảnh phóng to</span>
                        </a>
                    </div>

                    {{-- Badge góc trên --}}
                    <div class="absolute top-4 right-4 z-20 pointer-events-none">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-black/50 backdrop-blur-md text-white text-[11px] font-bold">
                            <span class="material-symbols-outlined text-[14px]">photo_camera</span>
                            <span>1 ảnh</span>
                        </span>
                    </div>
                </div>

            @elseif($totalImages === 2)
                {{-- TRƯỜNG HỢP 2: ĐÚNG 2 ẢNH (CHIA ĐÔI CÂN XỨNG 50% - 50%) --}}
                <div class="gallery-grid-2 rounded-2xl overflow-hidden relative group shadow-sm bg-gray-100 border border-gray-100">
                    @foreach($galleryList as $idx => $img)
                        <a href="{{ $img['url'] }}" data-fancybox="gallery" data-caption="{{ $img['caption'] }}" class="rounded-xl overflow-hidden cursor-zoom-in group/item relative block h-full">
                            <div class="absolute inset-0 bg-black/10 group-hover/item:bg-transparent transition-all z-10 pointer-events-none"></div>
                            <img src="{{ $img['url'] }}" alt="{{ $img['caption'] }}" class="w-full h-full object-cover group-hover/item:scale-105 transition-transform duration-500 ease-out" />
                        </a>
                    @endforeach

                    <div class="absolute bottom-4 right-4 z-20 pointer-events-none">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-black/60 backdrop-blur-md text-white text-xs font-bold shadow-md">
                            <span class="material-symbols-outlined text-[16px]">grid_view</span>
                            <span>Xem 2 ảnh</span>
                        </span>
                    </div>
                </div>

            @elseif($totalImages === 3)
                {{-- TRƯỜNG HỢP 3: ĐÚNG 3 ẢNH (1 LỚN BÊN TRÁI + 2 NHỎ BÊN PHẢI) --}}
                <div class="gallery-grid-3 rounded-2xl overflow-hidden relative group shadow-sm bg-gray-100 border border-gray-100">
                    <a href="{{ $galleryList[0]['url'] }}" data-fancybox="gallery" data-caption="{{ $galleryList[0]['caption'] }}" class="gallery-grid-3-item-1 rounded-xl overflow-hidden cursor-zoom-in group/item relative block h-full">
                        <div class="absolute inset-0 bg-black/10 group-hover/item:bg-transparent transition-all z-10 pointer-events-none"></div>
                        <img src="{{ $galleryList[0]['url'] }}" alt="{{ $galleryList[0]['caption'] }}" class="w-full h-full object-cover group-hover/item:scale-105 transition-transform duration-500 ease-out" />
                    </a>
                    <a href="{{ $galleryList[1]['url'] }}" data-fancybox="gallery" data-caption="{{ $galleryList[1]['caption'] }}" class="gallery-grid-3-item-2 rounded-xl overflow-hidden cursor-zoom-in group/item relative block h-full">
                        <div class="absolute inset-0 bg-black/10 group-hover/item:bg-transparent transition-all z-10 pointer-events-none"></div>
                        <img src="{{ $galleryList[1]['url'] }}" alt="{{ $galleryList[1]['caption'] }}" class="w-full h-full object-cover group-hover/item:scale-105 transition-transform duration-500 ease-out" />
                    </a>
                    <a href="{{ $galleryList[2]['url'] }}" data-fancybox="gallery" data-caption="{{ $galleryList[2]['caption'] }}" class="gallery-grid-3-item-3 rounded-xl overflow-hidden cursor-zoom-in group/item relative block h-full">
                        <div class="absolute inset-0 bg-black/10 group-hover/item:bg-transparent transition-all z-10 pointer-events-none"></div>
                        <img src="{{ $galleryList[2]['url'] }}" alt="{{ $galleryList[2]['caption'] }}" class="w-full h-full object-cover group-hover/item:scale-105 transition-transform duration-500 ease-out" />
                    </a>

                    <div class="absolute bottom-4 right-4 z-20 pointer-events-none">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-black/60 backdrop-blur-md text-white text-xs font-bold shadow-md">
                            <span class="material-symbols-outlined text-[16px]">grid_view</span>
                            <span>Xem tất cả (3 ảnh)</span>
                        </span>
                    </div>
                </div>

            @else
                {{-- TRƯỜNG HỢP 4+: TỪ 4 ẢNH TRỞ LÊN (BENTO GRID 4 SLOT AIRBNB KÈM NÚT XEM TẤT CẢ) --}}
                <div class="bento-grid relative">
                    {{-- Ô 1: Lớn bên trái --}}
                    <a href="{{ $galleryList[0]['url'] }}" data-fancybox="gallery" data-caption="{{ $galleryList[0]['caption'] }}" class="bento-item-1 rounded-xl overflow-hidden cursor-zoom-in group relative block h-full">
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-all z-10 pointer-events-none"></div>
                        <img src="{{ $galleryList[0]['url'] }}" alt="{{ $galleryList[0]['caption'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" />
                    </a>

                    {{-- Ô 2: Trên bên phải --}}
                    <a href="{{ $galleryList[1]['url'] }}" data-fancybox="gallery" data-caption="{{ $galleryList[1]['caption'] }}" class="bento-item-2 rounded-xl overflow-hidden cursor-zoom-in group relative block h-full">
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-all z-10 pointer-events-none"></div>
                        <img src="{{ $galleryList[1]['url'] }}" alt="{{ $galleryList[1]['caption'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" />
                    </a>

                    {{-- Ô 3: Dưới trái --}}
                    <a href="{{ $galleryList[2]['url'] }}" data-fancybox="gallery" data-caption="{{ $galleryList[2]['caption'] }}" class="bento-item-3 rounded-xl overflow-hidden cursor-zoom-in group relative block h-full">
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-all z-10 pointer-events-none"></div>
                        <img src="{{ $galleryList[2]['url'] }}" alt="{{ $galleryList[2]['caption'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" />
                    </a>

                    {{-- Ô 4: Dưới phải + Lớp phủ "Xem tất cả" --}}
                    <a href="{{ $galleryList[3]['url'] }}" data-fancybox="gallery" data-caption="{{ $galleryList[3]['caption'] }}" class="bento-item-4 rounded-xl overflow-hidden cursor-zoom-in group relative block h-full">
                        <img src="{{ $galleryList[3]['url'] }}" alt="{{ $galleryList[3]['caption'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" />
                        <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/45 group-hover:bg-black/30 transition-colors duration-300 z-10 text-white">
                            <span class="material-symbols-outlined text-2xl sm:text-3xl">grid_view</span>
                            <span class="font-bold text-xs sm:text-sm mt-1">Xem tất cả ({{ $totalImages }})</span>
                        </div>
                    </a>

                    {{-- Các ảnh từ thứ 5 trở đi được ẩn để Lightbox Fancybox duyệt toàn bộ ảnh --}}
                    @if($totalImages > 4)
                        @for($i = 4; $i < $totalImages; $i++)
                            <a href="{{ $galleryList[$i]['url'] }}" data-fancybox="gallery" data-caption="{{ $galleryList[$i]['caption'] }}" class="hidden"></a>
                        @endfor
                    @endif
                </div>
            @endif
        </section>

        <!-- Content Layout -->
        <div class="venue-content-layout">
            <!-- Main Column -->
            <div class="min-w-0">
                <!-- Business Info Header -->
                <div class="mb-6">
                    <div class="flex flex-wrap sm:flex-nowrap items-start justify-between gap-3 mb-1">
                        <div class="flex min-w-0 items-start gap-2">
                            <h1 class="min-w-0 font-headline-lg text-headline-lg text-on-background font-black break-words [overflow-wrap:anywhere]">{{ $quan->ten_quan }}</h1>
                            @if($quan->is_xac_thuc)
                                <span class="material-symbols-outlined shrink-0 text-tick-xanh fill-icon text-xl" title="Verified">verified</span>
                            @endif
                        </div>
                        <button type="button" 
                            id="detail-save-btn" 
                            data-quan-id="{{ $quan->id }}" 
                            data-saved="{{ $isSaved ? '1' : '0' }}"
                            onclick="toggleSaveDetail(this, '{{ $quan->id }}')" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-bold text-xs sm:text-sm shadow-xs transition-all active:scale-95 group shrink-0"
                            title="{{ $isSaved ? 'Bỏ lưu quán' : 'Lưu quán' }}">
                            <span class="material-symbols-outlined text-[20px] text-red-500 transition-transform group-hover:scale-110" style="font-variation-settings: 'FILL' {{ $isSaved ? '1' : '0' }};">favorite</span>
                            <span id="detail-save-text">{{ $isSaved ? 'Đã lưu' : 'Lưu quán' }}</span>
                        </button>
                    </div>
                    <p class="text-on-surface-variant flex items-start gap-1 mb-3 text-xs sm:text-sm">
                        <span class="material-symbols-outlined text-lg">location_on</span>
                        {{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_phuong_xa }}, {{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}
                    </p>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 py-3 border-y border-surface-variant text-xs">
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-primary fill-icon text-base">visibility</span>
                            <span class="font-bold">{{ number_format($quan->luot_xem) }}</span>
                            <span class="text-on-surface-variant">lượt xem</span>
                        </div>
                        <div class="flex items-center gap-1 text-on-surface-variant">
                            <span class="material-symbols-outlined text-base">schedule</span>
                            <span>{{ \Illuminate\Support\Str::substr($quan->gio_mo_cua, 0, 5) }} - {{ \Illuminate\Support\Str::substr($quan->gio_dong_cua, 0, 5) }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-on-surface-variant">
                            <span class="material-symbols-outlined text-base">payments</span>
                            <span>{{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                </div>

                @if($quan->khuyenMaiDangChay && $quan->khuyenMaiDangChay->isNotEmpty())
                    @php $kmHead = $quan->khuyenMaiDangChay->first(); @endphp
                    <div class="mb-5 rounded-2xl bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500 p-4 sm:p-5 text-white shadow-lg shadow-orange-500/20 relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-10">
                            <div class="flex items-start sm:items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0 text-xl font-bold shadow-inner">
                                    🎁
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black uppercase tracking-wider bg-white/25 px-2 py-0.5 rounded-full">Ưu Đãi Đang Diễn Ra</span>
                                        @if($quan->khuyenMaiDangChay->count() > 1)
                                            <span class="text-[10px] bg-black/20 px-2 py-0.5 rounded-full">+{{ $quan->khuyenMaiDangChay->count() - 1 }} ưu đãi khác</span>
                                        @endif
                                    </div>
                                    <h3 class="font-bold text-base sm:text-lg leading-tight mt-0.5">{{ $kmHead->tieu_de }}</h3>
                                    <p class="text-xs text-white/90 line-clamp-1 mt-0.5">{{ $kmHead->slogan ?: $kmHead->noi_dung_bai_dang }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if($kmHead->ma_voucher)
                                    <div class="flex items-center bg-white text-gray-900 rounded-xl px-3 py-1.5 shadow-sm">
                                        <span class="text-xs font-mono font-black text-orange-600 mr-2">{{ $kmHead->ma_voucher }}</span>
                                        <button type="button" onclick="copyCustomerVoucher('{{ $kmHead->ma_voucher }}')" class="text-xs font-bold text-gray-600 hover:text-orange-600 flex items-center gap-1 transition-colors" title="Sao chép mã">
                                            <span class="material-symbols-outlined text-sm">content_copy</span>
                                            <span>Lấy mã</span>
                                        </button>
                                    </div>
                                @endif
                                <button type="button" onclick="openPromoModal('promo-modal-{{ $kmHead->id }}')" class="px-3 py-1.5 bg-black/20 hover:bg-black/30 rounded-xl text-xs font-bold transition-colors flex items-center gap-1 cursor-pointer" title="Xem chi tiết bài viết khuyến mãi">
                                    <span>Chi tiết</span>
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                </button>
                                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->id() === $quan->chu_quan_id))
                                    <button type="button" onclick="deletePublicVoucher('{{ $kmHead->id }}', '{{ $kmHead->ma_voucher ?: $kmHead->tieu_de }}')" class="px-2.5 py-1.5 bg-red-600/80 hover:bg-red-600 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1 cursor-pointer shadow-sm" title="Xóa voucher này">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                        <span>Xóa</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tabbed Interface -->
                <div class="mb-6">
                    <nav id="venue-tabs" class="flex border-b border-surface-variant gap-5 overflow-x-auto no-scrollbar" aria-label="Điều hướng chi tiết quán">
                        <a href="#menu-section" aria-current="location" class="tab-active py-3 font-label-md text-xs sm:text-sm whitespace-nowrap px-1">THỰC ĐƠN</a>
                        @if($quan->khuyenMaiDangChay && $quan->khuyenMaiDangChay->isNotEmpty())
                            <a href="#promotions-section" class="text-on-surface-variant hover:text-primary py-3 font-label-md text-xs sm:text-sm whitespace-nowrap px-1 transition-colors flex items-center gap-1.5">
                                ƯU ĐÃI
                                <span class="bg-gradient-to-r from-orange-500 to-rose-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold shadow-sm animate-pulse">
                                    {{ $quan->khuyenMaiDangChay->count() }}
                                </span>
                            </a>
                        @endif
                        <a href="#reviews-section" class="text-on-surface-variant hover:text-primary py-3 font-label-md text-xs sm:text-sm whitespace-nowrap px-1 transition-colors">ĐÁNH GIÁ</a>
                        <a href="#info-section" class="text-on-surface-variant hover:text-primary py-3 font-label-md text-xs sm:text-sm whitespace-nowrap px-1 transition-colors">THÔNG TIN CHI TIẾT</a>
                        <a href="#gallery-section" class="text-on-surface-variant hover:text-primary py-3 font-label-md text-xs sm:text-sm whitespace-nowrap px-1 transition-colors">HÌNH ẢNH</a>
                    </nav>

                    <!-- Menu Section -->
                    <div id="menu-section" class="venue-section-anchor mt-5 space-y-6">
                        @if(isset($quan->danhMucMenu) && $quan->danhMucMenu->count() > 0)
                            @foreach($quan->danhMucMenu as $danhMuc)
                                <div>
                                    <h3 class="font-title-md text-sm sm:text-base text-on-background mb-3 flex items-center gap-2 min-w-0 break-words [overflow-wrap:anywhere]">
                                        <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                                        {{ $danhMuc->ten_danh_muc }}
                                    </h3>
                                    <div class="venue-menu-grid">
                                        @foreach($danhMuc->monAn as $monAn)
                                        @if($monAn->shopeefood_url)
                                        <a href="{{ $monAn->shopeefood_url }}" target="_blank" rel="noopener noreferrer" aria-label="Đặt {{ $monAn->ten_mon }} trên ShopeeFood" class="venue-menu-card bg-surface-card rounded-lg shadow-[0px_3px_12px_rgba(0,0,0,0.06)] hover:shadow-md transition-shadow cursor-pointer">
                                        @else
                                        <div class="venue-menu-card bg-surface-card rounded-lg shadow-[0px_3px_12px_rgba(0,0,0,0.06)] hover:shadow-md transition-shadow">
                                        @endif
                                            <div class="venue-menu-image">
                                                <img class="w-full h-full object-cover" loading="lazy" alt="{{ $monAn->ten_mon }}" src="{{ $monAn->hinh_anh_url ?: 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=300&q=80' }}"/>
                                            </div>
                                            <div class="flex flex-col justify-between flex-1 min-w-0">
                                                <div>
                                                    <h4 class="text-xs sm:text-sm font-bold leading-snug break-words [overflow-wrap:anywhere]">{{ $monAn->ten_mon }}</h4>
                                                    <p class="text-on-surface-variant text-[10px] sm:text-xs leading-snug mt-1 break-words [overflow-wrap:anywhere]">{{ $monAn->mo_ta }}</p>
                                                </div>
                                                <div class="flex justify-between items-center gap-1 mt-1">
                                                    <span class="text-primary font-bold text-xs sm:text-sm">{{ number_format($monAn->gia, 0, ',', '.') }}đ</span>
                                                    @if($monAn->shopeefood_url)
                                                        <span title="Đặt trên ShopeeFood" class="venue-menu-order-icon">
                                                            <span class="material-symbols-outlined text-base">shopping_bag</span>
                                                        </span>
                                                    @else
                                                        <span class="venue-menu-order-icon" title="Chưa có link ShopeeFood">
                                                            <span class="material-symbols-outlined text-base">shopping_bag</span>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @if($monAn->shopeefood_url)
                                        </a>
                                        @else
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-gray-500 py-12 bg-gray-50 rounded-2xl border border-gray-100">
                                <span class="material-symbols-outlined text-5xl mb-2 text-gray-300 block">restaurant_menu</span>
                                <p>Quán chưa cập nhật thực đơn.</p>
                            </div>
                        @endif
                    </div>

                    @if($quan->khuyenMaiDangChay && $quan->khuyenMaiDangChay->isNotEmpty())
                    <section id="promotions-section" hidden class="venue-section-anchor mt-5 space-y-6">
                        <!-- DANH SÁCH CÁC CHƯƠNG TRÌNH KHUYẾN MÃI -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-orange-500 text-xl">local_fire_department</span>
                                    Danh sách ưu đãi đang áp dụng ({{ $quan->khuyenMaiDangChay->count() }})
                                </h2>
                                <span class="text-xs text-gray-500">Áp dụng trực tiếp tại quán hoặc khi liên hệ</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($quan->khuyenMaiDangChay as $km)
                                    <div class="rounded-2xl border-2 border-orange-100 bg-white p-5 shadow-sm hover:shadow-md transition-all relative overflow-hidden flex flex-col justify-between">
                                        <div class="absolute -top-3 -right-3 w-16 h-16 bg-gradient-to-br from-orange-400 to-rose-400 opacity-10 rounded-full pointer-events-none"></div>

                                        <div>
                                            <div class="flex items-start justify-between gap-2 mb-2">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-600 border border-orange-200">
                                                    🏷️ {{ $km->gia_tri_khuyen_mai ?: 'Ưu Đãi Đặc Biệt' }}
                                                </span>
                                                @if($km->ngay_ket_thuc)
                                                    <span class="text-[11px] text-gray-500 flex items-center gap-1 font-medium">
                                                        <span class="material-symbols-outlined text-xs">schedule</span>
                                                        HSD: {{ $km->ngay_ket_thuc->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-xs">all_inclusive</span>
                                                        Đang diễn ra
                                                    </span>
                                                @endif
                                            </div>

                                            <h3 class="font-bold text-gray-900 text-base leading-snug">{{ $km->tieu_de }}</h3>
                                            @if($km->slogan)
                                                <p class="text-xs text-orange-600 font-medium italic mt-1">"{{ $km->slogan }}"</p>
                                            @endif

                                            @if($km->dieu_kien_ap_dung || $km->the_le)
                                                <div class="mt-3 bg-gray-50 rounded-xl p-2.5 text-xs text-gray-600 space-y-1 border border-gray-100">
                                                    <span class="font-bold text-gray-700 flex items-center gap-1 text-[11px]">
                                                        <span class="material-symbols-outlined text-xs text-gray-500">info</span> Điều kiện áp dụng:
                                                    </span>
                                                    <p class="text-[11px] leading-relaxed line-clamp-2">{{ $km->dieu_kien_ap_dung ?: $km->the_le }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2">
                                            @if($km->ma_voucher)
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <div class="px-2.5 py-1 bg-dashed border border-dashed border-orange-300 rounded-lg bg-orange-50/50">
                                                        <span class="font-mono font-black text-orange-600 text-xs tracking-wider">{{ $km->ma_voucher }}</span>
                                                    </div>
                                                    <button type="button" onclick="copyCustomerVoucher('{{ $km->ma_voucher }}')" class="p-1.5 px-2.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold flex items-center gap-1 shadow-sm transition-colors cursor-pointer" title="Sao chép mã voucher">
                                                        <span class="material-symbols-outlined text-xs">content_copy</span>
                                                        <span>Copy</span>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-500 italic">Áp dụng trực tiếp tại quầy</span>
                                            @endif

                                            {{-- Nút Xem chi tiết bài đăng voucher --}}
                                            <div class="flex items-center gap-1.5 ml-auto">
                                                <button type="button" onclick="openPromoModal('promo-modal-{{ $km->id }}')" class="px-3 py-1.5 rounded-lg border border-orange-200 text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100 text-xs font-bold flex items-center gap-1.5 transition-all cursor-pointer shadow-2xs hover:shadow-xs">
                                                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                                                    <span>Xem chi tiết</span>
                                                </button>
                                                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->id() === $quan->chu_quan_id))
                                                    <button type="button" onclick="deletePublicVoucher('{{ $km->id }}', '{{ $km->ma_voucher ?: $km->tieu_de }}')" class="px-2.5 py-1.5 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold flex items-center gap-1 transition-all cursor-pointer shadow-2xs" title="Xóa voucher này">
                                                        <span class="material-symbols-outlined text-[15px]">delete</span>
                                                        <span>Xóa</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal Xem Chi Tiết Bài Đăng Khuyến Mãi --}}
                                    <div id="promo-modal-{{ $km->id }}" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden transition-opacity" onclick="if(event.target === this) closePromoModal('promo-modal-{{ $km->id }}')">
                                        <div class="bg-white rounded-3xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl border border-gray-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                                            {{-- Modal Header --}}
                                            <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50/50 flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-sm">
                                                        <span class="material-symbols-outlined text-lg">campaign</span>
                                                    </div>
                                                    <div>
                                                        <h3 class="font-bold text-gray-900 text-sm sm:text-base leading-tight">Chi Tiết Bài Đăng Khuyến Mãi</h3>
                                                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $quan->ten_quan }}</p>
                                                    </div>
                                                </div>
                                                <button type="button" onclick="closePromoModal('promo-modal-{{ $km->id }}')" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer shadow-2xs">
                                                    <span class="material-symbols-outlined text-lg">close</span>
                                                </button>
                                            </div>

                                            {{-- Modal Body (Scrollable) --}}
                                            <div class="p-5 overflow-y-auto space-y-4 text-xs">
                                                {{-- Title & Slogan --}}
                                                <div>
                                                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-100 text-orange-700 border border-orange-200">
                                                            🏷️ {{ $km->gia_tri_khuyen_mai ?: 'Ưu Đãi Đặc Biệt' }}
                                                        </span>
                                                        @if($km->mon_ap_dung)
                                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-700">
                                                                Món: {{ $km->mon_ap_dung }}
                                                            </span>
                                                        @endif
                                                        @if($km->ngay_ket_thuc)
                                                            <span class="text-[11px] text-gray-500 font-medium ml-auto flex items-center gap-1">
                                                                <span class="material-symbols-outlined text-xs">schedule</span>
                                                                HSD: {{ $km->ngay_ket_thuc->format('d/m/Y') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <h4 class="font-black text-gray-900 text-base leading-snug">{{ $km->tieu_de }}</h4>
                                                    @if($km->slogan)
                                                        <p class="text-xs text-orange-600 font-semibold italic mt-1">"{{ $km->slogan }}"</p>
                                                    @endif
                                                </div>

                                                {{-- Voucher Code Box if available --}}
                                                @if($km->ma_voucher)
                                                    <div class="p-3 bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl border border-dashed border-orange-300 flex items-center justify-between gap-3">
                                                        <div>
                                                            <p class="text-[10px] uppercase font-bold text-orange-600 tracking-wider">Mã Voucher Ưu Đãi</p>
                                                            <p class="font-mono font-black text-lg text-gray-900 mt-0.5 tracking-wider">{{ $km->ma_voucher }}</p>
                                                        </div>
                                                        <button type="button" onclick="copyCustomerVoucher('{{ $km->ma_voucher }}')" class="px-3.5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-colors cursor-pointer">
                                                            <span class="material-symbols-outlined text-sm">content_copy</span>
                                                            <span>Lấy Mã</span>
                                                        </button>
                                                    </div>
                                                @endif

                                                {{-- Full Post Content --}}
                                                <div>
                                                    <div class="flex items-center justify-between mb-1.5">
                                                        <label class="font-bold text-gray-900 text-xs flex items-center gap-1">
                                                            <span class="material-symbols-outlined text-sm text-orange-500">article</span>
                                                            Nội dung bài viết quảng bá:
                                                        </label>
                                                        <button type="button" onclick="copyPromoArticle('promo-article-{{ $km->id }}', this)" class="text-[11px] font-bold text-orange-600 hover:text-orange-800 flex items-center gap-1 cursor-pointer transition-colors">
                                                            <span class="material-symbols-outlined text-xs">content_copy</span>
                                                            <span>Sao chép bài viết</span>
                                                        </button>
                                                    </div>
                                                    <div id="promo-article-{{ $km->id }}" class="bg-gray-50 border border-gray-200 rounded-2xl p-4 text-xs text-gray-800 leading-relaxed whitespace-pre-line font-sans select-text">
                                                        {{ $km->noi_dung ?: $km->noi_dung_bai_dang }}
                                                    </div>
                                                </div>

                                                {{-- Terms & Conditions --}}
                                                @if($km->the_le || $km->dieu_kien_ap_dung)
                                                    <div>
                                                        <label class="font-bold text-gray-900 text-xs flex items-center gap-1 mb-1.5">
                                                            <span class="material-symbols-outlined text-sm text-gray-500">gavel</span>
                                                            Thể lệ & Điều kiện áp dụng:
                                                        </label>
                                                        <div class="bg-amber-50/50 border border-amber-200/60 rounded-2xl p-3.5 text-xs text-gray-700 leading-relaxed whitespace-pre-line">
                                                            {{ $km->the_le ?: $km->dieu_kien_ap_dung }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Modal Footer --}}
                                            <div class="p-3.5 border-t border-gray-100 bg-gray-50/80 flex items-center justify-end gap-2">
                                                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->id() === $quan->chu_quan_id))
                                                    <button type="button" onclick="deletePublicVoucher('{{ $km->id }}', '{{ $km->ma_voucher ?: $km->tieu_de }}', 'promo-modal-{{ $km->id }}')" class="px-3.5 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold text-xs flex items-center gap-1.5 transition-colors cursor-pointer mr-auto" title="Xóa vĩnh viễn voucher này">
                                                        <span class="material-symbols-outlined text-sm">delete</span>
                                                        <span>Xóa voucher này</span>
                                                    </button>
                                                @endif
                                                <button type="button" onclick="closePromoModal('promo-modal-{{ $km->id }}')" class="px-4 py-2 rounded-xl text-gray-700 font-bold text-xs hover:bg-gray-200 transition-colors cursor-pointer">
                                                    Đóng
                                                </button>
                                                @if($km->ma_voucher)
                                                    <button type="button" onclick="copyCustomerVoucher('{{ $km->ma_voucher }}')" class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-colors cursor-pointer">
                                                        <span class="material-symbols-outlined text-sm">content_copy</span>
                                                        <span>Lấy mã voucher</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                    @endif
                    <section id="reviews-section" hidden class="venue-section-anchor mt-7 border-t border-surface-variant pt-4">
                        @php
                            $soDanhGia = $quan->danhGia->count();
                            $diemTrungBinh = $soDanhGia ? $quan->danhGia->avg('so_sao') : 0;
                        @endphp
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                            <h2 class="text-sm font-bold">Đánh giá ({{ $soDanhGia }})</h2>
                            @if($soDanhGia)
                                <span class="text-xs font-bold text-primary">★ {{ number_format($diemTrungBinh, 1) }} / 5</span>
                            @endif
                        </div>

                        @if(session('success'))
                            <p class="mb-3 rounded-lg bg-green-50 px-3 py-2 text-xs text-green-700">{{ session('success') }}</p>
                        @endif

                        @auth
                            <form method="POST" action="{{ route('quan.danh-gia.store', $quan->slug) }}" class="mb-5 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                                @csrf
                                <fieldset>
                                    <legend class="mb-2 text-xs font-bold">{{ $danhGiaCuaToi ? 'Sửa đánh giá của bạn' : 'Chọn số sao' }}</legend>
                                    <div id="rating-stars" class="flex justify-start gap-1" aria-label="Chọn số sao từ 1 đến 5">
                                        @for($sao = 1; $sao <= 5; $sao++)
                                            <input class="rating-choice sr-only" type="radio" id="rating-{{ $sao }}" name="so_sao" value="{{ $sao }}" required {{ (int) old('so_sao', $danhGiaCuaToi?->so_sao) === $sao ? 'checked' : '' }}>
                                            <label for="rating-{{ $sao }}" data-rating="{{ $sao }}" class="cursor-pointer text-2xl leading-none text-gray-300 transition-colors hover:text-amber-400" title="{{ $sao }} sao">★</label>
                                        @endfor
                                    </div>
                                </fieldset>
                                @error('so_sao') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                <label for="review-comment" class="mb-1 mt-3 block text-xs font-bold">Bình luận</label>
                                <textarea id="review-comment" name="binh_luan" rows="3" maxlength="2000" required class="w-full rounded-lg border border-gray-200 p-3 text-sm outline-none focus:border-primary" placeholder="Chia sẻ trải nghiệm của bạn...">{{ old('binh_luan', $danhGiaCuaToi?->binh_luan) }}</textarea>
                                @error('binh_luan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                <button type="submit" class="mt-3 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white hover:brightness-110">{{ $danhGiaCuaToi ? 'Cập nhật đánh giá' : 'Gửi đánh giá' }}</button>
                            </form>
                        @else
                            <p class="mb-4 rounded-lg bg-gray-50 p-3 text-xs text-on-surface-variant">
                                <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Đăng nhập</a> để chọn số sao và viết bình luận.
                            </p>
                        @endauth

                        <div class="space-y-3">
                            @forelse($quan->danhGia->sortByDesc('updated_at') as $danhGia)
                                <article class="rounded-lg border border-gray-100 bg-white p-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <h3 class="text-xs font-bold">{{ $danhGia->nguoiDung?->ho_ten ?? 'Thành viên' }}</h3>
                                        <time class="text-[10px] text-gray-400" datetime="{{ $danhGia->updated_at->timezone('Asia/Ho_Chi_Minh')->toISOString() }}" title="Thời gian cập nhật theo giờ Việt Nam">{{ $danhGia->updated_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</time>
                                    </div>
                                    <p class="mt-1 text-xs text-amber-500" aria-label="{{ $danhGia->so_sao }} trên 5 sao">{{ str_repeat('★', $danhGia->so_sao) }}{{ str_repeat('☆', 5 - $danhGia->so_sao) }}</p>
                                    <p class="review-comment-text mt-1 min-w-0 whitespace-pre-line text-sm text-on-surface-variant">{{ $danhGia->binh_luan }}</p>
                                    @if($danhGia->phan_hoi)
                                        <div class="mt-3 rounded-lg bg-orange-50 p-3">
                                            <p class="text-xs font-bold text-primary">Phản hồi của quán</p>
                                            <p class="mt-1 whitespace-pre-line break-words text-sm text-on-surface-variant [overflow-wrap:anywhere]">{{ $danhGia->phan_hoi }}</p>
                                            @if($danhGia->phan_hoi_luc)
                                                <time class="mt-1 block text-[10px] text-gray-400">{{ $danhGia->phan_hoi_luc->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</time>
                                            @endif
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <p class="text-xs text-on-surface-variant">Chưa có đánh giá. Hãy là người đầu tiên chia sẻ trải nghiệm!</p>
                            @endforelse
                        </div>
                    </section>
                    <section id="info-section" hidden class="venue-section-anchor mt-7 border-t border-surface-variant pt-4">
                        <h2 class="text-sm font-bold mb-2">Thông tin chi tiết</h2>
                        <h3 class="font-bold text-xs mb-1">Giới thiệu</h3>
                        <p class="text-on-surface-variant leading-relaxed break-words [overflow-wrap:anywhere]">{{ $quan->mo_ta ?: 'Chưa có thông tin giới thiệu.' }}</p>
                        <div class="mt-3 space-y-2 text-xs text-on-surface-variant">
                            <p><strong class="text-on-background">Địa chỉ:</strong> {{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_phuong_xa }}, {{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}</p>
                            @if($quan->so_dien_thoai)
                                <p><strong class="text-on-background">Điện thoại:</strong> <a class="text-primary hover:underline" href="tel:{{ $quan->so_dien_thoai }}">{{ $quan->so_dien_thoai }}</a></p>
                            @endif
                            @if($quan->email)
                                <p><strong class="text-on-background">Email:</strong> {{ $quan->email }}</p>
                            @endif
                        </div>
                        @if($quan->tiktok_url)
                            <a href="{{ $quan->tiktok_url }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-1.5 text-primary hover:underline font-bold text-xs">
                                <span class="material-symbols-outlined text-base">play_circle</span>
                                Xem review trên TikTok
                            </a>
                        @endif
                    </section>
                </div>
            </div>

            <!-- Sticky Sidebar -->
            <aside class="venue-sidebar">
                <div class="sticky-sidebar space-y-3">
                    <!-- CTA Card -->
                    <div class="venue-side-card bg-surface-card rounded-xl p-4 shadow-[0px_6px_20px_rgba(0,0,0,0.08)] border border-primary/10 overflow-hidden relative">
                        <h3 class="font-title-md text-sm font-bold text-on-background mb-2">Giao hàng tận nơi</h3>
                        <p class="text-on-surface-variant text-[11px] leading-relaxed mb-3">Nhấn để mở gian hàng ShopeeFood chính thức của quán.</p>
                        @if(filled($quan->shopeefood_url))
                            <a href="{{ $quan->shopeefood_url }}" target="_blank" rel="noopener noreferrer" class="w-full bg-[#EE4D2D] text-white py-2.5 px-3 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5 hover:brightness-110 transition-all shadow-sm mb-2">
                                <span class="material-symbols-outlined text-base">shopping_bag</span>
                                Đặt ngay trên ShopeeFood
                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                            </a>
                        @else
                            <div class="w-full rounded-lg bg-gray-100 px-3 py-2.5 text-center text-xs text-gray-500" role="status">
                                Quán chưa cập nhật liên kết ShopeeFood
                            </div>
                        @endif
                    </div>

                    <!-- Map Card -->
                    @php
                        $hasValidCoords = is_numeric($quan->vi_do) && is_numeric($quan->kinh_do) && abs((float)$quan->vi_do) <= 90 && abs((float)$quan->kinh_do) <= 180;
                        $fullAddrQuery = urlencode(trim($quan->ten_quan . ', ' . $quan->dia_chi_chi_tiet . ', ' . $quan->ten_phuong_xa . ', ' . $quan->ten_quan_huyen . ', ' . $quan->ten_tinh_thanh, ', '));
                        $mapEmbedQuery = $hasValidCoords ? ((float)$quan->vi_do . ',' . (float)$quan->kinh_do) : $fullAddrQuery;
                        $directionsUrl = $hasValidCoords
                            ? 'https://www.google.com/maps/dir/?api=1&destination=' . ((float)$quan->vi_do . ',' . (float)$quan->kinh_do)
                            : 'https://maps.google.com/?q=' . $fullAddrQuery;
                    @endphp
                    <div class="venue-side-card bg-surface-card rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-gray-100">
                        <div class="venue-map-frame h-36 w-full bg-surface-variant relative">
                            <iframe 
                                src="https://maps.google.com/maps?q={{ $mapEmbedQuery }}&t=&z=16&ie=UTF8&iwloc=&output=embed" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                        <div class="p-3 bg-white">
                            <div class="flex flex-col gap-2">
                                <div class="flex justify-between items-center gap-1">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wide">Vị trí & Chỉ đường</span>
                                    <a href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer" class="text-primary font-bold text-[10.5px] flex items-center gap-1 hover:underline">
                                        Chỉ đường Google Maps <span class="material-symbols-outlined text-xs">directions</span>
                                    </a>
                                </div>
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined text-gray-400 text-base shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">location_on</span>
                                    <p class="text-[10px] text-gray-700 leading-snug">
                                        {{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_phuong_xa }}, {{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- More Information Card -->
                    <div class="venue-side-card bg-surface-card rounded-xl p-4 shadow-[0px_4px_20px_rgba(0,0,0,0.05)]">
                        <h4 class="text-[10px] font-bold text-on-surface-variant mb-3 uppercase tracking-wider">Tiện ích của quán</h4>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-on-surface">
                                <span class="material-symbols-outlined text-secondary">category</span>
                                <span class="font-body-sm text-body-sm">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}</span>
                            </li>
                            @if($quan->email)
                            <li class="flex items-center gap-3 text-on-surface">
                                <span class="material-symbols-outlined text-secondary">mail</span>
                                <span class="font-body-sm text-body-sm">{{ $quan->email }}</span>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </aside>
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
    </script>

    <script>
        const tabs = document.querySelectorAll('#venue-tabs a[href^="#"]');
        const ratingInputs = document.querySelectorAll('.rating-choice');

        const updateRatingStars = selectedRating => {
            document.querySelectorAll('#rating-stars label[data-rating]').forEach(star => {
                const isSelected = Number(star.dataset.rating) <= Number(selectedRating || 0);
                star.classList.toggle('text-amber-400', isSelected);
                star.classList.toggle('text-gray-300', !isSelected);
            });
        };

        ratingInputs.forEach(input => {
            input.addEventListener('change', () => updateRatingStars(input.value));
        });
        updateRatingStars(document.querySelector('.rating-choice:checked')?.value);

        const setActiveTab = hash => {
            const panelId = hash.replace(/^#/, '');
            const panelExists = Array.from(tabs).some(tab => tab.getAttribute('href') === `#${panelId}`);
            const selectedId = panelExists ? panelId : 'menu-section';
            tabs.forEach(tab => {
                const isActive = tab.getAttribute('href') === `#${selectedId}`;
                tab.classList.toggle('tab-active', isActive);
                tab.classList.toggle('text-on-surface-variant', !isActive);
                tab.classList.toggle('hover:text-primary', !isActive);
                if (isActive) {
                    tab.setAttribute('aria-current', 'location');
                } else {
                    tab.removeAttribute('aria-current');
                }
            });

            tabs.forEach(tab => {
                const panel = document.querySelector(tab.getAttribute('href'));
                if (panel && tab.getAttribute('href') !== '#gallery-section') {
                    panel.hidden = tab.getAttribute('href') !== `#${selectedId}`;
                }
            });
        };

        tabs.forEach(tab => {
            tab.addEventListener('click', function(event) {
                event.preventDefault();
                setActiveTab(this.getAttribute('href'));
                if (this.getAttribute('href') === '#gallery-section') {
                    document.getElementById('gallery-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
        window.addEventListener('hashchange', () => setActiveTab(window.location.hash));
        setActiveTab(window.location.hash || '#menu-section');

        function showPromoTab() {
            const promoTab = document.querySelector('a[href="#promotions-section"]');
            if (promoTab) {
                promoTab.click();
                promoTab.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function copyCustomerVoucher(code) {
            if (!code) return;
            navigator.clipboard.writeText(code).then(() => {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-2 border border-orange-500/30 text-xs font-bold animate-bounce';
                toast.innerHTML = `<span class="text-base text-orange-400">🎉</span> Đã copy mã voucher: <span class="font-mono text-orange-300 font-bold">${code}</span>! Áp dụng ngay khi thanh toán!`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }).catch(() => {   
                prompt('Mã khuyến mãi:', code);
            });
        }

        async function deletePublicVoucher(id, title = '', modalId = null) {
            if (!confirm(`Bạn có chắc chắn muốn xóa voucher/ưu đãi "${title || 'này'}" không? Sau khi xóa sẽ không thể phục hồi!`)) return;

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
                    if (modalId) {
                        closePromoModal(modalId);
                    }
                    const toast = document.createElement('div');
                    toast.className = 'fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-2 border border-red-500/30 text-xs font-bold animate-bounce';
                    toast.innerHTML = `<span class="text-base text-red-400">🗑️</span> Đã xóa voucher thành công!`;
                    document.body.appendChild(toast);
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể xóa voucher.'));
                }
            } catch (err) {
                alert('Lỗi kết nối khi xóa voucher.');
            }
        }

        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(160, 65, 0, 0.7); }
                70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(160, 65, 0, 0); }
                100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(160, 65, 0, 0); }
            }
            .pulse-animation {
                animation: pulse 2s infinite;
            }
        `;
        document.head.appendChild(style);

        window.toggleSaveDetail = async function(btn, quanId) {
            const detailQuanName = @json($quan->ten_quan);
            const detailQuanImg = @json($quan->hinhAnh->first()->url ?? ($quan->anh_bia_url ?: null));

            @guest
                if (window.showToast) {
                    window.showToast({
                        eyebrow: 'Lưu địa điểm yêu thích',
                        title: 'Đăng nhập để lưu quán',
                        message: `Đăng nhập ngay để lưu "${detailQuanName}" vào bộ sưu tập của bạn.`,
                        type: 'warning',
                        image: detailQuanImg,
                        actionText: 'Đăng nhập ngay',
                        actionUrl: "{{ route('login') }}"
                    });
                } else {
                    window.location.href = "{{ route('login') }}";
                }
                return;
            @endguest

            if (btn.dataset.loading === 'true') return;

            const icon = btn.querySelector('.material-symbols-outlined');
            const textEl = document.getElementById('detail-save-text');
            const isIconFilled = (el) => el && /['"]?FILL['"]?\s*1/i.test(el.style.fontVariationSettings || el.getAttribute('style') || '');
            const currentlySaved = btn.dataset.saved !== undefined
                ? btn.dataset.saved === '1'
                : isIconFilled(icon);
            const targetSaved = !currentlySaved;

            const applyDetailSaveState = (state) => {
                btn.dataset.saved = state ? '1' : '0';
                btn.title = state ? 'Bỏ lưu quán' : 'Lưu quán';
                if (icon) {
                    icon.style.fontVariationSettings = state ? "'FILL' 1" : "'FILL' 0";
                }
                if (textEl) {
                    textEl.textContent = state ? 'Đã lưu' : 'Lưu quán';
                }
            };

            // 1. Optimistic visual update (0ms delay)
            applyDetailSaveState(targetSaved);
            btn.style.transform = 'scale(1.15)';
            setTimeout(() => { btn.style.transform = 'scale(1)'; }, 180);

            // 2. Immediate Toast
            if (window.showToast) {
                if (targetSaved) {
                    window.showToast({
                        eyebrow: 'Bộ sưu tập yêu thích',
                        title: `Đã lưu "${detailQuanName}"`,
                        message: 'Địa điểm đã được thêm vào danh sách quán yêu thích của bạn.',
                        type: 'saved',
                        image: detailQuanImg,
                        actionText: 'Xem quán đã lưu',
                        actionUrl: "{{ route('quan-da-luu.index') }}"
                    });
                } else {
                    window.showToast({
                        eyebrow: 'Đã cập nhật danh sách',
                        title: `Đã bỏ lưu "${detailQuanName}"`,
                        message: 'Địa điểm đã được gỡ khỏi danh sách yêu thích.',
                        type: 'unsaved',
                        image: detailQuanImg
                    });
                }
            }

            btn.dataset.loading = 'true';

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(`/quan-da-luu/${quanId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });

                if (res.status === 401) {
                    applyDetailSaveState(currentlySaved);
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                const data = await res.json();
                if (data.success) {
                    const finalSaved = data.status === 'saved';
                    applyDetailSaveState(finalSaved);
                } else {
                    applyDetailSaveState(currentlySaved);
                    if (window.showToast) {
                        window.showToast({
                            title: 'Không thể thực hiện',
                            message: data.message || 'Đã có lỗi xảy ra, vui lòng thử lại.',
                            type: 'error'
                        });
                    }
                }
            } catch (err) {
                console.error(err);
                applyDetailSaveState(currentlySaved);
                if (window.showToast) {
                    window.showToast({
                        title: 'Lỗi kết nối',
                        message: 'Không thể gửi yêu cầu đến máy chủ, vui lòng thử lại.',
                        type: 'error'
                    });
                }
            } finally {
                btn.dataset.loading = 'false';
            }
        };

        function openPromoModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closePromoModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function copyPromoArticle(elementId, btn) {
            const el = document.getElementById(elementId);
            if (!el) return;
            const text = el.innerText || el.textContent;
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = `<span class="material-symbols-outlined text-xs text-emerald-600">check</span><span class="text-emerald-600">Đã sao chép!</span>`;
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                }, 2000);
            }).catch(() => {
                alert('Không thể tự động sao chép, bạn vui lòng chọn bài viết và copy.');
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id^="promo-modal-"]:not(.hidden)').forEach(modal => {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            }
        });
    </script>
@endpush
