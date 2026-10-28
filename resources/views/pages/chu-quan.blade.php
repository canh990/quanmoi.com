@extends('layouts.app')

@section('title', 'Dành cho Chủ Quán - Đồng hành phát triển ẩm thực cùng Quán Mới')

@push('styles')
    <meta name="description" content="Đưa nhà hàng, quán cà phê của bạn tiếp cận hàng triệu thực khách. Miễn phí số hóa menu, định vị GPS thông minh và trợ lý tiếp thị AI Kira độc quyền tại Quán Mới.">
    <meta name="keywords" content="đăng quán ăn, đối tác quán mới, quảng bá quán cà phê, số hóa thực đơn, phần mềm quán ăn">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main class="pt-[72px] md:pt-[104px] pb-24 max-w-7xl mx-auto px-4 md:px-6 lg:px-10">
    <article>
        {{-- Hero Section --}}
        <header class="text-center max-w-4xl mx-auto py-12 md:py-20 relative">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary text-sm font-bold tracking-wide uppercase mb-6">
                <span class="material-symbols-outlined text-[18px]">verified</span>
                Giải pháp đối tác ẩm thực toàn diện
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-on-surface tracking-tight mb-6 leading-tight">
                Tiếp cận hàng triệu thực khách, <span class="text-primary">bứt phá doanh thu</span> cùng Quán Mới
            </h1>
            <p class="text-lg md:text-xl text-on-surface-variant leading-relaxed max-w-2xl mx-auto mb-10">
                Nền tảng ẩm thực thế hệ mới kết nối trực tiếp quán ăn của bạn với cộng đồng thực khách địa phương. Hoàn toàn miễn phí, nhanh chóng và hiệu quả.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('chu-quan.dang-quan') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-primary to-primary-container text-white font-extrabold text-base md:text-lg px-8 py-4 rounded-full shadow-lg shadow-primary/30 hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-300">
                    <span class="material-symbols-outlined text-[22px]">add_business</span>
                    Đăng ký quán miễn phí ngay
                </a>
                <a href="#loi-ich" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-bold text-base md:text-lg px-8 py-4 rounded-full transition-all duration-300">
                    <span class="material-symbols-outlined text-[22px]">arrow_downward</span>
                    Tìm hiểu quyền lợi
                </a>
            </div>
        </header>

        {{-- Stats Highlight --}}
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-20">
            <div class="bg-surface-container p-6 rounded-3xl text-center border border-outline-variant/20 hover:border-primary/40 transition-all">
                <p class="text-3xl md:text-4xl font-black text-primary mb-1">50.000+</p>
                <p class="text-xs md:text-sm text-on-surface-variant font-medium">Lượt tìm quán mỗi tháng</p>
            </div>
            <div class="bg-surface-container p-6 rounded-3xl text-center border border-outline-variant/20 hover:border-primary/40 transition-all">
                <p class="text-3xl md:text-4xl font-black text-secondary mb-1">0 VNĐ</p>
                <p class="text-xs md:text-sm text-on-surface-variant font-medium">Chi phí khởi tạo gian hàng</p>
            </div>
            <div class="bg-surface-container p-6 rounded-3xl text-center border border-outline-variant/20 hover:border-primary/40 transition-all">
                <p class="text-3xl md:text-4xl font-black text-tertiary mb-1">3.5x</p>
                <p class="text-xs md:text-sm text-on-surface-variant font-medium">Tăng trưởng khách mới</p>
            </div>
            <div class="bg-surface-container p-6 rounded-3xl text-center border border-outline-variant/20 hover:border-primary/40 transition-all">
                <p class="text-3xl md:text-4xl font-black text-emerald-600 mb-1">4.9/5</p>
                <p class="text-xs md:text-sm text-on-surface-variant font-medium">Hài lòng từ các chủ quán</p>
            </div>
        </section>

        {{-- Core Benefits Grid --}}
        <section id="loi-ich" class="mb-24">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-extrabold text-on-surface tracking-tight mb-4">
                    Vì sao chủ quán chọn Quán Mới?
                </h2>
                <p class="text-on-surface-variant text-base md:text-lg">
                    Bộ công cụ mạnh mẽ và miễn phí giúp bạn quản lý hình ảnh thương hiệu, tối ưu menu và tiếp cận đúng đối tượng khách hàng tiềm năng.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Benefit 1 --}}
                <div class="bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/30 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-[30px]">restaurant_menu</span>
                        </div>
                        <h3 class="text-xl font-bold text-on-surface mb-3">Số hóa Menu trực quan</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed mb-4">
                            Đăng tải thực đơn kèm hình ảnh sắc nét, cập nhật giá linh hoạt và gắn link ShopeeFood để khách hàng chốt đơn trong nháy mắt.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-primary inline-flex items-center gap-1">Tối ưu đặt món & ghé quán</span>
                </div>

                {{-- Benefit 2 --}}
                <div class="bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/30 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 bg-secondary/10 text-secondary rounded-2xl flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-[30px]">auto_awesome</span>
                        </div>
                        <h3 class="text-xl font-bold text-on-surface mb-3">Trợ lý AI Kira Marketing</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed mb-4">
                            Không cần thuê agency đắt đỏ. AI độc quyền hỗ trợ viết bài quảng cáo, tạo mã voucher khuyến mãi và gợi ý chiến lược hút khách theo mùa.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-secondary inline-flex items-center gap-1">Công nghệ AI độc quyền</span>
                </div>

                {{-- Benefit 3 --}}
                <div class="bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/30 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 bg-tertiary/10 text-tertiary rounded-2xl flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-[30px]">my_location</span>
                        </div>
                        <h3 class="text-xl font-bold text-on-surface mb-3">Định vị GPS thông minh</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed mb-4">
                            Quán của bạn được ưu tiên xuất hiện khi thực khách tìm kiếm "quán gần đây" trên bản đồ ẩm thực trực quan với khoảng cách chính xác.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-tertiary inline-flex items-center gap-1">Quét thực khách quanh quán</span>
                </div>

                {{-- Benefit 4 --}}
                <div class="bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/30 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 bg-emerald-500/10 text-emerald-600 rounded-2xl flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-[30px]">reviews</span>
                        </div>
                        <h3 class="text-xl font-bold text-on-surface mb-3">Tương tác & Lắng nghe</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed mb-4">
                            Nhận thông báo khi có đánh giá mới, trực tiếp phản hồi bình luận khách hàng và khẳng định chất lượng uy tín phục vụ.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 inline-flex items-center gap-1">Xây dựng uy tín bền vững</span>
                </div>
            </div>
        </section>

        {{-- 3 Steps Process --}}
        <section class="bg-surface-container p-8 md:p-14 rounded-[2.5rem] mb-24 border border-outline-variant/20">
            <div class="text-center max-w-xl mx-auto mb-12">
                <span class="text-primary font-bold text-sm tracking-wider uppercase">Đơn giản & Nhanh chóng</span>
                <h2 class="text-3xl font-extrabold text-on-surface mt-2">Bắt đầu chỉ với 3 bước</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 relative">
                    <span class="w-10 h-10 rounded-full bg-primary text-white font-extrabold flex items-center justify-center text-lg mb-4">1</span>
                    <h3 class="text-lg font-bold text-on-surface mb-2">Đăng ký & Điền thông tin</h3>
                    <p class="text-on-surface-variant text-sm leading-relaxed">
                        Cung cấp tên quán, địa chỉ, số điện thoại, khung giờ hoạt động và hình ảnh không gian của bạn.
                    </p>
                </div>
                <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 relative">
                    <span class="w-10 h-10 rounded-full bg-primary text-white font-extrabold flex items-center justify-center text-lg mb-4">2</span>
                    <h3 class="text-lg font-bold text-on-surface mb-2">Ban quản trị xét duyệt</h3>
                    <p class="text-on-surface-variant text-sm leading-relaxed">
                        Đội ngũ kiểm duyệt Quán Mới xác thực thông tin và kích hoạt trang quán của bạn trong vòng tối đa 24 giờ.
                    </p>
                </div>
                <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 relative">
                    <span class="w-10 h-10 rounded-full bg-primary text-white font-extrabold flex items-center justify-center text-lg mb-4">3</span>
                    <h3 class="text-lg font-bold text-on-surface mb-2">Tiếp cận hàng nghìn khách</h3>
                    <p class="text-on-surface-variant text-sm leading-relaxed">
                        Quán xuất hiện trên bản đồ ẩm thực, danh mục khám phá và sẵn sàng chào đón những thực khách đầu tiên.
                    </p>
                </div>
            </div>
        </section>

        {{-- Partner FAQ Section --}}
        <section class="max-w-3xl mx-auto mb-24">
            <h2 class="text-3xl font-bold text-on-surface text-center mb-10">Câu hỏi thường gặp của chủ quán</h2>
            <div class="space-y-4">
                <details class="group bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 transition-all open:shadow-md">
                    <summary class="font-bold text-base md:text-lg text-on-surface cursor-pointer flex items-center justify-between list-none">
                        <span>Đăng ký quán trên Quán Mới có mất phí duy trì không?</span>
                        <span class="material-symbols-outlined text-primary group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-on-surface-variant text-sm md:text-base mt-4 leading-relaxed">
                        Hoàn toàn <strong>KHÔNG</strong>. Quán Mới hỗ trợ các chủ quán tạo hồ sơ quán, đăng tải thông tin, hình ảnh và số hóa menu hoàn toàn miễn phí trọn đời nhằm xây dựng cộng đồng ẩm thực văn minh, chất lượng.
                    </p>
                </details>

                <details class="group bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 transition-all open:shadow-md">
                    <summary class="font-bold text-base md:text-lg text-on-surface cursor-pointer flex items-center justify-between list-none">
                        <span>Làm sao để quán của tôi được gắn tích xanh xác thực (Verified)?</span>
                        <span class="material-symbols-outlined text-primary group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-on-surface-variant text-sm md:text-base mt-4 leading-relaxed">
                        Sau khi quán được tạo, bạn có thể gửi yêu cầu xác thực bằng cách cung cấp giấy chứng nhận ĐKKD hoặc ảnh chụp mặt bằng quán thực tế. Đội ngũ kiểm duyệt sẽ xác minh để cấp huy hiệu xác thực, tăng độ uy tín lên gấp nhiều lần.
                    </p>
                </details>

                <details class="group bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 transition-all open:shadow-md">
                    <summary class="font-bold text-base md:text-lg text-on-surface cursor-pointer flex items-center justify-between list-none">
                        <span>Tôi có thể tự chỉnh sửa thực đơn và giá món ăn không?</span>
                        <span class="material-symbols-outlined text-primary group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-on-surface-variant text-sm md:text-base mt-4 leading-relaxed">
                        Có. Chủ quán có toàn quyền truy cập trang quản trị quán của mình để thêm món mới, cập nhật giá bán, bật/tắt trạng thái còn món hoặc thêm khuyến mãi bất kỳ lúc nào.
                    </p>
                </details>
            </div>
        </section>

        {{-- Final CTA Box --}}
        <section class="bg-gradient-to-br from-primary via-[#b3531b] to-primary-container text-white p-8 md:p-14 rounded-[2.5rem] shadow-xl text-center relative overflow-hidden">
            <div class="relative z-10 max-w-2xl mx-auto space-y-6">
                <h3 class="text-3xl md:text-4xl font-black tracking-tight leading-tight">
                    Sẵn sàng biến quán ăn của bạn thành điểm đến yêu thích tiếp theo?
                </h3>
                <p class="text-white/90 text-base md:text-lg leading-relaxed">
                    Đừng để thực khách bỏ lỡ những món ngon đậm đà của bạn. Bắt đầu ngay hôm nay chỉ với vài thao tác đơn giản.
                </p>
                <div class="pt-4 flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('chu-quan.dang-quan') }}" class="bg-white text-primary font-black text-lg px-8 py-4 rounded-full hover:bg-white/90 transition-all shadow-lg hover:scale-105 active:scale-95">
                        Đăng quán ngay bây giờ
                    </a>
                    <a href="{{ route('pages.lien-he') }}" class="bg-white/20 border border-white/40 text-white font-bold text-lg px-8 py-4 rounded-full hover:bg-white/30 transition-all">
                        Liên hệ tư vấn viên
                    </a>
                </div>
            </div>
        </section>
    </article>
</main>
@endsection
