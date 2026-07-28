@extends('admin.layout')

@section('title', 'Quản Lý SEO - Quán Mới Admin')

@section('content')
<div class="p-6 md:p-8 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-gray-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">search</span>
                Quản Lý SEO
            </h2>
            <p class="text-gray-500 mt-1 text-sm">Cài đặt thẻ meta title, description và Open Graph cho từng trang của website.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl">
        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- SEO Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($seoList as $seo)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary text-[20px]">web</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-[16px]">{{ $seo->ten_trang }}</h3>
                            <span class="text-xs text-gray-400 font-mono bg-gray-100 px-2 py-0.5 rounded-md">/{{ $seo->trang }}</span>
                        </div>
                    </div>
                    <span class="{{ $seo->meta_title ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} text-[11px] font-bold px-2.5 py-1 rounded-full flex-shrink-0">
                        {{ $seo->meta_title ? '✅ Đã cài đặt' : '⚠️ Chưa cài đặt' }}
                    </span>
                </div>

                {{-- Google Preview --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-4 border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-2">Preview kết quả Google</p>
                    <div class="flex items-center gap-1.5 mb-1">
                        <div class="w-4 h-4 rounded-full bg-primary/20 flex-shrink-0"></div>
                        <span class="text-[11px] text-gray-500">quanmoi.com › {{ $seo->trang }}</span>
                    </div>
                    <p class="text-blue-700 font-medium text-[14px] leading-tight truncate">
                        {{ $seo->meta_title ?: '(Chưa có tiêu đề)' }}
                    </p>
                    <p class="text-gray-500 text-[12px] mt-1 line-clamp-2 leading-relaxed">
                        {{ $seo->meta_description ?: '(Chưa có mô tả)' }}
                    </p>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-[12px] text-gray-400 space-y-0.5">
                        @if($seo->meta_keywords)
                        <p><span class="font-medium text-gray-600">Keywords:</span> {{ Str::limit($seo->meta_keywords, 40) }}</p>
                        @endif
                        @if($seo->og_image)
                        <p class="flex items-center gap-1"><span class="material-symbols-outlined text-[13px] text-emerald-500">image</span> OG Image đã cài</p>
                        @endif
                    </div>
                    <a href="{{ route('admin.seo.edit', $seo->trang) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-white rounded-xl font-bold text-[13px] hover:bg-primary/90 transition-all shadow-sm hover:shadow-md">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        Chỉnh sửa
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Info box --}}
    <div class="mt-8 bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-start gap-4">
        <span class="material-symbols-outlined text-blue-500 text-2xl flex-shrink-0 mt-0.5">info</span>
        <div>
            <p class="font-bold text-blue-800 text-sm">Hướng dẫn sử dụng SEO</p>
            <ul class="mt-2 space-y-1 text-[13px] text-blue-700">
                <li>• <strong>Meta Title:</strong> Tối đa 60 ký tự. Đây là tiêu đề hiển thị trên Google và tab trình duyệt.</li>
                <li>• <strong>Meta Description:</strong> Tối đa 160 ký tự. Mô tả ngắn hiện trên kết quả tìm kiếm.</li>
                <li>• <strong>OG Image:</strong> Ảnh thumbnail khi chia sẻ lên Facebook, Zalo. Kích thước lý tưởng: 1200x630px.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
