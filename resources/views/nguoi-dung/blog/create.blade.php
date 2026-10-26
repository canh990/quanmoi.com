@extends('layouts.app')
@section('title', 'Viết bài mới - Quán Mới')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<style>
    .ql-container {
        font-family: 'Be Vietnam Pro', sans-serif;
        font-size: 16px;
        min-height: 400px;
        border-radius: 0 0 0.75rem 0.75rem;
    }
    .ql-toolbar {
        border-radius: 0.75rem 0.75rem 0 0;
        border-color: #e5e7eb !important;
        background: #f9fafb;
    }
    .ql-container.ql-snow {
        border-color: #e5e7eb !important;
    }

    /* Custom Modern Toggle Switch */
    .qm-switch-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        padding: 4px;
    }
    .qm-switch-native {
        position: absolute !important;
        opacity: 0 !important;
        width: 1px !important;
        height: 1px !important;
        margin: -1px !important;
        padding: 0 !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        border: 0 !important;
        pointer-events: none !important;
    }
    .qm-switch-slider {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 28px;
        background-color: #cbd5e1;
        border-radius: 9999px;
        transition: background-color 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.08);
        cursor: pointer;
    }
    .qm-switch-knob {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 22px;
        height: 22px;
        background-color: #ffffff;
        border-radius: 9999px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.22);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .qm-switch-native:checked + .qm-switch-slider {
        background-color: #a04100;
    }
    .qm-switch-native:checked + .qm-switch-slider .qm-switch-knob {
        transform: translateX(22px);
    }
    .qm-switch-wrapper:hover .qm-switch-slider {
        filter: brightness(0.97);
    }
    .qm-switch-native:focus-visible + .qm-switch-slider {
        outline: 2px solid #a04100;
        outline-offset: 2px;
    }
</style>
@endpush

@section('content')
<main class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-screen">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        @include('nguoi-dung.partials.sidebar')
        
        <div class="lg:col-span-9 space-y-6">
            <div class="mb-8">
                <a href="{{ route('nguoi-dung.blog.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-on-surface-variant hover:text-primary transition-colors mb-4">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Quay lại danh sách
                </a>
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-extrabold text-on-surface">Viết bài mới</h1>
                    <span id="page-draft-indicator" class="text-xs font-semibold text-slate-500 opacity-0 transition-opacity flex items-center"></span>
                </div>
            </div>

            <!-- Draft Alert Banner -->
            <div id="qm-draft-alert-banner" class="hidden mb-6 p-4 rounded-2xl border border-amber-300 bg-gradient-to-r from-amber-50 to-orange-50/70 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-amber-950 animate-modal-in">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-xl">history_edu</span>
                    </div>
                    <div>
                        <span class="font-extrabold text-amber-900 text-sm block sm:inline" id="qm-draft-title-label">Đã tự động khôi phục nội dung đang viết dở của bạn!</span>
                        <span class="text-slate-500 font-medium ml-0 sm:ml-1 text-xs" id="qm-draft-time-label"></span>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="dismissDraftBanner()" class="px-3.5 py-1.5 rounded-xl bg-white border border-amber-200 text-amber-800 font-bold hover:bg-amber-100/60 transition-colors cursor-pointer shadow-2xs">
                        Tiếp tục viết
                    </button>
                    <button type="button" onclick="discardSavedDraft()" class="px-3.5 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 font-bold hover:bg-rose-100 transition-colors cursor-pointer flex items-center gap-1 shadow-2xs">
                        <span class="material-symbols-outlined text-sm">delete_sweep</span>
                        <span>Xóa nháp & Viết mới</span>
                    </button>
                </div>
            </div>

    @if(session('success'))
        <div class="page-success-banner mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-sm flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-emerald-900">Thành công!</h4>
                    <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.closest('.page-success-banner')?.remove()" class="p-1.5 text-emerald-500 hover:text-emerald-700 hover:bg-emerald-100 rounded-lg transition-colors cursor-pointer" title="Đóng">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <ul class="list-disc list-inside text-red-600 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('nguoi-dung.blog.store') }}" method="POST" enctype="multipart/form-data" id="blog-form" onsubmit="event.preventDefault(); submitForm('pending');" class="space-y-6">
        @csrf
        
        <!-- Cover Image -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-bold text-on-surface">Ảnh bìa bài viết</label>
                <span id="page-cover-badge" class="hidden text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full flex items-center gap-1 shadow-2xs">
                    <span class="material-symbols-outlined text-xs">aspect_ratio</span> Tỷ lệ 21:9 chuẩn
                </span>
            </div>

            <div class="relative w-full aspect-[21/9] min-h-[160px] bg-surface-container rounded-2xl overflow-hidden border-2 border-dashed border-outline-variant flex items-center justify-center group shadow-2xs" id="cover-preview-container">
                <input type="file" name="cover_image" id="cover_image" accept="image/jpeg,image/png,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(this)">
                
                <div class="text-center p-4 select-none pointer-events-none" id="cover-placeholder">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-2">add_photo_alternate</span>
                    <p class="text-sm font-medium text-on-surface-variant">Nhấn hoặc kéo thả để tải ảnh bìa lên</p>
                    <p class="text-xs text-on-surface-variant mt-1">Hỗ trợ JPG, PNG, WEBP (Tỷ lệ 21:9 chuẩn ảnh bìa, tối đa 5MB)</p>
                    <span class="inline-flex items-center gap-1 mt-2 text-xs font-bold text-primary bg-primary/10 px-3 py-1 rounded-full border border-primary/20">
                        <span class="material-symbols-outlined text-xs">crop</span> Có hỗ trợ cắt & chỉnh sửa ảnh trực tiếp
                    </span>
                </div>
                
                <img id="cover-preview" src="" alt="Ảnh bìa bài viết" class="absolute inset-0 w-full h-full object-cover hidden">
                
                <!-- Actions Bar khi đã có ảnh -->
                <div id="page-cover-actions-bar" class="absolute inset-0 bg-slate-950/45 backdrop-blur-[1px] opacity-0 group-hover:opacity-100 transition-opacity duration-200 hidden flex flex-col items-center justify-center gap-2 p-3 z-20">
                    <div class="flex items-center gap-2 flex-wrap justify-center">
                        <button type="button" onclick="editPageCoverImage(event)" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-extrabold text-xs shadow-lg flex items-center gap-1.5 transition-all transform hover:scale-105 active:scale-95 cursor-pointer">
                            <span class="material-symbols-outlined text-base">crop</span>
                            <span>Chỉnh sửa & Cắt ảnh</span>
                        </button>
                        <button type="button" onclick="changePageCoverImage(event)" class="px-4 py-2 rounded-xl bg-slate-900/85 hover:bg-slate-900 text-white font-bold text-xs backdrop-blur-md shadow-md flex items-center gap-1.5 transition-all hover:scale-105 active:scale-95 cursor-pointer border border-white/10">
                            <span class="material-symbols-outlined text-base">sync</span>
                            <span>Đổi ảnh</span>
                        </button>
                        <button type="button" onclick="removePageCoverImage(event)" class="p-2 rounded-xl bg-rose-600/90 hover:bg-rose-600 text-white font-bold text-xs backdrop-blur-md shadow-md flex items-center justify-center transition-all hover:scale-105 active:scale-95 cursor-pointer border border-white/10" title="Xóa ảnh bìa">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </div>
                </div>

                <!-- Nút Chỉnh sửa cố định góc dưới khi có ảnh -->
                <div id="page-cover-fixed-edit-btn" class="absolute bottom-3 right-3 hidden z-20 group-hover:hidden">
                    <button type="button" onclick="editPageCoverImage(event)" class="px-3.5 py-1.5 rounded-xl bg-slate-900/85 hover:bg-slate-900 text-amber-300 font-bold text-xs backdrop-blur-md shadow-md border border-slate-700 flex items-center gap-1.5 transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-sm text-amber-400">crop</span>
                        <span>Chỉnh sửa</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- AI Content Writer Assistant Toolbar --}}
        @include('nguoi-dung.blog.partials.ai-toolbar', ['context' => 'page'])

        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant space-y-6">
            <!-- Title -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="title" class="block text-sm font-bold text-on-surface">Tiêu đề <span class="text-red-500">*</span></label>
                    <button type="button" onclick="openAiTitleSuggestModal('page')" class="text-xs font-bold text-purple-600 hover:text-purple-800 flex items-center gap-1 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-sm">auto_fix_high</span> Gợi ý tiêu đề AI
                    </button>
                </div>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Nhập tiêu đề bài viết..." class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary text-lg font-medium py-3">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category with Live Autocomplete Suggestions -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="create_category_name" class="block text-sm font-bold text-on-surface">Danh mục bài viết <span class="text-red-500">*</span></label>
                        <button type="button" onclick="runAiTagging('page')" class="text-xs font-bold text-amber-600 hover:text-amber-800 flex items-center gap-1 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-sm">psychology</span> AI Đề xuất
                        </button>
                    </div>
                    <div class="relative autocomplete-wrapper">
                        <input type="text" 
                               id="create_category_name" 
                               name="category_name" 
                               list="create_category_datalist"
                               value="{{ old('category_name', old('new_category')) }}" 
                               placeholder="Gõ hoặc chọn danh mục (VD: Review Quán, Mon Ngon)..." 
                               autocomplete="off"
                               required
                               class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary font-bold text-sm py-3 px-4 pr-10 shadow-sm"
                               onclick="toggleCategoryDropdown('create_cat_dropdown')"
                               oninput="filterCategorySuggestions(this, 'create_cat_dropdown')">
                        <button type="button" 
                                onclick="toggleCategoryDropdown('create_cat_dropdown')"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-text-muted hover:text-primary p-1 rounded-lg transition-colors flex items-center justify-center cursor-pointer"
                                title="Mở danh mục">
                            <span class="material-symbols-outlined text-xl">expand_more</span>
                        </button>
                        
                        <!-- Suggestions Dropdown list attached right below input -->
                        <div id="create_cat_dropdown" class="cat-dropdown-menu absolute left-0 right-0 top-full mt-1 bg-white border border-amber-300 rounded-2xl shadow-xl z-50 max-h-48 overflow-y-auto hidden divide-y divide-slate-100">
                            @foreach($categories as $category)
                                <div class="cat-suggestion-item px-4 py-2.5 hover:bg-amber-50 cursor-pointer text-xs font-bold text-on-surface flex items-center justify-between transition-colors"
                                     data-name="{{ strtolower($category->name) }}"
                                     onclick="selectCategoryItem('create_category_name', '{{ addslashes($category->name) }}', 'create_cat_dropdown')">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-amber-600 text-sm">folder</span>
                                        <span>{{ $category->name }}</span>
                                    </span>
                                    <span class="text-[10px] text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full font-bold">Danh mục có sẵn</span>
                                </div>
                            @endforeach
                            <div class="cat-custom-option hidden px-4 py-2.5 bg-amber-50 hover:bg-amber-100 cursor-pointer text-xs font-bold text-amber-900 flex items-center justify-between transition-colors border-t border-amber-200"
                                 onclick="selectCustomCategoryItem('create_category_name', 'create_cat_dropdown')">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-amber-700 text-sm">add_circle</span>
                                    <span>Dùng danh mục mới: "<span class="cat-custom-text"></span>"</span>
                                </span>
                                <span class="text-[10px] text-white bg-amber-600 px-2 py-0.5 rounded-full font-bold">Mới</span>
                            </div>
                        </div>

                        <datalist id="create_category_datalist">
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                
                <!-- Tags with Live Autocomplete Suggestions & Quick Badges -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="create_custom_tags" class="block text-sm font-bold text-on-surface">Thẻ bài viết (Tags)</label>
                        <span class="text-xs font-bold text-primary">Gõ thẻ mới (nhấn Enter) hoặc chọn</span>
                    </div>
                    <div class="relative autocomplete-wrapper">
                        <input type="text" 
                               id="create_custom_tags" 
                               name="custom_tags" 
                               list="create_tag_datalist"
                               value="{{ old('custom_tags') }}" 
                               placeholder="Gõ tên thẻ mới (nhấn Enter hoặc dấu phẩy để thêm)..." 
                               autocomplete="off"
                               class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary text-xs font-semibold py-3 px-4 pr-10 shadow-sm"
                               onkeydown="handleTagInputKeydown(event, this, 'create_tag_dropdown')"
                               onclick="toggleTagDropdown('create_tag_dropdown')"
                               oninput="filterTagSuggestions(this, 'create_tag_dropdown'); syncQuickTagStates('create_custom_tags');">
                        <button type="button" 
                                onclick="toggleTagDropdown('create_tag_dropdown')"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-text-muted hover:text-primary p-1 rounded-lg transition-colors flex items-center justify-center cursor-pointer"
                                title="Mở gợi ý thẻ">
                            <span class="material-symbols-outlined text-lg">local_offer</span>
                        </button>
                        
                        <!-- Tag Suggestions Dropdown List attached right below input -->
                        <div id="create_tag_dropdown" class="tag-dropdown-menu absolute left-0 right-0 top-full mt-1 bg-white border border-amber-300 rounded-2xl shadow-xl z-50 max-h-48 overflow-y-auto hidden divide-y divide-slate-100">
                            @foreach($tags as $tag)
                                <div class="tag-suggestion-item px-4 py-2 hover:bg-amber-50 cursor-pointer text-xs font-bold text-on-surface flex items-center justify-between transition-colors"
                                     data-name="{{ strtolower($tag->name) }}"
                                     onclick="selectTagItem('create_custom_tags', '{{ addslashes($tag->name) }}', 'create_tag_dropdown')">
                                    <span class="flex items-center gap-1.5">
                                        <span class="text-primary font-bold">#</span>
                                        <span>{{ $tag->name }}</span>
                                    </span>
                                    <span class="text-[10px] text-text-muted">Gợi ý thẻ</span>
                                </div>
                            @endforeach
                            <div class="tag-custom-option hidden px-4 py-2.5 bg-amber-50 hover:bg-amber-100 cursor-pointer text-xs font-bold text-amber-900 flex items-center justify-between transition-colors border-t border-amber-200"
                                 onclick="selectCustomTagItem('create_custom_tags', 'create_tag_dropdown')">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-amber-700 text-sm">add_circle</span>
                                    <span>Thêm thẻ mới: #<span class="tag-custom-text font-bold text-amber-800"></span></span>
                                </span>
                                <span class="text-[10px] text-white bg-amber-600 px-2 py-0.5 rounded-full font-bold">Thêm mới</span>
                            </div>
                        </div>

                        <datalist id="create_tag_datalist">
                            @foreach($tags as $tag)
                                <option value="{{ $tag->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    
                    <!-- Quick Tag Badges with interactive toggle state -->
                    <div class="quick-tags-container flex flex-wrap gap-1.5 mt-2 max-h-24 overflow-y-auto p-2 bg-surface-container/50 rounded-xl border border-outline-variant/50" 
                         data-target="create_custom_tags"
                         data-popular='@json($tags->take(8)->pluck("name"))'>
                        @foreach($tags->take(8) as $tag)
                            <button type="button" 
                                    data-tag="{{ strtolower($tag->name) }}"
                                    onclick="addQuickTag('create_custom_tags', '{{ addslashes($tag->name) }}')"
                                    class="quick-tag-btn px-2.5 py-1 text-[11px] rounded-full border border-outline-variant bg-white text-on-surface-variant font-bold hover:border-primary hover:text-primary transition-all flex items-center gap-1 cursor-pointer select-none active:scale-95">
                                <span class="material-symbols-outlined text-xs tag-icon">add</span>
                                <span>#{{ $tag->name }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-bold text-on-surface mb-2">Mô tả ngắn</label>
                <textarea id="excerpt" name="excerpt" rows="3" placeholder="Tóm tắt ngắn gọn nội dung bài viết..." class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary resize-none">{{ old('excerpt') }}</textarea>
            </div>

            <!-- Content -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-bold text-on-surface">Nội dung <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openAiOutlineModal('page')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-sm">article</span> AI Dàn ý
                        </button>
                        <span class="text-slate-300">•</span>
                        <button type="button" onclick="runAiSpellCheck('page')" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 flex items-center gap-1 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-sm">spellcheck</span> AI Sửa chính tả
                        </button>
                    </div>
                </div>
                <input type="hidden" name="content" id="content-input" value="{{ old('content') }}">
                <div id="editor-container">
                    {!! old('content') !!}
                </div>
            </div>

            <!-- Settings: Allow Comments -->
            <div class="pt-5 border-t border-slate-200 flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <label for="allow_comments" class="text-sm font-bold text-slate-900 flex items-center gap-2 cursor-pointer select-none">
                        <span class="material-symbols-outlined text-amber-800 text-xl">forum</span>
                        <span>Cho phép độc giả bình luận</span>
                    </label>
                    <p class="text-xs text-slate-500">Bật tính năng này để cho phép độc giả trao đổi và để lại nhận xét dưới bài viết (được bảo vệ bởi AI Moderation).</p>
                </div>
                <label for="allow_comments" class="qm-switch-wrapper cursor-pointer select-none shrink-0" title="Bật / Tắt bình luận">
                    <input type="checkbox" name="allow_comments" id="allow_comments" value="1" {{ old('allow_comments', '1') == '1' ? 'checked' : '' }} class="qm-switch-native">
                    <span class="qm-switch-slider">
                        <span class="qm-switch-knob"></span>
                    </span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 sticky bottom-6 p-4 bg-white/90 backdrop-blur-md rounded-2xl shadow-[0_-4px_20px_rgba(0,0,0,0.05)] border border-outline-variant z-50">
            <button type="button" onclick="submitForm('draft')" class="px-6 py-2.5 rounded-full font-bold text-primary bg-primary/10 hover:bg-primary/20 transition-colors">
                Lưu bản nháp
            </button>
            <button type="button" onclick="submitForm('pending')" class="px-6 py-2.5 rounded-full font-bold text-on-primary bg-primary hover:bg-primary-hover transition-colors shadow-sm">
                Gửi duyệt bài
            </button>
        </div>
        
        <input type="hidden" name="action" id="action-input" value="draft">
    </form>
        </div>
    </div>
</main>

{{-- AI Assistant Modals & Script --}}
@include('nguoi-dung.blog.partials.ai-assistant')

{{-- Cover Image Cropper & Editor Modal --}}
@include('nguoi-dung.blog.partials.image-cropper-modal')

{{-- Blog Draft Auto-save & Restoration Manager --}}
@include('nguoi-dung.blog.partials.draft-manager', ['context' => 'page'])
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Viết nội dung tại đây...',
        modules: {
            toolbar: [
                [{ 'header': [2, 3, 4, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });

    function toggleCategoryDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const isHidden = dropdown.classList.contains('hidden');
        document.querySelectorAll('.cat-dropdown-menu, .tag-dropdown-menu').forEach(d => {
            if (d.id !== dropdownId) d.classList.add('hidden');
        });
        if (isHidden) {
            dropdown.querySelectorAll('.cat-suggestion-item').forEach(item => item.classList.remove('hidden'));
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }

    function filterCategorySuggestions(input, dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const query = input.value.trim().toLowerCase();
        dropdown.classList.remove('hidden');
        const items = dropdown.querySelectorAll('.cat-suggestion-item');
        let exactMatch = false;
        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            if (!query || name.includes(query)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
            if (query && name === query) {
                exactMatch = true;
            }
        });

        const customOption = dropdown.querySelector('.cat-custom-option');
        if (customOption) {
            if (query && !exactMatch) {
                customOption.classList.remove('hidden');
                const textSpan = customOption.querySelector('.cat-custom-text');
                if (textSpan) textSpan.textContent = input.value.trim();
            } else {
                customOption.classList.add('hidden');
            }
        }
    }

    function selectCategoryItem(inputId, value, dropdownId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.value = value;
            input.dispatchEvent(new Event('change'));
        }
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
    }

    function selectCustomCategoryItem(inputId, dropdownId) {
        const input = document.getElementById(inputId);
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
        if (input) input.focus();
    }

    function toggleTagDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const isHidden = dropdown.classList.contains('hidden');
        document.querySelectorAll('.tag-dropdown-menu, .cat-dropdown-menu').forEach(d => {
            if (d.id !== dropdownId) d.classList.add('hidden');
        });
        if (isHidden) {
            dropdown.querySelectorAll('.tag-suggestion-item').forEach(item => item.classList.remove('hidden'));
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }

    function filterTagSuggestions(input, dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const rawTerms = input.value.split(',');
        const currentTerm = rawTerms[rawTerms.length - 1].trim().toLowerCase().replace(/^#/, '');
        dropdown.classList.remove('hidden');
        const items = dropdown.querySelectorAll('.tag-suggestion-item');
        let exactMatch = false;
        items.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase().replace(/^#/, '');
            if (!currentTerm || name.includes(currentTerm)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
            if (currentTerm && name === currentTerm) {
                exactMatch = true;
            }
        });

        const customOption = dropdown.querySelector('.tag-custom-option');
        if (customOption) {
            const rawCurrent = rawTerms[rawTerms.length - 1].trim().replace(/^#/, '');
            if (rawCurrent && !exactMatch) {
                customOption.classList.remove('hidden');
                const textSpan = customOption.querySelector('.tag-custom-text');
                if (textSpan) textSpan.textContent = rawCurrent;
            } else {
                customOption.classList.add('hidden');
            }
        }
    }

    function handleTagInputKeydown(event, input, dropdownId) {
        if (event.key === 'Enter') {
            event.preventDefault();
            commitCurrentTag(input.id, dropdownId);
        }
    }

    function commitCurrentTag(inputId, dropdownId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const rawTerms = input.value.split(',');
        const currentTerm = rawTerms[rawTerms.length - 1].trim().replace(/^#/, '');
        if (currentTerm) {
            let terms = rawTerms.map(t => t.trim().replace(/^#/, '')).filter(t => t.length > 0);
            let uniqueTerms = [];
            terms.forEach(t => {
                if (!uniqueTerms.includes(t)) uniqueTerms.push(t);
            });
            input.value = uniqueTerms.join(', ') + ', ';
        } else if (input.value.trim().length > 0 && !input.value.trim().endsWith(',')) {
            input.value = input.value.trim() + ', ';
        }
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
        syncQuickTagStates(inputId);
    }

    function selectCustomTagItem(inputId, dropdownId) {
        commitCurrentTag(inputId, dropdownId);
        const input = document.getElementById(inputId);
        if (input) input.focus();
    }

    function selectTagItem(inputId, tagName, dropdownId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        tagName = tagName.replace(/^#/, '');
        let terms = input.value.split(',').map(t => t.trim().replace(/^#/, '')).filter(t => t.length > 0);
        if (!terms.includes(tagName)) {
            if (terms.length > 0) terms.pop();
            terms.push(tagName);
        }
        input.value = terms.join(', ') + ', ';
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
        syncQuickTagStates(inputId);
    }

    function addQuickTag(inputId, tagName) {
        const input = document.getElementById(inputId);
        if (!input) return;
        tagName = tagName.replace(/^#/, '').trim();
        let terms = input.value.split(',').map(t => t.trim().replace(/^#/, '')).filter(t => t.length > 0);
        const lowerList = terms.map(t => t.toLowerCase());
        const index = lowerList.indexOf(tagName.toLowerCase());
        if (index >= 0) {
            // Already present -> Toggle off (remove)
            terms.splice(index, 1);
        } else {
            // Not present -> Toggle on (add)
            terms.push(tagName);
        }
        input.value = terms.length > 0 ? terms.join(', ') + ', ' : '';
        input.dispatchEvent(new Event('input', { bubbles: true }));
        syncQuickTagStates(inputId);
    }

    function syncQuickTagStates(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        const container = document.querySelector(`.quick-tags-container[data-target="${inputId}"]`);
        if (!container) return;

        // Lấy danh sách thẻ người dùng đang có trong ô input
        const rawTerms = input.value.split(',').map(t => t.trim().replace(/^#/, '')).filter(t => t.length > 0);
        const currentTerms = [];
        rawTerms.forEach(t => {
            if (!currentTerms.some(c => c.toLowerCase() === t.toLowerCase())) {
                currentTerms.push(t);
            }
        });

        // Lấy danh sách thẻ phổ biến mặc định từ data-popular
        let popularList = [];
        try {
            popularList = JSON.parse(container.getAttribute('data-popular') || '[]');
        } catch(e) {}
        if (!popularList || popularList.length === 0) {
            popularList = ['ReviewQuan', 'MonNgon', 'AmThuc', 'KhongGianChill', 'DiaDiemAnUong', 'KhamPha', 'GocChuQuan', 'KhuyenMai'];
        }

        let html = '';

        // 1. Thẻ vừa dùng / đang dùng (luôn hiện lên đầu với màu cam nổi bật & dấu tích ✓)
        currentTerms.forEach(term => {
            const safeTerm = term.replace(/'/g, "\\'");
            html += `
                <button type="button" 
                        data-tag="${term.toLowerCase()}"
                        onclick="addQuickTag('${inputId}', '${safeTerm}')"
                        class="quick-tag-btn px-2.5 py-1 text-[11px] rounded-full border border-amber-600 bg-amber-600 text-white font-bold transition-all flex items-center gap-1 cursor-pointer select-none shadow-2xs active:scale-95"
                        title="Bấm để bỏ thẻ #${term}">
                    <span class="material-symbols-outlined text-xs tag-icon">check</span>
                    <span>#${term}</span>
                </button>
            `;
        });

        // 2. Vài thẻ phổ biến chưa dùng (chỉ hiện tối đa 6 gợi ý để thanh thẻ luôn gọn gàng)
        let unselectedCount = 0;
        popularList.forEach(popTag => {
            const isAlreadySelected = currentTerms.some(c => c.toLowerCase() === popTag.toLowerCase());
            if (!isAlreadySelected && unselectedCount < 6) {
                unselectedCount++;
                const safeTag = popTag.replace(/'/g, "\\'");
                html += `
                    <button type="button" 
                            data-tag="${popTag.toLowerCase()}"
                            onclick="addQuickTag('${inputId}', '${safeTag}')"
                            class="quick-tag-btn px-2.5 py-1 text-[11px] rounded-full border border-amber-200/80 bg-amber-50/60 text-amber-800 font-medium hover:bg-amber-100 hover:border-amber-300 transition-all flex items-center gap-1 cursor-pointer select-none active:scale-95"
                            title="Bấm để thêm nhanh #${popTag}">
                        <span class="material-symbols-outlined text-xs tag-icon">add</span>
                        <span>#${popTag}</span>
                    </button>
                `;
            }
        });

        container.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', function() {
        syncQuickTagStates('create_custom_tags');
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.autocomplete-wrapper')) {
            document.querySelectorAll('.cat-dropdown-menu, .tag-dropdown-menu').forEach(d => d.classList.add('hidden'));
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.cat-dropdown-menu, .tag-dropdown-menu').forEach(d => d.classList.add('hidden'));
        }
    });

    function submitForm(action) {
        // Clear local draft when submitting
        if (window.clearDraftOnSubmit) {
            window.clearDraftOnSubmit();
        }

        // Sync quill content to hidden input
        var html = quill.root.innerHTML;
        if(html === '<p><br></p>') html = '';
        document.getElementById('content-input').value = html;
        
        // Set action (draft or pending)
        document.getElementById('action-input').value = action;
        
        // Submit
        document.getElementById('blog-form').submit();
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!file.type.match(/^image\//)) {
                if (typeof showAiToast === 'function') {
                    showAiToast('Vui lòng chọn tệp hình ảnh hợp lệ (JPG, PNG, WEBP).', 'error');
                } else if (typeof showToast === 'function') {
                    showToast({ title: 'Định dạng không hỗ trợ', message: 'Vui lòng chọn ảnh JPG, PNG hoặc WEBP.', type: 'error' });
                }
                input.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                if (typeof showAiToast === 'function') {
                    showAiToast('Dung lượng ảnh không được vượt quá 5MB.', 'error');
                } else if (typeof showToast === 'function') {
                    showToast({ title: 'Ảnh quá lớn', message: 'Dung lượng ảnh không được vượt quá 5MB.', type: 'error' });
                }
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const imageSrc = e.target.result;
                const preview = document.getElementById('cover-preview');
                const placeholder = document.getElementById('cover-placeholder');
                const actionsBar = document.getElementById('page-cover-actions-bar');
                const fixedBtn = document.getElementById('page-cover-fixed-edit-btn');
                const badge = document.getElementById('page-cover-badge');
                const container = document.getElementById('cover-preview-container');

                if (preview) {
                    preview.src = imageSrc;
                    preview.classList.remove('hidden');
                }
                if (placeholder) placeholder.classList.add('hidden');
                if (actionsBar) actionsBar.classList.remove('hidden');
                if (fixedBtn) fixedBtn.classList.remove('hidden');
                if (badge) badge.classList.remove('hidden');
                if (container) {
                    container.classList.remove('border-dashed');
                    container.classList.add('border-solid', 'border-amber-400');
                }

                input.classList.add('pointer-events-none');

                // Tự động mở modal chỉnh sửa ảnh
                setTimeout(() => {
                    openCoverImageCropper(imageSrc, 'page');
                }, 120);
            };
            reader.readAsDataURL(file);
        }
    }

    function editPageCoverImage(event) {
        if (event) event.stopPropagation();
        const preview = document.getElementById('cover-preview');
        if (preview && preview.src && !preview.classList.contains('hidden')) {
            openCoverImageCropper(preview.src, 'page');
        } else {
            const input = document.getElementById('cover_image');
            if (input) {
                input.classList.remove('pointer-events-none');
                input.click();
            }
        }
    }

    function changePageCoverImage(event) {
        if (event) event.stopPropagation();
        const input = document.getElementById('cover_image');
        if (input) {
            input.classList.remove('pointer-events-none');
            input.click();
        }
    }

    function removePageCoverImage(event) {
        if (event) event.stopPropagation();
        const input = document.getElementById('cover_image');
        const preview = document.getElementById('cover-preview');
        const placeholder = document.getElementById('cover-placeholder');
        const actionsBar = document.getElementById('page-cover-actions-bar');
        const fixedBtn = document.getElementById('page-cover-fixed-edit-btn');
        const badge = document.getElementById('page-cover-badge');
        const container = document.getElementById('cover-preview-container');

        if (input) {
            input.value = '';
            input.classList.remove('pointer-events-none');
        }
        if (preview) {
            preview.src = '';
            preview.classList.add('hidden');
        }
        if (placeholder) placeholder.classList.remove('hidden');
        if (actionsBar) actionsBar.classList.add('hidden');
        if (fixedBtn) fixedBtn.classList.add('hidden');
        if (badge) badge.classList.add('hidden');
        if (container) {
            container.classList.add('border-dashed');
            container.classList.remove('border-solid', 'border-amber-400');
        }
    }
</script>
@endpush
