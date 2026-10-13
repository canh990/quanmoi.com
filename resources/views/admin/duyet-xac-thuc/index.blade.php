@extends('admin.layout')

@section('title', 'Trung Tâm Thẩm Định & Phê Duyệt Quán - Quán Mới Doanh Nghiệp')
@section('page-title', 'Trung Tâm Duyệt & Xác Thực')

@section('content')
<div class="space-y-6">
    {{-- Header Banner & Quick Actions --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-6 shadow-xs relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-amber-500/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500/10 text-amber-700 border border-amber-200/60">
                    <span class="material-symbols-outlined text-[13px]">verified_user</span>
                    Enterprise Hub
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Đồng bộ thời gian thực
                </span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                Trung Tâm Thẩm Định & Phê Duyệt Quán Ăn
            </h2>
            <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Hệ thống rà soát hồ sơ cơ sở kinh doanh, chứng thực tính hợp pháp của hình ảnh không gian/thực đơn và cấp phép hoạt động công khai.
            </p>
        </div>

        {{-- Quick Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 relative z-10 shrink-0">
            <div class="relative w-full sm:w-64">
                <span class="material-symbols-outlined text-[18px] text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
                <input 
                    type="text" 
                    id="duyet-quick-search" 
                    oninput="filterCurrentTabTable(this.value)" 
                    placeholder="Lọc nhanh quán, chủ quán, địa chỉ..." 
                    class="w-full pl-9 pr-8 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all placeholder:text-slate-400 font-medium"
                >
                <button 
                    type="button" 
                    id="clear-search-btn" 
                    onclick="clearSearchInput()" 
                    class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>

            <div class="flex items-center gap-2 flex-nowrap shrink-0">
                <button 
                    type="button" 
                    id="btn-sync-all" 
                    onclick="syncAllDuyetData(this)" 
                    class="btn-enterprise btn-enterprise-secondary" 
                    title="Đồng bộ dữ liệu tức thì"
                >
                    <span class="material-symbols-outlined text-[16px] text-slate-500" id="sync-icon">sync</span>
                    <span>Đồng Bộ</span>
                </button>

                <a 
                    href="{{ route('admin.quan.index') }}" 
                    class="btn-enterprise btn-enterprise-dark"
                >
                    <span class="material-symbols-outlined text-[16px] text-amber-400">storefront</span>
                    <span>Tất Cả Quán Ăn</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Top KPI Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Quán Chờ Duyệt --}}
        <div onclick="switchDuyetTab('pending')" class="group bg-white hover:bg-amber-50/20 rounded-2xl border border-slate-200 hover:border-amber-400 p-5 shadow-xs transition-all duration-200 cursor-pointer relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">hourglass_top</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-md border border-amber-300/80">
                    Cần duyệt
                </span>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight" id="stat-count-pending">{{ $stats['pending_quan'] }}</div>
            <div class="text-xs font-bold text-slate-700 mt-1">Quán Chờ Duyệt</div>
            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                <span>Hồ sơ mới gửi lên</span>
                <span class="material-symbols-outlined text-[13px] text-amber-600 group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </div>
        </div>

        {{-- Card 2: Quán Bị Từ Chối / Không Duyệt --}}
        <div onclick="switchDuyetTab('rejected')" class="group bg-white hover:bg-rose-50/20 rounded-2xl border border-slate-200 hover:border-rose-400 p-5 shadow-xs transition-all duration-200 cursor-pointer relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">cancel</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-800 bg-rose-50 px-2.5 py-0.5 rounded-md border border-rose-300/80">
                    Từ chối / Khóa
                </span>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight" id="stat-count-rejected">{{ $stats['rejected_quan'] }}</div>
            <div class="text-xs font-bold text-slate-700 mt-1">Quán Không Duyệt</div>
            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                <span>Có thể xem xét duyệt lại</span>
                <span class="material-symbols-outlined text-[13px] text-rose-600 group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </div>
        </div>

        {{-- Card 3: Quán Đã Phê Duyệt --}}
        <div onclick="switchDuyetTab('approved')" class="group bg-white hover:bg-emerald-50/20 rounded-2xl border border-slate-200 hover:border-emerald-400 p-5 shadow-xs transition-all duration-200 cursor-pointer relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">task_alt</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-300/80">
                    Công khai
                </span>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight" id="stat-count-approved">{{ $stats['approved_quan'] }}</div>
            <div class="text-xs font-bold text-slate-700 mt-1">Quán Đã Phê Duyệt</div>
            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                <span>Đang hiển thị hoạt động</span>
                <span class="material-symbols-outlined text-[13px] text-emerald-600 group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </div>
        </div>

        {{-- Card 4: Tài Khoản Chưa Xác Thực --}}
        <div onclick="switchDuyetTab('users')" class="group bg-white hover:bg-indigo-50/20 rounded-2xl border border-slate-200 hover:border-indigo-400 p-5 shadow-xs transition-all duration-200 cursor-pointer relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">badge</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-800 bg-indigo-50 px-2.5 py-0.5 rounded-md border border-indigo-300/80">
                    Tài khoản
                </span>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight" id="stat-count-users">{{ $stats['unverified_users'] }}</div>
            <div class="text-xs font-bold text-slate-700 mt-1">User Chưa Xác Thực</div>
            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                <span>Chưa xác minh email / SĐT</span>
                <span class="material-symbols-outlined text-[13px] text-indigo-600 group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </div>
        </div>
    </div>

    {{-- Enterprise Segmented Tabs Bar --}}
    <div class="flex items-center justify-between border-b border-slate-200 pb-2 overflow-x-auto">
        <div class="inline-flex p-1.5 bg-slate-100/90 rounded-2xl border border-slate-200 gap-1 overflow-x-auto max-w-full flex-nowrap shrink-0">
            {{-- Tab 1 --}}
            <button type="button" id="tab-btn-pending" onclick="switchDuyetTab('pending')" class="duyet-tab-btn px-4 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 cursor-pointer bg-white text-slate-900 shadow-xs whitespace-nowrap shrink-0">
                <span class="material-symbols-outlined text-[18px] text-amber-500">storefront</span>
                <span class="whitespace-nowrap">Quán Chờ Phê Duyệt</span>
                <span id="tab-badge-pending" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white shrink-0">
                    {{ $stats['pending_quan'] }}
                </span>
            </button>

            {{-- Tab 2 --}}
            <button type="button" id="tab-btn-rejected" onclick="switchDuyetTab('rejected')" class="duyet-tab-btn px-4 py-2 rounded-xl text-xs font-medium text-slate-600 hover:text-slate-900 transition-all inline-flex items-center gap-2 cursor-pointer whitespace-nowrap shrink-0">
                <span class="material-symbols-outlined text-[18px] text-rose-500">do_not_disturb_on</span>
                <span class="whitespace-nowrap">Quán Bị Từ Chối / Không Duyệt</span>
                <span id="tab-badge-rejected" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white shrink-0">
                    {{ $stats['rejected_quan'] }}
                </span>
            </button>

            {{-- Tab 3 --}}
            <button type="button" id="tab-btn-approved" onclick="switchDuyetTab('approved')" class="duyet-tab-btn px-4 py-2 rounded-xl text-xs font-medium text-slate-600 hover:text-slate-900 transition-all inline-flex items-center gap-2 cursor-pointer whitespace-nowrap shrink-0">
                <span class="material-symbols-outlined text-[18px] text-emerald-500">task_alt</span>
                <span class="whitespace-nowrap">Đã Phê Duyệt Gần Đây</span>
                <span id="tab-badge-approved" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500 text-white shrink-0">
                    {{ count($recentApproved) }}
                </span>
            </button>

            {{-- Tab 4 --}}
            <button type="button" id="tab-btn-users" onclick="switchDuyetTab('users')" class="duyet-tab-btn px-4 py-2 rounded-xl text-xs font-medium text-slate-600 hover:text-slate-900 transition-all inline-flex items-center gap-2 cursor-pointer whitespace-nowrap shrink-0">
                <span class="material-symbols-outlined text-[18px] text-indigo-500">verified_user</span>
                <span class="whitespace-nowrap">Tài Khoản Chưa Xác Thực</span>
                <span id="tab-badge-users" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-500 text-white shrink-0">
                    {{ $stats['unverified_users'] }}
                </span>
            </button>
        </div>
    </div>

    {{-- ==================== TAB 1: PENDING QUAN ==================== --}}
    <div id="tab-pane-pending" class="duyet-tab-pane">
        <div class="bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500">hourglass_empty</span>
                        Danh Sách Quán Chờ Phê Duyệt (<span id="pane-count-pending">{{ $pendingQuan->total() }}</span>)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Các hồ sơ địa điểm quán ăn mới gửi lên chờ Ban quản trị xét duyệt để hiển thị công khai.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50/80 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                        <tr>
                            <th class="p-3.5 min-w-[320px]">Hồ Sơ Quán & Hình Ảnh</th>
                            <th class="p-3.5 min-w-[190px]">Chủ Quán / Liên Hệ</th>
                            <th class="p-3.5 min-w-[180px] max-w-[240px]">Khu Vực & Địa Chỉ</th>
                            <th class="p-3.5 whitespace-nowrap min-w-[110px] w-[110px]">Thời Gian</th>
                            <th class="p-3.5 text-right whitespace-nowrap min-w-[210px] w-[210px]">Thao Tác Thẩm Định</th>
                        </tr>
                    </thead>
                    <tbody id="pending-quan-tbody" class="divide-y divide-slate-100">
                        @forelse($pendingQuan as $q)
                            @php
                                $galleryUrls = $q->hinhAnh ? $q->hinhAnh->map(fn($img) => $img->duong_dan)->values()->all() : [];
                                $mediaPayload = [
                                    'id' => $q->id,
                                    'ten_quan' => $q->ten_quan,
                                    'loai_hinh' => $q->loai_hinh_kinh_doanh,
                                    'dia_chi' => $q->dia_chi_chi_tiet ?? $q->dia_chi,
                                    'tinh_thanh' => $q->ten_tinh_thanh,
                                    'owner_name' => $q->chuQuan->ho_ten ?? 'Chưa xác định',
                                    'owner_phone' => $q->chuQuan->so_dien_thoai ?? $q->so_dien_thoai ?? 'Chưa có SĐT',
                                    'owner_email' => $q->chuQuan->email ?? 'Chưa có email',
                                    'created_at' => $q->created_at ? $q->created_at->format('d/m/Y H:i') : '',
                                    'anh_bia_url' => $q->anh_bia_url,
                                    'gallery' => $galleryUrls,
                                    'gio_mo_cua' => $q->gio_mo_cua,
                                    'gio_dong_cua' => $q->gio_dong_cua,
                                    'gia_nho_nhat' => $q->gia_nho_nhat,
                                    'gia_lon_nhat' => $q->gia_lon_nhat,
                                    'trang_thai' => $q->trang_thai,
                                ];
                            @endphp
                            <tr id="quan-row-{{ $q->id }}" class="duyet-table-row hover:bg-slate-50/80 transition-colors" data-search="{{ strtolower($q->ten_quan . ' ' . ($q->chuQuan->ho_ten ?? '') . ' ' . ($q->dia_chi_chi_tiet ?? '') . ' ' . ($q->loai_hinh_kinh_doanh ?? '')) }}">
                                <td class="p-3.5 font-medium min-w-[320px] max-w-[360px] overflow-hidden">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        {{-- Thumbnail With Zoom --}}
                                        <div onclick='openVenueMediaModal(@json($mediaPayload))' class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200/90 cursor-pointer group relative shadow-2xs hover:ring-2 hover:ring-amber-500 hover:border-amber-400 transition-all" title="Bấm để xem ảnh và thẩm định chi tiết">
                                            <img src="{{ $q->anh_bia_url }}" alt="{{ $q->ten_quan }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&auto=format&fit=crop&q=80';">
                                            <div class="absolute inset-0 bg-black/35 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white">
                                                <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                                            </div>
                                            <span class="absolute bottom-1 right-1 bg-black/70 backdrop-blur-xs text-white text-[9px] font-bold px-1.5 py-0.2 rounded-md">
                                                {{ count($galleryUrls) + 1 }}
                                            </span>
                                        </div>

                                        <div class="min-w-0 flex-1 overflow-hidden space-y-1">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="font-bold text-slate-900 text-sm truncate max-w-[170px]" title="{{ $q->ten_quan }}">
                                                    {{ $q->ten_quan }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300 shrink-0 whitespace-nowrap">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    <span>Chờ duyệt</span>
                                                </span>
                                            </div>
                                            @if($q->loai_hinh_kinh_doanh)
                                                <div class="mt-0.5">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200 max-w-full truncate" title="{{ $q->loai_hinh_kinh_doanh }}">
                                                        🏷️ {{ $q->loai_hinh_kinh_doanh }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="flex items-center gap-1.5 pt-0.5 flex-wrap">
                                                <button type="button" onclick='openVenueMediaModal(@json($mediaPayload))' class="btn-enterprise btn-enterprise-photo" title="Xem bộ sưu tập hình ảnh quán">
                                                    <span class="material-symbols-outlined text-[15px]">photo_library</span>
                                                    <span>Xem ảnh</span>
                                                    <span class="btn-enterprise-photo-count">{{ count($galleryUrls) + 1 }}</span>
                                                </button>
                                                @if($q->slug)
                                                    <a href="{{ route('quan.detail', $q->slug) }}" class="btn-enterprise btn-enterprise-secondary" title="Xem trang quán thực tế ngoài website">
                                                        <span>Trang quán</span>
                                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5 min-w-[180px] max-w-[210px] overflow-hidden">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($q->chuQuan->ho_ten ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1 space-y-0.5 overflow-hidden">
                                            <div class="font-bold text-slate-900 truncate text-xs block" title="{{ $q->chuQuan->ho_ten ?? 'Chưa xác định' }}">{{ $q->chuQuan->ho_ten ?? 'Chưa xác định' }}</div>
                                            <div class="text-[11px] text-slate-600 font-semibold truncate flex items-center gap-0.5" title="{{ $q->chuQuan->so_dien_thoai ?? $q->so_dien_thoai ?? 'Chưa có SĐT' }}">
                                                <span class="material-symbols-outlined text-[12px] text-slate-400 shrink-0">call</span>
                                                <span class="truncate">{{ $q->chuQuan->so_dien_thoai ?? $q->so_dien_thoai ?? 'Chưa có SĐT' }}</span>
                                            </div>
                                            <div class="text-[10px] text-slate-400 truncate font-mono block" title="{{ $q->chuQuan->email ?? 'Chưa có email' }}">📧 {{ $q->chuQuan->email ?? 'Chưa có email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5 text-slate-600 min-w-[180px] max-w-[240px] overflow-hidden">
                                    <div class="space-y-1 min-w-0">
                                        <div class="font-bold text-slate-800 text-xs leading-snug line-clamp-2 break-words" title="{{ $q->dia_chi_chi_tiet ?? $q->dia_chi }}">
                                            {{ $q->dia_chi_chi_tiet ?? $q->dia_chi ?: 'Chưa cập nhật chi tiết' }}
                                        </div>
                                        @if($q->ten_tinh_thanh || $q->ten_quan_huyen)
                                            <div class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-700 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-md max-w-full overflow-hidden" title="{{ $q->ten_quan_huyen ? $q->ten_quan_huyen . ', ' : '' }}{{ $q->ten_tinh_thanh }}">
                                                <span class="material-symbols-outlined text-[13px] text-slate-500 shrink-0">location_on</span>
                                                <span class="truncate">{{ $q->ten_quan_huyen ? $q->ten_quan_huyen . ', ' : '' }}{{ $q->ten_tinh_thanh }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-3.5 text-slate-500 whitespace-nowrap min-w-[110px] w-[110px] shrink-0">
                                    <div class="font-bold text-slate-800 whitespace-nowrap text-xs">{{ $q->created_at ? $q->created_at->diffForHumans() : 'Vừa xong' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 whitespace-nowrap">{{ $q->created_at ? $q->created_at->format('d/m/Y H:i') : '' }}</div>
                                </td>
                                <td class="p-3.5 text-right whitespace-nowrap min-w-[210px] w-[210px] shrink-0">
                                    <div class="flex items-center justify-end gap-2 flex-nowrap shrink-0">
                                        <button type="button" onclick="confirmApproveQuan('{{ $q->id }}', '{{ addslashes($q->ten_quan) }}')" class="btn-enterprise btn-enterprise-success" title="Phê duyệt hồ sơ quán này">
                                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                            <span class="uppercase tracking-wider">Duyệt</span>
                                        </button>

                                        <button type="button" onclick="confirmRejectQuan('{{ $q->id }}', '{{ addslashes($q->ten_quan) }}')" class="btn-enterprise btn-enterprise-danger" title="Không duyệt hồ sơ quán này">
                                            <span class="material-symbols-outlined text-[16px]">cancel</span>
                                            <span>Không duyệt</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="pending-quan-empty-row">
                                <td colspan="5" class="p-12 text-center text-xs text-slate-500 font-medium">
                                    <span class="material-symbols-outlined text-4xl text-emerald-400 block mb-2">task_alt</span>
                                    Tuyệt vời! Hiện không còn quán nào đang chờ phê duyệt.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $pendingQuan->appends(request()->except('pending_page'))->fragment('pending')->links() }}</div>
        </div>
    </div>

    {{-- ==================== TAB 2: REJECTED QUAN ==================== --}}
    <div id="tab-pane-rejected" class="duyet-tab-pane hidden">
        <div class="bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-rose-500">do_not_disturb_on</span>
                        Danh Sách Quán Bị Từ Chối / Không Duyệt (<span id="pane-count-rejected">{{ $rejectedQuan->total() }}</span>)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Các quán ăn đã bị từ chối phê duyệt hoặc khóa tạm. Bạn có thể duyệt lại khi đã bổ sung thông tin hợp lệ hoặc xóa bỏ hồ sơ.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-rose-50/50 text-slate-600 font-bold uppercase tracking-wider border-b border-rose-100 text-[11px]">
                        <tr>
                            <th class="p-3.5 min-w-[320px]">Hồ Sơ Quán & Hình Ảnh</th>
                            <th class="p-3.5 min-w-[190px]">Chủ Quán / Liên Hệ</th>
                            <th class="p-3.5 min-w-[180px] max-w-[240px]">Khu Vực & Địa Chỉ</th>
                            <th class="p-3.5 whitespace-nowrap min-w-[140px]">Trạng Thái</th>
                            <th class="p-3.5 whitespace-nowrap min-w-[110px] w-[110px]">Thời Gian Cập Nhật</th>
                            <th class="p-3.5 text-right whitespace-nowrap min-w-[210px] w-[210px]">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="rejected-quan-tbody" class="divide-y divide-slate-100">
                        @forelse($rejectedQuan as $q)
                            @php
                                $galleryUrls = $q->hinhAnh ? $q->hinhAnh->map(fn($img) => $img->duong_dan)->values()->all() : [];
                                $mediaPayload = [
                                    'id' => $q->id,
                                    'ten_quan' => $q->ten_quan,
                                    'loai_hinh' => $q->loai_hinh_kinh_doanh,
                                    'dia_chi' => $q->dia_chi_chi_tiet ?? $q->dia_chi,
                                    'tinh_thanh' => $q->ten_tinh_thanh,
                                    'owner_name' => $q->chuQuan->ho_ten ?? 'Chưa xác định',
                                    'owner_phone' => $q->chuQuan->so_dien_thoai ?? $q->so_dien_thoai ?? 'Chưa có SĐT',
                                    'owner_email' => $q->chuQuan->email ?? 'Chưa có email',
                                    'created_at' => $q->updated_at ? $q->updated_at->format('d/m/Y H:i') : '',
                                    'anh_bia_url' => $q->anh_bia_url,
                                    'gallery' => $galleryUrls,
                                    'gio_mo_cua' => $q->gio_mo_cua,
                                    'gio_dong_cua' => $q->gio_dong_cua,
                                    'gia_nho_nhat' => $q->gia_nho_nhat,
                                    'gia_lon_nhat' => $q->gia_lon_nhat,
                                    'trang_thai' => $q->trang_thai,
                                ];
                            @endphp
                            <tr id="rejected-row-{{ $q->id }}" class="duyet-table-row hover:bg-rose-50/20 transition-colors" data-search="{{ strtolower($q->ten_quan . ' ' . ($q->chuQuan->ho_ten ?? '') . ' ' . ($q->dia_chi_chi_tiet ?? '') . ' ' . ($q->loai_hinh_kinh_doanh ?? '')) }}">
                                <td class="p-3.5 font-medium min-w-[320px] max-w-[360px] overflow-hidden">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <div onclick='openVenueMediaModal(@json($mediaPayload))' class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200/90 cursor-pointer group relative shadow-2xs hover:ring-2 hover:ring-rose-400 transition-all" title="Bấm để xem ảnh quán">
                                            <img src="{{ $q->anh_bia_url }}" alt="{{ $q->ten_quan }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&auto=format&fit=crop&q=80';">
                                            <div class="absolute inset-0 bg-black/35 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white">
                                                <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                                            </div>
                                            <span class="absolute bottom-1 right-1 bg-black/70 backdrop-blur-xs text-white text-[9px] font-bold px-1.5 py-0.2 rounded-md">
                                                {{ count($galleryUrls) + 1 }}
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 overflow-hidden space-y-1">
                                            <div class="font-bold text-slate-900 text-sm truncate max-w-[200px]" title="{{ $q->ten_quan }}">{{ $q->ten_quan }}</div>
                                            @if($q->loai_hinh_kinh_doanh)
                                                <div class="mt-0.5">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200 max-w-full truncate" title="{{ $q->loai_hinh_kinh_doanh }}">🏷️ {{ $q->loai_hinh_kinh_doanh }}</span>
                                                </div>
                                            @endif
                                            <div class="flex items-center gap-1.5 pt-0.5 flex-wrap">
                                                <button type="button" onclick='openVenueMediaModal(@json($mediaPayload))' class="btn-enterprise btn-enterprise-photo" title="Xem bộ sưu tập hình ảnh quán">
                                                    <span class="material-symbols-outlined text-[16px]">photo_library</span>
                                                    <span>Xem Ảnh</span>
                                                    <span class="btn-enterprise-photo-count">{{ count($galleryUrls) + 1 }}</span>
                                                </button>
                                                <a href="{{ route('admin.quan.edit', $q->id) }}" class="btn-enterprise btn-enterprise-secondary" title="Chỉnh sửa hồ sơ quán">
                                                    <span>Sửa hồ sơ</span>
                                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5 min-w-[180px] max-w-[210px] overflow-hidden">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($q->chuQuan->ho_ten ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1 space-y-0.5 overflow-hidden">
                                            <div class="font-bold text-slate-900 truncate text-xs block" title="{{ $q->chuQuan->ho_ten ?? 'Chưa xác định' }}">{{ $q->chuQuan->ho_ten ?? 'Chưa xác định' }}</div>
                                            <div class="text-[11px] text-slate-600 font-semibold truncate flex items-center gap-0.5" title="{{ $q->chuQuan->so_dien_thoai ?? $q->so_dien_thoai ?? 'Chưa có SĐT' }}">
                                                <span class="material-symbols-outlined text-[12px] text-slate-400 shrink-0">call</span>
                                                <span class="truncate">{{ $q->chuQuan->so_dien_thoai ?? $q->so_dien_thoai ?? 'Chưa có SĐT' }}</span>
                                            </div>
                                            <div class="text-[10px] text-slate-400 truncate font-mono block" title="{{ $q->chuQuan->email ?? 'Chưa có email' }}">📧 {{ $q->chuQuan->email ?? 'Chưa có email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5 text-slate-600 min-w-[180px] max-w-[240px] overflow-hidden">
                                    <div class="space-y-1 min-w-0">
                                        <div class="font-bold text-slate-800 text-xs leading-snug line-clamp-2 break-words" title="{{ $q->dia_chi_chi_tiet ?? $q->dia_chi }}">
                                            {{ $q->dia_chi_chi_tiet ?? $q->dia_chi ?: 'Chưa cập nhật chi tiết' }}
                                        </div>
                                        @if($q->ten_tinh_thanh || $q->ten_quan_huyen)
                                            <div class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-200/70 px-2 py-0.5 rounded-lg max-w-full overflow-hidden" title="{{ $q->ten_quan_huyen ? $q->ten_quan_huyen . ', ' : '' }}{{ $q->ten_tinh_thanh }}">
                                                <span class="material-symbols-outlined text-[13px] text-rose-500 shrink-0">location_on</span>
                                                <span class="truncate">{{ $q->ten_quan_huyen ? $q->ten_quan_huyen . ', ' : '' }}{{ $q->ten_tinh_thanh }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-3.5 whitespace-nowrap min-w-[140px] w-[140px] shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-300 whitespace-nowrap shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600 mr-1.5 shrink-0"></span>
                                        <span>Bị từ chối / Khóa</span>
                                    </span>
                                </td>
                                <td class="p-3.5 text-slate-500 whitespace-nowrap min-w-[110px] w-[110px]">
                                    <div class="font-bold text-slate-800 whitespace-nowrap text-xs">{{ $q->updated_at ? $q->updated_at->diffForHumans() : '' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 whitespace-nowrap">{{ $q->updated_at ? $q->updated_at->format('d/m/Y H:i') : '' }}</div>
                                </td>
                                <td class="p-3.5 text-right whitespace-nowrap min-w-[210px] w-[210px]">
                                    <div class="flex items-center justify-end gap-2 flex-nowrap shrink-0">
                                        <button type="button" onclick="confirmReapproveQuan('{{ $q->id }}', '{{ addslashes($q->ten_quan) }}')" class="btn-enterprise btn-enterprise-success" title="Duyệt lại quán này">
                                            <span class="material-symbols-outlined text-[16px]">published_with_changes</span>
                                            <span>Duyệt Lại</span>
                                        </button>

                                        <button type="button" onclick="confirmDeleteRejectedQuan('{{ $q->id }}', '{{ addslashes($q->ten_quan) }}')" class="btn-enterprise btn-enterprise-danger" title="Xóa bỏ quán khỏi hệ thống">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                            <span>Xóa Bỏ</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="rejected-quan-empty-row">
                                <td colspan="6" class="p-12 text-center text-xs text-slate-500 font-medium">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">check_circle_outline</span>
                                    Không có quán nào trong danh sách bị từ chối / không duyệt.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $rejectedQuan->appends(request()->except('rejected_page'))->fragment('rejected')->links() }}</div>
        </div>
    </div>

    {{-- ==================== TAB 3: RECENT APPROVED ==================== --}}
    <div id="tab-pane-approved" class="duyet-tab-pane hidden">
        <div class="bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-500">task_alt</span>
                        Quán Đã Phê Duyệt Gần Đây (10 quán mới nhất)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Các địa điểm quán ăn đã được Ban quản trị phê duyệt và đang hoạt động công khai.</p>
                </div>
                <a href="{{ route('admin.quan.index') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                    Xem toàn bộ <span id="pane-count-approved-link">{{ $stats['approved_quan'] }}</span> quán đã duyệt
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-emerald-50/50 text-slate-600 font-bold uppercase tracking-wider border-b border-emerald-100 text-[11px]">
                        <tr>
                            <th class="p-3.5 min-w-[320px]">Hồ Sơ Quán & Hình Ảnh</th>
                            <th class="p-3.5 min-w-[190px]">Chủ Quán / Liên Hệ</th>
                            <th class="p-3.5 min-w-[180px] max-w-[240px]">Khu Vực & Địa Chỉ</th>
                            <th class="p-3.5 whitespace-nowrap min-w-[140px]">Trạng Thái</th>
                            <th class="p-3.5 whitespace-nowrap min-w-[110px] w-[110px]">Thời Điểm Duyệt</th>
                            <th class="p-3.5 text-right whitespace-nowrap min-w-[140px] w-[140px]">Xem & Quản Lý</th>
                        </tr>
                    </thead>
                    <tbody id="approved-quan-tbody" class="divide-y divide-slate-100">
                        @forelse($recentApproved as $q)
                            @php
                                $galleryUrls = $q->hinhAnh ? $q->hinhAnh->map(fn($img) => $img->duong_dan)->values()->all() : [];
                                $mediaPayload = [
                                    'id' => $q->id,
                                    'ten_quan' => $q->ten_quan,
                                    'loai_hinh' => $q->loai_hinh_kinh_doanh,
                                    'dia_chi' => $q->dia_chi_chi_tiet ?? $q->dia_chi,
                                    'tinh_thanh' => $q->ten_tinh_thanh,
                                    'owner_name' => $q->chuQuan->ho_ten ?? 'Chưa xác định',
                                    'owner_phone' => $q->chuQuan->so_dien_thoai ?? $q->so_dien_thoai ?? 'Chưa có SĐT',
                                    'owner_email' => $q->chuQuan->email ?? 'Chưa có email',
                                    'created_at' => $q->updated_at ? $q->updated_at->format('d/m/Y H:i') : '',
                                    'anh_bia_url' => $q->anh_bia_url,
                                    'gallery' => $galleryUrls,
                                    'gio_mo_cua' => $q->gio_mo_cua,
                                    'gio_dong_cua' => $q->gio_dong_cua,
                                    'gia_nho_nhat' => $q->gia_nho_nhat,
                                    'gia_lon_nhat' => $q->gia_lon_nhat,
                                    'trang_thai' => $q->trang_thai,
                                ];
                            @endphp
                            <tr class="duyet-table-row hover:bg-slate-50/80 transition-colors" data-search="{{ strtolower($q->ten_quan . ' ' . ($q->chuQuan->ho_ten ?? '') . ' ' . ($q->dia_chi_chi_tiet ?? '') . ' ' . ($q->loai_hinh_kinh_doanh ?? '')) }}">
                                <td class="p-3.5 font-medium min-w-[320px] max-w-[360px] overflow-hidden">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <div onclick='openVenueMediaModal(@json($mediaPayload))' class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200/90 cursor-pointer group relative shadow-2xs hover:ring-2 hover:ring-emerald-500 transition-all" title="Bấm để xem ảnh quán">
                                            <img src="{{ $q->anh_bia_url }}" alt="{{ $q->ten_quan }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&auto=format&fit=crop&q=80';">
                                            <div class="absolute inset-0 bg-black/35 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white">
                                                <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                                            </div>
                                            <span class="absolute bottom-1 right-1 bg-black/70 backdrop-blur-xs text-white text-[9px] font-bold px-1.5 py-0.2 rounded-md">
                                                {{ count($galleryUrls) + 1 }}
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 overflow-hidden space-y-1">
                                            <div class="font-bold text-slate-900 text-sm truncate max-w-[200px]" title="{{ $q->ten_quan }}">{{ $q->ten_quan }}</div>
                                            @if($q->loai_hinh_kinh_doanh)
                                                <div class="mt-0.5">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200 max-w-full truncate" title="{{ $q->loai_hinh_kinh_doanh }}">🏷️ {{ $q->loai_hinh_kinh_doanh }}</span>
                                                </div>
                                            @endif
                                            <div class="flex items-center gap-1.5 pt-0.5 flex-wrap">
                                                <button type="button" onclick='openVenueMediaModal(@json($mediaPayload))' class="btn-enterprise btn-enterprise-photo" title="Xem bộ sưu tập hình ảnh quán">
                                                    <span class="material-symbols-outlined text-[16px]">photo_library</span>
                                                    <span>Xem Ảnh</span>
                                                    <span class="btn-enterprise-photo-count">{{ count($galleryUrls) + 1 }}</span>
                                                </button>
                                                @if($q->slug)
                                                    <a href="{{ route('quan.detail', $q->slug) }}" class="btn-enterprise btn-enterprise-secondary" title="Xem trang quán thực tế ngoài website">
                                                        <span>Trang quán</span>
                                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5 min-w-[180px] max-w-[210px] overflow-hidden">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($q->chuQuan->ho_ten ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1 space-y-0.5 overflow-hidden">
                                            <div class="font-bold text-slate-900 truncate text-xs block" title="{{ $q->chuQuan->ho_ten ?? 'Chưa xác định' }}">{{ $q->chuQuan->ho_ten ?? 'Chưa xác định' }}</div>
                                            <div class="text-[11px] text-slate-500 truncate" title="{{ $q->chuQuan->email ?? 'Chưa có email' }}">📧 {{ $q->chuQuan->email ?? 'Chưa có email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5 text-slate-600 min-w-[180px] max-w-[240px] overflow-hidden">
                                    <div class="space-y-1 min-w-0">
                                        <div class="font-bold text-slate-800 text-xs leading-snug line-clamp-2 break-words" title="{{ $q->dia_chi_chi_tiet ?? $q->dia_chi }}">
                                            {{ $q->dia_chi_chi_tiet ?? $q->dia_chi ?: 'Chưa cập nhật chi tiết' }}
                                        </div>
                                        @if($q->ten_tinh_thanh || $q->ten_quan_huyen)
                                            <div class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-700 bg-rose-50 border border-rose-200/70 px-2 py-0.5 rounded-lg max-w-full overflow-hidden" title="{{ $q->ten_quan_huyen ? $q->ten_quan_huyen . ', ' : '' }}{{ $q->ten_tinh_thanh }}">
                                                <span class="material-symbols-outlined text-[13px] text-rose-500 shrink-0">location_on</span>
                                                <span class="truncate">{{ $q->ten_tinh_thanh ?? 'Toàn quốc' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-3.5 whitespace-nowrap min-w-[140px] w-[140px] shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 whitespace-nowrap shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5 shrink-0"></span>
                                        <span>Đang hoạt động</span>
                                    </span>
                                </td>
                                <td class="p-3.5 text-slate-500 whitespace-nowrap min-w-[110px] w-[110px] shrink-0">
                                    <div class="font-bold text-slate-800 whitespace-nowrap text-xs">{{ $q->updated_at ? $q->updated_at->diffForHumans() : '' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 whitespace-nowrap">{{ $q->updated_at ? $q->updated_at->format('d/m/Y H:i') : '' }}</div>
                                </td>
                                <td class="p-3.5 text-right whitespace-nowrap min-w-[140px] w-[140px]">
                                    <div class="flex items-center justify-end gap-2 flex-nowrap shrink-0">
                                        <a href="{{ route('admin.quan.edit', $q->id) }}" class="btn-enterprise btn-enterprise-dark" title="Chỉnh sửa và quản lý hồ sơ quán">
                                            <span class="material-symbols-outlined text-[16px] text-emerald-400">edit</span>
                                            <span>Quản Lý</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-xs text-slate-500 font-medium">
                                    Chưa có quán nào được duyệt gần đây.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ==================== TAB 4: UNVERIFIED USERS ==================== --}}
    <div id="tab-pane-users" class="duyet-tab-pane hidden">
        <div class="bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-600">badge</span>
                        Danh Sách Tài Khoản Chờ Xác Thực (<span id="pane-count-users">{{ $unverifiedUsers->total() }}</span>)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Danh sách thành viên đăng ký chưa hoàn tất xác thực thông tin tài khoản.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-indigo-50/40 text-slate-600 font-bold uppercase tracking-wider border-b border-indigo-100 text-[11px]">
                        <tr>
                            <th class="p-3.5 min-w-[180px] max-w-[220px]">Họ và Tên</th>
                            <th class="p-3.5 min-w-[200px] max-w-[240px]">Email</th>
                            <th class="p-3.5 min-w-[140px] whitespace-nowrap">Số Điện Thoại</th>
                            <th class="p-3.5 whitespace-nowrap min-w-[130px] w-[130px]">Ngày Đăng Ký</th>
                            <th class="p-3.5 text-right whitespace-nowrap min-w-[210px] w-[210px]">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="unverified-users-tbody" class="divide-y divide-slate-100">
                        @forelse($unverifiedUsers as $u)
                            <tr id="user-row-{{ $u->id }}" class="duyet-table-row hover:bg-slate-50 transition-all duration-300" data-search="{{ strtolower(($u->ho_ten ?? '') . ' ' . ($u->email ?? '') . ' ' . ($u->so_dien_thoai ?? '')) }}">
                                <td class="p-3.5 min-w-[180px] max-w-[220px] overflow-hidden">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center shrink-0 border border-indigo-200/50">
                                            {{ strtoupper(substr($u->ho_ten ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="font-bold text-slate-900 truncate text-xs block" title="{{ $u->ho_ten }}">{{ $u->ho_ten }}</div>
                                    </div>
                                </td>
                                <td class="p-3.5 text-slate-600 font-medium min-w-[200px] max-w-[240px] overflow-hidden">
                                    <div class="truncate text-xs block" title="{{ $u->email }}">{{ $u->email }}</div>
                                </td>
                                <td class="p-3.5 text-slate-600 font-medium whitespace-nowrap min-w-[140px]">{{ $u->so_dien_thoai ?? 'Chưa cập nhật' }}</td>
                                <td class="p-3.5 text-slate-500 whitespace-nowrap min-w-[130px] w-[130px] shrink-0">{{ $u->created_at ? $u->created_at->format('d/m/Y') : '' }}</td>
                                <td class="p-3.5 text-right whitespace-nowrap min-w-[210px] w-[210px] shrink-0">
                                    <div class="flex items-center justify-end gap-2 flex-nowrap shrink-0">
                                        <button type="button" onclick="confirmVerifyUser('{{ $u->id }}', '{{ addslashes($u->ho_ten) }}')" class="btn-enterprise btn-enterprise-indigo" title="Xác thực người dùng">
                                            <span class="material-symbols-outlined text-[16px]">verified</span>
                                            <span>Xác Thực</span>
                                        </button>

                                        <button type="button" onclick="confirmRejectUser('{{ $u->id }}', '{{ addslashes($u->ho_ten) }}')" class="btn-enterprise btn-enterprise-danger" title="Từ chối duyệt tài khoản">
                                            <span class="material-symbols-outlined text-[16px]">block</span>
                                            <span>Không Duyệt</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="unverified-users-empty-row">
                                <td colspan="5" class="p-12 text-center text-xs text-slate-500 font-medium">
                                    <span class="material-symbols-outlined text-4xl text-indigo-300 block mb-2">check_circle</span>
                                    Tất cả tài khoản thành viên đều đã xác thực đầy đủ.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $unverifiedUsers->appends(request()->except('users_page'))->fragment('users')->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab switching engine
    const duyetTabs = ['pending', 'rejected', 'approved', 'users'];
    let currentActiveTab = 'pending';

    function switchDuyetTab(tabName) {
        if (!duyetTabs.includes(tabName)) tabName = 'pending';
        currentActiveTab = tabName;

        // Update Tab Buttons
        duyetTabs.forEach(name => {
            const btn = document.getElementById(`tab-btn-${name}`);
            const pane = document.getElementById(`tab-pane-${name}`);

            if (name === tabName) {
                if (btn) {
                    btn.className = 'duyet-tab-btn px-4 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2 cursor-pointer bg-white text-slate-900 shadow-xs whitespace-nowrap shrink-0';
                }
                if (pane) {
                    pane.classList.remove('hidden');
                }
            } else {
                if (btn) {
                    btn.className = 'duyet-tab-btn px-4 py-2 rounded-xl text-xs font-medium text-slate-600 hover:text-slate-900 transition-all inline-flex items-center gap-2 cursor-pointer whitespace-nowrap shrink-0';
                }
                if (pane) {
                    pane.classList.add('hidden');
                }
            }
        });

        // Store hash in URL without jumping
        if (history.replaceState) {
            history.replaceState(null, null, '#' + tabName);
        } else {
            window.location.hash = '#' + tabName;
        }

        // Re-apply search filter if user already typed in search bar
        const searchInput = document.getElementById('duyet-quick-search');
        if (searchInput && searchInput.value) {
            filterCurrentTabTable(searchInput.value);
        }
    }

    // Client-side instant search filter across current tab table rows
    function filterCurrentTabTable(query) {
        const q = (query || '').trim().toLowerCase();
        const clearBtn = document.getElementById('clear-search-btn');
        if (clearBtn) {
            if (q.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        const activePane = document.getElementById(`tab-pane-${currentActiveTab}`);
        if (!activePane) return;

        const rows = activePane.querySelectorAll('.duyet-table-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowData = row.getAttribute('data-search') || row.innerText.toLowerCase();
            if (q === '' || rowData.includes(q)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    }

    function clearSearchInput() {
        const input = document.getElementById('duyet-quick-search');
        if (input) {
            input.value = '';
            filterCurrentTabTable('');
            input.focus();
        }
    }

    // Manual sync button
    function syncAllDuyetData(btn) {
        const icon = document.getElementById('sync-icon');
        if (icon) icon.classList.add('animate-spin');

        if (typeof checkRealtimeAdminNotifications === 'function') {
            checkRealtimeAdminNotifications();
        }

        setTimeout(() => {
            if (icon) icon.classList.remove('animate-spin');
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Đồng Bộ Dữ Liệu', 'Dữ liệu thẩm định đã được đồng bộ hóa mới nhất!', window.location.href, 'sync', 'bg-emerald-500');
            }
        }, 600);
    }

    // Listen for realtime notifications update event to sync counters
    window.addEventListener('admin:notifications-updated', function(e) {
        if (!e.detail) return;
        const data = e.detail;
        if (typeof updateAllStatsCounters === 'function') {
            updateAllStatsCounters({
                pending_quan: data.pending_quan,
                unverified_users: data.unverified_user
            });
        }
    });

    // Initialize Active Tab based on Hash or URL parameters
    document.addEventListener('DOMContentLoaded', function() {
        let activeTab = 'pending';
        const hash = window.location.hash.replace('#', '');
        const urlParams = new URLSearchParams(window.location.search);

        if (duyetTabs.includes(hash)) {
            activeTab = hash;
        } else if (urlParams.has('rejected_page')) {
            activeTab = 'rejected';
        } else if (urlParams.has('users_page')) {
            activeTab = 'users';
        }

        switchDuyetTab(activeTab);
    });
</script>
@endpush
