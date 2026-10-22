@extends('admin.layout')

@section('title', 'Phân Quyền & Override - Admin Quán Mới')
@section('page-title', 'Phân Quyền Hệ Thống')

@section('content')
<div class="space-y-4 text-slate-900 w-full max-w-full">

    {{-- TOP COMMAND BAR --}}
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4">
        {{-- Title + Subtitle --}}
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-700 to-violet-700 flex items-center justify-center shadow-sm shrink-0">
                <span class="material-symbols-outlined text-[20px] fill-1 text-white">security</span>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight whitespace-nowrap">Quyền Hạn & Override</h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        {{ $permissions->count() }} quyền · {{ $users->total() }} người dùng
                    </span>
                </div>
                <p class="text-xs text-slate-600 font-medium hidden sm:block mt-0.5">Quản lý danh mục quyền và cấu hình override đặc thù cho từng người dùng</p>
            </div>
        </div>

        {{-- KPI STAT CHIPS --}}
        <div class="flex items-center gap-2.5 overflow-x-auto custom-scrollbar py-1 shrink-0">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200/90 shrink-0">
                <span class="material-symbols-outlined text-[16px] text-indigo-700 fill-1">key</span>
                <span class="text-xs font-bold text-slate-700">Quyền:</span>
                <span class="text-xs font-extrabold text-indigo-800">{{ $permissions->count() }}</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200/90 shrink-0">
                <span class="material-symbols-outlined text-[16px] text-emerald-700 fill-1">verified</span>
                <span class="text-xs font-bold text-slate-700">Override (+):</span>
                <span class="text-xs font-extrabold text-emerald-800">
                    {{ \App\Models\NguoiDungQuyenHan::where('cho_phep', true)->count() }}
                </span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200/90 shrink-0 hidden sm:flex">
                <span class="material-symbols-outlined text-[16px] text-rose-700 fill-1">block</span>
                <span class="text-xs font-bold text-slate-700">Override (−):</span>
                <span class="text-xs font-extrabold text-rose-800">
                    {{ \App\Models\NguoiDungQuyenHan::where('cho_phep', false)->count() }}
                </span>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <button type="button" onclick="togglePermDrawer()" id="btn-toggle-perm-sidebar"
                    class="h-9 px-3.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer"
                    title="Mở danh mục quyền hạn hệ thống">
                <span class="material-symbols-outlined text-[18px] text-indigo-600" id="icon-toggle-perm">format_list_bulleted</span>
                <span id="text-toggle-perm">Danh mục quyền ({{ $permissions->count() }})</span>
            </button>
            <a href="{{ route('admin.vai-tro.index') }}"
               class="h-9 px-3.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[18px] text-indigo-600">admin_panel_settings</span>
                Vai Trò
            </a>
            <button type="button" onclick="toggleAddPermForm()"
                    class="h-9 px-4 rounded-xl text-xs font-extrabold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm hover:shadow transition-all flex items-center gap-2 cursor-pointer active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">add_task</span>
                Thêm Quyền Mới
            </button>
        </div>
    </div>

    {{-- ADD PERMISSION INLINE FORM --}}
    <div id="add-perm-form-bar"
         class="hidden bg-gradient-to-r from-indigo-50 via-violet-50 to-purple-50 border-2 border-indigo-200 rounded-2xl p-4 shadow-sm shrink-0 transition-all">
        <form action="{{ route('admin.phan-quyen.store') }}" method="POST" id="form-create-permission"
              onsubmit="adminSubmitCreatePermission(event, this)"
              class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
            @csrf
            <div class="flex items-center gap-2 text-indigo-900 font-extrabold text-xs shrink-0">
                <span class="material-symbols-outlined text-indigo-600 text-[20px]">add_task</span>
                <span>Thêm quyền:</span>
            </div>
            <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <input type="text" name="ten" id="input-perm-name"
                       placeholder="Mã quyền (vd: quan_ly_bai_viet, duyet_tin...)"
                       required
                       class="flex-1 px-3.5 py-2 text-xs font-semibold text-slate-900 bg-white rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none shadow-2xs">
                <input type="text" name="mo_ta"
                       placeholder="Mô tả chức năng chi tiết của quyền (tùy chọn)..."
                       class="flex-1 px-3.5 py-2 text-xs font-medium text-slate-900 bg-white rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none shadow-2xs">
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit"
                        class="h-9 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition-all flex items-center gap-1.5 cursor-pointer whitespace-nowrap active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Lưu Quyền
                </button>
                <button type="button" onclick="toggleAddPermForm()"
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
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 transition-all shadow-2xs">
            <span class="material-symbols-outlined text-[17px] text-slate-500">admin_panel_settings</span>
            <span>Vai Trò Hệ Thống</span>
        </a>
        <a href="{{ route('admin.phan-quyen.index') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-extrabold bg-indigo-600 text-white shadow-sm border border-indigo-600">
            <span class="material-symbols-outlined text-[17px] fill-1 text-white">security</span>
            <span>Quyền Hạn & Override User</span>
        </a>
    </div>

    {{-- MAIN TABLE CARD: 100% Full Width, Cố định trong màn hình, Không lướt qua lại --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col w-full overflow-hidden">
        {{-- Table Filter Header --}}
        <div class="p-4 bg-slate-50/70 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[20px] text-indigo-700 fill-1">manage_accounts</span>
                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Override Quyền Theo Người Dùng</h3>
            </div>

            <form method="GET" action="{{ route('admin.phan-quyen.index') }}"
                  class="flex flex-wrap items-center gap-2.5">
                <div class="admin-search-wrapper w-44 sm:w-56 bg-white border border-slate-300 focus-within:border-indigo-500 shadow-2xs">
                    <span class="material-symbols-outlined text-slate-500 text-[18px]">search</span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Tìm tên, email..."
                           class="w-full bg-transparent border-0 p-0 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none font-semibold">
                </div>
                <select name="role"
                        class="h-9 px-3 text-xs bg-white border border-slate-300 hover:border-slate-400 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-300 text-slate-800 font-bold transition-colors">
                    <option value="">-- Tất cả vai trò --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ request('role') == $r->id ? 'selected' : '' }}>{{ $r->ten }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="h-9 px-3.5 bg-slate-900 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-all flex items-center gap-1.5 cursor-pointer shadow-xs active:scale-[0.98]"
                        title="Lọc kết quả">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span class="hidden sm:inline">Lọc</span>
                </button>
                @if(request()->hasAny(['q','role']))
                    <a href="{{ route('admin.phan-quyen.index') }}"
                       class="h-9 px-3 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 rounded-xl flex items-center gap-1 text-xs font-bold cursor-pointer transition-colors shadow-2xs"
                       title="Xóa bộ lọc">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span class="hidden sm:inline">Bỏ lọc</span>
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Cố Định 100% Không Cuộn Ngang --}}
        <div class="w-full overflow-hidden">
            <table class="w-full table-fixed text-left text-xs text-slate-800 border-collapse">
                <colgroup>
                    <col class="w-[27%]">
                    <col class="w-[13%]">
                    <col class="w-[27%]">
                    <col class="w-[33%]">
                </colgroup>
                <thead>
                    <tr class="bg-slate-100 border-b-2 border-slate-200 text-[11px] font-black text-slate-700 uppercase tracking-wider">
                        <th class="px-4 py-3.5">Người Dùng</th>
                        <th class="px-2 py-3.5 text-center">Vai Trò</th>
                        <th class="px-3 py-3.5">Quyền Override Đang Có</th>
                        <th class="px-4 py-3.5 text-right">Gán / Ghi Đè Quyền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($users as $u)
                        <tr id="user-override-row-{{ $u->id }}" class="hover:bg-indigo-50/30 transition-colors group">
                            {{-- Cột 1: Thông tin User --}}
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <img src="{{ $u->anh_dai_dien_url }}" alt="{{ $u->ho_ten }}"
                                         class="w-8 h-8 rounded-full object-cover border border-slate-300 shadow-2xs shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-extrabold text-slate-900 text-xs truncate leading-snug" title="{{ $u->ho_ten }}">{{ $u->ho_ten }}</p>
                                        <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5" title="{{ $u->email }}">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Cột 2: Vai trò badge --}}
                            <td class="px-2 py-3 text-center align-middle">
                                @php
                                    $roleCls = match(strtolower($u->vaiTro?->ten ?? '')) {
                                        'admin' => 'bg-rose-100 text-rose-800 border border-rose-300',
                                        'chu_quan' => 'bg-amber-100 text-amber-900 border border-amber-300',
                                        default => 'bg-blue-100 text-blue-900 border border-blue-300'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $roleCls }} whitespace-nowrap inline-block shadow-2xs">
                                    {{ $u->ten_vai_tro_hien_thi }}
                                </span>
                            </td>

                            {{-- Cột 3: Overrides hiện có --}}
                            <td class="px-3 py-3 align-middle" id="user-overrides-cell-{{ $u->id }}">
                                <div class="flex flex-wrap gap-1 items-center">
                                    @forelse($u->quyenHanOverrides as $ov)
                                        <span data-perm="{{ $ov->ten }}"
                                              class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-[10px] font-extrabold shadow-2xs shrink-0
                                                  {{ $ov->pivot->cho_phep ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-rose-100 text-rose-900 border border-rose-300' }}"
                                              title="{{ $ov->ten }}: {{ $ov->pivot->cho_phep ? 'Cho phép' : 'Từ chối' }}">
                                            <span class="font-black">{{ $ov->pivot->cho_phep ? '✓' : '✕' }}</span>
                                            <span class="truncate max-w-[120px]">{{ $ov->ten }}</span>
                                        </span>
                                    @empty
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 italic">
                                            Mặc định vai trò
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Cột 4: Form thao tác Override gọn gàng nằm cố định trong màn hình --}}
                            <td class="px-4 py-3 align-middle">
                                <form action="{{ route('admin.phan-quyen.override') }}" method="POST"
                                      onsubmit="adminSubmitUserOverride(event, this, '{{ $u->id }}')"
                                      class="flex items-center justify-end gap-1.5 w-full">
                                    @csrf
                                    <input type="hidden" name="nguoi_dung_id" value="{{ $u->id }}">
                                    <select name="quyen_han_id"
                                            class="flex-1 min-w-0 h-8 px-2 text-[11px] font-bold text-slate-900 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-indigo-500 rounded-lg outline-none transition-colors shadow-2xs truncate">
                                        @foreach($permissions as $perm)
                                            <option value="{{ $perm->id }}" title="{{ $perm->ten }}">{{ $perm->ten }}</option>
                                        @endforeach
                                    </select>
                                    <select name="cho_phep"
                                            class="w-[74px] h-8 px-1 text-[11px] font-black bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-indigo-500 rounded-lg outline-none text-slate-900 shrink-0 transition-colors shadow-2xs">
                                        <option value="1" class="text-emerald-800 font-bold">+ Cho</option>
                                        <option value="0" class="text-rose-800 font-bold">− Chặn</option>
                                        <option value="-1" class="text-slate-600 font-semibold">↺ Reset</option>
                                    </select>
                                    <button type="submit"
                                            class="h-8 px-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-lg text-xs transition-all flex items-center gap-1 cursor-pointer whitespace-nowrap shrink-0 shadow-xs active:scale-[0.98]"
                                            title="Lưu cấu hình quyền">
                                        <span class="material-symbols-outlined text-[14px]">save</span>
                                        <span>Lưu</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-16 text-center text-slate-500">
                                <span class="material-symbols-outlined text-[48px] block mb-2 text-slate-300">search_off</span>
                                <p class="text-sm font-bold text-slate-700">Không tìm thấy người dùng nào</p>
                                @if(request()->hasAny(['q','role']))
                                    <a href="{{ route('admin.phan-quyen.index') }}" class="text-xs font-bold text-indigo-700 hover:underline mt-2 inline-block">
                                        Xóa bộ lọc tìm kiếm
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if($users->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 shrink-0 flex flex-wrap items-center justify-between gap-3 bg-slate-50/70">
                <p class="text-xs font-bold text-slate-700">
                    Hiển thị {{ $users->firstItem() }} – {{ $users->lastItem() }} / tổng cộng {{ $users->total() }} người dùng
                </p>
                <div class="text-xs font-bold">{{ $users->links() }}</div>
            </div>
        @endif
    </div>

</div>

{{-- OFFCANVAS SLIDE-OVER DRAWER: Danh Mục Quyền Hạn (Không chèn ép bảng chính) --}}
<div id="perm-drawer-backdrop" onclick="togglePermDrawer()"
     class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 hidden transition-opacity"></div>

<div id="left-perm-panel"
     class="fixed inset-y-0 right-0 max-w-full w-96 sm:w-[420px] bg-white shadow-2xl z-50 flex flex-col border-l border-slate-200 transform translate-x-full transition-transform duration-300 ease-in-out">
    
    {{-- Drawer Header --}}
    <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 shrink-0">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold shrink-0">
                <span class="material-symbols-outlined text-[18px]">key</span>
            </div>
            <div class="min-w-0">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide truncate">Danh Mục Quyền</h3>
                <p class="text-[11px] text-slate-500 font-medium">Tổng số: {{ $permissions->count() }} quyền hệ thống</p>
            </div>
        </div>
        <button type="button" onclick="togglePermDrawer()"
                class="w-8 h-8 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-600 hover:text-slate-900 transition-colors shadow-2xs cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>

    {{-- Search In Drawer --}}
    <div class="p-3 border-b border-slate-200 bg-white shrink-0">
        <div class="relative flex items-center">
            <span class="material-symbols-outlined absolute left-2.5 text-slate-400 text-[18px]">search</span>
            <input type="text" id="filter-perm-input" onkeyup="filterPermissionsList(this.value)"
                   placeholder="Tìm kiếm mã quyền hoặc mô tả..."
                   class="w-full pl-8 pr-3 py-1.5 text-xs font-semibold text-slate-900 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-300 outline-none">
        </div>
    </div>

    {{-- Permission Cards in Drawer --}}
    <div class="p-4 space-y-2.5 overflow-y-auto custom-scrollbar flex-1" id="permissions-grid-container">
        @forelse($permissions as $perm)
            <div id="perm-card-{{ $perm->id }}"
                 data-perm-search="{{ strtolower($perm->ten . ' ' . ($perm->mo_ta ?? '')) }}"
                 class="perm-item-card p-3 rounded-xl bg-slate-50 border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 transition-all flex flex-col gap-1.5">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <span class="material-symbols-outlined text-[16px] text-indigo-600 shrink-0">vpn_key</span>
                        <p class="text-xs font-bold text-slate-900 font-mono break-all leading-snug" title="{{ $perm->ten }}">{{ $perm->ten }}</p>
                    </div>
                    @if(($perm->vai_tro_count ?? 0) === 0)
                        <button type="button"
                                onclick="adminDeletePermission('{{ $perm->id }}', '{{ $perm->ten }}')"
                                class="w-6 h-6 rounded-lg text-slate-400 hover:text-rose-700 hover:bg-rose-100 flex items-center justify-center cursor-pointer transition-colors shrink-0"
                                title="Xóa quyền chưa sử dụng">
                            <span class="material-symbols-outlined text-[15px]">delete</span>
                        </button>
                    @else
                        <span class="text-[10px] font-extrabold text-indigo-800 px-2 py-0.5 rounded-md bg-indigo-100/80 border border-indigo-200 shrink-0"
                              title="Đang gán cho {{ $perm->vai_tro_count }} vai trò">
                            {{ $perm->vai_tro_count }} role
                        </span>
                    @endif
                </div>
                @if($perm->mo_ta)
                    <p class="text-[11px] text-slate-600 leading-normal pl-6">{{ $perm->mo_ta }}</p>
                @endif
            </div>
        @empty
            <div class="py-12 text-center text-slate-500">
                <span class="material-symbols-outlined text-[36px] block mb-2 text-slate-400">lock_open</span>
                <p class="text-xs font-bold">Chưa có quyền hạn nào</p>
            </div>
        @endforelse
    </div>

    {{-- Drawer Footer --}}
    <div class="p-3 border-t border-slate-200 bg-slate-50 shrink-0">
        <button type="button" onclick="togglePermDrawer(); toggleAddPermForm();"
                class="w-full h-9 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
            <span class="material-symbols-outlined text-[16px]">add_task</span>
            <span>Thêm Quyền Mới</span>
        </button>
    </div>
</div>

<script>
function togglePermDrawer() {
    const drawer = document.getElementById('left-perm-panel');
    const backdrop = document.getElementById('perm-drawer-backdrop');
    if (!drawer || !backdrop) return;
    
    const isClosed = drawer.classList.contains('translate-x-full');
    if (isClosed) {
        drawer.classList.remove('translate-x-full');
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        drawer.classList.add('translate-x-full');
        backdrop.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Alias for backwards compatibility
function togglePermSidebar() {
    togglePermDrawer();
}

function toggleAddPermForm() {
    const bar = document.getElementById('add-perm-form-bar');
    if (!bar) return;
    bar.classList.toggle('hidden');
    if (!bar.classList.contains('hidden')) {
        setTimeout(() => document.getElementById('input-perm-name')?.focus(), 60);
    }
}

function filterPermissionsList(query) {
    const q = (query || '').toLowerCase().trim();
    const items = document.querySelectorAll('.perm-item-card');
    items.forEach(item => {
        const text = item.getAttribute('data-perm-search') || '';
        item.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
}

function adminSubmitCreatePermission(e, form) {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    const ori = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="material-symbols-outlined text-[15px] animate-spin">progress_activity</span><span>Đang thêm...</span>'; }
    fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
        body: new FormData(form)
    })
    .then(async r => { const d = await r.json().catch(() => ({})); if (!r.ok) throw new Error(d.message || d.errors?.ten?.[0] || 'Lỗi ' + r.status); return d; })
    .then(d => {
        showRealtimeToast?.('Thêm Quyền Thành Công', d.message || 'Quyền hạn mới đã được thêm!', '', 'check_circle', 'bg-emerald-600');
        form.reset(); toggleAddPermForm(); clearAdminPageCache?.(); instantNavigate?.(window.location.href, false);
    })
    .catch(err => showRealtimeToast?.('Lỗi Thêm Quyền', err.message || 'Không thể thêm quyền hạn.', '', 'error', 'bg-rose-600'))
    .finally(() => { if (btn) { btn.disabled = false; btn.innerHTML = ori; } });
}

function adminDeletePermission(permId, permName) {
    openConfirmModal({
        title: 'Xóa Quyền Hạn',
        message: `Bạn có chắc muốn xóa quyền <b class="text-slate-900">"${permName}"</b>?<br><span class="text-rose-600 text-xs font-semibold">Lưu ý: Quyền phải chưa được gán cho vai trò nào.</span>`,
        icon: 'delete', iconBg: 'bg-rose-100 text-rose-800 border border-rose-300',
        confirmText: 'Xác Nhận Xóa', confirmClass: 'btn-enterprise-danger', confirmIcon: 'delete',
        onConfirm: () => {
            const card = document.getElementById('perm-card-' + permId);
            fetch('/admin/phan-quyen/' + permId, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
            })
            .then(async r => { const d = await r.json().catch(() => ({})); if (!r.ok) throw new Error(d.message || 'Lỗi ' + r.status); return d; })
            .then(d => {
                showRealtimeToast?.('Đã Xóa Quyền', d.message || 'Xóa quyền thành công.', '', 'delete', 'bg-slate-800');
                clearAdminPageCache?.();
                if (card) { card.style.transition='all 0.25s'; card.style.opacity='0'; card.style.maxHeight='0'; card.style.marginBottom='0'; card.style.padding='0'; setTimeout(() => card.remove(), 250); }
            })
            .catch(err => showRealtimeToast?.('Không Thể Xóa', err.message || 'Có lỗi xảy ra.', '', 'error', 'bg-rose-600'));
        }
    });
}

function adminSubmitUserOverride(e, form, userId) {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    const ori = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="material-symbols-outlined text-[13px] animate-spin">progress_activity</span>'; }
    fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
        body: new FormData(form)
    })
    .then(async r => { const d = await r.json().catch(() => ({})); if (!r.ok) throw new Error(d.message || 'Lỗi ' + r.status); return d; })
    .then(data => {
        const icon = data.action === '1' ? 'verified' : (data.action === '0' ? 'block' : 'restart_alt');
        const bg = data.action === '1' ? 'bg-emerald-600' : (data.action === '0' ? 'bg-rose-600' : 'bg-slate-700');
        showRealtimeToast?.('Cập Nhật Phân Quyền', data.message || 'Đã cập nhật override!', '', icon, bg);
        clearAdminPageCache?.();
        const cell = document.getElementById('user-overrides-cell-' + userId);
        if (cell && data.permission_name) {
            const pn = data.permission_name;
            const ex = cell.querySelector('[data-perm="' + pn + '"]');
            if (data.action === '-1') {
                if (ex) ex.remove();
                if (!cell.querySelector('[data-perm]')) {
                    cell.innerHTML = '<div class="flex flex-wrap gap-1 items-center"><span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 italic">Mặc định vai trò</span></div>';
                }
            } else {
                const ok = data.action === '1';
                const cls = ok
                    ? 'inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-[10px] font-extrabold shadow-2xs shrink-0 bg-emerald-100 text-emerald-900 border border-emerald-300'
                    : 'inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-[10px] font-extrabold shadow-2xs shrink-0 bg-rose-100 text-rose-900 border border-rose-300';
                const ph = cell.querySelector('.italic');
                if (ph) ph.remove();
                if (ex) {
                    ex.className = cls;
                    ex.innerHTML = '<span class="font-black">' + (ok ? '✓' : '✕') + '</span><span class="truncate max-w-[120px]">' + pn + '</span>';
                } else {
                    let wrap = cell.querySelector('.flex');
                    if (!wrap) { wrap = cell; }
                    const s = document.createElement('span');
                    s.setAttribute('data-perm', pn);
                    s.className = cls;
                    s.innerHTML = '<span class="font-black">' + (ok ? '✓' : '✕') + '</span><span class="truncate max-w-[120px]">' + pn + '</span>';
                    wrap.appendChild(s);
                }
            }
        }
    })
    .catch(err => showRealtimeToast?.('Lỗi Phân Quyền', err.message || 'Không thể cập nhật override.', '', 'error', 'bg-rose-600'))
    .finally(() => { if (btn) { btn.disabled = false; btn.innerHTML = ori; } });
}
</script>
@endsection
