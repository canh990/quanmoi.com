@extends('admin.layout')

@section('title', 'Chỉnh sửa SEO: ' . $seo->ten_trang . ' - Quán Mới Admin')

@section('content')
<div class="p-6 md:p-8 max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.seo.index') }}" class="p-2 text-gray-500 hover:text-primary hover:bg-gray-100 rounded-xl transition-all">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-xl font-black text-gray-900">SEO: {{ $seo->ten_trang }}</h2>
            <p class="text-gray-400 text-sm font-mono">quanmoi.com/{{ $seo->trang }}</p>
        </div>
    </div>

    <form action="{{ route('admin.seo.update', $seo->trang) }}" method="POST" id="seo-form">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Left: Form Fields --}}
            <div class="space-y-5">

                {{-- Meta Section --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">code</span>
                        Thẻ Meta (Google Search)
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">
                                Meta Title
                                <span id="title-count" class="text-xs font-normal text-gray-400 ml-2">0/60</span>
                            </label>
                            <input type="text" name="meta_title" id="meta_title" maxlength="60"
                                value="{{ old('meta_title', $seo->meta_title) }}"
                                placeholder="VD: Quán Mới - Khám phá ẩm thực Việt Nam"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-[14px] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            @error('meta_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">
                                Meta Description
                                <span id="desc-count" class="text-xs font-normal text-gray-400 ml-2">0/160</span>
                            </label>
                            <textarea name="meta_description" id="meta_description" maxlength="160" rows="3"
                                placeholder="Mô tả ngắn giúp người dùng hiểu nội dung trang (tối đa 160 ký tự)..."
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-[14px] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-none">{{ old('meta_description', $seo->meta_description) }}</textarea>
                            @error('meta_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Keywords</label>
                            <input type="text" name="meta_keywords"
                                value="{{ old('meta_keywords', $seo->meta_keywords) }}"
                                placeholder="quán ăn, nhà hàng, ẩm thực, cà phê..."
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-[14px] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            @error('meta_keywords') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Open Graph Section --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-500 text-[20px]">share</span>
                        Open Graph (Chia sẻ mạng xã hội)
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">OG Title</label>
                            <input type="text" name="og_title"
                                value="{{ old('og_title', $seo->og_title) }}"
                                placeholder="Để trống sẽ dùng Meta Title"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-[14px] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">OG Description</label>
                            <textarea name="og_description" rows="2"
                                placeholder="Để trống sẽ dùng Meta Description..."
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-[14px] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-none">{{ old('og_description', $seo->og_description) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">OG Image URL</label>
                            <input type="url" name="og_image" id="og_image"
                                value="{{ old('og_image', $seo->og_image) }}"
                                placeholder="https://..."
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-[14px] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            <p class="text-xs text-gray-400 mt-1">Kích thước lý tưởng: 1200 × 630px</p>
                            @error('og_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Save button --}}
                <button type="submit" class="w-full bg-primary text-white py-3.5 rounded-xl font-bold text-[15px] hover:bg-primary/90 transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    Lưu cài đặt SEO
                </button>
            </div>

            {{-- Right: Live Preview --}}
            <div class="space-y-5">

                {{-- Google Preview --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-4 flex items-center gap-2">
                        <span class="text-lg">🔍</span> Preview kết quả Google
                    </h3>
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <div class="flex items-center gap-1.5 mb-1">
                            <div class="w-4 h-4 rounded-full bg-green-100 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            </div>
                            <span class="text-[11px] text-gray-500">quanmoi.com › {{ $seo->trang }}</span>
                        </div>
                        <p id="preview-title" class="text-blue-700 font-medium text-[17px] leading-tight hover:underline cursor-pointer transition-all">
                            {{ $seo->meta_title ?: '(Chưa có tiêu đề)' }}
                        </p>
                        <p id="preview-desc" class="text-gray-600 text-[13px] mt-1.5 leading-relaxed">
                            {{ $seo->meta_description ?: '(Chưa có mô tả)' }}
                        </p>
                    </div>
                </div>

                {{-- Facebook / Zalo Preview --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-[15px] mb-4 flex items-center gap-2">
                        <span class="text-lg">📱</span> Preview khi chia sẻ
                    </h3>
                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                        <div id="og-image-preview" class="w-full h-40 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            @if($seo->og_image)
                            <img src="{{ $seo->og_image }}" class="w-full h-full object-cover" alt="OG Image">
                            @else
                            <span class="material-symbols-outlined text-gray-400 text-4xl">image</span>
                            @endif
                        </div>
                        <div class="bg-gray-50 p-3.5 border-t border-gray-200">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider">QUANMOI.COM</p>
                            <p id="og-title-preview" class="font-bold text-gray-900 text-[14px] mt-0.5 line-clamp-1">
                                {{ $seo->og_title ?: $seo->meta_title ?: '(Chưa có tiêu đề)' }}
                            </p>
                            <p id="og-desc-preview" class="text-gray-500 text-[12px] mt-0.5 line-clamp-2">
                                {{ $seo->og_description ?: $seo->meta_description ?: '(Chưa có mô tả)' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Character counters
    const titleInput = document.getElementById('meta_title');
    const descInput = document.getElementById('meta_description');
    const titleCount = document.getElementById('title-count');
    const descCount = document.getElementById('desc-count');
    const previewTitle = document.getElementById('preview-title');
    const previewDesc = document.getElementById('preview-desc');
    const ogTitleInput = document.querySelector('[name="og_title"]');
    const ogDescInput = document.querySelector('[name="og_description"]');
    const ogImageInput = document.getElementById('og_image');
    const ogTitlePreview = document.getElementById('og-title-preview');
    const ogDescPreview = document.getElementById('og-desc-preview');
    const ogImagePreview = document.getElementById('og-image-preview');

    function updateCount(input, counter, max) {
        const len = input.value.length;
        counter.textContent = `${len}/${max}`;
        counter.className = len > max * 0.9 ? 'text-xs font-normal text-red-500 ml-2' : 'text-xs font-normal text-gray-400 ml-2';
    }

    titleInput.addEventListener('input', () => {
        updateCount(titleInput, titleCount, 60);
        previewTitle.textContent = titleInput.value || '(Chưa có tiêu đề)';
        if (!ogTitleInput.value) ogTitlePreview.textContent = titleInput.value || '(Chưa có tiêu đề)';
    });

    descInput.addEventListener('input', () => {
        updateCount(descInput, descCount, 160);
        previewDesc.textContent = descInput.value || '(Chưa có mô tả)';
        if (!ogDescInput.value) ogDescPreview.textContent = descInput.value || '(Chưa có mô tả)';
    });

    ogTitleInput.addEventListener('input', () => {
        ogTitlePreview.textContent = ogTitleInput.value || titleInput.value || '(Chưa có tiêu đề)';
    });

    ogDescInput.addEventListener('input', () => {
        ogDescPreview.textContent = ogDescInput.value || descInput.value || '(Chưa có mô tả)';
    });

    ogImageInput.addEventListener('input', () => {
        const url = ogImageInput.value;
        if (url) {
            ogImagePreview.innerHTML = `<img src="${url}" class="w-full h-full object-cover" alt="OG Image" onerror="this.parentElement.innerHTML='<span class=\\'material-symbols-outlined text-red-400 text-4xl\\'>broken_image</span>'">`;
        } else {
            ogImagePreview.innerHTML = `<span class="material-symbols-outlined text-gray-400 text-4xl">image</span>`;
        }
    });

    // Init counters
    updateCount(titleInput, titleCount, 60);
    updateCount(descInput, descCount, 160);
</script>
@endpush
@endsection
