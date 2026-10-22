@extends('admin.layout')

@section('title', 'Quản Lý Vai Trò & Phân Quyền - Admin Quán Mới')
@section('page-title', 'Phân Quyền Hệ Thống')

@section('content')
<div class="space-y-4 text-slate-900">

    {{-- TOP COMMAND BAR --}}
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4 shrink-0">
        {{-- Title + Subtitle --}}
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-700 to-violet-700 flex items-center justify-center shadow-sm shrink-0">
                <span class="material-symbols-outlined text-[20px] fill-1 text-white">admin_panel_settings</span>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight whitespace-nowrap">Quản Lý Vai Trò</h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        {{ $roles->count() }} vai trò · {{ $permissions->count() }} quyền hạn
                    </span>
                </div>
                <p class="text-xs text-slate-600 font-medium hidden sm:block mt-0.5">Cấu hình nhóm quyền mặc định cho từng vai trò trong hệ thống</p>
            </div>
        </div>

        {{-- KPI CHIPS --}}
        <div class="flex items-center gap-2.5 overflow-x-auto custom-scrollbar py-1 shrink-0">
            @php
                $adminRole = $roles->firstWhere('ten', 'admin');
                $chuQuanRole = $roles->firstWhere('ten', 'chu_quan');
                $nguoiDungRole = $roles->firstWhere('ten', 'nguoi_dung');
            @endphp
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200/90 shrink-0">
                <span class="material-symbols-outlined text-[16px] text-rose-700 fill-1">manage_accounts</span>
                <span class="text-xs font-bold text-slate-700">Admin:</span>
                <span class="text-xs font-extrabold text-rose-800">{{ $adminRole?->nguoi_dung_count ?? 0 }}</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200/90 shrink-0">
                <span class="material-symbols-outlined text-[16px] text-amber-700 fill-1">store</span>
                <span class="text-xs font-bold text-slate-700">Chủ quán:</span>
                <span class="text-xs font-extrabold text-amber-800">{{ $chuQuanRole?->nguoi_dung_count ?? 0 }}</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200/90 shrink-0 hidden sm:flex">
                <span class="material-symbols-outlined text-[16px] text-blue-700 fill-1">person</span>
                <span class="text-xs font-bold text-slate-700">Thành viên:</span>
                <span class="text-xs font-extrabold text-blue-800">{{ $nguoiDungRole?->nguoi_dung_count ?? 0 }}</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200/90 shrink-0 hidden md:flex">
                <span class="material-symbols-outlined text-[16px] text-indigo-700 fill-1">key</span>
                <span class="text-xs font-bold text-slate-700">Quyền hạn:</span>
                <span class="text-xs font-extrabold text-indigo-800">{{ $permissions->count() }}</span>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <a href="{{ route('admin.phan-quyen.index') }}"
               class="h-9 px-3.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[18px] text-indigo-600">security</span>
                <span>Override User</span>
            </a>
            <button type="button" onclick="toggleTaoVaiTroForm()"
                    id="btn-tao-vai-tro"
                    class="h-9 px-4 rounded-xl text-xs font-extrabold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm hover:shadow transition-all flex items-center gap-2 cursor-pointer active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">add_moderator</span>
                <span>Tạo Vai Trò Mới</span>
            </button>
        </div>
    </div>

    {{-- INLINE CREATE FORM --}}
    <div id="create-role-form-bar"
         class="hidden bg-gradient-to-r from-indigo-50 via-violet-50 to-purple-50 border-2 border-indigo-200 rounded-2xl p-4 shadow-sm shrink-0 transition-all">
        <form action="{{ route('admin.vai-tro.store') }}" method="POST" id="form-create-role"
              onsubmit="adminSubmitCreateRole(event, this)"
              class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @csrf
            <div class="flex items-center gap-2 text-indigo-900 font-extrabold text-xs shrink-0">
                <span class="material-symbols-outlined text-indigo-600 text-[20px]">add_moderator</span>
                <span>Tạo vai trò:</span>
            </div>
            <div class="flex-1 min-w-0">
                <input type="text" name="ten"
                       id="input-role-name"
                       placeholder="Mã vai trò mới: vd bien_tap_vien, kiem_duyet_vien (chữ thường, số, gạch dưới)"
                       required
                       class="w-full px-3.5 py-2 text-xs font-semibold text-slate-900 bg-white rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none shadow-2xs">
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit"
                        class="h-9 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition-all flex items-center gap-1.5 cursor-pointer whitespace-nowrap active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Lưu Vai Trò
                </button>
                <button type="button" onclick="toggleTaoVaiTroForm()"
                        class="h-9 w-9 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 flex items-center justify-center cursor-pointer text-slate-700 hover:text-slate-900 transition-colors shadow-2xs"
                        title="Đóng biểu mẫu">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </form>
    </div>

    {{-- TABS --}}
    <div class="flex items-center gap-2 shrink-0">
        <a href="{{ route('admin.vai-tro.index') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-extrabold bg-indigo-600 text-white shadow-sm border border-indigo-600">
            <span class="material-symbols-outlined text-[17px] fill-1 text-white">admin_panel_settings</span>
            <span>Vai Trò Hệ Thống</span>
        </a>
        <a href="{{ route('admin.phan-quyen.index') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 transition-all shadow-2xs">
            <span class="material-symbols-outlined text-[17px] text-slate-500">security</span>
            <span>Quyền Hạn & Override User</span>
        </a>
    </div>

    {{-- MAIN CONTENT: ROLE CARDS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4.5" id="roles-grid-container">
        @foreach($roles as $role)
            @php
                $isSystem = in_array(strtolower($role->ten), ['admin', 'chu_quan', 'nguoi_dung']);
                
                // Cấu hình màu sắc cố định chuẩn 100%, không bị purge hay mất màu do CSS
                $roleTheme = match(strtolower($role->ten)) {
                    'admin' => [
                        'banner_style' => 'background: linear-gradient(135deg, #be123c, #9f1239);',
                        'border_cls'   => 'border-rose-300',
                        'btn_style'    => 'background-color: #be123c;',
                        'btn_hover'    => 'hover:bg-rose-800',
                        'badge_border' => 'border-rose-400',
                    ],
                    'chu_quan' => [
                        'banner_style' => 'background: linear-gradient(135deg, #d97706, #b45309);',
                        'border_cls'   => 'border-amber-300',
                        'btn_style'    => 'background-color: #d97706;',
                        'btn_hover'    => 'hover:bg-amber-800',
                        'badge_border' => 'border-amber-400',
                    ],
                    'nguoi_dung' => [
                        'banner_style' => 'background: linear-gradient(135deg, #1d4ed8, #1e40af);',
                        'border_cls'   => 'border-blue-300',
                        'btn_style'    => 'background-color: #1d4ed8;',
                        'btn_hover'    => 'hover:bg-blue-800',
                        'badge_border' => 'border-blue-400',
                    ],
                    default => [
                        'banner_style' => 'background: linear-gradient(135deg, #334155, #1e293b);',
                        'border_cls'   => 'border-slate-300',
                        'btn_style'    => 'background-color: #1e293b;',
                        'btn_hover'    => 'hover:bg-slate-950',
                        'badge_border' => 'border-slate-400',
                    ],
                };

                $roleLabel = match(strtolower($role->ten)) {
                    'admin'      => 'Quản Trị Viên',
                    'chu_quan'   => 'Chủ Quán',
                    'nguoi_dung' => 'Thành Viên',
                    default      => ucfirst(str_replace('_', ' ', $role->ten)),
                };
                $roleIcon = match(strtolower($role->ten)) {
                    'admin'      => 'manage_accounts',
                    'chu_quan'   => 'store',
                    'nguoi_dung' => 'person',
                    default      => 'badge',
                };
                $rolePermIds = $role->quyenHan ? $role->quyenHan->pluck('id')->flip()->toArray() : [];
            @endphp
            <div id="role-card-{{ $role->id }}"
                 class="bg-white rounded-2xl border-2 {{ $roleTheme['border_cls'] }} shadow-sm flex flex-col overflow-hidden transition-all hover:shadow-md">

                {{-- Card Header: Thiết kế 2 tầng siêu thoáng, 100% hiển thị trọn vẹn tên vai trò, KHÔNG BAO GIỜ BỊ MẤT HOẶC CẮT '...' --}}
                <div class="p-4 flex flex-col gap-2.5 text-white"
                     style="{{ $roleTheme['banner_style'] }} color: #ffffff;">
                    {{-- Hàng 1: Icon vai trò + Badge Hệ Thống / Nút Xóa --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="w-9 h-9 rounded-xl bg-white/20 border border-white/30 flex items-center justify-center shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-[20px] fill-1 text-white">{{ $roleIcon }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($isSystem)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-black/30 text-white border border-white/40 flex items-center gap-1 shadow-2xs">
                                    <span class="material-symbols-outlined text-[12px] fill-1">lock</span>Hệ Thống
                                </span>
                            @elseif($role->nguoi_dung_count == 0)
                                <button type="button"
                                        onclick="adminDeleteCustomRole('{{ $role->id }}', '{{ $role->ten }}')"
                                        class="w-8 h-8 rounded-xl bg-white/20 hover:bg-rose-600 border border-white/30 flex items-center justify-center cursor-pointer transition-colors text-white shadow-2xs"
                                        title="Xóa vai trò này">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Hàng 2: Tên hiển thị đầy đủ (100% bề rộng thẻ, font đậm rõ nét, không bị co kéo hay mất chữ) --}}
                    <div>
                        <h4 class="text-base font-black text-white leading-tight drop-shadow-xs break-words">{{ $roleLabel }}</h4>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="text-[11px] font-mono text-white/90 font-bold tracking-wide">{{ $role->ten }}</span>
                        </div>
                    </div>
                </div>

                {{-- Stats Row --}}
                <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between text-xs font-bold text-slate-800">
                    <span class="flex items-center gap-1.5 text-slate-700">
                        <span class="material-symbols-outlined text-[16px] text-slate-500">group</span>
                        <span>{{ $role->nguoi_dung_count }} user</span>
                    </span>
                    <span class="w-px h-3.5 bg-slate-300"></span>
                    <span class="flex items-center gap-1.5 text-indigo-900 font-extrabold">
                        <span class="material-symbols-outlined text-[16px] text-indigo-600">key</span>
                        <span>{{ $role->quyen_han_count }} quyền gán</span>
                    </span>
                </div>

                {{-- Permission Form --}}
                <form action="{{ route('admin.vai-tro.update-permissions', $role->id) }}"
                      method="POST"
                      onsubmit="adminSubmitRolePermissions(event, this, '{{ $role->ten }}')"
                      class="flex flex-col flex-1 p-4 gap-3">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <p class="text-[11px] font-black text-slate-800 uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-indigo-600">checklist</span>
                            <span>Danh Sách Quyền:</span>
                        </p>
                        <span class="text-[10px] font-bold text-indigo-800 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-200">Tick để gán</span>
                    </div>

                    {{-- List Quyền: Tương Phản Đậm, Chữ Rõ Ràng, Không Có Điểm Tròn Gây Rối Mắt --}}
                    <div class="flex-1 max-h-64 sm:max-h-72 overflow-y-auto custom-scrollbar space-y-1.5 pr-1">
                        @forelse($permissions as $perm)
                            @php $hasPerm = isset($rolePermIds[$perm->id]); @endphp
                            <label class="flex items-start gap-2.5 text-xs cursor-pointer p-2 rounded-xl transition-all border group
                                {{ $hasPerm ? 'bg-indigo-50/70 border-indigo-200' : 'bg-white hover:bg-slate-50 border-slate-200/70' }}">
                                <input type="checkbox"
                                       name="quyen_han[]"
                                       value="{{ $perm->id }}"
                                       {{ $hasPerm ? 'checked' : '' }}
                                       class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-1 shrink-0">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1.5">
                                        <span class="font-extrabold text-xs font-mono break-all leading-tight {{ $hasPerm ? 'text-indigo-950 font-black' : 'text-slate-800' }}">
                                            {{ $perm->ten }}
                                        </span>
                                        @if($hasPerm)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shrink-0">
                                                Đã gán
                                            </span>
                                        @endif
                                    </div>
                                    @if($perm->mo_ta)
                                        <span class="text-[11px] {{ $hasPerm ? 'text-slate-700' : 'text-slate-500' }} font-medium leading-snug mt-1 block break-words">
                                            {{ $perm->mo_ta }}
                                        </span>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <p class="text-xs text-slate-500 italic py-6 text-center">Chưa có quyền hạn nào trong hệ thống.</p>
                        @endforelse
                    </div>

                    {{-- Nút Lưu Cấu Hình Vai Trò: Đậm Nét, Cố Định Không Bị Mờ --}}
                    <button type="submit"
                            style="{{ $roleTheme['btn_style'] }} color: #ffffff;"
                            class="mt-auto w-full h-10 font-extrabold text-xs rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $roleTheme['btn_hover'] }} shadow-xs hover:shadow active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[16px] text-white">save</span>
                        <span class="text-white">Lưu Cấu Hình Vai Trò</span>
                    </button>
                </form>
            </div>
        @endforeach
    </div>

</div>

<script>
function toggleTaoVaiTroForm() {
    const bar = document.getElementById('create-role-form-bar');
    if (!bar) return;
    bar.classList.toggle('hidden');
    if (!bar.classList.contains('hidden')) {
        setTimeout(() => document.getElementById('input-role-name')?.focus(), 60);
    }
}

function adminSubmitCreateRole(e, form) {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    const ori = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="material-symbols-outlined text-[15px] animate-spin">progress_activity</span><span>Đang tạo...</span>'; }
    fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
        body: new FormData(form)
    })
    .then(async r => { const d = await r.json().catch(() => ({})); if (!r.ok) throw new Error(d.message || d.errors?.ten?.[0] || 'Lỗi ' + r.status); return d; })
    .then(d => {
        showRealtimeToast?.('Tạo Vai Trò Thành Công', d.message || 'Vai trò mới đã được tạo!', '', 'check_circle', 'bg-emerald-600');
        form.reset(); toggleTaoVaiTroForm(); clearAdminPageCache?.(); instantNavigate?.(window.location.href, false);
    })
    .catch(err => showRealtimeToast?.('Lỗi Tạo Vai Trò', err.message || 'Không thể tạo vai trò.', '', 'error', 'bg-rose-600'))
    .finally(() => { if (btn) { btn.disabled = false; btn.innerHTML = ori; } });
}

function adminSubmitRolePermissions(e, form, roleTen) {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    const ori = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="material-symbols-outlined text-[15px] animate-spin">progress_activity</span><span>Đang lưu...</span>'; }
    fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
        body: new FormData(form)
    })
    .then(async r => { const d = await r.json().catch(() => ({})); if (!r.ok) throw new Error(d.message || 'Lỗi ' + r.status); return d; })
    .then(d => { showRealtimeToast?.('Cập Nhật Thành Công', d.message || `Đã lưu quyền vai trò "${roleTen}"!`, '', 'check_circle', 'bg-emerald-600'); clearAdminPageCache?.(); })
    .catch(err => showRealtimeToast?.('Lỗi Cập Nhật', err.message || 'Không thể lưu quyền hạn.', '', 'error', 'bg-rose-600'))
    .finally(() => { if (btn) { btn.disabled = false; btn.innerHTML = ori; } });
}

function adminDeleteCustomRole(roleId, roleTen) {
    openConfirmModal({
        title: 'Xóa Vai Trò Tùy Chỉnh',
        message: `Bạn có chắc muốn xóa vai trò <b class="text-slate-900">"${roleTen}"</b>?<br><span class="text-rose-600 text-xs font-semibold">Lưu ý: Hành động này không thể hoàn tác.</span>`,
        icon: 'delete', iconBg: 'bg-rose-100 text-rose-800 border border-rose-300',
        confirmText: 'Xác Nhận Xóa', confirmClass: 'btn-enterprise-danger', confirmIcon: 'delete',
        onConfirm: () => {
            const card = document.getElementById('role-card-' + roleId);
            fetch('/admin/vai-tro/' + roleId, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
            })
            .then(async r => { const d = await r.json().catch(() => ({})); if (!r.ok) throw new Error(d.message || 'Lỗi ' + r.status); return d; })
            .then(d => {
                showRealtimeToast?.('Đã Xóa Vai Trò', d.message || 'Xóa thành công.', '', 'delete', 'bg-slate-800');
                clearAdminPageCache?.();
                if (card) { card.style.transition = 'all 0.3s'; card.style.opacity = '0'; card.style.transform = 'scale(0.92) translateY(-4px)'; setTimeout(() => card.remove(), 300); }
            })
            .catch(err => showRealtimeToast?.('Không Thể Xóa', err.message || 'Có lỗi xảy ra.', '', 'error', 'bg-rose-600'));
        }
    });
}
</script>
@endsection
