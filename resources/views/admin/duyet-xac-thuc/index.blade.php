@extends('admin.layout')

@section('title', 'Duyệt Xác Thực - Quán Mới Admin')
@section('page-title', 'Phê Duyệt Quán & Xác Thực Người Dùng')

@section('content')
<div class="space-y-8">
    {{-- Section 1: Pending Venues --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-600">storefront</span>
            Danh Sách Quán Chờ Duyệt ({{ $pendingQuan->total() }})
        </h3>
        <p class="text-xs text-slate-500 mb-4">Các địa điểm được đăng bởi người dùng hoặc chủ quán chờ Ban quản trị duyệt xuất hiện công khai.</p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="p-3">Tên Quán</th>
                        <th class="p-3">Chủ Quán</th>
                        <th class="p-3">Địa Chỉ</th>
                        <th class="p-3">Ngày Đăng</th>
                        <th class="p-3 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingQuan as $q)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-900">{{ $q->ten_quan }}</td>
                            <td class="p-3 font-medium">{{ $q->chuQuan->ho_ten ?? 'N/A' }}</td>
                            <td class="p-3 text-slate-600">{{ Str::limit($q->dia_chi, 50) }}</td>
                            <td class="p-3 text-slate-500">{{ $q->created_at ? $q->created_at->format('d/m/Y H:i') : '' }}</td>
                            <td class="p-3 text-right">
                                <form action="{{ route('admin.duyet-xac-thuc.quan', $q->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                        Duyệt & Xác thực
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-xs text-slate-500">Không có quán nào đang chờ duyệt.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $pendingQuan->links() }}</div>
    </div>

    {{-- Section 2: Unverified Users --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
            <span class="material-symbols-outlined text-indigo-600">verified_user</span>
            Tài Khoản Chưa Xác Thực ({{ $unverifiedUsers->total() }})
        </h3>
        <p class="text-xs text-slate-500 mb-4">Danh sách tài khoản chưa qua xác thực email/SĐT.</p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="p-3">Họ và Tên</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Số Điện Thoại</th>
                        <th class="p-3 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($unverifiedUsers as $u)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-900">{{ $u->ho_ten }}</td>
                            <td class="p-3 text-slate-600">{{ $u->email }}</td>
                            <td class="p-3 text-slate-600">{{ $u->so_dien_thoai ?? 'N/A' }}</td>
                            <td class="p-3 text-right">
                                <form action="{{ route('admin.duyet-xac-thuc.user', $u->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition-colors">
                                        Đánh dấu đã xác thực
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-xs text-slate-500">Tất cả tài khoản đều đã xác thực.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
