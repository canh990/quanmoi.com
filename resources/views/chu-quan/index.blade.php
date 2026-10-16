@extends('layouts.app')

@section('title', 'Quản lý cửa hàng | Quán Mới')

@section('content')
<main class="pt-6 pb-20 max-w-7xl mx-auto px-4 md:px-8 flex-grow">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <span class="inline-flex items-center gap-2 bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold">Chủ quán workspace</span>
            <h1 class="text-3xl md:text-4xl font-black text-on-surface mt-3">Quản lý cửa hàng của bạn</h1>
            <p class="text-gray-500 mt-2">Xem tất cả quán đã đăng và mở nhanh trang quản lý chi tiết của từng quán.</p>
        </div>
        <a href="{{ route('chu-quan.dang-quan') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-primary text-white font-bold shadow-sm hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined">add_location_alt</span>
            Đăng thêm quán mới
        </a>
    </div>

    <div id="owner-quan-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($quanList as $quan)
            <article id="owner-quan-card-{{ $quan->id }}" class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden flex flex-col h-full transition-all duration-300">
                <img src="{{ $quan->anh_bia_url ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $quan->ten_quan }}" class="w-full h-52 object-cover">
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h2 class="text-xl font-black text-on-surface break-words [overflow-wrap:anywhere]">{{ $quan->ten_quan }}</h2>
                            <p class="text-sm text-gray-500 mt-1 break-words [overflow-wrap:anywhere]">{{ $quan->ten_quan_huyen }}, {{ $quan->ten_tinh_thanh }}</p>
                        </div>
                        <span id="owner-quan-badge-{{ $quan->id }}" class="whitespace-nowrap flex-shrink-0 px-3 py-1 rounded-full text-xs font-bold {{ $quan->trang_thai === 'da_duyet' ? 'bg-emerald-100 text-emerald-700' : ($quan->trang_thai === 'bi_khoa' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800') }}">
                            {{ $quan->trang_thai === 'da_duyet' ? 'Đã duyệt' : ($quan->trang_thai === 'bi_khoa' ? 'Từ chối / Khóa' : 'Chờ duyệt') }}
                        </span>
                    </div>

                    <div id="owner-quan-note-{{ $quan->id }}"></div>

                    <p class="text-sm text-gray-500 mt-4 break-words [overflow-wrap:anywhere]">{{ $quan->mo_ta ?: 'Cập nhật mô tả để cửa hàng nổi bật hơn trên hệ thống.' }}</p>

                    <div class="mt-auto pt-4 flex flex-col gap-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">{{ $quan->gio_mo_cua }} - {{ $quan->gio_dong_cua }}</span>
                            <span class="font-bold text-primary">{{ number_format($quan->gia_nho_nhat, 0, ',', '.') }}đ - {{ number_format($quan->gia_lon_nhat, 0, ',', '.') }}đ</span>
                        </div>

                        <a href="{{ route('chu-quan.quan.show', ['slug' => $quan->slug]) }}" class="w-full h-11 rounded-2xl bg-primary text-white font-bold text-sm flex items-center justify-center gap-2 hover:bg-primary/90 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">storefront</span>
                            Quản lý cửa hàng
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-[28px] border border-dashed border-gray-200 bg-white p-10 text-center">
                <h2 class="text-2xl font-black text-on-surface">Bạn chưa có cửa hàng nào</h2>
                <p class="text-gray-500 mt-2">Đăng quán đầu tiên để bắt đầu sử dụng khu quản lý cửa hàng.</p>
                <a href="{{ route('chu-quan.dang-quan') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-3 rounded-2xl bg-primary text-white font-bold shadow-sm hover:bg-primary/90 transition-all">
                    <span class="material-symbols-outlined">add_location_alt</span>
                    Đăng quán ngay
                </a>
            </div>
        @endforelse
    </div>
</main>

<script>
    // Realtime synchronization for Venue Owner workspace
    window.addEventListener('quanmoi:quan-status-updated', function(e) {
        const notif = e.detail;
        if (!notif) return;

        const quanId = notif.quan_id;
        if (!quanId) return;

        const card = document.getElementById(`owner-quan-card-${quanId}`);
        const badge = document.getElementById(`owner-quan-badge-${quanId}`);
        const noteBox = document.getElementById(`owner-quan-note-${quanId}`);

        if (card) {
            // Highlight card
            card.classList.add('ring-2', 'ring-primary', 'shadow-lg');
            setTimeout(() => card.classList.remove('ring-2', 'ring-primary', 'shadow-lg'), 5000);

            // Update badge
            if (badge) {
                if (notif.action === 'approved' || notif.status === 'da_duyet') {
                    badge.className = 'whitespace-nowrap flex-shrink-0 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 transition-all';
                    badge.textContent = 'Đã duyệt';
                } else if (notif.action === 'rejected' || notif.status === 'bi_khoa') {
                    badge.className = 'whitespace-nowrap flex-shrink-0 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 transition-all';
                    badge.textContent = 'Từ chối / Khóa';
                }
            }

            // Update note box
            if (noteBox && notif.note) {
                noteBox.innerHTML = `
                    <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2">
                        <span class="material-symbols-outlined text-[16px] text-amber-600 shrink-0 mt-0.5">feedback</span>
                        <div class="flex-1 min-w-0">
                            <span class="font-bold text-[11px] block">Ghi chú từ Ban quản trị:</span>
                            <p class="mt-0.5">${notif.note}</p>
                        </div>
                    </div>
                `;
            }
        } else {
            // Background sync grid
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newGrid = doc.getElementById('owner-quan-grid');
                const currentGrid = document.getElementById('owner-quan-grid');
                if (newGrid && currentGrid) {
                    currentGrid.innerHTML = newGrid.innerHTML;
                }
            })
            .catch(() => {});
        }
    });
</script>
@endsection
