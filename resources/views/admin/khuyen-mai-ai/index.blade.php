@extends('admin.layout')

@section('title', 'Phê Duyệt & Quản Lý Voucher - Quán Mới Admin')
@section('page-title', 'Phê Duyệt & Quản Lý Voucher')

@section('content')
<div class="space-y-6">

    {{-- Tiêu đề trang & Thanh điều khiển --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500 text-3xl">local_activity</span>
                Phê Duyệt Voucher & Khuyến Mãi
            </h1>
            <p class="text-xs text-slate-500 mt-1">Kiểm duyệt các mã ưu đãi/voucher do chủ quán tạo trước khi kích hoạt hiển thị công khai trên hệ thống Quán Mới.</p>
        </div>

        <div class="flex items-center gap-2.5">
            {{-- Đèn báo Realtime --}}
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Thời gian thực</span>
            </div>

            {{-- Nút Làm mới --}}
            <button type="button" onclick="window.location.reload()" class="px-3.5 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer shadow-2xs" title="Nhấn để tải lại dữ liệu mới nhất">
                <span class="material-symbols-outlined text-[16px]">sync</span>
                <span>Làm mới</span>
            </button>
        </div>
    </div>

    {{-- 4 Thẻ chỉ số nhanh --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">pending_actions</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Chờ phê duyệt</p>
                <p id="stat-pending-promos" class="text-xl font-black text-amber-600">{{ number_format($pendingCount) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">check_circle</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Đã phê duyệt</p>
                <p id="stat-approved-promos" class="text-xl font-black text-emerald-600">{{ number_format($approvedCount) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">cancel</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Bị từ chối</p>
                <p id="stat-rejected-promos" class="text-xl font-black text-rose-600">{{ number_format($rejectedCount) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">confirmation_number</span>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng voucher</p>
                <p id="stat-total-promos" class="text-xl font-black text-slate-900">{{ number_format($totalCount) }}</p>
            </div>
        </div>
    </div>

    {{-- KHỐI DANH SÁCH & PHÊ DUYỆT VOUCHER --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                    <h3 class="font-black text-slate-900 text-lg tracking-tight">Hàng Đợi & Danh Sách Voucher</h3>
                    <span id="admin-promo-count-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                        {{ $promotionsCount }} ưu đãi
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Duyệt hoặc từ chối voucher do chủ quán tạo; quản lý trạng thái bật/tắt của toàn bộ ưu đãi.</p>
            </div>
            <div>
                <form method="GET" action="{{ route('admin.khuyen-mai-ai.index') }}" class="admin-search-wrapper w-full sm:w-72 shadow-2xs flex items-center gap-1.5">
                    @if(!empty($filterStatus))
                        <input type="hidden" name="trang_thai_duyet" value="{{ $filterStatus }}">
                    @endif
                    <button type="submit" title="Tìm kiếm qua Elasticsearch" class="text-slate-400 hover:text-purple-600 transition flex items-center cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                    </button>
                    <input type="text" id="admin-search-promo" name="q" value="{{ request('q') }}" onkeyup="filterAdminPromotions()" placeholder="Tìm mã voucher, tên quán, món..."
                           class="w-full bg-transparent border-0 p-0 text-xs font-medium outline-none text-slate-800 placeholder:text-slate-400">
                    @if(request()->filled('q'))
                        <a href="{{ route('admin.khuyen-mai-ai.index', !empty($filterStatus) ? ['trang_thai_duyet' => $filterStatus] : []) }}" title="Xóa tìm kiếm" class="text-slate-400 hover:text-rose-500 flex items-center">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Thanh Tabs Lọc Trạng Thái Phê Duyệt --}}
        <div class="flex flex-wrap items-center gap-2 pb-4 mb-5 border-b border-slate-100" id="promo-status-tabs">
            <a href="{{ route('admin.khuyen-mai-ai.index') }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ empty($filterStatus) ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <span class="material-symbols-outlined text-[16px]">list_alt</span>
                <span>Tất cả</span>
                <span id="tab-count-all" class="px-1.5 py-0.2 rounded-full text-[10px] {{ empty($filterStatus) ? 'bg-purple-800 text-purple-100' : 'bg-slate-200 text-slate-700' }}">{{ $totalCount }}</span>
            </a>

            <a href="{{ route('admin.khuyen-mai-ai.index', ['trang_thai_duyet' => 'cho_duyet']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $filterStatus === 'cho_duyet' ? 'bg-amber-500 text-white shadow-xs' : 'bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200/60' }}">
                <span class="w-2 h-2 rounded-full bg-amber-400 {{ $pendingCount > 0 ? 'animate-ping' : '' }}"></span>
                <span>🟡 Chờ duyệt</span>
                <span id="tab-count-pending" class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filterStatus === 'cho_duyet' ? 'bg-amber-700 text-white' : 'bg-amber-200 text-amber-900 font-black' }}">{{ $pendingCount }}</span>
            </a>

            <a href="{{ route('admin.khuyen-mai-ai.index', ['trang_thai_duyet' => 'da_duyet']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $filterStatus === 'da_duyet' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200/60' }}">
                <span class="material-symbols-outlined text-[16px]">verified</span>
                <span>🟢 Đã duyệt</span>
                <span id="tab-count-approved" class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filterStatus === 'da_duyet' ? 'bg-emerald-800 text-emerald-100' : 'bg-emerald-100 text-emerald-800' }}">{{ $approvedCount }}</span>
            </a>

            <a href="{{ route('admin.khuyen-mai-ai.index', ['trang_thai_duyet' => 'tu_choi']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $filterStatus === 'tu_choi' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200/60' }}">
                <span class="material-symbols-outlined text-[16px]">cancel</span>
                <span>🔴 Từ chối</span>
                <span id="tab-count-rejected" class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filterStatus === 'tu_choi' ? 'bg-rose-800 text-rose-100' : 'bg-rose-100 text-rose-800' }}">{{ $rejectedCount }}</span>
            </a>
        </div>

        {{-- Danh Sách Thẻ Voucher --}}
        <div id="admin-promo-list" class="space-y-3">
            @forelse($promotions as $promo)
                <div class="p-4 rounded-2xl border transition-all flex flex-col md:flex-row md:items-center justify-between gap-4 {{ $promo->trang_thai_duyet === 'cho_duyet' ? 'border-amber-300 ring-2 ring-amber-400/30 bg-amber-50/20' : ($promo->trang_thai_duyet === 'tu_choi' ? 'border-rose-200 bg-rose-50/10' : 'border-slate-200/80 bg-white hover:border-purple-300 shadow-2xs') }}"
                     id="admin-promo-card-{{ $promo->id }}"
                     data-search="{{ mb_strtolower(($promo->quan?->ten_quan ?? '') . ' ' . ($promo->ma_voucher ?? '') . ' ' . $promo->tieu_de) }}">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                                🏬 {{ $promo->quan?->ten_quan ?? 'Quán Mới' }}
                            </span>
                            @if($promo->ma_voucher)
                                <span class="px-2.5 py-1 rounded-lg font-mono font-black text-xs bg-purple-50 text-purple-700 border border-dashed border-purple-300 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">confirmation_number</span>
                                    {{ $promo->ma_voucher }}
                                </span>
                            @endif
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                {{ $promo->gia_tri_khuyen_mai ?: ($promo->gia_tri_giam ?: 'Ưu đãi') }}
                            </span>

                            {{-- Badge Trạng Thái Phê Duyệt --}}
                            <span id="admin-status-badge-{{ $promo->id }}">
                                @if($promo->trang_thai_duyet === 'cho_duyet')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white shadow-xs inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        🟡 Chờ Admin duyệt
                                    </span>
                                @elseif($promo->trang_thai_duyet === 'tu_choi')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">block</span>
                                        🔴 Bị từ chối
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $promo->trang_thai ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                        🟢 Đã duyệt ({{ $promo->trang_thai ? 'Đang bật' : 'Tạm dừng' }})
                                    </span>
                                @endif
                            </span>
                        </div>

                        <h4 class="font-bold text-slate-900 text-sm truncate">{{ $promo->tieu_de }}</h4>

                        @if($promo->trang_thai_duyet === 'tu_choi' && $promo->ly_do_tu_choi)
                            <p class="text-[11px] text-rose-600 mt-0.5 font-medium flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">info</span>
                                <span>Lý do từ chối: {{ $promo->ly_do_tu_choi }}</span>
                            </p>
                        @endif

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 mt-1">
                            <span>🍽️ Món: <strong>{{ $promo->mon_ap_dung ?: 'Toàn bộ menu' }}</strong></span>
                            @if($promo->ngay_bat_dau || $promo->ngay_ket_thuc)
                                <span>⏰ Hạn dùng: <strong>{{ $promo->ngay_bat_dau ? $promo->ngay_bat_dau->format('d/m/Y') : 'Từ nay' }} - {{ $promo->ngay_ket_thuc ? $promo->ngay_ket_thuc->format('d/m/Y') : 'Vô thời hạn' }}</strong></span>
                            @endif
                            <span class="text-[11px] text-slate-400">📅 Gửi: {{ $promo->created_at ? $promo->created_at->format('d/m/Y H:i') : '' }}</span>
                        </div>
                    </div>

                    {{-- Khối Thao Tác Phê Duyệt / Từ Chối (Không có icon thùng rác) --}}
                    <div id="action-buttons-{{ $promo->id }}" class="flex items-center gap-2 shrink-0">
                        @if($promo->trang_thai_duyet === 'cho_duyet')
                            <button type="button"
                                    id="btn-approve-{{ $promo->id }}"
                                    onclick="approveAdminPromotion('{{ $promo->id }}')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                <span>Phê duyệt</span>
                            </button>
                            <button type="button"
                                    id="btn-reject-{{ $promo->id }}"
                                    onclick="openRejectPromotionModal('{{ $promo->id }}', '{{ addslashes($promo->tieu_de) }}', '{{ addslashes($promo->quan?->ten_quan ?? '') }}')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">close</span>
                                <span>Từ chối</span>
                            </button>
                        @elseif($promo->trang_thai_duyet === 'tu_choi')
                            <button type="button"
                                    id="btn-approve-{{ $promo->id }}"
                                    onclick="approveAdminPromotion('{{ $promo->id }}')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                <span>Phê duyệt lại</span>
                            </button>
                        @else
                            {{-- Đã duyệt: Có thể Bật/Tắt hoặc Từ chối nếu cần gỡ bỏ --}}
                            <button type="button" onclick="toggleAdminPromotion('{{ $promo->id }}')"
                                    id="btn-admin-toggle-{{ $promo->id }}"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $promo->trang_thai ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ $promo->trang_thai ? 'Bật' : 'Tắt' }}
                            </button>
                            <button type="button"
                                    id="btn-reject-{{ $promo->id }}"
                                    onclick="openRejectPromotionModal('{{ $promo->id }}', '{{ addslashes($promo->tieu_de) }}', '{{ addslashes($promo->quan?->ten_quan ?? '') }}')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">close</span>
                                <span>Từ chối</span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div id="admin-promo-empty" class="py-12 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">confirmation_number</span>
                    @if($filterStatus === 'cho_duyet')
                        <h4 class="text-sm font-bold text-slate-700 mb-1">Không có voucher nào đang chờ phê duyệt</h4>
                        <p class="text-slate-400">Khi chủ quán gửi voucher ưu đãi mới, yêu cầu sẽ xuất hiện tại đây để bạn kiểm duyệt.</p>
                    @elseif($filterStatus === 'da_duyet')
                        <h4 class="text-sm font-bold text-slate-700 mb-1">Chưa có voucher nào đã duyệt</h4>
                        <p class="text-slate-400">Các voucher được phê duyệt thành công sẽ hiển thị ở đây.</p>
                    @elseif($filterStatus === 'tu_choi')
                        <h4 class="text-sm font-bold text-slate-700 mb-1">Không có voucher nào bị từ chối</h4>
                        <p class="text-slate-400">Danh sách voucher bị từ chối sẽ hiển thị tại đây kèm lý do.</p>
                    @else
                        <h4 class="text-sm font-bold text-slate-700 mb-1">Chưa có voucher khuyến mãi nào trên hệ thống</h4>
                        <p class="text-slate-400">Khi chủ các quán ăn tạo voucher, danh sách sẽ hiển thị tại đây để bạn quản lý.</p>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- MODAL TỪ CHỐI DUYỆT VOUCHER --}}
<div id="reject-promo-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" onclick="if(event.target === this) closeRejectPromotionModal()">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-rose-600 flex items-center gap-2">
                <span class="material-symbols-outlined text-rose-600">block</span>
                Từ Chối Duyệt Voucher
            </h3>
            <button type="button" onclick="closeRejectPromotionModal()" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <div class="space-y-3">
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                <p id="reject-promo-title" class="text-xs font-bold text-slate-800 line-clamp-2"></p>
                <p id="reject-promo-venue" class="text-[11px] font-semibold text-purple-700 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">storefront</span>
                    <span></span>
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Chọn nhanh lý do từ chối:</label>
                <div class="space-y-1.5 text-xs text-slate-600">
                    <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="quick_reject_promo_reason" value="Mức giảm giá hoặc thể lệ ưu đãi chưa rõ ràng/vượt quy định" onchange="document.getElementById('reject-promo-reason-input').value = this.value">
                        <span>Mức giảm giá hoặc thể lệ ưu đãi chưa rõ ràng</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="quick_reject_promo_reason" value="Món ăn áp dụng không thuộc thực đơn chính thức của quán" onchange="document.getElementById('reject-promo-reason-input').value = this.value">
                        <span>Món ăn áp dụng không đúng với thực đơn quán</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                        <input type="radio" name="quick_reject_promo_reason" value="Mã voucher hoặc tiêu đề chứa ký tự không phù hợp tiêu chuẩn" onchange="document.getElementById('reject-promo-reason-input').value = this.value">
                        <span>Mã voucher hoặc tiêu đề không đạt tiêu chuẩn</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nội dung phản hồi tới chủ quán:</label>
                <textarea id="reject-promo-reason-input" rows="3" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 outline-none" placeholder="Nhập lý do chi tiết từ chối..."></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeRejectPromotionModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">Hủy</button>
                <button type="button" onclick="submitRejectPromotion()" id="btn-confirm-reject-promo" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition cursor-pointer">
                    Xác Nhận Từ Chối
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // ========================================================
    // PHÊ DUYỆT & TỪ CHỐI VOUCHER (QUẢN LÝ BỞI ADMIN)
    // ========================================================

    async function approveAdminPromotion(id) {
        const btn = document.getElementById(`btn-approve-${id}`);
        if (btn) {
            btn.innerHTML = `<span class="material-symbols-outlined text-[15px] animate-spin">sync</span><span>Đang duyệt...</span>`;
            btn.disabled = true;
        }

        try {
            const res = await fetch(`/admin/khuyen-mai-ai/${id}/approve`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                const card = document.getElementById(`admin-promo-card-${id}`);
                const badge = document.getElementById(`admin-status-badge-${id}`);
                if (badge) {
                    badge.innerHTML = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">🟢 Đã duyệt (Đang bật)</span>`;
                }

                // Phát tín hiệu Realtime cho phía Chủ Quán ngay lập tức không cần load trang
                const broadcastPayload = {
                    action: 'voucher_approved',
                    status: 'da_duyet',
                    voucher_id: id,
                    quan_id: data.data ? data.data.quan_id : null,
                    ma_voucher: data.data ? data.data.ma_voucher : null,
                    tieu_de: data.data ? data.data.tieu_de : null,
                    trang_thai: true,
                    timestamp: Date.now()
                };
                if (typeof BroadcastChannel !== 'undefined') {
                    try {
                        const bc = new BroadcastChannel('quanmoi_voucher_sync');
                        bc.postMessage(broadcastPayload);
                        bc.close();
                    } catch(e) {}
                }
                try {
                    localStorage.setItem('quanmoi_voucher_event', JSON.stringify(broadcastPayload));
                } catch(e) {}

                // Cập nhật số đếm
                if (data.counts) {
                    updatePromoCountBadges(data.counts);
                }

                const urlParams = new URLSearchParams(window.location.search);
                const currentFilter = urlParams.get('trang_thai_duyet');

                // Nếu đang ở tab "Chờ duyệt", làm mờ và xóa card khỏi hàng đợi
                if (currentFilter === 'cho_duyet') {
                    if (card) {
                        card.style.transition = 'all 0.35s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            card.remove();
                            const remaining = document.querySelectorAll('#admin-promo-list > div[id^="admin-promo-card-"]');
                            if (remaining.length === 0) {
                                const list = document.getElementById('admin-promo-list');
                                if (list) {
                                    list.innerHTML = `
                                        <div class="py-12 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                            <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-2">
                                                <span class="material-symbols-outlined text-3xl">task_alt</span>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-700 mb-1">Đã phê duyệt xong tất cả voucher!</h4>
                                            <p class="text-slate-400">Không còn voucher nào trong hàng đợi chờ duyệt.</p>
                                        </div>
                                    `;
                                }
                            }
                        }, 350);
                    }
                } else if (card) {
                    // Ở tab Tất cả: bỏ viền vàng, chuyển nút sang Bật/Tắt và Từ chối
                    card.classList.remove('border-amber-300', 'ring-2', 'ring-amber-400/30', 'bg-amber-50/20');
                    card.classList.add('border-slate-200/80', 'bg-white');
                    const actions = document.getElementById(`action-buttons-${id}`);
                    if (actions) {
                        actions.innerHTML = `
                            <button type="button" onclick="toggleAdminPromotion('${id}')"
                                    id="btn-admin-toggle-${id}"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-emerald-100 text-emerald-800 hover:bg-emerald-200">
                                Bật
                            </button>
                            <button type="button"
                                    id="btn-reject-${id}"
                                    onclick="openRejectPromotionModal('${id}', '', '')"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">close</span>
                                <span>Từ chối</span>
                            </button>
                        `;
                    }
                }

                if (typeof showCenterRealtimeAlert === 'function') {
                    showCenterRealtimeAlert('Đã Phê Duyệt Voucher Thành Công', data.message || 'Mã ưu đãi đã được kích hoạt công khai.', 'check_circle', 'success');
                } else if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Phê Duyệt Thành Công', data.message || 'Voucher đã được kích hoạt.', '', 'check_circle', 'bg-emerald-600');
                }
            } else {
                alert(data.message || 'Không thể phê duyệt voucher.');
                if (btn) {
                    btn.innerHTML = `<span class="material-symbols-outlined text-[15px]">check_circle</span><span>Phê duyệt</span>`;
                    btn.disabled = false;
                }
            }
        } catch (err) {
            alert('Lỗi kết nối khi phê duyệt voucher.');
            if (btn) {
                btn.innerHTML = `<span class="material-symbols-outlined text-[15px]">check_circle</span><span>Phê duyệt</span>`;
                btn.disabled = false;
            }
        }
    }

    var currentRejectPromoId = null;

    function openRejectPromotionModal(id, title, quanName) {
        currentRejectPromoId = id;
        document.getElementById('reject-promo-title').innerText = title || 'Voucher ưu đãi';
        document.getElementById('reject-promo-venue').innerHTML = `<span class="material-symbols-outlined text-[14px]">storefront</span><span>${quanName || 'Quán của đối tác'}</span>`;
        document.getElementById('reject-promo-reason-input').value = 'Mức giảm giá hoặc thể lệ ưu đãi chưa rõ ràng/vượt quy định';

        const modal = document.getElementById('reject-promo-modal');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeRejectPromotionModal() {
        const modal = document.getElementById('reject-promo-modal');
        if (modal) modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        currentRejectPromoId = null;
    }

    async function submitRejectPromotion() {
        if (!currentRejectPromoId) return;

        const reason = document.getElementById('reject-promo-reason-input').value.trim();
        const btn = document.getElementById('btn-confirm-reject-promo');
        if (btn) {
            btn.innerHTML = '<span class="material-symbols-outlined text-[15px] animate-spin">sync</span> Đang xử lý...';
            btn.disabled = true;
        }

        try {
            const res = await fetch(`/admin/khuyen-mai-ai/${currentRejectPromoId}/reject`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ly_do: reason })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                const id = currentRejectPromoId;
                const card = document.getElementById(`admin-promo-card-${id}`);

                // Phát tín hiệu Realtime cho phía Chủ Quán ngay lập tức không cần load trang
                const broadcastPayload = {
                    action: 'voucher_rejected',
                    status: 'tu_choi',
                    voucher_id: id,
                    quan_id: data.data ? data.data.quan_id : null,
                    ly_do: reason,
                    ma_voucher: data.data ? data.data.ma_voucher : null,
                    tieu_de: data.data ? data.data.tieu_de : null,
                    trang_thai: false,
                    timestamp: Date.now()
                };
                if (typeof BroadcastChannel !== 'undefined') {
                    try {
                        const bc = new BroadcastChannel('quanmoi_voucher_sync');
                        bc.postMessage(broadcastPayload);
                        bc.close();
                    } catch(e) {}
                }
                try {
                    localStorage.setItem('quanmoi_voucher_event', JSON.stringify(broadcastPayload));
                } catch(e) {}

                if (data.counts) {
                    updatePromoCountBadges(data.counts);
                }

                closeRejectPromotionModal();

                const urlParams = new URLSearchParams(window.location.search);
                const currentFilter = urlParams.get('trang_thai_duyet');

                if (currentFilter === 'cho_duyet') {
                    if (card) {
                        card.style.transition = 'all 0.35s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            card.remove();
                            const remaining = document.querySelectorAll('#admin-promo-list > div[id^="admin-promo-card-"]');
                            if (remaining.length === 0) {
                                const list = document.getElementById('admin-promo-list');
                                if (list) {
                                    list.innerHTML = `
                                        <div class="py-12 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                            <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">confirmation_number</span>
                                            <h4 class="text-sm font-bold text-slate-700 mb-1">Không có voucher nào đang chờ phê duyệt</h4>
                                        </div>
                                    `;
                                }
                            }
                        }, 350);
                    }
                } else if (card) {
                    const badge = document.getElementById(`admin-status-badge-${id}`);
                    if (badge) {
                        badge.innerHTML = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">🔴 Bị từ chối</span>`;
                    }
                    card.classList.remove('border-amber-300', 'ring-2', 'ring-amber-400/30');
                    card.classList.add('border-rose-200', 'bg-rose-50/10');
                    const actions = document.getElementById(`action-buttons-${id}`);
                    if (actions) {
                        actions.innerHTML = `
                            <button type="button"
                                    id="btn-approve-${id}"
                                    onclick="approveAdminPromotion('${id}')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                <span>Phê duyệt lại</span>
                            </button>
                        `;
                    }
                }

                if (typeof showCenterRealtimeAlert === 'function') {
                    showCenterRealtimeAlert('Đã Từ Chối Duyệt Voucher', data.message || 'Đã gửi lý do từ chối tới chủ quán.', 'block', 'danger');
                } else if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Từ Chối Voucher', data.message || 'Đã từ chối voucher.', '', 'block', 'bg-rose-600');
                }
            } else {
                alert(data.message || 'Không thể từ chối voucher.');
            }
        } catch (err) {
            alert('Lỗi kết nối máy chủ.');
        } finally {
            if (btn) {
                btn.innerHTML = 'Xác Nhận Từ Chối';
                btn.disabled = false;
            }
        }
    }

    function updatePromoCountBadges(counts) {
        if (!counts) return;
        const tabAll = document.getElementById('tab-count-all');
        const tabPending = document.getElementById('tab-count-pending');
        const tabApproved = document.getElementById('tab-count-approved');
        const tabRejected = document.getElementById('tab-count-rejected');
        const promoBadge = document.getElementById('admin-promo-count-badge');
        const sidebarBadge = document.getElementById('sidebar-pending-vouchers-badge');

        const statPending = document.getElementById('stat-pending-promos');
        const statApproved = document.getElementById('stat-approved-promos');
        const statRejected = document.getElementById('stat-rejected-promos');
        const statTotal = document.getElementById('stat-total-promos');

        if (tabAll) tabAll.innerText = counts.total ?? 0;
        if (tabPending) tabPending.innerText = counts.pending ?? 0;
        if (tabApproved) tabApproved.innerText = counts.approved ?? 0;
        if (tabRejected) tabRejected.innerText = counts.rejected ?? 0;
        if (promoBadge) promoBadge.innerText = `${counts.total ?? 0} ưu đãi`;

        if (statPending) statPending.innerText = counts.pending ?? 0;
        if (statApproved) statApproved.innerText = counts.approved ?? 0;
        if (statRejected) statRejected.innerText = counts.rejected ?? 0;
        if (statTotal) statTotal.innerText = counts.total ?? 0;

        if (sidebarBadge) {
            const p = counts.pending ?? 0;
            sidebarBadge.innerText = p;
            if (p > 0) {
                sidebarBadge.classList.remove('hidden');
            } else {
                sidebarBadge.classList.add('hidden');
            }
        }
    }

    async function toggleAdminPromotion(id) {
        try {
            const res = await fetch(`/admin/khuyen-mai-ai/${id}/toggle`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                const btn = document.getElementById(`btn-admin-toggle-${id}`);
                const badge = document.getElementById(`admin-status-badge-${id}`);
                if (data.trang_thai) {
                    if (btn) {
                        btn.className = 'px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-emerald-100 text-emerald-800 hover:bg-emerald-200';
                        btn.innerText = 'Bật';
                    }
                    if (badge) {
                        badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
                        badge.innerText = 'Đang hoạt động';
                    }
                } else {
                    if (btn) {
                        btn.className = 'px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200';
                        btn.innerText = 'Tắt';
                    }
                    if (badge) {
                        badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500';
                        badge.innerText = 'Tạm dừng';
                    }
                }
                if (typeof showCenterRealtimeAlert === 'function') {
                    showCenterRealtimeAlert('Cập Nhật Trạng Thái Ưu Đãi', 'Trạng thái ưu đãi đã được đồng bộ vào CSDL.', 'local_activity', 'info');
                } else if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Trạng Thái Voucher', data.message || 'Đã cập nhật trạng thái', '', 'local_activity', 'bg-emerald-600');
                }
            } else {
                if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Lỗi Cập Nhật', 'Không thể thay đổi trạng thái.', '', 'error', 'bg-rose-500');
                } else {
                    alert('Không thể thay đổi trạng thái.');
                }
            }
        } catch (err) {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lỗi Kết Nối', 'Không thể kết nối máy chủ.', '', 'error', 'bg-rose-500');
            } else {
                alert('Lỗi kết nối.');
            }
        }
    }

    function filterAdminPromotions() {
        const query = (document.getElementById('admin-search-promo')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('#admin-promo-list > div[data-search]');
        cards.forEach(card => {
            const searchData = card.getAttribute('data-search') || '';
            if (!query || searchData.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
