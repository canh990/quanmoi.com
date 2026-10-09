@extends('layouts.app')
@section('title', 'Bài viết của tôi - Quán Mới')

@section('content')
<main class="max-w-[1200px] mx-auto px-container-margin py-stack-lg min-h-[819px] pt-6">
    @if(session('success'))
    <div id="toast-success" class="mb-6 flex items-center gap-3 bg-white border border-green-200 rounded-2xl px-5 py-4 shadow-lg shadow-green-100/50 animate-modal-in">
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
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-on-surface">Bài viết của tôi</h1>
                    <p class="text-on-surface-variant mt-1 text-sm">Quản lý các bài review, bản nháp và bài viết đã chia sẻ của bạn.</p>
                </div>
                <a href="{{ route('nguoi-dung.blog.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-full font-bold hover:bg-primary/90 transition-colors shadow-sm text-sm shrink-0">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Viết bài mới
                </a>
            </div>

            <!-- Status Filter Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-outline-variant/50">
                <a href="{{ route('nguoi-dung.blog.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ !request('status') ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                    <span>Tất cả</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ !request('status') ? 'bg-white/20 text-white' : 'bg-black/5 text-on-surface-variant' }}">{{ $totalCount ?? 0 }}</span>
                </a>
                
                <a href="{{ route('nguoi-dung.blog.index', ['status' => 'draft']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request('status') === 'draft' ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                    <span class="material-symbols-outlined text-sm">edit_note</span>
                    <span>Bản nháp</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') === 'draft' ? 'bg-white/20 text-white' : 'bg-black/5 text-on-surface-variant' }}">{{ $statusCounts['draft'] ?? 0 }}</span>
                </a>

                <a href="{{ route('nguoi-dung.blog.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                    <span class="material-symbols-outlined text-sm">hourglass_empty</span>
                    <span>Chờ duyệt</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') === 'pending' ? 'bg-white/20 text-white' : 'bg-black/5 text-on-surface-variant' }}">{{ $statusCounts['pending'] ?? 0 }}</span>
                </a>

                <a href="{{ route('nguoi-dung.blog.index', ['status' => 'published']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request('status') === 'published' ? 'bg-primary text-white shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    <span>Đã xuất bản</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ request('status') === 'published' ? 'bg-white/20 text-white' : 'bg-black/5 text-on-surface-variant' }}">{{ $statusCounts['published'] ?? 0 }}</span>
                </a>
            </div>

            @if($blogs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($blogs as $blog)
                        @php
                            $statusConfig = [
                                'draft' => ['bg' => 'bg-gray-100 text-gray-800 border-gray-300', 'label' => 'Bản nháp', 'icon' => 'edit_note'],
                                'pending' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-300', 'label' => 'Chờ duyệt', 'icon' => 'hourglass_empty'],
                                'need_revision' => ['bg' => 'bg-orange-100 text-orange-800 border-orange-300', 'label' => 'Cần sửa', 'icon' => 'warning'],
                                'published' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300', 'label' => 'Đã xuất bản', 'icon' => 'check_circle'],
                                'rejected' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-300', 'label' => 'Từ chối', 'icon' => 'cancel'],
                                'hidden' => ['bg' => 'bg-slate-200 text-slate-700 border-slate-300', 'label' => 'Đã ẩn', 'icon' => 'visibility_off'],
                            ];
                            $config = $statusConfig[$blog->status] ?? $statusConfig['draft'];
                        @endphp
                        
                        <article class="bg-white rounded-2xl overflow-hidden shadow-sm border border-outline-variant/60 flex flex-col group hover:shadow-md transition-all duration-300">
                            <!-- Image & Status -->
                            <div class="relative aspect-[4/3] bg-surface-container overflow-hidden">
                                @if($blog->cover_image_url)
                                    <img src="{{ $blog->cover_image_url }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                         alt="{{ $blog->title }}">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-slate-400">
                                        <span class="material-symbols-outlined text-4xl mb-1">image</span>
                                        <span class="text-xs font-semibold">Chưa có ảnh bìa</span>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-3 right-3 px-2.5 py-1 rounded-full flex items-center gap-1 text-xs font-bold border shadow-sm backdrop-blur-md {{ $config['bg'] }}">
                                    <span class="material-symbols-outlined text-[14px]">{{ $config['icon'] }}</span>
                                    {{ $config['label'] }}
                                </div>
                                
                                <!-- Category Badge -->
                                @if($blog->category)
                                    <div class="absolute bottom-3 left-3 px-2.5 py-0.5 rounded-md bg-black/70 backdrop-blur-md text-white text-[11px] font-bold uppercase tracking-wider">
                                        {{ $blog->category->name }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="font-bold text-base leading-snug text-on-surface mb-2 line-clamp-2 group-hover:text-primary transition-colors flex-1" title="{{ $blog->title }}">
                                    {{ $blog->title }}
                                </h3>
                                
                                @if($blog->tags && $blog->tags->count() > 0)
                                    <div class="flex flex-wrap gap-1 mb-3">
                                        @foreach($blog->tags->take(3) as $t)
                                            <span class="text-[10px] bg-amber-50 text-amber-800 font-bold px-2 py-0.5 rounded-full border border-amber-200">#{{ $t->name }}</span>
                                        @endforeach
                                        @if($blog->tags->count() > 3)
                                            <span class="text-[10px] text-text-muted">+{{ $blog->tags->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="flex items-center justify-between text-xs text-text-muted mb-4">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[15px]">calendar_today</span> {{ $blog->created_at->format('d/m/Y') }}</span>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[15px]">visibility</span> {{ number_format($blog->view_count ?? 0) }} lượt xem</span>
                                </div>
                                
                                <!-- Actions -->
                                <div class="pt-3 border-t border-outline-variant/40 flex items-center gap-2">
                                    @if(in_array($blog->status, ['draft', 'need_revision', 'rejected']))
                                        <a href="{{ route('nguoi-dung.blog.edit', $blog) }}" class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-white text-xs font-bold transition-colors">
                                            <span class="material-symbols-outlined text-base">edit</span> Chỉnh sửa & Đăng
                                        </a>
                                    @else
                                        <div class="flex-1 text-center py-2 text-xs text-text-muted font-medium bg-surface-container rounded-xl cursor-not-allowed flex items-center justify-center gap-1">
                                            <span class="material-symbols-outlined text-sm">lock</span> Đã gửi duyệt
                                        </div>
                                    @endif
                                    
                                    @if($blog->status === 'published')
                                        <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="w-9 h-9 rounded-xl flex items-center justify-center bg-surface-container text-on-surface hover:bg-primary hover:text-white transition-colors" title="Xem bài viết">
                                            <span class="material-symbols-outlined text-base">open_in_new</span>
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
                <div class="text-center py-16 bg-white rounded-3xl border border-outline-variant/60 shadow-sm">
                    <span class="material-symbols-outlined text-6xl text-slate-300 mb-3">edit_document</span>
                    <h3 class="text-lg font-bold text-on-surface mb-1">Không có bài viết nào {{ request('status') ? 'ở mục này' : '' }}</h3>
                    <p class="text-sm text-text-muted mb-6">Bạn có thể tạo bản nháp hoặc bài viết review bất kỳ lúc nào.</p>
                    <a href="{{ route('nguoi-dung.blog.create') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-full font-bold text-xs hover:bg-primary/90 transition-all shadow-sm">
                        <span class="material-symbols-outlined text-base">edit</span> Viết bài ngay
                    </a>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection
