@extends('admin.layout')

@section('title', 'Quản Lý Video TikTok Review - Quán Mới Admin')
@section('page-title', 'Quản Lý Video TikTok / Shorts Review')

@section('content')
<div class="space-y-6">

    {{-- Thống kê tổng quan & Nút Thêm mới --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500 text-3xl">smart_display</span>
                Quản Lý Video TikTok Review
            </h1>
            <p class="text-xs text-slate-500 mt-1">Quản lý, kiểm duyệt và liên kết các video ngắn TikTok review chân thực từ cộng đồng đến các quán ăn.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('video-review.index') }}" target="_blank" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                <span class="material-symbols-outlined text-[17px]">open_in_new</span>
                <span>Xem trang ngoài</span>
            </a>
            <button type="button" onclick="openAddVideoModal()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-md shadow-amber-500/20 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Thêm Video TikTok Mới</span>
            </button>
        </div>
    </div>

    {{-- 4 Thẻ chỉ số nhanh --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">video_library</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng video</p>
                <p class="text-lg font-black text-slate-900">{{ number_format($totalVideos) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">check_circle</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Đang hiển thị</p>
                <p class="text-lg font-black text-emerald-600">{{ number_format($activeVideos) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">pending_actions</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Chờ duyệt</p>
                <p class="text-lg font-black text-amber-600">{{ number_format($pendingVideos) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">visibility</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng lượt xem</p>
                <p class="text-lg font-black text-slate-900">{{ number_format($totalViews) }}</p>
            </div>
        </div>
    </div>

    {{-- Bộ lọc & Tìm kiếm --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.video-shorts.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Tìm kiếm từ khóa --}}
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Từ khóa tìm kiếm</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nhập tên video, creator, ID hoặc tên quán..." class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Trạng thái duyệt</label>
                <select name="trang_thai" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-white">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="da_duyet" {{ request('trang_thai') === 'da_duyet' ? 'selected' : '' }}>🟢 Đã duyệt</option>
                    <option value="cho_duyet" {{ request('trang_thai') === 'cho_duyet' ? 'selected' : '' }}>🟡 Chờ duyệt</option>
                    <option value="an" {{ request('trang_thai') === 'an' ? 'selected' : '' }}>⚪ Tạm ẩn</option>
                </select>
            </div>

            {{-- Chọn quán ăn --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Quán ăn liên kết</label>
                <select name="quan_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-white">
                    <option value="">Tất cả quán</option>
                    @foreach($danhSachQuan as $q)
                        <option value="{{ $q->id }}" {{ request('quan_id') == $q->id ? 'selected' : '' }}>
                            {{ $q->ten_quan }} ({{ $q->ten_quan_huyen }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nút lọc & sắp xếp --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Lọc</span>
                </button>
                <a href="{{ route('admin.video-shorts.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition flex items-center justify-center" title="Đặt lại bộ lọc">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Danh sách Video dạng Grid thẻ trực quan --}}
    @if($videos->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-3xl">smart_display</span>
            </div>
            <h3 class="text-base font-bold text-slate-800">Không tìm thấy video TikTok nào</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Thử thay đổi bộ lọc tìm kiếm hoặc bấm nút "Thêm Video TikTok Mới" bên trên để tạo video review đầu tiên.</p>
            <button type="button" onclick="openAddVideoModal()" class="mt-4 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl inline-flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[17px]">add_circle</span>
                <span>Thêm Video TikTok Ngay</span>
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($videos as $video)
                <div id="video-card-{{ $video->id }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between overflow-hidden group">
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
                                <span id="status-badge-{{ $video->id }}" class="px-2 py-0.5 rounded-full text-[10px] font-bold shadow-xs {{ $video->trang_thai === 'da_duyet' ? 'bg-emerald-500 text-white' : ($video->trang_thai === 'cho_duyet' ? 'bg-amber-500 text-slate-950' : 'bg-slate-600 text-white') }}">
                                    {{ $video->trang_thai === 'da_duyet' ? 'Đã duyệt' : ($video->trang_thai === 'cho_duyet' ? 'Chờ duyệt' : 'Đang ẩn') }}
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

                            <div class="text-[11px] text-slate-500 space-y-1 pt-1 border-t border-slate-100">
                                <p class="flex items-center gap-1 text-slate-700 font-semibold truncate">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">person</span>
                                    <span>{{ $video->nguoi_dang ?: '@tiktok_creator' }}</span>
                                </p>
                                <p class="flex items-center gap-1 text-purple-700 font-semibold truncate" title="{{ $video->quan?->ten_quan ?? 'Chưa liên kết quán' }}">
                                    <span class="material-symbols-outlined text-[14px] text-purple-500">storefront</span>
                                    <span>{{ $video->quan?->ten_quan ?? 'Chưa gán quán' }}</span>
                                </p>
                                <p class="text-[10px] text-slate-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">calendar_today</span>
                                    <span>{{ $video->created_at?->format('d/m/Y H:i') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Nút thao tác dưới cùng --}}
                    <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-1.5">
                        <button type="button"
                                id="btn-toggle-{{ $video->id }}"
                                onclick="toggleVideoStatus('{{ $video->id }}')"
                                class="flex-1 py-1.5 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer {{ $video->trang_thai === 'da_duyet' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
                            <span class="material-symbols-outlined text-[15px]">{{ $video->trang_thai === 'da_duyet' ? 'check' : 'visibility_off' }}</span>
                            <span id="btn-toggle-text-{{ $video->id }}">{{ $video->trang_thai === 'da_duyet' ? 'Hiển thị' : 'Duyệt ngay' }}</span>
                        </button>

                        {{-- Nút đồng bộ MP4 sạch nếu đang dùng Iframe --}}
                        @if(! $video->has_clean_video)
                            <button type="button"
                                    id="btn-sync-{{ $video->id }}"
                                    onclick="syncCleanVideo('{{ $video->id }}')"
                                    class="p-1.5 text-amber-600 hover:text-amber-800 hover:bg-amber-100 rounded-lg transition border border-amber-200 bg-amber-50 cursor-pointer"
                                    title="Tải video MP4 sạch (không logo/nút TikTok)">
                                <span class="material-symbols-outlined text-[18px]">cloud_download</span>
                            </button>
                        @endif

                        <button type="button"
                                onclick="openEditVideoModal({{ json_encode($video) }})"
                                class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-white rounded-lg transition border border-transparent hover:border-slate-200 cursor-pointer"
                                title="Chỉnh sửa thông tin video">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>

                        <button type="button"
                                onclick="deleteVideo('{{ $video->id }}', '{{ addslashes($video->tieu_de) }}')"
                                class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition border border-transparent hover:border-rose-200 cursor-pointer"
                                title="Xóa video này">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
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

{{-- MODAL 1: THÊM MỚI VIDEO TIKTOK / SHORTS --}}
<div id="add-video-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500">video_call</span>
                Thêm Video Review Mới
            </h3>
            <button type="button" onclick="closeAddVideoModal()" class="text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form action="{{ route('admin.video-shorts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
            @csrf

            {{-- Tab chọn nguồn video --}}
            <div class="flex rounded-xl bg-slate-100 p-1 text-xs font-bold">
                <button type="button" id="tab-btn-tiktok" onclick="switchAddTab('tiktok')" class="flex-1 py-1.5 rounded-lg bg-white text-slate-900 shadow-xs transition flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">link</span>
                    <span>Từ Link TikTok</span>
                </button>
                <button type="button" id="tab-btn-upload" onclick="switchAddTab('upload')" class="flex-1 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">upload_file</span>
                    <span>Tải File MP4 từ máy</span>
                </button>
            </div>

            {{-- 1. Nguồn Link TikTok --}}
            <div id="tab-content-tiktok" class="space-y-2">
                <label class="block text-xs font-bold text-slate-800 flex items-center justify-between">
                    <span>Đường dẫn link TikTok <span class="text-rose-500">*</span></span>
                    <span id="fetch-status-text" class="text-[11px] text-purple-600 font-semibold hidden flex items-center gap-1">
                        <span class="w-3 h-3 border-2 border-purple-600 border-t-transparent rounded-full animate-spin"></span>
                        Đang lấy thông tin & video sạch...
                    </span>
                </label>
                <div class="relative">
                    <input type="text"
                           name="video_input"
                           id="add-video-input"
                           placeholder="Dán link TikTok (VD: https://www.tiktok.com/@user/video/...)"
                           class="w-full pl-3.5 pr-10 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                    <button type="button" onclick="fetchTikTokNow()" class="absolute right-2 top-1/2 -translate-y-1/2 px-2 py-1 bg-amber-100 hover:bg-amber-200 text-amber-800 text-[10px] font-bold rounded-lg transition" title="Lấy dữ liệu tự động">
                        Phân tích
                    </button>
                </div>

                {{-- Hộp thông báo tìm thấy dữ liệu tự động (Có nút Xóa) --}}
                <div id="fetch-preview-box" class="p-3 bg-purple-50/70 border border-purple-200/80 rounded-xl text-xs flex items-center justify-between gap-2 hidden">
                    <div class="flex items-center gap-2 min-w-0 flex-1 truncate">
                        <img id="fetch-cover-img" src="" class="w-10 h-12 object-cover rounded-lg bg-slate-200 shrink-0 shadow-2xs">
                        <div class="min-w-0 truncate">
                            <p id="fetch-title-text" class="font-bold text-purple-900 truncate"></p>
                            <p id="fetch-author-text" class="text-[11px] text-purple-700"></p>
                        </div>
                    </div>
                    <button type="button" onclick="clearAdminTikTokPreview()" class="px-2 py-1 rounded-lg text-xs font-bold text-red-600 hover:text-red-700 hover:bg-red-50 border border-red-200 flex items-center gap-1 shrink-0 transition cursor-pointer" title="Hủy bỏ video TikTok này để chọn nguồn khác">
                        <span class="material-symbols-outlined text-sm">close</span>
                        <span>Xóa</span>
                    </button>
                </div>

                {{-- Tự động tải video MP4 sạch ngầm định --}}
                <input type="hidden" name="tai_video_sach" value="1">
            </div>

            {{-- 2. Nguồn Tải File từ máy --}}
            <div id="tab-content-upload" class="space-y-2 hidden">
                <label class="block text-xs font-bold text-slate-800">Chọn file Video (.mp4, .mov, tối đa 100MB)</label>
                <input type="file"
                       name="video_file"
                       id="add-video-file"
                       accept="video/mp4,video/quicktime,video/webm"
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
            </div>

            {{-- Tiêu đề video --}}
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1">Tiêu đề review</label>
                <input type="text"
                       name="tieu_de"
                       id="add-tieu-de"
                       placeholder="Để trống hệ thống sẽ tự lấy tiêu đề từ TikTok"
                       class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
            </div>

            {{-- Quán ăn liên kết --}}
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1">Quán ăn liên kết</label>
                <select name="quan_id" id="add-quan-id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-white">
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
                    <label class="block text-xs font-bold text-slate-800 mb-1">Tên kênh Reviewer</label>
                    <input type="text"
                           name="nguoi_dang"
                           id="add-nguoi-dang"
                           placeholder="VD: @foodie_saigon"
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1">Trạng thái phê duyệt</label>
                    <select name="trang_thai" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none bg-white">
                        <option value="da_duyet" selected>🟢 Đã duyệt (Hiện ra web)</option>
                        <option value="cho_duyet">🟡 Chờ duyệt</option>
                        <option value="an">⚪ Tạm ẩn</option>
                    </select>
                </div>
            </div>

            {{-- Lượt xem & Lượt thích tự động lấy từ TikTok (ẩn khỏi giao diện) --}}
            <input type="hidden" name="luot_xem" id="add-luot-xem" value="0">
            <input type="hidden" name="luot_thich" id="add-luot-thich" value="0">

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddVideoModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">Hủy bỏ</button>
                <button type="submit" id="btn-submit-add" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl transition flex items-center gap-1 shadow-md shadow-amber-500/20">
                    <span class="material-symbols-outlined text-[17px]">save</span>
                    <span>Lưu & Thêm Video</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: CHỈNH SỬA VIDEO --}}
<div id="edit-video-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">edit_note</span>
                Chỉnh Sửa Video
            </h3>
            <button type="button" onclick="closeEditVideoModal()" class="text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form id="edit-video-form" method="POST" enctype="multipart/form-data" class="space-y-3.5">
            @csrf
            @method('PUT')

            {{-- Link TikTok / ID --}}
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1">Link TikTok hoặc Video ID</label>
                <input type="text"
                       name="video_input"
                       id="edit-video-input"
                       placeholder="Để trống nếu không muốn đổi link video"
                       class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
            </div>

            {{-- Tùy chọn thay thế bằng file MP4 mới --}}
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1">Thay thế file MP4 (Tùy chọn)</label>
                <input type="file"
                       name="video_file"
                       accept="video/mp4,video/quicktime,video/webm"
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-100 file:text-purple-800 hover:file:bg-purple-200 cursor-pointer">
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
                    <label class="block text-xs font-bold text-slate-800 mb-1">Tên kênh Reviewer</label>
                    <input type="text"
                           name="nguoi_dang"
                           id="edit-nguoi-dang"
                           class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1">Trạng thái duyệt</label>
                    <select name="trang_thai" id="edit-trang-thai" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white">
                        <option value="da_duyet">🟢 Đã duyệt</option>
                        <option value="cho_duyet">🟡 Chờ duyệt</option>
                        <option value="an">⚪ Tạm ẩn</option>
                    </select>
                </div>
            </div>

            {{-- Lượt xem & Lượt thích tự động lấy từ TikTok (ẩn khỏi giao diện) --}}
            <input type="hidden" name="luot_xem" id="edit-luot-xem" value="0">
            <input type="hidden" name="luot_thich" id="edit-luot-thich" value="0">
            <input type="hidden" name="tai_video_sach" value="1">

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditVideoModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">Hủy bỏ</button>
                <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-1 shadow-md shadow-purple-600/20">
                    <span class="material-symbols-outlined text-[17px]">save</span>
                    <span>Cập nhật Video</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 3: XEM TRƯỚC VIDEO (HỖ TRỢ MP4 SẠCH & TIKTOK IFRAME) --}}
<div id="preview-video-modal" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="relative bg-slate-900 text-white rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl flex flex-col border border-slate-800">
        {{-- Header modal preview --}}
        <div class="p-3.5 bg-slate-950/90 border-b border-slate-800 flex items-center justify-between gap-2">
            <div class="truncate">
                <h4 id="preview-modal-title" class="text-xs font-bold text-white truncate">Tiêu đề video</h4>
                <p id="preview-modal-venue" class="text-[11px] text-amber-400 font-semibold truncate">Tên quán ăn</p>
            </div>
            <button type="button" onclick="closePreviewVideo()" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center shrink-0 transition">
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
    // Xóa preview TikTok admin
    function clearAdminTikTokPreview() {
        const input = document.getElementById('add-video-input');
        const previewBox = document.getElementById('fetch-preview-box');
        if (input) input.value = '';
        if (previewBox) previewBox.classList.add('hidden');
        const img = document.getElementById('fetch-cover-img');
        if (img) img.src = '';
        const title = document.getElementById('fetch-title-text');
        if (title) title.innerText = '';
        const author = document.getElementById('fetch-author-text');
        if (author) author.innerText = '';
    }

    // Chuyển tab trong form thêm mới
    function switchAddTab(tab) {
        const btnTikTok = document.getElementById('tab-btn-tiktok');
        const btnUpload = document.getElementById('tab-btn-upload');
        const contentTikTok = document.getElementById('tab-content-tiktok');
        const contentUpload = document.getElementById('tab-content-upload');

        if (tab === 'tiktok') {
            btnTikTok.className = 'flex-1 py-1.5 rounded-lg bg-white text-slate-900 shadow-xs transition flex items-center justify-center gap-1.5';
            btnUpload.className = 'flex-1 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition flex items-center justify-center gap-1.5';
            contentTikTok.classList.remove('hidden');
            contentUpload.classList.add('hidden');
            document.getElementById('add-video-input').required = true;
            document.getElementById('add-video-file').required = false;
            document.getElementById('add-video-file').value = '';
        } else {
            btnUpload.className = 'flex-1 py-1.5 rounded-lg bg-white text-slate-900 shadow-xs transition flex items-center justify-center gap-1.5';
            btnTikTok.className = 'flex-1 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition flex items-center justify-center gap-1.5';
            contentUpload.classList.remove('hidden');
            contentTikTok.classList.add('hidden');
            document.getElementById('add-video-file').required = true;
            document.getElementById('add-video-input').required = false;
            clearAdminTikTokPreview();
        }
    }

    // Tự động phân tích link TikTok và điền form
    let fetchDebounce;
    const addVideoInput = document.getElementById('add-video-input');
    if (addVideoInput) {
        addVideoInput.addEventListener('input', function() {
            clearTimeout(fetchDebounce);
            const val = this.value.trim();
            if (val.length > 15) {
                fetchDebounce = setTimeout(fetchTikTokNow, 600);
            }
        });
    }

    async function fetchTikTokNow() {
        const url = addVideoInput.value.trim();
        if (!url) return;

        const statusText = document.getElementById('fetch-status-text');
        const previewBox = document.getElementById('fetch-preview-box');

        statusText.classList.remove('hidden');

        try {
            const res = await fetch(`{{ route('admin.video-shorts.fetch-info') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ url: url })
            });

            const data = await res.json();
            statusText.classList.add('hidden');

            if (res.ok && data.success && data.data) {
                const info = data.data;
                previewBox.classList.remove('hidden');
                document.getElementById('fetch-title-text').innerText = info.title || 'Video TikTok';
                document.getElementById('fetch-author-text').innerText = info.author_name || '';
                if (info.cover_url) {
                    document.getElementById('fetch-cover-img').src = info.cover_url;
                }

                // Tự động điền vào form nếu chưa nhập
                if (info.title && !document.getElementById('add-tieu-de').value) {
                    document.getElementById('add-tieu-de').value = info.title;
                }
                if (info.author_name && !document.getElementById('add-nguoi-dang').value) {
                    document.getElementById('add-nguoi-dang').value = info.author_name;
                }
                if (info.views && Number(document.getElementById('add-luot-xem').value) === 0) {
                    document.getElementById('add-luot-xem').value = info.views;
                }
                if (info.likes && Number(document.getElementById('add-luot-thich').value) === 0) {
                    document.getElementById('add-luot-thich').value = info.likes;
                }
            }
        } catch (e) {
            statusText.classList.add('hidden');
        }
    }

    // Mở / Đóng modal Thêm mới
    function openAddVideoModal() {
        document.getElementById('add-video-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeAddVideoModal() {
        document.getElementById('add-video-modal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        const form = document.querySelector('#add-video-modal form');
        if (form) form.reset();
        clearAdminTikTokPreview();
    }

    // Mở / Đóng modal Chỉnh sửa
    function openEditVideoModal(video) {
        document.getElementById('edit-video-form').action = `/admin/video-shorts/${video.id}`;
        document.getElementById('edit-tieu-de').value = video.tieu_de || '';
        const isLocalStorage = (video.video_url || '').startsWith('/storage') || (video.video_url || '').startsWith('storage');
        document.getElementById('edit-video-input').value = isLocalStorage ? '' : (video.video_url || '');
        document.getElementById('edit-quan-id').value = video.quan_id || '';
        document.getElementById('edit-nguoi-dang').value = video.nguoi_dang || '';
        document.getElementById('edit-trang-thai').value = video.trang_thai || 'da_duyet';
        document.getElementById('edit-luot-xem').value = video.luot_xem || 0;
        document.getElementById('edit-luot-thich').value = video.luot_thich || 0;

        document.getElementById('edit-video-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeEditVideoModal() {
        document.getElementById('edit-video-modal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Xem trước Video (Hỗ trợ MP4 sạch hoặc Iframe)
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
            video.muted = true; // Tránh browser chặn phát tự động
            video.loop = true;
            video.playsInline = true;
            video.onerror = () => {
                console.warn('Không tải được video MP4, fallback sang Iframe TikTok');
                container.innerHTML = '';
                const iframe = document.createElement('iframe');
                iframe.src = `https://www.tiktok.com/embed/v2/${videoId}`;
                iframe.className = 'w-full h-full border-none';
                iframe.allow = 'autoplay; encrypted-media';
                iframe.allowFullscreen = true;
                container.appendChild(iframe);
            };
            container.appendChild(video);
            video.play().catch(e => console.log('Autoplay muted fallback'));
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

    // Đồng bộ chuyển đổi sang MP4 sạch
    async function syncCleanVideo(id) {
        const btn = document.getElementById(`btn-sync-${id}`);
        if (btn) {
            btn.innerHTML = `<span class="material-symbols-outlined text-[18px] animate-spin">sync</span>`;
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
                alert('Thành công: ' + data.message);
                window.location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể tải video MP4 sạch.'));
                if (btn) {
                    btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">cloud_download</span>`;
                    btn.disabled = false;
                }
            }
        } catch (e) {
            alert('Lỗi kết nối máy chủ.');
            if (btn) {
                btn.innerHTML = `<span class="material-symbols-outlined text-[18px]">cloud_download</span>`;
                btn.disabled = false;
            }
        }
    }

    // Bật / Tắt trạng thái video (AJAX)
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
                const btn = document.getElementById(`btn-toggle-${id}`);
                const btnText = document.getElementById(`btn-toggle-text-${id}`);
                const badge = document.getElementById(`status-badge-${id}`);

                if (data.trang_thai === 'da_duyet') {
                    if (btn) btn.className = 'flex-1 py-1.5 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer bg-emerald-100 text-emerald-800 hover:bg-emerald-200';
                    if (btnText) btnText.innerText = 'Hiển thị';
                    if (badge) {
                        badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold shadow-xs bg-emerald-500 text-white';
                        badge.innerText = 'Đã duyệt';
                    }
                } else {
                    if (btn) btn.className = 'flex-1 py-1.5 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer bg-slate-200 text-slate-700 hover:bg-slate-300';
                    if (btnText) btnText.innerText = 'Duyệt ngay';
                    if (badge) {
                        badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold shadow-xs bg-slate-600 text-white';
                        badge.innerText = 'Đang ẩn';
                    }
                }
            } else {
                alert('Không thể thay đổi trạng thái video.');
            }
        } catch (err) {
            alert('Lỗi kết nối máy chủ.');
        }
    }

    // Xóa video (AJAX)
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
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể xóa video.'));
            }
        } catch (err) {
            alert('Lỗi kết nối máy chủ khi xóa video.');
        }
    }

    // Đóng popup bằng Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddVideoModal();
            closeEditVideoModal();
            closePreviewVideo();
        }
    });
</script>
@endsection
