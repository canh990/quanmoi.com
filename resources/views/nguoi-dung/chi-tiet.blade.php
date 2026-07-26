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
    <meta property="og:image" content="{{ $quan->anh_bia ? Storage::url($quan->anh_bia) : 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80' }}" />

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
                @php
                    $hinhAnhs = $quan->hinhAnh ?? collect();
                    $biaUrl = $quan->anh_bia ? Storage::url($quan->anh_bia) : 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80';
                    $img2 = $hinhAnhs->count() > 0 ? Storage::url($hinhAnhs[0]->duong_dan) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuD2Ck5qL2_TpbcOo7YGVfL4jIzyWsaWPJOLKDfi0oZ7KRgPjfwEjx3LlprQ1c5zoWBwwq3BNDE8s8h_IBpNRJY5PVvyTDdhgy-7Q5HRK1_o0rHP3G5iRdWrMB0YVFQBGQn4KE7XA_nBiEW3soOPvbhB8fOkpV9CxFQT6Bbm8ZlArhLdCx4a10froFQaLxTZatgH_PH2DzxuUOfaVEhgmpPhVtBYqD9HVRQNvXi9n9k5rXIhenIx5tmk';
                    $img3 = $hinhAnhs->count() > 1 ? Storage::url($hinhAnhs[1]->duong_dan) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuC3bzrIzaqyG4B_yvZnS4Po8P9iifDNOlmkoZW-CkCRe3OtnGLgU0uzl0qdjB4FDgI1TgyNX21O1P9hfh3SsUrzHSFJW4pWjH3ibrjomvnY5wtcYKOrNP-OruwGf5REKaxsF1IdXYH6kO1PzwhIycGIT8QbgiUZ-skSvJkioA7BbMyMZqIuDVBAebFYczNCNAJM0VKixfDVfLnhdo0plxXmad2kEjdIk01MEUN64cbsrgVlWJQOwaDd';
                    $img4 = $hinhAnhs->count() > 2 ? Storage::url($hinhAnhs[2]->duong_dan) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuCfnTVIpmnxpE0pdetcfCZ2GMfN5F0yivGeSsRcxiD7rBqLQE74yWLzaFDYm5kFq82e1RHUwK-PhICSALqS2DYMANoWXy_P0OwtlwoShLH7Qph3_oohL6bWg1e45CE6ysjbUE6jUdCAk9Pp7pz33obm5JKvdkL_yhOKl0dhugz0OpJ4SBiZ7eBY7AsUdiEk02wTOhXwHQPlCd48tbnR8l8iVyyeaJyVg4tX1Mg0eRC-N2o5akKrHUpI';
                @endphp
                <div class="bento-item-1 rounded-xl overflow-hidden cursor-pointer group relative">
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-all"></div>
                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ $biaUrl }}')"></div>
                </div>
                <div class="bento-item-2 rounded-xl overflow-hidden cursor-pointer group relative">
                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ $img2 }}')"></div>
                </div>
                <div class="bento-item-3 rounded-xl overflow-hidden cursor-pointer group relative">
                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ $img3 }}')"></div>
                </div>
                <div class="bento-item-4 rounded-xl overflow-hidden cursor-pointer group relative bg-on-background flex items-center justify-center text-white">
                    <div class="w-full h-full bg-cover bg-center opacity-40" style="background-image: url('{{ $img4 }}')"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/40">
                        <span class="material-symbols-outlined text-3xl">grid_view</span>
                        <span class="font-label-md text-label-md mt-1">Xem tất cả</span>
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
                        <h1 class="font-headline-lg text-headline-lg text-on-background font-black">{{ $quan->ten_quan }}</h1>
                        <span class="material-symbols-outlined text-tick-xanh fill-icon text-xl" title="Verified">verified</span>
                    </div>
                    <p class="text-on-surface-variant flex items-center gap-1 mb-4 font-body-lg text-body-lg">
                        <span class="material-symbols-outlined text-lg">location_on</span>
                        {{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_phuong_xa }}, {{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}
                    </p>
                    <div class="flex flex-wrap items-center gap-6 py-4 border-y border-surface-variant">
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-primary fill-icon">star</span>
                            <span class="font-bold text-lg">4.9</span>
                            <span class="text-on-surface-variant text-sm">(Đánh giá)</span>
                        </div>
                        <div class="flex items-center gap-1 text-on-surface-variant">
                            <span class="material-symbols-outlined">schedule</span>
                            <span class="text-sm">{{ $quan->gio_mo_cua }} - {{ $quan->gio_dong_cua }}</span>
                            <span class="text-tertiary-container font-semibold ml-2">Đang mở cửa</span>
                        </div>
                        <div class="flex items-center gap-1 text-on-surface-variant">
                            <span class="material-symbols-outlined">payments</span>
                            <span class="text-sm">{{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h4 class="font-bold text-lg mb-2">Giới thiệu:</h4>
                        <p class="text-on-surface-variant leading-relaxed">{{ $quan->mo_ta ?: 'Chưa có thông tin giới thiệu.' }}</p>
                    </div>
                </div>

                <!-- Tabbed Interface -->
                <div class="mb-8">
                    <div class="flex border-b border-surface-variant gap-8 overflow-x-auto no-scrollbar">
                        <button class="tab-active py-4 font-label-md text-label-md whitespace-nowrap px-2">THỰC ĐƠN</button>
                        <button class="text-on-surface-variant hover:text-primary py-4 font-label-md text-label-md whitespace-nowrap px-2 transition-colors">ĐÁNH GIÁ</button>
                        <button class="text-on-surface-variant hover:text-primary py-4 font-label-md text-label-md whitespace-nowrap px-2 transition-colors">THÔNG TIN CHI TIẾT</button>
                    </div>

                    <!-- Menu Section -->
                    <div class="mt-8 space-y-12">
                        @if(isset($quan->danhMucMenu) && $quan->danhMucMenu->count() > 0)
                            @foreach($quan->danhMucMenu as $danhMuc)
                                <div>
                                    <h3 class="font-title-md text-title-md text-on-background mb-6 flex items-center gap-2">
                                        <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                                        {{ $danhMuc->ten_danh_muc }}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($danhMuc->monAns as $monAn)
                                        <div class="bg-surface-card rounded-xl p-4 shadow-[0px_4px_20px_rgba(0,0,0,0.05)] flex gap-4 hover:shadow-md transition-shadow cursor-pointer">
                                            <div class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0">
                                                <img class="w-full h-full object-cover" src="{{ $monAn->hinh_anh ? Storage::url($monAn->hinh_anh) : 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=300&q=80' }}"/>
                                            </div>
                                            <div class="flex flex-col justify-between flex-1">
                                                <div>
                                                    <h4 class="font-body-lg text-body-lg font-bold">{{ $monAn->ten_mon }}</h4>
                                                    <p class="text-on-surface-variant text-sm line-clamp-2">{{ $monAn->mo_ta }}</p>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-primary font-bold">{{ number_format($monAn->gia_ban, 0, ',', '.') }}đ</span>
                                                    <button class="w-8 h-8 rounded-full bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center hover:bg-primary transition-colors hover:text-white">
                                                        <span class="material-symbols-outlined text-xl">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
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
                </div>
            </div>

            <!-- Sticky Sidebar -->
            <aside class="w-full lg:w-96">
                <div class="sticky-sidebar space-y-6">
                    <!-- CTA Card -->
                    <div class="bg-surface-card rounded-xl p-6 shadow-[0px_8px_24px_rgba(0,0,0,0.08)] border border-primary/10 overflow-hidden relative">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-primary/5 rounded-full -mr-8 -mt-8"></div>
                        <h3 class="font-title-md text-title-md text-on-background mb-4">Liên hệ & Đặt bàn</h3>
                        <p class="text-on-surface-variant font-body-sm text-body-sm mb-6">Liên hệ trực tiếp với quán để đặt bàn hoặc đặt giao hàng.</p>
                        @if($quan->so_dien_thoai)
                        <a href="tel:{{ $quan->so_dien_thoai }}" class="w-full bg-[#EE4D2D] text-white py-3 px-4 rounded-xl font-bold flex items-center justify-center gap-2 hover:brightness-110 active:scale-[0.98] transition-all shadow-md mb-3">
                            <span class="material-symbols-outlined fill-icon">call</span>
                            {{ $quan->so_dien_thoai }}
                        </a>
                        @endif
                        <button class="w-full bg-gray-100 text-gray-800 py-3 px-4 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-gray-200 active:scale-[0.98] transition-all">
                            <span class="material-symbols-outlined fill-icon">share</span>
                            Chia sẻ địa điểm
                        </button>
                    </div>

                    <!-- Map Card -->
                    <div class="bg-surface-card rounded-xl overflow-hidden shadow-[0px_4px_20px_rgba(0,0,0,0.05)]">
                        <div class="h-48 w-full bg-surface-variant relative">
                            <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&w=600&q=80')">
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
                                <a href="https://maps.google.com/?q={{ urlencode($quan->dia_chi_chi_tiet . ', ' . $quan->ten_phuong_xa . ', ' . $quan->ten_quan_huyen) }}" target="_blank" class="text-primary font-bold text-sm flex items-center gap-1 hover:underline">
                                    Mở Google Maps
                                    <span class="material-symbols-outlined text-sm">open_in_new</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- More Information Card -->
                    <div class="bg-surface-card rounded-xl p-6 shadow-[0px_4px_20px_rgba(0,0,0,0.05)]">
                        <h4 class="font-label-md text-label-md text-on-surface-variant mb-4 uppercase tracking-wider">Thông tin thêm</h4>
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
    <script>
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
