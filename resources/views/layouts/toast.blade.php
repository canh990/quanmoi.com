<!-- Global Toast Notification Container (Góc trên bên phải chuẩn Hình 3) -->
<div id="global-toast-container" class="fixed top-20 right-4 sm:right-6 z-[99999] flex flex-col gap-3 pointer-events-none max-w-sm w-[calc(100vw-32px)] sm:w-96" aria-live="polite" aria-atomic="true">
</div>

<style>
    @keyframes toastHeartPop {
        0% { transform: scale(0.6); }
        45% { transform: scale(1.22); }
        75% { transform: scale(0.92); }
        100% { transform: scale(1); }
    }
    .toast-heart-pop {
        animation: toastHeartPop 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
</style>

<template id="global-toast-template">
    <div class="toast-item pointer-events-auto transform translate-x-6 opacity-0 scale-95 transition-all duration-300 ease-out bg-slate-900 text-white rounded-2xl p-4 shadow-2xl border border-slate-700/80 flex items-start gap-3.5 overflow-hidden relative group hover:border-amber-400/80">
        <!-- Toast Type Indicator Strip -->
        <div class="toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-emerald-500"></div>

        <!-- Toast Visual (Icon or Restaurant Thumbnail) -->
        <div class="toast-visual-container relative shrink-0 mt-0.5">
            <div class="toast-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center font-bold transition-transform duration-300 group-hover:scale-105" style="background-color: #10b981 !important; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.45) !important;">
                <span class="material-symbols-outlined text-[22px] toast-icon" style="color: #ffffff !important; font-variation-settings: 'FILL' 1;">check_circle</span>
            </div>
            <div class="toast-thumb-wrap hidden w-11 h-11 rounded-xl overflow-hidden ring-2 ring-emerald-400 shadow-sm relative">
                <img src="" alt="" class="toast-thumb-img w-full h-full object-cover" />
            </div>
            <div class="toast-thumb-badge hidden absolute -bottom-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center ring-2 ring-slate-900 shadow-xs" style="background-color: #10b981 !important; color: #ffffff !important;">
                <span class="material-symbols-outlined text-[12px] toast-badge-icon" style="font-variation-settings: 'FILL' 1;">favorite</span>
            </div>
        </div>

        <!-- Toast Text Content -->
        <div class="flex-1 min-w-0 pr-1">
            <div class="flex items-center justify-between gap-2 mb-1">
                <span class="toast-eyebrow-text text-[10px] font-bold uppercase tracking-wider text-amber-400 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">notifications_active</span>
                    <span>THÔNG BÁO MỚI</span>
                </span>
                <span class="text-[10px] text-slate-400 whitespace-nowrap">Vừa xong</span>
            </div>
            <h4 class="toast-title font-bold text-white text-[13px] leading-snug mb-0.5 truncate"></h4>
            <p class="toast-message text-slate-300 text-[11.5px] leading-relaxed line-clamp-2"></p>
            <div class="toast-action-container mt-2.5 hidden">
                <a href="#" class="toast-action-link inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500 hover:bg-amber-400 text-slate-950 text-[11px] font-bold transition-all duration-200 shadow-xs active:scale-95">
                    <span class="toast-action-text">Xem chi tiết</span>
                    <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Close Button -->
        <button type="button" class="toast-close-btn p-1 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition-colors shrink-0 -mr-1 -mt-1 cursor-pointer" aria-label="Đóng thông báo">
            <span class="material-symbols-outlined text-[16px]">close</span>
        </button>

        <!-- Progress Bar (Auto-dismiss countdown) -->
        <div class="absolute bottom-0 left-0 right-0 h-[2.5px] bg-slate-800 overflow-hidden">
            <div class="toast-progress-bar h-full w-full bg-gradient-to-r from-amber-400 to-emerald-400 origin-left transition-all ease-linear"></div>
        </div>
    </div>
</template>

<script>
(function() {
    window.showToast = function(options) {
        if (typeof options === 'string') {
            options = { title: options };
        }

        const {
            title = 'Thông báo',
            message = '',
            type = 'success', // 'success', 'saved', 'unsaved', 'error', 'warning', 'info'
            eyebrow = null,
            image = null,
            actionText = null,
            actionUrl = null,
            duration = 3600
        } = options;

        const container = document.getElementById('global-toast-container');
        const template = document.getElementById('global-toast-template');
        if (!container || !template) return;

        // Deduplicate identical toasts appearing in quick succession (< 2s)
        const cleanTitle = (title || '').trim();
        const cleanMsg = (message || '').trim();
        const existingToasts = container.querySelectorAll('.toast-item');
        for (let i = 0; i < existingToasts.length; i++) {
            const exTitle = existingToasts[i].querySelector('.toast-title')?.textContent?.trim() || '';
            const exMsg = existingToasts[i].querySelector('.toast-message')?.textContent?.trim() || '';
            if (exTitle === cleanTitle && exMsg === cleanMsg) {
                return; // Suppress duplicate toast notification
            }
        }

        // If showing a save/unsave toast, immediately replace any previous save/unsave toast
        if (type === 'saved' || type === 'unsaved') {
            container.querySelectorAll('.toast-item[data-toast-type="saved"], .toast-item[data-toast-type="unsaved"]').forEach(el => el.remove());
        }

        // Keep at most 2 active toasts so rapid clicks stay clean
        const activeToasts = container.querySelectorAll('.toast-item');
        if (activeToasts.length >= 2) {
            activeToasts[0].remove();
        }

        const toast = template.content.firstElementChild.cloneNode(true);
        toast.dataset.toastType = type;
        const eyebrowTextEl = toast.querySelector('.toast-eyebrow-text');
        const titleEl = toast.querySelector('.toast-title');
        const msgEl = toast.querySelector('.toast-message');
        const iconWrap = toast.querySelector('.toast-icon-wrap');
        const iconEl = toast.querySelector('.toast-icon');
        const thumbWrap = toast.querySelector('.toast-thumb-wrap');
        const thumbImg = toast.querySelector('.toast-thumb-img');
        const thumbBadge = toast.querySelector('.toast-thumb-badge');
        const badgeIcon = toast.querySelector('.toast-badge-icon');
        const indicator = toast.querySelector('.toast-indicator');
        const progressBar = toast.querySelector('.toast-progress-bar');
        const actionContainer = toast.querySelector('.toast-action-container');
        const actionLink = toast.querySelector('.toast-action-link');
        const actionTextEl = toast.querySelector('.toast-action-text');
        const closeBtn = toast.querySelector('.toast-close-btn');

        titleEl.textContent = title;
        if (message) {
            msgEl.textContent = message;
        } else {
            msgEl.remove();
        }

        // Configure Styling Based on Type with vivid, high-contrast icons
        if (type === 'bookmark') {
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-amber-500';
            iconWrap.className = 'toast-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center font-bold shrink-0';
            iconWrap.style.cssText = 'background-color: #f59e0b !important; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.45) !important;';
            iconEl.textContent = 'bookmark';
            iconEl.style.cssText = "color: #ffffff !important; font-variation-settings: 'FILL' 1;";
            iconEl.classList.add('toast-heart-pop');
            progressBar.className = 'toast-progress-bar h-full w-full bg-amber-500 origin-left transition-all ease-linear';
            if (eyebrowTextEl) eyebrowTextEl.innerHTML = `<span class="material-symbols-outlined text-[13px]">bookmark</span><span>${eyebrow || 'BÀI VIẾT ĐÃ LƯU'}</span>`;

            if (image) {
                iconWrap.classList.add('hidden');
                thumbWrap.classList.remove('hidden');
                thumbImg.src = image;
                thumbBadge.classList.remove('hidden');
                thumbBadge.className = 'toast-thumb-badge absolute -bottom-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center ring-2 ring-slate-900 shadow-xs toast-heart-pop';
                thumbBadge.style.cssText = 'background-color: #f59e0b !important; color: #ffffff !important;';
                badgeIcon.textContent = 'bookmark';
                badgeIcon.style.color = '#ffffff';
            }
        } else if (type === 'unbookmark') {
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-slate-500';
            iconWrap.className = 'toast-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center font-bold shrink-0';
            iconWrap.style.cssText = 'background-color: #475569 !important; box-shadow: 0 4px 14px rgba(71, 85, 105, 0.45) !important;';
            iconEl.textContent = 'bookmark_remove';
            iconEl.style.cssText = "color: #ffffff !important; font-variation-settings: 'FILL' 0;";
            progressBar.className = 'toast-progress-bar h-full w-full bg-slate-600 origin-left transition-all ease-linear';
            if (eyebrowTextEl) eyebrowTextEl.innerHTML = `<span class="material-symbols-outlined text-[13px]">bookmark_remove</span><span>${eyebrow || 'ĐÃ BỎ LƯU BÀI VIẾT'}</span>`;
        } else if (type === 'saved') {
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-rose-500';
            iconWrap.className = 'toast-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center font-bold shrink-0';
            iconWrap.style.cssText = 'background-color: #f43f5e !important; box-shadow: 0 4px 14px rgba(244, 63, 94, 0.45) !important;';
            iconEl.textContent = 'favorite';
            iconEl.style.cssText = "color: #ffffff !important; font-variation-settings: 'FILL' 1;";
            iconEl.classList.add('toast-heart-pop');
            progressBar.className = 'toast-progress-bar h-full w-full bg-rose-500 origin-left transition-all ease-linear';
            if (eyebrowTextEl) eyebrowTextEl.innerHTML = `<span class="material-symbols-outlined text-[13px]">favorite</span><span>${eyebrow || 'BỘ SƯU TẬP YÊU THÍCH'}</span>`;

            if (image) {
                iconWrap.classList.add('hidden');
                thumbWrap.classList.remove('hidden');
                thumbImg.src = image;
                thumbBadge.classList.remove('hidden');
                thumbBadge.className = 'toast-thumb-badge absolute -bottom-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center ring-2 ring-slate-900 shadow-xs toast-heart-pop';
                thumbBadge.style.cssText = 'background-color: #f43f5e !important; color: #ffffff !important;';
                badgeIcon.textContent = 'favorite';
                badgeIcon.style.color = '#ffffff';
            }
        } else if (type === 'unsaved') {
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-slate-500';
            iconWrap.className = 'toast-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center font-bold shrink-0';
            iconWrap.style.cssText = 'background-color: #475569 !important; box-shadow: 0 4px 14px rgba(71, 85, 105, 0.45) !important;';
            iconEl.textContent = 'favorite';
            iconEl.style.cssText = "color: #ffffff !important; font-variation-settings: 'FILL' 0;";
            progressBar.className = 'toast-progress-bar h-full w-full bg-slate-600 origin-left transition-all ease-linear';
            if (eyebrowTextEl) eyebrowTextEl.innerHTML = `<span class="material-symbols-outlined text-[13px]">heart_broken</span><span>${eyebrow || 'ĐÃ CẬP NHẬT DANH SÁCH'}</span>`;
        } else if (type === 'error') {
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-rose-500';
            iconWrap.className = 'toast-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center font-bold shrink-0';
            iconWrap.style.cssText = 'background-color: #f43f5e !important; box-shadow: 0 4px 14px rgba(244, 63, 94, 0.45) !important;';
            iconEl.textContent = 'error';
            iconEl.style.cssText = "color: #ffffff !important; font-variation-settings: 'FILL' 1;";
            progressBar.className = 'toast-progress-bar h-full w-full bg-rose-500 origin-left transition-all ease-linear';
            if (eyebrowTextEl) eyebrowTextEl.innerHTML = `<span class="material-symbols-outlined text-[13px]">error</span><span>CÓ LỖI XẢY RA</span>`;
        } else if (type === 'warning') {
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-amber-400';
            iconWrap.className = 'toast-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center font-bold shrink-0';
            iconWrap.style.cssText = 'background-color: #f59e0b !important; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.45) !important;';
            iconEl.textContent = 'warning';
            iconEl.style.cssText = "color: #ffffff !important; font-variation-settings: 'FILL' 1;";
            progressBar.className = 'toast-progress-bar h-full w-full bg-amber-400 origin-left transition-all ease-linear';
            if (eyebrowTextEl) eyebrowTextEl.innerHTML = `<span class="material-symbols-outlined text-[13px]">warning</span><span>${eyebrow || 'LƯU Ý'}</span>`;
        } else if (type === 'info') {
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-blue-500';
            iconWrap.className = 'toast-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center font-bold shrink-0';
            iconWrap.style.cssText = 'background-color: #3b82f6 !important; box-shadow: 0 4px 14px rgba(59, 130, 246, 0.45) !important;';
            iconEl.textContent = 'info';
            iconEl.style.cssText = "color: #ffffff !important; font-variation-settings: 'FILL' 1;";
            progressBar.className = 'toast-progress-bar h-full w-full bg-blue-500 origin-left transition-all ease-linear';
            if (eyebrowTextEl) eyebrowTextEl.innerHTML = `<span class="material-symbols-outlined text-[13px]">info</span><span>THÔNG BÁO</span>`;
        } else {
            // Default success
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-emerald-500';
            iconWrap.className = 'toast-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center font-bold shrink-0';
            iconWrap.style.cssText = 'background-color: #10b981 !important; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.45) !important;';
            iconEl.textContent = 'check_circle';
            iconEl.style.cssText = "color: #ffffff !important; font-variation-settings: 'FILL' 1;";
            progressBar.className = 'toast-progress-bar h-full w-full bg-emerald-500 origin-left transition-all ease-linear';
            if (eyebrowTextEl) eyebrowTextEl.innerHTML = `<span class="material-symbols-outlined text-[13px]">notifications_active</span><span>THÔNG BÁO MỚI</span>`;
        }

        // Action Link
        if (actionText && actionUrl) {
            actionContainer.classList.remove('hidden');
            actionLink.href = actionUrl;
            actionTextEl.textContent = actionText;
        }

        container.appendChild(toast);

        // Entrance animation
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-6', 'opacity-0', 'scale-95');
            toast.classList.add('translate-x-0', 'opacity-100', 'scale-100');

            // Start progress countdown bar
            progressBar.style.transitionDuration = `${duration}ms`;
            progressBar.style.width = '0%';
        });

        let isDismissed = false;
        let remainingTime = duration;
        let startTime = Date.now();
        let timer = null;

        const dismiss = () => {
            if (isDismissed) return;
            isDismissed = true;
            toast.classList.remove('translate-x-0', 'opacity-100', 'scale-100');
            toast.classList.add('translate-x-4', 'opacity-0', 'scale-95');
            setTimeout(() => toast.remove(), 300);
        };

        timer = setTimeout(dismiss, remainingTime);

        // Pause auto-dismiss when hovering
        toast.addEventListener('mouseenter', () => {
            if (isDismissed) return;
            clearTimeout(timer);
            const elapsed = Date.now() - startTime;
            remainingTime = Math.max(800, remainingTime - elapsed);
            const currentWidth = (remainingTime / duration) * 100;
            progressBar.style.transitionDuration = '0ms';
            progressBar.style.width = `${currentWidth}%`;
        });

        toast.addEventListener('mouseleave', () => {
            if (isDismissed) return;
            startTime = Date.now();
            progressBar.style.transitionDuration = `${remainingTime}ms`;
            progressBar.style.width = '0%';
            timer = setTimeout(dismiss, remainingTime);
        });

        closeBtn.addEventListener('click', () => {
            clearTimeout(timer);
            dismiss();
        });
    };

    // Auto-display Laravel session flash messages on page load
    document.addEventListener('DOMContentLoaded', () => {
        @if(session('success'))
            try {
                localStorage.removeItem('qm_blog_active_draft');
                sessionStorage.removeItem('qm_blog_active_draft');
                sessionStorage.removeItem('qm_blog_modal_open');
            } catch(e) {}

            // Nếu trên trang đã có thông báo ở trên đầu thì không hiện thêm popup toast để tránh trùng lặp
            const hasTopBanner = document.getElementById('blog-success-banner') || 
                                 document.getElementById('user-blog-success-banner') ||
                                 document.querySelector('.page-success-banner');
            if (!hasTopBanner) {
                window.showToast({
                    title: 'Thành công',
                    message: @json(session('success')),
                    type: 'success'
                });
            }
        @endif

        @if(session('error'))
            window.showToast({
                title: 'Có lỗi xảy ra',
                message: @json(session('error')),
                type: 'error'
            });
        @endif

        @if(session('status'))
            window.showToast({
                title: 'Thông báo',
                message: @json(session('status')),
                type: 'info'
            });
        @endif
    });
})();
</script>
