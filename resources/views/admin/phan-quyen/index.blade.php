@extends('admin.layout')

@section('title', 'Quản Lý & Override Phân Quyền - Quán Mới Admin')
@section('page-title', 'Phân Quyền Quyền Hạn & User Overrides')

@section('content')
<div class="space-y-8">
    
    {{-- Section 1: Add New Permission --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Thêm Quyền Hạn Mới Vấn Hệ Thống</h3>
        <p class="text-xs text-slate-500 mb-4">Các quyền hạn này có thể gán cho Role hoặc override trực tiếp theo từng User.</p>

        <form action="{{ route('admin.phan-quyen.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mã quyền (ten)</label>
                <input type="text" name="ten" placeholder="vd: quan_ly_nguoi_dung" required class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mô tả quyền hạn</label>
                <input type="text" name="mo_ta" placeholder="Mô tả chi tiết tác vụ..." class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">add_task</span>
                    Thêm Quyền Hạn
                </button>
            </div>
        </form>
    </div>

    {{-- Section 2: List of Permissions --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Danh Sách Quyền Hạn Hợp Lệ</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($permissions as $perm)
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                    <div>
                        <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200/60 inline-block">
                            {{ $perm->ten }}
                        </span>
                        <p class="text-xs text-slate-600 mt-2">{{ $perm->mo_ta ?? 'Chưa có mô tả' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Section 3: Per-User Permission Overrides (nguoi_dung_quyen_han) --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-slate-900">Cấu Hình Override Quyền Hạn Theo Người Dùng (nguoi_dung_quyen_han)</h3>
            <p class="text-xs text-slate-500 mt-1">
                Cho phép tùy chỉnh quyền hạn đặc biệt cho từng User cụ thể (gần như <span class="font-bold text-emerald-600">Cho phép</span> hoặc <span class="font-bold text-rose-600">Từ chối</span>) đè lên quy định mặc định của Role.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="p-3">Người Dùng</th>
                        <th class="p-3">Role Vẫn Gán</th>
                        <th class="p-3">Cấu Hình Override Hiện Tại</th>
                        <th class="p-3">Thao Tác Override</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-medium">
                                <div class="font-bold text-slate-900">{{ $u->ho_ten }}</div>
                                <div class="text-[11px] text-slate-500">{{ $u->email }}</div>
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                    {{ $u->ten_vai_tro_hien_thi }}
                                </span>
                            </td>
                            <td class="p-3">
                                @forelse($u->quyenHanOverrides as $ov)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold border mb-1 {{ $ov->pivot->cho_phep ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                        {{ $ov->ten }}: {{ $ov->pivot->cho_phep ? 'Cho phép' : 'Từ chối' }}
                                    </span>
                                @empty
                                    <span class="text-[11px] text-slate-400 italic">Dùng mặc định theo Role</span>
                                @endforelse
                            </td>
                            <td class="p-3">
                                <form action="{{ route('admin.phan-quyen.override') }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="nguoi_dung_id" value="{{ $u->id }}">
                                    
                                    <select name="quyen_han_id" class="px-2 py-1 text-xs border border-slate-300 rounded-lg outline-none">
                                        @foreach($permissions as $perm)
                                            <option value="{{ $perm->id }}">{{ $perm->ten }}</option>
                                        @endforeach
                                    </select>

                                    <select name="cho_phep" class="px-2 py-1 text-xs border border-slate-300 rounded-lg outline-none font-bold">
                                        <option value="1" class="text-emerald-600 font-bold">Cho phép (+)</option>
                                        <option value="0" class="text-rose-600 font-bold">Từ chối (-)</option>
                                        <option value="-1" class="text-slate-500">Bỏ override</option>
                                    </select>

                                    <button type="submit" class="px-3 py-1 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg text-xs transition-colors">
                                        Lưu
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
