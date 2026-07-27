@extends('layouts.app')

@section('title', 'Quán Mới - Khám phá tinh hoa ẩm thực địa phương')

@section('content')
    <!-- DESKTOP Main Content -->
    <main class="hidden md:block flex-grow">
        <!-- Hero Section -->
        <section class="relative w-full h-[540px] flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center scale-105 transform hover:scale-100 transition-transform duration-1000" style="background-image: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1600&q=80')">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/30"></div>
            </div>
            <div class="relative z-10 text-center px-4 max-w-4xl space-y-6">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full text-white text-sm font-semibold border border-white/30 mb-2">
                    <span class="material-symbols-outlined text-[18px] text-tick-xanh" style="font-variation-settings: 'FILL' 1;">verified</span>
                    Cộng đồng ẩm thực & giải trí hàng đầu
                </div>
                <h1 class="text-white font-display-lg text-[52px] leading-[62px] font-black drop-shadow-lg tracking-tight">
                    Khám phá <span class="text-primary-fixed">tinh hoa ẩm thực</span> địa phương
                </h1>
                <p class="text-white/90 text-lg max-w-2xl mx-auto font-normal drop-shadow">
                    Tìm kiếm hàng ngàn quán ăn, quán cà phê, tiệm trà sữa và địa điểm giải trí được yêu thích nhất gần bạn.
                </p>

                {{-- Hero Search Trigger Box --}}
                <div class="bg-white p-2.5 rounded-full flex items-center shadow-2xl max-w-2xl mx-auto w-full cursor-pointer hover:shadow-primary/20 transition-all border border-white/80" onclick="openLocationModal()">
                    <span class="material-symbols-outlined text-primary text-2xl ml-4 mr-2">location_on</span>
                    <input class="flex-grow bg-transparent border-none focus:ring-0 text-gray-800 text-[16px] font-medium px-2 outline-none cursor-pointer" placeholder="Bạn muốn ăn gì, tìm quán ở đâu?" type="text" readonly onclick="openLocationModal()"/>
                    <button type="button" onclick="openLocationModal()" class="bg-primary text-white px-7 py-3.5 rounded-full font-bold text-[15px] hover:bg-surface-tint transition-all active:scale-95 flex items-center gap-2 shadow-md">
                        <span class="material-symbols-outlined text-[20px]">search</span> Tìm kiếm
                    </button>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="max-w-[1240px] mx-auto py-16 px-container-margin">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-black text-on-surface">Danh mục khám phá</h2>
                    <p class="text-text-muted text-[15px] mt-1">Tìm địa điểm theo nhu cầu ẩm thực của bạn</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-5">
                <!-- Category 1 -->
                <div class="bg-surface-card rounded-2xl shadow-sm border border-surface-container hover:shadow-lg hover:-translate-y-1 transition-all cursor-pointer p-6 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-3 text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">restaurant</span>
                    </div>
                    <h3 class="font-bold text-[16px] text-on-surface">Quán ăn</h3>
                    <p class="text-text-muted text-[13px] mt-1">Bữa chính đậm đà</p>
                </div>
                <!-- Category 2 -->
                <div class="bg-surface-card rounded-2xl shadow-sm border border-surface-container hover:shadow-lg hover:-translate-y-1 transition-all cursor-pointer p-6 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl bg-secondary/10 flex items-center justify-center mb-3 text-secondary group-hover:bg-secondary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">local_cafe</span>
                    </div>
                    <h3 class="font-bold text-[16px] text-on-surface">Cà phê & Trà</h3>
                    <p class="text-text-muted text-[13px] mt-1">Tụ tập & Làm việc</p>
                </div>
                <!-- Category 3 -->
                <div class="bg-surface-card rounded-2xl shadow-sm border border-surface-container hover:shadow-lg hover:-translate-y-1 transition-all cursor-pointer p-6 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl bg-tertiary/10 flex items-center justify-center mb-3 text-tertiary group-hover:bg-tertiary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">sports_esports</span>
                    </div>
                    <h3 class="font-bold text-[16px] text-on-surface">Bida & Giải trí</h3>
                    <p class="text-text-muted text-[13px] mt-1">Vui chơi cuối tuần</p>
                </div>
                <!-- Category 4 -->
                <div class="bg-surface-card rounded-2xl shadow-sm border border-surface-container hover:shadow-lg hover:-translate-y-1 transition-all cursor-pointer p-6 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl bg-red-500/10 flex items-center justify-center mb-3 text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">fastfood</span>
                    </div>
                    <h3 class="font-bold text-[16px] text-on-surface">Đồ ăn vặt</h3>
                    <p class="text-text-muted text-[13px] mt-1">Ngon rẻ chuẩn gu</p>
                </div>
                <!-- Category 5 -->
                <div class="bg-surface-card rounded-2xl shadow-sm border border-surface-container hover:shadow-lg hover:-translate-y-1 transition-all cursor-pointer p-6 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl bg-amber-500/10 flex items-center justify-center mb-3 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">outdoor_grill</span>
                    </div>
                    <h3 class="font-bold text-[16px] text-on-surface">Lẩu & Nướng</h3>
                    <p class="text-text-muted text-[13px] mt-1">Tiệc tùng nhóm</p>
                </div>
                <!-- Category 6 -->
                <div class="bg-surface-card rounded-2xl shadow-sm border border-surface-container hover:shadow-lg hover:-translate-y-1 transition-all cursor-pointer p-6 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl bg-purple-500/10 flex items-center justify-center mb-3 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">nightlife</span>
                    </div>
                    <h3 class="font-bold text-[16px] text-on-surface">Quán Đêm 24/7</h3>
                    <p class="text-text-muted text-[13px] mt-1">Ăn đêm & Xuyên đêm</p>
                </div>
            </div>
        </section>

        <!-- SECTION 1: Trending Venues / Quán Nổi Bật -->
        <section class="bg-surface-container-low py-16">
            <div class="max-w-[1240px] mx-auto px-container-margin">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-primary text-xs font-bold uppercase tracking-wider mb-1">
                            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                            Hot nhất tuần
                        </div>
                        <h2 class="text-2xl font-black text-on-surface">Quán ăn nổi bật được đánh giá cao</h2>
                        <p class="text-text-muted text-[15px] mt-1">Những địa điểm nhận được nhiều phản hồi tích cực nhất từ cộng đồng</p>
                    </div>
                    <a class="text-primary font-bold text-[14px] flex items-center hover:underline gap-1" href="#">
                        Xem tất cả <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @forelse($quanNoiBat as $quan)
                    <a href="{{ route('quan.detail', $quan->slug) }}" class="block bg-white rounded-2xl shadow-sm overflow-hidden group cursor-pointer hover:shadow-xl transition-all border border-gray-100">
                        <div class="relative h-52 w-full overflow-hidden">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $quan->anh_bia ? $quan->anh_bia : 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80' }}"/>
                            <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-2.5 py-1 rounded-xl text-primary font-bold text-xs flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">star</span> 4.9 (520+)
                            </div>
                            <div class="absolute top-3 right-3 bg-primary text-white text-[11px] font-bold px-2.5 py-1 rounded-lg">Top 1 Đề Xuất</div>
                        </div>
                        <div class="p-5 space-y-2">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-[17px] text-on-surface truncate group-hover:text-primary transition-colors">{{ $quan->ten_quan }}</h3>
                                <span class="material-symbols-outlined text-tick-xanh text-[20px] flex-shrink-0" style="font-variation-settings: 'FILL' 1;" title="Đã xác thực">verified</span>
                            </div>
                            <p class="text-text-muted text-[13px] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px] text-gray-400">location_on</span>
                                <span class="truncate">{{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_phuong_xa }}, {{ $quan->ten_quan_huyen }}</span>
                            </p>
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <div class="flex gap-1.5">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded-md text-gray-600 text-[12px] font-medium">{{ str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}</span>
                                </div>
                                <span class="text-primary font-bold text-[14px]">{{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    </a>
                    @empty
                    <p class="col-span-3 text-center text-gray-500 py-10">Chưa có quán nào nổi bật.</p>
                    @endforelse
                </div>

                <div class="mt-10 rounded-[28px] bg-white border border-gray-100 shadow-sm p-6 md:p-7">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
                        <div>
                            <div class="inline-flex items-center gap-2 text-secondary text-xs font-bold uppercase tracking-wider mb-1">
                                <span class="material-symbols-outlined text-[16px]">new_releases</span>
                                Mới từ cộng đồng
                            </div>
                            <h3 class="text-xl md:text-2xl font-black text-on-surface">Quán mới vừa được thêm gần đây</h3>
                            <p class="text-text-muted text-[15px] mt-1">Những địa điểm mới lên sóng để bạn khám phá sớm trước khi thành trend.</p>
                        </div>
                        <a class="text-primary font-bold text-[14px] flex items-center gap-1 hover:underline" href="{{ route('chu-quan.dang-quan') }}">
                            Thêm quán của bạn <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        @forelse($quanMoi as $quan)
                        <a href="{{ route('quan.detail', $quan->slug) }}" class="group rounded-2xl border border-gray-100 bg-white hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full">
                            <div class="relative w-full h-44 flex-shrink-0 overflow-hidden">
                                <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="{{ $quan->anh_bia ? $quan->anh_bia : 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $quan->ten_quan }}" />
                                <div class="absolute top-2 left-2 bg-secondary text-white px-2 py-1 rounded-lg text-[10px] font-bold shadow-sm">Mới Mở</div>
                            </div>
                            <div class="p-4 flex flex-col flex-1">
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="material-symbols-outlined text-[14px] text-primary">schedule</span>
                                    <span class="text-[11px] text-primary font-bold">{{ $quan->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="font-bold text-[15px] text-on-surface group-hover:text-primary transition-colors truncate">{{ $quan->ten_quan }}</h4>
                                    <span class="material-symbols-outlined text-tick-xanh text-[16px] flex-shrink-0" style="font-variation-settings: 'FILL' 1;" title="Đã xác thực">verified</span>
                                </div>
                                <p class="text-[12px] text-text-muted mt-1 truncate flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    {{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}
                                </p>
                                <div class="mt-3 pt-3 flex items-center justify-between border-t border-gray-100">
                                    <span class="bg-gray-50 text-gray-600 px-2 py-1 rounded-md text-[11px] font-medium">{{ str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}</span>
                                    <span class="font-bold text-primary text-[13px]">{{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        </a>
                        @empty
                        <p class="sm:col-span-2 lg:col-span-4 text-center text-gray-500 py-10">Chưa có quán mới nào.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 2: Video Reviews Carousel (Mới thêm) -->
        <section class="max-w-[1240px] mx-auto py-16 px-container-margin">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <div class="inline-flex items-center gap-1.5 text-red-600 text-xs font-bold uppercase tracking-wider mb-1">
                        <span class="material-symbols-outlined text-[16px]">play_circle</span>
                        Video Review Thật
                    </div>
                    <h2 class="text-2xl font-black text-on-surface">Trải nghiệm thực tế qua Video Short</h2>
                    <p class="text-text-muted text-[15px] mt-1">Xem video đánh giá ngắn từ các Reviewer uy tín</p>
                </div>
                <a class="text-primary font-bold text-[14px] flex items-center hover:underline gap-1" href="#">
                    Xem tất cả video <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Video Card 1 --}}
                <div class="relative h-96 rounded-2xl overflow-hidden group cursor-pointer shadow-md hover:shadow-xl transition-all">
                    <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=500&q=80" alt="Review Phở" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/20"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-14 h-14 rounded-full bg-white/30 backdrop-blur-md flex items-center justify-center text-white group-hover:scale-110 group-hover:bg-primary transition-all">
                            <span class="material-symbols-outlined text-3xl ml-1" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                        </div>
                    </div>
                    <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-full text-white text-[12px] font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">visibility</span> 12.5k
                    </div>
                    <div class="absolute bottom-4 inset-x-4 text-white space-y-1">
                        <span class="bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded">Review Quán Ăn</span>
                        <h4 class="font-bold text-[15px] leading-tight line-clamp-2">Thử ngay tô Phở Bò Tái Lăn 65k chuẩn vị Hà Thành tại Q1</h4>
                        <p class="text-white/80 text-[12px]">bởi <span class="font-bold text-white">Sài Gòn Foodie</span></p>
                    </div>
                </div>

                {{-- Video Card 2 --}}
                <div class="relative h-96 rounded-2xl overflow-hidden group cursor-pointer shadow-md hover:shadow-xl transition-all">
                    <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=500&q=80" alt="Review Cà Phê" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/20"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-14 h-14 rounded-full bg-white/30 backdrop-blur-md flex items-center justify-center text-white group-hover:scale-110 group-hover:bg-primary transition-all">
                            <span class="material-symbols-outlined text-3xl ml-1" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                        </div>
                    </div>
                    <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-full text-white text-[12px] font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">visibility</span> 28.3k
                    </div>
                    <div class="absolute bottom-4 inset-x-4 text-white space-y-1">
                        <span class="bg-secondary text-white text-[10px] font-bold px-2 py-0.5 rounded">Cà Phê Sống Ảo</span>
                        <h4 class="font-bold text-[15px] leading-tight line-clamp-2">Quán cafe sân vườn kính ngắm mưa cực chill ở Q3</h4>
                        <p class="text-white/80 text-[12px]">bởi <span class="font-bold text-white">An An Review</span></p>
                    </div>
                </div>

                {{-- Video Card 3 --}}
                <div class="relative h-96 rounded-2xl overflow-hidden group cursor-pointer shadow-md hover:shadow-xl transition-all">
                    <img src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=500&q=80" alt="Review Lẩu Nướng" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/20"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-14 h-14 rounded-full bg-white/30 backdrop-blur-md flex items-center justify-center text-white group-hover:scale-110 group-hover:bg-primary transition-all">
                            <span class="material-symbols-outlined text-3xl ml-1" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                        </div>
                    </div>
                    <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-full text-white text-[12px] font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">visibility</span> 45.1k
                    </div>
                    <div class="absolute bottom-4 inset-x-4 text-white space-y-1">
                        <span class="bg-amber-600 text-white text-[10px] font-bold px-2 py-0.5 rounded">Quán Nhậu Đêm</span>
                        <h4 class="font-bold text-[15px] leading-tight line-clamp-2">Đêm muộn ăn lẩu bò nướng ngói thơm lừng phố cổ</h4>
                        <p class="text-white/80 text-[12px]">bởi <span class="font-bold text-white">Hà Nội Street Food</span></p>
                    </div>
                </div>

                {{-- Video Card 4 --}}
                <div class="relative h-96 rounded-2xl overflow-hidden group cursor-pointer shadow-md hover:shadow-xl transition-all">
                    <img src="https://images.unsplash.com/photo-1511192336575-5a79af67a629?auto=format&fit=crop&w=500&q=80" alt="Review Bida" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/20"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-14 h-14 rounded-full bg-white/30 backdrop-blur-md flex items-center justify-center text-white group-hover:scale-110 group-hover:bg-primary transition-all">
                            <span class="material-symbols-outlined text-3xl ml-1" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                        </div>
                    </div>
                    <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-full text-white text-[12px] font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">visibility</span> 19.8k
                    </div>
                    <div class="absolute bottom-4 inset-x-4 text-white space-y-1">
                        <span class="bg-tertiary text-white text-[10px] font-bold px-2 py-0.5 rounded">Giải Trí Bida</span>
                        <h4 class="font-bold text-[15px] leading-tight line-clamp-2">Trải nghiệm CLB Bida chuẩn pro dàn bàn nhập khẩu cực mượt</h4>
                        <p class="text-white/80 text-[12px]">bởi <span class="font-bold text-white">Billiards VN</span></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 3: Community Collections / BST Bộ Thẩm Ẩm Thực -->
        <section class="bg-gray-900 text-white py-16">
            <div class="max-w-[1240px] mx-auto px-container-margin">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <span class="text-primary-fixed text-xs font-bold uppercase tracking-wider block mb-1">Tuyển chọn đặc biệt</span>
                        <h2 class="text-2xl font-black text-white">Bộ sưu tập ẩm thực nổi bật</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="relative h-64 rounded-2xl overflow-hidden group cursor-pointer border border-white/10">
                        <img src="https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=600&q=80" alt="BST Cà Phê Muối" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                        <div class="absolute bottom-5 inset-x-5 space-y-1">
                            <span class="bg-primary/80 backdrop-blur-sm text-white text-[11px] font-bold px-2.5 py-1 rounded-md">15 Địa điểm</span>
                            <h3 class="font-bold text-[18px] text-white">BST Cà phê muối béo ngậy khó cưỡng</h3>
                            <p class="text-white/70 text-[13px]">Khám phá các quán cà phê muối ngon nhất thành phố</p>
                        </div>
                    </div>

                    <div class="relative h-64 rounded-2xl overflow-hidden group cursor-pointer border border-white/10">
                        <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=600&q=80" alt="BST Quán Nướng" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                        <div class="absolute bottom-5 inset-x-5 space-y-1">
                            <span class="bg-secondary/80 backdrop-blur-sm text-white text-[11px] font-bold px-2.5 py-1 rounded-md">24 Địa điểm</span>
                            <h3 class="font-bold text-[18px] text-white">Top Quán lẩu nướng sân vườn thoáng mát</h3>
                            <p class="text-white/70 text-[13px]">Lý tưởng cho những buổi tụ họp bạn bè cuối tuần</p>
                        </div>
                    </div>

                    <div class="relative h-64 rounded-2xl overflow-hidden group cursor-pointer border border-white/10">
                        <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=600&q=80" alt="BST Đồ Ăn Đêm" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                        <div class="absolute bottom-5 inset-x-5 space-y-1">
                            <span class="bg-tertiary/80 backdrop-blur-sm text-white text-[11px] font-bold px-2.5 py-1 rounded-md">18 Địa điểm</span>
                            <h3 class="font-bold text-[18px] text-white">Quán ăn đêm 24/7 cho các "cú đêm"</h3>
                            <p class="text-white/70 text-[13px]">Tổng hợp quán ăn ngon mở muộn sau 00:00</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 4: Community Impact Statistics -->
        <section class="max-w-[1240px] mx-auto py-20 px-container-margin">
            <div class="bg-gradient-to-br from-primary-fixed via-amber-50 to-orange-100 rounded-3xl p-10 md:p-14 flex flex-col md:flex-row items-center justify-between shadow-sm relative overflow-hidden border border-amber-200">
                <div class="relative z-10 md:w-1/3 mb-8 md:mb-0 space-y-3">
                    <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold">Quán Mới Network</span>
                    <h2 class="font-black text-3xl text-on-primary-fixed leading-tight">Cộng đồng<br/>ẩm thực tin cậy</h2>
                    <p class="text-on-surface-variant text-[15px] leading-relaxed">Cùng nhau đóng góp đánh giá trung thực để xây dựng bản đồ ẩm thực chất lượng nhất.</p>
                </div>
                <div class="relative z-10 md:w-2/3 grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-2xl text-center border border-white/80 shadow-sm">
                        <div class="font-black text-4xl text-primary mb-1">1,200+</div>
                        <div class="font-bold text-[15px] text-on-surface">Quán ăn đã xác thực</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-2xl text-center border border-white/80 shadow-sm">
                        <div class="font-black text-4xl text-secondary mb-1">85,000+</div>
                        <div class="font-bold text-[15px] text-on-surface">Thành viên năng động</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-2xl text-center border border-white/80 shadow-sm">
                        <div class="font-black text-4xl text-tertiary mb-1">150,000+</div>
                        <div class="font-bold text-[15px] text-on-surface">Đánh giá thật</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- MOBILE Main Content -->
    <main class="max-w-[1200px] mx-auto w-full md:hidden flex-grow space-y-6 pb-12">
        <!-- Hero Section -->
        <section class="px-4 pt-4 pb-6 bg-white rounded-b-2xl shadow-sm relative overflow-hidden border-b border-gray-100">
            <div class="relative z-10 space-y-3">
                <div class="inline-flex items-center gap-1 text-primary text-[11px] font-bold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[14px]">restaurant</span>
                    Khám phá địa điểm gần bạn
                </div>
                <h1 class="text-xl font-black text-on-surface">Hôm nay bạn muốn ăn gì?</h1>
                <div class="relative flex items-center w-full cursor-pointer" onclick="openLocationModal()">
                    <span class="material-symbols-outlined absolute left-3.5 text-primary z-10 text-[20px]">location_on</span>
                    <input class="w-full bg-gray-100 pl-10 pr-10 py-3 rounded-xl border-none text-[14px] font-medium placeholder:text-gray-400 outline-none cursor-pointer" placeholder="Tìm kiếm quán ăn, khu vực..." type="text" readonly onclick="openLocationModal()"/>
                    <button type="button" onclick="openLocationModal()" class="absolute right-2 bg-primary text-white p-2 rounded-lg flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Categories Grid Mobile (6 items) -->
        <section class="px-4">
            <div class="grid grid-cols-3 gap-3">
                <a class="flex flex-col items-center gap-1.5 p-3 rounded-2xl bg-white border border-gray-100 shadow-sm active:scale-95 transition-transform" href="#">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-[24px]" style="font-variation-settings: 'FILL' 1;">restaurant</span>
                    </div>
                    <span class="text-[12px] font-bold text-center text-on-surface">Quán ăn</span>
                </a>
                <a class="flex flex-col items-center gap-1.5 p-3 rounded-2xl bg-white border border-gray-100 shadow-sm active:scale-95 transition-transform" href="#">
                    <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[24px]" style="font-variation-settings: 'FILL' 1;">local_cafe</span>
                    </div>
                    <span class="text-[12px] font-bold text-center text-on-surface">Cà phê & Trà</span>
                </a>
                <a class="flex flex-col items-center gap-1.5 p-3 rounded-2xl bg-white border border-gray-100 shadow-sm active:scale-95 transition-transform" href="#">
                    <div class="w-12 h-12 rounded-xl bg-tertiary/10 flex items-center justify-center text-tertiary">
                        <span class="material-symbols-outlined text-[24px]" style="font-variation-settings: 'FILL' 1;">sports_esports</span>
                    </div>
                    <span class="text-[12px] font-bold text-center text-on-surface">Bida & Giải trí</span>
                </a>
                <a class="flex flex-col items-center gap-1.5 p-3 rounded-2xl bg-white border border-gray-100 shadow-sm active:scale-95 transition-transform" href="#">
                    <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-600">
                        <span class="material-symbols-outlined text-[24px]" style="font-variation-settings: 'FILL' 1;">fastfood</span>
                    </div>
                    <span class="text-[12px] font-bold text-center text-on-surface">Đồ ăn vặt</span>
                </a>
                <a class="flex flex-col items-center gap-1.5 p-3 rounded-2xl bg-white border border-gray-100 shadow-sm active:scale-95 transition-transform" href="#">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700">
                        <span class="material-symbols-outlined text-[24px]" style="font-variation-settings: 'FILL' 1;">outdoor_grill</span>
                    </div>
                    <span class="text-[12px] font-bold text-center text-on-surface">Lẩu & Nướng</span>
                </a>
                <a class="flex flex-col items-center gap-1.5 p-3 rounded-2xl bg-white border border-gray-100 shadow-sm active:scale-95 transition-transform" href="#">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-700">
                        <span class="material-symbols-outlined text-[24px]" style="font-variation-settings: 'FILL' 1;">nightlife</span>
                    </div>
                    <span class="text-[12px] font-bold text-center text-on-surface">Quán Đêm 24/7</span>
                </a>
            </div>
        </section>

        <!-- Recommended List Mobile -->
        <section class="px-4">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h2 class="font-bold text-[16px] text-on-surface">Quán gợi ý nổi bật</h2>
                    <p class="text-[12px] text-gray-500">Địa điểm đánh giá cao gần bạn</p>
                </div>
                <a class="text-xs text-primary font-bold flex items-center gap-0.5" href="#">Xem tất cả <span class="material-symbols-outlined text-[14px]">chevron_right</span></a>
            </div>
                        <div class="flex flex-col gap-3">
                @forelse($quanNoiBat as $quan)
                <a href="{{ route('quan.detail', $quan->slug) }}" class="bg-white rounded-2xl p-3 flex gap-3 shadow-sm border border-gray-100 active:bg-gray-50 transition-colors">
                    <div class="w-24 h-24 rounded-xl overflow-hidden flex-shrink-0 relative">
                        <img class="w-full h-full object-cover" src="{{ $quan->anh_bia ? $quan->anh_bia : 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=300&q=80' }}"/>
                        <div class="absolute bottom-1 right-1 bg-black/70 text-white px-1.5 py-0.5 rounded-md text-[10px] font-bold flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[10px] text-amber-400" style="font-variation-settings: 'FILL' 1;">star</span> 4.9
                        </div>
                    </div>
                    <div class="flex flex-col justify-between flex-1 py-0.5">
                        <div>
                            <h3 class="font-bold text-[15px] text-on-surface leading-tight flex items-center gap-1">{{ $quan->ten_quan }} <span class="material-symbols-outlined text-tick-xanh text-[15px]" style="font-variation-settings: 'FILL' 1;">verified</span></h3>
                            <p class="text-[12px] text-text-muted mt-1 truncate">{{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_quan_huyen }}</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-primary">{{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ</span>
                            <span class="text-[11px] text-gray-400 flex items-center gap-0.5"><span class="material-symbols-outlined text-[12px]">directions_walk</span> 500m</span>
                        </div>
                    </div>
                </a>
                @empty
                <p class="text-center text-gray-500 py-4">Chưa có quán nào.</p>
                @endforelse
            </div>
        </section>

        <!-- Video Shorts Carousel Mobile -->
        <section class="px-4">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <div class="inline-flex items-center gap-1 text-red-600 text-[11px] font-bold uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[14px]">play_circle</span>
                        Short Video Review
                    </div>
                    <h2 class="font-bold text-[16px] text-on-surface">Video trải nghiệm thực tế</h2>
                </div>
                <a class="text-xs text-primary font-bold flex items-center gap-0.5" href="#">Tất cả <span class="material-symbols-outlined text-[14px]">chevron_right</span></a>
            </div>
            <div class="flex overflow-x-auto no-scrollbar gap-3 snap-x snap-mandatory -mx-4 px-4 pb-2">
                {{-- Video Item 1 --}}
                <div class="min-w-[200px] w-[55%] snap-center relative h-72 rounded-2xl overflow-hidden group cursor-pointer shadow-sm border border-gray-100 flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=400&q=80" alt="Video Phở" class="absolute inset-0 w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/20"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-11 h-11 rounded-full bg-white/30 backdrop-blur-md flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-2xl ml-0.5" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                        </div>
                    </div>
                    <div class="absolute top-2.5 right-2.5 bg-black/60 backdrop-blur-md px-2 py-0.5 rounded-full text-white text-[10px] font-bold flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[12px]">visibility</span> 12.5k
                    </div>
                    <div class="absolute bottom-3 inset-x-3 text-white space-y-1">
                        <h4 class="font-bold text-[13px] leading-tight line-clamp-2">Phở Bò Tái Lăn 65k chuẩn vị Hà Thành</h4>
                        <p class="text-white/80 text-[11px]">Sài Gòn Foodie</p>
                    </div>
                </div>

                {{-- Video Item 2 --}}
                <div class="min-w-[200px] w-[55%] snap-center relative h-72 rounded-2xl overflow-hidden group cursor-pointer shadow-sm border border-gray-100 flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=400&q=80" alt="Video Cafe" class="absolute inset-0 w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/20"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-11 h-11 rounded-full bg-white/30 backdrop-blur-md flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-2xl ml-0.5" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                        </div>
                    </div>
                    <div class="absolute top-2.5 right-2.5 bg-black/60 backdrop-blur-md px-2 py-0.5 rounded-full text-white text-[10px] font-bold flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[12px]">visibility</span> 28.3k
                    </div>
                    <div class="absolute bottom-3 inset-x-3 text-white space-y-1">
                        <h4 class="font-bold text-[13px] leading-tight line-clamp-2">Quán cafe sân vườn kính ngắm mưa Q3</h4>
                        <p class="text-white/80 text-[11px]">An An Review</p>
                    </div>
                </div>

                {{-- Video Item 3 --}}
                <div class="min-w-[200px] w-[55%] snap-center relative h-72 rounded-2xl overflow-hidden group cursor-pointer shadow-sm border border-gray-100 flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=400&q=80" alt="Video Lẩu Nướng" class="absolute inset-0 w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/20"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-11 h-11 rounded-full bg-white/30 backdrop-blur-md flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-2xl ml-0.5" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                        </div>
                    </div>
                    <div class="absolute top-2.5 right-2.5 bg-black/60 backdrop-blur-md px-2 py-0.5 rounded-full text-white text-[10px] font-bold flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[12px]">visibility</span> 45.1k
                    </div>
                    <div class="absolute bottom-3 inset-x-3 text-white space-y-1">
                        <h4 class="font-bold text-[13px] leading-tight line-clamp-2">Lẩu bò nướng ngói thơm lừng phố cổ</h4>
                        <p class="text-white/80 text-[11px]">Hà Nội Street Food</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Curated Collections Mobile -->
        <section class="px-4">
            <div class="mb-3">
                <h2 class="font-bold text-[16px] text-on-surface">Bộ sưu tập đề xuất</h2>
                <p class="text-[12px] text-gray-500">Tuyển chọn các quán ăn theo gu</p>
            </div>
            <div class="flex overflow-x-auto no-scrollbar gap-3 snap-x snap-mandatory -mx-4 px-4 pb-2">
                {{-- Collection 1 --}}
                <div class="min-w-[240px] w-[70%] snap-center relative h-44 rounded-2xl overflow-hidden group cursor-pointer shadow-sm border border-gray-100 flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=400&q=80" alt="BST Cà Phê Muối" class="absolute inset-0 w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                    <div class="absolute bottom-3 inset-x-3 space-y-0.5">
                        <span class="bg-primary/80 backdrop-blur-sm text-white text-[9px] font-bold px-2 py-0.5 rounded">15 Địa điểm</span>
                        <h3 class="font-bold text-[14px] text-white leading-tight">BST Cà phê muối béo ngậy</h3>
                    </div>
                </div>

                {{-- Collection 2 --}}
                <div class="min-w-[240px] w-[70%] snap-center relative h-44 rounded-2xl overflow-hidden group cursor-pointer shadow-sm border border-gray-100 flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=400&q=80" alt="BST Quán Nướng" class="absolute inset-0 w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                    <div class="absolute bottom-3 inset-x-3 space-y-0.5">
                        <span class="bg-secondary/80 backdrop-blur-sm text-white text-[9px] font-bold px-2 py-0.5 rounded">24 Địa điểm</span>
                        <h3 class="font-bold text-[14px] text-white leading-tight">Top Lẩu nướng sân vườn</h3>
                    </div>
                </div>

                {{-- Collection 3 --}}
                <div class="min-w-[240px] w-[70%] snap-center relative h-44 rounded-2xl overflow-hidden group cursor-pointer shadow-sm border border-gray-100 flex-shrink-0">
                    <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=400&q=80" alt="BST Ăn Đêm" class="absolute inset-0 w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                    <div class="absolute bottom-3 inset-x-3 space-y-0.5">
                        <span class="bg-tertiary/80 backdrop-blur-sm text-white text-[9px] font-bold px-2 py-0.5 rounded">18 Địa điểm</span>
                        <h3 class="font-bold text-[14px] text-white leading-tight">Quán ăn đêm 24/7 ngon mịt</h3>
                    </div>
                </div>
            </div>
        </section>

        <!-- Community Impact Stats Mobile -->
        <section class="px-4 pt-2">
            <div class="bg-gradient-to-br from-primary-fixed via-amber-50 to-orange-100 rounded-2xl p-5 border border-amber-200 space-y-4 shadow-sm">
                <div class="space-y-1">
                    <span class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-full text-[10px] font-bold">Cộng đồng Quán Mới</span>
                    <h3 class="font-black text-xl text-on-primary-fixed">1,200+ Quán ăn đã xác thực</h3>
                    <p class="text-[12px] text-on-surface-variant">Hơn 85,000+ thành viên đã đóng góp hơn 150,000+ đánh giá thật.</p>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center pt-1">
                    <div class="bg-white/80 p-2.5 rounded-xl border border-white">
                        <div class="font-black text-lg text-primary">1.2k+</div>
                        <div class="text-[10px] text-gray-600 font-bold">Quán ăn</div>
                    </div>
                    <div class="bg-white/80 p-2.5 rounded-xl border border-white">
                        <div class="font-black text-lg text-secondary">85k+</div>
                        <div class="text-[10px] text-gray-600 font-bold">Thành viên</div>
                    </div>
                    <div class="bg-white/80 p-2.5 rounded-xl border border-white">
                        <div class="font-black text-lg text-tertiary">150k+</div>
                        <div class="text-[10px] text-gray-600 font-bold">Review thật</div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
