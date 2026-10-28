@extends('layouts.app')

@section('title', 'Liên Hệ Hợp Tác - Đồng Hành Cùng Nền Tảng Ẩm Thực Quán Mới')

@push('styles')
    <meta name="description" content="Liên hệ hợp tác kinh doanh, truyền thông quảng cáo và phát triển chuỗi ẩm thực cùng Quán Mới. Hotline 1900 1234, hỗ trợ chuyên nghiệp 24/7.">
    <meta name="keywords" content="liên hệ quán mới, hợp tác truyền thông ẩm thực, quảng cáo nhà hàng quán ăn, đối tác f&b">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main class="pt-[72px] md:pt-[104px] pb-24 max-w-7xl mx-auto px-4 md:px-6 lg:px-10">
    <article>
        {{-- Hero Header --}}
        <header class="text-center max-w-3xl mx-auto py-12 md:py-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary text-sm font-bold tracking-wide uppercase mb-6">
                <span class="material-symbols-outlined text-[18px]">handshake</span>
                Kết nối & Phát triển bền vững
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-on-surface tracking-tight mb-6 leading-tight">
                Liên hệ hợp tác cùng <span class="text-primary">Quán Mới</span>
            </h1>
            <p class="text-lg md:text-xl text-on-surface-variant leading-relaxed">
                Chúng tôi luôn sẵn sàng lắng nghe mọi đề xuất hợp tác truyền thông, phát triển chuỗi ẩm thực và đồng hành cùng các thương hiệu F&B trên toàn quốc.
            </p>
        </header>

        {{-- Contact Grid: Info & Form --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start mb-24">
            {{-- Left Column: Contact Cards (Spans 5 cols) --}}
            <div class="lg:col-span-5 space-y-6">
                {{-- Address Card --}}
                <div class="bg-surface-container p-6 rounded-3xl border border-outline-variant/30 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">location_on</span>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-on-surface mb-1">Trụ sở chính</h2>
                        <p class="text-sm text-on-surface-variant leading-relaxed">
                            Tầng 6, Tòa nhà Innovation, 123 Nguyễn Thị Minh Khai, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh
                        </p>
                    </div>
                </div>

                {{-- Hotline Card --}}
                <div class="bg-surface-container p-6 rounded-3xl border border-outline-variant/30 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">call</span>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-on-surface mb-1">Tổng đài hỗ trợ</h2>
                        <a href="tel:19001234" class="text-lg font-black text-primary hover:underline">1900 1234</a>
                        <p class="text-xs text-on-surface-variant mt-0.5">8:00 - 21:00 hàng ngày (kể cả Thứ 7 & CN)</p>
                    </div>
                </div>

                {{-- Email Card --}}
                <div class="bg-surface-container p-6 rounded-3xl border border-outline-variant/30 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-tertiary/10 text-tertiary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">mail</span>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-on-surface mb-1">Email liên hệ</h2>
                        <div class="space-y-1">
                            <p class="text-sm">
                                <span class="text-on-surface-variant">Hợp tác:</span> 
                                <a href="mailto:hoptac@quanmoi.com" class="font-bold text-on-surface hover:text-primary">hoptac@quanmoi.com</a>
                            </p>
                            <p class="text-sm">
                                <span class="text-on-surface-variant">Hỗ trợ:</span> 
                                <a href="mailto:contact@quanmoi.com" class="font-bold text-on-surface hover:text-primary">contact@quanmoi.com</a>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Working Process Card --}}
                <div class="bg-gradient-to-br from-primary/10 to-primary-container/20 p-6 rounded-3xl border border-primary/20">
                    <div class="flex items-center gap-2 text-primary font-bold text-sm mb-2">
                        <span class="material-symbols-outlined text-[20px]">bolt</span>
                        Cam kết phản hồi siêu tốc
                    </div>
                    <p class="text-on-surface-variant text-sm leading-relaxed">
                        Đội ngũ tư vấn viên của Quán Mới sẽ chủ động liên hệ lại qua số điện thoại hoặc email của bạn trong vòng tối đa <strong>24 giờ làm việc</strong>.
                    </p>
                </div>
            </div>

            {{-- Right Column: Interactive Form (Spans 7 cols) --}}
            <div class="lg:col-span-7 bg-white p-8 md:p-10 rounded-[2.5rem] border border-outline-variant/30 shadow-xl">
                <h2 class="text-2xl md:text-3xl font-extrabold text-on-surface mb-2">Gửi yêu cầu hợp tác</h2>
                <p class="text-on-surface-variant text-sm mb-8">Điền thông tin vào mẫu bên dưới, chúng tôi sẽ xây dựng kế hoạch phù hợp nhất cho bạn.</p>

                <form id="contactForm" onsubmit="event.preventDefault(); submitContactForm();" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-on-surface uppercase tracking-wider mb-2">Họ và tên *</label>
                            <input type="text" required placeholder="Nguyễn Văn A" class="w-full px-4 py-3 rounded-2xl bg-surface-container-low border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none transition-all text-sm font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-on-surface uppercase tracking-wider mb-2">Số điện thoại *</label>
                            <input type="tel" required placeholder="0912 345 678" class="w-full px-4 py-3 rounded-2xl bg-surface-container-low border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none transition-all text-sm font-medium">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-on-surface uppercase tracking-wider mb-2">Email của bạn *</label>
                            <input type="email" required placeholder="name@company.com" class="w-full px-4 py-3 rounded-2xl bg-surface-container-low border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none transition-all text-sm font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-on-surface uppercase tracking-wider mb-2">Tên quán / Doanh nghiệp</label>
                            <input type="text" placeholder="Nhà hàng Quán Ngon / Chuỗi Cafe" class="w-full px-4 py-3 rounded-2xl bg-surface-container-low border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none transition-all text-sm font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface uppercase tracking-wider mb-2">Nhu cầu hợp tác</label>
                        <select class="w-full px-4 py-3 rounded-2xl bg-surface-container-low border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none transition-all text-sm font-medium">
                            <option value="quang-ba">Quảng bá quán ăn & Review truyền thông</option>
                            <option value="chuoi-fb">Đăng ký chuỗi nhà hàng / thương hiệu F&B</option>
                            <option value="tai-tro">Tài trợ & Đồng hành sự kiện ẩm thực</option>
                            <option value="api-tech">Tích hợp công nghệ & API đối tác</option>
                            <option value="khac">Nhu cầu khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface uppercase tracking-wider mb-2">Nội dung chi tiết *</label>
                        <textarea required rows="4" placeholder="Chia sẻ thêm về mục tiêu, ngân sách dự kiến hoặc kế hoạch hợp tác của bạn..." class="w-full px-4 py-3 rounded-2xl bg-surface-container-low border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none transition-all text-sm font-medium"></textarea>
                    </div>

                    <button type="submit" id="submitBtn" class="w-full py-4 rounded-full bg-primary hover:bg-primary/90 text-white font-extrabold text-base shadow-lg shadow-primary/25 hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">send</span>
                        <span>Gửi thông tin liên hệ ngay</span>
                    </button>
                </form>

                {{-- Success Notice (Hidden initially) --}}
                <div id="successNotice" class="hidden mt-6 p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600 text-[26px]">check_circle</span>
                    <div>
                        <p class="font-bold">Đã gửi yêu cầu hợp tác thành công!</p>
                        <p class="text-xs text-emerald-700 mt-0.5">Cảm ơn bạn đã quan tâm. Quán Mới sẽ liên hệ lại với bạn trong thời gian sớm nhất.</p>
                    </div>
                </div>
            </div>
        </div>
    </article>
</main>

<script>
function submitContactForm() {
    const btn = document.getElementById('submitBtn');
    const notice = document.getElementById('successNotice');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> Đang gửi...';
    
    setTimeout(() => {
        btn.classList.add('hidden');
        notice.classList.remove('hidden');
    }, 800);
}
</script>
@endsection
