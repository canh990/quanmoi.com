@extends('layouts.app')

@section('title', 'Đã hủy nhận tin ẩm thực - ' . config('app.name', 'Quán Mới'))

@section('content')
<main class="max-w-[700px] mx-auto px-container-margin py-16 md:py-24 text-center">
    <div class="bg-surface-card rounded-3xl p-8 sm:p-12 shadow-[0_4px_25px_rgba(0,0,0,0.06)] border border-outline-variant/30">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-6 shadow-xs">
            <span class="material-symbols-outlined text-3xl">mark_email_read</span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold text-on-background mb-3">
            Đã hủy đăng ký nhận tin
        </h1>

        <p class="text-sm text-on-surface-variant max-w-md mx-auto leading-relaxed mb-8">
            Địa chỉ email <strong class="text-on-background">{{ $subscriber->email }}</strong> sẽ không còn nhận thông báo bài viết mới từ Quán Mới nữa.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('blog.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-full bg-primary text-white font-label-md text-sm hover:bg-primary/90 transition-all shadow-xs flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">auto_stories</span>
                Khám phá bài viết khác
            </a>
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-full border border-surface-dim font-label-md text-sm text-slate-700 hover:bg-surface-container transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">home</span>
                Về trang chủ
            </a>
        </div>
    </div>
</main>
@endsection
