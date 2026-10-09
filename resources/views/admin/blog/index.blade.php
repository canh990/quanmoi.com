@extends('admin.layout')

@section('title', 'Quản Lý Blog - Quán Mới Admin')
@section('page-title', 'Quản Lý Bài Viết')

@section('content')
<div class="space-y-6">
    <!-- Thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Tổng số bài viết</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600">article</span>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Chờ duyệt</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-600">pending_actions</span>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Đã xuất bản</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['published'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600">check_circle</span>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.blog.posts.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select name="status" class="px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                <option value="need_revision" {{ request('status') == 'need_revision' ? 'selected' : '' }}>Cần chỉnh sửa</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                Lọc
            </button>
        </form>
    </div>

    <!-- Danh sách -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-4">Bài viết</th>
                        <th class="px-6 py-4">Tác giả</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4">Hero</th>
                        <th class="px-6 py-4">Ngày tạo</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($blogs as $blog)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-10 rounded overflow-hidden bg-gray-100 shrink-0">
                                    @if($blog->cover_image)
                                        <img src="{{ Str::startsWith($blog->cover_image, 'http') ? $blog->cover_image : $r2Disk->url($blog->cover_image) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="max-w-[200px] truncate">
                                    <p class="font-bold text-gray-900 truncate" title="{{ $blog->title }}">{{ $blog->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $blog->category->name ?? 'Không phân loại' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <img src="{{ $blog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($blog->user->ho_ten) }}" class="w-6 h-6 rounded-full">
                                <span class="text-gray-700">{{ $blog->user->ho_ten }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusConfig = [
                                    'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Bản nháp'],
                                    'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Chờ duyệt'],
                                    'need_revision' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Cần sửa'],
                                    'published' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Xuất bản'],
                                    'scheduled' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Lên lịch'],
                                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Từ chối'],
                                    'hidden' => ['bg' => 'bg-gray-200', 'text' => 'text-gray-500', 'label' => 'Đã ẩn'],
                                ];
                                $config = $statusConfig[$blog->status] ?? $statusConfig['draft'];
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                {{ $config['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($blog->is_hero)
                                <span class="material-symbols-outlined text-amber-500">star</span>
                            @else
                                <form action="{{ route('admin.blog.posts.toggle-hero', $blog) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-gray-300 hover:text-amber-500 transition-colors" title="Đặt làm Hero">
                                        <span class="material-symbols-outlined">star</span>
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $blog->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.blog.posts.show', $blog) }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors" title="Xem chi tiết & Duyệt">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                                <form action="{{ route('admin.blog.posts.destroy', $blog) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-red-600 hover:bg-red-50 transition-colors" title="Xóa bài viết">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $blogs->links() }}
        </div>
    </div>
</div>
@endsection
