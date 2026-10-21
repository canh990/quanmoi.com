@extends('admin.layout')

@section('title', 'Phân Quyền - Quán Mới Admin')
@section('page-title', 'Phân Quyền & Quyền Hạn Overrides')

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
            <a href="{{ route('admin.vai-tro.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5 shadow-md shadow-blue-600/30">
                <span class="material-symbols-outlined text-[18px] text-white">add</span>
                <span class="text-white">Tạo vai trò mới</span>
            </a>
        </div>
    </div>

    {{-- Report-matching Tabs (Vai trò vs Quyền hạn) --}}
    <div class="flex items-center gap-6 border-b border-slate-200 text-xs font-bold">
        <a href="{{ route('admin.vai-tro.index') }}" class="pb-3 px-1 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
            <span>Vai trò</span>
        </a>
        <a href="{{ route('admin.phan-quyen.index') }}" class="pb-3 px-1 border-b-2 border-indigo-600 text-indigo-600 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">security</span>
            <span>Quyền hạn & Overrides ({{ $permissions->count() }})</span>
        </a>
    </div>
    
    {{-- Section 1: Add New Permission --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Thêm Quyền Hạn Mới Vào Hệ Thống</h3>
        <p class="text-xs text-slate-500 mb-4">Các quyền hạn này có thể gán cho Role hoặc override trực tiếp theo từng User.</p>

        <form action="{{ route('admin.phan-quyen.store') }}" method="POST" id="form-create-permission" onsubmit="adminSubmitCreatePermission(event, this)" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <button type="submit" class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">add_task</span>
                    <span>Thêm Quyền Hạn</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Section 2: List of Permissions --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Danh Sách Quyền Hạn Hợp Lệ</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3" id="permissions-grid-container">
            @foreach($permissions as $perm)
                <div id="perm-card-{{ $perm->id }}" class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200/60 inline-block">
                                {{ $perm->ten }}
                            </span>
                            @if(($perm->vai_tro_count ?? 0) === 0)
                                <button type="button" onclick="adminDeletePermission('{{ $perm->id }}', '{{ $perm->ten }}', this)" class="text-slate-400 hover:text-rose-600 transition-colors p-1 cursor-pointer" title="Xóa quyền hạn chưa dùng">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            @else
                                <span class="text-[10px] text-slate-400 font-medium">{{ $perm->vai_tro_count }} vai trò</span>
                            @endif
                        </div>
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

        {{-- Search & Filter Toolbar --}}
        <form method="GET" action="{{ route('admin.phan-quyen.index') }}" class="mb-4 flex flex-col sm:flex-row items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200">
            <div class="admin-search-wrapper flex-1 w-full shadow-2xs">
                <span class="material-symbols-outlined text-slate-400 text-[18px]">search</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm người dùng theo họ tên, email hoặc số điện thoại..." class="w-full bg-transparent border-0 p-0 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0 font-medium leading-normal">
            </div>

            <div class="w-full sm:w-56">
                <select name="role" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Tất cả vai trò --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ request('role') == $r->id ? 'selected' : '' }}>{{ $r->ten }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    <span>Lọc danh sách</span>
                </button>
                @if(request()->hasAny(['q', 'role']))
                    <a href="{{ route('admin.phan-quyen.index') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-lg transition-colors flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span>Đặt lại</span>
                    </a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="p-3">Người Dùng</th>
                        <th class="p-3">Role Gán Mặc Định</th>
                        <th class="p-3">Cấu Hình Override Hiện Tại</th>
                        <th class="p-3">Thao Tác Override</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        <tr id="user-override-row-{{ $u->id }}" class="hover:bg-slate-50 transition-colors">
                            <td class="p-3 font-medium">
                                <div class="font-bold text-slate-900">{{ $u->ho_ten }}</div>
                                <div class="text-[11px] text-slate-500">{{ $u->email }}</div>
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                    {{ $u->ten_vai_tro_hien_thi }}
                                </span>
                            </td>
                            <td class="p-3" id="user-overrides-cell-{{ $u->id }}">
                                @forelse($u->quyenHanOverrides as $ov)
                                    <span data-perm="{{ $ov->ten }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold border mb-1 {{ $ov->pivot->cho_phep ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                        {{ $ov->ten }}: {{ $ov->pivot->cho_phep ? 'Cho phép' : 'Từ chối' }}
                                    </span>
                                @empty
                                    <span class="text-[11px] text-slate-400 italic">Dùng mặc định theo Role</span>
                                @endforelse
                            </td>
                            <td class="p-3">
                                <form action="{{ route('admin.phan-quyen.override') }}" method="POST" onsubmit="adminSubmitUserOverride(event, this, '{{ $u->id }}')" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="nguoi_dung_id" value="{{ $u->id }}">
                                    
                                    <select name="quyen_han_id" class="px-2 py-1 text-xs border border-slate-300 rounded-lg outline-none bg-white">
                                        @foreach($permissions as $perm)
                                            <option value="{{ $perm->id }}">{{ $perm->ten }}</option>
                                        @endforeach
                                    </select>

                                    <select name="cho_phep" class="px-2 py-1 text-xs border border-slate-300 rounded-lg outline-none font-bold bg-white">
                                        <option value="1" class="text-emerald-600 font-bold">Cho phép (+)</option>
                                        <option value="0" class="text-rose-600 font-bold">Từ chối (-)</option>
                                        <option value="-1" class="text-slate-500">Bỏ override</option>
                                    </select>

                                    <button type="submit" class="px-3 py-1 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg text-xs transition-colors flex items-center gap-1 cursor-pointer">
                                        <span>Lưu</span>
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

<script>
    function adminSubmitCreatePermission(e, form) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span><span>Đang thêm...</span>';
        }

        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
            return data;
        })
        .then(data => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Thành Công', data.message || 'Thêm quyền hạn thành công!', '', 'check_circle', 'bg-emerald-500');
            }
            form.reset();
            clearAdminPageCache();
            instantNavigate(window.location.href, false);
        })
        .catch(err => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lỗi Thêm Quyền', err.message || 'Không thể tạo quyền hạn mới.', '', 'error', 'bg-rose-500');
            }
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    function adminDeletePermission(permId, permName, triggerBtn) {
        openConfirmModal({
            title: 'Xóa Quyền Hạn',
            message: `Bạn có chắc chắn muốn xóa quyền hạn <b class="text-slate-900 font-bold">"${permName}"</b> khỏi hệ thống không?<br><span class="text-rose-600 text-[11px] font-medium">Hành động này sẽ xóa hoàn toàn quyền hạn khỏi cơ sở dữ liệu.</span>`,
            icon: 'delete',
            iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
            confirmText: 'Xác Nhận Xóa',
            confirmClass: 'btn-enterprise-danger',
            confirmIcon: 'delete',
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const card = document.getElementById(`perm-card-${permId}`);

                fetch(`/admin/phan-quyen/${permId}`, {
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
                        showRealtimeToast('Đã Xóa Quyền Hạn', data.message || 'Đã xóa quyền hạn thành công.', '', 'delete', 'bg-slate-700');
                    }
                    clearAdminPageCache();
                    if (card) {
                        card.style.transition = 'all 0.35s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => card.remove(), 350);
                    }
                })
                .catch(err => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Không Thể Xóa', err.message || 'Có lỗi xảy ra khi xóa quyền hạn.', '', 'error', 'bg-rose-500');
                    }
                });
            }
        });
    }

    function adminSubmitUserOverride(e, form, userId) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">progress_activity</span>';
        }

        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
            return data;
        })
        .then(data => {
            if (typeof showRealtimeToast === 'function') {
                const toastIcon = data.action === '1' ? 'verified' : (data.action === '0' ? 'block' : 'restart_alt');
                const toastBg = data.action === '1' ? 'bg-emerald-500' : (data.action === '0' ? 'bg-rose-500' : 'bg-slate-600');
                showRealtimeToast('Cập Nhật Phân Quyền', data.message || 'Cập nhật override thành công!', '', toastIcon, toastBg);
            }
            clearAdminPageCache();

            // Dynamic live update of user overrides cell
            const cell = document.getElementById(`user-overrides-cell-${userId}`);
            if (cell && data.permission_name) {
                const permName = data.permission_name;
                const existingBadge = cell.querySelector(`[data-perm="${permName}"]`);

                if (data.action === '-1') {
                    if (existingBadge) existingBadge.remove();
                    if (!cell.querySelector('[data-perm]')) {
                        cell.innerHTML = '<span class="text-[11px] text-slate-400 italic">Dùng mặc định theo Role</span>';
                    }
                } else {
                    const isAllowed = data.action === '1';
                    const labelText = isAllowed ? `${permName}: Cho phép` : `${permName}: Từ chối`;
                    const badgeClass = isAllowed
                        ? 'inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold border mb-1 mr-1 bg-emerald-50 text-emerald-700 border-emerald-200'
                        : 'inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold border mb-1 mr-1 bg-rose-50 text-rose-700 border-rose-200';

                    // Remove "Dùng mặc định" placeholder if present
                    const defaultSpan = cell.querySelector('.italic');
                    if (defaultSpan) defaultSpan.remove();

                    if (existingBadge) {
                        existingBadge.className = badgeClass;
                        existingBadge.textContent = labelText;
                    } else {
                        const newBadge = document.createElement('span');
                        newBadge.setAttribute('data-perm', permName);
                        newBadge.className = badgeClass;
                        newBadge.textContent = labelText;
                        cell.appendChild(newBadge);
                    }
                }
            }
        })
        .catch(err => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lỗi Phân Quyền', err.message || 'Không thể cập nhật quyền cho người dùng.', '', 'error', 'bg-rose-500');
            }
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }
</script>
@endsection
