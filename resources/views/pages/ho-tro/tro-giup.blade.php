@extends('layouts.app')

@section('title', 'Trung Tâm Trợ Giúp - Hướng Dẫn & Giải Đáp Thắc Mắc | Quán Mới')

@push('styles')
    <meta name="description" content="Trung tâm trợ giúp Quán Mới. Hướng dẫn sử dụng cho thực khách và chủ quán, giải đáp các câu hỏi thường gặp, hỗ trợ kỹ thuật và an toàn tài khoản 24/7.">
    <meta name="keywords" content="trợ giúp quán mới, hướng dẫn đăng quán ăn, câu hỏi thường gặp quán mới, tổng đài hỗ trợ">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main class="pt-[72px] md:pt-[104px] pb-24 max-w-7xl mx-auto px-4 md:px-6 lg:px-10">
    <article>
        {{-- Hero Header with Live Search --}}
        <header class="text-center max-w-3xl mx-auto py-12 md:py-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary text-sm font-bold tracking-wide uppercase mb-6">
                <span class="material-symbols-outlined text-[18px]">help</span>
                Trung tâm hỗ trợ khách hàng
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-on-surface tracking-tight mb-6 leading-tight">
                Bạn cần Quán Mới giúp đỡ điều gì?
            </h1>
            <p class="text-lg text-on-surface-variant leading-relaxed mb-8">
                Tìm kiếm nhanh câu trả lời hoặc lựa chọn các chủ đề hướng dẫn phổ biến bên dưới.
            </p>

            {{-- Live Search Input --}}
            <div class="relative max-w-xl mx-auto">
                <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[24px]">search</span>
                <input 
                    type="text" 
                    id="faqSearchInput" 
                    oninput="filterFaqs()" 
                    placeholder="Nhập từ khóa tìm kiếm (vd: đăng quán, đổi mật khẩu, lưu quán...)"
                    class="w-full pl-14 pr-6 py-4 rounded-full bg-white border border-outline-variant/40 shadow-lg shadow-black/5 focus:border-primary focus:outline-none text-base font-medium transition-all"
                >
            </div>
        </header>

        {{-- Categories Grid --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
            <div class="bg-surface-container p-6 rounded-3xl border border-outline-variant/30 hover:border-primary/50 transition-all text-center">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[26px]">explore</span>
                </div>
                <h2 class="text-lg font-bold text-on-surface mb-2">Dành cho Thực khách</h2>
                <p class="text-xs text-on-surface-variant leading-relaxed">Tìm quán gần đây, chỉ đường GPS, lưu danh sách quán ăn yêu thích.</p>
            </div>

            <div class="bg-surface-container p-6 rounded-3xl border border-outline-variant/30 hover:border-primary/50 transition-all text-center">
                <div class="w-12 h-12 rounded-2xl bg-secondary/10 text-secondary flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[26px]">storefront</span>
                </div>
                <h2 class="text-lg font-bold text-on-surface mb-2">Dành cho Chủ quán</h2>
                <p class="text-xs text-on-surface-variant leading-relaxed">Đăng quán mới, quản lý thực đơn, tạo voucher AI và phản hồi khách hàng.</p>
            </div>

            <div class="bg-surface-container p-6 rounded-3xl border border-outline-variant/30 hover:border-primary/50 transition-all text-center">
                <div class="w-12 h-12 rounded-2xl bg-tertiary/10 text-tertiary flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[26px]">rate_review</span>
                </div>
                <h2 class="text-lg font-bold text-on-surface mb-2">Đánh giá & Bình luận</h2>
                <p class="text-xs text-on-surface-variant leading-relaxed">Quy định chấm điểm, chia sẻ hình ảnh món ăn và tiêu chuẩn cộng đồng.</p>
            </div>

            <div class="bg-surface-container p-6 rounded-3xl border border-outline-variant/30 hover:border-primary/50 transition-all text-center">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[26px]">lock_reset</span>
                </div>
                <h2 class="text-lg font-bold text-on-surface mb-2">Tài khoản & Bảo mật</h2>
                <p class="text-xs text-on-surface-variant leading-relaxed">Đổi mật khẩu, xác thực email OTP và quản lý thông tin cá nhân.</p>
            </div>
        </section>

        {{-- FAQ Accordion List --}}
        <section class="max-w-4xl mx-auto mb-24">
            <h2 class="text-3xl font-extrabold text-on-surface mb-8 text-center">Câu hỏi thường gặp</h2>
            
            <div id="faqList" class="space-y-4">
                {{-- Item 1 --}}
                <details class="faq-item group bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 transition-all open:shadow-md" data-keywords="tìm quán gần tôi gps định vị khoảng cách">
                    <summary class="font-bold text-base md:text-lg text-on-surface cursor-pointer flex items-center justify-between list-none">
                        <span>Làm thế nào để tìm quán ăn ngon ở gần vị trí của tôi nhất?</span>
                        <span class="material-symbols-outlined text-primary group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-on-surface-variant text-sm md:text-base mt-4 leading-relaxed">
                        Bạn có thể nhấn vào mục <strong>"Bản đồ"</strong> trên thanh điều hướng hoặc tại trang chủ chọn tính năng <strong>"Gần tôi"</strong> và cấp quyền truy cập vị trí GPS. Hệ thống sẽ tự động quét các quán ăn xung quanh bạn với khoảng cách chính xác tính theo km.
                    </p>
                </details>

                {{-- Item 2 --}}
                <details class="faq-item group bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 transition-all open:shadow-md" data-keywords="đăng quán tạo quán chủ quán miễn phí chi phí xét duyệt">
                    <summary class="font-bold text-base md:text-lg text-on-surface cursor-pointer flex items-center justify-between list-none">
                        <span>Chủ quán đăng ký hiển thị quán trên Quán Mới có mất phí không?</span>
                        <span class="material-symbols-outlined text-primary group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-on-surface-variant text-sm md:text-base mt-4 leading-relaxed">
                        Hoàn toàn <strong>miễn phí 100%</strong>. Mọi chủ quán đều có thể đăng ký tài khoản, gửi thông tin quán, hình ảnh và tạo thực đơn mà không phải chịu bất kỳ khoản phí ban đầu hay phí duy trì định kỳ nào.
                    </p>
                </details>

                {{-- Item 3 --}}
                <details class="faq-item group bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 transition-all open:shadow-md" data-keywords="viết đánh giá chấm điểm review bình luận hình ảnh sao">
                    <summary class="font-bold text-base md:text-lg text-on-surface cursor-pointer flex items-center justify-between list-none">
                        <span>Làm sao để viết đánh giá và chấm điểm cho một quán ăn?</span>
                        <span class="material-symbols-outlined text-primary group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-on-surface-variant text-sm md:text-base mt-4 leading-relaxed">
                        Sau khi đăng nhập tài khoản, bạn truy cập trang chi tiết của quán ăn đó, kéo xuống phần <strong>"Đánh giá từ cộng đồng"</strong>, chọn số sao (từ 1 đến 5 sao) và nhập nhận xét chân thực về hương vị món ăn, không gian và thái độ phục vụ.
                    </p>
                </details>

                {{-- Item 4 --}}
                <details class="faq-item group bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 transition-all open:shadow-md" data-keywords="quên mật khẩu lấy lại mật khẩu đổi pass otp email">
                    <summary class="font-bold text-base md:text-lg text-on-surface cursor-pointer flex items-center justify-between list-none">
                        <span>Tôi bị quên mật khẩu đăng nhập thì phải làm thế nào?</span>
                        <span class="material-symbols-outlined text-primary group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-on-surface-variant text-sm md:text-base mt-4 leading-relaxed">
                        Bạn chỉ cần bấm vào nút <strong>"Đăng nhập"</strong>, chọn <strong>"Quên mật khẩu?"</strong> và nhập địa chỉ email đã đăng ký. Hệ thống sẽ gửi cho bạn đường link hoặc mã xác thực để đặt lại mật khẩu mới an toàn trong vòng 1 phút.
                    </p>
                </details>

                {{-- Item 5 --}}
                <details class="faq-item group bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/30 transition-all open:shadow-md" data-keywords="báo cáo vi phạm đánh giá xấu vu khống khiếu nại tranh chấp">
                    <summary class="font-bold text-base md:text-lg text-on-surface cursor-pointer flex items-center justify-between list-none">
                        <span>Nếu gặp một đánh giá sai sự thật hoặc mang tính vu khống thì xử lý thế nào?</span>
                        <span class="material-symbols-outlined text-primary group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-on-surface-variant text-sm md:text-base mt-4 leading-relaxed">
                        Chủ quán có thể sử dụng tính năng <strong>"Phản hồi bình luận"</strong> để giải thích rõ ràng, hoặc gửi khiếu nại kèm bằng chứng qua email <code>khieunai@quanmoi.com</code>. Đội ngũ kiểm duyệt sẽ xác minh và gỡ bỏ bình luận vi phạm theo quy chế hoạt động.
                    </p>
                </details>
            </div>

            <div id="noFaqResult" class="hidden text-center py-12">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-2">search_off</span>
                <p class="text-lg font-bold text-on-surface">Không tìm thấy câu hỏi phù hợp</p>
                <p class="text-sm text-on-surface-variant mt-1">Vui lòng thử lại với từ khóa khác hoặc liên hệ bộ phận hỗ trợ bên dưới.</p>
            </div>
        </section>

        {{-- Direct Contact Box --}}
        <section class="bg-surface-container p-8 md:p-12 rounded-[2.5rem] border border-outline-variant/30 text-center max-w-3xl mx-auto">
            <h3 class="text-2xl md:text-3xl font-extrabold text-on-surface mb-3">Bạn vẫn cần trợ giúp thêm?</h3>
            <p class="text-on-surface-variant text-base mb-6">Đội ngũ hỗ trợ khách hàng của Quán Mới luôn sẵn sàng lắng nghe và giải quyết vấn đề của bạn.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="tel:19001234" class="inline-flex items-center gap-2 bg-primary text-white font-bold px-6 py-3 rounded-full hover:bg-primary/90 transition-all">
                    <span class="material-symbols-outlined">call</span>
                    <span>Hotline: 1900 1234</span>
                </a>
                <a href="{{ route('pages.lien-he') }}" class="inline-flex items-center gap-2 bg-surface-container-high text-on-surface font-bold px-6 py-3 rounded-full hover:bg-surface-container-highest transition-all">
                    <span class="material-symbols-outlined">mail</span>
                    <span>Gửi thư hỗ trợ</span>
                </a>
            </div>
        </section>
    </article>
</main>

<script>
function filterFaqs() {
    const input = document.getElementById('faqSearchInput').value.toLowerCase().trim();
    const items = document.querySelectorAll('.faq-item');
    let visibleCount = 0;

    items.forEach(item => {
        const text = (item.innerText + ' ' + (item.getAttribute('data-keywords') || '')).toLowerCase();
        if (text.includes(input)) {
            item.classList.remove('hidden');
            visibleCount++;
        } else {
            item.classList.add('hidden');
        }
    });

    const noResult = document.getElementById('noFaqResult');
    if (visibleCount === 0) {
        noResult.classList.remove('hidden');
    } else {
        noResult.classList.add('hidden');
    }
}
</script>
@endsection
