@extends('admin.layout')

@section('title', 'Tạo Khuyến Mãi & Voucher AI - Vận Hành & Phân Quyền')
@section('page-title', 'Tạo Khuyến Mãi & Voucher AI')

@section('content')
<div class="space-y-6">

    {{-- KHỐI STUDIO TẠO KHUYẾN MÃI --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-600">auto_awesome</span>
                    Kira AI Studio - Khởi Tạo Chiến Dịch Ưu Đãi Mới
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Chọn quán và thiết lập thông tin ưu đãi. Kira AI sẽ tạo ngay tiêu đề, mã voucher và bài viết quảng bá.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 mt-6">
            {{-- Cột trái: Form nhập liệu --}}
            <div class="lg:col-span-5 space-y-4">
                {{-- Chọn quán từ hệ thống --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Áp dụng cho Quán ăn / Nhà hàng <span class="text-red-500">*</span>
                    </label>
                    <select id="promo-quan-id" onchange="onSelectVenue(this)" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white font-medium">
                        <option value="">-- Chọn quán cần tạo ưu đãi --</option>
                        @foreach($danhSachQuan as $quan)
                            <option value="{{ $quan->id }}"
                                    data-ten="{{ $quan->ten_quan }}"
                                    data-loai="{{ $quan->loai_hinh_kinh_doanh ?? 'Quán ăn' }}"
                                    data-diachi="{{ trim("{$quan->dia_chi_chi_tiet}, {$quan->ten_quan_huyen}, {$quan->ten_tinh_thanh}", ', ') }}"
                                    data-monan="{{ json_encode($quan->danh_sach_mon ?? []) }}">
                                {{ $quan->ten_quan }} ({{ $quan->loai_hinh_kinh_doanh ?? 'Quán ăn' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tên quán & Loại hình --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tên quán hiển thị</label>
                        <input type="text" id="promo-ten-quan" placeholder="VD: Phở Bò Gia Truyền" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Loại hình kinh doanh</label>
                        <input type="text" id="promo-loai-hinh" placeholder="VD: Quán ăn, Cafe, Lẩu nướng" value="Quán ăn" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                    </div>
                </div>

                {{-- Địa chỉ quán --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Địa chỉ quán</label>
                    <input type="text" id="promo-dia-chi" placeholder="VD: 123 Cầu Giấy, Hà Nội" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                </div>

                {{-- Món áp dụng ưu đãi --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">Món áp dụng ưu đãi</label>
                        <span id="venue-menu-hint" class="text-[11px] text-slate-400">Gợi ý từ thực đơn quán</span>
                    </div>
                    <input type="text" id="promo-mon-an" placeholder="VD: Toàn bộ menu HOẶC chọn món bên dưới" value="Toàn bộ menu" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                    <p id="venue-dishes-error" class="hidden text-[11px] font-bold text-rose-600 mt-1"></p>

                    <div id="venue-dishes-tags" class="flex flex-wrap gap-1.5 mt-2 max-h-24 overflow-y-auto"></div>
                </div>

                {{-- Hình thức khuyến mãi & Mức giảm --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Hình thức khuyến mãi</label>
                        <select id="promo-loai-km" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white">
                            <option value="giam_phan_tram">Giảm theo % (20%, 30%...)</option>
                            <option value="giam_tien">Giảm số tiền cố định (50k, 100k...)</option>
                            <option value="tang_mon">Mua 1 Tặng 1 / Tặng đồ uống</option>
                            <option value="dong_gia">Đồng giá (19k, 39k, 99k...)</option>
                            <option value="voucher">Voucher ưu đãi đặc quyền</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mức giảm / Quà tặng</label>
                        <input type="text" id="promo-gia-tri-giam" placeholder="VD: 20%, 50.000đ, 1 Trà đào" value="20%" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                    </div>
                </div>

                {{-- Dịp khuyến mãi --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Dịp / Sự kiện khuyến mãi</label>
                    <input type="text" id="promo-dip-km" placeholder="VD: Khai trương, Cuối tuần, Sinh nhật quán" value="Ưu đãi ẩm thực độc quyền" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                </div>

                {{-- Phong cách viết & Thời gian --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Phong cách giọng văn</label>
                        <select id="promo-phong-cach" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white">
                            <option value="hao_hung">🔥 Hào hứng, sôi nổi (Foodie / Giới trẻ)</option>
                            <option value="sang_trong">✨ Sang trọng, tinh tế (Fine Dining)</option>
                            <option value="hai_huoc">😂 Hài hước, Gen Z (Bắt trend)</option>
                            <option value="ngan_gon">⚡ Ngắn gọn, súc tích (Flash Sale)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Thời gian áp dụng</label>
                        <div class="grid grid-cols-2 gap-1.5">
                            <div>
                                <input type="date" id="promo-ngay-bat-dau" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-xl outline-none transition focus:ring-2 focus:ring-purple-500">
                                <span class="text-[10px] text-slate-400 block mt-0.5">Bắt đầu</span>
                            </div>
                            <div>
                                <input type="date" id="promo-ngay-ket-thuc" value="{{ date('Y-m-d', strtotime('+7 days')) }}" min="{{ date('Y-m-d') }}" class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded-xl outline-none transition focus:ring-2 focus:ring-purple-500">
                                <span class="text-[10px] text-slate-400 block mt-0.5">Kết thúc</span>
                            </div>
                        </div>
                        <p id="promo-date-error" class="hidden text-[11px] font-bold text-rose-600 mt-1.5 flex items-center gap-1 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                            <span class="material-symbols-outlined text-[14px] shrink-0">error</span>
                            <span id="promo-date-error-text"></span>
                        </p>
                    </div>
                </div>

                {{-- Yêu cầu bổ sung --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Yêu cầu bổ sung (Tùy chọn)</label>
                    <textarea id="promo-yeu-cau-them" rows="2" placeholder="VD: Áp dụng hóa đơn trên 200k, áp dụng ăn tại bàn..." class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none"></textarea>
                </div>

                {{-- Nút Tạo Với AI --}}
                <button type="button" onclick="generateAiPromotion()" id="btn-generate-promo"
                        class="w-full py-3 bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-700 hover:from-purple-700 hover:to-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-purple-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer"
                        style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); background-color: #7c3aed; color: #ffffff;">
                    <span class="material-symbols-outlined text-[19px]">auto_awesome</span>
                    Kira AI Tạo Chiến Dịch Ngay
                </button>
            </div>

            {{-- Cột phải: Kết quả sinh ra từ AI --}}
            <div class="lg:col-span-7 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-purple-600">article</span>
                        Nội Dung Chiến Dịch Do AI Khởi Tạo
                    </h3>
                    <span id="promo-result-badge" class="hidden text-[11px] font-bold px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 border border-purple-200"></span>
                </div>

                {{-- Vùng kết quả rỗng (Placeholder) --}}
                <div id="promo-empty-state" class="flex-1 min-h-[420px] rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 flex flex-col items-center justify-center p-8 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-purple-100 text-purple-600 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-[32px]">psychology</span>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm mb-1">Chưa có nội dung chiến dịch</h4>
                    <p class="text-xs text-slate-500 max-w-sm">Chọn thông tin quán ở khung bên trái và bấm <strong>"Kira AI Tạo Chiến Dịch Ngay"</strong> để sinh tiêu đề, mã voucher và bài viết quảng bá chuyên nghiệp.</p>
                </div>

                {{-- Loading state --}}
                <div id="promo-loading-state" class="hidden flex-1 min-h-[420px] rounded-2xl border border-purple-100 bg-purple-50/30 flex flex-col items-center justify-center p-8 text-center">
                    <div class="w-12 h-12 border-3 border-purple-600 border-t-transparent rounded-full animate-spin mb-3"></div>
                    <p class="font-bold text-purple-900 text-sm">Kira AI đang suy nghĩ và sáng tạo chiến dịch...</p>
                    <p class="text-xs text-purple-600 mt-1">Đang tối ưu nội dung tiếp thị, khẩu hiệu và thể lệ ưu đãi F&B.</p>
                </div>

                {{-- Hộp hiển thị kết quả AI --}}
                <div id="promo-result-box" class="hidden flex-1 flex flex-col space-y-4 bg-white rounded-2xl border border-purple-200/80 p-5 shadow-xs">
                    {{-- 1. Tiêu đề khuyến mãi --}}
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-purple-700 mb-1">Tiêu đề chiến dịch</label>
                        <input type="text" id="res-tieu-de" class="w-full font-black text-slate-900 text-sm px-3 py-2 bg-purple-50/50 border border-purple-200 rounded-xl outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    {{-- 2. Mã Voucher & Slogan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-purple-700 mb-1">Mã Voucher Gợi Ý</label>
                            <div class="flex gap-1.5">
                                <input type="text" id="res-ma-voucher" class="w-full font-mono font-black text-purple-900 text-sm px-3 py-2 bg-purple-50 border border-purple-200 rounded-xl outline-none uppercase">
                                <button type="button" onclick="copyVoucherCode()" title="Sao chép mã" class="px-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition flex items-center justify-center cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">content_copy</span>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-purple-700 mb-1">Slogan Banner</label>
                            <input type="text" id="res-slogan" class="w-full italic text-xs font-semibold text-slate-700 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                        </div>
                    </div>

                    {{-- 3. Bài viết chi tiết mạng xã hội --}}
                    <div class="flex-1 flex flex-col">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-purple-700">Bài viết Social Media (Facebook / TikTok / Zalo)</label>
                            <button type="button" onclick="copyPostContent()" class="text-xs font-bold text-purple-600 hover:text-purple-800 flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-[14px]">content_copy</span>
                                Sao chép bài viết
                            </button>
                        </div>
                        <textarea id="res-noi-dung" rows="8" class="w-full text-xs leading-relaxed text-slate-800 p-3.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-purple-500 font-sans"></textarea>
                    </div>

                    {{-- 4. Thể lệ & Điều kiện --}}
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-purple-700 mb-1">Thể lệ & Điều kiện áp dụng</label>
                        <textarea id="res-the-le" rows="3" class="w-full text-xs text-slate-600 p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none"></textarea>
                    </div>

                    {{-- 5. Hashtags --}}
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-purple-700 mb-1">Hashtag tối ưu</label>
                        <p id="res-hashtags" class="text-xs font-mono text-purple-700 bg-purple-50 px-3 py-1.5 rounded-lg border border-purple-100"></p>
                    </div>

                    {{-- Action buttons --}}
                    <div class="pt-2 flex flex-wrap items-center gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="copyPostContent()" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">content_copy</span>
                            Sao chép bài viết
                        </button>

                        <button type="button" onclick="clearGeneratedPromo()" class="px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer" title="Xóa nội dung voucher vừa sinh ra">
                            <span class="material-symbols-outlined text-[16px]">delete_sweep</span>
                            <span>Xóa voucher vừa tạo</span>
                        </button>

                        <button type="button" onclick="savePromotionForVenue()" id="btn-save-promo"
                                class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer ml-auto"
                                style="background-color: #059669; color: #ffffff;">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            Lưu & Kích Hoạt Khuyến Mãi Cho Quán
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DANH SÁCH VOUCHER & KHUYẾN MÃI ĐÃ TẠO TRÊN HỆ THỐNG --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                    <h3 class="font-black text-slate-900 text-lg tracking-tight">Danh Sách Voucher & Khuyến Mãi Đã Tạo</h3>
                    <span id="admin-promo-count-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                        {{ $promotions->count() }} ưu đãi
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Quản lý, kích hoạt hoặc xóa các voucher ưu đãi đã được lưu cho các quán ăn trên Quán Mới.</p>
            </div>
            <div>
                <input type="text" id="admin-search-promo" onkeyup="filterAdminPromotions()" placeholder="🔍 Tìm mã voucher, tên quán..."
                       class="px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none w-full sm:w-64">
            </div>
        </div>

        <div id="admin-promo-list" class="space-y-3">
            @forelse($promotions as $promo)
                <div class="p-4 rounded-2xl border border-slate-200/80 hover:border-purple-300 bg-white shadow-2xs transition-all flex flex-col md:flex-row md:items-center justify-between gap-4"
                     id="admin-promo-card-{{ $promo->id }}"
                     data-search="{{ mb_strtolower(($promo->quan?->ten_quan ?? '') . ' ' . ($promo->ma_voucher ?? '') . ' ' . $promo->tieu_de) }}">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                                🏬 {{ $promo->quan?->ten_quan ?? 'Quán Mới' }}
                            </span>
                            @if($promo->ma_voucher)
                                <span class="px-2.5 py-1 rounded-lg font-mono font-black text-xs bg-purple-50 text-purple-700 border border-dashed border-purple-300 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">confirmation_number</span>
                                    {{ $promo->ma_voucher }}
                                </span>
                            @endif
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                {{ $promo->gia_tri_khuyen_mai ?: ($promo->gia_tri_giam ?: 'Ưu đãi') }}
                            </span>
                            <span id="admin-status-badge-{{ $promo->id }}" class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $promo->trang_thai ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                {{ $promo->trang_thai ? 'Đang hoạt động' : 'Tạm dừng' }}
                            </span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm truncate">{{ $promo->tieu_de }}</h4>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 mt-1">
                            <span>🍽️ Món: <strong>{{ $promo->mon_ap_dung ?: 'Toàn bộ menu' }}</strong></span>
                            @if($promo->ngay_bat_dau || $promo->ngay_ket_thuc)
                                <span>⏰ Hạn dùng: <strong>{{ $promo->ngay_bat_dau ? $promo->ngay_bat_dau->format('d/m/Y') : 'Từ nay' }} - {{ $promo->ngay_ket_thuc ? $promo->ngay_ket_thuc->format('d/m/Y') : 'Vô thời hạn' }}</strong></span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        {{-- Nút Bật/Tắt --}}
                        <button type="button" onclick="toggleAdminPromotion('{{ $promo->id }}')"
                                id="btn-admin-toggle-{{ $promo->id }}"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $promo->trang_thai ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $promo->trang_thai ? 'Bật' : 'Tắt' }}
                        </button>

                        {{-- Nút Xóa Voucher --}}
                        <button type="button" onclick="deleteAdminPromotion('{{ $promo->id }}', '{{ $promo->ma_voucher ?: $promo->tieu_de }}')"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 flex items-center gap-1 transition cursor-pointer shadow-2xs"
                                title="Xóa voucher này">
                            <span class="material-symbols-outlined text-[15px]">delete</span>
                            <span>Xóa</span>
                        </button>
                    </div>
                </div>
            @empty
                <div id="admin-promo-empty" class="py-10 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">confirmation_number</span>
                    Chưa có voucher nào được lưu trên hệ thống.<br>Hãy chọn quán bên trên và tạo voucher bằng Kira AI!
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    let currentVenueDishes = [];

    function onSelectVenue(select) {
        const option = select.options[select.selectedIndex];
        if (!option || !option.value) {
            currentVenueDishes = [];
            document.getElementById('promo-ten-quan').value = '';
            document.getElementById('promo-dia-chi').value = '';
            document.getElementById('venue-dishes-tags').innerHTML = '';
            document.getElementById('promo-mon-an').value = 'Toàn bộ menu';
            return;
        }

        const ten = option.getAttribute('data-ten') || '';
        const loai = option.getAttribute('data-loai') || 'Quán ăn';
        const diaChi = option.getAttribute('data-diachi') || '';
        const monAnJson = option.getAttribute('data-monan') || '[]';

        document.getElementById('promo-ten-quan').value = ten;
        document.getElementById('promo-loai-hinh').value = loai;
        document.getElementById('promo-dia-chi').value = diaChi;

        try {
            currentVenueDishes = JSON.parse(monAnJson);
        } catch(e) {
            currentVenueDishes = [];
        }

        const tagsContainer = document.getElementById('venue-dishes-tags');
        if (currentVenueDishes.length > 0) {
            let html = '<button type="button" onclick="selectDish(\'Toàn bộ menu\')" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 transition">Toàn bộ menu</button>';
            currentVenueDishes.forEach(dish => {
                html += `<button type="button" onclick="selectDish('${dish.replace(/'/g, "\\'")}')" class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 text-slate-700 hover:bg-purple-50 hover:text-purple-700 border border-slate-200 transition">${dish}</button>`;
            });
            tagsContainer.innerHTML = html;
        } else {
            tagsContainer.innerHTML = '';
            document.getElementById('promo-mon-an').value = 'Toàn bộ menu';
        }
    }

    function selectDish(dishName) {
        document.getElementById('promo-mon-an').value = dishName;
        const errorP = document.getElementById('venue-dishes-error');
        if (errorP) errorP.classList.add('hidden');
    }

    function getTodayString() {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        const d = String(now.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function validatePromoDates() {
        const startInput = document.getElementById('promo-ngay-bat-dau');
        const endInput = document.getElementById('promo-ngay-ket-thuc');
        const errorP = document.getElementById('promo-date-error');
        const errorText = document.getElementById('promo-date-error-text');

        const startVal = startInput?.value;
        const endVal = endInput?.value;
        const todayStr = getTodayString();

        let errorMsg = '';
        let invalidInput = null;

        if (startVal && startVal < todayStr) {
            errorMsg = 'Ngày bắt đầu áp dụng không được ở trong quá khứ (phải từ hôm nay trở đi)!';
            invalidInput = startInput;
        } else if (endVal && endVal < todayStr) {
            errorMsg = 'Ngày kết thúc áp dụng không được ở trong quá khứ (phải từ hôm nay trở đi)!';
            invalidInput = endInput;
        } else if (startVal && endVal && endVal < startVal) {
            errorMsg = 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu!';
            invalidInput = endInput;
        }

        startInput?.classList.remove('border-rose-500', 'bg-rose-50/40');
        endInput?.classList.remove('border-rose-500', 'bg-rose-50/40');

        if (errorMsg) {
            if (invalidInput) {
                invalidInput.classList.add('border-rose-500', 'bg-rose-50/40');
            }
            if (errorP && errorText) {
                errorText.innerText = errorMsg;
                errorP.classList.remove('hidden');
            }
            return { valid: false, message: errorMsg, input: invalidInput };
        } else {
            if (errorP) errorP.classList.add('hidden');
            if (startVal && endInput) {
                endInput.min = startVal > todayStr ? startVal : todayStr;
            }
            return { valid: true };
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const startInput = document.getElementById('promo-ngay-bat-dau');
        const endInput = document.getElementById('promo-ngay-ket-thuc');
        startInput?.addEventListener('change', validatePromoDates);
        startInput?.addEventListener('input', validatePromoDates);
        endInput?.addEventListener('change', validatePromoDates);
        endInput?.addEventListener('input', validatePromoDates);
    });

    function generateAiPromotion() {
        const tenQuan = document.getElementById('promo-ten-quan').value;
        const loaiHinh = document.getElementById('promo-loai-hinh').value;
        const diaChi = document.getElementById('promo-dia-chi').value;
        const monAn = document.getElementById('promo-mon-an').value;
        const giaTriGiam = document.getElementById('promo-gia-tri-giam').value;
        const loaiKm = document.getElementById('promo-loai-km').value;
        const dipKm = document.getElementById('promo-dip-km').value;
        const phongCach = document.getElementById('promo-phong-cach').value;
        const ngayBatDau = document.getElementById('promo-ngay-bat-dau').value;
        const ngayKetThuc = document.getElementById('promo-ngay-ket-thuc').value;
        const yeuCauThem = document.getElementById('promo-yeu-cau-them').value;
        const quanId = document.getElementById('promo-quan-id').value;

        // KIỂM TRA LỖI 1: Món ăn nhập vào có thuộc thực đơn quán đã chọn không
        const monAnInput = monAn.trim();
        const isAllMenu = !monAnInput || ['toàn bộ menu', 'toan bo menu', 'tất cả món', 'tat ca mon', 'toàn bộ', 'all', 'menu', 'tất cả'].includes(monAnInput.toLowerCase());

        if (currentVenueDishes.length > 0 && !isAllMenu) {
            const matched = currentVenueDishes.some(d =>
                d.toLowerCase().includes(monAnInput.toLowerCase()) ||
                monAnInput.toLowerCase().includes(d.toLowerCase())
            );

            if (!matched) {
                const sample = currentVenueDishes.slice(0, 4).join(', ');
                const errorP = document.getElementById('venue-dishes-error');
                const msg = `Món "${monAnInput}" không thuộc thực đơn của quán "${tenQuan || 'quán'}"! Thực đơn quán gồm: ${sample}... Vui lòng chọn món có trong menu hoặc "Toàn bộ menu".`;
                if (errorP) {
                    errorP.innerText = msg;
                    errorP.classList.remove('hidden');
                }
                alert(msg);
                document.getElementById('promo-mon-an').focus();
                return;
            }
        }

        // KIỂM TRA LỖI 2: Thời gian áp dụng không được ở trong quá khứ
        const dateCheck = validatePromoDates();
        if (!dateCheck.valid) {
            alert('⚠️ ' + dateCheck.message);
            if (dateCheck.input) dateCheck.input.focus();
            return;
        }

        const errorP = document.getElementById('venue-dishes-error');
        if (errorP) errorP.classList.add('hidden');

        const emptyState = document.getElementById('promo-empty-state');
        const loadingState = document.getElementById('promo-loading-state');
        const resultBox = document.getElementById('promo-result-box');
        const badge = document.getElementById('promo-result-badge');
        const btn = document.getElementById('btn-generate-promo');

        emptyState.classList.add('hidden');
        resultBox.classList.add('hidden');
        loadingState.classList.remove('hidden');
        btn.disabled = true;

        fetch('{{ route("admin.khuyen-mai-ai.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                quan_id: quanId,
                ten_quan: tenQuan,
                loai_hinh: loaiHinh,
                dia_chi: diaChi,
                mon_an: monAn,
                gia_tri_giam: giaTriGiam,
                loai_khuyen_mai: loaiKm,
                dip_khuyen_mai: dipKm,
                phong_cach: phongCach,
                ngay_bat_dau: ngayBatDau,
                ngay_ket_thuc: ngayKetThuc,
                yeu_cau_them: yeuCauThem
            })
        })
        .then(async res => {
            loadingState.classList.add('hidden');
            btn.disabled = false;

            const resData = await res.json();
            if (!res.ok || !resData.success) {
                const errorMsg = resData.message || (resData.errors ? Object.values(resData.errors).flat().join('\n') : 'Có lỗi xảy ra khi tạo nội dung!');
                alert(errorMsg);
                emptyState.classList.remove('hidden');
                return;
            }

            if (resData.success && resData.data) {
                const d = resData.data;
                document.getElementById('res-tieu-de').value = d.tieu_de || '';
                document.getElementById('res-ma-voucher').value = d.ma_voucher || '';
                document.getElementById('res-slogan').value = d.slogan || '';
                document.getElementById('res-noi-dung').value = d.noi_dung || '';
                document.getElementById('res-the-le').value = d.the_le || '';
                document.getElementById('res-hashtags').innerText = d.hashtags || '#QuanMoi #KhuyenMai';

                badge.classList.remove('hidden');
                if (d.source === 'kira_ai') {
                    badge.innerText = '⚡ Tạo bởi Kira AI (' + (d.model || 'kira-3.5-flash') + ')';
                    badge.className = 'text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200';
                } else {
                    const styleNames = {
                        'hao_hung': 'Hào hứng, sôi nổi 🔥',
                        'sang_trong': 'Sang trọng, tinh tế ✨',
                        'hai_huoc': 'Hài hước, Gen Z 😂',
                        'ngan_gon': 'Ngắn gọn, Flash Sale ⚡'
                    };
                    badge.innerText = '💡 Bộ Sinh AI Thông Minh (' + (styleNames[phongCach] || phongCach) + ')';
                    badge.className = 'text-[11px] font-bold px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200';
                }

                resultBox.classList.remove('hidden');
            } else {
                alert('Có lỗi xảy ra khi tạo nội dung!');
                emptyState.classList.remove('hidden');
            }
        })
        .catch(err => {
            loadingState.classList.add('hidden');
            btn.disabled = false;
            emptyState.classList.remove('hidden');
            alert('Lỗi kết nối tới hệ thống AI.');
        });
    }

    function copyVoucherCode() {
        const val = document.getElementById('res-ma-voucher').value;
        if (!val) return;
        navigator.clipboard.writeText(val).then(() => {
            alert('Đã sao chép mã voucher: ' + val);
        });
    }

    function copyPostContent() {
        const tieuDe = document.getElementById('res-tieu-de').value;
        const noiDung = document.getElementById('res-noi-dung').value;
        const theLe = document.getElementById('res-the-le').value;
        const hashtags = document.getElementById('res-hashtags').innerText;

        const fullText = tieuDe + "\n\n" + noiDung + "\n\n📋 THỂ LỆ & ĐIỀU KIỆN:\n" + theLe + "\n\n" + hashtags;

        navigator.clipboard.writeText(fullText).then(() => {
            alert('Đã sao chép toàn bộ bài đăng quảng bá vào bộ nhớ tạm!');
        });
    }

    function clearGeneratedPromo() {
        if (!confirm('Bạn có chắc chắn muốn xóa bản nháp voucher vừa tạo này không?')) return;
        document.getElementById('res-tieu-de').value = '';
        document.getElementById('res-ma-voucher').value = '';
        document.getElementById('res-slogan').value = '';
        document.getElementById('res-noi-dung').value = '';
        document.getElementById('res-the-le').value = '';
        document.getElementById('res-hashtags').innerText = '';
        document.getElementById('promo-result-box').classList.add('hidden');
        document.getElementById('promo-empty-state').classList.remove('hidden');
        document.getElementById('promo-result-badge')?.classList.add('hidden');
    }

    async function deleteAdminPromotion(id, title) {
        if (!confirm(`Bạn có chắc chắn muốn xóa vĩnh viễn voucher/ưu đãi "${title || 'này'}" không?`)) return;

        try {
            const res = await fetch(`/admin/khuyen-mai-ai/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                const card = document.getElementById(`admin-promo-card-${id}`);
                if (card) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => card.remove(), 300);
                }
                const badge = document.getElementById('admin-promo-count-badge');
                const headerCount = document.getElementById('header-promo-count');
                if (badge) {
                    const current = parseInt(badge.innerText) || 1;
                    const newCount = Math.max(0, current - 1);
                    badge.innerText = `${newCount} ưu đãi`;
                    if (headerCount) headerCount.innerText = newCount;
                }
                alert('✅ Đã xóa voucher thành công!');
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể xóa voucher'));
            }
        } catch (err) {
            alert('Lỗi kết nối khi xóa voucher.');
        }
    }

    async function toggleAdminPromotion(id) {
        try {
            const res = await fetch(`/admin/khuyen-mai-ai/${id}/toggle`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                const btn = document.getElementById(`btn-admin-toggle-${id}`);
                const badge = document.getElementById(`admin-status-badge-${id}`);
                if (data.trang_thai) {
                    if (btn) {
                        btn.className = 'px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-emerald-100 text-emerald-800 hover:bg-emerald-200';
                        btn.innerText = 'Bật';
                    }
                    if (badge) {
                        badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
                        badge.innerText = 'Đang hoạt động';
                    }
                } else {
                    if (btn) {
                        btn.className = 'px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200';
                        btn.innerText = 'Tắt';
                    }
                    if (badge) {
                        badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500';
                        badge.innerText = 'Tạm dừng';
                    }
                }
            } else {
                alert('Không thể thay đổi trạng thái.');
            }
        } catch (err) {
            alert('Lỗi kết nối.');
        }
    }

    function filterAdminPromotions() {
        const query = (document.getElementById('admin-search-promo')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('#admin-promo-list > div[data-search]');
        cards.forEach(card => {
            const searchData = card.getAttribute('data-search') || '';
            if (!query || searchData.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function savePromotionForVenue() {
        const quanId = document.getElementById('promo-quan-id').value;
        if (!quanId) {
            alert('Vui lòng chọn một quán cụ thể trong danh sách ở khung bên trái trước khi lưu!');
            return;
        }

        // KIỂM TRA LỖI: Thời gian áp dụng không được ở trong quá khứ
        const dateCheck = validatePromoDates();
        if (!dateCheck.valid) {
            alert('⚠️ ' + dateCheck.message);
            if (dateCheck.input) dateCheck.input.focus();
            return;
        }

        const data = {
            quan_id: quanId,
            tieu_de: document.getElementById('res-tieu-de').value,
            ma_voucher: document.getElementById('res-ma-voucher').value,
            loai_khuyen_mai: document.getElementById('promo-loai-km').value,
            gia_tri_giam: document.getElementById('promo-gia-tri-giam').value,
            slogan: document.getElementById('res-slogan').value,
            noi_dung: document.getElementById('res-noi-dung').value,
            the_le: document.getElementById('res-the-le').value,
            ngay_bat_dau: document.getElementById('promo-ngay-bat-dau').value,
            ngay_ket_thuc: document.getElementById('promo-ngay-ket-thuc').value,
            mon_ap_dung: document.getElementById('promo-mon-an').value,
        };

        const btn = document.getElementById('btn-save-promo');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">refresh</span> Đang lưu...';

        fetch('{{ route("admin.khuyen-mai-ai.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(async res => {
            const resData = await res.json();
            if (res.ok && resData.success) {
                alert('Thành công: ' + resData.message);
                setTimeout(() => window.location.reload(), 800);
            } else {
                const errorMsg = resData.message || (resData.errors ? Object.values(resData.errors).flat().join('\n') : 'Không thể lưu ưu đãi');
                alert('Lỗi: ' + errorMsg);
            }
        })
        .catch(() => alert('Lỗi máy chủ khi lưu ưu đãi.'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-[16px]">check_circle</span> Lưu & Kích Hoạt Khuyến Mãi Cho Quán';
            btn.style.backgroundColor = '#059669';
            btn.style.color = '#ffffff';
        });
    }
</script>
@endsection
