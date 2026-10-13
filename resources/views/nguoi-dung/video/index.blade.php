@extends('layouts.app')

@section('title', 'Video Review Thật - Quán Mới')

@section('content')
<div class="bg-gray-50 min-h-screen pt-24 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Video Review Thực Tế</h1>
            <p class="text-gray-500 mt-1">Khám phá các quán ăn qua góc nhìn chân thực nhất từ cộng đồng mạng.</p>
        </div>

        {{-- ===== KHU VỰC LỌC & TÌM KIẾM ===== --}}
        <form method="GET" action="{{ route('video-review.index') }}" id="filter-form">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-8">

                {{-- Thanh tìm kiếm --}}
                <div class="relative mb-4">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
                    <input type="text"
                           name="q"
                           id="search-input"
                           value="{{ request('q') }}"
                           placeholder="Tìm theo tên quán, món ăn..."
                           class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition placeholder-gray-400">
                </div>

                {{-- Bộ lọc hàng ngang --}}
                <div class="flex flex-wrap gap-3 items-center">

                    {{-- Lọc theo Quận/Huyện --}}
                    @if($danhSachQuanHuyen->isNotEmpty())
                    <div class="relative">
                        <select name="quan_huyen" id="select-quan-huyen"
                                onchange="document.getElementById('filter-form').submit()"
                                class="appearance-none pl-4 pr-9 py-2.5 text-sm font-medium border border-gray-200 rounded-xl bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary cursor-pointer hover:border-primary transition">
                            <option value="">📍 Tất cả khu vực</option>
                            @foreach($danhSachQuanHuyen as $qh)
                                <option value="{{ $qh }}" {{ request('quan_huyen') == $qh ? 'selected' : '' }}>
                                    {{ $qh }}
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-lg pointer-events-none">expand_more</span>
                    </div>
                    @endif

                    {{-- Lọc theo Loại hình --}}
                    @if($danhSachLoaiHinh->isNotEmpty())
                    <div class="relative">
                        <select name="loai_hinh" id="select-loai-hinh"
                                onchange="document.getElementById('filter-form').submit()"
                                class="appearance-none pl-4 pr-9 py-2.5 text-sm font-medium border border-gray-200 rounded-xl bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary cursor-pointer hover:border-primary transition">
                            <option value="">🍽️ Tất cả thể loại</option>
                            @foreach($danhSachLoaiHinh as $lh)
                                <option value="{{ $lh }}" {{ request('loai_hinh') == $lh ? 'selected' : '' }}>
                                    {{ $lh }}
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-lg pointer-events-none">expand_more</span>
                    </div>
                    @endif

                    {{-- Sắp xếp --}}
                    <div class="relative">
                        <select name="sort" id="select-sort"
                                onchange="document.getElementById('filter-form').submit()"
                                class="appearance-none pl-4 pr-9 py-2.5 text-sm font-medium border border-gray-200 rounded-xl bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary cursor-pointer hover:border-primary transition">
                            <option value="moi_nhat"  {{ request('sort', 'moi_nhat') == 'moi_nhat'  ? 'selected' : '' }}>🕐 Mới nhất</option>
                            <option value="nhieu_view" {{ request('sort') == 'nhieu_view' ? 'selected' : '' }}>🔥 Đang hot</option>
                            <option value="nhieu_like" {{ request('sort') == 'nhieu_like' ? 'selected' : '' }}>❤️ Yêu thích nhất</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-lg pointer-events-none">expand_more</span>
                    </div>

                    {{-- Nút xóa bộ lọc (chỉ hiện khi đang lọc) --}}
                    @if(request()->hasAny(['q', 'quan_huyen', 'loai_hinh']) || request('sort', 'moi_nhat') !== 'moi_nhat')
                    <a href="{{ route('video-review.index') }}"
                       class="inline-flex items-center gap-1.5 text-sm text-red-500 hover:text-red-700 font-medium py-2.5 px-4 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 transition ml-auto">
                        <span class="material-symbols-outlined text-base">close</span>
                        Xóa bộ lọc
                    </a>
                    @endif
                </div>

                {{-- Thông tin kết quả tìm kiếm --}}
                @if(request()->hasAny(['q', 'quan_huyen', 'loai_hinh']))
                <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-500">
                    Tìm thấy <span class="font-bold text-gray-800">{{ $videos->total() }}</span> video
                    @if(request('q'))
                        cho "<span class="font-semibold text-primary">{{ request('q') }}</span>"
                    @endif
                    @if(request('quan_huyen'))
                        tại <span class="font-semibold text-primary">{{ request('quan_huyen') }}</span>
                    @endif
                    @if(request('loai_hinh'))
                        • <span class="font-semibold text-primary">{{ request('loai_hinh') }}</span>
                    @endif
                </div>
                @endif
            </div>
        </form>

        {{-- ===== LƯỚI VIDEO ===== --}}
        @if($videos->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <span class="material-symbols-outlined text-6xl mb-4 block">videocam_off</span>
            <p class="text-xl font-semibold text-gray-500">Không tìm thấy video nào</p>
            <p class="text-sm mt-1">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm khác.</p>
            <a href="{{ route('video-review.index') }}" class="mt-4 inline-block text-primary font-medium hover:underline">Xem tất cả video</a>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="video-container">
            @foreach($videos as $video)
            @php
                $thumbnail = $video->thumbnail_url ?: ($video->quan?->anh_bia ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80');
            @endphp

            <div class="video-item bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-200 overflow-hidden cursor-pointer group flex flex-col h-[480px] transition-all duration-300 hover:-translate-y-1"
                 onclick="openVideoModal({{ $loop->index }})">

                {{-- Phần Trên: Ảnh Thumbnail --}}
                <div class="relative w-full overflow-hidden bg-gray-100" style="height: 350px;">
                    <img src="{{ $thumbnail }}"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                         alt="Video Thumbnail">

                    {{-- Nút Liên Kết Đến Quán --}}
                    @if($video->quan)
                    <div class="absolute top-4 left-4 z-20">
                        <a href="{{ route('quan.detail', $video->quan->slug) }}"
                           onclick="event.stopPropagation()"
                           class="inline-flex items-center gap-1.5 bg-black/60 backdrop-blur-md hover:bg-primary text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm max-w-[220px]">
                            <span class="material-symbols-outlined text-[16px]">location_on</span>
                            <span class="truncate">{{ $video->quan->ten_quan }}</span>
                        </a>
                    </div>
                    @endif

                    {{-- Lượt xem --}}
                    @if($video->luot_xem > 0)
                    <div class="absolute bottom-3 right-3 z-20">
                        <span class="inline-flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-md font-medium">
                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                            {{ number_format($video->luot_xem) }}
                        </span>
                    </div>
                    @endif

                    {{-- Icon Play Tròn Ở Giữa --}}
                    <div class="absolute inset-0 z-10 flex items-center justify-center pointer-events-none">
                        <div class="w-16 h-16 bg-black/60 rounded-full flex items-center justify-center backdrop-blur-sm group-hover:bg-primary group-hover:scale-110 transition-all duration-300 shadow-xl">
                            <span class="material-symbols-outlined text-white text-4xl ml-1">play_arrow</span>
                        </div>
                    </div>
                </div>

                {{-- Phần Dưới: Thông tin --}}
                <div class="p-4 flex flex-col flex-grow">
                    <p class="text-gray-900 font-bold text-[15px] leading-snug line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                        {{ $video->tieu_de ?: 'Review ' . ($video->quan ? $video->quan->ten_quan : 'Địa điểm') }}
                    </p>
                    @if($video->quan?->loai_hinh_kinh_doanh)
                    <span class="inline-block self-start text-xs bg-orange-50 text-orange-600 font-semibold px-2 py-0.5 rounded-full mb-2">
                        {{ $video->quan->loai_hinh_kinh_doanh }}
                    </span>
                    @endif
                    <div class="mt-auto flex items-center gap-2 text-gray-500 text-xs font-medium">
                        <div class="w-6 h-6 rounded-full bg-gray-200 overflow-hidden flex-shrink-0">
                            <img src="{{ $video->avatar_nguoi_dang ?: 'https://ui-avatars.com/api/?name='.urlencode($video->nguoi_dang ?: 'User').'&background=random' }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <span class="truncate font-semibold">{{ $video->nguoi_dang ?: '@reviewer' }}</span>
                        <span class="ml-auto flex-shrink-0">{{ $video->created_at ? $video->created_at->diffForHumans() : 'Mới đây' }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Phân trang --}}
        @if($videos->hasPages())
        <div class="mt-10">
            {{ $videos->links() }}
        </div>
        @endif
        @endif

    </div>
</div>

{{-- Modal Xem Video --}}
<div id="video-modal" class="fixed inset-0 hidden opacity-0 transition-opacity duration-300" style="display:none; z-index: 9999;">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="closeVideoModal()"></div>

    {{-- Nút Đóng ở góc trên phải màn hình --}}
    <button type="button" onclick="event.stopPropagation(); closeVideoModal()"
            class="fixed top-4 right-4 md:top-6 md:right-6 text-gray-900 bg-white hover:bg-gray-200 hover:scale-110 rounded-full w-12 h-12 flex items-center justify-center transition-transform shadow-2xl cursor-pointer"
            style="z-index: 10000;">
        <span class="material-symbols-outlined text-2xl font-black">close</span>
    </button>

    {{-- Nút Chuyển Trái / Phải --}}
    <button type="button" onclick="event.stopPropagation(); changeVideo(-1)" class="fixed left-2 md:left-8 top-1/2 -translate-y-1/2 text-gray-900 bg-white hover:bg-gray-200 hover:scale-110 rounded-full w-12 h-12 md:w-14 md:h-14 flex items-center justify-center transition-transform shadow-2xl cursor-pointer" style="z-index: 10000;">
        <span class="material-symbols-outlined text-3xl md:text-4xl font-black">chevron_left</span>
    </button>
    <button type="button" onclick="event.stopPropagation(); changeVideo(1)" class="fixed right-2 md:right-8 top-1/2 -translate-y-1/2 text-gray-900 bg-white hover:bg-gray-200 hover:scale-110 rounded-full w-12 h-12 md:w-14 md:h-14 flex items-center justify-center transition-transform shadow-2xl cursor-pointer" style="z-index: 10000;">
        <span class="material-symbols-outlined text-3xl md:text-4xl font-black">chevron_right</span>
    </button>

    {{-- Khung Video căn giữa hoàn hảo --}}
    <div class="fixed inset-0 flex items-center justify-center pointer-events-none" style="z-index: 10000;">
        <div class="pointer-events-auto rounded-2xl overflow-hidden shadow-2xl bg-black"
             style="width: 340px; height: min(680px, 86vh); position: relative;">

            {{-- Loading Spinner --}}
            <div id="video-loading" class="absolute inset-0 flex items-center justify-center bg-black" style="z-index: 10;">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-600 border-t-white"></div>
            </div>

            {{-- Nội dung Iframe --}}
            <div id="modal-video-container" class="w-full h-full" style="position: relative; z-index: 20;"></div>
        </div>
    </div>

<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
    const videoList = @json($videos->pluck('video_id'));
    let currentVideoIndex = 0;

    function openVideoModal(index) {
        currentVideoIndex = index;
        const videoId   = videoList[index];
        const modal     = document.getElementById('video-modal');
        const container = document.getElementById('modal-video-container');
        const loading   = document.getElementById('video-loading');

        modal.style.display = 'block';
        setTimeout(() => modal.classList.remove('opacity-0'), 10);
        loading.style.display = 'flex';
        container.innerHTML = '';

        const iframe = document.createElement('iframe');
        iframe.src             = `https://www.tiktok.com/embed/v2/${videoId}`;
        iframe.frameBorder     = '0';
        iframe.allow           = 'autoplay; clipboard-write; encrypted-media; picture-in-picture';
        iframe.allowFullscreen = true;
        iframe.style.cssText   = 'width:100%;height:100%;border:none;display:block;background:#000;';
        iframe.onload          = () => { loading.style.display = 'none'; };

        container.appendChild(iframe);
        document.body.style.overflow = 'hidden';
    }

    function closeVideoModal() {
        const modal     = document.getElementById('video-modal');
        const container = document.getElementById('modal-video-container');
        const loading   = document.getElementById('video-loading');

        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.style.display = 'none';
            container.innerHTML = '';
            loading.style.display = 'flex';
        }, 300);

        document.body.style.overflow = 'auto';
    }

    function changeVideo(dir) {
        if (videoList.length === 0) return;
        let newIndex = currentVideoIndex + dir;
        if (newIndex < 0) newIndex = videoList.length - 1;
        if (newIndex >= videoList.length) newIndex = 0;
        openVideoModal(newIndex);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeVideoModal();
        if (e.key === 'ArrowLeft')  changeVideo(-1);
        if (e.key === 'ArrowRight') changeVideo(1);
    });

    // Auto-submit khi gõ tìm kiếm (debounce 500ms)
    let searchTimeout;
    document.getElementById('search-input').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filter-form').submit();
        }, 500);
    });
</script>

@endsection
