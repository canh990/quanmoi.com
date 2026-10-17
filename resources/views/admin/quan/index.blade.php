@extends('admin.layout')

@section('title', 'Quản Lý Quán - Quán Mới Admin')
@section('page-title', 'Quản Lý Địa Điểm Quán & Duyệt Bài')

@section('content')
<div class="space-y-6">
    {{-- Search & Filters (Report Matching) --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.quan.index') }}" class="flex flex-wrap items-center gap-3 w-full">
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Q   Tìm tên quán, địa chỉ..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-slate-50/50">
                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
            </div>

            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:border-amber-500 bg-white text-slate-700">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="chua_duyet" {{ request('status') == 'chua_duyet' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="da_duyet" {{ request('status') == 'da_duyet' ? 'selected' : '' }}>Đã duyệt (Đang hoạt động)</option>
                <option value="bi_khoa" {{ request('status') == 'bi_khoa' ? 'selected' : '' }}>Bị khóa</option>
                <option value="soft_deleted" {{ request('status') == 'soft_deleted' ? 'selected' : '' }}>Đã xóa (Soft-delete)</option>
            </select>

            <select name="is_xac_thuc" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:border-amber-500 bg-white text-slate-700">
                <option value="">-- Tick xanh --</option>
                <option value="1" {{ request('is_xac_thuc') == '1' ? 'selected' : '' }}>Có Tick Xanh</option>
                <option value="0" {{ request('is_xac_thuc') == '0' ? 'selected' : '' }}>Chưa có Tick Xanh</option>
            </select>

            <button type="submit" class="bg-[#F59E0B] hover:bg-[#D97706] text-white px-5 py-2 rounded-xl font-bold text-xs transition-all shadow-xs flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">filter_alt</span> 
                <span>Lọc</span>
            </button>
        </form>
    </div>

    {{-- Venues Table (Report Matching) --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="w-full overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse text-xs min-w-[850px] table-fixed-enterprise">
                <colgroup>
                    <col class="w-[30%]">
                    <col class="w-[20%]">
                    <col class="w-[24%]">
                    <col class="w-[12%]">
                    <col class="w-[14%]">
                </colgroup>
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-3.5">TÊN QUÁN & LOẠI HÌNH</th>
                        <th class="py-3 px-3.5">CHỦ SỞ HỮU</th>
                        <th class="py-3 px-3.5">ĐỊA CHỈ</th>
                        <th class="py-3 px-3.5">TRẠNG THÁI</th>
                        <th class="py-3 px-3.5 text-right whitespace-nowrap">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($quanList as $quan)
                        <tr id="quan-mgmt-row-{{ $quan->id }}" class="hover:bg-slate-50/80 transition-colors {{ $quan->trashed() ? 'bg-rose-50/30' : '' }}">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img src="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=100&q=80' }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=100&q=80';" class="w-10 h-10 rounded-lg object-cover border border-slate-200 flex-shrink-0" alt="{{ $quan->ten_quan }}" />
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-slate-900 leading-tight flex items-center gap-1 cell-wrap-2line">
                                            {{ $quan->ten_quan }}
                                            @if($quan->is_xac_thuc)
                                                <span class="material-symbols-outlined text-blue-500 text-[14px] shrink-0" title="Có Tick Xanh" style="font-variation-settings: 'FILL' 1;">verified</span>
                                            @endif
                                        </p>
                                        <span class="inline-block bg-amber-50 text-amber-700 px-2 py-0.5 rounded text-[10px] font-semibold mt-0.5 border border-amber-200/60">
                                            {{ $quan->loai_hinh_kinh_doanh ?: 'Nhà hàng' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-800 cell-wrap-1line">{{ $quan->chuQuan?->ho_ten ?: 'Quản Trị Viên (Admin)' }}</p>
                                <p class="text-[11px] text-slate-400 font-mono leading-tight truncate">{{ $quan->chuQuan?->email ?: 'admin@quanmoi.com' }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <p class="font-medium cell-wrap-2line" title="{{ $quan->dia_chi_chi_tiet }}">{{ $quan->dia_chi_chi_tiet ?: '228 Love Creek Suite 319' }}</p>
                                <p class="text-[11px] text-slate-400 cell-wrap-1line">{{ $quan->ten_quan_huyen ?: 'Quận Ba Đình' }}, {{ $quan->ten_tinh_thanh ?: 'Thành phố Hà Nội' }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($quan->trashed())
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200">Đã xóa</span>
                                @elseif($quan->trang_thai === 'chua_duyet')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">⏳ Chờ duyệt</span>
                                @elseif($quan->trang_thai === 'bi_khoa')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700 border border-slate-300">🔒 Bị khóa</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200/80">• Đã duyệt</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap min-w-[170px]">
                                <div class="inline-flex items-center justify-end gap-1 flex-nowrap shrink-0">
                                    @if(!$quan->trashed())
                                        <button type="button" onclick="adminToggleQuanVerification('{{ $quan->id }}', this)" class="w-8 h-8 flex items-center justify-center rounded-lg transition-all cursor-pointer shrink-0 {{ $quan->is_xac_thuc ? 'text-blue-500 hover:bg-blue-50' : 'text-slate-400 hover:text-blue-500 hover:bg-slate-100' }}" title="{{ $quan->is_xac_thuc ? 'Gỡ Tick Xanh' : 'Cấp Tick Xanh' }}">
                                            <span class="material-symbols-outlined text-[18px]" {!! $quan->is_xac_thuc ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>check_circle</span>
                                        </button>

                                        <button type="button" onclick="adminToggleQuanFeatured('{{ $quan->id }}', this)" class="w-8 h-8 flex items-center justify-center rounded-lg transition-all cursor-pointer shrink-0 {{ $quan->is_noi_bat ? 'text-amber-500 hover:bg-amber-50' : 'text-slate-400 hover:text-amber-500 hover:bg-slate-100' }}" title="{{ $quan->is_noi_bat ? 'Gỡ khỏi danh sách nổi bật' : 'Đánh dấu nổi bật' }}">
                                            <span class="material-symbols-outlined text-[18px]" {!! $quan->is_noi_bat ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>star</span>
                                        </button>
                                    @endif

                                    <a href="{{ route('admin.quan.edit', $quan->id) }}" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:text-amber-600 hover:bg-slate-100 rounded-lg transition-all shrink-0" title="Chỉnh sửa & Duyệt">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>

                                    @if($quan->trashed())
                                        <button type="button" onclick="adminRestoreQuan('{{ $quan->id }}', '{{ addslashes($quan->ten_quan) }}')" class="w-8 h-8 flex items-center justify-center text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all cursor-pointer shrink-0" title="Khôi phục quán">
                                            <span class="material-symbols-outlined text-[18px]">restore_from_trash</span>
                                        </button>
                                        <button type="button" onclick="adminForceDeleteQuan('{{ $quan->id }}', '{{ addslashes($quan->ten_quan) }}')" class="w-8 h-8 flex items-center justify-center text-rose-700 hover:bg-rose-100 rounded-lg transition-all cursor-pointer shrink-0" title="Xóa cứng (Vĩnh viễn)">
                                            <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                                        </button>
                                    @else
                                        <button type="button" onclick="adminSoftDeleteQuan('{{ $quan->id }}', '{{ addslashes($quan->ten_quan) }}')" class="w-8 h-8 flex items-center justify-center text-rose-600 hover:bg-rose-50 rounded-lg transition-all cursor-pointer shrink-0" title="Xóa tạm (Soft-delete)">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Không tìm thấy địa điểm quán nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $quanList->links() }}
        </div>
    </div>
</div>

<script>
    function adminToggleQuanVerification(quanId, btn) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const iconSpan = btn.querySelector('.material-symbols-outlined');

        fetch(`/admin/quan/${quanId}/toggle-xac-thuc`, {
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
                showRealtimeToast('Xác Thực Quán', data.message, '', 'verified', 'bg-blue-500');
            }
            clearAdminPageCache();

            if (data.is_xac_thuc) {
                btn.className = 'p-1.5 rounded-lg transition-all cursor-pointer text-blue-500 hover:bg-blue-50';
                btn.title = 'Gỡ Tick Xanh';
                if (iconSpan) iconSpan.style.fontVariationSettings = "'FILL' 1";
            } else {
                btn.className = 'p-1.5 rounded-lg transition-all cursor-pointer text-slate-400 hover:text-blue-500 hover:bg-slate-100';
                btn.title = 'Cấp Tick Xanh';
                if (iconSpan) iconSpan.style.fontVariationSettings = "'FILL' 0";
            }
        })
        .catch(err => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lỗi Xác Thực', err.message || 'Không thể thay đổi trạng thái xác thực quán.', '', 'error', 'bg-rose-500');
            }
        });
    }

    function adminToggleQuanFeatured(quanId, btn) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const iconSpan = btn.querySelector('.material-symbols-outlined');

        fetch(`/admin/quan/${quanId}/toggle-noi-bat`, {
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
                showRealtimeToast('Quán Nổi Bật', data.message, '', 'star', 'bg-amber-500');
            }
            clearAdminPageCache();

            if (data.is_noi_bat) {
                btn.className = 'p-1.5 rounded-lg transition-all cursor-pointer text-amber-500 hover:bg-amber-50';
                btn.title = 'Gỡ khỏi danh sách nổi bật';
                if (iconSpan) iconSpan.style.fontVariationSettings = "'FILL' 1";
            } else {
                btn.className = 'p-1.5 rounded-lg transition-all cursor-pointer text-slate-400 hover:text-amber-500 hover:bg-slate-100';
                btn.title = 'Đánh dấu nổi bật';
                if (iconSpan) iconSpan.style.fontVariationSettings = "'FILL' 0";
            }
        })
        .catch(err => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lỗi Nổi Bật', err.message || 'Không thể cập nhật trạng thái nổi bật.', '', 'error', 'bg-rose-500');
            }
        });
    }

    function adminSoftDeleteQuan(quanId, quanName) {
        openConfirmModal({
            title: 'Xóa Tạm Quán Ăn',
            message: `Bạn có chắc chắn muốn xóa tạm quán <b class="text-slate-900 font-bold">"${quanName}"</b> vào thùng rác không?`,
            icon: 'delete',
            iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
            confirmText: 'Xác Nhận Xóa Tạm',
            confirmClass: 'btn-enterprise-danger',
            confirmIcon: 'delete',
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const row = document.getElementById(`quan-mgmt-row-${quanId}`);

                fetch(`/admin/quan/${quanId}`, {
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
                        showRealtimeToast('Đã Xóa Tạm Quán', data.message || 'Đã chuyển quán vào thùng rác.', '', 'delete', 'bg-slate-700');
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
                        showRealtimeToast('Không Thể Xóa', err.message || 'Có lỗi xảy ra khi xóa quán.', '', 'error', 'bg-rose-500');
                    }
                });
            }
        });
    }

    function adminRestoreQuan(quanId, quanName) {
        openConfirmModal({
            title: 'Khôi Phục Quán Ăn',
            message: `Bạn có chắc chắn muốn khôi phục quán <b class="text-slate-900 font-bold">"${quanName}"</b> về hoạt động bình thường không?`,
            icon: 'restore_from_trash',
            iconBg: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            confirmText: 'Khôi Phục Quán',
            confirmClass: 'btn-enterprise-success',
            confirmIcon: 'check_circle',
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                fetch(`/admin/quan/${quanId}/restore`, {
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
                        showRealtimeToast('Khôi Phục Thành Công', data.message || 'Quán đã được khôi phục!', '', 'check_circle', 'bg-emerald-500');
                    }
                    clearAdminPageCache();
                    instantNavigate(window.location.href, false);
                })
                .catch(err => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Lỗi Khôi Phục', err.message || 'Không thể khôi phục quán.', '', 'error', 'bg-rose-500');
                    }
                });
            }
        });
    }

    function adminForceDeleteQuan(quanId, quanName) {
        openConfirmModal({
            title: 'XÓA VĨNH VIỄN QUÁN ĂN',
            message: `CẢNH BÁO: Thao tác này sẽ xóa vĩnh viễn quán <b class="text-slate-900 font-bold">"${quanName}"</b> khỏi cơ sở dữ liệu. Không thể hoàn tác!<br><span class="text-rose-600 text-[11px] font-bold">Bạn có chắc chắn muốn xóa vĩnh viễn không?</span>`,
            icon: 'delete_forever',
            iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
            confirmText: 'Xác Nhận Xóa Vĩnh Viễn',
            confirmClass: 'btn-enterprise-danger',
            confirmIcon: 'delete_forever',
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const row = document.getElementById(`quan-mgmt-row-${quanId}`);

                fetch(`/admin/quan/${quanId}/force`, {
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
                        showRealtimeToast('Đã Xóa Vĩnh Viễn', data.message || 'Đã xóa vĩnh viễn quán ăn.', '', 'delete_forever', 'bg-rose-700');
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
</script>
@endsection
