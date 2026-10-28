@extends('layouts.app')

@section('title', 'Tuyển Dụng - Cùng Quán Mới Kiến Tạo Tương Lai Ẩm Thực Số')

@push('styles')
    <meta name="description" content="Khám phá cơ hội nghề nghiệp tại Quán Mới. Gia nhập đội ngũ sáng tạo công nghệ và truyền thông ẩm thực hàng đầu với chế độ đãi ngộ hấp dẫn.">
    <meta name="keywords" content="tuyển dụng quán mới, việc làm review ẩm thực, tuyển lập trình viên laravel, content creator ẩm thực">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main class="pt-[72px] md:pt-[104px] pb-24 max-w-7xl mx-auto px-4 md:px-6 lg:px-10">
    <article>
        {{-- Hero Header --}}
        <header class="text-center max-w-3xl mx-auto py-12 md:py-20">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-secondary/10 text-secondary text-sm font-bold tracking-wide uppercase mb-6">
                <span class="material-symbols-outlined text-[18px]">work</span>
                Gia nhập gia đình Quán Mới
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-on-surface tracking-tight mb-6 leading-tight">
                Cùng chúng tôi kiến tạo <span class="text-primary">tương lai ẩm thực số</span>
            </h1>
            <p class="text-lg md:text-xl text-on-surface-variant leading-relaxed max-w-2xl mx-auto mb-10">
                Tại Quán Mới, mỗi ngày là một hành trình khám phá những hương vị độc đáo và ứng dụng công nghệ hiện đại nhất để kết nối thực khách với quán ngon địa phương.
            </p>
            <div class="flex justify-center">
                <a href="#vi-tri-tuyen-dung" class="inline-flex items-center gap-2 bg-primary text-white font-bold text-base md:text-lg px-8 py-4 rounded-full shadow-lg shadow-primary/25 hover:shadow-xl hover:scale-105 transition-all">
                    <span>Xem 5 vị trí đang mở tuyển</span>
                    <span class="material-symbols-outlined">arrow_downward</span>
                </a>
            </div>
        </header>

        {{-- Culture & Perks Grid --}}
        <section class="mb-24">
            <div class="text-center max-w-xl mx-auto mb-14">
                <h2 class="text-3xl font-extrabold text-on-surface">Vì sao bạn sẽ yêu thích làm việc tại đây?</h2>
                <p class="text-on-surface-variant text-base mt-2">Chúng tôi xây dựng môi trường tôn trọng sự khác biệt, đề cao tính tự chủ và chăm sóc sức khỏe của từng thành viên.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-surface-container p-8 rounded-3xl border border-outline-variant/30 hover:-translate-y-1 transition-transform">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-[26px]">restaurant</span>
                    </div>
                    <h3 class="text-xl font-bold text-on-surface mb-2">Food Tour Hàng Tháng</h3>
                    <p class="text-on-surface-variant text-sm leading-relaxed">
                        Đặc quyền của người nhà Quán Mới: Cùng nhau trải nghiệm các nhà hàng, quán ngon mới mở với ngân sách ẩm thực công ty tài trợ 100%.
                    </p>
                </div>

                <div class="bg-surface-container p-8 rounded-3xl border border-outline-variant/30 hover:-translate-y-1 transition-transform">
                    <div class="w-12 h-12 rounded-2xl bg-secondary/10 text-secondary flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-[26px]">payments</span>
                    </div>
                    <h3 class="text-xl font-bold text-on-surface mb-2">Đãi Ngộ Hấp Dẫn & Minh Bạch</h3>
                    <p class="text-on-surface-variant text-sm leading-relaxed">
                        Thu nhập cạnh tranh theo năng lực, thưởng hiệu quả kinh doanh, xét tăng lương định kỳ 2 lần/năm cùng bảo hiểm chăm sóc sức khỏe cao cấp.
                    </p>
                </div>

                <div class="bg-surface-container p-8 rounded-3xl border border-outline-variant/30 hover:-translate-y-1 transition-transform">
                    <div class="w-12 h-12 rounded-2xl bg-tertiary/10 text-tertiary flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-[26px]">devices</span>
                    </div>
                    <h3 class="text-xl font-bold text-on-surface mb-2">Linh Hoạt & Công Nghệ Hiện Đại</h3>
                    <p class="text-on-surface-variant text-sm leading-relaxed">
                        Chế độ làm việc Hybrid (văn phòng kết hợp làm từ xa), trang bị Macbook / PC cấu hình cao và tự do sáng tạo không gò bó.
                    </p>
                </div>
            </div>
        </section>

        {{-- Open Jobs List --}}
        <section id="vi-tri-tuyen-dung" class="mb-24">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-primary font-bold text-sm uppercase tracking-wider">Cơ hội nghề nghiệp</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-on-surface mt-1">Các vị trí đang tuyển dụng</h2>
                </div>
                <span class="text-sm text-on-surface-variant">Cập nhật mới nhất tháng 10/2026</span>
            </div>

            <div class="space-y-4">
                {{-- Job 1 --}}
                <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl border border-outline-variant/30 hover:shadow-xl hover:border-primary/50 transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-600 font-bold text-xs">HOT</span>
                            <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-medium text-xs">Content & Media</span>
                            <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-medium text-xs">Hà Nội & TP.HCM</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-on-surface">Food Content Creator & Video Reviewer (TikTok / Shorts)</h3>
                        <p class="text-on-surface-variant text-sm max-w-2xl">
                            Khám phá và trực tiếp review các địa điểm ăn uống, lên kịch bản video ngắn và xuất hiện trong các series ẩm thực triệu view của Quán Mới.
                        </p>
                    </div>
                    <div class="flex items-center gap-4 shrink-0">
                        <a href="mailto:tuyendung@quanmoi.com?subject=Ứng tuyển Food Content Creator" class="inline-flex items-center gap-1.5 bg-primary text-white font-bold text-sm px-6 py-3 rounded-full hover:bg-primary/90 transition-all">
                            <span>Ứng tuyển</span>
                            <span class="material-symbols-outlined text-[18px]">send</span>
                        </a>
                    </div>
                </div>

                {{-- Job 2 --}}
                <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl border border-outline-variant/30 hover:shadow-xl hover:border-primary/50 transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-full bg-primary/10 text-primary font-bold text-xs">Engineering</span>
                            <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-medium text-xs">TP.HCM / Hybrid</span>
                            <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-medium text-xs">Full-time</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-on-surface">Senior Fullstack Web Developer (Laravel & Tailwind)</h3>
                        <p class="text-on-surface-variant text-sm max-w-2xl">
                            Phát triển kiến trúc backend, tối ưu hóa công cụ tìm kiếm ElasticSearch, bản đồ GPS và các module tương tác thời gian thực phục vụ hàng trăm nghìn người dùng.
                        </p>
                    </div>
                    <div class="flex items-center gap-4 shrink-0">
                        <a href="mailto:tuyendung@quanmoi.com?subject=Ứng tuyển Senior Fullstack Developer" class="inline-flex items-center gap-1.5 bg-primary text-white font-bold text-sm px-6 py-3 rounded-full hover:bg-primary/90 transition-all">
                            <span>Ứng tuyển</span>
                            <span class="material-symbols-outlined text-[18px]">send</span>
                        </a>
                    </div>
                </div>

                {{-- Job 3 --}}
                <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl border border-outline-variant/30 hover:shadow-xl hover:border-primary/50 transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 font-bold text-xs">Kinh Doanh</span>
                            <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-medium text-xs">TP.HCM & Hà Nội</span>
                            <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-medium text-xs">Full-time</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-on-surface">Chuyên Viên Phát Triển Đối Tác Chủ Quán (Merchant Partnership)</h3>
                        <p class="text-on-surface-variant text-sm max-w-2xl">
                            Kết nối với các chủ quán cà phê, nhà hàng và chuỗi ẩm thực; tư vấn giải pháp số hóa thực đơn và tiếp thị thương hiệu trên hệ sinh thái Quán Mới.
                        </p>
                    </div>
                    <div class="flex items-center gap-4 shrink-0">
                        <a href="mailto:tuyendung@quanmoi.com?subject=Ứng tuyển Merchant Partnership" class="inline-flex items-center gap-1.5 bg-primary text-white font-bold text-sm px-6 py-3 rounded-full hover:bg-primary/90 transition-all">
                            <span>Ứng tuyển</span>
                            <span class="material-symbols-outlined text-[18px]">send</span>
                        </a>
                    </div>
                </div>

                {{-- Job 4 --}}
                <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl border border-outline-variant/30 hover:shadow-xl hover:border-primary/50 transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-600 font-bold text-xs">AI & Data</span>
                            <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-medium text-xs">Remote / Hybrid</span>
                            <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-medium text-xs">Full-time</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-on-surface">Kỹ Sư AI & Prompt Specialist (Kira AI Platform)</h3>
                        <p class="text-on-surface-variant text-sm max-w-2xl">
                            Phát triển trợ lý thông minh Kira AI, tối ưu hóa hệ thống gợi ý món ăn theo thời tiết, tâm trạng và địa điểm của thực khách.
                        </p>
                    </div>
                    <div class="flex items-center gap-4 shrink-0">
                        <a href="mailto:tuyendung@quanmoi.com?subject=Ứng tuyển Kỹ Sư AI" class="inline-flex items-center gap-1.5 bg-primary text-white font-bold text-sm px-6 py-3 rounded-full hover:bg-primary/90 transition-all">
                            <span>Ứng tuyển</span>
                            <span class="material-symbols-outlined text-[18px]">send</span>
                        </a>
                    </div>
                </div>

                {{-- Job 5 --}}
                <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl border border-outline-variant/30 hover:shadow-xl hover:border-primary/50 transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-medium text-xs">Vận Hành</span>
                            <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-medium text-xs">Hà Nội</span>
                            <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-medium text-xs">Full-time</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-on-surface">Chuyên Viên Chăm Sóc Khách Hàng & Kiểm Duyệt Nội Dung</h3>
                        <p class="text-on-surface-variant text-sm max-w-2xl">
                            Hỗ trợ giải đáp thắc mắc của thực khách và chủ quán qua Hotline/Email, kiểm duyệt nội dung đánh giá và duy trì cộng đồng minh bạch.
                        </p>
                    </div>
                    <div class="flex items-center gap-4 shrink-0">
                        <a href="mailto:tuyendung@quanmoi.com?subject=Ứng tuyển CSKH" class="inline-flex items-center gap-1.5 bg-primary text-white font-bold text-sm px-6 py-3 rounded-full hover:bg-primary/90 transition-all">
                            <span>Ứng tuyển</span>
                            <span class="material-symbols-outlined text-[18px]">send</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- How to Apply Box --}}
        <section class="bg-surface-container p-8 md:p-12 rounded-[2.5rem] border border-outline-variant/30 text-center max-w-3xl mx-auto">
            <span class="material-symbols-outlined text-4xl text-primary mb-3">mark_email_read</span>
            <h3 class="text-2xl md:text-3xl font-extrabold text-on-surface mb-3">Chưa tìm thấy vị trí phù hợp?</h3>
            <p class="text-on-surface-variant text-base leading-relaxed mb-6">
                Chúng tôi luôn chào đón các tài năng đam mê ẩm thực và công nghệ. Hãy gửi CV và thư giới thiệu bản thân về hòm thư tuyển dụng của Quán Mới.
            </p>
            <div class="inline-flex items-center gap-2 px-6 py-3 bg-white rounded-full font-mono font-bold text-primary shadow-sm">
                <span>tuyendung@quanmoi.com</span>
            </div>
        </section>
    </article>
</main>
@endsection
