@extends('admin.layout')

@section('title', 'Quản Lý Quán - Quán Mới Admin')
@section('page-title', 'Quản Lý Địa Điểm Quán & Duyệt Bài')

@section('content')
<div class="space-y-4">
    {{-- Header Action & Status Overview Tabs --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        {{-- Status Filter Pills --}}
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="{{ route('admin.quan.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ !request('status') ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300' }}">
                Tất cả (<span id="quan-count-all">{{ $counts['all'] ?? 0 }}</span>)
            </a>
            <a href="{{ route('admin.quan.index', array_merge(request()->except('page'), ['status' => 'da_duyet'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'da_duyet' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-300' }}">
                • Hoạt động (<span id="quan-count-da_duyet">{{ $counts['da_duyet'] ?? 0 }}</span>)
            </a>
            <a href="{{ route('admin.quan.index', array_merge(request()->except('page'), ['status' => 'chua_duyet'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'chua_duyet' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white text-amber-800 hover:bg-amber-50 border border-amber-300' }}">
                ⏳ Chờ duyệt (<span id="quan-count-chua_duyet">{{ $counts['chua_duyet'] ?? 0 }}</span>)
            </a>
            <a href="{{ route('admin.quan.index', array_merge(request()->except('page'), ['status' => 'bi_khoa'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'bi_khoa' ? 'bg-slate-700 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300' }}">
                🔒 Bị khóa (<span id="quan-count-bi_khoa">{{ $counts['bi_khoa'] ?? 0 }}</span>)
            </a>
            <a href="{{ route('admin.quan.index', array_merge(request()->except('page'), ['status' => 'soft_deleted'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'soft_deleted' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-rose-700 hover:bg-rose-50 border border-rose-300' }}">
                🗑️ Đã xóa (<span id="quan-count-soft_deleted">{{ $counts['soft_deleted'] ?? 0 }}</span>)
            </a>
        </div>

        {{-- Add Venue Button --}}
        <a href="{{ route('admin.quan.create') }}" class="btn-action-primary-glow self-start md:self-auto shrink-0">
            <span class="material-symbols-outlined text-[18px]">add_location_alt</span>
            <span>Thêm Quán Mới</span>
        </a>
    </div>

    {{-- Search & Filters Bar (Sleek Compact Toolbar) --}}
    <div class="bg-white p-3 rounded-2xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.quan.index') }}" class="flex flex-wrap items-center gap-2.5 w-full">
            {{-- Search Keyword --}}
            <div class="admin-search-wrapper flex-1 min-w-[200px] shadow-2xs">
                <span class="material-symbols-outlined text-[16px] text-slate-400">search</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tên quán, địa chỉ, sđt, chủ sở hữu..." class="w-full bg-transparent border-0 p-0 text-xs font-medium outline-none text-slate-800 placeholder:text-slate-400">
            </div>

            {{-- Status Filter --}}
            <select name="status" class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-white text-slate-700">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="chua_duyet" {{ request('status') == 'chua_duyet' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="da_duyet" {{ request('status') == 'da_duyet' ? 'selected' : '' }}>Đã duyệt (Hoạt động)</option>
                <option value="bi_khoa" {{ request('status') == 'bi_khoa' ? 'selected' : '' }}>Bị khóa</option>
                <option value="soft_deleted" {{ request('status') == 'soft_deleted' ? 'selected' : '' }}>Đã xóa (Thùng rác)</option>
            </select>

            {{-- Business Type Filter --}}
            <select name="loai_hinh" class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-white text-slate-700">
                <option value="">-- Tất cả loại hình --</option>
                @foreach($businessTypes as $type)
                    <option value="{{ $type }}" {{ request('loai_hinh') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>

            {{-- Verified Filter --}}
            <select name="is_xac_thuc" class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-white text-slate-700">
                <option value="">-- Tick xanh --</option>
                <option value="1" {{ request('is_xac_thuc') === '1' ? 'selected' : '' }}>Có Tick Xanh</option>
                <option value="0" {{ request('is_xac_thuc') === '0' ? 'selected' : '' }}>Chưa có Tick Xanh</option>
            </select>

            {{-- Featured Filter --}}
            <select name="is_noi_bat" class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-medium outline-none focus:border-amber-500 bg-white text-slate-700">
                <option value="">-- Nổi bật --</option>
                <option value="1" {{ request('is_noi_bat') === '1' ? 'selected' : '' }}>Quán Nổi Bật</option>
                <option value="0" {{ request('is_noi_bat') === '0' ? 'selected' : '' }}>Quán Thường</option>
            </select>

            {{-- Submit Filter --}}
            <button type="submit" class="btn-action-primary-glow">
                <span class="material-symbols-outlined text-[16px]">filter_alt</span> 
                <span>Lọc Dữ Liệu</span>
            </button>

            {{-- Reset Filter Button --}}
            @if(request()->hasAny(['q', 'status', 'loai_hinh', 'is_xac_thuc', 'is_noi_bat']))
                <a href="{{ route('admin.quan.index') }}" class="btn-action-secondary" title="Xóa toàn bộ bộ lọc">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    <span>Xóa lọc</span>
                </a>
            @endif
        </form>
    </div>

    {{-- Venues Table --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="w-full overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse text-xs table-fixed-enterprise">
                <colgroup>
                    <col style="width: 37%;">
                    <col style="width: 25%;">
                    <col style="width: 38%;">
                    <col style="width: 95px;">
                    <col style="width: 170px;">
                </colgroup>
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-2.5 px-2.5 sm:px-3">TÊN QUÁN & LOẠI HÌNH</th>
                        <th class="py-2.5 px-2.5 sm:px-3">CHỦ SỞ HỮU & LIÊN HỆ</th>
                        <th class="py-2.5 px-2.5 sm:px-3">ĐỊA CHỈ & GIÁ</th>
                        <th class="py-2.5 px-1.5 sm:px-2 text-center" style="width: 95px;">TRẠNG THÁI</th>
                        <th class="py-2.5 px-1.5 sm:px-2 text-right whitespace-nowrap" style="width: 170px;">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($quanList as $quan)
                        <tr id="quan-mgmt-row-{{ $quan->id }}" class="hover:bg-slate-50/80 transition-colors {{ $quan->trashed() ? 'bg-rose-50/30' : '' }}">
                            {{-- 1. Tên Quán & Loại hình --}}
                            <td class="py-2.5 px-2.5 sm:px-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <img src="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=100&q=80' }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=100&q=80';" class="w-9 h-9 rounded-lg object-cover border border-slate-200 shrink-0" alt="{{ $quan->ten_quan }}" />
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-900 leading-tight flex items-center gap-1 cell-wrap-2line">
                                            <span>{{ $quan->ten_quan }}</span>
                                            <span id="quan-verified-badge-{{ $quan->id }}" class="material-symbols-outlined text-blue-500 text-[14px] shrink-0 {{ $quan->is_xac_thuc ? '' : 'hidden' }}" title="Đã có Tick Xanh xác thực" style="font-variation-settings: 'FILL' 1;">verified</span>
                                            <span id="quan-featured-badge-{{ $quan->id }}" class="material-symbols-outlined text-amber-500 text-[14px] shrink-0 {{ $quan->is_noi_bat ? '' : 'hidden' }}" title="Quán Nổi Bật" style="font-variation-settings: 'FILL' 1;">star</span>
                                        </div>
                                        <div class="flex items-center gap-1 mt-1 min-w-0">
                                            <span class="inline-block bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded text-[10px] font-semibold border border-amber-200/60 truncate max-w-[120px]">
                                                {{ $quan->loai_hinh_kinh_doanh ?: 'Nhà hàng' }}
                                            </span>
                                            <a href="{{ route('quan.detail', ['slug' => $quan->slug]) }}" target="_blank" class="text-slate-400 hover:text-amber-600 inline-flex items-center shrink-0" title="Xem trên web">
                                                <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- 2. Chủ sở hữu & Liên hệ --}}
                            <td class="py-2.5 px-2.5 sm:px-3">
                                <p class="font-bold text-slate-800 cell-wrap-1line">{{ $quan->chuQuan?->ho_ten ?: 'Quản Trị Viên (Admin)' }}</p>
                                <p class="text-[11px] text-slate-400 font-mono leading-tight truncate">{{ $quan->chuQuan?->email ?: 'admin@quanmoi.com' }}</p>
                                <p class="text-[11px] text-slate-600 mt-0.5 flex items-center gap-1 truncate">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400 shrink-0">call</span>
                                    <span class="truncate">{{ $quan->so_dien_thoai ?: 'Chưa cập nhật' }}</span>
                                </p>
                            </td>

                            {{-- 3. Địa chỉ & Giá --}}
                            <td class="py-2.5 px-2.5 sm:px-3 text-slate-600">
                                <p class="font-medium cell-wrap-2line text-slate-700" title="{{ $quan->dia_chi_chi_tiet }}">{{ $quan->dia_chi_chi_tiet ?: 'Chưa có địa chỉ chi tiết' }}</p>
                                <p class="text-[11px] text-slate-400 cell-wrap-1line">{{ $quan->ten_quan_huyen ?: 'Quận/Huyện' }}, {{ $quan->ten_tinh_thanh ?: 'Tỉnh/TP' }}</p>
                                @if($quan->gia_nho_nhat || $quan->gia_lon_nhat)
                                    <p class="text-[10px] text-amber-700 font-bold mt-0.5 truncate">
                                        {{ number_format($quan->gia_nho_nhat ?? 0, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat ?? 0, 0, ',', '.') }}đ
                                    </p>
                                @endif
                            </td>

                            {{-- 4. Trạng thái & Huy hiệu --}}
                            <td class="py-2.5 px-1.5 sm:px-2 text-center">
                                @if($quan->trashed())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-100 text-rose-700 border border-rose-200 whitespace-nowrap">Đã xóa</span>
                                @elseif($quan->trang_thai === 'chua_duyet')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-100 text-amber-800 border border-amber-200 whitespace-nowrap">⏳ Chờ duyệt</span>
                                @elseif($quan->trang_thai === 'bi_khoa')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-slate-200 text-slate-700 border border-slate-300 whitespace-nowrap">🔒 Bị khóa</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200/80 whitespace-nowrap">• Đã duyệt</span>
                                @endif
                            </td>

                            {{-- 5. Thao tác (5 nút màu sắc hiện đại, vừa khít 100%, không cần kéo ngang) --}}
                            <td class="py-2.5 px-1.5 sm:px-2 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1 flex-nowrap shrink-0">
                                    {{-- 1. Quick View Modal Button (Modern Indigo) --}}
                                    <button type="button" onclick="adminQuickViewQuan('{{ $quan->id }}')" class="w-7 h-7 flex items-center justify-center bg-indigo-50 border border-indigo-200 text-indigo-600 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 rounded-lg transition-all shadow-2xs hover:scale-110 active:scale-95 cursor-pointer shrink-0" title="Xem nhanh thông tin quán">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    </button>

                                    @if(!$quan->trashed())
                                        {{-- 2. Toggle Tick Xanh (Vibrant Blue With Confirmation) --}}
                                        <button type="button" 
                                                data-verified="{{ $quan->is_xac_thuc ? '1' : '0' }}"
                                                onclick="adminToggleQuanVerification('{{ $quan->id }}', '{{ addslashes($quan->ten_quan) }}', this)" 
                                                class="w-7 h-7 flex items-center justify-center rounded-lg transition-all hover:scale-110 active:scale-95 cursor-pointer shrink-0 {{ $quan->is_xac_thuc ? 'bg-blue-500 text-white border border-blue-500 shadow-xs hover:bg-blue-600' : 'bg-slate-100 text-slate-400 border border-slate-200/90 shadow-2xs hover:bg-blue-50 hover:text-blue-600 hover:border-blue-300' }}" 
                                                title="{{ $quan->is_xac_thuc ? 'Gỡ Tick Xanh Xác Thực' : 'Cấp Tick Xanh Xác Thực' }}">
                                            <span class="material-symbols-outlined text-[16px]" {!! $quan->is_xac_thuc ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>check_circle</span>
                                        </button>

                                        {{-- 3. Toggle Nổi Bật (Vibrant Gold/Amber With Confirmation) --}}
                                        <button type="button" 
                                                data-featured="{{ $quan->is_noi_bat ? '1' : '0' }}"
                                                onclick="adminToggleQuanFeatured('{{ $quan->id }}', '{{ addslashes($quan->ten_quan) }}', this)" 
                                                class="w-7 h-7 flex items-center justify-center rounded-lg transition-all hover:scale-110 active:scale-95 cursor-pointer shrink-0 {{ $quan->is_noi_bat ? 'bg-amber-500 text-white border border-amber-500 shadow-xs hover:bg-amber-600' : 'bg-slate-100 text-slate-400 border border-slate-200/90 shadow-2xs hover:bg-amber-50 hover:text-amber-600 hover:border-amber-300' }}" 
                                                title="{{ $quan->is_noi_bat ? 'Gỡ khỏi danh sách nổi bật' : 'Đánh dấu quán nổi bật' }}">
                                            <span class="material-symbols-outlined text-[16px]" {!! $quan->is_noi_bat ? 'style="font-variation-settings: \'FILL\' 1;"' : '' !!}>star</span>
                                        </button>
                                    @endif

                                    {{-- 4. Edit Button (Modern Emerald) --}}
                                    <a href="{{ route('admin.quan.edit', $quan->id) }}" class="w-7 h-7 flex items-center justify-center bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 rounded-lg transition-all shadow-2xs hover:scale-110 active:scale-95 shrink-0" title="Chỉnh sửa thông tin quán">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </a>

                                    @if($quan->trashed())
                                        {{-- Restore (Teal / Emerald) --}}
                                        <button type="button" onclick="adminRestoreQuan('{{ $quan->id }}', '{{ addslashes($quan->ten_quan) }}')" class="w-7 h-7 flex items-center justify-center bg-emerald-500 border border-emerald-500 text-white hover:bg-emerald-600 rounded-lg transition-all shadow-xs hover:scale-110 active:scale-95 cursor-pointer shrink-0" title="Khôi phục quán">
                                            <span class="material-symbols-outlined text-[16px]">restore_from_trash</span>
                                        </button>
                                        {{-- Force Delete (Solid Rose) --}}
                                        <button type="button" onclick="adminForceDeleteQuan('{{ $quan->id }}', '{{ addslashes($quan->ten_quan) }}')" class="w-7 h-7 flex items-center justify-center bg-rose-600 border border-rose-600 text-white hover:bg-rose-700 rounded-lg transition-all shadow-xs hover:scale-110 active:scale-95 cursor-pointer shrink-0" title="Xóa vĩnh viễn khỏi CSDL">
                                            <span class="material-symbols-outlined text-[16px]">delete_forever</span>
                                        </button>
                                    @else
                                        {{-- 5. Soft Delete (Modern Rose / Red) --}}
                                        <button type="button" onclick="adminSoftDeleteQuan('{{ $quan->id }}', '{{ addslashes($quan->ten_quan) }}')" class="w-7 h-7 flex items-center justify-center bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-600 hover:text-white hover:border-rose-600 rounded-lg transition-all shadow-2xs hover:scale-110 active:scale-95 cursor-pointer shrink-0" title="Xóa tạm vào thùng rác">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">storefront</span>
                                <p class="text-xs font-semibold text-slate-500">Không tìm thấy địa điểm quán nào phù hợp.</p>
                                @if(request()->hasAny(['q', 'status', 'loai_hinh', 'is_xac_thuc', 'is_noi_bat']))
                                    <a href="{{ route('admin.quan.index') }}" class="text-xs font-bold text-amber-600 hover:underline mt-2 inline-block">
                                        Xóa bộ lọc tìm kiếm
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination & Summary --}}
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <p class="text-xs text-slate-500">
                Hiển thị từ <span class="font-bold text-slate-700">{{ $quanList->firstItem() ?? 0 }}</span> đến <span class="font-bold text-slate-700">{{ $quanList->lastItem() ?? 0 }}</span> trong tổng số <span class="font-bold text-slate-700">{{ $quanList->total() }}</span> quán
            </p>
            <div>
                {{ $quanList->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Quick View Modal --}}
<div id="quick-view-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-200 transform transition-all">
        <div id="quick-view-modal-content" class="p-6 space-y-4">
            <div class="flex items-center justify-center py-12">
                <span class="material-symbols-outlined text-3xl text-amber-500 animate-spin">progress_activity</span>
            </div>
        </div>
    </div>
</div>

<script>
    // Quick View Modal handler
    function adminQuickViewQuan(quanId) {
        const modal = document.getElementById('quick-view-modal');
        const content = document.getElementById('quick-view-modal-content');
        if (!modal || !content) return;

        modal.classList.remove('hidden');
        content.innerHTML = `
            <div class="flex items-center justify-center py-12">
                <span class="material-symbols-outlined text-3xl text-amber-500 animate-spin">progress_activity</span>
            </div>
        `;

        fetch(`/admin/quan/${quanId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (!res.success || !res.data) throw new Error('Không thể tải thông tin quán.');
            const q = res.data;

            content.innerHTML = `
                <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-base text-slate-800">${q.ten_quan}</h3>
                        ${q.is_xac_thuc ? '<span class="material-symbols-outlined text-blue-500 text-[18px]" style="font-variation-settings: \'FILL\' 1;">verified</span>' : ''}
                        ${q.is_noi_bat ? '<span class="material-symbols-outlined text-amber-500 text-[18px]" style="font-variation-settings: \'FILL\' 1;">star</span>' : ''}
                    </div>
                    <button type="button" onclick="closeQuickViewModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>

                <div class="w-full h-44 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                    <img src="${q.anh_bia_url}" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80';" class="w-full h-full object-cover" alt="${q.ten_quan}">
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-3.5 rounded-xl border border-slate-200/60">
                    <div>
                        <span class="text-slate-400 font-semibold block">Loại hình:</span>
                        <span class="font-bold text-slate-700">${q.loai_hinh_kinh_doanh}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block">Chủ sở hữu:</span>
                        <span class="font-bold text-slate-700">${q.chu_quan ? q.chu_quan.ho_ten : 'Quản Trị Viên'}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block">Điện thoại liên hệ:</span>
                        <span class="font-bold text-slate-700">${q.so_dien_thoai || 'Chưa cập nhật'}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block">Giờ mở cửa:</span>
                        <span class="font-bold text-slate-700">${q.gio_mo_cua} - ${q.gio_dong_cua}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block">Bảng giá:</span>
                        <span class="font-bold text-amber-700">${q.gia_nho_nhat} - ${q.gia_lon_nhat}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block">Trạng thái:</span>
                        <span class="font-bold ${q.trang_thai === 'da_duyet' ? 'text-emerald-700' : (q.trang_thai === 'bi_khoa' ? 'text-slate-600' : 'text-amber-700')}">
                            ${q.trang_thai === 'da_duyet' ? '• Đã duyệt' : (q.trang_thai === 'bi_khoa' ? '🔒 Bị khóa' : '⏳ Chờ duyệt')}
                        </span>
                    </div>
                </div>

                <div class="text-xs">
                    <span class="text-slate-400 font-semibold block mb-0.5">Địa chỉ chi tiết:</span>
                    <p class="font-medium text-slate-700">${q.dia_chi_day_du}</p>
                </div>

                <div class="text-xs">
                    <span class="text-slate-400 font-semibold block mb-0.5">Mô tả:</span>
                    <p class="text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100">${q.mo_ta}</p>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                    <div class="flex items-center gap-3 text-slate-500">
                        <span>📸 ${q.hinh_anh_count} ảnh</span>
                        <span>🍽️ ${q.menu_count} món</span>
                        <span>⭐ ${q.danh_gia_count} đánh giá</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="${q.detail_url}" target="_blank" class="btn-enterprise btn-enterprise-secondary text-xs">
                            <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                            <span>Xem trên Web</span>
                        </a>
                        <a href="/admin/quan/${q.id}/edit" class="bg-[#F59E0B] hover:bg-[#D97706] text-white px-3.5 py-1.5 rounded-lg font-bold text-xs transition-all shadow-xs inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">edit</span>
                            <span>Chỉnh sửa</span>
                        </a>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            content.innerHTML = `
                <div class="p-6 text-center text-xs text-rose-600">
                    <span class="material-symbols-outlined text-3xl text-rose-500 block mb-2">error</span>
                    <p>${err.message || 'Có lỗi xảy ra khi tải dữ liệu quán.'}</p>
                    <button type="button" onclick="closeQuickViewModal()" class="btn-enterprise btn-enterprise-secondary mt-3">Đóng</button>
                </div>
            `;
        });
    }

    function closeQuickViewModal() {
        document.getElementById('quick-view-modal')?.classList.add('hidden');
    }

    // Close modal on click outside
    document.getElementById('quick-view-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeQuickViewModal();
    });

    // Helper to update venue count pills in real-time
    function updateVenueCounts(counts) {
        if (!counts) return;
        ['all', 'da_duyet', 'chua_duyet', 'bi_khoa', 'soft_deleted'].forEach(key => {
            const el = document.getElementById(`quan-count-${key}`);
            if (el && counts[key] !== undefined) {
                if (el.textContent != counts[key]) {
                    el.textContent = counts[key];
                    el.classList.add('scale-125', 'text-amber-500', 'transition-all');
                    setTimeout(() => el.classList.remove('scale-125', 'text-amber-500'), 500);
                }
            }
        });
    }

    // Verification toggle With Confirmation Modal
    function adminToggleQuanVerification(quanId, quanName, btn) {
        const isVerified = btn.getAttribute('data-verified') === '1';
        const actionTitle = isVerified ? 'Gỡ Tick Xanh Xác Thực' : 'Cấp Tick Xanh Xác Thực';
        const actionMsg = isVerified 
            ? `Bạn có chắc chắn muốn <b class="text-rose-600 font-bold">gỡ bỏ Tick Xanh Xác Thực</b> của quán <b class="text-slate-900 font-bold">"${quanName}"</b> không?`
            : `Bạn có chắc chắn muốn <b class="text-blue-600 font-bold">cấp Tick Xanh Xác Thực</b> cho quán <b class="text-slate-900 font-bold">"${quanName}"</b> không?`;

        openConfirmModal({
            title: actionTitle,
            message: actionMsg,
            icon: 'verified',
            iconBg: isVerified ? 'bg-rose-100 text-rose-600 border border-rose-200' : 'bg-blue-100 text-blue-600 border border-blue-200',
            confirmText: isVerified ? 'Xác Nhận Gỡ Tick' : 'Cấp Tick Xanh',
            confirmClass: isVerified ? 'modal-btn-danger' : 'modal-btn-primary',
            confirmIcon: isVerified ? 'remove_moderator' : 'verified',
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const iconSpan = btn.querySelector('.material-symbols-outlined');
                const badge = document.getElementById(`quan-verified-badge-${quanId}`);

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

                    btn.setAttribute('data-verified', data.is_xac_thuc ? '1' : '0');
                    if (data.is_xac_thuc) {
                        btn.className = 'w-7 h-7 flex items-center justify-center rounded-lg transition-all shadow-xs hover:scale-110 active:scale-95 cursor-pointer shrink-0 bg-blue-500 text-white border border-blue-500 hover:bg-blue-600';
                        btn.title = 'Gỡ Tick Xanh Xác Thực';
                        if (iconSpan) iconSpan.style.fontVariationSettings = "'FILL' 1";
                        if (badge) badge.classList.remove('hidden');
                    } else {
                        btn.className = 'w-7 h-7 flex items-center justify-center rounded-lg transition-all shadow-2xs hover:scale-110 active:scale-95 cursor-pointer shrink-0 bg-slate-100 text-slate-400 border border-slate-200/90 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-300';
                        btn.title = 'Cấp Tick Xanh Xác Thực';
                        if (iconSpan) iconSpan.style.fontVariationSettings = "'FILL' 0";
                        if (badge) badge.classList.add('hidden');
                    }
                })
                .catch(err => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Lỗi Xác Thực', err.message || 'Không thể thay đổi trạng thái xác thực quán.', '', 'error', 'bg-rose-500');
                    }
                });
            }
        });
    }

    // Featured toggle With Confirmation Modal
    function adminToggleQuanFeatured(quanId, quanName, btn) {
        const isFeatured = btn.getAttribute('data-featured') === '1';
        const actionTitle = isFeatured ? 'Gỡ Khỏi Danh Sách Nổi Bật' : 'Đánh Dấu Quán Nổi Bật';
        const actionMsg = isFeatured 
            ? `Bạn có chắc chắn muốn đưa quán <b class="text-slate-900 font-bold">"${quanName}"</b> về trạng thái quán thông thường?`
            : `Bạn có chắc chắn muốn đưa quán <b class="text-slate-900 font-bold">"${quanName}"</b> lên danh sách <b class="text-amber-600 font-bold">Quán Nổi Bật VIP</b>?`;

        openConfirmModal({
            title: actionTitle,
            message: actionMsg,
            icon: isFeatured ? 'star_half' : 'star',
            iconBg: isFeatured ? 'bg-slate-100 text-slate-700 border border-slate-300' : 'bg-amber-100 text-amber-700 border border-amber-200',
            confirmText: isFeatured ? 'Xác Nhận Gỡ Nổi Bật' : 'Đưa Vào Nổi Bật',
            confirmClass: isFeatured ? 'modal-btn-dark' : 'modal-btn-warning',
            confirmIcon: isFeatured ? 'star_half' : 'star',
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const iconSpan = btn.querySelector('.material-symbols-outlined');
                const badge = document.getElementById(`quan-featured-badge-${quanId}`);

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

                    btn.setAttribute('data-featured', data.is_noi_bat ? '1' : '0');
                    if (data.is_noi_bat) {
                        btn.className = 'w-7 h-7 flex items-center justify-center rounded-lg transition-all shadow-xs hover:scale-110 active:scale-95 cursor-pointer shrink-0 bg-amber-500 text-white border border-amber-500 hover:bg-amber-600';
                        btn.title = 'Gỡ khỏi danh sách nổi bật';
                        if (iconSpan) iconSpan.style.fontVariationSettings = "'FILL' 1";
                        if (badge) badge.classList.remove('hidden');
                    } else {
                        btn.className = 'w-7 h-7 flex items-center justify-center rounded-lg transition-all shadow-2xs hover:scale-110 active:scale-95 cursor-pointer shrink-0 bg-slate-100 text-slate-400 border border-slate-200/90 hover:bg-amber-50 hover:text-amber-600 hover:border-amber-300';
                        btn.title = 'Đánh dấu quán nổi bật';
                        if (iconSpan) iconSpan.style.fontVariationSettings = "'FILL' 0";
                        if (badge) badge.classList.add('hidden');
                    }
                })
                .catch(err => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Lỗi Nổi Bật', err.message || 'Không thể cập nhật trạng thái nổi bật.', '', 'error', 'bg-rose-500');
                    }
                });
            }
        });
    }

    // Soft Delete With Realtime Count Update
    function adminSoftDeleteQuan(quanId, quanName) {
        openConfirmModal({
            title: 'Xóa Tạm Quán Ăn',
            message: `Bạn có chắc chắn muốn xóa tạm quán <b class="text-slate-900 font-bold">"${quanName}"</b> vào thùng rác không?`,
            icon: 'delete',
            iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
            confirmText: 'Xác Nhận Xóa Tạm',
            confirmClass: 'modal-btn-danger',
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
                    if (data.counts) {
                        updateVenueCounts(data.counts);
                    }
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

    // Restore With Realtime Count Update
    function adminRestoreQuan(quanId, quanName) {
        openConfirmModal({
            title: 'Khôi Phục Quán Ăn',
            message: `Bạn có chắc chắn muốn khôi phục quán <b class="text-slate-900 font-bold">"${quanName}"</b> về hoạt động bình thường không?`,
            icon: 'restore_from_trash',
            iconBg: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            confirmText: 'Khôi Phục Quán',
            confirmClass: 'modal-btn-success',
            confirmIcon: 'check_circle',
            onConfirm: () => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const row = document.getElementById(`quan-mgmt-row-${quanId}`);

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
                    if (data.counts) {
                        updateVenueCounts(data.counts);
                    }
                    if (row) {
                        row.style.transition = 'all 0.35s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';
                        setTimeout(() => row.remove(), 350);
                    }
                })
                .catch(err => {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Lỗi Khôi Phục', err.message || 'Không thể khôi phục quán.', '', 'error', 'bg-rose-500');
                    }
                });
            }
        });
    }

    // Force Delete With Realtime Count Update
    function adminForceDeleteQuan(quanId, quanName) {
        openConfirmModal({
            title: 'XÓA VĨNH VIỄN QUÁN ĂN',
            message: `CẢNH BÁO: Thao tác này sẽ xóa vĩnh viễn quán <b class="text-slate-900 font-bold">"${quanName}"</b> khỏi cơ sở dữ liệu. Không thể hoàn tác!<br><span class="text-rose-600 text-[11px] font-bold">Bạn có chắc chắn muốn xóa vĩnh viễn không?</span>`,
            icon: 'delete_forever',
            iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
            confirmText: 'Xác Nhận Xóa Vĩnh Viễn',
            confirmClass: 'modal-btn-danger',
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
                    if (data.counts) {
                        updateVenueCounts(data.counts);
                    }
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
