@extends('admin.layout')

@section('title', 'Quản Lý Người Dùng - Quán Mới Admin')
@section('page-title', 'Quản Lý Người Dùng & Vai Trò')

@section('content')
<div class="space-y-6">
    {{-- Search & Filters --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.nguoi-dung.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-72">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tên, email, sdt..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
            </div>

            <select name="role" class="px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                <option value="">-- Tất cả vai trò --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->ten }}</option>
                @endforeach
            </select>

            <select name="status" class="px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="hoat_dong" {{ request('status') == 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
                <option value="bi_khoa" {{ request('status') == 'bi_khoa' ? 'selected' : '' }}>Bị khóa</option>
                <option value="soft_deleted" {{ request('status') == 'soft_deleted' ? 'selected' : '' }}>Đã xóa (Soft-delete)</option>
            </select>

            <button type="submit" class="bg-primary text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-primary/90 transition-all flex items-center gap-1">
                <span class="material-symbols-outlined text-lg">filter_alt</span> Lọc
            </button>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase">
                        <th class="py-3.5 px-4">Người Dùng</th>
                        <th class="py-3.5 px-4">Email & SĐT</th>
                        <th class="py-3.5 px-4">Vai Trò</th>
                        <th class="py-3.5 px-4">Trạng Thái</th>
                        <th class="py-3.5 px-4 text-center">Quán</th>
                        <th class="py-3.5 px-4 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/80 transition-colors {{ $user->trashed() ? 'bg-red-50/30' : '' }}">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->anh_dai_dien ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&q=80' }}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="" />
                                    <div>
                                        <p class="font-bold text-gray-900 flex items-center gap-1">
                                            {{ $user->ho_ten }}
                                            @if($user->isAdmin())
                                                <span class="material-symbols-outlined text-amber-500 text-sm" title="Admin">verified</span>
                                            @endif
                                        </p>
                                        <span class="text-xs text-gray-400">ID: {{ substr($user->id, 0, 8) }}...</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-medium text-gray-800">{{ $user->email }}</p>
                                <p class="text-xs text-gray-400">{{ $user->so_dien_thoai ?: 'Chưa cập nhật SĐT' }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $user->isAdmin() ? 'bg-amber-100 text-amber-800' : ($user->vaiTro?->ten === 'chu_quan' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $user->vaiTro?->ten ?: ($user->isAdmin() ? 'admin' : 'nguoi_dung') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->trashed())
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Đã xóa (Soft Deleted)</span>
                                @elseif($user->trang_thai === 'bi_khoa')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-700">Bị khóa</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Hoạt động</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-bold text-gray-700">{{ $user->quan->count() }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.nguoi-dung.edit', $user->id) }}" class="p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-all" title="Chỉnh sửa">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>

                                    @if($user->trashed())
                                        <form action="{{ route('admin.nguoi-dung.restore', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Khôi phục">
                                                <span class="material-symbols-outlined text-lg">restore_from_trash</span>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.nguoi-dung.force-destroy', $user->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Xóa cứng sẽ xóa vĩnh viễn người dùng này và tất cả các quán của họ khỏi cơ sở dữ liệu. Không thể khôi phục. Bạn có chắc chắn?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-700 hover:bg-red-100 rounded-lg transition-all" title="Xóa cứng (Vĩnh viễn)">
                                                <span class="material-symbols-outlined text-lg">delete_forever</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.nguoi-dung.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tạm người dùng này? Các quán của họ cũng sẽ tạm khóa.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Xóa tạm (Soft-delete)">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Không tìm thấy người dùng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
