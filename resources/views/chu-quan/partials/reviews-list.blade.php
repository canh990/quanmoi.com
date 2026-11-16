@forelse($ownerReviews as $danhGia)
    <article id="owner-review-item-{{ $danhGia->id }}" class="rounded-2xl border border-gray-100 bg-white p-4 md:p-5 shadow-2xs hover:border-gray-200 transition-all duration-300 relative">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                @if($danhGia->nguoiDung?->anh_dai_dien)
                    <img src="{{ $danhGia->nguoiDung->anh_dai_dien }}" alt="{{ $danhGia->nguoiDung->ho_ten }}" class="w-9 h-9 rounded-full object-cover shrink-0 border border-gray-100 shadow-2xs">
                @else
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-500 to-orange-500 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                        {{ mb_strtoupper(mb_substr($danhGia->nguoiDung?->ho_ten ?? 'K', 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <h4 class="font-bold text-sm text-gray-900 truncate">{{ $danhGia->nguoiDung?->ho_ten ?? 'Thành viên Quán Mới' }}</h4>
                    <div class="flex items-center gap-2 mt-0.5">
                        <p class="text-amber-500 text-xs tracking-wider font-semibold" aria-label="{{ $danhGia->so_sao }} trên 5 sao">
                            {{ str_repeat('★', (int) $danhGia->so_sao) }}{{ str_repeat('☆', 5 - (int) $danhGia->so_sao) }}
                            <span class="text-gray-500 font-normal ml-1">({{ $danhGia->so_sao }}.0)</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="text-right shrink-0">
                <time class="text-[11px] text-gray-400 flex items-center gap-1" datetime="{{ $danhGia->updated_at?->toISOString() }}">
                    <span class="material-symbols-outlined text-[13px]">schedule</span>
                    {{ $danhGia->updated_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                </time>
                @if($danhGia->created_at && $danhGia->updated_at && $danhGia->updated_at->gt($danhGia->created_at->addMinutes(5)))
                    <span class="text-[10px] text-gray-400 block mt-0.5 italic">(Đã chỉnh sửa)</span>
                @endif
            </div>
        </div>

        @if($danhGia->binh_luan)
            <div class="mt-3 text-sm text-gray-700 whitespace-pre-line leading-relaxed break-words [overflow-wrap:anywhere] bg-gray-50/60 p-3 rounded-xl border border-gray-100/70">
                {{ $danhGia->binh_luan }}
            </div>
        @endif

        {{-- Phản hồi đã có của quán --}}
        @if($danhGia->phan_hoi)
            <div class="mt-3.5 rounded-xl bg-gradient-to-r from-orange-50/80 to-amber-50/50 border border-orange-100/80 p-3.5 space-y-1">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-orange-700">
                        <span class="material-symbols-outlined text-[15px]">store</span>
                        <span>Phản hồi của quán</span>
                    </div>
                    @if($danhGia->phan_hoi_luc)
                        <time class="text-[11px] text-orange-600/70">{{ $danhGia->phan_hoi_luc->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</time>
                    @endif
                </div>
                <p class="whitespace-pre-line break-words text-xs text-gray-700 leading-relaxed [overflow-wrap:anywhere] pt-1">
                    {{ $danhGia->phan_hoi }}
                </p>
            </div>
        @endif

        {{-- Form trả lời / chỉnh sửa phản hồi --}}
        @php
            $reviewerName = $danhGia->nguoiDung?->ho_ten ?? 'quý khách';
            $soSao = (int) $danhGia->so_sao;
            $tenQuan = $quan->ten_quan;

            if ($soSao >= 4) {
                $pool = [
                    ['label' => '🌟 Cảm ơn lịch sự', 'text' => "Dạ {$tenQuan} chân thành cảm ơn {$reviewerName} đã dành thời gian đánh giá và ủng hộ quán ạ! Sự hài lòng của bạn là niềm vui rất lớn của đội ngũ quán. Chúc bạn luôn ngon miệng và mong sớm được đón tiếp bạn quay lại nhé! ❤️"],
                    ['label' => '💖 Thân thiện ấm áp', 'text' => "Dạ mến chào {$reviewerName} iu quý! Cả quán ai đọc được review của bạn cũng nở nụ cười rạng rỡ luôn đó ạ 🥰. Cảm ơn bạn đã yêu thích hương vị của quán. Lần tới ghé nhớ bảo tụi mình để quán phục vụ bạn thật chu đáo nha!"],
                    ['label' => '🎁 Tri ân & Ưu đãi', 'text' => "Cảm ơn {$reviewerName} đã tin yêu {$tenQuan}! Quán luôn có ưu đãi đặc biệt dành cho khách quen, lần tới ghé bạn nhớ nhắn quán để nhận thêm quà tặng tri ân bất ngờ nhé! ✨"],
                    ['label' => '⚡ Ngắn gọn súc tích', 'text' => "Dạ {$tenQuan} chân thành cảm ơn {$reviewerName} đã ghé ủng hộ quán ạ! Chúc bạn ngày mới vui vẻ và hẹn sớm gặp lại nhé!"],
                    ['label' => '🍲 Vinh hạnh phục vụ', 'text' => "Dạ {$tenQuan} rất vinh hạnh được đón tiếp {$reviewerName}! Chúc bạn luôn có những bữa ăn thật ngon miệng và ấm cúng cùng bạn bè, người thân tại quán ạ!"],
                    ['label' => '✨ Giữ trọn hương vị', 'text' => "Cảm ơn những lời khen nồng nhiệt từ {$reviewerName}! Bếp và các bạn phục vụ hứa sẽ luôn giữ vững phong độ này để đón tiếp bạn trong những lần tới ạ."],
                    ['label' => '🎉 Quán quen thân thương', 'text' => "Thấy {$reviewerName} chấm 5 sao là cả quán ngập tràn niềm vui rồi ạ! Cảm ơn bạn nhiều thiệt nhiều, nhớ ghé quán thường xuyên bạn nhé! ❤️"],
                    ['label' => '☕ Hẹn sớm gặp lại', 'text' => "Dạ {$tenQuan} chúc {$reviewerName} một ngày tràn đầy năng lượng! Cảm ơn bạn đã ghé thăm và hẹn sớm gặp lại bạn tại quán nha! ✨"],
                ];
            } elseif ($soSao === 3) {
                $pool = [
                    ['label' => '🤝 Cầu thị tiếp thu', 'text' => "Dạ chào {$reviewerName}, {$tenQuan} rất cảm ơn bạn đã ghé thăm và gửi lại chia sẻ quý báu. Quán đã ghi nhận chi tiết góp ý để cải thiện dịch vụ tốt hơn nữa. Rất mong sẽ được phục vụ bạn chu đáo hơn trong lần tới ạ!"],
                    ['label' => '🛠️ Cam kết cải thiện', 'text' => "Dạ chào bạn {$reviewerName}, tụi mình rất tiếc khi trải nghiệm lần này chưa trọn vẹn 100%. Quán đã họp lại ngay để chấn chỉnh và rút kinh nghiệm. Lần tới bạn ghé lại, tụi mình nhất định làm tốt hơn nha!"],
                    ['label' => '🎁 Mời trải nghiệm lại', 'text' => "Dạ {$tenQuan} xin cảm ơn góp ý chân thành từ {$reviewerName}! Quán đang hoàn thiện lại quy trình từng ngày. Mong bạn cho quán cơ hội được phục vụ bạn một bữa ăn thật hoàn hảo trong lần tới ạ!"],
                    ['label' => '⚡ Tiếp thu nhanh', 'text' => "Dạ {$tenQuan} chân thành cảm ơn góp ý của {$reviewerName} ạ! Quán xin tiếp thu để nâng cao chất lượng phục vụ tốt hơn."],
                    ['label' => '📋 Ghi nhận cải tiến', 'text' => "Dạ ban quản lý quán đã ghi nhận chi tiết đánh giá của {$reviewerName} vào sổ cải tiến chất lượng. Cảm ơn bạn đã giúp quán nhìn nhận vấn đề để hoàn thiện hơn ạ!"],
                    ['label' => '❤️ Mong bạn thông cảm', 'text' => "Tụi mình rất hiểu cảm giác chưa thật sự vừa ý của {$reviewerName}. Mong bạn rộng lòng thông cảm và tiếp tục ủng hộ để quán chứng minh sự thay đổi tích cực trong lần tới nhé!"],
                    ['label' => '🔍 Kiểm tra quy trình', 'text' => "Dạ {$tenQuan} đã rà soát lại quy trình chuẩn bị và ra món sau góp ý của {$reviewerName}. Chúc bạn nhiều sức khỏe và mong sớm đón tiếp bạn quay lại ạ!"],
                ];
            } else {
                $pool = [
                    ['label' => '🙇 Xin lỗi chân thành', 'text' => "Dạ kính chào {$reviewerName}, {$tenQuan} xin chân thành xin lỗi vì trải nghiệm chưa tốt của bạn trong lần ghé quán vừa qua ạ. Quán hoàn toàn nhận trách nhiệm và đã làm việc ngay với bộ phận liên quan để khắc phục dứt điểm. Rất mong nhận được sự lượng thứ từ bạn ạ."],
                    ['label' => '🛠️ Cam kết xử lý', 'text' => "Dạ chào bạn {$reviewerName}, tụi mình thật sự rất buồn và áy náy khi chưa mang đến cho bạn trải nghiệm như ý. Ban quản lý quán đã trực tiếp chấn chỉnh lại quy trình phục vụ. Quán chân thành tiếp thu và xin lỗi bạn rất nhiều ạ!"],
                    ['label' => '🎁 Chuộc lỗi & Đền bù', 'text' => "Dạ chào {$reviewerName}, {$tenQuan} vô cùng tiếc vì sơ sót lần này. Quán rất mong có thể liên hệ trực tiếp với bạn để xin lỗi và gửi tặng một ưu đãi/voucher chuộc lỗi. Rất mong bạn rộng lòng cho quán cơ hội được phục vụ bạn chu đáo hơn ạ!"],
                    ['label' => '📞 Hỗ trợ trực tiếp', 'text' => "Dạ {$tenQuan} xin thành thật xin lỗi {$reviewerName} về trải nghiệm không vui vừa qua. Quán rất mong được bạn nhắn tin hoặc gọi trực tiếp để quán lắng nghe và hỗ trợ giải quyết thỏa đáng nhất ạ!"],
                    ['label' => '⚡ Nhận lỗi nghiêm túc', 'text' => "Dạ quán xin cúi đầu nhận lỗi cùng {$reviewerName}. Sự cố lần này là bài học lớn cho đội ngũ quán, chúng mình đã xử lý triệt để và cam kết không tái diễn."],
                    ['label' => '🤝 Lắng nghe khắc phục', 'text' => "Dạ {$tenQuan} xin chân thành cảm ơn {$reviewerName} đã thẳng thắn phản ánh. Quán đã tiến hành đào tạo lại ca trực và mong có cơ hội được chuộc lỗi cùng bạn trong lần ghé tới ạ."],
                ];
            }

            // Phân bổ xoay vòng thông minh theo review ID để mỗi đánh giá trong danh sách có 5 mẫu khác nhau hoàn toàn
            $countPool = count($pool);
            $startIndex = (abs((int) $danhGia->id) * 3) % $countPool;
            $quickReplies = [];
            for ($i = 0; $i < min(5, $countPool); $i++) {
                $quickReplies[] = $pool[($startIndex + $i) % $countPool];
            }
        @endphp

        <form method="POST" action="{{ route('chu-quan.quan.danh-gia.reply', ['slug' => $quan->slug, 'reviewId' => $danhGia->id]) }}" class="mt-4 space-y-3 pt-3 border-t border-slate-100">
            @csrf
            @method('PUT')
            <div class="flex flex-wrap items-center justify-between gap-2">
                <label for="phan-hoi-{{ $danhGia->id }}" class="block text-xs font-bold text-slate-800">
                    {{ $danhGia->phan_hoi ? 'Chỉnh sửa câu trả lời của quán' : 'Trả lời khách hàng này' }}
                </label>
                <div class="flex items-center gap-2">
                    {{-- Nút AI Gợi ý trả lời (Bấm là tự động phân tích và điền ngay lập tức) --}}
                    <button type="button"
                            id="btn-ai-suggest-{{ $danhGia->id }}"
                            onclick="suggestAiReply('{{ $quan->slug }}', '{{ $danhGia->id }}', this)"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-xl transition border border-purple-200/90 cursor-pointer shadow-2xs group active:scale-95">
                        <span class="material-symbols-outlined text-[16px] text-purple-600 group-hover:rotate-12 transition-transform">auto_awesome</span>
                        <span>✨ AI Gợi ý trả lời</span>
                    </button>

                    {{-- Nút Đổi câu khác (chỉ xuất hiện sau khi AI gợi ý, bấm để xoay vòng tức thì sang kiểu kế tiếp) --}}
                    <button type="button"
                            id="btn-cycle-reply-{{ $danhGia->id }}"
                            onclick="cycleNextAiReply('{{ $danhGia->id }}')"
                            class="hidden inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition border border-indigo-200/90 cursor-pointer shadow-2xs active:scale-95"
                            title="Đổi ngay sang câu trả lời phong cách khác trong danh sách AI đề xuất">
                        <span class="material-symbols-outlined text-[15px] text-indigo-600">cached</span>
                        <span>Đổi câu khác (<span id="cycle-count-{{ $danhGia->id }}">Kiểu 1/6</span>)</span>
                    </button>
                </div>
            </div>

            {{-- Thanh Gợi ý Trả lời Nhanh Tức Thì (Mẫu có sẵn) --}}
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-amber-500">flash_on</span>
                        <span>Mẫu trả lời nhanh:</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-medium">Bấm để điền ngay</span>
                </div>
                <div id="quick-replies-container-{{ $danhGia->id }}" class="flex flex-wrap items-center gap-1.5">
                    @foreach($quickReplies as $qr)
                        <button type="button"
                                onclick="applyQuickReply('{{ $danhGia->id }}', @js($qr['text']), '{{ $qr['label'] }}')"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-white hover:bg-amber-50 hover:text-amber-900 hover:border-amber-300 border border-slate-200 text-slate-700 transition cursor-pointer shadow-2xs flex items-center gap-1">
                            <span>{{ $qr['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Thanh Trạng thái AI Thông minh: Tự hiểu Khen/Chê, vấn đề thực tế & Điều khiển phong cách --}}
            <div id="ai-smart-status-{{ $danhGia->id }}" class="hidden p-3 rounded-2xl bg-gradient-to-r from-purple-50/90 via-slate-50 to-indigo-50/80 border border-purple-200/90 shadow-2xs space-y-2.5 transition-all duration-300">
                <div class="flex flex-wrap items-center justify-between gap-2.5">
                    {{-- Phân loại Khen / Chê & Vấn đề nhận diện --}}
                    <div class="flex flex-wrap items-center gap-2 min-w-0">
                        {{-- Badge Khen / Chê --}}
                        <span id="ai-sentiment-badge-{{ $danhGia->id }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold shadow-2xs">
                            <!-- Sẽ được điền tự động: 🌟 Khách khen ngợi / ⚠️ Khách phàn nàn / 💡 Khách góp ý -->
                        </span>

                        {{-- Vấn đề cụ thể AI tự hiểu --}}
                        <div class="text-xs text-slate-700 font-medium flex items-center gap-1 truncate">
                            <span class="text-slate-400 shrink-0">AI hiểu:</span>
                            <span id="ai-issue-text-{{ $danhGia->id }}" class="font-bold text-slate-900 truncate">...</span>
                        </div>
                    </div>

                    {{-- Phong cách hiện tại & Nút đổi câu nhanh --}}
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span id="ai-active-style-label-{{ $danhGia->id }}" class="text-[11px] font-bold text-purple-800 bg-white px-2.5 py-1 rounded-lg border border-purple-200 shadow-2xs">
                            <!-- Kiểu 1/6 • Lịch sự & Chuẩn mực -->
                        </span>
                        <button type="button"
                                onclick="cycleNextAiReply('{{ $danhGia->id }}')"
                                class="px-2.5 py-1 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition flex items-center gap-1 cursor-pointer shadow-xs active:scale-95"
                                title="Đổi ngay sang kiểu tiếp theo trong các kiểu đề xuất">
                            <span class="material-symbols-outlined text-[14px]">sync</span>
                            <span>Đổi câu</span>
                        </button>
                    </div>
                </div>

                {{-- Các viên thuốc (pills) cho từng phong cách: 1 click là đổi ngay, hiển thị rõ kiểu đang áp dụng --}}
                <div class="flex items-center justify-between gap-2 pt-1 border-t border-purple-100/80 text-[11px]">
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 scrollbar-none" id="ai-styles-pills-{{ $danhGia->id }}">
                        <!-- Render danh sách phong cách: Lịch sự, Thân thiện, Tri ân, Trực diện... -->
                    </div>
                    <button type="button"
                            onclick="suggestAiReply('{{ $quan->slug }}', '{{ $danhGia->id }}', this, true)"
                            class="shrink-0 text-slate-500 hover:text-purple-700 text-[10.5px] font-semibold flex items-center gap-0.5 cursor-pointer transition ml-2"
                            title="Tạo mới một bộ phản hồi hoàn toàn khác">
                        <span class="material-symbols-outlined text-[13px]">refresh</span>
                        <span>Sinh bộ mới</span>
                    </button>
                </div>
            </div>

            <textarea id="phan-hoi-{{ $danhGia->id }}"
                      name="phan_hoi_{{ $danhGia->id }}"
                      rows="3"
                      maxlength="2000"
                      required
                      class="w-full rounded-xl border border-slate-200 p-3.5 text-xs sm:text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition leading-relaxed bg-white text-slate-800"
                      placeholder="Viết phản hồi chân thành gửi tới khách hàng...">{{ old('phan_hoi_'.$danhGia->id, $danhGia->phan_hoi) }}</textarea>

            {{-- Thông báo trạng thái đã áp dụng gợi ý --}}
            <div id="applied-hint-{{ $danhGia->id }}" class="hidden text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-3 py-1.5 rounded-xl flex items-center justify-between gap-2 shadow-2xs">
                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="material-symbols-outlined text-sm text-emerald-600 shrink-0">check_circle</span>
                    <span class="hint-text truncate">Đã áp dụng gợi ý</span>
                </div>
                <span class="text-[10px] text-emerald-600 font-normal shrink-0">Bấm "Gửi phản hồi" bên dưới để hoàn tất</span>
            </div>

            @error('phan_hoi_'.$danhGia->id)
                <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
            @enderror
            <div class="flex items-center justify-end">
                <button type="submit" class="rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:brightness-110 shadow-xs transition cursor-pointer flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">send</span>
                    <span>{{ $danhGia->phan_hoi ? 'Cập nhật phản hồi' : 'Gửi phản hồi cho khách' }}</span>
                </button>
            </div>
        </form>
    </article>
@empty
    <div class="text-center py-10 px-4 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
        <span class="material-symbols-outlined text-4xl text-gray-300 block mb-2">rate_review</span>
        <p class="text-sm font-semibold text-gray-600">Quán chưa có đánh giá nào từ thực khách</p>
        <p class="text-xs text-gray-400 mt-1">Khi khách hàng thưởng thức và gửi đánh giá, các phản hồi sẽ hiển thị tự động tại đây tức thì.</p>
    </div>
@endforelse
