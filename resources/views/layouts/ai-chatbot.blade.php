{{-- Floating AI Chatbot Widget: Kira AI Assistant (Facebook Messenger Mini Style) --}}
@php
$botAvatarSvg = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block;">
    <path d="M12 2C6.48 2 2 6.03 2 11C2 13.84 3.42 16.36 5.61 17.99C5.89 18.2 6.05 18.53 5.99 18.88L5.47 21.26C5.38 21.68 5.8 22.03 6.17 21.82L8.97 20.25C9.23 20.1 9.54 20.05 9.84 20.12C10.5 20.27 11.2 20.34 11.93 20.34C17.45 20.34 21.93 16.31 21.93 11.34C21.93 6.37 17.48 2 12 2Z" fill="#FFFFFF"/>
    <path d="M12 6.5C12 8.8 10.3 10.5 8 10.5C10.3 10.5 12 12.2 12 14.5C12 12.2 13.7 10.5 16 10.5C13.7 10.5 12 8.8 12 6.5Z" fill="#EA580C"/>
    <circle cx="12" cy="10.5" r="1.1" fill="#F59E0B"/>
</svg>';
@endphp

<div id="aiChatbotContainer" style="position: fixed !important; bottom: 12px !important; right: 12px !important; z-index: 99999 !important;" class="fixed bottom-3 right-3 sm:bottom-4 sm:right-4 z-50 flex flex-col items-end print:hidden pointer-events-none">

    {{-- Chat Window (Facebook Messenger Mini Pop-up Tab - Thu nhỏ gọn gàng, cố định khít khung màn hình) --}}
    <div id="aiChatWindow" 
         style="width: 320px !important; max-width: calc(100vw - 24px) !important; height: 420px !important; max-height: calc(100vh - 35px) !important;"
         class="hidden flex-col bg-white rounded-2xl border border-gray-200/90 shadow-[0_8px_30px_rgba(0,0,0,0.14)] overflow-hidden transition-all duration-300 origin-bottom-right pointer-events-auto">
        
        {{-- Messenger Compact Header (Cố định phía trên, cao 40px chuẩn Messenger) --}}
        <div id="aiChatHeader" 
             onclick="handleHeaderClick()" 
             class="h-[40px] px-2.5 py-1 bg-white border-b border-gray-100 flex items-center justify-between shrink-0 shadow-[0_1px_2px_rgba(0,0,0,0.03)] select-none cursor-pointer">
            
            {{-- Bot Avatar & Status Info --}}
            <div class="flex items-center gap-1.5 min-w-0">
                <div class="kira-avatar-badge relative" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; margin-top: 0;">
                    {!! $botAvatarSvg !!}
                    {{-- Active status green dot --}}
                    <span class="absolute bottom-0 right-0 w-1.5 h-1.5 rounded-full bg-emerald-500 border-2 border-white ring-1 ring-emerald-500/20"></span>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1">
                        <h4 class="font-bold text-[11.5px] tracking-tight truncate text-gray-900 leading-tight">Kira AI</h4>
                        <span class="px-1.5 py-0.5 rounded-full bg-orange-100 text-[8px] font-bold text-primary">Trợ lý</span>
                    </div>
                    <p class="text-[9px] text-emerald-600 font-medium truncate leading-tight mt-0.5 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Đang hoạt động
                    </p>
                </div>
            </div>

            {{-- Facebook Messenger Window Controls (Refresh, Minimize, Close) --}}
            <div class="flex items-center gap-0.5 shrink-0" onclick="event.stopPropagation()">
                <button type="button" 
                        onclick="clearAiChatHistory()" 
                        class="kira-ctrl-btn text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer" 
                        style="width: 24px; height: 24px; min-width: 24px; min-height: 24px;"
                        title="Làm mới cuộc trò chuyện">
                    <span class="material-symbols-outlined text-[14px]">refresh</span>
                </button>
                <button type="button" 
                        id="aiChatMinBtn"
                        onclick="minimizeAiChatbot(event)" 
                        class="kira-ctrl-btn text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer" 
                        style="width: 24px; height: 24px; min-width: 24px; min-height: 24px;"
                        title="Thu nhỏ cửa sổ chat">
                    <span id="aiChatMinIcon" class="material-symbols-outlined text-[15px]">remove</span>
                </button>
                <button type="button" 
                        onclick="toggleAiChatbot(false)" 
                        class="kira-ctrl-btn close-btn text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" 
                        style="width: 24px; height: 24px; min-width: 24px; min-height: 24px;"
                        title="Đóng cửa sổ chat">
                    <span class="material-symbols-outlined text-[15px]">close</span>
                </button>
            </div>
        </div>

        {{-- Messages Scroll Area (Facebook Messenger Styled - Chỉ vùng này được cuộn, không cuộn trang web) --}}
        <div id="aiChatMessages" class="flex-1 min-h-0 p-2 space-y-2 bg-[#f0f2f5] text-[11.5px] custom-scrollbar overscroll-contain select-text">
            
            {{-- Initial AI Greeting Message (Messenger Mini White Bubble) --}}
            <div class="flex items-start gap-1.5 max-w-[96%]">
                <div class="kira-avatar-badge" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; margin-top: 2px;">
                    {!! $botAvatarSvg !!}
                </div>
                <div class="bg-white text-gray-800 p-2 rounded-[14px] rounded-tl-[3px] leading-relaxed space-y-1 shadow-2xs border border-gray-100/90">
                    <p class="text-[11px]">Hế lô bạn iu! 👋 Mình là <b>Kira AI</b> - trợ lý ẩm thực chuẩn gu Quán Mới đây! Bạn đang thèm món gì, cứ bảo mình một câu nha ✨</p>
                    <p class="text-[9.5px] text-gray-500 font-medium">Bấm nhanh câu hỏi bên dưới hoặc nhắn tin cho mình nhé:</p>
                </div>
            </div>

            @php
                $chatbotHour = now()->hour;
                $chatbotIsWeekend = in_array(now()->dayOfWeek, [0, 6]);
                if ($chatbotHour >= 5 && $chatbotHour < 10) {
                    $initSuggestions = [
                        ['text' => 'Ăn sáng ngon & nóng hổi gần đây', 'icon' => '☀️'],
                        ['text' => 'Cà phê sáng tỉnh táo làm việc', 'icon' => '☕'],
                        ['text' => 'Phở bò, bánh mì giòn ngon', 'icon' => '🍜'],
                        ['text' => 'Bữa sáng bình dân dưới 35k', 'icon' => '💰'],
                        ['text' => 'Quán có voucher giảm giá', 'icon' => '🏷️'],
                    ];
                } elseif ($chatbotHour >= 10 && $chatbotHour < 14) {
                    $initSuggestions = [
                        ['text' => 'Cơm trưa văn phòng có máy lạnh', 'icon' => '🍱'],
                        ['text' => 'Món trưa nhanh gọn no bụng dưới 50k', 'icon' => '⚡'],
                        ['text' => 'Cơm tấm, bún bò trưa nay', 'icon' => '🍛'],
                        ['text' => 'Quán trưa có giao tận nơi', 'icon' => '🛵'],
                        ['text' => 'Quán đang có khuyến mãi', 'icon' => '🏷️'],
                    ];
                } elseif ($chatbotHour >= 14 && $chatbotHour < 17) {
                    $initSuggestions = [
                        ['text' => 'Trà sữa mát lạnh xế chiều', 'icon' => '🧋'],
                        ['text' => 'Quán cafe yên tĩnh làm việc chill', 'icon' => '☕'],
                        ['text' => 'Đồ ăn vặt xế chiều chuẩn vị', 'icon' => '🍟'],
                        ['text' => 'Bánh ngọt & trà giải nhiệt', 'icon' => '🍰'],
                        ['text' => 'Quán có mã ưu đãi hôm nay', 'icon' => '🏷️'],
                    ];
                } elseif ($chatbotHour >= 17 && $chatbotHour < 21) {
                    if ($chatbotIsWeekend) {
                        $initSuggestions = [
                            ['text' => 'Lẩu nướng tụ tập bạn bè cuối tuần', 'icon' => '🔥'],
                            ['text' => 'Buffet nướng BBQ ăn thả ga', 'icon' => '🥩'],
                            ['text' => 'Quán ăn gia đình ấm cúng cuối tuần', 'icon' => '🍲'],
                            ['text' => 'Hải sản tươi sống chế biến ngon', 'icon' => '🍣'],
                            ['text' => 'Săn voucher giảm giá cuối tuần', 'icon' => '🏷️'],
                        ];
                    } else {
                        $initSuggestions = [
                            ['text' => 'Bữa tối ấm cúng sau giờ làm', 'icon' => '🍲'],
                            ['text' => 'Lẩu nướng ấm áp bạn bè', 'icon' => '🔥'],
                            ['text' => 'Món ngon bình dân dưới 60k', 'icon' => '💰'],
                            ['text' => 'Cơm gia đình đậm đà chuẩn vị', 'icon' => '🍚'],
                            ['text' => 'Quán ăn có khuyến mãi tối nay', 'icon' => '🏷️'],
                        ];
                    }
                } else {
                    $initSuggestions = [
                        ['text' => 'Quán ăn đêm mở khuya 24/7', 'icon' => '🌙'],
                        ['text' => 'Cháo nóng, ốc đêm êm bụng', 'icon' => '🥣'],
                        ['text' => 'Mì cay, lẩu đêm ấm bụng', 'icon' => '🍜'],
                        ['text' => 'Quán ăn khuya giao nhanh', 'icon' => '🛵'],
                        ['text' => 'Món ăn đêm giá sinh viên', 'icon' => '💰'],
                    ];
                }
            @endphp

            {{-- Quick Suggestion Chips Container (AI tự động tạo theo nhu cầu thực tế của người dùng) --}}
            <div id="aiSuggestionChips" class="flex flex-wrap gap-1 pl-0 sm:pl-7">
                @foreach ($initSuggestions as $chip)
                    <button type="button" onclick="sendAiQuickMessage('{{ addslashes($chip['text']) }}')" class="ai-quick-chip cursor-pointer">
                        <span class="text-[10px] shrink-0">{{ $chip['icon'] }}</span>
                        <span class="truncate">{{ $chip['text'] }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Dynamic Messages injected here by JavaScript --}}
            <div id="aiDynamicMessagesContainer" class="space-y-2"></div>

            {{-- Loading / Typing Indicator (Facebook Messenger Style) --}}
            <div id="aiChatTyping" class="hidden items-start gap-1.5 max-w-[85%]">
                <div class="kira-avatar-badge" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; margin-top: 2px;">
                    {!! $botAvatarSvg !!}
                </div>
                <div class="bg-white px-2.5 py-1.5 rounded-[14px] rounded-tl-[3px] flex items-center gap-1 shadow-2xs border border-gray-100/90">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0ms;"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 150ms;"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 300ms;"></span>
                    <span class="text-[9.5px] text-gray-400 font-medium ml-1">Kira đang tìm quán...</span>
                </div>
            </div>
        </div>

        {{-- Facebook Messenger Input Bar (Cố định phía dưới cùng, thu nhỏ gọn) --}}
        <div id="aiChatFooter" class="p-1.5 bg-white border-t border-gray-100 shrink-0">
            <form id="aiChatForm" onsubmit="handleAiChatSubmit(event)" class="relative flex items-center gap-1">
                <div class="flex-1 bg-[#f0f2f5] hover:bg-[#e4e6eb] focus-within:bg-white focus-within:border-primary/50 focus-within:ring-2 focus-within:ring-primary/15 border border-transparent rounded-full px-2.5 py-1 flex items-center transition-all">
                    <input type="text" 
                           id="aiChatInput" 
                           placeholder="Hỏi quán ngon, cafe chill..." 
                           autocomplete="off"
                           maxlength="1000"
                           class="w-full bg-transparent outline-none text-[11.5px] text-gray-800 placeholder-gray-400" />
                </div>
                
                <button type="submit" 
                        id="aiChatSendBtn"
                        style="width: 28px; height: 28px; min-width: 28px; min-height: 28px;"
                        class="rounded-full bg-primary hover:bg-primary/90 text-white flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer shrink-0 disabled:opacity-30 disabled:pointer-events-none"
                        title="Gửi câu hỏi (Enter)">
                    <span class="material-symbols-outlined text-[13px]">send</span>
                </button>
            </form>
            <div class="flex items-center justify-between mt-0.5 px-1 text-[8.5px] text-gray-400 select-none">
                <span>Dữ liệu Quán Mới</span>
                <span>Enter để gửi</span>
            </div>
        </div>
    </div>

    {{-- Modern Floating Launcher Button & Tooltip (FB Messenger Bubble Style) --}}
    <div id="aiChatLauncherWrapper" class="relative flex items-center pointer-events-auto">
        {{-- Tooltip banner (shown initially, auto dismisses) --}}
        <div id="aiChatTooltip" class="hidden md:flex items-center gap-1.5 bg-slate-900/90 text-white text-[11px] font-medium py-1.5 px-3 rounded-full shadow-lg mr-2.5 backdrop-blur-sm border border-slate-700/60 transition-opacity animate-pulse">
            <span class="material-symbols-outlined text-amber-400 text-[14px]">auto_awesome</span>
            <span>Hỏi AI tìm quán ngon</span>
        </div>

        {{-- Main Circular Messenger-style Floating Button with High-tech Logo --}}
        <button type="button" 
                id="aiChatToggleBtn"
                onclick="toggleAiChatbot(true)" 
                style="width: 52px; height: 52px; min-width: 52px; min-height: 52px; background: linear-gradient(135deg, #FF6B00 0%, #EA580C 50%, #C2410C 100%) !important; box-shadow: 0 8px 24px rgba(234, 88, 12, 0.45) !important;"
                class="relative rounded-full text-white flex items-center justify-center transition-all duration-300 hover:scale-108 active:scale-95 cursor-pointer group pointer-events-auto"
                title="Mở Trợ lý AI Quán Mới"
                aria-label="Mở Trợ lý AI Quán Mới">
            
            {{-- Breathing ambient glow --}}
            <span class="absolute inset-0 rounded-full bg-orange-500/40 animate-ping -z-10 opacity-70"></span>

            {{-- Modern High-End White Messenger Bubble + Orange & Golden AI Sparkle --}}
            <div class="w-8 h-8 flex items-center justify-center transition-transform group-hover:scale-110 group-hover:rotate-6 pointer-events-none">
                <svg viewBox="0 0 32 32" width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.22));">
                    <!-- Messenger Chat Bubble in Pure Crisp White -->
                    <path d="M16 3C8.82 3 3 8.37 3 15C3 18.78 4.9 22.14 7.88 24.32C8.26 24.6 8.48 25.05 8.39 25.52L7.68 28.77C7.55 29.35 8.13 29.83 8.64 29.54L12.47 27.39C12.83 27.19 13.25 27.12 13.67 27.22C14.57 27.42 15.53 27.52 16.53 27.52C23.71 27.52 29.53 22.15 29.53 15.52C29.53 8.37 23.18 3 16 3Z" fill="#FFFFFF"/>
                    <!-- Vibrant Sunset Orange AI Sparkle -->
                    <path d="M16 8.5C16 11.8 13.5 14.2 10.2 14.2C13.5 14.2 16 16.6 16 20C16 16.6 18.5 14.2 21.8 14.2C18.5 14.2 16 11.8 16 8.5Z" fill="#EA580C"/>
                    <!-- Golden secondary accent sparkle -->
                    <path d="M21.5 8C21.5 9.4 20.4 10.5 19 10.5C20.4 10.5 21.5 11.6 21.5 13C21.5 11.6 22.6 10.5 24 10.5C22.6 10.5 21.5 9.4 21.5 8Z" fill="#F59E0B"/>
                    <circle cx="16" cy="14.2" r="1.6" fill="#FBBF24"/>
                </svg>
            </div>

            {{-- Unread Sparkle Badge --}}
            <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-gradient-to-tr from-amber-400 to-yellow-300 text-slate-900 font-extrabold text-[10px] flex items-center justify-center border-2 border-white shadow-md">
                ✨
            </span>
        </button>
    </div>
</div>

{{-- Chatbot Styles & Client-side Script --}}
<style>
    #aiChatbotContainer {
        position: fixed !important;
        bottom: 12px !important;
        right: 12px !important;
        z-index: 99999 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-end !important;
        pointer-events: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    @media (min-width: 640px) {
        #aiChatbotContainer {
            bottom: 16px !important;
            right: 16px !important;
        }
    }
    #aiChatWindow {
        width: 320px !important;
        max-width: calc(100vw - 24px) !important;
        height: 420px !important;
        max-height: calc(100vh - 35px) !important;
        background-color: #ffffff !important;
        border-radius: 16px !important;
        border: 1px solid rgba(229, 231, 235, 0.95) !important;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18) !important;
        overflow: hidden !important;
        pointer-events: auto !important;
        display: flex !important;
        flex-direction: column !important;
    }
    #aiChatWindow.hidden {
        display: none !important;
    }
    .kira-avatar-badge {
        width: 24px !important;
        height: 24px !important;
        min-width: 24px !important;
        min-height: 24px !important;
        max-width: 24px !important;
        max-height: 24px !important;
        flex-shrink: 0 !important;
        border-radius: 9999px !important;
        overflow: hidden !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: linear-gradient(135deg, #FF6B00 0%, #EA580C 100%) !important;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.28) !important;
    }
    .kira-avatar-badge svg {
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        min-height: 16px !important;
        max-width: 16px !important;
        max-height: 16px !important;
        display: block !important;
    }
    .ai-quick-chip {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 3px 8px !important;
        border-radius: 9999px !important;
        background: #ffffff !important;
        border: 1px solid rgba(229, 231, 235, 0.95) !important;
        color: #374151 !important;
        font-size: 9.5px !important;
        font-weight: 500 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        transition: all 0.18s ease-in-out !important;
        cursor: pointer !important;
        white-space: nowrap !important;
        max-width: 100% !important;
        user-select: none !important;
    }
    .ai-quick-chip:hover {
        background: #fff7ed !important;
        border-color: #fb923c !important;
        color: #c2410c !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 5px rgba(234, 88, 12, 0.12) !important;
    }
    .ai-quick-chip.primary-chip {
        background: #fff7ed !important;
        border-color: #fdba74 !important;
        color: #c2410c !important;
        font-weight: 600 !important;
    }
    .kira-ctrl-btn {
        width: 24px !important;
        height: 24px !important;
        min-width: 24px !important;
        min-height: 24px !important;
        max-width: 24px !important;
        max-height: 24px !important;
        border-radius: 9999px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }
    #aiChatSendBtn {
        width: 28px !important;
        height: 28px !important;
        min-width: 28px !important;
        min-height: 28px !important;
        max-width: 28px !important;
        max-height: 28px !important;
        border-radius: 9999px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }
    .kira-venue-thumb {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        min-height: 38px !important;
        max-width: 38px !important;
        max-height: 38px !important;
        border-radius: 6px !important;
        overflow: hidden !important;
        flex-shrink: 0 !important;
    }
    .kira-venue-thumb img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
    }
    .kira-venue-arrow {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        min-height: 18px !important;
        border-radius: 9999px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }
    .ai-chat-markdown p { margin-bottom: 0.25rem; line-height: 1.45; word-break: break-word; }
    .ai-chat-markdown p:last-child { margin-bottom: 0; }
    .ai-chat-markdown ul { padding-left: 1rem; margin-top: 0.2rem; margin-bottom: 0.25rem; list-style-type: disc; }
    .ai-chat-markdown ol { padding-left: 1.1rem; margin-top: 0.2rem; margin-bottom: 0.25rem; list-style-type: decimal; }
    .ai-chat-markdown li { margin-bottom: 0.15rem; line-height: 1.4; word-break: break-word; }
    .ai-chat-markdown strong, .ai-chat-markdown b { font-weight: 700; color: #0f172a; }
    .ai-chat-markdown a { color: #b94a00; font-weight: 600; text-decoration: underline; text-underline-offset: 2px; word-break: break-word; }
    .ai-chat-markdown a:hover { color: #ea580c; }
    .ai-chat-markdown h4, .ai-chat-markdown h5 { font-weight: 700; color: #1e293b; }
    #aiDynamicMessagesContainer {
        overflow: visible !important;
    }
    #aiChatMessages {
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.45) transparent;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        touch-action: pan-y;
        word-break: break-word;
        overflow-wrap: anywhere;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }
    #aiChatMessages::-webkit-scrollbar {
        width: 4px;
    }
    #aiChatMessages::-webkit-scrollbar-track {
        background: transparent;
    }
    #aiChatMessages::-webkit-scrollbar-thumb {
        background-color: rgba(148, 163, 184, 0.45);
        border-radius: 9999px;
    }
    #aiChatMessages::-webkit-scrollbar-thumb:hover {
        background-color: rgba(100, 116, 139, 0.7);
    }
</style>

<script>
    const AI_CHAT_SESSION_KEY = 'quanmoi_ai_chat_history_v1';
    let aiConversationHistory = [];
    let isAiChatMinimized = false;
    let isAiChatLoading = false;

    const BOT_AVATAR_SVG = `<svg viewBox="0 0 24 24" width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block;">
        <path d="M12 2C6.48 2 2 6.03 2 11C2 13.84 3.42 16.36 5.61 17.99C5.89 18.2 6.05 18.53 5.99 18.88L5.47 21.26C5.38 21.68 5.8 22.03 6.17 21.82L8.97 20.25C9.23 20.1 9.54 20.05 9.84 20.12C10.5 20.27 11.2 20.34 11.93 20.34C17.45 20.34 21.93 16.31 21.93 11.34C21.93 6.37 17.48 2 12 2Z" fill="#FFFFFF"/>
        <path d="M12 6.5C12 8.8 10.3 10.5 8 10.5C10.3 10.5 12 12.2 12 14.5C12 12.2 13.7 10.5 16 10.5C13.7 10.5 12 8.8 12 6.5Z" fill="#EA580C"/>
        <circle cx="12" cy="10.5" r="1.1" fill="#F59E0B"/>
    </svg>`;

    function getSuggestionIcon(text) {
        const t = (text || '').toLowerCase();
        if (t.includes('lẩu') || t.includes('nướng') || t.includes('bbq')) return '🔥';
        if (t.includes('cafe') || t.includes('cà phê') || t.includes('chill')) return '☕';
        if (t.includes('rẻ') || t.includes('sinh viên') || t.includes('50k') || t.includes('35k') || t.includes('60k') || t.includes('tiền')) return '💰';
        if (t.includes('đêm') || t.includes('khuya') || t.includes('24/7')) return '🌙';
        if (t.includes('hải sản') || t.includes('sushi') || t.includes('ốc')) return '🍣';
        if (t.includes('quận') || t.includes('huyện') || t.includes('khu vực') || t.includes('gần')) return '📍';
        if (t.includes('trà sữa') || t.includes('nước') || t.includes('uống') || t.includes('kem')) return '🧋';
        if (t.includes('pizza') || t.includes('mì') || t.includes('phở') || t.includes('bún')) return '🍜';
        if (t.includes('cơm') || t.includes('sườn')) return '🍱';
        if (t.includes('chay') || t.includes('thanh đạm') || t.includes('gỏi')) return '🥗';
        if (t.includes('voucher') || t.includes('khuyến mãi') || t.includes('giảm') || t.includes('ưu đãi')) return '🏷️';
        return '✨';
    }

    // AI tự động sinh câu hỏi gợi ý phù hợp nhất với nhu cầu & thời gian của người dùng (Không cần nút đổi thủ công)
    function renderSmartInitialSuggestions() {
        const chipsContainer = document.getElementById('aiSuggestionChips');
        if (!chipsContainer) return;

        const now = new Date();
        const hour = now.getHours();
        const day = now.getDay();
        const isWeekend = (day === 0 || day === 5 || day === 6);

        // Lấy lịch sử tìm kiếm từ localStorage
        let searchHistory = [];
        try {
            const raw = localStorage.getItem('qm_search_history');
            if (raw) searchHistory = JSON.parse(raw);
        } catch (e) {}
        const historyStr = (searchHistory || []).join(' ').toLowerCase();

        // Lấy địa điểm nếu người dùng đang chọn trên web
        let loc = '';
        const locSelect = document.getElementById('provinceSelect') || document.querySelector('select[name="tinh_thanh_id"]');
        if (locSelect && locSelect.selectedIndex > 0) {
            loc = locSelect.options[locSelect.selectedIndex]?.text?.trim() || '';
        }

        let smartChips = [];

        // 1. Gợi ý theo khung giờ sinh hoạt ẩm thực thực tế
        if (hour >= 5 && hour < 10) {
            smartChips.push({ text: 'Ăn sáng ngon & nóng hổi gần đây', icon: '☀️' });
            smartChips.push({ text: 'Cà phê sáng tỉnh táo làm việc', icon: '☕' });
            smartChips.push({ text: 'Phở bò, bánh mì giòn ngon', icon: '🍜' });
            smartChips.push({ text: 'Bữa sáng bình dân dưới 35k', icon: '💰' });
        } else if (hour >= 10 && hour < 14) {
            smartChips.push({ text: 'Cơm trưa văn phòng có máy lạnh', icon: '🍱' });
            smartChips.push({ text: 'Món trưa nhanh gọn no bụng dưới 50k', icon: '⚡' });
            smartChips.push({ text: 'Cơm tấm, bún bò trưa nay', icon: '🍛' });
            smartChips.push({ text: 'Quán trưa có giao tận nơi', icon: '🛵' });
        } else if (hour >= 14 && hour < 17) {
            smartChips.push({ text: 'Trà sữa mát lạnh xế chiều', icon: '🧋' });
            smartChips.push({ text: 'Quán cafe yên tĩnh làm việc chill', icon: '☕' });
            smartChips.push({ text: 'Đồ ăn vặt xế chiều chuẩn vị', icon: '🍟' });
            smartChips.push({ text: 'Bánh ngọt & trà giải nhiệt', icon: '🍰' });
        } else if (hour >= 17 && hour < 21) {
            if (isWeekend) {
                smartChips.push({ text: 'Lẩu nướng tụ tập bạn bè cuối tuần', icon: '🔥' });
                smartChips.push({ text: 'Buffet nướng BBQ ăn thả ga', icon: '🥩' });
                smartChips.push({ text: 'Quán ăn gia đình ấm cúng cuối tuần', icon: '🍲' });
                smartChips.push({ text: 'Hải sản tươi sống chế biến ngon', icon: '🍣' });
            } else {
                smartChips.push({ text: 'Bữa tối ấm cúng sau giờ làm', icon: '🍲' });
                smartChips.push({ text: 'Lẩu nướng ấm áp bạn bè', icon: '🔥' });
                smartChips.push({ text: 'Món ngon bình dân dưới 60k', icon: '💰' });
                smartChips.push({ text: 'Cơm gia đình đậm đà chuẩn vị', icon: '🍚' });
            }
        } else {
            smartChips.push({ text: 'Quán ăn đêm mở khuya 24/7', icon: '🌙' });
            smartChips.push({ text: 'Cháo nóng, ốc đêm êm bụng', icon: '🥣' });
            smartChips.push({ text: 'Mì cay, lẩu đêm ấm bụng', icon: '🍜' });
            smartChips.push({ text: 'Quán ăn khuya giao nhanh', icon: '🛵' });
        }

        // 2. Tự động cá nhân hóa theo thói quen lịch sử tìm kiếm
        if (historyStr.includes('chay')) {
            smartChips.unshift({ text: 'Quán chay thanh đạm gần đây', icon: '🥗' });
        } else if (historyStr.includes('hải sản') || historyStr.includes('ốc')) {
            smartChips.unshift({ text: 'Hải sản tươi ngon chuẩn vị', icon: '🍣' });
        } else if (historyStr.includes('trà sữa') || historyStr.includes('cafe')) {
            smartChips.unshift({ text: 'Trà sữa, cafe view đẹp check-in', icon: '🧋' });
        } else if (historyStr.includes('lẩu') || historyStr.includes('nướng')) {
            smartChips.unshift({ text: 'Lẩu nướng ngon đang hot', icon: '🔥' });
        }

        // 3. Ưu đãi / Khuyến mãi
        smartChips.push({ text: 'Quán nào đang có voucher giảm giá?', icon: '🏷️' });

        if (loc) {
            smartChips.push({ text: `Tìm quán ngon nhất tại ${loc}`, icon: '📍' });
        }

        // Lấy 5 gợi ý phù hợp nhất
        const selectedChips = smartChips.slice(0, 5);
        chipsContainer.innerHTML = selectedChips.map(item => `
            <button type="button" 
                    onclick="sendAiQuickMessage('${escapeHtml(item.text)}')" 
                    class="ai-quick-chip cursor-pointer">
                <span class="text-[10px] shrink-0">${item.icon}</span>
                <span class="truncate">${escapeHtml(item.text)}</span>
            </button>
        `).join('');
    }

    // Tải lịch sử chat từ sessionStorage khi tải trang
    document.addEventListener('DOMContentLoaded', () => {
        try {
            const saved = sessionStorage.getItem(AI_CHAT_SESSION_KEY);
            if (saved) {
                const parsed = JSON.parse(saved);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    // Lọc bỏ các tin nhắn lỗi gián đoạn cũ nếu có trong session
                    aiConversationHistory = parsed.filter(item => 
                        typeof item.content === 'string' && !item.content.includes('kết nối trợ lý AI hiện đang bận')
                    );
                    const container = document.getElementById('aiDynamicMessagesContainer');
                    if (container) {
                        aiConversationHistory.forEach(item => {
                            if (item.role === 'user') {
                                appendUserMessageBubble(item.content, false);
                            } else if (item.role === 'assistant') {
                                appendAiMessageBubble(item.content, item.venues || [], false);
                            }
                        });
                        const area = document.getElementById('aiChatMessages');
                        if (area) area.scrollTop = 0;
                    }
                }
            }
        } catch (e) {
            console.warn('Lỗi đọc session AI chat:', e);
        }

        // Tự động sinh gợi ý câu hỏi thông minh theo nhu cầu & thời gian nếu chưa có hội thoại cũ
        if (aiConversationHistory.length === 0) {
            renderSmartInitialSuggestions();
        }

        // Tự động ẩn tooltip sau 8 giây để không che màn hình
        setTimeout(() => {
            const tooltip = document.getElementById('aiChatTooltip');
            if (tooltip) tooltip.style.display = 'none';
        }, 8000);

        // Bắt sự kiện bàn phím IME tiếng Việt (chống submit nhầm khi gõ phím Enter chọn dấu)
        const inputEl = document.getElementById('aiChatInput');
        if (inputEl) {
            inputEl.addEventListener('keydown', (e) => {
                if (e.isComposing || e.keyCode === 229) {
                    return; // Đang gõ bộ gõ tiếng Việt (Unikey/EVKey) -> không submit
                }
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    handleAiChatSubmit(e);
                }
            });
        }
    });

    // Mở / Đóng cửa sổ Chat (Messenger Style: ẩn hiện nút tròn nổi mượt mà)
    function toggleAiChatbot(forceOpen) {
        const chatWindow = document.getElementById('aiChatWindow');
        const launcherBtn = document.getElementById('aiChatToggleBtn');
        const tooltip = document.getElementById('aiChatTooltip');
        const input = document.getElementById('aiChatInput');

        if (!chatWindow) return;

        const isHidden = chatWindow.classList.contains('hidden');
        const shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : isHidden;

        if (shouldOpen) {
            chatWindow.classList.remove('hidden');
            chatWindow.classList.add('flex');
            if (launcherBtn) launcherBtn.classList.add('hidden');
            if (tooltip) tooltip.style.display = 'none';

            // Khi mở khung chat, nếu chưa có hội thoại thì tự động tạo gợi ý bám sát nhu cầu & thời gian
            if (aiConversationHistory.length === 0) {
                renderSmartInitialSuggestions();
            }

            // Khóa cuộn trang web nền trên màn hình nhỏ để ngăn chặn thanh cuộn kép
            if (window.innerWidth < 640) {
                document.body.style.overflow = 'hidden';
            }

            // Phục hồi kích thước nếu đang ở trạng thái thu nhỏ
            if (isAiChatMinimized) {
                minimizeAiChatbot();
            }

            if (input) setTimeout(() => input.focus({ preventScroll: true }), 150);
            const area = document.getElementById('aiChatMessages');
            if (area) {
                // Đảm bảo mở khung chat thì thấy toàn bộ nội dung từ trên xuống, không tự ý trôi tuột xuống đáy
                area.scrollTop = 0;
            }
        } else {
            chatWindow.classList.remove('flex');
            chatWindow.classList.add('hidden');
            if (launcherBtn) launcherBtn.classList.remove('hidden');
            document.body.style.overflow = '';
        }
    }

    // Thu nhỏ / Phóng to cửa sổ chat giống Facebook Messenger tab
    function minimizeAiChatbot(e) {
        if (e && e.stopPropagation) e.stopPropagation();
        const chatWindow = document.getElementById('aiChatWindow');
        const minIcon = document.getElementById('aiChatMinIcon');
        const messages = document.getElementById('aiChatMessages');
        const footer = document.getElementById('aiChatFooter');
        const minBtn = document.getElementById('aiChatMinBtn');

        if (!chatWindow) return;

        isAiChatMinimized = !isAiChatMinimized;

        if (isAiChatMinimized) {
            chatWindow.style.setProperty('height', '40px', 'important');
            chatWindow.style.setProperty('max-height', '40px', 'important');
            document.body.style.overflow = '';
            if (messages) messages.classList.add('hidden');
            if (footer) footer.classList.add('hidden');
            if (minIcon) minIcon.innerText = 'crop_square';
            if (minBtn) minBtn.title = 'Mở rộng cửa sổ chat';
        } else {
            chatWindow.style.setProperty('height', '420px', 'important');
            chatWindow.style.setProperty('max-height', 'calc(100vh - 35px)', 'important');
            if (window.innerWidth < 640) {
                document.body.style.overflow = 'hidden';
            }
            if (messages) messages.classList.remove('hidden');
            if (footer) footer.classList.remove('hidden');
            if (minIcon) minIcon.innerText = 'remove';
            if (minBtn) minBtn.title = 'Thu nhỏ cửa sổ chat';
            const input = document.getElementById('aiChatInput');
            if (input) setTimeout(() => input.focus({ preventScroll: true }), 100);
            setTimeout(() => scrollAiChatToBottom(false), 50);
        }
    }

    // Nhấp vào tiêu đề khi đang thu nhỏ sẽ tự động mở lại
    function handleHeaderClick() {
        if (isAiChatMinimized) {
            minimizeAiChatbot();
        }
    }

    // Xóa lịch sử cuộc trò chuyện
    function clearAiChatHistory() {
        aiConversationHistory = [];
        try {
            sessionStorage.removeItem(AI_CHAT_SESSION_KEY);
        } catch (e) {}

        const container = document.getElementById('aiDynamicMessagesContainer');
        if (container) container.innerHTML = '';

        const chips = document.getElementById('aiSuggestionChips');
        if (chips) chips.classList.remove('hidden');

        const input = document.getElementById('aiChatInput');
        if (input) {
            input.disabled = false;
            input.focus({ preventScroll: true });
        }
        const area = document.getElementById('aiChatMessages');
        if (area) area.scrollTop = 0;
        isAiChatLoading = false;
        const sendBtn = document.getElementById('aiChatSendBtn');
        if (sendBtn) sendBtn.disabled = false;
    }

    // Gửi câu hỏi nhanh từ chip gợi ý (chống spam bấm nhiều lần khi đang tải, không gián đoạn ngữ cảnh)
    function sendAiQuickMessage(text) {
        if (isAiChatLoading) return;
        const cleanText = text.replace(/^[^\p{L}\p{N}]+/u, '').trim();
        const input = document.getElementById('aiChatInput');
        if (input) {
            input.value = cleanText;
            // Vô hiệu hóa ngay các nút gợi ý để tránh click lặp ngoài ý muốn
            document.querySelectorAll('.ai-suggestion-pills-row button').forEach(b => {
                b.disabled = true;
                b.style.pointerEvents = 'none';
            });
            handleAiChatSubmit(new Event('submit'));
        }
    }

    // Xử lý gửi tin nhắn (Bảo vệ chống gửi trùng lặp & xung đột race condition)
    function handleAiChatSubmit(e) {
        if (e && e.preventDefault) e.preventDefault();
        if (isAiChatLoading) return;

        const input = document.getElementById('aiChatInput');
        const sendBtn = document.getElementById('aiChatSendBtn');
        const typing = document.getElementById('aiChatTyping');
        const chips = document.getElementById('aiSuggestionChips');

        if (!input) return;
        const message = input.value.trim();
        if (message === '') return;

        // Bật cờ loading và vô hiệu hóa input tạm thời
        isAiChatLoading = true;
        input.disabled = true;
        if (sendBtn) sendBtn.disabled = true;

        // Ẩn chips mở đầu khi đã bắt đầu chat
        if (chips) chips.classList.add('hidden');

        // Chuẩn bị payload lịch sử ngữ cảnh TRƯỚC KHI push câu hỏi hiện tại (tránh lặp câu)
        const historyPayload = aiConversationHistory.slice(-8).map(h => ({
            role: h.role,
            content: (typeof h.content === 'string') ? h.content.slice(0, 4000) : '',
            venues: h.venues || []
        }));

        // Hiển thị bóng chat của người dùng
        const userBubble = appendUserMessageBubble(message, true);
        input.value = '';

        if (typing) {
            typing.classList.remove('hidden');
            typing.classList.add('flex');
        }

        // Định vị nhẹ nhàng ở tin nhắn người dùng vừa gửi
        if (userBubble) {
            scrollToMessage(userBubble, true);
        }

        // Lấy CSRF token nếu có
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

        fetch('/api/ai-chatbot/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                message: message,
                history: historyPayload
            })
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || `Lỗi phản hồi (${res.status})`);
            return data;
        })
        .then(data => {
            isAiChatLoading = false;
            if (typing) {
                typing.classList.remove('flex');
                typing.classList.add('hidden');
            }
            if (input) {
                input.disabled = false;
                input.focus({ preventScroll: true });
            }
            if (sendBtn) sendBtn.disabled = false;

            const reply = data.reply || 'Mình đã tìm thấy một số gợi ý phù hợp cho bạn.';
            const venues = data.venues || [];

            // Hiển thị tin nhắn AI và các card quán đề xuất
            const aiBubble = appendAiMessageBubble(reply, venues, true);

            // Cập nhật gợi ý tiếp theo nếu có
            if (data.suggestions && Array.isArray(data.suggestions) && data.suggestions.length > 0) {
                appendSuggestionPills(data.suggestions);
            }

            // Định vị ngay phần đầu tin nhắn phản hồi của Kira để người dùng đọc tự nhiên từ trên xuống, không tự ý trôi tuột xuống đáy
            if (aiBubble) {
                setTimeout(() => scrollToMessage(aiBubble, true), 40);
            }
        })
        .catch(err => {
            console.error('Lỗi gọi trợ lý AI:', err);
            isAiChatLoading = false;
            if (typing) {
                typing.classList.remove('flex');
                typing.classList.add('hidden');
            }
            if (input) {
                input.disabled = false;
                input.focus({ preventScroll: true });
            }
            if (sendBtn) sendBtn.disabled = false;

            // Xóa lượt tin nhắn user mồ côi vừa push để tránh mất đồng bộ luồng hội thoại
            if (aiConversationHistory.length > 0 && aiConversationHistory[aiConversationHistory.length - 1].role === 'user') {
                aiConversationHistory.pop();
                saveAiSession();
            }

            const errBubble = appendAiMessageBubble("Xin lỗi bạn, kết nối trợ lý AI hiện đang bận hoặc gián đoạn. Bạn vui lòng thử lại câu hỏi sau giây lát nhé!", [], false);
            if (errBubble) {
                setTimeout(() => scrollToMessage(errBubble, true), 40);
            }
        });
    }

    // Thêm bong bóng chat người dùng (Facebook Messenger Mini Style)
    function appendUserMessageBubble(text, saveHistory) {
        const container = document.getElementById('aiDynamicMessagesContainer');
        if (!container) return null;

        const bubble = document.createElement('div');
        bubble.className = 'flex justify-end';
        bubble.innerHTML = `
            <div class="bg-gradient-to-r from-orange-500 via-primary to-orange-600 text-white px-2.5 py-1.5 rounded-[14px] rounded-br-[3px] shadow-2xs max-w-[85%] text-[11.5px] leading-relaxed break-words select-text">
                ${escapeHtml(text)}
            </div>
        `;
        container.appendChild(bubble);

        if (saveHistory) {
            aiConversationHistory.push({ role: 'user', content: text });
            saveAiSession();
        }

        return bubble;
    }

    // Thêm bong bóng chat AI (Facebook Messenger Mini Style kèm Avatar Logo)
    function appendAiMessageBubble(markdownText, venues, saveHistory) {
        const container = document.getElementById('aiDynamicMessagesContainer');
        if (!container) return null;

        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-start gap-1.5 max-w-[96%]';

        let venuesHtml = '';
        if (venues && venues.length > 0) {
            venuesHtml = `
                <div class="mt-1.5 space-y-1">
                    <div class="flex items-center justify-between">
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[11px] text-primary">storefront</span>
                            Địa điểm AI đề xuất (${venues.length})
                        </p>
                    </div>
                    <div class="space-y-1">
                        ${venues.map(v => renderVenueCard(v)).join('')}
                    </div>
                </div>
            `;
        }

        wrapper.innerHTML = `
            <div class="kira-avatar-badge" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; margin-top: 2px;">
                ${BOT_AVATAR_SVG}
            </div>
            <div class="bg-white text-[#111827] px-2.5 py-1.5 rounded-[14px] rounded-tl-[3px] text-[11.5px] leading-relaxed break-words flex-1 min-w-0 shadow-2xs border border-gray-100/90 select-text">
                <div class="ai-chat-markdown space-y-1">${renderSimpleMarkdown(markdownText)}</div>
                ${venuesHtml}
            </div>
        `;

        container.appendChild(wrapper);

        if (saveHistory) {
            aiConversationHistory.push({ role: 'assistant', content: markdownText, venues: venues });
            saveAiSession();
        }

        return wrapper;
    }

    // Render thẻ quán ăn trực quan thu nhỏ như link preview Facebook Messenger
    function renderVenueCard(v) {
        const hasRating = (v.rating && Number(v.rating) > 0);
        const star = hasRating ? `⭐ ${v.rating}/5` : '✨ Quán mới';
        const reviews = (v.review_count && Number(v.review_count) > 0) ? `(${v.review_count} review)` : '';

        const statusBadge = v.is_open_now 
            ? `<span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[8px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Đang mở</span>`
            : `<span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[8px] font-medium bg-gray-100 text-gray-500"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Đóng cửa</span>`;
        
        const verifiedBadge = v.is_verified 
            ? `<span class="material-symbols-outlined text-[12px] text-sky-500 shrink-0" title="Quán đã xác thực">verified</span>` 
            : '';

        const featuredBadge = v.is_featured 
            ? `<span class="px-1 py-0.5 rounded text-[8px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">Nổi bật</span>` 
            : '';

        const promoBadge = v.promotion 
            ? `<div class="mt-0.5 text-[9px] text-rose-600 font-semibold truncate flex items-center gap-0.5 bg-rose-50/70 px-1.5 py-0.5 rounded border border-rose-100">
                    <span class="material-symbols-outlined text-[10px] shrink-0">local_offer</span>
                    <span class="truncate">${escapeHtml(v.promotion)}</span>
               </div>`
            : '';

        const hoursLine = (v.open_hours && v.open_hours !== 'Chưa cập nhật giờ')
            ? `<div class="flex items-center gap-1 text-[9px] text-gray-500 truncate mt-0.5">
                    <span class="material-symbols-outlined text-[10px] text-gray-400 shrink-0">schedule</span>
                    <span class="truncate">${escapeHtml(v.open_hours)}</span>
               </div>`
            : '';

        const dishesBadge = (v.highlight_dishes && v.highlight_dishes.length > 0)
            ? `<div class="flex flex-wrap gap-1 mt-1">
                    ${v.highlight_dishes.slice(0, 3).map(d => `<span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[8.5px] font-medium border border-slate-200/60">${escapeHtml(d)}</span>`).join('')}
               </div>`
            : '';

        let safeUrl = (v.url || '').replace(/^https?:\/\/[^\/]+/i, '');
        if (!safeUrl.startsWith('/')) safeUrl = '/' + safeUrl;

        return `
            <a href="${safeUrl}" target="_self" class="block p-2 rounded-lg border border-gray-200/80 hover:border-primary/50 bg-[#f9fafb] hover:bg-orange-50/25 transition-all group shadow-2xs hover:shadow-xs">
                <div class="flex items-start gap-2">
                    <div class="kira-venue-thumb relative" style="width: 38px; height: 38px; min-width: 38px; min-height: 38px;">
                        <img src="${v.image}" 
                             alt="${escapeHtml(v.name)}" 
                             style="width: 100%; height: 100%; object-fit: cover;"
                             class="group-hover:scale-105 transition-transform duration-300"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=160&q=80';" />
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <div class="flex items-center gap-1 min-w-0">
                                <h5 class="font-bold text-[11px] text-gray-900 truncate group-hover:text-primary transition-colors">${escapeHtml(v.name)}</h5>
                                ${verifiedBadge}
                                ${featuredBadge}
                            </div>
                            ${statusBadge}
                        </div>
                        
                        <p class="text-[9.5px] text-gray-500 truncate mt-0.5 flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[10px] text-gray-400 shrink-0">location_on</span>
                            <span class="truncate">${escapeHtml(v.address)}</span>
                        </p>

                        ${hoursLine}

                        <div class="flex items-center gap-1 mt-0.5 text-[9px]">
                            <span class="font-bold text-amber-600">${star} ${reviews}</span>
                            <span class="text-gray-300">•</span>
                            <span class="font-semibold text-gray-700 truncate">${escapeHtml(v.price_range)}</span>
                        </div>

                        ${promoBadge}
                    </div>

                    <div class="kira-venue-arrow bg-white group-hover:bg-primary group-hover:text-white transition-colors text-gray-400 self-center border border-gray-200/60 shadow-2xs" style="width: 18px; height: 18px; min-width: 18px; min-height: 18px;">
                        <span class="material-symbols-outlined text-[11px]">chevron_right</span>
                    </div>
                </div>
                ${dishesBadge}
            </a>
        `;
    }

    // Hiển thị hàng nút gợi ý câu hỏi tiếp theo bám sát cuộc trò chuyện (Không có nút đổi thủ công)
    function appendSuggestionPills(suggestions) {
        const container = document.getElementById('aiDynamicMessagesContainer');
        if (!container) return;

        // Xóa gợi ý cũ trước đó
        const oldPills = container.querySelectorAll('.ai-suggestion-pills-row');
        oldPills.forEach(p => p.remove());

        let list = Array.isArray(suggestions) && suggestions.length > 0 ? suggestions : [];
        if (list.length === 0) {
            list = [
                'Xem menu và giá các món',
                'Quán này có khuyến mãi gì không?',
                'Tìm quán tương tự ở gần hơn',
                'Chỉ đường tới quán gần nhất'
            ];
        }

        const row = document.createElement('div');
        row.className = 'ai-suggestion-pills-row flex flex-wrap items-center gap-1 pl-0 sm:pl-7 pt-1';
        row.innerHTML = list.slice(0, 4).map(s => {
            const clean = s.replace(/^[^\p{L}\p{N}]+/u, '').trim();
            const icon = getSuggestionIcon(clean);
            return `
                <button type="button" 
                        onclick="sendAiQuickMessage('${escapeHtml(clean)}')" 
                        class="ai-quick-chip cursor-pointer">
                    <span class="text-[10px] shrink-0">${icon}</span>
                    <span class="truncate">${escapeHtml(clean)}</span>
                </button>
            `;
        }).join('');

        container.appendChild(row);
    }

    // Lưu session chat
    function saveAiSession() {
        try {
            sessionStorage.setItem(AI_CHAT_SESSION_KEY, JSON.stringify(aiConversationHistory.slice(-12)));
        } catch (e) {}
    }

    // Cuộn tập trung vào phần đầu tin nhắn mới để người dùng đọc tự nhiên từ trên xuống, không tự ý trôi tuột xuống đáy
    function scrollToMessage(element, smooth = true) {
        const area = document.getElementById('aiChatMessages');
        if (!area || !element) return;
        requestAnimationFrame(() => {
            try {
                const areaRect = area.getBoundingClientRect();
                const elemRect = element.getBoundingClientRect();
                const offset = elemRect.top - areaRect.top + area.scrollTop - 6;
                area.scrollTo({
                    top: Math.max(0, offset),
                    behavior: smooth ? 'smooth' : 'auto'
                });
            } catch (e) {
                element.scrollIntoView({ behavior: smooth ? 'smooth' : 'auto', block: 'nearest' });
            }
        });
    }

    // Cuộn nhẹ nhàng tới tin nhắn mới nhất trong chuỗi
    function scrollAiChatToBottom(smooth = true) {
        const area = document.getElementById('aiChatMessages');
        if (!area) return;
        const lastMsg = area.querySelector('#aiDynamicMessagesContainer > div:last-child');
        if (lastMsg) {
            scrollToMessage(lastMsg, smooth);
        }
    }

    // Chuyển đổi Markdown có cấu trúc sang HTML an toàn, gọn gàng, chuẩn xác (Hỗ trợ tiêu đề, bảng, danh sách, khối code)
    // Gom các dòng văn bản liền kề thành đoạn văn thống nhất, không ngắt dòng hay chia nhỏ câu tùy tiện
    function renderSimpleMarkdown(md) {
        if (!md) return '';

        const rawLines = md.split(/\r?\n/);
        let html = '';
        let inUl = false;
        let inOl = false;
        let inCodeBlock = false;
        let inTable = false;
        let paragraphBuffer = [];

        function flushParagraph() {
            if (paragraphBuffer.length > 0) {
                const text = paragraphBuffer.join(' ').trim();
                if (text) {
                    html += `<p class="leading-relaxed mb-1.5 text-gray-800 text-[12px]">${formatInlineMarkdown(text)}</p>`;
                }
                paragraphBuffer = [];
            }
        }

        for (let i = 0; i < rawLines.length; i++) {
            let line = rawLines[i];
            let trimmed = line.trim();

            // Khối code đa dòng (```)
            if (/^```/.test(trimmed)) {
                flushParagraph();
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                if (inTable) { html += '</tbody></table></div>'; inTable = false; }

                if (inCodeBlock) {
                    html += '</code></pre>';
                    inCodeBlock = false;
                } else {
                    html += '<pre class="bg-gray-800 text-gray-100 p-2 rounded-lg text-[10.5px] overflow-x-auto my-1 font-mono shadow-inner"><code>';
                    inCodeBlock = true;
                }
                continue;
            }

            if (inCodeBlock) {
                html += escapeHtml(line) + '\n';
                continue;
            }

            if (!trimmed) {
                flushParagraph();
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                if (inTable) { html += '</tbody></table></div>'; inTable = false; }
                continue;
            }

            // Headers
            if (/^###\s+(.+)$/.test(trimmed)) {
                flushParagraph();
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                if (inTable) { html += '</tbody></table></div>'; inTable = false; }
                const content = trimmed.replace(/^###\s+/, '');
                html += `<h5 class="font-bold text-[11.5px] text-gray-900 mt-1.5 mb-0.5">${formatInlineMarkdown(content)}</h5>`;
                continue;
            }
            if (/^##\s+(.+)$/.test(trimmed)) {
                flushParagraph();
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                if (inTable) { html += '</tbody></table></div>'; inTable = false; }
                const content = trimmed.replace(/^##\s+/, '');
                html += `<h4 class="font-bold text-[12px] text-gray-900 mt-2 mb-0.5">${formatInlineMarkdown(content)}</h4>`;
                continue;
            }
            if (/^#\s+(.+)$/.test(trimmed)) {
                flushParagraph();
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                if (inTable) { html += '</tbody></table></div>'; inTable = false; }
                const content = trimmed.replace(/^#\s+/, '');
                html += `<h4 class="font-bold text-[12.5px] text-gray-900 mt-2 mb-1">${formatInlineMarkdown(content)}</h4>`;
                continue;
            }

            // Tables: Cột phân cách bằng |
            if (trimmed.startsWith('|') && trimmed.endsWith('|')) {
                flushParagraph();
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }

                // Bỏ qua dòng phân cách |---|---|
                if (/^\|[\s\-:|]+\|$/.test(trimmed)) {
                    continue;
                }

                const cells = trimmed.slice(1, -1).split('|').map(c => c.trim());
                if (!inTable) {
                    html += '<div class="overflow-x-auto my-1 rounded-lg border border-gray-200"><table class="w-full text-left text-[10.5px] border-collapse bg-white"><tbody class="divide-y divide-gray-100">';
                    inTable = true;
                }

                html += '<tr class="even:bg-gray-50/50">';
                cells.forEach(cell => {
                    html += `<td class="p-1 border-r border-gray-100 last:border-r-0 leading-tight">${formatInlineMarkdown(cell)}</td>`;
                });
                html += '</tr>';
                continue;
            } else if (inTable) {
                html += '</tbody></table></div>';
                inTable = false;
            }

            // Bullet list (- or *)
            if (/^[-*]\s+(.+)$/.test(trimmed)) {
                flushParagraph();
                if (inOl) { html += '</ol>'; inOl = false; }
                if (!inUl) { html += '<ul class="list-disc pl-3.5 my-1 space-y-0.5 text-gray-700 text-[12px]">'; inUl = true; }
                const content = trimmed.replace(/^[-*]\s+/, '');
                html += `<li class="leading-relaxed">${formatInlineMarkdown(content)}</li>`;
                continue;
            }

            // Ordered list (1. 2. 3.) - Hiển thị số thứ tự chuẩn xác 1, 2, 3 bằng badge đẹp mắt, không bao giờ bị reset về 1
            const olMatch = trimmed.match(/^(\d+)\.\s+(.+)$/);
            if (olMatch) {
                flushParagraph();
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                const num = olMatch[1];
                const content = olMatch[2];
                html += `<div class="mt-1.5 mb-1 leading-relaxed flex items-start gap-1.5"><span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-orange-100 text-primary text-[9px] font-black shrink-0 mt-0.5 shadow-2xs">${num}</span><div class="min-w-0 flex-1 text-[12px] text-gray-800">${formatInlineMarkdown(content)}</div></div>`;
                continue;
            }

            // Blockquote
            if (/^>\s+(.+)$/.test(trimmed)) {
                flushParagraph();
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                const content = trimmed.replace(/^>\s+/, '');
                html += `<div class="border-l-2 border-primary/50 pl-2 py-0.5 my-1 text-gray-600 bg-orange-50/40 rounded-r text-[11px] leading-relaxed">${formatInlineMarkdown(content)}</div>`;
                continue;
            }

            // Dòng văn bản bình thường: gom vào buffer đoạn văn hiện tại để tránh ngắt câu vô lý
            paragraphBuffer.push(trimmed);
        }

        flushParagraph();
        if (inUl) html += '</ul>';
        if (inOl) html += '</ol>';
        if (inTable) html += '</tbody></table></div>';
        if (inCodeBlock) html += '</code></pre>';

        return html;
    }

    function formatInlineMarkdown(text) {
        let s = escapeHtml(text);
        // Inline code: `code`
        s = s.replace(/`([^`\n]+)`/g, '<code class="px-1 py-0.2 bg-gray-200/80 rounded text-[10.5px] font-mono text-pink-700">$1</code>');
        // Links: [Text](url) -> Chuyển trực tiếp trong cùng trang, không nhảy tab hay sang trang khác
        s = s.replace(/\[(.*?)\]\((https?:\/\/[^\s)]+|\/[^\s)]+)\)/g, (match, label, url) => {
            let safeUrl = url.replace(/^https?:\/\/[^\/]+/i, '');
            if (!safeUrl.startsWith('/')) safeUrl = '/' + safeUrl;
            return `<a href="${safeUrl}" target="_self" class="font-bold text-primary hover:underline hover:text-orange-600 transition-colors">${label}</a>`;
        });
        // Bold: **text**
        s = s.replace(/\*\*(.*?)\*\*/g, '<b class="font-bold text-gray-900">$1</b>');
        // Italic: *text*
        s = s.replace(/\*([^*\n]+)\*/g, '<em class="italic text-gray-600">$1</em>');
        return s;
    }

    // Escape HTML an toàn chống XSS
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>
