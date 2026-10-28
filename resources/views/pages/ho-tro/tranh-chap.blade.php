@extends('layouts.app')

@section('title', 'Cơ Chế Giải Quyết Tranh Chấp & Khiếu Nại | Quán Mới')

@push('styles')
    <meta name="description" content="Quy trình tiếp nhận và giải quyết khiếu nại, tranh chấp giữa người tiêu dùng và cơ sở kinh doanh ẩm thực trên nền tảng Quán Mới. Công bằng, minh bạch, đúng pháp luật.">
    <meta name="keywords" content="giải quyết tranh chấp quán mới, khiếu nại quán ăn, bảo vệ người tiêu dùng ẩm thực">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main class="pt-[72px] md:pt-[104px] pb-24 max-w-5xl mx-auto px-4 md:px-6 lg:px-8">
    <article class="space-y-12">
        {{-- Header --}}
        <header class="text-center max-w-3xl mx-auto py-10 md:py-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary text-sm font-bold tracking-wide uppercase mb-6">
                <span class="material-symbols-outlined text-[18px]">gavel</span>
                Bảo vệ quyền lợi người dùng & chủ quán
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-on-surface tracking-tight mb-6 leading-tight">
                Cơ chế giải quyết khiếu nại & <span class="text-primary">tranh chấp</span>
            </h1>
            <p class="text-base md:text-lg text-on-surface-variant leading-relaxed">
                Quán Mới cam kết xây dựng một môi trường ẩm thực văn minh, nơi quyền lợi của thực khách và các chủ quán luôn được lắng nghe, bảo vệ công bằng và minh bạch.
            </p>
        </header>

        {{-- Principles Section --}}
        <section class="bg-surface-container p-8 md:p-10 rounded-3xl border border-outline-variant/30">
            <h2 class="text-2xl font-bold text-on-surface mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-[28px]">balance</span>
                1. Nguyên tắc giải quyết tranh chấp
            </h2>
            <div class="space-y-4 text-on-surface-variant text-base leading-relaxed">
                <p>
                    <strong>1.1. Thượng tôn pháp luật:</strong> Mọi tranh chấp phát sinh giữa người dùng (thực khách) và bên cung cấp dịch vụ ẩm thực (chủ quán) đều được xử lý dựa trên các quy định pháp luật hiện hành của Việt Nam, đặc biệt là Luật Bảo vệ quyền lợi người tiêu dùng và các nghị định về thương mại điện tử.
                </p>
                <p>
                    <strong>1.2. Ưu tiên đàm phán & hòa giải:</strong> Quán Mới luôn khuyến khích phương thức tự hòa giải và thương lượng thiện chí giữa các bên nhằm đạt được giải pháp thấu đáo, giữ gìn uy tín kinh doanh và trải nghiệm ẩm thực tốt đẹp.
                </p>
                <p>
                    <strong>1.3. Căn cứ minh bạch:</strong> Quá trình xử lý khiếu nại dựa trên các chứng cứ xác thực như hóa đơn thanh toán, hình ảnh/video thực tế tại quán, dữ liệu đặt bàn hoặc đoạn ghi âm/tin nhắn trao đổi.
                </p>
            </div>
        </section>

        {{-- 4 Steps Process Section --}}
        <section class="bg-white p-8 md:p-12 rounded-[2.5rem] border border-outline-variant/30 shadow-sm">
            <h2 class="text-2xl font-bold text-on-surface mb-8 text-center">
                2. Quy trình 4 bước xử lý khiếu nại tại Quán Mới
            </h2>

            <div class="space-y-8">
                {{-- Step 1 --}}
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <div class="w-12 h-12 rounded-2xl bg-primary text-white font-black text-xl flex items-center justify-center shrink-0">
                        1
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-on-surface mb-2">Bước 1: Tiếp nhận thông tin khiếu nại</h3>
                        <p class="text-on-surface-variant text-sm md:text-base leading-relaxed">
                            Khách hàng hoặc chủ quán phát sinh tranh chấp gửi thông tin phản ánh qua email <strong>khieunai@quanmoi.com</strong> hoặc hotline <strong>1900 1234</strong>. Thông tin cần có: Tên quán, thời gian xảy ra sự việc, nội dung sự việc và bằng chứng kèm theo (ảnh chụp, video, hóa đơn...).
                        </p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <div class="w-12 h-12 rounded-2xl bg-secondary text-white font-black text-xl flex items-center justify-center shrink-0">
                        2
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-on-surface mb-2">Bước 2: Xác minh và yêu cầu giải trình</h3>
                        <p class="text-on-surface-variant text-sm md:text-base leading-relaxed">
                            Trong vòng <strong>24 giờ làm việc</strong> kể từ lúc nhận khiếu nại, bộ phận Pháp chế & Kiểm duyệt của Quán Mới sẽ liên hệ với bên bị khiếu nại, yêu cầu phản hồi và cung cấp tài liệu giải trình đối ứng.
                        </p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <div class="w-12 h-12 rounded-2xl bg-tertiary text-white font-black text-xl flex items-center justify-center shrink-0">
                        3
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-on-surface mb-2">Bước 3: Trung gian hòa giải</h3>
                        <p class="text-on-surface-variant text-sm md:text-base leading-relaxed">
                            Quán Mới đóng vai trò cầu nối độc lập, hỗ trợ hai bên thương lượng phương án khắc phục (ví dụ: đổi trả món, hoàn tiền, xin lỗi công khai, đính chính đánh giá sai lệch...).
                        </p>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white font-black text-xl flex items-center justify-center shrink-0">
                        4
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-on-surface mb-2">Bước 4: Kết luận & Chế tài xử lý</h3>
                        <p class="text-on-surface-variant text-sm md:text-base leading-relaxed">
                            Nếu bên bị khiếu nại có hành vi cố tình vi phạm (như lừa đảo, mất an toàn thực phẩm nghiêm trọng, xúc phạm khách hàng), Quán Mới sẽ áp dụng chế tài: tạm ngưng hiển thị quán, xóa tài khoản vĩnh viễn và phối hợp cơ quan chức năng nếu vụ việc vượt quá thẩm quyền.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contact Intake Section --}}
        <section class="bg-surface-container p-8 md:p-10 rounded-3xl border border-outline-variant/30 text-center">
            <h2 class="text-xl font-bold text-on-surface mb-3">Đầu mối tiếp nhận khiếu nại chính thức</h2>
            <p class="text-sm text-on-surface-variant mb-6 max-w-xl mx-auto">
                Mọi vấn đề cần phản ánh, khiếu nại vui lòng gửi về Ban Giải Quyết Tranh Chấp - Sàn thông tin ẩm thực Quán Mới.
            </p>
            <div class="inline-flex flex-wrap justify-center gap-4 text-sm font-semibold text-on-surface">
                <span class="px-4 py-2 rounded-full bg-white border border-outline-variant/40">Hotline: 1900 1234</span>
                <span class="px-4 py-2 rounded-full bg-white border border-outline-variant/40">Email: khieunai@quanmoi.com</span>
                <span class="px-4 py-2 rounded-full bg-white border border-outline-variant/40">Địa chỉ: Tầng 6, Innovation Tower, Q.1, TP.HCM</span>
            </div>
        </section>
    </article>
</main>
@endsection
