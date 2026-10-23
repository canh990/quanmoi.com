@extends('layouts.app')

@section('title', 'Trung tâm thông báo — Quán Mới')

@section('content')
<main class="max-w-[1200px] mx-auto px-container-margin py-stack-lg min-h-[819px] pt-6">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-stack-lg">
        <!-- Sidebar Navigation -->
        @include('nguoi-dung.partials.sidebar')

        <!-- Main Content Area -->
        <div class="md:col-span-9 space-y-6">
            <!-- Header Card -->
            <div class="bg-surface-card p-6 rounded-2xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] border border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-on-surface">Trung tâm thông báo</h1>
                        @if($unreadCount > 0)
                            <span id="page-unread-badge" class="px-2.5 py-0.5 text-xs font-bold bg-primary text-white rounded-full">
                                {{ $unreadCount }} chưa đọc
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-text-muted mt-1">Cập nhật tức thì các biến động về quán ăn, bài viết, đánh giá và phân quyền.</p>
                </div>

                @if($unreadCount > 0)
                    <button type="button" onclick="markAllNotificationsRead()" class="self-start sm:self-auto inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 hover:from-amber-600 hover:to-orange-600 text-white font-bold text-xs shadow-sm shadow-amber-500/25 transition-all hover:scale-102 active:scale-95 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px] text-white">done_all</span>
                        <span>Đánh dấu tất cả đã đọc</span>
                    </button>
                @endif
            </div>

            <!-- Tabs Navigation (Đậm nét, phân loại màu sắc rực rỡ ấn tượng) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs font-bold">
                <a href="{{ route('nguoi-dung.thong-bao.index', ['tab' => 'tat_ca']) }}" class="px-4 py-2.5 rounded-xl transition-all whitespace-nowrap {{ $tab === 'tat_ca' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white font-black shadow-sm shadow-amber-500/25' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200' }}">
                    Tất cả
                </a>
                <a href="{{ route('nguoi-dung.thong-bao.index', ['tab' => 'chua_doc']) }}" class="px-4 py-2.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'chua_doc' ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white font-black shadow-sm shadow-rose-500/25' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200' }}">
                    <span>Chưa đọc</span>
                    @if($unreadCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $tab === 'chua_doc' ? 'bg-white/25 text-white' : 'bg-rose-500 text-white' }}">{{ $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('nguoi-dung.thong-bao.index', ['tab' => 'quan']) }}" class="px-4 py-2.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'quan' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white font-black shadow-sm shadow-amber-500/25' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200' }}">
                    <span class="material-symbols-outlined text-[16px]">storefront</span>
                    <span>Quán ăn</span>
                </a>
                <a href="{{ route('nguoi-dung.thong-bao.index', ['tab' => 'blog']) }}" class="px-4 py-2.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'blog' ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-black shadow-sm shadow-blue-500/25' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200' }}">
                    <span class="material-symbols-outlined text-[16px]">article</span>
                    <span>Bài viết</span>
                </a>
                <a href="{{ route('nguoi-dung.thong-bao.index', ['tab' => 'danh_gia']) }}" class="px-4 py-2.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'danh_gia' ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white font-black shadow-sm shadow-pink-500/25' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200' }}">
                    <span class="material-symbols-outlined text-[16px]">rate_review</span>
                    <span>Đánh giá</span>
                </a>
                <a href="{{ route('nguoi-dung.thong-bao.index', ['tab' => 'he_thong']) }}" class="px-4 py-2.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'he_thong' ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-black shadow-sm shadow-purple-500/25' : 'bg-white text-slate-700 hover:bg-slate-100 hover:text-slate-900 border border-slate-200' }}">
                    <span class="material-symbols-outlined text-[16px]">campaign</span>
                    <span>Hệ thống</span>
                </a>
            </div>

            <!-- Notifications List -->
            <div id="notifications-container" class="space-y-3">
                @forelse($notifications as $notif)
                    @php
                        $data = $notif->data ?? [];
                        $isRead = !is_null($notif->read_at);
                        $action = $data['action'] ?? '';
                        $title = $data['title'] ?? 'Thông báo hệ thống';
                        $message = $data['message'] ?? '';
                        $note = $data['note'] ?? null;
                        $link = !empty($data['link']) ? $data['link'] : \App\Http\Controllers\NguoiDung\ThongBaoController::resolveTargetLink($data);
                        $statusLabel = $data['status_label'] ?? 'Thông báo';
                        $coverImage = $data['cover_image_url'] ?? null;

                        // Compute Icon and Color
                        $icon = 'notifications';
                        $iconClass = 'bg-primary/10 text-primary';
                        $badgeClass = 'bg-primary/10 text-primary';

                        if (in_array($action, ['approved', 'published', 'verified', 'verified_granted', 'permission_granted', 'restored'])) {
                            $icon = in_array($action, ['verified', 'verified_granted']) ? 'verified' : (in_array($action, ['permission_granted']) ? 'lock_open' : (in_array($action, ['restored']) ? 'unarchive' : 'check_circle'));
                            $iconClass = 'bg-emerald-100 text-emerald-700';
                            $badgeClass = 'bg-emerald-100 text-emerald-800';
                        } elseif (in_array($action, ['rejected', 'verification_rejected', 'verified_revoked', 'permission_denied', 'deleted', 'force_deleted'])) {
                            $icon = in_array($action, ['deleted', 'force_deleted']) ? 'delete_forever' : (in_array($action, ['permission_denied']) ? 'lock' : (in_array($action, ['verification_rejected', 'verified_revoked']) ? 'gpp_bad' : 'cancel'));
                            $iconClass = 'bg-rose-100 text-rose-700';
                            $badgeClass = 'bg-rose-100 text-rose-800';
                        } elseif (in_array($action, ['requested_revision', 'need_revision', 'updated'])) {
                            $icon = $action === 'updated' ? 'store' : 'edit_note';
                            $iconClass = 'bg-amber-100 text-amber-700';
                            $badgeClass = 'bg-amber-100 text-amber-800';
                        } elseif (in_array($action, ['submitted', 'pending'])) {
                            $icon = 'schedule';
                            $iconClass = 'bg-blue-100 text-blue-700';
                            $badgeClass = 'bg-blue-100 text-blue-800';
                        } elseif ($action === 'hero') {
                            $icon = 'auto_awesome';
                            $iconClass = 'bg-amber-100 text-amber-700';
                            $badgeClass = 'bg-amber-100 text-amber-800';
                        } elseif ($action === 'featured_granted') {
                            $icon = 'hotel_class';
                            $iconClass = 'bg-amber-100 text-amber-700';
                            $badgeClass = 'bg-amber-100 text-amber-800';
                        } elseif ($action === 'featured_revoked') {
                            $icon = 'star';
                            $iconClass = 'bg-slate-200 text-slate-700';
                            $badgeClass = 'bg-slate-200 text-slate-800';
                        } elseif ($action === 'created') {
                            $icon = 'add_business';
                            $iconClass = 'bg-indigo-100 text-indigo-700';
                            $badgeClass = 'bg-indigo-100 text-indigo-800';
                        } elseif ($action === 'new_review') {
                            $icon = 'rate_review';
                            $iconClass = 'bg-amber-100 text-amber-700';
                            $badgeClass = 'bg-amber-100 text-amber-800';
                        } elseif ($action === 'broadcast') {
                            $icon = 'campaign';
                            $iconClass = 'bg-purple-100 text-purple-700';
                            $badgeClass = 'bg-purple-100 text-purple-800';
                        } elseif ($action === 'role_updated') {
                            $icon = 'admin_panel_settings';
                            $iconClass = 'bg-indigo-100 text-indigo-700';
                            $badgeClass = 'bg-indigo-100 text-indigo-800';
                        } elseif ($action === 'review_reply') {
                            $icon = 'reply';
                            $iconClass = 'bg-blue-100 text-blue-700';
                            $badgeClass = 'bg-blue-100 text-blue-800';
                        } elseif ($action === 'blog_comment') {
                            $icon = 'chat';
                            $iconClass = 'bg-sky-100 text-sky-700';
                            $badgeClass = 'bg-sky-100 text-sky-800';
                        } elseif ($action === 'comment_reply') {
                            $icon = 'quickreply';
                            $iconClass = 'bg-teal-100 text-teal-700';
                            $badgeClass = 'bg-teal-100 text-teal-800';
                        } elseif ($action === 'menu_updated') {
                            $icon = 'restaurant_menu';
                            $iconClass = 'bg-teal-100 text-teal-700';
                            $badgeClass = 'bg-teal-100 text-teal-800';
                        } elseif ($action === 'verification_requested') {
                            $icon = 'verified_user';
                            $iconClass = 'bg-indigo-100 text-indigo-700';
                            $badgeClass = 'bg-indigo-100 text-indigo-800';
                        }
                    @endphp

                    <div id="notif-card-{{ $notif->id }}" onclick="handleUserNotifClick('{{ $notif->id }}', '{{ $link }}')" class="bg-white rounded-2xl p-4 sm:p-5 shadow-[0px_2px_12px_rgba(0,0,0,0.04)] border transition-all hover:shadow-md flex flex-col sm:flex-row items-start gap-4 relative cursor-pointer {{ !$isRead ? 'border-primary/40 bg-amber-50/20' : 'border-gray-100' }}">
                        <!-- Icon -->
                        <div class="w-11 h-11 rounded-2xl {{ $iconClass }} flex items-center justify-center shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-[24px]">{{ $icon }}</span>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $badgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                    @if(!$isRead)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-primary">
                                            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                                            <span>Mới</span>
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-text-muted whitespace-nowrap">{{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</span>
                            </div>

                            <h3 class="text-sm sm:text-base font-bold text-on-surface leading-snug">
                                {{ $title }}
                            </h3>

                            <p class="text-xs sm:text-sm text-gray-600 mt-1 leading-relaxed">
                                {{ $message }}
                            </p>

                            @if($note)
                                <div class="mt-2.5 p-3 rounded-xl bg-gray-50 border-l-4 border-primary text-xs text-gray-700 italic">
                                    <span class="font-bold not-italic text-gray-900 block mb-0.5">Ghi chú phản hồi:</span>
                                    "{{ $note }}"
                                </div>
                            @endif

                            @if($coverImage)
                                <div class="mt-3">
                                    <img src="{{ $coverImage }}" alt="{{ $title }}" class="w-24 h-16 object-cover rounded-xl border border-gray-100 shadow-xs">
                                </div>
                            @endif

                            <div class="mt-3.5 flex flex-wrap items-center gap-2.5 pt-3 border-t border-slate-100">
                                @if($link)
                                    <a href="{{ $link }}" onclick="event.stopPropagation(); handleUserNotifClick('{{ $notif->id }}', '{{ $link }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 shadow-sm shadow-amber-500/25 hover:scale-105 active:scale-95 transition-all">
                                        <span>Xem chi tiết</span>
                                        <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                                    </a>
                                @endif

                                @if(!$isRead)
                                    <button type="button" onclick="event.stopPropagation(); markSingleRead('{{ $notif->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 hover:border-emerald-300 shadow-2xs hover:scale-105 active:scale-95 transition-all cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px] text-emerald-600">done</span>
                                        <span>Đánh dấu đã đọc</span>
                                    </button>
                                @endif

                                <button type="button" onclick="event.stopPropagation(); deleteNotification('{{ $notif->id }}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 hover:border-rose-300 shadow-2xs hover:scale-105 active:scale-95 transition-all ml-auto cursor-pointer" title="Xóa thông báo">
                                    <span class="material-symbols-outlined text-[16px] text-rose-600">delete</span>
                                    <span>Xóa</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
                        <div class="w-16 h-16 mx-auto rounded-full bg-gray-50 flex items-center justify-center text-gray-400 mb-3">
                            <span class="material-symbols-outlined text-3xl">notifications_off</span>
                        </div>
                        <h4 class="font-bold text-base text-gray-700">Chưa có thông báo nào</h4>
                        <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">
                            Khi có cập nhật mới về quán ăn, bài viết hoặc phân quyền tài khoản, hệ thống sẽ tự động hiển thị tại đây.
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</main>

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Mark single notification read
    window.markSingleRead = function(id, event) {
        if (event && event.stopPropagation) event.stopPropagation();
        if (window.event && window.event.stopPropagation) window.event.stopPropagation();

        fetch(`/thanh-vien/thong-bao/${id}/danh-dau-da-doc`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                const card = document.getElementById(`notif-card-${id}`);
                if (card) {
                    card.classList.remove('border-primary/40', 'bg-amber-50/20', 'bg-amber-50/25');
                    card.classList.add('border-gray-100');
                    const badge = card.querySelector('.animate-pulse, .animate-ping')?.closest('.inline-flex');
                    if (badge) badge.remove();
                    const markBtn = card.querySelector('button[onclick*="markSingleRead"]');
                    if (markBtn) markBtn.remove();
                }
                updatePageBadge(data.unread_count);
            }
        })
        .catch(() => {});
    };

    // Mark all notifications read without page reload
    window.markAllNotificationsRead = function() {
        fetch('{{ route("nguoi-dung.thong-bao.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                // Remove all unread borders and badges instantly
                document.querySelectorAll('#notifications-container > div').forEach(card => {
                    card.classList.remove('border-primary/40', 'bg-amber-50/20', 'bg-amber-50/25');
                    card.classList.add('border-gray-100');
                    const newBadge = card.querySelector('.animate-pulse, .animate-ping')?.closest('.inline-flex');
                    if (newBadge) newBadge.remove();
                    const markBtn = card.querySelector('button[onclick*="markSingleRead"]');
                    if (markBtn) markBtn.remove();
                });

                // Update unread badges
                updatePageBadge(0);

                // Hide the mark all button
                const markAllBtn = document.querySelector('button[onclick="markAllNotificationsRead()"]');
                if (markAllBtn) {
                    markAllBtn.style.opacity = '0';
                    setTimeout(() => markAllBtn.remove(), 200);
                }
            }
        })
        .catch(() => {});
    };

    // Delete single notification
    window.deleteNotification = function(id, event) {
        if (event && event.stopPropagation) event.stopPropagation();
        if (window.event && window.event.stopPropagation) window.event.stopPropagation();
        if (!confirm('Bạn có chắc muốn xóa thông báo này không?')) return;

        fetch(`/thanh-vien/thong-bao/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                const card = document.getElementById(`notif-card-${id}`);
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => card.remove(), 250);
                }
                updatePageBadge(data.unread_count);
            }
        })
        .catch(() => {});
    };

    function updatePageBadge(count) {
        let badge = document.getElementById('page-unread-badge');
        if (count > 0) {
            if (!badge) {
                const h1 = document.querySelector('h1');
                if (h1 && h1.parentElement) {
                    badge = document.createElement('span');
                    badge.id = 'page-unread-badge';
                    badge.className = 'px-2.5 py-0.5 text-xs font-bold bg-primary text-white rounded-full';
                    h1.parentElement.appendChild(badge);
                }
            }
            if (badge) badge.textContent = `${count} chưa đọc`;
        } else {
            if (badge) badge.remove();
        }
    }

    // Dynamic Prepend Element Generator for 0-reload live updates
    function createNotifCardElement(notif) {
        const action = notif.action || '';
        let icon = 'notifications';
        let iconClass = 'bg-primary/10 text-primary';
        let badgeClass = 'bg-primary/10 text-primary';

        if (['approved', 'published', 'verified', 'verified_granted', 'permission_granted', 'restored'].includes(action)) {
            icon = ['verified', 'verified_granted'].includes(action) ? 'verified' : (action === 'permission_granted' ? 'lock_open' : (action === 'restored' ? 'unarchive' : 'check_circle'));
            iconClass = 'bg-emerald-100 text-emerald-700';
            badgeClass = 'bg-emerald-100 text-emerald-800';
        } else if (['rejected', 'verification_rejected', 'verified_revoked', 'permission_denied', 'deleted', 'force_deleted'].includes(action)) {
            icon = ['deleted', 'force_deleted'].includes(action) ? 'delete_forever' : (action === 'permission_denied' ? 'lock' : (['verification_rejected', 'verified_revoked'].includes(action) ? 'gpp_bad' : 'cancel'));
            iconClass = 'bg-rose-100 text-rose-700';
            badgeClass = 'bg-rose-100 text-rose-800';
        } else if (['requested_revision', 'need_revision', 'updated'].includes(action)) {
            icon = action === 'updated' ? 'store' : 'edit_note';
            iconClass = 'bg-amber-100 text-amber-700';
            badgeClass = 'bg-amber-100 text-amber-800';
        } else if (action === 'submitted' || action === 'pending') {
            icon = 'schedule';
            iconClass = 'bg-blue-100 text-blue-700';
            badgeClass = 'bg-blue-100 text-blue-800';
        } else if (action === 'hero') {
            icon = 'auto_awesome';
            iconClass = 'bg-amber-100 text-amber-700';
            badgeClass = 'bg-amber-100 text-amber-800';
        } else if (action === 'featured_granted') {
            icon = 'hotel_class';
            iconClass = 'bg-amber-100 text-amber-700';
            badgeClass = 'bg-amber-100 text-amber-800';
        } else if (action === 'featured_revoked') {
            icon = 'star';
            iconClass = 'bg-slate-200 text-slate-700';
            badgeClass = 'bg-slate-200 text-slate-800';
        } else if (action === 'created') {
            icon = 'add_business';
            iconClass = 'bg-indigo-100 text-indigo-700';
            badgeClass = 'bg-indigo-100 text-indigo-800';
        } else if (action === 'new_review') {
            icon = 'rate_review';
            iconClass = 'bg-amber-100 text-amber-700';
            badgeClass = 'bg-amber-100 text-amber-800';
        } else if (action === 'broadcast') {
            icon = 'campaign';
            iconClass = 'bg-purple-100 text-purple-700';
            badgeClass = 'bg-purple-100 text-purple-800';
        } else if (action === 'role_updated') {
            icon = 'admin_panel_settings';
            iconClass = 'bg-indigo-100 text-indigo-700';
            badgeClass = 'bg-indigo-100 text-indigo-800';
        } else if (action === 'review_reply') {
            icon = 'reply';
            iconClass = 'bg-blue-100 text-blue-700';
            badgeClass = 'bg-blue-100 text-blue-800';
        } else if (action === 'blog_comment') {
            icon = 'chat';
            iconClass = 'bg-sky-100 text-sky-700';
            badgeClass = 'bg-sky-100 text-sky-800';
        } else if (action === 'comment_reply') {
            icon = 'quickreply';
            iconClass = 'bg-teal-100 text-teal-700';
            badgeClass = 'bg-teal-100 text-teal-800';
        } else if (action === 'menu_updated') {
            icon = 'restaurant_menu';
            iconClass = 'bg-teal-100 text-teal-700';
            badgeClass = 'bg-teal-100 text-teal-800';
        } else if (action === 'verification_requested') {
            icon = 'verified_user';
            iconClass = 'bg-indigo-100 text-indigo-700';
            badgeClass = 'bg-indigo-100 text-indigo-800';
        }

        const div = document.createElement('div');
        div.id = `notif-card-${notif.id}`;
        div.onclick = function() {
            handleUserNotifClick(notif.id, notif.link || '');
        };
        div.className = 'bg-white rounded-2xl p-4 sm:p-5 shadow-[0px_2px_12px_rgba(0,0,0,0.04)] border border-primary/40 bg-amber-50/25 transition-all hover:shadow-md flex flex-col sm:flex-row items-start gap-4 relative animate-pulse cursor-pointer';

        div.innerHTML = `
            <div class="w-11 h-11 rounded-2xl ${iconClass} flex items-center justify-center shrink-0 shadow-xs">
                <span class="material-symbols-outlined text-[24px]">${icon}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold ${badgeClass}">
                            ${notif.status_label || 'Thông báo mới'}
                        </span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-primary">
                            <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
                            <span>Mới nhận</span>
                        </span>
                    </div>
                    <span class="text-xs text-text-muted whitespace-nowrap">Vừa xong</span>
                </div>
                <h3 class="text-sm sm:text-base font-bold text-on-surface leading-snug">
                    ${notif.title || 'Thông báo mới'}
                </h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1 leading-relaxed">
                    ${notif.message || ''}
                </p>
                ${notif.note ? `
                    <div class="mt-2.5 p-3 rounded-xl bg-gray-50 border-l-4 border-primary text-xs text-gray-700 italic">
                        <span class="font-bold not-italic text-gray-900 block mb-0.5">Ghi chú phản hồi:</span>
                        "${notif.note}"
                    </div>
                ` : ''}
                ${notif.cover_image_url ? `
                    <div class="mt-3">
                        <img src="${notif.cover_image_url}" class="w-24 h-16 object-cover rounded-xl border border-gray-100 shadow-xs">
                    </div>
                ` : ''}
                <div class="mt-3.5 flex flex-wrap items-center gap-2.5 pt-3 border-t border-slate-100">
                    ${notif.link ? `
                        <a href="${notif.link}" onclick="event.stopPropagation(); handleUserNotifClick('${notif.id}', '${notif.link}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 shadow-sm shadow-amber-500/25 hover:scale-105 active:scale-95 transition-all">
                            <span>Xem chi tiết</span>
                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                        </a>
                    ` : ''}
                    <button type="button" onclick="event.stopPropagation(); markSingleRead('${notif.id}', event)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 hover:border-emerald-300 shadow-2xs hover:scale-105 active:scale-95 transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">done</span>
                        <span>Đánh dấu đã đọc</span>
                    </button>
                    <button type="button" onclick="event.stopPropagation(); deleteNotification('${notif.id}', event)" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 hover:border-rose-300 shadow-2xs hover:scale-105 active:scale-95 transition-all ml-auto cursor-pointer" title="Xóa thông báo">
                        <span class="material-symbols-outlined text-[16px] text-rose-600">delete</span>
                        <span>Xóa</span>
                    </button>
                </div>
            </div>
        `;
        setTimeout(() => div.classList.remove('animate-pulse'), 5000);
        return div;
    }

    // Live Instant Prepend on new notification arrival
    window.addEventListener('quanmoi:notification-received', function(e) {
        const notif = e.detail;
        if (!notif) return;

        const container = document.getElementById('notifications-container');
        if (container) {
            // Remove empty state if present
            const emptyState = container.querySelector('.bg-white.p-12');
            if (emptyState) emptyState.remove();

            if (!document.getElementById(`notif-card-${notif.id}`)) {
                const newCard = createNotifCardElement(notif);
                container.insertBefore(newCard, container.firstChild);
            }
        }
    });
</script>
@endpush
@endsection
