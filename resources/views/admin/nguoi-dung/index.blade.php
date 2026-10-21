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
                <option value="soft_deleted" {{ request('status') == 'soft_deleted' ? 'selected' : '' }}>Đã xóa tạm thời</option>
            </select>

            <select name="da_xac_thuc" class="px-3 py-2 rounded-xl border border-gray-200 text-sm outline-none focus:border-primary bg-white">
                <option value="">-- Xác thực Tick Xanh --</option>
                <option value="1" {{ request('da_xac_thuc') == '1' ? 'selected' : '' }}>Có Tick Xanh</option>
                <option value="0" {{ request('da_xac_thuc') == '0' ? 'selected' : '' }}>Chưa có Tick Xanh</option>
            </select>

            <button type="submit" class="bg-primary text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-primary/90 transition-all flex items-center gap-1">
                <span class="material-symbols-outlined text-lg">filter_alt</span> Lọc
            </button>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="w-full overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[800px] table-fixed-enterprise">
                <colgroup>
                    <col class="w-[28%]">
                    <col class="w-[24%]">
                    <col class="w-[14%]">
                    <col class="w-[12%]">
                    <col class="w-[8%]">
                    <col class="w-[14%]">
                </colgroup>
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase">
                        <th class="py-3 px-3.5">Người Dùng</th>
                        <th class="py-3 px-3.5">Email & SĐT</th>
                        <th class="py-3 px-3.5">Vai Trò</th>
                        <th class="py-3 px-3.5">Trạng Thái</th>
                        <th class="py-3 px-3.5 text-center">Quán</th>
                        <th class="py-3 px-3.5 text-right whitespace-nowrap">Thao Tác</th>
                    </tr>
                </thead>
                <tbody id="users-tbody" class="divide-y divide-gray-100 text-sm">
                    @forelse($users as $user)
                        <tr id="user-row-{{ $user->id }}" class="hover:bg-gray-50/80 transition-colors {{ $user->trashed() ? 'bg-red-50/30' : '' }}">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img src="{{ $user->anh_dai_dien ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&q=80' }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&q=80';" class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0" alt="{{ $user->ho_ten }}" />
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-gray-900 flex items-center gap-1 cell-wrap-2line">
                                             {{ $user->ho_ten }}
                                            @if($user->isAdmin())
                                                <span class="material-symbols-outlined text-amber-500 text-sm shrink-0" title="Quản trị viên">shield</span>
                                            @endif
                                            @if($user->da_xac_thuc)
                                                <span class="material-symbols-outlined text-blue-500 text-[16px] shrink-0" title="Có Tick Xanh" style="font-variation-settings: 'FILL' 1;">verified</span>
                                            @endif
                                        </p>
                                        <span class="text-xs text-gray-400">ID: {{ substr($user->id, 0, 8) }}...</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-medium text-gray-800 cell-wrap-1line">{{ $user->email }}</p>
                                <p class="text-xs text-gray-400">{{ $user->so_dien_thoai ?: 'Chưa cập nhật SĐT' }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $user->isAdmin() ? 'bg-amber-100 text-amber-800' : ($user->vaiTro?->ten === 'chu_quan' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $user->ten_vai_tro_hien_thi }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4" id="user-status-cell-{{ $user->id }}">
                                @if($user->trashed())
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Đã xóa tạm thời</span>
                                @elseif($user->trang_thai === 'bi_khoa')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Bị khóa</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Hoạt động</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-bold text-gray-700">{{ $user->quan->count() }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap min-w-[160px]">
                                <div class="inline-flex items-center justify-end gap-1 flex-nowrap shrink-0">
                                    @if(!$user->trashed())
                                        <button type="button" 
                                                id="btn-lock-user-{{ $user->id }}"
                                                onclick="adminToggleUserLock('{{ $user->id }}', '{{ addslashes($user->ho_ten) }}', {{ $user->trang_thai === 'bi_khoa' ? 'true' : 'false' }})" 
                                                class="w-8 h-8 flex items-center justify-center rounded-lg transition-all cursor-pointer shrink-0 {{ $user->trang_thai === 'bi_khoa' ? 'text-amber-600 hover:bg-amber-50' : 'text-gray-400 hover:text-amber-600 hover:bg-gray-100' }}" 
                                                title="{{ $user->trang_thai === 'bi_khoa' ? 'Mở khóa tài khoản' : 'Khóa tài khoản' }}">
                                            <span class="material-symbols-outlined text-lg">{{ $user->trang_thai === 'bi_khoa' ? 'lock' : 'lock_open' }}</span>
                                        </button>

                                        <button type="button" onclick="adminToggleUserVerification('{{ $user->id }}', this)" class="w-8 h-8 flex items-center justify-center rounded-lg transition-all cursor-pointer shrink-0 {{ $user->da_xac_thuc ? 'text-blue-500 hover:bg-blue-50' : 'text-gray-400 hover:text-blue-500 hover:bg-gray-100' }}" title="{{ $user->da_xac_thuc ? 'Gỡ Tick Xanh' : 'Cấp Tick Xanh' }}">
                                            <span class="material-symbols-outlined text-lg" {!! $user->da_xac_thuc ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>verified</span>
                                        </button>
                                    @endif

                                    <a href="{{ route('admin.nguoi-dung.edit', $user->id) }}" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-all shrink-0" title="Chỉnh sửa">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>

                                    @if($user->trashed())
                                        <button type="button" onclick="adminRestoreUser('{{ $user->id }}', '{{ addslashes($user->ho_ten) }}')" class="w-8 h-8 flex items-center justify-center text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all cursor-pointer shrink-0" title="Khôi phục">
                                            <span class="material-symbols-outlined text-lg">restore_from_trash</span>
                                        </button>
                                        <button type="button" onclick="adminForceDeleteUser('{{ $user->id }}', '{{ addslashes($user->ho_ten) }}')" class="w-8 h-8 flex items-center justify-center text-red-700 hover:bg-red-100 rounded-lg transition-all cursor-pointer shrink-0" title="Xóa vĩnh viễn">
                                            <span class="material-symbols-outlined text-lg">delete_forever</span>
                                        </button>
                                    @else
                                        <button type="button" onclick="adminSoftDeleteUser('{{ $user->id }}', '{{ addslashes($user->ho_ten) }}')" class="w-8 h-8 flex items-center justify-center text-red-600 hover:bg-red-50 rounded-lg transition-all cursor-pointer shrink-0" title="Xóa tạm thời">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Không tìm thấy người dùng nào phù hợp với từ khóa tìm kiếm hoặc bộ lọc.</td>
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

<script>
    function adminToggleUserVerification(userId, btn) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const iconSpan = btn.querySelector('.material-symbols-outlined');

        fetch(`/admin/nguoi-dung/${userId}/toggle-xac-thuc`, {
            method: 'PUT',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            }
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
            return data;
        })
        .then(data => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Xác Thực Người Dùng', data.message, '', 'verified', 'bg-blue-500');
            }
            clearAdminPageCache();

            const isVerified = data.verified;
            if (isVerified) {
                btn.className = 'p-2 rounded-lg transition-all cursor-pointer text-blue-500 hover:bg-blue-50';
                btn.title = 'Gỡ Tick Xanh';
                if (iconSpan) iconSpan.style.fontVariationSettings = "'FILL' 1";
            } else {
                btn.className = 'p-2 rounded-lg transition-all cursor-pointer text-gray-400 hover:text-blue-500 hover:bg-gray-100';
                btn.title = 'Cấp Tick Xanh';
                if (iconSpan) iconSpan.style.fontVariationSettings = "'FILL' 0";
            }
        })
        .catch(err => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lỗi Xác Thực', err.message || 'Không thể thay đổi trạng thái xác thực.', '', 'error', 'bg-rose-500');
            }
        });
    }

    function adminSoftDeleteUser(userId, userName) {
        openConfirmModal({
            title: 'Xóa Tạm Thời Người Dùng',
            message: `Bạn có chắc chắn muốn xóa tạm thời người dùng <b class="text-slate-900 font-bold">"${userName}"</b> không?<br><span class="text-rose-600 text-[11px] font-medium">Tất cả quán trực thuộc người dùng này cũng sẽ bị khóa tạm thời.</span>`,
            icon: 'delete',
            iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
            confirmText: 'Xác Nhận Xóa',
            confirmClass: 'btn-enterprise-danger',
            confirmIcon: 'delete',
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const row = document.getElementById(`user-row-${userId}`);

                fetch(`/admin/nguoi-dung/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    }
                })
                .then(async res => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
                    return data;
                })
                .then(data => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Đã Xóa Người Dùng', data.message || 'Đã xóa người dùng thành công.', '', 'delete', 'bg-slate-700');
                    }
                    clearAdminPageCache();
                    if (row) {
                        row.style.transition = 'all 0.35s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => row.remove(), 350);
                    }
                })
                .catch(err => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Không Thể Xóa', err.message || 'Có lỗi xảy ra khi xóa người dùng.', '', 'error', 'bg-rose-500');
                    }
                });
            }
        });
    }

    function adminRestoreUser(userId, userName) {
        openConfirmModal({
            title: 'Khôi Phục Tài Khoản',
            message: `Bạn có chắc chắn muốn khôi phục tài khoản người dùng <b class="text-slate-900 font-bold">"${userName}"</b> không?<br><span class="text-emerald-600 text-[11px] font-medium">Các quán trực thuộc cũng sẽ được khôi phục về trạng thái hoạt động.</span>`,
            icon: 'restore_from_trash',
            iconBg: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            confirmText: 'Khôi Phục Ngay',
            confirmClass: 'btn-enterprise-success',
            confirmIcon: 'check_circle',
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                fetch(`/admin/nguoi-dung/${userId}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    }
                })
                .then(async res => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
                    return data;
                })
                .then(data => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Khôi Phục Thành Công', data.message || 'Khôi phục thành công!', '', 'check_circle', 'bg-emerald-500');
                    }
                    clearAdminPageCache();
                    instantNavigate(window.location.href, false);
                })
                .catch(err => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Lỗi Khôi Phục', err.message || 'Không thể khôi phục tài khoản.', '', 'error', 'bg-rose-500');
                    }
                });
            }
        });
    }

    function adminForceDeleteUser(userId, userName) {
        openConfirmModal({
            title: 'XÓA VĨNH VIỄN NGƯỜI DÙNG',
            message: `CẢNH BÁO: Thao tác này sẽ xóa vĩnh viễn người dùng <b class="text-slate-900 font-bold">"${userName}"</b> và toàn bộ cơ sở dữ liệu liên quan. Không thể hoàn tác!<br><span class="text-rose-600 text-[11px] font-bold">Bạn có chắc chắn muốn tiếp tục không?</span>`,
            icon: 'delete_forever',
            iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
            confirmText: 'Xác Nhận Xóa Vĩnh Viễn',
            confirmClass: 'btn-enterprise-danger',
            confirmIcon: 'delete_forever',
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const row = document.getElementById(`user-row-${userId}`);

                fetch(`/admin/nguoi-dung/${userId}/force`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    }
                })
                .then(async res => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
                    return data;
                })
                .then(data => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Đã Xóa Vĩnh Viễn', data.message || 'Đã xóa vĩnh viễn người dùng.', '', 'delete_forever', 'bg-rose-700');
                    }
                    clearAdminPageCache();
                    if (row) {
                        row.style.transition = 'all 0.35s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => row.remove(), 350);
                    }
                })
                .catch(err => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Không Thể Xóa', err.message || 'Có lỗi xảy ra khi xóa vĩnh viễn.', '', 'error', 'bg-rose-500');
                    }
                });
            }
        });
    }

    function adminToggleUserLock(userId, userName, isCurrentlyLocked) {
        const actionTitle = isCurrentlyLocked ? 'Mở Khóa Tài Khoản' : 'Khóa Tài Khoản';
        const actionMessage = isCurrentlyLocked
            ? `Bạn có chắc muốn mở khóa cho tài khoản <b class="text-slate-900 font-bold">"${userName}"</b>? Người dùng này sẽ có thể đăng nhập lại hệ thống.`
            : `Bạn có chắc muốn khóa tài khoản <b class="text-slate-900 font-bold">"${userName}"</b>?<br><span class="text-amber-600 text-[11px] font-medium">Người dùng sẽ bị ngắt phiên đăng nhập ngay lập tức và không thể tiếp tục sử dụng hệ thống cho đến khi được mở khóa.</span>`;
        const actionBtnText = isCurrentlyLocked ? 'Xác Nhận Mở Khóa' : 'Xác Nhận Khóa';
        const actionIcon = isCurrentlyLocked ? 'lock_open' : 'lock';
        const actionIconBg = isCurrentlyLocked ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200';
        const confirmClass = isCurrentlyLocked ? 'bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-xs' : 'bg-amber-600 hover:bg-amber-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-xs';

        openConfirmModal({
            title: actionTitle,
            message: actionMessage,
            icon: actionIcon,
            iconBg: actionIconBg,
            confirmText: actionBtnText,
            confirmClass: confirmClass,
            confirmIcon: actionIcon,
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                fetch(`/admin/nguoi-dung/${userId}/toggle-khoa`, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    }
                })
                .then(async res => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
                    return data;
                })
                .then(data => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Trạng Thái Tài Khoản', data.message, '', data.is_locked ? 'lock' : 'lock_open', data.is_locked ? 'bg-amber-600' : 'bg-emerald-600');
                    }
                    clearAdminPageCache();

                    const statusCell = document.getElementById(`user-status-cell-${userId}`);
                    if (statusCell) {
                        statusCell.innerHTML = data.is_locked
                            ? '<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Bị khóa</span>'
                            : '<span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Hoạt động</span>';
                    }

                    const btn = document.getElementById(`btn-lock-user-${userId}`);
                    if (btn) {
                        if (data.is_locked) {
                            btn.className = 'w-8 h-8 flex items-center justify-center rounded-lg transition-all cursor-pointer shrink-0 text-amber-600 hover:bg-amber-50';
                            btn.title = 'Mở khóa tài khoản';
                            btn.innerHTML = '<span class="material-symbols-outlined text-lg">lock</span>';
                            btn.setAttribute('onclick', `adminToggleUserLock('${userId}', '${userName}', true)`);
                        } else {
                            btn.className = 'w-8 h-8 flex items-center justify-center rounded-lg transition-all cursor-pointer shrink-0 text-gray-400 hover:text-amber-600 hover:bg-gray-100';
                            btn.title = 'Khóa tài khoản';
                            btn.innerHTML = '<span class="material-symbols-outlined text-lg">lock_open</span>';
                            btn.setAttribute('onclick', `adminToggleUserLock('${userId}', '${userName}', false)`);
                        }
                    }
                })
                .catch(err => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Lỗi Khóa Tài Khoản', err.message || 'Không thể thay đổi trạng thái tài khoản.', '', 'error', 'bg-rose-500');
                    }
                });
            }
        });
    }
</script>
@endsection
