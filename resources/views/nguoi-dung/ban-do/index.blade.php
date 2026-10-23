@extends('layouts.app')

@section('title', 'Bản đồ ẩm thực lân cận | Quán Mới')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<style>
    /* ==========================================================================
       BẢN ĐỒ QUÁN MỚI - SCOPED STYLESHEET (KHÔNG PHỤ THUỘC VÀO TAILWIND PRECOMPILED)
       ========================================================================== */
    :root {
        --bd-primary: #ea580c;
        --bd-primary-hover: #c2410c;
        --bd-primary-light: #fff7ed;
        --bd-primary-border: #fed7aa;
        --bd-gray-50: #f9fafb;
        --bd-gray-100: #f3f4f6;
        --bd-gray-200: #e5e7eb;
        --bd-gray-300: #d1d5db;
        --bd-gray-500: #6b7280;
        --bd-gray-700: #374151;
        --bd-gray-900: #111827;
        --bd-success: #10b981;
        --bd-success-light: #ecfdf5;
        --bd-radius: 14px;
        --bd-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        --bd-font: 'Be Vietnam Pro', ui-sans-serif, system-ui, sans-serif;
    }

    /* Container toàn trang bản đồ */
    .bd-app-root {
        position: relative;
        width: 100%;
        height: calc(100vh - 72px);
        min-height: 540px;
        display: flex;
        overflow: hidden;
        font-family: var(--bd-font);
        background-color: #f8fafc;
    }

    @media (max-width: 768px) {
        .bd-app-root {
            height: calc(100vh - 72px - 64px);
            min-height: calc(100vh - 136px);
        }
    }

    /* Sidebar máy tính */
    .bd-sidebar {
        width: 440px;
        flex: 0 0 440px;
        height: 100%;
        background: #ffffff;
        border-right: 1px solid var(--bd-gray-200);
        display: flex;
        flex-direction: column;
        z-index: 25;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Map container */
    .bd-map-canvas-wrap {
        flex: 1 1 0%;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    #bando-leaflet-map {
        width: 100%;
        height: 100%;
        z-index: 10;
        background: #e2e8f0;
    }

    /* Header của Sidebar */
    .bd-sidebar-header {
        padding: 16px;
        background: #ffffff;
        border-bottom: 1px solid var(--bd-gray-200);
        flex-shrink: 0;
    }

    .bd-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .bd-title-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .bd-logo-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--bd-primary-light);
        color: var(--bd-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .bd-title-main {
        font-size: 17px;
        font-weight: 800;
        color: var(--bd-gray-900);
        line-height: 1.2;
    }

    .bd-title-sub {
        font-size: 11.5px;
        color: var(--bd-gray-500);
        font-weight: 500;
        margin-top: 2px;
    }

    .bd-nav-back-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--bd-gray-700);
        background: var(--bd-gray-100);
        padding: 6px 10px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .bd-nav-back-link:hover {
        background: var(--bd-primary-light);
        color: var(--bd-primary);
    }

    /* Search input & suggestions */
    .bd-search-box-wrap {
        position: relative;
        margin-bottom: 10px;
    }

    .bd-search-input {
        width: 100%;
        height: 40px;
        padding: 0 36px 0 38px;
        border-radius: 10px;
        border: 1px solid var(--bd-gray-300);
        background: var(--bd-gray-50);
        font-size: 13px;
        color: var(--bd-gray-900);
        font-family: inherit;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .bd-search-input:focus {
        background: #ffffff;
        border-color: var(--bd-primary);
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
    }

    .bd-search-icon-left {
        position: absolute;
        left: 10px;
        top: 10px;
        color: var(--bd-gray-500);
        font-size: 20px;
        pointer-events: none;
    }

    .bd-search-clear-btn {
        position: absolute;
        right: 8px;
        top: 8px;
        background: none;
        border: none;
        color: var(--bd-gray-500);
        cursor: pointer;
        padding: 4px;
        border-radius: 50%;
        display: none;
    }

    .bd-search-clear-btn:hover {
        color: var(--bd-gray-900);
        background: var(--bd-gray-200);
    }

    .bd-search-suggestions {
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid var(--bd-gray-200);
        border-radius: 10px;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        z-index: 50;
        max-height: 290px;
        overflow-y: auto;
        display: none;
    }

    .bd-suggestion-group-header {
        padding: 6px 12px;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--bd-gray-500);
        background: var(--bd-gray-50);
        border-bottom: 1px solid var(--bd-gray-100);
    }

    .bd-suggestion-item {
        padding: 9px 14px;
        font-size: 12.5px;
        color: var(--bd-gray-700);
        cursor: pointer;
        border-bottom: 1px solid var(--bd-gray-100);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bd-suggestion-item:last-child {
        border-bottom: none;
    }

    .bd-suggestion-item:hover {
        background: var(--bd-primary-light);
        color: var(--bd-primary);
    }

    .bd-suggestion-sub {
        font-size: 11px;
        color: var(--bd-gray-500);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-top: 1px;
    }

    .bd-active-keyword-bar {
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 6px 10px;
        margin-top: 8px;
        border-radius: 8px;
        background: var(--bd-primary-light);
        border: 1px solid var(--bd-primary-border);
        font-size: 11.5px;
        color: var(--bd-primary-hover);
        font-weight: 600;
    }

    .bd-load-more-btn {
        width: 100%;
        padding: 10px 14px;
        margin-top: 4px;
        border-radius: 10px;
        border: 1px solid var(--bd-primary-border);
        background: var(--bd-primary-light);
        color: var(--bd-primary);
        font-size: 12.5px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .bd-load-more-btn:hover {
        background: var(--bd-primary);
        color: #ffffff;
        border-color: var(--bd-primary);
    }

    .qm-cluster-badge {
        width: 38px;
        height: 38px;
        border-radius: 9999px;
        background: #ea580c;
        color: #ffffff;
        font-weight: 800;
        font-size: 12.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #ffffff;
        box-shadow: 0 6px 16px rgba(234, 88, 12, 0.45);
        font-family: var(--bd-font);
    }

    /* GPS scan primary button */
    .bd-btn-gps-scan {
        width: 100%;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: var(--bd-primary);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.3);
        transition: all 0.2s ease;
    }

    .bd-btn-gps-scan:hover {
        background: var(--bd-primary-hover);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.4);
    }

    .bd-btn-gps-scan:active {
        transform: scale(0.98);
    }

    /* Preset & Radius selector rows */
    .bd-control-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 10px;
    }

    .bd-control-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--bd-gray-500);
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .bd-pill-group {
        display: flex;
        align-items: center;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 2px;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        user-select: none;
        -webkit-user-select: none;
        cursor: grab;
    }

    .bd-pill-group.is-dragging,
    .bd-pill-group.is-dragging * {
        cursor: grabbing !important;
        user-select: none !important;
    }

    .bd-pill-group::-webkit-scrollbar {
        display: none;
    }

    .bd-chip-btn {
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        border: 1px solid var(--bd-gray-200);
        background: #ffffff;
        color: var(--bd-gray-700);
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.15s ease;
        font-family: inherit;
        user-select: none;
        -webkit-user-select: none;
    }

    .bd-chip-btn:hover {
        background: var(--bd-gray-50);
        border-color: var(--bd-gray-300);
    }

    .bd-chip-btn.active {
        background: var(--bd-primary);
        color: #ffffff;
        border-color: var(--bd-primary);
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
    }

    /* Category filter row */
    .bd-cat-scroll {
        display: flex;
        align-items: center;
        gap: 6px;
        overflow-x: auto;
        padding: 8px 0 2px;
        scrollbar-width: none;
        margin-top: 6px;
        -webkit-overflow-scrolling: touch;
        user-select: none;
        -webkit-user-select: none;
        cursor: grab;
    }

    .bd-cat-scroll.is-dragging,
    .bd-cat-scroll.is-dragging * {
        cursor: grabbing !important;
        user-select: none !important;
    }

    .bd-cat-scroll::-webkit-scrollbar {
        display: none;
    }

    .bd-cat-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 12px;
        border-radius: 9999px;
        font-size: 11.5px;
        font-weight: 600;
        border: 1px solid var(--bd-gray-200);
        background: #ffffff;
        color: var(--bd-gray-700);
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.15s ease;
        font-family: inherit;
        user-select: none;
        -webkit-user-select: none;
    }

    .bd-cat-chip:hover {
        background: var(--bd-gray-50);
        color: var(--bd-gray-900);
    }

    .bd-cat-chip.active {
        background: var(--bd-gray-900);
        color: #ffffff;
        border-color: var(--bd-gray-900);
    }

    .bd-cat-chip-count {
        font-size: 10px;
        opacity: 0.7;
    }

    /* Sub-bar (Status & Toolbar) */
    .bd-sub-toolbar {
        padding: 8px 16px;
        background: var(--bd-gray-50);
        border-bottom: 1px solid var(--bd-gray-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11.5px;
        color: var(--bd-gray-500);
        flex-shrink: 0;
        gap: 8px;
    }

    .bd-status-info {
        font-weight: 600;
        color: var(--bd-gray-700);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .bd-tool-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .bd-toggle-open-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid var(--bd-gray-300);
        background: #ffffff;
        font-size: 11px;
        font-weight: 700;
        color: var(--bd-gray-700);
        cursor: pointer;
        font-family: inherit;
    }

    .bd-toggle-open-btn.active {
        background: var(--bd-success-light);
        color: #065f46;
        border-color: #6ee7b7;
    }

    .bd-sort-select {
        height: 26px;
        padding: 0 6px;
        border-radius: 6px;
        border: 1px solid var(--bd-gray-300);
        background: #ffffff;
        font-size: 11px;
        font-weight: 600;
        color: var(--bd-gray-700);
        font-family: inherit;
        outline: none;
    }

    /* List cards area */
    .bd-cards-scroll-container {
        flex: 1 1 0%;
        overflow-y: auto;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f8fafc;
    }

    /* Venue Card item */
    .bd-card-item {
        background: #ffffff;
        border: 1px solid var(--bd-gray-200);
        border-radius: var(--bd-radius);
        padding: 12px;
        display: flex;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .bd-card-item:hover {
        border-color: var(--bd-primary);
        box-shadow: 0 4px 16px rgba(234, 88, 12, 0.12);
        transform: translateY(-1px);
    }

    .bd-card-item.is-selected {
        border-color: var(--bd-primary);
        background: var(--bd-primary-light);
        box-shadow: 0 0 0 2px var(--bd-primary);
    }

    .bd-card-thumb-wrap {
        width: 86px;
        height: 86px;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
        background: var(--bd-gray-100);
    }

    .bd-card-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .bd-card-item:hover .bd-card-thumb-img {
        transform: scale(1.06);
    }

    .bd-card-rank-badge {
        position: absolute;
        bottom: 4px;
        right: 4px;
        background: rgba(17, 24, 39, 0.75);
        color: #ffffff;
        font-size: 9px;
        font-weight: 800;
        padding: 1px 5px;
        border-radius: 4px;
    }

    .bd-card-content {
        flex: 1 1 0%;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .bd-card-name-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 2px;
    }

    .bd-card-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--bd-gray-900);
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .bd-card-item:hover .bd-card-title {
        color: var(--bd-primary);
    }

    .bd-card-meta-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11.5px;
        color: var(--bd-gray-500);
        margin-bottom: 4px;
    }

    .bd-rating-badge {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        color: #d97706;
        font-weight: 700;
    }

    .bd-card-address {
        font-size: 11.5px;
        color: var(--bd-gray-500);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-bottom: 6px;
    }

    .bd-card-footer-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 6px;
        border-top: 1px solid var(--bd-gray-100);
    }

    .bd-card-distance-pill {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 11px;
        font-weight: 800;
        color: #be123c;
        background: #fff1f2;
        padding: 2px 7px;
        border-radius: 6px;
    }

    .bd-card-price-tag {
        font-size: 11px;
        font-weight: 600;
        color: var(--bd-gray-700);
    }

    .bd-card-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bd-card-link-dir {
        font-size: 11px;
        font-weight: 700;
        color: #2563eb;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }

    .bd-card-link-dir:hover {
        text-decoration: underline;
    }

    .bd-card-link-detail {
        font-size: 11px;
        font-weight: 700;
        color: var(--bd-primary);
        text-decoration: none;
    }

    .bd-card-link-detail:hover {
        text-decoration: underline;
    }

    /* Floating map controls */
    .bd-map-floating-top {
        position: absolute;
        top: 16px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 30;
    }

    .bd-btn-search-area {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 9999px;
        background: #ffffff;
        color: var(--bd-gray-900);
        border: 1px solid var(--bd-gray-200);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        font-size: 12.5px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .bd-btn-search-area:hover {
        background: var(--bd-primary-light);
        color: var(--bd-primary);
        border-color: var(--bd-primary-border);
        box-shadow: 0 6px 18px rgba(234, 88, 12, 0.2);
    }

    .bd-map-floating-controls {
        position: absolute;
        bottom: 114px;
        right: 16px;
        z-index: 30;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* Style Leaflet controls to prevent overlapping */
    .leaflet-top.leaflet-right {
        top: 16px !important;
        right: 16px !important;
        margin: 0 !important;
    }

    .leaflet-control-layers {
        margin: 0 !important;
        border-radius: 12px !important;
        border: 1px solid var(--bd-gray-200) !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12) !important;
        background: #ffffff !important;
        font-family: inherit !important;
        overflow: hidden;
    }

    .leaflet-control-layers-toggle {
        width: 38px !important;
        height: 38px !important;
        background-size: 20px 20px !important;
    }

    .leaflet-control-layers-expanded {
        padding: 10px 14px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #1f2937 !important;
    }

    .leaflet-bottom.leaflet-right {
        bottom: 16px !important;
        right: 16px !important;
        margin: 0 !important;
    }

    .leaflet-control-zoom {
        margin: 0 !important;
        border-radius: 10px !important;
        overflow: hidden;
        border: 1px solid var(--bd-gray-200) !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1) !important;
    }

    .leaflet-control-zoom a {
        width: 36px !important;
        height: 36px !important;
        line-height: 36px !important;
        color: var(--bd-gray-700) !important;
        font-size: 16px !important;
    }

    .leaflet-control-zoom a:hover {
        color: var(--bd-primary) !important;
        background: var(--bd-gray-50) !important;
    }

    .bd-floating-icon-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid var(--bd-gray-200);
        color: var(--bd-gray-700);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .bd-floating-icon-btn:hover {
        color: var(--bd-primary);
        transform: scale(1.05);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    /* Leaflet marker & popup styles */
    .qm-venue-marker-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 9px;
        border-radius: 9999px;
        background: #ea580c;
        color: #ffffff;
        font-weight: 800;
        font-size: 11px;
        white-space: nowrap;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.4);
        cursor: pointer;
        transition: transform 0.18s ease, background 0.18s ease, box-shadow 0.18s ease;
    }

    .qm-venue-marker-badge:hover,
    .qm-venue-marker-badge.is-active {
        background: #be123c;
        transform: scale(1.15) translateY(-2px);
        box-shadow: 0 6px 18px rgba(190, 18, 60, 0.55);
        z-index: 999 !important;
    }

    .qm-user-gps-dot {
        width: 20px;
        height: 20px;
        border-radius: 9999px;
        background: #2563eb;
        border: 3px solid #ffffff;
        box-shadow: 0 0 0 6px rgba(37, 99, 235, 0.25), 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .leaflet-popup-content-wrapper {
        border-radius: 16px !important;
        padding: 0 !important;
        overflow: hidden;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.22) !important;
    }

    .leaflet-popup-content {
        margin: 0 !important;
        line-height: inherit;
    }

    /* Popup structure */
    .bd-popup-card {
        width: 280px;
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        font-family: var(--bd-font);
    }

    .bd-popup-hero {
        position: relative;
        width: 100%;
        height: 125px;
        background: var(--bd-gray-100);
        overflow: hidden;
    }

    .bd-popup-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .bd-popup-body {
        padding: 12px;
    }

    .bd-popup-title {
        font-size: 14.5px;
        font-weight: 800;
        color: var(--bd-gray-900);
        margin: 0 0 4px;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .bd-popup-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11.5px;
        color: var(--bd-gray-500);
        margin-bottom: 6px;
    }

    .bd-popup-address {
        font-size: 11.5px;
        color: var(--bd-gray-600);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-bottom: 10px;
    }

    .bd-popup-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding-top: 8px;
        border-top: 1px solid var(--bd-gray-100);
    }

    .bd-popup-btn-dir {
        padding: 5px 10px;
        border-radius: 8px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .bd-popup-btn-dir:hover {
        background: #dbeafe;
    }

    .bd-popup-btn-detail {
        padding: 5px 12px;
        border-radius: 8px;
        background: var(--bd-primary);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
    }

    .bd-popup-btn-detail:hover {
        background: var(--bd-primary-hover);
    }

    /* Radar Pulse Animation */
    @keyframes bdRadarPulse {
        0% { transform: scale(0.95); opacity: 0.9; }
        50% { transform: scale(1.08); opacity: 0.35; }
        100% { transform: scale(0.95); opacity: 0.9; }
    }

    .bd-radar-pulse {
        animation: bdRadarPulse 2.4s infinite ease-in-out;
    }

    /* Empty state */
    .bd-empty-state-wrap {
        padding: 40px 16px;
        text-align: center;
    }

    .bd-empty-radar-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        background: var(--bd-primary-light);
        color: var(--bd-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
    }

    /* Mobile toggle button */
    .bd-mobile-toggle-wrap {
        display: none;
        position: absolute;
        bottom: 16px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 35;
    }

    .bd-mobile-toggle-btn {
        padding: 10px 20px;
        border-radius: 9999px;
        background: var(--bd-gray-900);
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        border: none;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-family: inherit;
    }

    .bd-mobile-toggle-btn:active {
        transform: scale(0.96);
    }

    .bd-mobile-close-btn {
        display: none;
        background: none;
        border: none;
        color: var(--bd-gray-700);
        cursor: pointer;
        padding: 4px;
        border-radius: 8px;
    }

    /* Responsive Rules */
    @media (max-width: 768px) {
        .bd-sidebar {
            position: fixed;
            top: 72px;
            left: 0;
            right: 0;
            bottom: 64px;
            width: 100%;
            flex: none;
            z-index: 45;
            transform: translateY(105%);
        }

        .bd-sidebar.mobile-open {
            transform: translateY(0);
        }

        .bd-mobile-toggle-wrap {
            display: block;
        }

        .bd-mobile-close-btn {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="bd-app-root" id="bando-root">

    {{-- SIDEBAR: TÌM KIẾM, BỘ LỌC & DANH SÁCH QUÁN ĂN --}}
    <aside id="bando-sidebar" class="bd-sidebar">
        
        {{-- Header Sidebar: Tiêu đề + Tìm kiếm địa chỉ + Nút GPS --}}
        <div class="bd-sidebar-header">
            <div class="bd-title-row">
                <div class="bd-title-group">
                    <div class="bd-logo-icon">
                        <span class="material-symbols-outlined" style="font-size: 22px;">map</span>
                    </div>
                    <div>
                        <h1 class="bd-title-main">Bản đồ Ẩm thực</h1>
                        <p class="bd-title-sub">Khám phá các quán ăn quanh bạn</p>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 6px;">
                    <a href="{{ route('kham-pha') }}" class="bd-nav-back-link" title="Trở về trang Khám phá">
                        <span class="material-symbols-outlined" style="font-size: 16px;">view_list</span>
                        <span>Danh sách</span>
                    </a>
                    {{-- Nút đóng trên mobile --}}
                    <button type="button" class="bd-mobile-close-btn" onclick="toggleBandoMobileView()" title="Đóng danh sách">
                        <span class="material-symbols-outlined" style="font-size: 22px;">close</span>
                    </button>
                </div>
            </div>

            {{-- Ô tìm kiếm tên quán, món ăn hoặc địa chỉ/khu vực thông minh --}}
            <div class="bd-search-box-wrap">
                <span class="material-symbols-outlined bd-search-icon-left">search</span>
                <input type="text" 
                       id="bd-address-search-input" 
                       class="bd-search-input" 
                       value="{{ $tuKhoa ?? '' }}"
                       placeholder="Tìm tên quán, món ăn, phố, quận..." 
                       autocomplete="off" />
                <button type="button" id="bd-address-clear-btn" class="bd-search-clear-btn" style="{{ !empty($tuKhoa) ? 'display: block;' : '' }}" onclick="clearAddressSearch()">
                    <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                </button>
                <div id="bd-address-suggestions" class="bd-search-suggestions"></div>
            </div>

            {{-- Thanh hiển thị từ khóa đang lọc --}}
            <div id="bd-active-keyword-bar" class="bd-active-keyword-bar" style="{{ !empty($tuKhoa) ? 'display: flex;' : 'display: none;' }}">
                <div style="display: flex; align-items: center; gap: 5px; overflow: hidden;">
                    <span class="material-symbols-outlined" style="font-size: 15px;">filter_alt</span>
                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Từ khóa: <strong id="bd-active-keyword-text">{{ $tuKhoa ?? '' }}</strong></span>
                </div>
                <button type="button" onclick="clearKeywordFilter()" style="background: none; border: none; color: inherit; cursor: pointer; font-weight: 800; font-size: 11px; padding: 2px 6px;">Xóa</button>
            </div>

            {{-- Nút bấm Định vị GPS --}}
            <button type="button" id="bando-btn-scan" onclick="handleBandoScan()" class="bd-btn-gps-scan" style="margin-top: 8px;">
                <span class="material-symbols-outlined" id="bando-btn-scan-icon" style="font-size: 19px;">my_location</span>
                <span id="bando-btn-scan-text">Định vị & Quét quán gần tôi</span>
            </button>

            {{-- Chọn nhanh địa điểm nổi tiếng --}}
            <div class="bd-control-row">
                <span class="bd-control-label">Điểm nóng:</span>
                <div class="bd-pill-group">
                    <button type="button" onclick="scanBandoPreset(21.028511, 105.854200, 'Hoàn Kiếm')" class="bd-chip-btn">Hoàn Kiếm</button>
                    <button type="button" onclick="scanBandoPreset(21.031000, 105.782000, 'Cầu Giấy')" class="bd-chip-btn">Cầu Giấy</button>
                    <button type="button" onclick="scanBandoPreset(10.776889, 106.700806, 'Quận 1 HCM')" class="bd-chip-btn">Q.1 HCM</button>
                    <button type="button" onclick="scanBandoPreset(16.054407, 108.202167, 'Đà Nẵng')" class="bd-chip-btn">Đà Nẵng</button>
                </div>
            </div>

            {{-- Bộ chọn bán kính --}}
            <div class="bd-control-row">
                <span class="bd-control-label">Bán kính:</span>
                <div class="bd-pill-group">
                    @foreach([1 => '1 km', 3 => '3 km', 5 => '5 km', 10 => '10 km'] as $radVal => $radLabel)
                        <button type="button" 
                                onclick="changeBandoRadius({{ $radVal }})" 
                                data-radius="{{ $radVal }}"
                                class="bd-chip-btn bando-radius-btn {{ (int) $banKinh === $radVal ? 'active' : '' }}">
                            {{ $radLabel }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Bộ lọc danh mục ẩm thực động từ Database --}}
            <div class="bd-cat-scroll" id="bd-category-scroll">
                <button type="button" 
                        data-category="" 
                        class="bd-cat-chip bando-cat-btn {{ empty($danhMuc) ? 'active' : '' }}">
                    Tất cả
                </button>
                @if(isset($categories) && count($categories) > 0)
                    @foreach($categories as $cat)
                        <button type="button" 
                                data-category="{{ $cat['name'] }}" 
                                class="bd-cat-chip bando-cat-btn {{ $danhMuc === $cat['name'] ? 'active' : '' }}">
                            <span>{{ $cat['name'] }}</span>
                            <span class="bd-cat-chip-count">({{ $cat['total'] }})</span>
                        </button>
                    @endforeach
                @else
                    @foreach(['Nhà hàng', 'Cà phê & Trà', 'Lẩu & Nướng', 'Ăn vặt', 'Billiards & Giải trí', 'Quán Đêm 24/7'] as $staticCat)
                        <button type="button" 
                                data-category="{{ $staticCat }}" 
                                class="bd-cat-chip bando-cat-btn {{ $danhMuc === $staticCat ? 'active' : '' }}">
                            {{ $staticCat }}
                        </button>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Thanh công cụ phụ (Trạng thái kết quả + Lọc đang mở + Sắp xếp) --}}
        <div class="bd-sub-toolbar">
            <div class="bd-status-info" id="bando-status-text">Đang tải dữ liệu...</div>
            <div class="bd-tool-actions">
                <button type="button" id="bd-filter-open-now-btn" class="bd-toggle-open-btn {{ !empty($dangMo) ? 'active' : '' }}" onclick="toggleOpenNowFilter()" title="Chỉ hiện các quán đang mở">
                    <span class="material-symbols-outlined" style="font-size: 13px;">schedule</span>
                    <span>Đang mở</span>
                </button>
                <select id="bd-sort-select" class="bd-sort-select" onchange="changeSortOrder(this.value)">
                    <option value="distance_asc" {{ ($sapXep ?? 'distance_asc') === 'distance_asc' ? 'selected' : '' }}>Gần nhất</option>
                    <option value="rating_desc" {{ ($sapXep ?? '') === 'rating_desc' ? 'selected' : '' }}>Đánh giá cao</option>
                    <option value="price_asc" {{ ($sapXep ?? '') === 'price_asc' ? 'selected' : '' }}>Giá rẻ nhất</option>
                </select>
            </div>
        </div>

        {{-- Danh sách các thẻ quán ăn lân cận --}}
        <div id="bando-venue-list" class="bd-cards-scroll-container">
            {{-- Trạng thái tải ban đầu --}}
            <div class="bd-empty-state-wrap">
                <div class="bd-empty-radar-icon bd-radar-pulse">
                    <span class="material-symbols-outlined" style="font-size: 32px;">radar</span>
                </div>
                <h3 style="font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 4px;">Đang quét quán lân cận...</h3>
                <p style="font-size: 12px; color: #6b7280; max-width: 260px; margin: 0 auto;">Vui lòng đợi giây lát để hệ thống tìm các địa điểm ẩm thực quanh bạn.</p>
            </div>
        </div>
    </aside>

    {{-- KHỐI BẢN ĐỒ TOÀN MÀN HÌNH (MAP CANVAS) --}}
    <main class="bd-map-canvas-wrap">
        {{-- Bản đồ Leaflet (Bắt buộc giữ ID để khớp test suite) --}}
        <div id="bando-leaflet-map"></div>

        {{-- Nút nổi: "Tìm lại ở vùng này" (Search This Area) --}}
        <div class="bd-map-floating-top" id="bd-search-area-pill" style="display: none;">
            <button type="button" onclick="scanAtCurrentMapCenter()" class="bd-btn-search-area">
                <span class="material-symbols-outlined" style="font-size: 17px;">refresh</span>
                <span>Tìm lại ở vùng này</span>
            </button>
        </div>

        {{-- Nút điều khiển nổi góc phải bản đồ --}}
        <div class="bd-map-floating-controls">
            {{-- Nút Quay về vị trí của bạn --}}
            <button type="button" 
                    id="bando-btn-recenter" 
                    onclick="recenterBandoToUser()" 
                    class="bd-floating-icon-btn" 
                    title="Về vị trí GPS của tôi">
                <span class="material-symbols-outlined" style="font-size: 20px;">my_location</span>
            </button>

            {{-- Nút quét lại --}}
            <button type="button" 
                    onclick="scanAtCurrentMapCenter()" 
                    class="bd-floating-icon-btn" 
                    title="Quét lại tâm bản đồ">
                <span class="material-symbols-outlined" style="font-size: 20px;">sync</span>
            </button>
        </div>

        {{-- Nút chuyển chế độ xem danh sách trên Mobile --}}
        <div class="bd-mobile-toggle-wrap">
            <button type="button" 
                    id="bando-mobile-toggle-btn"
                    onclick="toggleBandoMobileView()"
                    class="bd-mobile-toggle-btn">
                <span class="material-symbols-outlined" id="bando-mobile-toggle-icon" style="font-size: 18px;">format_list_bulleted</span>
                <span id="bando-mobile-toggle-text">Xem danh sách (<span id="bando-mobile-count">0</span>)</span>
            </button>
        </div>
    </main>
</div>
@endsection

{{-- Ẩn footer ở trang bản đồ để trải nghiệm toàn màn hình như Google Maps / Grab --}}
@section('footer')
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
(function() {
    // Quản lý trạng thái bản đồ và bộ lọc
    const state = {
        map: null,
        markersLayer: null,
        userGpsMarker: null,
        radiusCircle: null,
        gpsLat: null,
        gpsLng: null,
        userLat: {{ $lat && is_numeric($lat) ? (float)$lat : 'null' }},
        userLng: {{ $lng && is_numeric($lng) ? (float)$lng : 'null' }},
        currentRadius: {{ (int) ($banKinh ?: 5) }},
        currentCategory: @json($danhMuc ?? ''),
        searchKeyword: @json($tuKhoa ?? ''),
        currentSort: @json($sapXep ?? 'distance_asc'),
        filterOpenOnly: {{ !empty($dangMo) ? 'true' : 'false' }},
        currentPage: 1,
        lastPage: 1,
        totalMatched: 0,
        rawQuans: [],
        filteredQuans: [],
        selectedVenueId: null,
        isLoading: false,
        fetchAbortController: null,
        geocodeAbortController: null,
        isPopupOpen: false,
        lastPopupCloseTime: 0,
        isMobileListOpen: false,
        lastMapCenter: null,
        searchDebounceTimer: null
    };

    // Khởi tạo bản đồ Leaflet
    function initMap() {
        // Tọa độ mặc định: Trung tâm Hoàn Kiếm, Hà Nội
        const defaultLat = state.userLat || 21.028511;
        const defaultLng = state.userLng || 105.854200;
        const defaultZoom = getZoomForRadius(state.currentRadius);

        state.map = L.map('bando-leaflet-map', {
            center: [defaultLat, defaultLng],
            zoom: defaultZoom,
            zoomControl: false
        });

        // Zoom control góc dưới bên phải
        L.control.zoom({ position: 'bottomright' }).addTo(state.map);

        // 1. Google Maps Roadmap
        const googleStreetLayer = L.tileLayer('https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            subdomains: ['0', '1', '2', '3'],
            maxZoom: 20,
            attribution: '&copy; Google Maps',
            keepBuffer: 8,
            updateWhenIdle: false
        });

        // 2. CartoDB Voyager
        const cartoVoyagerLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 19,
            keepBuffer: 8,
            updateWhenIdle: false
        });

        // 3. Google Maps Vệ tinh kết hợp đường phố
        const googleSatelliteLayer = L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            subdomains: ['0', '1', '2', '3'],
            maxZoom: 20,
            attribution: '&copy; Google Maps',
            keepBuffer: 8,
            updateWhenIdle: false
        });

        googleStreetLayer.addTo(state.map);

        L.control.layers({
            "Google Đường phố": googleStreetLayer,
            "Carto Tươi sáng": cartoVoyagerLayer,
            "Google Vệ tinh": googleSatelliteLayer
        }, null, { position: 'topright' }).addTo(state.map);

        // Sửa triệt để hiện tượng ô xám khi mới mở trang
        setTimeout(() => { if (state.map) state.map.invalidateSize(); }, 150);
        setTimeout(() => { if (state.map) state.map.invalidateSize(); }, 600);
        window.addEventListener('resize', () => { if (state.map) state.map.invalidateSize(); });

        // Sử dụng MarkerClusterGroup nếu đã nạp thư viện để tránh chồng chéo điểm ghim ở vùng đông quán
        if (typeof L.markerClusterGroup === 'function') {
            state.markersLayer = L.markerClusterGroup({
                showCoverageOnHover: false,
                maxClusterRadius: 42,
                disableClusteringAtZoom: 16,
                spiderfyOnMaxZoom: true,
                iconCreateFunction: function(cluster) {
                    const count = cluster.getChildCount();
                    return L.divIcon({
                        html: `<div class="qm-cluster-badge"><span>+${count}</span></div>`,
                        className: 'custom-cluster-icon',
                        iconSize: L.point(38, 38)
                    });
                }
            }).addTo(state.map);
        } else {
            state.markersLayer = L.layerGroup().addTo(state.map);
        }

        // Theo dõi trạng thái đóng/mở Popup để không quét nhầm khi người dùng click ra ngoài đóng Popup
        state.map.on('popupopen', function() {
            state.isPopupOpen = true;
        });
        state.map.on('popupclose', function() {
            state.isPopupOpen = false;
            state.lastPopupCloseTime = Date.now();
        });

        // Sự kiện di chuyển/kéo bản đồ -> hiện nút "Tìm lại ở vùng này"
        state.map.on('moveend', function() {
            const currentCenter = state.map.getCenter();
            if (state.lastMapCenter) {
                const movedDistance = calculateDistanceKm(
                    state.lastMapCenter.lat, state.lastMapCenter.lng,
                    currentCenter.lat, currentCenter.lng
                );
                const pill = document.getElementById('bd-search-area-pill');
                if (pill && movedDistance > 0.3) {
                    pill.style.display = 'block';
                }
            }
        });

        // Nhấp lên vùng trống bản đồ để đổi tâm quét (bỏ qua nếu vừa đóng popup)
        state.map.on('click', function(e) {
            if (state.isPopupOpen || (Date.now() - state.lastPopupCloseTime < 350)) {
                return;
            }
            hideSearchAreaPill();
            updateUserPosition(e.latlng.lat, e.latlng.lng, false);
            fetchNearbyVenues(e.latlng.lat, e.latlng.lng, state.currentRadius, state.currentCategory, 1, false);
        });

        // Thiết lập sự kiện click cho các nút danh mục bằng data-category an toàn
        const catScroll = document.getElementById('bd-category-scroll');
        if (catScroll) {
            catScroll.addEventListener('click', function(e) {
                const btn = e.target.closest('.bando-cat-btn');
                if (!btn) return;
                filterBandoCategory(btn.getAttribute('data-category') || '');
            });
        }

        // Kích hoạt kéo chuột qua lại (Drag to scroll) & lăn con lăn chuột ngang cho các bộ lọc
        initHorizontalDragScroll(catScroll);
        document.querySelectorAll('.bd-pill-group').forEach(group => {
            initHorizontalDragScroll(group);
        });

        // Khởi chạy quét ban đầu
        updateUserPosition(defaultLat, defaultLng, false);
        fetchNearbyVenues(defaultLat, defaultLng, state.currentRadius, state.currentCategory, 1, false);

        // Thiết lập tìm kiếm địa chỉ & quán ăn
        setupAddressSearch();

        // Tự động kiểm tra GPS nếu chưa có tọa độ trên URL và đã từng cấp quyền
        if (!{{ $lat && is_numeric($lat) ? 'true' : 'false' }} && navigator.permissions && navigator.permissions.query) {
            navigator.permissions.query({ name: 'geolocation' }).then(result => {
                if (result.state === 'granted') {
                    handleBandoScan();
                }
            }).catch(() => {});
        }
    }

    // Đồng bộ trạng thái lọc lên URL để F5 hoặc bấm Back không mất vị trí đang xem
    function syncUrlState() {
        if (!window.history || !window.history.replaceState) return;
        const params = new URLSearchParams();
        if (state.userLat && state.userLng) {
            params.set('lat', Number(state.userLat).toFixed(6));
            params.set('lng', Number(state.userLng).toFixed(6));
        }
        if (state.currentRadius && state.currentRadius !== 5) {
            params.set('ban_kinh', String(state.currentRadius));
        }
        if (state.currentCategory) {
            params.set('danh_muc', state.currentCategory);
        }
        if (state.searchKeyword) {
            params.set('q', state.searchKeyword);
        }
        if (state.currentSort && state.currentSort !== 'distance_asc') {
            params.set('sap_xep', state.currentSort);
        }
        if (state.filterOpenOnly) {
            params.set('dang_mo', '1');
        }
        const queryString = params.toString();
        const newUrl = window.location.pathname + (queryString ? `?${queryString}` : '');
        window.history.replaceState({ path: newUrl }, '', newUrl);
    }

    // Cập nhật vị trí tâm quét trên bản đồ
    function updateUserPosition(lat, lng, panTo = true) {
        state.userLat = lat;
        state.userLng = lng;
        state.lastMapCenter = { lat: lat, lng: lng };

        if (state.userGpsMarker) {
            state.userGpsMarker.setLatLng([lat, lng]);
        } else {
            const gpsIcon = L.divIcon({
                className: 'qm-user-gps-dot',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });
            state.userGpsMarker = L.marker([lat, lng], { icon: gpsIcon, zIndexOffset: 1000 }).addTo(state.map);
            state.userGpsMarker.bindTooltip('Vị trí tâm quét', { permanent: false, direction: 'top' });
        }

        const radiusMeters = state.currentRadius * 1000;
        if (state.radiusCircle) {
            state.radiusCircle.setLatLng([lat, lng]);
            state.radiusCircle.setRadius(radiusMeters);
        } else {
            state.radiusCircle = L.circle([lat, lng], {
                radius: radiusMeters,
                color: '#ea580c',
                weight: 1.5,
                opacity: 0.6,
                fillColor: '#ea580c',
                fillOpacity: 0.06,
                dashArray: '6, 6'
            }).addTo(state.map);
        }

        if (panTo && state.map) {
            state.map.setView([lat, lng], getZoomForRadius(state.currentRadius));
        }

        syncUrlState();
    }

    function getZoomForRadius(radiusKm) {
        if (radiusKm <= 1) return 15;
        if (radiusKm <= 3) return 14;
        if (radiusKm <= 5) return 13;
        return 12;
    }

    // Gọi API lấy quán lân cận (sử dụng AbortController chống race condition khi bấm lọc nhanh)
    async function fetchNearbyVenues(lat, lng, radius, category = '', page = 1, append = false) {
        if (state.fetchAbortController) {
            state.fetchAbortController.abort();
        }
        const abortController = new AbortController();
        state.fetchAbortController = abortController;

        state.isLoading = true;
        updateScanButtonState(true);

        const statusText = document.getElementById('bando-status-text');
        if (statusText) {
            statusText.textContent = append
                ? `Đang tải thêm quán (trang ${page})...`
                : `Đang quét trong bán kính ${radius} km...`;
        }

        try {
            const params = new URLSearchParams({
                lat: String(lat),
                lng: String(lng),
                radius: String(radius),
                page: String(page),
                per_page: '40'
            });
            if (category) params.set('danh_muc', category);
            if (state.searchKeyword) params.set('q', state.searchKeyword);

            const res = await fetch(`/api/ban-do/quan-lan-can?${params.toString()}`, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
                signal: abortController.signal
            });
            const json = await res.json();

            if (json.success && Array.isArray(json.data)) {
                state.currentPage = json.meta?.current_page || page;
                state.lastPage = json.meta?.last_page || 1;
                state.totalMatched = json.meta?.total ?? json.data.length;

                if (append) {
                    const existingIds = new Set(state.rawQuans.map(item => item.id));
                    json.data.forEach(item => {
                        if (!existingIds.has(item.id)) {
                            state.rawQuans.push(item);
                        }
                    });
                } else {
                    state.rawQuans = json.data;
                }

                applyLocalFiltersAndSort();
            } else {
                if (!append) {
                    state.rawQuans = [];
                    state.filteredQuans = [];
                    state.totalMatched = 0;
                    state.currentPage = 1;
                    state.lastPage = 1;
                    renderVenueCards([]);
                    renderVenueMarkers([]);
                }
                if (statusText) statusText.textContent = json.message || 'Không tìm thấy quán nào';
            }
        } catch (err) {
            if (err && err.name === 'AbortError') {
                return;
            }
            console.error('Lỗi quét quán lân cận:', err);
            if (statusText) statusText.textContent = 'Lỗi kết nối máy chủ';
        } finally {
            if (state.fetchAbortController === abortController) {
                state.isLoading = false;
                updateScanButtonState(false);
                hideSearchAreaPill();
            }
        }
    }

    window.loadMoreVenues = function() {
        if (state.isLoading || state.currentPage >= state.lastPage) return;
        fetchNearbyVenues(
            state.userLat,
            state.userLng,
            state.currentRadius,
            state.currentCategory,
            state.currentPage + 1,
            true
        );
    };

    // Lọc và sắp xếp nội bộ ngay lập tức + cập nhật chính xác số đếm trạng thái
    function applyLocalFiltersAndSort() {
        let list = [...state.rawQuans];

        if (state.filterOpenOnly) {
            list = list.filter(q => q.dang_mo_cua);
        }

        if (state.currentSort === 'distance_asc') {
            list.sort((a, b) => a.khoang_cach_km - b.khoang_cach_km);
        } else if (state.currentSort === 'rating_desc') {
            list.sort((a, b) => (b.so_sao_trung_binh || 0) - (a.so_sao_trung_binh || 0));
        } else if (state.currentSort === 'price_asc') {
            list.sort((a, b) => {
                const priceA = a.gia_nho_nhat || 999999999;
                const priceB = b.gia_nho_nhat || 999999999;
                return priceA - priceB;
            });
        }

        state.filteredQuans = list;
        renderVenueMarkers(list);
        renderVenueCards(list);

        const statusText = document.getElementById('bando-status-text');
        const mobileCount = document.getElementById('bando-mobile-count');
        const displayedCount = list.length;
        const total = state.filterOpenOnly ? displayedCount : (state.totalMatched || displayedCount);

        if (statusText) {
            if (state.filterOpenOnly) {
                statusText.textContent = `Đang mở ${displayedCount} / ${state.rawQuans.length} quán (${state.currentRadius} km)`;
            } else {
                statusText.textContent = `Tìm thấy ${total} quán trong ${state.currentRadius} km`;
            }
        }
        if (mobileCount) {
            mobileCount.textContent = `${displayedCount}`;
        }

        syncUrlState();
    }

    // Vẽ marker lên bản đồ Leaflet
    function renderVenueMarkers(quans) {
        if (!state.markersLayer) return;
        state.markersLayer.clearLayers();

        const originLat = state.gpsLat || state.userLat;
        const originLng = state.gpsLng || state.userLng;

        quans.forEach((q) => {
            if (!q.latitude || !q.longitude) return;

            const shortName = q.ten_quan.length > 15 ? q.ten_quan.substring(0, 14) + '…' : q.ten_quan;
            const markerHtml = `
                <div class="qm-venue-marker-badge ${state.selectedVenueId === q.id ? 'is-active' : ''}" 
                     id="bd-marker-${q.id}" 
                     data-id="${q.id}">
                    <span class="material-symbols-outlined" style="font-size: 13px;">restaurant</span>
                    <span>${escapeHtml(shortName)}</span>
                </div>
            `;

            const icon = L.divIcon({
                className: 'custom-venue-badge-container',
                html: markerHtml,
                iconSize: [null, null],
                iconAnchor: [30, 15]
            });

            const marker = L.marker([q.latitude, q.longitude], { icon: icon });
            state.markersLayer.addLayer(marker);

            const dirUrl = originLat && originLng
                ? `https://www.google.com/maps/dir/?api=1&origin=${originLat},${originLng}&destination=${q.latitude},${q.longitude}`
                : `https://www.google.com/maps/dir/?api=1&destination=${q.latitude},${q.longitude}`;

            const openStatusBadge = q.dang_mo_cua
                ? `<span style="background: rgba(16, 185, 129, 0.9); color: white; padding: 2px 7px; border-radius: 9999px; font-size: 10px; font-weight: 700;">Đang mở cửa</span>`
                : `<span style="background: rgba(107, 114, 128, 0.9); color: white; padding: 2px 7px; border-radius: 9999px; font-size: 10px; font-weight: 700;">Tạm đóng</span>`;

            const ratingDisplay = q.so_sao_trung_binh > 0
                ? `<span class="bd-rating-badge"><span class="material-symbols-outlined" style="font-size: 13px; font-variation-settings: 'FILL' 1;">star</span>${q.so_sao_trung_binh} (${q.so_luong_danh_gia || 0})</span>`
                : `<span style="color: #9ca3af; font-size: 11px;">Chưa có đánh giá</span>`;

            const priceDisplay = (q.gia_nho_nhat && q.gia_lon_nhat)
                ? `${formatPrice(q.gia_nho_nhat)} - ${formatPrice(q.gia_lon_nhat)}`
                : (q.gia_nho_nhat ? `Từ ${formatPrice(q.gia_nho_nhat)}` : 'Giá đang cập nhật');

            const popupHtml = `
                <div class="bd-popup-card">
                    <div class="bd-popup-hero">
                        <img src="${q.anh_dai_dien || 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=400&q=80'}" alt="${escapeHtml(q.ten_quan)}" />
                        <div style="position: absolute; top: 8px; left: 8px;">${openStatusBadge}</div>
                        <div style="position: absolute; top: 8px; right: 8px; background: #ea580c; color: white; padding: 2px 7px; border-radius: 9999px; font-size: 10px; font-weight: 800;">
                            ${q.khoang_cach_km} km
                        </div>
                    </div>
                    <div class="bd-popup-body">
                        <h4 class="bd-popup-title">${escapeHtml(q.ten_quan)}</h4>
                        <div class="bd-popup-meta">
                            <span>${escapeHtml(q.loai_hinh_kinh_doanh || 'Quán ăn')}</span>
                            <span>•</span>
                            ${ratingDisplay}
                        </div>
                        <div class="bd-popup-address">${escapeHtml(q.dia_chi || 'Địa chỉ cập nhật')}</div>
                        <div style="font-size: 11.5px; font-weight: 700; color: #ea580c; margin-bottom: 8px;">
                            ${priceDisplay}
                        </div>
                        <div class="bd-popup-actions">
                            <a href="${dirUrl}" target="_blank" rel="noopener noreferrer" class="bd-popup-btn-dir">
                                <span class="material-symbols-outlined" style="font-size: 13px;">directions</span>
                                Chỉ đường
                            </a>
                            <a href="${q.url}" class="bd-popup-btn-detail">Xem quán ➔</a>
                        </div>
                    </div>
                </div>
            `;

            marker.bindPopup(popupHtml, { maxWidth: 300 });

            marker.on('click', () => {
                selectVenue(q.id, false);
            });

            q._marker = marker;
        });
    }

    // Hiển thị danh sách card ở sidebar
    function renderVenueCards(quans) {
        const container = document.getElementById('bando-venue-list');
        if (!container) return;

        if (quans.length === 0) {
            const extraHint = state.searchKeyword
                ? `Không có quán nào khớp từ khóa "${escapeHtml(state.searchKeyword)}" trong vùng này.`
                : 'Hãy thử tăng bán kính lên 10 km hoặc chọn danh mục "Tất cả".';
            container.innerHTML = `
                <div class="bd-empty-state-wrap">
                    <div class="bd-empty-radar-icon" style="background: #f1f5f9; color: #94a3b8;">
                        <span class="material-symbols-outlined" style="font-size: 32px;">location_off</span>
                    </div>
                    <h3 style="font-size: 14.5px; font-weight: 700; color: #334155; margin-bottom: 4px;">Không tìm thấy quán nào</h3>
                    <p style="font-size: 12px; color: #64748b; max-width: 260px; margin: 0 auto 12px;">${extraHint}</p>
                    <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                        ${state.searchKeyword ? '<button type="button" onclick="clearKeywordFilter()" class="bd-chip-btn">Xóa lọc từ khóa</button>' : ''}
                        <button type="button" onclick="changeBandoRadius(10)" class="bd-chip-btn active">Tăng bán kính lên 10 km</button>
                    </div>
                </div>
            `;
            return;
        }

        const originLat = state.gpsLat || state.userLat;
        const originLng = state.gpsLng || state.userLng;

        let html = '';
        quans.forEach((q, idx) => {
            const isSelected = state.selectedVenueId === q.id;
            const priceText = (q.gia_nho_nhat && q.gia_lon_nhat)
                ? `${formatPrice(q.gia_nho_nhat)} - ${formatPrice(q.gia_lon_nhat)}`
                : (q.gia_nho_nhat ? `Từ ${formatPrice(q.gia_nho_nhat)}` : 'Đang cập nhật');

            const ratingHtml = q.so_sao_trung_binh > 0
                ? `<span class="bd-rating-badge"><span class="material-symbols-outlined" style="font-size: 13px; font-variation-settings: 'FILL' 1;">star</span>${q.so_sao_trung_binh}</span>`
                : `<span style="color: #94a3b8; font-size: 11px;">Mới</span>`;

            const statusDot = q.dang_mo_cua
                ? `<span style="color: #10b981; font-weight: 700;">● Mở</span>`
                : `<span style="color: #94a3b8; font-weight: 600;">○ Đóng</span>`;

            const dirUrl = originLat && originLng
                ? `https://www.google.com/maps/dir/?api=1&origin=${originLat},${originLng}&destination=${q.latitude},${q.longitude}`
                : `https://www.google.com/maps/dir/?api=1&destination=${q.latitude},${q.longitude}`;

            html += `
                <article class="bd-card-item ${isSelected ? 'is-selected' : ''}" 
                         id="bd-card-${q.id}" 
                         onclick="focusVenueOnMap('${q.id}')"
                         onmouseenter="hoverVenueMarker('${q.id}', true)"
                         onmouseleave="hoverVenueMarker('${q.id}', false)">
                    
                    <div class="bd-card-thumb-wrap">
                        <img src="${q.anh_dai_dien || 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=300&q=80'}" 
                             class="bd-card-thumb-img" 
                             alt="${escapeHtml(q.ten_quan)}" 
                             loading="lazy" />
                        <span class="bd-card-rank-badge">#${idx + 1}</span>
                    </div>

                    <div class="bd-card-content">
                        <div>
                            <div class="bd-card-name-row">
                                <h3 class="bd-card-title">${escapeHtml(q.ten_quan)}</h3>
                                ${q.is_xac_thuc ? '<span class="material-symbols-outlined text-tick-xanh" style="font-size: 15px; font-variation-settings: \'FILL\' 1; flex-shrink: 0;" title="Đã xác thực">verified</span>' : ''}
                            </div>
                            
                            <div class="bd-card-meta-row">
                                <span>${escapeHtml(q.loai_hinh_kinh_doanh || 'Quán')}</span>
                                <span>•</span>
                                ${ratingHtml}
                                <span>•</span>
                                ${statusDot}
                            </div>

                            <p class="bd-card-address">${escapeHtml(q.dia_chi || 'Đang cập nhật địa chỉ')}</p>
                        </div>

                        <div class="bd-card-footer-row">
                            <span class="bd-card-distance-pill">
                                <span class="material-symbols-outlined" style="font-size: 12px;">near_me</span>
                                ${q.khoang_cach_km} km
                            </span>

                            <span class="bd-card-price-tag">${priceText}</span>

                            <div class="bd-card-actions">
                                <a href="${dirUrl}" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()" class="bd-card-link-dir" title="Chỉ đường trên Google Maps">
                                    <span class="material-symbols-outlined" style="font-size: 13px;">directions</span>
                                </a>
                                <a href="${q.url}" onclick="event.stopPropagation()" class="bd-card-link-detail">
                                    Xem ➔
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            `;
        });

        if (state.currentPage < state.lastPage) {
            const remaining = Math.max(0, state.totalMatched - state.rawQuans.length);
            html += `
                <button type="button" class="bd-load-more-btn" onclick="loadMoreVenues()">
                    <span class="material-symbols-outlined" style="font-size: 17px;">expand_more</span>
                    <span>Xem thêm quán ${remaining > 0 ? `(còn ${remaining} quán)` : ''}</span>
                </button>
            `;
        }

        container.innerHTML = html;
    }

    // Chọn quán và điều hướng bản đồ
    window.focusVenueOnMap = function(venueId) {
        selectVenue(venueId, true);
    };

    function selectVenue(venueId, flyMap = true) {
        state.selectedVenueId = venueId;
        const venue = state.rawQuans.find(q => q.id === venueId);
        if (!venue) return;

        document.querySelectorAll('.bd-card-item').forEach(c => c.classList.remove('is-selected'));
        const card = document.getElementById(`bd-card-${venueId}`);
        if (card) {
            card.classList.add('is-selected');
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        if (flyMap && state.map && venue.latitude && venue.longitude) {
            if (venue._marker && state.markersLayer && typeof state.markersLayer.zoomToShowLayer === 'function') {
                state.markersLayer.zoomToShowLayer(venue._marker, () => {
                    venue._marker.openPopup();
                    document.querySelectorAll('.qm-venue-marker-badge').forEach(m => m.classList.remove('is-active'));
                    const markerElem = document.getElementById(`bd-marker-${venueId}`);
                    if (markerElem) markerElem.classList.add('is-active');
                });
            } else {
                state.map.flyTo([venue.latitude, venue.longitude], 16, { duration: 0.9 });
                if (venue._marker) {
                    setTimeout(() => {
                        venue._marker.openPopup();
                        document.querySelectorAll('.qm-venue-marker-badge').forEach(m => m.classList.remove('is-active'));
                        const markerElem = document.getElementById(`bd-marker-${venueId}`);
                        if (markerElem) markerElem.classList.add('is-active');
                    }, 550);
                }
            }
        } else {
            document.querySelectorAll('.qm-venue-marker-badge').forEach(m => m.classList.remove('is-active'));
            const markerElem = document.getElementById(`bd-marker-${venueId}`);
            if (markerElem) markerElem.classList.add('is-active');
        }

        if (window.innerWidth <= 768 && state.isMobileListOpen) {
            toggleBandoMobileView();
        }
    }

    // Hover hiệu ứng marker khi di chuột qua card
    window.hoverVenueMarker = function(venueId, isHover) {
        const markerElem = document.getElementById(`bd-marker-${venueId}`);
        if (markerElem && state.selectedVenueId !== venueId) {
            if (isHover) {
                markerElem.classList.add('is-active');
            } else {
                markerElem.classList.remove('is-active');
            }
        }
    };

    // Thiết lập tìm kiếm địa chỉ + tên quán / món ăn thông minh (dùng Event Delegation an toàn chống lỗi nháy đơn/XSS)
    function setupAddressSearch() {
        const input = document.getElementById('bd-address-search-input');
        const clearBtn = document.getElementById('bd-address-clear-btn');
        const suggestionsBox = document.getElementById('bd-address-suggestions');
        if (!input) return;

        if (suggestionsBox) {
            suggestionsBox.addEventListener('click', function(e) {
                const itemEl = e.target.closest('.bd-suggestion-item');
                if (!itemEl) return;

                const action = itemEl.getAttribute('data-action');
                if (action === 'filter-keyword') {
                    const kw = itemEl.getAttribute('data-keyword') || '';
                    applyKeywordSearch(kw);
                    suggestionsBox.style.display = 'none';
                    return;
                }

                const lat = parseFloat(itemEl.getAttribute('data-lat'));
                const lng = parseFloat(itemEl.getAttribute('data-lng'));
                const label = itemEl.getAttribute('data-label') || '';
                const venueId = itemEl.getAttribute('data-venue-id') || '';

                if (Number.isFinite(lat) && Number.isFinite(lng)) {
                    selectSearchedLocation(lat, lng, label, venueId);
                }
            });
        }

        input.addEventListener('input', function() {
            const query = input.value.trim();
            if (clearBtn) clearBtn.style.display = query.length > 0 ? 'block' : 'none';

            clearTimeout(state.searchDebounceTimer);
            if (query.length < 2) {
                if (suggestionsBox) suggestionsBox.style.display = 'none';
                return;
            }

            state.searchDebounceTimer = setTimeout(() => {
                fetchAddressGeocode(query, false);
            }, 350);
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(state.searchDebounceTimer);
                const query = input.value.trim();
                if (query.length >= 2) {
                    fetchAddressGeocode(query, true);
                } else if (query.length === 0 && state.searchKeyword) {
                    clearKeywordFilter();
                }
            }
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.bd-search-box-wrap') && suggestionsBox) {
                suggestionsBox.style.display = 'none';
            }
        });
    }

    function applyKeywordSearch(keyword) {
        state.searchKeyword = keyword.trim();
        const bar = document.getElementById('bd-active-keyword-bar');
        const textEl = document.getElementById('bd-active-keyword-text');
        if (bar && textEl) {
            if (state.searchKeyword) {
                textEl.textContent = state.searchKeyword;
                bar.style.display = 'flex';
            } else {
                bar.style.display = 'none';
            }
        }
        fetchNearbyVenues(state.userLat, state.userLng, state.currentRadius, state.currentCategory, 1, false);
    }

    window.clearKeywordFilter = function() {
        state.searchKeyword = '';
        const bar = document.getElementById('bd-active-keyword-bar');
        if (bar) bar.style.display = 'none';
        fetchNearbyVenues(state.userLat, state.userLng, state.currentRadius, state.currentCategory, 1, false);
    };

    async function fetchAddressGeocode(query, autoSelectFirst = false) {
        const suggestionsBox = document.getElementById('bd-address-suggestions');
        if (state.geocodeAbortController) {
            state.geocodeAbortController.abort();
        }
        const abortCtrl = new AbortController();
        state.geocodeAbortController = abortCtrl;

        try {
            const res = await fetch(`/api/ban-do/geocode?address=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' },
                signal: abortCtrl.signal
            });
            const json = await res.json();

            const venues = Array.isArray(json.venues) ? json.venues : [];
            const suggestions = Array.isArray(json.suggestions) ? json.suggestions : [];

            if (autoSelectFirst) {
                if (venues.length > 0) {
                    selectSearchedLocation(venues[0].lat, venues[0].lng, venues[0].ten_quan, venues[0].id);
                } else if (json.success && Number.isFinite(parseFloat(json.lat)) && Number.isFinite(parseFloat(json.lng))) {
                    selectSearchedLocation(parseFloat(json.lat), parseFloat(json.lng), json.display_name || query);
                } else {
                    applyKeywordSearch(query);
                }
                if (suggestionsBox) suggestionsBox.style.display = 'none';
                return;
            }

            if (!suggestionsBox) return;

            let html = `
                <div class="bd-suggestion-item" data-action="filter-keyword" data-keyword="${escapeHtml(query)}">
                    <span class="material-symbols-outlined" style="font-size: 16px; color: #ea580c;">manage_search</span>
                    <div style="min-width: 0; flex: 1;">
                        <div style="font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Lọc quán có "${escapeHtml(query)}" ở vùng này</div>
                        <div class="bd-suggestion-sub">Tìm theo tên quán hoặc món ăn trong bán kính ${state.currentRadius} km</div>
                    </div>
                </div>
            `;

            if (venues.length > 0) {
                html += `<div class="bd-suggestion-group-header">Quán ăn trong hệ thống</div>`;
                venues.forEach(v => {
                    html += `
                        <div class="bd-suggestion-item" 
                             data-lat="${v.lat}" 
                             data-lng="${v.lng}" 
                             data-label="${escapeHtml(v.ten_quan)}"
                             data-venue-id="${escapeHtml(v.id)}">
                            <span class="material-symbols-outlined" style="font-size: 16px; color: #ea580c; flex-shrink: 0;">storefront</span>
                            <div style="min-width: 0; flex: 1;">
                                <div style="font-weight: 700; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(v.ten_quan)}</div>
                                <div class="bd-suggestion-sub">${escapeHtml(v.dia_chi || v.loai_hinh_kinh_doanh)}</div>
                            </div>
                        </div>
                    `;
                });
            }

            if (suggestions.length > 0) {
                html += `<div class="bd-suggestion-group-header">Địa điểm & Khu vực</div>`;
                suggestions.forEach(item => {
                    html += `
                        <div class="bd-suggestion-item" 
                             data-lat="${item.lat}" 
                             data-lng="${item.lng}" 
                             data-label="${escapeHtml(item.display_name)}">
                            <span class="material-symbols-outlined" style="font-size: 16px; color: #2563eb; flex-shrink: 0;">location_on</span>
                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(item.display_name)}</span>
                        </div>
                    `;
                });
            }

            suggestionsBox.innerHTML = html;
            suggestionsBox.style.display = 'block';
        } catch (e) {
            if (e && e.name === 'AbortError') return;
            console.warn('Geocode error:', e);
        }
    }

    window.selectSearchedLocation = function(lat, lng, label, venueId = '') {
        const input = document.getElementById('bd-address-search-input');
        if (input && label) input.value = label;
        const suggestionsBox = document.getElementById('bd-address-suggestions');
        if (suggestionsBox) suggestionsBox.style.display = 'none';

        if (venueId) {
            state.selectedVenueId = venueId;
        }

        updateUserPosition(lat, lng, true);
        fetchNearbyVenues(lat, lng, state.currentRadius, state.currentCategory, 1, false).then(() => {
            if (venueId) {
                selectVenue(venueId, true);
            }
        });
    };

    window.clearAddressSearch = function() {
        const input = document.getElementById('bd-address-search-input');
        if (input) input.value = '';
        const clearBtn = document.getElementById('bd-address-clear-btn');
        if (clearBtn) clearBtn.style.display = 'none';
        const suggestionsBox = document.getElementById('bd-address-suggestions');
        if (suggestionsBox) suggestionsBox.style.display = 'none';
        if (state.searchKeyword) {
            clearKeywordFilter();
        }
    };

    // Xử lý nút quét GPS thực tế của thiết bị
    window.handleBandoScan = function() {
        const statusText = document.getElementById('bando-status-text');
        if (!navigator.geolocation) {
            if (statusText) statusText.textContent = 'Trình duyệt không hỗ trợ GPS';
            return;
        }

        updateScanButtonState(true);
        if (statusText) statusText.textContent = 'Đang xin quyền định vị GPS...';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                state.gpsLat = lat;
                state.gpsLng = lng;
                updateUserPosition(lat, lng, true);
                fetchNearbyVenues(lat, lng, state.currentRadius, state.currentCategory, 1, false);
            },
            (err) => {
                console.warn('Lỗi định vị:', err);
                updateScanButtonState(false);
                let msg = 'Không lấy được GPS. Nhấp lên bản đồ hoặc chọn Điểm nóng.';
                if (err.code === 1) msg = 'Đã từ chối GPS — Hãy nhấp bản đồ hoặc chọn Điểm nóng.';
                if (statusText) statusText.textContent = msg;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 }
        );
    };

    // Chọn nhanh điểm nóng
    window.scanBandoPreset = function(lat, lng, label) {
        updateUserPosition(lat, lng, true);
        fetchNearbyVenues(lat, lng, state.currentRadius, state.currentCategory, 1, false);
    };

    // Đổi bán kính
    window.changeBandoRadius = function(newRadius) {
        state.currentRadius = newRadius;
        document.querySelectorAll('.bando-radius-btn').forEach(btn => {
            const r = parseInt(btn.dataset.radius);
            if (r === newRadius) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        if (state.userLat && state.userLng) {
            updateUserPosition(state.userLat, state.userLng, true);
            fetchNearbyVenues(state.userLat, state.userLng, state.currentRadius, state.currentCategory, 1, false);
        }
    };

    // Lọc danh mục ẩm thực
    window.filterBandoCategory = function(catKey) {
        state.currentCategory = catKey;
        document.querySelectorAll('.bando-cat-btn').forEach(btn => {
            if (btn.dataset.category === catKey) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        if (state.userLat && state.userLng) {
            fetchNearbyVenues(state.userLat, state.userLng, state.currentRadius, state.currentCategory, 1, false);
        }
    };

    // Toggle lọc đang mở cửa
    window.toggleOpenNowFilter = function() {
        state.filterOpenOnly = !state.filterOpenOnly;
        const btn = document.getElementById('bd-filter-open-now-btn');
        if (btn) {
            if (state.filterOpenOnly) btn.classList.add('active');
            else btn.classList.remove('active');
        }
        applyLocalFiltersAndSort();
    };

    // Đổi thứ tự sắp xếp
    window.changeSortOrder = function(order) {
        state.currentSort = order;
        applyLocalFiltersAndSort();
    };

    // Quay lại đúng vị trí GPS thật của thiết bị (nếu đã lấy GPS), hoặc xin lại GPS
    window.recenterBandoToUser = function() {
        if (state.gpsLat && state.gpsLng && state.map) {
            updateUserPosition(state.gpsLat, state.gpsLng, false);
            state.map.flyTo([state.gpsLat, state.gpsLng], getZoomForRadius(state.currentRadius), { duration: 1.0 });
            fetchNearbyVenues(state.gpsLat, state.gpsLng, state.currentRadius, state.currentCategory, 1, false);
        } else {
            handleBandoScan();
        }
    };

    // Quét tại tâm bản đồ đang xem
    window.scanAtCurrentMapCenter = function() {
        if (!state.map) return;
        const center = state.map.getCenter();
        hideSearchAreaPill();
        updateUserPosition(center.lat, center.lng, false);
        fetchNearbyVenues(center.lat, center.lng, state.currentRadius, state.currentCategory, 1, false);
    };

    function hideSearchAreaPill() {
        const pill = document.getElementById('bd-search-area-pill');
        if (pill) pill.style.display = 'none';
    }

    // Chuyển đổi hiển thị trên mobile
    window.toggleBandoMobileView = function() {
        const sidebar = document.getElementById('bando-sidebar');
        const toggleIcon = document.getElementById('bando-mobile-toggle-icon');
        const toggleText = document.getElementById('bando-mobile-toggle-text');
        const total = state.filteredQuans.length;

        state.isMobileListOpen = !state.isMobileListOpen;
        if (state.isMobileListOpen) {
            sidebar.classList.add('mobile-open');
            if (toggleIcon) toggleIcon.textContent = 'map';
            if (toggleText) toggleText.innerHTML = 'Xem bản đồ';
        } else {
            sidebar.classList.remove('mobile-open');
            if (toggleIcon) toggleIcon.textContent = 'format_list_bulleted';
            if (toggleText) toggleText.innerHTML = `Xem danh sách (<span id="bando-mobile-count">${total}</span>)`;
        }
    };

    function updateScanButtonState(loading) {
        const btnText = document.getElementById('bando-btn-scan-text');
        const btnIcon = document.getElementById('bando-btn-scan-icon');
        if (btnText) btnText.textContent = loading ? 'Đang quét...' : 'Định vị & Quét quán gần tôi';
        if (btnIcon) {
            if (loading) btnIcon.classList.add('animate-spin');
            else btnIcon.classList.remove('animate-spin');
        }
    }

    function calculateDistanceKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function formatPrice(val) {
        if (!val || isNaN(val)) return '';
        if (val >= 1000) {
            return (val / 1000).toLocaleString('vi-VN') + 'k';
        }
        return val.toLocaleString('vi-VN') + 'đ';
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Kéo cuộn chuột ngang như trên mobile (Drag to scroll) + lăn con lăn chuột (wheel)
    function initHorizontalDragScroll(container) {
        if (!container) return;

        let isDown = false;
        let isDragging = false;
        let startX = 0;
        let scrollStart = 0;
        let lastX = 0;
        let lastTime = 0;
        let velocity = 0;

        // Chống kéo ghost image mặc định của trình duyệt
        container.addEventListener('dragstart', function(e) {
            e.preventDefault();
        });

        container.querySelectorAll('button, a, span, img').forEach(function(el) {
            el.setAttribute('draggable', 'false');
        });

        // Nhấn chuột xuống (mousedown)
        container.addEventListener('mousedown', function(e) {
            if (e.button !== 0) return; // Chỉ nhận click chuột trái
            isDown = true;
            isDragging = false;
            startX = e.pageX;
            scrollStart = container.scrollLeft;
            lastX = e.pageX;
            lastTime = performance.now();
            velocity = 0;
            container.classList.add('is-dragging');
        });

        // Di chuyển chuột (mousemove) trên window để mượt mà kể cả khi trỏ chuột ra ngoài thanh
        window.addEventListener('mousemove', function(e) {
            if (!isDown) return;
            const dx = e.pageX - startX;
            if (Math.abs(dx) > 4) {
                isDragging = true;
            }

            container.scrollLeft = scrollStart - dx;

            const now = performance.now();
            const dt = now - lastTime;
            if (dt > 8) {
                velocity = (e.pageX - lastX) / dt;
                lastX = e.pageX;
                lastTime = now;
            }
        });

        // Nhả chuột (mouseup)
        window.addEventListener('mouseup', function(e) {
            if (!isDown) return;
            isDown = false;
            container.classList.remove('is-dragging');

            // Quán tính lướt (momentum glide) mượt mà như trên mobile
            if (isDragging && Math.abs(velocity) > 0.15) {
                const glideDistance = -velocity * 160;
                container.scrollBy({ left: glideDistance, behavior: 'smooth' });
            }

            // Chặn kích hoạt click thẻ <button> nếu người dùng vừa thực hiện kéo chuột
            if (isDragging) {
                const blockClick = function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    window.removeEventListener('click', blockClick, true);
                };
                window.addEventListener('click', blockClick, true);
                setTimeout(function() {
                    window.removeEventListener('click', blockClick, true);
                }, 120);
            }

            isDragging = false;
        });

        // Khi cửa sổ mất tiêu điểm trong lúc đang giữ chuột
        window.addEventListener('blur', function() {
            if (isDown) {
                isDown = false;
                isDragging = false;
                container.classList.remove('is-dragging');
            }
        });

        // Lăn con lăn chuột dọc -> tự động cuộn ngang mượt mà
        container.addEventListener('wheel', function(e) {
            if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                if (container.scrollWidth > container.clientWidth) {
                    e.preventDefault();
                    container.scrollLeft += e.deltaY;
                }
            }
        }, { passive: false });
    }

    // Khởi chạy khi DOM sẵn sàng
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }
})();
</script>
@endpush
