@extends('layouts.app')

@section('title', 'Quán Mới - Khám phá tinh hoa ẩm thực')


@section('content')
    <!-- DESKTOP Main Content -->
    <main class="hidden md:block flex-grow">
        <!-- Hero Section -->
        <section class="relative w-full h-[500px] flex items-center justify-center">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAxmwRoMYqrN1heR6D3PvMJVnBs-XHl_Bh5fu5FQ3iWlsce1YbhmhwQyYF7NpVeQEvKyyAOp-tDxbHghRQXOLJIOSOkh--IqL9BGuFuSgqKd4_aB33xgjLl-QTAH5CGJm-AcXKDHVugdm0YQOFF6UxWargg8dFBtoUOzewgu7co8bX23McgyJ7jVduHLA99vgEN1Lx-JDJ8F87dfGS843efTqSx2kBftrxGL1mUnbloF5IwBJ3VilJN')">
                <div class="absolute inset-0 bg-black/40"></div>
            </div>
            <div class="relative z-10 text-center px-4 max-w-3xl">
                <h1 class="text-white font-display-lg text-display-lg md:text-[48px] md:leading-[56px] mb-6 drop-shadow-lg">Khám phá tinh hoa ẩm thực địa phương</h1>
                <div class="bg-surface p-2 rounded-full flex items-center shadow-lg max-w-2xl mx-auto w-full">
                    <span class="material-symbols-outlined text-primary ml-3 mr-2">location_on</span>
                    <input class="flex-grow bg-transparent border-none focus:ring-0 text-on-surface text-body-lg px-2 text-main" placeholder="Bạn muốn ăn gì, ở đâu?" type="text"/>
                    <button class="bg-primary text-white px-6 py-3 rounded-full font-title-md text-title-md hover:bg-surface-tint transition-colors active:scale-95 flex items-center">
                        <span class="material-symbols-outlined mr-2">search</span> Tìm kiếm
                    </button>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="max-w-[1200px] mx-auto py-16 px-container-margin">
            <h2 class="font-headline-lg text-headline-lg text-on-surface mb-8">Danh mục khám phá</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Category 1 -->
                <div class="bg-surface-card rounded-xl shadow-sm border border-surface-container hover:shadow-md hover:-translate-y-1 transition-all cursor-pointer p-6 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-primary-fixed flex items-center justify-center mb-4 text-primary">
                        <span class="material-symbols-outlined text-3xl">restaurant</span>
                    </div>
                    <h3 class="font-title-md text-title-md text-on-surface">Quán ăn</h3>
                    <p class="text-text-muted font-body-sm text-body-sm mt-2">Bữa chính đậm đà</p>
                </div>
                <!-- Category 2 -->
                <div class="bg-surface-card rounded-xl shadow-sm border border-surface-container hover:shadow-md hover:-translate-y-1 transition-all cursor-pointer p-6 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-secondary-fixed flex items-center justify-center mb-4 text-secondary">
                        <span class="material-symbols-outlined text-3xl">local_cafe</span>
                    </div>
                    <h3 class="font-title-md text-title-md text-on-surface">Quán nước</h3>
                    <p class="text-text-muted font-body-sm text-body-sm mt-2">Cà phê &amp; Trà sữa</p>
                </div>
                <!-- Category 3 -->
                <div class="bg-surface-card rounded-xl shadow-sm border border-surface-container hover:shadow-md hover:-translate-y-1 transition-all cursor-pointer p-6 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-tertiary-fixed flex items-center justify-center mb-4 text-tertiary">
                        <span class="material-symbols-outlined text-3xl">sports_esports</span>
                    </div>
                    <h3 class="font-title-md text-title-md text-on-surface">Bida</h3>
                    <p class="text-text-muted font-body-sm text-body-sm mt-2">Giải trí cuối tuần</p>
                </div>
                <!-- Category 4 -->
                <div class="bg-surface-card rounded-xl shadow-sm border border-surface-container hover:shadow-md hover:-translate-y-1 transition-all cursor-pointer p-6 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-[#ffdad6] flex items-center justify-center mb-4 text-[#93000a]">
                        <span class="material-symbols-outlined text-3xl">icecream</span>
                    </div>
                    <h3 class="font-title-md text-title-md text-on-surface">Đồ ăn vặt</h3>
                    <p class="text-text-muted font-body-sm text-body-sm mt-2">Ngon miệng, giá rẻ</p>
                </div>
            </div>
        </section>

        <!-- Featured Venues -->
        <section class="bg-surface-container-low py-16">
            <div class="max-w-[1200px] mx-auto px-container-margin">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h2 class="font-headline-lg text-headline-lg text-on-surface">Quán nổi bật</h2>
                        <p class="text-text-muted font-body-lg text-body-lg mt-1">Những địa điểm được cộng đồng đánh giá cao nhất</p>
                    </div>
                    <a class="text-primary font-title-md text-title-md flex items-center hover:underline" href="#">Xem tất cả <span class="material-symbols-outlined ml-1">arrow_forward</span></a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Venue Card 1 -->
                    <a href="/quan/1" class="block bg-surface-card rounded-xl shadow-sm overflow-hidden group cursor-pointer hover:shadow-md transition-shadow">
                        <div class="relative h-48 w-full">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAMFi7L3ajcoG_YDWwzQnLUKGR8SOJB7UNavQpMf999YeiJh_wpVo8CO2FAEJ8YJMqEsIvlvIVpWy9DTnWXizPyn15vwY79KaeGbI4wx43pxpqpY1-vrPGD-hkI9fuWYQeSllEjU7BXkc9kxNtrnQBjaPNKHOhemyK1mmkd146zlVBzQ0ZCMlFXFHlU9iOSHcFuXwziUFSGuxrFYgHiFA14tqmtdveipaVBR4WHpNYB4MTwuV2y_6Ty"/>
                            <div class="absolute top-3 left-3 bg-surface px-2 py-1 rounded text-primary font-label-md text-label-md flex items-center">
                                <span class="material-symbols-outlined text-[14px] mr-1" style="font-variation-settings: 'FILL' 1;">star</span> 4.8
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center mb-2">
                                <h3 class="font-title-md text-title-md text-on-surface truncate mr-2">Phở Gia Truyền Bát Đàn</h3>
                                <span class="material-symbols-outlined text-tick-xanh text-[18px]" style="font-variation-settings: 'FILL' 1;" title="Verified">verified</span>
                            </div>
                            <div class="flex items-center text-text-muted font-body-sm text-body-sm mb-4">
                                <span class="material-symbols-outlined text-[16px] mr-1">location_on</span>
                                <span class="truncate">49 Bát Đàn, Hoàn Kiếm, Hà Nội</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="bg-surface-container px-2 py-1 rounded text-on-surface-variant font-label-sm text-label-sm">Phở</span>
                                <span class="bg-surface-container px-2 py-1 rounded text-on-surface-variant font-label-sm text-label-sm">Ăn sáng</span>
                                <span class="bg-surface-container px-2 py-1 rounded text-on-surface-variant font-label-sm text-label-sm">Lâu đời</span>
                            </div>
                        </div>
                    </a>
                    <!-- Venue Card 2 -->
                    <a href="/quan/1" class="block bg-surface-card rounded-xl shadow-sm overflow-hidden group cursor-pointer hover:shadow-md transition-shadow">
                        <div class="relative h-48 w-full">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBdDIm-f3fYmquDwI4yxFYLNk8c2DvdP9gVyJR0l31kij4uJBPIepwy8SthgaQE4vtzOe60bP96ZGDT0N6vSNfbyDUljWquuydu_d-JZtKcURhsVI__AiSvRaPjdg9Ma1W39SPzmlNCE4q5NYGic1YboetnOXpgXDYmGS2OxOdmTbets2EvAXpGwxgR1iwzH6uAYBnPyu77pY8oLM8mbX3GL4DZp_TwapwPX19XhKqvvNYI6GiewTEf"/>
                            <div class="absolute top-3 left-3 bg-surface px-2 py-1 rounded text-primary font-label-md text-label-md flex items-center">
                                <span class="material-symbols-outlined text-[14px] mr-1" style="font-variation-settings: 'FILL' 1;">star</span> 4.9
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center mb-2">
                                <h3 class="font-title-md text-title-md text-on-surface truncate mr-2">The Note Coffee</h3>
                                <span class="material-symbols-outlined text-tick-xanh text-[18px]" style="font-variation-settings: 'FILL' 1;" title="Verified">verified</span>
                            </div>
                            <div class="flex items-center text-text-muted font-body-sm text-body-sm mb-4">
                                <span class="material-symbols-outlined text-[16px] mr-1">location_on</span>
                                <span class="truncate">64 Lương Văn Can, Hoàn Kiếm, Hà Nội</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="bg-surface-container px-2 py-1 rounded text-on-surface-variant font-label-sm text-label-sm">Cà phê</span>
                                <span class="bg-surface-container px-2 py-1 rounded text-on-surface-variant font-label-sm text-label-sm">View đẹp</span>
                                <span class="bg-surface-container px-2 py-1 rounded text-on-surface-variant font-label-sm text-label-sm">Sống ảo</span>
                            </div>
                        </div>
                    </a>
                    <!-- Venue Card 3 -->
                    <a href="/quan/1" class="block bg-surface-card rounded-xl shadow-sm overflow-hidden group cursor-pointer hover:shadow-md transition-shadow">
                        <div class="relative h-48 w-full">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCJQzjw-ulQ0mbtVf1_wBiHRJoAAIoPfsuSVTzpsxXY1rWPuCaWsHHs1hzsRQBhtyqzfc1EeoY6EXtzujXVcBc4KLLvMxlElgWSecPfEvEWEKvri7o4Zl6Zp_VUblP9U-y9bQmknWKIvKojJeKzBfCZ581bbdA-gKrAyDG8Yn3WQ1SsWdQauzgTYfe_w3SJGS-AVS6qQyVEQ3wZ1ga3LFI5MW3T9FB1aPIY3qlLiuuV9ub-J1IloBtq"/>
                            <div class="absolute top-3 left-3 bg-surface px-2 py-1 rounded text-primary font-label-md text-label-md flex items-center">
                                <span class="material-symbols-outlined text-[14px] mr-1" style="font-variation-settings: 'FILL' 1;">star</span> 4.7
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center mb-2">
                                <h3 class="font-title-md text-title-md text-on-surface truncate mr-2">Magic Billiards Club</h3>
                            </div>
                            <div class="flex items-center text-text-muted font-body-sm text-body-sm mb-4">
                                <span class="material-symbols-outlined text-[16px] mr-1">location_on</span>
                                <span class="truncate">15 Thái Hà, Đống Đa, Hà Nội</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="bg-surface-container px-2 py-1 rounded text-on-surface-variant font-label-sm text-label-sm">Bida</span>
                                <span class="bg-surface-container px-2 py-1 rounded text-on-surface-variant font-label-sm text-label-sm">Giải trí</span>
                                <span class="bg-surface-container px-2 py-1 rounded text-on-surface-variant font-label-sm text-label-sm">Mở muộn</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- Community Impact -->
        <section class="max-w-[1200px] mx-auto py-20 px-container-margin">
            <div class="bg-primary-fixed rounded-2xl p-10 flex flex-col md:flex-row items-center justify-between shadow-sm relative overflow-hidden">
                <div class="relative z-10 md:w-1/3 mb-8 md:mb-0">
                    <h2 class="font-display-lg text-display-lg text-on-primary-fixed mb-4">Cộng đồng<br/>Quán Mới</h2>
                    <p class="text-on-surface-variant font-body-lg text-body-lg">Cùng nhau xây dựng bản đồ ẩm thực địa phương chất lượng, đáng tin cậy.</p>
                </div>
                <div class="relative z-10 md:w-2/3 grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
                    <div class="bg-surface/80 backdrop-blur-sm p-6 rounded-xl text-center border border-white/50">
                        <div class="font-display-lg text-display-lg text-primary mb-2">1,000+</div>
                        <div class="font-title-md text-title-md text-on-surface">Quán ăn</div>
                    </div>
                    <div class="bg-surface/80 backdrop-blur-sm p-6 rounded-xl text-center border border-white/50">
                        <div class="font-display-lg text-display-lg text-secondary mb-2">50k+</div>
                        <div class="font-title-md text-title-md text-on-surface">Người dùng</div>
                    </div>
                    <div class="bg-surface/80 backdrop-blur-sm p-6 rounded-xl text-center border border-white/50">
                        <div class="font-display-lg text-display-lg text-tertiary mb-2">120k+</div>
                        <div class="font-title-md text-title-md text-on-surface">Đánh giá</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- MOBILE Main Content -->
    <main class="max-w-[1200px] mx-auto w-full md:hidden flex-grow">
        <!-- Hero Section -->
        <section class="px-container-margin pt-stack-md pb-stack-lg bg-surface-card rounded-b-xl shadow-sm relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-primary-fixed opacity-50 rounded-full blur-2xl"></div>
            <div class="absolute top-20 -left-10 w-32 h-32 bg-secondary-container opacity-50 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <h1 class="font-headline-lg-mobile text-headline-lg-mobile mb-4 text-on-surface">Hôm nay bạn muốn đi đâu?</h1>
                <div class="relative flex items-center w-full">
                    <span class="material-symbols-outlined absolute left-4 text-on-surface-variant z-10">search</span>
                    <input class="w-full bg-surface pl-12 pr-4 py-3 rounded-xl border-outline-variant focus:border-secondary focus:ring-secondary text-body-sm shadow-sm transition-all" placeholder="Tìm kiếm quán ăn, món ăn, khu vực..." type="text"/>
                    <button class="absolute right-2 bg-primary-container text-on-primary-container p-2 rounded-lg scale-98 active:scale-95 transition-transform flex items-center justify-center">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">tune</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="py-stack-md px-container-margin">
            <div class="flex justify-between items-start gap-gutter">
                <a class="flex flex-col items-center gap-2 group w-1/4" href="#">
                    <div class="w-14 h-14 rounded-full bg-primary-fixed flex items-center justify-center text-primary group-active:scale-95 transition-transform shadow-[0px_4px_20px_rgba(0,0,0,0.05)]">
                        <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">restaurant</span>
                    </div>
                    <span class="font-label-md text-label-md text-center text-on-surface whitespace-nowrap">Quán ăn</span>
                </a>
                <a class="flex flex-col items-center gap-2 group w-1/4" href="#">
                    <div class="w-14 h-14 rounded-full bg-secondary-container flex items-center justify-center text-secondary group-active:scale-95 transition-transform shadow-[0px_4px_20px_rgba(0,0,0,0.05)]">
                        <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">local_cafe</span>
                    </div>
                    <span class="font-label-md text-label-md text-center text-on-surface whitespace-nowrap">Quán nước</span>
                </a>
                <a class="flex flex-col items-center gap-2 group w-1/4" href="#">
                    <div class="w-14 h-14 rounded-full bg-tertiary-fixed flex items-center justify-center text-tertiary group-active:scale-95 transition-transform shadow-[0px_4px_20px_rgba(0,0,0,0.05)]">
                        <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">sports_esports</span>
                    </div>
                    <span class="font-label-md text-label-md text-center text-on-surface whitespace-nowrap">Bida</span>
                </a>
                <a class="flex flex-col items-center gap-2 group w-1/4" href="#">
                    <div class="w-14 h-14 rounded-full bg-error-container flex items-center justify-center text-error group-active:scale-95 transition-transform shadow-[0px_4px_20px_rgba(0,0,0,0.05)]">
                        <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">fastfood</span>
                    </div>
                    <span class="font-label-md text-label-md text-center text-on-surface whitespace-nowrap">Đồ ăn vặt</span>
                </a>
            </div>
        </section>

        <!-- Hot Deals Carousel -->
        <section class="py-stack-md">
            <div class="px-container-margin mb-3 flex justify-between items-center">
                <h2 class="font-title-md text-title-md text-on-surface">Ưu đãi hot</h2>
                <a class="font-label-md text-label-md text-primary flex items-center" href="#">Xem tất cả <span class="material-symbols-outlined text-[16px]">chevron_right</span></a>
            </div>
            <div class="flex overflow-x-auto no-scrollbar gap-gutter px-container-margin snap-x snap-mandatory pb-4">
                <!-- Deal Card 1 -->
                <a href="/quan/1" class="block min-w-[280px] w-[85%] snap-center rounded-xl bg-surface-card overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-variant flex flex-col active:shadow-lg transition-shadow">
                    <div class="h-32 w-full relative">
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBYUbUAf4rQHpTniopZ2033Wd2WsUaTB3SxAMqEFxvfLqoxLzyXy-NGJLNy3_o2tu7bxzH5bRRltA9dNs_43JJBGKnflIdzEzsNO1sESXycd0vy3Q1iTLh3fJHVHDFUhSWuOBS56BiYDmZ5BspSY_fxOxRgRzFim9BqfeVLIIudIY1BOH2CYBZDf-ahzzRhdoD_cK72kSdsiOiv40tUuMtqhnRUKJUK1nVv6atlJeZuiJvxvh94Yfoa"/>
                        <div class="absolute top-2 left-2 bg-error text-on-error font-label-md text-label-md px-2 py-1 rounded-md">Giảm 20%</div>
                    </div>
                    <div class="p-3">
                        <h3 class="font-title-md text-title-md text-on-surface mb-1 flex items-center gap-1">Phở Thìn Lò Đúc <span class="material-symbols-outlined text-tick-xanh text-[16px]" style="font-variation-settings: 'FILL' 1;">verified</span></h3>
                        <p class="font-body-sm text-body-sm text-text-muted flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">location_on</span> Quận 1, TP.HCM</p>
                    </div>
                </a>
                <!-- Deal Card 2 -->
                <a href="/quan/1" class="block min-w-[280px] w-[85%] snap-center rounded-xl bg-surface-card overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-variant flex flex-col active:shadow-lg transition-shadow">
                    <div class="h-32 w-full relative">
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAdRggjNj_HfU6UVkKPy5mCIomUlf78mfcD96RQBWOK5YiY0MQP5ckmQmVLVqv5zz9SjORF-cQR4MFcsPzPYNLirsRjmdKrkYDaMysn1DWfRWwzjVImwegXTQIzTSTR_1HtbyWBc0TwzMHli9iluGdas8sksA2iW8P4Tnu6pa8aGooEKC7WLiVf-tOnKUyMx6naDuvdwJLlrf9ufyBWwU_iUY9KLSqV-F47CYmCUWDFvcyuEz5uOMte"/>
                        <div class="absolute top-2 left-2 bg-error text-on-error font-label-md text-label-md px-2 py-1 rounded-md">Mua 1 Tặng 1</div>
                    </div>
                    <div class="p-3">
                        <h3 class="font-title-md text-title-md text-on-surface mb-1 flex items-center gap-1">The Coffee House</h3>
                        <p class="font-body-sm text-body-sm text-text-muted flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">location_on</span> Quận 3, TP.HCM</p>
                    </div>
                </a>
            </div>
        </section>

        <!-- Recommended List -->
        <section class="py-stack-md px-container-margin">
            <div class="mb-4">
                <h2 class="font-title-md text-title-md text-on-surface">Gợi ý cho bạn</h2>
                <p class="font-body-sm text-body-sm text-text-muted">Dựa trên các địa điểm bạn đã lưu</p>
            </div>
            <div class="flex flex-col gap-stack-md">
                <!-- Recommended Item 1 -->
                <a href="/quan/1" class="block bg-surface-card rounded-xl p-3 flex gap-3 shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-variant active:bg-surface-container-low transition-colors">
                    <div class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0 relative">
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA-hv4Iw_MNnyf0On9OSu_2b5LN3iH4ik3VjczbOW-wloQKEsSF3WroKxkIoMgi45imzUtdar0N1qGL_CD4vq0vdHczi8UebwfXafj5JLs33JPqbdpExyNO79pq2pBMacA_K3zMEK9G3_qbdYX5dS99KDLfCKkOqnKd_jkz1a9K-6MhDm2dfaHKvlR6BHI94SCDt8HIuCmKpO2CpdMA9Umdx1HsQhxHnwAT-ApvAnSxAhkckeg3gv0x"/>
                        <div class="absolute bottom-1 right-1 bg-surface/90 backdrop-blur-sm px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[10px] text-[#f59e0b]" style="font-variation-settings: 'FILL' 1;">star</span> 4.8
                        </div>
                    </div>
                    <div class="flex flex-col justify-center flex-1">
                        <div class="flex justify-between items-start">
                            <h3 class="font-title-md text-title-md text-on-surface leading-tight flex items-center gap-1">Bánh Mì Huỳnh Hoa <span class="material-symbols-outlined text-tick-xanh text-[16px]" style="font-variation-settings: 'FILL' 1;">verified</span></h3>
                            <button class="text-on-surface-variant hover:text-error transition-colors p-1"><span class="material-symbols-outlined text-[20px]">favorite_border</span></button>
                        </div>
                        <p class="font-body-sm text-body-sm text-text-muted line-clamp-1 mt-1">Đồ ăn vặt • Bánh mì</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="font-label-sm text-label-sm bg-primary-fixed text-on-primary-fixed-variant px-2 py-1 rounded">Gần đây</span>
                            <span class="font-label-sm text-label-sm text-text-muted flex items-center gap-0.5"><span class="material-symbols-outlined text-[12px]">directions_walk</span> 500m</span>
                        </div>
                    </div>
                </a>
                <!-- Recommended Item 2 -->
                <a href="/quan/1" class="block bg-surface-card rounded-xl p-3 flex gap-3 shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-variant active:bg-surface-container-low transition-colors">
                    <div class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0 relative">
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBg3bq9omZUdQ2jr7tarebEfK57mJnyURKHejeZ-omsOcUpE7p9fqymX1IYtrRkvz5gBnRyBTYEozt00uYWzqgeOsFtVINX5CKWaMpn6ZCV_s75C31g7sGDOnTJiA8pooRTa6Oo88S_W05FjgesDOz_VkrLa0eguaGmPVwnPnH_0lE-yQmz5SU6EIAKiKh7DptLVyAJGgEdvCdEg6f9gVBHGch0bXfVCNGPc-fcuoMSsyXyg29PiTD6"/>
                        <div class="absolute bottom-1 right-1 bg-surface/90 backdrop-blur-sm px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[10px] text-[#f59e0b]" style="font-variation-settings: 'FILL' 1;">star</span> 4.5
                        </div>
                    </div>
                    <div class="flex flex-col justify-center flex-1">
                        <div class="flex justify-between items-start">
                            <h3 class="font-title-md text-title-md text-on-surface leading-tight flex items-center gap-1">Bida Phúc Thịnh <span class="material-symbols-outlined text-tick-xanh text-[16px]" style="font-variation-settings: 'FILL' 1;">verified</span></h3>
                            <button class="text-on-surface-variant hover:text-error transition-colors p-1"><span class="material-symbols-outlined text-[20px]">favorite_border</span></button>
                        </div>
                        <p class="font-body-sm text-body-sm text-text-muted line-clamp-1 mt-1">Giải trí • Bida</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="font-label-sm text-label-sm bg-secondary-fixed text-on-secondary-fixed-variant px-2 py-1 rounded">Mới mở</span>
                            <span class="font-label-sm text-label-sm text-text-muted flex items-center gap-0.5"><span class="material-symbols-outlined text-[12px]">directions_car</span> 2.5km</span>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        <!-- Latest Blog Bento Grid -->
        <section class="py-stack-md px-container-margin mb-6">
            <div class="mb-4 flex justify-between items-center">
                <h2 class="font-title-md text-title-md text-on-surface">Blog ẩm thực</h2>
                <a class="font-label-md text-label-md text-primary flex items-center" href="#">Xem thêm <span class="material-symbols-outlined text-[16px]">chevron_right</span></a>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <!-- Large Bento Item -->
                <div class="col-span-2 relative h-48 rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] group cursor-pointer">
                    <img class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDmjAALenZjkGx1j0ShVDeq85fDBM6uYptvz2lsARZ86zvKJ5eqgzyBUBz6W3Wfnbu8NOBhv7kGk-cQanPiGSVVVyvn43639xCrl26_jLtei_iwjxE0BdlkXC-Jb9jQJ_mx7nRD46sG_mK3mp0s9SLD-GpJDUM5Lr0Xf8RDen-_L8l88F6wVtpRDHxehnkzABupKR0OrmvLgLy73P4bEjBbXqSZsMUvCr9KA5NPrXkoVwLjMTt4fiYl"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-4 w-full">
                        <span class="font-label-sm text-label-sm bg-primary text-on-primary px-2 py-1 rounded-md inline-block mb-2">Khám phá</span>
                        <h3 class="font-title-md text-title-md text-white line-clamp-2">Top 10 quán ăn vỉa hè không thể bỏ qua tại Quận 1</h3>
                    </div>
                </div>
                <!-- Small Bento Items -->
                <div class="col-span-1 relative h-32 rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] group cursor-pointer">
                    <img class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC8FkHXqyHVNBaB8I7w_IhYaiMLqrYXLo3_9Gls0OxD8wWWR4Iw-RRax4y26XaQf1P7RJuh1KwYDUcLpdg1KXYOoAIpBVLui6_VcBVXXP0lvTlxqqVxMi1xmlheFQucRteBUl5sgkTqDM9of9XdL0QRogYlWCVTUdXpUco3dQ0adjaR-4qiD6IT7PG5eJ8e7DnZHmSdXPnf4kX19ZbEfuscg3qkajKZbepz-m6wMz7WsHdYIABM-Vhy"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-3 w-full">
                        <h3 class="font-label-md text-label-md text-white line-clamp-2">Quán cafe view đẹp cuối tuần</h3>
                    </div>
                </div>
                <div class="col-span-1 relative h-32 rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)] group cursor-pointer">
                    <img class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBLJLoNIio-dL2f0pWJf13IwPZE_giyiTmRP209rPE125l9OelWE3x7HOu4jkJN_F5UpHWbpixpgvTYmbO4VnUj3DHOmpZ0TBxVuhdUdEslDpJK4galiqkvlgpUVeYshwrwn74vLKWqpxXnTvyjxlNTS9tqRNtCoLRM_fvvOAH6fgo4F_wikMEx5xTAfANUKCboVn_Ho1LrFrYtP6oQANL3m8yIC87sfdR7DGT9SjV_Xe9fSEJ5-nw2"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-3 w-full">
                        <h3 class="font-label-md text-label-md text-white line-clamp-2">Review lẩu cá kèo trứ danh</h3>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('footer')
    <!-- DESKTOP Footer -->
    <footer class="hidden md:block bg-surface-container border-t border-outline-variant w-full mt-stack-lg font-body-sm text-body-sm transition-all duration-200">
        <div class="max-w-[1200px] mx-auto py-stack-lg px-container-margin flex flex-col md:flex-row justify-between items-center gap-stack-md">
            <div class="font-title-md text-title-md text-on-surface mb-4 md:mb-0">
                Quán Mới
            </div>
            <div class="flex flex-wrap justify-center gap-6 mb-4 md:mb-0">
                <a class="text-text-muted hover:text-primary underline transition-colors" href="#">Điều khoản dịch vụ</a>
                <a class="text-text-muted hover:text-primary underline transition-colors" href="#">Chính sách bảo mật</a>
                <a class="text-text-muted hover:text-primary underline transition-colors" href="#">Trung tâm hỗ trợ</a>
                <a class="text-text-muted hover:text-primary underline transition-colors" href="#">Liên hệ</a>
            </div>
            <div class="text-text-muted text-center md:text-right">
                © 2026 Quán Mới. Bảo lưu mọi quyền.
            </div>
        </div>
    </footer>
@endsection

