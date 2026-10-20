@extends('admin.layout')

@section('title', 'Kira AI Marketing Studio - Quán Mới Admin')
@section('page-title', 'Trung Tâm AI Tiếp Thị & Khuyến Mãi')

@section('content')
<div class="space-y-8">
    {{-- Top Hero Banner: Thiết kế đồng bộ, viền bao quanh đều đặn, sạch đẹp --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-3xl">
                {{-- Badges thông tin hệ thống --}}
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs"
                          style="background-color: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe;">
                        <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: #10b981;"></span>
                        Kira AI Powered
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-2xs"
                          style="background-color: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;">
                        Tương thích OpenAI API v1
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-2xs"
                          style="background-color: #f8fafc; color: #334155; border: 1px solid #e2e8f0;">
                        Tổng ưu đãi: <strong class="text-slate-900 font-bold">{{ $promotionsCount }}</strong>
                    </span>
                </div>

                {{-- Tiêu đề & Giới thiệu sắc nét, cực kỳ dễ đọc --}}
                <div class="flex items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-md shadow-purple-500/20 shrink-0 mt-0.5"
                         style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); background-color: #7c3aed; color: #ffffff;">
                        <span class="material-symbols-outlined" style="font-size: 28px; color: #ffffff; line-height: 1;">auto_awesome</span>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                            Kira AI Studio Tiếp Thị & Khuyến Mãi
                        </h1>
                        <p class="text-slate-600 text-xs sm:text-sm mt-1.5 leading-relaxed font-normal">
                            Trợ lý thông minh tự động sáng tạo chiến dịch ưu đãi, bài viết mạng xã hội cuốn hút, mã voucher và thể lệ chuẩn ngành F&B chỉ trong vài giây.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Thẻ thông số nổi bật bên phải --}}
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 shrink-0">
                <div class="px-4 py-3 rounded-2xl border border-slate-200/80 bg-slate-50/80 flex items-center gap-3 shadow-2xs">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background-color: #ede9fe; color: #6d28d9;">
                        <span class="material-symbols-outlined text-[20px]" style="font-size: 20px; color: #6d28d9;">bolt</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tốc độ sinh AI</p>
                        <p class="text-xs font-black text-slate-800">Tức thì &lt; 2s</p>
                    </div>
                </div>
                <div class="px-4 py-3 rounded-2xl border border-slate-200/80 bg-slate-50/80 flex items-center gap-3 shadow-2xs">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background-color: #ecfdf5; color: #047857;">
                        <span class="material-symbols-outlined text-[20px]" style="font-size: 20px; color: #047857;">share</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kênh tối ưu</p>
                        <p class="text-xs font-black text-slate-800">FB • Insta • TikTok</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Input ẩn API Key Kira AI từ .env --}}
    <input type="hidden" id="kira-api-key-input" value="{{ config('ai.kira.api_key') }}">

    {{-- THÔNG BÁO TÁCH MODULE RIÊNG --}}
    <div class="bg-gradient-to-r from-amber-500/10 via-purple-500/10 to-indigo-500/10 rounded-2xl border border-amber-300/80 p-5 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center font-black shadow-md shadow-amber-500/20 shrink-0">
                <span class="material-symbols-outlined text-[24px]">campaign</span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-amber-200 text-amber-900 border border-amber-300">Phân hệ mới</span>
                    <h2 class="text-sm font-bold text-slate-900">Chức năng Tạo Khuyến Mãi & Voucher AI đã được tách riêng</h2>
                </div>
                <p class="text-xs text-slate-600 mt-1">
                    Đã chuyển thành module độc lập đặt tại <strong class="text-slate-900">Vận Hành & Phân Quyền &gt; Khuyến Mãi & Voucher AI</strong> với quyền hạn phân bổ riêng (<code class="bg-white px-1.5 py-0.5 rounded text-indigo-700 font-mono text-[11px] border border-slate-200">quan_ly_khuyen_mai_ai</code>).
                </p>
            </div>
        </div>
        <a href="{{ route('admin.khuyen-mai-ai.index') }}" class="shrink-0 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-purple-600/20 flex items-center gap-2 transition-all">
            <span>Đến Studio Khuyến Mãi AI</span>
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
        </a>
    </div>

    {{-- PHẦN CHÍNH: BỘ TẠO KHUYẾN MÃI KIRA AI --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4"
             style="background: linear-gradient(to right, #faf5ff 0%, #ffffff 100%);">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-md shadow-purple-500/25 shrink-0"
                     style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); background-color: #7c3aed; color: #ffffff;">
                    <span class="material-symbols-outlined" style="font-size: 24px; color: #ffffff; line-height: 1;">campaign</span>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        Trình Tạo Nội Dung Khuyến Mãi AI
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Tùy biến món ăn, mức ưu đãi, dịp lễ và phong cách để AI xuất bản chiến dịch hoàn hảo.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-500">Được tối ưu cho:</span>
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">Facebook</span>
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background-color: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8;">Instagram</span>
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background-color: #f8fafc; color: #334155; border: 1px solid #e2e8f0;">TikTok & Zalo</span>
            </div>
        </div>

        <div class="p-6 lg:p-8 grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Cột trái: Form nhập liệu --}}
            <div class="lg:col-span-5 space-y-4">
                {{-- Chọn quán --}}
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-purple-600">storefront</span>
                        Chọn quán trên hệ thống (hoặc nhập tự do)
                    </label>
                    <select id="promo-quan-id" onchange="handleSelectQuan(this)" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white">
                        <option value="">-- Nhập thông tin tùy biến --</option>
                        @foreach($danhSachQuan as $q)
                            <option value="{{ $q->id }}"
                                    data-ten="{{ $q->ten_quan }}"
                                    data-loai="{{ $q->loai_hinh_kinh_doanh }}"
                                    data-diachi="{{ $q->dia_chi_chi_tiet }}, {{ $q->ten_quan_huyen }}, {{ $q->ten_tinh_thanh }}"
                                    data-monan="{{ json_encode($q->danh_sach_mon ?? []) }}">
                                {{ $q->ten_quan }} ({{ $q->loai_hinh_kinh_doanh ?? 'Quán ăn' }} - {{ $q->ten_quan_huyen }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tên quán & Loại hình --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Tên quán ăn / Thương hiệu</label>
                        <input type="text" id="promo-ten-quan" placeholder="VD: Lẩu Bò Sài Gòn Phố" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1">Loại hình kinh doanh</label>
                        <input type="text" id="promo-loai-hinh" placeholder="VD: Quán Lẩu, Cafe, Quán nướng" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                    </div>
                </div>

                {{-- Địa chỉ quán --}}
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1">Địa chỉ quán</label>
                    <input type="text" id="promo-dia-chi" placeholder="VD: 123 Nguyễn Thị Minh Khai, Quận 1, TP.HCM" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                </div>

                {{-- Món áp dụng & Mức giảm --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px] text-amber-500">restaurant</span>
                                Món áp dụng ưu đãi
                            </span>
                            <span id="venue-menu-count" class="text-[10px] text-purple-600 font-semibold hidden"></span>
                        </label>
                        <input type="text" id="promo-mon-an" list="danh-sach-mon-datalist" placeholder="Chọn món trong menu quán hoặc 'Toàn bộ menu'" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                        <datalist id="danh-sach-mon-datalist">
                            <option value="Toàn bộ menu">
                        </datalist>
                        <div id="venue-dishes-tags" class="mt-1.5 flex flex-wrap gap-1 items-center hidden"></div>
                        <p id="venue-dishes-error" class="mt-1 text-[11px] text-rose-600 font-bold hidden"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-rose-500">percent</span>
                            Mức giảm / Quà tặng
                        </label>
                        <input type="text" id="promo-gia-tri-giam" placeholder="VD: Giảm 20% hoặc 50.000đ" value="20%" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                    </div>
                </div>

                {{-- Hình thức & Dịp khuyến mãi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Hình thức ưu đãi</label>
                        <select id="promo-loai-km" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white">
                            <option value="giam_phan_tram">Giảm theo % (VD: 20%, 30%)</option>
                            <option value="giam_tien">Giảm tiền mặt trực tiếp</option>
                            <option value="mua_1_tang_1">Mua 1 Tặng 1</option>
                            <option value="tang_mon">Tặng đồ uống / Tráng miệng</option>
                            <option value="combo">Combo giá sốc giờ vàng</option>
                            <option value="voucher">Voucher tri ân khách hàng</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Dịp khuyến mãi</label>
                        <input type="text" id="promo-dip-km" placeholder="VD: Khai trương, Cuối tuần, Sinh nhật quán" value="Khai trương cơ sở mới" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none">
                    </div>
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
                            Sao chép toàn bộ bài đăng
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

    {{-- CÁC CÔNG CỤ PHỤ (SEO Writer & Tóm tắt đánh giá) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Tool 1: SEO Writer --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <h3 class="font-bold text-slate-900 text-base mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">auto_fix_high</span>
                Tạo Thẻ Mô Tả Chuẩn SEO (Meta Description)
            </h3>
            <p class="text-xs text-slate-500 mb-4">Nhập tên quán hoặc chủ đề để AI tự động sinh thẻ meta description tối ưu thứ hạng Google.</p>

            <form id="ai-seo-form" class="space-y-3">
                <textarea id="ai-prompt" rows="3" placeholder="Nhập tên quán, khu vực hoặc từ khóa... (ví dụ: Bánh Mì Chảo Bờ Hồ chuẩn vị Hà Nội)" required class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none"></textarea>
                <button type="button" onclick="generateAiSeo()" class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer"
                        style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); background-color: #7c3aed; color: #ffffff;">
                    <span class="material-symbols-outlined text-[18px]">smart_toy</span>
                    Tạo Thẻ Meta SEO
                </button>
            </form>

            <div id="ai-result-box" class="mt-4 p-4 rounded-xl bg-purple-50 border border-purple-200 text-purple-900 text-xs hidden">
                <p class="font-bold text-purple-700 mb-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span> Kết quả gợi ý từ AI:
                </p>
                <div id="ai-result-content" class="leading-relaxed"></div>
            </div>
        </div>

        {{-- Tool 2: Content Optimization --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <h3 class="font-bold text-slate-900 text-base mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">summarize</span>
                Tóm Tắt Đánh Giá Quán
            </h3>
            <p class="text-xs text-slate-500 mb-4">Tổng hợp tự động các phản hồi của thực khách để đưa ra đánh giá tổng quan về chất lượng món ăn và dịch vụ.</p>
            <div class="p-6 text-center text-xs text-slate-500 bg-slate-50 rounded-xl border border-dashed border-slate-200 flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-3xl text-indigo-400">psychology</span>
                <p>Chọn quán từ danh sách trên để xem tổng hợp phân tích điểm khen - chê từ khách hàng một cách thông minh.</p>
            </div>
        </div>
    </div>
</div>

<script>
    let currentVenueDishes = [];

    function handleSelectQuan(select) {
        const selected = select.options[select.selectedIndex];
        if (!selected.value) {
            currentVenueDishes = [];
            updateDishesDatalist([]);
            return;
        }

        document.getElementById('promo-ten-quan').value = selected.getAttribute('data-ten') || '';
        document.getElementById('promo-loai-hinh').value = selected.getAttribute('data-loai') || '';
        document.getElementById('promo-dia-chi').value = selected.getAttribute('data-diachi') || '';

        try {
            currentVenueDishes = JSON.parse(selected.getAttribute('data-monan') || '[]');
        } catch(e) {
            currentVenueDishes = [];
        }
        updateDishesDatalist(currentVenueDishes);
    }

    function updateDishesDatalist(dishes) {
        const datalist = document.getElementById('danh-sach-mon-datalist');
        const tagsContainer = document.getElementById('venue-dishes-tags');
        const countSpan = document.getElementById('venue-menu-count');
        const errorP = document.getElementById('venue-dishes-error');
        if (errorP) errorP.classList.add('hidden');

        datalist.innerHTML = '<option value="Toàn bộ menu">';
        if (dishes && dishes.length > 0) {
            if (countSpan) {
                countSpan.innerText = `(Menu có ${dishes.length} món)`;
                countSpan.classList.remove('hidden');
            }
            dishes.forEach(d => {
                datalist.innerHTML += `<option value="${d}">`;
            });

            // Hiển thị các nút gợi ý chọn nhanh món của quán
            let tagsHtml = '<span class="text-[10px] text-slate-400 font-medium">Chọn nhanh:</span>';
            tagsHtml += `<button type="button" onclick="selectDish('Toàn bộ menu')" class="px-2 py-0.5 text-[10px] bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold rounded-md border border-purple-200 transition cursor-pointer">Toàn bộ menu</button>`;
            dishes.slice(0, 3).forEach(d => {
                const escaped = d.replace(/"/g, '&quot;').replace(/'/g, "\\'");
                tagsHtml += `<button type="button" onclick="selectDish('${escaped}')" class="px-2 py-0.5 text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-md border border-slate-200 transition cursor-pointer">${d}</button>`;
            });
            if (dishes.length > 3) {
                tagsHtml += `<span class="text-[10px] text-slate-400 italic">(+${dishes.length - 3} món khác)</span>`;
            }
            tagsContainer.innerHTML = tagsHtml;
            tagsContainer.classList.remove('hidden');

            const monAnInput = document.getElementById('promo-mon-an');
            if (!monAnInput.value || monAnInput.value === 'Combo Lẩu Bò Đặc Biệt (hoặc Toàn bộ menu)') {
                monAnInput.value = dishes[0] || 'Toàn bộ menu';
            }
        } else {
            if (countSpan) countSpan.classList.add('hidden');
            tagsContainer.classList.add('hidden');
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
        const apiKeyInput = document.getElementById('kira-api-key-input');
        const apiKey = apiKeyInput ? apiKeyInput.value : '';
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

        fetch('{{ route("admin.ai-tools.generate-promotion") }}', {
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
                yeu_cau_them: yeuCauThem,
                api_key: apiKey,
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
            const res = await fetch(`/admin/ai-tools/promotions/${id}`, {
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
                if (badge) {
                    const current = parseInt(badge.innerText) || 1;
                    const newCount = Math.max(0, current - 1);
                    badge.innerText = `${newCount} ưu đãi`;
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
            const res = await fetch(`/admin/ai-tools/promotions/${id}/toggle`, {
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

        fetch('{{ route("admin.ai-tools.save-promotion") }}', {
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

    function generateAiSeo() {
        const prompt = document.getElementById('ai-prompt').value;
        if (!prompt) return alert('Vui lòng nhập từ khóa hoặc chủ đề!');

        const resultBox = document.getElementById('ai-result-box');
        const resultContent = document.getElementById('ai-result-content');

        resultBox.classList.remove('hidden');
        resultContent.innerText = 'Đang suy nghĩ và tạo nội dung...';

        fetch('{{ route("admin.ai-tools.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ prompt: prompt })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                resultContent.innerText = data.result;
            } else {
                resultContent.innerText = 'Đã có lỗi xảy ra.';
            }
        })
        .catch(() => {
            resultContent.innerText = 'Lỗi kết nối tới máy chủ AI.';
        });
    }
</script>
@endsection
