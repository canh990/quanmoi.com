@extends('layouts.app')

@section('title', 'Giới thiệu về Quán Mới - Nền tảng khám phá ẩm thực số 1')

@push('styles')
    <meta name="description" content="Quán Mới là nền tảng cộng đồng giúp bạn khám phá, đánh giá và chia sẻ các địa điểm ẩm thực, quán ăn, quán cà phê chất lượng nhất. Khám phá tinh hoa ẩm thực địa phương cùng Quán Mới.">
    <meta name="keywords" content="giới thiệu quán mới, review ẩm thực, quán ăn ngon, quán cà phê đẹp, địa điểm giải trí">
    <link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<main class="pt-[72px] md:pt-[104px] pb-24 max-w-7xl mx-auto px-4 md:px-6 lg:px-10">
    <article>
        {{-- Hero Header --}}
        <header class="text-center max-w-3xl mx-auto py-12 md:py-20">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-on-surface tracking-tight mb-6 leading-tight">
                Khám phá <span class="text-primary">Tinh hoa Ẩm thực</span> Địa phương
            </h1>
            <p class="text-lg md:text-xl text-on-surface-variant leading-relaxed">
                Quán Mới là người bạn đồng hành tin cậy, kết nối hàng triệu tín đồ ẩm thực với những địa điểm ăn uống, giải trí tuyệt vời nhất xung quanh bạn.
            </p>
        </header>

        {{-- Main Feature Grid --}}
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-24">
            {{-- Feature 1 --}}
            <div class="bg-surface-container p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                <div class="w-14 h-14 bg-primary-container rounded-2xl flex items-center justify-center mb-6 text-primary">
                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">restaurant_menu</span>
                </div>
                <h2 class="text-2xl font-bold text-on-surface mb-3">Đa dạng lựa chọn</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    Từ những quán ăn đường phố bình dân đến các nhà hàng sang trọng, Quán Mới mang đến danh sách phong phú nhất đáp ứng mọi nhu cầu của bạn.
                </p>
            </div>

            {{-- Feature 2 --}}
            <div class="bg-surface-container p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                <div class="w-14 h-14 bg-tertiary-container rounded-2xl flex items-center justify-center mb-6 text-tertiary">
                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">star_rate</span>
                </div>
                <h2 class="text-2xl font-bold text-on-surface mb-3">Đánh giá chân thực</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    Hệ thống đánh giá đa chiều từ cộng đồng người dùng thực tế giúp bạn có cái nhìn khách quan nhất trước khi quyết định trải nghiệm.
                </p>
            </div>

            {{-- Feature 3 --}}
            <div class="bg-surface-container p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300">
                <div class="w-14 h-14 bg-secondary-container rounded-2xl flex items-center justify-center mb-6 text-secondary">
                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">group</span>
                </div>
                <h2 class="text-2xl font-bold text-on-surface mb-3">Cộng đồng gắn kết</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    Nơi hội tụ của những người đam mê ẩm thực, cùng chia sẻ khoảnh khắc, viết review và giao lưu kết bạn bốn phương.
                </p>
            </div>
        </section>

        {{-- About Story --}}
        <section class="max-w-4xl mx-auto bg-white border border-outline-variant/30 p-8 md:p-12 rounded-[2.5rem] shadow-xl">
            <h2 class="text-3xl md:text-4xl font-bold text-on-surface mb-8 text-center">Câu chuyện của chúng tôi</h2>
            
            <div class="space-y-6 text-lg text-on-surface-variant leading-relaxed">
                <p>
                    Được thành lập vào năm 2026, <strong>Quán Mới</strong> ra đời từ niềm đam mê khám phá ẩm thực vô tận và mong muốn giải quyết một câu hỏi quen thuộc: <em>"Hôm nay ăn gì, ở đâu?"</em>
                </p>
                <p>
                    Chúng tôi nhận ra rằng, đằng sau mỗi món ăn ngon là tâm huyết của người đầu bếp, là một câu chuyện văn hóa thú vị cần được lan tỏa. Vì vậy, Quán Mới không chỉ là một ứng dụng tìm kiếm, mà còn là chiếc cầu nối giữa thực khách và những tinh hoa ẩm thực địa phương.
                </p>
                <p>
                    Sứ mệnh của chúng tôi là xây dựng một hệ sinh thái minh bạch, nơi người dùng có thể dễ dàng chia sẻ trải nghiệm chân thực, còn các chủ quán có cơ hội tiếp cận đúng khách hàng mục tiêu để không ngừng nâng cao chất lượng dịch vụ.
                </p>
                <p class="font-medium text-primary text-xl pt-4 text-center">
                    "Hãy để Quán Mới dẫn lối vị giác của bạn!"
                </p>
            </div>
        </section>

        {{-- CTA --}}
        <section class="mt-24 text-center">
            <h3 class="text-3xl font-bold text-on-surface mb-6">Bạn đã sẵn sàng khám phá?</h3>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/" class="bg-primary text-white font-bold text-lg px-8 py-4 rounded-full hover:bg-primary/90 transition-transform hover:scale-105 shadow-lg hover:shadow-primary/30">
                    Khám phá ngay
                </a>
                <a href="{{ route('register') }}" class="bg-surface-container-high text-on-surface font-bold text-lg px-8 py-4 rounded-full hover:bg-outline-variant/30 transition-transform hover:scale-105">
                    Tham gia cộng đồng
                </a>
            </div>
        </section>
    </article>
</main>
@endsection
