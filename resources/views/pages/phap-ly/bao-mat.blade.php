@extends('layouts.app')

@section('title', 'Chính Sách Bảo Mật Thông Tin Cá Nhân | Quán Mới')

@push('styles')
    <meta name="description" content="Chính sách bảo mật dữ liệu người dùng tại Quán Mới, tuân thủ Nghị định 13/2023/NĐ-CP. Cam kết bảo vệ an toàn thông tin cá nhân và quyền riêng tư tuyệt đối.">
    <meta name="keywords" content="chính sách bảo mật quán mới, an toàn dữ liệu quán mới, quyền riêng tư ẩm thực">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main class="pt-[72px] md:pt-[104px] pb-24 max-w-5xl mx-auto px-4 md:px-6 lg:px-8">
    <article class="space-y-10">
        {{-- Header --}}
        <header class="text-center max-w-3xl mx-auto py-10 md:py-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary text-sm font-bold tracking-wide uppercase mb-6">
                <span class="material-symbols-outlined text-[18px]">security</span>
                Tuân thủ Nghị định 13/2023/NĐ-CP
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-on-surface tracking-tight mb-4 leading-tight">
                Chính sách <span class="text-primary">bảo mật thông tin</span>
            </h1>
            <p class="text-sm md:text-base text-on-surface-variant">
                Quán Mới cam kết bảo vệ dữ liệu cá nhân và sự riêng tư của mọi thành viên thực khách và đối tác chủ quán.
            </p>
        </header>

        {{-- Content Box --}}
        <div class="bg-white p-8 md:p-12 rounded-[2.5rem] border border-outline-variant/30 shadow-sm space-y-8 text-on-surface-variant text-base leading-relaxed">
            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">1.</span> Dữ liệu chúng tôi thu thập
                </h2>
                <p>
                    Để mang lại trải nghiệm khám phá ẩm thực tối ưu, Quán Mới thu thập một số thông tin cần thiết:
                </p>
                <ul class="list-disc list-inside space-y-2 pl-4">
                    <li><strong>Thông tin tài khoản:</strong> Họ tên, địa chỉ email, số điện thoại, ảnh đại diện khi bạn đăng ký tài khoản.</li>
                    <li><strong>Dữ liệu vị trí (GPS):</strong> Khi bạn cho phép, chúng tôi sử dụng tọa độ GPS để hiển thị khoảng cách đến các quán ăn xung quanh bạn. Dữ liệu này chỉ xử lý tức thời và không theo dõi lịch trình di chuyển cá nhân.</li>
                    <li><strong>Nội dung tương tác:</strong> Các đánh giá, bình luận, hình ảnh món ăn bạn tải lên và danh sách quán ăn đã lưu.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">2.</span> Mục đích sử dụng dữ liệu
                </h2>
                <p>
                    Thông tin được thu thập chỉ được sử dụng cho các mục đích hợp pháp sau:
                </p>
                <ul class="list-disc list-inside space-y-2 pl-4">
                    <li>Gợi ý các quán ăn, món ngon phù hợp với vị trí và sở thích cá nhân của bạn.</li>
                    <li>Gửi thông báo xác thực bảo mật tài khoản (OTP) và hỗ trợ khôi phục mật khẩu.</li>
                    <li>Thông báo các chương trình ưu đãi, voucher giảm giá từ quán bạn quan tâm.</li>
                    <li>Ngăn chặn các hành vi gian lận, spam đánh giá ảo hoặc phá hoại hệ thống.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">3.</span> Cam kết không chia sẻ dữ liệu trái phép
                </h2>
                <p>
                    Quán Mới <strong>cam kết không bán, trao đổi hoặc chia sẻ</strong> thông tin cá nhân của người dùng cho bất kỳ bên thứ ba nào vì mục đích tiếp thị thương mại độc lập mà không có sự đồng ý rõ ràng của bạn.
                </p>
                <p>
                    Chúng tôi chỉ cung cấp thông tin trong các trường hợp theo yêu cầu của cơ quan pháp luật có thẩm quyền theo đúng trình tự pháp luật Việt Nam.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">4.</span> Biện pháp an ninh & Bảo mật kỹ thuật
                </h2>
                <p>
                    Hệ thống Quán Mới áp dụng các tiêu chuẩn an ninh tiên tiến nhất hiện nay:
                </p>
                <ul class="list-disc list-inside space-y-2 pl-4">
                    <li>Toàn bộ dữ liệu truyền tải đều được mã hóa bằng giao thức SSL/TLS tiêu chuẩn cao.</li>
                    <li>Mật khẩu người dùng được băm (hash) một chiều bằng thuật toán an toàn Bcrypt.</li>
                    <li>Hệ thống máy chủ được bảo vệ bởi tường lửa đa lớp và sao lưu tự động định kỳ.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">5.</span> Quyền của chủ thể dữ liệu
                </h2>
                <p>
                    Bạn có toàn quyền truy cập, cập nhật thông tin cá nhân trong mục <strong>Cài đặt tài khoản</strong> hoặc yêu cầu xóa vĩnh viễn tài khoản và toàn bộ dữ liệu liên quan bất cứ lúc nào bằng cách liên hệ với chúng tôi qua email <code>privacy@quanmoi.com</code>.
                </p>
            </section>
        </div>
    </article>
</main>
@endsection
