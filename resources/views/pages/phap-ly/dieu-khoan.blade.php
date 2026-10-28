@extends('layouts.app')

@section('title', 'Quy Định Sử Dụng & Điều Khoản Dịch Vụ | Quán Mới')

@push('styles')
    <meta name="description" content="Quy định sử dụng và điều khoản dịch vụ của nền tảng ẩm thực Quán Mới. Tiêu chuẩn cộng đồng, quyền và nghĩa vụ của người dùng và đối tác chủ quán.">
    <meta name="keywords" content="quy định sử dụng quán mới, điều khoản dịch vụ quán mới, tiêu chuẩn cộng đồng review ẩm thực">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main class="pt-[72px] md:pt-[104px] pb-24 max-w-5xl mx-auto px-4 md:px-6 lg:px-8">
    <article class="space-y-10">
        {{-- Header --}}
        <header class="text-center max-w-3xl mx-auto py-10 md:py-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary text-sm font-bold tracking-wide uppercase mb-6">
                <span class="material-symbols-outlined text-[18px]">policy</span>
                Điều khoản pháp lý & Cam kết dịch vụ
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-on-surface tracking-tight mb-4 leading-tight">
                Quy định sử dụng & <span class="text-primary">Điều khoản dịch vụ</span>
            </h1>
            <p class="text-sm md:text-base text-on-surface-variant">
                Có hiệu lực từ ngày 01 tháng 01 năm 2026. Cập nhật định kỳ nhằm đảm bảo môi trường cộng đồng lành mạnh.
            </p>
        </header>

        {{-- Legal Document Body --}}
        <div class="bg-white p-8 md:p-12 rounded-[2.5rem] border border-outline-variant/30 shadow-sm space-y-8 text-on-surface-variant text-base leading-relaxed">
            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">1.</span> Giới thiệu chung & Chấp thuận điều khoản
                </h2>
                <p>
                    Chào mừng bạn đến với <strong>Quán Mới</strong> (quanmoi.com). Bằng việc truy cập, tạo tài khoản hoặc sử dụng bất kỳ tính năng nào trên website, bạn xác nhận rằng bạn đã đọc, hiểu và đồng ý tuân thủ toàn bộ các điều khoản được nêu tại đây cùng Chính sách bảo mật của chúng tôi.
                </p>
                <p>
                    Nếu bạn không đồng ý với bất kỳ phần nào của các điều khoản này, vui lòng ngừng sử dụng dịch vụ ngay lập tức.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">2.</span> Đăng ký tài khoản & Bảo mật
                </h2>
                <p>
                    Người dùng cần cung cấp thông tin chính xác, cập nhật khi đăng ký tài khoản (họ tên, email, số điện thoại). Bạn có trách nhiệm tự bảo mật mật khẩu tài khoản và các hoạt động phát sinh từ tài khoản của mình.
                </p>
                <p>
                    Quán Mới có quyền tạm khóa hoặc xóa vĩnh viễn các tài khoản có dấu hiệu mạo danh, tạo nhiều tài khoản ảo để thao túng đánh giá hoặc thực hiện các hành vi gian lận.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">3.</span> Tiêu chuẩn cộng đồng & Quy định đánh giá
                </h2>
                <p>
                    Mọi đánh giá (review), chấm điểm và hình ảnh chia sẻ trên Quán Mới phải phản ánh trải nghiệm ẩm thực chân thực của chính bạn. Chúng tôi nghiêm cấm:
                </p>
                <ul class="list-disc list-inside space-y-2 pl-4">
                    <li>Đăng tải nội dung mang tính vu khống, xúc phạm cá nhân hoặc bôi nhọ danh dự của chủ quán và nhân viên.</li>
                    <li>Sử dụng ngôn từ thù địch, phân biệt chủng tộc, tôn giáo hoặc nội dung trái thuần phong mỹ tục Việt Nam.</li>
                    <li>Đánh giá giả mạo nhằm mục đích cạnh tranh không lành mạnh hoặc tống tiền, trục lợi từ chủ quán.</li>
                    <li>Đăng tải nội dung quảng cáo rác, chèn liên kết spam hoặc mã độc hại.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">4.</span> Quyền và nghĩa vụ của Đối tác Chủ quán
                </h2>
                <p>
                    Chủ quán có quyền tạo và chỉnh sửa thông tin quán ăn, hình ảnh món ăn, bảng giá và các chương trình ưu đãi. Chủ quán cam kết:
                </p>
                <ul class="list-disc list-inside space-y-2 pl-4">
                    <li>Cung cấp thông tin địa chỉ, khung giờ mở cửa và giá cả chính xác với thực tế phục vụ.</li>
                    <li>Đảm bảo các quy định an toàn vệ sinh thực phẩm theo quy chuẩn của Bộ Y tế.</li>
                    <li>Không sử dụng thủ thuật seeding đánh giá ảo để đánh lừa khách hàng.</li>
                </ul>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">5.</span> Bản quyền & Quyền sở hữu trí tuệ
                </h2>
                <p>
                    Tất cả logo, giao diện thiết kế, mã nguồn, cơ sở dữ liệu và thương hiệu "Quán Mới" thuộc quyền sở hữu trí tuệ của Quán Mới. Mọi hành vi sao chép, trích xuất dữ liệu tự động (crawl/scrape) trái phép đều bị nghiêm cấm.
                </p>
                <p>
                    Đối với hình ảnh do người dùng đăng tải, bạn cấp cho Quán Mới quyền sử dụng phi độc quyền để hiển thị, quảng bá ẩm thực trên nền tảng mà không phải trả phí tác quyền.
                </p>
            </section>

            <section class="space-y-3">
                <h2 class="text-xl md:text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="text-primary font-black">6.</span> Giới hạn trách nhiệm pháp lý
                </h2>
                <p>
                    Quán Mới đóng vai trò là nền tảng trung gian thông tin kết nối thực khách và các địa điểm ẩm thực. Chúng tôi nỗ lực tối đa để xác thực thông tin, nhưng không chịu trách nhiệm pháp lý trực tiếp đối với chất lượng thực phẩm, thái độ phục vụ hoặc các tranh chấp thanh toán phát sinh tại cơ sở của quán ăn.
                </p>
            </section>
        </div>
    </article>
</main>
@endsection
