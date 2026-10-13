@extends('admin.layout')

@section('title', 'Thông Báo Hệ Thống - Quán Mới Admin')
@section('page-title', 'Trung Tâm Quản Lý & Gửi Thông Báo')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
            <span class="material-symbols-outlined text-indigo-600">campaign</span>
            Gửi Thông Báo Hệ Thống (Broadcast)
        </h3>
        <p class="text-xs text-slate-500 mb-6">Tạo và gửi thông báo chung tới nhóm người dùng hoặc toàn bộ thành viên Quán Mới.</p>

        <form action="{{ route('admin.thong-bao.send') }}" method="POST" class="space-y-4 max-w-2xl">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Đối tượng nhận thông báo</label>
                <select name="doi_tuong" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                    <option value="tat_ca">Tất cả người dùng trên hệ thống</option>
                    <option value="chu_quan">Chỉ gửi các Chủ quán kinh doanh</option>
                    <option value="nguoi_dung">Chỉ gửi Thành viên thông thường</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tiêu đề thông báo</label>
                <input type="text" name="tieu_de" placeholder="Nhập tiêu đề thông báo..." required class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nội dung chi tiết</label>
                <textarea name="noi_dung" rows="4" placeholder="Nội dung thông báo..." required class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none"></textarea>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">send</span>
                Phát Thông Báo
            </button>
        </form>
    </div>
</div>
@endsection
