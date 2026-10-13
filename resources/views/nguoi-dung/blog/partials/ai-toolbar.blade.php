@props(['context' => 'modal'])

{{-- Modern AI Assistant Action Bar --}}
<div class="p-3 sm:p-4 rounded-2xl bg-gradient-to-r from-purple-50 via-indigo-50/60 to-amber-50/80 border border-purple-200/90 shadow-2xs space-y-2.5">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center shadow-xs">
                <span class="material-symbols-outlined text-[18px]">psychology</span>
            </div>
            <div>
                <span class="text-xs font-extrabold text-slate-900 tracking-wide flex items-center gap-1.5">
                    Trợ Lý AI Viết Bài & Tối Ưu Tự Động
                    <span class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full uppercase tracking-wider">AI Powered</span>
                </span>
            </div>
        </div>
        <span class="text-[11px] font-medium text-slate-500 hidden sm:inline-block">Chọn công cụ để AI hỗ trợ tức thì</span>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        {{-- Tool 1: Gợi ý Tiêu đề --}}
        <button type="button" 
                onclick="openAiTitleSuggestModal('{{ $context }}')" 
                class="p-2 sm:py-2.5 sm:px-3 bg-white hover:bg-purple-50 border border-slate-200 hover:border-purple-300 rounded-xl transition-all shadow-2xs group flex items-center gap-2 text-left">
            <span class="material-symbols-outlined text-purple-600 text-lg group-hover:scale-110 transition-transform">auto_fix_high</span>
            <div class="leading-tight">
                <span class="block text-[11px] font-extrabold text-slate-800 group-hover:text-purple-700">Gợi ý Tiêu đề</span>
                <span class="block text-[9px] text-slate-400">5 tiêu đề hút view</span>
            </div>
        </button>

        {{-- Tool 2: Dàn ý Review Quán --}}
        <button type="button" 
                onclick="openAiOutlineModal('{{ $context }}')" 
                class="p-2 sm:py-2.5 sm:px-3 bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 rounded-xl transition-all shadow-2xs group flex items-center gap-2 text-left">
            <span class="material-symbols-outlined text-indigo-600 text-lg group-hover:scale-110 transition-transform">article</span>
            <div class="leading-tight">
                <span class="block text-[11px] font-extrabold text-slate-800 group-hover:text-indigo-700">Dàn ý Review</span>
                <span class="block text-[9px] text-slate-400">Tạo theo từ khóa</span>
            </div>
        </button>

        {{-- Tool 3: Sửa lỗi chính tả --}}
        <button type="button" 
                onclick="runAiSpellCheck('{{ $context }}')" 
                class="p-2 sm:py-2.5 sm:px-3 bg-white hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 rounded-xl transition-all shadow-2xs group flex items-center gap-2 text-left">
            <span class="material-symbols-outlined text-emerald-600 text-lg group-hover:scale-110 transition-transform">spellcheck</span>
            <div class="leading-tight">
                <span class="block text-[11px] font-extrabold text-slate-800 group-hover:text-emerald-700">Sửa chính tả</span>
                <span class="block text-[9px] text-slate-400">Chuẩn hóa câu từ</span>
            </div>
        </button>

        {{-- Tool 4: Gán Thẻ & Danh mục --}}
        <button type="button" 
                onclick="runAiTagging('{{ $context }}')" 
                class="p-2 sm:py-2.5 sm:px-3 bg-white hover:bg-amber-50 border border-slate-200 hover:border-amber-300 rounded-xl transition-all shadow-2xs group flex items-center gap-2 text-left">
            <span class="material-symbols-outlined text-amber-600 text-lg group-hover:scale-110 transition-transform">sell</span>
            <div class="leading-tight">
                <span class="block text-[11px] font-extrabold text-slate-800 group-hover:text-amber-700">Thẻ & Danh mục</span>
                <span class="block text-[9px] text-slate-400">Đề xuất tự động</span>
            </div>
        </button>
    </div>
</div>
