@php
    $isMobile = $isMobile ?? false;
    $isAdmin = $isAdmin ?? (Auth::check() && Auth::user()->isAdmin());
    $dropdownId = $isMobile ? 'user-mobile-notif-dropdown' : 'user-notif-dropdown';
    $listId = $isMobile ? 'user-mobile-notif-list' : 'user-notif-list';
    $pillId = $isMobile ? 'user-mobile-notif-badge-pill' : 'user-notif-badge-pill';
    $dropdownPos = $isMobile ? 'fixed top-14 left-2 right-2 max-w-sm mx-auto' : 'absolute right-0 top-full mt-2 w-80 sm:w-96';
    $notifCount = $navUnreadCount ?? 0;
@endphp

<div id="{{ $dropdownId }}" class="hidden {{ $dropdownPos }} bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.22)] border border-gray-100 overflow-hidden transition-all duration-200" style="z-index: 99999; background-color: #ffffff;">
    {{-- Header --}}
    <div class="p-3.5 bg-slate-50/90 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="font-bold text-sm text-gray-900">{{ $isAdmin ? 'Thông báo Quản trị' : 'Thông báo' }}</span>
            <span id="{{ $pillId }}" class="text-[11px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full">{{ $notifCount }} mới</span>
        </div>
        <button type="button" onclick="markAllNotifsRead()" class="text-[11px] font-semibold text-primary hover:text-primary-hover transition-colors cursor-pointer flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">done_all</span>
            <span>Đọc tất cả</span>
        </button>
    </div>

    {{-- Notification List (Pre-rendered directly in Blade for 0ms instant display) --}}
    <div id="{{ $listId }}" class="max-h-[380px] overflow-y-auto divide-y divide-gray-100 custom-scrollbar">
        @if($isAdmin)
            {{-- Admin Notifications View --}}
            @if(isset($navNotifications) && count($navNotifications) > 0)
                @foreach($navNotifications as $item)
                    @php
                        $isUnread = empty($item['read']);
                        $nId = $item['id'] ?? '';
                        $nTitle = $item['title'] ?? 'Thông báo hệ thống';
                        $nDesc = $item['description'] ?? '';
                        $nUrl = $item['url'] ?? route('admin.thong-bao.index');
                        $nIcon = $item['icon'] ?? 'notifications';
                        $nBadgeClass = $item['badge_class'] ?? 'bg-amber-100 text-amber-700';
                        $nCatLabel = $item['category_label'] ?? 'Hệ thống';
                        $nTimeAgo = $item['time_ago'] ?? '';
                    @endphp
                    <div onclick="handleNotifClick('{{ $nId }}', '{{ $nUrl }}')" class="p-3.5 hover:bg-gray-50/80 transition-colors cursor-pointer flex items-start gap-3 relative {{ $isUnread ? 'bg-amber-50/30' : '' }}">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">{{ $nIcon }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $nBadgeClass }}">{{ $nCatLabel }}</span>
                                <span class="text-[10px] text-gray-400 whitespace-nowrap">{{ $nTimeAgo }}</span>
                            </div>
                            <h4 class="text-xs {{ $isUnread ? 'font-black text-gray-900' : 'font-normal text-gray-600' }} leading-tight truncate mb-1" title="{{ $nTitle }}">{{ $nTitle }}</h4>
                            <p class="text-[11px] {{ $isUnread ? 'text-gray-800 font-medium' : 'text-gray-500' }} leading-relaxed line-clamp-2">{{ $nDesc }}</p>
                        </div>
                        @if($isUnread)
                            <span class="w-2.5 h-2.5 rounded-full bg-primary shrink-0 self-center ring-2 ring-white"></span>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="py-10 px-4 text-center text-gray-400 text-xs">
                    <span class="material-symbols-outlined text-4xl text-gray-300 block mb-2">notifications_none</span>
                    <p class="font-medium text-gray-600">Bạn chưa có thông báo quản trị nào</p>
                    <p class="text-[11px] text-gray-400 mt-1">Khi có quán, bài viết hoặc tài khoản cần duyệt, thông báo sẽ xuất hiện tại đây.</p>
                </div>
            @endif
        @else
            {{-- User / Restaurant Owner Notifications View --}}
            @if(isset($navNotifications) && count($navNotifications) > 0)
                @foreach($navNotifications as $notifItem)
                    @php
                        $nData = is_array($notifItem) ? ($notifItem['data'] ?? $notifItem) : ($notifItem->data ?? []);
                        $notifId = is_array($notifItem) ? ($notifItem['id'] ?? '') : $notifItem->id;
                        $isUnread = is_array($notifItem) ? empty($notifItem['read']) : empty($notifItem->read_at);
                        $nAction = $nData['action'] ?? ($nData['status'] ?? '');
                        $nTitle = $nData['title'] ?? 'Thông báo hệ thống';
                        $nMessage = $nData['message'] ?? '';
                        $nNote = $nData['note'] ?? null;
                        $nLink = !empty($nData['link']) ? $nData['link'] : \App\Http\Controllers\NguoiDung\ThongBaoController::resolveTargetLink($nData);
                        $nTimeAgo = is_array($notifItem) ? ($notifItem['time_ago'] ?? '') : ($notifItem->created_at ? $notifItem->created_at->diffForHumans() : '');
                        $nStatusLabel = $nData['status_label'] ?? 'Đã cập nhật';

                        $nIcon = 'notifications';
                        $nIconClass = 'bg-primary/10 text-primary';
                        $nBadgeClass = 'bg-gray-100 text-gray-700';

                        if (in_array($nAction, ['approved', 'published'])) {
                            $nIcon = 'check_circle';
                            $nIconClass = 'bg-emerald-100 text-emerald-700';
                            $nBadgeClass = 'bg-emerald-100 text-emerald-700';
                            $nStatusLabel = $nAction === 'published' ? 'Đã xuất bản' : ($nData['status_label'] ?? 'Đã duyệt');
                        } elseif ($nAction === 'rejected') {
                            $nIcon = 'cancel';
                            $nIconClass = 'bg-rose-100 text-rose-700';
                            $nBadgeClass = 'bg-rose-100 text-rose-700';
                        } elseif (in_array($nAction, ['submitted', 'pending'])) {
                            $nIcon = 'schedule';
                            $nIconClass = 'bg-blue-100 text-blue-700';
                            $nBadgeClass = 'bg-blue-100 text-blue-700';
                        } elseif (in_array($nAction, ['requested_revision', 'need_revision'])) {
                            $nIcon = 'edit_note';
                            $nIconClass = 'bg-amber-100 text-amber-700';
                            $nBadgeClass = 'bg-amber-100 text-amber-700';
                            $nStatusLabel = 'Cần sửa';
                        } elseif ($nAction === 'hero') {
                            $nIcon = 'auto_awesome';
                            $nIconClass = 'bg-amber-100 text-amber-700';
                            $nBadgeClass = 'bg-amber-100 text-amber-700';
                            $nStatusLabel = 'Hero Blog';
                        } elseif ($nAction === 'featured_granted') {
                            $nIcon = 'hotel_class';
                            $nIconClass = 'bg-amber-100 text-amber-700';
                            $nBadgeClass = 'bg-amber-100 text-amber-700';
                            $nStatusLabel = 'Quán nổi bật';
                        } elseif ($nAction === 'featured_revoked') {
                            $nIcon = 'star_border';
                            $nIconClass = 'bg-slate-200 text-slate-700';
                            $nBadgeClass = 'bg-slate-200 text-slate-700';
                            $nStatusLabel = 'Hết nổi bật';
                        } elseif ($nAction === 'verified_granted' || $nAction === 'verified') {
                            $nIcon = 'verified';
                            $nIconClass = 'bg-emerald-100 text-emerald-700';
                            $nBadgeClass = 'bg-emerald-100 text-emerald-700';
                            $nStatusLabel = 'Đã xác thực';
                        } elseif ($nAction === 'verified_revoked' || $nAction === 'verification_rejected') {
                            $nIcon = 'gpp_bad';
                            $nIconClass = 'bg-rose-100 text-rose-700';
                            $nBadgeClass = 'bg-rose-100 text-rose-700';
                            $nStatusLabel = 'Từ chối xác thực';
                        } elseif ($nAction === 'created') {
                            $nIcon = 'add_business';
                            $nIconClass = 'bg-indigo-100 text-indigo-700';
                            $nBadgeClass = 'bg-indigo-100 text-indigo-700';
                            $nStatusLabel = 'Khởi tạo';
                        } elseif ($nAction === 'updated') {
                            $nIcon = 'store';
                            $nIconClass = 'bg-amber-100 text-amber-700';
                            $nBadgeClass = 'bg-amber-100 text-amber-700';
                            $nStatusLabel = 'Cập nhật quán';
                        } elseif ($nAction === 'deleted' || $nAction === 'force_deleted') {
                            $nIcon = 'delete_forever';
                            $nIconClass = 'bg-rose-100 text-rose-700';
                            $nBadgeClass = 'bg-rose-100 text-rose-700';
                            $nStatusLabel = 'Đã tạm xóa';
                        } elseif ($nAction === 'restored') {
                            $nIcon = 'unarchive';
                            $nIconClass = 'bg-emerald-100 text-emerald-700';
                            $nBadgeClass = 'bg-emerald-100 text-emerald-700';
                            $nStatusLabel = 'Đã khôi phục';
                        } elseif ($nAction === 'new_review') {
                            $nIcon = 'rate_review';
                            $nIconClass = 'bg-amber-100 text-amber-700';
                            $nBadgeClass = 'bg-amber-100 text-amber-700';
                            $nStatusLabel = 'Đánh giá';
                        } elseif ($nAction === 'broadcast') {
                            $nIcon = 'campaign';
                            $nIconClass = 'bg-purple-100 text-purple-700';
                            $nBadgeClass = 'bg-purple-100 text-purple-700';
                            $nStatusLabel = 'Ban quản trị';
                        } elseif ($nAction === 'role_updated') {
                            $nIcon = 'admin_panel_settings';
                            $nIconClass = 'bg-purple-100 text-purple-700';
                            $nBadgeClass = 'bg-purple-100 text-purple-700';
                            $nStatusLabel = 'Đổi vai trò';
                        } elseif ($nAction === 'permission_granted') {
                            $nIcon = 'lock_open';
                            $nIconClass = 'bg-teal-100 text-teal-700';
                            $nBadgeClass = 'bg-teal-100 text-teal-700';
                            $nStatusLabel = 'Cấp quyền';
                        } elseif ($nAction === 'permission_denied') {
                            $nIcon = 'lock';
                            $nIconClass = 'bg-rose-100 text-rose-700';
                            $nBadgeClass = 'bg-rose-100 text-rose-700';
                            $nStatusLabel = 'Tước quyền';
                        } elseif ($nAction === 'permission_reset') {
                            $nIcon = 'refresh';
                            $nIconClass = 'bg-sky-100 text-sky-700';
                            $nBadgeClass = 'bg-sky-100 text-sky-700';
                            $nStatusLabel = 'Quyền mặc định';
                        } elseif ($nAction === 'status_updated') {
                            $nIcon = 'manage_accounts';
                            $nIconClass = 'bg-blue-100 text-blue-700';
                            $nBadgeClass = 'bg-blue-100 text-blue-700';
                            $nStatusLabel = 'Đổi trạng thái';
                        } elseif ($nAction === 'review_reply') {
                            $nIcon = 'reply';
                            $nIconClass = 'bg-blue-100 text-blue-700';
                            $nBadgeClass = 'bg-blue-100 text-blue-700';
                            $nStatusLabel = 'Đã phản hồi';
                        } elseif ($nAction === 'blog_comment') {
                            $nIcon = 'chat';
                            $nIconClass = 'bg-sky-100 text-sky-700';
                            $nBadgeClass = 'bg-sky-100 text-sky-700';
                            $nStatusLabel = 'Bình luận';
                        } elseif ($nAction === 'comment_reply') {
                            $nIcon = 'quickreply';
                            $nIconClass = 'bg-teal-100 text-teal-700';
                            $nBadgeClass = 'bg-teal-100 text-teal-700';
                            $nStatusLabel = 'Phản hồi';
                        } elseif ($nAction === 'menu_updated') {
                            $nIcon = 'restaurant_menu';
                            $nIconClass = 'bg-teal-100 text-teal-700';
                            $nBadgeClass = 'bg-teal-100 text-teal-700';
                            $nStatusLabel = 'Thực đơn';
                        } elseif ($nAction === 'verification_requested') {
                            $nIcon = 'verified_user';
                            $nIconClass = 'bg-indigo-100 text-indigo-700';
                            $nBadgeClass = 'bg-indigo-100 text-indigo-700';
                            $nStatusLabel = 'Chờ xác thực';
                        }
                    @endphp
                    <div onclick="handleNotifClick('{{ $notifId }}', '{{ $nLink }}')" class="p-3.5 hover:bg-gray-50/80 transition-colors cursor-pointer flex items-start gap-3 relative {{ $isUnread ? 'bg-amber-50/30' : '' }}">
                        <div class="w-9 h-9 rounded-xl {{ $nIconClass }} flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">{{ $nIcon }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $nBadgeClass }}">{{ $nStatusLabel }}</span>
                                <span class="text-[10px] text-gray-400 whitespace-nowrap">{{ $nTimeAgo }}</span>
                            </div>
                            <h4 class="text-xs {{ $isUnread ? 'font-black text-gray-900' : 'font-normal text-gray-600' }} leading-tight truncate mb-1" title="{{ $nTitle }}">{{ $nTitle }}</h4>
                            <p class="text-[11px] {{ $isUnread ? 'text-gray-800 font-medium' : 'text-gray-500' }} leading-relaxed line-clamp-2">{{ $nMessage }}</p>
                            @if($nNote)
                                <div class="mt-1.5 p-2 rounded-lg bg-gray-100/80 text-[11px] text-gray-700 border-l-2 border-primary/50 italic line-clamp-2">"{{ $nNote }}"</div>
                            @endif
                        </div>
                        @if($isUnread)
                            <span class="w-2.5 h-2.5 rounded-full bg-primary shrink-0 self-center ring-2 ring-white"></span>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="py-10 px-4 text-center text-gray-400 text-xs">
                    <span class="material-symbols-outlined text-4xl text-gray-300 block mb-2">notifications_none</span>
                    <p class="font-medium text-gray-600">Bạn chưa có thông báo nào</p>
                    <p class="text-[11px] text-gray-400 mt-1">Các thông báo duyệt quán, bài viết và đánh giá sẽ xuất hiện tại đây.</p>
                </div>
            @endif
        @endif
    </div>

    {{-- Footer --}}
    <div class="p-2.5 bg-gray-50/90 border-t border-gray-100 flex items-center justify-between px-4">
        @if($isAdmin)
            <a href="{{ route('admin.thong-bao.index') }}" class="text-xs font-bold text-primary hover:text-primary-hover transition-colors flex items-center gap-1">
                <span>Trung tâm thông báo Admin</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
            <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="text-[11px] text-gray-500 hover:text-gray-800 transition-colors">
                Duyệt & Xác thực
            </a>
        @else
            <a href="{{ route('nguoi-dung.thong-bao.index') }}" class="text-xs font-bold text-primary hover:text-primary-hover transition-colors flex items-center gap-1">
                <span>Xem tất cả thông báo</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
            <a href="{{ Auth::check() && Auth::user()->hasRole('chu_quan') ? route('chu-quan.quan.index') : route('nguoi-dung.blog.index') }}" class="text-[11px] text-gray-500 hover:text-gray-800 transition-colors">
                {{ Auth::check() && Auth::user()->hasRole('chu_quan') ? 'Quản lý cửa hàng' : 'Bài viết của tôi' }}
            </a>
        @endif
    </div>
</div>
