@extends('layouts.app')
@section('title', 'Blog - Quán Mới | Khám phá ẩm thực địa phương')

@push('styles')
<style>
    .level-1-card { background: white; border-radius: 12px; box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05); transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .level-1-card:hover { transform: translateY(-4px); box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.08); }
    .active-tab { color: #a04100; font-weight: 700; border-bottom: 2px solid #a04100; padding-bottom: 4px; }
</style>
@endpush

@section('content')
<main class="mt-6 max-w-[1200px] mx-auto px-container-margin pb-stack-lg min-h-[800px]">
    <!-- Featured Post Hero -->
    <section class="relative w-full h-[480px] rounded-xl overflow-hidden mb-stack-lg group cursor-pointer">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBgE9AhiwhRG0iMIDalUuezBkh8QgtuGivtqJDWACIc-l5B3ILO8qbq1wl58ArJH6J1qcTS0pyEo7XSimbdICDPXazjP3CdkPC57NdYvS7fuPkjG18mf0PKW8vSh-xHP3Z39VjZl0p5MOr5jmZkrUPMiiFaZZi0haIHI8QZAaiTq6F-1yg00ptU5svD1XHADCaHFcfbV9QjjEhMJh9koOKfHu6pYaGc7ntl2uqjzktotsVlFwbX9ass0wxve2B_xfK7acLfp5tkG2w')">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-8 md:p-12 max-w-3xl">
            <span class="bg-primary-container text-on-primary px-3 py-1 rounded-lg font-label-md text-label-md mb-4 inline-block uppercase tracking-wider">Khám phá</span>
            <h1 class="font-display-lg text-white text-4xl md:text-5xl mb-4 leading-tight">Top 10 địa điểm ăn sáng nhất định phải thử tại Sài Gòn</h1>
            <p class="text-white/90 font-body-lg text-body-lg mb-6 line-clamp-2">Từ những tô phở bò gia truyền thơm nức mũi đến những gánh xôi mặn vỉa hè, hãy cùng Quán Mới đi một vòng thành phố để tìm kiếm những hương vị khởi đầu ngày mới tuyệt vời nhất.</p>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center overflow-hidden border border-white/30">
                    <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDA7Q3j1lZ54VQy6usIU_m4MXVN4FTWrqnJ-wbokZmkI_spwGHhKjhb9BQd5wcxB8p9YJBQQVAiKi_s1R4L4TlVdbYOj6KgqgLVdJlw3-Xsjg60y6Ip3Qb84bTC-nbrnHSs5lqoj-3QHTS1UjH1H85Y6v63OL37vyIr-lsSMFjZBZ8_AXwpoHV1cjgaKED24o1vUkQ5c8d_YBPqc92234apfmxtlHi888GfQsjp5UCmssScnp98aiPNRpWOOSdiMl_48MDvvYsK-GI"/>
                </div>
                <div class="text-white">
                    <p class="font-label-md text-label-md">Bởi Minh Tú</p>
                    <p class="text-xs opacity-75">15 Tháng 5, 2024 • 8 phút đọc</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Category Navigation -->
    <nav class="flex items-center gap-8 mb-stack-lg border-b border-outline-variant overflow-x-auto whitespace-nowrap pb-1 scrollbar-hide" id="blog-category-nav">
        <a class="font-title-md text-title-md active-tab" href="#">Tất cả</a>
        <a class="font-title-md text-title-md text-text-muted hover:text-primary transition-colors" href="#">Khám phá</a>
        <a class="font-title-md text-title-md text-text-muted hover:text-primary transition-colors" href="#">Review quán</a>
        <a class="font-title-md text-title-md text-text-muted hover:text-primary transition-colors" href="#">Mẹo nấu ăn</a>
        <a class="font-title-md text-title-md text-text-muted hover:text-primary transition-colors" href="#">Văn hóa ẩm thực</a>
    </nav>
    
    <div class="flex flex-col lg:flex-row gap-gutter">
        <!-- Article Grid -->
        <div class="lg:w-3/4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
                <!-- Blog Card 1 -->
                <article class="level-1-card flex flex-col h-full cursor-pointer">
                    <div class="relative aspect-[4/3] rounded-t-xl overflow-hidden">
                        <div class="absolute top-3 left-3 z-10 bg-primary-container text-on-primary px-2 py-0.5 rounded-md font-label-md text-label-md">Review quán</div>
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCbLnhiSb5JTqa4gFrtyxJzMgv5PndNNyr59Dv3fdEncsjexEdZHHR9yqlF9SdzM4hHNKWb3P3_vxnuUljEFZMZ36LOwdOaqlhGfDB9u43nqagyy4iKt7lE8BrSLeehwppwSkNkNyjPahMpjOIwAvfn1Zq3ny77LXLFFZ930wxuul7K7qP2Aca8YSNB1C2dOqSqb9ReaTDT2aEC2w2xk_TRUoG0aTIMzl2vHmSQgDzetdEoVyN7n058W7ZU6vkycBlG8cHWk585Cfg"/>
                    </div>
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="font-title-md text-title-md text-on-surface mb-2 line-clamp-2">Bí mật đằng sau quán Bún Chả lâu đời nhất Hà Nội</h3>
                        <p class="text-text-muted font-body-sm text-body-sm mb-4 line-clamp-3">Ít ai biết rằng để giữ được hương vị đặc trưng suốt 40 năm, chủ quán đã phải kỳ công chọn lựa từng loại than hoa...</p>
                        <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                            <span class="font-label-sm text-label-sm text-text-muted">Lê Hùng • 12/05/2024</span>
                            <span class="material-symbols-outlined text-primary hover:scale-110 transition-transform">bookmark</span>
                        </div>
                    </div>
                </article>
                
                <!-- Blog Card 2 -->
                <article class="level-1-card flex flex-col h-full cursor-pointer">
                    <div class="relative aspect-[4/3] rounded-t-xl overflow-hidden">
                        <div class="absolute top-3 left-3 z-10 bg-primary-container text-on-primary px-2 py-0.5 rounded-md font-label-md text-label-md">Mẹo nấu ăn</div>
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAiTM4GfEzqy90Gtma6qyLDQ7KcUz3CZhjYUkJFO-99TefFiVVa18ivhMWjk-mEo3ZcWq1oGIOvapH__HEYlwwtmhWzd7di8Au8pffwFivQRfMPQxTkgoa4yg4rkP6_omUIvRet4-pAYU2WPgFyfuD_EHYjODTa62yROkuF_oqu9HApCjKrCBG3axduFhqFZcXBf-J84KLfWZnvCO88pf0on4dOB2F3JQInu4q07wY54nM4nn_G9KgZGNToF4baC9s_Mw38jDXTQlw"/>
                    </div>
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="font-title-md text-title-md text-on-surface mb-2 line-clamp-2">5 Cách làm Nước Chấm 'Thần Thánh' Cân Mọi Món Ăn</h3>
                        <p class="text-text-muted font-body-sm text-body-sm mb-4 line-clamp-3">Nước chấm được ví như linh hồn của món ăn Việt. Hãy cùng khám phá công thức tỷ lệ vàng của các đầu bếp 5 sao...</p>
                        <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                            <span class="font-label-sm text-label-sm text-text-muted">Hoàng Lan • 10/05/2024</span>
                            <span class="material-symbols-outlined text-primary hover:scale-110 transition-transform">bookmark</span>
                        </div>
                    </div>
                </article>
                
                <!-- Blog Card 3 -->
                <article class="level-1-card flex flex-col h-full cursor-pointer">
                    <div class="relative aspect-[4/3] rounded-t-xl overflow-hidden">
                        <div class="absolute top-3 left-3 z-10 bg-primary-container text-on-primary px-2 py-0.5 rounded-md font-label-md text-label-md">Văn hóa</div>
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDebKk_uGce1Bv6FrhS5P8NWsBimhnTsPGtefEaMaim46InnED9fZU9vhWtXWsg1jFeY18GzR_8l013vhAVDi1LJNUF-o5MADzi1oxbLKDbNKXw7QkFqQi83XYs2a3zU07i_HJSLHOf_hEZcwAb6YS9B8DQ4s8dUFbHCJWHU_u_CetWXz8BvMCd-sxZTRL_r7HTubNWQAic02__QP0K36lBy2Hic7ZXfvziWYH9ZwbtGo2F3uTTnVoKCv2kuwRw8ukBk3Ji9YvjZBU"/>
                    </div>
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="font-title-md text-title-md text-on-surface mb-2 line-clamp-2">Hành trình tìm về cội nguồn của Nước Mắm truyền thống</h3>
                        <p class="text-text-muted font-body-sm text-body-sm mb-4 line-clamp-3">Chuyến đi thực tế đến các nhà thùng tại Phú Quốc để hiểu hơn về quy trình ủ chượp ròng rã suốt 12 tháng trời...</p>
                        <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                            <span class="font-label-sm text-label-sm text-text-muted">Quốc Anh • 08/05/2024</span>
                            <span class="material-symbols-outlined text-primary hover:scale-110 transition-transform">bookmark</span>
                        </div>
                    </div>
                </article>
                
                <!-- Blog Card 4 -->
                <article class="level-1-card flex flex-col h-full cursor-pointer">
                    <div class="relative aspect-[4/3] rounded-t-xl overflow-hidden">
                        <div class="absolute top-3 left-3 z-10 bg-primary-container text-on-primary px-2 py-0.5 rounded-md font-label-md text-label-md">Khám phá</div>
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDVqh6hVub-KFSZG7gVx9JO8YVVNEpPxAFiw9tnAnpM8JKz2o3P82hc9lbM4z9M-YEX14OYDzImc0Z33namJb56aMmHSFGkWhRGEGOkhpoYYQPmuO09kSeeo7kR7exorreJ6OA0T3yPfAREpQrEIsEtxiopEe0qorsINA5CMc6aUQp9LBs2UZfKtmclfyOW4pl6Ma20x_Rkje4QHDcvaN482mDBeKF4lKin_fMv1PzhSAgN8T9sBJduL3vsZOz6uX0b_fbx7TLujpQ"/>
                    </div>
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="font-title-md text-title-md text-on-surface mb-2 line-clamp-2">Cà phê trứng - Nét quyến rũ nồng nàn giữa lòng Sài Thành</h3>
                        <p class="text-text-muted font-body-sm text-body-sm mb-4 line-clamp-3">Giữa nhịp sống hối hả, vẫn có những góc nhỏ yên bình để bạn nhâm nhi lớp kem trứng béo ngậy cùng vị đắng đậm đà...</p>
                        <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                            <span class="font-label-sm text-label-sm text-text-muted">Mai Phương • 05/05/2024</span>
                            <span class="material-symbols-outlined text-primary hover:scale-110 transition-transform">bookmark</span>
                        </div>
                    </div>
                </article>
                
                <!-- Blog Card 5 -->
                <article class="level-1-card flex flex-col h-full cursor-pointer">
                    <div class="relative aspect-[4/3] rounded-t-xl overflow-hidden">
                        <div class="absolute top-3 left-3 z-10 bg-primary-container text-on-primary px-2 py-0.5 rounded-md font-label-md text-label-md">Review quán</div>
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC3ctuAA5AF4Wkzq14eSOLSKhrLcqZB4-N7YA5-OTApTd-GAwn0pO88yKT8hzaGN8DNnuPS7SaqzUU-pWqrPaV16-OzGK_gZUzvG3nATHWcapVhTisExn5g1b4DFvNjHjntgqAUsvK1Dzn1UEu10021k6Kye06FhOE3oByFdBH3mLxomZNDgH6_DdcrFXWFzKYXP3Xy7L-79GQ8NKYUFLqsHPrSS2t9azIYSyaaXbVrtlxkACma074MYdizzhinDArxP8hSQv52RhU"/>
                    </div>
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="font-title-md text-title-md text-on-surface mb-2 line-clamp-2">Những quán ăn đêm nức tiếng không thể bỏ qua</h3>
                        <p class="text-text-muted font-body-sm text-body-sm mb-4 line-clamp-3">Danh sách những địa chỉ cứu cánh cho những tâm hồn ăn uống thích đi tìm hương vị khi phố xá đã lên đèn...</p>
                        <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                            <span class="font-label-sm text-label-sm text-text-muted">Trung Kiên • 03/05/2024</span>
                            <span class="material-symbols-outlined text-primary hover:scale-110 transition-transform">bookmark</span>
                        </div>
                    </div>
                </article>
                
                <!-- Blog Card 6 -->
                <article class="level-1-card flex flex-col h-full cursor-pointer">
                    <div class="relative aspect-[4/3] rounded-t-xl overflow-hidden">
                        <div class="absolute top-3 left-3 z-10 bg-primary-container text-on-primary px-2 py-0.5 rounded-md font-label-md text-label-md">Mẹo hay</div>
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDN4v3p3esz7f6JbqYXiWNi1qX5546n9zenJD6BAi4YpC95p7B7SNzCrP8yW40S6rv_2autYHdylUtLQbYqCePl6p81Dc6xz8NC-iPCb8qeSeoyQ4ZbdkBQXqWeP2Phh_Fz9TmJUpwCRBAB5jw0y1tUb__7J2FSfQluCSgTOzHdJf9U_tpqG3ouiPY3zcKPojI5J_8oahHK3_h0QPooXtMVMDR9dN-0Tpyp7X8KDVhybkCGdYtuN-GTnqZNcAHg3BDL8LyM0WcJjUE"/>
                    </div>
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="font-title-md text-title-md text-on-surface mb-2 line-clamp-2">Bí kíp chọn rau thơm 'chuẩn không cần chỉnh'</h3>
                        <p class="text-text-muted font-body-sm text-body-sm mb-4 line-clamp-3">Phân biệt các loại rau gia vị thường gặp và cách bảo quản để chúng luôn tươi xanh như vừa hái từ vườn...</p>
                        <div class="mt-auto pt-4 border-t border-outline-variant flex justify-between items-center">
                            <span class="font-label-sm text-label-sm text-text-muted">Thanh Thủy • 01/05/2024</span>
                            <span class="material-symbols-outlined text-primary hover:scale-110 transition-transform">bookmark</span>
                        </div>
                    </div>
                </article>
            </div>
            
            <div class="mt-12 flex justify-center">
                <button class="px-8 py-3 border-2 border-primary text-primary font-title-md text-title-md rounded-xl hover:bg-primary-fixed transition-colors flex items-center gap-2">
                    Xem thêm bài viết
                    <span class="material-symbols-outlined">expand_more</span>
                </button>
            </div>
        </div>
        
        <!-- Sidebar -->
        <aside class="lg:w-1/4 space-y-stack-lg mt-8 lg:mt-0">
            <!-- Newsletter Signup -->
            <div class="bg-primary-fixed p-6 rounded-xl border border-primary-container/20">
                <h4 class="font-title-md text-title-md text-on-primary-container mb-2">Nhận tin ẩm thực</h4>
                <p class="text-on-primary-fixed-variant font-body-sm text-body-sm mb-4">Đừng bỏ lỡ các review quán mới và công thức nấu ăn độc quyền hàng tuần.</p>
                <div class="space-y-3">
                    <input class="w-full px-4 py-3 bg-white border border-outline-variant rounded-xl text-body-sm focus:ring-primary focus:border-primary" placeholder="Email của bạn..." type="email"/>
                    <button class="w-full py-3 bg-primary-container text-on-primary font-title-md text-title-md rounded-xl shadow-sm hover:opacity-90 active:scale-[0.98] transition-all">Đăng ký ngay</button>
                </div>
            </div>
            
            <!-- Popular Posts -->
            <div class="level-1-card p-6">
                <h4 class="font-title-md text-title-md text-on-surface mb-4 pb-2 border-b border-outline-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">trending_up</span>
                    Bài viết phổ biến
                </h4>
                <div class="space-y-6">
                    <a class="flex gap-3 group" href="#">
                        <span class="text-2xl font-bold text-primary-container/30 group-hover:text-primary transition-colors">01</span>
                        <div>
                            <h5 class="font-label-md text-label-md text-on-surface group-hover:text-primary transition-colors line-clamp-2">Lịch trình Food Tour 24h tại Đà Lạt mộng mơ</h5>
                            <p class="text-[10px] text-text-muted mt-1 uppercase tracking-tighter">15k lượt xem</p>
                        </div>
                    </a>
                    <a class="flex gap-3 group" href="#">
                        <span class="text-2xl font-bold text-primary-container/30 group-hover:text-primary transition-colors">02</span>
                        <div>
                            <h5 class="font-label-md text-label-md text-on-surface group-hover:text-primary transition-colors line-clamp-2">Tại sao cơm tấm Sài Gòn lại ăn kèm với bì chả?</h5>
                            <p class="text-[10px] text-text-muted mt-1 uppercase tracking-tighter">12.5k lượt xem</p>
                        </div>
                    </a>
                    <a class="flex gap-3 group" href="#">
                        <span class="text-2xl font-bold text-primary-container/30 group-hover:text-primary transition-colors">03</span>
                        <div>
                            <h5 class="font-label-md text-label-md text-on-surface group-hover:text-primary transition-colors line-clamp-2">Cách nấu phở bò chuẩn vị Bắc ngay tại nhà</h5>
                            <p class="text-[10px] text-text-muted mt-1 uppercase tracking-tighter">9.8k lượt xem</p>
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Hot Topics Tag Cloud -->
            <div class="level-1-card p-6">
                <h4 class="font-title-md text-title-md text-on-surface mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">local_fire_department</span>
                    Chủ đề hot
                </h4>
                <div class="flex flex-wrap gap-2">
                    <a class="px-3 py-1 bg-surface-container text-on-surface-variant font-label-sm text-label-sm rounded-full hover:bg-primary-fixed hover:text-primary transition-all" href="#">#BúnĐậu</a>
                    <a class="px-3 py-1 bg-surface-container text-on-surface-variant font-label-sm text-label-sm rounded-full hover:bg-primary-fixed hover:text-primary transition-all" href="#">#StreetFood</a>
                    <a class="px-3 py-1 bg-surface-container text-on-surface-variant font-label-sm text-label-sm rounded-full hover:bg-primary-fixed hover:text-primary transition-all" href="#">#HealthyEat</a>
                    <a class="px-3 py-1 bg-surface-container text-on-surface-variant font-label-sm text-label-sm rounded-full hover:bg-primary-fixed hover:text-primary transition-all" href="#">#FoodTour</a>
                    <a class="px-3 py-1 bg-surface-container text-on-surface-variant font-label-sm text-label-sm rounded-full hover:bg-primary-fixed hover:text-primary transition-all" href="#">#SaigonCoffee</a>
                    <a class="px-3 py-1 bg-surface-container text-on-surface-variant font-label-sm text-label-sm rounded-full hover:bg-primary-fixed hover:text-primary transition-all" href="#">#CookingHacks</a>
                    <a class="px-3 py-1 bg-surface-container text-on-surface-variant font-label-sm text-label-sm rounded-full hover:bg-primary-fixed hover:text-primary transition-all" href="#">#VietnameseCuisine</a>
                </div>
            </div>
            
            <!-- Verified Authors -->
            <div class="level-1-card p-6">
                <h4 class="font-title-md text-title-md text-on-surface mb-4">Cộng tác viên</h4>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 cursor-pointer group">
                        <img class="w-10 h-10 rounded-full object-cover group-hover:scale-105 transition-transform" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDGlxv7h4xzhqcB3zBwydt_N4_PMNFcxCmvLuvqOteRzcqBLsjzSU2iwq4DV2ahQvx2zR4vnCZuiRh4n8R8AWE_yJR1deNtPqE5uglOQHWoNj-II0EHT-ywNxxF5yHHOhybBR4-G_kQyjvxAzKl_2f7-Npq9OMpc_fRc9xe2W3EOKGglwjf3sEIwjJnUrJSF1Co8B9PpngQhfYAUiipzE9w-m9DJfH5tvFMs-nFJuYk7rLfQ3DH_y3KPyAC8tR6W89rPo99m6-CJdA"/>
                        <div>
                            <p class="font-label-md text-label-md text-on-surface flex items-center gap-1 group-hover:text-primary transition-colors">
                                Thùy Linh
                                <span class="material-symbols-outlined text-[14px] text-tick-xanh" style="font-variation-settings: 'FILL' 1;">verified</span>
                            </p>
                            <p class="text-[10px] text-text-muted">Food Blogger chuyên nghiệp</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 cursor-pointer group">
                        <img class="w-10 h-10 rounded-full object-cover group-hover:scale-105 transition-transform" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA-Z6lHYMGdq9pegVpMx-VVPEe52e6varkHEHEZLUGHkxga09SfbB5ref5y1Mo0oBT7qGXb38IZopuVFX1RXEb4vdzuA0Rgo2V2BDwjSDQ5ANqVaPsKvjoeYfq-Gi2BrynG_H30YCBlz0GLkTFLS2VW98_hKlVdxKzjCeMKXjWxf76W6DJnF3NJe4Idr71Nvot_haS43oNyk5TDnCJ3zL64wQnVtXYps99TdaW4oCShL4A7_n6Abyp4qgQczZ8pu5cx94xnwtcLIic"/>
                        <div>
                            <p class="font-label-md text-label-md text-on-surface flex items-center gap-1 group-hover:text-primary transition-colors">
                                Chef Tuấn
                                <span class="material-symbols-outlined text-[14px] text-tick-xanh" style="font-variation-settings: 'FILL' 1;">verified</span>
                            </p>
                            <p class="text-[10px] text-text-muted">Bếp trưởng Nhà hàng Sen</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</main>
@endsection

@push('scripts')
<script>
    // Simple active tab logic
    const tabs = document.querySelectorAll('#blog-category-nav a');
    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            tabs.forEach(t => t.classList.remove('active-tab'));
            tabs.forEach(t => t.classList.add('text-text-muted'));
            tab.classList.add('active-tab');
            tab.classList.remove('text-text-muted');
        });
    });
</script>
@endpush
