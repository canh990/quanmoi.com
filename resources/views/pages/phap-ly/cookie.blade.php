@extends('layouts.app')

@section('title', 'Chính Sách Cookie - Trải Nghiệm Duyệt Web Tối Ưu | Quán Mới')

@push('styles')
    <meta name="description" content="Chính sách sử dụng cookie tại Quán Mới. Tìm hiểu mục đích của cookie và cách tùy chỉnh cài đặt cookie trên trình duyệt của bạn.">
    <meta name="keywords" content="chính sách cookie quán mới, cài đặt cookie trình duyệt, bảo vệ riêng tư duyệt web">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main class="pt-[72px] md:pt-[104px] pb-24 max-w-5xl mx-auto px-4 md:px-6 lg:px-8">
    <article class="space-y-10">
        {{-- Header --}}
        <header class="text-center max-w-3xl mx-auto py-10 md:py-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary text-sm font-bold tracking-wide uppercase mb-6">
                <span class="material-symbols-outlined text-[18px]">cookie</span>
                Minh bạch công nghệ & Trải nghiệm
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-on-surface tracking-tight mb-4 leading-tight">
                Chính sách sử dụng <span class="text-primary">Cookie</span>
            </h1>
            <p class="text-sm md:text-base text-on-surface-variant">
                Quán Mới sử dụng cookie và các công nghệ lưu trữ tương tự nhằm đem lại trải nghiệm lướt web mượt mà, tiện lợi và an toàn nhất.
            </p>
        </header>

        {{-- Content Box --}}
        <div class="bg-white p-8 md:p-12 rounded-[2.5rem] border border-outline-variant/30 shadow-sm space-y-8 text-on-surface-variant text-base leading-relaxed">
            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">1.</span> Cookie là gì?
                </h2>
                <p>
                    Cookie là những tệp dữ liệu văn bản nhỏ được lưu trữ trên thiết bị của bạn (máy tính, điện thoại) khi bạn truy cập một website. Cookie giúp website ghi nhớ các tùy chọn cá nhân và trạng thái đăng nhập để bạn không phải nhập lại mỗi lần quay trở lại.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">2.</span> Các loại Cookie được sử dụng tại Quán Mới
                </h2>
                <div class="space-y-4">
                    <div class="p-5 rounded-2xl bg-surface-container border border-outline-variant/20">
                        <h3 class="font-bold text-on-surface text-base mb-1">Cookie Cần thiết (Bắt buộc)</h3>
                        <p class="text-sm">
                            Đây là các cookie cốt lõi để website hoạt động ổn định, ví dụ như duy trì phiên đăng nhập của người dùng, xác thực token chống tấn công giả mạo yêu cầu (CSRF token) và giỏ hàng/yêu cầu lưu quán. Nếu không có các cookie này, hệ thống không thể hoạt động bình thường.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-surface-container border border-outline-variant/20">
                        <h3 class="font-bold text-on-surface text-base mb-1">Cookie Chức năng (Tùy chọn trải nghiệm)</h3>
                        <p class="text-sm">
                            Cho phép website ghi nhớ các lựa chọn của bạn như: khu vực tỉnh/thành phố bạn vừa xem, chế độ xem bản đồ hoặc danh sách, giúp bạn không cần chọn lại ở các lần truy cập tiếp theo.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-surface-container border border-outline-variant/20">
                        <h3 class="font-bold text-on-surface text-base mb-1">Cookie Hiệu năng & Thống kê</h3>
                        <p class="text-sm">
                            Thu thập dữ liệu ẩn danh về cách người dùng tương tác với website (những trang được xem nhiều nhất, thời gian tải trang) để chúng tôi không ngừng cải tiến và tối ưu hóa tốc độ nền tảng.
                        </p>
                    </div>
                </div>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">3.</span> Cách bạn có thể kiểm soát Cookie
                </h2>
                <p>
                    Hầu hết các trình duyệt web hiện đại đều cho phép bạn quản lý, chặn hoặc xóa các cookie đã lưu:
                </p>
                <ul class="list-disc list-inside space-y-2 pl-4">
                    <li><strong>Google Chrome:</strong> Cài đặt &rarr; Quyền riêng tư và bảo mật &rarr; Cookie của bên thứ ba.</li>
                    <li><strong>Apple Safari:</strong> Tùy chọn &rarr; Quyền riêng tư &rarr; Chặn tất cả cookie.</li>
                    <li><strong>Mozilla Firefox:</strong> Cài đặt &rarr; Quyền riêng tư & Bảo mật &rarr; Cookie và dữ liệu trang web.</li>
                    <li><strong>Microsoft Edge:</strong> Cài đặt &rarr; Cookie và quyền truy cập trang web &rarr; Quản lý và xóa cookie.</li>
                </ul>
                <p class="text-xs text-amber-700 bg-amber-50 p-4 rounded-xl border border-amber-200 mt-2">
                    * Lưu ý: Việc tắt hoàn toàn cookie có thể khiến tính năng đăng nhập và lưu quán của bạn không hoạt động chính xác.
                </p>
            </section>
        </div>
    </article>
</main>
@endsection
