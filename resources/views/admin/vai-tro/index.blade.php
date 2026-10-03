@extends('admin.layout')

@section('title', 'Phân Quyền - Quán Mới Admin')
@section('page-title', 'Quản Lý Vai Trò & Phân Quyền')

@section('content')
<div class="space-y-6">
    
    {{-- Report-matching Header with Quay lại and Tạo vai trò mới buttons --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Phân quyền</h1>
            <p class="text-xs text-slate-500 mt-1">Quản lý vai trò và phân quyền của người dùng</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Button 1: Quay lại (Gray button) --}}
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5 shadow-xs">
                <span class="material-symbols-outlined text-[18px] text-slate-800">arrow_back</span>
                <span class="text-slate-800">Quay lại</span>
            </a>

            {{-- Button 2: Tạo vai trò mới (Blue button) --}}
            <button type="button" onclick="toggleTaoVaiTroModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5 shadow-md shadow-blue-600/30 cursor-pointer">
                <span class="material-symbols-outlined text-[18px] text-white">add</span>
                <span class="text-white">Tạo vai trò mới</span>
            </button>
        </div>
    </div>

    {{-- Report-matching Tabs (Vai trò vs Quyền hạn) --}}
    <div class="flex items-center gap-6 border-b border-slate-200 text-xs font-bold">
        <a href="{{ route('admin.vai-tro.index') }}" class="pb-3 px-1 border-b-2 border-blue-600 text-blue-600 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
            <span>Vai trò ({{ $roles->count() }})</span>
        </a>
        <a href="{{ route('admin.phan-quyen.index') }}" class="pb-3 px-1 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">security</span>
            <span>Quyền hạn ({{ $permissions->count() }})</span>
        </a>
    </div>

    {{-- Modal / Form Tạo Vai Trò Mới --}}
    <div id="tao-vai-tro-modal" class="bg-blue-50/60 rounded-2xl border border-blue-200 p-5 shadow-xs transition-all">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-blue-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600 text-[20px]">add_moderator</span>
                Tạo Vai Trò Mới Cho Hệ Thống
            </h3>
            <button type="button" onclick="toggleTaoVaiTroModal()" class="text-blue-400 hover:text-blue-700">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form action="{{ route('admin.vai-tro.store') }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @csrf
            <input type="text" name="ten" placeholder="Nhập mã vai trò mới (ví dụ: quan_tri_vien_phu)..." required class="flex-1 px-4 py-2.5 text-xs bg-white border border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Xác nhận tạo vai trò</span>
            </button>
        </form>
    </div>

    {{-- Roles List Grid --}}
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

                    <h4 class="text-base font-bold text-slate-900 mt-3 capitalize">
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
                                    <input type="checkbox" name="quyen_han[]" value="{{ $perm->id }}" {{ $hasPerm ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
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

<script>
    function toggleTaoVaiTroModal() {
        const modal = document.getElementById('tao-vai-tro-modal');
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }
</script>
@endsection

