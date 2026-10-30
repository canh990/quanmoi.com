<!-- Global Toast Notification Container (Đồng bộ toàn website Quán Mới) -->
<div id="global-toast-container" class="qm-toast-container" aria-live="polite" aria-atomic="true"></div>
@if(session('success') || session('error') || session('error_conflict') || session('info') || session('status'))
<div class="hidden" aria-hidden="true">{{ session('success') }} {{ session('error') }} {{ session('error_conflict') }} {{ session('info') }} {{ session('status') }}</div>
@endif

<style>
    .qm-toast-container {
        position: fixed;
        top: 84px;
        right: 20px;
        z-index: 999999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        pointer-events: none;
        width: 380px;
        max-width: calc(100vw - 32px);
    }
    @media (max-width: 640px) {
        .qm-toast-container {
            top: 74px;
            right: 16px;
            left: 16px;
            width: auto;
            max-width: none;
        }
    }

    @keyframes toastHeartPop {
        0% { transform: scale(0.6); }
        45% { transform: scale(1.2); }
        75% { transform: scale(0.94); }
        100% { transform: scale(1); }
    }
    .toast-heart-pop {
        animation: toastHeartPop 0.42s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .qm-toast-card {
        pointer-events: auto;
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        width: 100%;
        padding: 15px 16px 17px 18px;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.95);
        box-shadow: 0 18px 42px -8px rgba(15, 23, 42, 0.14), 0 4px 12px -2px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        transform: translateX(20px) scale(0.96);
        opacity: 0;
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                    opacity 0.25s ease,
                    border-color 0.2s ease,
                    box-shadow 0.2s ease;
    }
    .qm-toast-card.is-visible {
        transform: translateX(0) scale(1);
        opacity: 1;
    }
    .qm-toast-card.is-dismissing {
        transform: translateX(24px) scale(0.95);
        opacity: 0;
        pointer-events: none;
    }

    .qm-toast-card .hidden {
        display: none !important;
    }

    /* Left accent strip */
    .qm-toast-indicator {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #10b981;
    }

    /* Visual / Thumbnail / Icon */
    .qm-toast-visual {
        position: relative;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .qm-toast-icon-wrap {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .qm-toast-icon-wrap .material-symbols-outlined {
        font-size: 22px !important;
        line-height: 1 !important;
        font-variation-settings: 'FILL' 1;
    }

    .qm-toast-thumb-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #ffe4e6;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
        background: #f8fafc;
    }
    .qm-toast-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .qm-toast-thumb-badge {
        position: absolute;
        bottom: -3px;
        right: -3px;
        width: 20px;
        height: 20px;
        border-radius: 9999px;
        background: #e11d48;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(225, 29, 72, 0.35);
    }
    .qm-toast-thumb-badge .material-symbols-outlined {
        font-size: 11px !important;
        line-height: 1 !important;
        color: #ffffff !important;
        font-variation-settings: 'FILL' 1;
    }

    /* Content */
    .qm-toast-body {
        flex: 1;
        min-width: 0;
    }
    .qm-toast-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 3px;
    }
    .qm-toast-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #059669;
    }
    .qm-toast-eyebrow .material-symbols-outlined {
        font-size: 13px !important;
        line-height: 1 !important;
        font-variation-settings: 'FILL' 1;
    }
    .qm-toast-time {
        font-size: 11px;
        font-weight: 500;
        color: #94a3b8;
        white-space: nowrap;
    }
    .qm-toast-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        margin: 0 0 2px 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .qm-toast-message {
        font-size: 12px;
        color: #475569;
        line-height: 1.45;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Action CTA */
    .qm-toast-action {
        margin-top: 9px;
    }
    .qm-toast-action-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 9999px;
        font-size: 11.5px;
        font-weight: 700;
        text-decoration: none;
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
        transition: all 0.18s ease;
    }
    .qm-toast-action-link:hover {
        background: #e11d48;
        color: #ffffff;
        border-color: #e11d48;
    }
    .qm-toast-action-link .material-symbols-outlined {
        font-size: 14px !important;
        line-height: 1 !important;
        transition: transform 0.18s ease;
    }
    .qm-toast-action-link:hover .material-symbols-outlined {
        transform: translateX(2px);
    }

    /* Close button */
    .qm-toast-close {
        flex-shrink: 0;
        width: 26px;
        height: 26px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        background: transparent;
        border: none;
        cursor: pointer;
        margin-top: -2px;
        margin-right: -4px;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    .qm-toast-close:hover {
        background: #f1f5f9;
        color: #334155;
    }
    .qm-toast-close .material-symbols-outlined {
        font-size: 17px !important;
        line-height: 1 !important;
    }

    /* Progress bar */
    .qm-toast-progress-wrap {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #f1f5f9;
        overflow: hidden;
    }
    .qm-toast-progress-bar {
        height: 100%;
        width: 100%;
        background: #10b981;
        transform-origin: left;
        transition-property: width;
        transition-timing-function: linear;
    }
</style>

<template id="global-toast-template">
    <div class="toast-item qm-toast-card">
        <!-- Toast Type Indicator Strip -->
        <div class="toast-indicator qm-toast-indicator"></div>

        <!-- Toast Visual (Icon or Thumbnail) -->
        <div class="toast-visual-container qm-toast-visual">
            <div class="toast-icon-wrap qm-toast-icon-wrap">
                <span class="material-symbols-outlined toast-icon">check_circle</span>
            </div>
            <div class="toast-thumb-wrap qm-toast-thumb-wrap hidden">
                <img src="" alt="" class="toast-thumb-img qm-toast-thumb-img" />
            </div>
            <div class="toast-thumb-badge qm-toast-thumb-badge hidden">
                <span class="material-symbols-outlined toast-badge-icon">favorite</span>
            </div>
        </div>

        <!-- Toast Text Content -->
        <div class="qm-toast-body">
            <div class="qm-toast-header">
                <span class="toast-eyebrow-text qm-toast-eyebrow">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span>THÔNG BÁO MỚI</span>
                </span>
                <span class="qm-toast-time">Vừa xong</span>
            </div>
            <h4 class="toast-title qm-toast-title"></h4>
            <p class="toast-message qm-toast-message"></p>
            <div class="toast-action-container qm-toast-action hidden">
                <a href="#" class="toast-action-link qm-toast-action-link">
                    <span class="toast-action-text">Xem chi tiết</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Close Button -->
        <button type="button" class="toast-close-btn qm-toast-close" aria-label="Đóng thông báo">
            <span class="material-symbols-outlined">close</span>
        </button>

        <!-- Progress Bar (Auto-dismiss countdown) -->
        <div class="qm-toast-progress-wrap">
            <div class="toast-progress-bar qm-toast-progress-bar"></div>
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
            type = 'success', // 'success', 'saved', 'unsaved', 'bookmark', 'unbookmark', 'error', 'warning', 'info'
            eyebrow = null,
            image = null,
            actionText = null,
            actionUrl = null,
            duration = 3600
        } = options;

        const container = document.getElementById('global-toast-container');
        const template = document.getElementById('global-toast-template');
        if (!container || !template) return;

        // Deduplicate identical toasts appearing in quick succession
        const cleanTitle = (title || '').trim();
        const cleanMsg = (message || '').trim();
        const existingToasts = container.querySelectorAll('.toast-item:not(.is-dismissing)');
        for (let i = 0; i < existingToasts.length; i++) {
            const exTitle = existingToasts[i].querySelector('.toast-title')?.textContent?.trim() || '';
            const exMsg = existingToasts[i].querySelector('.toast-message')?.textContent?.trim() || '';
            if (exTitle === cleanTitle && exMsg === cleanMsg) {
                return;
            }
        }

        // Chỉ hiển thị duy nhất 1 popup toast tại một thời điểm
        container.querySelectorAll('.toast-item').forEach(el => el.remove());
        const floatingUserContainer = document.getElementById('user-floating-toast-container');
        if (floatingUserContainer) {
            floatingUserContainer.innerHTML = '';
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

        // Theme configuration per type
        const themes = {
            saved: {
                accent: 'linear-gradient(180deg, #e11d48 0%, #a04100 100%)',
                progress: 'linear-gradient(90deg, #e11d48 0%, #f97316 100%)',
                eyebrowColor: '#be123c',
                eyebrowIcon: 'favorite',
                eyebrowLabel: eyebrow || 'Bộ sưu tập yêu thích',
                iconBg: '#fff1f2',
                iconColor: '#e11d48',
                iconBorder: '#fecdd3',
                iconName: 'favorite',
                thumbBorder: '#fecdd3',
                badgeBg: '#e11d48',
                badgeIcon: 'favorite',
                btnBg: '#fff1f2',
                btnColor: '#be123c',
                btnBorder: '#fecdd3',
                pop: true
            },
            bookmark: {
                accent: 'linear-gradient(180deg, #a04100 0%, #d97706 100%)',
                progress: 'linear-gradient(90deg, #a04100 0%, #f59e0b 100%)',
                eyebrowColor: '#a04100',
                eyebrowIcon: 'bookmark',
                eyebrowLabel: eyebrow || 'Bài viết đã lưu',
                iconBg: '#fff7ed',
                iconColor: '#a04100',
                iconBorder: '#fed7aa',
                iconName: 'bookmark',
                thumbBorder: '#fed7aa',
                badgeBg: '#a04100',
                badgeIcon: 'bookmark',
                btnBg: '#fff7ed',
                btnColor: '#a04100',
                btnBorder: '#fed7aa',
                pop: true
            },
            unsaved: {
                accent: '#64748b',
                progress: '#64748b',
                eyebrowColor: '#64748b',
                eyebrowIcon: 'heart_broken',
                eyebrowLabel: eyebrow || 'Đã cập nhật danh sách',
                iconBg: '#f1f5f9',
                iconColor: '#475569',
                iconBorder: '#e2e8f0',
                iconName: 'favorite',
                thumbBorder: '#e2e8f0',
                badgeBg: '#64748b',
                badgeIcon: 'remove',
                btnBg: '#f8fafc',
                btnColor: '#334155',
                btnBorder: '#e2e8f0',
                pop: false
            },
            unbookmark: {
                accent: '#64748b',
                progress: '#64748b',
                eyebrowColor: '#64748b',
                eyebrowIcon: 'bookmark_remove',
                eyebrowLabel: eyebrow || 'Đã bỏ lưu bài viết',
                iconBg: '#f1f5f9',
                iconColor: '#475569',
                iconBorder: '#e2e8f0',
                iconName: 'bookmark_remove',
                thumbBorder: '#e2e8f0',
                badgeBg: '#64748b',
                badgeIcon: 'remove',
                btnBg: '#f8fafc',
                btnColor: '#334155',
                btnBorder: '#e2e8f0',
                pop: false
            },
            error: {
                accent: '#ef4444',
                progress: '#ef4444',
                eyebrowColor: '#dc2626',
                eyebrowIcon: 'error',
                eyebrowLabel: eyebrow || 'Có lỗi xảy ra',
                iconBg: '#fef2f2',
                iconColor: '#dc2626',
                iconBorder: '#fecaca',
                iconName: 'error',
                thumbBorder: '#fecaca',
                badgeBg: '#dc2626',
                badgeIcon: 'priority_high',
                btnBg: '#fef2f2',
                btnColor: '#dc2626',
                btnBorder: '#fecaca',
                pop: false
            },
            warning: {
                accent: '#f59e0b',
                progress: '#f59e0b',
                eyebrowColor: '#d97706',
                eyebrowIcon: 'warning',
                eyebrowLabel: eyebrow || 'Lưu ý',
                iconBg: '#fffbeb',
                iconColor: '#d97706',
                iconBorder: '#fde68a',
                iconName: 'warning',
                thumbBorder: '#fde68a',
                badgeBg: '#d97706',
                badgeIcon: 'priority_high',
                btnBg: '#fffbeb',
                btnColor: '#b45309',
                btnBorder: '#fde68a',
                pop: false
            },
            info: {
                accent: '#3b82f6',
                progress: '#3b82f6',
                eyebrowColor: '#2563eb',
                eyebrowIcon: 'info',
                eyebrowLabel: eyebrow || 'Thông báo',
                iconBg: '#eff6ff',
                iconColor: '#2563eb',
                iconBorder: '#bfdbfe',
                iconName: 'info',
                thumbBorder: '#bfdbfe',
                badgeBg: '#2563eb',
                badgeIcon: 'info',
                btnBg: '#eff6ff',
                btnColor: '#1d4ed8',
                btnBorder: '#bfdbfe',
                pop: false
            },
            success: {
                accent: '#10b981',
                progress: '#10b981',
                eyebrowColor: '#059669',
                eyebrowIcon: 'check_circle',
                eyebrowLabel: eyebrow || 'Thành công',
                iconBg: '#ecfdf5',
                iconColor: '#059669',
                iconBorder: '#a7f3d0',
                iconName: 'check_circle',
                thumbBorder: '#a7f3d0',
                badgeBg: '#059669',
                badgeIcon: 'check',
                btnBg: '#ecfdf5',
                btnColor: '#047857',
                btnBorder: '#a7f3d0',
                pop: false
            }
        };

        const cfg = themes[type] || themes.success;

        indicator.style.background = cfg.accent;
        progressBar.style.background = cfg.progress;

        iconWrap.style.backgroundColor = cfg.iconBg;
        iconWrap.style.color = cfg.iconColor;
        iconWrap.style.borderColor = cfg.iconBorder;
        iconEl.textContent = cfg.iconName;
        if (cfg.pop) {
            iconEl.classList.add('toast-heart-pop');
        }

        if (eyebrowTextEl) {
            eyebrowTextEl.style.color = cfg.eyebrowColor;
            eyebrowTextEl.innerHTML = `<span class="material-symbols-outlined">${cfg.eyebrowIcon}</span><span>${cfg.eyebrowLabel}</span>`;
        }

        if (image) {
            iconWrap.classList.add('hidden');
            thumbWrap.classList.remove('hidden');
            thumbWrap.style.borderColor = cfg.thumbBorder;
            thumbImg.src = image;
            thumbBadge.classList.remove('hidden');
            thumbBadge.style.backgroundColor = cfg.badgeBg;
            if (cfg.pop) {
                thumbBadge.classList.add('toast-heart-pop');
            }
            badgeIcon.textContent = cfg.badgeIcon;
        }

        // Action Link
        if (actionText && actionUrl) {
            actionContainer.classList.remove('hidden');
            actionLink.href = actionUrl;
            actionLink.style.backgroundColor = cfg.btnBg;
            actionLink.style.color = cfg.btnColor;
            actionLink.style.borderColor = cfg.btnBorder;
            actionLink.addEventListener('mouseenter', () => {
                actionLink.style.backgroundColor = cfg.badgeBg;
                actionLink.style.color = '#ffffff';
                actionLink.style.borderColor = cfg.badgeBg;
            });
            actionLink.addEventListener('mouseleave', () => {
                actionLink.style.backgroundColor = cfg.btnBg;
                actionLink.style.color = cfg.btnColor;
                actionLink.style.borderColor = cfg.btnBorder;
            });
            actionTextEl.textContent = actionText;
        }

        container.appendChild(toast);

        // Entrance animation
        requestAnimationFrame(() => {
            toast.classList.add('is-visible');
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
            toast.classList.remove('is-visible');
            toast.classList.add('is-dismissing');
            setTimeout(() => {
                if (toast.parentNode) toast.remove();
            }, 280);
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

    // Auto-display Laravel session flash messages on page load (Đồng bộ 1 giao diện Toast duy nhất toàn website)
    document.addEventListener('DOMContentLoaded', () => {
        @if(session('success'))
            try {
                localStorage.removeItem('qm_blog_active_draft');
                sessionStorage.removeItem('qm_blog_active_draft');
                sessionStorage.removeItem('qm_blog_modal_open');
            } catch(e) {}

            window.showToast({
                title: 'Thành công!',
                message: @json(session('success')),
                type: 'success'
            });
        @endif

        @if(session('error_conflict'))
            window.showToast({
                title: 'Xung đột dữ liệu!',
                message: @json(session('error_conflict')),
                type: 'warning',
                actionText: 'Tải lại trang',
                actionUrl: window.location.href,
                duration: 5000
            });
        @elseif(session('error'))
            window.showToast({
                title: 'Có lỗi xảy ra',
                message: @json(session('error')),
                type: 'error'
            });
        @elseif(isset($errors) && $errors->any())
            window.showToast({
                title: 'Vui lòng kiểm tra lại thông tin',
                message: @json($errors->first()),
                type: 'error',
                duration: 4500
            });
        @endif

        @if(session('info'))
            window.showToast({
                title: 'Thông báo',
                message: @json(session('info')),
                type: 'info'
            });
        @elseif(session('status'))
            window.showToast({
                title: 'Thông báo',
                message: @json(session('status')),
                type: 'info'
            });
        @endif
    });
})();
</script>
