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
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(2, minmax(0, 1fr));
            aspect-ratio: 3.05 / 1;
            gap: 8px;
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

@section('content')
    <main class="venue-page-layout mt-4 max-w-7xl mx-auto px-3 sm:px-4 md:px-8 pb-20 flex-grow">

        <!-- Hero Gallery Section -->
        <section id="gallery-section" class="venue-section-anchor mb-5">
            <div class="bento-grid">
                @php
                    $hinhAnhs = $quan->hinhAnh ?? collect();
                    $biaUrl = $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80';
                    $img2 = $hinhAnhs->count() > 0 ? $hinhAnhs[0]->duong_dan : 'https://lh3.googleusercontent.com/aida-public/AB6AXuD2Ck5qL2_TpbcOo7YGVfL4jIzyWsaWPJOLKDfi0oZ7KRgPjfwEjx3LlprQ1c5zoWBwwq3BNDE8s8h_IBpNRJY5PVvyTDdhgy-7Q5HRK1_o0rHP3G5iRdWrMB0YVFQBGQn4KE7XA_nBiEW3soOPvbhB8fOkpV9CxFQT6Bbm8ZlArhLdCx4a10froFQaLxTZatgH_PH2DzxuUOfaVEhgmpPhVtBYqD9HVRQNvXi9n9k5rXIhenIx5tmk';
                    $img3 = $hinhAnhs->count() > 1 ? $hinhAnhs[1]->duong_dan : 'https://lh3.googleusercontent.com/aida-public/AB6AXuC3bzrIzaqyG4B_yvZnS4Po8P9iifDNOlmkoZW-CkCRe3OtnGLgU0uzl0qdjB4FDgI1TgyNX21O1P9hfh3SsUrzHSFJW4pWjH3ibrjomvnY5wtcYKOrNP-OruwGf5REKaxsF1IdXYH6kO1PzwhIycGIT8QbgiUZ-skSvJkioA7BbMyMZqIuDVBAebFYczNCNAJM0VKixfDVfLnhdo0plxXmad2kEjdIk01MEUN64cbsrgVlWJQOwaDd';
                    $img4 = $hinhAnhs->count() > 2 ? $hinhAnhs[2]->duong_dan : 'https://lh3.googleusercontent.com/aida-public/AB6AXuCfnTVIpmnxpE0pdetcfCZ2GMfN5F0yivGeSsRcxiD7rBqLQE74yWLzaFDYm5kFq82e1RHUwK-PhICSALqS2DYMANoWXy_P0OwtlwoShLH7Qph3_oohL6bWg1e45CE6ysjbUE6jUdCAk9Pp7pz33obm5JKvdkL_yhOKl0dhugz0OpJ4SBiZ7eBY7AsUdiEk02wTOhXwHQPlCd48tbnR8l8iVyyeaJyVg4tX1Mg0eRC-N2o5akKrHUpI';
                @endphp
                <a href="{{ $biaUrl }}" data-fancybox="gallery" class="bento-item-1 rounded-xl overflow-hidden cursor-pointer group relative block">
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-all z-10"></div>
                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ $biaUrl }}')"></div>
                </a>
                <a href="{{ $img2 }}" data-fancybox="gallery" class="bento-item-2 rounded-xl overflow-hidden cursor-pointer group relative block">
                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ $img2 }}')"></div>
                </a>
                <a href="{{ $img3 }}" data-fancybox="gallery" class="bento-item-3 rounded-xl overflow-hidden cursor-pointer group relative block">
                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ $img3 }}')"></div>
                </a>
                <a href="{{ $img4 }}" data-fancybox="gallery" class="bento-item-4 rounded-xl overflow-hidden cursor-pointer group relative bg-on-background flex items-center justify-center text-white block">
                    <div class="w-full h-full absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('{{ $img4 }}')"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/40 group-hover:bg-black/20 transition-colors duration-500 z-10">
                        <span class="material-symbols-outlined text-3xl">grid_view</span>
                        <span class="font-label-md text-label-md mt-1">Xem tất cả</span>
                    </div>
                </a>
                
                {{-- Hidden gallery images for lightbox --}}
                @if($hinhAnhs->count() > 3)
                    @for($i = 3; $i < $hinhAnhs->count(); $i++)
                        <a href="{{ $hinhAnhs[$i]->duong_dan }}" data-fancybox="gallery" class="hidden"></a>
                    @endfor
                @endif
            </div>
        </section>

        <!-- Content Layout -->
        <div class="venue-content-layout">
            <!-- Main Column -->
            <div class="min-w-0">
                <!-- Business Info Header -->
                <div class="mb-6">
                    <div class="flex min-w-0 items-start gap-2 mb-1">
                        <h1 class="min-w-0 font-headline-lg text-headline-lg text-on-background font-black break-words [overflow-wrap:anywhere]">{{ $quan->ten_quan }}</h1>
                        @if($quan->is_xac_thuc)
                            <span class="material-symbols-outlined shrink-0 text-tick-xanh fill-icon text-xl" title="Verified">verified</span>
                        @endif
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

                <!-- Tabbed Interface -->
                <div class="mb-6">
                    <nav id="venue-tabs" class="flex border-b border-surface-variant gap-5 overflow-x-auto no-scrollbar" aria-label="Điều hướng chi tiết quán">
                        <a href="#menu-section" aria-current="location" class="tab-active py-3 font-label-md text-xs sm:text-sm whitespace-nowrap px-1">THỰC ĐƠN</a>
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
                        <h3 class="font-title-md text-sm font-bold text-on-background mb-2">Đặt món trên ShopeeFood</h3>
                        <p class="text-on-surface-variant text-[11px] leading-relaxed mb-3">Nhấn để mở gian hàng ShopeeFood chính thức của quán.</p>
                        @if(filled($quan->shopeefood_url))
                            <a href="{{ $quan->shopeefood_url }}" target="_blank" rel="noopener noreferrer" class="w-full bg-[#EE4D2D] text-white py-2.5 px-3 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5 hover:brightness-110 transition-all shadow-sm mb-2">
                                <span class="material-symbols-outlined text-base">shopping_bag</span>
                                Mở ShopeeFood của quán
                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                            </a>
                        @else
                            <div class="w-full rounded-lg bg-gray-100 px-3 py-2.5 text-center text-xs text-gray-500" role="status">
                                Quán chưa cập nhật liên kết ShopeeFood
                            </div>
                        @endif
                    </div>

                    <!-- Map Card -->
                    <div class="venue-side-card bg-surface-card rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-gray-100">
                        <div class="venue-map-frame h-36 w-full bg-surface-variant relative">
                            <iframe 
                                src="https://maps.google.com/maps?q={{ urlencode($quan->ten_quan . ', ' . $quan->dia_chi_chi_tiet . ', ' . $quan->ten_phuong_xa . ', ' . $quan->ten_quan_huyen . ', ' . $quan->ten_tinh_thanh) }}&t=&z=16&ie=UTF8&iwloc=&output=embed" 
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
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wide">Chỉ đường</span>
                                    <a href="https://maps.google.com/?q={{ urlencode($quan->ten_quan . ', ' . $quan->dia_chi_chi_tiet . ', ' . $quan->ten_phuong_xa . ', ' . $quan->ten_quan_huyen . ', ' . $quan->ten_tinh_thanh) }}" target="_blank" rel="noopener noreferrer" class="text-primary font-bold text-[10px] flex items-center gap-1 hover:underline">
                                        Google Maps <span class="material-symbols-outlined text-xs">open_in_new</span>
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
    </script>
@endpush
