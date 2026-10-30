<!DOCTYPE html>
<html lang="vi" class="bg-slate-50 min-h-screen scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hệ Thống Quản Trị - Quán Mới Doanh Nghiệp')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .material-symbols-outlined.fill-1,
        .material-symbols-outlined.fill-icon,
        .material-symbols-outlined.material-symbols-filled,
        .material-symbols-filled { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }

        /* ==========================================================================
           ENTERPRISE SEARCH BOX & ICON ALIGNMENT STANDARD (ANTI-CONFLICT)
           Đảm bảo 100% icon tìm kiếm luôn nằm chính giữa hàng dọc, không bị lệch, 
           không bị đè lên text, không bị văng lên viền.
           ========================================================================== */
        .admin-search-wrapper {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.75rem !important;
            padding: 0.375rem 0.625rem !important;
            transition: all 0.15s ease-in-out !important;
            min-height: 2.125rem !important;
            box-sizing: border-box !important;
        }
        .admin-search-wrapper:focus-within {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.18) !important;
            background-color: #ffffff !important;
        }
        .admin-search-wrapper .material-symbols-outlined {
            font-size: 17px !important;
            line-height: 1 !important;
            color: #94a3b8 !important;
            flex-shrink: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            user-select: none !important;
            pointer-events: none !important;
        }
        .admin-search-wrapper input {
            width: 100% !important;
            background: transparent !important;
            border: 0 !important;
            padding: 0 !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            line-height: 1.25rem !important;
            color: #0f172a !important;
            outline: none !important;
            box-shadow: none !important;
        }
        .admin-search-wrapper input::placeholder {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }

        /* Fix triệt để mọi icon tìm kiếm có class search-icon-centered */
        .search-icon-centered {
            position: absolute !important;
            left: 0.625rem !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            line-height: 1 !important;
            pointer-events: none !important;
        }


        @media (min-width: 768px) {
            #admin-sidebar {
                display: flex !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 216px !important;
                height: 100vh !important;
                z-index: 40 !important;
            }
            .admin-main-wrapper {
                margin-left: 216px !important;
                width: calc(100% - 216px) !important;
                min-width: 0 !important;
            }
        }

        /* ==========================================================================
           ENTERPRISE DESIGN SYSTEM: BUTTONS & CONTROLS
           Chuẩn hóa doanh nghiệp B2B: Tương phản cao, chữ sắc nét, không bị chìm màu
           ========================================================================== */
        .btn-enterprise {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.375rem !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            line-height: 1rem !important;
            padding: 0.4375rem 0.875rem !important;
            border-radius: 0.5rem !important;
            white-space: nowrap !important;
            cursor: pointer !important;
            user-select: none !important;
            text-decoration: none !important;
            border: 1px solid transparent !important;
            transition: all 0.15s ease-in-out !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            flex-shrink: 0 !important;
        }
        .btn-enterprise:focus-visible {
            outline: 2px solid #0284c7 !important;
            outline-offset: 2px !important;
        }
        .btn-enterprise:disabled,
        .btn-enterprise[disabled] {
            opacity: 0.55 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            box-shadow: none !important;
            transform: none !important;
        }
        .btn-enterprise .material-symbols-outlined {
            font-size: 16px !important;
            line-height: 1 !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        /* 1. SUCCESS: Phê duyệt / Kích hoạt / Duyệt lại */
        .btn-enterprise-success {
            background-color: #15803d !important;
            border-color: #166534 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-success:hover {
            background-color: #166534 !important;
            border-color: #14532d !important;
            color: #ffffff !important;
        }
        .btn-enterprise-success:active {
            background-color: #14532d !important;
            border-color: #052e16 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-success .material-symbols-outlined {
            color: #ffffff !important;
        }

        /* 2. DANGER: Không duyệt / Từ chối / Xóa */
        .btn-enterprise-danger {
            background-color: #b91c1c !important;
            border-color: #991b1b !important;
            color: #ffffff !important;
        }
        .btn-enterprise-danger:hover {
            background-color: #991b1b !important;
            border-color: #7f1d1d !important;
            color: #ffffff !important;
        }
        .btn-enterprise-danger:active {
            background-color: #7f1d1d !important;
            border-color: #450a0a !important;
            color: #ffffff !important;
        }
        .btn-enterprise-danger .material-symbols-outlined {
            color: #ffffff !important;
        }

        /* 2b. SOFT DANGER: Không duyệt nhẹ nhàng */
        .btn-enterprise-soft-danger {
            background-color: #ffffff !important;
            border-color: #fca5a5 !important;
            color: #b91c1c !important;
        }
        .btn-enterprise-soft-danger:hover {
            background-color: #fef2f2 !important;
            border-color: #f87171 !important;
            color: #991b1b !important;
        }
        .btn-enterprise-soft-danger:active {
            background-color: #fee2e2 !important;
            border-color: #ef4444 !important;
            color: #7f1d1d !important;
        }
        .btn-enterprise-soft-danger .material-symbols-outlined {
            color: #b91c1c !important;
        }

        /* 3. SECONDARY: Nút phụ / Đóng / Quay lại */
        .btn-enterprise-secondary {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
        }
        .btn-enterprise-secondary:hover {
            background-color: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
        .btn-enterprise-secondary:active {
            background-color: #f1f5f9 !important;
            border-color: #64748b !important;
            color: #0f172a !important;
        }
        .btn-enterprise-secondary .material-symbols-outlined {
            color: #475569 !important;
        }
        .btn-enterprise-secondary:hover .material-symbols-outlined {
            color: #0f172a !important;
        }

        /* 4. PHOTO: Xem ảnh quán (tương phản cao) */
        .btn-enterprise-photo {
            background-color: #fffbeb !important;
            border-color: #fcd34d !important;
            color: #92400e !important;
        }
        .btn-enterprise-photo:hover {
            background-color: #fef3c7 !important;
            border-color: #f59e0b !important;
            color: #78350f !important;
        }
        .btn-enterprise-photo:active {
            background-color: #fde68a !important;
            border-color: #d97706 !important;
            color: #78350f !important;
        }
        .btn-enterprise-photo .material-symbols-outlined {
            color: #b45309 !important;
        }
        .btn-enterprise-photo:hover .material-symbols-outlined {
            color: #78350f !important;
        }

        /* 5. DARK: Trang quán / Xem ngoài web */
        .btn-enterprise-dark {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
            color: #ffffff !important;
        }
        .btn-enterprise-dark:hover {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-dark:active {
            background-color: #020617 !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }
        .btn-enterprise-dark .material-symbols-outlined {
            color: #ffffff !important;
        }

        /* 6. INDIGO: Xác thực tài khoản */
        .btn-enterprise-indigo {
            background-color: #4338ca !important;
            border-color: #3730a3 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-indigo:hover {
            background-color: #3730a3 !important;
            border-color: #312e81 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-indigo:active {
            background-color: #312e81 !important;
            border-color: #1e1b4b !important;
            color: #ffffff !important;
        }
        .btn-enterprise-indigo .material-symbols-outlined {
            color: #ffffff !important;
        }

        /* 7. PRIMARY / BRAND: Amber-600 */
        .btn-enterprise-primary {
            background-color: #d97706 !important;
            border-color: #b45309 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-primary:hover {
            background-color: #b45309 !important;
            border-color: #92400e !important;
            color: #ffffff !important;
        }
        .btn-enterprise-primary:active {
            background-color: #92400e !important;
            border-color: #78350f !important;
            color: #ffffff !important;
        }
        .btn-enterprise-primary .material-symbols-outlined {
            color: #ffffff !important;
        }

        /* 7b. WARNING: Amber-500/600 */
        .btn-enterprise-warning {
            background-color: #f59e0b !important;
            border-color: #d97706 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-warning:hover {
            background-color: #d97706 !important;
            border-color: #b45309 !important;
            color: #ffffff !important;
        }
        .btn-enterprise-warning:active {
            background-color: #b45309 !important;
            border-color: #92400e !important;
            color: #ffffff !important;
        }
        .btn-enterprise-warning .material-symbols-outlined {
            color: #ffffff !important;
        }

        /* ==========================================================================
           ENTERPRISE MODAL CONFIRMATION CONTROLS
           Đảm bảo 100% hiển thị rõ ràng, tương phản cao, không bao giờ mất nút
           ========================================================================== */
        .modal-btn-cancel {
            width: 50% !important;
            padding: 0.625rem 1rem !important;
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            border-radius: 0.75rem !important;
            background-color: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.375rem !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            user-select: none !important;
            text-decoration: none !important;
        }
        .modal-btn-cancel:hover {
            background-color: #e2e8f0 !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
            transform: scale(1.02) !important;
        }
        .modal-btn-cancel:active {
            transform: scale(0.97) !important;
        }
        .modal-btn-cancel .material-symbols-outlined {
            font-size: 16px !important;
            color: #475569 !important;
        }

        .modal-btn-action {
            width: 50% !important;
            padding: 0.625rem 1rem !important;
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            border-radius: 0.75rem !important;
            border: 1px solid transparent !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.375rem !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1) !important;
            user-select: none !important;
            text-decoration: none !important;
            color: #ffffff !important;
        }
        .modal-btn-action:hover {
            transform: scale(1.02) !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
            color: #ffffff !important;
        }
        .modal-btn-action:active {
            transform: scale(0.97) !important;
        }
        .modal-btn-action .material-symbols-outlined {
            font-size: 16px !important;
            color: #ffffff !important;
        }

        /* Success / Restore / Duyệt (Emerald Green) */
        .modal-btn-success {
            background-color: #059669 !important;
            background-image: linear-gradient(135deg, #10b981, #059669) !important;
            border-color: #047857 !important;
            color: #ffffff !important;
        }
        .modal-btn-success:hover {
            background-color: #047857 !important;
            background-image: linear-gradient(135deg, #059669, #047857) !important;
            color: #ffffff !important;
        }

        /* Danger / Delete / Xóa vĩnh viễn (Rose Red) */
        .modal-btn-danger {
            background-color: #e11d48 !important;
            background-image: linear-gradient(135deg, #f43f5e, #e11d48) !important;
            border-color: #be123c !important;
            color: #ffffff !important;
        }
        .modal-btn-danger:hover {
            background-color: #be123c !important;
            background-image: linear-gradient(135deg, #e11d48, #be123c) !important;
            color: #ffffff !important;
        }

        /* Warning / Nổi bật / Cảnh báo (Amber Orange) */
        .modal-btn-warning {
            background-color: #d97706 !important;
            background-image: linear-gradient(135deg, #f59e0b, #d97706) !important;
            border-color: #b45309 !important;
            color: #ffffff !important;
        }
        .modal-btn-warning:hover {
            background-color: #b45309 !important;
            background-image: linear-gradient(135deg, #d97706, #b45309) !important;
            color: #ffffff !important;
        }

        /* Primary / Blue (Xác thực, Tick xanh, Chi tiết) */
        .modal-btn-primary {
            background-color: #2563eb !important;
            background-image: linear-gradient(135deg, #3b82f6, #2563eb) !important;
            border-color: #1d4ed8 !important;
            color: #ffffff !important;
        }
        .modal-btn-primary:hover {
            background-color: #1d4ed8 !important;
            background-image: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
            color: #ffffff !important;
        }

        /* Dark / Slate (Gỡ nổi bật, Ẩn nội dung, Thao tác quản trị cấp cao) */
        .modal-btn-dark {
            background-color: #1e293b !important;
            background-image: linear-gradient(135deg, #334155, #1e293b) !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }
        .modal-btn-dark:hover {
            background-color: #0f172a !important;
            background-image: linear-gradient(135deg, #1e293b, #0f172a) !important;
            color: #ffffff !important;
        }

        /* Button Glow & Secondary Utilities */
        .btn-action-primary-glow {
            background-color: #d97706 !important;
            background-image: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: #ffffff !important;
            border: 1px solid #b45309 !important;
            box-shadow: 0 4px 10px -1px rgba(217, 119, 6, 0.35) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.375rem !important;
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            padding: 0.4375rem 0.875rem !important;
            border-radius: 0.75rem !important;
            transition: all 0.15s ease !important;
            cursor: pointer !important;
            text-decoration: none !important;
            white-space: nowrap !important;
        }
        .btn-action-primary-glow:hover {
            background-color: #b45309 !important;
            background-image: linear-gradient(135deg, #d97706, #b45309) !important;
            transform: scale(1.02) !important;
            box-shadow: 0 6px 14px -2px rgba(217, 119, 6, 0.45) !important;
            color: #ffffff !important;
        }
        .btn-action-primary-glow .material-symbols-outlined {
            color: #ffffff !important;
        }

        .btn-action-secondary {
            background-color: #f1f5f9 !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #334155 !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            padding: 0.4375rem 0.75rem !important;
            border-radius: 0.75rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.25rem !important;
            transition: all 0.15s ease !important;
            cursor: pointer !important;
            text-decoration: none !important;
            white-space: nowrap !important;
        }
        .btn-action-secondary:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
            transform: scale(1.02) !important;
        }

        /* 8. TABLE CELL RESPONSIVE WRAPPING & ANTI-OVERFLOW */
        .duyet-table-row td {
            vertical-align: middle;
        }
        .duyet-table-row td .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }

        /* Classes to allow text to wrap down (xuống hàng nội dung) when zooming in */
        .cell-wrap-2line {
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            line-height: 1.35 !important;
        }
        .cell-wrap-1line {
            display: -webkit-box !important;
            -webkit-line-clamp: 1 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            line-height: 1.35 !important;
        }
        .cell-wrap-free {
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
        }

        /* 9. TABLE FIXED & RESPONSIVE SCROLL SYSTEM */
        html, body {
            max-width: 100vw;
        }
        #admin-main-content {
            width: 100%;
            min-width: 0;
        }
        .table-fixed-enterprise {
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse;
        }
        .table-fixed-enterprise th,
        .table-fixed-enterprise td {
            vertical-align: middle;
        }
        .table-responsive-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .btn-action-compact {
            padding: 0.2rem 0.35rem !important;
            font-size: 0.7rem !important;
            line-height: 0.95rem !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.2rem !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            border-radius: 0.375rem !important;
        }
        .btn-action-compact .material-symbols-outlined {
            font-size: 13px !important;
        }

        /* ==========================================================================
           10. CHỐNG XUỐNG HÀNG & CHỐNG MẤT CHỮ TOÀN DIỆN CHO ADMIN
           Đảm bảo 100% tất cả các nút bấm, badge, tiêu đề cột, ngày giờ không bao giờ bị cắt chữ hay gãy dòng
           ========================================================================== */
        button,
        a.btn,
        .btn,
        .btn-enterprise,
        .btn-action-compact,
        a[class*="rounded-xl"],
        button[class*="rounded-xl"],
        a[class*="rounded-lg"],
        button[class*="rounded-lg"] {
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }

        /* Tất cả badge trạng thái không bao giờ rớt dòng */
        [class*="rounded-full"],
        .badge {
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }

        /* Tiêu đề bảng không bao giờ bị xuống hàng */
        thead th {
            white-space: nowrap !important;
        }

        /* Cột thao tác trong bảng luôn giữ nguyên trên 1 dòng */
        td:last-child,
        td.text-right {
            white-space: nowrap !important;
        }

        /* Cột ngày giờ không bị cắt cụt dấu ... */
        .table-date-cell,
        td.whitespace-nowrap div {
            white-space: nowrap;
        }
    </style>
</head>
<body class="text-slate-800 bg-[#f8fafc] font-sans antialiased flex flex-col md:flex-row min-h-screen overflow-x-hidden">

    {{-- Instant Loading Top Progress Bar --}}
    <div id="admin-top-progress-bar" class="fixed top-0 left-0 h-[3px] bg-amber-500 z-[9999] transition-all duration-150 pointer-events-none opacity-0 w-0 shadow-xs"></div>

    {{-- Backdrop Mobile --}}
    <div id="mobile-sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-40 hidden md:hidden transition-opacity"></div>

    {{-- Mobile Sidebar Drawer --}}
    <aside id="admin-sidebar-mobile" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex md:hidden flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out border-r border-slate-800 shadow-2xl">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800 bg-slate-950/60">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 shrink-0 group">
                    <div class="w-8 h-8 rounded-lg bg-[#F59E0B] flex items-center justify-center text-white shadow-sm font-bold shrink-0">
                        <span class="material-symbols-outlined text-[20px]">space_dashboard</span>
                    </div>
                    <div class="min-w-0">
                        <h1 class="font-bold text-sm text-white tracking-tight leading-none truncate">Quán Mới Quản Trị</h1>
                        <span class="text-[10px] text-slate-400 font-medium truncate block mt-0.5">Trang quản trị hệ thống</span>
                    </div>
                </a>
                <button onclick="toggleSidebar()" class="text-slate-400 hover:text-white p-1 rounded-md">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5 custom-scrollbar">
                @include('admin.partials.sidebar-nav')
            </nav>

            <div class="p-3.5 border-t border-slate-800 bg-slate-950/40 flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-md bg-indigo-600 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->ho_ten ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-white truncate leading-tight">{{ auth()->user()->ho_ten ?? 'Quản Trị Viên' }}</p>
                        <p class="text-[10px] text-slate-400 truncate leading-tight">Quản Trị Cao Cấp</p>
                    </div>
                </div>
                <form id="mobile-logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="button" onclick="confirmAdminLogout('mobile-logout-form')" title="Đăng xuất" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-md transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Desktop Sidebar --}}
    <aside id="admin-sidebar" class="hidden md:flex flex-col justify-between fixed top-0 left-0 z-40 w-[216px] h-screen bg-slate-900 text-slate-300 border-r border-slate-800/80 flex-shrink-0">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between px-3.5 py-3.5 border-b border-slate-800/80 bg-slate-950/60">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 shrink-0 group">
                    <div class="w-8 h-8 rounded-lg bg-[#F59E0B] flex items-center justify-center text-white shadow-sm font-bold shrink-0">
                        <span class="material-symbols-outlined text-[20px]">space_dashboard</span>
                    </div>
                    <div class="min-w-0">
                        <h1 class="font-bold text-xs text-white tracking-tight leading-none truncate">Quán Mới Quản Trị</h1>
                        <span class="text-[9px] text-slate-400 font-medium truncate block mt-0.5">Trang quản trị hệ thống</span>
                    </div>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-2.5 py-3 space-y-4 custom-scrollbar">
                @include('admin.partials.sidebar-nav')
            </nav>

            <div class="p-3 border-t border-slate-800 bg-slate-950/40 flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 rounded-md bg-indigo-600 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->ho_ten ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-white truncate leading-tight">{{ auth()->user()->ho_ten ?? 'Quản Trị Viên' }}</p>
                        <p class="text-[10px] text-slate-400 truncate leading-tight">Quản Trị Cấp Cao</p>
                    </div>
                </div>
                <form id="desktop-logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="button" onclick="confirmAdminLogout('desktop-logout-form')" title="Đăng xuất" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-md transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content Wrapper --}}
    <div class="admin-main-wrapper flex-1 flex flex-col min-w-0 min-h-screen bg-[#f8fafc] md:ml-[216px] w-full md:w-[calc(100%-216px)]">
        
        {{-- Header Topbar (Đảm bảo không bao giờ bị xuống hàng khi phóng to / zoom) --}}
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/90 px-3 sm:px-4 py-2.5 sm:py-3 flex items-center justify-between gap-2 sm:gap-4 flex-nowrap min-w-0">
            <div class="flex items-center gap-2 sm:gap-3 shrink-0 min-w-0">
                <button onclick="toggleSidebar()" class="md:hidden p-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[22px]">menu</span>
                </button>
                
                {{-- Breadcrumb (Không bị xuống hàng) --}}
                <div class="flex items-center gap-1.5 sm:gap-2 text-xs font-medium text-slate-500 whitespace-nowrap shrink-0 min-w-0">
                    <span class="shrink-0">Quản trị</span>
                    <span class="material-symbols-outlined text-[14px] text-slate-300 shrink-0">chevron_right</span>
                    <span id="header-page-title" class="font-semibold text-slate-900 whitespace-nowrap truncate max-w-[150px] sm:max-w-[280px] lg:max-w-none">@yield('page-title', 'Bảng Điều Khiển')</span>
                </div>
            </div>

            {{-- Topbar Right Tools (Tất cả nút nằm thẳng hàng, không xuống dòng) --}}
            <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0 whitespace-nowrap">
                {{-- Quick Search Button --}}
                <div onclick="openCommandPalette()" class="hidden xl:flex items-center gap-2 px-3 py-1.5 bg-slate-100/80 hover:bg-slate-100 border border-slate-200 rounded-lg text-xs text-slate-400 w-44 2xl:w-56 cursor-pointer transition-colors shadow-2xs whitespace-nowrap shrink-0">
                    <span class="material-symbols-outlined text-[16px] text-slate-400 shrink-0">search</span>
                    <span class="flex-1 truncate">Tìm kiếm lối tắt...</span>
                    <kbd class="px-1.5 py-0.5 text-[10px] font-semibold bg-white border border-slate-200 rounded text-slate-500 shrink-0">Ctrl K</kbd>
                </div>

                <a href="{{ route('admin.vai-tro.index') }}" class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200/80 hover:bg-blue-100 transition-colors shadow-2xs whitespace-nowrap shrink-0">
                    <span class="material-symbols-outlined text-[16px] text-blue-600 shrink-0">admin_panel_settings</span>
                    <span class="whitespace-nowrap">Phân quyền</span>
                </a>

                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-white border border-slate-200/90 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-300 transition-all shadow-2xs group whitespace-nowrap shrink-0" title="Quay về trang chủ website">
                    <span class="material-symbols-outlined text-[18px] text-amber-500 group-hover:scale-110 transition-transform shrink-0">home</span>
                    <span class="font-bold whitespace-nowrap">Quay về trang chủ</span>
                </a>

                {{-- Real-time Admin Notification Bell --}}
                <div class="relative shrink-0" id="admin-notif-container">
                    <button id="admin-notif-bell" onclick="toggleNotifDropdown()" class="relative p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">notifications</span>
                        <span id="realtime-badge-count" class="{{ ($sidebarUnreadCount ?? 0) > 0 ? '' : 'hidden' }} absolute -top-1 -right-1 min-w-[20px] h-[20px] px-1 bg-rose-500 text-white text-[11px] font-bold rounded-full flex items-center justify-center border-2 border-white shadow-xs">{{ ($sidebarUnreadCount ?? 0) > 99 ? '99+' : ($sidebarUnreadCount ?? 0) }}</span>
                    </button>

                    {{-- Notification Dropdown Menu --}}
                    <div id="admin-notif-dropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden transform transition-all">
                        <div class="px-4 py-3 bg-slate-900 text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-amber-400">notifications_active</span>
                                <h3 class="text-xs font-bold uppercase tracking-wider">Thông Báo Thời Gian Thực</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="realtime-badge-dropdown" class="px-2 py-0.5 text-[10px] font-bold bg-amber-500 text-slate-950 rounded-full">{{ $sidebarUnreadCount ?? 0 }} mới</span>
                                <button type="button" onclick="markAllAdminNotifsReadDropdown(event)" class="text-[10px] font-medium text-slate-300 hover:text-white underline hover:no-underline cursor-pointer transition-colors" title="Đánh dấu tất cả thông báo là đã đọc">
                                    Đã đọc tất cả
                                </button>
                            </div>
                        </div>

                        {{-- Quick Filter Buttons in Dropdown --}}
                        <div class="px-3 py-2 bg-slate-50 border-b border-slate-100 flex items-center gap-1 overflow-x-auto text-[11px]">
                            <button type="button" onclick="filterDropdownNotifs('all')" class="notif-tab-btn active px-2.5 py-1 rounded-lg font-bold bg-slate-900 text-white transition-colors" data-filter="all">Tất cả</button>
                            <button type="button" onclick="filterDropdownNotifs('quan')" class="notif-tab-btn px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:bg-slate-200 transition-colors" data-filter="quan">Quán</button>
                            <button type="button" onclick="filterDropdownNotifs('blog')" class="notif-tab-btn px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:bg-slate-200 transition-colors" data-filter="blog">Bài viết</button>
                            <button type="button" onclick="filterDropdownNotifs('user')" class="notif-tab-btn px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:bg-slate-200 transition-colors" data-filter="user">Người dùng</button>
                            <button type="button" onclick="filterDropdownNotifs('broadcast')" class="notif-tab-btn px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:bg-slate-200 transition-colors" data-filter="broadcast">Hệ thống</button>
                        </div>

                        <div id="realtime-notif-list" class="max-h-80 overflow-y-auto divide-y divide-slate-100 p-2 text-xs">
                            <div class="p-4 text-center text-slate-400 font-medium">Đang tải toàn bộ thông báo...</div>
                        </div>

                        <div class="p-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold">
                            <a href="{{ route('admin.thong-bao.index') }}" class="text-indigo-600 hover:text-indigo-800 transition-colors">Trung tâm thông báo →</a>
                            <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="text-amber-700 hover:text-amber-800 transition-colors">Xem chờ duyệt →</a>
                        </div>
                    </div>
                </div>

                <div class="h-5 w-px bg-slate-200 shrink-0"></div>

                {{-- Interactive Admin User Profile Dropdown (Không bao giờ rớt dòng) --}}
                <div class="relative shrink-0" id="admin-profile-container">
                    <button type="button" id="admin-profile-trigger" onclick="toggleAdminProfileDropdown()" class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/90 rounded-full transition-all cursor-pointer shadow-2xs group focus:outline-none focus:ring-2 focus:ring-emerald-400 whitespace-nowrap shrink-0" title="Bấm để xem hồ sơ và tùy chọn quản trị">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                        <span class="text-xs font-bold text-emerald-800 whitespace-nowrap">{{ auth()->user()->ho_ten ?? 'Quản Trị Viên (Admin)' }}</span>
                        <span class="material-symbols-outlined text-[16px] text-emerald-600 group-hover:rotate-180 transition-transform shrink-0">expand_more</span>
                    </button>

                    <div id="admin-profile-dropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-200 z-50 overflow-hidden py-1.5 transition-all text-xs">
                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/70">
                            <p class="font-bold text-slate-900 text-sm truncate">{{ auth()->user()->ho_ten ?? 'Quản Trị Viên' }}</p>
                            <p class="text-slate-500 text-[11px] truncate">{{ auth()->user()->email ?? 'admin@quanmoi.com' }}</p>
                            <div class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Quản Trị Cấp Cao
                            </div>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('tai-khoan.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900 font-medium transition-colors">
                                <span class="material-symbols-outlined text-[18px] text-slate-400">account_circle</span>
                                <span>Hồ sơ cá nhân</span>
                            </a>
                            <a href="{{ route('tai-khoan.cai-dat') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900 font-medium transition-colors">
                                <span class="material-symbols-outlined text-[18px] text-slate-400">settings</span>
                                <span>Cài đặt tài khoản</span>
                            </a>
                            <a href="{{ route('admin.vai-tro.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900 font-medium transition-colors">
                                <span class="material-symbols-outlined text-[18px] text-blue-500">admin_panel_settings</span>
                                <span>Vai trò & Phân quyền</span>
                            </a>
                            <a href="{{ route('admin.bao-mat.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900 font-medium transition-colors">
                                <span class="material-symbols-outlined text-[18px] text-emerald-500">security</span>
                                <span>Cấu hình bảo mật</span>
                            </a>
                        </div>

                        <div class="border-t border-slate-100 pt-1 mt-1">
                            <button type="button" onclick="confirmAdminLogout('desktop-logout-form')" class="w-full flex items-center gap-2.5 px-4 py-2 text-rose-600 hover:bg-rose-50 font-bold transition-colors text-left cursor-pointer">
                                <span class="material-symbols-outlined text-[18px] text-rose-500">logout</span>
                                <span>Đăng xuất hệ thống</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main id="admin-main-content" class="p-2 sm:p-3 flex-grow transition-opacity duration-150 min-w-0 w-full overflow-x-hidden">
            {{-- Flash Alerts Container (chỉ hiển thị tại chức năng phát sinh thông báo) --}}
            <div id="admin-flash-container" class="space-y-3 mb-4 empty:hidden">
                @if(session('success'))
                    <div class="admin-flash-alert bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 rounded-xl text-xs font-medium flex items-center justify-between gap-3 shadow-xs transition-all duration-300">
                        <div class="flex items-center gap-2.5 flex-1 min-w-0">
                            <span class="material-symbols-outlined text-emerald-600 text-[18px] shrink-0">check_circle</span>
                            <div class="flex-1 break-words font-medium">{{ session('success') }}</div>
                        </div>
                        <button type="button" onclick="dismissAdminFlashAlert(this)" class="text-emerald-700/60 hover:text-emerald-900 p-1 rounded-lg hover:bg-emerald-100/60 transition-colors shrink-0" title="Đóng thông báo">
                            <span class="material-symbols-outlined text-[16px] block">close</span>
                        </button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="admin-flash-alert bg-blue-50 border border-blue-200 text-blue-900 px-4 py-3 rounded-xl text-xs font-medium flex items-center justify-between gap-3 shadow-xs transition-all duration-300">
                        <div class="flex items-center gap-2.5 flex-1 min-w-0">
                            <span class="material-symbols-outlined text-blue-600 text-[18px] shrink-0">info</span>
                            <div class="flex-1 break-words font-medium">{{ session('info') }}</div>
                        </div>
                        <button type="button" onclick="dismissAdminFlashAlert(this)" class="text-blue-700/60 hover:text-blue-900 p-1 rounded-lg hover:bg-blue-100/60 transition-colors shrink-0" title="Đóng thông báo">
                            <span class="material-symbols-outlined text-[16px] block">close</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="admin-flash-alert bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 rounded-xl text-xs font-medium flex items-center justify-between gap-3 shadow-xs transition-all duration-300">
                        <div class="flex items-center gap-2.5 flex-1 min-w-0">
                            <span class="material-symbols-outlined text-rose-600 text-[18px] shrink-0">error</span>
                            <div class="flex-1 break-words font-medium">{{ session('error') }}</div>
                        </div>
                        <button type="button" onclick="dismissAdminFlashAlert(this)" class="text-rose-700/60 hover:text-rose-900 p-1 rounded-lg hover:bg-rose-100/60 transition-colors shrink-0" title="Đóng thông báo">
                            <span class="material-symbols-outlined text-[16px] block">close</span>
                        </button>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="admin-flash-alert bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 rounded-xl text-xs font-medium flex items-start justify-between gap-3 shadow-xs transition-all duration-300">
                        <div class="flex items-start gap-2.5 flex-1 min-w-0">
                            <span class="material-symbols-outlined text-amber-600 text-[18px] shrink-0 mt-0.5">warning</span>
                            <div class="flex-1">
                                <div class="font-bold mb-1">Vui lòng kiểm tra lại thông tin:</div>
                                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-amber-800">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" onclick="dismissAdminFlashAlert(this)" class="text-amber-700/60 hover:text-amber-900 p-1 rounded-lg hover:bg-amber-100/60 transition-colors shrink-0 mt-0.5" title="Đóng thông báo">
                            <span class="material-symbols-outlined text-[16px] block">close</span>
                        </button>
                    </div>
                @endif
            </div>

            @yield('content')
        </main>
    </div>

    {{-- Floating Back to Top Button --}}
    <button id="admin-back-to-top" type="button" onclick="scrollAdminToTop(true)" class="fixed bottom-6 right-6 z-40 bg-slate-900/90 hover:bg-slate-950 text-white px-3.5 py-2.5 rounded-2xl shadow-xl border border-slate-700/60 backdrop-blur-md transition-all duration-300 opacity-0 pointer-events-none translate-y-3 flex items-center justify-center gap-1.5 text-xs font-bold cursor-pointer hover:shadow-2xl hover:scale-105" title="Cuộn lên đầu trang">
        <span class="material-symbols-outlined text-[18px] text-amber-400">arrow_upward</span>
        <span class="text-[11px]">Lên đầu trang</span>
    </button>

    {{-- Enterprise Command Palette Modal (Ctrl + K) --}}
    <div id="command-palette-modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden flex items-start justify-center pt-20 px-4 transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden transform transition-all">
            <div class="flex items-center px-4 border-b border-slate-100 bg-slate-50/50">
                <span class="material-symbols-outlined text-slate-400">search</span>
                <input id="command-palette-input" onkeyup="filterCommandPalette()" type="text" placeholder="Gõ từ khóa để chuyển nhanh trang quản trị..." class="w-full px-3 py-3.5 text-xs text-slate-800 placeholder-slate-400 bg-transparent focus:outline-none font-medium">
                <button onclick="closeCommandPalette()" class="px-1.5 py-0.5 text-[10px] font-bold bg-slate-200/80 hover:bg-slate-200 text-slate-600 rounded">ESC</button>
            </div>
            <div id="command-palette-list" class="p-2 max-h-80 overflow-y-auto space-y-1 text-xs">
                <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Lối Tắt Truy Cập Nhanh</div>
                <a href="{{ route('home') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-amber-50 hover:text-amber-700 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-amber-500">home</span>
                    <span>Quay Về Trang Chủ Website</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">space_dashboard</span>
                    <span>Bảng Điều Khiển Trung Tâm</span>
                </a>
                <a href="{{ route('admin.duyet-xac-thuc.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">fact_check</span>
                    <span>Duyệt Yêu Cầu Quán & Người Dùng</span>
                </a>
                <a href="{{ route('admin.quan.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                    <span>Quản Lý Danh Sách Quán</span>
                </a>
                <a href="{{ route('admin.nguoi-dung.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                    <span>Quản Lý Người Dùng & Tài Khoản</span>
                </a>
                <a href="{{ route('admin.thanh-toan.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                    <span>Quản Lý Nạp Tiền & Doanh Thu Ví</span>
                </a>
                <a href="{{ route('admin.thong-ke.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">query_stats</span>
                    <span>Báo Cáo & Thống Kê Doanh Thu</span>
                </a>
                <a href="{{ route('admin.audit-log.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">reorder</span>
                    <span>Nhật Ký Tác Vụ Hệ Thống (Audit Log)</span>
                </a>
                <a href="{{ route('admin.ai-tools.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">neurology</span>
                    <span>Trợ Lý Trí Tuệ Nhân Tạo AI</span>
                </a>
                <a href="{{ route('admin.bao-mat.index') }}" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">shield</span>
                    <span>Cấu Hình Bảo Mật Hệ Thống</span>
                </a>
                <div onclick="closeCommandPalette(); confirmAdminLogout('desktop-logout-form');" class="cmd-item flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 text-rose-700 font-semibold transition-colors cursor-pointer border-t border-slate-100 mt-1">
                    <span class="material-symbols-outlined text-[18px] text-rose-500">logout</span>
                    <span>Đăng Xuất Khỏi Quản Trị</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Enterprise Custom Confirmation Modal --}}
    <div id="admin-confirm-modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 transition-opacity duration-200" style="display: none;">
        <div id="admin-confirm-box" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full p-6 transform transition-all scale-95 duration-200 text-center">
            <div id="confirm-modal-icon-bg" class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4 transition-colors bg-amber-100 text-amber-600">
                <span id="confirm-modal-icon" class="material-symbols-outlined text-[32px]">help</span>
            </div>
            <h3 id="confirm-modal-title" class="text-base font-bold text-slate-900 mb-1.5">Xác Nhận Thao Tác</h3>
            <div id="confirm-modal-message" class="text-xs text-slate-600 mb-6 leading-relaxed">Bạn có chắc chắn muốn thực hiện hành động này?</div>

            <div class="flex items-center justify-center gap-3">
                <button type="button" onclick="closeConfirmModal()" id="confirm-modal-cancel-btn" class="modal-btn-cancel">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    <span>Hủy bỏ</span>
                </button>
                <button type="button" id="confirm-modal-submit-btn" class="modal-btn-action modal-btn-success">
                    <span id="confirm-modal-btn-icon" class="material-symbols-outlined text-[16px]">check_circle</span>
                    <span id="confirm-modal-btn-text">Xác nhận</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Enterprise Venue Media & Detail Inspection Modal --}}
    <div id="venue-media-modal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 hidden flex items-center justify-center p-3 sm:p-5 transition-opacity duration-200" style="display: none;">
        <div id="venue-media-box" class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 max-w-5xl w-full max-h-[94vh] flex flex-col overflow-hidden transform transition-all scale-95 duration-200">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80 shrink-0">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center border border-amber-200/50">
                        <span class="material-symbols-outlined text-[22px]">verified</span>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 id="vm-title" class="text-base font-black text-slate-900 leading-tight">Hồ Sơ & Thẩm Định Quán Ăn</h3>
                            <span id="vm-status-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Chờ duyệt</span>
                        </div>
                        <p id="vm-subtitle" class="text-xs text-slate-500 mt-0.5">Thẩm định tính xác thực của hình ảnh, địa điểm và thông tin chủ cơ sở</p>
                    </div>
                </div>
                <button type="button" onclick="closeVenueMediaModal()" class="w-9 h-9 rounded-xl bg-slate-200/70 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors cursor-pointer" title="Đóng cửa sổ">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- Body: 2 Columns on Desktop --}}
            <div class="p-6 overflow-y-auto flex-grow">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {{-- Left Col: Media Gallery (7 cols) --}}
                    <div class="lg:col-span-7 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-amber-500">photo_library</span>
                                Album Hình Ảnh Thẩm Định
                            </span>
                            <span id="vm-gallery-count" class="text-[11px] text-slate-500 font-semibold bg-slate-100 px-2 py-0.5 rounded-full">1 hình ảnh</span>
                        </div>

                        {{-- Main Big Photo Preview --}}
                        <div class="relative w-full h-72 sm:h-96 rounded-2xl overflow-hidden bg-slate-950 shadow-inner group border border-slate-200">
                            <img id="vm-main-img" src="" alt="Ảnh quán" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-102" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&auto=format&fit=crop&q=80';">
                            <div class="absolute bottom-3 left-3 bg-slate-950/80 backdrop-blur-md text-white text-[11px] font-semibold px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-md">
                                <span class="material-symbols-outlined text-[14px] text-amber-400">image</span>
                                <span id="vm-main-label">Ảnh Bìa Đại Diện</span>
                            </div>
                            <a id="vm-view-origin" href="#" target="_blank" class="absolute top-3 right-3 bg-white/90 hover:bg-white text-slate-800 text-xs font-bold px-3 py-1.5 rounded-xl shadow-md transition-all flex items-center gap-1 opacity-0 group-hover:opacity-100 cursor-pointer">
                                <span class="material-symbols-outlined text-[15px]">open_in_full</span>
                                <span>Xem ảnh gốc</span>
                            </a>
                        </div>

                        {{-- Gallery Thumbnails Strip --}}
                        <div id="vm-gallery-strip" class="flex items-center gap-2.5 overflow-x-auto pb-2 pt-1">
                            {{-- Injected dynamically --}}
                        </div>
                    </div>

                    {{-- Right Col: Inspection Dossier (5 cols) --}}
                    <div class="lg:col-span-5 space-y-4">
                        <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200/80 space-y-3">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[15px] text-indigo-500">description</span>
                                Hồ Sơ Đăng Ký Địa Điểm
                            </h4>
                            
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tên Địa Điểm</span>
                                <div id="vm-detail-name" class="font-black text-slate-900 text-base mt-0.5">--</div>
                                <div class="mt-1">
                                    <span id="vm-detail-type" class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/70">--</span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Địa Chỉ Thực Tế</span>
                                <div id="vm-detail-address" class="text-xs text-slate-700 font-medium mt-0.5 flex items-start gap-1">
                                    <span class="material-symbols-outlined text-[15px] text-rose-500 shrink-0 mt-0.5">location_on</span>
                                    <span>--</span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Chủ Quán / Người Đăng</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[15px]">person</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div id="vm-detail-owner" class="font-bold text-xs text-slate-900 truncate">--</div>
                                        <div id="vm-detail-contact" class="text-[11px] text-slate-500 space-y-0.5 mt-0.5">--</div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Giờ Mở Cửa & Khung Giá</span>
                                <div id="vm-detail-hours-price" class="text-xs text-slate-700 font-medium mt-0.5">--</div>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-400">
                                <span>Thời gian nộp duyệt:</span>
                                <span id="vm-detail-date" class="font-bold text-slate-600">--</span>
                            </div>
                        </div>

                        {{-- Quality Verification Checklist --}}
                        <div class="bg-amber-50/50 rounded-2xl p-3.5 border border-amber-200/50 text-[11px] space-y-1.5 text-amber-900">
                            <div class="font-bold flex items-center gap-1.5 text-amber-800">
                                <span class="material-symbols-outlined text-[16px] text-amber-600">fact_check</span>
                                Tiêu Chuẩn Thẩm Định Doanh Nghiệp
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-600">
                                <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
                                <span>Hình ảnh chụp thật, không chứa hình mờ hoặc vi phạm</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-600">
                                <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
                                <span>Địa chỉ rõ ràng, đúng khu vực đăng ký</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-600">
                                <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
                                <span>Thông tin liên hệ chủ cơ sở chính chủ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Action Buttons --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between shrink-0 flex-wrap sm:flex-nowrap gap-3">
                <button type="button" onclick="closeVenueMediaModal()" class="btn-enterprise btn-enterprise-secondary">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    <span class="whitespace-nowrap">Đóng lại</span>
                </button>
                <div id="vm-actions" class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                    {{-- Approve & Reject buttons injected dynamically --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Enterprise Blog Quick Preview Modal --}}
    <div id="admin-blog-preview-modal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs z-50 hidden flex items-center justify-center p-3 sm:p-5 transition-opacity duration-200" style="display: none;">
        <div id="admin-blog-preview-box" class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-4xl lg:max-w-5xl w-full max-h-[92vh] flex flex-col overflow-hidden transform transition-all scale-95 duration-200">
            {{-- Header --}}
            <div class="px-5 sm:px-6 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/90 shrink-0 gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <img id="bp-author-avatar" src="https://ui-avatars.com/api/?name=Admin" alt="Avatar" class="w-10 h-10 rounded-2xl object-cover border border-slate-200 shrink-0 shadow-2xs">
                    <div class="min-w-0 flex-1">
                        <h3 id="bp-title" class="text-sm sm:text-base font-black text-slate-900 leading-tight truncate">Xem Nhanh Bài Viết Phê Duyệt</h3>
                        <p id="bp-meta" class="text-[11px] text-slate-500 mt-0.5 truncate leading-tight">Tác giả: ... • Chuyên mục: ...</p>
                    </div>
                </div>
                <button type="button" onclick="closeBlogQuickPreviewModal()" class="w-9 h-9 rounded-xl bg-slate-200/70 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors cursor-pointer shrink-0" title="Đóng cửa sổ">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 sm:p-6 overflow-y-auto flex-grow space-y-4 custom-scrollbar">
                {{-- Cover Image --}}
                <div id="bp-cover-container" class="w-full max-h-72 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200/90 shadow-2xs">
                    <img id="bp-cover-img" src="" alt="Ảnh bìa bài viết" class="w-full h-full max-h-72 object-cover">
                </div>

                {{-- Badges & Metadata --}}
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <span id="bp-badge" class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Chờ duyệt</span>
                        <span id="bp-hero-badge" class="hidden inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white shadow-2xs">
                            <span class="material-symbols-outlined text-[13px]">star</span>
                            <span>Hero Nổi Bật</span>
                        </span>
                        <span id="bp-date-badge" class="text-[11px] text-slate-400 font-medium"></span>
                    </div>

                    <h2 id="bp-heading" class="text-lg sm:text-2xl font-black text-slate-900 leading-snug break-words whitespace-normal cell-wrap-free"></h2>

                    {{-- Tags --}}
                    <div id="bp-tags-container" class="flex flex-wrap gap-1.5 mt-2"></div>

                    {{-- Excerpt --}}
                    <div id="bp-excerpt" class="hidden mt-3 text-xs sm:text-[13px] text-slate-600 bg-amber-50/50 p-3.5 rounded-2xl border border-amber-200/60 italic leading-relaxed break-words whitespace-normal"></div>

                    {{-- Moderation Notes History Callout --}}
                    <div id="bp-log-container" class="hidden mt-3 text-xs p-3.5 rounded-2xl border leading-relaxed break-words whitespace-normal"></div>
                </div>

                {{-- Post Full Content --}}
                <div id="bp-content" class="prose max-w-none text-xs sm:text-sm text-slate-700 border-t border-slate-100 pt-4 leading-relaxed break-words overflow-x-auto custom-scrollbar"></div>
            </div>

            {{-- Footer Actions --}}
            <div class="px-5 sm:px-6 py-3.5 border-t border-slate-100 bg-slate-50/95 flex items-center justify-between gap-2.5 sm:gap-4 shrink-0 overflow-x-auto custom-scrollbar flex-nowrap">
                <div class="flex items-center gap-2 flex-nowrap shrink-0">
                    <a id="bp-detail-link" href="#" class="btn-enterprise btn-enterprise-secondary h-9 px-3.5 text-xs whitespace-nowrap shrink-0 inline-flex items-center gap-1.5 font-semibold" title="Mở trang chi tiết đầy đủ">
                        <span class="material-symbols-outlined text-[16px] shrink-0">open_in_new</span>
                        <span class="whitespace-nowrap">Mở trang chi tiết đầy đủ</span>
                    </a>
                    <a id="bp-public-link" href="#" target="_blank" class="hidden btn-enterprise btn-enterprise-dark h-9 px-3.5 text-xs whitespace-nowrap shrink-0 inline-flex items-center gap-1.5 font-semibold" title="Xem trên giao diện công khai">
                        <span class="material-symbols-outlined text-[16px] shrink-0">visibility</span>
                        <span class="whitespace-nowrap">Xem ngoài website</span>
                    </a>
                </div>

                <div class="flex items-center gap-2 flex-nowrap shrink-0">
                    <button type="button" id="bp-btn-delete" class="btn-enterprise btn-enterprise-secondary text-gray-500 hover:text-rose-600 hover:bg-rose-50 border-gray-200 h-9 px-3 text-xs whitespace-nowrap shrink-0 inline-flex items-center gap-1.5 font-semibold" title="Xóa bài viết">
                        <span class="material-symbols-outlined text-[16px] shrink-0">delete</span>
                        <span class="whitespace-nowrap">Xóa bài</span>
                    </button>
                    <button type="button" id="bp-btn-reject" class="btn-enterprise btn-enterprise-danger h-9 px-3.5 text-xs whitespace-nowrap shrink-0 inline-flex items-center gap-1.5 font-semibold" title="Không duyệt bài viết">
                        <span class="material-symbols-outlined text-[16px] shrink-0">cancel</span>
                        <span class="whitespace-nowrap">Không duyệt</span>
                    </button>
                    <button type="button" id="bp-btn-approve" class="btn-enterprise btn-enterprise-success h-9 px-4 text-xs whitespace-nowrap shrink-0 inline-flex items-center gap-1.5 font-bold" title="Phê duyệt bài viết">
                        <span class="material-symbols-outlined text-[16px] shrink-0">check_circle</span>
                        <span class="whitespace-nowrap">Phê Duyệt Bài Viết</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Global Blog Moderation Modal (Từ chối / Yêu cầu sửa) --}}
    <div id="admin-blog-moderation-modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 transition-opacity duration-200" style="display: none;">
        <div id="admin-blog-moderation-box" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-6 transform transition-all scale-95 duration-200">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-200/70">
                        <span class="material-symbols-outlined text-[22px]">gavel</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight">Phản Hồi & Kiểm Duyệt Bài Viết</h3>
                        <p id="mod-modal-blog-title" class="text-xs text-slate-500 font-medium truncate max-w-xs mt-0.5">Tiêu đề bài viết...</p>
                    </div>
                </div>
                <button type="button" onclick="closeBlogModerationModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <input type="hidden" id="mod-modal-blog-id" value="">
            <input type="hidden" id="mod-modal-expected-status" value="">

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Hành động kiểm duyệt <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50">
                        <input type="radio" name="mod_action_type" value="rejected" checked class="text-rose-600 focus:ring-rose-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900">Từ chối bài viết</span>
                            <span class="block text-[10px] text-slate-500">Không cho phép đăng</span>
                        </div>
                    </label>
                    <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50/50">
                        <input type="radio" name="mod_action_type" value="need_revision" class="text-orange-600 focus:ring-orange-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900">Yêu cầu sửa đổi</span>
                            <span class="block text-[10px] text-slate-500">Tác giả sửa lại</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Gợi ý lý do phổ biến:</label>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="setModerationReasonChip('Nội dung vi phạm tiêu chuẩn cộng đồng hoặc không phù hợp ẩm thực.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium rounded-lg transition-colors cursor-pointer">
                        Vi phạm tiêu chuẩn
                    </button>
                    <button type="button" onclick="setModerationReasonChip('Hình ảnh mờ, chất lượng thấp hoặc chứa watermark bản quyền.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium rounded-lg transition-colors cursor-pointer">
                        Ảnh mờ / bản quyền
                    </button>
                    <button type="button" onclick="setModerationReasonChip('Nội dung bài viết quá sơ sài, thiếu thông tin trải nghiệm thực tế.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium rounded-lg transition-colors cursor-pointer">
                        Nội dung sơ sài
                    </button>
                    <button type="button" onclick="setModerationReasonChip('Nội dung trùng lặp hoặc sao chép từ bài viết khác trên hệ thống.')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium rounded-lg transition-colors cursor-pointer">
                        Trùng lặp nội dung
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label for="mod-modal-note" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Lý do chi tiết & Hướng dẫn chỉnh sửa <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    id="mod-modal-note" 
                    rows="3" 
                    placeholder="Nhập lý do cụ thể gửi đến tác giả bài viết..." 
                    class="w-full p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all font-medium placeholder:text-slate-400"
                ></textarea>
                <p class="text-[10px] text-slate-400 mt-1">Lý do này sẽ hiển thị trực tiếp trong trang quản lý bài viết của tác giả và gửi thông báo.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeBlogModerationModal()" class="btn-enterprise btn-enterprise-secondary py-2">
                    <span>Hủy bỏ</span>
                </button>
                <button type="button" id="btn-submit-blog-moderation" onclick="adminSubmitModerationModal()" class="btn-enterprise btn-enterprise-danger py-2">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    <span id="btn-submit-blog-moderation-text">Xác Nhận & Gửi Phản Hồi</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Floating Toast Notification Container (Góc trên bên phải) --}}
    <div id="realtime-toast-container" class="fixed top-5 right-5 z-[99999] flex flex-col gap-3 max-w-sm pointer-events-auto"></div>

    <script>
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        // Enterprise Confirmation Modal Engine
        let confirmModalCallback = null;

        function openConfirmModal(options) {
            const modal = document.getElementById('admin-confirm-modal');
            const box = document.getElementById('admin-confirm-box');
            const titleEl = document.getElementById('confirm-modal-title');
            const msgEl = document.getElementById('confirm-modal-message');
            const iconEl = document.getElementById('confirm-modal-icon');
            const iconBgEl = document.getElementById('confirm-modal-icon-bg');
            const submitBtn = document.getElementById('confirm-modal-submit-btn');
            const btnText = document.getElementById('confirm-modal-btn-text');
            const btnIcon = document.getElementById('confirm-modal-btn-icon');

            if (!modal) return;

            confirmModalCallback = options.onConfirm || null;

            titleEl.textContent = options.title || 'Xác Nhận Thao Tác';
            msgEl.innerHTML = options.message || 'Bạn có chắc chắn muốn thực hiện hành động này?';
            iconEl.textContent = options.icon || 'help';
            iconBgEl.className = 'w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4 ' + (options.iconBg || 'bg-amber-100 text-amber-600');
            btnText.textContent = options.confirmText || 'Xác nhận';
            btnIcon.textContent = options.confirmIcon || 'check_circle';

            // Intelligent style mapping: ALWAYS assigns a vibrant, high-contrast action color with white text
            let actionColorClass = 'modal-btn-success';
            const cClass = (options.confirmClass || '').toLowerCase();
            if (cClass.includes('danger') || cClass.includes('rose') || cClass.includes('red') || cClass.includes('delete')) {
                actionColorClass = 'modal-btn-danger';
            } else if (cClass.includes('warning') || cClass.includes('amber') || cClass.includes('orange') || cClass.includes('yellow')) {
                actionColorClass = 'modal-btn-warning';
            } else if (cClass.includes('primary') || cClass.includes('blue') || cClass.includes('indigo') || cClass.includes('verified')) {
                actionColorClass = 'modal-btn-primary';
            } else if (cClass.includes('dark') || cClass.includes('slate') || cClass.includes('secondary') || cClass.includes('cancel') || cClass.includes('gray')) {
                actionColorClass = 'modal-btn-dark';
            }

            // Always assign full, reliable classes and ensure visible inline color
            submitBtn.className = 'modal-btn-action ' + actionColorClass;
            submitBtn.style.color = '#ffffff';
            submitBtn.style.display = 'inline-flex';

            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            setTimeout(() => {
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            }, 10);
        }

        function closeConfirmModal() {
            const modal = document.getElementById('admin-confirm-modal');
            const box = document.getElementById('admin-confirm-box');
            if (!modal) return;

            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                confirmModalCallback = null;
            }, 150);
        }

        document.getElementById('confirm-modal-submit-btn')?.addEventListener('click', function() {
            if (typeof confirmModalCallback === 'function') {
                const cb = confirmModalCallback;
                closeConfirmModal();
                cb();
            } else {
                closeConfirmModal();
            }
        });

        // Xác nhận đăng xuất bằng Custom Modal tiếng Việt
        function confirmAdminLogout(formId = 'desktop-logout-form') {
            openConfirmModal({
                title: 'Xác Nhận Đăng Xuất',
                message: 'Bạn có chắc chắn muốn đăng xuất khỏi trang quản trị <b class="text-slate-900 font-bold">QuanMoi Admin</b> không?<br><span class="text-slate-500 text-[11px]">Vui lòng lưu lại mọi nội dung đang chỉnh sửa trước khi rời khỏi hệ thống.</span>',
                icon: 'logout',
                iconBg: 'bg-rose-100 text-rose-700 border border-rose-200',
                confirmText: 'Đăng Xuất Ngay',
                confirmClass: 'btn-enterprise-danger',
                confirmIcon: 'logout',
                onConfirm: () => {
                    const form = document.getElementById(formId);
                    if (form) {
                        form.submit();
                    }
                }
            });
        }

        document.getElementById('venue-media-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeVenueMediaModal();
            }
        });

        function openVenueMediaModal(v) {
            if (!v) return;
            const modal = document.getElementById('venue-media-modal');
            const box = document.getElementById('venue-media-box');
            if (!modal) return;

            document.getElementById('vm-title').textContent = v.ten_quan || 'Hồ Sơ Quán Ăn';
            document.getElementById('vm-subtitle').textContent = (v.loai_hinh ? v.loai_hinh + ' • ' : '') + (v.dia_chi || '');
            
            // Status badge in modal header
            const statusBadge = document.getElementById('vm-status-badge');
            if (statusBadge) {
                if (v.trang_thai === 'da_duyet') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200/60';
                    statusBadge.textContent = 'Đang Hoạt Động';
                } else if (v.trang_thai === 'bi_khoa') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200/60';
                    statusBadge.textContent = 'Bị Từ Chối / Khóa';
                } else {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200/60';
                    statusBadge.textContent = 'Chờ Phê Duyệt';
                }
            }

            const mainImg = document.getElementById('vm-main-img');
            const viewOrigin = document.getElementById('vm-view-origin');
            const fallbackSrc = 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&auto=format&fit=crop&q=80';
            const defaultCover = v.anh_bia_url || fallbackSrc;
            
            mainImg.src = defaultCover;
            viewOrigin.href = defaultCover;
            document.getElementById('vm-main-label').textContent = 'Ảnh Bìa Đại Diện (1/' + ((v.gallery ? v.gallery.length : 0) + 1) + ')';

            // Gallery Strip
            const galleryStrip = document.getElementById('vm-gallery-strip');
            galleryStrip.innerHTML = '';
            
            const allImages = [];
            if (v.anh_bia_url) {
                allImages.push({ url: v.anh_bia_url, label: 'Ảnh Bìa Đại Diện' });
            }
            if (Array.isArray(v.gallery)) {
                v.gallery.forEach((gUrl, idx) => {
                    allImages.push({ url: gUrl, label: 'Hình Không Gian / Món ' + (idx + 1) });
                });
            }

            document.getElementById('vm-gallery-count').textContent = allImages.length > 0 ? (allImages.length + ' hình ảnh') : 'Chưa có ảnh phụ';

            if (allImages.length > 0) {
                allImages.forEach((imgObj, i) => {
                    const thumb = document.createElement('div');
                    thumb.className = 'w-16 h-16 rounded-xl overflow-hidden shrink-0 border-2 cursor-pointer transition-all ' + (i === 0 ? 'border-amber-500 scale-105 shadow-sm ring-2 ring-amber-300' : 'border-slate-200 hover:border-slate-400 opacity-70 hover:opacity-100');
                    thumb.innerHTML = `<img src="${imgObj.url}" alt="${imgObj.label}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='${fallbackSrc}';">`;
                    thumb.onclick = function() {
                        galleryStrip.querySelectorAll('div').forEach(d => {
                            d.className = 'w-16 h-16 rounded-xl overflow-hidden shrink-0 border-2 border-slate-200 hover:border-slate-400 opacity-70 hover:opacity-100 cursor-pointer transition-all';
                        });
                        thumb.className = 'w-16 h-16 rounded-xl overflow-hidden shrink-0 border-2 border-amber-500 scale-105 shadow-sm ring-2 ring-amber-300 cursor-pointer transition-all';
                        mainImg.src = imgObj.url;
                        viewOrigin.href = imgObj.url;
                        document.getElementById('vm-main-label').textContent = imgObj.label + ' (' + (i + 1) + '/' + allImages.length + ')';
                    };
                    galleryStrip.appendChild(thumb);
                });
            } else {
                galleryStrip.innerHTML = '<span class="text-xs text-slate-400 italic py-2">Quán chưa đăng tải thêm album hình ảnh phụ.</span>';
            }

            // Detail fields
            document.getElementById('vm-detail-name').textContent = v.ten_quan || 'Chưa cập nhật';
            document.getElementById('vm-detail-type').textContent = v.loai_hinh || 'Địa điểm ẩm thực';
            document.getElementById('vm-detail-address').textContent = (v.dia_chi || '') + (v.tinh_thanh ? ' (' + v.tinh_thanh + ')' : '');
            document.getElementById('vm-detail-owner').textContent = v.owner_name || (v.nguoi_dang ? v.nguoi_dang.ho_ten : 'Chưa xác định');
            
            const email = v.owner_email || (v.nguoi_dang ? v.nguoi_dang.email : '');
            const phone = v.owner_phone || (v.nguoi_dang ? v.nguoi_dang.so_dien_thoai : '');
            document.getElementById('vm-detail-contact').innerHTML = 
                (email ? `<div><a href="mailto:${email}" class="hover:text-primary underline">📧 ${email}</a></div>` : '') + 
                (phone ? `<div><a href="tel:${phone}" class="hover:text-primary font-bold">📞 ${phone}</a></div>` : '');

            const hours = (v.gio_mo_cua && v.gio_dong_cua) ? `${v.gio_mo_cua} - ${v.gio_dong_cua}` : 'Chưa thiết lập giờ';
            const price = (v.gia_nho_nhat && v.gia_lon_nhat) ? `${Number(v.gia_nho_nhat).toLocaleString()}đ - ${Number(v.gia_lon_nhat).toLocaleString()}đ` : 'Chưa cập nhật giá';
            document.getElementById('vm-detail-hours-price').textContent = `⏰ ${hours} • 💰 ${price}`;
            document.getElementById('vm-detail-date').textContent = v.created_at || (v.created_at_formatted || 'Gần đây');

            // Contextual Action Buttons based on status
            const actionsContainer = document.getElementById('vm-actions');
            const safeName = (v.ten_quan || '').replace(/'/g, "\\'");
            
            if (v.trang_thai === 'da_duyet') {
                actionsContainer.innerHTML = `
                    <a href="/admin/quan/${v.id}/edit" class="btn-enterprise btn-enterprise-dark">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        <span class="whitespace-nowrap">Quản Lý Hồ Sơ</span>
                    </a>
                `;
            } else if (v.trang_thai === 'bi_khoa') {
                actionsContainer.innerHTML = `
                    <button type="button" onclick="closeVenueMediaModal(); confirmReapproveQuan('${v.id}', '${safeName}')" class="btn-enterprise btn-enterprise-success">
                        <span class="material-symbols-outlined text-[16px]">published_with_changes</span>
                        <span class="whitespace-nowrap uppercase tracking-wider">Duyệt Lại Quán Này</span>
                    </button>
                    <button type="button" onclick="closeVenueMediaModal(); confirmDeleteRejectedQuan('${v.id}', '${safeName}')" class="btn-enterprise btn-enterprise-danger">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        <span class="whitespace-nowrap">Xóa Hồ Sơ</span>
                    </button>
                `;
            } else {
                actionsContainer.innerHTML = `
                    <button type="button" onclick="closeVenueMediaModal(); confirmApproveQuan('${v.id}', '${safeName}')" class="btn-enterprise btn-enterprise-success">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        <span class="whitespace-nowrap uppercase tracking-wider">Phê Duyệt Quán Này</span>
                    </button>
                    <button type="button" onclick="closeVenueMediaModal(); confirmRejectQuan('${v.id}', '${safeName}')" class="btn-enterprise btn-enterprise-danger">
                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                        <span class="whitespace-nowrap">Không Duyệt Quán</span>
                    </button>
                    <button type="button" onclick="closeVenueMediaModal(); confirmDeletePendingQuan('${v.id}', '${safeName}')" class="btn-enterprise btn-enterprise-secondary text-slate-500 hover:text-rose-600 hover:bg-rose-50 border-slate-200">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        <span class="whitespace-nowrap">Xóa Hồ Sơ</span>
                    </button>
                `;
            }

            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            setTimeout(() => {
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            }, 10);
        }

        function closeVenueMediaModal() {
            const modal = document.getElementById('venue-media-modal');
            const box = document.getElementById('venue-media-box');
            if (!modal) return;
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }, 150);
        }

        // Global Blog Quick Preview Engine
        document.getElementById('admin-blog-preview-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBlogQuickPreviewModal();
            }
        });

        function openBlogQuickPreviewModal(blogId) {
            const modal = document.getElementById('admin-blog-preview-modal');
            const box = document.getElementById('admin-blog-preview-box');
            if (!modal) return;

            document.getElementById('bp-title').textContent = 'Đang tải bài viết...';
            document.getElementById('bp-meta').textContent = 'Vui lòng chờ trong giây lát...';
            document.getElementById('bp-heading').textContent = '';
            document.getElementById('bp-excerpt').textContent = '';
            document.getElementById('bp-excerpt').classList.add('hidden');
            document.getElementById('bp-log-container').classList.add('hidden');
            document.getElementById('bp-tags-container').innerHTML = '';
            document.getElementById('bp-hero-badge').classList.add('hidden');
            document.getElementById('bp-public-link').classList.add('hidden');
            document.getElementById('bp-content').innerHTML = '<div class="py-12 text-center text-slate-400"><span class="material-symbols-outlined text-3xl animate-spin">progress_activity</span><p class="mt-2 text-xs font-medium">Đang tải nội dung bài viết...</p></div>';
            document.getElementById('bp-cover-container').classList.add('hidden');
            document.getElementById('bp-detail-link').href = `/admin/blog/bai-viet/${blogId}`;

            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            setTimeout(() => {
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            }, 10);

            fetch(`/admin/blog/bai-viet/${blogId}?ajax=1`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data || !data.success || !data.blog) {
                    throw new Error(data.message || `Lỗi máy chủ HTTP ${res.status}`);
                }
                return data;
            })
            .then(data => {
                const b = data.blog;
                document.getElementById('bp-title').textContent = 'Chi Tiết & Xem Nhanh Bài Viết';
                document.getElementById('bp-meta').textContent = `Tác giả: ${b.author_name} ${b.author_email ? '(' + b.author_email + ')' : ''} • Chuyên mục: ${b.category_name} • Ngày tạo: ${b.created_at_formatted || 'Gần đây'}`;
                document.getElementById('bp-heading').textContent = b.title;

                const avatarEl = document.getElementById('bp-author-avatar');
                if (avatarEl && b.author_avatar) {
                    avatarEl.src = b.author_avatar;
                }

                if (b.cover_image_url) {
                    const img = document.getElementById('bp-cover-img');
                    img.src = b.cover_image_url;
                    img.onerror = function() { this.src = 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80'; };
                    document.getElementById('bp-cover-container').classList.remove('hidden');
                } else {
                    document.getElementById('bp-cover-container').classList.add('hidden');
                }

                const badgeEl = document.getElementById('bp-badge');
                if (badgeEl) {
                    if (b.status === 'published') {
                        badgeEl.className = 'px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200';
                        badgeEl.textContent = 'Đã xuất bản';
                    } else if (b.status === 'rejected') {
                        badgeEl.className = 'px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-100 text-rose-800 border border-rose-200';
                        badgeEl.textContent = 'Đã từ chối';
                    } else if (b.status === 'need_revision') {
                        badgeEl.className = 'px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-orange-100 text-orange-800 border border-orange-200';
                        badgeEl.textContent = 'Cần sửa đổi';
                    } else if (b.status === 'scheduled') {
                        badgeEl.className = 'px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-purple-100 text-purple-800 border border-purple-200';
                        badgeEl.textContent = 'Lên lịch';
                    } else {
                        badgeEl.className = 'px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-100 text-amber-800 border border-amber-200';
                        badgeEl.textContent = 'Chờ duyệt';
                    }
                }

                const heroBadge = document.getElementById('bp-hero-badge');
                if (heroBadge) {
                    if (b.is_hero) {
                        heroBadge.classList.remove('hidden');
                    } else {
                        heroBadge.classList.add('hidden');
                    }
                }

                const dateBadge = document.getElementById('bp-date-badge');
                if (dateBadge) {
                    dateBadge.textContent = b.created_at_formatted ? `• Ngày đăng: ${b.created_at_formatted}` : '';
                }

                const tagsContainer = document.getElementById('bp-tags-container');
                if (tagsContainer) {
                    tagsContainer.innerHTML = '';
                    if (b.tags && b.tags.length > 0) {
                        b.tags.forEach(t => {
                            const tEl = document.createElement('span');
                            tEl.className = 'px-2 py-0.5 bg-slate-100 text-slate-700 font-medium text-[10.5px] rounded-md border border-slate-200/80';
                            tEl.textContent = '#' + (typeof t === 'object' ? t.name : t);
                            tagsContainer.appendChild(tEl);
                        });
                    }
                }

                const excerptEl = document.getElementById('bp-excerpt');
                if (excerptEl) {
                    if (b.excerpt) {
                        excerptEl.textContent = '“ ' + b.excerpt + ' ”';
                        excerptEl.classList.remove('hidden');
                    } else {
                        excerptEl.classList.add('hidden');
                    }
                }

                const logContainer = document.getElementById('bp-log-container');
                if (logContainer) {
                    if (b.latest_log && b.latest_log.note) {
                        const isReject = b.latest_log.action === 'rejected';
                        logContainer.className = `mt-3 text-xs p-3.5 rounded-2xl border leading-relaxed break-words whitespace-normal ${isReject ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-orange-50 border-orange-200 text-orange-800'}`;
                        logContainer.innerHTML = `
                            <div class="flex items-center gap-1.5 font-bold mb-1">
                                <span class="material-symbols-outlined text-[16px]">${isReject ? 'cancel' : 'rule'}</span>
                                <span>Ghi chú kiểm duyệt gần nhất (${b.latest_log.admin_name || 'Admin'} - ${b.latest_log.created_at || ''}):</span>
                            </div>
                            <div class="pl-5 italic">"${escapeHtml(b.latest_log.note)}"</div>
                        `;
                        logContainer.classList.remove('hidden');
                    } else {
                        logContainer.classList.add('hidden');
                    }
                }

                document.getElementById('bp-content').innerHTML = b.content || '<em class="text-slate-400">Không có nội dung chi tiết.</em>';

                const publicLink = document.getElementById('bp-public-link');
                if (publicLink) {
                    if (b.public_url) {
                        publicLink.href = b.public_url;
                        publicLink.classList.remove('hidden');
                    } else {
                        publicLink.classList.add('hidden');
                    }
                }

                const btnApprove = document.getElementById('bp-btn-approve');
                const btnReject = document.getElementById('bp-btn-reject');
                const btnDelete = document.getElementById('bp-btn-delete');

                if (btnApprove) {
                    btnApprove.style.display = b.status === 'published' ? 'none' : 'inline-flex';
                    btnApprove.onclick = function() {
                        adminQuickApproveBlog(b.id, b.title);
                    };
                }

                if (btnReject) {
                    btnReject.style.display = b.status === 'rejected' ? 'none' : 'inline-flex';
                    btnReject.onclick = function() {
                        adminOpenRejectModal(b.id, b.title, b.status);
                    };
                }

                if (btnDelete) {
                    btnDelete.onclick = function() {
                        adminConfirmDeleteBlog(b.id, b.title);
                    };
                }
            })
            .catch(err => {
                document.getElementById('bp-content').innerHTML = `
                    <div class="p-6 text-center text-rose-500 text-xs">
                        <span class="material-symbols-outlined text-3xl mb-1 text-rose-400 block">error</span>
                        <p class="font-bold mb-1">Không thể nạp nội dung bài viết này (${escapeHtml(err.message || 'Lỗi kết nối')})</p>
                        <p class="text-slate-500 mb-3">Vui lòng thử lại hoặc mở trực tiếp trang kiểm duyệt chi tiết.</p>
                        <button type="button" onclick="openBlogQuickPreviewModal(${blogId})" class="btn-enterprise btn-enterprise-secondary py-1.5 text-xs mr-2">
                            <span class="material-symbols-outlined text-[14px]">refresh</span>
                            <span>Thử lại</span>
                        </button>
                    </div>
                `;
            });
        }

        function closeBlogQuickPreviewModal() {
            const modal = document.getElementById('admin-blog-preview-modal');
            const box = document.getElementById('admin-blog-preview-box');
            if (!modal) return;
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }, 150);
        }

        // Global Blog Actions: Approve, Reject/Revision, Delete
        function adminQuickApproveBlog(blogId, blogTitle) {
            let title = blogTitle;
            if (!title) {
                const titleEl = document.querySelector(`#admin-blog-row-${blogId} a[title]`);
                title = titleEl ? titleEl.getAttribute('title') : '#' + blogId;
            }

            openConfirmModal({
                title: 'Xác Nhận Duyệt Bài Viết',
                message: `Bạn có chắc chắn muốn phê duyệt và xuất bản bài viết <b class="text-slate-900 font-bold">"${escapeHtml(title)}"</b> không?<br><span class="text-emerald-700 text-[11px] font-medium">Bài viết sẽ chính thức được xuất bản công khai ngay lập tức.</span>`,
                icon: 'task_alt',
                iconBg: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                confirmText: 'Đồng Ý Duyệt',
                confirmClass: 'btn-enterprise-success',
                confirmIcon: 'check_circle',
                onConfirm: () => executeQuickApproveBlog(blogId)
            });
        }

        function executeQuickApproveBlog(blogId) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const url = `/admin/blog/bai-viet/${blogId}/status`;

            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    status: 'published',
                    note: 'Phê duyệt xuất bản bài viết từ trang quản trị.'
                })
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
                return data;
            })
            .then(data => {
                if (data.success) {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Đã Phê Duyệt', data.message || 'Bài viết đã được xuất bản thành công!', '', 'check_circle', 'bg-emerald-500');
                    }
                    clearAdminPageCache();
                    closeBlogQuickPreviewModal();

                    if (typeof updateRowStatusBadge === 'function') {
                        updateRowStatusBadge(blogId, data.status, data.status_label);
                    }
                    if (typeof updateAdminBlogStats === 'function' && data.stats) {
                        updateAdminBlogStats(data.stats);
                    }

                    const isPendingPage = (new URLSearchParams(window.location.search).get('status') === 'pending') || window.location.pathname.includes('/cho-duyet');
                    if (isPendingPage) {
                        const rowEl = document.getElementById(`admin-blog-row-${blogId}`);
                        if (rowEl) {
                            rowEl.style.transition = 'all 0.35s ease';
                            rowEl.style.opacity = '0';
                            rowEl.style.transform = 'translateX(20px)';
                            setTimeout(() => {
                                rowEl.remove();
                                const tbody = document.getElementById('admin-blog-tbody');
                                if (tbody && tbody.querySelectorAll('tr').length === 0) {
                                    tbody.innerHTML = `
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">
                                                <span class="material-symbols-outlined text-3xl text-emerald-400 block mb-2">task_alt</span>
                                                Tuyệt vời! Hiện không còn bài viết nào đang chờ duyệt.
                                            </td>
                                        </tr>
                                    `;
                                }
                            }, 350);
                        }
                    }
                } else {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Duyệt Thất Bại', data.message || 'Không thể phê duyệt bài viết.', '', 'error', 'bg-rose-500');
                    }
                }
            })
            .catch(err => {
                if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Lỗi Kết Nối', err.message || 'Không thể kết nối máy chủ.', '', 'wifi_off', 'bg-rose-500');
                }
            });
        }

        function adminOpenRejectModal(blogId, blogTitle, currentStatus = 'pending') {
            const modal = document.getElementById('admin-blog-moderation-modal');
            const box = document.getElementById('admin-blog-moderation-box');
            const idInput = document.getElementById('mod-modal-blog-id');
            const expInput = document.getElementById('mod-modal-expected-status');
            const titleEl = document.getElementById('mod-modal-blog-title');
            const noteInput = document.getElementById('mod-modal-note');

            if (!modal) return;

            let title = blogTitle;
            if (!title) {
                const titleElDom = document.querySelector(`#admin-blog-row-${blogId} a[title]`);
                title = titleElDom ? titleElDom.getAttribute('title') : '#' + blogId;
            }

            idInput.value = blogId;
            expInput.value = currentStatus;
            titleEl.textContent = title || 'Bài viết #' + blogId;
            noteInput.value = '';

            const firstRadio = document.querySelector('input[name="mod_action_type"][value="rejected"]');
            if (firstRadio) firstRadio.checked = true;

            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            setTimeout(() => {
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
                noteInput.focus();
            }, 10);
        }

        function closeBlogModerationModal() {
            const modal = document.getElementById('admin-blog-moderation-modal');
            const box = document.getElementById('admin-blog-moderation-box');
            if (!modal) return;

            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }, 150);
        }

        function setModerationReasonChip(text) {
            const noteInput = document.getElementById('mod-modal-note');
            if (noteInput) {
                noteInput.value = text;
                noteInput.focus();
            }
        }

        function adminSubmitModerationModal() {
            const blogId = document.getElementById('mod-modal-blog-id')?.value;
            const expectedStatus = document.getElementById('mod-modal-expected-status')?.value;
            const selectedRadio = document.querySelector('input[name="mod_action_type"]:checked');
            const status = selectedRadio ? selectedRadio.value : 'rejected';
            const note = document.getElementById('mod-modal-note')?.value.trim();

            if (!note) {
                if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Chưa Nhập Lý Do', 'Vui lòng nhập lý do từ chối hoặc hướng dẫn sửa để tác giả được biết.', '', 'warning', 'bg-amber-500');
                }
                document.getElementById('mod-modal-note')?.focus();
                return;
            }

            const actionText = status === 'rejected' ? 'từ chối (không duyệt)' : 'yêu cầu chỉnh sửa';
            const actionTitle = status === 'rejected' ? 'Xác Nhận Không Duyệt Bài Viết' : 'Xác Nhận Yêu Cầu Chỉnh Sửa';
            const blogTitle = document.getElementById('mod-modal-blog-title')?.textContent || '#' + blogId;

            closeBlogModerationModal();
            openConfirmModal({
                title: actionTitle,
                message: `Bạn có chắc chắn muốn <b>${actionText}</b> bài viết <b class="text-slate-900 font-bold">"${escapeHtml(blogTitle)}"</b> không?<br><span class="text-slate-500 text-[11px]">Thông báo kèm theo lý do "<i>${escapeHtml(note)}</i>" sẽ được gửi trực tiếp đến tác giả.</span>`,
                icon: status === 'rejected' ? 'cancel' : 'edit_note',
                iconBg: status === 'rejected' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-orange-100 text-orange-800 border border-orange-200',
                confirmText: status === 'rejected' ? 'Xác Nhận Không Duyệt' : 'Gửi Yêu Cầu Sửa',
                confirmClass: status === 'rejected' ? 'btn-enterprise-danger' : 'btn-enterprise-warning',
                confirmIcon: status === 'rejected' ? 'cancel' : 'send',
                onConfirm: () => executeSubmitModeration(blogId, status, note, expectedStatus)
            });
        }

        function executeSubmitModeration(blogId, status, note, expectedStatus) {
            const submitBtn = document.getElementById('btn-submit-blog-moderation');
            const submitText = document.getElementById('btn-submit-blog-moderation-text');
            if (submitBtn) submitBtn.disabled = true;
            if (submitText) submitText.textContent = 'Đang xử lý...';

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const url = `/admin/blog/bai-viet/${blogId}/status`;

            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    status: status,
                    note: note,
                    expected_status: expectedStatus || null
                })
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(data.message || `Lỗi HTTP ${res.status}`);
                return data;
            })
            .then(data => {
                if (data.success) {
                    if (typeof showRealtimeToast === 'function') {
                        const icon = status === 'rejected' ? 'cancel' : 'rule';
                        const color = status === 'rejected' ? 'bg-rose-500' : 'bg-orange-500';
                        showRealtimeToast('Thành Công', data.message || 'Cập nhật trạng thái bài viết thành công!', '', icon, color);
                    }
                    clearAdminPageCache();
                    closeBlogQuickPreviewModal();

                    if (typeof updateRowStatusBadge === 'function') {
                        updateRowStatusBadge(blogId, data.status, data.status_label);
                    }
                    if (typeof updateAdminBlogStats === 'function' && data.stats) {
                        updateAdminBlogStats(data.stats);
                    }

                    const isPendingPage = (new URLSearchParams(window.location.search).get('status') === 'pending') || window.location.pathname.includes('/cho-duyet');
                    if (isPendingPage && data.status !== 'pending') {
                        const rowEl = document.getElementById(`admin-blog-row-${blogId}`);
                        if (rowEl) {
                            rowEl.style.transition = 'all 0.35s ease';
                            rowEl.style.opacity = '0';
                            rowEl.style.transform = 'translateX(20px)';
                            setTimeout(() => {
                                rowEl.remove();
                                const tbody = document.getElementById('admin-blog-tbody');
                                if (tbody && tbody.querySelectorAll('tr').length === 0) {
                                    tbody.innerHTML = `
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">
                                                <span class="material-symbols-outlined text-3xl text-emerald-400 block mb-2">task_alt</span>
                                                Tuyệt vời! Hiện không còn bài viết nào đang chờ duyệt.
                                            </td>
                                        </tr>
                                    `;
                                }
                            }, 350);
                        }
                    }
                } else {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Thao Tác Thất Bại', data.message || 'Có lỗi xảy ra, vui lòng thử lại.', '', 'error', 'bg-rose-500');
                    }
                }
            })
            .catch(err => {
                if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Lỗi Xử Lý', err.message || 'Không thể kết nối đến máy chủ.', '', 'wifi_off', 'bg-rose-500');
                }
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
                if (submitText) submitText.textContent = 'Xác Nhận & Gửi Phản Hồi';
            });
        }

        // Global Blog Delete with Custom Modal
        function adminConfirmDeleteBlog(blogId, blogTitle) {
            openConfirmModal({
                title: 'Xóa Bài Viết',
                message: `Bạn có chắc chắn muốn xóa bài viết <b class="text-slate-900 font-bold">"${escapeHtml(blogTitle || '#' + blogId)}"</b> không?<br><span class="text-rose-600 text-[11px] font-medium">Bài viết sẽ được chuyển vào thùng rác hệ thống.</span>`,
                icon: 'delete',
                iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
                confirmText: 'Xác Nhận Xóa',
                confirmClass: 'btn-enterprise-danger',
                confirmIcon: 'delete',
                onConfirm: () => executeGlobalBlogDelete(blogId)
            });
        }

        function executeGlobalBlogDelete(blogId) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const url = `/admin/blog/bai-viet/${blogId}`;
            const rowEl = document.getElementById(`admin-blog-row-${blogId}`);

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(data.message || `Lỗi máy chủ HTTP ${res.status}`);
                return data;
            })
            .then(data => {
                if (data.success) {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Đã Xóa', data.message || 'Bài viết đã được xóa thành công.', '', 'delete', 'bg-slate-700');
                    }
                    clearAdminPageCache();
                    closeBlogQuickPreviewModal();

                    if (rowEl) {
                        rowEl.style.transition = 'all 0.35s ease';
                        rowEl.style.opacity = '0';
                        rowEl.style.transform = 'translateX(20px)';
                        setTimeout(() => {
                            rowEl.remove();
                            const tbody = document.getElementById('admin-blog-tbody');
                            if (tbody && tbody.querySelectorAll('tr').length === 0) {
                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">
                                            <span class="material-symbols-outlined text-3xl text-gray-300 block mb-2">article</span>
                                            Không còn bài viết nào trong danh sách.
                                        </td>
                                    </tr>
                                `;
                            }
                        }, 350);
                    }

                    if (data.stats && typeof updateAdminBlogStats === 'function') {
                        updateAdminBlogStats(data.stats);
                    }
                } else {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Xóa Thất Bại', data.message || 'Không thể xóa bài viết này.', '', 'error', 'bg-rose-500');
                    }
                }
            })
            .catch(err => {
                if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Lỗi Kết Nối', err.message || 'Không thể kết nối đến máy chủ.', '', 'wifi_off', 'bg-rose-500');
                }
            });
        }

        // Venue Pending Deletion
        function confirmDeletePendingQuan(id, name) {
            openConfirmModal({
                title: 'Xóa Hồ Sơ Quán Chờ Duyệt',
                message: `Bạn có chắc chắn muốn xóa hồ sơ quán <b class="text-slate-900 font-bold">"${escapeHtml(name)}"</b> không?<br><span class="text-rose-600 text-[11px] font-medium">Hồ sơ quán này sẽ bị khóa và chuyển vào thùng rác.</span>`,
                icon: 'delete',
                iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
                confirmText: 'Xác Nhận Xóa',
                confirmClass: 'btn-enterprise-danger',
                confirmIcon: 'delete',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/quan/${id}`, 'DELETE', 'quan-row-' + id)
            });
        }

        // Global Action Helpers for Approval / Rejection
        function confirmApproveQuan(id, name) {
            openConfirmModal({
                title: 'Xác Nhận Duyệt Quán Ăn',
                message: `Bạn có chắc chắn muốn phê duyệt quán <b class="text-slate-900 font-bold">"${name}"</b> không?<br><span class="text-slate-500 text-[11px]">Quán sẽ được cấp quyền kinh doanh và hiển thị công khai ngay lập tức.</span>`,
                icon: 'task_alt',
                iconBg: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                confirmText: 'Đồng Ý Duyệt',
                confirmClass: 'btn-enterprise-success',
                confirmIcon: 'check_circle',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/quan/${id}`, 'PUT', 'quan-row-' + id)
            });
        }

        function confirmRejectQuan(id, name) {
            openConfirmModal({
                title: 'Từ Chối Duyệt Quán Ăn',
                message: `Bạn có chắc chắn muốn từ chối hồ sơ quán <b class="text-slate-900 font-bold">"${name}"</b> không?<br><span class="text-rose-600 text-[11px]">Hồ sơ quán này sẽ chuyển sang trạng thái bị từ chối / khóa.</span>`,
                icon: 'cancel',
                iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
                confirmText: 'Không Duyệt Quán',
                confirmClass: 'btn-enterprise-danger',
                confirmIcon: 'block',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/quan/${id}/reject`, 'PUT', 'quan-row-' + id)
            });
        }

        function confirmVerifyUser(id, name) {
            openConfirmModal({
                title: 'Xác Nhận Xác Thực Tài Khoản',
                message: `Bạn có chắc chắn muốn xác thực tài khoản cho người dùng <b class="text-slate-900 font-bold">"${name}"</b> không?<br><span class="text-slate-500 text-[11px]">Tài khoản sẽ được gắn huy hiệu đã xác thực đầy đủ.</span>`,
                icon: 'verified',
                iconBg: 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                confirmText: 'Xác Thực Ngay',
                confirmClass: 'btn-enterprise-indigo',
                confirmIcon: 'verified',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/user/${id}`, 'PUT', 'user-row-' + id)
            });
        }

        function confirmRejectUser(id, name) {
            openConfirmModal({
                title: 'Từ Chối Xác Thực Người Dùng',
                message: `Bạn có chắc chắn không duyệt xác thực cho người dùng <b class="text-slate-900 font-bold">"${name}"</b> không?`,
                icon: 'person_off',
                iconBg: 'bg-slate-200 text-slate-800 border border-slate-300',
                confirmText: 'Không Duyệt',
                confirmClass: 'btn-enterprise-danger',
                confirmIcon: 'block',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/user/${id}/reject`, 'PUT', 'user-row-' + id)
            });
        }

        function confirmReapproveQuan(id, name) {
            openConfirmModal({
                title: 'Phê Duyệt Lại Quán Ăn',
                message: `Bạn có chắc chắn muốn duyệt lại hồ sơ quán <b class="text-slate-900 font-bold">"${name}"</b> không?<br><span class="text-slate-500 text-[11px]">Quán sẽ được chuyển sang trạng thái đã duyệt và hiển thị công khai trên hệ thống.</span>`,
                icon: 'published_with_changes',
                iconBg: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                confirmText: 'Duyệt Lại Quán',
                confirmClass: 'btn-enterprise-success',
                confirmIcon: 'check_circle',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/quan/${id}`, 'PUT', 'rejected-row-' + id)
            });
        }

        function confirmDeleteRejectedQuan(id, name) {
            openConfirmModal({
                title: 'Xóa Hồ Sơ Quán Ăn',
                message: `Bạn có chắc chắn muốn xóa quán <b class="text-slate-900 font-bold">"${name}"</b> khỏi danh sách từ chối không?<br><span class="text-rose-600 text-[11px]">Hồ sơ quán này sẽ được chuyển vào thùng rác hệ thống.</span>`,
                icon: 'delete_forever',
                iconBg: 'bg-rose-100 text-rose-800 border border-rose-200',
                confirmText: 'Xác Nhận Xóa',
                confirmClass: 'btn-enterprise-danger',
                confirmIcon: 'delete',
                onConfirm: () => executeActionAjax(`/admin/duyet-xac-thuc/quan/${id}`, 'DELETE', 'rejected-row-' + id)
            });
        }

        // Global synchronization for stats counters
        function updateAllStatsCounters(stats) {
            if (!stats) return;
            const fields = [
                ['stat-count-pending', 'tab-badge-pending', 'pane-count-pending', stats.pending_quan],
                ['stat-count-rejected', 'tab-badge-rejected', 'pane-count-rejected', stats.rejected_quan],
                ['stat-count-approved', 'tab-badge-approved', 'pane-count-approved', stats.approved_quan],
                ['stat-count-users', 'tab-badge-users', 'pane-count-users', stats.unverified_users],
            ];
            fields.forEach(([statId, tabId, paneId, val]) => {
                if (val !== undefined && val !== null) {
                    const elStat = document.getElementById(statId);
                    const elTab = document.getElementById(tabId);
                    const elPane = document.getElementById(paneId);
                    if (elStat) elStat.textContent = val;
                    if (elTab) elTab.textContent = val;
                    if (elPane) elPane.textContent = val;
                }
            });

            // Cập nhật huy hiệu thông báo Header & Sidebar theo số lượng thông báo CHƯA ĐỌC
            const unreadCount = (stats.unread_count !== undefined && stats.unread_count !== null)
                ? parseInt(stats.unread_count, 10)
                : parseInt(document.getElementById('realtime-badge-count')?.textContent || '0', 10);

            const badgeCount = document.getElementById('realtime-badge-count');
            const badgeDropdown = document.getElementById('realtime-badge-dropdown');
            if (badgeCount) {
                if (unreadCount > 0) {
                    badgeCount.textContent = (unreadCount > 99 ? '99+' : unreadCount);
                    badgeCount.classList.remove('hidden');
                } else {
                    badgeCount.classList.add('hidden');
                    badgeCount.textContent = '0';
                }
            }
            if (badgeDropdown) {
                badgeDropdown.textContent = (unreadCount > 0 ? unreadCount : 0) + ' mới';
            }
            document.querySelectorAll('.sidebar-notif-badge').forEach(b => {
                if (unreadCount > 0) {
                    b.textContent = (unreadCount > 99 ? '99+' : unreadCount);
                    b.classList.remove('hidden');
                } else {
                    b.classList.add('hidden');
                    b.textContent = '0';
                }
            });

            if (stats.pending_blog !== undefined) {
                document.querySelectorAll('.sidebar-blog-pending-badge').forEach(b => {
                    const badgeText = b.querySelector('.badge-text');
                    if (stats.pending_blog > 0) {
                        if (badgeText) badgeText.textContent = stats.pending_blog + ' cần duyệt';
                        b.classList.remove('hidden');
                    } else {
                        b.classList.add('hidden');
                    }
                });
            }

            if (stats.pending_quan !== undefined) {
                document.querySelectorAll('.sidebar-quan-pending-badge').forEach(b => {
                    const badgeText = b.querySelector('.badge-text');
                    if (stats.pending_quan > 0) {
                        if (badgeText) badgeText.textContent = stats.pending_quan + ' cần duyệt';
                        b.classList.remove('hidden');
                    } else {
                        b.classList.add('hidden');
                    }
                });
            }
        }

        function executeActionAjax(url, method, rowId) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            let tr = rowId ? document.getElementById(rowId) : null;
            if (!tr && rowId) {
                const cleanId = String(rowId).replace(/^(quan-row-|pending-row-|rejected-row-|user-row-|blog-row-)/, '');
                tr = document.getElementById('quan-row-' + cleanId)
                    || document.getElementById('rejected-row-' + cleanId)
                    || document.getElementById('pending-row-' + cleanId)
                    || document.getElementById('user-row-' + cleanId)
                    || document.getElementById('admin-blog-row-' + cleanId);
            }

            const formData = new FormData();
            formData.append('_token', csrf);
            formData.append('_method', method);

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(data.message || `Lỗi máy chủ HTTP ${res.status}`);
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Hoàn Tất', data.message || 'Thao tác thành công', '', 'check_circle', 'bg-emerald-500');
                    }
                    clearAdminPageCache();

                    if (data.stats) {
                        updateAllStatsCounters(data.stats);
                    }

                    if (tr) {
                        const tbody = tr.closest('tbody');
                        tr.style.transition = 'all 0.35s ease';
                        tr.style.opacity = '0';
                        tr.style.transform = 'translateX(20px)';
                        setTimeout(() => {
                            tr.remove();
                            if (tbody && tbody.querySelectorAll('tr').length === 0) {
                                let emptyMsg = 'Không có dữ liệu trong danh sách.';
                                let colSpan = 5;
                                if (tbody.id === 'pending-quan-tbody') {
                                    emptyMsg = 'Tuyệt vời! Hiện không còn quán nào đang chờ duyệt.';
                                } else if (tbody.id === 'rejected-quan-tbody') {
                                    emptyMsg = 'Không có quán nào trong danh sách bị từ chối / không duyệt.';
                                } else if (tbody.id === 'approved-quan-tbody') {
                                    emptyMsg = 'Chưa có quán nào được duyệt gần đây.';
                                } else if (tbody.id === 'unverified-users-tbody') {
                                    emptyMsg = 'Tất cả tài khoản người dùng đều đã xác thực.';
                                    colSpan = 4;
                                }
                                tbody.innerHTML = `<tr><td colspan="${colSpan}" class="p-8 text-center text-xs text-slate-500 font-medium">${emptyMsg}</td></tr>`;
                            }
                            if (typeof updateDuyetCounts === 'function') {
                                updateDuyetCounts();
                            }
                        }, 350);
                    }

                    if (typeof checkRealtimeAdminNotifications === 'function') {
                        checkRealtimeAdminNotifications();
                    }
                } else {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Thao Tác Thất Bại', data.message || 'Có lỗi xảy ra, vui lòng thử lại.', '', 'error', 'bg-rose-500');
                    } else {
                        alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                    }
                }
            })
            .catch(err => {
                const msg = (err && err.message) ? err.message : 'Lỗi kết nối máy chủ, vui lòng kiểm tra mạng và thử lại!';
                if (typeof showRealtimeToast === 'function') {
                    showRealtimeToast('Thao Tác Thất Bại', msg, '', 'error', 'bg-rose-500');
                } else {
                    alert(msg);
                }
            });
        }
        // Real-time Notification Engine
        let lastSeenQuanKey = null;
        let lastSeenBlogKey = null;
        let isNotifInit = false;
        let currentDropdownFilter = 'all';
        window.__allNotifications = {!! json_encode($adminInitialNotifications ?? [], JSON_UNESCAPED_UNICODE) !!};
        window.__lastSeenTopNotifId = null;
        window.__adminJustMarkedAllReadAt = null;

        function toggleNotifDropdown() {
            const dropdown = document.getElementById('admin-notif-dropdown');
            if (!dropdown) return;

            const isOpening = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden');

            if (isOpening) {
                // Hành vi chuẩn YouTube: Khi nhấn vào nút thông báo thì ngay lập tức xóa số lượng thông báo chưa đọc
                const badgeCount = document.getElementById('realtime-badge-count');
                const badgeDropdown = document.getElementById('realtime-badge-dropdown');

                // 1. Tức thì ẩn huy hiệu số thông báo trên nút chuông và sidebar
                if (badgeCount) {
                    badgeCount.classList.add('hidden');
                    badgeCount.textContent = '0';
                }
                if (badgeDropdown) {
                    badgeDropdown.textContent = '0 mới';
                }
                document.querySelectorAll('.sidebar-notif-badge').forEach(b => {
                    b.classList.add('hidden');
                    b.textContent = '0';
                });

                // 2. Đánh dấu tất cả thông báo hiện tại trong bộ nhớ là đã đọc (bỏ chấm vàng chưa đọc)
                if (window.__allNotifications && Array.isArray(window.__allNotifications)) {
                    window.__allNotifications.forEach(n => { n.read = true; });
                    renderDropdownList(window.__allNotifications);
                }

                // 3. Ghi nhớ thời điểm đánh dấu đọc để tránh race condition với realtime check
                window.__adminJustMarkedAllReadAt = Date.now();

                // 4. Gửi yêu cầu lưu trạng thái đã đọc lên máy chủ ngầm (keepalive)
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                try {
                    fetch('{{ route("admin.thong-bao.mark-all-read") }}', {
                        method: 'POST',
                        keepalive: true,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).catch(() => {});
                } catch (e) {}
            }
        }

        document.addEventListener('click', function(e) {
            const bell = document.getElementById('admin-notif-bell');
            const dropdown = document.getElementById('admin-notif-dropdown');
            if (bell && dropdown && !bell.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function filterDropdownNotifs(filterType) {
            currentDropdownFilter = filterType;
            document.querySelectorAll('#admin-notif-dropdown .notif-tab-btn').forEach(btn => {
                if (btn.getAttribute('data-filter') === filterType) {
                    btn.className = 'notif-tab-btn active px-2.5 py-1 rounded-lg font-bold bg-slate-900 text-white transition-colors';
                } else {
                    btn.className = 'notif-tab-btn px-2.5 py-1 rounded-lg font-medium text-slate-600 hover:bg-slate-200 transition-colors';
                }
            });
            renderDropdownList(window.__allNotifications);
        }

        function handleAdminNotifClick(event, id, targetUrl) {
            if (event) {
                event.preventDefault();
            }

            // 1. Cập nhật trạng thái đã đọc cục bộ
            if (window.__allNotifications && Array.isArray(window.__allNotifications)) {
                const item = window.__allNotifications.find(n => String(n.id) === String(id));
                if (item) item.read = true;
            }

            // 2. Giảm ngay số lượng hiển thị trên badge, nếu về 0 thì ẩn hoàn toàn
            const badgeCount = document.getElementById('realtime-badge-count');
            const badgeDropdown = document.getElementById('realtime-badge-dropdown');
            let current = parseInt(badgeCount?.textContent || '0', 10);
            let next = Math.max(0, current - 1);

            if (badgeCount) {
                if (next > 0) {
                    badgeCount.textContent = (next > 99 ? '99+' : next);
                    badgeCount.classList.remove('hidden');
                } else {
                    badgeCount.classList.add('hidden');
                    badgeCount.textContent = '0';
                }
            }
            if (badgeDropdown) {
                badgeDropdown.textContent = (next > 0 ? next : 0) + ' mới';
            }
            document.querySelectorAll('.sidebar-notif-badge').forEach(b => {
                if (next > 0) {
                    b.textContent = (next > 99 ? '99+' : next);
                    b.classList.remove('hidden');
                } else {
                    b.classList.add('hidden');
                    b.textContent = '0';
                }
            });

            // 3. Gửi request đánh dấu đã đọc lên máy chủ ngầm (keepalive: true để không bị gián đoạn)
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                fetch(`/admin/thong-bao/${encodeURIComponent(id)}/mark-read`, {
                    method: 'POST',
                    keepalive: true,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).catch(() => {});
            } catch(e) {}

            // 4. Chuyển ngay đến trang nội dung thông báo
            if (targetUrl) {
                window.location.href = targetUrl;
            }
        }

        function markAllAdminNotifsReadDropdown(event) {
            if (event) event.preventDefault();

            // Ẩn toàn bộ huy hiệu thông báo ngay lập tức
            const badgeCount = document.getElementById('realtime-badge-count');
            const badgeDropdown = document.getElementById('realtime-badge-dropdown');
            if (badgeCount) {
                badgeCount.classList.add('hidden');
                badgeCount.textContent = '0';
            }
            if (badgeDropdown) {
                badgeDropdown.textContent = '0 mới';
            }
            document.querySelectorAll('.sidebar-notif-badge').forEach(b => {
                b.classList.add('hidden');
                b.textContent = '0';
            });

            // Đánh dấu tất cả thông báo hiện tại là đã đọc trong bộ nhớ
            if (window.__allNotifications && Array.isArray(window.__allNotifications)) {
                window.__allNotifications.forEach(n => { n.read = true; });
                renderDropdownList(window.__allNotifications);
            }

            // Gửi API lên máy chủ
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch('{{ route("admin.thong-bao.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token || '',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast('Đã Đọc Tất Cả', 'Toàn bộ thông báo đã được đánh dấu là đã đọc thành công.', '', 'done_all', 'bg-emerald-500');
                    }
                }
            })
            .catch(() => {});
        }

        function renderDropdownList(notifications) {
            const list = document.getElementById('realtime-notif-list');
            if (!list) return;

            const notifs = notifications || [];
            const filtered = (currentDropdownFilter === 'all')
                ? notifs
                : notifs.filter(n => n.type === currentDropdownFilter);

            if (!filtered || filtered.length === 0) {
                list.innerHTML = `
                    <div class="p-6 text-center text-slate-400 font-medium text-xs">
                        <span class="material-symbols-outlined text-2xl text-slate-300 block mb-1">notifications_off</span>
                        Không có thông báo nào trong mục này.
                    </div>
                `;
                return;
            }

            list.innerHTML = filtered.map(item => `
                <a href="${item.url}" onclick="handleAdminNotifClick(event, '${item.id}', '${item.url}')" class="flex items-start gap-3 p-2.5 hover:bg-slate-50 rounded-xl transition-colors border-b border-slate-50 last:border-0 group ${item.read ? 'opacity-70' : ''}">
                    <div class="w-8 h-8 rounded-lg ${item.badge_class || 'bg-slate-100 text-slate-700'} flex items-center justify-center flex-shrink-0 font-bold group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-[18px]">${item.icon || 'notifications'}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">${item.category_label}</span>
                            <div class="flex items-center gap-1.5">
                                ${!item.read ? '<span class="w-2 h-2 rounded-full bg-amber-500 shrink-0" title="Chưa đọc"></span>' : ''}
                                <span class="text-[10px] text-slate-400 whitespace-nowrap">${item.time_ago || ''}</span>
                            </div>
                        </div>
                        <p class="text-[11px] font-bold text-slate-800 truncate group-hover:text-amber-700">${item.title}</p>
                        <p class="text-[10px] text-slate-500 truncate">${item.description || ''}</p>
                    </div>
                </a>
            `).join('');
        }

        // Tải hiển thị dữ liệu thông báo ban đầu ngay lập tức
        if (window.__allNotifications && window.__allNotifications.length > 0) {
            renderDropdownList(window.__allNotifications);
        }

        // Web Audio Synthesizer Chime
        function playChimeSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            } catch (e) {
                // Audio context not allowed until interaction
            }
        }

        function showRealtimeToast(title, body, url, iconName = 'notifications_active', badgeBg = 'bg-amber-500') {
            const container = document.getElementById('realtime-toast-container');
            if (!container) return;

            playChimeSound();

            let bgHex = '#10b981'; // Emerald (mặc định)
            let shadowHex = 'rgba(16, 185, 129, 0.45)';

            if (iconName === 'error' || iconName === 'close' || iconName === 'block' || badgeBg.includes('rose') || badgeBg.includes('red')) {
                bgHex = '#f43f5e'; // Rose
                shadowHex = 'rgba(244, 63, 94, 0.45)';
            } else if (iconName === 'wifi_off' || iconName === 'warning' || badgeBg.includes('amber')) {
                bgHex = '#f59e0b'; // Amber
                shadowHex = 'rgba(245, 158, 11, 0.45)';
            } else if (badgeBg.includes('blue') || iconName === 'info' || iconName === 'visibility_off') {
                bgHex = '#3b82f6'; // Bright Blue
                shadowHex = 'rgba(59, 130, 246, 0.45)';
            } else if (badgeBg.includes('emerald') || iconName === 'check_circle' || iconName === 'check') {
                bgHex = '#10b981'; // Emerald
                shadowHex = 'rgba(16, 185, 129, 0.45)';
            }

            const toast = document.createElement('div');
            toast.className = 'bg-slate-900 text-white rounded-2xl p-4 shadow-2xl border border-slate-700/80 flex items-start gap-3.5 transform translate-x-full transition-all duration-300 ease-out hover:border-amber-400/80 ' + (url && url !== '#' ? 'cursor-pointer' : '');
            if (url && url !== '#') {
                toast.onclick = () => window.location.href = url;
            }
            toast.innerHTML = `
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 font-bold shadow-lg transition-transform hover:scale-105" style="background-color: ${bgHex} !important; box-shadow: 0 4px 14px ${shadowHex};">
                    <span class="material-symbols-outlined text-[22px]" style="color: #ffffff !important; font-variation-settings: 'FILL' 1;">${iconName}</span>
                </div>
                <div class="flex-1 min-w-0 pr-1">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">notifications_active</span>
                            <span>THÔNG BÁO MỚI</span>
                        </span>
                        <span class="text-[10px] text-slate-400 whitespace-nowrap">Vừa xong</span>
                    </div>
                    <h4 class="text-xs font-bold text-white truncate">${title}</h4>
                    <p class="text-[11px] text-slate-300 mt-0.5 line-clamp-2 leading-relaxed">${body}</p>
                </div>
                <button onclick="event.stopPropagation(); this.parentElement.remove()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors shrink-0 -mr-1 -mt-1 cursor-pointer" title="Đóng thông báo">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            `;

            container.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-x-full'), 50);

            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 8000);
        }

        function checkRealtimeAdminNotifications() {
            fetch('{{ route("admin.realtime-check") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) return;

                let unreadCount = parseInt(data.unread_count ?? 0, 10);
                if (window.__adminJustMarkedAllReadAt && (Date.now() - window.__adminJustMarkedAllReadAt < 4000)) {
                    unreadCount = 0;
                }
                const badgeCount = document.getElementById('realtime-badge-count');
                const badgeDropdown = document.getElementById('realtime-badge-dropdown');

                if (badgeCount) {
                    if (unreadCount > 0) {
                        badgeCount.textContent = (unreadCount > 99 ? '99+' : unreadCount);
                        badgeCount.classList.remove('hidden');
                    } else {
                        badgeCount.classList.add('hidden');
                        badgeCount.textContent = '0';
                    }
                }

                if (badgeDropdown) {
                    badgeDropdown.textContent = (unreadCount > 0 ? unreadCount : 0) + ' mới';
                }

                // Cập nhật tất cả badge thông báo tổng (cả desktop & mobile sidebar)
                document.querySelectorAll('.sidebar-notif-badge').forEach(b => {
                    if (unreadCount > 0) {
                        b.textContent = (unreadCount > 99 ? '99+' : unreadCount);
                        b.classList.remove('hidden');
                    } else {
                        b.classList.add('hidden');
                        b.textContent = '0';
                    }
                });

                // Cập nhật huy hiệu "Cần duyệt" cho mục Duyệt Bài Viết (cả desktop & mobile)
                if (data.pending_blog !== undefined) {
                    document.querySelectorAll('.sidebar-blog-pending-badge').forEach(b => {
                        const badgeText = b.querySelector('.badge-text');
                        if (data.pending_blog > 0) {
                            if (badgeText) badgeText.textContent = data.pending_blog + ' cần duyệt';
                            b.classList.remove('hidden');
                        } else {
                            b.classList.add('hidden');
                        }
                    });
                }

                // Cập nhật huy hiệu "Cần duyệt" cho mục Quán Chờ Duyệt (cả desktop & mobile)
                if (data.pending_quan !== undefined) {
                    document.querySelectorAll('.sidebar-quan-pending-badge').forEach(b => {
                        const badgeText = b.querySelector('.badge-text');
                        if (data.pending_quan > 0) {
                            if (badgeText) badgeText.textContent = data.pending_quan + ' cần duyệt';
                            b.classList.remove('hidden');
                        } else {
                            b.classList.add('hidden');
                        }
                    });
                }

                // Cập nhật huy hiệu "Cần duyệt" cho mục Video TikTok / Shorts
                if (data.pending_video !== undefined) {
                    document.querySelectorAll('.sidebar-video-pending-badge').forEach(b => {
                        const badgeText = b.querySelector('.badge-text') || b;
                        if (data.pending_video > 0) {
                            badgeText.textContent = data.pending_video;
                            b.classList.remove('hidden');
                        } else {
                            b.classList.add('hidden');
                        }
                    });
                }

                // Cập nhật thống kê bài viết trên màn hình quản lý bài viết nếu đang hiển thị
                const statBlogPending = document.getElementById('stat-blog-pending');
                if (statBlogPending && data.pending_blog !== undefined) statBlogPending.textContent = data.pending_blog;
                const statBlogTotal = document.getElementById('stat-blog-total');
                if (statBlogTotal && data.total_blogs !== undefined) statBlogTotal.textContent = data.total_blogs;

                // Render toàn bộ thông báo vào dropdown bell (sử dụng all_notifications để không bị rỗng sau khi đánh dấu đọc)
                window.__allNotifications = (data.all_notifications && data.all_notifications.length > 0)
                    ? data.all_notifications
                    : (data.notifications || []);

                if (window.__adminJustMarkedAllReadAt && (Date.now() - window.__adminJustMarkedAllReadAt < 4000)) {
                    window.__allNotifications.forEach(n => { n.read = true; });
                }

                renderDropdownList(window.__allNotifications);

                // Phát tín hiệu cập nhật toàn bộ hệ thống cho các trang con (Duyệt xác thực, Thông báo...)
                window.dispatchEvent(new CustomEvent('admin:notifications-updated', { detail: data }));

                // Toast & chime notification nếu có bài viết mới được gửi duyệt
                if (data.latest_blog) {
                    if (isNotifInit && window.__lastSeenBlogId && window.__lastSeenBlogId !== data.latest_blog.id) {
                        showRealtimeToast(
                            'Bài Viết Chờ Phê Duyệt',
                            `"${data.latest_blog.title}" của ${data.latest_blog.author || 'Người dùng'} vừa gửi duyệt`,
                            data.latest_blog.detail_url || data.latest_blog.url,
                            'article',
                            'bg-amber-400'
                        );
                    }
                    window.__lastSeenBlogId = data.latest_blog.id;
                }

                // Toast & chime notification nếu có quán mới gửi duyệt
                if (data.latest_quan) {
                    if (isNotifInit && window.__lastSeenQuanKey && window.__lastSeenQuanKey !== data.latest_quan.id) {
                        showRealtimeToast(
                            'Quán Ăn Chờ Phê Duyệt',
                            `Hồ sơ quán "${data.latest_quan.ten_quan}" vừa đăng ký mở bán`,
                            data.latest_quan.url,
                            'storefront',
                            'bg-amber-400'
                        );
                    }
                    window.__lastSeenQuanKey = data.latest_quan.id;
                }

                // Toast & chime notification nếu có video review mới chờ duyệt
                if (data.latest_video) {
                    if (isNotifInit && window.__lastSeenVideoId && window.__lastSeenVideoId !== data.latest_video.id) {
                        showRealtimeToast(
                            'Video Review Chờ Phê Duyệt',
                            `Quán "${data.latest_video.quan_name}" vừa gửi video review mới`,
                            data.latest_video.url,
                            'smart_display',
                            'bg-amber-500'
                        );
                    }
                    window.__lastSeenVideoId = data.latest_video.id;
                }

                // Toast & chime notification tổng quát
                if (data.notifications && data.notifications.length > 0) {
                    const topNotif = data.notifications[0];
                    if (isNotifInit && window.__lastSeenTopNotifId && window.__lastSeenTopNotifId !== topNotif.id && (!window.__lastSeenBlogId || topNotif.id !== 'blog_' + window.__lastSeenBlogId) && (!window.__lastSeenQuanKey || topNotif.id !== 'quan_' + window.__lastSeenQuanKey)) {
                        showRealtimeToast(
                            topNotif.category_label + ': ' + topNotif.title,
                            topNotif.description || 'Có thông báo mới cần xử lý',
                            topNotif.url,
                            topNotif.icon || 'notifications_active',
                            topNotif.type === 'quan' ? 'bg-amber-400' : (topNotif.type === 'blog' ? 'bg-blue-400' : (topNotif.type === 'user' ? 'bg-indigo-400' : 'bg-emerald-400'))
                        );
                    }
                    window.__lastSeenTopNotifId = topNotif.id;
                }

                // Nếu admin đang ở trang Duyệt Quán, chèn quán mới trực tiếp vào DOM không reload
                const tbody = document.getElementById('pending-quan-tbody');
                if (tbody && data.latest_quan && !document.getElementById('quan-row-' + data.latest_quan.id)) {
                    const emptyRow = document.getElementById('pending-quan-empty-row');
                    if (emptyRow) emptyRow.remove();

                    const newRow = document.createElement('tr');
                    newRow.id = 'quan-row-' + data.latest_quan.id;
                    newRow.className = 'duyet-table-row bg-amber-100/90 transition-all duration-1000 animate-pulse';
                    
                    const qData = data.latest_quan;
                    window.__venueMediaData = window.__venueMediaData || {};
                    window.__venueMediaData[qData.id] = qData;

                    const ownerName = qData.owner_name || (qData.nguoi_dang ? qData.nguoi_dang.ho_ten : 'Chưa xác định');
                    const ownerEmail = qData.owner_email || (qData.nguoi_dang ? qData.nguoi_dang.email : 'Chưa có Email');
                    const ownerPhone = qData.owner_phone || (qData.nguoi_dang ? (qData.nguoi_dang.so_dien_thoai || 'Chưa có SĐT') : '');
                    const quanAddr = qData.dia_chi || 'Chưa cập nhật';
                    const anhBia = qData.anh_bia_url || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&auto=format&fit=crop&q=80';
                    const safeName = (qData.ten_quan || '').replace(/'/g, "\\'");
                    const galleryCount = (qData.gallery && qData.gallery.length > 0) ? (qData.gallery.length + 1) : 1;
                    const loaiHinh = (qData.loai_hinh_kinh_doanh || qData.loai_hinh) 
                        ? `<span class="text-[11px] text-slate-500 font-medium block mt-0.5">🏷️ ${qData.loai_hinh_kinh_doanh || qData.loai_hinh}</span>` 
                        : '';

                    newRow.setAttribute('data-search', `${qData.ten_quan} ${ownerName} ${quanAddr} ${qData.loai_hinh_kinh_doanh || ''}`.toLowerCase());

                    newRow.innerHTML = `
                        <td class="px-3.5 py-3 font-medium overflow-hidden">
                            <div class="flex items-center gap-3 min-w-0">
                                <div onclick="openVenueMediaModal(window.__venueMediaData['${qData.id}'])" class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200/90 cursor-pointer group relative shadow-2xs hover:ring-2 hover:ring-amber-500 hover:border-amber-400 transition-all" title="Bấm để xem ảnh và thẩm định chi tiết">
                                    <img src="${anhBia}" alt="${qData.ten_quan}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&auto=format&fit=crop&q=80';">
                                    <div class="absolute inset-0 bg-black/35 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white">
                                        <span class="material-symbols-outlined text-[16px]">zoom_in</span>
                                    </div>
                                    <span class="absolute bottom-0.5 right-0.5 bg-black/70 backdrop-blur-xs text-white text-[8px] font-bold px-1 py-0.2 rounded">
                                        ${galleryCount}
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 overflow-hidden space-y-0.5">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping shrink-0"></span>
                                        <span class="font-bold text-slate-900 text-xs truncate" title="${safeName}">
                                            ${qData.ten_quan}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-300 shrink-0 whitespace-nowrap">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Chờ duyệt</span>
                                        </span>
                                    </div>
                                    ${loaiHinh ? `<div class="truncate text-[10px] text-slate-500" title="${qData.loai_hinh_kinh_doanh || qData.loai_hinh}">🏷️ ${qData.loai_hinh_kinh_doanh || qData.loai_hinh}</div>` : ''}
                                    <div class="flex items-center gap-1 pt-0.5 flex-nowrap">
                                        <button type="button" onclick="openVenueMediaModal(window.__venueMediaData['${qData.id}'])" class="btn-enterprise btn-enterprise-photo btn-action-compact" title="Xem bộ sưu tập hình ảnh quán">
                                            <span class="material-symbols-outlined text-[13px]">photo_library</span>
                                            <span>Ảnh (${galleryCount})</span>
                                        </button>
                                        ${qData.slug ? `
                                            <a href="/quan/${qData.slug}" class="btn-enterprise btn-enterprise-secondary btn-action-compact" title="Xem trang quán thực tế ngoài website">
                                                <span>Trang quán</span>
                                                <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                                            </a>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3.5 py-3 overflow-hidden">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-full bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                    ${ownerName ? ownerName.charAt(0).toUpperCase() : 'U'}
                                </div>
                                <div class="min-w-0 flex-1 space-y-0.5 overflow-hidden">
                                    <div class="font-bold text-slate-900 truncate text-xs block" title="${ownerName}">${ownerName}</div>
                                    <div class="text-[11px] text-slate-600 font-semibold truncate flex items-center gap-0.5" title="${ownerPhone}">
                                        <span class="material-symbols-outlined text-[12px] text-slate-400 shrink-0">call</span>
                                        <span class="truncate">${ownerPhone}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 truncate font-mono block" title="${ownerEmail}">📧 ${ownerEmail}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3.5 py-3 text-slate-600 overflow-hidden">
                            <div class="space-y-0.5 min-w-0">
                                <div class="font-semibold text-slate-800 text-xs truncate" title="${quanAddr}">
                                    ${quanAddr || 'Chưa cập nhật chi tiết'}
                                </div>
                                ${qData.ten_tinh_thanh ? `
                                    <div class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-1.5 py-0.2 rounded max-w-full overflow-hidden" title="${qData.ten_tinh_thanh}">
                                        <span class="material-symbols-outlined text-[12px] text-slate-400 shrink-0">location_on</span>
                                        <span class="truncate">${qData.ten_tinh_thanh}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </td>
                        <td class="px-2 py-2 text-emerald-600 font-bold whitespace-nowrap overflow-hidden">
                            <div class="whitespace-nowrap font-bold text-xs">Vừa xong</div>
                            <div class="text-[10px] text-slate-400 font-normal whitespace-nowrap">Mới đăng</div>
                        </td>
                        <td class="px-1.5 py-1.5 text-right whitespace-nowrap">
                            <div class="grid grid-cols-2 gap-1 w-[150px] ml-auto">
                                {{-- Hàng 1, Cột 1: Duyệt --}}
                                <button type="button" onclick="confirmApproveQuan('${qData.id}', '${safeName}')" class="btn-enterprise btn-enterprise-success btn-action-compact font-bold shadow-2xs w-full justify-center text-[10.5px] py-1 px-1" title="Phê duyệt hồ sơ quán này">
                                    <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                    <span>Duyệt</span>
                                </button>
                                {{-- Hàng 1, Cột 2: Không duyệt --}}
                                <button type="button" onclick="confirmRejectQuan('${qData.id}', '${safeName}')" class="btn-enterprise btn-enterprise-danger btn-action-compact font-semibold shadow-2xs w-full justify-center text-[10.5px] py-1 px-1" title="Không duyệt hồ sơ quán này">
                                    <span class="material-symbols-outlined text-[13px]">cancel</span>
                                    <span>Không duyệt</span>
                                </button>
                                {{-- Hàng 2, Cột 1: Xem --}}
                                <button type="button" onclick="openVenueMediaModal(window.__venueMediaData['${qData.id}'])" class="btn-enterprise btn-enterprise-secondary btn-action-compact font-semibold w-full justify-center text-[10.5px] py-1 px-1" title="Xem chi tiết hồ sơ & thẩm định ảnh">
                                    <span class="material-symbols-outlined text-[13px]">visibility</span>
                                    <span>Xem</span>
                                </button>
                                {{-- Hàng 2, Cột 2: Xóa --}}
                                <button type="button" onclick="confirmDeletePendingQuan('${qData.id}', '${safeName}')" class="btn-enterprise btn-enterprise-secondary btn-action-compact text-gray-500 hover:text-rose-600 hover:bg-rose-50 border-gray-200 w-full justify-center text-[10.5px] py-1 px-1" title="Xóa bỏ quán khỏi danh sách chờ duyệt">
                                    <span class="material-symbols-outlined text-[13px]">delete</span>
                                    <span>Xóa</span>
                                </button>
                            </div>
                        </td>
                    `;

                    tbody.insertBefore(newRow, tbody.firstChild);

                    if (data.pending_quan !== undefined && typeof updateAllStatsCounters === 'function') {
                        updateAllStatsCounters({
                            pending_quan: data.pending_quan,
                            unverified_users: data.unverified_user
                        });
                    }

                    setTimeout(() => {
                        newRow.classList.remove('bg-amber-100/90', 'animate-pulse');
                        newRow.classList.add('hover:bg-slate-50/80');
                    }, 5000);
                }

                // Nếu admin đang ở trang Duyệt Xác Thực Người Dùng, chèn trực tiếp không reload
                const unverifiedTbody = document.getElementById('unverified-users-tbody');
                if (unverifiedTbody && data.unverified_users && data.unverified_users.length > 0) {
                    data.unverified_users.forEach(u => {
                        if (!document.getElementById('user-row-' + u.id)) {
                            const emptyRow = document.getElementById('unverified-users-empty-row');
                            if (emptyRow) emptyRow.remove();

                            const uRow = document.createElement('tr');
                            uRow.id = 'user-row-' + u.id;
                            uRow.className = 'bg-indigo-50/90 transition-all duration-1000 animate-pulse';
                            uRow.innerHTML = `
                                <td class="p-3 font-bold text-slate-900">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-ping"></span>
                                        ${u.ho_ten}
                                    </div>
                                </td>
                                <td class="p-3 text-slate-600">${u.email}</td>
                                <td class="p-3 text-slate-600">${u.so_dien_thoai || 'N/A'}</td>
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" onclick="confirmVerifyUser('${u.id}', '${(u.ho_ten || '').replace(/'/g, "\\'")}')" class="btn-enterprise btn-enterprise-indigo">
                                            <span class="material-symbols-outlined text-[16px]">verified</span>
                                            <span>Xác thực</span>
                                        </button>
                                        <button type="button" onclick="confirmRejectUser('${u.id}', '${(u.ho_ten || '').replace(/'/g, "\\'")}')" class="btn-enterprise btn-enterprise-danger">
                                            <span class="material-symbols-outlined text-[16px]">block</span>
                                            <span>Không duyệt</span>
                                        </button>
                                    </div>
                                </td>
                            `;
                            unverifiedTbody.insertBefore(uRow, unverifiedTbody.firstChild);
                            setTimeout(() => {
                                uRow.classList.remove('bg-indigo-50/90', 'animate-pulse');
                                uRow.classList.add('hover:bg-slate-50');
                            }, 5000);
                        }
                    });
                }

                // Update Dashboard KPI Cards Live
                const kpiTotalQuan = document.getElementById('kpi-total-quan');
                if (kpiTotalQuan && data.total_quan !== undefined) kpiTotalQuan.textContent = data.total_quan.toLocaleString();

                const kpiPendingQuan = document.getElementById('kpi-pending-quan');
                if (kpiPendingQuan && data.pending_quan !== undefined) kpiPendingQuan.textContent = data.pending_quan.toLocaleString();

                const kpiTotalUsers = document.getElementById('kpi-total-users');
                if (kpiTotalUsers && data.total_users !== undefined) kpiTotalUsers.textContent = data.total_users.toLocaleString();

                const kpiActiveUsers = document.getElementById('kpi-active-users');
                if (kpiActiveUsers && data.active_users !== undefined) kpiActiveUsers.textContent = data.active_users.toLocaleString() + ' người';

                // Inject Live User Row if standing on User Management screen
                const usersTbody = document.getElementById('users-tbody');
                if (usersTbody && data.latest_user && !document.getElementById('user-row-' + data.latest_user.id)) {
                    const uRow = document.createElement('tr');
                    uRow.id = 'user-row-' + data.latest_user.id;
                    uRow.className = 'hover:bg-gray-50/80 transition-colors bg-indigo-50/90 animate-pulse';
                    uRow.innerHTML = `
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-sm">
                                    ${(data.latest_user.ho_ten || 'U').substring(0, 1).toUpperCase()}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-ping"></span>
                                        ${data.latest_user.ho_ten}
                                    </p>
                                    <span class="text-xs text-gray-400">ID: ${data.latest_user.id.substring(0, 8)}...</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-medium text-gray-800">${data.latest_user.email}</p>
                            <p class="text-xs text-gray-400">${data.latest_user.so_dien_thoai || 'Chưa cập nhật SĐT'}</p>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">nguoi_dung</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Hoạt động</span>
                        </td>
                        <td class="py-3.5 px-4 text-center text-xs text-gray-400">0</td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="/admin/nguoi-dung/${data.latest_user.id}/edit" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-bold text-xs inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">edit</span> Chỉnh sửa
                            </a>
                        </td>
                    `;
                    usersTbody.insertBefore(uRow, usersTbody.firstChild);
                    setTimeout(() => uRow.classList.remove('bg-indigo-50/90', 'animate-pulse'), 5000);
                }

                // Inject Live Blog Row if standing on Blog Management screen
                const blogTbody = document.getElementById('admin-blog-tbody');
                if (blogTbody && (data.latest_blog || (data.pending_blogs && data.pending_blogs.length > 0))) {
                    window.__pendingBlogsData = window.__pendingBlogsData || {};
                    const blogsToProcess = data.pending_blogs ? [...data.pending_blogs].reverse() : [data.latest_blog];

                    blogsToProcess.forEach(bData => {
                        if (!bData || document.getElementById('admin-blog-row-' + bData.id)) return;
                        window.__pendingBlogsData[bData.id] = bData;

                        const emptyBlogRow = blogTbody.querySelector('.empty-blog-row');
                        if (emptyBlogRow) emptyBlogRow.remove();

                        const safeTitle = (bData.title || '').replace(/'/g, "\\'");
                        const bRow = document.createElement('tr');
                        bRow.id = 'admin-blog-row-' + bData.id;
                        bRow.setAttribute('data-status', 'pending');
                        bRow.className = 'hover:bg-gray-50/60 transition-all duration-1000 bg-amber-50/90 animate-pulse';
                        bRow.innerHTML = `
                            <td class="px-3.5 py-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 shrink-0 border border-gray-200/60">
                                        <img src="${bData.cover_image_url}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80';" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0 flex-1 overflow-hidden">
                                        <a href="${bData.detail_url || bData.url}" class="font-bold text-gray-900 hover:text-amber-700 truncate block transition-colors text-xs" title="${bData.title}">
                                            ${bData.title}
                                        </a>
                                        <p class="text-[11px] text-gray-400 mt-0.5 truncate">${bData.category_name || 'Chưa phân loại'}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3.5 py-3">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <img src="${bData.author_avatar}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(bData.author || 'U')}';" class="w-6 h-6 rounded-full border border-gray-200 shrink-0">
                                    <span class="font-semibold text-gray-800 truncate text-xs">${bData.author || 'Người dùng'}</span>
                                </div>
                            </td>
                            <td class="px-3.5 py-3" id="admin-blog-status-td-${bData.id}">
                                <span id="admin-blog-status-badge-${bData.id}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold border bg-amber-100 text-amber-800 border-amber-200 whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[13px]">pending_actions</span>
                                    <span>Chờ duyệt</span>
                                </span>
                            </td>
                            <td class="px-3.5 py-3 text-center" id="admin-blog-hero-td-${bData.id}">
                                <button type="button" onclick="adminToggleHero(${bData.id}, this)" class="text-gray-300 hover:text-amber-500 transition-colors cursor-pointer" title="Đặt làm Hero nổi bật">
                                    <span class="material-symbols-outlined text-[18px]">star</span>
                                </button>
                            </td>
                            <td class="px-3.5 py-3 text-gray-500 text-[11px] whitespace-nowrap">
                                <span class="font-bold text-amber-600 block leading-tight">Vừa xong</span>
                                <span class="text-gray-400 text-[10px] block leading-tight">Mới gửi duyệt</span>
                            </td>
                            <td class="px-3.5 pr-6 py-3 text-right whitespace-nowrap" id="admin-blog-actions-${bData.id}">
                                <div class="inline-flex items-center justify-end gap-1.5 flex-nowrap shrink-0">
                                    <button type="button" onclick="openBlogQuickPreviewModal(${bData.id})" class="btn-enterprise btn-enterprise-secondary btn-action-compact font-semibold w-[68px] justify-center shrink-0" title="Xem nhanh nội dung bài viết">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                                        <span>Xem</span>
                                    </button>
                                    <button type="button" onclick="adminQuickApproveBlog(${bData.id}, '${safeTitle}')" class="btn-enterprise btn-enterprise-success btn-action-compact font-bold shadow-2xs w-[72px] justify-center shrink-0" title="Phê duyệt bài viết ngay">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                        <span>Duyệt</span>
                                    </button>
                                    <button type="button" onclick="adminOpenRejectModal(${bData.id}, '${safeTitle}', 'pending')" class="btn-enterprise btn-enterprise-danger btn-action-compact font-semibold shadow-2xs w-[98px] justify-center shrink-0" title="Từ chối hoặc yêu cầu sửa">
                                        <span class="material-symbols-outlined text-[14px]">cancel</span>
                                        <span>Không duyệt</span>
                                    </button>
                                    <button type="button" onclick="adminConfirmDeleteBlog(${bData.id}, '${safeTitle}')" class="btn-enterprise btn-enterprise-secondary btn-action-compact text-gray-400 hover:text-rose-600 hover:bg-rose-50 border-gray-200 w-8 h-8 p-0 flex items-center justify-center shrink-0" title="Xóa bài viết">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        `;
                        blogTbody.insertBefore(bRow, blogTbody.firstChild);
                        setTimeout(() => bRow.classList.remove('bg-amber-50/90', 'animate-pulse'), 5000);
                    });
                }

                isNotifInit = true;
            })
            .catch(() => {});
        }

        function toggleSidebar() {
            const mobileSidebar = document.getElementById('admin-sidebar-mobile');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            if (mobileSidebar) {
                mobileSidebar.classList.toggle('-translate-x-full');
            }
            if (backdrop) {
                backdrop.classList.toggle('hidden');
            }
        }

        function openCommandPalette() {
            const modal = document.getElementById('command-palette-modal');
            const input = document.getElementById('command-palette-input');
            if (modal) {
                modal.classList.remove('hidden');
                if (input) {
                    input.value = '';
                    filterCommandPalette();
                    setTimeout(() => input.focus(), 50);
                }
            }
        }

        function closeCommandPalette() {
            const modal = document.getElementById('command-palette-modal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function toggleAdminProfileDropdown() {
            const dropdown = document.getElementById('admin-profile-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('admin-profile-container');
            const dropdown = document.getElementById('admin-profile-dropdown');
            if (container && dropdown && !container.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function filterCommandPalette() {
            const query = (document.getElementById('command-palette-input')?.value || '').toLowerCase();
            const items = document.querySelectorAll('#command-palette-list .cmd-item');
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? 'flex' : 'none';
            });
        }

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                openCommandPalette();
            }
            if (e.key === 'Escape') {
                closeCommandPalette();
                closeConfirmModal();
                document.getElementById('admin-profile-dropdown')?.classList.add('hidden');
                document.getElementById('admin-notif-dropdown')?.classList.add('hidden');
            }
        });

        document.getElementById('command-palette-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCommandPalette();
            }
        });

        function scrollAdminToTop(smooth = false) {
            try {
                const behavior = smooth ? 'smooth' : 'instant';
                window.scrollTo({ top: 0, left: 0, behavior });
                document.documentElement.scrollTo({ top: 0, left: 0, behavior });
                document.body.scrollTo({ top: 0, left: 0, behavior });
                document.documentElement.scrollTop = 0;
                document.body.scrollTop = 0;
                const wrapper = document.querySelector('.admin-main-wrapper');
                if (wrapper) wrapper.scrollTop = 0;
                const main = document.getElementById('admin-main-content');
                if (main) main.scrollTop = 0;
            } catch (e) {}
        }

        window.addEventListener('scroll', function() {
            const btn = document.getElementById('admin-back-to-top');
            if (!btn) return;
            const top = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
            if (top > 220) {
                btn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-3');
                btn.classList.add('opacity-100', 'translate-y-0');
            } else {
                btn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-3');
                btn.classList.remove('opacity-100', 'translate-y-0');
            }
        }, { passive: true });

        document.addEventListener('DOMContentLoaded', function() {
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
            updateSidebarActiveLinks(window.location.href);
            checkRealtimeAdminNotifications();
            setInterval(checkRealtimeAdminNotifications, 3000);
        });

        // ========================================================
        // INSTANT SPA TURBO NAVIGATION & HOVER PREFETCHING ENGINE
        // ========================================================
        const adminPageCache = new Map();
        window.adminPageCache = adminPageCache;

        function clearAdminPageCache() {
            try {
                if (window.adminPageCache) {
                    window.adminPageCache.clear();
                }
            } catch (e) {}
        }
        let currentNavAbort = null;

        function stripFlashFromHtml(html) {
            if (!html || typeof html !== 'string') return html;
            try {
                const p = new DOMParser();
                const d = p.parseFromString(html, 'text/html');
                const flash = d.querySelector('#admin-flash-container');
                if (flash) {
                    flash.innerHTML = '';
                }
                return d.documentElement.outerHTML;
            } catch (e) {
                return html;
            }
        }

        function dismissAdminFlashAlert(btnOrEl) {
            const alert = (btnOrEl && btnOrEl.closest) ? btnOrEl.closest('.admin-flash-alert') : btnOrEl;
            if (!alert) return;
            alert.style.transition = 'all 0.25s ease-out';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-6px)';
            setTimeout(() => {
                alert.remove();
            }, 250);
        }

        function initAdminFlashAlerts() {
            document.querySelectorAll('.admin-flash-alert').forEach(alert => {
                if (alert.dataset.hasDismissTimer) return;
                alert.dataset.hasDismissTimer = 'true';

                // Tự động đóng sau 4.5s cho thông báo thành công hoặc thông tin
                if (!alert.classList.contains('bg-rose-50') && !alert.classList.contains('bg-amber-50')) {
                    let timer = setTimeout(() => {
                        dismissAdminFlashAlert(alert);
                    }, 4500);

                    // Tạm dừng khi rê chuột vào để người dùng đọc kịp
                    alert.addEventListener('mouseenter', () => clearTimeout(timer));
                    alert.addEventListener('mouseleave', () => {
                        timer = setTimeout(() => {
                            dismissAdminFlashAlert(alert);
                        }, 2500);
                    });
                }
            });
        }
        document.addEventListener('DOMContentLoaded', initAdminFlashAlerts);
        initAdminFlashAlerts();

        function prefetchUrl(url) {
            if (!url || adminPageCache.has(url)) return;
            try {
                const parsed = new URL(url, window.location.origin);
                if (parsed.origin !== window.location.origin || !parsed.pathname.startsWith('/admin')) return;

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => {
                        if (res.ok) return res.text();
                        return null;
                    })
                    .then(html => {
                        if (html) {
                            adminPageCache.set(url, stripFlashFromHtml(html));
                            if (adminPageCache.size > 25) {
                                const firstKey = adminPageCache.keys().next().value;
                                adminPageCache.delete(firstKey);
                            }
                        }
                    })
                    .catch(() => {});
            } catch (e) {}
        }

        function instantNavigate(url, pushState = true) {
            // Ngay lập tức ẩn thông báo flash của trang trước, đảm bảo không bao giờ lan sang chức năng mới
            const activeFlash = document.getElementById('admin-flash-container');
            if (activeFlash) {
                activeFlash.innerHTML = '';
            }

            // Luôn luôn cuộn lên đỉnh đầu trang khi bấm chuyển chức năng / liên kết
            scrollAdminToTop(false);

            const bar = document.getElementById('admin-top-progress-bar');
            if (bar) {
                bar.style.opacity = '1';
                bar.style.width = '35%';
            }

            if (currentNavAbort) {
                currentNavAbort.abort();
            }
            const controller = new AbortController();
            currentNavAbort = controller;

            const cachedHtml = adminPageCache.get(url);
            const fetchPromise = cachedHtml
                ? Promise.resolve(cachedHtml)
                : fetch(url, { signal: controller.signal, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => {
                        if (!res.ok) throw new Error('Network error');
                        return res.text();
                    })
                    .then(html => {
                        adminPageCache.set(url, stripFlashFromHtml(html));
                        return html;
                    });

            if (bar) bar.style.width = '70%';

            fetchPromise.then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newMain = doc.querySelector('#admin-main-content');
                const currentMain = document.querySelector('#admin-main-content');

                if (newMain && currentMain) {
                    document.title = doc.title;

                    const newHeaderTitle = doc.querySelector('#header-page-title');
                    const currentHeaderTitle = document.querySelector('#header-page-title');
                    if (newHeaderTitle && currentHeaderTitle) {
                        currentHeaderTitle.innerHTML = newHeaderTitle.innerHTML;
                    }

                    currentMain.style.opacity = '0';
                    currentMain.style.transition = 'opacity 0.08s ease';

                    setTimeout(() => {
                        currentMain.innerHTML = newMain.innerHTML;
                        currentMain.className = newMain.className;

                        // Execute scripts in page content
                        doc.querySelectorAll('#admin-main-content script, main script').forEach(s => {
                            const newScript = document.createElement('script');
                            Array.from(s.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                            newScript.textContent = s.textContent;
                            document.body.appendChild(newScript);
                            newScript.remove();
                        });

                        // Re-run push scripts if any
                        doc.querySelectorAll('body > script:not([src])').forEach(s => {
                            if (!s.textContent.includes('Real-time Notification Engine') && 
                                !s.textContent.includes('INSTANT SPA TURBO NAVIGATION') &&
                                !s.textContent.includes('adminPageCache')) {
                                const newScript = document.createElement('script');
                                newScript.textContent = s.textContent;
                                document.body.appendChild(newScript);
                                newScript.remove();
                            }
                        });

                        currentMain.style.opacity = '1';
                        scrollAdminToTop(false);
                        requestAnimationFrame(() => scrollAdminToTop(false));

                        initAdminFlashAlerts();
                        updateSidebarActiveLinks(url);

                        if (pushState) {
                            window.history.pushState({ url }, '', url);
                        }

                        // Close mobile sidebar if open
                        const mobileSidebar = document.getElementById('admin-sidebar-mobile');
                        const backdrop = document.getElementById('mobile-sidebar-backdrop');
                        if (mobileSidebar && !mobileSidebar.classList.contains('-translate-x-full')) {
                            mobileSidebar.classList.add('-translate-x-full');
                            backdrop?.classList.add('hidden');
                        }

                        if (bar) {
                            bar.style.width = '100%';
                            setTimeout(() => {
                                bar.style.opacity = '0';
                                bar.style.width = '0';
                            }, 200);
                        }

                        if (typeof checkRealtimeAdminNotifications === 'function') {
                            checkRealtimeAdminNotifications();
                        }
                    }, 80);
                } else {
                    window.location.href = url;
                }
            })
            .catch(err => {
                if (err.name !== 'AbortError') {
                    window.location.href = url;
                }
            });
        }

        function updateSidebarActiveLinks(currentUrl) {
            try {
                const parsedUrl = new URL(currentUrl, window.location.origin);
                const parsedPath = parsedUrl.pathname.replace(/\/+$/, '') || '/';
                const items = document.querySelectorAll('.sidebar-item');
                if (!items || items.length === 0) return;

                let bestPath = null;
                let bestMatchLen = -1;

                // Vòng 1: Tìm duy nhất 1 đường dẫn khớp chính xác nhất (ưu tiên khớp hoàn toàn, hoặc prefix có dấu /)
                items.forEach(a => {
                    const aHref = a.getAttribute('href');
                    if (!aHref || aHref.startsWith('#') || aHref.startsWith('javascript:')) return;
                    const aPath = new URL(a.href, window.location.origin).pathname.replace(/\/+$/, '') || '/';

                    let isMatch = false;
                    if (aPath === parsedPath) {
                        isMatch = true;
                    } else if (aPath !== '/admin' && aPath !== '/' && parsedPath.startsWith(aPath + '/')) {
                        isMatch = true;
                    }

                    if (isMatch && aPath.length > bestMatchLen) {
                        bestPath = aPath;
                        bestMatchLen = aPath.length;
                    }
                });

                // Vòng 2: Duyệt qua tất cả items, CHỈ TÔ VÀNG DUY NHẤT ITEM KHỚP, TẤT CẢ Ô CÒN LẠI XÓA VÀNG
                items.forEach(a => {
                    const aHref = a.getAttribute('href');
                    if (!aHref || aHref.startsWith('#') || aHref.startsWith('javascript:')) return;
                    const aPath = new URL(a.href, window.location.origin).pathname.replace(/\/+$/, '') || '/';

                    if (bestPath && aPath === bestPath) {
                        a.classList.remove('text-slate-300', 'hover:bg-slate-800/80', 'hover:text-white');
                        a.classList.add('bg-[#F59E0B]', 'text-white', 'font-bold', 'shadow-md');
                    } else {
                        a.classList.remove('bg-[#F59E0B]', 'text-white', 'font-bold', 'shadow-md');
                        a.classList.add('text-slate-300', 'hover:bg-slate-800/80', 'hover:text-white');
                    }
                });
            } catch (e) {}
        }

        // Intercept clicks on admin internal links for instant 0-delay load
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
            if (link.getAttribute('target') === '_blank' || link.hasAttribute('download') || href.includes('export')) return;

            try {
                const parsed = new URL(link.href, window.location.origin);
                if (parsed.origin !== window.location.origin || !parsed.pathname.startsWith('/admin')) return;

                if (parsed.href === window.location.href) {
                    e.preventDefault();
                    return;
                }

                if (parsed.pathname === window.location.pathname && parsed.search === window.location.search && parsed.hash) {
                    return;
                }

                e.preventDefault();

                // Phản hồi giao diện tức thì (0ms latency):
                // Nếu click vào mục menu sidebar, chuyển highlight vàng duy nhất ô đó ngay lập tức!
                if (link.classList.contains('sidebar-item') || link.closest('.sidebar-item')) {
                    updateSidebarActiveLinks(parsed.href);
                }

                instantNavigate(parsed.href, true);
            } catch (e) {}
        });

        // Intercept GET search/filter forms for instant 0-delay load (ngăn chặn Chrome tự động cuộn xuống nút Lọc)
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || (form.method && form.method.toUpperCase() !== 'GET')) return;
            const action = form.getAttribute('action') || window.location.pathname;
            try {
                const parsed = new URL(action, window.location.origin);
                if (parsed.origin !== window.location.origin || !parsed.pathname.startsWith('/admin')) return;

                e.preventDefault();
                const formData = new FormData(form);
                const params = new URLSearchParams();
                for (const [key, value] of formData.entries()) {
                    if (value !== '' && value !== null && value !== undefined) {
                        params.append(key, value);
                    }
                }
                const queryString = params.toString();
                const targetUrl = parsed.pathname + (queryString ? '?' + queryString : '');
                instantNavigate(targetUrl, true);
            } catch (err) {}
        });

        // Hover prefetch: loads page into RAM when cursor approaches (0ms latency on click)
        document.addEventListener('mouseover', function(e) {
            const link = e.target.closest('a');
            if (!link) return;
            const href = link.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                prefetchUrl(link.href);
            }
        }, { passive: true });

        document.addEventListener('touchstart', function(e) {
            const link = e.target.closest('a');
            if (!link) return;
            const href = link.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                prefetchUrl(link.href);
            }
        }, { passive: true });

        // Intelligent Idle Prefetch: nạp ngầm các trang quản trị chính khi CPU/mạng rảnh (0ms click latency)
        const idlePrefetchSidebar = () => {
            try {
                const links = Array.from(document.querySelectorAll('.sidebar-item, #admin-sidebar a[href^="/admin"]'))
                    .map(a => a.href)
                    .filter(h => h && !h.includes('#') && !h.includes('logout') && !adminPageCache.has(h));
                let i = 0;
                const prefetchNext = () => {
                    if (i >= links.length) return;
                    prefetchUrl(links[i++]);
                    if ('requestIdleCallback' in window) {
                        window.requestIdleCallback(prefetchNext, { timeout: 2000 });
                    } else {
                        setTimeout(prefetchNext, 350);
                    }
                };
                if ('requestIdleCallback' in window) {
                    window.requestIdleCallback(prefetchNext, { timeout: 1500 });
                } else {
                    setTimeout(prefetchNext, 600);
                }
            } catch (e) {}
        };
        if (document.readyState === 'complete') {
            setTimeout(idlePrefetchSidebar, 600);
        } else {
            window.addEventListener('load', () => setTimeout(idlePrefetchSidebar, 600));
        }

        // Handle Browser Back / Forward buttons instantly from cache
        window.addEventListener('popstate', function() {
            scrollAdminToTop(false);
            updateSidebarActiveLinks(window.location.href);
            instantNavigate(window.location.href, false);
        });

        window.addEventListener('pageshow', function() {
            scrollAdminToTop(false);
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
