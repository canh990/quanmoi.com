@extends('admin.layout')

@section('title', 'Công Cụ AI Tools - Quán Mới Admin')
@section('page-title', 'AI Assist Tools cho Trợ Lý Admin')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold px-3 py-1 rounded-full inline-block mb-3">AI Powered</span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-[32px] text-purple-400">psychology</span>
                Công Cụ Trợ Lý AI (AI Tools)
            </h1>
            <p class="text-purple-200 text-sm mt-2">Hỗ trợ tự động tạo nội dung mô tả SEO, tối ưu bài viết blog, tóm tắt đánh giá quán và viết bài tiếp thị.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Tool 1: SEO Writer --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <h3 class="font-bold text-slate-900 text-base mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">auto_fix_high</span>
                Tạo Mô Tả SEO Tự Động
            </h3>
            <p class="text-xs text-slate-500 mb-4">Nhập tên quán hoặc chủ đề để AI tự động sinh thẻ meta description tối ưu chuẩn SEO.</p>

            <form id="ai-seo-form" class="space-y-3">
                @csrf
                <textarea id="ai-prompt" rows="3" placeholder="Nhập tên quán, khu vực hoặc từ khóa... (ví dụ: Phở Thìn Hà Nội khu Quận 1 chuẩn vị)" required class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none"></textarea>
                <button type="button" onclick="generateAiSeo()" class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">smart_toy</span>
                    Tạo Nội Dung Với AI
                </button>
            </form>

            <div id="ai-result-box" class="mt-4 p-4 rounded-xl bg-purple-50 border border-purple-200 text-purple-900 text-xs hidden">
                <p class="font-bold text-purple-700 mb-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span> Kết quả gợi ý từ AI:
                </p>
                <div id="ai-result-content" class="leading-relaxed"></div>
            </div>
        </div>

        {{-- Tool 2: Content Optimization --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <h3 class="font-bold text-slate-900 text-base mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">summarize</span>
                Tóm Tắt Đánh Giá Quán
            </h3>
            <p class="text-xs text-slate-500 mb-4">Tổng hợp tự động các phản hồi của thực khách để đưa ra đánh giá tổng quan về chất lượng món ăn và dịch vụ.</p>
            <div class="p-8 text-center text-xs text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                Chức năng tóm tắt đánh giá tự động đang trong tiến trình kết nối API LLM.
            </div>
        </div>
    </div>
</div>

<script>
    function generateAiSeo() {
        const prompt = document.getElementById('ai-prompt').value;
        if (!prompt) return alert('Vui lòng nhập từ khóa hoặc chủ đề!');

        const resultBox = document.getElementById('ai-result-box');
        const resultContent = document.getElementById('ai-result-content');

        resultBox.classList.remove('hidden');
        resultContent.innerText = 'Đang suy nghĩ và tạo nội dung...';

        fetch('{{ route("admin.ai-tools.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ prompt: prompt })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                resultContent.innerText = data.result;
            } else {
                resultContent.innerText = 'Đã có lỗi xảy ra.';
            }
        })
        .catch(() => {
            resultContent.innerText = 'Lỗi kết nối tới máy chủ AI.';
        });
    }
</script>
@endsection
