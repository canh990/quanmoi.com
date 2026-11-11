@extends(request()->ajax() ? 'admin.ajax_layout' : 'admin.layout')

@section('title', 'Quản Lý Người Dùng - Quán Mới Admin')
@section('page-title', 'Quản Lý Người Dùng & Vai Trò')

@section('content')
<div class="space-y-6">
    {{-- Search & Filters --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.nguoi-dung.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="admin-search-wrapper w-full md:w-72 shadow-2xs">
                <span class="material-symbols-outlined text-[18px] text-slate-400">search</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tên, email, sdt..." class="w-full bg-transparent border-0 p-0 text-xs font-medium outline-none text-slate-800 placeholder:text-slate-400">
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

            <button type="submit" class="bg-primary text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-primary/90 transition-all flex items-center gap-1 cursor-pointer">
                <span class="material-symbols-outlined text-lg">filter_alt</span> Lọc
            </button>
        </form>

        <div class="flex items-center gap-2 shrink-0 w-full md:w-auto justify-end">
            <button type="button" onclick="openCreateUserModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Thêm Người Dùng</span>
            </button>
        </div>
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
            if (typeof showCenterRealtimeAlert === 'function') {
                showCenterRealtimeAlert('Cập Nhật Xác Thực Người Dùng', data.message || 'Trạng thái xác thực đã được đồng bộ vào CSDL.', 'verified', 'info');
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
                    window.__adminLastActionAt = Date.now();
                    const displayName = data.user_name || userName || 'Người dùng';
                    if (typeof showCenterRealtimeAlert === 'function') {
                        showCenterRealtimeAlert('Đã Xóa Người Dùng', `Người dùng <b class="text-slate-900 font-bold">"${displayName}"</b> đã bị xóa.`, 'person_remove', 'danger');
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
                    window.__adminLastActionAt = Date.now();
                    const displayName = data.user_name || userName || 'Người dùng';
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Khôi Phục Thành Công', data.message || `Đã khôi phục tài khoản "${displayName}" thành công!`, '', 'check_circle', 'bg-emerald-500');
                    }
                    if (typeof showCenterRealtimeAlert === 'function') {
                        showCenterRealtimeAlert('Đã Khôi Phục Tài Khoản Vào CSDL', data.message || `Tài khoản <b class="text-slate-900 font-bold">"${displayName}"</b> đã được khôi phục thành công.`, 'how_to_reg', 'success');
                    }
                    clearAdminPageCache();
                    instantNavigate(window.location.href, false, true);
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
                    window.__adminLastActionAt = Date.now();
                    const displayName = data.user_name || userName || 'Người dùng';
                    if (typeof showCenterRealtimeAlert === 'function') {
                        showCenterRealtimeAlert('Đã Xóa Người Dùng', `Người dùng <b class="text-slate-900 font-bold">"${displayName}"</b> đã bị xóa.`, 'delete_forever', 'danger');
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
                    if (typeof showCenterRealtimeAlert === 'function') {
                        showCenterRealtimeAlert(data.is_locked ? 'Đã Khóa Tài Khoản Trong CSDL' : 'Đã Mở Khóa Tài Khoản Trong CSDL', data.message, data.is_locked ? 'lock' : 'lock_open', data.is_locked ? 'warning' : 'success');
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

    // Engine Tạo Người Dùng Mới & Đồng Bộ CSDL Realtime
    function openCreateUserModal() {
        document.getElementById('admin-create-user-modal')?.classList.remove('hidden');
        setTimeout(() => document.getElementById('new-user-hoten')?.focus(), 50);
    }

    function closeCreateUserModal() {
        document.getElementById('admin-create-user-modal')?.classList.add('hidden');
        document.getElementById('create-user-error')?.classList.add('hidden');
        document.getElementById('create-user-form')?.reset();
    }

    function adminSubmitCreateUser(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-submit-create-user');
        const errBox = document.getElementById('create-user-error');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const payload = {
            ho_ten: document.getElementById('new-user-hoten').value.trim(),
            email: document.getElementById('new-user-email').value.trim(),
            mat_khau: document.getElementById('new-user-password').value,
            so_dien_thoai: document.getElementById('new-user-sdt').value.trim(),
            vai_tro_id: document.getElementById('new-user-role').value || null,
            trang_thai: document.getElementById('new-user-status').value,
            da_xac_thuc: document.getElementById('new-user-verified').checked ? 1 : 0
        };

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">refresh</span> Đang lưu CSDL...';
        if (errBox) errBox.classList.add('hidden');

        fetch('{{ route("admin.nguoi-dung.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : `Lỗi HTTP ${res.status}`);
                throw new Error(errorMsg);
            }
            return data;
        })
        .then(data => {
            closeCreateUserModal();
            if (typeof showCenterRealtimeAlert === 'function') {
                showCenterRealtimeAlert('Đã Thêm Người Dùng Vào CSDL', data.message || `Tài khoản "${payload.ho_ten}" đã được lưu vào database!`, 'person_add', 'success');
            } else if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Tạo Thành Công', data.message, '', 'check_circle', 'bg-emerald-600');
            }
            clearAdminPageCache();
            if (typeof instantNavigate === 'function') {
                instantNavigate(window.location.href, false, true);
            }
        })
        .catch(err => {
            if (errBox) {
                errBox.textContent = err.message || 'Có lỗi xảy ra khi tạo người dùng.';
                errBox.classList.remove('hidden');
            } else {
                alert(err.message || 'Lỗi khi tạo người dùng.');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-[16px]">check_circle</span><span>Tạo & Lưu CSDL</span>';
        });
    }
</script>

{{-- Modal Thêm Người Dùng Mới --}}
<div id="admin-create-user-modal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden" role="dialog" aria-modal="true">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-lg w-full p-6 text-left relative overflow-hidden transform transition-all">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[22px]">person_add</span>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Thêm Người Dùng Mới</h3>
                    <p class="text-xs text-slate-500">Tạo tài khoản và đồng bộ ngay lập tức vào cơ sở dữ liệu</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateUserModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form id="create-user-form" onsubmit="adminSubmitCreateUser(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Họ và Tên <span class="text-rose-500">*</span></label>
                <input type="text" id="new-user-hoten" required placeholder="Ví dụ: Nguyễn Văn A" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Địa Chỉ Email <span class="text-rose-500">*</span></label>
                <input type="email" id="new-user-email" required placeholder="user@quanmoi.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mật Khẩu <span class="text-rose-500">*</span></label>
                    <input type="password" id="new-user-password" required minlength="6" placeholder="Tối thiểu 6 ký tự" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Số Điện Thoại</label>
                    <input type="text" id="new-user-sdt" placeholder="0901234567" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Vai Trò Hệ Thống</label>
                    <select id="new-user-role" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500 outline-none bg-white">
                        <option value="">Thành viên</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->ten }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Trạng Thái</label>
                    <select id="new-user-status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500 outline-none bg-white">
                        <option value="hoat_dong">Hoạt động</option>
                        <option value="bi_khoa">Bị khóa</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="new-user-verified" value="1" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                <label for="new-user-verified" class="text-xs font-semibold text-slate-700 cursor-pointer">Cấp Tick Xanh xác thực ngay</label>
            </div>

            <div id="create-user-error" class="hidden text-xs text-rose-600 bg-rose-50 border border-rose-200 p-2.5 rounded-xl"></div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeCreateUserModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold transition-colors">
                    Hủy bỏ
                </button>
                <button type="submit" id="btn-submit-create-user" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    <span>Tạo & Lưu CSDL</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
