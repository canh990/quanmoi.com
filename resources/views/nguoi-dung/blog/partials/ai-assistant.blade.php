{{-- AI Assistant Partial for Blog Creation & Editing --}}
<style>
    /* Absolute top priority stacking context for all AI Modals */
    .ai-modal-overlay {
        position: fixed !important;
        inset: 0 !important;
        z-index: 999999 !important;
        background-color: rgba(15, 23, 42, 0.75) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 1rem !important;
    }
    .ai-modal-overlay.hidden {
        display: none !important;
    }
    .ai-modal-overlay:not(.hidden) {
        display: flex !important;
    }
    .ai-modal-card {
        position: relative !important;
        z-index: 1000000 !important;
        background-color: #ffffff !important;
        opacity: 1 !important;
        isolation: isolate !important;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5) !important;
        border: 1px solid #e2e8f0 !important;
    }
    .ai-modal-card input, 
    .ai-modal-card textarea {
        background-color: #ffffff !important;
        color: #0f172a !important;
    }
    #ai-toast {
        z-index: 9999999 !important;
    }
</style>

<div id="ai-assistant-wrapper">
    {{-- Global AI Toast Notification --}}
    <div id="ai-toast" class="hidden fixed top-6 right-6 max-w-sm px-4 py-3 rounded-2xl shadow-2xl border text-xs font-bold transition-all duration-300 transform flex items-center gap-2.5">
        <span class="material-symbols-outlined text-lg" id="ai-toast-icon">check_circle</span>
        <span id="ai-toast-msg">Thông báo AI</span>
    </div>

    {{-- MODAL 1: GỢI Ý TIÊU ĐỀ AI --}}
    <div id="aiTitleModal" class="ai-modal-overlay hidden" onclick="if(event.target===this) closeAiModal('aiTitleModal')">
        <div class="ai-modal-card rounded-3xl max-w-xl w-full p-6 animate-modal-in max-h-[90vh] flex flex-col text-left">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-xl">auto_fix_high</span>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">AI Gợi Ý Tiêu Đề Bài Viết</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Tạo 5 tiêu đề hấp dẫn, chuẩn SEO & thu hút người đọc</p>
                    </div>
                </div>
                <button type="button" onclick="closeAiModal('aiTitleModal')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>

            <div class="py-4 space-y-4 overflow-y-auto flex-1">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Chủ đề hoặc Tên quán / Món ăn</label>
                    <div class="flex gap-2">
                        <input type="text" id="ai-title-input" placeholder="Ví dụ: Quán ốc Bé Hai Quận 4, Cà phê view hoàng hôn..." class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none" style="background-color: #ffffff !important; color: #0f172a !important;">
                        <button type="button" id="ai-title-btn" onclick="fetchAiTitles()" class="px-4 py-2.5 text-white rounded-xl text-xs font-bold hover:opacity-90 transition-opacity flex items-center gap-1.5 shrink-0 shadow-sm cursor-pointer" style="background-color: #7c3aed !important; color: #ffffff !important;">
                            <span class="material-symbols-outlined text-base">psychology</span>
                            <span>Tạo gợi ý</span>
                        </button>
                    </div>
                </div>

                <div id="ai-title-loading" class="hidden py-8 text-center text-xs font-bold text-purple-700 space-y-2">
                    <div class="inline-block animate-spin rounded-full h-7 w-7 border-3 border-purple-500 border-t-transparent"></div>
                    <p>AI đang sáng tạo các góc nhìn tiêu đề hấp dẫn...</p>
                </div>

                <div id="ai-title-results" class="space-y-2.5">
                    {{-- Dynamically populated --}}
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closeAiModal('aiTitleModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer">Đóng</button>
            </div>
        </div>
    </div>

    {{-- MODAL 2: TẠO DÀN Ý REVIEW ẨM THỰC AI --}}
    <div id="aiOutlineModal" class="ai-modal-overlay hidden" onclick="if(event.target===this) closeAiModal('aiOutlineModal')">
        <div class="ai-modal-card rounded-3xl max-w-2xl w-full p-6 animate-modal-in max-h-[90vh] flex flex-col text-left">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-xl">article</span>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">AI Tạo Dàn Ý Bài Viết Review Quán</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Tự động cấu trúc bài viết ẩm thực hoàn chỉnh theo từ khóa</p>
                    </div>
                </div>
                <button type="button" onclick="closeAiModal('aiOutlineModal')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>

            <div class="py-4 space-y-3.5 overflow-y-auto flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tên quán ăn / Địa điểm</label>
                        <input type="text" id="ai-outline-name" placeholder="Ví dụ: Quán Lẩu Bò Ba Toa" class="w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none" style="background-color: #ffffff !important; color: #0f172a !important;">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Món ăn nổi bật / Loại hình</label>
                        <input type="text" id="ai-outline-dish" placeholder="Ví dụ: Lẩu đuôi bò, bò nướng sốt trứng muối" class="w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none" style="background-color: #ffffff !important; color: #0f172a !important;">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Từ khóa trải nghiệm & Đặc điểm nổi bật</label>
                    <textarea id="ai-outline-kw" rows="2" placeholder="Ví dụ: Không gian ấm cúng, nước lẩu ngọt thanh từ xương, giá sinh viên, nhân viên nhanh nhẹn, nên đặt bàn trước cuối tuần..." class="w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none" style="background-color: #ffffff !important; color: #0f172a !important;"></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="button" id="ai-outline-btn" onclick="fetchAiOutline()" class="px-5 py-2.5 text-white rounded-xl text-xs font-bold hover:opacity-90 transition-opacity flex items-center gap-1.5 shadow-sm cursor-pointer" style="background-color: #4f46e5 !important; color: #ffffff !important;">
                        <span class="material-symbols-outlined text-base">draw</span>
                        <span>Tạo dàn ý bài viết</span>
                    </button>
                </div>

                <div id="ai-outline-loading" class="hidden py-8 text-center text-xs font-bold text-indigo-700 space-y-2">
                    <div class="inline-block animate-spin rounded-full h-7 w-7 border-3 border-indigo-500 border-t-transparent"></div>
                    <p>AI đang viết dàn ý bài review ẩm thực chuyên nghiệp...</p>
                </div>

                <div id="ai-outline-result-box" class="hidden space-y-3 pt-2">
                    <div class="p-3 bg-indigo-50/70 border border-indigo-200 rounded-2xl">
                        <p class="text-[11px] font-bold text-indigo-900 mb-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">visibility</span> Bản xem trước dàn ý bài viết:
                        </p>
                        <div id="ai-outline-preview" class="prose prose-xs max-h-56 overflow-y-auto bg-white p-3 rounded-xl border border-indigo-100 text-slate-800 text-xs" style="background-color: #ffffff !important;"></div>
                    </div>

                    <div class="space-y-1.5 text-xs text-slate-700 font-medium">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="ai-outline-opt-title" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span>Áp dụng Tiêu đề bài viết nếu đang để trống</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="ai-outline-opt-excerpt" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span>Tự động điền Mô tả tóm tắt (Excerpt)</span>
                        </label>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" onclick="applyAiOutline()" class="px-5 py-2.5 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm cursor-pointer" style="background-color: #059669 !important; color: #ffffff !important;">
                            <span class="material-symbols-outlined text-base">check_circle</span>
                            <span>Chèn dàn ý vào nội dung bài viết</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 3: SỬA LỖI CHÍNH TẢ & CHUẨN HÓA VĂN PHONG AI --}}
    <div id="aiSpellModal" class="ai-modal-overlay hidden" onclick="if(event.target===this) closeAiModal('aiSpellModal')">
        <div class="ai-modal-card rounded-3xl max-w-2xl w-full p-6 animate-modal-in max-h-[90vh] flex flex-col text-left">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-xl">spellcheck</span>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">AI Sửa Lỗi Chính Tả & Chuẩn Hóa</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Sửa từ viết tắt, teencode, lỗi dấu câu và văn phong tiếng Việt</p>
                    </div>
                </div>
                <button type="button" onclick="closeAiModal('aiSpellModal')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>

            <div class="py-4 space-y-4 overflow-y-auto flex-1">
                <div id="ai-spell-loading" class="hidden py-8 text-center text-xs font-bold text-emerald-700 space-y-2">
                    <div class="inline-block animate-spin rounded-full h-7 w-7 border-3 border-emerald-500 border-t-transparent"></div>
                    <p>AI đang quét chính tả và chuẩn hóa câu từ bài viết...</p>
                </div>

                <div id="ai-spell-results" class="space-y-3">
                    {{-- Dynamically populated --}}
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <span id="ai-spell-badge" class="text-xs font-bold text-slate-600"></span>
                <div class="flex gap-2">
                    <button type="button" onclick="closeAiModal('aiSpellModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer">Hủy</button>
                    <button type="button" id="ai-spell-apply-btn" onclick="applyAiSpellFix()" class="px-5 py-2 text-white rounded-xl text-xs font-bold transition-colors hidden flex items-center gap-1 shadow-sm cursor-pointer" style="background-color: #059669 !important; color: #ffffff !important;">
                        <span class="material-symbols-outlined text-sm">done_all</span>
                        <span>Áp dụng sửa lỗi</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 4: TỰ ĐỘNG GÁN THẺ & DANH MỤC AI --}}
    <div id="aiTagCategoryModal" class="ai-modal-overlay hidden" onclick="if(event.target===this) closeAiModal('aiTagCategoryModal')">
        <div class="ai-modal-card rounded-3xl max-w-lg w-full p-6 animate-modal-in max-h-[90vh] flex flex-col text-left">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-xl">sell</span>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">AI Phân Tích Thẻ & Danh Mục</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Tự động nhận diện chủ đề để gán Danh mục và Thẻ xu hướng</p>
                    </div>
                </div>
                <button type="button" onclick="closeAiModal('aiTagCategoryModal')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>

            <div class="py-4 space-y-4 overflow-y-auto flex-1">
                <div id="ai-tag-loading" class="hidden py-8 text-center text-xs font-bold text-amber-700 space-y-2">
                    <div class="inline-block animate-spin rounded-full h-7 w-7 border-3 border-amber-500 border-t-transparent"></div>
                    <p>AI đang phân tích ngữ nghĩa nội dung và tìm thẻ phù hợp...</p>
                </div>

                <div id="ai-tag-results" class="space-y-4">
                    {{-- Dynamically populated --}}
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeAiModal('aiTagCategoryModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer">Đóng</button>
                <button type="button" id="ai-tag-apply-all-btn" onclick="applyAiTagsAndCategory()" class="px-5 py-2 text-white rounded-xl text-xs font-bold transition-colors hidden flex items-center gap-1 shadow-sm cursor-pointer" style="background-color: #d97706 !important; color: #ffffff !important;">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    <span>Áp dụng tất cả</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIC FOR AI ASSISTANT --}}
<script>
    let currentAiContext = 'modal'; // 'modal' or 'page'
    let lastGeneratedOutline = null;
    let lastSpellCheckResult = null;
    let lastTagCategoryResult = null;

    function setAiContext(ctx) {
        currentAiContext = ctx;
    }

    function showAiToast(msg, type = 'success') {
        const toast = document.getElementById('ai-toast');
        const icon = document.getElementById('ai-toast-icon');
        const text = document.getElementById('ai-toast-msg');
        if (!toast) return;

        text.textContent = msg;
        toast.style.cssText = 'position: fixed !important; top: 1.5rem !important; right: 1.5rem !important; z-index: 9999999 !important; max-width: 24rem !important; padding: 0.625rem 0.875rem !important; border-radius: 0.875rem !important; font-size: 0.75rem !important; font-weight: 700 !important; display: flex !important; align-items: center !important; gap: 0.5rem !important; box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3) !important; cursor: pointer !important; transition: opacity 0.25s ease, transform 0.25s ease !important;';

        if (type === 'success') {
            toast.style.backgroundColor = '#059669';
            toast.style.color = '#ffffff';
            toast.style.border = '1px solid #10b981';
            icon.textContent = 'check_circle';
        } else if (type === 'error') {
            toast.style.backgroundColor = '#dc2626';
            toast.style.color = '#ffffff';
            toast.style.border = '1px solid #ef4444';
            icon.textContent = 'error';
        } else {
            toast.style.backgroundColor = '#4f46e5';
            toast.style.color = '#ffffff';
            toast.style.border = '1px solid #6366f1';
            icon.textContent = 'psychology';
        }

        toast.classList.remove('hidden');
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';

        // Click to close immediately
        toast.onclick = () => {
            toast.style.opacity = '0';
            setTimeout(() => toast.classList.add('hidden'), 200);
        };

        if (window.aiToastTimer) clearTimeout(window.aiToastTimer);
        window.aiToastTimer = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-6px)';
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 200);
        }, 1500);
    }

    function closeAiModal(modalId) {
        const el = document.getElementById(modalId);
        if (el) el.classList.add('hidden');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function getTargetTitleInput() {
        if (currentAiContext === 'page') {
            return document.getElementById('title') || document.getElementById('modal_title');
        }
        return document.getElementById('modal_title') || document.getElementById('title');
    }

    function getTargetCategoryInput() {
        if (currentAiContext === 'page') {
            return document.getElementById('edit_category_name') 
                || document.getElementById('create_category_name') 
                || document.getElementById('modal_category_name');
        }
        return document.getElementById('modal_category_name') 
            || document.getElementById('create_category_name') 
            || document.getElementById('edit_category_name');
    }

    function getTargetTagsInput() {
        if (currentAiContext === 'page') {
            return document.getElementById('edit_custom_tags') 
                || document.getElementById('create_custom_tags') 
                || document.getElementById('modal_custom_tags');
        }
        return document.getElementById('modal_custom_tags') 
            || document.getElementById('create_custom_tags') 
            || document.getElementById('edit_custom_tags');
    }

    function getTargetExcerptInput() {
        if (currentAiContext === 'page') {
            return document.getElementById('excerpt') || document.getElementById('modal_excerpt');
        }
        return document.getElementById('modal_excerpt') || document.getElementById('excerpt');
    }

    function getTargetContentInput() {
        if (currentAiContext === 'page') {
            return document.getElementById('content-input') || document.getElementById('modal-content-input');
        }
        return document.getElementById('modal-content-input') || document.getElementById('content-input');
    }

    function getTargetQuillInstance() {
        if (currentAiContext === 'page') {
            if (typeof quill !== 'undefined' && quill) return quill;
            if (typeof modalQuill !== 'undefined' && modalQuill) return modalQuill;
        } else {
            if (typeof modalQuill !== 'undefined' && modalQuill) return modalQuill;
            if (typeof quill !== 'undefined' && quill) return quill;
        }
        return null;
    }

    // ─────────────────────────────────────────────────────────────
    // 1. GỢI Ý TIÊU ĐỀ
    // ─────────────────────────────────────────────────────────────
    function openAiTitleSuggestModal(ctx = 'modal') {
        currentAiContext = ctx;
        const modal = document.getElementById('aiTitleModal');
        const input = document.getElementById('ai-title-input');
        const results = document.getElementById('ai-title-results');
        
        const titleEl = getTargetTitleInput();
        const currentTitle = titleEl ? titleEl.value.trim() : '';
        if (input && currentTitle) {
            input.value = currentTitle;
        }

        if (modal) {
            modal.classList.remove('hidden');
            modal.scrollTop = 0;
        }

        if (currentTitle && (!results || results.children.length === 0)) {
            fetchAiTitles();
        }
    }

    function fetchAiTitles() {
        const input = document.getElementById('ai-title-input');
        const prompt = input ? input.value.trim() : '';
        const loading = document.getElementById('ai-title-loading');
        const results = document.getElementById('ai-title-results');
        const btn = document.getElementById('ai-title-btn');

        if (loading) loading.classList.remove('hidden');
        if (results) results.innerHTML = '';
        if (btn) btn.disabled = true;

        fetch('{{ route("ai.blog.suggest-titles") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                topic: prompt,
                current_title: prompt
            })
        })
        .then(res => res.json())
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (btn) btn.disabled = false;

            if (data.success && data.titles && data.titles.length > 0) {
                renderTitleResults(data.titles);
            } else {
                if (results) results.innerHTML = '<div class="p-4 text-center text-xs text-slate-500">Không tìm thấy tiêu đề phù hợp. Vui lòng nhập từ khóa khác.</div>';
            }
        })
        .catch(err => {
            if (loading) loading.classList.add('hidden');
            if (btn) btn.disabled = false;
            showAiToast('Lỗi kết nối AI khi gợi ý tiêu đề!', 'error');
        });
    }

    function renderTitleResults(titles) {
        const results = document.getElementById('ai-title-results');
        if (!results) return;

        results.innerHTML = titles.map((item, idx) => `
            <div class="p-3.5 rounded-2xl border border-slate-200/90 hover:border-purple-300 hover:bg-purple-50/40 transition-all flex items-start justify-between gap-3 group" style="background-color: #ffffff !important;">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full" style="background-color: #f3e8ff !important; color: #7e22ce !important;">${item.badge || 'Gợi ý ' + (idx + 1)}</span>
                        <span class="text-[11px] font-semibold text-slate-400">${item.type || 'Chủ đề'}</span>
                    </div>
                    <h4 class="text-xs font-bold text-slate-900 group-hover:text-purple-700 leading-snug">${item.title}</h4>
                    <p class="text-[10px] text-slate-500 font-medium">${item.description || ''}</p>
                </div>
                <button type="button" onclick="selectAiTitle('${item.title.replace(/'/g, "\\'")}')" class="px-3.5 py-1.5 text-white rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1 cursor-pointer shadow-sm hover:opacity-90" style="background-color: #7c3aed !important; color: #ffffff !important;">
                    <span class="material-symbols-outlined text-sm">check</span>
                    <span>Chọn</span>
                </button>
            </div>
        `).join('');
    }

    function selectAiTitle(title) {
        const titleInput = getTargetTitleInput();
        if (titleInput) {
            titleInput.value = title;
            titleInput.classList.add('ring-2', 'ring-purple-500');
            setTimeout(() => titleInput.classList.remove('ring-2', 'ring-purple-500'), 1500);
        }
        closeAiModal('aiTitleModal');
        showAiToast('Đã áp dụng tiêu đề AI thành công!');
    }

    // ─────────────────────────────────────────────────────────────
    // 2. DÀN Ý REVIEW ẨM THỰC
    // ─────────────────────────────────────────────────────────────
    function openAiOutlineModal(ctx = 'modal') {
        currentAiContext = ctx;
        const modal = document.getElementById('aiOutlineModal');
        const nameInput = document.getElementById('ai-outline-name');
        const kwInput = document.getElementById('ai-outline-kw');
        
        const titleEl = getTargetTitleInput();
        if (nameInput && titleEl && titleEl.value.trim() && !nameInput.value) {
            nameInput.value = titleEl.value.trim();
        }

        if (modal) {
            modal.classList.remove('hidden');
            modal.scrollTop = 0;
        }
    }

    function fetchAiOutline() {
        const name = document.getElementById('ai-outline-name')?.value.trim() || '';
        const dish = document.getElementById('ai-outline-dish')?.value.trim() || '';
        const kw = document.getElementById('ai-outline-kw')?.value.trim() || '';

        if (!kw && !name) {
            showAiToast('Vui lòng nhập tên quán hoặc từ khóa nổi bật!', 'error');
            return;
        }

        const loading = document.getElementById('ai-outline-loading');
        const resultBox = document.getElementById('ai-outline-result-box');
        const preview = document.getElementById('ai-outline-preview');
        const btn = document.getElementById('ai-outline-btn');

        if (loading) loading.classList.remove('hidden');
        if (resultBox) resultBox.classList.add('hidden');
        if (btn) btn.disabled = true;

        fetch('{{ route("ai.blog.generate-outline") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                keywords: kw || (name + ' ' + dish),
                restaurant_name: name,
                dish_type: dish
            })
        })
        .then(res => res.json())
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (btn) btn.disabled = false;

            if (data.success && data.outline) {
                lastGeneratedOutline = data.outline;
                if (preview) preview.innerHTML = data.outline.html_content;
                if (resultBox) resultBox.classList.remove('hidden');
            } else {
                showAiToast('Không thể tạo dàn ý. Vui lòng thử lại!', 'error');
            }
        })
        .catch(err => {
            if (loading) loading.classList.add('hidden');
            if (btn) btn.disabled = false;
            showAiToast('Lỗi kết nối AI khi tạo dàn ý!', 'error');
        });
    }

    function applyAiOutline() {
        if (!lastGeneratedOutline) return;

        const quillInstance = getTargetQuillInstance();
        if (quillInstance) {
            quillInstance.clipboard.dangerouslyPasteHTML(lastGeneratedOutline.html_content);
        } else {
            const contentInput = document.getElementById('content-input') || document.getElementById('modal-content-input');
            if (contentInput) contentInput.value = lastGeneratedOutline.html_content;
        }

        // Optional title
        const optTitle = document.getElementById('ai-outline-opt-title');
        const titleInput = getTargetTitleInput();
        if (optTitle && optTitle.checked && titleInput && !titleInput.value.trim() && lastGeneratedOutline.title) {
            titleInput.value = lastGeneratedOutline.title;
        }

        // Optional excerpt
        const optExcerpt = document.getElementById('ai-outline-opt-excerpt');
        const excerptInput = getTargetExcerptInput();
        if (optExcerpt && optExcerpt.checked && excerptInput && lastGeneratedOutline.excerpt) {
            excerptInput.value = lastGeneratedOutline.excerpt;
        }

        closeAiModal('aiOutlineModal');
        showAiToast('Đã chèn dàn ý review ẩm thực thành công!');
    }

    // ─────────────────────────────────────────────────────────────
    // 3. SỬA LỖI CHÍNH TẢ & CHUẨN HÓA VĂN PHONG
    // ─────────────────────────────────────────────────────────────
    function runAiSpellCheck(ctx = 'modal') {
        currentAiContext = ctx;
        const modal = document.getElementById('aiSpellModal');
        const loading = document.getElementById('ai-spell-loading');
        const results = document.getElementById('ai-spell-results');
        const badge = document.getElementById('ai-spell-badge');
        const applyBtn = document.getElementById('ai-spell-apply-btn');

        if (modal) {
            modal.classList.remove('hidden');
            modal.scrollTop = 0;
        }
        if (loading) loading.classList.remove('hidden');
        if (results) results.innerHTML = '';
        if (badge) badge.textContent = '';
        if (applyBtn) applyBtn.classList.add('hidden');

        // Gather all inputs
        const titleEl = getTargetTitleInput();
        const excerptEl = getTargetExcerptInput();
        const quillInstance = getTargetQuillInstance();
        const contentInput = getTargetContentInput();

        const titleVal = titleEl ? titleEl.value.trim() : '';
        const excerptVal = excerptEl ? excerptEl.value.trim() : '';

        let contentVal = '';
        if (quillInstance) {
            const plain = quillInstance.getText().trim();
            if (plain.length > 0) {
                contentVal = quillInstance.root.innerHTML;
            }
        } else if (contentInput && contentInput.value.trim()) {
            contentVal = contentInput.value.trim();
        }

        if (!titleVal && !excerptVal && !contentVal) {
            if (loading) loading.classList.add('hidden');
            if (results) results.innerHTML = `
                <div class="p-6 text-center text-xs text-slate-500 font-medium space-y-1">
                    <span class="material-symbols-outlined text-3xl text-slate-400">edit_note</span>
                    <p class="font-bold text-slate-700">Chưa có nội dung bài viết để kiểm tra chính tả.</p>
                    <p class="text-[11px] text-slate-400">Vui lòng nhập tiêu đề hoặc nội dung bài viết trước khi bấm sửa chính tả.</p>
                </div>
            `;
            return;
        }

        fetch('{{ route("ai.blog.fix-spelling") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                title: titleEl ? titleEl.value : '',
                excerpt: excerptEl ? excerptEl.value : '',
                content: contentVal
            })
        })
        .then(res => res.json())
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (data.success && data.result) {
                lastSpellCheckResult = data.result;
                renderSpellResults(data.result);
            } else {
                showAiToast(data.message || 'Không thể kiểm tra chính tả lúc này!', 'error');
            }
        })
        .catch(err => {
            if (loading) loading.classList.add('hidden');
            showAiToast('Lỗi kết nối AI khi phân tích chính tả!', 'error');
        });
    }

    function renderSpellResults(res) {
        const results = document.getElementById('ai-spell-results');
        const badge = document.getElementById('ai-spell-badge');
        const applyBtn = document.getElementById('ai-spell-apply-btn');
        if (!results) return;

        if (res.is_identical || res.changes_count === 0) {
            results.innerHTML = `
                <div class="p-6 text-center bg-emerald-50 rounded-2xl border border-emerald-200 text-emerald-800 space-y-2" style="background-color: #ecfdf5 !important;">
                    <span class="material-symbols-outlined text-3xl text-emerald-600">verified</span>
                    <h4 class="text-xs font-extrabold text-emerald-900">Tuyệt vời! Không phát hiện lỗi chính tả</h4>
                    <p class="text-[11px] text-emerald-700">Văn phong bài viết của bạn rất chuẩn chỉnh, không có từ viết tắt hay lỗi từ ngữ nào cần sửa.</p>
                </div>
            `;
            if (badge) badge.textContent = 'Trạng thái: Chuẩn chỉnh';
            if (applyBtn) applyBtn.classList.add('hidden');
            return;
        }

        if (badge) badge.textContent = `Phát hiện ${res.changes_count} từ cần chuẩn hóa`;
        if (applyBtn) applyBtn.classList.remove('hidden');

        results.innerHTML = `
            <div class="space-y-3.5">
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 text-xs" style="background-color: #fffbeb !important;">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold flex items-center gap-1.5 text-amber-900">
                            <span class="material-symbols-outlined text-base text-amber-600">auto_fix_high</span>
                            Danh sách các từ viết tắt / lỗi chính tả đã nhận diện:
                        </span>
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-200 text-amber-900">${res.changes_count} lỗi</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
                        ${res.changes.map(c => `
                            <div class="p-2.5 bg-white rounded-xl border border-amber-100 flex items-center justify-between text-xs shadow-2xs" style="background-color: #ffffff !important;">
                                <div class="flex items-center gap-1.5 overflow-hidden">
                                    ${c.field ? `<span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 uppercase shrink-0">${escapeHtml(c.field)}</span>` : ''}
                                    <span class="line-through text-rose-500 font-bold bg-rose-50 px-1.5 py-0.5 rounded truncate max-w-[90px]">${escapeHtml(c.from)}</span>
                                    <span class="material-symbols-outlined text-xs text-slate-400 shrink-0">arrow_forward</span>
                                    <span class="text-emerald-700 font-extrabold bg-emerald-50 px-1.5 py-0.5 rounded truncate max-w-[110px]">${escapeHtml(c.to)}</span>
                                </div>
                                ${c.count > 1 ? `<span class="text-[10px] text-slate-400 font-bold ml-1 shrink-0">x${c.count}</span>` : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl space-y-2.5">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm text-slate-500">visibility</span>
                        Bản xem trước sau khi AI chuẩn hóa:
                    </span>
                    ${res.title_corrected && res.title_corrected !== res.title_original ? `
                        <div class="bg-white p-2.5 rounded-xl border border-purple-200 text-xs" style="background-color: #ffffff !important;">
                            <span class="text-[10px] font-bold text-purple-700 uppercase block mb-0.5">Tiêu đề (Đã sửa):</span>
                            <p class="font-bold text-slate-900">${escapeHtml(res.title_corrected)}</p>
                        </div>
                    ` : ''}
                    ${res.excerpt_corrected && res.excerpt_corrected !== res.excerpt_original ? `
                        <div class="bg-white p-2.5 rounded-xl border border-indigo-200 text-xs" style="background-color: #ffffff !important;">
                            <span class="text-[10px] font-bold text-indigo-700 uppercase block mb-0.5">Tóm tắt (Đã sửa):</span>
                            <p class="text-slate-800">${escapeHtml(res.excerpt_corrected)}</p>
                        </div>
                    ` : ''}
                    ${res.content_corrected && res.content_corrected !== res.content_original ? `
                        <div class="bg-white p-2.5 rounded-xl border border-emerald-200 text-xs max-h-40 overflow-y-auto" style="background-color: #ffffff !important;">
                            <span class="text-[10px] font-bold text-emerald-700 uppercase block mb-1">Nội dung (Đã sửa):</span>
                            <div class="prose prose-xs text-slate-800">${res.content_corrected}</div>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    function applyAiSpellFix() {
        if (!lastSpellCheckResult || lastSpellCheckResult.is_identical) return;

        const titleEl = getTargetTitleInput();
        const excerptEl = getTargetExcerptInput();
        const quillInstance = getTargetQuillInstance();
        const contentInput = getTargetContentInput();

        let appliedFields = [];

        // 1. Update Title
        if (titleEl && lastSpellCheckResult.title_corrected && lastSpellCheckResult.title_corrected !== lastSpellCheckResult.title_original) {
            titleEl.value = lastSpellCheckResult.title_corrected;
            titleEl.dispatchEvent(new Event('input', { bubbles: true }));
            titleEl.classList.add('ring-4', 'ring-emerald-400');
            setTimeout(() => {
                titleEl.classList.remove('ring-4', 'ring-emerald-400');
            }, 2500);
            appliedFields.push('Tiêu đề');
        }

        // 2. Update Excerpt
        if (excerptEl && lastSpellCheckResult.excerpt_corrected && lastSpellCheckResult.excerpt_corrected !== lastSpellCheckResult.excerpt_original) {
            excerptEl.value = lastSpellCheckResult.excerpt_corrected;
            excerptEl.dispatchEvent(new Event('input', { bubbles: true }));
            excerptEl.classList.add('ring-4', 'ring-emerald-400');
            setTimeout(() => {
                excerptEl.classList.remove('ring-4', 'ring-emerald-400');
            }, 2500);
            appliedFields.push('Mô tả tóm tắt');
        }

        // 3. Update Quill & Hidden Input
        if (lastSpellCheckResult.content_corrected && lastSpellCheckResult.content_corrected !== lastSpellCheckResult.content_original) {
            if (quillInstance) {
                quillInstance.clipboard.dangerouslyPasteHTML(lastSpellCheckResult.content_corrected);
                const quillContainer = quillInstance.container;
                if (quillContainer) {
                    quillContainer.classList.add('ring-4', 'ring-emerald-400');
                    setTimeout(() => {
                        quillContainer.classList.remove('ring-4', 'ring-emerald-400');
                    }, 2500);
                }
            }
            if (contentInput) {
                contentInput.value = lastSpellCheckResult.content_corrected;
            }
            appliedFields.push('Nội dung');
        }

        closeAiModal('aiSpellModal');

        const totalCount = lastSpellCheckResult.changes_count || 0;
        const fieldsMsg = appliedFields.length > 0 ? ` vào ${appliedFields.join(', ')}` : '';
        showAiToast(`Đã tự động sửa ${totalCount} lỗi chính tả và áp dụng${fieldsMsg}!`, 'success');
    }

    // ─────────────────────────────────────────────────────────────
    // 4. TỰ ĐỘNG GÁN THẺ & DANH MỤC
    // ─────────────────────────────────────────────────────────────
    function runAiTagging(ctx = 'modal') {
        currentAiContext = ctx;
        const modal = document.getElementById('aiTagCategoryModal');
        const loading = document.getElementById('ai-tag-loading');
        const results = document.getElementById('ai-tag-results');
        const applyAllBtn = document.getElementById('ai-tag-apply-all-btn');

        if (modal) {
            modal.classList.remove('hidden');
            modal.scrollTop = 0;
        }
        if (loading) loading.classList.remove('hidden');
        if (results) results.innerHTML = '';
        if (applyAllBtn) applyAllBtn.classList.add('hidden');

        const title = getTargetTitleInput()?.value || '';
        const excerpt = getTargetExcerptInput()?.value || '';
        const quillInstance = getTargetQuillInstance();
        const content = quillInstance ? quillInstance.root.innerText : '';

        if (!title.trim() && !content.trim()) {
            if (loading) loading.classList.add('hidden');
            if (results) results.innerHTML = '<div class="p-6 text-center text-xs text-slate-500 font-medium">Vui lòng nhập tiêu đề hoặc nội dung bài viết để AI có dữ liệu phân tích.</div>';
            return;
        }

        fetch('{{ route("ai.blog.suggest-tags-category") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                title: title,
                content: content,
                excerpt: excerpt
            })
        })
        .then(res => res.json())
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (data.success && data.data) {
                lastTagCategoryResult = data.data;
                renderTagCategoryResults(data.data);
                if (applyAllBtn) applyAllBtn.classList.remove('hidden');
            }
        })
        .catch(err => {
            if (loading) loading.classList.add('hidden');
            showAiToast('Lỗi phân tích thẻ & danh mục!', 'error');
        });
    }

    function renderTagCategoryResults(data) {
        const results = document.getElementById('ai-tag-results');
        if (!results) return;

        results.innerHTML = `
            {{-- Category Proposal --}}
            <div class="p-4 rounded-2xl space-y-2 border border-amber-200" style="background-color: #fffbeb !important;">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-amber-700">folder_special</span>
                        Danh mục bài viết đề xuất:
                    </span>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full" style="background-color: #fde68a !important; color: #92400e !important;">${data.category_confidence}% Độ chuẩn xác</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="text-sm font-extrabold text-slate-900">${data.category}</span>
                    <button type="button" onclick="applySingleCategory('${data.category.replace(/'/g, "\\'")}')" class="px-3.5 py-1.5 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-sm" style="background-color: #d97706 !important; color: #ffffff !important;">
                        Chọn danh mục này
                    </button>
                </div>
            </div>

            {{-- Tags Proposal --}}
            <div class="p-4 rounded-2xl space-y-2 border border-slate-200" style="background-color: #f8fafc !important;">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-primary">sell</span>
                        Thẻ bài viết (Tags) xu hướng đề xuất:
                    </span>
                    <span class="text-[10px] text-slate-500 font-medium">Bấm vào thẻ để thêm</span>
                </div>
                <div class="flex flex-wrap gap-1.5 pt-1">
                    ${data.tags.map(t => `
                        <button type="button" onclick="appendSingleTag('${t.replace(/'/g, "\\'")}')" class="px-3 py-1.5 text-xs font-bold rounded-full border border-slate-200 hover:border-primary hover:text-primary transition-all flex items-center gap-1 shadow-2xs cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important;">
                            <span class="text-primary font-bold">#</span>
                            <span>${t}</span>
                            <span class="material-symbols-outlined text-xs text-slate-400">add</span>
                        </button>
                    `).join('')}
                </div>
            </div>
        `;
    }

    function applySingleCategory(catName) {
        const catInput = getTargetCategoryInput();
        if (catInput) {
            catInput.value = catName;
            catInput.classList.add('ring-2', 'ring-amber-500');
            setTimeout(() => catInput.classList.remove('ring-2', 'ring-amber-500'), 1500);
        }
        showAiToast(`Đã gán danh mục "${catName}"!`);
    }

    function appendSingleTag(tagName) {
        const tagsInput = getTargetTagsInput();
        if (!tagsInput) return;

        let current = tagsInput.value.split(',').map(s => s.trim().replace(/^#/, '')).filter(Boolean);
        if (!current.includes(tagName)) {
            current.push(tagName);
            tagsInput.value = current.join(', ');
            if (typeof syncQuickTagStates === 'function') {
                syncQuickTagStates(tagsInput.id);
            }
            showAiToast(`Đã thêm thẻ #${tagName}!`);
        } else {
            showAiToast(`Thẻ #${tagName} đã có trong danh sách.`);
        }
    }

    function applyAiTagsAndCategory() {
        if (!lastTagCategoryResult) return;

        // Apply category
        applySingleCategory(lastTagCategoryResult.category);

        // Apply tags
        const tagsInput = getTargetTagsInput();
        if (tagsInput) {
            let current = tagsInput.value.split(',').map(s => s.trim().replace(/^#/, '')).filter(Boolean);
            lastTagCategoryResult.tags.forEach(t => {
                if (!current.includes(t)) current.push(t);
            });
            tagsInput.value = current.join(', ');
            if (typeof syncQuickTagStates === 'function') {
                syncQuickTagStates(tagsInput.id);
            }
        }

        closeAiModal('aiTagCategoryModal');
        showAiToast('Đã áp dụng toàn bộ Danh mục & Thẻ đề xuất!');
    }
</script>
