@extends('layouts.app')
@section('title', 'Chỉnh sửa bài viết - Quán Mới')

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
<main class="max-w-[900px] mx-auto px-container-margin py-stack-lg min-h-[819px] pt-6">
    
    <div class="mb-8">
        <a href="{{ route('nguoi-dung.blog.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-on-surface-variant hover:text-primary transition-colors mb-4">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Quay lại danh sách
        </a>
        <h1 class="text-3xl font-extrabold text-on-surface">Chỉnh sửa bài viết</h1>
        
        @if($blog->status === 'need_revision')
            <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-xl flex gap-3">
                <span class="material-symbols-outlined text-orange-600">info</span>
                <div>
                    <h4 class="font-bold text-orange-800 text-sm">Bài viết cần chỉnh sửa</h4>
                    <p class="text-orange-700 text-sm mt-1">Quản trị viên đã yêu cầu bạn chỉnh sửa bài viết này trước khi xuất bản.</p>
                </div>
            </div>
        @endif
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

    <form action="{{ route('nguoi-dung.blog.update', $blog) }}" method="POST" enctype="multipart/form-data" id="blog-form" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Cover Image -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant">
            <label class="block text-sm font-bold text-on-surface mb-2">Ảnh bìa</label>
            <div class="relative w-full aspect-[21/9] bg-surface-container rounded-2xl overflow-hidden border-2 border-dashed border-outline-variant flex items-center justify-center group cursor-pointer" id="cover-preview-container">
                <input type="file" name="cover_image" id="cover_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(this)">
                
                <div class="text-center {{ $blog->cover_image ? 'hidden' : '' }}" id="cover-placeholder">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-2">add_photo_alternate</span>
                    <p class="text-sm font-medium text-on-surface-variant">Nhấn để tải ảnh bìa lên</p>
                    <p class="text-xs text-on-surface-variant mt-1">Tỷ lệ 21:9 (Max 5MB)</p>
                </div>
                
                <img id="cover-preview" src="{{ $blog->cover_image ? asset('storage/'.$blog->cover_image) : '' }}" class="absolute inset-0 w-full h-full object-cover {{ $blog->cover_image ? '' : 'hidden' }}">
                
                <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center z-20 pointer-events-none" id="cover-overlay">
                    <span class="text-white font-medium flex items-center gap-2"><span class="material-symbols-outlined">edit</span> Thay đổi ảnh</span>
                </div>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-bold text-on-surface mb-2">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $blog->title) }}" required class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary text-lg font-medium py-3">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-bold text-on-surface mb-2">Danh mục <span class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id" required class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $blog->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Tags -->
                <div>
                    <label for="tags" class="block text-sm font-bold text-on-surface mb-2">Thẻ (Tags)</label>
                    <select id="tags" name="tags[]" multiple class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary h-[42px]">
                        @php
                            $selectedTags = old('tags', $blog->tags->pluck('id')->toArray());
                        @endphp
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" {{ in_array($tag->id, $selectedTags) ? 'selected' : '' }}>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-on-surface-variant mt-1">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều thẻ.</p>
                </div>
            </div>

            <!-- Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-bold text-on-surface mb-2">Mô tả ngắn</label>
                <textarea id="excerpt" name="excerpt" rows="3" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary resize-none">{{ old('excerpt', $blog->excerpt) }}</textarea>
            </div>

            <!-- Content -->
            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Nội dung <span class="text-red-500">*</span></label>
                <input type="hidden" name="content" id="content-input" value="{{ old('content', $blog->content) }}">
                <div id="editor-container">
                    {!! old('content', $blog->content) !!}
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 sticky bottom-6 p-4 bg-white/90 backdrop-blur-md rounded-2xl shadow-[0_-4px_20px_rgba(0,0,0,0.05)] border border-outline-variant z-50">
            <button type="button" onclick="submitForm('draft')" class="px-6 py-2.5 rounded-full font-bold text-primary bg-primary/10 hover:bg-primary/20 transition-colors">
                Lưu bản nháp
            </button>
            <button type="button" onclick="submitForm('pending')" class="px-6 py-2.5 rounded-full font-bold text-on-primary bg-primary hover:bg-primary-hover transition-colors shadow-sm">
                Gửi duyệt lại
            </button>
        </div>
        
        <input type="hidden" name="action" id="action-input" value="draft">
    </form>
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

    function submitForm(action) {
        var html = quill.root.innerHTML;
        if(html === '<p><br></p>') html = '';
        document.getElementById('content-input').value = html;
        document.getElementById('action-input').value = action;
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
