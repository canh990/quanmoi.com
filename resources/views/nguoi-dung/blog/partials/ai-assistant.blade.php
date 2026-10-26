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
    .spell-hl-mark {
        scroll-margin: 60px;
    }
    .spell-hl-mark:hover {
        filter: brightness(0.95);
    }
    .spell-hl-mark.spell-hl-active {
        animation: spellPulse 2s infinite ease-in-out;
    }
    @keyframes spellPulse {
        0%, 100% { box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.45); }
        50% { box-shadow: 0 0 0 7px rgba(245, 158, 11, 0.2); }
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

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-indigo-600">tune</span>
                            <span>Phong cách cấu trúc dàn ý</span>
                        </label>
                        <span class="text-[11px] font-medium text-slate-400">Bấm chọn 1 phong cách</span>
                    </div>

                    {{-- Hidden input holding current selected style --}}
                    <input type="hidden" id="ai-outline-style" value="random">

                    {{-- Visual style cards grid (No overlapping dropdowns, flows smoothly) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" id="ai-outline-style-grid">
                        {{-- 1. Đổi mới ngẫu nhiên (Full width recommendation) --}}
                        <div onclick="selectOutlineStyle('random', this)" class="outline-style-card sm:col-span-2 p-3 rounded-2xl border-2 transition-all cursor-pointer flex items-center justify-between gap-3 border-indigo-600 bg-indigo-50/80 text-indigo-950 shadow-sm" data-style="random">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <span class="material-symbols-outlined text-lg">casino</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-900">🎲 Đổi mới ngẫu nhiên</span>
                                        <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-indigo-200 text-indigo-800 uppercase tracking-wide">Khuyên dùng</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 font-medium">Mỗi lần tạo là một góc nhìn, cốt truyện & bố cục hoàn toàn khác biệt</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-indigo-600 text-lg style-check-icon" style="display: inline-flex !important;">check_circle</span>
                        </div>

                        {{-- 2. Cẩm nang Ăn Sập Menu --}}
                        <div onclick="selectOutlineStyle('must_try_guide', this)" class="outline-style-card p-2.5 rounded-2xl border border-slate-200 hover:border-indigo-300 hover:bg-slate-50 transition-all cursor-pointer flex items-start justify-between gap-2 bg-white" data-style="must_try_guide">
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <span class="material-symbols-outlined text-base">receipt_long</span>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block">📋 Cẩm nang Ăn Sập Menu</span>
                                    <p class="text-[10px] text-slate-500 font-medium mt-0.5">Top món đinh, mẹo tiết kiệm & thứ tự gọi món</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-indigo-600 text-lg style-check-icon" style="display: none !important;">check_circle</span>
                        </div>

                        {{-- 3. Chuẩn Food Reviewer --}}
                        <div onclick="selectOutlineStyle('deep_dive', this)" class="outline-style-card p-2.5 rounded-2xl border border-slate-200 hover:border-indigo-300 hover:bg-slate-50 transition-all cursor-pointer flex items-start justify-between gap-2 bg-white" data-style="deep_dive">
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <span class="material-symbols-outlined text-base">menu_book</span>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block">📖 Chuẩn Food Reviewer</span>
                                    <p class="text-[10px] text-slate-500 font-medium mt-0.5">Toàn diện, thẩm định 5 tiêu chí & độ tươi</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-indigo-600 text-lg style-check-icon" style="display: none !important;">check_circle</span>
                        </div>

                        {{-- 4. Ký sự trải nghiệm --}}
                        <div onclick="selectOutlineStyle('storytelling', this)" class="outline-style-card p-2.5 rounded-2xl border border-slate-200 hover:border-indigo-300 hover:bg-slate-50 transition-all cursor-pointer flex items-start justify-between gap-2 bg-white" data-style="storytelling">
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <span class="material-symbols-outlined text-base">theater_comedy</span>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block">🎭 Ký sự trải nghiệm</span>
                                    <p class="text-[10px] text-slate-500 font-medium mt-0.5">Kể chuyện cảm xúc, không gian & buổi hẹn</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-indigo-600 text-lg style-check-icon" style="display: none !important;">check_circle</span>
                        </div>

                        {{-- 5. Review Nhanh 60s --}}
                        <div onclick="selectOutlineStyle('fast_pros_cons', this)" class="outline-style-card p-2.5 rounded-2xl border border-slate-200 hover:border-indigo-300 hover:bg-slate-50 transition-all cursor-pointer flex items-start justify-between gap-2 bg-white" data-style="fast_pros_cons">
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <span class="material-symbols-outlined text-base">bolt</span>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block">⚡ Review Nhanh 60s</span>
                                    <p class="text-[10px] text-slate-500 font-medium mt-0.5">Ưu - Nhược điểm thực tế & Đánh giá P/P</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-indigo-600 text-lg style-check-icon" style="display: none !important;">check_circle</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <span class="text-[11px] text-slate-500 font-medium flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm text-indigo-500">autorenew</span>
                        <span>Mỗi lần tạo sẽ tự động đổi sang ý tưởng mới</span>
                    </span>
                    <button type="button" id="ai-outline-btn" onclick="fetchAiOutline()" class="px-5 py-2.5 text-white rounded-xl text-xs font-bold hover:opacity-90 transition-opacity flex items-center gap-1.5 shadow-sm cursor-pointer shrink-0" style="background-color: #4f46e5 !important; color: #ffffff !important;">
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
                        <div class="flex items-center justify-between pb-1.5 border-b border-indigo-200/60 mb-2">
                            <p class="text-[11px] font-bold text-indigo-900 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">visibility</span> Bản xem trước dàn ý bài viết:
                            </p>
                            <button type="button" onclick="fetchAiOutline()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-sm">refresh</span>
                                <span>Đổi ý tưởng & cấu trúc khác</span>
                            </button>
                        </div>
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
        <div class="ai-modal-card rounded-3xl max-w-4xl w-full p-6 animate-modal-in max-h-[92vh] flex flex-col text-left">
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
                <button type="button" id="ai-tag-apply-all-btn" onclick="applyAiTagsAndCategory()" class="px-5 py-2 text-white bg-amber-600 hover:bg-amber-700 rounded-xl text-xs font-bold transition-all hidden flex items-center gap-1 shadow-sm cursor-pointer">
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
    let selectedSpellIndices = new Set();
    let activeSelectiveSpell = null;
    let activeSpellIndex = null;
    let currentSpellPreviewTab = 'highlight'; // 'highlight' or 'final'
    let lastTagCategoryResult = null;

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function setAiContext(ctx) {
        currentAiContext = ctx;
    }

    function showAiToast(msg, type = 'success') {
        if (typeof window.showToast === 'function') {
            let toastType = 'success';
            let title = 'Trợ lý AI';
            if (type === 'error') {
                toastType = 'error';
                title = 'Lỗi Trợ lý AI';
            } else if (type === 'warning') {
                toastType = 'warning';
                title = 'Chú ý';
            } else if (type === 'info') {
                toastType = 'info';
                title = 'Gợi ý AI';
            }
            window.showToast({
                title: title,
                message: msg,
                type: toastType,
                duration: 3200
            });
            return;
        }

        const toast = document.getElementById('ai-toast');
        const icon = document.getElementById('ai-toast-icon');
        const text = document.getElementById('ai-toast-msg');
        if (!toast) return;

        text.textContent = msg;
        toast.style.cssText = 'position: fixed !important; bottom: 1.5rem !important; right: 1.5rem !important; z-index: 99999999 !important; max-width: 24rem !important; padding: 0.625rem 0.875rem !important; border-radius: 0.875rem !important; font-size: 0.75rem !important; font-weight: 700 !important; display: flex !important; align-items: center !important; gap: 0.5rem !important; box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3) !important; cursor: pointer !important; transition: opacity 0.25s ease, transform 0.25s ease !important;';

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
            toast.style.transform = 'translateY(6px)';
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 200);
        }, 1800);
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

        results.innerHTML = `
            <div class="flex items-center justify-between pb-1">
                <span class="text-xs font-bold text-slate-800 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm text-purple-600">lightbulb</span>
                    Gợi ý tiêu đề (${titles.length} góc nhìn mới):
                </span>
                <button type="button" onclick="fetchAiTitles()" class="text-xs font-bold text-purple-600 hover:text-purple-800 flex items-center gap-1 cursor-pointer">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                    <span>Đổi 5 gợi ý khác</span>
                </button>
            </div>
        ` + titles.map((item, idx) => `
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
    function selectOutlineStyle(style, el) {
        const input = document.getElementById('ai-outline-style');
        if (input) input.value = style;

        document.querySelectorAll('.outline-style-card').forEach(card => {
            card.classList.remove('border-indigo-600', 'bg-indigo-50/80', 'shadow-sm', 'border-2', 'text-indigo-950');
            card.classList.add('border-slate-200', 'bg-white', 'border');
            const icon = card.querySelector('.style-check-icon');
            if (icon) {
                icon.style.setProperty('display', 'none', 'important');
            }
        });

        if (el) {
            el.classList.remove('border-slate-200', 'bg-white', 'border');
            el.classList.add('border-indigo-600', 'bg-indigo-50/80', 'shadow-sm', 'border-2', 'text-indigo-950');
            const icon = el.querySelector('.style-check-icon');
            if (icon) {
                icon.style.setProperty('display', 'inline-flex', 'important');
            }
        }
    }

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
        const style = document.getElementById('ai-outline-style')?.value || 'random';

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
                dish_type: dish,
                style: style
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

    function replacePreservingCase(match, toWord) {
        if (match === match.toUpperCase()) {
            return toWord.toUpperCase();
        }
        if (match.charAt(0) === match.charAt(0).toUpperCase()) {
            return toWord.charAt(0).toUpperCase() + toWord.slice(1);
        }
        return toWord;
    }

    function replaceInText(text, fromWord, toWord) {
        if (!text || !fromWord) return text || '';
        const escaped = fromWord.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp('(?<![\\p{L}\\p{N}])' + escaped + '(?![\\p{L}\\p{N}])', 'giu');
        if (toWord === '(Loại bỏ từ vô nghĩa)') {
            return text.replace(regex, '').replace(/\s{2,}/g, ' ');
        }
        return text.replace(regex, (m) => replacePreservingCase(m, toWord));
    }

    function replaceInHtml(htmlString, fromWord, toWord) {
        if (!htmlString || !fromWord) return htmlString || '';
        const div = document.createElement('div');
        div.innerHTML = htmlString;

        const escaped = fromWord.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp('(?<![\\p{L}\\p{N}])' + escaped + '(?![\\p{L}\\p{N}])', 'giu');

        const walker = document.createTreeWalker(div, NodeFilter.SHOW_TEXT, null, false);
        let node;
        while (node = walker.nextNode()) {
            if (node.nodeValue && regex.test(node.nodeValue)) {
                regex.lastIndex = 0;
                if (toWord === '(Loại bỏ từ vô nghĩa)') {
                    node.nodeValue = node.nodeValue.replace(regex, '').replace(/\s{2,}/g, ' ');
                } else {
                    node.nodeValue = node.nodeValue.replace(regex, (m) => replacePreservingCase(m, toWord));
                }
            }
        }
        return div.innerHTML;
    }

    function getFieldBadgeClass(field) {
        if (field === 'Tiêu đề') return 'bg-purple-100 text-purple-700';
        if (field === 'Tóm tắt') return 'bg-indigo-100 text-indigo-700';
        return 'bg-emerald-100 text-emerald-700';
    }

    function highlightInPlainText(text, changes, selectedIndices, activeIndex, targetField) {
        if (!text) return '';
        let escapedText = escapeHtml(text);
        
        const applicable = [];
        changes.forEach((c, idx) => {
            if (c.from && (!c.field || c.field === targetField)) {
                applicable.push({ 
                    idx, 
                    from: c.from, 
                    to: c.to, 
                    isSelected: selectedIndices.has(idx), 
                    isActive: idx === activeIndex 
                });
            }
        });

        if (applicable.length === 0) return escapedText;

        applicable.sort((a, b) => b.from.length - a.from.length);

        applicable.forEach(item => {
            const escapedWord = escapeHtml(item.from).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp('(?<![\\p{L}\\p{N}])' + escapedWord + '(?![\\p{L}\\p{N}])', 'giu');
            
            escapedText = escapedText.replace(regex, (m) => {
                if (item.isSelected) {
                    const isActive = item.isActive;
                    const style = isActive
                        ? 'background-color: #fde047 !important; color: #000000 !important; border: 2px solid #ea580c !important; border-radius: 6px !important; padding: 2px 7px !important; font-weight: 800 !important; cursor: pointer !important; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.45) !important; display: inline-block !important; margin: 0 1px !important;'
                        : 'background-color: #fef08a !important; color: #713f12 !important; border: 1.5px solid #facc15 !important; border-radius: 6px !important; padding: 1px 6px !important; font-weight: 700 !important; cursor: pointer !important; display: inline-block !important; margin: 0 1px !important;';
                    return `<mark id="spell-mark-${item.idx}" onclick="focusSpellWord(${item.idx})" class="spell-hl-mark" style="${style}" title="Bấm để xem từ này">${m} <span style="font-size: 10px; font-weight: 700; color: #047857; background-color: #d1fae5; padding: 1px 4px; border-radius: 3px; margin-left: 3px;">➔ ${escapeHtml(item.to)}</span></mark>`;
                } else {
                    return `<span class="opacity-60 underline decoration-slate-400 decoration-wavy cursor-pointer" onclick="focusSpellWord(${item.idx})" title="Chưa chọn sửa: ${m}">${m}</span>`;
                }
            });
        });

        return escapedText;
    }

    function highlightInHtmlContent(htmlString, changes, selectedIndices, activeIndex) {
        if (!htmlString) return '';
        const container = document.createElement('div');
        container.innerHTML = htmlString;

        const applicable = [];
        changes.forEach((c, idx) => {
            if (c.from && (!c.field || c.field === 'Nội dung')) {
                applicable.push({
                    idx: idx,
                    from: c.from,
                    to: c.to,
                    isSelected: selectedIndices.has(idx),
                    isActive: idx === activeIndex
                });
            }
        });

        if (applicable.length === 0) {
            return container.innerHTML;
        }

        applicable.sort((a, b) => b.from.length - a.from.length);

        const map = new Map();
        applicable.forEach(item => {
            map.set(item.from.toLowerCase(), item);
        });

        const patterns = applicable.map(item => item.from.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));
        const combinedRegex = new RegExp('(?<![\\p{L}\\p{N}])(' + patterns.join('|') + ')(?![\\p{L}\\p{N}])', 'giu');

        const walker = document.createTreeWalker(container, NodeFilter.SHOW_TEXT, null, false);
        const textNodes = [];
        let n;
        while (n = walker.nextNode()) {
            if (n.parentNode && (n.parentNode.nodeName === 'SCRIPT' || n.parentNode.nodeName === 'STYLE')) {
                continue;
            }
            if (n.nodeValue && n.nodeValue.trim().length > 0) {
                textNodes.push(n);
            }
        }

        const firstMarkAssigned = new Set();

        textNodes.forEach(node => {
            const text = node.nodeValue;
            if (!combinedRegex.test(text)) return;
            combinedRegex.lastIndex = 0;

            const fragment = document.createDocumentFragment();
            let lastIndex = 0;
            let match;

            while ((match = combinedRegex.exec(text)) !== null) {
                if (match.index > lastIndex) {
                    fragment.appendChild(document.createTextNode(text.substring(lastIndex, match.index)));
                }

                const matchedWord = match[0];
                const item = map.get(matchedWord.toLowerCase());
                
                if (item && item.isSelected) {
                    const isActive = item.isActive;
                    const mark = document.createElement('mark');
                    mark.className = `spell-hl-mark ${isActive ? 'spell-hl-active' : ''}`;
                    mark.setAttribute('data-idx', item.idx);
                    
                    if (!firstMarkAssigned.has(item.idx)) {
                        mark.id = `spell-mark-${item.idx}`;
                        firstMarkAssigned.add(item.idx);
                    }
                    
                    mark.setAttribute('onclick', `focusSpellWord(${item.idx})`);
                    
                    let style = 'background-color: #fef08a !important; color: #713f12 !important; border: 1.5px solid #facc15 !important; border-radius: 6px !important; padding: 1px 6px !important; font-weight: 700 !important; cursor: pointer !important; display: inline-block !important; margin: 0 1px !important; transition: all 0.2s ease !important;';
                    if (isActive) {
                        style = 'background-color: #fde047 !important; color: #000000 !important; border: 2px solid #ea580c !important; border-radius: 6px !important; padding: 2px 7px !important; font-weight: 800 !important; cursor: pointer !important; display: inline-block !important; margin: 0 1px !important; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.45) !important; transform: scale(1.04) !important;';
                    }
                    mark.setAttribute('style', style);
                    mark.setAttribute('title', `Bấm để xem/chọn từ "${item.from}" (Gợi ý: ${item.to})`);

                    const wordSpan = document.createElement('span');
                    wordSpan.textContent = matchedWord;
                    mark.appendChild(wordSpan);

                    const badgeSpan = document.createElement('span');
                    badgeSpan.style.cssText = 'font-size: 10px; font-weight: 700; color: #047857; background-color: #d1fae5; padding: 1px 4px; border-radius: 3px; margin-left: 3px; vertical-align: middle; display: inline-block;';
                    badgeSpan.textContent = `➔ ${item.to}`;
                    mark.appendChild(badgeSpan);

                    fragment.appendChild(mark);
                } else if (item && !item.isSelected) {
                    const unselectedSpan = document.createElement('span');
                    unselectedSpan.style.cssText = 'opacity: 0.55; text-decoration: underline wavy #94a3b8; cursor: pointer; padding: 0 2px;';
                    unselectedSpan.setAttribute('onclick', `focusSpellWord(${item.idx})`);
                    unselectedSpan.setAttribute('title', `Chưa chọn sửa: "${matchedWord}" (Bấm để xem)`);
                    unselectedSpan.textContent = matchedWord;
                    fragment.appendChild(unselectedSpan);
                } else {
                    fragment.appendChild(document.createTextNode(matchedWord));
                }

                lastIndex = combinedRegex.lastIndex;
            }

            if (lastIndex < text.length) {
                fragment.appendChild(document.createTextNode(text.substring(lastIndex)));
            }

            node.parentNode.replaceChild(fragment, node);
        });

        return container.innerHTML;
    }

    function renderSpellResults(res) {
        const results = document.getElementById('ai-spell-results');
        const badge = document.getElementById('ai-spell-badge');
        const applyBtn = document.getElementById('ai-spell-apply-btn');
        if (!results) return;

        if (res.is_identical || !res.changes || res.changes.length === 0) {
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

        // Initialize all items as selected by default
        selectedSpellIndices = new Set(res.changes.map((_, i) => i));
        activeSpellIndex = res.changes.length > 0 ? 0 : null;
        currentSpellPreviewTab = 'highlight';

        results.innerHTML = `
            <div class="space-y-3.5">
                <div class="p-3.5 bg-amber-50/70 border border-amber-200 rounded-2xl text-amber-900 text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-amber-200/60 mb-2.5">
                        <span class="font-bold flex items-center gap-1.5 text-amber-950">
                            <span class="material-symbols-outlined text-base text-amber-600">checklist</span>
                            <span>Tích chọn những từ bạn muốn sửa (<strong id="spell-selected-count" class="text-emerald-700">${res.changes.length}</strong>/${res.changes.length}):</span>
                        </span>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="selectAllSpellWords(true)" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-900 hover:underline cursor-pointer">Chọn tất cả</button>
                            <span class="text-amber-300">|</span>
                            <button type="button" onclick="selectAllSpellWords(false)" class="text-[11px] font-bold text-slate-500 hover:text-slate-800 hover:underline cursor-pointer">Bỏ chọn hết</button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto pr-1" id="spell-checklist-container">
                        ${res.changes.map((c, idx) => `
                            <div class="spell-word-card p-2.5 rounded-xl border flex items-center justify-between text-xs cursor-pointer transition-all select-none ${idx === activeSpellIndex ? 'border-2 border-amber-500 bg-amber-50 shadow-sm ring-2 ring-amber-400/30' : 'border-emerald-300 bg-emerald-50/50 text-slate-900 shadow-2xs hover:border-emerald-400'}" 
                                 id="spell-card-${idx}" 
                                 onclick="focusSpellWord(${idx})">
                                <div class="flex items-center gap-2 overflow-hidden flex-1">
                                    <input type="checkbox" 
                                           class="spell-word-cb rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer shrink-0" 
                                           data-idx="${idx}" 
                                           checked 
                                           onclick="event.stopPropagation()" 
                                           onchange="toggleSpellWord(${idx}, this.checked)">
                                    <div class="flex items-center gap-1.5 overflow-hidden flex-1">
                                        ${c.field ? `<span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded ${getFieldBadgeClass(c.field)} uppercase shrink-0">${escapeHtml(c.field)}</span>` : ''}
                                        <span class="line-through text-rose-500 font-bold bg-rose-50 px-1.5 py-0.5 rounded truncate max-w-[85px]">${escapeHtml(c.from)}</span>
                                        <span class="material-symbols-outlined text-xs text-slate-400 shrink-0">arrow_forward</span>
                                        <span class="text-emerald-700 font-extrabold bg-emerald-50 px-1.5 py-0.5 rounded truncate max-w-[95px]">${escapeHtml(c.to)}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0 ml-1">
                                    ${c.count > 1 ? `<span class="text-[10px] text-slate-400 font-bold">x${c.count}</span>` : ''}
                                    <span class="material-symbols-outlined text-sm text-slate-400 hover:text-amber-600 transition-colors" title="Bấm để cuộn đến vị trí từ này trong bài">visibility</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl space-y-2.5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200/80 pb-2">
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    id="spell-tab-highlight-btn" 
                                    onclick="setSpellPreviewTab('highlight')" 
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer bg-amber-500 text-white shadow-xs">
                                <span class="material-symbols-outlined text-sm">highlight</span>
                                <span>Văn bản bài viết (Tô vàng từ cần sửa)</span>
                            </button>
                            <button type="button" 
                                    id="spell-tab-final-btn" 
                                    onclick="setSpellPreviewTab('final')" 
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer bg-slate-200 text-slate-700 hover:bg-slate-300">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                <span>Xem văn bản sau khi sửa</span>
                            </button>
                        </div>
                        <span class="text-[11px] text-slate-500 font-medium hidden sm:inline flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm text-amber-500">touch_app</span>
                            Bấm vào từ bất kỳ để định vị trong bài
                        </span>
                    </div>

                    <div id="ai-spell-preview-box" class="space-y-2">
                        {{-- Populated dynamically --}}
                    </div>
                </div>
            </div>
        `;

        if (applyBtn) applyBtn.classList.remove('hidden');
        updateSpellPreview();

        // Automatically smooth scroll to first error
        setTimeout(() => {
            if (activeSpellIndex !== null) {
                const markEl = document.getElementById(`spell-mark-${activeSpellIndex}`);
                if (markEl) {
                    markEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }, 100);
    }

    function setSpellPreviewTab(tab) {
        currentSpellPreviewTab = tab;
        const hlBtn = document.getElementById('spell-tab-highlight-btn');
        const finalBtn = document.getElementById('spell-tab-final-btn');
        if (hlBtn && finalBtn) {
            if (tab === 'highlight') {
                hlBtn.className = "px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer bg-amber-500 text-white shadow-xs";
                finalBtn.className = "px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer bg-slate-200 text-slate-700 hover:bg-slate-300";
            } else {
                hlBtn.className = "px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer bg-slate-200 text-slate-700 hover:bg-slate-300";
                finalBtn.className = "px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer bg-emerald-600 text-white shadow-xs";
            }
        }
        updateSpellPreview();
    }

    function focusSpellWord(idx) {
        if (!lastSpellCheckResult || !lastSpellCheckResult.changes || !lastSpellCheckResult.changes[idx]) return;
        
        activeSpellIndex = idx;
        setSpellPreviewTab('highlight');

        // Update card styles
        lastSpellCheckResult.changes.forEach((_, i) => {
            const card = document.getElementById(`spell-card-${i}`);
            if (card) {
                const isChecked = selectedSpellIndices.has(i);
                if (i === idx) {
                    card.className = "spell-word-card p-2.5 rounded-xl border-2 flex items-center justify-between text-xs cursor-pointer transition-all select-none border-amber-500 bg-amber-50 text-slate-900 shadow-md ring-2 ring-amber-400/30";
                } else if (isChecked) {
                    card.className = "spell-word-card p-2.5 rounded-xl border flex items-center justify-between text-xs cursor-pointer transition-all select-none border-emerald-300 bg-emerald-50/50 text-slate-900 shadow-2xs hover:border-emerald-400";
                } else {
                    card.className = "spell-word-card p-2.5 rounded-xl border flex items-center justify-between text-xs cursor-pointer transition-all select-none border-slate-200 bg-white text-slate-400 opacity-60 hover:opacity-80";
                }
            }
        });

        updateSpellPreview();

        setTimeout(() => {
            const markEl = document.getElementById(`spell-mark-${idx}`);
            if (markEl) {
                markEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                markEl.classList.add('animate-pulse');
                setTimeout(() => markEl.classList.remove('animate-pulse'), 1200);
            }
        }, 60);
    }

    function toggleSpellWord(idx, isChecked) {
        if (isChecked) {
            selectedSpellIndices.add(idx);
            activeSpellIndex = idx;
        } else {
            selectedSpellIndices.delete(idx);
            if (activeSpellIndex === idx) {
                activeSpellIndex = selectedSpellIndices.size > 0 ? Array.from(selectedSpellIndices)[0] : null;
            }
        }

        const card = document.getElementById(`spell-card-${idx}`);
        if (card) {
            if (activeSpellIndex === idx) {
                card.className = "spell-word-card p-2.5 rounded-xl border-2 flex items-center justify-between text-xs cursor-pointer transition-all select-none border-amber-500 bg-amber-50 text-slate-900 shadow-md ring-2 ring-amber-400/30";
            } else if (isChecked) {
                card.className = "spell-word-card p-2.5 rounded-xl border flex items-center justify-between text-xs cursor-pointer transition-all select-none border-emerald-300 bg-emerald-50/50 text-slate-900 shadow-2xs hover:border-emerald-400";
            } else {
                card.className = "spell-word-card p-2.5 rounded-xl border flex items-center justify-between text-xs cursor-pointer transition-all select-none border-slate-200 bg-white text-slate-400 opacity-60 hover:opacity-80";
            }
        }

        updateSpellPreview();

        if (isChecked) {
            setTimeout(() => {
                const markEl = document.getElementById(`spell-mark-${idx}`);
                if (markEl) {
                    markEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 60);
        }
    }

    function selectAllSpellWords(selectAll) {
        if (!lastSpellCheckResult || !lastSpellCheckResult.changes) return;
        if (selectAll) {
            selectedSpellIndices = new Set(lastSpellCheckResult.changes.map((_, i) => i));
            activeSpellIndex = 0;
        } else {
            selectedSpellIndices.clear();
            activeSpellIndex = null;
        }

        document.querySelectorAll('.spell-word-cb').forEach(cb => {
            cb.checked = selectAll;
        });

        lastSpellCheckResult.changes.forEach((_, idx) => {
            const card = document.getElementById(`spell-card-${idx}`);
            if (card) {
                if (selectAll && idx === activeSpellIndex) {
                    card.className = "spell-word-card p-2.5 rounded-xl border-2 flex items-center justify-between text-xs cursor-pointer transition-all select-none border-amber-500 bg-amber-50 text-slate-900 shadow-md ring-2 ring-amber-400/30";
                } else if (selectAll) {
                    card.className = "spell-word-card p-2.5 rounded-xl border flex items-center justify-between text-xs cursor-pointer transition-all select-none border-emerald-300 bg-emerald-50/50 text-slate-900 shadow-2xs hover:border-emerald-400";
                } else {
                    card.className = "spell-word-card p-2.5 rounded-xl border flex items-center justify-between text-xs cursor-pointer transition-all select-none border-slate-200 bg-white text-slate-400 opacity-60 hover:opacity-80";
                }
            }
        });

        updateSpellPreview();
    }

    function updateSpellPreview() {
        if (!lastSpellCheckResult) return;

        let currentTitle = lastSpellCheckResult.title_original || '';
        let currentExcerpt = lastSpellCheckResult.excerpt_original || '';
        let currentContent = lastSpellCheckResult.content_original || '';

        // Calculate replaced text for apply action
        lastSpellCheckResult.changes.forEach((c, idx) => {
            if (selectedSpellIndices.has(idx)) {
                if (c.field === 'Tiêu đề' || !c.field) {
                    currentTitle = replaceInText(currentTitle, c.from, c.to);
                }
                if (c.field === 'Tóm tắt' || !c.field) {
                    currentExcerpt = replaceInText(currentExcerpt, c.from, c.to);
                }
                if (c.field === 'Nội dung' || !c.field) {
                    currentContent = replaceInHtml(currentContent, c.from, c.to);
                }
            }
        });

        activeSelectiveSpell = {
            title: currentTitle,
            excerpt: currentExcerpt,
            content: currentContent,
            selectedCount: selectedSpellIndices.size
        };

        // Update counts
        const badge = document.getElementById('ai-spell-badge');
        const countEl = document.getElementById('spell-selected-count');
        if (countEl) countEl.textContent = selectedSpellIndices.size;
        if (badge) {
            badge.textContent = `Đã chọn: ${selectedSpellIndices.size} / ${lastSpellCheckResult.changes.length} từ`;
        }

        // Update Preview HTML
        const previewContainer = document.getElementById('ai-spell-preview-box');
        if (previewContainer) {
            let html = '';

            if (currentSpellPreviewTab === 'highlight') {
                // TAB 1: HIGHLIGHT IN ARTICLE (TÔ VÀNG CHỮ ĐÓ TRONG BÀI)
                html += `
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-amber-50 border border-amber-200/70 text-amber-900 text-xs">
                        <span class="material-symbols-outlined text-base text-amber-600 shrink-0">palette</span>
                        <span>Đang <strong>tô màu vàng</strong> các chữ có lỗi chính tả trong bài văn. Bạn có thể bấm vào chữ màu vàng để đối chiếu với danh sách.</span>
                    </div>
                `;

                if (lastSpellCheckResult.title_original) {
                    const titleHl = highlightInPlainText(lastSpellCheckResult.title_original, lastSpellCheckResult.changes, selectedSpellIndices, activeSpellIndex, 'Tiêu đề');
                    html += `
                        <div class="bg-white p-3 rounded-2xl border border-purple-200 text-xs shadow-2xs">
                            <span class="text-[10px] font-extrabold text-purple-700 uppercase tracking-wider block mb-1">Tiêu đề bài viết:</span>
                            <div class="font-bold text-slate-900 text-sm leading-snug">${titleHl}</div>
                        </div>
                    `;
                }

                if (lastSpellCheckResult.excerpt_original) {
                    const excerptHl = highlightInPlainText(lastSpellCheckResult.excerpt_original, lastSpellCheckResult.changes, selectedSpellIndices, activeSpellIndex, 'Tóm tắt');
                    html += `
                        <div class="bg-white p-3 rounded-2xl border border-indigo-200 text-xs shadow-2xs">
                            <span class="text-[10px] font-extrabold text-indigo-700 uppercase tracking-wider block mb-1">Tóm tắt bài viết:</span>
                            <div class="text-slate-800 leading-relaxed">${excerptHl}</div>
                        </div>
                    `;
                }

                if (lastSpellCheckResult.content_original) {
                    const contentHl = highlightInHtmlContent(lastSpellCheckResult.content_original, lastSpellCheckResult.changes, selectedSpellIndices, activeSpellIndex);
                    html += `
                        <div class="bg-white p-3.5 rounded-2xl border border-slate-200 text-xs shadow-inner">
                            <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                                <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider">Nội dung bài văn (Đang định vị lỗi):</span>
                                <span class="text-[10px] text-slate-400">Cuộn để xem toàn bộ bài</span>
                            </div>
                            <div id="spell-highlighted-content-scroll" class="max-h-64 overflow-y-auto pr-2 prose prose-sm max-w-none text-slate-800 leading-relaxed">
                                ${contentHl}
                            </div>
                        </div>
                    `;
                }
            } else {
                // TAB 2: FINAL REPLACED PREVIEW
                const titleChanged = currentTitle !== lastSpellCheckResult.title_original;
                const excerptChanged = currentExcerpt !== lastSpellCheckResult.excerpt_original;
                const contentChanged = currentContent !== lastSpellCheckResult.content_original;

                if (titleChanged) {
                    html += `
                        <div class="bg-white p-2.5 rounded-xl border border-purple-200 text-xs shadow-2xs">
                            <span class="text-[10px] font-bold text-purple-700 uppercase block mb-0.5">Tiêu đề (Sau khi sửa):</span>
                            <p class="font-bold text-slate-900">${escapeHtml(currentTitle)}</p>
                        </div>
                    `;
                }
                if (excerptChanged) {
                    html += `
                        <div class="bg-white p-2.5 rounded-xl border border-indigo-200 text-xs shadow-2xs">
                            <span class="text-[10px] font-bold text-indigo-700 uppercase block mb-0.5">Tóm tắt (Sau khi sửa):</span>
                            <p class="text-slate-800">${escapeHtml(currentExcerpt)}</p>
                        </div>
                    `;
                }
                if (contentChanged) {
                    html += `
                        <div class="bg-white p-2.5 rounded-xl border border-emerald-200 text-xs shadow-2xs">
                            <span class="text-[10px] font-bold text-emerald-700 uppercase block mb-1">Nội dung (Sau khi sửa):</span>
                            <div class="max-h-56 overflow-y-auto prose prose-xs text-slate-800">${currentContent}</div>
                        </div>
                    `;
                }
                if (!titleChanged && !excerptChanged && !contentChanged) {
                    html = `
                        <div class="p-4 text-center bg-white rounded-xl border border-slate-200 text-slate-400 text-xs italic">
                            Chưa chọn từ nào để sửa. Bản xem trước đang giữ nguyên nội dung gốc của bài viết.
                        </div>
                    `;
                }
            }

            previewContainer.innerHTML = html;
        }

        // Update Apply button
        const applyBtn = document.getElementById('ai-spell-apply-btn');
        if (applyBtn) {
            if (selectedSpellIndices.size > 0) {
                applyBtn.disabled = false;
                applyBtn.style.opacity = '1';
                applyBtn.style.cursor = 'pointer';
                applyBtn.innerHTML = `
                    <span class="material-symbols-outlined text-sm">done_all</span>
                    <span>Áp dụng sửa (${selectedSpellIndices.size} từ đã chọn)</span>
                `;
            } else {
                applyBtn.disabled = true;
                applyBtn.style.opacity = '0.5';
                applyBtn.style.cursor = 'not-allowed';
                applyBtn.innerHTML = `
                    <span class="material-symbols-outlined text-sm">block</span>
                    <span>Chưa chọn từ nào</span>
                `;
            }
        }
    }

    function applyAiSpellFix() {
        if (!lastSpellCheckResult || !activeSelectiveSpell || activeSelectiveSpell.selectedCount === 0) {
            showAiToast('Vui lòng chọn ít nhất 1 từ muốn sửa!', 'warning');
            return;
        }

        const titleEl = getTargetTitleInput();
        const excerptEl = getTargetExcerptInput();
        const quillInstance = getTargetQuillInstance();
        const contentInput = getTargetContentInput();

        let appliedFields = [];

        // 1. Update Title if changed
        if (titleEl && activeSelectiveSpell.title !== lastSpellCheckResult.title_original) {
            titleEl.value = activeSelectiveSpell.title;
            titleEl.dispatchEvent(new Event('input', { bubbles: true }));
            titleEl.classList.add('ring-4', 'ring-emerald-400');
            setTimeout(() => {
                titleEl.classList.remove('ring-4', 'ring-emerald-400');
            }, 2500);
            appliedFields.push('Tiêu đề');
        }

        // 2. Update Excerpt if changed
        if (excerptEl && activeSelectiveSpell.excerpt !== lastSpellCheckResult.excerpt_original) {
            excerptEl.value = activeSelectiveSpell.excerpt;
            excerptEl.dispatchEvent(new Event('input', { bubbles: true }));
            excerptEl.classList.add('ring-4', 'ring-emerald-400');
            setTimeout(() => {
                excerptEl.classList.remove('ring-4', 'ring-emerald-400');
            }, 2500);
            appliedFields.push('Mô tả tóm tắt');
        }

        // 3. Update Quill & Hidden Input if changed
        if (activeSelectiveSpell.content !== lastSpellCheckResult.content_original) {
            if (quillInstance) {
                quillInstance.clipboard.dangerouslyPasteHTML(activeSelectiveSpell.content);
                const quillContainer = quillInstance.container;
                if (quillContainer) {
                    quillContainer.classList.add('ring-4', 'ring-emerald-400');
                    setTimeout(() => {
                        quillContainer.classList.remove('ring-4', 'ring-emerald-400');
                    }, 2500);
                }
            }
            if (contentInput) {
                contentInput.value = activeSelectiveSpell.content;
            }
            appliedFields.push('Nội dung');
        }

        closeAiModal('aiSpellModal');

        const selectedCount = activeSelectiveSpell.selectedCount;
        const fieldsMsg = appliedFields.length > 0 ? ` vào ${appliedFields.join(', ')}` : '';
        showAiToast(`Đã áp dụng sửa ${selectedCount} từ đã chọn thành công${fieldsMsg}!`, 'success');
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

        const catInput = getTargetCategoryInput();
        const currentCat = (catInput ? catInput.value : '').trim().toLowerCase();
        const isCatSelected = currentCat.length > 0 && currentCat === (data.category || '').trim().toLowerCase();

        const tagsInput = getTargetTagsInput();
        const currentTags = (tagsInput ? tagsInput.value : '')
            .split(',')
            .map(s => s.trim().toLowerCase().replace(/^#/, ''))
            .filter(Boolean);

        results.innerHTML = `
            {{-- Category Proposal --}}
            <div class="p-4 rounded-2xl space-y-2 border border-amber-200 transition-all" style="background-color: #fffbeb !important;">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-amber-700">folder_special</span>
                        Danh mục bài viết đề xuất:
                    </span>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-200 text-amber-900">${data.category_confidence}% Độ chuẩn xác</span>
                </div>
                <div class="flex items-center justify-between pt-1 gap-3">
                    <span class="text-sm font-extrabold text-slate-900">${data.category}</span>
                    <button type="button" 
                            id="ai-apply-cat-btn"
                            onclick="toggleSingleCategory('${data.category.replace(/'/g, "\\'")}')" 
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm flex items-center gap-1.5 shrink-0 ${isCatSelected ? 'bg-emerald-600 hover:bg-emerald-700 text-white ring-2 ring-emerald-400/40 shadow-xs' : 'bg-amber-600 hover:bg-amber-700 text-white active:scale-95'}">
                        <span class="material-symbols-outlined text-sm ai-cat-icon">${isCatSelected ? 'check_circle' : 'touch_app'}</span>
                        <span class="ai-cat-text">${isCatSelected ? 'Đã chọn danh mục' : 'Chọn danh mục này'}</span>
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
                    <span class="text-[10px] text-slate-500 font-medium">Bấm vào thẻ để thêm / bỏ</span>
                </div>
                <div class="flex flex-wrap gap-2 pt-1" id="ai-suggested-tags-container">
                    ${data.tags.map(t => {
                        const isSelected = currentTags.includes(t.toLowerCase().replace(/^#/, ''));
                        return `
                        <button type="button" 
                                data-tag="${t}"
                                onclick="toggleSingleAiTag('${t.replace(/'/g, "\\'")}', this)" 
                                class="ai-suggested-tag-btn px-3 py-1.5 text-xs font-bold rounded-full transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs active:scale-95 ${isSelected ? 'bg-amber-600 text-white border border-amber-600 ring-2 ring-amber-400/40 shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:border-amber-400 hover:bg-amber-50/70 hover:text-amber-900'}">
                            <span class="tag-hash font-bold ${isSelected ? 'text-white' : 'text-primary'}">#</span>
                            <span class="tag-name">${t}</span>
                            <span class="material-symbols-outlined text-xs tag-action-icon ${isSelected ? 'text-white' : 'text-slate-400'}">${isSelected ? 'check' : 'add'}</span>
                            ${isSelected ? '<span class="tag-badge text-[10px] bg-amber-700/70 text-white px-1.5 py-0.2 rounded-full font-bold ml-0.5">Đã thêm</span>' : ''}
                        </button>
                    `}).join('')}
                </div>
            </div>
        `;
    }

    function toggleSingleCategory(catName) {
        const catInput = getTargetCategoryInput();
        const btn = document.getElementById('ai-apply-cat-btn');
        if (!catInput) return;

        const currentCat = (catInput.value || '').trim();
        const isCurrentlySelected = currentCat.toLowerCase() === catName.toLowerCase();

        if (isCurrentlySelected) {
            // Hủy chọn
            catInput.value = '';
            catInput.dispatchEvent(new Event('input', { bubbles: true }));
            if (btn) {
                btn.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white active:scale-95 shrink-0";
                const icon = btn.querySelector('.ai-cat-icon');
                const text = btn.querySelector('.ai-cat-text');
                if (icon) icon.textContent = 'touch_app';
                if (text) text.textContent = 'Chọn danh mục này';
            }
            showAiToast(`Đã hủy chọn danh mục "${catName}"!`, 'info');
        } else {
            // Chọn danh mục
            catInput.value = catName;
            catInput.dispatchEvent(new Event('input', { bubbles: true }));
            catInput.classList.add('ring-2', 'ring-amber-500');
            setTimeout(() => catInput.classList.remove('ring-2', 'ring-amber-500'), 1500);

            if (btn) {
                btn.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white ring-2 ring-emerald-400/40 active:scale-95 shrink-0 scale-105";
                setTimeout(() => btn.classList.remove('scale-105'), 200);
                const icon = btn.querySelector('.ai-cat-icon');
                const text = btn.querySelector('.ai-cat-text');
                if (icon) icon.textContent = 'check_circle';
                if (text) text.textContent = 'Đã chọn danh mục';
            }
            showAiToast(`Đã gán danh mục "${catName}"!`);
        }
    }

    function applySingleCategory(catName) {
        toggleSingleCategory(catName);
    }

    function toggleSingleAiTag(tagName, btn) {
        const tagsInput = getTargetTagsInput();
        if (!tagsInput) return;

        const cleanTag = tagName.replace(/^#/, '').trim();
        let current = tagsInput.value.split(',').map(s => s.trim().replace(/^#/, '')).filter(Boolean);
        const lowerList = current.map(s => s.toLowerCase());
        const index = lowerList.indexOf(cleanTag.toLowerCase());

        const targetBtn = btn || document.querySelector(`.ai-suggested-tag-btn[data-tag="${cleanTag}"]`);

        if (index === -1) {
            // Chưa có -> Thêm thẻ và chuyển nút sang trạng thái "Đã thêm"
            current.push(cleanTag);
            tagsInput.value = current.join(', ');
            tagsInput.dispatchEvent(new Event('input', { bubbles: true }));
            if (typeof syncQuickTagStates === 'function') {
                syncQuickTagStates(tagsInput.id);
            }

            if (targetBtn) {
                targetBtn.className = "ai-suggested-tag-btn px-3 py-1.5 text-xs font-bold rounded-full transition-all flex items-center gap-1.5 cursor-pointer shadow-sm active:scale-95 bg-amber-600 text-white border border-amber-600 ring-2 ring-amber-400/40 scale-105";
                setTimeout(() => targetBtn.classList.remove('scale-105'), 200);
                const hash = targetBtn.querySelector('.tag-hash');
                const icon = targetBtn.querySelector('.tag-action-icon');
                if (hash) hash.className = 'tag-hash font-bold text-white';
                if (icon) {
                    icon.textContent = 'check';
                    icon.className = 'material-symbols-outlined text-xs tag-action-icon text-white';
                }
                if (!targetBtn.querySelector('.tag-badge')) {
                    const badge = document.createElement('span');
                    badge.className = 'tag-badge text-[10px] bg-amber-700/70 text-white px-1.5 py-0.2 rounded-full font-bold ml-0.5';
                    badge.textContent = 'Đã thêm';
                    targetBtn.appendChild(badge);
                }
            }
            showAiToast(`Đã thêm thẻ #${cleanTag}!`);
        } else {
            // Đã có -> Hủy thẻ và chuyển nút về trạng thái ban đầu
            current.splice(index, 1);
            tagsInput.value = current.length > 0 ? current.join(', ') : '';
            tagsInput.dispatchEvent(new Event('input', { bubbles: true }));
            if (typeof syncQuickTagStates === 'function') {
                syncQuickTagStates(tagsInput.id);
            }

            if (targetBtn) {
                targetBtn.className = "ai-suggested-tag-btn px-3 py-1.5 text-xs font-bold rounded-full transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs active:scale-95 bg-white text-slate-700 border border-slate-200 hover:border-amber-400 hover:bg-amber-50/70 hover:text-amber-900";
                const hash = targetBtn.querySelector('.tag-hash');
                const icon = targetBtn.querySelector('.tag-action-icon');
                if (hash) hash.className = 'tag-hash font-bold text-primary';
                if (icon) {
                    icon.textContent = 'add';
                    icon.className = 'material-symbols-outlined text-xs tag-action-icon text-slate-400';
                }
                const badge = targetBtn.querySelector('.tag-badge');
                if (badge) badge.remove();
            }
            showAiToast(`Đã gỡ thẻ #${cleanTag}!`, 'info');
        }
    }

    function appendSingleTag(tagName) {
        toggleSingleAiTag(tagName);
    }

    function applyAiTagsAndCategory() {
        if (!lastTagCategoryResult) return;

        // 1. Áp dụng danh mục
        const catInput = getTargetCategoryInput();
        if (catInput) {
            catInput.value = lastTagCategoryResult.category;
            catInput.dispatchEvent(new Event('input', { bubbles: true }));
            catInput.classList.add('ring-2', 'ring-amber-500');
            setTimeout(() => catInput.classList.remove('ring-2', 'ring-amber-500'), 1500);
        }

        const catBtn = document.getElementById('ai-apply-cat-btn');
        if (catBtn) {
            catBtn.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm flex items-center gap-1.5 bg-emerald-600 text-white ring-2 ring-emerald-400/40 shrink-0";
            const icon = catBtn.querySelector('.ai-cat-icon');
            const text = catBtn.querySelector('.ai-cat-text');
            if (icon) icon.textContent = 'check_circle';
            if (text) text.textContent = 'Đã chọn danh mục';
        }

        // 2. Áp dụng toàn bộ thẻ
        const tagsInput = getTargetTagsInput();
        if (tagsInput) {
            let current = tagsInput.value.split(',').map(s => s.trim().replace(/^#/, '')).filter(Boolean);
            lastTagCategoryResult.tags.forEach(t => {
                const clean = t.replace(/^#/, '').trim();
                if (!current.some(c => c.toLowerCase() === clean.toLowerCase())) {
                    current.push(clean);
                }
            });
            tagsInput.value = current.join(', ');
            tagsInput.dispatchEvent(new Event('input', { bubbles: true }));
            if (typeof syncQuickTagStates === 'function') {
                syncQuickTagStates(tagsInput.id);
            }
        }

        // Cập nhật tất cả các nút thẻ trong modal thành "Đã thêm"
        document.querySelectorAll('.ai-suggested-tag-btn').forEach(targetBtn => {
            targetBtn.className = "ai-suggested-tag-btn px-3 py-1.5 text-xs font-bold rounded-full transition-all flex items-center gap-1.5 cursor-pointer shadow-sm bg-amber-600 text-white border border-amber-600 ring-2 ring-amber-400/40";
            const hash = targetBtn.querySelector('.tag-hash');
            const icon = targetBtn.querySelector('.tag-action-icon');
            if (hash) hash.className = 'tag-hash font-bold text-white';
            if (icon) {
                icon.textContent = 'check';
                icon.className = 'material-symbols-outlined text-xs tag-action-icon text-white';
            }
            if (!targetBtn.querySelector('.tag-badge')) {
                const badge = document.createElement('span');
                badge.className = 'tag-badge text-[10px] bg-amber-700/70 text-white px-1.5 py-0.2 rounded-full font-bold ml-0.5';
                badge.textContent = 'Đã thêm';
                targetBtn.appendChild(badge);
            }
        });

        // Cập nhật nút Áp dụng tất cả
        const applyAllBtn = document.getElementById('ai-tag-apply-all-btn');
        if (applyAllBtn) {
            applyAllBtn.className = "px-5 py-2 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-1.5 bg-emerald-600 ring-2 ring-emerald-400/40";
            applyAllBtn.innerHTML = '<span class="material-symbols-outlined text-sm">done_all</span><span>Đã áp dụng tất cả!</span>';
        }

        showAiToast('Đã áp dụng toàn bộ Danh mục & Thẻ đề xuất!');

        setTimeout(() => {
            closeAiModal('aiTagCategoryModal');
            if (applyAllBtn) {
                applyAllBtn.className = "px-5 py-2 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1 shadow-sm cursor-pointer bg-amber-600 hover:bg-amber-700";
                applyAllBtn.innerHTML = '<span class="material-symbols-outlined text-sm">check_circle</span><span>Áp dụng tất cả</span>';
            }
        }, 800);
    }
</script>
