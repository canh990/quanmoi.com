@extends('layouts.app')

@section('title', 'Phở Chào Hà Nội - 123 Lê Lợi, Quận 1, TP.HCM | Quán Mới')

@push('styles')
    {{-- SEO Meta Tags & OpenGraph --}}
    <meta name="description" content="Phở Chào Hà Nội tại 123 Đường Lê Lợi, Phường Bến Thành, Quận 1, TP. HCM. Đánh giá 4.8★ từ 500+ khách hàng. Xem thực đơn phở bò tái lăn đậm đà, bảng giá & chỉ đường." />
    <meta name="keywords" content="phở chào hà nội, phở ngon quận 1, phở bò tái lăn, quán ăn quận 1, địa điểm ăn uống hcm" />
    <link rel="canonical" href="{{ url()->current() }}" />

    {{-- OpenGraph Meta Tags --}}
    <meta property="og:title" content="Phở Chào Hà Nội - Quán Phở Bò Tái Lăn Ngon Tại Quận 1" />
    <meta property="og:description" content="Thưởng thức phở bò tái lăn chuẩn vị Hà Thành tại Quận 1. Đánh giá 4.8★ từ 500+ thực khách." />
    <meta property="og:type" content="restaurant" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="https://lh3.googleusercontent.com/aida-public/AB6AXuBZT9uAiYKcdy3HvCa0HWpy4HYJInkXNudXh90FI143Ij_XOb8uCHMMeOppbch7HDqbJ3McTaIS4mpK4x1kOcuypIqixC3bZz7TRpDYmWUSs-ps8wunPmONXD8fHD-FP152tDpYtEaOejKiuOcVEWMGsMjpTQuvBOg4Uf9I0cWwed8HUVNVYyMQWlnleUG0U4JRo3LuE70KW0VZkLnW0IsDzgYgBUoIGJPGCaZ6BvAe2Jz1BG28uUzv" />

    {{-- Schema.org Restaurant JSON-LD Structured Data for Google Rich Snippets --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Restaurant",
      "name": "Phở Chào Hà Nội",
      "image": [
        "https://lh3.googleusercontent.com/aida-public/AB6AXuBZT9uAiYKcdy3HvCa0HWpy4HYJInkXNudXh90FI143Ij_XOb8uCHMMeOppbch7HDqbJ3McTaIS4mpK4x1kOcuypIqixC3bZz7TRpDYmWUSs-ps8wunPmONXD8fHD-FP152tDpYtEaOejKiuOcVEWMGsMjpTQuvBOg4Uf9I0cWwed8HUVNVYyMQWlnleUG0U4JRo3LuE70KW0VZkLnW0IsDzgYgBUoIGJPGCaZ6BvAe2Jz1BG28uUzv"
      ],
      "servesCuisine": ["Vietnamese", "Pho"],
      "priceRange": "35.000đ - 85.000đ",
      "address": {
        "@@type": "PostalAddress",
        "streetAddress": "123 Đường Lê Lợi, Phường Bến Thành",
        "addressLocality": "Quận 1",
        "addressRegion": "TP. Hồ Chí Minh",
        "addressCountry": "VN"
      },
      "aggregateRating": {
        "@@type": "AggregateRating",
        "ratingValue": "4.8",
        "reviewCount": "524"
      },
      "openingHours": "Mo-Su 06:00-22:00"
    }
    </script>

    <style>
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(2, 200px);
            gap: 12px;
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
            border-bottom: 3px solid #a04100;
            color: #a04100;
        }
    </style>
@endpush


@section('content')
    <main class="mt-20 max-w-7xl mx-auto px-4 md:px-8 pb-20 flex-grow">
        <!-- Hero Gallery Section -->
        <section class="mb-8">
            <div class="bento-grid">
                <div class="bento-item-1 rounded-xl overflow-hidden cursor-pointer group relative">
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-all"></div>
                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBZT9uAiYKcdy3HvCa0HWpy4HYJInkXNudXh90FI143Ij_XOb8uCHMMeOppbch7HDqbJ3McTaIS4mpK4x1kOcuypIqixC3bZz7TRpDYmWUSs-ps8wunPmONXD8fHD-FP152tDpYtEaOejKiuOcVEWMGsMjpTQuvBOg4Uf9I0cWwed8HUVNVYyMQWlnleUG0U4JRo3LuE70KW0VZkLnW0IsDzgYgBUoIGJPGCaZ6BvAe2Jz1BG28uUzv')" title="Không gian Phở Chào Hà Nội"></div>
                </div>
                <div class="bento-item-2 rounded-xl overflow-hidden cursor-pointer group relative">
                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuD2Ck5qL2_TpbcOo7YGVfL4jIzyWsaWPJOLKDfi0oZ7KRgPjfwEjx3LlprQ1c5zoWBwwq3BNDE8s8h_IBpNRJY5PVvyTDdhgy-7Q5HRK1_o0rHP3G5iRdWrMB0YVFQBGQn4KE7XA_nBiEW3soOPvbhB8fOkpV9CxFQT6Bbm8ZlArhLdCx4a10froFQaLxTZatgH_PH2DzxuUOfaVEhgmpPhVtBYqD9HVRQNvXi9n9k5rXIhenIx5tmk')" title="Thịt bò tươi ngon tại Phở Chào"></div>
                </div>
                <div class="bento-item-3 rounded-xl overflow-hidden cursor-pointer group relative">
                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuC3bzrIzaqyG4B_yvZnS4Po8P9iifDNOlmkoZW-CkCRe3OtnGLgU0uzl0qdjB4FDgI1TgyNX21O1P9hfh3SsUrzHSFJW4pWjH3ibrjomvnY5wtcYKOrNP-OruwGf5REKaxsF1IdXYH6kO1PzwhIycGIT8QbgiUZ-skSvJkioA7BbMyMZqIuDVBAebFYczNCNAJM0VKixfDVfLnhdo0plxXmad2kEjdIk01MEUN64cbsrgVlWJQOwaDd')" title="Nước dùng phở bò đậm đà"></div>
                </div>
                <div class="bento-item-4 rounded-xl overflow-hidden cursor-pointer group relative bg-on-background flex items-center justify-center text-white">
                    <div class="w-full h-full bg-cover bg-center opacity-40" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCfnTVIpmnxpE0pdetcfCZ2GMfN5F0yivGeSsRcxiD7rBqLQE74yWLzaFDYm5kFq82e1RHUwK-PhICSALqS2DYMANoWXy_P0OwtlwoShLH7Qph3_oohL6bWg1e45CE6ysjbUE6jUdCAk9Pp7pz33obm5JKvdkL_yhOKl0dhugz0OpJ4SBiZ7eBY7AsUdiEk02wTOhXwHQPlCd48tbnR8l8iVyyeaJyVg4tX1Mg0eRC-N2o5akKrHUpI')"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/40">
                        <span class="material-symbols-outlined text-3xl">grid_view</span>
                        <span class="font-label-md text-label-md mt-1">Xem tất cả (24)</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Layout -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Column -->
            <div class="flex-1">
                <!-- Business Info Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-2">
                        <h1 class="font-headline-lg text-headline-lg text-on-background font-black">Phở Chào Hà Nội - Quán Mới</h1>
                        <span class="material-symbols-outlined text-tick-xanh fill-icon text-xl" title="Verified">verified</span>
                    </div>
                    <p class="text-on-surface-variant flex items-center gap-1 mb-4 font-body-lg text-body-lg">
                        <span class="material-symbols-outlined text-lg">location_on</span>
                        123 Đường Lê Lợi, Phường Bến Thành, Quận 1, TP. HCM
                    </p>
                    <div class="flex flex-wrap items-center gap-6 py-4 border-y border-surface-variant">
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-primary fill-icon">star</span>
                            <span class="font-bold text-lg">4.8</span>
                            <span class="text-on-surface-variant text-sm">(500+ đánh giá)</span>
                        </div>
                        <div class="flex items-center gap-1 text-on-surface-variant">
                            <span class="material-symbols-outlined">schedule</span>
                            <span class="text-sm">06:00 - 22:00</span>
                            <span class="text-tertiary-container font-semibold ml-2">Đang mở cửa</span>
                        </div>
                        <div class="flex items-center gap-1 text-on-surface-variant">
                            <span class="material-symbols-outlined">payments</span>
                            <span class="text-sm">35.000đ - 85.000đ</span>
                        </div>
                    </div>
                </div>

                <!-- Tabbed Interface -->
                <div class="mb-8">
                    <div class="flex border-b border-surface-variant gap-8 overflow-x-auto no-scrollbar">
                        <button class="tab-active py-4 font-label-md text-label-md whitespace-nowrap px-2">THỰC ĐƠN</button>
                        <button class="text-on-surface-variant hover:text-primary py-4 font-label-md text-label-md whitespace-nowrap px-2 transition-colors">ĐÁNH GIÁ (524)</button>
                        <button class="text-on-surface-variant hover:text-primary py-4 font-label-md text-label-md whitespace-nowrap px-2 transition-colors">THÔNG TIN CHI TIẾT</button>
                        <button class="text-on-surface-variant hover:text-primary py-4 font-label-md text-label-md whitespace-nowrap px-2 transition-colors">HÌNH ẢNH</button>
                    </div>

                    <!-- Menu Section -->
                    <div class="mt-8 space-y-12">
                        <!-- Category 1 -->
                        <div>
                            <h3 class="font-title-md text-title-md text-on-background mb-6 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                                Món Nổi Bật (Best Sellers)
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Menu Item 1 -->
                                <div class="bg-surface-card rounded-xl p-4 shadow-[0px_4px_20px_rgba(0,0,0,0.05)] flex gap-4 hover:shadow-md transition-shadow cursor-pointer">
                                    <div class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0">
                                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCBDG8TL6iX2tC_HLxco_uojhUDGskAbVp9lIbIqe6dDd9Yk2pyE_L3nOgefYju2oS50PjH9HeroRj9Fr9obaVYCkJKo12dFwJxUwUaY-DYcHg4EfvYUVTAmn9On9DItDocI8_IjqDTuZJKYago6XyumHo3t2JXLyh5HAR82dekxZ9-571Oa7MxQXhzlD_ATVQqkwF3Wu8wKNw7CnmKE_hDOGzBFcxu6wA4yiMElpd0l-XgxStF4X7s"/>
                                    </div>
                                    <div class="flex flex-col justify-between flex-1">
                                        <div>
                                            <h4 class="font-body-lg text-body-lg font-bold">Phở Bò Tái Lăn</h4>
                                            <p class="text-on-surface-variant text-sm line-clamp-2">Thịt bò bắp hoa xào tỏi thơm lừng, nước dùng đậm đà hương vị truyền thống.</p>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-primary font-bold">65.000đ</span>
                                            <button class="w-8 h-8 rounded-full bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center hover:bg-primary transition-colors hover:text-white">
                                                <span class="material-symbols-outlined text-xl">add</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Menu Item 2 -->
                                <div class="bg-surface-card rounded-xl p-4 shadow-[0px_4px_20px_rgba(0,0,0,0.05)] flex gap-4 hover:shadow-md transition-shadow cursor-pointer">
                                    <div class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0">
                                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCYTLNbRhKZ41SRc9TdfJ63-lGgYmyiXHImTvn-_IPMY6EcLsr0EjSmoRmiiHih1BgqhZ_1ssXUkHe_al9u7cdlaA1QparzxJBxnVg73UfrIdrRaTW0ZqzzNTtKTqueMns1QmXeg3RyYK0_kLWwXdAGbSnrtq_ohXB_1iA_jMFRDmZq0_J527kH0oI5HcZ66f2ZvWwDc69BwBULqwnTcdFHZ6U_RIY6YYLllnsq2hS7Da2ET_H8jRae"/>
                                    </div>
                                    <div class="flex flex-col justify-between flex-1">
                                        <div>
                                            <h4 class="font-body-lg text-body-lg font-bold">Bánh Mì Đặc Biệt</h4>
                                            <p class="text-on-surface-variant text-sm line-clamp-2">Pate gan nhà làm, giò lụa, xá xíu cùng rau chua thanh mát giòn tan.</p>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-primary font-bold">45.000đ</span>
                                            <button class="w-8 h-8 rounded-full bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center hover:bg-primary transition-colors hover:text-white">
                                                <span class="material-symbols-outlined text-xl">add</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Category 2 -->
                        <div>
                            <h3 class="font-title-md text-title-md text-on-background mb-6 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-secondary rounded-full"></span>
                                Thức Uống
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Menu Item 3 -->
                                <div class="bg-surface-card rounded-xl p-4 shadow-[0px_4px_20px_rgba(0,0,0,0.05)] flex gap-4 hover:shadow-md transition-shadow cursor-pointer">
                                    <div class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0">
                                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDg9gWrMwqgdUrDM9bD_WZS8GZN-UZNE9-DKv_YUe0nK0aQOQ9XFxDgBKPrHhq0_f6H40ZkR-maHM62UtaqqtI23v1Imr2lP0VJhWGHbbDAYVYW3QLNfm5LPEmnr98ZnupgyNFL5WFU9lgSES4cjdiy_pMumKF9pZvlF6tZ-ZuQFJ7WAc848jXssVIe7CZpWV5C6uuTn4pY8W2ZFgl2QGe7E1Gm2N-KYrwhfabBy82_4HTJuXfgYJFj"/>
                                    </div>
                                    <div class="flex flex-col justify-between flex-1">
                                        <div>
                                            <h4 class="font-body-lg text-body-lg font-bold">Cà Phê Sữa Đá</h4>
                                            <p class="text-on-surface-variant text-sm line-clamp-2">Hạt Arabica &amp; Robusta rang mộc, sữa đặc sánh mịn truyền thống.</p>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-primary font-bold">25.000đ</span>
                                            <button class="w-8 h-8 rounded-full bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center hover:bg-primary transition-colors hover:text-white">
                                                <span class="material-symbols-outlined text-xl">add</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Menu Item 4 -->
                                <div class="bg-surface-card rounded-xl p-4 shadow-[0px_4px_20px_rgba(0,0,0,0.05)] flex gap-4 hover:shadow-md transition-shadow cursor-pointer">
                                    <div class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0">
                                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAbxS-tBrU2TB7EyLcshsz_GYUZ2oReudYYpipSo8H8MA_JkpRkpOWvOLjCCGpYQAalY0RfEcShff2aIq2QQJkd6mNqORHHhgG3gOcpOT9jATVk5qe3cVOrcMkKrj__SWguoZoD0k4N-tnlob0jN9Yj59pmnZuVH8bvdY4emK0C0BddNuOhRryiidp33dRRyI9o1Eu7IV1_9OY56YKOSmIP5dUR65KsYEtfIZJI3ueFIETg1vTGtKtI"/>
                                    </div>
                                    <div class="flex flex-col justify-between flex-1">
                                        <div>
                                            <h4 class="font-body-lg text-body-lg font-bold">Trà Sen Nhãn</h4>
                                            <p class="text-on-surface-variant text-sm line-clamp-2">Hạt sen bùi bùi kết hợp cùng thịt nhãn lồng tươi ngọt, thanh lọc cơ thể.</p>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-primary font-bold">35.000đ</span>
                                            <button class="w-8 h-8 rounded-full bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center hover:bg-primary transition-colors hover:text-white">
                                                <span class="material-symbols-outlined text-xl">add</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Sidebar -->
            <aside class="w-full lg:w-96">
                <div class="sticky-sidebar space-y-6">
                    <!-- Shopee Food CTA Card -->
                    <div class="bg-surface-card rounded-xl p-6 shadow-[0px_8px_24px_rgba(0,0,0,0.08)] border border-primary/10 overflow-hidden relative">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-primary/5 rounded-full -mr-8 -mt-8"></div>
                        <h3 class="font-title-md text-title-md text-on-background mb-4">Giao hàng tận nơi</h3>
                        <p class="text-on-surface-variant font-body-sm text-body-sm mb-6">Bạn đang bận? Hãy đặt hàng ngay qua ứng dụng Shopee Food để nhận ưu đãi miễn phí vận chuyển.</p>
                        <button class="w-full bg-[#EE4D2D] text-white py-3 px-4 rounded-xl font-bold flex items-center justify-center gap-2 hover:brightness-110 active:scale-[0.98] transition-all shadow-md">
                            <span class="material-symbols-outlined fill-icon">shopping_bag</span>
                            Đặt ngay trên Shopee Food
                        </button>
                        <div class="mt-4 pt-4 border-t border-surface-variant flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">local_shipping</span>
                                <span class="font-label-md text-label-md text-on-surface-variant">Thời gian giao: 15-30'</span>
                            </div>
                        </div>
                    </div>

                    <!-- Map Card -->
                    <div class="bg-surface-card rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)]">
                        <div class="h-48 w-full bg-surface-variant relative">
                            <div class="w-full h-full bg-cover bg-center">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="bg-primary text-white p-2 rounded-full shadow-lg pulse-animation">
                                        <span class="material-symbols-outlined fill-icon">location_on</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-4">
                                <span class="font-label-md text-label-md text-on-surface-variant">CHỈ ĐƯỜNG</span>
                                <button class="text-primary font-bold text-sm flex items-center gap-1 hover:underline">
                                    Mở Google Maps
                                    <span class="material-symbols-outlined text-sm">open_in_new</span>
                                </button>
                            </div>
                            <div class="flex gap-2">
                                <button class="flex-1 border border-secondary text-secondary py-2 rounded-lg font-label-md text-label-md flex items-center justify-center gap-1 hover:bg-secondary-container transition-colors">
                                    <span class="material-symbols-outlined text-lg">call</span>
                                    Gọi điện
                                </button>
                                <button class="flex-1 border border-secondary text-secondary py-2 rounded-lg font-label-md text-label-md flex items-center justify-center gap-1 hover:bg-secondary-container transition-colors">
                                    <span class="material-symbols-outlined text-lg">share</span>
                                    Chia sẻ
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- More Information Card -->
                    <div class="bg-surface-card rounded-xl p-6 shadow-[0px_4px_20px_rgba(0,0,0,0.05)]">
                        <h4 class="font-label-md text-label-md text-on-surface-variant mb-4 uppercase tracking-wider">Tiện ích của quán</h4>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-on-surface">
                                <span class="material-symbols-outlined text-secondary">wifi</span>
                                <span class="font-body-sm text-body-sm">Wifi miễn phí tốc độ cao</span>
                            </li>
                            <li class="flex items-center gap-3 text-on-surface">
                                <span class="material-symbols-outlined text-secondary">ac_unit</span>
                                <span class="font-body-sm text-body-sm">Máy lạnh &amp; Quạt hơi nước</span>
                            </li>
                            <li class="flex items-center gap-3 text-on-surface">
                                <span class="material-symbols-outlined text-secondary">local_parking</span>
                                <span class="font-body-sm text-body-sm">Chỗ để xe máy miễn phí</span>
                            </li>
                            <li class="flex items-center gap-3 text-on-surface">
                                <span class="material-symbols-outlined text-secondary">credit_card</span>
                                <span class="font-body-sm text-body-sm">Thanh toán chuyển khoản &amp; Thẻ</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        // Micro-interactions for tabs
        const tabs = document.querySelectorAll('button[class*="text-on-surface-variant"]');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const activeTab = document.querySelector('.tab-active');
                if (activeTab) {
                    activeTab.classList.remove('tab-active');
                    activeTab.classList.add('text-on-surface-variant', 'hover:text-primary');
                }
                this.classList.remove('text-on-surface-variant', 'hover:text-primary');
                this.classList.add('tab-active');
            });
        });

        // Simple pulse animation for map pin
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
