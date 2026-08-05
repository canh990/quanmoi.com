@extends('layouts.app')
@section('title', 'Bài viết của tôi - Quán Mới')

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
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-outline-variant bg-surface-container/50">
                            <th class="p-4 font-semibold text-on-surface-variant text-sm whitespace-nowrap">Bài viết</th>
                            <th class="p-4 font-semibold text-on-surface-variant text-sm whitespace-nowrap">Trạng thái</th>
                            <th class="p-4 font-semibold text-on-surface-variant text-sm whitespace-nowrap">Ngày tạo</th>
                            <th class="p-4 font-semibold text-on-surface-variant text-sm whitespace-nowrap">Lượt xem</th>
                            <th class="p-4 font-semibold text-on-surface-variant text-sm whitespace-nowrap text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @foreach($blogs as $blog)
                        <tr class="hover:bg-surface-container/30 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-16 h-12 rounded-lg bg-surface-container overflow-hidden shrink-0">
                                        @if($blog->cover_image)
                                            <img src="{{ Storage::disk('r2')->url($blog->cover_image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-on-surface-variant">
                                                <span class="material-symbols-outlined">image</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-on-surface line-clamp-1 max-w-[300px]">{{ $blog->title }}</p>
                                        <p class="text-xs text-on-surface-variant mt-1">{{ $blog->category->name ?? 'Không có' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                @php
                                    $statusConfig = [
                                        'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Bản nháp'],
                                        'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Đang chờ duyệt'],
                                        'need_revision' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Cần chỉnh sửa'],
                                        'published' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Đã xuất bản'],
                                        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Bị từ chối'],
                                        'hidden' => ['bg' => 'bg-gray-200', 'text' => 'text-gray-500', 'label' => 'Đã ẩn'],
                                    ];
                                    $config = $statusConfig[$blog->status] ?? $statusConfig['draft'];
                                @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="p-4 text-sm text-on-surface-variant">
                                {{ $blog->created_at->format('d/m/Y') }}
                            </td>
                            <td class="p-4 text-sm text-on-surface-variant">
                                {{ $blog->views_count ?? 0 }}
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(in_array($blog->status, ['draft', 'need_revision', 'rejected']))
                                        <a href="{{ route('nguoi-dung.blog.edit', $blog) }}" class="w-8 h-8 rounded-full flex items-center justify-center bg-primary/10 text-primary hover:bg-primary hover:text-white transition-colors" title="Chỉnh sửa">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                    @endif
                                    @if($blog->status === 'published')
                                        <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="w-8 h-8 rounded-full flex items-center justify-center bg-surface-container text-on-surface-variant hover:bg-surface-container-highest transition-colors" title="Xem bài viết">
                                            <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-outline-variant">
                {{ $blogs->links() }}
            </div>
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
</main>
@endsection
