{{-- ==================== AUTO-SAVE & DRAFT RESTORATION MANAGER ==================== --}}
@php
    $draftContext = $context ?? 'page'; // 'page' hoặc 'modal'
@endphp

<script>
(function() {
    const DRAFT_KEY = 'qm_blog_active_draft';
    const currentContext = '{{ $draftContext }}';
    let draftSaveTimeout = null;
    let isDiscardingDraft = false;
    window.isSubmittingPost = false;

    /**
     * Helper chuyển đổi DataURL thành File
     */
    function dataURLtoFile(dataurl, filename) {
        if (!dataurl || typeof dataurl !== 'string' || !dataurl.startsWith('data:')) return null;
        try {
            const arr = dataurl.split(',');
            const mimeMatch = arr[0].match(/:(.*?);/);
            const mime = mimeMatch ? mimeMatch[1] : 'image/jpeg';
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new File([u8arr], filename, { type: mime });
        } catch(e) {
            console.warn('[DraftManager] Không thể chuyển đổi dataURL sang File:', e);
            return null;
        }
    }

    /**
     * Helper lấy instance Quill editor theo ngữ cảnh
     */
    function getQuill() {
        if (currentContext === 'modal') {
            return (typeof modalQuill !== 'undefined' && modalQuill) ? modalQuill : null;
        }
        return (typeof quill !== 'undefined' && quill) ? quill : null;
    }

    /**
     * Thu thập dữ liệu hiện tại đang soạn thảo
     */
    function collectCurrentFormData() {
        if (isDiscardingDraft || window.isSubmittingPost) return {};
        const isModal = currentContext === 'modal';
        const titleEl = document.getElementById(isModal ? 'modal_title' : 'title');
        const catEl = document.getElementById(isModal ? 'modal_category_name' : 'create_category_name');
        const tagsEl = document.getElementById(isModal ? 'modal_custom_tags' : 'create_custom_tags');
        const excerptEl = document.getElementById(isModal ? 'modal_excerpt' : 'excerpt');
        const commentsEl = document.getElementById('allow_comments');
        const previewEl = document.getElementById(isModal ? 'modal-cover-preview' : 'cover-preview');

        const q = getQuill();
        let contentHtml = '';
        let contentText = '';
        if (q) {
            contentHtml = q.root.innerHTML;
            contentText = q.getText().trim();
            if (contentHtml === '<p><br></p>' || contentText.length === 0) {
                contentHtml = '';
            }
        }

        // Lấy DataURL ảnh bìa nếu đã có ảnh xem trước
        let coverData = '';
        if (previewEl && previewEl.src && !previewEl.classList.contains('hidden')) {
            if (previewEl.src.startsWith('data:image')) {
                coverData = previewEl.src;
            }
        }

        return {
            title: titleEl ? titleEl.value.trim() : '',
            category_name: catEl ? catEl.value.trim() : '',
            custom_tags: tagsEl ? tagsEl.value.trim() : '',
            excerpt: excerptEl ? excerptEl.value.trim() : '',
            content_html: contentHtml,
            content_text: contentText,
            cover_image_data: coverData,
            allow_comments: commentsEl ? commentsEl.checked : true,
            updated_at: Date.now()
        };
    }

    /**
     * Lưu bản nháp vào LocalStorage (có bọc try-catch chống tràn dung lượng)
     */
    function saveDraftNow() {
        if (isDiscardingDraft || window.isSubmittingPost) return;
        const data = collectCurrentFormData();
        
        // Kiểm tra xem có nội dung gì đáng lưu không
        const hasContent = data.title || data.category_name || data.custom_tags || data.excerpt || data.content_html || data.cover_image_data;
        if (!hasContent) {
            return;
        }

        try {
            localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
            if (currentContext === 'modal') {
                sessionStorage.setItem('qm_blog_modal_open', '1');
                sessionStorage.removeItem('qm_blog_modal_closed');
            }
            updateAutoSaveIndicator('saved');
        } catch (e) {
            // Nếu tràn dung lượng do ảnh bìa quá lớn, bỏ ảnh bìa để lưu giữ toàn bộ chữ viết
            if (data.cover_image_data) {
                try {
                    data.cover_image_data = '';
                    localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
                    updateAutoSaveIndicator('saved');
                } catch(err) {
                    console.warn('[DraftManager] Lỗi lưu LocalStorage:', err);
                }
            }
        }
    }

    /**
     * Tự động lưu có debounce 600ms
     */
    function scheduleAutoSave() {
        if (isDiscardingDraft) return;
        updateAutoSaveIndicator('saving');
        clearTimeout(draftSaveTimeout);
        draftSaveTimeout = setTimeout(() => {
            saveDraftNow();
        }, 600);
    }

    /**
     * Hiển thị trạng thái lưu tự động nhẹ nhàng
     */
    function updateAutoSaveIndicator(status) {
        const indicator = document.getElementById(currentContext === 'modal' ? 'modal-draft-indicator' : 'page-draft-indicator');
        if (!indicator) return;

        if (status === 'saving') {
            indicator.innerHTML = '<span class="inline-block animate-spin rounded-full h-2 w-2 border-2 border-amber-600 border-t-transparent mr-1"></span> Đang lưu nháp...';
            indicator.classList.remove('opacity-0');
        } else if (status === 'saved') {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            indicator.innerHTML = `<span class="material-symbols-outlined text-[13px] text-emerald-600 align-middle mr-0.5">cloud_done</span> Tự động lưu lúc ${timeStr}`;
            indicator.classList.remove('opacity-0');
            setTimeout(() => {
                if (indicator) indicator.classList.add('opacity-0');
            }, 3000);
        }
    }

    /**
     * Khôi phục bản nháp vào form
     */
    function restoreDraft() {
        const raw = localStorage.getItem(DRAFT_KEY);
        if (!raw) return false;

        let draft = null;
        try {
            draft = JSON.parse(raw);
        } catch(e) {
            return false;
        }

        if (!draft || typeof draft !== 'object') return false;

        const isModal = currentContext === 'modal';
        const titleEl = document.getElementById(isModal ? 'modal_title' : 'title');
        const catEl = document.getElementById(isModal ? 'modal_category_name' : 'create_category_name');
        const tagsEl = document.getElementById(isModal ? 'modal_custom_tags' : 'create_custom_tags');
        const excerptEl = document.getElementById(isModal ? 'modal_excerpt' : 'excerpt');
        const commentsEl = document.getElementById('allow_comments');
        const contentInput = document.getElementById(isModal ? 'modal-content-input' : 'content-input');
        
        // Ảnh bìa
        const inputEl = document.getElementById(isModal ? 'modal_cover_image' : 'cover_image');
        const previewEl = document.getElementById(isModal ? 'modal-cover-preview' : 'cover-preview');
        const placeholderEl = document.getElementById(isModal ? 'modal-cover-placeholder' : 'cover-placeholder');
        const actionsBarEl = document.getElementById(isModal ? 'modal-cover-actions-bar' : 'page-cover-actions-bar');
        const fixedBtnEl = document.getElementById(isModal ? 'modal-cover-fixed-edit-btn' : 'page-cover-fixed-edit-btn');
        const badgeEl = document.getElementById(isModal ? 'modal-cover-badge' : 'page-cover-badge');
        const containerEl = document.getElementById(isModal ? 'modal-cover-container' : 'cover-preview-container');

        let restoredAny = false;

        // 1. Tiêu đề
        if (titleEl && draft.title && (!titleEl.value || titleEl.value.trim() === '')) {
            titleEl.value = draft.title;
            titleEl.dispatchEvent(new Event('input', { bubbles: true }));
            restoredAny = true;
        }

        // 2. Danh mục
        if (catEl && draft.category_name && (!catEl.value || catEl.value.trim() === '')) {
            catEl.value = draft.category_name;
            catEl.dispatchEvent(new Event('input', { bubbles: true }));
            restoredAny = true;
        }

        // 3. Thẻ (Tags)
        if (tagsEl && draft.custom_tags && (!tagsEl.value || tagsEl.value.trim() === '')) {
            tagsEl.value = draft.custom_tags;
            tagsEl.dispatchEvent(new Event('input', { bubbles: true }));
            if (typeof syncQuickTagStates === 'function') {
                syncQuickTagStates(tagsEl.id);
            }
            restoredAny = true;
        }

        // 4. Mô tả ngắn (Excerpt)
        if (excerptEl && draft.excerpt && (!excerptEl.value || excerptEl.value.trim() === '')) {
            excerptEl.value = draft.excerpt;
            excerptEl.dispatchEvent(new Event('input', { bubbles: true }));
            restoredAny = true;
        }

        // 5. Nội dung Quill Editor — chờ Quill sẵn sàng rồi mới khôi phục
        if (draft.content_html) {
            // Đồng bộ ngay hidden input trước (để nếu submit trước khi Quill ready vẫn có dữ liệu)
            if (contentInput && !contentInput.value) {
                contentInput.value = draft.content_html;
            }

            waitForQuillThen(function(q) {
                if (q && draft.content_html) {
                    const currentPlainText = q.getText().trim();
                    if (currentPlainText.length === 0) {
                        q.clipboard.dangerouslyPasteHTML(draft.content_html);
                        if (contentInput) {
                            contentInput.value = draft.content_html;
                        }
                    }
                }
            });
            restoredAny = true;
        }

        // 6. Cho phép bình luận
        if (commentsEl && typeof draft.allow_comments !== 'undefined') {
            commentsEl.checked = !!draft.allow_comments;
        }

        // 7. Khôi phục ảnh bìa (DataURL & Native File qua DataTransfer)
        if (previewEl && draft.cover_image_data && draft.cover_image_data.startsWith('data:image')) {
            if (!previewEl.src || previewEl.classList.contains('hidden')) {
                previewEl.src = draft.cover_image_data;
                previewEl.classList.remove('hidden');
                if (placeholderEl) placeholderEl.classList.add('hidden');
                if (actionsBarEl) actionsBarEl.classList.remove('hidden');
                if (fixedBtnEl) fixedBtnEl.classList.remove('hidden');
                if (badgeEl) badgeEl.classList.remove('hidden');
                if (containerEl) {
                    containerEl.classList.remove('border-dashed');
                    containerEl.classList.add('border-solid', 'border-amber-400');
                }

                // Gán File object thật vào native file input để khi gửi form ảnh vẫn được upload
                if (inputEl && window.DataTransfer) {
                    try {
                        const fileObj = dataURLtoFile(draft.cover_image_data, 'anh_bia_nhap_' + Date.now() + '.jpg');
                        if (fileObj) {
                            const dt = new DataTransfer();
                            dt.items.add(fileObj);
                            inputEl.files = dt.files;
                            inputEl.classList.add('pointer-events-none');
                        }
                    } catch(e) {
                        console.warn('[DraftManager] Lỗi gán ảnh bìa vào input:', e);
                    }
                }
                restoredAny = true;
            }
        }

        // Hiển thị Banner thông báo đã khôi phục bản nháp
        if (restoredAny) {
            showDraftRestoredBanner(draft.updated_at);
        }

        return restoredAny;
    }

    /**
     * Hiển thị banner đã khôi phục
     */
    function showDraftRestoredBanner(updatedTimestamp) {
        const bannerId = currentContext === 'modal' ? 'modal-draft-alert-banner' : 'qm-draft-alert-banner';
        const banner = document.getElementById(bannerId);
        if (!banner) return;

        let timeText = '';
        if (updatedTimestamp) {
            const d = new Date(updatedTimestamp);
            timeText = `(Lưu lúc ${d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })})`;
        }

        const timeLabel = document.getElementById('qm-draft-time-label');
        if (timeLabel) timeLabel.textContent = timeText;

        // Kiểm tra xem có phải vừa chuyển từ modal sang giao diện đầy đủ không
        const transferNotice = sessionStorage.getItem('qm_blog_transfer_notice');
        if (transferNotice === '1') {
            sessionStorage.removeItem('qm_blog_transfer_notice');
            const noticeTitle = document.getElementById('qm-draft-title-label');
            if (noticeTitle) {
                noticeTitle.textContent = 'Đã chuyển toàn bộ nội dung từ modal sang giao diện đầy đủ thành công!';
            }
        }

        banner.classList.remove('hidden');
    }

    /**
     * Xóa toàn bộ bản nháp và làm sạch form
     */
    window.discardSavedDraft = function() {
        const modal = document.getElementById('qm-discard-draft-modal');
        if (modal) {
            modal.style.setProperty('display', 'flex', 'important');
            modal.classList.remove('hidden');
            return;
        }
        executeDiscardSavedDraft();
    };

    window.closeDiscardDraftModal = function() {
        const modal = document.getElementById('qm-discard-draft-modal');
        if (modal) {
            modal.style.setProperty('display', 'none', 'important');
            modal.classList.add('hidden');
        }
    };

    window.executeDiscardSavedDraft = function() {
        isDiscardingDraft = true;
        clearTimeout(draftSaveTimeout);
        closeDiscardDraftModal();

        try {
            localStorage.removeItem(DRAFT_KEY);
            sessionStorage.removeItem('qm_blog_modal_open');
            sessionStorage.setItem('qm_blog_modal_closed', '1');
            sessionStorage.removeItem('qm_blog_transfer_notice');
            if (window.location.hash === '#tao-bai-viet') {
                history.replaceState(null, null, window.location.pathname + window.location.search);
            }
        } catch(e) {}

        const isModal = currentContext === 'modal';
        const titleEl = document.getElementById(isModal ? 'modal_title' : 'title');
        const catEl = document.getElementById(isModal ? 'modal_category_name' : 'create_category_name');
        const tagsEl = document.getElementById(isModal ? 'modal_custom_tags' : 'create_custom_tags');
        const excerptEl = document.getElementById(isModal ? 'modal_excerpt' : 'excerpt');
        const contentInput = document.getElementById(isModal ? 'modal-content-input' : 'content-input');
        
        if (titleEl) titleEl.value = '';
        if (catEl) catEl.value = '';
        if (tagsEl) {
            tagsEl.value = '';
            if (typeof syncQuickTagStates === 'function') syncQuickTagStates(tagsEl.id);
        }
        if (excerptEl) excerptEl.value = '';
        if (contentInput) contentInput.value = '';

        const q = getQuill();
        if (q) {
            try {
                q.setContents([]);
                q.setText('');
            } catch(err) {}
        }

        // Xóa ảnh bìa
        try {
            if (isModal) {
                if (typeof removeModalCoverImage === 'function') removeModalCoverImage();
            } else {
                if (typeof removePageCoverImage === 'function') removePageCoverImage();
            }
        } catch(err) {}

        // Làm sạch cả trường cover preview bằng tay
        const previewId = isModal ? 'modal-cover-preview' : 'cover-preview';
        const previewEl = document.getElementById(previewId);
        if (previewEl) {
            previewEl.src = '';
            previewEl.classList.add('hidden');
        }
        const placeholderId = isModal ? 'modal-cover-placeholder' : 'cover-placeholder';
        const placeholderEl = document.getElementById(placeholderId);
        if (placeholderEl) placeholderEl.classList.remove('hidden');

        const fileInputId = isModal ? 'modal_cover_image' : 'cover_image';
        const fileInput = document.getElementById(fileInputId);
        if (fileInput) fileInput.value = '';

        // Ẩn toàn bộ banner nháp
        const banner1 = document.getElementById('modal-draft-alert-banner');
        if (banner1) banner1.classList.add('hidden');
        const banner2 = document.getElementById('qm-draft-alert-banner');
        if (banner2) banner2.classList.add('hidden');

        // Bỏ cờ sau 800ms
        setTimeout(() => {
            isDiscardingDraft = false;
        }, 800);

        if (window.showAiToast) {
            window.showAiToast('Đã xóa toàn bộ bản nháp thành công!');
        } else if (window.showToast) {
            window.showToast({
                title: 'Đã xóa bản nháp',
                message: 'Bản nháp đã được làm mới hoàn toàn.',
                type: 'success'
            });
        }
    };

    /**
     * Đóng banner khôi phục bản nháp
     */
    window.dismissDraftBanner = function() {
        const bannerId = currentContext === 'modal' ? 'modal-draft-alert-banner' : 'qm-draft-alert-banner';
        const banner = document.getElementById(bannerId);
        if (banner) banner.classList.add('hidden');
    };

    /**
     * Chuyển toàn bộ bản nháp từ modal sang trang soạn thảo đầy đủ
     */
    window.transferDraftToFullEditor = function(event) {
        if (event) event.preventDefault();
        
        // Lưu ngay lập tức trạng thái hiện tại của modal
        saveDraftNow();
        sessionStorage.setItem('qm_blog_transfer_notice', '1');
        
        // Chuyển hướng sang giao diện đầy đủ
        window.location.href = "{{ route('nguoi-dung.blog.create') }}";
    };

    /**
     * Xóa bản nháp khi người dùng gửi form bài viết
     */
    window.clearDraftOnSubmit = function() {
        isDiscardingDraft = true;
        window.isSubmittingPost = true;
        if (draftSaveTimeout) {
            clearTimeout(draftSaveTimeout);
            draftSaveTimeout = null;
        }
        localStorage.removeItem(DRAFT_KEY);
        try {
            sessionStorage.removeItem('qm_blog_active_draft');
            sessionStorage.removeItem('qm_blog_modal_open');
            sessionStorage.removeItem('qm_blog_modal_closed');
            if (window.location.hash === '#tao-bai-viet') {
                history.replaceState(null, null, window.location.pathname + window.location.search);
            }
        } catch(e) {}
    };

    /**
     * Lắng nghe các sự kiện nhập liệu để tự động lưu
     */
    function bindAutoSaveListeners() {
        const isModal = currentContext === 'modal';
        const form = document.getElementById(isModal ? 'modal-blog-form' : 'blog-form');
        
        const watchedInputs = [
            isModal ? 'modal_title' : 'title',
            isModal ? 'modal_category_name' : 'create_category_name',
            isModal ? 'modal_custom_tags' : 'create_custom_tags',
            isModal ? 'modal_excerpt' : 'excerpt',
            'allow_comments'
        ];

        watchedInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', scheduleAutoSave);
                el.addEventListener('change', scheduleAutoSave);
            }
        });

        // Lắng nghe Quill Editor (chờ cho đến khi Quill sẵn sàng)
        waitForQuillThen(function(q) {
            if (q) {
                q.on('text-change', function() {
                    scheduleAutoSave();
                });
            }
        });

        // Xóa sạch draft khi form submit
        if (form) {
            form.addEventListener('submit', function() {
                if (window.clearDraftOnSubmit) {
                    window.clearDraftOnSubmit();
                } else {
                    isDiscardingDraft = true;
                    window.isSubmittingPost = true;
                    if (draftSaveTimeout) clearTimeout(draftSaveTimeout);
                    localStorage.removeItem(DRAFT_KEY);
                }
            });
        }

        // Lắng nghe khi tải ảnh bìa mới để lưu draft
        const coverInput = document.getElementById(isModal ? 'modal_cover_image' : 'cover_image');
        if (coverInput) {
            coverInput.addEventListener('change', function() {
                setTimeout(scheduleAutoSave, 300);
            });
        }
    }

    /**
     * Chờ Quill editor sẵn sàng rồi mới thực hiện callback
     * Polling mỗi 100ms, tối đa 50 lần (5 giây)
     */
    function waitForQuillThen(callback, maxAttempts) {
        maxAttempts = maxAttempts || 50;
        let attempts = 0;
        function check() {
            attempts++;
            const q = getQuill();
            if (q) {
                callback(q);
            } else if (attempts < maxAttempts) {
                setTimeout(check, 100);
            } else {
                // Quill vẫn chưa sẵn sàng sau 5 giây, thực hiện callback không có Quill
                // để ít nhất các trường text vẫn được khôi phục
                callback(null);
            }
        }
        check();
    }

    // Khởi tạo khôi phục khi trang sẵn sàng
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            // Vừa gửi duyệt bài thành công -> Xóa sạch lịch sử bản nháp và không khôi phục
            isDiscardingDraft = true;
            window.isSubmittingPost = true;
            localStorage.removeItem(DRAFT_KEY);
            try {
                sessionStorage.removeItem('qm_blog_active_draft');
                sessionStorage.removeItem('qm_blog_modal_open');
                sessionStorage.removeItem('qm_blog_modal_closed');
            } catch(e) {}

            // Làm sạch nội dung các ô input trên modal / trang
            const isModal = currentContext === 'modal';
            const titleEl = document.getElementById(isModal ? 'modal_title' : 'title');
            if (titleEl) titleEl.value = '';
            const catEl = document.getElementById(isModal ? 'modal_category_name' : 'create_category_name');
            if (catEl) catEl.value = '';
            const tagsEl = document.getElementById(isModal ? 'modal_custom_tags' : 'create_custom_tags');
            if (tagsEl) {
                tagsEl.value = '';
                if (typeof syncQuickTagStates === 'function') syncQuickTagStates(tagsEl.id);
            }
            const excerptEl = document.getElementById(isModal ? 'modal_excerpt' : 'excerpt');
            if (excerptEl) excerptEl.value = '';
            const contentInput = document.getElementById(isModal ? 'modal-content-input' : 'content-input');
            if (contentInput) contentInput.value = '';

            // Reset Quill
            waitForQuillThen(function(q) {
                if (q) {
                    try {
                        q.setContents([]);
                        q.setText('');
                    } catch(err) {}
                }
            });

            setTimeout(() => {
                isDiscardingDraft = false;
                window.isSubmittingPost = false;
            }, 600);
        @else
            bindAutoSaveListeners();

            // Đợi Quill khởi tạo xong rồi mới khôi phục bản nháp
            waitForQuillThen(function() {
                restoreDraft();
            });
        @endif
    });

    // Lưu draft ngay trước khi trang bị đóng / reload (beforeunload)
    window.addEventListener('beforeunload', function() {
        if (!isDiscardingDraft && !window.isSubmittingPost) {
            saveDraftNow();
        }
    });

    // Xuất hàm để có thể gọi từ ngoài khi mở modal
    window.restoreBlogDraft = restoreDraft;
    window.saveBlogDraftNow = saveDraftNow;
})();
</script>

{{-- ==================== CUSTOM DISCARD DRAFT CONFIRMATION MODAL ==================== --}}
<div id="qm-discard-draft-modal" 
     style="display: none; position: fixed !important; inset: 0 !important; z-index: 99999999 !important; background-color: rgba(15, 23, 42, 0.75) !important; backdrop-filter: blur(4px) !important; align-items: center; justify-content: center; padding: 1rem;" 
     onclick="if(event.target===this) closeDiscardDraftModal()">
    <div class="relative w-full max-w-sm bg-white rounded-3xl p-6 text-center shadow-2xl border border-slate-200/90 animate-modal-in space-y-4 text-left" style="background-color: #ffffff !important; z-index: 100000000 !important;">
        <div class="flex items-start gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl text-rose-600">delete_forever</span>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900 leading-snug">Xóa bản nháp bài viết?</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Toàn bộ tiêu đề, nội dung và ảnh bìa đang soạn dở sẽ bị xóa để bắt đầu lại từ đầu.</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 pt-2">
            <button type="button" 
                    onclick="closeDiscardDraftModal()" 
                    class="w-1/2 py-2.5 px-3 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-all cursor-pointer text-center">
                Hủy bỏ
            </button>
            <button type="button" 
                    onclick="executeDiscardSavedDraft()" 
                    class="w-1/2 py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition-all cursor-pointer text-center flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-sm">delete</span>
                <span>Xóa bản nháp</span>
            </button>
        </div>
    </div>
</div>

