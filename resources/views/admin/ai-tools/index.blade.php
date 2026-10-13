@extends('admin.layout')

@section('title', 'Công Cụ AI Tools - Quán Mới Admin')
@section('page-title', 'AI Assist Tools cho Trợ Lý Admin')

@section('content')
<div class="space-y-6">
    <div class="bg-slate-900 text-white rounded-2xl p-6 border border-slate-800 shadow-xs relative">
        <div class="relative z-10 max-w-2xl">
            <span class="bg-slate-800 text-slate-300 border border-slate-700 text-xs font-bold px-3 py-1 rounded-full inline-flex items-center gap-1.5 mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span>Công cụ Tự Động Hóa</span>
            </span>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight flex items-center gap-2.5 text-white">
                <span class="material-symbols-outlined text-[26px] text-slate-300">smart_toy</span>
                Hỗ Trợ Biên Tập & Tối Ưu Nội Dung
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed">Hỗ trợ tự động tạo nội dung mô tả SEO, tối ưu hóa thông tin quán ăn, tóm tắt đánh giá thực khách theo tiêu chuẩn thương hiệu Quán Mới.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Tool 1: SEO Writer --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <h3 class="font-bold text-slate-900 text-base mb-1.5 flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-700 text-[20px]">edit_note</span>
                Tạo Mô Tả SEO Tự Động
            </h3>
            <p class="text-xs text-slate-500 mb-4">Nhập tên quán hoặc chủ đề để hệ thống tự động sinh thẻ meta description chuẩn SEO.</p>

            <form id="ai-seo-form" class="space-y-3">
                @csrf
                <textarea id="ai-prompt" rows="3" placeholder="Nhập tên quán, khu vực hoặc từ khóa... (ví dụ: Phở Thìn Hà Nội khu Quận 1 chuẩn vị)" required class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:border-slate-800 focus:ring-1 focus:ring-slate-800 outline-none transition-colors text-slate-900 font-medium"></textarea>
                <button type="button" onclick="generateAiSeo()" class="btn-enterprise btn-enterprise-dark w-full py-2.5 justify-center shadow-xs">
                    <span class="material-symbols-outlined text-[17px]">auto_fix_high</span>
                    <span>Tạo Nội Dung Ngay</span>
                </button>
            </form>

            <div id="ai-result-box" class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs hidden">
                <p class="font-bold text-slate-800 mb-1.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                    <span>Nội dung gợi ý:</span>
                </p>
                <div id="ai-result-content" class="leading-relaxed text-slate-700 bg-white p-3 rounded-lg border border-slate-200 font-medium select-all"></div>
            </div>
        </div>

        {{-- Tool 2: Content Optimization --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <h3 class="font-bold text-slate-900 text-base mb-1.5 flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-700 text-[20px]">summarize</span>
                Tóm Tắt Đánh Giá Quán
            </h3>
            <p class="text-xs text-slate-500 mb-4">Tổng hợp tự động các phản hồi của thực khách để đưa ra báo cáo tổng quan về chất lượng món ăn và dịch vụ.</p>
            <div class="p-8 text-center text-xs text-slate-500 bg-slate-50 rounded-xl border border-dashed border-slate-200 font-medium">
                Chức năng tóm tắt đánh giá tự động đang trong tiến trình kết nối dữ liệu máy chủ.
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
