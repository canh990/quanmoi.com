@extends('admin.layout')

@section('title', 'Quản Lý Bài Viết & Duyệt Blog - Quán Mới Admin')
@section('page-title', 'Quản Lý & Phê Duyệt Bài Viết')

@section('content')
<div class="space-y-6">
    <!-- Thống kê trạng thái bài viết (Click để lọc và hiển thị nội dung ngay) -->
    <div id="admin-blog-stats-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        {{-- Card 1: Tổng số bài --}}
        <a href="{{ route('admin.blog.posts.index') }}" 
           class="group bg-white p-5 rounded-2xl border transition-all duration-200 cursor-pointer flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 {{ !request('status') ? 'border-blue-500 ring-2 ring-blue-500/20 shadow-sm bg-blue-50/20' : 'border-gray-200/80 hover:border-blue-300' }}"
           title="Xem tất cả bài viết">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider {{ !request('status') ? 'text-blue-700' : 'text-gray-500 group-hover:text-blue-600' }}">Tổng số bài</p>
                <p id="stat-blog-total" class="text-2xl font-black text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <span class="material-symbols-outlined text-[22px]">article</span>
            </div>
        </a>

        {{-- Card 2: Chờ duyệt --}}
        <a href="{{ route('admin.blog.posts.index', ['status' => 'pending']) }}" 
           class="group bg-white p-5 rounded-2xl border transition-all duration-200 cursor-pointer flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 {{ request('status') === 'pending' ? 'border-amber-500 ring-2 ring-amber-500/20 shadow-sm bg-amber-50/40' : 'border-amber-200/80 hover:border-amber-400 bg-gradient-to-br from-white to-amber-50/30' }}"
           title="Xem bài viết chờ duyệt">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-amber-700 group-hover:text-amber-800">Chờ duyệt</p>
                <p id="stat-blog-pending" class="text-2xl font-black text-amber-600 mt-1">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-100/80 text-amber-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <span class="material-symbols-outlined text-[22px]">pending_actions</span>
            </div>
        </a>

        {{-- Card 3: Cần sửa --}}
        <a href="{{ route('admin.blog.posts.index', ['status' => 'need_revision']) }}" 
           class="group bg-white p-5 rounded-2xl border transition-all duration-200 cursor-pointer flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 {{ request('status') === 'need_revision' ? 'border-orange-500 ring-2 ring-orange-500/20 shadow-sm bg-orange-50/40' : 'border-orange-200/80 hover:border-orange-400' }}"
           title="Xem bài viết cần chỉnh sửa">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-orange-700 group-hover:text-orange-800">Cần sửa</p>
                <p id="stat-blog-need_revision" class="text-2xl font-black text-orange-600 mt-1">{{ $stats['need_revision'] ?? 0 }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <span class="material-symbols-outlined text-[22px]">rule</span>
            </div>
        </a>

        {{-- Card 4: Từ chối --}}
        <a href="{{ route('admin.blog.posts.index', ['status' => 'rejected']) }}" 
           class="group bg-white p-5 rounded-2xl border transition-all duration-200 cursor-pointer flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 {{ request('status') === 'rejected' ? 'border-rose-500 ring-2 ring-rose-500/20 shadow-sm bg-rose-50/40' : 'border-rose-200/80 hover:border-rose-400' }}"
           title="Xem bài viết đã từ chối">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-rose-700 group-hover:text-rose-800">Từ chối</p>
                <p id="stat-blog-rejected" class="text-2xl font-black text-rose-600 mt-1">{{ $stats['rejected'] ?? 0 }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <span class="material-symbols-outlined text-[22px]">cancel</span>
            </div>
        </a>

        {{-- Card 5: Đã xuất bản --}}
        <a href="{{ route('admin.blog.posts.index', ['status' => 'published']) }}" 
           class="group bg-white p-5 rounded-2xl border transition-all duration-200 cursor-pointer flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 {{ request('status') === 'published' ? 'border-emerald-500 ring-2 ring-emerald-500/20 shadow-sm bg-emerald-50/40' : 'border-emerald-200/80 hover:border-emerald-400' }}"
           title="Xem bài viết đã xuất bản">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 group-hover:text-emerald-800">Đã xuất bản</p>
                <p id="stat-blog-published" class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['published'] ?? 0 }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <span class="material-symbols-outlined text-[22px]">check_circle</span>
            </div>
        </a>
    </div>

    <!-- Bộ lọc & Lối tắt -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col lg:flex-row items-center justify-between gap-4">
        <div class="flex items-center flex-wrap gap-2 w-full lg:w-auto">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mr-1">Lọc nhanh:</span>
            <a href="{{ route('admin.blog.posts.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ !request('status') ? 'bg-primary text-white shadow-sm ring-1 ring-primary' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Tất cả ({{ $stats['total'] ?? 0 }})
            </a>
            <a href="{{ route('admin.blog.posts.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-sm ring-1 ring-amber-600' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                Chờ duyệt ({{ $stats['pending'] ?? 0 }})
            </a>
            <a href="{{ route('admin.blog.posts.index', ['status' => 'need_revision']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'need_revision' ? 'bg-orange-600 text-white shadow-sm ring-1 ring-orange-600' : 'bg-orange-50 text-orange-800 hover:bg-orange-100' }}">
                Cần sửa ({{ $stats['need_revision'] ?? 0 }})
            </a>
            <a href="{{ route('admin.blog.posts.index', ['status' => 'rejected']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'rejected' ? 'bg-rose-600 text-white shadow-sm ring-1 ring-rose-600' : 'bg-rose-50 text-rose-800 hover:bg-rose-100' }}">
                Từ chối ({{ $stats['rejected'] ?? 0 }})
            </a>
            <a href="{{ route('admin.blog.posts.index', ['status' => 'published']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'published' ? 'bg-emerald-600 text-white shadow-sm ring-1 ring-emerald-600' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                Đã xuất bản ({{ $stats['published'] ?? 0 }})
            </a>

            <form method="GET" action="{{ route('admin.blog.posts.index') }}" class="inline-flex items-center ml-1">
                <select name="status" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-xl border border-gray-300 text-xs font-medium outline-none focus:border-amber-500 bg-white shadow-2xs">
                    <option value="">Khác...</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                    <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Đã ẩn</option>
                </select>
            </form>

            @if(request('status'))
                <a href="{{ route('admin.blog.posts.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-xl transition-colors inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">close</span>
                    <span>Bỏ lọc</span>
                </a>
            @endif
        </div>

        <div class="flex items-center gap-2 text-xs text-gray-500 self-end lg:self-auto shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Duyệt AJAX tức thì</span>
            </span>
        </div>
    </div>

    <!-- Danh sách bài viết -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
        <div class="w-full overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-xs min-w-[920px] table-fixed-enterprise">
                <colgroup>
                    <col class="w-[28%]">
                    <col class="w-[13%]">
                    <col class="w-[12%]">
                    <col class="w-[6%]">
                    <col class="w-[10%]">
                    <col class="w-[31%]">
                </colgroup>
                <thead class="bg-gray-50/80 border-b border-gray-200/80 text-gray-500 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-3.5 py-3">Bài viết</th>
                        <th class="px-3.5 py-3">Tác giả</th>
                        <th class="px-3.5 py-3">Trạng thái</th>
                        <th class="px-2 py-3 text-center whitespace-nowrap">Nổi bật</th>
                        <th class="px-3.5 py-3">Ngày tạo</th>
                        <th class="px-3.5 pr-6 py-3 text-right whitespace-nowrap">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="admin-blog-tbody" class="divide-y divide-gray-100 text-xs">
                    @forelse($blogs as $blog)
                    <tr id="admin-blog-row-{{ $blog->id }}" data-status="{{ $blog->status }}" class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-3.5 py-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 shrink-0 border border-gray-200/60">
                                    <img src="{{ $blog->cover_image_url }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80';" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('admin.blog.posts.show', $blog) }}" class="font-bold text-gray-900 hover:text-amber-700 block transition-colors text-xs cell-wrap-2line" title="{{ $blog->title }}">
                                        {{ $blog->title }}
                                    </a>
                                    <p class="text-[11px] text-gray-400 mt-0.5 cell-wrap-1line">{{ $blog->category->name ?? 'Không phân loại' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3.5 py-3">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <img src="{{ $blog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($blog->user->ho_ten ?? 'U') }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($blog->user->ho_ten ?? 'U') }}';" class="w-6 h-6 rounded-full border border-gray-200 shrink-0">
                                <span class="font-semibold text-gray-800 text-xs cell-wrap-1line" title="{{ $blog->user->ho_ten ?? 'Tài khoản ẩn' }}">{{ $blog->user->ho_ten ?? 'Tài khoản ẩn' }}</span>
                            </div>
                        </td>
                        <td class="px-3.5 py-3" id="admin-blog-status-td-{{ $blog->id }}">
                            @php
                                $statusConfig = [
                                    'draft' => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Bản nháp', 'icon' => 'edit_note'],
                                    'pending' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'label' => 'Chờ duyệt', 'icon' => 'pending_actions'],
                                    'need_revision' => ['bg' => 'bg-orange-100 text-orange-800 border-orange-200', 'label' => 'Cần sửa', 'icon' => 'rule'],
                                    'published' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'label' => 'Xuất bản', 'icon' => 'check_circle'],
                                    'scheduled' => ['bg' => 'bg-purple-100 text-purple-800 border-purple-200', 'label' => 'Lên lịch', 'icon' => 'schedule'],
                                    'rejected' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-200', 'label' => 'Từ chối', 'icon' => 'cancel'],
                                    'hidden' => ['bg' => 'bg-gray-200 text-gray-600 border-gray-300', 'label' => 'Đã ẩn', 'icon' => 'visibility_off'],
                                ];
                                $cfg = $statusConfig[$blog->status] ?? $statusConfig['draft'];
                            @endphp
                            <span id="admin-blog-status-badge-{{ $blog->id }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold border {{ $cfg['bg'] }} whitespace-nowrap">
                                <span class="material-symbols-outlined text-[13px]">{{ $cfg['icon'] }}</span>
                                <span>{{ $cfg['label'] }}</span>
                            </span>
                        </td>
                        <td class="px-3.5 py-3 text-center" id="admin-blog-hero-td-{{ $blog->id }}">
                            @if($blog->is_hero)
                                <span class="material-symbols-outlined text-amber-500 fill-1 text-[18px]" title="Bài viết Hero nổi bật">star</span>
                            @else
                                <button type="button" onclick="adminToggleHero({{ $blog->id }}, this)" class="text-gray-300 hover:text-amber-500 transition-colors cursor-pointer" title="Đặt làm Hero nổi bật">
                                    <span class="material-symbols-outlined text-[18px]">star</span>
                                </button>
                            @endif
                        </td>
                        <td class="px-3.5 py-3 text-gray-500 font-medium text-xs whitespace-nowrap">
                            {{ $blog->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-3.5 pr-6 py-3 text-right whitespace-nowrap" id="admin-blog-actions-{{ $blog->id }}">
                            <div class="inline-flex items-center justify-end gap-1.5 flex-nowrap shrink-0">
                                {{-- Nút Xem bài viết (Cố định chiều rộng 68px) --}}
                                <button type="button" onclick="openBlogQuickPreviewModal({{ $blog->id }})" class="btn-enterprise btn-enterprise-secondary btn-action-compact font-semibold w-[68px] justify-center shrink-0" title="Xem nhanh nội dung bài viết">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    <span>Xem</span>
                                </button>

                                @if($blog->status === 'pending')
                                    {{-- Nút duyệt bài viết --}}
                                    <button type="button" onclick="adminQuickApproveBlog({{ $blog->id }}, '{{ addslashes($blog->title) }}')" class="btn-enterprise btn-enterprise-success btn-action-compact font-bold shadow-2xs w-[72px] justify-center shrink-0" title="Phê duyệt và xuất bản ngay">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                        <span>Duyệt</span>
                                    </button>
                                    {{-- Nút không duyệt / yêu cầu sửa --}}
                                    <button type="button" onclick="adminOpenRejectModal({{ $blog->id }}, '{{ addslashes($blog->title) }}', '{{ $blog->status }}')" class="btn-enterprise btn-enterprise-danger btn-action-compact font-semibold shadow-2xs w-[98px] justify-center shrink-0" title="Không duyệt hoặc yêu cầu sửa">
                                        <span class="material-symbols-outlined text-[14px]">cancel</span>
                                        <span>Không duyệt</span>
                                    </button>
                                @else
                                    @if($blog->status !== 'published')
                                        <button type="button" onclick="adminQuickApproveBlog({{ $blog->id }}, '{{ addslashes($blog->title) }}')" class="btn-enterprise btn-enterprise-success btn-action-compact font-bold shadow-2xs w-[98px] justify-center shrink-0" title="Duyệt bài viết này">
                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                            <span>Duyệt</span>
                                        </button>
                                    @endif
                                    @if($blog->status !== 'rejected')
                                        <button type="button" onclick="adminOpenRejectModal({{ $blog->id }}, '{{ addslashes($blog->title) }}', '{{ $blog->status }}')" class="btn-enterprise btn-enterprise-danger btn-action-compact font-semibold shadow-2xs w-[98px] justify-center shrink-0" title="Từ chối hoặc yêu cầu chỉnh sửa">
                                            <span class="material-symbols-outlined text-[14px]">cancel</span>
                                            <span>Không duyệt</span>
                                        </button>
                                    @endif
                                @endif

                                {{-- Xóa bài viết --}}
                                <button type="button" onclick="adminConfirmDeleteBlog({{ $blog->id }}, '{{ addslashes($blog->title) }}')" class="btn-enterprise btn-enterprise-secondary btn-action-compact text-gray-400 hover:text-rose-600 hover:bg-rose-50 border-gray-200 w-8 h-8 p-0 flex items-center justify-center shrink-0" title="Xóa bài viết">
                                    <span class="material-symbols-outlined text-[14px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-blog-row">
                        <td colspan="6" class="px-3.5 py-10 text-center text-gray-400 font-medium">
                            <span class="material-symbols-outlined text-3xl text-gray-300 block mb-2">article</span>
                            Không tìm thấy bài viết nào phù hợp với bộ lọc hiện tại.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($blogs->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $blogs->links() }}
        </div>
        @endif
    </div>
</div>



<script>
    // Cập nhật thẻ trạng thái của một dòng bài viết trong bảng
    function updateRowStatusBadge(blogId, status, statusLabel) {
        const badgeEl = document.getElementById(`admin-blog-status-badge-${blogId}`);
        const rowEl = document.getElementById(`admin-blog-row-${blogId}`);
        const actionsEl = document.getElementById(`admin-blog-actions-${blogId}`);

        if (rowEl) {
            rowEl.setAttribute('data-status', status);
        }

        const mapConfig = {
            published: { bg: 'bg-emerald-100 text-emerald-800 border-emerald-200', icon: 'check_circle', label: statusLabel || 'Xuất bản' },
            rejected: { bg: 'bg-rose-100 text-rose-800 border-rose-200', icon: 'cancel', label: statusLabel || 'Từ chối' },
            need_revision: { bg: 'bg-orange-100 text-orange-800 border-orange-200', icon: 'rule', label: statusLabel || 'Cần sửa' },
            pending: { bg: 'bg-amber-100 text-amber-800 border-amber-200', icon: 'pending_actions', label: statusLabel || 'Chờ duyệt' },
            draft: { bg: 'bg-gray-100 text-gray-700 border-gray-200', icon: 'edit_note', label: statusLabel || 'Bản nháp' },
            hidden: { bg: 'bg-gray-200 text-gray-600 border-gray-300', icon: 'visibility_off', label: statusLabel || 'Đã ẩn' },
        };

        const cfg = mapConfig[status] || mapConfig.pending;

        if (badgeEl) {
            badgeEl.className = `inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border ${cfg.bg}`;
            badgeEl.innerHTML = `
                <span class="material-symbols-outlined text-[13px]">${cfg.icon}</span>
                <span>${cfg.label}</span>
            `;
        }

        // Cập nhật các nút thao tác sau khi đổi trạng thái
        if (actionsEl) {
            actionsEl.className = 'px-3.5 pr-6 py-3 text-right whitespace-nowrap';
            actionsEl.innerHTML = `
                <div class="inline-flex items-center justify-end gap-1.5 flex-nowrap shrink-0">
                    <button type="button" onclick="openBlogQuickPreviewModal(${blogId})" class="btn-enterprise btn-enterprise-secondary btn-action-compact font-semibold w-[68px] justify-center shrink-0" title="Xem nhanh nội dung bài viết">
                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                        <span>Xem</span>
                    </button>
                    ${status !== 'published' ? `
                        <button type="button" onclick="adminQuickApproveBlog(${blogId}, '')" class="btn-enterprise btn-enterprise-success btn-action-compact font-bold shadow-2xs w-[98px] justify-center shrink-0" title="Duyệt bài viết này">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                            <span>Duyệt</span>
                        </button>
                    ` : ''}
                    ${status !== 'rejected' ? `
                        <button type="button" onclick="adminOpenRejectModal(${blogId}, '', '${status}')" class="btn-enterprise btn-enterprise-danger btn-action-compact font-semibold shadow-2xs w-[98px] justify-center shrink-0" title="Từ chối bài viết">
                            <span class="material-symbols-outlined text-[14px]">cancel</span>
                            <span>Không duyệt</span>
                        </button>
                    ` : ''}
                    <button type="button" onclick="adminConfirmDeleteBlog(${blogId}, '')" class="btn-enterprise btn-enterprise-secondary btn-action-compact text-gray-400 hover:text-rose-600 hover:bg-rose-50 border-gray-200 w-8 h-8 p-0 flex items-center justify-center shrink-0" title="Xóa bài viết">
                        <span class="material-symbols-outlined text-[14px]">delete</span>
                    </button>
                </div>
            `;
        }
    }

    // Toggle Hero bằng AJAX
    function adminToggleHero(blogId, btnEl) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const url = `/admin/blog/bai-viet/${blogId}/hero`;
        fetch(url, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || 'Lỗi đặt Hero');
            return data;
        })
        .then(data => {
            if (data.success) {
                if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Hero Nổi Bật', data.message || 'Đã đặt làm bài viết Hero.', '', 'star', 'bg-amber-500');
                }
                // Đặt lại các icon hero khác trên bảng
                document.querySelectorAll('#admin-blog-tbody td[id^="admin-blog-hero-td-"]').forEach(td => {
                    const otherId = td.id.replace('admin-blog-hero-td-', '');
                    td.innerHTML = `
                        <button type="button" onclick="adminToggleHero(${otherId}, this)" class="text-gray-300 hover:text-amber-500 transition-colors cursor-pointer" title="Đặt làm Hero nổi bật">
                            <span class="material-symbols-outlined text-[20px]">star</span>
                        </button>
                    `;
                });
                const currentTd = document.getElementById(`admin-blog-hero-td-${blogId}`);
                if (currentTd) {
                    currentTd.innerHTML = `<span class="material-symbols-outlined text-amber-500 fill-1 text-[20px]" title="Bài viết Hero nổi bật">star</span>`;
                }
            }
        })
        .catch(err => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lỗi', err.message || 'Không thể đặt Hero', '', 'error', 'bg-rose-500');
            }
        });
    }

    // Realtime synchronization engine for Admin Blog Management
    let isSyncingBlogTable = false;
    let lastKnownPendingCount = {{ $stats['pending'] ?? 0 }};

    function syncAdminBlogTable(highlightBlogId = null) {
        if (isSyncingBlogTable) return;
        isSyncingBlogTable = true;

        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newTbody = doc.getElementById('admin-blog-tbody');
            const currentTbody = document.getElementById('admin-blog-tbody');
            if (newTbody && currentTbody) {
                currentTbody.innerHTML = newTbody.innerHTML;
            }

            const newStats = doc.getElementById('admin-blog-stats-grid');
            const currentStats = document.getElementById('admin-blog-stats-grid');
            if (newStats && currentStats) {
                currentStats.innerHTML = newStats.innerHTML;
            }

            if (highlightBlogId) {
                const row = document.getElementById(`admin-blog-row-${highlightBlogId}`);
                if (row) {
                    row.classList.add('bg-amber-50', 'ring-2', 'ring-amber-400');
                    setTimeout(() => {
                        row.classList.remove('bg-amber-50', 'ring-2', 'ring-amber-400');
                    }, 4000);
                }
            }
        })
        .catch(() => {})
        .finally(() => {
            isSyncingBlogTable = false;
        });
    }

    window.addEventListener('admin:notifications-updated', function(e) {
        if (!e.detail) return;
        const data = e.detail;

        // 1. Cập nhật số lượng bài viết chờ duyệt trên thẻ thống kê
        if (data.pending_blog !== undefined) {
            const statPending = document.getElementById('stat-blog-pending');
            if (statPending) statPending.textContent = data.pending_blog;
            const badgePending = document.getElementById('tab-badge-pending');
            if (badgePending) badgePending.textContent = data.pending_blog;
        }

        // 2. Tự động đồng bộ bảng khi có bài viết mới hoặc số lượng thay đổi
        if (data.latest_blog && data.latest_blog.id) {
            const existingRow = document.getElementById(`admin-blog-row-${data.latest_blog.id}`);
            if (!existingRow) {
                syncAdminBlogTable(data.latest_blog.id);
            }
        } else if (data.pending_blog !== undefined && data.pending_blog !== lastKnownPendingCount) {
            lastKnownPendingCount = data.pending_blog;
            syncAdminBlogTable();
        }
    });
</script>
@endsection
