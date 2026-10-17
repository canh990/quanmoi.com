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
    <div id="tao-vai-tro-modal" class="hidden bg-blue-50/60 rounded-2xl border border-blue-200 p-5 shadow-xs transition-all">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-blue-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600 text-[20px]">add_moderator</span>
                Tạo Vai Trò Mới Cho Hệ Thống
            </h3>
            <button type="button" onclick="toggleTaoVaiTroModal()" class="text-blue-400 hover:text-blue-700 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form action="{{ route('admin.vai-tro.store') }}" method="POST" id="form-create-role" onsubmit="adminSubmitCreateRole(event, this)" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @csrf
            <input type="text" name="ten" placeholder="Nhập mã vai trò mới (ví dụ: bien_tap_vien)..." required class="flex-1 px-4 py-2.5 text-xs bg-white border border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Xác nhận tạo vai trò</span>
            </button>
        </form>
    </div>

    {{-- Roles List Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" id="roles-grid-container">
        @foreach($roles as $role)
            <div id="role-card-{{ $role->id }}" class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full uppercase border border-indigo-200/60">
                            {{ $role->ten }}
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-500">
                                {{ $role->nguoi_dung_count }} người dùng
                            </span>
                            @if(!in_array(strtolower($role->ten), ['admin', 'chu_quan', 'nguoi_dung']) && $role->nguoi_dung_count == 0)
                                <button type="button" onclick="adminDeleteCustomRole('{{ $role->id }}', '{{ $role->ten }}', this)" class="text-slate-400 hover:text-rose-600 transition-colors p-0.5 cursor-pointer" title="Xóa vai trò tùy chỉnh">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <h4 class="text-base font-bold text-slate-900 mt-3 capitalize">
                        Role: {{ str_replace('_', ' ', $role->ten) }}
                    </h4>
                    <p class="text-xs text-slate-500 mt-1">Gồm {{ $role->quyen_han_count }} quyền hạn mặc định.</p>

                    <form action="{{ route('admin.vai-tro.update-permissions', $role->id) }}" method="POST" onsubmit="adminSubmitRolePermissions(event, this)" class="mt-4 pt-4 border-t border-slate-100 space-y-2">
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

                        <button type="submit" class="w-full mt-3 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            <span>Lưu cấu hình Role</span>
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
            if (!modal.classList.contains('hidden')) {
                const input = modal.querySelector('input[name="ten"]');
                if (input) input.focus();
            }
        }
    }

    function adminSubmitCreateRole(e, form) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span><span>Đang tạo vai trò...</span>';
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
                showRealtimeToast('Thành Công', data.message || 'Tạo vai trò thành công!', '', 'check_circle', 'bg-emerald-500');
            }
            form.reset();
            toggleTaoVaiTroModal();
            clearAdminPageCache();
            instantNavigate(window.location.href, false);
        })
        .catch(err => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lỗi Tạo Vai Trò', err.message || 'Không thể tạo vai trò mới.', '', 'error', 'bg-rose-500');
            }
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    function adminSubmitRolePermissions(e, form) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span><span>Đang lưu...</span>';
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
                showRealtimeToast('Thành Công', data.message || 'Cập nhật quyền hạn thành công!', '', 'check_circle', 'bg-emerald-500');
            }
            clearAdminPageCache();
        })
        .catch(err => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lỗi Cập Nhật', err.message || 'Không thể lưu quyền hạn.', '', 'error', 'bg-rose-500');
            }
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    function adminDeleteCustomRole(roleId, roleName, triggerBtn) {
        openConfirmModal({
            title: 'Xóa Vai Trò Tùy Chỉnh',
            message: `Bạn có chắc chắn muốn xóa vai trò <b class="text-slate-900 font-bold">"${roleName}"</b> khỏi hệ thống không?<br><span class="text-rose-600 text-[11px] font-medium">Thao tác này sẽ xóa vĩnh viễn vai trò và gỡ bỏ toàn bộ liên kết phân quyền.</span>`,
            icon: 'delete',
            iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
            confirmText: 'Xác Nhận Xóa',
            confirmClass: 'btn-enterprise-danger',
            confirmIcon: 'delete',
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const card = document.getElementById(`role-card-${roleId}`);

                fetch(`/admin/vai-tro/${roleId}`, {
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
                        showRealtimeToast('Đã Xóa Vai Trò', data.message || 'Đã xóa vai trò thành công.', '', 'delete', 'bg-slate-700');
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
                        showRealtimeToast('Không Thể Xóa', err.message || 'Có lỗi xảy ra khi xóa vai trò.', '', 'error', 'bg-rose-500');
                    }
                });
            }
        });
    }
</script>
@endsection

