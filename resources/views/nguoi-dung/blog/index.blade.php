@extends('layouts.app')
@section('title', 'Bài viết của tôi - Quán Mới')

@php
    /** @var \Illuminate\Filesystem\FilesystemAdapter $r2Disk */
    $r2Disk = Storage::disk('r2');
@endphp

@section('content')
<main class="max-w-[1200px] mx-auto px-container-margin py-stack-lg min-h-[819px] pt-6">
    @if(session('success'))
    <div id="toast-success" class="mb-6 flex items-center gap-3 bg-white border border-green-200 rounded-2xl px-5 py-4 shadow-lg shadow-green-100/50">
        <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-green-600" style="font-variation-settings:'FILL' 1">check_circle</span>
        </div>
        <div class="flex-1">
            <p class="font-bold text-gray-900 text-[15px]">Thành công</p>
            <p class="text-sm text-gray-600 mt-0.5">{{ session('success') }}</p>
        </div>
        <button onclick="document.getElementById('toast-success').remove()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-stack-lg">
        @include('nguoi-dung.partials.sidebar')

        <div class="md:col-span-9 space-y-stack-lg">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-on-surface">Bài viết của tôi</h1>
            <p class="text-on-surface-variant mt-2">Quản lý và theo dõi các bài review, chia sẻ của bạn.</p>
        </div>
        <a href="{{ route('nguoi-dung.blog.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-full font-bold hover:bg-primary-hover transition-colors shadow-sm">
            <span class="material-symbols-outlined">add</span>
            Viết bài mới
        </a>
    </div>

    @if($blogs->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-stack-lg mb-8">
            @foreach($blogs as $blog)
                @php
                    $statusConfig = [
                        'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Bản nháp', 'icon' => 'edit_note'],
                        'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Chờ duyệt', 'icon' => 'hourglass_empty'],
                        'need_revision' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Cần sửa', 'icon' => 'warning'],
                        'published' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Đã xuất bản', 'icon' => 'check_circle'],
                        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Từ chối', 'icon' => 'cancel'],
                        'hidden' => ['bg' => 'bg-gray-200', 'text' => 'text-gray-500', 'label' => 'Đã ẩn', 'icon' => 'visibility_off'],
                    ];
                    $config = $statusConfig[$blog->status] ?? $statusConfig['draft'];
                @endphp
                
                <article class="bg-surface-card rounded-2xl overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.04)] border border-outline-variant/30 flex flex-col group hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300">
                    <!-- Image & Status -->
                    <div class="relative aspect-[4/3] bg-surface-container overflow-hidden">
                        @if($blog->cover_image)
                            <img src="{{ Str::startsWith($blog->cover_image, 'http') ? $blog->cover_image : $r2Disk->url($blog->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-on-surface-variant/50">
                                <span class="material-symbols-outlined text-5xl">image</span>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3 px-3 py-1.5 rounded-full flex items-center gap-1.5 text-xs font-bold shadow-sm backdrop-blur-md {{ $config['bg'] }} {{ $config['text'] }} bg-opacity-90">
                            <span class="material-symbols-outlined text-[14px]">{{ $config['icon'] }}</span>
                            {{ $config['label'] }}
                        </div>
                        
                        <!-- Category Badge -->
                        @if($blog->category)
                            <div class="absolute bottom-3 left-3 px-3 py-1 rounded-lg bg-black/60 backdrop-blur-md text-white text-xs font-medium">
                                {{ $blog->category->name }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-title-md text-[16px] leading-[22px] text-on-surface mb-3 line-clamp-2 group-hover:text-primary transition-colors flex-1" title="{{ $blog->title }}">
                            {{ $blog->title }}
                        </h3>
                        
                        <div class="flex items-center justify-between text-xs text-on-surface-variant mb-4">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">calendar_today</span> {{ $blog->created_at->format('d/m/Y') }}</span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">visibility</span> {{ number_format($blog->view_count ?? 0) }} view</span>
                        </div>
                        
                        <!-- Actions -->
                        <div class="pt-4 border-t border-surface-dim flex items-center justify-between">
                            @if(in_array($blog->status, ['draft', 'need_revision', 'rejected']))
                                <a href="{{ route('nguoi-dung.blog.edit', $blog) }}" class="flex-1 flex items-center justify-center gap-2 py-2 rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-white font-label-md transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">edit</span> Sửa bài
                                </a>
                            @else
                                <div class="flex-1 text-center py-2 text-xs text-text-muted font-medium bg-surface-container rounded-xl cursor-not-allowed">
                                    Không thể sửa
                                </div>
                            @endif
                            
                            @if($blog->status === 'published')
                                <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="w-10 h-10 ml-3 rounded-xl flex items-center justify-center bg-surface-container text-on-surface hover:bg-primary-fixed hover:text-primary-fixed-variant transition-colors" title="Xem bài viết">
                                    <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        
        <div class="flex justify-center">
            {{ $blogs->links() }}
        </div>
    @else
        <div class="text-center py-20 bg-surface-container-lowest rounded-3xl border border-outline-variant">
            <span class="material-symbols-outlined text-6xl text-on-surface-variant mb-4">edit_document</span>
            <h3 class="text-xl font-bold text-on-surface mb-2">Bạn chưa có bài viết nào</h3>
            <p class="text-on-surface-variant mb-6">Hãy chia sẻ trải nghiệm và câu chuyện của bạn với mọi người.</p>
            <a href="{{ route('nguoi-dung.blog.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-full font-bold hover:bg-primary-hover transition-colors">
                <span class="material-symbols-outlined">edit</span> Viết bài ngay
            </a>
        </div>
            @endif
        </div>
    </div>
</main>
@endsection
