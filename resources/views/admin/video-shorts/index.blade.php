@extends('admin.layout')

@section('title', 'Phê Duyệt Video TikTok Review - Quán Mới Admin')
@section('page-title', 'Phê Duyệt Video TikTok / Shorts Review')

@section('content')
<div class="space-y-6">

    {{-- Tiêu đề trang & Thanh trạng thái thời gian thực --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500 text-3xl">verified</span>
                Phê Duyệt Video TikTok Review
            </h1>
            <p class="text-xs text-slate-500 mt-1">Kiểm duyệt, phê duyệt và quản lý video review TikTok do các quán ăn gửi lên hệ thống. Tự động nhận yêu cầu mới theo thời gian thực.</p>
        </div>

        <div class="flex items-center gap-2.5">
            {{-- Đèn báo Realtime --}}
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Thời gian thực (Realtime)</span>
            </div>

            {{-- Nút đồng bộ thủ công --}}
            <button type="button" onclick="syncVideoShortsGrid(null, true)" id="btn-manual-sync" class="px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-1 cursor-pointer shadow-2xs" title="Nhấn để kiểm tra và nạp video mới ngay lập tức">
                <span class="material-symbols-outlined text-[16px]" id="sync-icon">sync</span>
                <span>Làm mới</span>
            </button>

            <a href="{{ route('video-review.index') }}" target="_blank" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                <span class="material-symbols-outlined text-[17px]">open_in_new</span>
                <span>Xem trang khách</span>
            </a>
        </div>
    </div>

    {{-- 4 Thẻ chỉ số nhanh --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">pending_actions</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Chờ phê duyệt</p>
                <p id="stat-pending-videos" class="text-xl font-black text-amber-600">{{ number_format($pendingVideos) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">check_circle</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Đã phê duyệt</p>
                <p id="stat-active-videos" class="text-xl font-black text-emerald-600">{{ number_format($activeVideos) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">video_library</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng video quán</p>
                <p id="stat-total-videos" class="text-xl font-black text-slate-900">{{ number_format($totalVideos) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">visibility</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng lượt xem</p>
                <p id="stat-total-views" class="text-xl font-black text-slate-900">{{ number_format($totalViews) }}</p>
            </div>
        </div>
    </div>

    {{-- Bộ lọc & Tìm kiếm --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs space-y-3">
        {{-- Quick status tabs --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-100 pb-3">
            <a href="{{ route('admin.video-shorts.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ !request('trang_thai') || request('trang_thai') === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                <span>Tất cả</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ !request('trang_thai') || request('trang_thai') === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $totalVideos }}</span>
            </a>

            <a href="{{ route('admin.video-shorts.index', ['trang_thai' => 'cho_duyet']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request('trang_thai') === 'cho_duyet' ? 'bg-amber-500 text-slate-950 font-black ring-2 ring-amber-400' : 'bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200' }}">
                <span class="material-symbols-outlined text-[15px]">pending_actions</span>
                <span>Chờ duyệt</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-200/80 text-amber-900 font-black">{{ $pendingVideos }}</span>
            </a>

            <a href="{{ route('admin.video-shorts.index', ['trang_thai' => 'da_duyet']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request('trang_thai') === 'da_duyet' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                <span class="material-symbols-outlined text-[15px]">check_circle</span>
                <span>Đã duyệt</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-200 text-emerald-900">{{ $activeVideos }}</span>
            </a>

            <a href="{{ route('admin.video-shorts.index', ['trang_thai' => 'an']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request('trang_thai') === 'an' ? 'bg-slate-700 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                <span class="material-symbols-outlined text-[15px]">visibility_off</span>
                <span>Tạm ẩn</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 text-slate-700">{{ $hiddenVideos }}</span>
            </a>
        </div>

        <form method="GET" action="{{ route('admin.video-shorts.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Tìm kiếm từ khóa --}}
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Tìm kiếm video</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nhập tên video, người đăng, ID hoặc quán..." class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Trạng thái duyệt</label>
                <select name="trang_thai" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-white">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="cho_duyet" {{ request('trang_thai') === 'cho_duyet' ? 'selected' : '' }}>🟡 Chờ duyệt ({{ $pendingVideos }})</option>
                    <option value="da_duyet" {{ request('trang_thai') === 'da_duyet' ? 'selected' : '' }}>🟢 Đã duyệt</option>
                    <option value="an" {{ request('trang_thai') === 'an' ? 'selected' : '' }}>⚪ Tạm ẩn</option>
                </select>
            </div>

            {{-- Chọn quán ăn --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Quán ăn</label>
                <select name="quan_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-white">
                    <option value="">Tất cả quán</option>
                    @foreach($danhSachQuan as $q)
                        <option value="{{ $q->id }}" {{ request('quan_id') == $q->id ? 'selected' : '' }}>
                            {{ $q->ten_quan }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nút lọc & sắp xếp --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Lọc danh sách</span>
                </button>
                <a href="{{ route('admin.video-shorts.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition flex items-center justify-center" title="Đặt lại bộ lọc">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Vùng chứa lưới danh sách Video (Có ID để AJAX Realtime thay thế mà không cần load lại trang) --}}
    <div id="videos-grid-container">
        @if($videos->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
                <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-3xl">smart_display</span>
                </div>
                <h3 class="text-base font-bold text-slate-800">Không có video review nào cần xử lý</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Khi các chủ quán đăng tải video review TikTok từ trang quản lý quán, yêu cầu duyệt sẽ tự động xuất hiện tại đây ngay tức thì mà bạn không cần tải lại trang.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($videos as $video)
                    <div id="video-card-{{ $video->id }}" class="bg-white rounded-2xl border {{ $video->trang_thai === 'cho_duyet' ? 'border-amber-300 ring-2 ring-amber-400/30' : 'border-slate-200/80' }} shadow-xs hover:shadow-md transition-all flex flex-col justify-between overflow-hidden group">
                        {{-- Phần preview trên --}}
                        <div>
                            <div class="relative aspect-[9/12] bg-slate-950 overflow-hidden flex items-center justify-center">
                                @if($video->effective_thumbnail)
                                    <img src="{{ $video->effective_thumbnail }}"
                                         alt="{{ $video->tieu_de }}"
                                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80';"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-purple-900/60 to-slate-900 flex flex-col items-center justify-center p-4 text-center">
                                        <span class="material-symbols-outlined text-4xl text-purple-300 mb-2">play_circle</span>
                                        <span class="text-xs font-mono text-purple-200/70">ID: {{ $video->video_id }}</span>
                                    </div>
                                @endif

                                {{-- Overlay Play Button --}}
                                <button type="button" onclick="previewVideo('{{ $video->video_id }}', '{{ $video->clean_video_url }}', '{{ addslashes($video->tieu_de) }}', '{{ $video->quan?->ten_quan ?? 'Chưa liên kết quán' }}')" class="absolute inset-0 bg-black/30 hover:bg-black/50 flex items-center justify-center transition-all cursor-pointer">
                                    <div class="w-12 h-12 rounded-full bg-white/90 hover:bg-white text-slate-900 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                                    </div>
                                </button>

                                {{-- Badges trên ảnh --}}
                                <div class="absolute top-2.5 left-2.5 flex flex-col gap-1 items-start">
                                    <span id="status-badge-{{ $video->id }}" class="px-2 py-0.5 rounded-full text-[10px] font-bold shadow-xs {{ $video->trang_thai === 'da_duyet' ? 'bg-emerald-500 text-white' : ($video->trang_thai === 'cho_duyet' ? 'bg-amber-500 text-slate-950 font-black animate-pulse' : 'bg-slate-600 text-white') }}">
                                        {{ $video->trang_thai === 'da_duyet' ? '🟢 Đã duyệt' : ($video->trang_thai === 'cho_duyet' ? '🟡 Chờ duyệt' : '⚪ Đang ẩn') }}
                                    </span>
                                    @if($video->has_clean_video)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shadow-xs bg-emerald-600/90 text-white backdrop-blur-xs flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[12px]">videocam</span> MP4 Sạch
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shadow-xs bg-slate-800/80 text-slate-200 backdrop-blur-xs flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[12px]">code</span> Iframe
                                        </span>
                                    @endif
                                </div>

                                {{-- Thống kê views/likes --}}
                                <div class="absolute bottom-2.5 left-2.5 right-2.5 flex items-center justify-between text-[11px] font-bold text-white bg-black/60 backdrop-blur-xs px-2.5 py-1 rounded-lg">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">visibility</span>
                                        {{ number_format($video->luot_xem) }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px] text-rose-400" style="font-variation-settings: 'FILL' 1;">favorite</span>
                                        {{ number_format($video->luot_thich) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Thông tin nội dung --}}
                            <div class="p-4 space-y-2">
                                <h3 class="text-xs font-bold text-slate-900 line-clamp-2 leading-relaxed" title="{{ $video->tieu_de }}">
                                    {{ $video->tieu_de }}
                                </h3>

                                <div class="text-[11px] text-slate-500 space-y-1.5 pt-1.5 border-t border-slate-100">
                                    <p class="flex items-center gap-1.5 text-purple-700 font-bold truncate" title="{{ $video->quan?->ten_quan ?? 'Chưa liên kết quán' }}">
                                        <span class="material-symbols-outlined text-[16px] text-purple-600 shrink-0">storefront</span>
                                        <span class="truncate">{{ $video->quan?->ten_quan ?? 'Chưa liên kết quán' }}</span>
                                    </p>
                                    <p class="flex items-center gap-1 text-slate-600 font-semibold truncate">
                                        <span class="material-symbols-outlined text-[14px] text-slate-400 shrink-0">person</span>
                                        <span class="truncate">{{ $video->nguoi_dang ?: '@creator' }}</span>
                                    </p>
                                    <p class="text-[10px] text-slate-400 flex items-center justify-between">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px]">schedule</span>
                                            <span>{{ $video->created_at?->diffForHumans() }}</span>
                                        </span>
                                        <span>{{ $video->created_at?->format('d/m/Y H:i') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Nút thao tác duyệt dưới cùng --}}
                        <div class="p-3 bg-slate-50 border-t border-slate-100 flex flex-col gap-2">
                            {{-- Khối thao tác chính: Duyệt / Từ chối / Ẩn --}}
                            <div id="action-buttons-{{ $video->id }}" class="flex items-center gap-1.5 w-full">
                                @if($video->trang_thai === 'cho_duyet')
                                    <button type="button"
                                            id="btn-approve-{{ $video->id }}"
                                            onclick="approveVideo('{{ $video->id }}')"
                                            class="flex-1 py-1.5 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs">
                                        <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                        <span>Phê duyệt</span>
                                    </button>
                                    <button type="button"
                                            id="btn-reject-{{ $video->id }}"
                                            onclick="rejectVideo('{{ $video->id }}')"
                                            class="py-1.5 px-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200">
                                        <span class="material-symbols-outlined text-[15px]">close</span>
                                        <span>Từ chối</span>
                                    </button>
                                @elseif($video->trang_thai === 'da_duyet')
                                    <button type="button"
                                            id="btn-toggle-{{ $video->id }}"
                                            onclick="toggleVideoStatus('{{ $video->id }}')"
                                            class="flex-1 py-1.5 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer bg-slate-200 hover:bg-slate-300 text-slate-700">
                                        <span class="material-symbols-outlined text-[15px]">visibility_off</span>
                                        <span id="btn-toggle-text-{{ $video->id }}">Tạm ẩn video</span>
                                    </button>
                                @else
                                    <button type="button"
                                            id="btn-toggle-{{ $video->id }}"
                                            onclick="toggleVideoStatus('{{ $video->id }}')"
                                            class="flex-1 py-1.5 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer bg-emerald-100 hover:bg-emerald-200 text-emerald-800">
                                        <span class="material-symbols-outlined text-[15px]">check</span>
                                        <span id="btn-toggle-text-{{ $video->id }}">Duyệt lại</span>
                                    </button>
                                @endif
                            </div>

                            {{-- Khối nút phụ: Đồng bộ MP4 sạch, Sửa, Xóa --}}
                            <div class="flex items-center justify-end gap-1 pt-1 border-t border-slate-200/60">
                                @if(! $video->has_clean_video)
                                    <button type="button"
                                            id="btn-sync-{{ $video->id }}"
                                            onclick="syncCleanVideo('{{ $video->id }}')"
                                            class="px-2 py-1 text-amber-700 hover:text-amber-900 hover:bg-amber-100 rounded-lg transition border border-amber-200 bg-amber-50 text-[11px] font-bold flex items-center gap-0.5 cursor-pointer"
                                            title="Tải video MP4 sạch không logo TikTok">
                                        <span class="material-symbols-outlined text-[15px]">cloud_download</span>
                                        <span>Tải sạch</span>
                                    </button>
                                @endif

                                <button type="button"
                                        onclick="openEditVideoModal({{ json_encode($video) }})"
                                        class="p-1 text-slate-500 hover:text-slate-800 hover:bg-white rounded-lg transition border border-transparent hover:border-slate-200 cursor-pointer"
                                        title="Chỉnh sửa thông tin">
                                    <span class="material-symbols-outlined text-[17px]">edit</span>
                                </button>

                                <button type="button"
                                        onclick="deleteVideo('{{ $video->id }}', '{{ addslashes($video->tieu_de) }}')"
                                        class="p-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition border border-transparent hover:border-rose-200 cursor-pointer"
                                        title="Xóa video khỏi hệ thống">
                                    <span class="material-symbols-outlined text-[17px]">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Phân trang --}}
            <div class="mt-6">
                {{ $videos->links() }}
            </div>
        @endif
    </div>

</div>

{{-- MODAL: CHỈNH SỬA THÔNG TIN VIDEO --}}
<div id="edit-video-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">edit_note</span>
                Chỉnh Sửa Video Review
            </h3>
            <button type="button" onclick="closeEditVideoModal()" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form id="edit-video-form" method="POST" enctype="multipart/form-data" class="space-y-3.5">
            @csrf
            @method('PUT')

            {{-- Link TikTok / ID --}}
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1">Đường dẫn link TikTok</label>
                <input type="text"
                       name="video_input"
                       id="edit-video-input"
                       placeholder="Để trống nếu không muốn đổi link video"
                       class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
            </div>

            {{-- Tiêu đề video --}}
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1">Tiêu đề review <span class="text-rose-500">*</span></label>
                <input type="text"
                       name="tieu_de"
                       id="edit-tieu-de"
                       required
                       class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
            </div>

            {{-- Quán ăn liên kết --}}
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1">Quán ăn liên kết</label>
                <select name="quan_id" id="edit-quan-id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white">
                    <option value="">-- Không liên kết quán cụ thể --</option>
                    @foreach($danhSachQuan as $q)
                        <option value="{{ $q->id }}">
                            {{ $q->ten_quan }} ({{ $q->ten_quan_huyen }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kênh người đăng & Trạng thái --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1">Kênh người đăng</label>
                    <input type="text"
                           name="nguoi_dang"
                           id="edit-nguoi-dang"
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1">Trạng thái phê duyệt</label>
                    <select name="trang_thai" id="edit-trang-thai" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white">
                        <option value="da_duyet">🟢 Đã duyệt</option>
                        <option value="cho_duyet">🟡 Chờ duyệt</option>
                        <option value="an">⚪ Tạm ẩn</option>
                    </select>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditVideoModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">Hủy</button>
                <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl transition shadow-md shadow-purple-500/20 cursor-pointer">
                    Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: XEM TRƯỚC VIDEO REVIEW --}}
<div id="preview-video-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl relative">
        <div class="p-3.5 flex items-center justify-between border-b border-slate-800 bg-slate-900/90 text-white">
            <div class="min-w-0 pr-2">
                <p id="preview-modal-title" class="text-xs font-bold truncate">Xem video</p>
                <p id="preview-modal-venue" class="text-[11px] text-amber-400 truncate"></p>
            </div>
            <button type="button" onclick="closePreviewVideo()" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center shrink-0 cursor-pointer">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        {{-- Khung hiển thị Video --}}
        <div id="preview-media-container" class="w-full aspect-[9/16] bg-black relative flex items-center justify-center">
            <div id="preview-loading" class="absolute inset-0 flex flex-col items-center justify-center text-xs text-slate-400">
                <div class="w-8 h-8 border-2 border-amber-500 border-t-transparent rounded-full animate-spin mb-2"></div>
                Đang tải video...
            </div>
        </div>
    </div>
</div>

<script>
    // ========================================================
    // ENGINE REALTIME TỰ ĐỘNG CẬP NHẬT YÊU CẦU DUYỆT KHÔNG CẦN LOAD TRANG
    // ========================================================
    let isSyncingVideoShorts = false;
    let lastKnownPendingVideoCount = {{ $pendingVideos }};

    function syncVideoShortsGrid(highlightVideoId = null, isManual = false) {
        if (isSyncingVideoShorts) return;
        isSyncingVideoShorts = true;

        const icon = document.getElementById('sync-icon');
        if (isManual && icon) icon.classList.add('animate-spin');

        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newGrid = doc.getElementById('videos-grid-container');
            const currentGrid = document.getElementById('videos-grid-container');
            if (newGrid && currentGrid) {
                currentGrid.innerHTML = newGrid.innerHTML;
            }

            // Cập nhật các chỉ số nhanh
            ['stat-pending-videos', 'stat-active-videos', 'stat-total-videos', 'stat-total-views'].forEach(id => {
                const newEl = doc.getElementById(id);
                const curEl = document.getElementById(id);
                if (newEl && curEl) curEl.innerText = newEl.innerText;
            });

            // Nếu có video mới được thêm, nhấp nháy làm nổi bật
            if (highlightVideoId) {
                const card = document.getElementById(`video-card-${highlightVideoId}`);
                if (card) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    card.classList.add('ring-4', 'ring-amber-400', 'bg-amber-50/70');
                    setTimeout(() => {
                        card.classList.remove('ring-4', 'ring-amber-400', 'bg-amber-50/70');
                    }, 5000);
                }
            }
        })
        .catch(err => console.warn('Lỗi đồng bộ realtime video:', err))
        .finally(() => {
            isSyncingVideoShorts = false;
            if (isManual && icon) {
                setTimeout(() => icon.classList.remove('animate-spin'), 500);
            }
        });
    }

    // Lắng nghe sự kiện từ admin layout (kiểm tra định kỳ mỗi 3s)
    window.addEventListener('admin:notifications-updated', function(e) {
        if (!e.detail) return;
        const data = e.detail;

        // Cập nhật số đếm thống kê
        if (data.pending_video !== undefined) {
            const el = document.getElementById('stat-pending-videos');
            if (el) el.innerText = new Intl.NumberFormat().format(data.pending_video);
        }
        if (data.total_videos !== undefined) {
            const el = document.getElementById('stat-total-videos');
            if (el) el.innerText = new Intl.NumberFormat().format(data.total_videos);
        }

        // Tự động nạp lại lưới video nếu có video mới hoặc số lượng video chờ duyệt thay đổi
        if (data.latest_video && data.latest_video.id) {
            const existing = document.getElementById(`video-card-${data.latest_video.id}`);
            if (!existing) {
                syncVideoShortsGrid(data.latest_video.id);
            }
        } else if (data.pending_video !== undefined && data.pending_video !== lastKnownPendingVideoCount) {
            lastKnownPendingVideoCount = data.pending_video;
            syncVideoShortsGrid();
        }
    });

    // Thêm vòng lặp kiểm tra dự phòng mỗi 4 giây
    setInterval(function() {
        if (!document.hidden) {
            syncVideoShortsGrid();
        }
    }, 4000);

    // ========================================================
    // THAO TÁC DUYỆT / TỪ CHỐI / TẠM ẨN VIDEO
    // ========================================================

    // 1. Phê duyệt video
    async function approveVideo(id) {
        const btn = document.getElementById(`btn-approve-${id}`);
        if (btn) {
            btn.innerHTML = `<span class="material-symbols-outlined text-[15px] animate-spin">sync</span><span>Đang duyệt...</span>`;
            btn.disabled = true;
        }

        try {
            const res = await fetch(`/admin/video-shorts/${id}/approve`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                // Cập nhật UI ngay lập tức
                const badge = document.getElementById(`status-badge-${id}`);
                if (badge) {
                    badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold shadow-xs bg-emerald-500 text-white';
                    badge.innerText = '🟢 Đã duyệt';
                }
                const actionsContainer = document.getElementById(`action-buttons-${id}`);
                if (actionsContainer) {
                    actionsContainer.innerHTML = `
                        <button type="button"
                                id="btn-toggle-${id}"
                                onclick="toggleVideoStatus('${id}')"
                                class="flex-1 py-1.5 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer bg-slate-200 hover:bg-slate-300 text-slate-700">
                            <span class="material-symbols-outlined text-[15px]">visibility_off</span>
                            <span id="btn-toggle-text-${id}">Tạm ẩn video</span>
                        </button>
                    `;
                }

                // Cập nhật lại số đếm thống kê
                syncVideoShortsGrid();

                if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Phê Duyệt Video', 'Đã phê duyệt video review thành công!', window.location.href, 'check_circle', 'bg-emerald-500');
                }
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể phê duyệt video.'));
                if (btn) btn.disabled = false;
            }
        } catch (e) {
            alert('Lỗi kết nối máy chủ.');
            if (btn) btn.disabled = false;
        }
    }

    // 2. Từ chối / Tạm ẩn video
    async function rejectVideo(id) {
        if (!confirm('Bạn có chắc chắn muốn từ chối video review này của quán không?')) return;

        try {
            const res = await fetch(`/admin/video-shorts/${id}/reject`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                const badge = document.getElementById(`status-badge-${id}`);
                if (badge) {
                    badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold shadow-xs bg-slate-600 text-white';
                    badge.innerText = '⚪ Đang ẩn';
                }
                const actionsContainer = document.getElementById(`action-buttons-${id}`);
                if (actionsContainer) {
                    actionsContainer.innerHTML = `
                        <button type="button"
                                id="btn-toggle-${id}"
                                onclick="toggleVideoStatus('${id}')"
                                class="flex-1 py-1.5 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer bg-emerald-100 hover:bg-emerald-200 text-emerald-800">
                            <span class="material-symbols-outlined text-[15px]">check</span>
                            <span id="btn-toggle-text-${id}">Duyệt lại</span>
                        </button>
                    `;
                }
                syncVideoShortsGrid();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể từ chối video.'));
            }
        } catch (e) {
            alert('Lỗi kết nối máy chủ.');
        }
    }

    // 3. Đổi trạng thái Ẩn <-> Hiện (Toggle)
    async function toggleVideoStatus(id) {
        try {
            const res = await fetch(`/admin/video-shorts/${id}/toggle`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                syncVideoShortsGrid();
            } else {
                alert('Không thể thay đổi trạng thái video.');
            }
        } catch (err) {
            alert('Lỗi kết nối máy chủ.');
        }
    }

    // 4. Xem trước Video
    function previewVideo(videoId, cleanUrl, title, venue) {
        document.getElementById('preview-modal-title').innerText = title || 'Xem video';
        document.getElementById('preview-modal-venue').innerText = venue || '';
        const container = document.getElementById('preview-media-container');
        container.innerHTML = '';

        if (cleanUrl) {
            const video = document.createElement('video');
            video.src = cleanUrl;
            video.className = 'w-full h-full object-cover';
            video.controls = true;
            video.autoplay = true;
            video.muted = true;
            video.loop = true;
            video.playsInline = true;
            video.onerror = () => {
                container.innerHTML = '';
                const iframe = document.createElement('iframe');
                iframe.src = `https://www.tiktok.com/embed/v2/${videoId}`;
                iframe.className = 'w-full h-full border-none';
                iframe.allow = 'autoplay; encrypted-media';
                iframe.allowFullscreen = true;
                container.appendChild(iframe);
            };
            container.appendChild(video);
            video.play().catch(e => console.log('Autoplay muted'));
        } else {
            const iframe = document.createElement('iframe');
            iframe.src = `https://www.tiktok.com/embed/v2/${videoId}`;
            iframe.className = 'w-full h-full border-none';
            iframe.allow = 'autoplay; encrypted-media';
            iframe.allowFullscreen = true;
            container.appendChild(iframe);
        }

        document.getElementById('preview-video-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closePreviewVideo() {
        const container = document.getElementById('preview-media-container');
        container.innerHTML = '';
        document.getElementById('preview-video-modal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // 5. Tải / Đồng bộ sang MP4 sạch
    async function syncCleanVideo(id) {
        const btn = document.getElementById(`btn-sync-${id}`);
        if (btn) {
            btn.innerHTML = `<span class="material-symbols-outlined text-[15px] animate-spin">sync</span>`;
            btn.disabled = true;
        }

        try {
            const res = await fetch(`/admin/video-shorts/${id}/sync-clean`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                syncVideoShortsGrid();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể tải video MP4 sạch.'));
                if (btn) {
                    btn.innerHTML = `<span class="material-symbols-outlined text-[15px]">cloud_download</span><span>Tải sạch</span>`;
                    btn.disabled = false;
                }
            }
        } catch (e) {
            alert('Lỗi kết nối máy chủ.');
            if (btn) {
                btn.innerHTML = `<span class="material-symbols-outlined text-[15px]">cloud_download</span><span>Tải sạch</span>`;
                btn.disabled = false;
            }
        }
    }

    // 6. Xóa video
    async function deleteVideo(id, title) {
        if (!confirm(`Bạn có chắc chắn muốn xóa vĩnh viễn video "${title || 'này'}" không?`)) return;

        try {
            const res = await fetch(`/admin/video-shorts/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                const card = document.getElementById(`video-card-${id}`);
                if (card) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => card.remove(), 300);
                }
                syncVideoShortsGrid();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể xóa video.'));
            }
        } catch (err) {
            alert('Lỗi kết nối máy chủ khi xóa video.');
        }
    }

    // 7. Mở / Đóng modal Chỉnh sửa
    function openEditVideoModal(video) {
        document.getElementById('edit-video-form').action = `/admin/video-shorts/${video.id}`;
        document.getElementById('edit-tieu-de').value = video.tieu_de || '';
        const isLocalStorage = (video.video_url || '').startsWith('/storage') || (video.video_url || '').startsWith('storage');
        document.getElementById('edit-video-input').value = isLocalStorage ? '' : (video.video_url || '');
        document.getElementById('edit-quan-id').value = video.quan_id || '';
        document.getElementById('edit-nguoi-dang').value = video.nguoi_dang || '';
        document.getElementById('edit-trang-thai').value = video.trang_thai || 'da_duyet';

        document.getElementById('edit-video-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeEditVideoModal() {
        document.getElementById('edit-video-modal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Đóng popup bằng Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditVideoModal();
            closePreviewVideo();
        }
    });
</script>
@endsection
