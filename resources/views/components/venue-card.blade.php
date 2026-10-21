@props(['quan', 'savedQuanIds' => []])

@php
    $hasVideo = $quan->co_video_review ?? ($quan->relationLoaded('videos') ? $quan->videos->isNotEmpty() : (method_exists($quan, 'videos') && $quan->videos()->exists()));
    $isSaved = in_array($quan->id, $savedQuanIds ?? []);
    $isDangMo = method_exists($quan, 'isDangMoCua') && $quan->isDangMoCua();
@endphp

<a href="{{ route('quan.detail', $quan->slug) }}" class="block bg-white rounded-2xl shadow-sm overflow-hidden group cursor-pointer hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 flex flex-col h-full">
    {{-- Khối hình ảnh thumbnail & các huy hiệu trên ảnh --}}
    <div class="relative h-48 sm:h-52 w-full overflow-hidden flex-shrink-0">
        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
             src="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80' }}" 
             alt="{{ $quan->ten_quan }}" 
             loading="lazy" />
        
        {{-- Góc trên bên trái: Đánh giá sao --}}
        <div class="absolute top-3 left-3 z-10">
            <div class="bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-xl text-primary font-bold text-xs flex items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined text-[15px] text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span>
                <span>4.9</span>
            </div>
        </div>

        {{-- Góc dưới bên trái: Khoảng cách GPS (nếu có toạ độ người dùng) --}}
        @if(isset($quan->khoang_cach_km) && $quan->khoang_cach_km !== null)
            <div class="absolute bottom-3 left-3 z-10 bg-primary/95 text-white backdrop-blur-md px-2.5 py-1 rounded-xl font-bold text-xs flex items-center gap-1 shadow-sm" title="Cách vị trí của bạn {{ $quan->khoang_cach_km }} km">
                <span class="material-symbols-outlined text-[14px]">near_me</span>
                <span>{{ $quan->khoang_cach_km }} km</span>
            </div>
        @endif

        {{-- Góc trên bên phải: Các huy hiệu trạng thái (Video, Mở cửa, Quán Nổi bật) --}}
        <div class="absolute top-3 right-3 flex items-center gap-1 z-10">
            @if($hasVideo)
                <span class="bg-purple-600/95 backdrop-blur-md text-white px-2 py-0.5 rounded-lg text-[10.5px] font-bold flex items-center gap-0.5 shadow-sm" title="Có video review">
                    <span class="material-symbols-outlined text-[13px]">play_circle</span>
                    Video
                </span>
            @endif
            @if($isDangMo)
                <span class="bg-emerald-600/90 backdrop-blur-md text-white px-2 py-0.5 rounded-lg text-[10.5px] font-bold flex items-center gap-1 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                    Mở cửa
                </span>
            @endif
            @if($quan->is_noi_bat)
                <span class="bg-amber-500 text-white font-bold text-[10.5px] px-2 py-0.5 rounded-lg shadow-sm flex items-center gap-0.5" title="Quán nổi bật">
                    <span class="material-symbols-outlined text-[12px]" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                </span>
            @endif
        </div>

        {{-- Góc dưới bên phải: Nút Trái tim lưu quán --}}
        <button type="button" 
                data-quan-id="{{ $quan->id }}" 
                onclick="event.preventDefault(); toggleSave(this, '{{ $quan->id }}')" 
                class="absolute bottom-3 right-3 w-9 h-9 bg-white/90 backdrop-blur-md rounded-full flex items-center justify-center text-red-500 shadow-sm hover:scale-110 active:scale-90 transition-transform z-10" 
                title="{{ $isSaved ? 'Bỏ lưu quán' : 'Lưu quán' }}">
            <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' {{ $isSaved ? '1' : '0' }};">favorite</span>
        </button>
    </div>

    {{-- Khối nội dung thông tin quán --}}
    <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between gap-2.5">
        <div class="space-y-1.5">
            {{-- Tên quán + Tích xanh xác thực --}}
            <div class="flex items-center justify-between gap-1.5">
                <h3 class="min-w-0 flex-1 font-bold text-[16px] sm:text-[17px] text-on-surface break-words [overflow-wrap:anywhere] group-hover:text-primary transition-colors pr-1">{{ $quan->ten_quan }}</h3>
                @if($quan->is_xac_thuc)
                    <span class="material-symbols-outlined text-tick-xanh text-[18px] sm:text-[19px] flex-shrink-0" style="font-variation-settings: 'FILL' 1;" title="Đã xác thực">verified</span>
                @endif
            </div>

            {{-- Địa chỉ quán + Icon định vị --}}
            <p class="text-text-muted text-[13px] flex items-start gap-1">
                <span class="material-symbols-outlined text-[16px] text-gray-400 flex-shrink-0 mt-0.5">location_on</span>
                <span class="line-clamp-2 leading-relaxed">{{ $quan->dia_chi_chi_tiet }}, {{ $quan->ten_phuong_xa }}, {{ $quan->ten_quan_huyen }}</span>
            </p>
        </div>

        {{-- Chân thẻ: Loại hình ẩm thực + Lượt xem + ShopeeFood + Khoảng giá --}}
        <div class="flex items-center justify-between pt-2.5 border-t border-gray-100 gap-2 min-w-0 mt-auto">
            <div class="flex items-center gap-1.5 min-w-0 flex-shrink">
                {{-- Badge loại hình kinh doanh --}}
                <span class="bg-gray-100 px-2 py-0.5 rounded-md text-gray-600 text-[11px] font-medium whitespace-nowrap truncate max-w-[95px] xl:max-w-[120px]" title="{{ str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}">
                    {{ str_replace('_', ' ', \Illuminate\Support\Str::title($quan->loai_hinh_kinh_doanh ?? 'Quán ăn')) }}
                </span>

                {{-- Lượt xem --}}
                <span class="flex items-center gap-0.5 text-gray-500 text-[11px] whitespace-nowrap flex-shrink-0" title="{{ number_format($quan->luot_xem ?? 0) }} lượt xem">
                    <span class="material-symbols-outlined text-[13px]">visibility</span>
                    {{ number_format($quan->luot_xem ?? 0) }}
                </span>

                {{-- ShopeeFood (nếu có) --}}
                @if(!empty($quan->shopeefood_url))
                    <span class="bg-orange-50 text-[#EE4D2D] border border-orange-200/60 px-1.5 py-0.5 rounded text-[10.5px] font-bold whitespace-nowrap flex items-center gap-0.5" title="Có đặt trên ShopeeFood">
                        <span class="material-symbols-outlined text-[12px]">moped</span>
                    </span>
                @endif
            </div>

            {{-- Khoảng giá --}}
            <span class="text-primary font-bold text-[12.5px] xl:text-[13.5px] whitespace-nowrap flex-shrink-0 ml-auto">
                {{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ
            </span>
        </div>
    </div>
</a>
