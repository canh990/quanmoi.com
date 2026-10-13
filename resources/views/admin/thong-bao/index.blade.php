@extends('admin.layout')

@section('title', 'Trung Tâm Thông Báo Thời Gian Thực - Quán Mới Admin')
@section('page-title', 'Trung Tâm Quản Lý & Thông Báo Thời Gian Thực')

@section('content')
<div class="space-y-6">
    {{-- Header Bar: Real-time Connection Status & Quick Controls --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[22px]">campaign</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Bảng Tin & Dữ Liệu Thông Báo Thời Gian Thực</h2>
                    <p class="text-xs text-slate-500">Toàn bộ dữ liệu thông báo được tự động hiển thị và cập nhật trực tiếp theo chu kỳ không cần tải lại (reload) trang.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-bold text-emerald-700 shadow-2xs">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                <span>Trực tiếp (Real-time Live)</span>
            </div>

            <button type="button" id="btn-manual-sync" onclick="manualSyncNotifs()" class="btn-enterprise btn-enterprise-secondary" title="Lấy dữ liệu thông báo mới nhất ngay lập tức">
                <span id="sync-icon" class="material-symbols-outlined text-[16px] text-slate-500 group-hover:rotate-180 transition-transform duration-500">sync</span>
                <span>Làm mới tức thì</span>
            </button>
        </div>
    </div>

    {{-- KPI Cards Summary: Auto Updated via Real-time Polling without Reload --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        {{-- Card 1: Tổng chờ xử lý --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs hover:border-amber-400 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tổng Chờ Duyệt</span>
                <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900" id="page-kpi-total-pending">
                {{ ($pendingQuanCount ?? 0) + ($pendingBlogCount ?? 0) + ($unverifiedUserCount ?? 0) }}
            </div>
            <p class="text-[10px] text-slate-400 mt-0.5">Tất cả mục cần admin duyệt</p>
        </div>

        {{-- Card 2: Quán chờ duyệt --}}
        <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs hover:border-amber-400 hover:bg-amber-50/30 transition-all group block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-amber-700">Quán Chờ Duyệt</span>
                <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[16px]">storefront</span>
                </span>
            </div>
            <div class="text-2xl font-black text-amber-600" id="page-kpi-pending-quan">
                {{ $pendingQuanCount ?? 0 }}
            </div>
            <p class="text-[10px] text-amber-700/80 mt-0.5 group-hover:underline">Xem danh sách quán →</p>
        </a>

        {{-- Card 3: Bài viết Blog chờ duyệt --}}
        <a href="{{ route('admin.blog.posts.pending') }}" class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs hover:border-blue-400 hover:bg-blue-50/30 transition-all group block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-blue-700">Bài Viết Blog</span>
                <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[16px]">article</span>
                </span>
            </div>
            <div class="text-2xl font-black text-blue-600" id="page-kpi-pending-blog">
                {{ $pendingBlogCount ?? 0 }}
            </div>
            <p class="text-[10px] text-blue-700/80 mt-0.5 group-hover:underline">Xem bài viết chờ duyệt →</p>
        </a>

        {{-- Card 4: Người dùng chưa xác thực --}}
        <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs hover:border-indigo-400 hover:bg-indigo-50/30 transition-all group block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-indigo-700">Chờ Xác Thực User</span>
                <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[16px]">verified_user</span>
                </span>
            </div>
            <div class="text-2xl font-black text-indigo-600" id="page-kpi-unverified-user">
                {{ $unverifiedUserCount ?? 0 }}
            </div>
            <p class="text-[10px] text-indigo-700/80 mt-0.5 group-hover:underline">Xem tài khoản chưa xác thực →</p>
        </a>

        {{-- Card 5: Thông báo phát sóng --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs hover:border-emerald-400 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Phát Sóng Hệ Thống</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[16px]">cell_tower</span>
                </span>
            </div>
            <div class="text-2xl font-black text-emerald-600" id="page-kpi-broadcast-count">
                {{ $broadcastCount ?? 0 }}
            </div>
            <p class="text-[10px] text-slate-400 mt-0.5">Thông báo đã gửi thành công</p>
        </div>
    </div>

    {{-- Main Section: Live Notification Feed (Toàn bộ dữ liệu hiển thị không cần reload) --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-4">
        {{-- Section Header & Filter Controls --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">dynamic_feed</span>
                    <span>Toàn Bộ Dữ Liệu Thông Báo Hệ Thống</span>
                </h3>
                <p class="text-xs text-slate-500">Danh sách tổng hợp các thông báo mới nhất phát sinh từ người dùng, chủ quán và ban quản trị.</p>
            </div>

            {{-- Instant Search Input --}}
            <div class="flex items-center gap-2">
                <div class="relative w-full md:w-64">
                    <span class="material-symbols-outlined text-[18px] text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
                    <input type="text" id="notif-search-input" oninput="handleSearchNotifs(this.value)" placeholder="Tìm kiếm thông báo..." class="w-full pl-9 pr-3 py-1.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all">
                </div>
            </div>
        </div>

        {{-- Filter Tabs (Client-side instant filtering without page reload) --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
            <button type="button" onclick="setFeedFilter('all')" class="feed-tab-btn active px-3 py-1.5 rounded-xl font-bold bg-slate-900 text-white transition-all shadow-2xs" data-feed-filter="all">
                Tất cả (<span id="tab-count-all">0</span>)
            </button>
            <button type="button" onclick="setFeedFilter('quan')" class="feed-tab-btn px-3 py-1.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all" data-feed-filter="quan">
                Quán ăn (<span id="tab-count-quan">0</span>)
            </button>
            <button type="button" onclick="setFeedFilter('blog')" class="feed-tab-btn px-3 py-1.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all" data-feed-filter="blog">
                Bài viết Blog (<span id="tab-count-blog">0</span>)
            </button>
            <button type="button" onclick="setFeedFilter('user')" class="feed-tab-btn px-3 py-1.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all" data-feed-filter="user">
                Người dùng (<span id="tab-count-user">0</span>)
            </button>
            <button type="button" onclick="setFeedFilter('broadcast')" class="feed-tab-btn px-3 py-1.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all" data-feed-filter="broadcast">
                Phát sóng hệ thống (<span id="tab-count-broadcast">0</span>)
            </button>
        </div>

        {{-- Live Notifications Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-y border-slate-200">
                    <tr>
                        <th class="p-3 w-40">Phân Loại</th>
                        <th class="p-3">Tiêu Đề & Chi Tiết Thông Báo</th>
                        <th class="p-3 w-52">Thời Gian Nhận</th>
                        <th class="p-3 text-right w-36">Thao Tác</th>
                    </tr>
                </thead>
                <tbody id="live-notifications-tbody" class="divide-y divide-slate-100">
                    <tr id="live-notif-loading-row">
                        <td colspan="4" class="p-8 text-center text-slate-400">
                            <span class="inline-block animate-spin material-symbols-outlined text-2xl text-amber-500 mb-2">progress_activity</span>
                            <p class="font-medium">Đang tải toàn bộ dữ liệu thông báo thời gian thực...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bottom Form Card: Broadcast Message via AJAX without Reload --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-600">send</span>
                    <span>Tạo & Phát Thông Báo Hệ Thống (Broadcast) Mới</span>
                </h3>
                <p class="text-xs text-slate-500">Gửi thông báo ngầm qua AJAX — Dữ liệu mới sẽ xuất hiện trực tiếp trên trang ngay lập tức mà không reload.</p>
            </div>
            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200/80 rounded-lg text-[11px] font-bold">AJAX Instant Delivery</span>
        </div>

        {{-- Success/Error Alert Container for Form --}}
        <div id="broadcast-alert" class="hidden mb-4 p-3 rounded-xl text-xs font-medium flex items-center gap-2"></div>

        <form id="form-broadcast" action="{{ route('admin.thong-bao.send') }}" method="POST" class="space-y-4 max-w-3xl">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Đối tượng nhận thông báo</label>
                    <select name="doi_tuong" id="broadcast_doi_tuong" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                        <option value="tat_ca">Tất cả người dùng trên hệ thống</option>
                        <option value="chu_quan">Chỉ gửi các Chủ quán kinh doanh</option>
                        <option value="nguoi_dung">Chỉ gửi Thành viên thông thường</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tiêu đề thông báo</label>
                    <input type="text" name="tieu_de" id="broadcast_tieu_de" placeholder="Nhập tiêu đề thông báo..." required class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nội dung chi tiết</label>
                <textarea name="noi_dung" id="broadcast_noi_dung" rows="3" placeholder="Nhập nội dung thông báo gửi đến các tài khoản..." required class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" id="btn-broadcast-submit" class="btn-enterprise btn-enterprise-indigo">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span>Phát Thông Báo Ngay</span>
                </button>
                <span class="text-[11px] text-slate-500 font-medium">Không gây tải lại trang web, tự động đồng bộ hóa trên toàn hệ thống.</span>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeFeedFilter = 'all';
    let searchQuery = '';

    function setFeedFilter(filterType) {
        activeFeedFilter = filterType;
        document.querySelectorAll('.feed-tab-btn').forEach(btn => {
            if (btn.getAttribute('data-feed-filter') === filterType) {
                btn.className = 'feed-tab-btn active px-3 py-1.5 rounded-xl font-bold bg-slate-900 text-white transition-all shadow-2xs';
            } else {
                btn.className = 'feed-tab-btn px-3 py-1.5 rounded-xl font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all';
            }
        });
        renderLiveNotificationFeed();
    }

    function handleSearchNotifs(val) {
        searchQuery = (val || '').toLowerCase().trim();
        renderLiveNotificationFeed();
    }

    function manualSyncNotifs() {
        const syncIcon = document.getElementById('sync-icon');
        if (syncIcon) syncIcon.classList.add('animate-spin');

        if (typeof checkRealtimeAdminNotifications === 'function') {
            checkRealtimeAdminNotifications();
        }

        setTimeout(() => {
            if (syncIcon) syncIcon.classList.remove('animate-spin');
        }, 800);
    }

    function renderLiveNotificationFeed() {
        const tbody = document.getElementById('live-notifications-tbody');
        if (!tbody) return;

        const all = window.__allNotifications || [];

        // Cập nhật số lượng trên các tab filter
        const countAll = all.length;
        const countQuan = all.filter(n => n.type === 'quan').length;
        const countBlog = all.filter(n => n.type === 'blog').length;
        const countUser = all.filter(n => n.type === 'user').length;
        const countBroadcast = all.filter(n => n.type === 'broadcast').length;

        const elAll = document.getElementById('tab-count-all');
        if (elAll) elAll.textContent = countAll;
        const elQuan = document.getElementById('tab-count-quan');
        if (elQuan) elQuan.textContent = countQuan;
        const elBlog = document.getElementById('tab-count-blog');
        if (elBlog) elBlog.textContent = countBlog;
        const elUser = document.getElementById('tab-count-user');
        if (elUser) elUser.textContent = countUser;
        const elBroadcast = document.getElementById('tab-count-broadcast');
        if (elBroadcast) elBroadcast.textContent = countBroadcast;

        // Lọc theo tab
        let filtered = (activeFeedFilter === 'all')
            ? all
            : all.filter(n => n.type === activeFeedFilter);

        // Lọc theo search input
        if (searchQuery) {
            filtered = filtered.filter(n => {
                const t = (n.title || '').toLowerCase();
                const d = (n.description || '').toLowerCase();
                const c = (n.category_label || '').toLowerCase();
                return t.includes(searchQuery) || d.includes(searchQuery) || c.includes(searchQuery);
            });
        }

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-400">
                        <span class="material-symbols-outlined text-3xl text-slate-300 block mb-1">notifications_off</span>
                        <p class="font-medium text-xs">Không có dữ liệu thông báo nào phù hợp.</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = filtered.map(item => `
            <tr id="feed-row-${item.id}" class="hover:bg-slate-50/80 transition-colors group">
                <td class="p-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold ${item.badge_class || 'bg-slate-100 text-slate-700'}">
                        <span class="material-symbols-outlined text-[15px]">${item.icon || 'notifications'}</span>
                        <span>${item.category_label}</span>
                    </span>
                </td>
                <td class="p-3">
                    <p class="font-bold text-slate-900 group-hover:text-amber-700 transition-colors text-xs">${item.title}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">${item.description || 'Không có mô tả chi tiết'}</p>
                </td>
                <td class="p-3 whitespace-nowrap">
                    <p class="font-semibold text-slate-700 text-[11px]">${item.time_ago || 'Vừa xong'}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">${item.created_at_formatted || (item.created_at ? new Date(item.created_at).toLocaleString('vi-VN') : '')}</p>
                </td>
                <td class="p-3 text-right whitespace-nowrap">
                    <a href="${item.url}" class="btn-enterprise btn-enterprise-dark">
                        <span>Xử lý</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </td>
            </tr>
        `).join('');
    }

    // Lắng nghe sự kiện toàn cục khi hệ thống tự động nhận dữ liệu polling mới
    window.addEventListener('admin:notifications-updated', function(e) {
        const data = e.detail;
        if (!data) return;

        // Cập nhật các thẻ KPI trên đầu trang
        const totalPendingEl = document.getElementById('page-kpi-total-pending');
        if (totalPendingEl && data.total_pending !== undefined) totalPendingEl.textContent = data.total_pending;

        const quanPendingEl = document.getElementById('page-kpi-pending-quan');
        if (quanPendingEl && data.pending_quan !== undefined) quanPendingEl.textContent = data.pending_quan;

        const blogPendingEl = document.getElementById('page-kpi-pending-blog');
        if (blogPendingEl && data.pending_blog !== undefined) blogPendingEl.textContent = data.pending_blog;

        const userPendingEl = document.getElementById('page-kpi-unverified-user');
        if (userPendingEl && data.unverified_user !== undefined) userPendingEl.textContent = data.unverified_user;

        const broadcastCountEl = document.getElementById('page-kpi-broadcast-count');
        if (broadcastCountEl && data.broadcast_count !== undefined) broadcastCountEl.textContent = data.broadcast_count;

        // Render lại bảng dữ liệu
        renderLiveNotificationFeed();
    });

    // Xử lý gửi Form Broadcast bằng AJAX không cần reload trang
    document.addEventListener('DOMContentLoaded', function() {
        // Render ngay dữ liệu đã có từ cache nếu có
        if (window.__allNotifications && window.__allNotifications.length > 0) {
            renderLiveNotificationFeed();
        }

        const formBroadcast = document.getElementById('form-broadcast');
        if (formBroadcast) {
            formBroadcast.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = document.getElementById('btn-broadcast-submit');
                const origBtnContent = submitBtn ? submitBtn.innerHTML : '';
                const alertBox = document.getElementById('broadcast-alert');

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="inline-block animate-spin material-symbols-outlined text-[16px]">progress_activity</span> <span>Đang phát sóng...</span>';
                }

                if (alertBox) {
                    alertBox.className = 'hidden';
                }

                const formData = new FormData(formBroadcast);

                fetch(formBroadcast.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (alertBox) {
                            alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-2';
                            alertBox.innerHTML = '<span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span> <span>' + (data.message || 'Đã gửi thông báo hệ thống thành công!') + '</span>';
                        }

                        // Reset các trường nhập
                        document.getElementById('broadcast_tieu_de').value = '';
                        document.getElementById('broadcast_noi_dung').value = '';

                        // Chèn trực tiếp thông báo mới vào đầu bảng live feed
                        if (data.notification) {
                            const newNotifItem = {
                                id: 'broadcast_' + (data.notification.id || Date.now()),
                                item_id: data.notification.id,
                                type: 'broadcast',
                                category_label: 'Thông báo hệ thống',
                                title: data.notification.tieu_de,
                                description: data.notification.noi_dung,
                                time_ago: 'Vừa xong',
                                created_at: new Date().toISOString(),
                                created_at_formatted: data.notification.created_at || 'Vừa xong',
                                url: '{{ route("admin.thong-bao.index") }}',
                                icon: 'campaign',
                                badge_class: 'bg-emerald-100 text-emerald-700',
                            };

                            if (!window.__allNotifications) window.__allNotifications = [];
                            window.__allNotifications.unshift(newNotifItem);
                            renderLiveNotificationFeed();

                            // Hiệu ứng highlight dòng vừa thêm
                            setTimeout(() => {
                                const row = document.getElementById('feed-row-' + newNotifItem.id);
                                if (row) {
                                    row.classList.add('bg-emerald-50', 'animate-pulse');
                                    setTimeout(() => row.classList.remove('bg-emerald-50', 'animate-pulse'), 3000);
                                }
                            }, 50);
                        }

                        // Gọi đồng bộ ngầm toàn bộ
                        if (typeof checkRealtimeAdminNotifications === 'function') {
                            checkRealtimeAdminNotifications();
                        }
                    } else {
                        if (alertBox) {
                            alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200 flex items-center gap-2';
                            alertBox.innerHTML = '<span class="material-symbols-outlined text-[18px] text-rose-600">error</span> <span>' + (data.message || 'Không thể gửi thông báo, vui lòng kiểm tra lại.') + '</span>';
                        }
                    }
                })
                .catch(() => {
                    if (alertBox) {
                        alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200 flex items-center gap-2';
                        alertBox.innerHTML = '<span class="material-symbols-outlined text-[18px] text-rose-600">error</span> <span>Có lỗi kết nối máy chủ, vui lòng thử lại!</span>';
                    }
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = origBtnContent;
                    }
                });
            });
        }
    });
</script>
@endpush
