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
                $thumbnail = $video->effective_thumbnail;
            @endphp

            <div class="video-item bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-200 overflow-hidden cursor-pointer group flex flex-col h-[480px] transition-all duration-300 hover:-translate-y-1"
                 onclick="openVideoModal({{ $loop->index }})">

                {{-- Phần Trên: Ảnh Thumbnail --}}
                <div class="relative w-full overflow-hidden bg-gray-100" style="height: 350px;">
                    <img src="{{ $thumbnail }}"
                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80';"
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

{{-- Modal Xem Video (Chỉ phát video sạch hoặc fallback Iframe) --}}
<div id="video-modal" class="fixed inset-0 hidden opacity-0 transition-opacity duration-300" style="display:none; z-index: 9999;">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/85 backdrop-blur-md" onclick="closeVideoModal()"></div>

    {{-- Nút Đóng ở góc trên phải màn hình --}}
    <button type="button" onclick="event.stopPropagation(); closeVideoModal()"
            class="fixed top-4 right-4 md:top-6 md:right-6 text-gray-900 bg-white hover:bg-gray-200 hover:scale-110 rounded-full w-12 h-12 flex items-center justify-center transition-transform shadow-2xl cursor-pointer"
            style="z-index: 10000;" title="Đóng (Esc)">
        <span class="material-symbols-outlined text-2xl font-black">close</span>
    </button>

    {{-- Nút Chuyển Trái / Phải --}}
    <button type="button" onclick="event.stopPropagation(); changeVideo(-1)" class="fixed left-2 md:left-8 top-1/2 -translate-y-1/2 text-gray-900 bg-white hover:bg-gray-200 hover:scale-110 rounded-full w-12 h-12 md:w-14 md:h-14 flex items-center justify-center transition-transform shadow-2xl cursor-pointer" style="z-index: 10000;" title="Video trước">
        <span class="material-symbols-outlined text-3xl md:text-4xl font-black">chevron_left</span>
    </button>
    <button type="button" onclick="event.stopPropagation(); changeVideo(1)" class="fixed right-2 md:right-8 top-1/2 -translate-y-1/2 text-gray-900 bg-white hover:bg-gray-200 hover:scale-110 rounded-full w-12 h-12 md:w-14 md:h-14 flex items-center justify-center transition-transform shadow-2xl cursor-pointer" style="z-index: 10000;" title="Video kế tiếp">
        <span class="material-symbols-outlined text-3xl md:text-4xl font-black">chevron_right</span>
    </button>

    {{-- Khung Video căn giữa hoàn hảo --}}
    <div class="fixed inset-0 flex items-center justify-center pointer-events-none" style="z-index: 10000;">
        <div class="pointer-events-auto rounded-3xl overflow-hidden shadow-2xl bg-black border border-white/10"
             style="width: min(480px, calc(90vh * 9 / 16), 94vw); height: min(860px, 90vh); aspect-ratio: 9/16; position: relative;">

            {{-- Loading Spinner --}}
            <div id="video-loading" class="absolute inset-0 flex items-center justify-center bg-black" style="z-index: 10;">
                <div class="flex flex-col items-center gap-3">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-600 border-t-orange-500"></div>
                    <span class="text-xs text-gray-400 font-medium">Đang tải video...</span>
                </div>
            </div>

            {{-- Container phát video (HTML5 thuần hoặc Iframe) --}}
            <div id="modal-video-container" class="w-full h-full relative" style="z-index: 20;"></div>
        </div>
    </div>
</div>

<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    @keyframes pingOnce {
        0% { transform: scale(0.8); opacity: 0; }
        50% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(1.4); opacity: 0; }
    }
    .animate-ping-once {
        animation: pingOnce 0.4s ease-out forwards;
    }
</style>

@php
    $jsonVideoData = $videos->map(function($v) {
        return [
            'id' => $v->id,
            'video_id' => $v->video_id,
            'clean_url' => $v->clean_video_url,
            'embed_url' => $v->embed_url,
            'watch_url' => $v->watch_url,
            'title' => $v->tieu_de,
            'author' => $v->nguoi_dang,
            'avatar' => $v->avatar_nguoi_dang,
            'views' => $v->luot_xem,
            'quan_name' => $v->quan?->ten_quan,
            'quan_url' => $v->quan ? route('quan.detail', $v->quan->slug) : null,
            'quan_tag' => $v->quan?->loai_hinh_kinh_doanh,
        ];
    })->values()->all();
@endphp

<script>
    const videoDataList = {!! json_encode($jsonVideoData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!};

    let currentVideoIndex = 0;
    let activeVideoPlayer = null;
    let isMuted = false;

    function openVideoModal(index) {
        if (!videoDataList || videoDataList.length === 0) return;
        currentVideoIndex = index;
        const video     = videoDataList[index];
        const modal     = document.getElementById('video-modal');
        const container = document.getElementById('modal-video-container');
        const loading   = document.getElementById('video-loading');

        modal.style.display = 'block';
        setTimeout(() => modal.classList.remove('opacity-0'), 10);
        loading.style.display = 'flex';
        container.innerHTML = '';
        activeVideoPlayer = null;

        // ƯU TIÊN 1: Phát bằng video HTML5 thuần nếu có link video MP4 sạch (Không dính bất kỳ nút/chữ TikTok nào)
        if (video.clean_url) {
            const playerWrapper = document.createElement('div');
            playerWrapper.className = 'relative w-full h-full bg-black flex items-center justify-center select-none overflow-hidden';

            const videoElem = document.createElement('video');
            videoElem.src = video.clean_url;
            videoElem.className = 'w-full h-full object-cover';
            videoElem.autoplay = true;
            videoElem.loop = true;
            videoElem.playsInline = true;
            videoElem.muted = isMuted;
            videoElem.preload = 'auto';

            // Top overlay: Quán ăn & Nút âm lượng
            const topBar = document.createElement('div');
            topBar.className = 'absolute top-3 inset-x-3 flex items-center justify-between z-30 pointer-events-none';
            topBar.innerHTML = `
                ${video.quan_name ? `
                    <a href="${video.quan_url}" onclick="event.stopPropagation()" class="pointer-events-auto inline-flex items-center gap-1.5 bg-black/60 hover:bg-orange-600 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-full transition shadow-lg max-w-[240px]">
                        <span class="material-symbols-outlined text-sm text-orange-400">restaurant</span>
                        <span class="truncate">${video.quan_name}</span>
                    </a>
                ` : '<div></div>'}
                <div class="flex items-center gap-2 pointer-events-auto">
                    <button type="button" id="btn-toggle-sound" class="w-9 h-9 rounded-full bg-black/60 hover:bg-black/80 backdrop-blur-md text-white flex items-center justify-center transition shadow-lg cursor-pointer">
                        <span class="material-symbols-outlined text-lg" id="icon-sound">${isMuted ? 'volume_off' : 'volume_up'}</span>
                    </button>
                </div>
            `;

            // Center indicator khi click play/pause
            const centerIcon = document.createElement('div');
            centerIcon.className = 'absolute inset-0 flex items-center justify-center pointer-events-none z-20';
            centerIcon.innerHTML = `
                <div id="play-pause-bubble" class="w-16 h-16 rounded-full bg-black/60 backdrop-blur-md flex items-center justify-center text-white opacity-0 transition-opacity">
                    <span class="material-symbols-outlined text-4xl" id="play-pause-icon">pause</span>
                </div>
            `;

            // Bottom overlay: Tiêu đề, tác giả & Thanh tiến trình
            const bottomBar = document.createElement('div');
            bottomBar.className = 'absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent p-4 pt-10 z-30 pointer-events-none';
            bottomBar.innerHTML = `
                <p class="text-white font-bold text-sm leading-snug line-clamp-2 drop-shadow-md mb-1">${video.title || 'Video Review'}</p>
                <div class="flex items-center gap-2 text-white/80 text-xs">
                    <span class="font-semibold">${video.author || '@reviewer'}</span>
                    ${video.views > 0 ? `<span>• ${Number(video.views).toLocaleString('vi-VN')} lượt xem</span>` : ''}
                </div>
                <div id="video-progress-container" class="mt-2.5 w-full bg-white/20 hover:bg-white/40 h-1.5 rounded-full overflow-hidden cursor-pointer pointer-events-auto relative">
                    <div id="video-progress-bar" class="bg-orange-500 h-full w-0 transition-all duration-75"></div>
                </div>
            `;

            playerWrapper.appendChild(videoElem);
            playerWrapper.appendChild(centerIcon);
            playerWrapper.appendChild(topBar);
            playerWrapper.appendChild(bottomBar);
            container.appendChild(playerWrapper);

            activeVideoPlayer = videoElem;

            // Xử lý sự kiện video
            videoElem.onloadeddata = () => { loading.style.display = 'none'; };
            videoElem.onerror = () => {
                loading.style.display = 'none';
                console.warn('Lỗi phát MP4, tự động chuyển về nhúng Iframe TikTok');
                container.innerHTML = '';
                const iframe = document.createElement('iframe');
                iframe.src = `https://www.tiktok.com/embed/v2/${video.video_id}`;
                iframe.frameBorder = '0';
                iframe.allow = 'autoplay; clipboard-write; encrypted-media; picture-in-picture';
                iframe.allowFullscreen = true;
                iframe.style.cssText = 'width:100%;height:100%;border:none;display:block;background:#000;';
                container.appendChild(iframe);
            };
            videoElem.ontimeupdate = () => {
                const prog = document.getElementById('video-progress-bar');
                if (prog && videoElem.duration) {
                    prog.style.width = `${(videoElem.currentTime / videoElem.duration) * 100}%`;
                }
            };

            // Tự động phát mượt mà (nếu trình duyệt chặn âm thanh thì phát ở chế độ tắt tiếng)
            videoElem.play().catch(() => {
                videoElem.muted = true;
                isMuted = true;
                const icon = document.getElementById('icon-sound');
                if (icon) icon.textContent = 'volume_off';
                videoElem.play().catch(() => {});
            });

            // Click vào video để dừng/phát
            playerWrapper.onclick = (e) => {
                if (e.target.closest('#btn-toggle-sound') || e.target.closest('#video-progress-container') || e.target.closest('a')) return;
                togglePlayPause(videoElem);
            };

            // Nút bật/tắt tiếng
            const btnSound = topBar.querySelector('#btn-toggle-sound');
            if (btnSound) {
                btnSound.onclick = (e) => {
                    e.stopPropagation();
                    isMuted = !isMuted;
                    videoElem.muted = isMuted;
                    document.getElementById('icon-sound').textContent = isMuted ? 'volume_off' : 'volume_up';
                };
            }

            // Click vào thanh timeline để tua
            const progContainer = bottomBar.querySelector('#video-progress-container');
            if (progContainer) {
                progContainer.onclick = (e) => {
                    e.stopPropagation();
                    const rect = progContainer.getBoundingClientRect();
                    const pos = (e.clientX - rect.left) / rect.width;
                    if (videoElem.duration) {
                        videoElem.currentTime = pos * videoElem.duration;
                    }
                };
            }
        }
        // ƯU TIÊN 2: Fallback Iframe nếu chưa có bản MP4 sạch
        else {
            const iframe = document.createElement('iframe');
            iframe.src             = `https://www.tiktok.com/embed/v2/${video.video_id}`;
            iframe.frameBorder     = '0';
            iframe.allow           = 'autoplay; clipboard-write; encrypted-media; picture-in-picture';
            iframe.allowFullscreen = true;
            iframe.style.cssText   = 'width:100%;height:100%;border:none;display:block;background:#000;';
            iframe.onload          = () => { loading.style.display = 'none'; };
            container.appendChild(iframe);
        }

        document.body.style.overflow = 'hidden';
    }

    function togglePlayPause(videoElem) {
        if (!videoElem) return;
        const bubble = document.getElementById('play-pause-bubble');
        const icon = document.getElementById('play-pause-icon');

        if (videoElem.paused) {
            videoElem.play();
            if (icon) icon.textContent = 'play_arrow';
        } else {
            videoElem.pause();
            if (icon) icon.textContent = 'pause';
        }

        if (bubble) {
            bubble.classList.remove('opacity-0');
            setTimeout(() => bubble.classList.add('opacity-0'), 500);
        }
    }

    function closeVideoModal() {
        const modal     = document.getElementById('video-modal');
        const container = document.getElementById('modal-video-container');
        const loading   = document.getElementById('video-loading');

        if (activeVideoPlayer) {
            activeVideoPlayer.pause();
            activeVideoPlayer.src = '';
            activeVideoPlayer = null;
        }

        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.style.display = 'none';
            container.innerHTML = '';
            loading.style.display = 'flex';
        }, 300);

        document.body.style.overflow = 'auto';
    }

    function changeVideo(dir) {
        if (!videoDataList || videoDataList.length === 0) return;
        let newIndex = currentVideoIndex + dir;
        if (newIndex < 0) newIndex = videoDataList.length - 1;
        if (newIndex >= videoDataList.length) newIndex = 0;
        openVideoModal(newIndex);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeVideoModal();
        if (e.key === 'ArrowLeft')  changeVideo(-1);
        if (e.key === 'ArrowRight') changeVideo(1);
        if (e.key === ' ' && activeVideoPlayer) {
            e.preventDefault();
            togglePlayPause(activeVideoPlayer);
        }
    });

    // Auto-submit khi gõ tìm kiếm (debounce 500ms)
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filter-form').submit();
            }, 500);
        });
    }
</script>

@endsection
