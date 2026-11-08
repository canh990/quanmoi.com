@extends('layouts.app')

@section('title', 'Video Review Thật - Khám Phá Ẩm Thực Qua Shorts | Quán Mới')

@push('styles')
<style>
    /* ==========================================================================
       VIDEO REVIEW SHORTS - ENTERPRISE SCOPED UI SYSTEM
       ========================================================================== */
    .vr-page {
        background: #f8fafc;
        min-height: calc(100vh - 72px);
        padding-bottom: 96px;
    }

    /* Hero Banner */
    .vr-hero {
        position: relative;
        background: linear-gradient(135deg, #1c1917 0%, #431407 52%, #7c2d12 100%);
        color: #ffffff;
        padding: 36px 0 64px;
        overflow: hidden;
    }
    .vr-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 82% 20%, rgba(251, 146, 60, 0.25) 0%, transparent 48%),
                    radial-gradient(circle at 18% 80%, rgba(225, 29, 72, 0.2) 0%, transparent 45%);
        pointer-events: none;
    }
    .vr-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.22);
        backdrop-filter: blur(8px);
        font-size: 12px;
        font-weight: 700;
        color: #fed7aa;
        margin-bottom: 12px;
    }
    .vr-hero-title {
        font-size: clamp(24px, 3.5vw, 36px);
        font-weight: 900;
        line-height: 1.2;
        letter-spacing: -0.02em;
        color: #ffffff;
        margin-bottom: 8px;
    }
    .vr-hero-sub {
        font-size: 14.5px;
        color: rgba(255, 255, 255, 0.82);
        max-width: 600px;
        line-height: 1.55;
    }
    .vr-hero-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 18px;
    }
    .vr-stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 14px;
        background: rgba(15, 23, 42, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(8px);
        font-size: 12.5px;
        color: #f8fafc;
    }

    /* Filter Card (Floating overlap) */
    .vr-filter-wrap {
        margin-top: -36px;
        position: relative;
        z-index: 20;
        margin-bottom: 28px;
    }
    .vr-filter-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid rgba(226, 232, 240, 0.95);
        box-shadow: 0 12px 32px -8px rgba(15, 23, 42, 0.08), 0 2px 6px rgba(15, 23, 42, 0.03);
        padding: 18px 20px;
    }
    .vr-filter-top {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }
    @media (min-width: 900px) {
        .vr-filter-top {
            grid-template-columns: minmax(260px, 1.5fr) auto auto;
            align-items: center;
        }
    }

    /* Search Input */
    .vr-search-box {
        position: relative;
        display: flex;
        align-items: center;
    }
    .vr-search-icon {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        pointer-events: none;
    }
    .vr-search-input {
        width: 100%;
        height: 44px;
        padding: 0 92px 0 42px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 13.5px;
        font-weight: 500;
        color: #0f172a;
        transition: all 0.2s ease;
    }
    .vr-search-input:focus {
        outline: none;
        background: #ffffff;
        border-color: #c2410c;
        box-shadow: 0 0 0 3px rgba(194, 65, 12, 0.12);
    }
    .vr-search-btn {
        position: absolute;
        right: 5px;
        height: 34px;
        padding: 0 14px;
        border-radius: 9px;
        background: linear-gradient(135deg, #c2410c 0%, #9a3412 100%);
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        border: none;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }
    .vr-search-btn:hover {
        opacity: 0.94;
        transform: translateY(-1px);
    }

    /* Selects & Sort Pills */
    .vr-select-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }
    .vr-select-wrap {
        position: relative;
        min-width: 170px;
        flex: 1 1 auto;
    }
    .vr-select {
        width: 100%;
        height: 44px;
        padding: 0 36px 0 14px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        appearance: none;
        cursor: pointer;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .vr-select:hover {
        border-color: #cbd5e1;
    }
    .vr-select:focus {
        outline: none;
        border-color: #c2410c;
        box-shadow: 0 0 0 3px rgba(194, 65, 12, 0.12);
    }
    .vr-select-arrow {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
    }

    /* Quick Sort Tabs */
    .vr-sort-tabs {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 12px;
        gap: 4px;
    }
    .vr-sort-tab {
        height: 36px;
        padding: 0 12px;
        border-radius: 9px;
        font-size: 12.5px;
        font-weight: 700;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        border: none;
        background: transparent;
        transition: all 0.18s ease;
        white-space: nowrap;
    }
    .vr-sort-tab:hover {
        color: #0f172a;
    }
    .vr-sort-tab.is-active {
        background: #ffffff;
        color: #c2410c;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
    }

    /* Category Quick Chips */
    .vr-chips-row {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-top: 14px;
        margin-top: 14px;
        border-top: 1px solid #f1f5f9;
        scrollbar-width: none;
    }
    .vr-chips-row::-webkit-scrollbar { display: none; }
    .vr-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 32px;
        padding: 0 13px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        transition: all 0.18s ease;
        text-decoration: none;
    }
    .vr-chip:hover {
        border-color: #fdba74;
        background: #fff7ed;
        color: #c2410c;
    }
    .vr-chip.is-active {
        background: #c2410c;
        border-color: #c2410c;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(194, 65, 12, 0.22);
    }

    /* Video Shorts Grid (4 columns on desktop like TikTok/Reels) */
    .vr-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    @media (min-width: 640px) {
        .vr-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }
    }
    @media (min-width: 900px) {
        .vr-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }
    }
    @media (min-width: 1200px) {
        .vr-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
        }
    }

    /* Video Card */
    .vr-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                    box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                    border-color 0.22s ease;
    }
    .vr-card:hover {
        transform: translateY(-5px);
        border-color: rgba(194, 65, 12, 0.28);
        box-shadow: 0 20px 36px -12px rgba(15, 23, 42, 0.14);
    }

    /* Vertical 9:16 Poster Container */
    .vr-poster {
        position: relative;
        width: 100%;
        aspect-ratio: 9 / 14;
        background: #0f172a;
        overflow: hidden;
    }
    .vr-poster img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.65s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .vr-card:hover .vr-poster img {
        transform: scale(1.06);
    }
    .vr-poster-shade {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg,
            rgba(15, 23, 42, 0.45) 0%,
            rgba(15, 23, 42, 0) 28%,
            rgba(15, 23, 42, 0.08) 55%,
            rgba(15, 23, 42, 0.88) 100%);
        pointer-events: none;
        z-index: 1;
    }

    /* Top Badges on Poster */
    .vr-poster-top {
        position: absolute;
        top: 10px;
        left: 10px;
        right: 10px;
        z-index: 5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }
    .vr-venue-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        max-width: calc(100% - 40px);
        padding: 5px 10px;
        border-radius: 9999px;
        background: rgba(15, 23, 42, 0.72);
        border: 1px solid rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .vr-venue-pill:hover {
        background: #c2410c;
        transform: scale(1.02);
    }
    .vr-short-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 9999px;
        background: linear-gradient(135deg, #f43f5e 0%, #ea580c 100%);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(244, 63, 94, 0.4);
        flex-shrink: 0;
    }

    /* Play Button Center */
    .vr-play-center {
        position: absolute;
        inset: 0;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    .vr-play-circle {
        width: 54px;
        height: 54px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.22);
        border: 1.5px solid rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(8px);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.28);
        transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .vr-card:hover .vr-play-circle {
        background: #ea580c;
        border-color: #fed7aa;
        transform: scale(1.12);
        box-shadow: 0 10px 28px rgba(234, 88, 12, 0.5);
    }

    /* Bottom Overlay inside Poster */
    .vr-poster-bottom {
        position: absolute;
        bottom: 10px;
        left: 10px;
        right: 10px;
        z-index: 4;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        pointer-events: none;
    }
    .vr-metric-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 8px;
        background: rgba(15, 23, 42, 0.68);
        backdrop-filter: blur(6px);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
    }
    .vr-cat-pill {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        border-radius: 8px;
        background: rgba(234, 88, 12, 0.9);
        color: #ffffff;
        font-size: 10.5px;
        font-weight: 700;
        max-width: 60%;
    }

    /* Card Footer Body */
    .vr-body {
        padding: 13px 14px 14px;
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        justify-content: space-between;
        gap: 10px;
    }
    .vr-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.42;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.18s ease;
    }
    .vr-card:hover .vr-title {
        color: #c2410c;
    }
    .vr-author-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding-top: 8px;
        border-top: 1px solid #f1f5f9;
        font-size: 11.5px;
        color: #64748b;
    }
    .vr-author-info {
        display: flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
    }
    .vr-author-avatar {
        width: 22px;
        height: 22px;
        border-radius: 9999px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }

    @media (max-width: 639px) {
        .vr-hero {
            padding: 24px 0 48px;
        }
        .vr-filter-card {
            padding: 14px;
            border-radius: 16px;
        }
        .vr-play-circle {
            width: 44px;
            height: 44px;
        }
        .vr-body {
            padding: 10px;
        }
        .vr-title {
            font-size: 12.5px;
        }
    }
</style>
@endpush

@section('content')
@php
    $currentSort = request('sort', 'moi_nhat');
    $currentQ = request('q', '');
    $currentQuanHuyen = request('quan_huyen', '');
    $currentLoaiHinh = request('loai_hinh', '');
    $hasActiveFilter = $currentQ !== '' || $currentQuanHuyen !== '' || $currentLoaiHinh !== '' || $currentSort !== 'moi_nhat';
@endphp

<div class="vr-page">
    {{-- ===== HERO SECTION ===== --}}
    <section class="vr-hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <div class="vr-hero-badge">
                        <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">play_circle</span>
                        <span>QUÁN MỚI REELS & SHORTS</span>
                    </div>
                    <h1 class="vr-hero-title">Video Review Ẩm Thực Thực Tế</h1>
                    <p class="vr-hero-sub">
                        Trải nghiệm không gian và món ngon qua góc nhìn chân thực nhất từ các Food Reviewer & thực khách địa phương.
                    </p>
                </div>

                <div class="vr-hero-stats">
                    <div class="vr-stat-pill">
                        <span class="material-symbols-outlined text-[18px] text-amber-400" style="font-variation-settings: 'FILL' 1;">smart_display</span>
                        <div>
                            <div class="text-[11px] text-slate-300 leading-none">Tổng video</div>
                            <div class="font-extrabold text-sm text-white mt-0.5">{{ number_format($videos->total()) }} Shorts</div>
                        </div>
                    </div>
                    <div class="vr-stat-pill">
                        <span class="material-symbols-outlined text-[18px] text-emerald-400" style="font-variation-settings: 'FILL' 1;">verified</span>
                        <div>
                            <div class="text-[11px] text-slate-300 leading-none">Độ tin cậy</div>
                            <div class="font-extrabold text-sm text-white mt-0.5">100% Quán Xác Thực</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- ===== KHU VỰC LỌC & TÌM KIẾM ===== --}}
        <div class="vr-filter-wrap">
            <form method="GET" action="{{ route('video-review.index') }}" id="filter-form" class="vr-filter-card">
                <input type="hidden" name="sort" id="input-sort" value="{{ $currentSort }}">

                <div class="vr-filter-top">
                    {{-- 1. Thanh tìm kiếm --}}
                    <div class="vr-search-box">
                        <span class="material-symbols-outlined vr-search-icon text-[20px]">search</span>
                        <input type="text"
                               name="q"
                               id="search-input"
                               value="{{ $currentQ }}"
                               placeholder="Tìm theo tên quán, món ăn, chủ đề review..."
                               class="vr-search-input">
                        <button type="submit" class="vr-search-btn">
                            <span>Tìm</span>
                        </button>
                    </div>

                    {{-- 2. Dropdown Quận/Huyện & Thể loại --}}
                    <div class="vr-select-group">
                        @if($danhSachQuanHuyen->isNotEmpty())
                        <div class="vr-select-wrap">
                            <select name="quan_huyen" id="select-quan-huyen"
                                    onchange="document.getElementById('filter-form').submit()"
                                    class="vr-select">
                                <option value="">📍 Tất cả khu vực</option>
                                @foreach($danhSachQuanHuyen as $qh)
                                    <option value="{{ $qh }}" {{ $currentQuanHuyen == $qh ? 'selected' : '' }}>
                                        {{ $qh }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined vr-select-arrow text-[18px]">expand_more</span>
                        </div>
                        @endif

                        @if($danhSachLoaiHinh->isNotEmpty())
                        <div class="vr-select-wrap">
                            <select name="loai_hinh" id="select-loai-hinh"
                                    onchange="document.getElementById('filter-form').submit()"
                                    class="vr-select">
                                <option value="">🍽️ Tất cả thể loại</option>
                                @foreach($danhSachLoaiHinh as $lh)
                                    <option value="{{ $lh }}" {{ $currentLoaiHinh == $lh ? 'selected' : '' }}>
                                        {{ $lh }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined vr-select-arrow text-[18px]">expand_more</span>
                        </div>
                        @endif
                    </div>

                    {{-- 3. Nút chuyển nhanh tiêu chí sắp xếp --}}
                    <div class="flex items-center justify-between sm:justify-end gap-2">
                        <div class="vr-sort-tabs">
                            <button type="button"
                                    onclick="setVideoSort('moi_nhat')"
                                    class="vr-sort-tab {{ $currentSort === 'moi_nhat' ? 'is-active' : '' }}">
                                <span class="material-symbols-outlined text-[15px]">schedule</span>
                                <span>Mới nhất</span>
                            </button>
                            <button type="button"
                                    onclick="setVideoSort('nhieu_view')"
                                    class="vr-sort-tab {{ $currentSort === 'nhieu_view' ? 'is-active' : '' }}">
                                <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                                <span>Đang hot</span>
                            </button>
                            <button type="button"
                                    onclick="setVideoSort('nhieu_like')"
                                    class="vr-sort-tab {{ $currentSort === 'nhieu_like' ? 'is-active' : '' }}">
                                <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                                <span>Yêu thích</span>
                            </button>
                        </div>

                        @if($hasActiveFilter)
                        <a href="{{ route('video-review.index') }}"
                           title="Xóa tất cả bộ lọc"
                           style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;"
                           class="inline-flex items-center gap-1 text-xs font-bold h-9 px-3 rounded-xl hover:opacity-90 transition shrink-0">
                            <span class="material-symbols-outlined text-[15px]">restart_alt</span>
                            <span class="hidden sm:inline">Đặt lại</span>
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Dải Chip lọc nhanh theo Thể loại --}}
                @if($danhSachLoaiHinh->isNotEmpty())
                <div class="vr-chips-row">
                    <span class="text-[11.5px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Chủ đề:</span>
                    <a href="{{ route('video-review.index', array_filter(['q' => $currentQ, 'quan_huyen' => $currentQuanHuyen, 'sort' => $currentSort !== 'moi_nhat' ? $currentSort : null])) }}"
                       class="vr-chip {{ $currentLoaiHinh === '' ? 'is-active' : '' }}">
                        <span>Tất cả</span>
                    </a>
                    @foreach($danhSachLoaiHinh as $lh)
                        <a href="{{ route('video-review.index', array_filter(['q' => $currentQ, 'quan_huyen' => $currentQuanHuyen, 'loai_hinh' => $lh, 'sort' => $currentSort !== 'moi_nhat' ? $currentSort : null])) }}"
                           class="vr-chip {{ $currentLoaiHinh === $lh ? 'is-active' : '' }}">
                            <span>{{ $lh }}</span>
                        </a>
                    @endforeach
                </div>
                @endif

                {{-- Thông tin kết quả đang lọc --}}
                @if($hasActiveFilter)
                <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                    <div>
                        Tìm thấy <strong class="text-slate-900 font-extrabold">{{ $videos->total() }}</strong> video phù hợp
                        @if($currentQ)
                            với từ khóa <span class="font-bold text-primary">"{{ $currentQ }}"</span>
                        @endif
                        @if($currentQuanHuyen)
                            tại <span class="font-bold text-primary">{{ $currentQuanHuyen }}</span>
                        @endif
                        @if($currentLoaiHinh)
                            thuộc danh mục <span class="font-bold text-primary">{{ $currentLoaiHinh }}</span>
                        @endif
                    </div>
                </div>
                @endif
            </form>
        </div>

        {{-- ===== LƯỚI VIDEO SHORTS ===== --}}
        @if($videos->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center max-w-lg mx-auto my-8 shadow-xs">
                <div style="background: #fff7ed; color: #ea580c;" class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center">
                    <span class="material-symbols-outlined text-4xl">videocam_off</span>
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">Chưa tìm thấy video phù hợp</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Thử thay đổi từ khóa tìm kiếm hoặc chọn khu vực, thể loại quán khác để khám phá thêm các video review hấp dẫn.
                </p>
                <a href="{{ route('video-review.index') }}"
                   style="background: #c2410c; color: #ffffff;"
                   class="mt-5 inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold shadow-sm hover:opacity-95 transition">
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                    <span>Xem tất cả video</span>
                </a>
            </div>
        @else
            <div class="vr-grid" id="video-container">
                @foreach($videos as $video)
                    @php
                        $thumbnail = $video->effective_thumbnail;
                    @endphp

                    <article class="vr-card group" onclick="openVideoModal({{ $loop->index }})">
                        {{-- Khung Poster Dọc 9:14 --}}
                        <div class="vr-poster">
                            <img src="{{ $thumbnail }}"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80';"
                                 alt="{{ $video->tieu_de ?: 'Video Review' }}"
                                 loading="lazy">

                            <div class="vr-poster-shade"></div>

                            {{-- Góc trên: Link tới quán & Icon Shorts --}}
                            <div class="vr-poster-top">
                                @if($video->quan)
                                    <a href="{{ route('quan.detail', $video->quan->slug) }}"
                                       onclick="event.stopPropagation()"
                                       class="vr-venue-pill"
                                       title="Xem chi tiết quán {{ $video->quan->ten_quan }}">
                                        <span class="material-symbols-outlined text-[14px] text-amber-400 shrink-0" style="font-variation-settings: 'FILL' 1;">location_on</span>
                                        <span class="truncate">{{ $video->quan->ten_quan }}</span>
                                    </a>
                                @else
                                    <span></span>
                                @endif

                                <span class="vr-short-badge" title="Video Short Review">
                                    <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">bolt</span>
                                </span>
                            </div>

                            {{-- Nút Play ở tâm --}}
                            <div class="vr-play-center">
                                <div class="vr-play-circle">
                                    <span class="material-symbols-outlined text-[28px] ml-0.5" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                                </div>
                            </div>

                            {{-- Góc dưới trên ảnh: Danh mục & Lượt xem --}}
                            <div class="vr-poster-bottom">
                                @if($video->quan?->loai_hinh_kinh_doanh)
                                    <span class="vr-cat-pill truncate">
                                        {{ $video->quan->loai_hinh_kinh_doanh }}
                                    </span>
                                @else
                                    <span></span>
                                @endif

                                <div class="flex items-center gap-1.5">
                                    @if($video->luot_thich > 0)
                                        <span class="vr-metric-pill">
                                            <span class="material-symbols-outlined text-[12px] text-rose-400" style="font-variation-settings: 'FILL' 1;">favorite</span>
                                            <span>{{ number_format($video->luot_thich) }}</span>
                                        </span>
                                    @endif
                                    <span class="vr-metric-pill">
                                        <span class="material-symbols-outlined text-[13px] text-amber-300">visibility</span>
                                        <span>{{ number_format($video->luot_xem ?? 0) }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Phần Thông Tin Dưới Card --}}
                        <div class="vr-body">
                            <h3 class="vr-title" title="{{ $video->tieu_de ?: 'Review ' . ($video->quan ? $video->quan->ten_quan : 'Địa điểm') }}">
                                {{ $video->tieu_de ?: 'Review ' . ($video->quan ? $video->quan->ten_quan : 'Địa điểm ẩm thực') }}
                            </h3>

                            <div class="vr-author-row">
                                <div class="vr-author-info">
                                    <img src="{{ $video->avatar_nguoi_dang ?: 'https://ui-avatars.com/api/?name='.urlencode($video->nguoi_dang ?: 'Reviewer').'&background=ffedd5&color=9a3412&bold=true' }}"
                                         onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=R&background=ffedd5&color=9a3412&bold=true';"
                                         alt="{{ $video->nguoi_dang ?: '@reviewer' }}"
                                         class="vr-author-avatar">
                                    <span class="font-bold text-slate-700 truncate">{{ $video->nguoi_dang ?: '@reviewer' }}</span>
                                </div>
                                <span class="text-[11px] text-slate-400 shrink-0">{{ $video->created_at ? $video->created_at->diffForHumans() : 'Mới đây' }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Phân trang --}}
            @if($videos->hasPages())
                <div class="mt-10">
                    {{ $videos->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

{{-- Modal Xem Video (Chỉ phát video sạch hoặc fallback Iframe) --}}
<div id="video-modal" class="fixed inset-0 hidden opacity-0 transition-opacity duration-300" style="display:none; z-index: 100000;">
    {{-- Backdrop --}}
    <div class="absolute inset-0" style="background: rgba(2, 6, 23, 0.88); backdrop-filter: blur(10px);" onclick="closeVideoModal()"></div>

    {{-- Nút Đóng ở góc trên phải màn hình --}}
    <button type="button" onclick="event.stopPropagation(); closeVideoModal()"
            class="fixed top-4 right-4 md:top-6 md:right-6 text-slate-900 bg-white hover:bg-slate-100 hover:scale-110 rounded-full w-11 h-11 flex items-center justify-center transition-transform shadow-2xl cursor-pointer"
            style="z-index: 100001;" title="Đóng (Esc)">
        <span class="material-symbols-outlined text-2xl font-black">close</span>
    </button>

    {{-- Nút Chuyển Trái / Phải --}}
    <button type="button" onclick="event.stopPropagation(); changeVideo(-1)"
            class="fixed left-2 md:left-8 top-1/2 -translate-y-1/2 text-slate-900 bg-white/95 hover:bg-white hover:scale-110 rounded-full w-11 h-11 md:w-13 md:h-13 flex items-center justify-center transition-transform shadow-2xl cursor-pointer"
            style="z-index: 100001;" title="Video trước">
        <span class="material-symbols-outlined text-3xl font-black">chevron_left</span>
    </button>
    <button type="button" onclick="event.stopPropagation(); changeVideo(1)"
            class="fixed right-2 md:right-8 top-1/2 -translate-y-1/2 text-slate-900 bg-white/95 hover:bg-white hover:scale-110 rounded-full w-11 h-11 md:w-13 md:h-13 flex items-center justify-center transition-transform shadow-2xl cursor-pointer"
            style="z-index: 100001;" title="Video kế tiếp">
        <span class="material-symbols-outlined text-3xl font-black">chevron_right</span>
    </button>

    {{-- Khung Video căn giữa hoàn hảo --}}
    <div class="fixed inset-0 flex items-center justify-center pointer-events-none" style="z-index: 100001;">
        <div class="pointer-events-auto rounded-3xl overflow-hidden shadow-2xl bg-black border border-white/15"
             style="width: min(440px, calc(88vh * 9 / 16), 92vw); height: min(820px, 88vh); aspect-ratio: 9/16; position: relative;">

            {{-- Loading Spinner --}}
            <div id="video-loading" class="absolute inset-0 flex items-center justify-center bg-black" style="z-index: 10;">
                <div class="flex flex-col items-center gap-3">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-600 border-t-orange-500"></div>
                    <span class="text-xs text-gray-400 font-medium">Đang tải video...</span>
                </div>
            </div>

            {{-- Container phát video (HTML5 thuần hoặc Iframe) --}}
            <div id="modal-video-container" class="w-full h-full relative" style="z-index: 20;"></div>
        </div>
    </div>
</div>

@php
    $jsonVideoData = $videos->map(function($v) {
        return [
            'id' => $v->id,
            'video_id' => $v->video_id,
            'clean_url' => $v->clean_video_url,
            'embed_url' => $v->embed_url,
            'watch_url' => $v->watch_url,
            'title' => $v->tieu_de,
            'author' => $v->nguoi_dang,
            'avatar' => $v->avatar_nguoi_dang,
            'views' => $v->luot_xem,
            'quan_name' => $v->quan?->ten_quan,
            'quan_url' => $v->quan ? route('quan.detail', $v->quan->slug) : null,
            'quan_tag' => $v->quan?->loai_hinh_kinh_doanh,
        ];
    })->values()->all();
@endphp

<script>
    const videoDataList = {!! json_encode($jsonVideoData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!};

    let currentVideoIndex = 0;
    let activeVideoPlayer = null;
    let isMuted = false;

    function setVideoSort(sortVal) {
        const sortInput = document.getElementById('input-sort');
        const form = document.getElementById('filter-form');
        if (sortInput && form) {
            sortInput.value = sortVal;
            form.submit();
        }
    }

    function openVideoModal(index) {
        if (!videoDataList || videoDataList.length === 0) return;
        currentVideoIndex = index;
        const video     = videoDataList[index];
        const modal     = document.getElementById('video-modal');
        const container = document.getElementById('modal-video-container');
        const loading   = document.getElementById('video-loading');

        modal.style.display = 'block';
        setTimeout(() => modal.classList.remove('opacity-0'), 10);
        loading.style.display = 'flex';
        container.innerHTML = '';
        activeVideoPlayer = null;

        // ƯU TIÊN 1: Phát bằng video HTML5 thuần nếu có link video MP4 sạch
        if (video.clean_url) {
            const playerWrapper = document.createElement('div');
            playerWrapper.className = 'relative w-full h-full bg-black flex items-center justify-center select-none overflow-hidden';

            const videoElem = document.createElement('video');
            videoElem.src = video.clean_url;
            videoElem.className = 'w-full h-full object-cover';
            videoElem.autoplay = true;
            videoElem.loop = true;
            videoElem.playsInline = true;
            videoElem.muted = isMuted;
            videoElem.preload = 'auto';

            // Top overlay: Quán ăn & Nút âm lượng
            const topBar = document.createElement('div');
            topBar.className = 'absolute top-3 inset-x-3 flex items-center justify-between z-30 pointer-events-none';
            topBar.innerHTML = `
                ${video.quan_name ? `
                    <a href="${video.quan_url}" onclick="event.stopPropagation()" style="background: rgba(15, 23, 42, 0.75); color: #ffffff;" class="pointer-events-auto inline-flex items-center gap-1.5 hover:bg-orange-600 backdrop-blur-md text-xs font-bold px-3 py-1.5 rounded-full transition shadow-lg max-w-[240px]">
                        <span class="material-symbols-outlined text-sm text-amber-400">restaurant</span>
                        <span class="truncate">${video.quan_name}</span>
                    </a>
                ` : '<div></div>'}
                <div class="flex items-center gap-2 pointer-events-auto">
                    <button type="button" id="btn-toggle-sound" style="background: rgba(15, 23, 42, 0.75); color: #ffffff;" class="w-9 h-9 rounded-full hover:bg-black/80 backdrop-blur-md flex items-center justify-center transition shadow-lg cursor-pointer">
                        <span class="material-symbols-outlined text-lg" id="icon-sound">${isMuted ? 'volume_off' : 'volume_up'}</span>
                    </button>
                </div>
            `;

            // Center indicator khi click play/pause
            const centerIcon = document.createElement('div');
            centerIcon.className = 'absolute inset-0 flex items-center justify-center pointer-events-none z-20';
            centerIcon.innerHTML = `
                <div id="play-pause-bubble" style="background: rgba(15, 23, 42, 0.68); color: #ffffff;" class="w-16 h-16 rounded-full backdrop-blur-md flex items-center justify-center opacity-0 transition-opacity">
                    <span class="material-symbols-outlined text-4xl" id="play-pause-icon">pause</span>
                </div>
            `;

            // Bottom overlay: Tiêu đề, tác giả & Thanh tiến trình
            const bottomBar = document.createElement('div');
            bottomBar.className = 'absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent p-4 pt-10 z-30 pointer-events-none';
            bottomBar.innerHTML = `
                <p class="text-white font-bold text-sm leading-snug line-clamp-2 drop-shadow-md mb-1">${video.title || 'Video Review'}</p>
                <div class="flex items-center gap-2 text-white/80 text-xs">
                    <span class="font-semibold">${video.author || '@reviewer'}</span>
                    ${video.views > 0 ? `<span>• ${Number(video.views).toLocaleString('vi-VN')} lượt xem</span>` : ''}
                </div>
                <div id="video-progress-container" class="mt-2.5 w-full bg-white/25 hover:bg-white/40 h-1.5 rounded-full overflow-hidden cursor-pointer pointer-events-auto relative">
                    <div id="video-progress-bar" style="background: #f97316;" class="h-full w-0 transition-all duration-75"></div>
                </div>
            `;

            playerWrapper.appendChild(videoElem);
            playerWrapper.appendChild(centerIcon);
            playerWrapper.appendChild(topBar);
            playerWrapper.appendChild(bottomBar);
            container.appendChild(playerWrapper);

            activeVideoPlayer = videoElem;

            videoElem.onloadeddata = () => { loading.style.display = 'none'; };
            videoElem.onerror = () => {
                loading.style.display = 'none';
                container.innerHTML = '';
                const iframe = document.createElement('iframe');
                iframe.src = `https://www.tiktok.com/embed/v2/${video.video_id}`;
                iframe.frameBorder = '0';
                iframe.allow = 'autoplay; clipboard-write; encrypted-media; picture-in-picture';
                iframe.allowFullscreen = true;
                iframe.style.cssText = 'width:100%;height:100%;border:none;display:block;background:#000;';
                container.appendChild(iframe);
            };
            videoElem.ontimeupdate = () => {
                const prog = document.getElementById('video-progress-bar');
                if (prog && videoElem.duration) {
                    prog.style.width = `${(videoElem.currentTime / videoElem.duration) * 100}%`;
                }
            };

            videoElem.play().catch(() => {
                videoElem.muted = true;
                isMuted = true;
                const icon = document.getElementById('icon-sound');
                if (icon) icon.textContent = 'volume_off';
                videoElem.play().catch(() => {});
            });

            playerWrapper.onclick = (e) => {
                if (e.target.closest('#btn-toggle-sound') || e.target.closest('#video-progress-container') || e.target.closest('a')) return;
                togglePlayPause(videoElem);
            };

            const btnSound = topBar.querySelector('#btn-toggle-sound');
            if (btnSound) {
                btnSound.onclick = (e) => {
                    e.stopPropagation();
                    isMuted = !isMuted;
                    videoElem.muted = isMuted;
                    document.getElementById('icon-sound').textContent = isMuted ? 'volume_off' : 'volume_up';
                };
            }

            const progContainer = bottomBar.querySelector('#video-progress-container');
            if (progContainer) {
                progContainer.onclick = (e) => {
                    e.stopPropagation();
                    const rect = progContainer.getBoundingClientRect();
                    const pos = (e.clientX - rect.left) / rect.width;
                    if (videoElem.duration) {
                        videoElem.currentTime = pos * videoElem.duration;
                    }
                };
            }
        }
        // ƯU TIÊN 2: Fallback Iframe nếu chưa có bản MP4 sạch
        else {
            const iframe = document.createElement('iframe');
            iframe.src             = `https://www.tiktok.com/embed/v2/${video.video_id}`;
            iframe.frameBorder     = '0';
            iframe.allow           = 'autoplay; clipboard-write; encrypted-media; picture-in-picture';
            iframe.allowFullscreen = true;
            iframe.style.cssText   = 'width:100%;height:100%;border:none;display:block;background:#000;';
            iframe.onload          = () => { loading.style.display = 'none'; };
            container.appendChild(iframe);
        }

        document.body.style.overflow = 'hidden';
    }

    function togglePlayPause(videoElem) {
        if (!videoElem) return;
        const bubble = document.getElementById('play-pause-bubble');
        const icon = document.getElementById('play-pause-icon');

        if (videoElem.paused) {
            videoElem.play();
            if (icon) icon.textContent = 'play_arrow';
        } else {
            videoElem.pause();
            if (icon) icon.textContent = 'pause';
        }

        if (bubble) {
            bubble.classList.remove('opacity-0');
            setTimeout(() => bubble.classList.add('opacity-0'), 500);
        }
    }

    function closeVideoModal() {
        const modal     = document.getElementById('video-modal');
        const container = document.getElementById('modal-video-container');
        const loading   = document.getElementById('video-loading');

        if (activeVideoPlayer) {
            activeVideoPlayer.pause();
            activeVideoPlayer.src = '';
            activeVideoPlayer = null;
        }

        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.style.display = 'none';
            container.innerHTML = '';
            loading.style.display = 'flex';
        }, 300);

        document.body.style.overflow = 'auto';
    }

    function changeVideo(dir) {
        if (!videoDataList || videoDataList.length === 0) return;
        let newIndex = currentVideoIndex + dir;
        if (newIndex < 0) newIndex = videoDataList.length - 1;
        if (newIndex >= videoDataList.length) newIndex = 0;
        openVideoModal(newIndex);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeVideoModal();
        if (e.key === 'ArrowLeft')  changeVideo(-1);
        if (e.key === 'ArrowRight') changeVideo(1);
        if (e.key === ' ' && activeVideoPlayer) {
            e.preventDefault();
            togglePlayPause(activeVideoPlayer);
        }
    });
</script>
@endsection
