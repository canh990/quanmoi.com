@extends('layouts.app')
@section('title', 'Viết bài mới - Quán Mới')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
        <h1 class="text-3xl font-extrabold text-on-surface">Viết bài mới</h1>
    </div>

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
            <label class="block text-sm font-bold text-on-surface mb-2">Ảnh bìa</label>
            <div class="relative w-full aspect-[21/9] min-h-[160px] bg-surface-container rounded-2xl overflow-hidden border-2 border-dashed border-outline-variant flex items-center justify-center group cursor-pointer" id="cover-preview-container">
                <input type="file" name="cover_image" id="cover_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(this)">
                
                <div class="text-center" id="cover-placeholder">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-2">add_photo_alternate</span>
                    <p class="text-sm font-medium text-on-surface-variant">Nhấn để tải ảnh bìa lên</p>
                    <p class="text-xs text-on-surface-variant mt-1">Tỷ lệ 21:9 (Max 5MB)</p>
                </div>
                
                <img id="cover-preview" src="" class="absolute inset-0 w-full h-full object-cover hidden">
                
                <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center z-20 pointer-events-none" id="cover-overlay">
                    <span class="text-white font-medium flex items-center gap-2"><span class="material-symbols-outlined">edit</span> Thay đổi ảnh</span>
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
                    <div class="quick-tags-container flex flex-wrap gap-1.5 mt-2 max-h-20 overflow-y-auto p-2 bg-surface-container/50 rounded-xl border border-outline-variant/50" data-target="create_custom_tags">
                        @foreach($tags as $tag)
                            <button type="button" 
                                    data-tag="{{ strtolower($tag->name) }}"
                                    onclick="addQuickTag('create_custom_tags', '{{ addslashes($tag->name) }}')"
                                    class="quick-tag-btn px-2.5 py-1 text-[11px] rounded-full border border-outline-variant bg-white text-on-surface-variant font-bold hover:border-primary hover:text-primary transition-all flex items-center gap-1 cursor-pointer select-none">
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
@endsection

@push('scripts')
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
        tagName = tagName.replace(/^#/, '');
        let terms = input.value.split(',').map(t => t.trim().replace(/^#/, '')).filter(t => t.length > 0);
        const index = terms.indexOf(tagName);
        if (index >= 0) {
            // Already present -> Toggle off (remove)
            terms.splice(index, 1);
        } else {
            // Not present -> Toggle on (add)
            terms.push(tagName);
        }
        input.value = terms.length > 0 ? terms.join(', ') + ', ' : '';
        syncQuickTagStates(inputId);
    }

    function syncQuickTagStates(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let terms = input.value.split(',').map(t => t.trim().toLowerCase().replace(/^#/, '')).filter(t => t.length > 0);
        
        const container = document.querySelector(`.quick-tags-container[data-target="${inputId}"]`);
        if (!container) return;

        container.querySelectorAll('.quick-tag-btn').forEach(btn => {
            const tag = (btn.getAttribute('data-tag') || '').toLowerCase().replace(/^#/, '');
            const icon = btn.querySelector('.tag-icon');
            if (terms.includes(tag)) {
                btn.className = "quick-tag-btn px-2.5 py-1 text-[11px] rounded-full border border-primary bg-primary text-white font-bold transition-all flex items-center gap-1 cursor-pointer select-none shadow-2xs";
                if (icon) icon.textContent = "check";
            } else {
                btn.className = "quick-tag-btn px-2.5 py-1 text-[11px] rounded-full border border-outline-variant bg-white text-on-surface-variant font-bold hover:border-primary hover:text-primary transition-all flex items-center gap-1 cursor-pointer select-none";
                if (icon) icon.textContent = "add";
            }
        });
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
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('cover-preview').src = e.target.result;
                document.getElementById('cover-preview').classList.remove('hidden');
                document.getElementById('cover-placeholder').classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
