@extends(request()->ajax() ? 'admin.ajax_layout' : 'admin.layout')

@section('title', 'Nhật Ký Hệ Thống - Quán Mới Admin')
@section('page-title', 'Nhật Ký Tác Vụ Quản Trị')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
            <span class="material-symbols-outlined text-slate-700">history</span>
            Lịch Sử Thao Tác Quản Trị Hệ Thống
        </h3>
        <p class="text-xs text-slate-500 mb-4">Theo dõi mọi thay đổi dữ liệu, hành động cập nhật, khóa/xóa tài khoản hoặc duyệt thông tin.</p>

        {{-- Search Bar with Elasticsearch --}}
        <div class="mb-5">
            <form method="GET" action="{{ route('admin.audit-log.index') }}" class="flex flex-wrap items-center gap-2.5">
                <div class="admin-search-wrapper flex-1 min-w-[240px] max-w-md shadow-2xs">
                    <span class="material-symbols-outlined text-[16px] text-slate-400">search</span>
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Tìm người thực hiện, hành động, ID đối tượng, IP..." class="w-full bg-transparent border-0 p-0 text-xs font-medium outline-none text-slate-800 placeholder:text-slate-400">
                </div>
                <button type="submit" class="btn-action-primary-glow">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Tìm kiếm</span>
                </button>
                @if(!empty($q))
                    <a href="{{ route('admin.audit-log.index') }}" class="btn-action-secondary" title="Xóa tìm kiếm">
                        <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                        <span>Xóa lọc</span>
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="p-3">Thời Gian</th>
                        <th class="p-3">Người Thực Hiện</th>
                        <th class="p-3">Hành Động</th>
                        <th class="p-3">Đối Tượng</th>
                        <th class="p-3">Địa Chỉ IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-mono text-[11px] text-slate-500">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : 'N/A' }}</td>
                            <td class="p-3 font-bold text-slate-900">{{ $log->admin->ho_ten ?? 'Hệ Thống Admin' }}</td>
                            <td class="p-3 font-medium">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    {{ $log->hanh_dong ?? 'Hệ thống' }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-600">{{ $log->doi_tuong_loai ?? '' }} #{{ Str::limit($log->doi_tuong_id ?? '', 8) }}</td>
                            <td class="p-3 font-mono text-[11px] text-slate-500">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-xs text-slate-500">Chưa ghi nhận nhật ký tác vụ nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
