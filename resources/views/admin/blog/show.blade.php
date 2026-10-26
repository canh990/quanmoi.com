@extends('admin.layout')

@section('title', 'Duyệt bài viết: ' . $blog->title . ' - Quán Mới Admin')
@section('page-title', 'Kiểm Duyệt Chi Tiết Bài Viết')

@section('content')
<div class="space-y-6">
    {{-- Header Top Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.blog.posts.index') }}" class="flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors" title="Quay lại danh sách">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-lg font-bold text-gray-900 leading-tight">Chi Tiết & Phê Duyệt Bài Viết</h2>
                    @php
                        $statusMap = [
                            'draft' => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Bản nháp', 'icon' => 'edit_note'],
                            'pending' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'label' => 'Chờ duyệt', 'icon' => 'pending_actions'],
                            'need_revision' => ['bg' => 'bg-orange-100 text-orange-800 border-orange-200', 'label' => 'Cần sửa', 'icon' => 'rule'],
                            'published' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'label' => 'Xuất bản', 'icon' => 'check_circle'],
                            'scheduled' => ['bg' => 'bg-purple-100 text-purple-800 border-purple-200', 'label' => 'Lên lịch', 'icon' => 'schedule'],
                            'rejected' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-200', 'label' => 'Từ chối', 'icon' => 'cancel'],
                            'hidden' => ['bg' => 'bg-gray-200 text-gray-600 border-gray-300', 'label' => 'Đã ẩn', 'icon' => 'visibility_off'],
                        ];
                        $currCfg = $statusMap[$blog->status] ?? $statusMap['draft'];
                    @endphp
                    <span id="show-blog-status-badge" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $currCfg['bg'] }}">
                        <span class="material-symbols-outlined text-[13px]">{{ $currCfg['icon'] }}</span>
                        <span id="show-blog-status-text">{{ $currCfg['label'] }}</span>
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">Mã bài viết: #{{ $blog->id }} • Cập nhật lần cuối: {{ $blog->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($blog->status !== 'published')
                <button type="button" onclick="showQuickApproveBlog()" class="btn-enterprise btn-enterprise-success text-xs py-2 px-3.5 inline-flex items-center gap-1.5 font-bold shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>Duyệt Bài Viết</span>
                </button>
            @endif

            @if($blog->status !== 'rejected')
                <button type="button" onclick="showQuickRejectBlog()" class="btn-enterprise btn-enterprise-danger text-xs py-2 px-3.5 inline-flex items-center gap-1.5 font-semibold shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                    <span>Không Duyệt</span>
                </button>
            @endif

            @if($blog->status !== 'need_revision')
                <button type="button" onclick="showQuickRevisionBlog()" class="btn-enterprise btn-enterprise-secondary text-xs py-2 px-3.5 inline-flex items-center gap-1.5 font-semibold shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">rule</span>
                    <span>Yêu Cầu Sửa</span>
                </button>
            @endif

            @if($blog->status === 'published')
                <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="btn-enterprise btn-enterprise-secondary text-xs py-2 px-3.5 inline-flex items-center gap-1.5" title="Xem trên giao diện người dùng">
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                    <span>Xem ngoài web</span>
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200/80 shadow-xs">
                @if($blog->cover_image)
                    <div class="w-full max-h-[420px] rounded-xl overflow-hidden mb-6 border border-gray-200/60 bg-gray-50">
                        <img src="{{ $blog->cover_image_url }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1000&q=80';" class="w-full h-full object-cover">
                    </div>
                @endif
                
                <div class="mb-4">
                    <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg mb-2">
                        {{ $blog->category->name ?? 'Chưa phân loại' }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-3 leading-tight">{{ $blog->title }}</h1>
                    @if($blog->excerpt)
                        <p class="text-base text-gray-600 font-medium mb-6 bg-slate-50 p-4 rounded-xl border border-slate-100 leading-relaxed italic">
                            {{ $blog->excerpt }}
                        </p>
                    @endif
                </div>

                <div class="prose max-w-none border-t border-gray-100 pt-6 text-gray-800 leading-relaxed text-sm">
                    {!! $blog->content !!}
                </div>
                
                @if($blog->tags->count() > 0)
                    <div class="flex flex-wrap gap-2 mt-8 pt-6 border-t border-gray-100">
                        @foreach($blog->tags as $tag)
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 font-medium text-xs rounded-lg border border-amber-200/50">#{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            
            {{-- Lịch sử kiểm duyệt (Moderation Logs) --}}
            <div id="moderation-logs-card" class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600 text-[18px]">history</span>
                        <span>Lịch sử kiểm duyệt & phản hồi</span>
                    </h3>
                    <span id="moderation-logs-count" class="text-[11px] font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                        {{ $blog->moderationLogs->count() }} lượt
                    </span>
                </div>

                <ul id="moderation-logs-list" class="space-y-3.5 divide-y divide-gray-100">
                    @forelse($blog->moderationLogs as $log)
                        <li class="pt-3.5 first:pt-0 flex gap-3 text-xs">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[16px]">history_edu</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-900 leading-snug">
                                    <span class="font-bold text-slate-800">{{ $log->admin->ho_ten ?? 'Quản trị viên' }}</span>
                                    đã thực hiện
                                    <span class="font-bold text-amber-700">{{ $log->action }}</span>
                                </p>
                                @if($log->note)
                                    <p class="text-gray-700 mt-1 italic bg-amber-50/60 p-2.5 rounded-lg border border-amber-200/60 leading-relaxed">
                                        "{{ $log->note }}"
                                    </p>
                                @endif
                                <p class="text-gray-400 text-[11px] mt-1">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </li>
                    @empty
                        <li id="moderation-logs-empty" class="text-center py-6 text-gray-400 text-xs italic">
                            Chưa có lịch sử kiểm duyệt nào cho bài viết này.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Sidebar / Actions (1 col) -->
        <div class="space-y-6">
            {{-- Form Phê Duyệt / Cập Nhật Trạng Thái (AJAX) --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs sticky top-20">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">fact_check</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Phê Duyệt Trạng Thái</h3>
                        <p class="text-[11px] text-gray-400">Đồng bộ tức thì và gửi thông báo</p>
                    </div>
                </div>
                
                <form id="admin-blog-status-form" action="{{ route('admin.blog.posts.update-status', $blog) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="show-expected-status" name="expected_status" value="{{ $blog->status }}">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Trạng thái bài viết <span class="text-rose-500">*</span>
                        </label>
                        <select id="show-status-select" name="status" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-xs font-semibold outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white">
                            <option value="published" {{ $blog->status == 'published' ? 'selected' : '' }}>✅ Xuất bản (Công khai bài viết)</option>
                            <option value="need_revision" {{ $blog->status == 'need_revision' ? 'selected' : '' }}>📝 Yêu cầu chỉnh sửa lại</option>
                            <option value="rejected" {{ $blog->status == 'rejected' ? 'selected' : '' }}>❌ Từ chối bài viết</option>
                            <option value="hidden" {{ $blog->status == 'hidden' ? 'selected' : '' }}>👁️ Ẩn bài viết khỏi hệ thống</option>
                        </select>
                    </div>

                    {{-- Chips gợi ý lý do --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Gợi ý ghi chú nhanh:
                        </label>
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            <button type="button" onclick="setQuickNote('Duyệt bài viết thành công. Nội dung hấp dẫn và đạt chuẩn.')" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-semibold rounded-md transition-colors cursor-pointer">
                                Duyệt đạt chuẩn
                            </button>
                            <button type="button" onclick="setQuickNote('Hình ảnh mờ hoặc không hợp lệ, vui lòng chụp ảnh rõ nét hơn.')" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-semibold rounded-md transition-colors cursor-pointer">
                                Ảnh mờ
                            </button>
                            <button type="button" onclick="setQuickNote('Nội dung quá sơ sài, vui lòng bổ sung đánh giá chi tiết món ăn và giá cả.')" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-semibold rounded-md transition-colors cursor-pointer">
                                Nội dung sơ sài
                            </button>
                            <button type="button" onclick="setQuickNote('Bài viết trùng lặp với nội dung đã có trên website.')" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-semibold rounded-md transition-colors cursor-pointer">
                                Trùng lặp
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Ghi chú / Phản hồi cho tác giả
                        </label>
                        <textarea id="show-note-textarea" name="note" rows="3" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 resize-none leading-relaxed" placeholder="Nhập lý do phê duyệt, từ chối hoặc hướng dẫn sửa..."></textarea>
                        <p class="text-[10px] text-gray-400 mt-1">Nội dung ghi chú này sẽ được gửi kèm thông báo tiếng Việt tới người đăng.</p>
                    </div>
                    
                    <button type="submit" id="show-submit-btn" class="w-full py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary/90 transition-all flex items-center justify-center gap-1.5 shadow-sm cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                        <span id="show-submit-icon" class="material-symbols-outlined text-[16px]">save</span>
                        <span id="show-submit-text">Cập Nhật Trạng Thái Ngay</span>
                    </button>
                </form>
            </div>

            {{-- Thông tin tác giả --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Thông tin tác giả bài viết</h3>
                <div class="flex items-center gap-3.5 mb-4">
                    <img src="{{ $blog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($blog->user->ho_ten ?? 'U') }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($blog->user->ho_ten ?? 'U') }}';" class="w-12 h-12 rounded-full border border-gray-200 object-cover">
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 text-sm truncate">{{ $blog->user->ho_ten ?? 'Tài khoản ẩn' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $blog->user->email ?? 'Chưa có email' }}</p>
                    </div>
                </div>
                <div class="text-xs text-gray-600 space-y-2 pt-3 border-t border-gray-100">
                    <p class="flex justify-between">
                        <span class="text-gray-400">Ngày đăng:</span>
                        <span class="font-semibold text-gray-700">{{ $blog->created_at->format('d/m/Y H:i') }}</span>
                    </p>
                    <p class="flex justify-between">
                        <span class="text-gray-400">Xuất bản:</span>
                        <span class="font-semibold text-gray-700">{{ $blog->published_at ? $blog->published_at->format('d/m/Y H:i') : 'Chưa xuất bản' }}</span>
                    </p>
                    <p class="flex justify-between">
                        <span class="text-gray-400">Cập nhật:</span>
                        <span class="font-semibold text-gray-700">{{ $blog->updated_at->format('d/m/Y H:i') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function setQuickNote(text) {
        const noteEl = document.getElementById('show-note-textarea');
        if (noteEl) {
            noteEl.value = text;
            noteEl.focus();
        }
    }

    function showQuickApproveBlog() {
        const statusSelect = document.getElementById('show-status-select');
        if (statusSelect) statusSelect.value = 'published';
        const noteEl = document.getElementById('show-note-textarea');
        if (noteEl && !noteEl.value.trim()) {
            noteEl.value = 'Phê duyệt bài viết thành công.';
        }
        promptAndExecuteStatusUpdate('published');
    }

    function showQuickRejectBlog() {
        const statusSelect = document.getElementById('show-status-select');
        if (statusSelect) statusSelect.value = 'rejected';
        const noteEl = document.getElementById('show-note-textarea');
        if (noteEl && !noteEl.value.trim()) {
            noteEl.value = 'Nội dung bài viết chưa phù hợp với tiêu chuẩn đăng tải.';
        }
        promptAndExecuteStatusUpdate('rejected');
    }

    function showQuickRevisionBlog() {
        const statusSelect = document.getElementById('show-status-select');
        if (statusSelect) statusSelect.value = 'need_revision';
        const noteEl = document.getElementById('show-note-textarea');
        if (noteEl) {
            noteEl.focus();
            noteEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (!noteEl.value.trim()) {
                noteEl.value = 'Vui lòng bổ sung thêm thông tin chi tiết và cập nhật hình ảnh chất lượng cao hơn.';
            }
        }
    }

    function promptAndExecuteStatusUpdate(forcedStatus = null) {
        const form = document.getElementById('admin-blog-status-form');
        const statusSelect = document.getElementById('show-status-select');
        const noteTextarea = document.getElementById('show-note-textarea');
        const expectedStatusInput = document.getElementById('show-expected-status');

        if (forcedStatus && statusSelect) {
            statusSelect.value = forcedStatus;
        }

        const newStatus = statusSelect ? statusSelect.value : 'published';
        const note = noteTextarea ? noteTextarea.value.trim() : '';
        const expectedStatus = expectedStatusInput ? expectedStatusInput.value : '';

        // Nếu từ chối hoặc cần sửa, khuyến khích nhập ghi chú
        if ((newStatus === 'rejected' || newStatus === 'need_revision') && !note) {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lưu Ý', 'Vui lòng nhập lý do từ chối hoặc cần sửa để tác giả được biết.', '', 'warning', 'bg-amber-500');
            }
            noteTextarea?.focus();
            return;
        }

        const blogTitle = @json($blog->title);
        const safeTitle = typeof escapeHtml === 'function' ? escapeHtml(blogTitle) : blogTitle;

        let title = 'Xác Nhận Cập Nhật Trạng Thái';
        let message = `Bạn có chắc chắn muốn cập nhật trạng thái bài viết <b class="text-slate-900 font-bold">"${safeTitle}"</b> không?`;
        let icon = 'help';
        let iconBg = 'bg-amber-100 text-amber-800 border border-amber-200';
        let confirmText = 'Xác Nhận';
        let confirmClass = 'btn-enterprise-primary';
        let confirmIcon = 'check_circle';

        if (newStatus === 'published') {
            title = 'Xác Nhận Phê Duyệt Bài Viết';
            message = `Bạn có chắc chắn muốn phê duyệt và xuất bản bài viết <b class="text-slate-900 font-bold">"${safeTitle}"</b> không?<br><span class="text-emerald-700 text-[11px] font-medium">Bài viết sẽ hiển thị công khai ngay lập tức và tác giả sẽ nhận thông báo chúc mừng qua hòm thư tiếng Việt.</span>`;
            icon = 'check_circle';
            iconBg = 'bg-emerald-100 text-emerald-800 border border-emerald-200';
            confirmText = 'Đồng Ý Duyệt';
            confirmClass = 'btn-enterprise-success';
            confirmIcon = 'check_circle';
        } else if (newStatus === 'rejected') {
            title = 'Xác Nhận Không Duyệt Bài Viết';
            message = `Bạn có chắc chắn không duyệt bài viết <b class="text-slate-900 font-bold">"${safeTitle}"</b> không?<br><span class="text-rose-600 text-[11px] font-medium">Bài viết sẽ bị từ chối và lý do sẽ được gửi trực tiếp đến tác giả.</span>`;
            icon = 'cancel';
            iconBg = 'bg-rose-100 text-rose-800 border border-rose-200';
            confirmText = 'Xác Nhận Không Duyệt';
            confirmClass = 'btn-enterprise-danger';
            confirmIcon = 'cancel';
        } else if (newStatus === 'need_revision') {
            title = 'Xác Nhận Yêu Cầu Chỉnh Sửa';
            message = `Bạn có chắc chắn muốn yêu cầu chỉnh sửa bài viết <b class="text-slate-900 font-bold">"${safeTitle}"</b> không?<br><span class="text-orange-600 text-[11px] font-medium">Tác giả sẽ nhận hướng dẫn cụ thể để chỉnh sửa và nộp lại bài.</span>`;
            icon = 'edit_note';
            iconBg = 'bg-orange-100 text-orange-800 border border-orange-200';
            confirmText = 'Gửi Yêu Cầu Sửa';
            confirmClass = 'btn-enterprise-warning';
            confirmIcon = 'send';
        } else if (newStatus === 'hidden') {
            title = 'Xác Nhận Ẩn Bài Viết';
            message = `Bạn có chắc chắn muốn ẩn bài viết <b class="text-slate-900 font-bold">"${safeTitle}"</b> khỏi website không?<br><span class="text-slate-600 text-[11px] font-medium">Bài viết sẽ chuyển sang trạng thái ẩn và tạm thời không xuất hiện công khai.</span>`;
            icon = 'visibility_off';
            iconBg = 'bg-slate-200 text-slate-800 border border-slate-300';
            confirmText = 'Xác Nhận Ẩn';
            confirmClass = 'btn-enterprise-secondary';
            confirmIcon = 'visibility_off';
        }

        if (typeof openConfirmModal === 'function') {
            openConfirmModal({
                title: title,
                message: message,
                icon: icon,
                iconBg: iconBg,
                confirmText: confirmText,
                confirmClass: confirmClass,
                confirmIcon: confirmIcon,
                onConfirm: () => executeAdminBlogStatusUpdate(form, newStatus, note, expectedStatus)
            });
        } else {
            const plainMsg = message.replace(/<[^>]*>/g, '');
            if (confirm(plainMsg)) {
                executeAdminBlogStatusUpdate(form, newStatus, note, expectedStatus);
            }
        }
    }

    function executeAdminBlogStatusUpdate(form, newStatus, note, expectedStatus) {
        const submitBtn = document.getElementById('show-submit-btn');
        const submitText = document.getElementById('show-submit-text');
        const submitIcon = document.getElementById('show-submit-icon');
        const expectedStatusInput = document.getElementById('show-expected-status');

        // Set loading state
        if (submitBtn) submitBtn.disabled = true;
        if (submitText) submitText.textContent = 'Đang cập nhật...';
        if (submitIcon) {
            submitIcon.textContent = 'progress_activity';
            submitIcon.classList.add('animate-spin');
        }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                _method: 'PUT',
                status: newStatus,
                note: note,
                expected_status: expectedStatus
            })
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(data.message || `Lỗi máy chủ HTTP ${res.status}`);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Toast notification
                if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Thành Công', data.message || 'Cập nhật trạng thái bài viết thành công!', '', 'check_circle', 'bg-emerald-500');
                }

                // Cập nhật expected status
                if (expectedStatusInput) expectedStatusInput.value = data.status;

                // Cập nhật Header Badge
                const statusMap = {
                    published: { bg: 'bg-emerald-100 text-emerald-800 border-emerald-200', icon: 'check_circle', label: 'Xuất bản' },
                    rejected: { bg: 'bg-rose-100 text-rose-800 border-rose-200', icon: 'cancel', label: 'Từ chối' },
                    need_revision: { bg: 'bg-orange-100 text-orange-800 border-orange-200', icon: 'rule', label: 'Cần sửa' },
                    pending: { bg: 'bg-amber-100 text-amber-800 border-amber-200', icon: 'pending_actions', label: 'Chờ duyệt' },
                    draft: { bg: 'bg-gray-100 text-gray-700 border-gray-200', icon: 'edit_note', label: 'Bản nháp' },
                    hidden: { bg: 'bg-gray-200 text-gray-600 border-gray-300', icon: 'visibility_off', label: 'Đã ẩn' },
                };
                const cfg = statusMap[data.status] || statusMap.draft;
                const badgeEl = document.getElementById('show-blog-status-badge');
                if (badgeEl) {
                    badgeEl.className = `inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border ${cfg.bg}`;
                    badgeEl.innerHTML = `
                        <span class="material-symbols-outlined text-[13px]">${cfg.icon}</span>
                        <span id="show-blog-status-text">${data.status_label || cfg.label}</span>
                    `;
                }

                // Thêm một dòng vào danh sách lịch sử kiểm duyệt mà không cần tải lại trang
                if (data.log) {
                    const emptyRow = document.getElementById('moderation-logs-empty');
                    if (emptyRow) emptyRow.remove();

                    const listEl = document.getElementById('moderation-logs-list');
                    if (listEl) {
                        const newLi = document.createElement('li');
                        newLi.className = 'pt-3.5 first:pt-0 flex gap-3 text-xs animate-pulse';
                        newLi.innerHTML = `
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[16px]">verified</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-900 leading-snug">
                                    <span class="font-bold text-slate-800">${data.log.admin_name || 'Quản trị viên'}</span>
                                    đã thực hiện
                                    <span class="font-bold text-emerald-700">${data.log.action_label || data.log.action}</span>
                                </p>
                                ${data.log.note ? `
                                    <p class="text-gray-700 mt-1 italic bg-amber-50/60 p-2.5 rounded-lg border border-amber-200/60 leading-relaxed">
                                        "${escapeHtml ? escapeHtml(data.log.note) : data.log.note}"
                                    </p>
                                ` : ''}
                                <p class="text-gray-400 text-[11px] mt-1">${data.log.created_at || 'Vừa xong'}</p>
                            </div>
                        `;
                        listEl.prepend(newLi);
                        setTimeout(() => newLi.classList.remove('animate-pulse'), 2000);
                    }
                }
            } else {
                if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Thất Bại', data.message || 'Không thể cập nhật trạng thái.', '', 'error', 'bg-rose-500');
                }
            }
        })
        .catch(err => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lỗi Kết Nối', err.message || 'Không thể kết nối đến máy chủ.', '', 'wifi_off', 'bg-rose-500');
            }
        })
        .finally(() => {
            if (submitBtn) submitBtn.disabled = false;
            if (submitText) submitText.textContent = 'Cập Nhật Trạng Thái Ngay';
            if (submitIcon) {
                submitIcon.textContent = 'save';
                submitIcon.classList.remove('animate-spin');
            }
        });
    }

    document.getElementById('admin-blog-status-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        promptAndExecuteStatusUpdate();
    });
</script>
@endsection
