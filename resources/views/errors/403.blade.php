@extends('layouts.app')

@section('title', '403 - Không có quyền truy cập')

@section('content')
<main class="flex-grow pt-24 pb-16 flex flex-col items-center justify-center min-h-screen relative overflow-hidden">
    {{-- Animated Background --}}
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-tertiary/5 via-background to-primary-fixed/10"></div>
        <div class="absolute top-20 -right-20 w-96 h-96 bg-tertiary/10 rounded-full blur-3xl animate-float-slow"></div>
        <div class="absolute bottom-20 -left-20 w-[400px] h-[400px] bg-primary/10 rounded-full blur-3xl animate-float-slow-reverse"></div>
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, #006e2f 1.5px, transparent 1.5px); background-size: 40px 40px;"></div>
    </div>

    <div class="container mx-auto px-4 flex flex-col items-center text-center relative z-10">
        {{-- 403 Graphic --}}
        <div class="relative mb-8">
            <h1 class="text-[120px] md:text-[180px] font-black leading-none tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-primary via-tertiary to-secondary drop-shadow-sm select-none" style="filter: drop-shadow(0 10px 20px rgba(160, 65, 0, 0.15));">
                403
            </h1>
            
            {{-- Floating icon over text --}}
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-24 h-24 md:w-36 md:h-36 bg-white/30 backdrop-blur-md rounded-full shadow-[0_10px_40px_rgba(0,110,47,0.15)] border border-white/50 flex items-center justify-center animate-bounce-slow">
                <span class="material-symbols-outlined text-[60px] md:text-[80px] text-tertiary" style="font-variation-settings: 'FILL' 1;">lock</span>
            </div>
        </div>

        {{-- Content --}}
        <h2 class="text-2xl md:text-4xl font-extrabold text-on-surface mb-4">
            Từ chối truy cập!
        </h2>
        
        <p class="text-[15px] md:text-[17px] text-on-surface-variant max-w-md mx-auto mb-10 leading-relaxed">
            {{ $exception->getMessage() ?: 'Bạn không có quyền truy cập trang Quản trị Admin. Vui lòng liên hệ quản trị viên nếu bạn nghĩ đây là lỗi.' }}
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-8 h-[52px] bg-primary text-white font-bold text-[15px] rounded-2xl shadow-[0_4px_16px_rgba(160,65,0,0.25)] hover:shadow-[0_8px_24px_rgba(160,65,0,0.35)] hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[20px]">home</span>
                Về Trang Chủ
            </a>
            <button onclick="window.history.back()" class="w-full sm:w-auto px-8 h-[52px] bg-white text-on-surface font-bold text-[15px] rounded-2xl border border-gray-200 shadow-sm hover:bg-gray-50 hover:shadow active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                Quay lại trang trước
            </button>
        </div>
    </div>
</main>
@endsection

@push('styles')
<style>
    @keyframes floatSlow {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -20px) scale(1.05); }
        66% { transform: translate(-15px, 15px) scale(0.95); }
    }
    @keyframes floatSlowReverse {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(-25px, 15px) scale(0.95); }
        66% { transform: translate(20px, -25px) scale(1.05); }
    }
    @keyframes bounceSlow {
        0%, 100% { transform: translate(-50%, -50%) translateY(0); }
        50% { transform: translate(-50%, -50%) translateY(-15px); }
    }
    .animate-float-slow { animation: floatSlow 12s ease-in-out infinite; }
    .animate-float-slow-reverse { animation: floatSlowReverse 15s ease-in-out infinite; }
    .animate-bounce-slow { animation: bounceSlow 4s ease-in-out infinite; }
</style>
@endpush
