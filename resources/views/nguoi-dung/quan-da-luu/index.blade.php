@extends('layouts.app')
@section('title', 'Quán đã lưu - Quán Mới')

@push('styles')
<style>
    .haptic-button:active { transform: scale(0.98); }
    .tonal-elevation { box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05); }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')
<main class="max-w-[1200px] mx-auto px-container-margin py-stack-lg min-h-[819px] pt-6">
    <div class="w-full">
        <div class="w-full">
            <header class="mb-stack-lg flex flex-col md:flex-row md:justify-between md:items-end gap-4">
                <div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface">Quán đã lưu</h1>
                    <p class="font-body-lg text-body-lg text-on-surface-variant mt-1">Khám phá lại những địa điểm yêu thích của bạn.</p>
                </div>
                <div class="flex gap-stack-sm">
                    <button class="flex items-center gap-2 px-4 py-2 border border-outline rounded-full font-label-md text-label-md hover:bg-surface-container transition-colors haptic-button">
                        <span class="material-symbols-outlined text-[18px]">filter_list</span>
                        Bộ lọc
                    </button>
                    <button class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-full font-label-md text-label-md shadow-sm haptic-button">
                        <span class="material-symbols-outlined text-[18px]">map</span>
                        Xem bản đồ
                    </button>
                </div>
            </header>

            <!-- Categories / Filter Pills -->
            <div class="flex gap-stack-sm mb-stack-lg overflow-x-auto pb-2 no-scrollbar">
                <button class="px-5 py-2 bg-primary-container text-white rounded-full font-label-md text-label-md whitespace-nowrap">Tất cả ({{ $quanDaLuu->count() }})</button>
                <button class="px-5 py-2 bg-white border border-outline-variant text-on-surface-variant rounded-full font-label-md text-label-md whitespace-nowrap hover:border-primary transition-colors">Cà phê</button>
                <button class="px-5 py-2 bg-white border border-outline-variant text-on-surface-variant rounded-full font-label-md text-label-md whitespace-nowrap hover:border-primary transition-colors">Nhà hàng Âu</button>
                <button class="px-5 py-2 bg-white border border-outline-variant text-on-surface-variant rounded-full font-label-md text-label-md whitespace-nowrap hover:border-primary transition-colors">Quán nhậu</button>
                <button class="px-5 py-2 bg-white border border-outline-variant text-on-surface-variant rounded-full font-label-md text-label-md whitespace-nowrap hover:border-primary transition-colors">Tráng miệng</button>
            </div>

            <!-- Bento-ish Grid of Cards -->
            <!-- Responsive Wrapper -->
            <div>
                <!-- Desktop Bento Grid (Hidden on Mobile) -->
                <div class="hidden md:block">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-gutter lg:gap-stack-md" id="saved-places-grid-desktop">
                        @forelse($quanDaLuu as $quan)
                        <article id="quan-card-desktop-{{ $quan->id }}" class="bg-surface-card rounded-xl overflow-hidden tonal-elevation group cursor-pointer transition-transform hover:-translate-y-1">
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="{{ $quan->hinhAnh->first()->url ?? ($quan->anh_bia ?? 'https://placehold.co/600x400?text=No+Image') }}"/>
                                <button type="button" onclick="toggleSave(this, '{{ $quan->id }}')" class="absolute top-3 right-3 w-10 h-10 bg-white/80 backdrop-blur-md rounded-full flex items-center justify-center text-red-500 haptic-button z-10 transition-colors">
                                    <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                                </button>
                                <a href="{{ route('quan.detail', $quan->slug) }}" class="absolute inset-0 z-0"></a>
                                <div class="absolute bottom-3 left-3 px-3 py-1 bg-black/60 backdrop-blur-sm text-white rounded-lg font-label-sm text-label-sm flex items-center gap-1 z-10 pointer-events-none">
                                    <span class="material-symbols-outlined text-[14px] text-yellow-400" style="font-variation-settings: 'FILL' 1;">star</span>
                                    4.9 (1.2k)
                                </div>
                            </div>
                            <div class="p-4 relative">
                                <a href="{{ route('quan.detail', $quan->slug) }}" class="absolute inset-0 z-0"></a>
                                <div class="flex items-center gap-1 mb-1 relative z-10 pointer-events-none">
                                    <h3 class="font-title-md text-title-md text-on-surface truncate">{{ $quan->ten_quan }}</h3>
                                    @if($quan->is_noi_bat)
                                    <span class="material-symbols-outlined text-tick-xanh text-[18px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1 text-on-surface-variant mb-3 relative z-10 pointer-events-none">
                                    <span class="material-symbols-outlined text-[18px]">location_on</span>
                                    <p class="font-body-sm text-body-sm truncate">{{ $quan->ten_quan_huyen ?? ($quan->dia_chi_chi_tiet ?? 'Đang cập nhật') }}</p>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t border-outline-variant relative z-10">
                                    <span class="font-label-md text-label-md text-secondary uppercase tracking-wider">{{ $quan->loai_hinh_kinh_doanh ?? 'Chưa rõ' }}</span>
                                    <a href="{{ route('quan.detail', $quan->slug) }}" class="text-primary font-label-md text-label-md hover:underline relative z-20">Chi tiết</a>
                                </div>
                            </div>
                        </article>
                        @empty
                        <div class="col-span-full py-12 text-center text-on-surface-variant empty-state-desktop">
                            <span class="material-symbols-outlined text-[48px] mb-2 text-red-300 opacity-60">favorite_border</span>
                            <p class="font-body-lg text-body-lg">Bạn chưa lưu địa điểm nào.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Mobile List View (Hidden on Desktop) -->
                <div class="block md:hidden">
                    <div class="flex flex-col gap-stack-md" id="saved-places-list-mobile">
                        @forelse($quanDaLuu as $quan)
                        <div id="quan-card-mobile-{{ $quan->id }}" class="bg-surface-card rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.05)] flex p-stack-sm gap-stack-md active:scale-95 transition-transform duration-200 cursor-pointer overflow-hidden border border-transparent hover:border-primary/10">
                            <div class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0 relative">
                                <img class="w-full h-full object-cover" src="{{ $quan->hinhAnh->first()->url ?? ($quan->anh_bia ?? 'https://placehold.co/600x400?text=No+Image') }}"/>
                                <a href="{{ route('quan.detail', $quan->slug) }}" class="absolute inset-0 z-10"></a>
                            </div>
                            <div class="flex-grow py-1 flex flex-col justify-between relative">
                                <a href="{{ route('quan.detail', $quan->slug) }}" class="absolute inset-0 z-0"></a>
                                <div class="relative z-10">
                                    <div class="flex justify-between items-start">
                                        <h3 class="font-title-md text-title-md text-main flex items-center gap-1 max-w-[150px] truncate">
                                            {{ $quan->ten_quan }}
                                            @if($quan->is_noi_bat)
                                            <span class="material-symbols-outlined text-tick-xanh text-[18px]" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                                            @endif
                                        </h3>
                                        <button type="button" onclick="toggleSave(this, '{{ $quan->id }}')" class="material-symbols-outlined text-primary-container active:scale-110 transition-transform relative z-20" style="font-variation-settings: 'FILL' 1;">favorite</button>
                                    </div>
                                    <p class="font-body-sm text-body-sm text-text-muted truncate max-w-[200px]">{{ $quan->loai_hinh_kinh_doanh ?? 'Chưa rõ' }} • {{ $quan->ten_quan_huyen ?? 'Đang cập nhật' }}</p>
                                </div>
                                <div class="flex items-center justify-between mt-auto relative z-10 pointer-events-none">
                                    <div class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[#FFB800] text-[18px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="font-label-md text-label-md text-main">4.8</span>
                                        <span class="font-label-sm text-label-sm text-text-muted">(2.1k+)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-12 text-center text-on-surface-variant empty-state-mobile">
                            <span class="material-symbols-outlined text-[48px] mb-2 text-red-300 opacity-60">favorite_border</span>
                            <p class="font-body-lg text-body-lg">Bạn chưa lưu địa điểm nào.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- View More Section -->
            @if($quanDaLuu->count() > 0)
            <div class="mt-stack-lg flex flex-col items-center gap-4 py-8 border-t border-dashed border-outline-variant">
                <p class="font-body-lg text-body-lg text-on-surface-variant">Bạn có tổng cộng {{ $quanDaLuu->count() }} địa điểm đã lưu.</p>
            </div>
            @endif
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    async function toggleSave(btn, quanId) {
        try {
            const res = await fetch(`/quan-da-luu/${quanId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            if (data.success) {
                if (data.status === 'unsaved') {
                    // Remove both desktop and mobile cards from the list with a nice animation
                    const cardDesktop = document.getElementById(`quan-card-desktop-${quanId}`);
                    const cardMobile = document.getElementById(`quan-card-mobile-${quanId}`);
                    
                    const removeCard = (card) => {
                        if (card) {
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                card.remove();
                                // Check if grid is empty
                                const gridDesktop = document.getElementById('saved-places-grid-desktop');
                                const gridMobile = document.getElementById('saved-places-list-mobile');
                                
                                if (gridDesktop && gridDesktop.querySelectorAll('article').length === 0) {
                                    gridDesktop.innerHTML = `
                                    <div class="col-span-full py-12 text-center text-on-surface-variant empty-state-desktop">
                                        <span class="material-symbols-outlined text-[48px] mb-2 text-red-300 opacity-60">favorite_border</span>
                                        <p class="font-body-lg text-body-lg">Bạn chưa lưu địa điểm nào.</p>
                                    </div>
                                    `;
                                }
                                if (gridMobile && gridMobile.querySelectorAll('div[id^="quan-card-mobile-"]').length === 0) {
                                    gridMobile.innerHTML = `
                                    <div class="py-12 text-center text-on-surface-variant empty-state-mobile">
                                        <span class="material-symbols-outlined text-[48px] mb-2 text-red-300 opacity-60">favorite_border</span>
                                        <p class="font-body-lg text-body-lg">Bạn chưa lưu địa điểm nào.</p>
                                    </div>
                                    `;
                                }
                            }, 300);
                        }
                    };
                    
                    removeCard(cardDesktop);
                    removeCard(cardMobile);
                } else {
                    // This shouldn't normally happen on the saved places page unless they untoggle and toggle again very fast
                }
            }
        } catch (err) {
            console.error(err);
            alert('Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }
</script>
@endpush
