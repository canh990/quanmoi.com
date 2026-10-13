@extends('admin.layout')

@section('title', 'Cấu Hình Bảo Mật - Quán Mới Admin')
@section('page-title', 'Bảo Mật & An Toàn Hệ Thống')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs max-w-3xl">
        <h3 class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
            <span class="material-symbols-outlined text-rose-600">shield</span>
            Cấu Hình Bảo Mật Quản Trị
        </h3>
        <p class="text-xs text-slate-500 mb-6">Thiết lập tham số giới hạn truy cập, chống tấn công Brute-force và bảo vệ phiên đăng nhập Admin.</p>

        <form action="{{ route('admin.bao-mat.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-slate-900">Xác thực 2 yếu tố (2FA)</h4>
                    <p class="text-[11px] text-slate-500">Yêu cầu mã OTP qua Email/Authenticator khi Admin đăng nhập.</p>
                </div>
                <input type="checkbox" name="two_factor_auth" value="1" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500 w-5 h-5">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Giới hạn lần đăng nhập thất bại / phút</label>
                <input type="number" name="login_rate_limit" value="{{ $securitySettings['login_rate_limit'] }}" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Thời gian tự động hết hạn Session Admin (phút)</label>
                <input type="number" name="session_timeout" value="{{ $securitySettings['session_timeout'] }}" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">IP Whitelist (Phân tách bằng dấu phẩy)</label>
                <input type="text" name="ip_whitelist" value="{{ $securitySettings['ip_whitelist'] }}" placeholder="127.0.0.1, 192.168.1.1" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
            </div>

            <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Lưu Cấu Hình Bảo Mật
            </button>
        </form>
    </div>
</div>
@endsection
