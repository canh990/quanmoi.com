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

    <form action="{{ route('nguoi-dung.blog.store') }}" method="POST" enctype="multipart/form-data" id="blog-form" class="space-y-6">
        @csrf
        
        <!-- Cover Image -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant">
            <label class="block text-sm font-bold text-on-surface mb-2">Ảnh bìa</label>
            <div class="relative w-full aspect-[21/9] bg-surface-container rounded-2xl overflow-hidden border-2 border-dashed border-outline-variant flex items-center justify-center group cursor-pointer" id="cover-preview-container">
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

        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-bold text-on-surface mb-2">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Nhập tiêu đề bài viết..." class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary text-lg font-medium py-3">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category with Live Autocomplete Suggestions -->
                <div>
                    <label for="create_category_name" class="block text-sm font-bold text-on-surface mb-2">Danh mục bài viết <span class="text-red-500">*</span></label>
                    <div class="relative autocomplete-wrapper">
                        <input type="text" 
                               id="create_category_name" 
                               name="category_name" 
                               value="{{ old('category_name', old('new_category')) }}" 
                               placeholder="Gõ hoặc chọn danh mục (VD: Review Quán, Mon Ngon)..." 
                               autocomplete="off"
                               required
                               class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary font-bold text-sm py-3 px-4 pr-10 shadow-sm"
                               onfocus="showCategorySuggestions('create_cat_dropdown')"
                               oninput="filterCategorySuggestions(this, 'create_cat_dropdown')">
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-text-muted pointer-events-none text-xl">expand_more</span>
                        
                        <!-- Suggestions Dropdown list attached right below input -->
                        <div id="create_cat_dropdown" class="absolute left-0 right-0 top-full mt-1 bg-white border border-amber-300 rounded-2xl shadow-xl z-50 max-h-48 overflow-y-auto hidden divide-y divide-slate-100">
                            @foreach($categories as $category)
                                <div class="cat-suggestion-item px-4 py-2.5 hover:bg-amber-50 cursor-pointer text-xs font-bold text-on-surface flex items-center justify-between transition-colors"
                                     data-name="{{ strtolower($category->name) }}"
                                     onclick="selectCategoryItem('create_category_name', '{{ addslashes($category->name) }}', 'create_cat_dropdown')">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-amber-600 text-sm">folder</span>
                                        <span>{{ $category->name }}</span>
                                    </span>
                                    <span class="text-[10px] text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full font-bold">Gợi ý</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Tags with Live Autocomplete Suggestions & Quick Badges -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="create_custom_tags" class="block text-sm font-bold text-on-surface">Thẻ bài viết (Tags)</label>
                        <span class="text-xs font-bold text-primary">Gõ trực tiếp hoặc chọn bên dưới</span>
                    </div>
                    <div class="relative autocomplete-wrapper">
                        <input type="text" 
                               id="create_custom_tags" 
                               name="custom_tags" 
                               value="{{ old('custom_tags') }}" 
                               placeholder="Gõ tên thẻ (VD: Lẩu thái, Ăn đêm, Monngon)..." 
                               autocomplete="off"
                               class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest focus:ring-2 focus:ring-primary text-xs font-semibold py-3 px-4 pr-10 shadow-sm"
                               onfocus="showTagSuggestions('create_tag_dropdown')"
                               oninput="filterTagSuggestions(this, 'create_tag_dropdown')">
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-text-muted pointer-events-none text-lg">local_offer</span>
                        
                        <!-- Tag Suggestions Dropdown List attached right below input -->
                        <div id="create_tag_dropdown" class="absolute left-0 right-0 top-full mt-1 bg-white border border-amber-300 rounded-2xl shadow-xl z-50 max-h-44 overflow-y-auto hidden divide-y divide-slate-100">
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
                        </div>
                    </div>
                    
                    <!-- Quick Tag Badges -->
                    <div class="flex flex-wrap gap-1.5 mt-2 max-h-20 overflow-y-auto p-2 bg-surface-container/50 rounded-xl border border-outline-variant/50">
                        @foreach($tags as $tag)
                            <button type="button" 
                                    onclick="addQuickTag('create_custom_tags', '{{ addslashes($tag->name) }}')"
                                    class="px-2.5 py-1 text-[11px] rounded-full border border-outline-variant bg-white text-on-surface-variant font-bold hover:border-primary hover:text-primary transition-all flex items-center gap-1 cursor-pointer select-none">
                                <span>+#{{ $tag->name }}</span>
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
                <label class="block text-sm font-bold text-on-surface mb-2">Nội dung <span class="text-red-500">*</span></label>
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

    function showCategorySuggestions(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.remove('hidden');
    }

    function filterCategorySuggestions(input, dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const query = input.value.trim().toLowerCase();
        dropdown.classList.remove('hidden');
        const items = dropdown.querySelectorAll('.cat-suggestion-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            if (!query || name.includes(query)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    function selectCategoryItem(inputId, value, dropdownId) {
        const input = document.getElementById(inputId);
        if (input) input.value = value;
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
    }

    function showTagSuggestions(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.remove('hidden');
    }

    function filterTagSuggestions(input, dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const terms = input.value.split(',');
        const currentTerm = terms[terms.length - 1].trim().toLowerCase();
        dropdown.classList.remove('hidden');
        const items = dropdown.querySelectorAll('.tag-suggestion-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            if (!currentTerm || name.includes(currentTerm)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    function selectTagItem(inputId, tagName, dropdownId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let terms = input.value.split(',').map(t => t.trim()).filter(t => t.length > 0);
        if (!terms.includes(tagName)) {
            if (terms.length > 0) terms.pop();
            terms.push(tagName);
        }
        input.value = terms.join(', ') + ', ';
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) dropdown.classList.add('hidden');
    }

    function addQuickTag(inputId, tagName) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let terms = input.value.split(',').map(t => t.trim()).filter(t => t.length > 0);
        if (!terms.includes(tagName)) {
            terms.push(tagName);
        }
        input.value = terms.join(', ') + ', ';
        const dropdown = document.getElementById('create_tag_dropdown');
        if (dropdown) dropdown.classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.autocomplete-wrapper')) {
            document.querySelectorAll('#create_cat_dropdown, #create_tag_dropdown').forEach(d => d.classList.add('hidden'));
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('#create_cat_dropdown, #create_tag_dropdown').forEach(d => d.classList.add('hidden'));
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
