@extends('admin.layout')

@section('title', 'Thẻ Blog - Quán Mới Admin')
@section('page-title', 'Quản Lý Thẻ (Tags)')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Form Thêm mới -->
    <div class="md:col-span-1">
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Thêm Thẻ</h3>
            <form action="{{ route('admin.blog.tags.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên thẻ <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full rounded-xl border border-gray-300 px-3 py-2 outline-none focus:border-primary">
                </div>
                <button type="submit" class="w-full py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-hover transition-colors">
                    Thêm mới
                </button>
            </form>
        </div>
    </div>

    <!-- Danh sách -->
    <div class="md:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden p-6">
            <div class="flex flex-wrap gap-3">
                @foreach($tags as $tag)
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 border border-gray-200 rounded-full text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer">
                        #{{ $tag->name }}
                        <span class="bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $tag->blogs_count }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $tags->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
