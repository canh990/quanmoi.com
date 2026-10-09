@extends('admin.layout')

@section('title', 'Duyệt bài viết - Quán Mới Admin')
@section('page-title', 'Duyệt Bài Viết')

@section('content')
<div class="space-y-6">
    
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.blog.posts.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <h2 class="text-xl font-bold text-gray-900">Chi tiết bài viết</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
                @if($blog->cover_image)
                    <img src="{{ $blog->cover_image_url }}" class="w-full rounded-xl mb-6 shadow-sm">
                @endif
                
                <div class="mb-4">
                    <span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full mb-2">{{ $blog->category->name ?? 'Chưa phân loại' }}</span>
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-4">{{ $blog->title }}</h1>
                    @if($blog->excerpt)
                        <p class="text-lg text-gray-600 font-medium mb-6">{{ $blog->excerpt }}</p>
                    @endif
                </div>

                <div class="prose max-w-none border-t border-gray-100 pt-6">
                    {!! $blog->content !!}
                </div>
                
                @if($blog->tags->count() > 0)
                    <div class="flex flex-wrap gap-2 mt-8 pt-6 border-t border-gray-100">
                        @foreach($blog->tags as $tag)
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">#{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            
            @if($blog->moderationLogs->count() > 0)
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Lịch sử kiểm duyệt</h3>
                <ul class="space-y-4">
                    @foreach($blog->moderationLogs as $log)
                        <li class="flex gap-3 text-sm">
                            <span class="material-symbols-outlined text-gray-400">history</span>
                            <div>
                                <p class="text-gray-900"><span class="font-bold">{{ $log->admin->ho_ten ?? 'Hệ thống' }}</span> đã <span class="font-bold text-primary">{{ $log->action }}</span> bài viết.</p>
                                @if($log->note)
                                    <p class="text-gray-600 mt-1 italic border-l-2 border-gray-200 pl-2">{{ $log->note }}</p>
                                @endif
                                <p class="text-gray-500 text-xs mt-1">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <!-- Sidebar / Actions -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin tác giả</h3>
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ $blog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($blog->user->ho_ten) }}" class="w-12 h-12 rounded-full">
                    <div>
                        <p class="font-bold text-gray-900">{{ $blog->user->ho_ten }}</p>
                        <p class="text-sm text-gray-500">{{ $blog->user->email }}</p>
                    </div>
                </div>
                <div class="text-sm text-gray-600 space-y-2">
                    <p><strong>Ngày tạo:</strong> {{ $blog->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Cập nhật:</strong> {{ $blog->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Phê duyệt</h3>
                
                <form action="{{ route('admin.blog.posts.update-status', $blog) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái mới</label>
                        <select name="status" class="w-full rounded-xl border border-gray-300 px-3 py-2 outline-none focus:border-primary">
                            <option value="published" {{ $blog->status == 'published' ? 'selected' : '' }}>Xuất bản (Approve)</option>
                            <option value="need_revision" {{ $blog->status == 'need_revision' ? 'selected' : '' }}>Yêu cầu chỉnh sửa</option>
                            <option value="rejected" {{ $blog->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                            <option value="hidden" {{ $blog->status == 'hidden' ? 'selected' : '' }}>Ẩn bài viết</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú (Gửi cho tác giả)</label>
                        <textarea name="note" rows="3" class="w-full rounded-xl border border-gray-300 px-3 py-2 outline-none focus:border-primary resize-none" placeholder="Lý do từ chối hoặc cần sửa..."></textarea>
                    </div>
                    
                    <button type="submit" class="w-full py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-hover transition-colors">
                        Cập nhật trạng thái
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
