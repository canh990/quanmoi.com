@extends('admin.layout')

@section('title', 'Chi Tiết Người Dùng - Quán Mới Admin')
@section('page-title', 'Chi Tiết Tài Khoản')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    {{-- Header Action Bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.nguoi-dung.index') }}" class="p-2 bg-white border border-gray-200 hover:bg-gray-100 rounded-xl text-gray-600 transition-all flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    {{ $user->ho_ten }}
                    @if($user->isAdmin())
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">shield</span> {{ $user->ten_vai_tro_hien_thi }}
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                            {{ $user->ten_vai_tro_hien_thi }}
                        </span>
                    @endif

                    @if($user->da_xac_thuc)
                        <span class="material-symbols-outlined text-blue-500 text-xl" title="Tài khoản đã xác thực Tick Xanh" style="font-variation-settings: 'FILL' 1;">verified</span>
                    @endif
                </h2>
                <p class="text-xs text-gray-500">ID: {{ $user->id }} &bull; Đã tham gia: {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            @if(!$user->trashed())
                {{-- Toggle Status (Lock/Unlock) --}}
                <form action="{{ route('admin.nguoi-dung.toggle-trang-thai', $user->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $user->trang_thai === 'hoat_dong' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }}">
                        <span class="material-symbols-outlined text-base">{{ $user->trang_thai === 'hoat_dong' ? 'lock' : 'lock_open' }}</span>
                        {{ $user->trang_thai === 'hoat_dong' ? 'Khóa Tài Khoản' : 'Mở Khóa Tài Khoản' }}
                    </button>
                </form>

                {{-- Toggle Verified (Tick Xanh) --}}
                <form action="{{ route('admin.nguoi-dung.toggle-xac-thuc', $user->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $user->da_xac_thuc ? 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200' }}">
                        <span class="material-symbols-outlined text-base" {!! $user->da_xac_thuc ? '' : 'style="font-variation-settings: \'FILL\' 1;"' !!}>verified</span>
                        {{ $user->da_xac_thuc ? 'Gỡ Tick Xanh' : 'Cấp Tick Xanh' }}
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.nguoi-dung.edit', $user->id) }}" class="px-4 py-2 bg-primary text-white hover:bg-primary/90 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-base">edit</span> Chỉnh Sửa
            </a>
        </div>
    </div>

    {{-- Overview Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Trạng Thái Tài Khoản</p>
                <div class="mt-1 flex items-center gap-2">
                    @if($user->trashed())
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Đã xóa</span>
                    @elseif($user->trang_thai === 'bi_khoa')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-800">Bị khóa</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Hoạt động</span>
                    @endif
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600">
                <span class="material-symbols-outlined text-xl">account_circle</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Số Quán Sở Hữu</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $user->quan->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined text-xl">storefront</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Bài Đăng Blog</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $user->blogs->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                <span class="material-symbols-outlined text-xl">article</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Phân Quyền Override</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $user->quyenHanOverrides->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-xl">admin_panel_settings</span>
            </div>
        </div>
    </div>

    {{-- Main Info Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6">
            <div class="flex flex-col items-center text-center pb-4 border-b border-gray-100">
                <img src="{{ $user->anh_dai_dien ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80' }}" class="w-24 h-24 rounded-full object-cover border-2 border-primary/20 shadow-md mb-3" alt="" />
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-1">
                    {{ $user->ho_ten }}
                </h3>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>

            <div class="space-y-4 text-sm">
                <div>
                    <span class="text-xs text-gray-400 block font-bold uppercase">Số Điện Thoại</span>
                    <span class="font-medium text-gray-800">{{ $user->so_dien_thoai ?: 'Chưa cập nhật' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block font-bold uppercase">Giới Tính</span>
                    <span class="font-medium text-gray-800">
                        @if($user->gioi_tinh === 'nam') Nam
                        @elseif($user->gioi_tinh === 'nu') Nữ
                        @elseif($user->gioi_tinh === 'khac') Khác
                        @else Chưa cập nhật
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block font-bold uppercase">Ngày Sinh</span>
                    <span class="font-medium text-gray-800">{{ $user->ngay_sinh ? \Carbon\Carbon::parse($user->ngay_sinh)->format('d/m/Y') : 'Chưa cập nhật' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block font-bold uppercase">Địa Chỉ</span>
                    <span class="font-medium text-gray-800">{{ $user->dia_chi ?: 'Chưa cập nhật' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block font-bold uppercase">Liên Kết Google</span>
                    <span class="font-medium text-gray-800 flex items-center gap-1">
                        @if($user->google_id)
                            <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span> Đã liên kết Google ID
                        @else
                            <span class="text-gray-400">Chưa liên kết</span>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 block font-bold uppercase">Ngày Xác Thực Tick Xanh</span>
                    <span class="font-medium text-gray-800">{{ $user->ngay_xac_thuc ? $user->ngay_xac_thuc->format('d/m/Y H:i') : 'Chưa xác thực' }}</span>
                </div>
            </div>
        </div>

        {{-- Right Section: Tabs for Venues, Blogs, Permissions, Audit Logs --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Owned Venues --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">storefront</span>
                        Danh sách Quán sở hữu ({{ $user->quan->count() }})
                    </h3>
                </div>

                @if($user->quan->isEmpty())
                    <p class="text-sm text-gray-400 py-4 text-center">Người dùng này chưa có quán ăn/cửa hàng nào.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-xs font-bold text-gray-500 uppercase border-b border-gray-200">
                                    <th class="py-2.5 px-3">Tên Quán</th>
                                    <th class="py-2.5 px-3">Loại Hình</th>
                                    <th class="py-2.5 px-3">Trạng Thái</th>
                                    <th class="py-2.5 px-3 text-right">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($user->quan as $quanItem)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-3">
                                            <p class="font-bold text-gray-800">{{ $quanItem->ten_quan }}</p>
                                            <p class="text-xs text-gray-400">{{ $quanItem->dia_chi_chi_tiet }}</p>
                                        </td>
                                        <td class="py-3 px-3 text-xs text-gray-600">{{ $quanItem->loai_hinh_kinh_doanh ?: 'Chưa phân loại' }}</td>
                                        <td class="py-3 px-3">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $quanItem->trang_thai === 'da_duyet' ? 'bg-emerald-100 text-emerald-700' : ($quanItem->trang_thai === 'chua_duyet' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                                {{ $quanItem->trang_thai === 'da_duyet' ? 'Đã duyệt' : ($quanItem->trang_thai === 'chua_duyet' ? 'Chờ duyệt' : 'Đã ẩn/khóa') }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right">
                                            <a href="{{ route('admin.quan.edit', $quanItem->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg inline-block transition-all" title="Chỉnh sửa quán">
                                                <span class="material-symbols-outlined text-base">edit</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Permission Overrides --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600">admin_panel_settings</span>
                        Cấu Hình Phân Quyền Riêng ({{ $user->quyenHanOverrides->count() }})
                    </h3>
                    <a href="{{ route('admin.phan-quyen.index') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                        Quản lý Phân Quyền <span class="material-symbols-outlined text-sm">open_in_new</span>
                    </a>
                </div>

                @if($user->quyenHanOverrides->isEmpty())
                    <p class="text-sm text-gray-400 py-4 text-center">Tài khoản này đang sử dụng bộ quyền mặc định theo vai trò <strong>{{ $user->ten_vai_tro_hien_thi }}</strong>.</p>
                @else
                    <div class="space-y-2">
                        @foreach($user->quyenHanOverrides as $perm)
                            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 bg-gray-50 text-sm">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $perm->ten }}</p>
                                    <p class="text-xs text-gray-400">{{ $perm->mo_ta ?: 'Không có mô tả' }}</p>
                                </div>
                                <div>
                                    @if($perm->pivot->cho_phep)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">check_circle</span> Đã cấp quyền
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">cancel</span> Tước quyền
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Audit Logs --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-600">history</span>
                        Lịch Sử Thao Tác Gần Đây
                    </h3>
                </div>

                @if($auditLogs->isEmpty())
                    <p class="text-sm text-gray-400 py-4 text-center">Chưa có nhật ký hoạt động nào ghi nhận cho người dùng này.</p>
                @else
                    <div class="space-y-3">
                        @foreach($auditLogs as $log)
                            <div class="flex items-start justify-between p-3 rounded-xl border border-gray-100 bg-gray-50/50 text-xs">
                                <div>
                                    <span class="font-bold text-gray-800 uppercase px-2 py-0.5 rounded bg-gray-200 text-[10px] inline-block mb-1">{{ $log->action }}</span>
                                    <p class="text-gray-600">IP: {{ $log->ip_address ?: 'Internal' }}</p>
                                </div>
                                <span class="text-gray-400">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
