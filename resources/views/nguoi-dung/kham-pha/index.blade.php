@extends('layouts.app')

@php
    $seoSlug = match($danhMuc) {
        'Quán Nổi Bật' => 'quan-noi-bat',
        'Quán Mới'     => 'quan-moi',
        default        => 'kham-pha',
    };
    $seo = \App\Models\SeoSetting::forPage($seoSlug);
@endphp

@section('title', $seo->meta_title ?: (($danhMuc ? $danhMuc . ' - ' : '') . 'Khám phá quán ngon | Quán Mới'))

@push('seo')
    @if($seo->meta_description)
    <meta name="description" content="{{ $seo->meta_description }}">
    @endif
    @if($seo->meta_keywords)
    <meta name="keywords" content="{{ $seo->meta_keywords }}">
    @endif
    <meta property="og:title" content="{{ $seo->og_title ?: $seo->meta_title ?: 'Quán Mới' }}">
    <meta property="og:description" content="{{ $seo->og_description ?: $seo->meta_description ?: '' }}">
    @if($seo->og_image)
    <meta property="og:image" content="{{ $seo->og_image }}">
    @endif
    <meta property="og:type" content="website">
@endpush

@section('content')
<main class="pb-24 bg-surface-container-lowest min-h-screen">
    
    {{-- Header Banner --}}
    <div class="bg-primary/5 border-b border-primary/10">
        <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-10 py-10 md:py-16">
            <h1 class="text-3xl md:text-4xl font-black text-on-surface mb-3 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-4xl">travel_explore</span>
                @if(isset($tuKhoa) && $tuKhoa !== '')
                    Kết quả tìm kiếm: "{{ $tuKhoa }}"
                @else
                    {{ $danhMuc ? 'Khám phá: ' . $danhMuc : 'Khám phá tất cả quán ngon' }}
                @endif
            </h1>
            <p class="text-on-surface-variant text-lg mb-6">
                Tìm kiếm những địa điểm ẩm thực và giải trí tuyệt vời nhất.
            </p>

            {{-- Search Bar --}}
            <div class="max-w-2xl">
                <x-search-bar prefix="kp" />
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-10 mt-10">
        
        {{-- Sort Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <p class="text-on-surface-variant font-medium text-[15px]">Tìm thấy <span class="font-bold text-on-surface">{{ $quans->total() }}</span> kết quả</p>
            <div class="flex items-center gap-3">
                <label for="sap_xep" class="text-[14px] text-gray-500 font-medium whitespace-nowrap">Sắp xếp:</label>
                <select id="sap_xep" class="border border-gray-200 rounded-xl px-3 py-2 text-[14px] bg-white outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 text-gray-700 font-medium cursor-pointer transition-all" onchange="const url = new URL(window.location.href); url.searchParams.set('sap_xep', this.value); window.location.href = url.toString();">
                    <option value="" {{ empty($sapXep) ? 'selected' : '' }}>Độ liên quan / Mặc định</option>
                    <option value="near_me" {{ ($sapXep ?? '') === 'near_me' ? 'selected' : '' }}>Gần tôi nhất</option>
                    <option value="view_desc" {{ ($sapXep ?? '') === 'view_desc' ? 'selected' : '' }}>Xem nhiều nhất</option>
                    <option value="created_desc" {{ ($sapXep ?? '') === 'created_desc' ? 'selected' : '' }}>Mới đăng gần đây</option>
                </select>
            </div>
        </div>

        @if($quans->isEmpty())
            <div class="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">search_off</span>
                <h3 class="text-xl font-bold text-on-surface mb-2">Không tìm thấy quán nào</h3>
                <p class="text-on-surface-variant mb-6">Hiện tại chưa có quán nào thuộc danh mục này.</p>
                <a href="{{ route('kham-pha') }}" class="bg-primary text-white px-6 py-2.5 rounded-full font-semibold hover:bg-primary/90 transition-colors">
                    Xem tất cả các quán
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($quans as $quan)
                    <a href="{{ route('quan.detail', $quan->slug) }}" class="block bg-white rounded-2xl shadow-sm overflow-hidden group cursor-pointer hover:shadow-xl transition-all border border-gray-100 flex flex-col h-full">
                        <div class="relative h-48 w-full overflow-hidden flex-shrink-0">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $quan->anh_bia ? $quan->anh_bia : 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $quan->ten_quan }}"/>
                            
                            {{-- Optional badge --}}
                            <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-2.5 py-1 rounded-xl text-primary font-bold text-xs flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">star</span> 4.9
                            </div>
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-bold text-[17px] text-on-surface truncate group-hover:text-primary transition-colors pr-2">{{ $quan->ten_quan }}</h3>
                                <span class="material-symbols-outlined text-tick-xanh text-[18px] flex-shrink-0" style="font-variation-settings: 'FILL' 1;" title="Đã xác thực">verified</span>
                            </div>
                            <p class="text-text-muted text-[13px] flex items-start gap-1.5 mb-4 flex-grow">
                                <span class="material-symbols-outlined text-[16px] text-gray-400 mt-0.5">location_on</span>
                                <span class="line-clamp-2 leading-relaxed">{{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_phuong_xa }}, {{ $quan->ten_quan_huyen }}</span>
                            </p>
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto">
                                <div class="flex gap-1.5">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded-md text-gray-600 text-[12px] font-medium truncate max-w-[100px]">
                                        {{ str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}
                                    </span>
                                </div>
                                <span class="text-primary font-bold text-[13px] truncate">
                                    {{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12 flex justify-center">
                {{ $quans->withQueryString()->links() }}
            </div>
        @endif

    </div>
</main>
@endsection
