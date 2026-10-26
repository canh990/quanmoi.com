<!-- Global Toast Notification Container -->
<div id="global-toast-container" class="fixed bottom-5 right-4 sm:bottom-6 sm:right-6 z-[99999999] flex flex-col-reverse gap-3 pointer-events-none max-w-[calc(100vw-2rem)] sm:max-w-[390px] w-full items-end" aria-live="polite" aria-atomic="true">
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
    .toast-item {
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), 
                    opacity 0.3s ease, 
                    max-height 0.35s cubic-bezier(0.16, 1, 0.3, 1), 
                    margin 0.35s ease, 
                    padding 0.35s ease;
        max-height: 200px;
        will-change: transform, opacity, max-height;
    }
    .toast-item.dismissing {
        opacity: 0 !important;
        transform: translateX(110%) scale(0.94) !important;
        max-height: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        border-width: 0 !important;
        box-shadow: none !important;
        overflow: hidden !important;
        pointer-events: none !important;
    }
</style>

<template id="global-toast-template">
    <div class="toast-item pointer-events-auto transform translate-y-4 opacity-0 scale-95 transition-all duration-300 ease-out bg-white/95 backdrop-blur-xl rounded-2xl p-3.5 sm:p-4 shadow-[0_16px_40px_-6px_rgba(25,28,29,0.18)] border border-outline-variant/60 flex items-start gap-3.5 overflow-hidden relative group w-full">
        <!-- Toast Type Indicator Strip -->
        <div class="toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-primary-container to-primary"></div>

        <!-- Toast Visual (Icon or Restaurant Thumbnail) -->
        <div class="toast-visual-container relative shrink-0 mt-0.5">
            <div class="toast-icon-wrap w-11 h-11 rounded-xl flex items-center justify-center shadow-xs transition-transform duration-300 group-hover:scale-105">
                <span class="material-symbols-outlined text-[22px] toast-icon">check_circle</span>
            </div>
            <div class="toast-thumb-wrap hidden w-12 h-12 rounded-xl overflow-hidden ring-2 ring-primary-fixed shadow-sm relative">
                <img src="" alt="" class="toast-thumb-img w-full h-full object-cover" />
            </div>
            <div class="toast-thumb-badge hidden absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-gradient-to-br from-primary-container to-primary text-white flex items-center justify-center ring-2 ring-white shadow-xs">
                <span class="material-symbols-outlined text-[12px] toast-badge-icon" style="font-variation-settings: 'FILL' 1;">favorite</span>
            </div>
        </div>

        <!-- Toast Text Content -->
        <div class="flex-1 min-w-0 pr-1">
            <div class="toast-eyebrow hidden items-center gap-1 text-[10px] font-extrabold uppercase tracking-wider text-primary mb-0.5">
                <span class="toast-eyebrow-text">Quán Mới • Yêu thích</span>
            </div>
            <h4 class="toast-title font-bold text-on-surface text-[14px] leading-snug mb-0.5 truncate"></h4>
            <p class="toast-message font-body-sm text-on-surface-variant text-[12.5px] leading-relaxed line-clamp-2"></p>
            <div class="toast-action-container mt-2.5 hidden">
                <a href="#" class="toast-action-link inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-fixed/70 hover:bg-primary text-primary hover:text-white text-[11.5px] font-bold transition-all duration-200 shadow-2xs active:scale-95">
                    <span class="toast-action-text">Xem chi tiết</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Close Button -->
        <button type="button" class="toast-close-btn p-1.5 text-on-surface-variant/60 hover:text-on-surface hover:bg-surface-container rounded-xl transition-colors shrink-0 -mr-1 -mt-1 cursor-pointer" aria-label="Đóng thông báo">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>

        <!-- Progress Bar (Auto-dismiss countdown) -->
        <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-surface-container overflow-hidden">
            <div class="toast-progress-bar h-full w-full bg-gradient-to-r from-primary-container to-primary origin-left transition-all ease-linear"></div>
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
            duration = 3800
        } = options;

        const container = document.getElementById('global-toast-container');
        const template = document.getElementById('global-toast-template');
        if (!container || !template) return;

        // Debounce: Nếu thông báo cùng nội dung vừa xuất hiện trong vòng 1 giây thì làm mới countdown của thông báo đó
        const existingToasts = Array.from(container.querySelectorAll('.toast-item:not(.dismissing)'));
        const duplicateToast = existingToasts.find(t => {
            const tTitle = t.querySelector('.toast-title')?.textContent;
            const tMsg = t.querySelector('.toast-message')?.textContent || '';
            return tTitle === title && tMsg === message;
        });

        if (duplicateToast && typeof duplicateToast._resetTimer === 'function') {
            duplicateToast._resetTimer();
            duplicateToast.classList.add('scale-105');
            setTimeout(() => duplicateToast.classList.remove('scale-105'), 200);
            return;
        }

        // Giữ tối đa 3 toast đang active để không che khuất màn hình
        if (existingToasts.length >= 3) {
            const oldestToast = existingToasts[0];
            if (oldestToast && typeof oldestToast._dismiss === 'function') {
                oldestToast._dismiss();
            } else if (oldestToast) {
                oldestToast.remove();
            }
        }

        const toast = template.content.firstElementChild.cloneNode(true);
        const eyebrowEl = toast.querySelector('.toast-eyebrow');
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

        // Configure Styling Based on Type
        if (type === 'saved') {
            toast.classList.add('bg-gradient-to-br', 'from-[#fff9f5]', 'via-white', 'to-white', 'border-primary-fixed', 'shadow-[0_16px_40px_-6px_rgba(160,65,0,0.18)]');
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-primary-container via-primary to-rose-500';
            iconWrap.className = 'toast-icon-wrap w-11 h-11 rounded-xl flex items-center justify-center shadow-xs bg-gradient-to-br from-primary-fixed to-[#ffe4d6] text-primary border border-primary/15';
            iconEl.textContent = 'favorite';
            iconEl.style.fontVariationSettings = "'FILL' 1";
            iconEl.classList.add('toast-heart-pop', 'text-rose-500');
            progressBar.className = 'toast-progress-bar h-full w-full bg-gradient-to-r from-primary-container via-primary to-rose-500 origin-left transition-all ease-linear';

            eyebrowEl.classList.remove('hidden');
            eyebrowEl.classList.add('inline-flex');
            eyebrowTextEl.textContent = eyebrow || 'Bộ sưu tập yêu thích';

            if (image) {
                iconWrap.classList.add('hidden');
                thumbWrap.classList.remove('hidden');
                thumbImg.src = image;
                thumbBadge.classList.remove('hidden');
                thumbBadge.className = 'toast-thumb-badge absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-gradient-to-br from-rose-500 to-primary text-white flex items-center justify-center ring-2 ring-white shadow-xs toast-heart-pop';
                badgeIcon.textContent = 'favorite';
            }
        } else if (type === 'unsaved') {
            toast.classList.add('border-outline-variant/60');
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-outline';
            iconWrap.className = 'toast-icon-wrap w-11 h-11 rounded-xl flex items-center justify-center shadow-xs bg-surface-container text-on-surface-variant border border-outline-variant/50';
            iconEl.textContent = 'favorite';
            iconEl.style.fontVariationSettings = "'FILL' 0";
            progressBar.className = 'toast-progress-bar h-full w-full bg-outline origin-left transition-all ease-linear';

            eyebrowEl.classList.remove('hidden');
            eyebrowEl.classList.add('inline-flex', 'text-on-surface-variant');
            eyebrowTextEl.textContent = eyebrow || 'Đã cập nhật danh sách';

            if (image) {
                iconWrap.classList.add('hidden');
                thumbWrap.classList.remove('hidden', 'ring-primary-fixed');
                thumbWrap.classList.add('ring-outline-variant/60', 'grayscale-[35%]', 'opacity-85');
                thumbImg.src = image;
                thumbBadge.classList.remove('hidden');
                thumbBadge.className = 'toast-thumb-badge absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-on-surface-variant text-white flex items-center justify-center ring-2 ring-white shadow-xs';
                badgeIcon.textContent = 'remove';
            }
        } else if (type === 'error') {
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-error';
            iconWrap.className = 'toast-icon-wrap w-11 h-11 rounded-xl flex items-center justify-center shadow-xs bg-error-container/60 text-error border border-error/15';
            iconEl.textContent = 'error';
            progressBar.className = 'toast-progress-bar h-full w-full bg-error origin-left transition-all ease-linear';
        } else if (type === 'warning') {
            toast.classList.add('bg-gradient-to-br', 'from-[#fff9f5]', 'to-white', 'border-primary-fixed');
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-primary-container';
            iconWrap.className = 'toast-icon-wrap w-11 h-11 rounded-xl flex items-center justify-center shadow-xs bg-primary-fixed/80 text-primary border border-primary/15';
            iconEl.textContent = 'lock_person';
            progressBar.className = 'toast-progress-bar h-full w-full bg-primary-container origin-left transition-all ease-linear';
            eyebrowEl.classList.remove('hidden');
            eyebrowEl.classList.add('inline-flex');
            eyebrowTextEl.textContent = eyebrow || 'Tài khoản Quán Mới';
        } else if (type === 'info') {
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-secondary';
            iconWrap.className = 'toast-icon-wrap w-11 h-11 rounded-xl flex items-center justify-center shadow-xs bg-secondary-fixed/50 text-secondary border border-secondary/15';
            iconEl.textContent = 'info';
            progressBar.className = 'toast-progress-bar h-full w-full bg-secondary origin-left transition-all ease-linear';
        } else {
            // Default success
            indicator.className = 'toast-indicator absolute left-0 top-0 bottom-0 w-1.5 bg-emerald-500';
            iconWrap.className = 'toast-icon-wrap w-11 h-11 rounded-xl flex items-center justify-center shadow-xs bg-emerald-50 text-emerald-600 border border-emerald-200';
            iconEl.textContent = 'check_circle';
            progressBar.className = 'toast-progress-bar h-full w-full bg-emerald-500 origin-left transition-all ease-linear';
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
            toast.classList.remove('translate-y-4', 'opacity-0', 'scale-95');
            toast.classList.add('translate-y-0', 'opacity-100', 'scale-100');

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
            toast.classList.add('dismissing');
            setTimeout(() => {
                if (toast.parentNode) toast.remove();
            }, 350);
        };

        toast._dismiss = dismiss;

        toast._resetTimer = () => {
            if (isDismissed) return;
            clearTimeout(timer);
            remainingTime = duration;
            startTime = Date.now();
            progressBar.style.transitionDuration = '0ms';
            progressBar.style.width = '100%';
            requestAnimationFrame(() => {
                progressBar.style.transitionDuration = `${duration}ms`;
                progressBar.style.width = '0%';
            });
            timer = setTimeout(dismiss, remainingTime);
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

        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
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

        @if(session('warning'))
            window.showToast({
                title: 'Cảnh báo',
                message: @json(session('warning')),
                type: 'warning'
            });
        @endif

        @if(session('status'))
            window.showToast({
                title: 'Thông báo',
                message: @json(session('status')),
                type: 'info'
            });
        @endif

        @if(session('info'))
            window.showToast({
                title: 'Thông tin',
                message: @json(session('info')),
                type: 'info'
            });
        @endif
    });
})();
</script>
