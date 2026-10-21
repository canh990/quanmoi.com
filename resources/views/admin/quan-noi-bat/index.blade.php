@extends('admin.layout')

@section('title', 'Quản Lý Quán Nổi Bật - Quán Mới Admin')
@section('page-title', 'Quản Lý Địa Điểm Quán Nổi Bật')

@section('content')
{{-- Viewport-Fit Container: Khung nhìn cố định toàn màn hình, không cần cuộn trang chính --}}
<div class="h-[calc(100vh-5.5rem)] max-h-[calc(100vh-5.5rem)] flex flex-col gap-2 min-w-0 overflow-hidden text-slate-800">

    {{-- TOP COMMAND BAR & KPI METRICS (Nằm cố định trên cùng, hiển thị ngay khi mở trang) --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 px-2.5 py-1.5 sm:py-2 shadow-2xs flex flex-wrap items-center justify-between gap-2 shrink-0">
        {{-- Tiêu đề & Trạng thái Live Sync --}}
        <div class="flex items-center gap-2 min-w-0">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-500 via-amber-400 to-amber-300 text-slate-950 flex items-center justify-center shadow-xs shrink-0">
                <span class="material-symbols-outlined text-[20px] fill-1 text-slate-950">hotel_class</span>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <h2 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight whitespace-nowrap">Quản Lý Quán Nổi Bật</h2>
                    <span id="live-sync-badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs transition-all duration-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="live-sync-text"><span class="hidden sm:inline">Đồng bộ</span> Trang người dùng</span>
                    </span>
                </div>
                <p class="text-[10px] text-slate-500 truncate hidden md:block">Ghim hiển thị trên Slider trang chủ và top ưu tiên tìm kiếm Khám phá</p>
            </div>
        </div>

        {{-- 4 KPI Chips trực quan, nổi khối, màu sắc rõ nét --}}
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto custom-scrollbar py-0.5 shrink-0">
            {{-- KPI 1: Tổng nổi bật --}}
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-50/90 border border-amber-300/80 text-amber-950 shadow-2xs shrink-0">
                <span class="material-symbols-outlined text-[15px] text-amber-600 fill-1">star</span>
                <span class="text-[11px] font-bold text-slate-600">Nổi bật:</span>
                <span id="kpi-total-featured" class="text-xs sm:text-sm font-black text-amber-700">{{ $totalFeatured }}</span>
            </div>

            {{-- KPI 2: Đang hoạt động --}}
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-50/90 border border-emerald-300/80 text-emerald-950 shadow-2xs shrink-0">
                <span class="material-symbols-outlined text-[15px] text-emerald-600">check_circle</span>
                <span class="text-[11px] font-bold text-slate-600">Hiển thị:</span>
                <span id="kpi-active-featured" class="text-xs sm:text-sm font-black text-emerald-700">{{ $totalActiveFeatured }}</span>
            </div>

            {{-- KPI 3: Có Tick xanh --}}
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-blue-50/90 border border-blue-300/80 text-blue-950 shadow-2xs shrink-0 hidden sm:flex">
                <span class="material-symbols-outlined text-[15px] text-blue-600 fill-1">verified</span>
                <span class="text-[11px] font-bold text-slate-600">Tick xanh:</span>
                <span id="kpi-verified-featured" class="text-xs sm:text-sm font-black text-blue-700">{{ $totalVerifiedFeatured }}</span>
            </div>

            {{-- KPI 4: Lượt xem tích lũy --}}
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-purple-50/90 border border-purple-300/80 text-purple-950 shadow-2xs shrink-0 hidden md:flex">
                <span class="material-symbols-outlined text-[15px] text-purple-600">visibility</span>
                <span class="text-[11px] font-bold text-slate-600">Lượt xem:</span>
                <span class="text-xs sm:text-sm font-black text-purple-700">{{ number_format($totalViewsFeatured) }}</span>
            </div>
        </div>

        {{-- Actions Buttons: Thiết kế to rõ, màu sắc ấn tượng, độ tương phản cao --}}
        <div class="flex items-center gap-1.5 shrink-0">
            {{-- Nút mở Modal tìm thêm quán --}}
            <button type="button" onclick="openAddFeaturedModal()" class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-950 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-500 hover:from-amber-300 hover:to-amber-400 shadow-sm hover:shadow active:scale-95 transition-all flex items-center gap-1.5 cursor-pointer border border-amber-300/80" title="Mở hộp thoại tra cứu và thêm quán nổi bật">
                <span class="material-symbols-outlined text-[16px] text-slate-950 font-bold">add_circle</span>
                <span>Thêm Nổi Bật</span>
            </button>

            {{-- Nút xem trang người dùng --}}
            <a href="{{ route('quan-noi-bat') }}" target="_blank" class="px-2.5 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 hover:text-slate-900 border border-slate-300 shadow-2xs transition-all flex items-center gap-1 cursor-pointer hidden xl:inline-flex" title="Xem trực tiếp giao diện quán nổi bật trên website">
                <span class="material-symbols-outlined text-[15px] text-amber-500 font-bold">open_in_new</span>
                <span>Xem Trang Khách</span>
            </a>

            {{-- Nút làm mới danh sách --}}
            <button type="button" onclick="window.location.reload()" class="w-7.5 h-7.5 sm:w-8 sm:h-8 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 border border-slate-200 bg-white shadow-2xs transition-all flex items-center justify-center cursor-pointer" title="Tải lại trang">
                <span class="material-symbols-outlined text-[16px]">refresh</span>
            </button>
        </div>
    </div>

    {{-- MOBILE TAB SWITCHER (Chỉ hiển thị trên màn hình nhỏ < 1024px) --}}
    <div class="lg:hidden flex items-center bg-slate-200/80 p-1 rounded-xl shrink-0 text-xs font-bold">
        <button type="button" id="tab-btn-featured" onclick="switchMobileTab('featured')" class="flex-1 py-1.5 rounded-lg bg-white text-slate-900 shadow-xs flex items-center justify-center gap-1.5 transition-all">
            <span class="material-symbols-outlined text-[16px] text-amber-500 fill-1">star</span>
            <span>Đang Nổi Bật (<span id="mobile-featured-badge">{{ $featuredQuan->count() }}</span>)</span>
        </button>
        <button type="button" id="tab-btn-candidates" onclick="switchMobileTab('candidates')" class="flex-1 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 flex items-center justify-center gap-1.5 transition-all">
            <span class="material-symbols-outlined text-[16px] text-slate-500">storefront</span>
            <span>Kho Đề Cử (<span id="mobile-candidates-badge">{{ $allQuan->count() }}</span>)</span>
        </button>
    </div>

    {{-- MAIN SPLIT WORKSPACE: 2 PANEL CÂN ĐỐI, VỪA KHÍT 100% KHUNG NHÌN, KHÔNG CUỘN NGANG --}}
    <div class="flex-1 min-h-0 flex flex-col lg:flex-row gap-2.5 overflow-hidden">

        {{-- ========================================================
             PANEL TRÁI: DANH SÁCH QUÁN ĐANG ĐƯỢC ĐÁNH DẤU NỔI BẬT
             ======================================================== --}}
        <div id="panel-featured" class="flex-1 min-w-0 flex flex-col bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden h-full">
            {{-- Toolbar Lọc Nhanh Tại Chỗ --}}
            <div class="px-3 py-2 bg-slate-50/80 border-b border-slate-200/90 flex flex-wrap items-center justify-between gap-2 shrink-0">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <span class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5 whitespace-nowrap shrink-0">
                        <span class="material-symbols-outlined text-amber-500 text-[18px] fill-1">hotel_class</span>
                        <span>Danh Sách Đang Ghim (<span id="featured-count-badge" class="text-amber-600 font-black">{{ $featuredQuan->count() }}</span>)</span>
                    </span>
                    
                    {{-- Search Input lọc nhanh client-side (Đồng bộ admin-search-wrapper, không lệch icon, không conflict) --}}
                    <div class="admin-search-wrapper flex-1 min-w-[140px] max-w-xs shadow-2xs">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">search</span>
                        <input type="text" id="featured-quick-search" oninput="filterFeaturedTable()" placeholder="Lọc tên quán, địa chỉ..." class="w-full bg-transparent border-0 p-0 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0 font-medium leading-normal">
                    </div>
                </div>

                {{-- Select Loại hình kinh doanh --}}
                <div class="flex items-center gap-1.5 shrink-0">
                    <select id="featured-filter-category" onchange="filterFeaturedTable()" class="px-2 py-1 text-xs font-medium rounded-xl border border-slate-300 bg-white text-slate-800 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                        <option value="">Tất cả loại hình</option>
                        @foreach($businessTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>

                    <button type="button" onclick="resetFeaturedFilter()" class="p-1 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors shadow-2xs" title="Xóa bộ lọc">
                        <span class="material-symbols-outlined text-[15px] block">filter_alt_off</span>
                    </button>
                </div>
            </div>

            {{-- Table Wrapper: Cuộn dọc nội bộ, KHÔNG CUỘN NGANG (overflow-x-hidden), Header cố định --}}
            <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar relative">
                <table class="w-full text-left text-xs table-fixed-enterprise border-collapse">
                    <colgroup>
                        <col style="width: 38%;">
                        <col style="width: 25%;">
                        <col style="width: 14%;">
                        <col style="width: 23%;">
                    </colgroup>
                    <thead class="sticky top-0 z-10 bg-slate-100/95 backdrop-blur-xs text-slate-800 font-extrabold uppercase text-[11px] tracking-wider border-b border-slate-200 shadow-2xs">
                        <tr>
                            <th class="py-2.5 pl-3.5 pr-2">Địa Điểm Quán Ăn</th>
                            <th class="py-2.5 px-2">Trạng Thái & Giờ</th>
                            <th class="py-2.5 px-2 text-center">Tương Tác</th>
                            <th class="py-2.5 pr-3 pl-1 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="featured-table-body" class="divide-y divide-slate-100 font-medium">
                        @forelse($featuredQuan as $q)
                            @php
                                $isDangMo = $q->isDangMoCua();
                                $isPublic = ($q->trang_thai === 'da_duyet');
                                $addressText = trim($q->dia_chi_chi_tiet . ', ' . $q->ten_quan_huyen . ', ' . $q->ten_tinh_thanh);
                            @endphp
                            <tr id="featured-row-{{ $q->id }}" 
                                class="featured-item-row hover:bg-amber-50/50 transition-colors group"
                                data-name="{{ Str::lower($q->ten_quan) }}"
                                data-address="{{ Str::lower($addressText) }}"
                                data-category="{{ $q->loai_hinh_kinh_doanh }}">
                                
                                {{-- Cột 1: Thông tin quán (ảnh + tên + loại hình + địa chỉ) --}}
                                <td class="py-2 pl-3.5 pr-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        {{-- Ảnh bìa thumbnail --}}
                                        <div class="relative w-9 h-9 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0 shadow-2xs">
                                            <img src="{{ $q->anh_bia_url }}" alt="{{ $q->ten_quan }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80'">
                                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-amber-500 text-white rounded-tl flex items-center justify-center shadow-xs">
                                                <span class="material-symbols-outlined text-[9px] fill-1 text-slate-950 font-bold">star</span>
                                            </span>
                                        </div>

                                        {{-- Tên quán & thông tin chi tiết (Bấm vào xem chi tiết ngay trên trang) --}}
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1 flex-nowrap">
                                                <button type="button" 
                                                        onclick="openQuickViewModalById('{{ $q->id }}')" 
                                                        class="font-bold text-slate-900 hover:text-amber-600 transition-colors truncate text-xs inline-flex items-center gap-0.5 text-left cursor-pointer" 
                                                        title="Xem chi tiết quán ngay trên trang">
                                                    <span class="truncate">{{ $q->ten_quan }}</span>
                                                    <span class="material-symbols-outlined text-[12px] text-slate-400 group-hover:text-amber-500 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">visibility</span>
                                                </button>
                                                @if($q->is_xac_thuc)
                                                    <span class="material-symbols-outlined text-blue-500 text-[14px] shrink-0 fill-1" title="Quán đã cấp Tick Xanh xác thực">verified</span>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-1.5 mt-0.5 text-[10px] text-slate-500 truncate">
                                                <span class="px-1 py-0.2 rounded bg-slate-100 text-slate-700 font-semibold shrink-0">{{ $q->loai_hinh_kinh_doanh ?? 'Ẩm thực' }}</span>
                                                <span class="truncate" title="{{ $addressText }}">{{ $addressText ?: 'Chưa cập nhật địa chỉ' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Cột 2: Trạng thái & Giờ mở cửa --}}
                                <td class="py-2 px-2">
                                    <div class="flex flex-col gap-0.5 min-w-0">
                                        {{-- Badge hiển thị frontend --}}
                                        @if($isPublic)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded-md border border-emerald-300 w-fit shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span>Đang hiển thị Web</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-900 bg-amber-50 px-1.5 py-0.5 rounded-md border border-amber-300 w-fit shadow-2xs" title="Quán chưa duyệt hoặc bị khóa, không hiển thị trên trang người dùng">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <span>{{ $q->trang_thai === 'chua_duyet' ? 'Chờ duyệt' : 'Đang tạm khóa' }}</span>
                                            </span>
                                        @endif

                                        {{-- Giờ mở cửa --}}
                                        <div class="text-[10px] text-slate-600 flex items-center gap-1 truncate">
                                            @if($q->gio_mo_cua && $q->gio_dong_cua)
                                                <span class="material-symbols-outlined text-[13px] {{ $isDangMo ? 'text-emerald-500' : 'text-slate-400' }}">schedule</span>
                                                <span class="truncate">{{ substr($q->gio_mo_cua, 0, 5) }} - {{ substr($q->gio_dong_cua, 0, 5) }}</span>
                                                <span class="text-[9px] font-bold {{ $isDangMo ? 'text-emerald-700 bg-emerald-50 px-1 rounded' : 'text-slate-500 bg-slate-100 px-1 rounded' }} shrink-0">{{ $isDangMo ? 'Mở cửa' : 'Đóng cửa' }}</span>
                                            @else
                                                <span class="text-slate-400 italic">24/7 hoặc chưa đặt giờ</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Cột 3: Tương tác (Lượt xem & Chủ quán) --}}
                                <td class="py-2 px-2 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-800 bg-purple-50 px-1.5 py-0.5 rounded-md border border-purple-200">
                                            <span class="material-symbols-outlined text-[13px] text-purple-600">visibility</span>
                                            <span>{{ number_format($q->luot_xem ?? 0) }}</span>
                                        </span>
                                        <span class="text-[9px] text-slate-400 truncate max-w-[85px] mt-0.5" title="Chủ quán: {{ $q->chuQuan->ho_ten ?? 'Hệ thống' }}">
                                            {{ $q->chuQuan->ho_ten ?? 'Hệ thống' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Cột 4: Thao tác (Bộ nút rõ ràng, gọn gàng, sắc nét, tương phản cao, 100% chạy trong trang) --}}
                                <td class="py-2 pr-3 pl-1 text-right">
                                    <div class="inline-flex items-center justify-end gap-1 flex-nowrap shrink-0">
                                        {{-- Nút sửa nhanh (Xanh Dương nổi bật, mở modal sửa ngay tại chỗ) --}}
                                        <button type="button" 
                                                onclick="openQuickEditModal(this)"
                                                data-id="{{ $q->id }}"
                                                data-name="{{ $q->ten_quan }}"
                                                data-category="{{ $q->loai_hinh_kinh_doanh }}"
                                                data-phone="{{ $q->so_dien_thoai }}"
                                                data-open="{{ substr($q->gio_mo_cua ?? '', 0, 5) }}"
                                                data-close="{{ substr($q->gio_dong_cua ?? '', 0, 5) }}"
                                                data-shopee="{{ $q->shopeefood_url }}"
                                                data-tiktok="{{ $q->tiktok_url }}"
                                                data-verified="{{ $q->is_xac_thuc ? '1' : '0' }}"
                                                data-featured="{{ $q->is_noi_bat ? '1' : '0' }}"
                                                data-slug="{{ $q->slug }}"
                                                class="btn-action-edit px-2 py-1 rounded-lg text-[11px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 shadow-2xs hover:shadow-xs transition-all flex items-center gap-0.5 active:scale-95 cursor-pointer shrink-0" 
                                                title="Sửa thông tin quán nổi bật">
                                            <span class="material-symbols-outlined text-[13px]">edit_note</span>
                                            <span>Sửa</span>
                                        </button>

                                        {{-- Nút xem chi tiết (Mở modal xem trực tiếp ngay trên trang, KHÔNG CHUYỂN TRANG) --}}
                                        <button type="button" 
                                                onclick="openQuickViewModal(this)"
                                                data-id="{{ $q->id }}"
                                                data-name="{{ $q->ten_quan }}"
                                                data-category="{{ $q->loai_hinh_kinh_doanh }}"
                                                data-address="{{ $addressText }}"
                                                data-phone="{{ $q->so_dien_thoai }}"
                                                data-open="{{ substr($q->gio_mo_cua ?? '', 0, 5) }}"
                                                data-close="{{ substr($q->gio_dong_cua ?? '', 0, 5) }}"
                                                data-is-open="{{ $isDangMo ? '1' : '0' }}"
                                                data-views="{{ number_format($q->luot_xem ?? 0) }}"
                                                data-owner="{{ $q->chuQuan->ho_ten ?? 'Hệ thống' }}"
                                                data-image="{{ $q->anh_bia_url }}"
                                                data-verified="{{ $q->is_xac_thuc ? '1' : '0' }}"
                                                data-featured="{{ $q->is_noi_bat ? '1' : '0' }}"
                                                data-status="{{ $q->trang_thai }}"
                                                data-shopee="{{ $q->shopeefood_url }}"
                                                data-tiktok="{{ $q->tiktok_url }}"
                                                class="btn-action-view w-6.5 h-6.5 sm:w-7 sm:h-7 rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-800 hover:text-white border border-slate-300 hover:border-slate-800 shadow-2xs hover:shadow-xs transition-all flex items-center justify-center active:scale-95 cursor-pointer shrink-0" 
                                                title="Xem chi tiết hồ sơ quán ngay trên trang">
                                            <span class="material-symbols-outlined text-[13px]">visibility</span>
                                        </button>

                                        {{-- Nút gỡ nổi bật (Đỏ Rose nổi bật, mở modal xác nhận tiếng Việt ngay tại chỗ) --}}
                                        <button type="button" 
                                                onclick="promptRemoveFeatured('{{ $q->id }}', '{{ addslashes($q->ten_quan) }}', this)" 
                                                class="btn-action-remove px-2 py-1 rounded-lg text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-600 hover:text-white border border-rose-200 hover:border-rose-600 shadow-2xs hover:shadow-xs transition-all flex items-center gap-0.5 active:scale-95 cursor-pointer shrink-0" 
                                                title="Gỡ quán khỏi danh sách nổi bật">
                                            <span class="material-symbols-outlined text-[13px]">star_half</span>
                                            <span>Gỡ</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="featured-empty-row">
                                <td colspan="4" class="p-8 text-center">
                                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto text-slate-500">
                                        <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mb-2 shadow-2xs">
                                            <span class="material-symbols-outlined text-[24px]">stars</span>
                                        </div>
                                        <h4 class="text-xs font-bold text-slate-800">Chưa có quán nào trong danh sách nổi bật</h4>
                                        <p class="text-[11px] text-slate-500 mt-0.5 mb-2.5">Chọn các quán ẩm thực chất lượng từ cột bên phải để ghim lên vị trí hàng đầu.</p>
                                        <button type="button" onclick="openAddFeaturedModal()" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-slate-950 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 shadow-sm transition-all flex items-center gap-1.5 cursor-pointer border border-amber-300">
                                            <span class="material-symbols-outlined text-[16px] font-bold">add_circle</span>
                                            <span>Thêm Quán Nổi Bật Ngay</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Tóm Tắt & Gợi Ý: Cố định chân panel, không bị taskbar che --}}
            <div class="px-3.5 py-1.5 bg-slate-50 border-t border-slate-200 text-[11px] text-slate-500 flex items-center justify-between shrink-0">
                <span class="flex items-center gap-1.5 truncate">
                    <span class="material-symbols-outlined text-[14px] text-amber-500">lightbulb</span>
                    <span class="truncate">Slider trang chủ tự động hiển thị các quán nổi bật cập nhật mới nhất.</span>
                </span>
                <a href="{{ route('quan-noi-bat') }}" target="_blank" class="text-amber-700 hover:text-amber-900 font-bold hover:underline shrink-0 flex items-center gap-0.5 ml-2">
                    <span class="hidden sm:inline">Xem trang khách</span>
                    <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                </a>
            </div>
        </div>

        {{-- ========================================================
             PANEL PHẢI: KHO QUÁN ĐÃ DUYỆT - ĐỀ CỬ THÊM NHANH VÀO NỔI BẬT
             (Độ rộng 290px-320px cân đối, nhường không gian cho bảng trái)
             ======================================================== --}}
        <div id="panel-candidates" class="w-full lg:w-[290px] xl:w-[320px] shrink-0 flex flex-col bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden h-full">
            {{-- Header Kho Quán --}}
            <div class="px-3 py-2 bg-slate-50/80 border-b border-slate-200/90 flex flex-col gap-1.5 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-slate-700 text-[17px]">storefront</span>
                        <h3 class="text-xs font-bold text-slate-900">Kho Quán Đã Duyệt</h3>
                        <span id="candidates-count-badge" class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-200 text-slate-800">{{ $allQuan->count() }}</span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium">1-click Thêm nhanh</span>
                </div>

                {{-- Live Search Input (Đồng bộ admin-search-wrapper, không lệch icon, không conflict) --}}
                <div class="admin-search-wrapper w-full shadow-2xs">
                    <span class="material-symbols-outlined text-[16px] text-slate-400">search</span>
                    <input type="text" id="candidate-live-search" oninput="filterCandidateVenues()" placeholder="Gõ tìm quán theo tên, địa chỉ..." class="w-full bg-transparent border-0 p-0 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0 font-medium leading-normal">
                </div>
            </div>

            {{-- Danh Sách Quán Khả Dụng (Cuộn nội bộ, thẻ nhỏ gọn, nút Thêm nổi bật sắc nét) --}}
            <div id="candidates-list-container" class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-2 space-y-1.5">
                @forelse($allQuan as $candidate)
                    @php
                        $candAddress = trim($candidate->dia_chi_chi_tiet . ', ' . $candidate->ten_quan_huyen . ', ' . $candidate->ten_tinh_thanh);
                    @endphp
                    <div id="candidate-card-{{ $candidate->id }}" 
                         class="candidate-card p-2 rounded-xl border border-slate-200 bg-white hover:border-amber-400 hover:bg-amber-50/20 transition-all flex items-center justify-between gap-2 shadow-2xs group"
                         data-name="{{ Str::lower($candidate->ten_quan) }}"
                         data-address="{{ Str::lower($candAddress) }}"
                         data-category="{{ $candidate->loai_hinh_kinh_doanh }}">
                        
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <div class="w-8 h-8 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                <img src="{{ $candidate->anh_bia_url }}" alt="{{ $candidate->ten_quan }}" class="w-full h-full object-cover" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=100&q=80'">
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1">
                                    <h5 class="text-xs font-bold text-slate-900 truncate" title="{{ $candidate->ten_quan }}">{{ $candidate->ten_quan }}</h5>
                                    @if($candidate->is_xac_thuc)
                                        <span class="material-symbols-outlined text-blue-500 text-[13px] shrink-0 fill-1" title="Tick xanh">verified</span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-slate-500 truncate" title="{{ $candAddress }}">{{ $candAddress ?: 'Ẩm thực' }}</p>
                            </div>
                        </div>

                        {{-- Nút 1-click Ghim Nổi Bật (Amber cao cấp, nổi bật) --}}
                        <button type="button" 
                                onclick="adminAddFeatured('{{ $candidate->id }}', this)" 
                                class="px-2 py-1 rounded-lg text-[11px] font-bold text-amber-900 bg-amber-100 hover:bg-amber-500 hover:text-slate-950 border border-amber-300 hover:border-amber-400 shadow-2xs hover:shadow-xs transition-all flex items-center gap-0.5 shrink-0 active:scale-95 cursor-pointer" 
                                title="Đánh dấu đưa quán này lên danh sách nổi bật">
                            <span class="material-symbols-outlined text-[13px] text-amber-700 fill-1">star</span>
                            <span>+ Nổi Bật</span>
                        </button>
                    </div>
                @empty
                    <div id="candidates-empty-msg" class="p-8 text-center text-xs text-slate-400">
                        Kho quán đã duyệt trống hoặc toàn bộ quán đã được ghim nổi bật.
                    </div>
                @endforelse
            </div>

            {{-- Footer Panel Phải --}}
            <div class="px-3 py-1.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-[11px] text-slate-500 shrink-0">
                <span>Không tìm thấy quán?</span>
                <button type="button" onclick="openAddFeaturedModal()" class="text-amber-700 hover:text-amber-900 font-bold underline flex items-center gap-0.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[13px]">search</span>
                    <span>Tra cứu nâng cao</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL 1: SỬA NHANH QUÁN NỔI BẬT (QUICK EDIT MODAL)
     ======================================================== --}}
<div id="quick-edit-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 transition-opacity duration-200">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden transform transition-all scale-95 opacity-0 duration-200" id="quick-edit-modal-box">
        {{-- Modal Header --}}
        <div class="px-4 py-3 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-[20px] fill-1">edit_note</span>
                <h3 class="text-xs sm:text-sm font-bold">Chỉnh Sửa Nhanh Quán Nổi Bật</h3>
            </div>
            <button type="button" onclick="closeQuickEditModal()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        {{-- Form Sửa --}}
        <form id="quick-edit-form" onsubmit="submitQuickEdit(event)" class="p-4 space-y-3.5 text-xs">
            <input type="hidden" id="edit-quan-id" name="quan_id">

            {{-- Tên Quán --}}
            <div>
                <label class="block font-bold text-slate-700 mb-1">Tên quán ăn / cơ sở <span class="text-rose-500">*</span></label>
                <input type="text" id="edit-ten-quan" name="ten_quan" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none text-xs text-slate-900 font-medium">
            </div>

            {{-- Loại Hình Kinh Doanh & Số Điện Thoại --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Loại hình kinh doanh <span class="text-rose-500">*</span></label>
                    <select id="edit-loai-hinh" name="loai_hinh_kinh_doanh" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none text-xs text-slate-800 bg-white font-medium">
                        @foreach($businessTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Số điện thoại liên hệ</label>
                    <input type="text" id="edit-so-dien-thoai" name="so_dien_thoai" placeholder="0901234567" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none text-xs text-slate-900 font-medium">
                </div>
            </div>

            {{-- Giờ Mở Cửa - Giờ Đóng Cửa --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Giờ mở cửa (HH:mm)</label>
                    <input type="time" id="edit-gio-mo-cua" name="gio_mo_cua" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none text-xs text-slate-800 bg-white font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Giờ đóng cửa (HH:mm)</label>
                    <input type="time" id="edit-gio-dong-cua" name="gio_dong_cua" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none text-xs text-slate-800 bg-white font-medium">
                </div>
            </div>

            {{-- Link ShopeeFood & TikTok --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Link ShopeeFood</label>
                    <input type="url" id="edit-shopeefood-url" name="shopeefood_url" placeholder="https://shopeefood.vn/..." class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none text-xs text-slate-900 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Link Video TikTok Review</label>
                    <input type="url" id="edit-tiktok-url" name="tiktok_url" placeholder="https://tiktok.com/@..." class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none text-xs text-slate-900 font-medium">
                </div>
            </div>

            {{-- Checkboxes: Tick xanh & Trạng thái nổi bật --}}
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/90 flex items-center justify-between gap-4">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="edit-is-xac-thuc" name="is_xac_thuc" value="1" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                    <span class="font-bold text-slate-800 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-blue-500 fill-1">verified</span>
                        Tick Xanh Xác Thực
                    </span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="edit-is-noi-bat" name="is_noi_bat" value="1" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-500" checked>
                    <span class="font-bold text-slate-800 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-amber-500 fill-1">star</span>
                        Trạng Thái Nổi Bật
                    </span>
                </label>
            </div>

            {{-- Actions: Chạy trực tiếp trong trang, không chuyển trang --}}
            <div class="pt-2.5 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeQuickEditModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-all cursor-pointer">
                    Hủy
                </button>
                <button type="submit" id="btn-submit-quick-edit" class="px-5 py-2 rounded-xl text-xs font-bold text-slate-950 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 shadow-sm transition-all flex items-center gap-1.5 cursor-pointer border border-amber-400">
                    <span class="material-symbols-outlined text-[16px] text-slate-950 font-bold">save</span>
                    <span>Lưu Thay Đổi</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL 2: TÌM KIẾM & THÊM QUÁN NỔI BẬT NÂNG CAO
     ======================================================== --}}
<div id="add-featured-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 transition-opacity duration-200">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-xl overflow-hidden transform transition-all scale-95 opacity-0 duration-200 flex flex-col max-h-[85vh]" id="add-featured-modal-box">
        {{-- Header --}}
        <div class="px-4 py-3 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-[20px] fill-1">add_location_alt</span>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold">Thêm Quán Vào Danh Sách Nổi Bật</h3>
                    <p class="text-[10px] text-slate-300">Tra cứu nhanh toàn bộ quán đã duyệt để đưa lên Slider trang chủ</p>
                </div>
            </div>
            <button type="button" onclick="closeAddFeaturedModal()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-700 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        {{-- Search & Filter Controls (Flexbox căn giữa hoàn hảo, không lệch icon, không conflict) --}}
        <div class="p-3 bg-slate-50 border-b border-slate-200/80 flex flex-col sm:flex-row gap-2 shrink-0">
            <div class="admin-search-wrapper flex-1 shadow-2xs">
                <span class="material-symbols-outlined text-[18px] text-slate-400">search</span>
                <input type="text" id="modal-search-input" oninput="debounceModalSearch()" placeholder="Nhập tên quán, địa chỉ chi tiết, tên chủ quán..." class="w-full bg-transparent border-0 p-0 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0 font-medium leading-normal">
            </div>

            <select id="modal-filter-category" onchange="debounceModalSearch()" class="px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none text-slate-700 font-medium">
                <option value="">Tất cả loại hình</option>
                @foreach($businessTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>

        {{-- Search Results List --}}
        <div id="modal-search-results" class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-2 min-h-[220px]">
            <div class="p-8 text-center text-slate-400 text-xs">
                <span class="material-symbols-outlined text-[28px] text-slate-300 mb-1">search</span>
                <p>Nhập từ khóa tìm kiếm hoặc bấm lọc để tìm quán ẩm thực phù hợp.</p>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs shrink-0">
            <span class="text-[11px] text-slate-500">Chỉ áp dụng cho các cơ sở đã được kiểm duyệt hợp lệ.</span>
            <button type="button" onclick="closeAddFeaturedModal()" class="px-3.5 py-1.5 rounded-xl border border-slate-300 bg-white text-slate-700 hover:bg-slate-100 font-bold text-xs cursor-pointer">Đóng</button>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL 3: XÁC NHẬN GỠ QUÁN NỔI BẬT (TIẾNG VIỆT CHUYÊN NGHIỆP)
     ======================================================== --}}
<div id="confirm-remove-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 transition-opacity duration-200">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0 duration-200" id="confirm-remove-modal-box">
        <div class="p-4 sm:p-5 text-center">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3 shadow-xs">
                <span class="material-symbols-outlined text-[28px]">star_half</span>
            </div>
            <h3 class="text-sm sm:text-base font-black text-slate-900">Xác Nhận Gỡ Khỏi Quán Nổi Bật</h3>
            <p class="text-xs text-slate-600 mt-2">
                Bạn có chắc chắn muốn gỡ quán <span id="remove-modal-quan-name" class="font-bold text-slate-900"></span> khỏi danh sách nổi bật?
            </p>
            <p class="text-[11px] text-slate-400 mt-1">
                Quán sẽ được gỡ khỏi Slider trang chủ và hoàn trả lại kho quán đã duyệt.
            </p>
            <div class="mt-4 flex items-center justify-center gap-2.5">
                <button type="button" onclick="closeConfirmRemoveModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-all cursor-pointer">
                    Hủy Bỏ
                </button>
                <button type="button" id="btn-confirm-remove-action" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition-all flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    <span>Xác Nhận Gỡ</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL 4: XEM CHI TIẾT QUÁN NỔI BẬT (CHẠY NGAY TRONG TRANG, KHÔNG CHUYỂN TRANG)
     ======================================================== --}}
<div id="quick-view-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 transition-opacity duration-200">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden transform transition-all scale-95 opacity-0 duration-200" id="quick-view-modal-box">
        {{-- Header Modal --}}
        <div class="px-4 py-3 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2 min-w-0">
                <span class="material-symbols-outlined text-amber-400 text-[20px] fill-1">storefront</span>
                <h3 class="text-xs sm:text-sm font-bold truncate">Hồ Sơ Chi Tiết Quán Nổi Bật</h3>
            </div>
            <button type="button" onclick="closeQuickViewModal()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        {{-- Body Modal --}}
        <div class="p-4 space-y-3 max-h-[75vh] overflow-y-auto custom-scrollbar text-xs">
            {{-- Ảnh bìa & Tên quán --}}
            <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200/90">
                <div class="relative w-16 h-16 rounded-xl overflow-hidden bg-slate-200 shrink-0 shadow-xs border border-slate-300">
                    <img id="view-modal-img" src="" alt="" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80'">
                    <span id="view-modal-badge-star" class="absolute bottom-0 right-0 w-4 h-4 bg-amber-500 text-white rounded-tl flex items-center justify-center shadow-xs">
                        <span class="material-symbols-outlined text-[10px] fill-1 text-slate-950 font-bold">star</span>
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <h4 id="view-modal-name" class="text-sm font-extrabold text-slate-900"></h4>
                        <span id="view-modal-verified" class="hidden material-symbols-outlined text-blue-500 text-[16px] fill-1" title="Quán đã cấp Tick Xanh xác thực">verified</span>
                    </div>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span id="view-modal-category" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-800"></span>
                        <span id="view-modal-status-badge" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">Đang hiển thị Web</span>
                    </div>
                </div>
            </div>

            {{-- Thông tin chi tiết trong grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div class="p-2.5 rounded-xl bg-white border border-slate-200">
                    <span class="text-[10px] text-slate-400 block font-semibold">Địa chỉ cơ sở:</span>
                    <p id="view-modal-address" class="text-xs text-slate-800 font-medium mt-0.5 break-words"></p>
                </div>
                <div class="p-2.5 rounded-xl bg-white border border-slate-200">
                    <span class="text-[10px] text-slate-400 block font-semibold">Số điện thoại:</span>
                    <p id="view-modal-phone" class="text-xs text-slate-800 font-bold mt-0.5"></p>
                </div>
                <div class="p-2.5 rounded-xl bg-white border border-slate-200">
                    <span class="text-[10px] text-slate-400 block font-semibold">Giờ mở cửa:</span>
                    <p id="view-modal-hours" class="text-xs text-slate-800 font-bold mt-0.5 flex items-center gap-1"></p>
                </div>
                <div class="p-2.5 rounded-xl bg-white border border-slate-200">
                    <span class="text-[10px] text-slate-400 block font-semibold">Lượt xem & Quản lý:</span>
                    <p id="view-modal-views-owner" class="text-xs text-slate-800 font-bold mt-0.5"></p>
                </div>
            </div>

            {{-- Kênh trực tuyến --}}
            <div id="view-modal-links-box" class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 hidden">
                <span class="text-[10px] text-slate-400 block font-semibold mb-1">Kênh trực tuyến:</span>
                <div class="flex items-center gap-2 flex-wrap">
                    <a id="view-modal-shopee" href="#" target="_blank" class="hidden px-2.5 py-1 rounded-lg bg-orange-50 text-orange-700 border border-orange-200 text-[11px] font-bold items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]">shopping_bag</span>
                        <span>ShopeeFood</span>
                    </a>
                    <a id="view-modal-tiktok" href="#" target="_blank" class="hidden px-2.5 py-1 rounded-lg bg-slate-100 text-slate-900 border border-slate-300 text-[11px] font-bold items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]">videocam</span>
                        <span>TikTok Review</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Footer Modal --}}
        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <button type="button" id="btn-view-modal-edit" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-600 hover:text-white border border-blue-200 transition-all flex items-center gap-1 cursor-pointer">
                <span class="material-symbols-outlined text-[14px]">edit_note</span>
                <span>Chuyển Sang Sửa</span>
            </button>
            <button type="button" onclick="closeQuickViewModal()" class="px-4 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 transition-all cursor-pointer">
                Đóng
            </button>
        </div>
    </div>
</div>

{{-- TOAST CONTAINER NỔI BẬT (Hiển thị thông báo tiếng Việt trên mọi lớp giao diện) --}}
<div id="quan-noi-bat-toast-container" class="fixed top-4 right-4 z-[99999] flex flex-col gap-2.5 max-w-sm pointer-events-none"></div>

{{-- JAVASCRIPT XỬ LÝ TOÀN BỘ NGHIỆP VỤ QUÁN NỔI BẬT: 100% SINGLE PAGE SPA, KHÔNG RELOAD, KHÔNG CHUYỂN TRANG --}}
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ========================================================
    // HỆ THỐNG THÔNG BÁO TIẾNG VIỆT CAO CẤP
    // ========================================================
    function showAdminToast(title, message, type = 'success', duration = 5000) {
        let container = document.getElementById('quan-noi-bat-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'quan-noi-bat-toast-container';
            container.className = 'fixed top-4 right-4 z-[99999] flex flex-col gap-2.5 max-w-sm pointer-events-none';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto bg-slate-900 text-white rounded-2xl p-3.5 shadow-2xl border transition-all duration-300 ease-out transform translate-x-full flex items-start gap-3 w-full ' +
            (type === 'error' ? 'border-rose-500/80' : (type === 'info' ? 'border-amber-400/80' : 'border-emerald-500/80'));

        let iconName = 'check_circle';
        let iconBg = 'bg-emerald-500 text-slate-950';
        let badgeText = 'Đồng Bộ Website';
        let badgeColor = 'text-emerald-400';

        if (type === 'error') {
            iconName = 'error';
            iconBg = 'bg-rose-500 text-white';
            badgeText = 'Lỗi Thao Tác';
            badgeColor = 'text-rose-400';
        } else if (type === 'info') {
            iconName = 'hotel_class';
            iconBg = 'bg-amber-400 text-slate-950';
            badgeText = 'Quán Nổi Bật';
            badgeColor = 'text-amber-400';
        }

        toast.innerHTML = `
            <div class="w-8 h-8 rounded-xl ${iconBg} flex items-center justify-center shrink-0 font-bold shadow-xs">
                <span class="material-symbols-outlined text-[18px]">${iconName}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider ${badgeColor}">${badgeText}</span>
                    <span class="text-[10px] text-slate-400">Vừa xong</span>
                </div>
                <h4 class="text-xs font-bold text-white truncate mt-0.5">${title}</h4>
                <p class="text-[11px] text-slate-300 mt-0.5 leading-snug">${message}</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer shrink-0">
                <span class="material-symbols-outlined text-[15px]">close</span>
            </button>
        `;

        container.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-x-full'), 30);

        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 350);
        }, duration);
    }

    // Hiệu ứng đồng bộ tức thời trên Top Bar
    function triggerSyncAnimation(text = 'Đã đồng bộ website') {
        const badge = document.getElementById('live-sync-badge');
        const textEl = document.getElementById('live-sync-text');
        if (!badge || !textEl) return;

        const originalHtml = textEl.innerHTML;
        badge.classList.remove('bg-emerald-50', 'text-emerald-800', 'border-emerald-300');
        badge.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600', 'scale-105');
        textEl.innerHTML = `<span class="material-symbols-outlined text-[12px] inline align-middle mr-0.5">check</span> ${text}`;

        setTimeout(() => {
            badge.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-600', 'scale-105');
            badge.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-300');
            textEl.innerHTML = originalHtml;
        }, 3000);
    }

    // ========================================================
    // 1. THÊM QUÁN VÀO NỔI BẬT (CHẠY NGAY TRONG TRANG, KHÔNG RELOAD)
    // ========================================================
    function adminAddFeatured(quanId, btn) {
        if (!quanId || btn.disabled) return;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-[13px] animate-spin">progress_activity</span> <span class="text-[10px]">Đang ghim...</span>';

        fetch('{{ route("admin.quan-noi-bat.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ quan_id: quanId })
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
            return data;
        })
        .then(data => {
            const q = data.quan || {};
            const tenQuan = q.ten_quan || 'quán ăn';
            const msg = data.message || `Đã ghim quán "${tenQuan}" vào danh sách nổi bật và đồng bộ hiển thị lên Slider trang chủ website!`;

            // Xóa thẻ khỏi danh sách đề cử bên phải
            const card = document.getElementById(`candidate-card-${quanId}`);
            if (card) {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'translateX(-15px)';
                setTimeout(() => card.remove(), 300);
            }

            // Chèn dòng mới trực tiếp vào bảng bên trái (Single-Page SPA, không reload trang)
            insertFeaturedRowToTable(q);

            // Cập nhật số đếm
            updateCandidateCount(-1);
            updateFeaturedCount(+1);

            // Thông báo tiếng Việt nổi bật và hiệu ứng đồng bộ
            showAdminToast('Đã Thêm Nổi Bật', msg, 'success');
            triggerSyncAnimation('Đã đồng bộ website');

            if (typeof clearAdminPageCache === 'function') {
                clearAdminPageCache();
            }

            // Nếu nút nằm trong Modal Thêm Nâng Cao, đổi trạng thái nút thành Đã Ghim
            if (btn && btn.closest('#add-featured-modal')) {
                btn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold text-emerald-800 bg-emerald-100 border border-emerald-300 flex items-center gap-1 shrink-0';
                btn.innerHTML = '<span class="material-symbols-outlined text-[15px]">check</span> <span>Đã Ghim</span>';
            }
        })
        .catch(err => {
            showAdminToast('Lỗi Thao Tác', err.message || 'Không thể thêm quán vào nổi bật.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    // Helper chèn một hàng quán mới vào bảng danh sách nổi bật
    function insertFeaturedRowToTable(q) {
        const tbody = document.getElementById('featured-table-body');
        const emptyRow = document.getElementById('featured-empty-row');
        if (emptyRow) emptyRow.remove();

        const isDangMo = q.is_dang_mo_cua;
        const isPublic = (q.trang_thai === 'da_duyet');
        const gioMo = (q.gio_mo_cua || '').substring(0, 5);
        const gioDong = (q.gio_dong_cua || '').substring(0, 5);
        const hasHours = Boolean(gioMo && gioDong);
        const addressText = q.dia_chi || 'Chưa cập nhật địa chỉ';

        const tr = document.createElement('tr');
        tr.id = `featured-row-${q.id}`;
        tr.className = 'featured-item-row hover:bg-amber-50/50 transition-colors group';
        tr.setAttribute('data-name', (q.ten_quan || '').toLowerCase());
        tr.setAttribute('data-address', (q.dia_chi || '').toLowerCase());
        tr.setAttribute('data-category', q.loai_hinh_kinh_doanh || '');

        tr.innerHTML = `
            <td class="py-2 pl-3.5 pr-2">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="relative w-9 h-9 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0 shadow-2xs">
                        <img src="${q.anh_bia_url || ''}" alt="${q.ten_quan || ''}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80'">
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-amber-500 text-white rounded-tl flex items-center justify-center shadow-xs">
                            <span class="material-symbols-outlined text-[9px] fill-1 text-slate-950 font-bold">star</span>
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1 flex-nowrap">
                            <button type="button" onclick="openQuickViewModalById('${q.id}')" class="font-bold text-slate-900 hover:text-amber-600 transition-colors truncate text-xs inline-flex items-center gap-0.5 text-left cursor-pointer" title="Xem chi tiết quán ngay trên trang">
                                <span class="truncate">${q.ten_quan || ''}</span>
                                <span class="material-symbols-outlined text-[12px] text-slate-400 group-hover:text-amber-500 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">visibility</span>
                            </button>
                            ${q.is_xac_thuc ? '<span class="material-symbols-outlined text-blue-500 text-[14px] shrink-0 fill-1" title="Quán đã cấp Tick Xanh xác thực">verified</span>' : ''}
                        </div>
                        <div class="flex items-center gap-1.5 mt-0.5 text-[10px] text-slate-500 truncate">
                            <span class="px-1 py-0.2 rounded bg-slate-100 text-slate-700 font-semibold shrink-0">${q.loai_hinh_kinh_doanh || 'Ẩm thực'}</span>
                            <span class="truncate" title="${addressText}">${addressText}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="py-2 px-2">
                <div class="flex flex-col gap-0.5 min-w-0">
                    ${isPublic ? `
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded-md border border-emerald-300 w-fit shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Đang hiển thị Web</span>
                        </span>
                    ` : `
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-900 bg-amber-50 px-1.5 py-0.5 rounded-md border border-amber-300 w-fit shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span>${q.trang_thai === 'chua_duyet' ? 'Chờ duyệt' : 'Đang tạm khóa'}</span>
                        </span>
                    `}
                    <div class="text-[10px] text-slate-600 flex items-center gap-1 truncate">
                        ${hasHours ? `
                            <span class="material-symbols-outlined text-[13px] ${isDangMo ? 'text-emerald-500' : 'text-slate-400'}">schedule</span>
                            <span class="truncate">${gioMo} - ${gioDong}</span>
                            <span class="text-[9px] font-bold ${isDangMo ? 'text-emerald-700 bg-emerald-50 px-1 rounded' : 'text-slate-500 bg-slate-100 px-1 rounded'} shrink-0">${isDangMo ? 'Mở cửa' : 'Đóng cửa'}</span>
                        ` : `
                            <span class="text-slate-400 italic">24/7 hoặc chưa đặt giờ</span>
                        `}
                    </div>
                </div>
            </td>
            <td class="py-2 px-2 text-center">
                <div class="flex flex-col items-center justify-center">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-800 bg-purple-50 px-1.5 py-0.5 rounded-md border border-purple-200">
                        <span class="material-symbols-outlined text-[13px] text-purple-600">visibility</span>
                        <span>${Number(q.luot_xem || 0).toLocaleString()}</span>
                    </span>
                    <span class="text-[9px] text-slate-400 truncate max-w-[85px] mt-0.5" title="Chủ quán: ${q.chu_quan || 'Hệ thống'}">
                        ${q.chu_quan || 'Hệ thống'}
                    </span>
                </div>
            </td>
            <td class="py-2 pr-3 pl-1 text-right">
                <div class="inline-flex items-center justify-end gap-1 flex-nowrap shrink-0">
                    <button type="button" 
                            onclick="openQuickEditModal(this)"
                            data-id="${q.id}"
                            data-name="${q.ten_quan || ''}"
                            data-category="${q.loai_hinh_kinh_doanh || ''}"
                            data-phone="${q.so_dien_thoai || ''}"
                            data-open="${gioMo}"
                            data-close="${gioDong}"
                            data-shopee="${q.shopeefood_url || ''}"
                            data-tiktok="${q.tiktok_url || ''}"
                            data-verified="${q.is_xac_thuc ? '1' : '0'}"
                            data-featured="1"
                            data-slug="${q.slug || ''}"
                            class="btn-action-edit px-2 py-1 rounded-lg text-[11px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-600 hover:text-white border border-blue-200 hover:border-blue-600 shadow-2xs hover:shadow-xs transition-all flex items-center gap-0.5 active:scale-95 cursor-pointer shrink-0" 
                            title="Sửa thông tin quán nổi bật">
                        <span class="material-symbols-outlined text-[13px]">edit_note</span>
                        <span>Sửa</span>
                    </button>
                    <button type="button" 
                            onclick="openQuickViewModal(this)"
                            data-id="${q.id}"
                            data-name="${q.ten_quan || ''}"
                            data-category="${q.loai_hinh_kinh_doanh || ''}"
                            data-address="${addressText}"
                            data-phone="${q.so_dien_thoai || ''}"
                            data-open="${gioMo}"
                            data-close="${gioDong}"
                            data-is-open="${isDangMo ? '1' : '0'}"
                            data-views="${Number(q.luot_xem || 0).toLocaleString()}"
                            data-owner="${q.chu_quan || 'Hệ thống'}"
                            data-image="${q.anh_bia_url || ''}"
                            data-verified="${q.is_xac_thuc ? '1' : '0'}"
                            data-featured="1"
                            data-status="${q.trang_thai || 'da_duyet'}"
                            data-shopee="${q.shopeefood_url || ''}"
                            data-tiktok="${q.tiktok_url || ''}"
                            class="btn-action-view w-6.5 h-6.5 sm:w-7 sm:h-7 rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-800 hover:text-white border border-slate-300 hover:border-slate-800 shadow-2xs hover:shadow-xs transition-all flex items-center justify-center active:scale-95 cursor-pointer shrink-0" 
                            title="Xem chi tiết hồ sơ quán ngay trên trang">
                        <span class="material-symbols-outlined text-[13px]">visibility</span>
                    </button>
                    <button type="button" 
                            onclick="promptRemoveFeatured('${q.id}', '${(q.ten_quan || '').replace(/'/g, "\\'")}', this)" 
                            class="btn-action-remove px-2 py-1 rounded-lg text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-600 hover:text-white border border-rose-200 hover:border-rose-600 shadow-2xs hover:shadow-xs transition-all flex items-center gap-0.5 active:scale-95 cursor-pointer shrink-0" 
                            title="Gỡ quán khỏi danh sách nổi bật">
                        <span class="material-symbols-outlined text-[13px]">star_half</span>
                        <span>Gỡ</span>
                    </button>
                </div>
            </td>
        `;

        tbody.insertBefore(tr, tbody.firstChild);
    }

    // ========================================================
    // 2. GỠ QUÁN KHỎI DANH SÁCH NỔI BẬT (CHẠY NGAY TRONG TRANG, KHÔNG RELOAD)
    // ========================================================
    let pendingRemoveQuanId = null;
    let pendingRemoveBtn = null;
    let pendingRemoveQuanName = '';

    function promptRemoveFeatured(quanId, quanName, btn) {
        pendingRemoveQuanId = quanId;
        pendingRemoveBtn = btn;
        pendingRemoveQuanName = quanName || 'quán ăn này';

        document.getElementById('remove-modal-quan-name').innerText = `"${pendingRemoveQuanName}"`;
        const modal = document.getElementById('confirm-remove-modal');
        const box = document.getElementById('confirm-remove-modal-box');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);

        const confirmBtn = document.getElementById('btn-confirm-remove-action');
        confirmBtn.onclick = function() {
            executeRemoveFeatured();
        };
    }

    function closeConfirmRemoveModal() {
        const modal = document.getElementById('confirm-remove-modal');
        const box = document.getElementById('confirm-remove-modal-box');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            pendingRemoveQuanId = null;
            pendingRemoveBtn = null;
        }, 200);
    }

    function executeRemoveFeatured() {
        const quanId = pendingRemoveQuanId;
        const btn = pendingRemoveBtn;
        const quanName = pendingRemoveQuanName;
        if (!quanId) return;

        const confirmBtn = document.getElementById('btn-confirm-remove-action');
        const originalBtnText = confirmBtn.innerHTML;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="material-symbols-outlined text-[15px] animate-spin">progress_activity</span> Đang gỡ...';

        fetch(`/admin/quan-noi-bat/${quanId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
            return data;
        })
        .then(data => {
            closeConfirmRemoveModal();
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalBtnText;

            const msg = data.message || `Đã gỡ quán "${quanName}" khỏi danh sách nổi bật và hoàn trả về kho quán! Dữ liệu đã đồng bộ ra website.`;
            showAdminToast('Đã Gỡ Nổi Bật', msg, 'info');
            triggerSyncAnimation('Đã đồng bộ website');

            if (typeof clearAdminPageCache === 'function') {
                clearAdminPageCache();
            }

            // Animate row removal trong bảng
            const row = document.getElementById(`featured-row-${quanId}`);
            if (row) {
                row.style.transition = 'all 0.35s ease';
                row.style.opacity = '0';
                row.style.transform = 'scale(0.96)';
                setTimeout(() => {
                    row.remove();
                    const tbody = document.getElementById('featured-table-body');
                    if (tbody && tbody.querySelectorAll('.featured-item-row').length === 0) {
                        tbody.innerHTML = '<tr id="featured-empty-row"><td colspan="4" class="p-8 text-center text-xs text-slate-500">Chưa có quán nào trong danh sách nổi bật.</td></tr>';
                    }
                }, 350);
            }

            // Hoàn trả thẻ quán vào Panel Phải "Kho Quán Đã Duyệt" để đồng bộ 2 chiều
            const candidatesList = document.getElementById('candidates-list-container');
            const emptyMsg = document.getElementById('candidates-empty-msg');
            if (emptyMsg) emptyMsg.remove();

            if (candidatesList && data.quan) {
                const qData = data.quan;
                const newCard = document.createElement('div');
                newCard.id = `candidate-card-${qData.id}`;
                newCard.className = 'candidate-card p-2 rounded-xl border border-slate-200 bg-white hover:border-amber-400 hover:bg-amber-50/20 transition-all flex items-center justify-between gap-2 shadow-2xs group';
                newCard.setAttribute('data-name', (qData.ten_quan || '').toLowerCase());
                newCard.setAttribute('data-address', (qData.dia_chi || '').toLowerCase());
                newCard.setAttribute('data-category', qData.loai_hinh_kinh_doanh || '');

                newCard.innerHTML = `
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <div class="w-8 h-8 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                            <img src="${qData.anh_bia_url}" alt="${qData.ten_quan}" class="w-full h-full object-cover" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=100&q=80'">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1">
                                <h5 class="text-xs font-bold text-slate-900 truncate" title="${qData.ten_quan}">${qData.ten_quan}</h5>
                                ${qData.is_xac_thuc ? '<span class="material-symbols-outlined text-blue-500 text-[13px] shrink-0 fill-1" title="Tick xanh">verified</span>' : ''}
                            </div>
                            <p class="text-[10px] text-slate-500 truncate" title="${qData.dia_chi}">${qData.dia_chi || 'Ẩm thực'}</p>
                        </div>
                    </div>
                    <button type="button" onclick="adminAddFeatured('${qData.id}', this)" class="px-2 py-1 rounded-lg text-[11px] font-bold text-amber-900 bg-amber-100 hover:bg-amber-500 hover:text-slate-950 border border-amber-300 hover:border-amber-400 shadow-2xs hover:shadow-xs transition-all flex items-center gap-0.5 shrink-0 active:scale-95 cursor-pointer" title="Đánh dấu đưa quán này lên danh sách nổi bật">
                        <span class="material-symbols-outlined text-[13px] text-amber-700 fill-1">star</span>
                        <span>+ Nổi Bật</span>
                    </button>
                `;
                candidatesList.insertBefore(newCard, candidatesList.firstChild);
            }

            updateFeaturedCount(-1);
            updateCandidateCount(+1);
        })
        .catch(err => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalBtnText;
            showAdminToast('Lỗi Thao Tác', err.message || 'Không thể gỡ quán khỏi danh sách nổi bật.', 'error');
        });
    }

    // ========================================================
    // 3. MỞ & ĐÓNG MODAL SỬA NHANH QUÁN NỔI BẬT
    // ========================================================
    function openQuickEditModal(btn) {
        const data = btn.dataset;
        document.getElementById('edit-quan-id').value = data.id || '';
        document.getElementById('edit-ten-quan').value = data.name || '';
        document.getElementById('edit-loai-hinh').value = data.category || '';
        document.getElementById('edit-so-dien-thoai').value = data.phone || '';
        document.getElementById('edit-gio-mo-cua').value = data.open || '';
        document.getElementById('edit-gio-dong-cua').value = data.close || '';
        document.getElementById('edit-shopeefood-url').value = data.shopee || '';
        document.getElementById('edit-tiktok-url').value = data.tiktok || '';
        document.getElementById('edit-is-xac-thuc').checked = (data.verified === '1');
        document.getElementById('edit-is-noi-bat').checked = (data.featured === '1');

        const modal = document.getElementById('quick-edit-modal');
        const box = document.getElementById('quick-edit-modal-box');
        modal.classList.remove('hidden');
        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeQuickEditModal() {
        const modal = document.getElementById('quick-edit-modal');
        const box = document.getElementById('quick-edit-modal-box');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    // ========================================================
    // 4. SUBMIT FORM SỬA NHANH QUÁN NỔI BẬT (CHẠY NGAY TRONG TRANG, KHÔNG RELOAD)
    // ========================================================
    function submitQuickEdit(event) {
        event.preventDefault();
        const quanId = document.getElementById('edit-quan-id').value;
        const submitBtn = document.getElementById('btn-submit-quick-edit');
        if (!quanId || submitBtn.disabled) return;

        const payload = {
            ten_quan: document.getElementById('edit-ten-quan').value,
            loai_hinh_kinh_doanh: document.getElementById('edit-loai-hinh').value,
            so_dien_thoai: document.getElementById('edit-so-dien-thoai').value,
            gio_mo_cua: document.getElementById('edit-gio-mo-cua').value || null,
            gio_dong_cua: document.getElementById('edit-gio-dong-cua').value || null,
            shopeefood_url: document.getElementById('edit-shopeefood-url').value || null,
            tiktok_url: document.getElementById('edit-tiktok-url').value || null,
            is_xac_thuc: document.getElementById('edit-is-xac-thuc').checked ? 1 : 0,
            is_noi_bat: document.getElementById('edit-is-noi-bat').checked ? 1 : 0,
        };

        const originalBtnHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-symbols-outlined text-[15px] animate-spin">progress_activity</span> Đang lưu...';

        fetch(`/admin/quan-noi-bat/${quanId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
            return data;
        })
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;

            const q = data.quan || {};
            const tenQuan = q.ten_quan || payload.ten_quan;
            const msg = data.message || `Đã cập nhật thông tin quán "${tenQuan}" và đồng bộ dữ liệu toàn hệ thống website!`;
            
            closeQuickEditModal();
            showAdminToast('Cập Nhật Thành Công', msg, 'success');
            triggerSyncAnimation('Đã đồng bộ website');

            if (typeof clearAdminPageCache === 'function') {
                clearAdminPageCache();
            }

            // Cập nhật DOM của hàng trong bảng ngay lập tức (Single-Page SPA)
            const row = document.getElementById(`featured-row-${quanId}`);
            if (row) {
                // Update datasets
                row.setAttribute('data-name', (q.ten_quan || '').toLowerCase());
                row.setAttribute('data-category', q.loai_hinh_kinh_doanh || '');

                // Update text tên quán
                const nameBtn = row.querySelector('button[onclick*="openQuickViewModalById"] span');
                if (nameBtn) nameBtn.innerText = q.ten_quan;

                // Update badge tick xanh
                const verifiedContainer = row.querySelector('.flex.items-center.gap-1.flex-nowrap');
                let verifiedBadge = verifiedContainer ? verifiedContainer.querySelector('.material-symbols-outlined.text-blue-500') : null;
                if (q.is_xac_thuc) {
                    if (!verifiedBadge && verifiedContainer) {
                        const vSpan = document.createElement('span');
                        vSpan.className = 'material-symbols-outlined text-blue-500 text-[14px] shrink-0 fill-1';
                        vSpan.title = 'Quán đã cấp Tick Xanh xác thực';
                        vSpan.innerText = 'verified';
                        verifiedContainer.appendChild(vSpan);
                    }
                } else if (verifiedBadge) {
                    verifiedBadge.remove();
                }

                // Update category badge
                const catBadge = row.querySelector('.text-\\[10px\\].text-slate-500 span.bg-slate-100');
                if (catBadge) catBadge.innerText = q.loai_hinh_kinh_doanh || 'Ẩm thực';

                // Update button datasets
                const editBtn = row.querySelector('.btn-action-edit');
                if (editBtn) {
                    editBtn.dataset.name = q.ten_quan || '';
                    editBtn.dataset.category = q.loai_hinh_kinh_doanh || '';
                    editBtn.dataset.phone = q.so_dien_thoai || '';
                    editBtn.dataset.open = (q.gio_mo_cua || '').substring(0, 5);
                    editBtn.dataset.close = (q.gio_dong_cua || '').substring(0, 5);
                    editBtn.dataset.shopee = q.shopeefood_url || '';
                    editBtn.dataset.tiktok = q.tiktok_url || '';
                    editBtn.dataset.verified = q.is_xac_thuc ? '1' : '0';
                    editBtn.dataset.featured = q.is_noi_bat ? '1' : '0';
                }

                const viewBtn = row.querySelector('.btn-action-view');
                if (viewBtn) {
                    viewBtn.dataset.name = q.ten_quan || '';
                    viewBtn.dataset.category = q.loai_hinh_kinh_doanh || '';
                    viewBtn.dataset.phone = q.so_dien_thoai || '';
                    viewBtn.dataset.open = (q.gio_mo_cua || '').substring(0, 5);
                    viewBtn.dataset.close = (q.gio_dong_cua || '').substring(0, 5);
                    viewBtn.dataset.shopee = q.shopeefood_url || '';
                    viewBtn.dataset.tiktok = q.tiktok_url || '';
                    viewBtn.dataset.verified = q.is_xac_thuc ? '1' : '0';
                    viewBtn.dataset.featured = q.is_noi_bat ? '1' : '0';
                }
            }
        })
        .catch(err => {
            showAdminToast('Lỗi Cập Nhật', err.message || 'Không thể lưu thông tin quán.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        });
    }

    // ========================================================
    // 5. MỞ & ĐÓNG MODAL XEM CHI TIẾT HỒ SƠ QUÁN (CHẠY TRONG TRANG)
    // ========================================================
    function openQuickViewModal(btn) {
        const d = btn.dataset;
        document.getElementById('view-modal-name').innerText = d.name || 'Quán ăn';
        document.getElementById('view-modal-category').innerText = d.category || 'Ẩm thực';
        document.getElementById('view-modal-address').innerText = d.address || 'Chưa cập nhật địa chỉ';
        document.getElementById('view-modal-phone').innerText = d.phone || 'Chưa có số điện thoại';

        const openTime = d.open || '';
        const closeTime = d.close || '';
        const isOpen = (d.isOpen === '1');
        const hoursEl = document.getElementById('view-modal-hours');
        if (openTime && closeTime) {
            hoursEl.innerHTML = `
                <span class="material-symbols-outlined text-[15px] ${isOpen ? 'text-emerald-500' : 'text-slate-400'}">schedule</span>
                <span>${openTime} - ${closeTime}</span>
                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold ${isOpen ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'}">${isOpen ? 'Đang mở cửa' : 'Đóng cửa'}</span>
            `;
        } else {
            hoursEl.innerHTML = '<span class="text-slate-400 italic font-normal">24/7 hoặc chưa đặt giờ</span>';
        }

        document.getElementById('view-modal-views-owner').innerText = `${d.views || 0} lượt xem • Quản lý: ${d.owner || 'Hệ thống'}`;
        
        const imgEl = document.getElementById('view-modal-img');
        if (d.image) {
            imgEl.src = d.image;
        } else {
            imgEl.src = 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=120&q=80';
        }

        // Tick xanh
        const verifiedEl = document.getElementById('view-modal-verified');
        if (d.verified === '1') {
            verifiedEl.classList.remove('hidden');
        } else {
            verifiedEl.classList.add('hidden');
        }

        // Trạng thái web
        const statusBadge = document.getElementById('view-modal-status-badge');
        if (d.status === 'da_duyet') {
            statusBadge.className = 'px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300';
            statusBadge.innerText = 'Đang hiển thị Web';
        } else {
            statusBadge.className = 'px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300';
            statusBadge.innerText = (d.status === 'chua_duyet' ? 'Chờ duyệt' : 'Đang tạm khóa');
        }

        // Shopee / TikTok links
        const linksBox = document.getElementById('view-modal-links-box');
        const shopeeLink = document.getElementById('view-modal-shopee');
        const tiktokLink = document.getElementById('view-modal-tiktok');
        let hasLinks = false;

        if (d.shopee) {
            shopeeLink.href = d.shopee;
            shopeeLink.classList.remove('hidden');
            shopeeLink.classList.add('inline-flex');
            hasLinks = true;
        } else {
            shopeeLink.classList.add('hidden');
            shopeeLink.classList.remove('inline-flex');
        }

        if (d.tiktok) {
            tiktokLink.href = d.tiktok;
            tiktokLink.classList.remove('hidden');
            tiktokLink.classList.add('inline-flex');
            hasLinks = true;
        } else {
            tiktokLink.classList.add('hidden');
            tiktokLink.classList.remove('inline-flex');
        }

        if (hasLinks) {
            linksBox.classList.remove('hidden');
        } else {
            linksBox.classList.add('hidden');
        }

        // Nút chuyển sang sửa
        const editBtn = document.getElementById('btn-view-modal-edit');
        editBtn.onclick = function() {
            closeQuickViewModal();
            setTimeout(() => {
                const row = document.getElementById(`featured-row-${d.id}`);
                const rowEditBtn = row ? row.querySelector('.btn-action-edit') : null;
                if (rowEditBtn) openQuickEditModal(rowEditBtn);
            }, 250);
        };

        const modal = document.getElementById('quick-view-modal');
        const box = document.getElementById('quick-view-modal-box');
        modal.classList.remove('hidden');
        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function openQuickViewModalById(id) {
        const row = document.getElementById(`featured-row-${id}`);
        if (!row) return;
        const viewBtn = row.querySelector('.btn-action-view');
        if (viewBtn) openQuickViewModal(viewBtn);
    }

    function closeQuickViewModal() {
        const modal = document.getElementById('quick-view-modal');
        const box = document.getElementById('quick-view-modal-box');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    // ========================================================
    // 6. MỞ & ĐÓNG MODAL THÊM QUÁN NÂNG CAO
    // ========================================================
    function openAddFeaturedModal() {
        const modal = document.getElementById('add-featured-modal');
        const box = document.getElementById('add-featured-modal-box');
        modal.classList.remove('hidden');
        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
            document.getElementById('modal-search-input').focus();
            fetchAvailableVenues('');
        }, 10);
    }

    function closeAddFeaturedModal() {
        const modal = document.getElementById('add-featured-modal');
        const box = document.getElementById('add-featured-modal-box');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    let searchDebounceTimer = null;
    function debounceModalSearch() {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            const q = document.getElementById('modal-search-input').value;
            const loaiHinh = document.getElementById('modal-filter-category').value;
            fetchAvailableVenues(q, loaiHinh);
        }, 300);
    }

    function fetchAvailableVenues(q = '', loaiHinh = '') {
        const resultsBox = document.getElementById('modal-search-results');
        resultsBox.innerHTML = '<div class="p-8 text-center text-slate-400 text-xs flex items-center justify-center gap-2"><span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span> Đang tìm kiếm quán phù hợp...</div>';

        const url = new URL('{{ route("admin.quan-noi-bat.search-available") }}', window.location.origin);
        if (q) url.searchParams.set('q', q);
        if (loaiHinh) url.searchParams.set('loai_hinh', loaiHinh);

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            const list = data.data || [];
            if (list.length === 0) {
                resultsBox.innerHTML = '<div class="p-8 text-center text-slate-400 text-xs font-medium">Không tìm thấy quán nào phù hợp hoặc các quán này đã được gắn nổi bật.</div>';
                return;
            }

            let html = '';
            list.forEach(v => {
                html += `
                    <div class="p-2.5 rounded-xl border border-slate-200 bg-white hover:border-amber-400 hover:bg-amber-50/20 transition-all flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <img src="${v.anh_bia_url}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shrink-0 shadow-2xs" onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=100&q=80'">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <h5 class="text-xs font-bold text-slate-900 truncate">${v.ten_quan}</h5>
                                    ${v.is_xac_thuc ? '<span class="material-symbols-outlined text-blue-500 text-[14px] fill-1" title="Tick xanh">verified</span>' : ''}
                                </div>
                                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 mt-0.5">
                                    <span class="px-1.5 py-0.2 bg-slate-100 rounded text-slate-700 font-semibold">${v.loai_hinh_kinh_doanh}</span>
                                    <span class="truncate">${v.dia_chi || 'Chưa có địa chỉ'}</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="adminAddFeatured('${v.id}', this)" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-950 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 shadow-xs transition-all flex items-center gap-1 shrink-0 border border-amber-300 active:scale-95 cursor-pointer">
                            <span class="material-symbols-outlined text-[15px] fill-1">star</span>
                            <span>Ghim Nổi Bật</span>
                        </button>
                    </div>
                `;
            });
            resultsBox.innerHTML = html;
        })
        .catch(err => {
            resultsBox.innerHTML = `<div class="p-6 text-center text-rose-500 text-xs">Lỗi tìm kiếm: ${err.message}</div>`;
        });
    }

    // ========================================================
    // 7. LỌC TỨC THÌ BẢNG QUÁN ĐANG NỔI BẬT (CLIENT-SIDE FILTER)
    // ========================================================
    function filterFeaturedTable() {
        const keyword = (document.getElementById('featured-quick-search').value || '').toLowerCase().trim();
        const category = document.getElementById('featured-filter-category').value;
        const rows = document.querySelectorAll('.featured-item-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const address = row.getAttribute('data-address') || '';
            const rowCat = row.getAttribute('data-category') || '';

            const matchKeyword = !keyword || name.includes(keyword) || address.includes(keyword);
            const matchCategory = !category || rowCat === category;

            if (matchKeyword && matchCategory) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const badge = document.getElementById('featured-count-badge');
        if (badge) badge.innerText = visibleCount;
    }

    function resetFeaturedFilter() {
        document.getElementById('featured-quick-search').value = '';
        document.getElementById('featured-filter-category').value = '';
        filterFeaturedTable();
        showAdminToast('Đã Xóa Bộ Lọc', 'Đang hiển thị toàn bộ danh sách quán nổi bật.', 'info', 3000);
    }

    // ========================================================
    // 8. LỌC TỨC THÌ KHO QUÁN ĐỀ CỬ BÊN PHẢI
    // ========================================================
    function filterCandidateVenues() {
        const keyword = (document.getElementById('candidate-live-search').value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.candidate-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const address = card.getAttribute('data-address') || '';
            if (!keyword || name.includes(keyword) || address.includes(keyword)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const badge = document.getElementById('candidates-count-badge');
        if (badge) badge.innerText = visibleCount;
    }

    // ========================================================
    // 9. CHUYỂN ĐỔI TAB TRÊN MOBILE (< 1024px)
    // ========================================================
    function switchMobileTab(tab) {
        const panelFeatured = document.getElementById('panel-featured');
        const panelCandidates = document.getElementById('panel-candidates');
        const btnFeatured = document.getElementById('tab-btn-featured');
        const btnCandidates = document.getElementById('tab-btn-candidates');

        if (tab === 'featured') {
            panelFeatured.classList.remove('hidden');
            panelCandidates.classList.add('hidden');
            btnFeatured.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
            btnFeatured.classList.remove('text-slate-600');
            btnCandidates.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
            btnCandidates.classList.add('text-slate-600');
        } else {
            panelFeatured.classList.add('hidden');
            panelCandidates.classList.remove('hidden');
            btnCandidates.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
            btnCandidates.classList.remove('text-slate-600');
            btnFeatured.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
            btnFeatured.classList.add('text-slate-600');
        }
    }

    // Helper cập nhật số lượng KPI tức thì
    function updateFeaturedCount(delta) {
        const el = document.getElementById('kpi-total-featured');
        const badge = document.getElementById('featured-count-badge');
        const mobileBadge = document.getElementById('mobile-featured-badge');
        if (el) {
            const current = parseInt(el.innerText || '0', 10);
            el.innerText = Math.max(0, current + delta);
        }
        if (badge) {
            const current = parseInt(badge.innerText || '0', 10);
            badge.innerText = Math.max(0, current + delta);
        }
        if (mobileBadge) {
            const current = parseInt(mobileBadge.innerText || '0', 10);
            mobileBadge.innerText = Math.max(0, current + delta);
        }
    }

    function updateCandidateCount(delta) {
        const badge = document.getElementById('candidates-count-badge');
        const mobileBadge = document.getElementById('mobile-candidates-badge');
        if (badge) {
            const current = parseInt(badge.innerText || '0', 10);
            badge.innerText = Math.max(0, current + delta);
        }
        if (mobileBadge) {
            const current = parseInt(mobileBadge.innerText || '0', 10);
            mobileBadge.innerText = Math.max(0, current + delta);
        }
    }
</script>
@endsection

