@extends('admin.layout')

@section('title', 'Quản Lý Quán Nổi Bật - Quán Mới Admin')
@section('page-title', 'Danh Sách Địa Điểm Quán Nổi Bật')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500 fill-1">star</span>
            Địa Điểm Quán Đang Được Đánh Dấu Nổi Bật ({{ $featuredQuan->total() }})
        </h3>
        <p class="text-xs text-slate-500 mb-6">Các quán được ghim nổi bật trên Slider trang chủ và vị trí ưu tiên tìm kiếm.</p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="p-3">Tên Quán</th>
                        <th class="p-3">Chủ Quán</th>
                        <th class="p-3">Địa Chỉ</th>
                        <th class="p-3 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($featuredQuan as $q)
                        <tr id="featured-row-{{ $q->id }}" class="hover:bg-slate-50 transition-colors">
                            <td class="p-3 font-bold text-slate-900 flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-500 fill-1 text-[18px]">star</span>
                                {{ $q->ten_quan }}
                            </td>
                            <td class="p-3 font-medium">{{ $q->chuQuan->ho_ten ?? 'N/A' }}</td>
                            <td class="p-3 text-slate-600">{{ Str::limit($q->dia_chi, 40) }}</td>
                            <td class="p-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5 flex-nowrap shrink-0">
                                    <button type="button" onclick="adminToggleFeaturedPage('{{ $q->id }}', this)" class="btn-enterprise btn-enterprise-danger btn-action-compact font-semibold shadow-2xs w-[115px] justify-center shrink-0" title="Hủy đánh dấu nổi bật">
                                        <span class="material-symbols-outlined text-[14px]">star_half</span>
                                        <span>Hủy Nổi Bật</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="featured-empty-row">
                            <td colspan="4" class="p-8 text-center text-xs text-slate-500">Chưa có quán nào được đánh dấu nổi bật.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $featuredQuan->links() }}</div>
    </div>
</div>

<script>
    function adminToggleFeaturedPage(quanId, btn) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const row = document.getElementById(`featured-row-${quanId}`);
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>';

        fetch(`/admin/quan-noi-bat/${quanId}/toggle`, {
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
                showRealtimeToast('Quán Nổi Bật', data.message || 'Cập nhật thành công!', '', 'star', 'bg-slate-700');
            }
            clearAdminPageCache();
            if (row) {
                row.style.transition = 'all 0.35s ease';
                row.style.opacity = '0';
                row.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    row.remove();
                    const tbody = document.querySelector('tbody');
                    if (tbody && tbody.querySelectorAll('tr').length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="p-8 text-center text-xs text-slate-500">Chưa có quán nào được đánh dấu nổi bật.</td></tr>';
                    }
                }, 350);
            }
        })
        .catch(err => {
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Lỗi Thao Tác', err.message || 'Không thể thay đổi trạng thái nổi bật.', '', 'error', 'bg-rose-500');
            }
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }
</script>
@endsection
