@props(['quan', 'savedQuanIds' => []])

@php
    $hasVideo = $quan->co_video_review ?? ($quan->relationLoaded('videos') ? $quan->videos->isNotEmpty() : (method_exists($quan, 'videos') && $quan->videos()->exists()));
    $isSaved = in_array($quan->id, $savedQuanIds ?? []);
    $isDangMo = method_exists($quan, 'isDangMoCua') && $quan->isDangMoCua();
    $tenLoaiHinh = str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn'));
    $ratingScore = isset($quan->danh_gia_avg_so_sao) && (float) $quan->danh_gia_avg_so_sao > 0
        ? number_format((float) $quan->danh_gia_avg_so_sao, 1)
        : '4.9';
@endphp

<a href="{{ route('quan.detail', $quan->slug) }}" class="qm-venue-card group cursor-pointer">
    {{-- Khối hình ảnh thumbnail & các huy hiệu trên ảnh --}}
    <div class="qm-venue-thumb">
        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
             src="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80' }}"
             alt="{{ $quan->ten_quan }}"
             loading="lazy" />

        {{-- Góc trên bên trái: Đánh giá sao --}}
        <div class="qm-venue-pos-tl">
            <div class="qm-venue-badge-rating">
                <span class="material-symbols-outlined text-[13px] text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span>
                <span>{{ $ratingScore }}</span>
            </div>
        </div>

        {{-- Góc trên bên phải: Các huy hiệu trạng thái (Video, Mở cửa, Quán Nổi bật) --}}
        <div class="qm-venue-pos-tr">
            @if($hasVideo)
                <span class="qm-venue-badge-status bg-purple-600/95 text-white" title="Có video review">
                    <span class="material-symbols-outlined text-[12px]" style="font-variation-settings: 'FILL' 1;">play_circle</span>
                    <span class="hidden sm:inline">Video</span>
                </span>
            @endif
            @if($isDangMo)
                <span class="qm-venue-badge-status bg-emerald-600/95 text-white">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                    <span>Mở cửa</span>
                </span>
            @endif
            @if($quan->is_noi_bat)
                <span class="qm-venue-badge-hot" title="Quán nổi bật">
                    <span class="material-symbols-outlined text-[13px]" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                </span>
            @endif
        </div>

        {{-- Góc dưới bên trái: Tên danh mục quán & Khoảng cách GPS (nếu có) --}}
        <div class="qm-venue-pos-bl">
            <span class="qm-venue-badge-category truncate" title="{{ $tenLoaiHinh }}">
                <span class="material-symbols-outlined text-[12px] text-primary shrink-0">storefront</span>
                <span class="truncate">{{ $tenLoaiHinh }}</span>
            </span>
            @if(isset($quan->khoang_cach_km) && $quan->khoang_cach_km !== null)
                <span class="qm-venue-badge-status bg-primary/95 text-white whitespace-nowrap" title="Cách vị trí của bạn {{ $quan->khoang_cach_km }} km">
                    <span class="material-symbols-outlined text-[11px]" style="font-variation-settings: 'FILL' 1;">near_me</span>
                    <span>{{ $quan->khoang_cach_km }} km</span>
                </span>
            @endif
        </div>

        {{-- Góc dưới bên phải: Nút Trái tim lưu quán --}}
        <button type="button"
                data-quan-id="{{ $quan->id }}"
                data-saved="{{ $isSaved ? '1' : '0' }}"
                onclick="event.preventDefault(); toggleSave(this, '{{ $quan->id }}')"
                class="qm-venue-save-btn cursor-pointer"
                title="{{ $isSaved ? 'Bỏ lưu quán' : 'Lưu quán' }}">
            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' {{ $isSaved ? '1' : '0' }};">favorite</span>
        </button>
    </div>

    {{-- Khối nội dung thông tin quán --}}
    <div class="qm-venue-body">
        <div class="space-y-1 sm:space-y-2">
            {{-- Tên quán + Tích xanh xác thực --}}
            <div class="flex items-start justify-between gap-1 sm:gap-2">
                <h3 class="min-w-0 flex-1 font-bold text-[13px] sm:text-[16px] leading-snug text-slate-900 line-clamp-1 group-hover:text-primary transition-colors">
                    {{ $quan->ten_quan }}
                </h3>
                @if($quan->is_xac_thuc)
                    <span class="inline-flex items-center justify-center w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-emerald-50 text-emerald-600 shrink-0 mt-0.5" title="Quán đã xác thực uy tín">
                        <span class="material-symbols-outlined text-[13px] sm:text-[15px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                    </span>
                @endif
            </div>

            {{-- Địa chỉ quán + Icon định vị chuyên nghiệp --}}
            <div class="flex items-start gap-1.5 sm:gap-2 text-slate-500 text-[11px] sm:text-[12.5px]">
                <span class="qm-venue-loc-icon">
                    <span class="material-symbols-outlined text-[11px] sm:text-[13px]" style="font-variation-settings: 'FILL' 1;">location_on</span>
                </span>
                <span class="line-clamp-1 sm:line-clamp-2 leading-relaxed text-slate-600">{{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_phuong_xa }}, {{ $quan->ten_quan_huyen }}</span>
            </div>
        </div>

        {{-- Chân thẻ: Lượt xem + ShopeeFood + Khoảng giá --}}
        <div class="flex flex-wrap items-center justify-between pt-2 sm:pt-2.5 border-t border-slate-100 gap-1 sm:gap-2 min-w-0 mt-auto">
            <div class="flex items-center gap-1.5 sm:gap-2 min-w-0 flex-shrink">
                {{-- Lượt xem --}}
                <span class="inline-flex items-center gap-1 text-slate-500 text-[10.5px] sm:text-[12px] font-medium whitespace-nowrap flex-shrink-0" title="{{ number_format($quan->luot_xem ?? 0) }} lượt xem">
                    <span class="material-symbols-outlined text-[12px] sm:text-[14px] text-slate-400">visibility</span>
                    <span>{{ number_format($quan->luot_xem ?? 0, 0, ',', '.') }}</span>
                </span>

                {{-- ShopeeFood (nếu có) --}}
                @if(!empty($quan->shopeefood_url))
                    <span class="bg-orange-50 text-[#EE4D2D] border border-orange-200/60 px-1 sm:px-1.5 py-0.5 rounded-md text-[10px] sm:text-[10.5px] font-bold whitespace-nowrap inline-flex items-center gap-0.5" title="Có đặt trên ShopeeFood">
                        <span class="material-symbols-outlined text-[11px] sm:text-[12px]">moped</span>
                    </span>
                @endif
            </div>

            {{-- Khoảng giá --}}
            <span class="qm-venue-price-pill ml-auto">
                <span>{{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ</span>
            </span>
        </div>
    </div>
</a>
