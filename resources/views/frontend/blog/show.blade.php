@extends('layouts.app')

@section('title', $blog->seo_title ?? $blog->title)
@section('description', $blog->seo_description ?? Str::limit(strip_tags($blog->excerpt ?? $blog->content), 150))
@section('keywords', $blog->meta_keywords ?? '')

@push('styles')
<style>
    .prose-custom {
        font-size: 1.125rem;
        line-height: 1.8;
    }
    .prose-custom h2, .prose-custom h3 {
        color: var(--color-on-background);
        font-weight: 700;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
    }
    .prose-custom h2 {
        font-size: 1.5rem;
        color: var(--color-primary);
    }
    .prose-custom h3 {
        font-size: 1.25rem;
    }
    .prose-custom p {
        margin-bottom: 1.25em;
        color: var(--color-on-background);
    }
    .prose-custom img {
        border-radius: 0.75rem;
        margin: 1.5em 0;
    }
    .prose-custom ul {
        list-style-type: disc;
        padding-left: 1.5em;
        margin-bottom: 1.5em;
    }
    .prose-custom li {
        margin-bottom: 0.5em;
    }

    /* ─── Hiệu ứng Thả cảm xúc ẩm thực Siêu Mượt 60FPS (Pro Food Reactions Motion System) ─── */
    .food-reaction-dock {
        position: absolute;
        bottom: calc(100% + 14px);
        left: 0;
        transform-origin: 32px bottom;
        display: inline-flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        white-space: nowrap !important;
        width: max-content !important;
        max-width: none !important;
        transition: opacity 0.26s cubic-bezier(0.16, 1, 0.3, 1), 
                    transform 0.38s cubic-bezier(0.34, 1.56, 0.64, 1),
                    visibility 0.26s;
        opacity: 0;
        pointer-events: none;
        visibility: hidden;
        transform: translateY(12px) scale(0.75);
        z-index: 80;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 20px 48px -10px rgba(0, 0, 0, 0.22), 
                    0 0 0 1px rgba(255, 255, 255, 0.9) inset,
                    0 10px 24px -4px rgba(217, 119, 6, 0.15);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        will-change: transform, opacity;
    }
    /* Compact dock version for comments */
    .food-reaction-dock.comment-reaction-dock {
        bottom: calc(100% + 8px);
        padding: 4px 8px;
        gap: 4px;
        display: inline-flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        white-space: nowrap !important;
        width: max-content !important;
        max-width: none !important;
        box-shadow: 0 12px 32px -4px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.9) inset;
    }
    .food-reaction-dock.comment-reaction-dock .food-reaction-item {
        width: 32px;
        height: 32px;
        font-size: 1.15rem;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        border-radius: 9999px;
    }
    .food-reaction-dock::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 30px;
        width: 10px;
        height: 10px;
        background: rgba(255, 255, 255, 0.96);
        transform: rotate(45deg);
        border-right: 1px solid rgba(226, 232, 240, 0.8);
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        pointer-events: none;
    }
    .food-reaction-dock.comment-reaction-dock::after {
        left: 20px;
        width: 8px;
        height: 8px;
        bottom: -4px;
    }
    .food-reaction-dock.dock-open {
        opacity: 1;
        pointer-events: auto;
        visibility: visible;
        transform: translateY(0) scale(1);
    }

    /* Hiệu ứng sóng trồi lên lần lượt (Staggered Spring Entrance) từng emoji khi mở dock */
    .food-reaction-dock .food-reaction-item {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        transform: translateY(16px) scale(0.65);
        opacity: 0;
        transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), 
                    opacity 0.22s ease-out,
                    filter 0.18s ease;
        will-change: transform, opacity;
        cursor: pointer;
        position: relative;
    }
    .food-reaction-dock.dock-open .food-reaction-item {
        transform: translateY(0) scale(var(--dock-scale, 1));
        opacity: 1;
    }
    .food-reaction-dock.dock-open .food-reaction-item:nth-child(1) { transition-delay: 0.02s; }
    .food-reaction-dock.dock-open .food-reaction-item:nth-child(2) { transition-delay: 0.05s; }
    .food-reaction-dock.dock-open .food-reaction-item:nth-child(3) { transition-delay: 0.08s; }
    .food-reaction-dock.dock-open .food-reaction-item:nth-child(4) { transition-delay: 0.11s; }
    .food-reaction-dock.dock-open .food-reaction-item:nth-child(5) { transition-delay: 0.14s; }
    .food-reaction-dock.dock-open .food-reaction-item:nth-child(6) { transition-delay: 0.17s; }

    /* Khi rê chuột vào 1 emoji: Phóng to đàn hồi nhẹ nhàng tinh tế (1.28x) */
    .food-reaction-dock.dock-open .food-reaction-item:hover {
        transform: translateY(-8px) scale(1.28) !important;
        transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        z-index: 50;
        filter: drop-shadow(0 6px 14px rgba(0, 0, 0, 0.18));
    }
    .food-reaction-dock.dock-open .food-reaction-item:active {
        transform: translateY(-4px) scale(1.15) !important;
    }

    /* Hiệu ứng lân cận (Mac-style Wave Proximity): Các icon xung quanh hơi co nhẹ */
    .food-reaction-dock:has(.food-reaction-item:hover) .food-reaction-item:not(:hover) {
        transform: translateY(-2px) scale(0.96);
        filter: brightness(0.95);
    }

    /* ─── Hiệu ứng chuyển động sống động riêng biệt cho từng Emoji (Alive Micro-Animations) ─── */
    .reaction-emoji-symbol {
        display: inline-block;
        transform-origin: center center;
        will-change: transform;
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Hiệu ứng thở nhẹ bập bồng (Idle Breathing) khi dock mở ra, tạo cảm giác emoji có hồn sống */
    @keyframes animDockIdleFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-3px) scale(1.04); }
    }
    .food-reaction-dock.dock-open .food-reaction-item:nth-child(odd) .reaction-emoji-symbol {
        animation: animDockIdleFloat 2.2s ease-in-out infinite;
    }
    .food-reaction-dock.dock-open .food-reaction-item:nth-child(even) .reaction-emoji-symbol {
        animation: animDockIdleFloat 2.2s ease-in-out infinite 0.55s;
    }

    /* 1. ❤️ YÊU THÍCH: Tim đập thình thịch 2 nhịp hữu cơ (Organic Heartbeat Pulse) */
    @keyframes animHeartPulse {
        0%, 100% {
            transform: scale(1, 1) rotate(0deg);
            filter: drop-shadow(0 2px 6px rgba(225, 29, 72, 0.35));
        }
        14% {
            /* Nhịp 1: Đập nảy nở nhanh */
            transform: scale(1.38, 1.32) rotate(-6deg);
            filter: drop-shadow(0 0 16px rgba(225, 29, 72, 0.9)) drop-shadow(0 4px 12px rgba(244, 63, 94, 0.8));
        }
        28% {
            /* Thu về tức thì */
            transform: scale(1.12, 1.08) rotate(2deg);
        }
        42% {
            /* Nhịp 2: Bơm máu căng tràn lồng ngực */
            transform: scale(1.5, 1.44) rotate(-3deg);
            filter: drop-shadow(0 0 22px rgba(225, 29, 72, 1)) drop-shadow(0 6px 18px rgba(244, 63, 94, 0.95));
        }
        65% {
            /* Thở êm dịu */
            transform: scale(1.06, 1.04) rotate(0deg);
        }
    }
    .food-reaction-item[data-reaction="love"]:hover .reaction-emoji-symbol {
        animation: animHeartPulse 0.85s ease-in-out infinite !important;
    }

    /* 2. 🤤 THÈM QUÁ: Lắc lư, nhún nhảy thèm thuồng chảy nước miếng sống động (Squash & Stretch Yummy Bounce) */
    @keyframes animDroolBounce {
        0%, 100% {
            transform: translateY(0) scale(1, 1) rotate(0deg);
            filter: drop-shadow(0 2px 6px rgba(217, 119, 6, 0.4));
        }
        18% {
            /* Chùng người xuống lấy đà háo hức (Squash) */
            transform: translateY(3px) scale(1.3, 0.82) rotate(-8deg);
            filter: drop-shadow(0 4px 10px rgba(245, 158, 11, 0.65));
        }
        38% {
            /* Nhảy vút lên thèm thuồng vươn cổ (Stretch leap) */
            transform: translateY(-11px) scale(0.86, 1.4) rotate(10deg);
            filter: drop-shadow(0 14px 22px rgba(245, 158, 11, 0.95)) drop-shadow(0 0 14px rgba(251, 191, 36, 0.85));
        }
        58% {
            /* Lắc đầu sang bên liếm môi ngon lành */
            transform: translateY(-4px) scale(1.18, 0.94) rotate(-12deg);
            filter: drop-shadow(0 8px 18px rgba(245, 158, 11, 0.85));
        }
        78% {
            /* Đáp xuống nhấp nhô nhẹ */
            transform: translateY(1px) scale(0.95, 1.08) rotate(4deg);
            filter: drop-shadow(0 4px 8px rgba(245, 158, 11, 0.5));
        }
    }
    .food-reaction-item[data-reaction="drool"]:hover .reaction-emoji-symbol,
    .food-reaction-item[data-reaction="delicious"]:hover .reaction-emoji-symbol {
        animation: animDroolBounce 0.85s cubic-bezier(0.34, 1.56, 0.64, 1) infinite !important;
    }

    /* 3. 🔥 PHẢI THỬ NGAY: Lửa bốc ngùn ngụt bập bùng dữ dội (Blazing Fire Dance & Flare) */
    @keyframes animFireDance {
        0%, 100% {
            transform: scale(1, 1) rotate(0deg);
            filter: drop-shadow(0 2px 8px rgba(234, 88, 12, 0.6));
        }
        18% {
            /* Lửa thót lại rồi vươn cao */
            transform: scale(0.84, 1.46) translateY(-6px) rotate(-9deg);
            filter: drop-shadow(0 -4px 18px rgba(239, 68, 68, 0.95)) drop-shadow(0 0 16px rgba(245, 158, 11, 0.9));
        }
        38% {
            /* Lửa bung xòe sang phải */
            transform: scale(1.36, 0.9) translateY(2px) rotate(8deg);
            filter: drop-shadow(0 2px 14px rgba(234, 88, 12, 0.9)) drop-shadow(0 -2px 12px rgba(251, 146, 60, 0.85));
        }
        60% {
            /* Bốc lửa dữ dội đỉnh điểm */
            transform: scale(1.05, 1.44) translateY(-7px) rotate(-6deg);
            filter: drop-shadow(0 -6px 24px rgba(239, 68, 68, 1)) drop-shadow(0 0 20px rgba(234, 88, 12, 1));
        }
        82% {
            /* Hạ nhiệt nhẹ */
            transform: scale(1.2, 1.04) translateY(-1px) rotate(5deg);
        }
    }
    .food-reaction-item[data-reaction="fire"]:hover .reaction-emoji-symbol {
        animation: animFireDance 0.68s ease-in-out infinite !important;
    }

    /* 4. 👍 HỮU ÍCH: Giơ ngón cái dứt khoát gật gù (Confident Thumb Pump & High-Five) */
    @keyframes animThumbPump {
        0%, 100% {
            transform: scale(1) rotate(0deg) translateY(0);
            filter: drop-shadow(0 2px 6px rgba(37, 99, 235, 0.35));
        }
        20% {
            /* Ngả về sau lấy đà */
            transform: scale(0.9) rotate(-26deg) translateY(2px);
        }
        45% {
            /* Cú giơ ngón cái bừng sáng về phía trước */
            transform: scale(1.44) rotate(14deg) translateY(-5px);
            filter: drop-shadow(0 8px 18px rgba(37, 99, 235, 0.88)) drop-shadow(0 0 14px rgba(59, 130, 246, 0.8));
        }
        70% {
            /* Nhún nảy hồi vị */
            transform: scale(1.18) rotate(-8deg) translateY(-1px);
            filter: drop-shadow(0 4px 12px rgba(37, 99, 235, 0.6));
        }
    }
    .food-reaction-item[data-reaction="like"]:hover .reaction-emoji-symbol {
        animation: animThumbPump 0.78s cubic-bezier(0.34, 1.56, 0.64, 1) infinite !important;
    }

    /* 5. 😢 KHÓC: Rung rinh nước mắt nấc nghẹn đau buồn (Emotional Tear Sob & Heave) */
    @keyframes animCrySob {
        0%, 100% {
            transform: scale(1, 1) translateY(0) rotate(0deg);
            filter: drop-shadow(0 2px 6px rgba(14, 165, 233, 0.4));
        }
        18% {
            /* Chực khóc cúi mặt */
            transform: scale(1.16, 0.9) translateY(3px) rotate(-6deg);
            filter: drop-shadow(0 4px 10px rgba(14, 165, 233, 0.7));
        }
        40% {
            /* Nấc nghẹn sụt sùi nảy lên */
            transform: scale(0.9, 1.38) translateY(-7px) rotate(7deg);
            filter: drop-shadow(0 8px 20px rgba(14, 165, 233, 0.95)) drop-shadow(0 0 14px rgba(56, 189, 248, 0.85));
        }
        65% {
            /* Run rẩy đau buồn */
            transform: scale(1.22, 0.94) translateY(-2px) rotate(-5deg);
            filter: drop-shadow(0 6px 14px rgba(14, 165, 233, 0.8));
        }
        85% {
            transform: scale(1.04, 1.08) translateY(1px) rotate(3deg);
        }
    }
    .food-reaction-item[data-reaction="cry"]:hover .reaction-emoji-symbol {
        animation: animCrySob 0.82s ease-in-out infinite !important;
    }

    /* 6. 😡 PHẪN NỘ: Rung bần bật giận dữ cực độ (Furious Rage Tremor & Crimson Fury) */
    @keyframes animRageTremor {
        0%, 100% {
            transform: scale(1) translate(0, 0) rotate(0deg);
            filter: drop-shadow(0 2px 6px rgba(220, 38, 38, 0.4));
        }
        10% {
            transform: scale(1.36) translate(-4px, -2px) rotate(-10deg);
            filter: drop-shadow(0 0 18px rgba(220, 38, 38, 0.95)) drop-shadow(0 0 28px rgba(185, 28, 28, 0.8));
        }
        22% {
            transform: scale(1.38) translate(4px, 2px) rotate(10deg);
        }
        35% {
            transform: scale(1.42) translate(-3px, 3px) rotate(-8deg);
            filter: drop-shadow(0 0 22px rgba(220, 38, 38, 1)) drop-shadow(0 0 32px rgba(239, 68, 68, 0.9));
        }
        50% {
            transform: scale(1.36) translate(3px, -3px) rotate(8deg);
        }
        65% {
            transform: scale(1.4) translate(-4px, 1px) rotate(-6deg);
        }
        80% {
            transform: scale(1.35) translate(3px, -1px) rotate(6deg);
        }
    }
    .food-reaction-item[data-reaction="angry"]:hover .reaction-emoji-symbol {
        animation: animRageTremor 0.52s linear infinite !important;
    }

    /* Tooltip hiển thị nhãn cảm xúc với mũi tên tam giác tinh tế */
    .food-reaction-item .reaction-tooltip {
        position: absolute;
        bottom: calc(100% + 11px);
        left: 50%;
        transform: translateX(-50%) translateY(6px) scale(0.85);
        opacity: 0;
        pointer-events: none;
        transition: all 0.18s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        white-space: nowrap;
        background: rgba(15, 23, 42, 0.94);
        backdrop-filter: blur(8px);
        color: #ffffff;
        border-radius: 9999px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.28);
    }
    .food-reaction-item .reaction-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 4px;
        border-style: solid;
        border-color: rgba(15, 23, 42, 0.94) transparent transparent transparent;
    }
    .food-reaction-item:hover .reaction-tooltip {
        opacity: 1;
        transform: translateX(-50%) translateY(0) scale(1);
    }

    /* Nút chính nhún đàn hồi khi thả cảm xúc (Juicy Punch Spring Animation) */
    @keyframes reactionPunchJuicy {
        0% { transform: scale(1); }
        25% { transform: scale(0.86) translateY(2px); }
        50% { transform: scale(1.16) translateY(-3px); }
        75% { transform: scale(0.96) translateY(1px); }
        100% { transform: scale(1) translateY(0); }
    }
    .reaction-punch {
        animation: reactionPunchJuicy 0.48s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    }

    /* Vòng sóng xung kích kép (Dual Shockwave Ripple) khi chạm thả cảm xúc */
    @keyframes reactionShockwaveRing {
        0% {
            transform: translate(-50%, -50%) scale(0.4);
            opacity: 1;
            box-shadow: 0 0 0 0 var(--ring-color, #f59e0b);
        }
        70% {
            opacity: 0.7;
            box-shadow: 0 0 16px 4px var(--ring-color, #f59e0b);
        }
        100% {
            transform: translate(-50%, -50%) scale(1.8);
            opacity: 0;
            box-shadow: 0 0 24px 6px rgba(255, 255, 255, 0);
        }
    }
    .reaction-shockwave-ring {
        position: fixed;
        width: 34px;
        height: 34px;
        border-radius: 9999px;
        border: 2px solid var(--ring-color, #f59e0b);
        pointer-events: none;
        z-index: 99998;
        transform: translate(-50%, -50%);
        animation: reactionShockwaveRing 0.55s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Hạt pháo lấp lánh nổ bung đa hướng nhỏ xinh (Confetti Sparkle Particles) */
    @keyframes particleBurstSmooth {
        0% {
            transform: translate(-50%, -50%) translate(0, 0) scale(1) rotate(0deg);
            opacity: 1;
        }
        60% {
            opacity: 0.95;
        }
        100% {
            transform: translate(-50%, -50%) translate(var(--tx), var(--ty)) scale(0) rotate(var(--rot, 180deg));
            opacity: 0;
        }
    }
    .reaction-burst-particle {
        position: fixed;
        border-radius: 9999px;
        pointer-events: none;
        z-index: 99998;
        box-shadow: 0 0 6px currentColor;
        animation: particleBurstSmooth 0.65s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    /* Emoji bay lên lượn sóng nhỏ gọn tinh tế (Compact Flying Emoji) */
    @keyframes compactFlyingEmoji {
        0% {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.4) rotate(0deg);
        }
        20% {
            opacity: 1;
            transform: translate(-50%, -18px) scale(1.18) rotate(-8deg);
        }
        60% {
            opacity: 0.95;
            transform: translate(calc(-50% + 8px), -48px) scale(1.22) rotate(6deg);
        }
        100% {
            opacity: 0;
            transform: translate(calc(-50% - 4px), -80px) scale(0.85) rotate(-4deg);
        }
    }
    .floating-food-reaction {
        position: fixed;
        pointer-events: none;
        z-index: 99999;
        font-size: 1.55rem;
        user-select: none;
        filter: drop-shadow(0 6px 14px rgba(0, 0, 0, 0.2));
        animation: compactFlyingEmoji 0.88s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* 2 hạt vệ tinh mini bay kèm (Companion Micro Sparks) */
    @keyframes companionSparkFly {
        0% {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.2);
        }
        25% {
            opacity: 1;
            transform: translate(calc(-50% + var(--ox)), -22px) scale(0.95);
        }
        100% {
            opacity: 0;
            transform: translate(calc(-50% + calc(var(--ox) * 1.5)), -65px) scale(0.3);
        }
    }
    .mini-floating-spark {
        position: fixed;
        pointer-events: none;
        z-index: 99998;
        font-size: 0.95rem;
        user-select: none;
        filter: drop-shadow(0 3px 8px rgba(0, 0, 0, 0.15));
        animation: companionSparkFly 0.78s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Hiệu ứng nảy số lượt thả (Count Pop Smooth Animation) */
    @keyframes countPopSmooth {
        0% { transform: scale(1); }
        45% { transform: scale(1.38) translateY(-2px); color: #ea580c; text-shadow: 0 2px 10px rgba(234, 88, 12, 0.35); }
        75% { transform: scale(0.96); }
        100% { transform: scale(1) translateY(0); }
    }
    .count-pop-anim {
        display: inline-block;
        animation: countPopSmooth 0.42s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Hiệu ứng bung quạt avatar top cảm xúc (Top Emojis Avatar Fan-Out) */
    .food-reactions-top-emojis {
        display: inline-flex;
        align-items: center;
    }
    .food-reactions-top-emojis span {
        transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1), 
                    margin 0.28s cubic-bezier(0.34, 1.56, 0.64, 1), 
                    box-shadow 0.2s ease;
        will-change: transform, margin;
    }
    #food-reactions-summary-pill:hover .food-reactions-top-emojis span {
        margin-left: 2px !important;
        transform: scale(1.18);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }
    #food-reactions-summary-pill:hover .food-reactions-top-emojis span:hover {
        transform: scale(1.42) translateY(-4px) !important;
        z-index: 30;
    }

    /* Modal danh sách người thả cảm xúc - Cuộn êm & Khóa cuộn tràn */
    #reactions-modal-body {
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    #reactions-modal-body::-webkit-scrollbar {
        width: 6px;
    }
    #reactions-modal-body::-webkit-scrollbar-track {
        background: transparent;
    }
    #reactions-modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    #reactions-modal-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush

@section('content')
<!-- Main Content -->
<main class="max-w-[1200px] mx-auto px-container-margin pt-12 md:pt-20 pb-stack-lg flex flex-col md:flex-row gap-stack-lg">
    
    <!-- Article Section (Left Column) -->
    <div class="w-full md:w-2/3">
        <article class="bg-surface-card rounded-xl p-6 md:p-stack-lg shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-outline-variant/30">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 mb-stack-sm text-on-surface-variant font-label-md text-label-md overflow-x-auto whitespace-nowrap pb-2">
                <a class="hover:text-primary transition-colors flex items-center" href="{{ route('home') }}">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                </a>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <a class="hover:text-primary transition-colors" href="{{ route('blog.index') }}">Blog</a>
                @if($blog->category)
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    <a class="hover:text-primary transition-colors" href="{{ route('blog.index', ['category' => $blog->category->slug]) }}">{{ $blog->category->name }}</a>
                @endif
            </div>
            
            <h1 class="font-display-lg text-3xl md:text-[40px] md:leading-[48px] text-on-background mb-stack-md font-bold">
                {{ $blog->title }}
            </h1>
            
            <!-- ─── Header tác giả & tương tác (Chuẩn bài viết chuyên nghiệp 2 bên) ─── -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-3.5 mb-6 border-y border-slate-100">
                <!-- Bên trái: Avatar + Tác giả + Ngày đăng & Thống kê ngắn gọn -->
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ $blog->user ? route('blog.author', $blog->user->id) : '#' }}" class="group/author shrink-0" title="Xem bài viết của tác giả {{ $blog->user?->ho_ten }}">
                        <div class="w-11 h-11 rounded-full bg-slate-100 overflow-hidden border border-slate-200/80 ring-2 ring-transparent group-hover/author:ring-primary/40 transition-all shadow-2xs">
                            <img class="w-full h-full object-cover group-hover/author:scale-105 transition-transform" src="{{ $blog->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($blog->user->ho_ten ?? 'User').'&background=random' }}" alt="{{ $blog->user->ho_ten ?? 'Tác giả' }}"/>
                        </div>
                    </a>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <a href="{{ $blog->user ? route('blog.author', $blog->user->id) : '#' }}" class="font-bold text-[14.5px] text-slate-900 hover:text-primary transition-colors flex items-center gap-1">
                                <span>{{ $blog->user->ho_ten ?? 'Tác giả' }}</span>
                                @if($blog->user && ($blog->user->hasRole('chu_quan') || $blog->user->isAdmin()))
                                    <span class="material-symbols-outlined text-tick-xanh text-[16px]" style="font-variation-settings: 'FILL' 1;" title="Đã xác thực">verified</span>
                                @endif
                            </a>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5 whitespace-nowrap">
                            <span>{{ ($blog->published_at ?? $blog->created_at)?->format('d/m/Y') ?? 'Mới' }}</span>
                            <span class="text-slate-300">•</span>
                            <span class="flex items-center gap-1" title="{{ number_format($blog->view_count ?? 0) }} lượt xem">
                                <span class="material-symbols-outlined text-[15px] text-amber-600">visibility</span>
                                <span id="blog-view-count" class="font-semibold text-slate-700">{{ number_format($blog->view_count ?? 0) }}</span>
                            </span>
                            <span class="text-slate-300">•</span>
                            <a href="#comments-section" class="flex items-center gap-1 hover:text-primary transition-colors" title="{{ number_format($blog->comment_count) }} bình luận">
                                <span class="material-symbols-outlined text-[15px] text-primary">forum</span>
                                <span id="blog-header-comment-count" class="font-semibold text-slate-700">{{ number_format($blog->comment_count) }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bên phải: Nhóm nút hành động nhanh (Cảm xúc & Lưu bài viết) -->
                <div class="flex items-center gap-2.5 shrink-0 self-start sm:self-center">
                    <!-- Food Reaction Pill Button -->
                    @php
                        $myReactionKey = $userReaction ? $userReaction->reaction : null;
                        $reactionConfig = $myReactionKey && isset(\App\Models\BlogReaction::TYPES[$myReactionKey]) ? \App\Models\BlogReaction::TYPES[$myReactionKey] : null;
                        $totalReactions = $reactionsSummary['total'] ?? 0;
                        $reactionBreakdown = $reactionsSummary['breakdown'] ?? [];
                        $topReactionKeys = $reactionsSummary['top'] ?? [];
                    @endphp
                    <div class="relative food-reaction-container" data-container="header">
                        <!-- Hover Reaction Dock -->
                        <div class="food-reaction-dock bg-white/95 backdrop-blur-md px-2.5 py-1.5 rounded-full shadow-[0_10px_35px_rgba(0,0,0,0.18)] border border-slate-200/90 flex items-center gap-1.5" role="toolbar" aria-label="Thả cảm xúc ẩm thực">
                            @foreach(\App\Models\BlogReaction::TYPES as $key => $r)
                                <button type="button" 
                                        onclick="triggerFoodReaction('{{ $key }}', event)" 
                                        class="food-reaction-item w-9 h-9 rounded-full flex items-center justify-center text-2xl hover:bg-slate-100/90 transition-all select-none active:scale-90 cursor-pointer"
                                        data-reaction="{{ $key }}"
                                        title="{{ $r['name'] }}">
                                    <span class="reaction-emoji-symbol">{{ $r['emoji'] }}</span>
                                    <span class="reaction-tooltip px-2.5 py-1 rounded-full bg-slate-900 text-white text-[11px] font-bold shadow-md">
                                        {{ $r['name'] }}
                                    </span>
                                </button>
                            @endforeach
                        </div>

                        <!-- Main Reaction Button -->
                        <button type="button" 
                                id="header-food-reaction-btn" 
                                class="food-reaction-main-btn flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border transition-all cursor-pointer active:scale-95 whitespace-nowrap {{ $reactionConfig ? $reactionConfig['bg_class'] . ' font-bold ' . $reactionConfig['border_class'] . ' ' . $reactionConfig['text_class'] : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30' }}"
                                data-reacted="{{ $myReactionKey ? '1' : '0' }}"
                                data-current="{{ $myReactionKey ?? '' }}"
                                title="Thả cảm xúc ẩm thực (Bấm để Thích, rê chuột để chọn cảm xúc)">
                            <span class="food-reaction-icon text-[16px]">
                                {{ $reactionConfig ? $reactionConfig['emoji'] : '👍' }}
                            </span>
                            <span class="food-reaction-label text-xs font-semibold">
                                {{ $reactionConfig ? $reactionConfig['name'] : 'Thích' }}
                            </span>
                            <span onclick="event.stopPropagation(); openBlogReactionsModal(event);" 
                                  class="food-reaction-count items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-bold transition-all cursor-pointer hover:scale-105 active:scale-95 hover:ring-2 hover:ring-amber-400/40 {{ $totalReactions > 0 ? ($reactionConfig ? 'inline-flex bg-white/90 text-slate-800 shadow-2xs border border-black/5' : 'inline-flex bg-slate-200/80 text-slate-700') : 'hidden' }}"
                                  title="Bấm để xem danh sách ai đã thả cảm xúc">
                                <span class="food-reaction-top-icons inline-flex items-center -space-x-1 select-none">
                                    @foreach($topReactionKeys as $topKey)
                                        @if(isset(\App\Models\BlogReaction::TYPES[$topKey]))
                                            <span class="w-4 h-4 rounded-full bg-white shadow-2xs border border-white flex items-center justify-center text-[10px] leading-none shrink-0" 
                                                  title="{{ \App\Models\BlogReaction::TYPES[$topKey]['name'] }}: {{ $reactionBreakdown[$topKey] ?? 0 }}">
                                                {{ \App\Models\BlogReaction::TYPES[$topKey]['emoji'] }}
                                            </span>
                                        @endif
                                    @endforeach
                                </span>
                                <span class="food-reaction-number text-[11px] font-extrabold tracking-tight">
                                    {{ number_format($totalReactions) }}
                                </span>
                            </span>
                        </button>
                    </div>

                    <!-- Bookmark Button -->
                    <button type="button" 
                            onclick="toggleBlogBookmark(this, '{{ $blog->id }}', '{{ $blog->slug }}', '{{ addslashes($blog->title) }}')" 
                            data-blog-id="{{ $blog->id }}" 
                            data-blog-slug="{{ $blog->slug }}" 
                            class="blog-bookmark-btn flex items-center gap-1.5 bg-slate-50 hover:bg-amber-50 px-3.5 py-1.5 rounded-full border border-slate-200 hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 transition-all cursor-pointer active:scale-95 whitespace-nowrap text-xs font-semibold text-slate-700" 
                            title="Lưu bài viết"
                            aria-label="Lưu bài viết">
                        <span class="material-symbols-outlined text-[16px] text-amber-500">bookmark</span>
                        <span class="blog-bookmark-text">Lưu</span>
                    </button>
                </div>
            </div>

            <!-- AI Summarizer Feature Section -->
            <div class="mb-stack-lg border border-primary/25 bg-gradient-to-br from-amber-500/5 via-primary/5 to-purple-500/5 rounded-2xl p-4 md:p-5 transition-all shadow-xs relative overflow-hidden" id="ai-summarizer-card">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background: linear-gradient(135deg, #a04100 0%, #ea580c 100%); color: #ffffff;">
                            <span class="material-symbols-outlined text-xl" style="color: #ffffff;">auto_awesome</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-title-md text-sm md:text-base font-bold text-on-background">AI Tóm tắt bài viết</h3>
                                <span class="bg-primary/10 text-primary text-[10px] font-extrabold px-2 py-0.5 rounded-full border border-primary/20">5s nắm trọn cốt lõi</span>
                            </div>
                            <p class="text-xs text-on-surface-variant mt-0.5">Rút gọn 4 ý chính: Địa chỉ, món nên thử, mức giá & ưu/nhược điểm trong 5 giây.</p>
                        </div>
                    </div>
                    <button type="button" onclick="toggleAiSummary()" id="ai-summary-btn" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-bold shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">bolt</span>
                        <span id="ai-summary-btn-text">Tóm tắt bằng AI</span>
                    </button>
                </div>

                <!-- AI Summary Loading Spinner / Shimmer -->
                <div id="ai-summary-loading" class="hidden mt-4 pt-4 border-t border-primary/10 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-primary animate-pulse">
                        <span class="material-symbols-outlined text-base animate-spin">progress_activity</span>
                        <span>AI đang đọc và tổng hợp nội dung bài viết...</span>
                    </div>
                    <div class="h-3.5 bg-slate-200/80 rounded-full animate-pulse w-3/4"></div>
                    <div class="h-3.5 bg-slate-200/80 rounded-full animate-pulse w-5/6"></div>
                </div>

                <!-- AI Summary Content Details (Expands smoothly) -->
                <div id="ai-summary-content" class="hidden mt-4 pt-4 border-t border-primary/15">
                    <!-- Card Overview TL;DR -->
                    <div class="p-3.5 bg-white/90 backdrop-blur-sm rounded-xl border border-primary/10 mb-3.5 shadow-2xs">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] font-extrabold text-primary flex items-center gap-1 uppercase tracking-wider">
                                <span class="material-symbols-outlined text-[14px]">psychology</span> Tóm lược nhanh (TL;DR)
                            </span>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="speakAiSummary()" id="ai-tts-btn" class="text-[11px] font-semibold text-slate-500 hover:text-primary flex items-center gap-1 px-2 py-0.5 rounded-md hover:bg-slate-100 transition-colors cursor-pointer" title="Đọc to tóm tắt">
                                    <span class="material-symbols-outlined text-[14px]" id="ai-tts-icon">volume_up</span>
                                    <span id="ai-tts-text">Nghe đọc</span>
                                </button>
                                <button type="button" onclick="copyAiSummary()" class="text-[11px] font-semibold text-slate-500 hover:text-primary flex items-center gap-1 px-2 py-0.5 rounded-md hover:bg-slate-100 transition-colors cursor-pointer" title="Sao chép tóm tắt">
                                    <span class="material-symbols-outlined text-[14px]">content_copy</span>
                                    <span>Sao chép</span>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-on-surface leading-relaxed font-medium" id="ai-summary-overview"></p>
                    </div>

                    <!-- 4 Key Pillars Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Pillar 1: Địa chỉ -->
                        <div class="p-3 bg-white/95 rounded-xl border border-outline-variant/30 flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight block">1. Địa chỉ & Khu vực</span>
                                <p class="text-xs font-bold text-slate-800 mt-0.5 leading-snug" id="ai-summary-address"></p>
                            </div>
                        </div>

                        <!-- Pillar 2: Món nên thử -->
                        <div class="p-3 bg-white/95 rounded-xl border border-outline-variant/30 flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[16px]">restaurant_menu</span>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight block">2. Món nên thử (Must-try)</span>
                                <div class="flex flex-wrap gap-1 mt-1" id="ai-summary-musttry"></div>
                            </div>
                        </div>

                        <!-- Pillar 3: Mức giá -->
                        <div class="p-3 bg-white/95 rounded-xl border border-outline-variant/30 flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[16px]">payments</span>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight block">3. Mức giá & Phân khúc</span>
                                <p class="text-xs font-bold text-slate-800 mt-0.5 leading-snug" id="ai-summary-price"></p>
                            </div>
                        </div>

                        <!-- Pillar 4: Ưu & Nhược điểm -->
                        <div class="p-3 bg-white/95 rounded-xl border border-outline-variant/30 flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[16px]">balance</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight block">4. Ưu & Nhược điểm</span>
                                <ul class="text-xs space-y-1 mt-1 text-slate-700" id="ai-summary-proscons"></ul>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Footer -->
                    <div class="mt-3 flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                        <span id="ai-summary-saved-time">Tiết kiệm 95% thời gian đọc (nắm trọn trong 5 giây)</span>
                        <button type="button" onclick="toggleAiSummary(true)" class="hover:text-primary font-medium flex items-center gap-0.5 cursor-pointer">
                            <span>Thu gọn</span>
                            <span class="material-symbols-outlined text-[14px]">expand_less</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hero Image -->
            @if($blog->cover_image)
            <div class="w-full h-[300px] md:h-[400px] rounded-xl overflow-hidden mb-stack-lg shadow-[0_4px_20px_rgba(0,0,0,0.05)]">
                <img class="w-full h-full object-cover" src="{{ Str::startsWith($blog->cover_image, 'http') ? $blog->cover_image : asset('storage/'.$blog->cover_image) }}" alt="{{ $blog->title }}"/>
            </div>
            @endif

            <!-- Article Content -->
            @if($blog->excerpt)
                <p class="text-lg font-medium text-on-surface-variant mb-6 leading-relaxed italic border-l-4 border-primary pl-4">{{ $blog->excerpt }}</p>
            @endif
            
            <div class="prose-custom max-w-none">
                {!! $blog->content !!}
            </div>

            <!-- Tags -->
            @if($blog->tags->count() > 0)
            <div class="mt-stack-lg pt-stack-md border-t border-surface-dim flex flex-wrap gap-2">
                @foreach($blog->tags as $tag)
                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="px-3 py-1 bg-surface-container rounded-full font-label-md text-label-md text-on-surface-variant hover:bg-outline-variant hover:text-on-primary-container cursor-pointer transition-colors">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
            @endif


        </article>
        
        <!-- Comments Section -->
        <section id="comments-section" class="mt-8 bg-surface-card rounded-2xl p-6 md:p-stack-lg shadow-[0_4px_24px_rgba(0,0,0,0.06)] border border-outline-variant/30">
            <div class="flex items-center justify-between mb-6 flex-wrap gap-3 pb-4 border-b border-surface-dim">
                <h3 class="font-title-md text-title-md text-on-background flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-2xl">forum</span>
                    <span>Bình luận (<span id="comments-count-badge">{{ number_format($blog->comment_count) }}</span>)</span>
                </h3>
            </div>

            {{-- Dynamic Alert Banner --}}
            <div id="comment-alert-box" class="hidden mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs">
                <span class="material-symbols-outlined text-xl shrink-0" id="comment-alert-icon">info</span>
                <div class="flex-1">
                    <h5 class="font-extrabold text-sm mb-0.5" id="comment-alert-title">Thông báo</h5>
                    <p id="comment-alert-msg" class="leading-relaxed"></p>
                </div>
                <button type="button" onclick="document.getElementById('comment-alert-box').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
            
            @if(! $blog->allow_comments)
                <div class="p-6 bg-surface-container rounded-2xl mb-6 text-center border border-outline-variant/30">
                    <span class="material-symbols-outlined text-3xl text-text-muted mb-2">lock</span>
                    <p class="text-on-surface-variant font-medium text-sm">Tác giả đã tạm khóa tính năng bình luận cho bài viết này.</p>
                </div>
            @else
                @auth
                    <!-- Main Comment Form -->
                    <form id="blog-comment-form" action="{{ route('blog.comment.store', $blog->slug) }}" method="POST" onsubmit="handleCommentSubmit(event, 'blog-comment-form')" class="mb-8 flex gap-4 items-start">
                        @csrf
                        <img src="{{ auth()->user()->anh_dai_dien_url }}" class="w-10 h-10 rounded-full flex-shrink-0 shadow-sm border border-gray-100 object-cover" alt="{{ auth()->user()->ho_ten }}">
                        <div class="flex-1 space-y-2">
                            <div class="relative">
                                <textarea id="comment-content-input" name="content" rows="3" required placeholder="Chia sẻ suy nghĩ hoặc nhận xét của bạn về bài viết này..." oninput="updateCharCount(this, 'main-char-count')" class="w-full rounded-2xl border border-surface-dim bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary resize-none text-sm p-3.5 outline-none transition-all"></textarea>
                            </div>
                            <div class="flex items-center justify-between flex-wrap gap-2 text-xs">
                                <div class="flex items-center gap-3 text-[11px] text-text-muted">
                                    <span id="main-char-count" class="text-slate-400">0/1000 ký tự</span>
                                    <span class="hidden sm:inline text-slate-400">• Ctrl + Enter để gửi</span>
                                </div>
                                <button type="submit" id="comment-submit-btn" class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-full font-label-md text-label-md transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                                    <span class="material-symbols-outlined text-base">send</span>
                                    <span id="comment-submit-text">Gửi bình luận</span>
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="p-6 bg-surface-container rounded-2xl mb-8 text-center border border-outline-variant/30">
                        <span class="material-symbols-outlined text-3xl text-primary mb-2">account_circle</span>
                        <p class="text-on-surface-variant font-medium text-sm mb-3">Vui lòng đăng nhập tài khoản để tham gia bình luận bài viết.</p>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-6 py-2.5 bg-primary text-white rounded-full font-label-md hover:bg-primary/90 transition-all shadow-xs">
                            <span class="material-symbols-outlined text-base">login</span>
                            Đăng nhập ngay
                        </a>
                    </div>
                @endauth
            @endif
            
            <!-- Comment List -->
            <div id="comments-list-container" class="space-y-4">
                @forelse($blog->rootComments as $comment)
                    <div id="comment-block-{{ $comment->id }}" class="comment-item p-4 rounded-2xl bg-white hover:bg-surface-container/20 transition-all border border-surface-dim/60 shadow-2xs">
                        <div class="flex gap-3.5 items-start">
                            <img src="{{ $comment->user->anh_dai_dien_url }}" class="w-10 h-10 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs" alt="{{ $comment->user->ho_ten }}">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1 flex-wrap">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-title-md text-[14px] font-bold text-on-background">{{ $comment->user->ho_ten }}</span>
                                        
                                        @if($comment->user_id === $blog->user_id)
                                            <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5 border border-primary/20">
                                                <span class="material-symbols-outlined text-[12px]">edit_note</span>
                                                Tác giả
                                            </span>
                                        @endif

                                        @if($comment->user->isAdmin())
                                            <span class="bg-purple-50 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5 border border-purple-200">
                                                <span class="material-symbols-outlined text-[12px]">shield_person</span>
                                                Quản trị viên
                                            </span>
                                        @endif

                                        <span class="text-xs text-on-surface-variant">• {{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <p class="text-on-surface font-body-sm whitespace-pre-line text-sm leading-relaxed">{{ $comment->content }}</p>
                                
                                <!-- Comment Actions: Like, Reply, Delete -->
                                <div class="flex items-center gap-4 mt-2.5 pt-2 text-xs text-on-surface-variant font-medium">
                                    @php
                                        $cUserReaction = auth()->check() ? $comment->userReaction(auth()->user()) : null;
                                        $cReactionKey = $cUserReaction ? ($cUserReaction->reaction ?? 'like') : null;
                                        $cReactionConfig = $cReactionKey && isset(\App\Models\BlogReaction::TYPES[$cReactionKey]) ? \App\Models\BlogReaction::TYPES[$cReactionKey] : null;
                                        $cSummary = $comment->reactions_summary;
                                        $cTopKey = $cSummary['top_one'] ?? null;
                                        $cTopConfig = $cTopKey && isset(\App\Models\BlogReaction::TYPES[$cTopKey]) ? \App\Models\BlogReaction::TYPES[$cTopKey] : null;
                                        $cTotal = $cSummary['total'] ?? 0;
                                    @endphp
                                    <div class="relative comment-reaction-container flex items-center gap-1.5" data-comment-id="{{ $comment->id }}">
                                        @auth
                                            <!-- Floating Hover Dock -->
                                            <div class="food-reaction-dock comment-reaction-dock flex items-center flex-nowrap whitespace-nowrap" role="toolbar" aria-label="Thả cảm xúc bình luận">
                                                @foreach(\App\Models\BlogReaction::TYPES as $key => $r)
                                                    <button type="button" 
                                                            onclick="triggerCommentReaction({{ $comment->id }}, '{{ $key }}', event)" 
                                                            class="food-reaction-item select-none active:scale-90 cursor-pointer"
                                                            data-reaction="{{ $key }}"
                                                            title="{{ $r['name'] }}">
                                                        <span class="reaction-emoji-symbol">{{ $r['emoji'] }}</span>
                                                        <span class="reaction-tooltip px-2 py-0.5 rounded-full bg-slate-900 text-white text-[10px] font-bold shadow-md">
                                                            {{ $r['name'] }}
                                                        </span>
                                                    </button>
                                                @endforeach
                                            </div>

                                            <!-- Reaction Button -->
                                            <button type="button" 
                                                    onclick="triggerCommentReaction({{ $comment->id }}, null, event)" 
                                                    id="comment-react-btn-{{ $comment->id }}"
                                                    class="comment-react-btn flex items-center gap-1 transition-all cursor-pointer font-medium {{ $cReactionConfig ? $cReactionConfig['text_class'] . ' font-bold' : 'text-slate-500 hover:text-primary' }}"
                                                    data-reacted="{{ $cReactionKey ? '1' : '0' }}"
                                                    data-current="{{ $cReactionKey ?? '' }}"
                                                    title="Bấm để Thích, rê chuột để chọn cảm xúc">
                                                <span class="comment-reaction-icon text-[14px]">
                                                    {{ $cReactionConfig ? $cReactionConfig['emoji'] : '👍' }}
                                                </span>
                                                <span class="comment-reaction-label text-xs">
                                                    {{ $cReactionConfig ? $cReactionConfig['name'] : 'Thích' }}
                                                </span>
                                            </button>
                                        @else
                                            <a href="{{ route('login') }}" class="comment-react-btn flex items-center gap-1 text-slate-500 hover:text-primary transition-colors">
                                                <span class="comment-reaction-icon text-[14px]">👍</span>
                                                <span class="comment-reaction-label text-xs">Thích</span>
                                            </a>
                                        @endauth

                                        <!-- Badge số tương tác (CHỈ HIỆN 1 ICON NHIỀU NHẤT) -->
                                        <span id="comment-reaction-count-{{ $comment->id }}" 
                                              onclick="openCommentReactionsModal({{ $comment->id }}, event)"
                                              class="comment-reaction-count-badge items-center gap-1 px-1.5 py-0.5 rounded-full text-xs font-bold transition-all bg-slate-100 text-slate-700 hover:bg-amber-100/90 hover:text-amber-900 border border-slate-200/80 shadow-2xs cursor-pointer hover:scale-105 active:scale-95 {{ $cTotal > 0 ? 'inline-flex' : 'hidden' }}"
                                              title="Bấm để xem danh sách ai đã thả cảm xúc">
                                            <span class="comment-top-icon text-[11px] leading-none shrink-0">
                                                {{ $cTopConfig ? $cTopConfig['emoji'] : '👍' }}
                                            </span>
                                            <span class="comment-total-count text-[11px] font-bold tracking-tight">
                                                {{ $cTotal }}
                                            </span>
                                        </span>
                                    </div>

                                    @if($blog->allow_comments)
                                        <button type="button" onclick="toggleReplyBox({{ $comment->id }}, '{{ addslashes($comment->user->ho_ten) }}')" class="flex items-center gap-1 text-slate-500 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-[16px]">reply</span>
                                            <span>Trả lời</span>
                                        </button>
                                    @endif

                                    @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->id() === $blog->user_id || auth()->user()->isAdmin()))
                                        <button type="button" onclick="openDeleteModal({{ $comment->id }})" class="flex items-center gap-1 text-slate-400 hover:text-rose-600 transition-colors ml-auto">
                                            <span class="material-symbols-outlined text-[15px]">delete</span>
                                            <span>Xóa</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Inline Reply Box for this Comment -->
                        @auth
                            <div id="reply-box-{{ $comment->id }}" class="hidden mt-3 pl-10 pr-2 pt-2 border-t border-surface-dim">
                                <form id="reply-form-{{ $comment->id }}" action="{{ route('blog.comment.store', $blog->slug) }}" method="POST" onsubmit="handleCommentSubmit(event, 'reply-form-{{ $comment->id }}', {{ $comment->id }})" class="flex gap-3 items-start">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <img src="{{ auth()->user()->anh_dai_dien_url }}" class="w-8 h-8 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs" alt="{{ auth()->user()->ho_ten }}">
                                    <div class="flex-1 space-y-2">
                                        <textarea id="reply-input-{{ $comment->id }}" name="content" rows="2" required placeholder="Trả lời @{{ $comment->user->ho_ten }}..." oninput="updateCharCount(this, 'char-count-{{ $comment->id }}')" class="w-full rounded-xl border border-surface-dim bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary resize-none text-xs p-3 outline-none transition-all"></textarea>
                                        <div class="flex items-center justify-between flex-wrap gap-2 text-xs">
                                            <span id="char-count-{{ $comment->id }}" class="text-[11px] text-slate-400">0/1000 ký tự</span>
                                            <div class="flex items-center gap-2">
                                                <button type="button" onclick="toggleReplyBox({{ $comment->id }})" class="px-3 py-1.5 rounded-full text-slate-500 hover:bg-slate-100 font-medium transition-colors cursor-pointer text-xs">
                                                    Hủy
                                                </button>
                                                <button type="submit" id="reply-btn-{{ $comment->id }}" class="px-4 py-1.5 bg-primary hover:bg-primary/90 text-white rounded-full font-label-sm transition-all shadow-xs flex items-center gap-1 cursor-pointer text-xs">
                                                    <span class="material-symbols-outlined text-sm">send</span>
                                                    <span id="reply-btn-text-{{ $comment->id }}">Gửi phản hồi</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endauth

                        <!-- Nested Replies List -->
                        <div id="replies-list-{{ $comment->id }}" class="space-y-3 mt-3 pl-8 sm:pl-10 {{ $comment->replies->count() > 0 ? 'pt-2' : '' }}">
                            @foreach($comment->replies as $reply)
                                <div id="comment-block-{{ $reply->id }}" class="reply-item p-3.5 rounded-xl bg-surface-container/30 border border-surface-dim/70 flex gap-3 items-start transition-all">
                                    <img src="{{ $reply->user->anh_dai_dien_url }}" class="w-8 h-8 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs" alt="{{ $reply->user->ho_ten }}">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2 mb-1 flex-wrap">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-title-md text-[13px] font-bold text-on-background">{{ $reply->user->ho_ten }}</span>
                                                
                                                @if($reply->user_id === $blog->user_id)
                                                    <span class="bg-primary/10 text-primary text-[9px] font-bold px-1.5 py-0.2 rounded-full flex items-center gap-0.5 border border-primary/20">
                                                        <span class="material-symbols-outlined text-[11px]">edit_note</span>
                                                        Tác giả
                                                    </span>
                                                @endif

                                                @if($reply->user->isAdmin())
                                                    <span class="bg-purple-50 text-purple-700 text-[9px] font-bold px-1.5 py-0.2 rounded-full flex items-center gap-0.5 border border-purple-200">
                                                        <span class="material-symbols-outlined text-[11px]">shield_person</span>
                                                        Quản trị viên
                                                    </span>
                                                @endif

                                                <span class="text-[11px] text-on-surface-variant">• {{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <p class="text-on-surface font-body-sm whitespace-pre-line text-xs leading-relaxed">{{ $reply->content }}</p>
                                        
                                        <div class="flex items-center gap-3 mt-2 text-[11px] text-on-surface-variant font-medium">
                                            @php
                                                $rUserReaction = auth()->check() ? $reply->userReaction(auth()->user()) : null;
                                                $rReactionKey = $rUserReaction ? ($rUserReaction->reaction ?? 'like') : null;
                                                $rReactionConfig = $rReactionKey && isset(\App\Models\BlogReaction::TYPES[$rReactionKey]) ? \App\Models\BlogReaction::TYPES[$rReactionKey] : null;
                                                $rSummary = $reply->reactions_summary;
                                                $rTopKey = $rSummary['top_one'] ?? null;
                                                $rTopConfig = $rTopKey && isset(\App\Models\BlogReaction::TYPES[$rTopKey]) ? \App\Models\BlogReaction::TYPES[$rTopKey] : null;
                                                $rTotal = $rSummary['total'] ?? 0;
                                            @endphp
                                            <div class="relative comment-reaction-container flex items-center gap-1" data-comment-id="{{ $reply->id }}">
                                                @auth
                                                    <!-- Floating Hover Dock -->
                                                    <div class="food-reaction-dock comment-reaction-dock flex items-center flex-nowrap whitespace-nowrap" role="toolbar" aria-label="Thả cảm xúc phản hồi">
                                                        @foreach(\App\Models\BlogReaction::TYPES as $key => $r)
                                                            <button type="button" 
                                                                    onclick="triggerCommentReaction({{ $reply->id }}, '{{ $key }}', event)" 
                                                                    class="food-reaction-item select-none active:scale-90 cursor-pointer"
                                                                    data-reaction="{{ $key }}"
                                                                    title="{{ $r['name'] }}">
                                                                <span class="reaction-emoji-symbol">{{ $r['emoji'] }}</span>
                                                                <span class="reaction-tooltip px-2 py-0.5 rounded-full bg-slate-900 text-white text-[10px] font-bold shadow-md">
                                                                    {{ $r['name'] }}
                                                                </span>
                                                            </button>
                                                        @endforeach
                                                    </div>

                                                    <!-- Reaction Button -->
                                                    <button type="button" 
                                                            onclick="triggerCommentReaction({{ $reply->id }}, null, event)" 
                                                            id="comment-react-btn-{{ $reply->id }}"
                                                            class="comment-react-btn flex items-center gap-0.5 transition-all cursor-pointer font-medium {{ $rReactionConfig ? $rReactionConfig['text_class'] . ' font-bold' : 'text-slate-500 hover:text-primary' }}"
                                                            data-reacted="{{ $rReactionKey ? '1' : '0' }}"
                                                            data-current="{{ $rReactionKey ?? '' }}"
                                                            title="Bấm để Thích, rê chuột để chọn cảm xúc">
                                                        <span class="comment-reaction-icon text-[13px]">
                                                            {{ $rReactionConfig ? $rReactionConfig['emoji'] : '👍' }}
                                                        </span>
                                                        <span class="comment-reaction-label text-[11px]">
                                                            {{ $rReactionConfig ? $rReactionConfig['name'] : 'Thích' }}
                                                        </span>
                                                    </button>
                                                @else
                                                    <a href="{{ route('login') }}" class="comment-react-btn flex items-center gap-0.5 text-slate-500 hover:text-primary transition-colors">
                                                        <span class="comment-reaction-icon text-[13px]">👍</span>
                                                        <span class="comment-reaction-label text-[11px]">Thích</span>
                                                    </a>
                                                @endauth

                                                <!-- Badge số tương tác (CHỈ HIỆN 1 ICON NHIỀU NHẤT) -->
                                                <span id="comment-reaction-count-{{ $reply->id }}" 
                                                      onclick="openCommentReactionsModal({{ $reply->id }}, event)"
                                                      class="comment-reaction-count-badge items-center gap-0.5 px-1.5 py-0.2 rounded-full text-[10px] font-bold transition-all bg-slate-100 text-slate-700 hover:bg-amber-100/90 hover:text-amber-900 border border-slate-200/80 shadow-2xs cursor-pointer hover:scale-105 active:scale-95 {{ $rTotal > 0 ? 'inline-flex' : 'hidden' }}"
                                                      title="Bấm để xem danh sách ai đã thả cảm xúc">
                                                    <span class="comment-top-icon text-[10px] leading-none shrink-0">
                                                        {{ $rTopConfig ? $rTopConfig['emoji'] : '👍' }}
                                                    </span>
                                                    <span class="comment-total-count text-[10px] font-bold tracking-tight">
                                                        {{ $rTotal }}
                                                    </span>
                                                </span>
                                            </div>

                                            @if($blog->allow_comments)
                                                <button type="button" onclick="toggleReplyBox({{ $comment->id }}, '{{ addslashes($reply->user->ho_ten) }}')" class="flex items-center gap-0.5 text-slate-500 hover:text-primary transition-colors">
                                                    <span class="material-symbols-outlined text-[14px]">reply</span>
                                                    <span>Trả lời</span>
                                                </button>
                                            @endif

                                            @if(auth()->check() && (auth()->id() === $reply->user_id || auth()->id() === $blog->user_id || auth()->user()->isAdmin()))
                                                <button type="button" onclick="openDeleteModal({{ $reply->id }})" class="flex items-center gap-0.5 text-slate-400 hover:text-rose-600 transition-colors ml-auto">
                                                    <span class="material-symbols-outlined text-[14px]">delete</span>
                                                    <span>Xóa</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div id="comments-empty-state" class="p-8 text-center rounded-2xl bg-surface-container/20 border border-dashed border-outline-variant/40">
                        <span class="material-symbols-outlined text-4xl text-text-muted mb-2">forum</span>
                        <p class="text-sm font-bold text-slate-700">Chưa có bình luận nào cho bài viết này</p>
                        <p class="text-xs text-text-muted mt-1">Hãy là người đầu tiên chia sẻ cảm nghĩ và nhận xét của bạn!</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Delete Comment Confirmation Modal -->
        <div id="delete-comment-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs hidden transition-all">
            <div class="bg-surface-card rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-outline-variant/40 animate-in fade-in zoom-in-95 duration-200">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mb-4 mx-auto">
                    <span class="material-symbols-outlined text-2xl">delete_forever</span>
                </div>
                <h4 class="font-title-md text-center text-on-background text-lg font-bold mb-2">Xác nhận xóa bình luận?</h4>
                <p class="text-xs text-on-surface-variant text-center mb-6 leading-relaxed">
                    Bình luận này sẽ bị xóa vĩnh viễn khỏi bài viết cùng với các phản hồi liên quan. Hành động này không thể hoàn tác.
                </p>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 py-2.5 rounded-full border border-surface-dim font-label-md text-xs font-semibold text-slate-700 hover:bg-surface-container transition-colors cursor-pointer">
                        Hủy bỏ
                    </button>
                    <button type="button" id="confirm-delete-btn" onclick="executeDeleteComment()" class="flex-1 py-2.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white font-label-md text-xs font-semibold transition-all shadow-xs cursor-pointer">
                        Xóa bình luận
                    </button>
                </div>
            </div>
        </div>

        
        <!-- Related Articles -->
        <section class="mt-12 pt-8 border-t border-surface-dim">
            <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">auto_stories</span>
                    </div>
                    <div>
                        <h3 class="font-display-lg text-xl sm:text-2xl text-on-background font-bold">Bài viết liên quan</h3>
                        <p class="text-xs text-on-surface-variant mt-0.5">Khám phá các bài viết cùng chủ đề ẩm thực hấp dẫn</p>
                    </div>
                </div>
                @if($relatedBlogs->count() > 0)
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-surface-container text-slate-600 border border-outline-variant/30">
                        {{ $relatedBlogs->count() }} bài gợi ý
                    </span>
                @endif
            </div>

            @if($relatedBlogs->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                    @foreach($relatedBlogs as $related)
                        <div class="relative bg-surface-card rounded-2xl overflow-hidden shadow-xs hover:shadow-xl border border-outline-variant/30 hover:border-amber-400 hover:ring-2 hover:ring-amber-400/30 transition-all duration-300 hover:-translate-y-1.5 flex flex-col group">
                            <a href="{{ route('blog.show', $related->slug) }}" class="flex flex-col flex-1">
                                {{-- Cover Image --}}
                                <div class="relative w-full h-[200px] overflow-hidden bg-slate-100 flex-shrink-0" style="height: 200px;">
                                    <img src="{{ $related->cover_image ? (Str::startsWith($related->cover_image, 'http') ? $related->cover_image : asset('storage/'.$related->cover_image)) : 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80' }}" 
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" 
                                         style="width: 100%; height: 100%; object-fit: cover; object-position: center;"
                                         alt="{{ $related->title }}"
                                         loading="lazy"
                                         onerror="this.src='https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80'">
                                    
                                    {{-- Category Badge --}}
                                    @if($related->category)
                                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md text-amber-700 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-xs border border-amber-200/60 uppercase tracking-wider">
                                            {{ $related->category->name }}
                                        </div>
                                    @endif
                                </div>

                            {{-- Content --}}
                            <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between gap-3">
                                <div>
                                    <h4 class="font-title-md text-[15px] sm:text-[16px] leading-snug text-on-background font-bold line-clamp-2 group-hover:text-primary transition-colors">
                                        {{ $related->title }}
                                    </h4>
                                    @if($related->excerpt)
                                        <p class="text-xs text-on-surface-variant line-clamp-2 leading-relaxed mt-2 text-slate-500 font-normal">
                                            {{ $related->excerpt }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Meta footer: Author, Date, Views --}}
                                <div class="pt-3 border-t border-surface-dim/70 flex items-center justify-between text-xs text-on-surface-variant gap-2 flex-wrap">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <img class="w-6 h-6 rounded-full object-cover border border-gray-100 shrink-0" 
                                             src="{{ $related->user->anh_dai_dien_url ?? 'https://ui-avatars.com/api/?name='.urlencode($related->user->ho_ten ?? 'User').'&background=random' }}" 
                                             alt="{{ $related->user->ho_ten ?? 'Tác giả' }}">
                                        <span class="font-semibold text-slate-700 truncate max-w-[110px] text-xs">
                                            {{ $related->user->ho_ten ?? 'Tác giả' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0 text-[11px] text-slate-400 font-medium">
                                        <span title="{{ ($related->published_at ?? $related->created_at)?->format('d/m/Y H:i') }}">
                                            {{ ($related->published_at ?? $related->created_at)?->diffForHumans() ?? 'Mới' }}
                                        </span>
                                        <span class="flex items-center gap-0.5 text-slate-500" title="{{ number_format($related->view_count ?? 0) }} lượt xem">
                                            <span class="material-symbols-outlined text-[13px] text-amber-500">visibility</span>
                                            <span>{{ number_format($related->view_count ?? 0) }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        {{-- Nút Bookmark ở góc trên bên phải card liên quan --}}
                        <button type="button" 
                                onclick="event.preventDefault(); event.stopPropagation(); toggleBlogBookmark(this, '{{ $related->id }}', '{{ $related->slug }}', '{{ addslashes($related->title) }}')" 
                                data-blog-id="{{ $related->id }}" 
                                data-blog-slug="{{ $related->slug }}" 
                                class="blog-bookmark-btn absolute top-3 right-3 z-10 w-8 h-8 bg-white/90 hover:bg-white backdrop-blur-md rounded-full flex items-center justify-center text-slate-700 hover:text-amber-500 shadow-sm hover:scale-110 active:scale-90 transition-transform cursor-pointer" 
                                title="Lưu bài viết"
                                aria-label="Lưu bài viết">
                            <span class="material-symbols-outlined text-[18px]">bookmark</span>
                        </button>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center rounded-2xl bg-surface-container/20 border border-dashed border-outline-variant/40">
                    <span class="material-symbols-outlined text-3xl text-text-muted mb-1">auto_stories</span>
                    <p class="text-xs text-on-surface-variant">Hiện chưa có thêm bài viết liên quan nào khác.</p>
                </div>
            @endif
        </section>
        
    </div>
    
    <!-- Sidebar (Right Column) -->
    <aside class="w-full md:w-1/3 space-y-stack-lg">
        
        <!-- Popular Articles -->
        <div class="bg-surface-card rounded-xl p-stack-md shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-outline-variant/30">
            <h3 class="font-title-md text-title-md text-on-background mb-stack-md border-l-4 border-primary pl-2">Bài viết phổ biến</h3>
            
            @foreach(\App\Models\Blog::where('status', 'published')->orderBy('view_count', 'desc')->limit(5)->get() as $popularBlog)
                <a href="{{ route('blog.show', $popularBlog->slug) }}" class="flex gap-3 mb-stack-md group cursor-pointer last:mb-0">
                    <div class="w-20 h-20 rounded-lg overflow-hidden shrink-0">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="{{ $popularBlog->cover_image ? (Str::startsWith($popularBlog->cover_image, 'http') ? $popularBlog->cover_image : asset('storage/'.$popularBlog->cover_image)) : 'https://placehold.co/100x100/png' }}" alt="{{ $popularBlog->title }}"/>
                    </div>
                    <div>
                        <h4 class="font-title-md text-[14px] leading-[20px] text-on-background group-hover:text-primary transition-colors line-clamp-2 mb-1">
                            {{ $popularBlog->title }}
                        </h4>
                        <div class="font-label-sm text-label-sm text-on-surface-variant">{{ ($popularBlog->published_at ?? $popularBlog->created_at)?->format('d/m/Y') ?? 'Mới' }}</div>
                    </div>
                </a>
            @endforeach
        </div>
        
        <!-- Hot Topics -->
        <div class="bg-surface-card rounded-xl p-stack-md shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-outline-variant/30">
            <h3 class="font-title-md text-title-md text-on-background mb-stack-md border-l-4 border-primary pl-2">Chủ đề hot</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $idx => $tag)
                    @if($idx % 4 == 0)
                        <a class="px-3 py-1 bg-secondary-fixed text-on-secondary-fixed-variant rounded-full font-label-md text-label-md hover:bg-secondary hover:text-on-secondary transition-colors" href="{{ route('blog.index', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a>
                    @elseif($idx % 4 == 1)
                        <a class="px-3 py-1 bg-primary-fixed text-on-primary-fixed-variant rounded-full font-label-md text-label-md hover:bg-primary hover:text-on-primary transition-colors" href="{{ route('blog.index', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a>
                    @else
                        <a class="px-3 py-1 bg-surface-container rounded-full font-label-md text-label-md text-on-surface-variant hover:bg-outline-variant hover:text-on-primary-container transition-colors" href="{{ route('blog.index', ['tag' => $tag->slug]) }}">#{{ $tag->name }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        
        <!-- Newsletter -->
        <div class="bg-primary-container rounded-xl p-stack-md shadow-[0_4px_20px_rgba(0,0,0,0.05)] text-on-primary-container text-center relative overflow-hidden">
            <!-- Decorative abstract background pattern -->
            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0); background-size: 16px 16px;"></div>
            <span class="material-symbols-outlined text-[40px] mb-2 z-10 relative text-white">mail</span>
            <h3 class="font-title-md text-title-md mb-2 z-10 relative text-white font-bold">Đừng bỏ lỡ quán ngon!</h3>
            <p class="font-body-sm text-body-sm mb-4 z-10 relative opacity-90 text-white/90">Đăng ký để nhận danh sách review ẩm thực mới nhất mỗi tuần từ Quán Mới.</p>

            @if(session('newsletter_success'))
                <div class="mb-3 p-3 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-xl text-xs font-bold flex items-center gap-2 text-left z-10 relative shadow-2xs">
                    <span class="material-symbols-outlined text-base text-emerald-600">check_circle</span>
                    <span>{{ session('newsletter_success') }}</span>
                </div>
            @elseif(session('newsletter_info'))
                <div class="mb-3 p-3 bg-amber-100 text-amber-900 border border-amber-300 rounded-xl text-xs font-bold flex items-center gap-2 text-left z-10 relative shadow-2xs">
                    <span class="material-symbols-outlined text-base text-amber-600">info</span>
                    <span>{{ session('newsletter_info') }}</span>
                </div>
            @endif

            <form id="blog-show-newsletter-form" action="{{ route('blog.newsletter.subscribe') }}" method="POST" onsubmit="handleNewsletterSubmit(event, this)" class="flex flex-col gap-2.5 z-10 relative">
                @csrf
                <input type="hidden" name="source" value="blog_show">
                <input class="w-full rounded-lg border-none bg-surface-container-lowest focus:ring-2 focus:ring-white px-4 py-3 font-body-sm text-body-sm text-on-background placeholder:text-text-muted outline-none text-slate-800" 
                       name="email" 
                       value="{{ auth()->check() ? auth()->user()->email : old('email') }}" 
                       placeholder="Email của bạn..." 
                       type="email" 
                       required/>
                <button class="w-full bg-white text-primary rounded-lg py-3 font-label-md text-label-md font-bold hover:bg-slate-100 transition-all active:scale-[0.98] shadow-sm flex items-center justify-center gap-1.5 cursor-pointer" type="submit">
                     <span class="btn-text">Đăng ký ngay</span>
                     <span class="material-symbols-outlined text-base">send</span>
                </button>
                <div class="newsletter-feedback hidden text-xs font-semibold p-2.5 rounded-xl text-left"></div>
            </form>
        </div>
        
    </aside>
</main>

<!-- Reactions List Modal (Xem ai đã thả cảm xúc - Root level, z-index 999999) -->
<div id="reactions-list-modal" 
     class="hidden"
     style="position: fixed; inset: 0; z-index: 999999; background: rgba(0, 0, 0, 0.65); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; padding: 16px; overflow-y: auto;"
     onclick="if(event.target === this) closeReactionsModal()">
    <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 460px; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4); border: 1px solid #e2e8f0; overflow: hidden; margin: auto;">
        <!-- Modal Header -->
        <div style="padding: 14px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: #fafafa; flex-shrink: 0;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span id="reactions-modal-icon" style="font-size: 18px;">✨</span>
                <h4 id="reactions-modal-title" style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">Người đã thả cảm xúc</h4>
            </div>
            <button type="button" 
                    onclick="closeReactionsModal()" 
                    style="width: 32px; height: 32px; border-radius: 9999px; border: none; background: transparent; display: flex; align-items: center; justify-content: center; color: #64748b; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a';"
                    onmouseout="this.style.background='transparent'; this.style.color='#64748b';"
                    title="Đóng (Esc)">
                <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
            </button>
        </div>

        <!-- Tabs Filter (Tất cả, ❤️, 🤤, 🔥, 👍, 😢, 😡) -->
        <div id="reactions-modal-tabs" style="padding: 8px 16px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 6px; overflow-x: auto; white-space: nowrap; flex-shrink: 0; background: #ffffff;">
            <!-- Dynamic tabs inserted here -->
        </div>

        <!-- Users List Body -->
        <div id="reactions-modal-body" style="padding: 8px 16px; overflow-y: auto; flex: 1 1 auto; max-height: 400px; min-height: 120px; overscroll-behavior: contain;">
            <!-- Dynamic user rows inserted here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeDeleteCommentId = null;

    // Character counter helper
    function updateCharCount(el, targetId) {
        const len = el.value.length;
        const target = document.getElementById(targetId);
        if (target) {
            target.textContent = `${len}/1000 ký tự`;
            if (len > 900) {
                target.classList.add('text-amber-600', 'font-bold');
            } else {
                target.classList.remove('text-amber-600', 'font-bold');
            }
        }
    }

    // Toggle reply form visibility
    function toggleReplyBox(commentId, authorName = '') {
        const box = document.getElementById(`reply-box-${commentId}`);
        if (!box) return;
        
        const isHidden = box.classList.contains('hidden');
        // Hide all other open reply boxes
        document.querySelectorAll('[id^="reply-box-"]').forEach(b => {
            if (b.id !== `reply-box-${commentId}`) b.classList.add('hidden');
        });

        if (isHidden) {
            hideAlertBox();
            box.classList.remove('hidden');
            const input = document.getElementById(`reply-input-${commentId}`);
            if (input) {
                if (authorName && !input.value.trim()) {
                    input.value = `@${authorName} `;
                }
                input.focus();
                // Move cursor to end
                input.setSelectionRange(input.value.length, input.value.length);
            }
        } else {
            box.classList.add('hidden');
        }
    }

    // Auto-dismiss alert banner when user focuses or types new input
    function hideAlertBox() {
        const alertBox = document.getElementById('comment-alert-box');
        if (alertBox && !alertBox.classList.contains('hidden')) {
            alertBox.classList.add('hidden');
        }
    }

    // Dismiss old notifications immediately when typing or clicking into any comment/reply input
    document.addEventListener('input', function(e) {
        if (e.target && e.target.tagName === 'TEXTAREA') {
            hideAlertBox();
        }
    });

    document.addEventListener('focusin', function(e) {
        if (e.target && e.target.tagName === 'TEXTAREA') {
            hideAlertBox();
        }
    });

    // Open & Close Delete Confirmation Modal
    function openDeleteModal(commentId) {
        hideAlertBox();
        activeDeleteCommentId = commentId;
        const modal = document.getElementById('delete-comment-modal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        activeDeleteCommentId = null;
        const modal = document.getElementById('delete-comment-modal');
        if (modal) modal.classList.add('hidden');
    }

    // Execute Delete
    function executeDeleteComment() {
        if (!activeDeleteCommentId) return;
        const targetCommentId = activeDeleteCommentId;

        const btn = document.getElementById('confirm-delete-btn');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Đang xóa...';
        }

        const deleteUrl = "{{ url('/blog/' . $blog->slug . '/binh-luan') }}/" + targetCommentId;

        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Xóa bình luận';
            }
            closeDeleteModal();

            if (data.success) {
                const el = document.getElementById(`comment-block-${targetCommentId}`);
                if (el) {
                    el.style.transition = 'all 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-8px) scale(0.96)';
                    el.style.maxHeight = el.scrollHeight + 'px';
                    setTimeout(() => {
                        el.style.maxHeight = '0px';
                        el.style.paddingTop = '0px';
                        el.style.paddingBottom = '0px';
                        el.style.marginTop = '0px';
                        el.style.marginBottom = '0px';
                        el.style.overflow = 'hidden';
                    }, 50);
                    setTimeout(() => {
                        el.remove();
                        // Check if list is empty
                        const list = document.getElementById('comments-list-container');
                        if (list && list.querySelectorAll('.comment-item').length === 0) {
                            list.innerHTML = `
                                <div id="comments-empty-state" class="p-8 text-center rounded-2xl bg-surface-container/20 border border-dashed border-outline-variant/40 animate-in fade-in duration-300">
                                    <span class="material-symbols-outlined text-4xl text-text-muted mb-2">forum</span>
                                    <p class="text-sm font-bold text-slate-700">Chưa có bình luận nào cho bài viết này</p>
                                    <p class="text-xs text-text-muted mt-1">Hãy là người đầu tiên chia sẻ cảm nghĩ và nhận xét của bạn!</p>
                                </div>
                            `;
                        }
                    }, 350);
                }

                const badge = document.getElementById('comments-count-badge');
                if (badge && data.comment_count !== undefined) {
                    badge.textContent = data.comment_count;
                }
                const headerBadge = document.getElementById('blog-header-comment-count');
                if (headerBadge && data.comment_count !== undefined) {
                    headerBadge.textContent = data.comment_count;
                }

                showAlert('info', 'Thông báo', 'Đã xóa bình luận thành công.');
            } else {
                showAlert('error', 'Lỗi xóa bình luận', data.message || 'Không thể xóa bình luận.');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Xóa bình luận';
            }
            closeDeleteModal();
            showAlert('error', 'Lỗi hệ thống', 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.');
        });
    }

    // ══════════════════════════════════════════════════════════════════════════
    // THẢ CẢM XÚC BÌNH LUẬN (COMMENT FOOD REACTIONS)
    // - Hỗ trợ 6 loại cảm xúc: ❤️ Yêu thích, 🤤 Thèm quá, 🔥 Phải thử ngay, 👍 Hữu ích, 😢 Khóc, 😡 Phẫn nộ
    // - Rê chuột (hover): Hiện reaction dock lơ lửng với animation
    // - Khi hiện số tương tác: CHỈ HIỆN 1 ICON NHIỀU NHẤT + TỔNG SỐ LƯỢNG
    // ══════════════════════════════════════════════════════════════════════════
    const COMMENT_REACTIONS_MAP = {
        'like':  { emoji: '👍', name: 'Hữu ích',   color: '#f59e0b', text_class: 'text-amber-700' },
        'love':  { emoji: '❤️', name: 'Yêu thích', color: '#e11d48', text_class: 'text-rose-600' },
        'drool': { emoji: '🤤', name: 'Thèm quá',  color: '#ea580c', text_class: 'text-orange-600' },
        'fire':  { emoji: '🔥', name: 'Phải thử',  color: '#d97706', text_class: 'text-amber-600' },
        'cry':   { emoji: '😢', name: 'Khóc',      color: '#0284c7', text_class: 'text-sky-600' },
        'angry': { emoji: '😡', name: 'Phẫn nộ',   color: '#dc2626', text_class: 'text-red-600' }
    };

    window.triggerCommentReaction = async function(commentId, reactionKey = null, event = null) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // Đóng tất cả dock cảm xúc đang mở
        document.querySelectorAll('.comment-reaction-dock').forEach(d => d.classList.remove('dock-open'));

        @guest
            if (window.showToast) {
                window.showToast({
                    eyebrow: 'Cảm xúc bình luận',
                    title: 'Đăng nhập để thả cảm xúc',
                    message: 'Vui lòng đăng nhập tài khoản để thả cảm xúc cho bình luận này!',
                    type: 'warning',
                    actionText: 'Đăng nhập ngay',
                    actionUrl: "{{ route('login') }}"
                });
            } else {
                window.location.href = "{{ route('login') }}";
            }
            return;
        @endguest

        const btn = document.getElementById(`comment-react-btn-${commentId}`);
        const currentReaction = btn ? (btn.dataset.current || null) : null;

        // Nếu bấm trực tiếp vào nút chính:
        // - Nếu đã thả cảm xúc trước đó -> Bấm tiếp sẽ gỡ (toggle off)
        // - Nếu chưa thả cảm xúc -> Mặc định là 'like' (👍 Hữu ích)
        let targetReaction = reactionKey;
        if (!targetReaction) {
            targetReaction = currentReaction || 'like';
        }

        const rItem = COMMENT_REACTIONS_MAP[targetReaction] || COMMENT_REACTIONS_MAP['like'];

        // Phát âm thanh đa âm sắc theo cảm xúc
        if (typeof window.playReactionPopSound === 'function') {
            window.playReactionPopSound(targetReaction);
        }

        // Chùm pháo hoa hạt sparkles + shockwave ring nếu có vị trí chuột
        if (typeof window.spawnFloatingEmoji === 'function') {
            const clickX = event?.clientX || (event?.touches && event.touches[0] ? event.touches[0].clientX : window.innerWidth / 2);
            const clickY = event?.clientY || (event?.touches && event.touches[0] ? event.touches[0].clientY : window.innerHeight / 2);
            window.spawnFloatingEmoji(rItem.emoji, clickX, clickY, rItem.color, targetReaction);
        }

        // Hiệu ứng nhún đàn hồi trên nút reaction của comment
        if (btn) {
            btn.classList.remove('reaction-punch');
            void btn.offsetWidth;
            btn.classList.add('reaction-punch');
        }

        const likeUrl = "{{ url('/blog/' . $blog->slug . '/binh-luan') }}/" + commentId + "/thich";

        try {
            const res = await fetch(likeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ reaction: targetReaction })
            });

            if (res.status === 401) {
                if (window.showToast) {
                    window.showToast({
                        eyebrow: 'Cảm xúc bình luận',
                        title: 'Phiên đăng nhập hết hạn',
                        message: 'Vui lòng đăng nhập lại.',
                        type: 'warning',
                        actionText: 'Đăng nhập',
                        actionUrl: "{{ route('login') }}"
                    });
                }
                return;
            }

            const data = await res.json();
            if (data.success) {
                updateCommentReactionUI(commentId, data);
            }
        } catch (err) {
            console.error('Lỗi khi thả cảm xúc bình luận:', err);
        }
    };

    // Cập nhật giao diện cảm xúc của bình luận / phản hồi
    window.updateCommentReactionUI = function(commentId, data) {
        const btn = document.getElementById(`comment-react-btn-${commentId}`);
        const countBadge = document.getElementById(`comment-reaction-count-${commentId}`);

        if (btn) {
            btn.dataset.reacted = data.liked ? '1' : '0';
            btn.dataset.current = data.current_reaction || '';

            const iconEl = btn.querySelector('.comment-reaction-icon');
            const labelEl = btn.querySelector('.comment-reaction-label');

            // Reset class chuẩn thiết kế
            const isSmall = btn.closest('.reply-item') !== null;
            btn.className = `comment-react-btn flex items-center ${isSmall ? 'gap-0.5' : 'gap-1'} transition-all cursor-pointer font-medium active:scale-95`;

            if (data.liked && data.reaction_info) {
                btn.classList.add(data.reaction_info.text_class || 'text-amber-700', 'font-bold');
                if (iconEl) iconEl.textContent = data.reaction_info.emoji;
                if (labelEl) labelEl.textContent = data.reaction_info.name;
            } else {
                btn.classList.add('text-slate-500', 'hover:text-primary');
                if (iconEl) iconEl.textContent = '👍';
                if (labelEl) labelEl.textContent = 'Thích';
            }
        }

        // Cập nhật badge số tương tác: CHỈ HIỆN 1 ICON NHIỀU NHẤT
        if (countBadge) {
            if (data.total_count > 0) {
                countBadge.classList.remove('hidden');
                countBadge.classList.add('inline-flex');

                const topIconEl = countBadge.querySelector('.comment-top-icon');
                const totalEl = countBadge.querySelector('.comment-total-count');

                if (topIconEl) {
                    topIconEl.textContent = data.top_one_emoji || '👍';
                }
                if (totalEl) {
                    totalEl.textContent = data.formatted_count || data.total_count;
                }

                countBadge.title = `${data.top_one_name || 'Hữu ích'}: ${data.breakdown?.[data.top_one] || 0} lượt (Tổng ${data.total_count} cảm xúc)`;

                countBadge.classList.remove('count-pop-anim');
                void countBadge.offsetWidth;
                countBadge.classList.add('count-pop-anim');
            } else {
                countBadge.classList.add('hidden');
                countBadge.classList.remove('inline-flex');
            }
        }
    };

    window.handleLikeComment = function(commentId) {
        window.triggerCommentReaction(commentId, null);
    };

    // Quản lý mở / đóng dock cảm xúc bình luận bằng event delegation
    (function initCommentReactionDelegation() {
        let hoverTimers = new Map();
        let closeTimers = new Map();

        document.addEventListener('mouseover', function(e) {
            // 1. Rê chuột vào badge xem danh sách người thả cảm xúc: KHÔNG mở dock và đóng ngay dock nếu đang mở
            if (e.target.closest('.comment-reaction-count-badge')) {
                const container = e.target.closest('.comment-reaction-container');
                if (container) {
                    if (hoverTimers.has(container)) {
                        clearTimeout(hoverTimers.get(container));
                        hoverTimers.delete(container);
                    }
                    const dock = container.querySelector('.food-reaction-dock');
                    if (dock) dock.classList.remove('dock-open');
                }
                return;
            }

            // 2. Chỉ kích hoạt mở dock khi rê chuột vào nút Thích (.comment-react-btn) hoặc rê chuột trong chính dock (.comment-reaction-dock)
            const reactBtn = e.target.closest('.comment-react-btn');
            const inDock = e.target.closest('.comment-reaction-dock');

            if (!reactBtn && !inDock) return;

            const container = (reactBtn || inDock).closest('.comment-reaction-container');
            if (!container) return;

            if (closeTimers.has(container)) {
                clearTimeout(closeTimers.get(container));
                closeTimers.delete(container);
            }

            if (!hoverTimers.has(container)) {
                const timer = setTimeout(() => {
                    const dock = container.querySelector('.food-reaction-dock');
                    if (dock) dock.classList.add('dock-open');
                }, 260);
                hoverTimers.set(container, timer);
            }
        });

        document.addEventListener('mouseout', function(e) {
            const container = e.target.closest('.comment-reaction-container');
            if (!container) return;

            // Nếu con trỏ chuột vẫn nằm trong nút react hoặc dock của container này thì không đóng
            if (e.relatedTarget) {
                const relatedBtn = e.relatedTarget.closest && e.relatedTarget.closest('.comment-react-btn');
                const relatedDock = e.relatedTarget.closest && e.relatedTarget.closest('.comment-reaction-dock');
                if ((relatedBtn || relatedDock) && (relatedBtn || relatedDock).closest('.comment-reaction-container') === container) {
                    return;
                }
            }

            if (hoverTimers.has(container)) {
                clearTimeout(hoverTimers.get(container));
                hoverTimers.delete(container);
            }

            const timer = setTimeout(() => {
                const dock = container.querySelector('.food-reaction-dock');
                if (dock) dock.classList.remove('dock-open');
            }, 250);
            closeTimers.set(container, timer);
        });

        // Hiệu ứng lướt Mac-dock phóng to từng emoji theo khoảng cách chuột
        document.addEventListener('mousemove', function(e) {
            const dock = e.target.closest('.comment-reaction-dock');
            if (!dock) return;
            const mouseX = e.clientX;
            dock.querySelectorAll('.food-reaction-item').forEach(item => {
                const rect = item.getBoundingClientRect();
                const itemCenterX = rect.left + rect.width / 2;
                const dist = Math.abs(mouseX - itemCenterX);
                if (dist < 48) {
                    const scale = 1 + (0.35 * (1 - dist / 48));
                    item.style.setProperty('--dock-scale', scale.toFixed(2));
                } else {
                    item.style.removeProperty('--dock-scale');
                }
            });
        });

        // Đóng dock khi click bên ngoài hoặc click vào badge
        document.addEventListener('click', function(e) {
            if (e.target.closest('.comment-reaction-count-badge') || !e.target.closest('.comment-reaction-container')) {
                document.querySelectorAll('.comment-reaction-dock').forEach(d => d.classList.remove('dock-open'));
            }
        });
    })();

    // ══════════════════════════════════════════════════════════════════════════
    // MODAL XEM DANH SÁCH AI ĐÃ THẢ CẢM XÚC (WHO REACTED MODAL)
    // ══════════════════════════════════════════════════════════════════════════
    let reactionsModalContext = {
        type: null,
        id: null,
        data: null,
        activeFilter: 'all'
    };

    window.openBlogReactionsModal = function(event = null) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        document.querySelectorAll('.food-reaction-dock, .comment-reaction-dock').forEach(d => d.classList.remove('dock-open'));
        const url = "{{ url('/blog/' . $blog->slug . '/danh-sach-cam-xuc') }}";
        fetchAndShowReactionsModal('blog', null, url, 'Cảm xúc bài viết');
    };

    window.openCommentReactionsModal = function(commentId, event = null) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        document.querySelectorAll('.food-reaction-dock, .comment-reaction-dock').forEach(d => d.classList.remove('dock-open'));
        const url = "{{ url('/blog/' . $blog->slug . '/binh-luan') }}/" + commentId + "/danh-sach-cam-xuc";
        fetchAndShowReactionsModal('comment', commentId, url, 'Cảm xúc bình luận');
    };

    function fetchAndShowReactionsModal(type, id, url, defaultTitle) {
        const modal = document.getElementById('reactions-list-modal');
        const titleEl = document.getElementById('reactions-modal-title');
        const tabsEl = document.getElementById('reactions-modal-tabs');
        const bodyEl = document.getElementById('reactions-modal-body');

        if (!modal) return;

        reactionsModalContext = {
            type: type,
            id: id,
            data: null,
            activeFilter: 'all'
        };

        if (titleEl) titleEl.textContent = defaultTitle;
        if (tabsEl) tabsEl.innerHTML = '';
        if (bodyEl) {
            bodyEl.innerHTML = `
                <div style="padding: 48px 16px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; gap: 8px;">
                    <div style="width: 28px; height: 28px; border: 2.5px solid #ea580c; border-top-color: transparent; border-radius: 9999px; animation: spin 0.8s linear infinite;"></div>
                    <span style="font-size: 12px; font-weight: 500;">Đang tải danh sách cảm xúc...</span>
                </div>
            `;
        }

        // Mở modal dạng flex và khóa cuộn trang
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                reactionsModalContext.data = data;
                renderReactionsModalContent();
            } else {
                if (bodyEl) {
                    bodyEl.innerHTML = `
                        <div style="padding: 32px 16px; text-align: center; font-size: 13px; color: #f43f5e; font-weight: 600;">
                            Không thể tải danh sách cảm xúc. Vui lòng thử lại.
                        </div>
                    `;
                }
            }
        })
        .catch(err => {
            if (bodyEl) {
                bodyEl.innerHTML = `
                    <div style="padding: 32px 16px; text-align: center; font-size: 13px; color: #f43f5e; font-weight: 600;">
                        Lỗi kết nối đến máy chủ. Vui lòng thử lại sau.
                    </div>
                `;
            }
        });
    }

    function renderReactionsModalContent() {
        const data = reactionsModalContext.data;
        if (!data) return;

        const tabsEl = document.getElementById('reactions-modal-tabs');
        const bodyEl = document.getElementById('reactions-modal-body');
        const activeFilter = reactionsModalContext.activeFilter || 'all';

        if (!tabsEl || !bodyEl) return;

        // 1. Render Tabs
        tabsEl.innerHTML = '';

        // Tab "Tất cả"
        const allTab = document.createElement('button');
        allTab.type = 'button';
        const isAllActive = (activeFilter === 'all');
        allTab.style.cssText = `padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; flex-shrink: 0; transition: all 0.2s; ${
            isAllActive 
                ? 'background: #0f172a; color: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.15);' 
                : 'background: #f1f5f9; color: #475569;'
        }`;
        allTab.innerHTML = `Tất cả <span style="margin-left: 4px; opacity: 0.85;">${data.total_count}</span>`;
        allTab.onclick = () => switchReactionsFilter('all');
        tabsEl.appendChild(allTab);

        // Các Tab emoji từng loại
        const REACTION_CONFIG = {
            'like':  { emoji: '👍', name: 'Hữu ích' },
            'love':  { emoji: '❤️', name: 'Yêu thích' },
            'drool': { emoji: '🤤', name: 'Thèm quá' },
            'fire':  { emoji: '🔥', name: 'Phải thử' },
            'cry':   { emoji: '😢', name: 'Khóc' },
            'angry': { emoji: '😡', name: 'Phẫn nộ' }
        };

        Object.keys(REACTION_CONFIG).forEach(key => {
            const count = data.counts?.[key] || 0;
            if (count > 0) {
                const conf = REACTION_CONFIG[key];
                const tab = document.createElement('button');
                tab.type = 'button';
                const isActive = (activeFilter === key);
                tab.style.cssText = `padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px; flex-shrink: 0; transition: all 0.2s; ${
                    isActive 
                        ? 'background: #ea580c; color: #ffffff; box-shadow: 0 2px 6px rgba(234, 88, 12, 0.35);' 
                        : 'background: #f1f5f9; color: #334155;'
                }`;
                tab.innerHTML = `<span>${conf.emoji}</span><span>${count}</span>`;
                tab.onclick = () => switchReactionsFilter(key);
                tabsEl.appendChild(tab);
            }
        });

        // 2. Lọc danh sách users theo activeFilter
        const users = (activeFilter === 'all') 
            ? data.users 
            : data.users.filter(u => u.reaction === activeFilter);

        // 3. Render Users List
        bodyEl.innerHTML = '';
        if (users.length === 0) {
            bodyEl.innerHTML = `
                <div style="padding: 40px 16px; text-align: center; font-size: 12px; color: #94a3b8;">
                    <span style="font-size: 32px; display: block; margin-bottom: 8px;">🍃</span>
                    Chưa có ai thả cảm xúc này
                </div>
            `;
            return;
        }

        users.forEach(u => {
            const row = document.createElement('div');
            row.style.cssText = 'padding: 10px 8px; display: flex; align-items: center; justify-content: space-between; gap: 12px; border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease; border-radius: 12px; margin-bottom: 2px;';
            row.onmouseover = () => row.style.background = '#f8fafc';
            row.onmouseout = () => row.style.background = 'transparent';

            const authorBadge = u.is_author 
                ? `<span style="background: rgba(234, 88, 12, 0.1); color: #ea580c; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 9999px; border: 1px solid rgba(234, 88, 12, 0.25); white-space: nowrap;">Tác giả</span>`
                : '';
            const adminBadge = u.is_admin 
                ? `<span style="background: #faf5ff; color: #7e22ce; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 9999px; border: 1px solid #e9d5ff; white-space: nowrap;">Admin</span>`
                : '';

            row.innerHTML = `
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1;">
                    <div style="position: relative; width: 40px; height: 40px; flex-shrink: 0;">
                        <img src="${u.avatar}" 
                             style="width: 40px; height: 40px; border-radius: 9999px; object-fit: cover; border: 1px solid #e2e8f0; display: block;" 
                             alt="${u.name}"
                             onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=ffdbcc&color=a04100&bold=true&size=128'">
                        <span style="position: absolute; bottom: -2px; right: -2px; width: 18px; height: 18px; border-radius: 9999px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.2); border: 1.5px solid #ffffff; display: flex; align-items: center; justify-content: center; font-size: 11px; line-height: 1; user-select: none;">
                            ${u.reaction_emoji}
                        </span>
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                            <span style="font-weight: 700; font-size: 13px; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${u.name}</span>
                            ${authorBadge}
                            ${adminBadge}
                        </div>
                        <span style="font-size: 11px; color: #94a3b8; display: block; margin-top: 1px;">${u.created_at_human}</span>
                    </div>
                </div>
                <div style="flex-shrink: 0;">
                    <span style="font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 9999px; border: 1px solid ${u.reaction_color}30; background: ${u.reaction_color}12; color: ${u.reaction_color}; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                        <span>${u.reaction_emoji}</span>
                        <span>${u.reaction_name}</span>
                    </span>
                </div>
            `;
            bodyEl.appendChild(row);
        });
    }

    window.switchReactionsFilter = function(reactionKey) {
        reactionsModalContext.activeFilter = reactionKey;
        renderReactionsModalContent();
    };

    window.closeReactionsModal = function() {
        const modal = document.getElementById('reactions-list-modal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
        }
        document.body.style.overflow = '';
    };

    // Phím Esc đóng modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReactionsModal();
        }
    });

    // Chống lăn chuột làm trôi trang nền khi đang xem danh sách cảm xúc
    (function initModalScrollLock() {
        const modal = document.getElementById('reactions-list-modal');
        if (!modal) return;

        modal.addEventListener('wheel', function(e) {
            const bodyEl = document.getElementById('reactions-modal-body');
            // Nếu rê chuột trên header, tabs hoặc backdrop nền mờ -> Chặn hoàn toàn lăn chuột nền
            if (!bodyEl || !bodyEl.contains(e.target)) {
                e.preventDefault();
                return;
            }

            // Nếu lăn chuột trong danh sách người dùng nhưng đã chạm đỉnh hoặc chạm đáy -> Chặn không cho tràn ra cuộn trang nền
            const atTop = bodyEl.scrollTop <= 0 && e.deltaY < 0;
            const atBottom = (bodyEl.scrollTop + bodyEl.clientHeight >= bodyEl.scrollHeight - 1) && e.deltaY > 0;
            if (atTop || atBottom) {
                e.preventDefault();
            }
        }, { passive: false });

        let touchStartY = 0;
        modal.addEventListener('touchstart', function(e) {
            if (e.touches && e.touches.length > 0) {
                touchStartY = e.touches[0].clientY;
            }
        }, { passive: true });

        modal.addEventListener('touchmove', function(e) {
            const bodyEl = document.getElementById('reactions-modal-body');
            if (!bodyEl || !bodyEl.contains(e.target)) {
                e.preventDefault();
                return;
            }

            if (e.touches && e.touches.length > 0) {
                const currentY = e.touches[0].clientY;
                const deltaY = touchStartY - currentY;
                const atTop = bodyEl.scrollTop <= 0 && deltaY < 0;
                const atBottom = (bodyEl.scrollTop + bodyEl.clientHeight >= bodyEl.scrollHeight - 1) && deltaY > 0;
                if (atTop || atBottom) {
                    e.preventDefault();
                }
            }
        }, { passive: false });
    })();

    // Alert display helper
    // Alert display helper
    function showAlert(type, title, message) {
        const alertBox = document.getElementById('comment-alert-box');
        const alertTitle = document.getElementById('comment-alert-title');
        const alertMsg = document.getElementById('comment-alert-msg');
        const alertIcon = document.getElementById('comment-alert-icon');
        if (!alertBox) return;

        if (type === 'success') {
            alertBox.className = 'mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs bg-emerald-50 border border-emerald-200 text-emerald-800 animate-in fade-in duration-300';
            alertIcon.className = 'material-symbols-outlined text-xl text-emerald-600 shrink-0';
            alertIcon.textContent = 'check_circle';
        } else if (type === 'moderated') {
            alertBox.className = 'mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs bg-rose-50 border border-rose-200 text-rose-800 animate-in shake duration-300';
            alertIcon.className = 'material-symbols-outlined text-xl text-rose-600 shrink-0';
            alertIcon.textContent = 'error';
        } else {
            alertBox.className = 'mb-6 p-4 rounded-2xl text-xs font-bold transition-all flex items-start gap-3 shadow-2xs bg-amber-50 border border-amber-200 text-amber-800 animate-in fade-in duration-300';
            alertIcon.className = 'material-symbols-outlined text-xl text-amber-600 shrink-0';
            alertIcon.textContent = 'info';
        }

        alertTitle.textContent = title;
        alertMsg.textContent = message;
        alertBox.classList.remove('hidden');

        alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Handle Comment / Reply Submission
    function handleCommentSubmit(e, formId, parentId = null) {
        e.preventDefault();
        hideAlertBox();
        const form = document.getElementById(formId);
        if (!form) return;

        const isReply = (parentId !== null);
        const textarea = isReply 
            ? document.getElementById(`reply-input-${parentId}`)
            : document.getElementById('comment-content-input');
        const btn = isReply 
            ? document.getElementById(`reply-btn-${parentId}`)
            : document.getElementById('comment-submit-btn');
        const btnText = isReply 
            ? document.getElementById(`reply-btn-text-${parentId}`)
            : document.getElementById('comment-submit-text');
        const counterBadge = document.getElementById('comments-count-badge');
        const emptyState = document.getElementById('comments-empty-state');
        const listContainer = document.getElementById('comments-list-container');

        const content = textarea.value.trim();
        if (!content) return;

        // Loading state
        btn.disabled = true;
        const originalText = btnText.textContent;
        btnText.textContent = 'Đang gửi...';
        btn.classList.add('opacity-75');

        const payload = { content: content };
        if (parentId) {
            payload.parent_id = parentId;
        }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => {
            return res.json().then(data => ({ status: res.status, body: data })).catch(() => ({
                status: res.status,
                body: { success: false, message: 'Phản hồi từ máy chủ không hợp lệ (' + res.status + ')' }
            }));
        })
        .then(({ status, body }) => {
            btn.disabled = false;
            btnText.textContent = originalText;
            btn.classList.remove('opacity-75');

            if (status === 200 && body.success) {
                showAlert('success', 'Thành công', body.message || 'Bình luận đã được đăng thành công!');
                textarea.value = '';

                // Reset char counter
                if (!isReply) {
                    const charSpan = document.getElementById('main-char-count');
                    if (charSpan) charSpan.textContent = '0/1000 ký tự';
                }

                // Remove empty state if visible
                if (emptyState) emptyState.remove();

                const c = body.comment;
                const authorBadge = c.is_author 
                    ? `<span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5 border border-primary/20"><span class="material-symbols-outlined text-[12px]">edit_note</span>Tác giả</span>`
                    : '';
                const adminBadge = c.is_admin 
                    ? `<span class="bg-purple-50 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5 border border-purple-200"><span class="material-symbols-outlined text-[12px]">shield_person</span>Quản trị viên</span>`
                    : '';

                const renderReactionDockHtml = (id) => `
                    <div class="food-reaction-dock comment-reaction-dock flex items-center flex-nowrap whitespace-nowrap" role="toolbar" aria-label="Thả cảm xúc">
                        <button type="button" onclick="triggerCommentReaction(${id}, 'like', event)" class="food-reaction-item select-none active:scale-90 cursor-pointer" data-reaction="like" title="Hữu ích">
                            <span class="reaction-emoji-symbol">👍</span>
                            <span class="reaction-tooltip px-2 py-0.5 rounded-full bg-slate-900 text-white text-[10px] font-bold shadow-md">Hữu ích</span>
                        </button>
                        <button type="button" onclick="triggerCommentReaction(${id}, 'love', event)" class="food-reaction-item select-none active:scale-90 cursor-pointer" data-reaction="love" title="Yêu thích">
                            <span class="reaction-emoji-symbol">❤️</span>
                            <span class="reaction-tooltip px-2 py-0.5 rounded-full bg-slate-900 text-white text-[10px] font-bold shadow-md">Yêu thích</span>
                        </button>
                        <button type="button" onclick="triggerCommentReaction(${id}, 'drool', event)" class="food-reaction-item select-none active:scale-90 cursor-pointer" data-reaction="drool" title="Thèm quá">
                            <span class="reaction-emoji-symbol">🤤</span>
                            <span class="reaction-tooltip px-2 py-0.5 rounded-full bg-slate-900 text-white text-[10px] font-bold shadow-md">Thèm quá</span>
                        </button>
                        <button type="button" onclick="triggerCommentReaction(${id}, 'fire', event)" class="food-reaction-item select-none active:scale-90 cursor-pointer" data-reaction="fire" title="Phải thử ngay">
                            <span class="reaction-emoji-symbol">🔥</span>
                            <span class="reaction-tooltip px-2 py-0.5 rounded-full bg-slate-900 text-white text-[10px] font-bold shadow-md">Phải thử ngay</span>
                        </button>
                        <button type="button" onclick="triggerCommentReaction(${id}, 'cry', event)" class="food-reaction-item select-none active:scale-90 cursor-pointer" data-reaction="cry" title="Khóc">
                            <span class="reaction-emoji-symbol">😢</span>
                            <span class="reaction-tooltip px-2 py-0.5 rounded-full bg-slate-900 text-white text-[10px] font-bold shadow-md">Khóc</span>
                        </button>
                        <button type="button" onclick="triggerCommentReaction(${id}, 'angry', event)" class="food-reaction-item select-none active:scale-90 cursor-pointer" data-reaction="angry" title="Phẫn nộ">
                            <span class="reaction-emoji-symbol">😡</span>
                            <span class="reaction-tooltip px-2 py-0.5 rounded-full bg-slate-900 text-white text-[10px] font-bold shadow-md">Phẫn nộ</span>
                        </button>
                    </div>
                `;

                if (isReply) {
                    // Hide reply form
                    const replyBox = document.getElementById(`reply-box-${parentId}`);
                    if (replyBox) replyBox.classList.add('hidden');

                    // Append reply
                    const repliesList = document.getElementById(`replies-list-${parentId}`);
                    const newReplyHtml = `
                        <div id="comment-block-${c.id}" class="reply-item p-3.5 rounded-xl bg-surface-container/30 border border-surface-dim/70 flex gap-3 items-start transition-all animate-in fade-in duration-300">
                            <img src="${c.user_avatar}" class="w-8 h-8 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs" alt="${c.user_name}">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1 flex-wrap">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-title-md text-[13px] font-bold text-on-background">${c.user_name}</span>
                                        ${authorBadge}
                                        ${adminBadge}
                                        <span class="text-[11px] text-on-surface-variant">• ${c.created_at_human}</span>
                                    </div>
                                </div>
                                <p class="text-on-surface font-body-sm whitespace-pre-line text-xs leading-relaxed">${c.content}</p>
                                <div class="flex items-center gap-3 mt-2 text-[11px] text-on-surface-variant font-medium">
                                    <div class="relative comment-reaction-container flex items-center gap-1" data-comment-id="${c.id}">
                                        ${renderReactionDockHtml(c.id)}
                                        <button type="button" 
                                                onclick="triggerCommentReaction(${c.id}, null, event)" 
                                                id="comment-react-btn-${c.id}" 
                                                class="comment-react-btn flex items-center gap-0.5 transition-all cursor-pointer font-medium text-slate-500 hover:text-primary"
                                                data-reacted="0"
                                                data-current=""
                                                title="Bấm để Thích, rê chuột để chọn cảm xúc">
                                            <span class="comment-reaction-icon text-[13px]">👍</span>
                                            <span class="comment-reaction-label text-[11px]">Thích</span>
                                        </button>
                                        <span id="comment-reaction-count-${c.id}" 
                                              onclick="openCommentReactionsModal(${c.id}, event)"
                                              class="comment-reaction-count-badge hidden items-center gap-0.5 px-1.5 py-0.2 rounded-full text-[10px] font-bold transition-all bg-slate-100 text-slate-700 hover:bg-amber-100/90 hover:text-amber-900 border border-slate-200/80 shadow-2xs cursor-pointer hover:scale-105 active:scale-95"
                                              title="Bấm để xem danh sách ai đã thả cảm xúc">
                                            <span class="comment-top-icon text-[10px] leading-none shrink-0">👍</span>
                                            <span class="comment-total-count text-[10px] font-bold tracking-tight">0</span>
                                        </span>
                                    </div>
                                    <button type="button" onclick="toggleReplyBox(${parentId}, '${c.user_name}')" class="flex items-center gap-0.5 text-slate-500 hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[14px]">reply</span>
                                        <span>Trả lời</span>
                                    </button>
                                    <button type="button" onclick="openDeleteModal(${c.id})" class="flex items-center gap-0.5 text-slate-400 hover:text-rose-600 transition-colors ml-auto">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        <span>Xóa</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    if (repliesList) {
                        repliesList.insertAdjacentHTML('beforeend', newReplyHtml);
                    }
                } else {
                    // Prepend root comment
                    const newCommentHtml = `
                        <div id="comment-block-${c.id}" class="comment-item p-4 rounded-2xl bg-white hover:bg-surface-container/20 transition-all border border-surface-dim/60 shadow-2xs animate-in fade-in duration-300">
                            <div class="flex gap-3.5 items-start">
                                <img src="${c.user_avatar}" class="w-10 h-10 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs" alt="${c.user_name}">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1 flex-wrap">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-title-md text-[14px] font-bold text-on-background">${c.user_name}</span>
                                            ${authorBadge}
                                            ${adminBadge}
                                            <span class="text-xs text-on-surface-variant">• ${c.created_at_human}</span>
                                        </div>
                                    </div>
                                    <p class="text-on-surface font-body-sm whitespace-pre-line text-sm leading-relaxed">${c.content}</p>
                                    <div class="flex items-center gap-4 mt-2.5 pt-2 text-xs text-on-surface-variant font-medium">
                                        <div class="relative comment-reaction-container flex items-center gap-1.5" data-comment-id="${c.id}">
                                            ${renderReactionDockHtml(c.id)}
                                            <button type="button" 
                                                    onclick="triggerCommentReaction(${c.id}, null, event)" 
                                                    id="comment-react-btn-${c.id}" 
                                                    class="comment-react-btn flex items-center gap-1 transition-all cursor-pointer font-medium text-slate-500 hover:text-primary"
                                                    data-reacted="0"
                                                    data-current=""
                                                    title="Bấm để Thích, rê chuột để chọn cảm xúc">
                                                <span class="comment-reaction-icon text-[14px]">👍</span>
                                                <span class="comment-reaction-label text-xs">Thích</span>
                                            </button>
                                            <span id="comment-reaction-count-${c.id}" 
                                                  onclick="openCommentReactionsModal(${c.id}, event)"
                                                  class="comment-reaction-count-badge hidden items-center gap-1 px-1.5 py-0.5 rounded-full text-xs font-bold transition-all bg-slate-100 text-slate-700 hover:bg-amber-100/90 hover:text-amber-900 border border-slate-200/80 shadow-2xs cursor-pointer hover:scale-105 active:scale-95"
                                                  title="Bấm để xem danh sách ai đã thả cảm xúc">
                                                <span class="comment-top-icon text-[11px] leading-none shrink-0">👍</span>
                                                <span class="comment-total-count text-[11px] font-bold tracking-tight">0</span>
                                            </span>
                                        </div>
                                        <button type="button" onclick="toggleReplyBox(${c.id}, '${c.user_name}')" class="flex items-center gap-1 text-slate-500 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-[16px]">reply</span>
                                            <span>Trả lời</span>
                                        </button>
                                        <button type="button" onclick="openDeleteModal(${c.id})" class="flex items-center gap-1 text-slate-400 hover:text-rose-600 transition-colors ml-auto">
                                            <span class="material-symbols-outlined text-[15px]">delete</span>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Inline Reply Box -->
                            <div id="reply-box-${c.id}" class="hidden mt-3 pl-10 pr-2 pt-2 border-t border-surface-dim">
                                <form id="reply-form-${c.id}" action="{{ route('blog.comment.store', $blog->slug) }}" method="POST" onsubmit="handleCommentSubmit(event, 'reply-form-${c.id}', ${c.id})" class="flex gap-3 items-start">
                                    <input type="hidden" name="parent_id" value="${c.id}">
                                    <img src="${c.user_avatar}" class="w-8 h-8 rounded-full flex-shrink-0 object-cover border border-gray-100 shadow-2xs">
                                    <div class="flex-1 space-y-2">
                                        <textarea id="reply-input-${c.id}" name="content" rows="2" required placeholder="Trả lời @${c.user_name}..." oninput="updateCharCount(this, 'char-count-${c.id}')" class="w-full rounded-xl border border-surface-dim bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-primary resize-none text-xs p-3 outline-none transition-all"></textarea>
                                        <div class="flex items-center justify-between flex-wrap gap-2 text-xs">
                                            <span id="char-count-${c.id}" class="text-[11px] text-slate-400">0/1000 ký tự</span>
                                            <div class="flex items-center gap-2">
                                                <button type="button" onclick="toggleReplyBox(${c.id})" class="px-3 py-1.5 rounded-full text-slate-500 hover:bg-slate-100 font-medium transition-colors cursor-pointer text-xs">
                                                    Hủy
                                                </button>
                                                <button type="submit" id="reply-btn-${c.id}" class="px-4 py-1.5 bg-primary hover:bg-primary/90 text-white rounded-full font-label-sm transition-all shadow-xs flex items-center gap-1 cursor-pointer text-xs">
                                                    <span class="material-symbols-outlined text-sm">send</span>
                                                    <span id="reply-btn-text-${c.id}">Gửi phản hồi</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div id="replies-list-${c.id}" class="space-y-3 mt-3 pl-8 sm:pl-10"></div>
                        </div>
                    `;
                    listContainer.insertAdjacentHTML('afterbegin', newCommentHtml);
                }

                if (counterBadge && body.comment_count !== undefined) {
                    counterBadge.textContent = body.comment_count;
                }
                const headerBadge = document.getElementById('blog-header-comment-count');
                if (headerBadge && body.comment_count !== undefined) {
                    headerBadge.textContent = body.comment_count;
                }
            } else {
                showAlert('moderated', 'Từ không hợp lệ', body.message || 'Từ không hợp lệ.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.textContent = originalText;
            btn.classList.remove('opacity-75');
            showAlert('error', 'Lỗi gửi bình luận', 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.');
        });
    }

    // Ctrl + Enter shortcut to quickly submit comment
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            const activeEl = document.activeElement;
            if (activeEl && activeEl.tagName === 'TEXTAREA') {
                const form = activeEl.closest('form');
                if (form) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }
        }
    });

    // ─── AI Summarizer Logic ───────────────────────────────────────────────
    let currentSummaryData = null;
    let isSpeaking = false;

    function toggleAiSummary(forceClose = false) {
        const card = document.getElementById('ai-summary-content');
        const btnText = document.getElementById('ai-summary-btn-text');
        const loading = document.getElementById('ai-summary-loading');

        if (forceClose || !card.classList.contains('hidden')) {
            card.classList.add('hidden');
            btnText.textContent = 'Tóm tắt bằng AI';
            if (window.speechSynthesis) window.speechSynthesis.cancel();
            return;
        }

        if (currentSummaryData) {
            card.classList.remove('hidden');
            btnText.textContent = 'Thu gọn tóm tắt';
            return;
        }

        // Fetch summary via AJAX
        loading.classList.remove('hidden');
        btnText.textContent = 'Đang tóm tắt...';

        fetch("{{ route('blog.ai.summarize', $blog->slug) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            loading.classList.add('hidden');
            if (data.success) {
                currentSummaryData = data;
                renderAiSummary(data);
                card.classList.remove('hidden');
                btnText.textContent = 'Thu gọn tóm tắt';
            } else {
                alert('Không thể tạo tóm tắt lúc này. Vui lòng thử lại sau.');
                btnText.textContent = 'Tóm tắt bằng AI';
            }
        })
        .catch(err => {
            loading.classList.add('hidden');
            btnText.textContent = 'Tóm tắt bằng AI';
            console.error('Error fetching AI summary:', err);
        });
    }

    function renderAiSummary(data) {
        document.getElementById('ai-summary-overview').textContent = data.overview || '';
        document.getElementById('ai-summary-address').textContent = data.address || 'Đang cập nhật địa chỉ';
        document.getElementById('ai-summary-price').textContent = data.price_range || 'Mức giá hợp lý';

        // Must try dishes
        const mustTryContainer = document.getElementById('ai-summary-musttry');
        mustTryContainer.innerHTML = '';
        (data.must_try || []).forEach(dish => {
            const span = document.createElement('span');
            span.className = 'px-2 py-0.5 bg-amber-50 text-amber-900 border border-amber-200 text-[11px] font-bold rounded-md';
            span.textContent = dish;
            mustTryContainer.appendChild(span);
        });

        // Pros & Cons
        const prosConsList = document.getElementById('ai-summary-proscons');
        prosConsList.innerHTML = '';
        (data.pros || []).forEach(pro => {
            const li = document.createElement('li');
            li.className = 'flex items-start gap-1 text-emerald-700 font-medium';
            li.innerHTML = `<span class="material-symbols-outlined text-[14px] text-emerald-600 shrink-0 mt-0.5">check_circle</span> <span>${pro}</span>`;
            prosConsList.appendChild(li);
        });
        (data.cons || []).forEach(con => {
            const li = document.createElement('li');
            li.className = 'flex items-start gap-1 text-slate-500 font-medium';
            li.innerHTML = `<span class="material-symbols-outlined text-[14px] text-amber-500 shrink-0 mt-0.5">info</span> <span>${con}</span>`;
            prosConsList.appendChild(li);
        });

        document.getElementById('ai-summary-saved-time').textContent = data.reading_time_saved || 'Tiết kiệm 95% thời gian đọc (nắm trọn trong 5 giây)';
    }

    function copyAiSummary() {
        if (!currentSummaryData) return;
        const text = `✨ TÓM TẮT BÀI VIẾT BẰNG AI: ${currentSummaryData.title}\n\n` +
                     `📍 Địa chỉ: ${currentSummaryData.address}\n` +
                     `🍲 Món nên thử: ${(currentSummaryData.must_try || []).join(', ')}\n` +
                     `💵 Mức giá: ${currentSummaryData.price_range}\n` +
                     `📝 Tóm lược: ${currentSummaryData.overview}`;
        navigator.clipboard.writeText(text).then(() => {
            showAlert('success', 'Đã sao chép', 'Đã sao chép tóm tắt bài viết vào bộ nhớ tạm!');
        });
    }

    function speakAiSummary() {
        if (!('speechSynthesis' in window) || !currentSummaryData) return;
        if (isSpeaking) {
            window.speechSynthesis.cancel();
            isSpeaking = false;
            document.getElementById('ai-tts-icon').textContent = 'volume_up';
            document.getElementById('ai-tts-text').textContent = 'Nghe đọc';
            return;
        }

        const textToRead = `${currentSummaryData.title}. Tóm tắt nhanh: ${currentSummaryData.overview}. Địa chỉ: ${currentSummaryData.address}. Món nên thử: ${(currentSummaryData.must_try || []).join(', ')}. Mức giá: ${currentSummaryData.price_range}.`;
        const utterance = new SpeechSynthesisUtterance(textToRead);
        utterance.lang = 'vi-VN';
        utterance.rate = 1.0;
        utterance.onstart = () => {
            isSpeaking = true;
            document.getElementById('ai-tts-icon').textContent = 'stop_circle';
            document.getElementById('ai-tts-text').textContent = 'Dừng đọc';
        };
        utterance.onend = utterance.onerror = () => {
            isSpeaking = false;
            document.getElementById('ai-tts-icon').textContent = 'volume_up';
            document.getElementById('ai-tts-text').textContent = 'Nghe đọc';
        };
        window.speechSynthesis.speak(utterance);
    }

    // Newsletter Ajax Submit
    function handleNewsletterSubmit(event, form) {
        event.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const btnText = btn ? btn.querySelector('.btn-text') : null;
        const originalText = btnText ? btnText.textContent : 'Đăng ký ngay';
        const feedback = form.querySelector('.newsletter-feedback');
        const emailInput = form.querySelector('input[name="email"]');
        const email = emailInput ? emailInput.value.trim() : '';

        if (!email) {
            if (feedback) {
                feedback.className = 'newsletter-feedback block text-xs font-bold p-2.5 rounded-xl bg-rose-100 text-rose-800 border border-rose-300 shadow-2xs mt-2';
                feedback.innerHTML = '<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">error</span>Vui lòng nhập email của bạn.</span>';
            }
            return;
        }

        if (btn) {
            btn.disabled = true;
            if (btnText) btnText.textContent = 'Đang xử lý...';
        }

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
            }
            return data;
        })
        .then(data => {
            if (feedback) {
                if (data.already_subscribed) {
                    feedback.className = 'newsletter-feedback block text-xs font-bold p-3 rounded-xl bg-amber-50 text-amber-900 border border-amber-300 shadow-2xs mt-2 text-left';
                    feedback.innerHTML = `<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-amber-600">info</span><span>${data.message}</span></span>`;
                } else {
                    feedback.className = 'newsletter-feedback block text-xs font-bold p-3 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-300 shadow-2xs mt-2 text-left';
                    feedback.innerHTML = `<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-emerald-600">check_circle</span><span>${data.message}</span></span>`;
                    @guest
                    if (emailInput) emailInput.value = '';
                    @endguest
                }
            }
        })
        .catch(err => {
            if (feedback) {
                feedback.className = 'newsletter-feedback block text-xs font-bold p-3 rounded-xl bg-rose-50 text-rose-800 border border-rose-300 shadow-2xs mt-2 text-left';
                feedback.innerHTML = `<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-rose-600">error</span><span>${err.message}</span></span>`;
            }
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                if (btnText) btnText.textContent = originalText;
            }
        });
    }

    // Tự động đồng bộ lượt xem sang trang Blog Index và Author Profile mà không cần tải lại trang
    (function() {
        const blogViewPayload = {
            slug: @json($blog->slug),
            blogId: @json($blog->id),
            viewCount: {{ (int) ($blog->view_count ?? 0) }},
            formatted: @json(number_format($blog->view_count ?? 0)),
            authorId: @json($blog->user_id ?? null),
            timestamp: Date.now()
        };

        try {
            localStorage.setItem('qm_latest_blog_view', JSON.stringify(blogViewPayload));
        } catch(e) {}

        try {
            if ('BroadcastChannel' in window) {
                const bc = new BroadcastChannel('qm_blog_views_sync');
                bc.postMessage(blogViewPayload);
                setTimeout(() => bc.close(), 1000);
            }
        } catch(e) {}
    })();

    /* ══════════════════════════════════════════════════════════════════════════
       THẢ CẢM XÚC ẨM THỰC (FOOD REACTIONS)
       Hỗ trợ 6 loại: ❤️ Yêu thích, 🤤 Thèm quá, 🔥 Phải thử ngay, 👍 Hữu ích, 😢 Khóc, 😡 Phẫn nộ
       Quy tắc:
       - Rê chuột (hover) 280ms ngắn: Hiện menu cảm xúc lơ lửng với animation
       - Nhấp chuột (click) 1 lần: Mặc định là Thích (👍 Hữu ích) hoặc gỡ cảm xúc nếu đã thích
       ══════════════════════════════════════════════════════════════════════════ */
    (function() {
        const REACTION_TYPES = @json(\App\Models\BlogReaction::TYPES);
        const BLOG_SLUG = @json($blog->slug);
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        let currentReaction = @json($myReactionKey);
        let hoverTimers = new Map();
        let closeTimers = new Map();

        // 1. Âm thanh phản hồi pop vui tai nhẹ nhàng, đa âm sắc chuẩn từng cảm xúc (Web Audio API)
        function playReactionPopSound(reactionKey = 'like') {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const now = ctx.currentTime;
                
                if (reactionKey === 'love') {
                    // Âm hưởng đàn hạc tình yêu ngọt ngào: F5 (698Hz) + C6 (1046Hz)
                    [698.46, 1046.5].forEach((freq, idx) => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, now + idx * 0.05);
                        gain.gain.setValueAtTime(0.12, now + idx * 0.05);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.05 + 0.28);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(now + idx * 0.05);
                        osc.stop(now + idx * 0.05 + 0.28);
                    });
                } else if (reactionKey === 'drool' || reactionKey === 'delicious') {
                    // Âm bong bóng giọt nước vui nhộn: trượt tần số 480Hz -> 960Hz
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(480, now);
                    osc.frequency.exponentialRampToValueAtTime(960, now + 0.09);
                    gain.gain.setValueAtTime(0.18, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.14);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.14);
                } else if (reactionKey === 'fire') {
                    // Âm lửa bốc sôi động ba nốt thăng hoa: 523Hz -> 784Hz -> 1046Hz
                    [523.25, 783.99, 1046.50].forEach((freq, idx) => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(freq, now + idx * 0.04);
                        gain.gain.setValueAtTime(0.1, now + idx * 0.04);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.04 + 0.22);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(now + idx * 0.04);
                        osc.stop(now + idx * 0.04 + 0.22);
                    });
                } else if (reactionKey === 'angry') {
                    // Âm bass trầm đục dứt khoát 160Hz -> 65Hz
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(160, now);
                    osc.frequency.exponentialRampToValueAtTime(65, now + 0.12);
                    gain.gain.setValueAtTime(0.2, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.16);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.16);
                } else if (reactionKey === 'cry') {
                    // Âm giọt lệ trầm lắng 560Hz -> 320Hz
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(560, now);
                    osc.frequency.exponentialRampToValueAtTime(320, now + 0.18);
                    gain.gain.setValueAtTime(0.12, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.2);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.2);
                } else {
                    // Mặc định: Nốt đôi tươi tắn 587Hz -> 880Hz
                    [587.33, 880].forEach((freq, idx) => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, now + idx * 0.05);
                        gain.gain.setValueAtTime(0.16, now + idx * 0.05);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.05 + 0.14);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(now + idx * 0.05);
                        osc.stop(now + idx * 0.05 + 0.14);
                    });
                }
            } catch(e) {}
        }

        // 2. Chùm hiệu ứng pháo hoa cảm xúc (Shockwave + Sparkles + Grand Floating Emoji + Sparks)
        function spawnFloatingEmoji(emoji, x, y, accentColor = '#f59e0b', reactionKey = 'like') {
            // A. Vòng sóng lan tỏa (Shockwave Ring)
            const ring = document.createElement('div');
            ring.className = 'reaction-shockwave-ring';
            ring.style.left = `${x}px`;
            ring.style.top = `${y}px`;
            ring.style.setProperty('--ring-color', accentColor);
            document.body.appendChild(ring);
            setTimeout(() => { if (ring.parentNode) ring.remove(); }, 680);

            // B. 8 hạt pháo lấp lánh nổ bung nhẹ nhàng nhỏ xinh (Compact Confetti Sparkle Burst)
            const palette = [accentColor, '#f59e0b', '#e11d48', '#ea580c', '#3b82f6', '#10b981', '#ec4899', '#8b5cf6'];
            for (let i = 0; i < 8; i++) {
                const p = document.createElement('div');
                p.className = 'reaction-burst-particle';
                const angle = (i / 8) * 2 * Math.PI + (Math.random() * 0.3 - 0.15);
                const distance = 24 + Math.random() * 26;
                const tx = Math.cos(angle) * distance;
                const ty = Math.sin(angle) * distance;
                const size = 3.5 + Math.random() * 2.5;
                const rot = (Math.random() * 360 - 180) + 'deg';
                p.style.width = `${size}px`;
                p.style.height = `${size}px`;
                p.style.setProperty('--tx', `${tx}px`);
                p.style.setProperty('--ty', `${ty}px`);
                p.style.setProperty('--rot', rot);
                p.style.backgroundColor = palette[i % palette.length];
                p.style.color = palette[i % palette.length];
                p.style.left = `${x}px`;
                p.style.top = `${y}px`;
                document.body.appendChild(p);
                setTimeout(() => { if (p.parentNode) p.remove(); }, 650);
            }

            // C. 2 hạt vệ tinh mini lượn sóng cùng (Companion Micro Sparks)
            [-16, 16].forEach(offset => {
                const spark = document.createElement('div');
                spark.className = 'mini-floating-spark';
                spark.textContent = (reactionKey === 'love' || reactionKey === 'fire') ? '✨' : emoji;
                spark.style.setProperty('--ox', `${offset}px`);
                spark.style.left = `${x}px`;
                spark.style.top = `${y}px`;
                document.body.appendChild(spark);
                setTimeout(() => { if (spark.parentNode) spark.remove(); }, 880);
            });

            // D. Emoji khổng lồ bay lên lượn sóng
            const el = document.createElement('div');
            el.className = 'floating-food-reaction';
            el.textContent = emoji;
            el.style.left = `${x}px`;
            el.style.top = `${y}px`;
            document.body.appendChild(el);
            setTimeout(() => { if (el.parentNode) el.remove(); }, 980);

            // E. Hiệu ứng nhún đàn hồi trên nút chính và pill đang chọn (Punch animation)
            document.querySelectorAll('.food-reaction-main-btn').forEach(btn => {
                btn.classList.remove('reaction-punch');
                void btn.offsetWidth; // Force CSS reflow
                btn.classList.add('reaction-punch');
            });
            const pillTarget = document.getElementById(`reaction-pill-${reactionKey}`);
            if (pillTarget) {
                pillTarget.classList.remove('reaction-punch');
                void pillTarget.offsetWidth;
                pillTarget.classList.add('reaction-punch');
            }
        }
        window.playReactionPopSound = playReactionPopSound;
        window.spawnFloatingEmoji = spawnFloatingEmoji;

        // 3. Quản lý mở / đóng Reaction Dock
        function openDock(container) {
            const dock = container.querySelector('.food-reaction-dock');
            if (dock) {
                dock.classList.add('dock-open');
            }
        }

        function closeDock(container) {
            const dock = container.querySelector('.food-reaction-dock');
            if (dock) {
                dock.classList.remove('dock-open');
            }
        }

        function closeAllDocks() {
            document.querySelectorAll('.food-reaction-dock').forEach(d => d.classList.remove('dock-open'));
        }

        // 4. Gắn sự kiện hover và click cho các container cảm xúc
        document.querySelectorAll('.food-reaction-container').forEach(container => {
            const btn = container.querySelector('.food-reaction-main-btn');
            const dock = container.querySelector('.food-reaction-dock');

            // Hover vào container: Sau 280ms mới mở menu cảm xúc (tránh vô tình lướt qua)
            container.addEventListener('mouseenter', () => {
                if (closeTimers.has(container)) {
                    clearTimeout(closeTimers.get(container));
                    closeTimers.delete(container);
                }
                const timer = setTimeout(() => {
                    openDock(container);
                }, 280);
                hoverTimers.set(container, timer);
            });

            // Nếu rê chuột vào badge đếm số lượng người thả cảm xúc bài viết -> không mở dock và đóng dock ngay
            const countBadge = container.querySelector('.food-reaction-count');
            if (countBadge) {
                countBadge.addEventListener('mouseenter', () => {
                    if (hoverTimers.has(container)) {
                        clearTimeout(hoverTimers.get(container));
                        hoverTimers.delete(container);
                    }
                    closeDock(container);
                });
            }

            // Rời chuột khỏi container: Hủy hẹn giờ mở và đóng êm sau 350ms
            container.addEventListener('mouseleave', () => {
                if (hoverTimers.has(container)) {
                    clearTimeout(hoverTimers.get(container));
                    hoverTimers.delete(container);
                }
                const timer = setTimeout(() => {
                    closeDock(container);
                }, 350);
                closeTimers.set(container, timer);
            });

            // Nếu chuột đi vào dock: giữ dock luôn mở và tạo hiệu ứng phóng to Mac-dock mượt mà theo khoảng cách chuột
            if (dock) {
                dock.addEventListener('mouseenter', () => {
                    if (closeTimers.has(container)) {
                        clearTimeout(closeTimers.get(container));
                        closeTimers.delete(container);
                    }
                });

                dock.addEventListener('mousemove', (e) => {
                    const items = dock.querySelectorAll('.food-reaction-item');
                    const mouseX = e.clientX;
                    items.forEach(item => {
                        const rect = item.getBoundingClientRect();
                        const itemCenterX = rect.left + rect.width / 2;
                        const dist = Math.abs(mouseX - itemCenterX);
                        if (dist < 64) {
                            const scale = 1 + (0.38 * (1 - dist / 64));
                            item.style.setProperty('--dock-scale', scale.toFixed(2));
                        } else {
                            item.style.removeProperty('--dock-scale');
                        }
                    });
                });

                dock.addEventListener('mouseleave', () => {
                    dock.querySelectorAll('.food-reaction-item').forEach(item => {
                        item.style.removeProperty('--dock-scale');
                    });
                });
            }

            // Hỗ trợ cảm ứng trên điện thoại (nhấn giữ 300ms mở menu cảm xúc)
            let touchTimer = null;
            if (btn) {
                btn.addEventListener('touchstart', (e) => {
                    touchTimer = setTimeout(() => {
                        openDock(container);
                    }, 300);
                }, { passive: true });

                btn.addEventListener('touchend', () => {
                    if (touchTimer) {
                        clearTimeout(touchTimer);
                        touchTimer = null;
                    }
                }, { passive: true });

                // Nhấp chuột 1 cái vào nút chính:
                // Nếu chưa có cảm xúc -> Mặc định là 'like' (👍 Hữu ích)
                // Nếu đã có cảm xúc hiện tại -> Nhấp tiếp để gỡ (toggle off)
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    closeAllDocks();

                    const defaultReaction = currentReaction || 'like';
                    window.triggerFoodReaction(defaultReaction, e);
                });
            }
        });

        // Đóng dock khi nhấn ra ngoài trang
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.food-reaction-container')) {
                closeAllDocks();
            }
        });

        // 5. Cập nhật DOM giao diện đồng bộ toàn trang
        function updateReactionsUI(data) {
            currentReaction = data.current_reaction;
            const rInfo = data.reaction_info;
            const isReacted = !!currentReaction;

            // 5.1 Cập nhật các nút chính (Header & Footer)
            document.querySelectorAll('.food-reaction-main-btn').forEach(btn => {
                btn.dataset.reacted = isReacted ? '1' : '0';
                btn.dataset.current = currentReaction || '';

                const icon = btn.querySelector('.food-reaction-icon');
                const label = btn.querySelector('.food-reaction-label');
                const countBadge = btn.querySelector('.food-reaction-count');

                // Reset class màu sắc chuẩn thiết kế
                btn.className = 'food-reaction-main-btn flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border transition-all cursor-pointer active:scale-95 whitespace-nowrap';

                if (isReacted && rInfo) {
                    btn.classList.add(
                        ...rInfo.bg_class.split(' '),
                        ...rInfo.border_class.split(' '),
                        ...rInfo.text_class.split(' '),
                        'font-bold'
                    );
                    if (icon) icon.textContent = rInfo.emoji;
                    if (label) label.textContent = rInfo.name;
                } else {
                    btn.classList.add('bg-slate-50', 'hover:bg-slate-100', 'text-slate-700', 'border-slate-200', 'hover:border-amber-400', 'hover:ring-2', 'hover:ring-amber-400/30');
                    if (icon) icon.textContent = '👍';
                    if (label) label.textContent = 'Thích';
                }

                if (countBadge) {
                    if (data.total_count > 0) {
                        countBadge.classList.remove('hidden');
                        countBadge.classList.add('inline-flex');

                        // Cập nhật số lượng tương tác
                        const numEl = countBadge.querySelector('.food-reaction-number');
                        if (numEl) {
                            numEl.textContent = data.formatted_count;
                        }

                        // Cập nhật danh sách icon cảm xúc nhiều nhất (tối đa 3 icon xếp theo số lượng giảm dần)
                        const topIconsContainer = countBadge.querySelector('.food-reaction-top-icons');
                        if (topIconsContainer) {
                            topIconsContainer.innerHTML = '';
                            const topKeys = (data.top_reactions && data.top_reactions.length > 0) 
                                ? data.top_reactions.slice(0, 3) 
                                : [currentReaction || 'like'];

                            topKeys.forEach(topKey => {
                                const r = REACTION_TYPES[topKey];
                                if (r) {
                                    const span = document.createElement('span');
                                    span.className = 'w-4 h-4 rounded-full bg-white shadow-2xs border border-white flex items-center justify-center text-[10px] leading-none shrink-0 transition-transform hover:scale-125';
                                    span.textContent = r.emoji;
                                    const count = data.breakdown?.[topKey] || 0;
                                    span.title = `${r.name}: ${count}`;
                                    topIconsContainer.appendChild(span);
                                }
                            });
                        }

                        countBadge.classList.remove('count-pop-anim');
                        void countBadge.offsetWidth;
                        countBadge.classList.add('count-pop-anim');

                        if (isReacted && rInfo) {
                            countBadge.className = 'food-reaction-count inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-bold transition-all bg-white/90 text-slate-800 shadow-2xs border border-black/5 count-pop-anim';
                        } else {
                            countBadge.className = 'food-reaction-count inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-bold transition-all bg-slate-200/80 text-slate-700 count-pop-anim';
                        }
                    } else {
                        countBadge.classList.add('hidden');
                        countBadge.classList.remove('inline-flex');
                    }
                }
            });

            // 5.2 Cập nhật thanh tổng hợp cảm xúc (#food-reactions-summary-pill)
            const totalEl = document.getElementById('food-reaction-total-count');
            if (totalEl) {
                totalEl.textContent = data.formatted_count;
                totalEl.classList.remove('count-pop-anim');
                void totalEl.offsetWidth;
                totalEl.classList.add('count-pop-anim');
            }

            const topContainer = document.querySelector('.food-reactions-top-emojis');
            if (topContainer) {
                topContainer.innerHTML = '';
                const tops = (data.top_reactions && data.top_reactions.length > 0) ? data.top_reactions : ['like'];
                tops.forEach(key => {
                    const item = REACTION_TYPES[key];
                    if (item) {
                        const span = document.createElement('span');
                        span.className = 'w-6 h-6 rounded-full bg-slate-50 border border-white flex items-center justify-center text-xs shadow-2xs transition-transform hover:scale-125';
                        span.textContent = item.emoji;
                        topContainer.appendChild(span);
                    }
                });
            }

            // 5.3 Cập nhật 6 thẻ pill nhanh (#reaction-pill-{key})
            Object.keys(REACTION_TYPES).forEach(key => {
                const pill = document.getElementById(`reaction-pill-${key}`);
                if (!pill) return;

                const countEl = pill.querySelector('.reaction-pill-count');
                const count = (data.breakdown && data.breakdown[key]) ? data.breakdown[key] : 0;
                if (countEl) {
                    countEl.textContent = count;
                    countEl.classList.remove('count-pop-anim');
                    void countEl.offsetWidth;
                    countEl.classList.add('count-pop-anim');
                    countEl.className = `reaction-pill-count font-bold text-[11px] count-pop-anim ${count > 0 ? 'text-slate-800' : 'text-slate-400'}`;
                }

                const r = REACTION_TYPES[key];
                const isSelected = (currentReaction === key);
                pill.className = `reaction-quick-pill px-2.5 py-1.5 rounded-full text-xs font-semibold border transition-all flex items-center gap-1.5 cursor-pointer active:scale-95 ${
                    isSelected 
                        ? `${r.bg_class} ${r.border_class} ${r.text_class} ring-2 ring-primary/30 font-bold scale-105`
                        : 'bg-white/90 hover:bg-slate-50 text-slate-600 border-slate-200/80 hover:border-amber-300'
                }`;
            });
        }

        // 6. Gửi request thả cảm xúc qua AJAX
        window.triggerFoodReaction = async function(reactionKey, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            closeAllDocks();

            @guest
                if (window.showToast) {
                    window.showToast({
                        eyebrow: 'Cảm xúc ẩm thực',
                        title: 'Đăng nhập để thả cảm xúc',
                        message: 'Vui lòng đăng nhập tài khoản để cùng cộng đồng thả cảm xúc cho bài viết này!',
                        type: 'warning',
                        actionText: 'Đăng nhập ngay',
                        actionUrl: "{{ route('login') }}"
                    });
                } else {
                    window.location.href = "{{ route('login') }}";
                }
                return;
            @endguest

            const reactionItem = REACTION_TYPES[reactionKey] || REACTION_TYPES['like'];
            const clickX = event?.clientX || (event?.touches && event.touches[0] ? event.touches[0].clientX : window.innerWidth / 2);
            const clickY = event?.clientY || (event?.touches && event.touches[0] ? event.touches[0].clientY : window.innerHeight / 2);

            // Hiệu ứng pháo hoa hạt sparkles 360 độ + shockwave ring + âm thanh đa âm chuẩn từng cảm xúc
            spawnFloatingEmoji(reactionItem.emoji, clickX, clickY, reactionItem.color, reactionKey);
            playReactionPopSound(reactionKey);

            try {
                const response = await fetch(`/blog/${encodeURIComponent(BLOG_SLUG)}/cam-xuc`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ reaction: reactionKey })
                });

                if (response.status === 401) {
                    if (window.showToast) {
                        window.showToast({
                            eyebrow: 'Cảm xúc ẩm thực',
                            title: 'Phiên đăng nhập hết hạn',
                            message: 'Vui lòng đăng nhập lại để thực hiện thao tác này.',
                            type: 'warning',
                            actionText: 'Đăng nhập',
                            actionUrl: "{{ route('login') }}"
                        });
                    }
                    return;
                }

                const data = await response.json();
                if (data.success) {
                    updateReactionsUI(data);

                    if (window.showToast && data.action !== 'removed') {
                        window.showToast({
                            eyebrow: 'Thả cảm xúc thành công',
                            title: `${reactionItem.emoji} ${reactionItem.name}`,
                            message: `Bạn vừa thả cảm xúc cho bài viết "${BLOG_TITLE}".`,
                            type: 'success',
                            duration: 2500
                        });
                    }
                }
            } catch (err) {
                console.error('Lỗi khi thả cảm xúc:', err);
            }
        };
    })();
</script>
@endpush
