@extends('admin.layout')

@section('title', 'Quản Lý Vai Trò - Quán Mới Admin')
@section('page-title', 'Quản Lý Vai Trò (Roles)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-black text-slate-900">Danh Sách Vai Trò Trong Hệ Thống</h3>
            <p class="text-xs text-slate-500 mt-1">Cấu hình các nhóm vai trò và gán quyền hạn tương ứng cho từng nhóm.</p>
        </div>

        {{-- Modal button or form --}}
        <form action="{{ route('admin.vai-tro.store') }}" method="POST" class="flex items-center gap-2">
            @csrf
            <input type="text" name="ten" placeholder="Tên vai trò mới (ví dụ: bien_tap_vien)" required class="px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none w-64">
            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5 whitespace-nowrap">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Tạo vai trò
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full uppercase border border-indigo-200/60">
                            {{ $role->ten }}
                        </span>
                        <span class="text-xs font-semibold text-slate-500">
                            {{ $role->nguoi_dung_count }} người dùng
                        </span>
                    </div>

                    <h4 class="text-lg font-bold text-slate-900 mt-3 capitalize">
                        Role: {{ str_replace('_', ' ', $role->ten) }}
                    </h4>
                    <p class="text-xs text-slate-500 mt-1">Gồm {{ $role->quyen_han_count }} quyền hạn mặc định.</p>

                    <form action="{{ route('admin.vai-tro.update-permissions', $role->id) }}" method="POST" class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                        @csrf
                        @method('PUT')
                        <p class="text-xs font-bold text-slate-700 uppercase">Quyền hạn gắn với Role này:</p>

                        <div class="max-h-48 overflow-y-auto space-y-1.5 custom-scrollbar pr-1">
                            @foreach($permissions as $perm)
                                @php
                                    $hasPerm = $role->quyenHan->contains($perm->id);
                                @endphp
                                <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer hover:bg-slate-50 p-1.5 rounded-lg">
                                    <input type="checkbox" name="quyen_han[]" value="{{ $perm->id }}" {{ $hasPerm ? 'checked' : '' }} class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                    <span class="font-medium">{{ $perm->ten }}</span>
                                </label>
                            @endforeach
                        </div>

                        <button type="submit" class="w-full mt-3 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-colors">
                            Lưu cấu hình Role
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
