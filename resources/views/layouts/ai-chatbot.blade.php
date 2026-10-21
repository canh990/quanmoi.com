{{-- Floating AI Chatbot Widget: Kira AI Assistant (Facebook Messenger Style) --}}

{{-- Global SVG Definition for Modern AI Kira Logo --}}
<svg class="sr-only" aria-hidden="true">
    <defs>
        {{-- High-end Sunset Gradient for Messenger Bubble --}}
        <linearGradient id="kiraLogoBg" x1="10%" y1="0%" x2="90%" y2="100%">
            <stop offset="0%" stop-color="#FF7A00" />
            <stop offset="45%" stop-color="#FF4D00" />
            <stop offset="100%" stop-color="#B83200" />
        </linearGradient>
        {{-- Sparkling Gold / Pearl gradient for AI Star --}}
        <linearGradient id="kiraSparkleGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#FFFFFF" />
            <stop offset="60%" stop-color="#FFF3E0" />
            <stop offset="100%" stop-color="#FFE0B2" />
        </linearGradient>
        {{-- Subtle soft ambient shadow --}}
        <filter id="kiraShadow" x="-10%" y="-10%" width="120%" height="120%">
            <feDropShadow dx="0" dy="2" stdDeviation="2.5" flood-color="#000000" flood-opacity="0.18" />
        </filter>
        {{-- Master Logo Vector Symbol --}}
        <g id="kira-ai-logo">
            <!-- Messenger-style smooth curved speech bubble -->
            <path d="M50 8C26.8 8 8 24.8 8 45.5C8 57.3 13.9 67.8 23.2 74.6C24.4 75.5 25.1 76.9 24.8 78.4L22.6 88.5C22.2 90.3 24 91.8 25.6 90.9L37.5 84.1C38.6 83.5 39.9 83.3 41.2 83.6C44 84.2 47 84.5 50 84.5C73.2 84.5 92 67.7 92 47C92 26.3 73.2 8 50 8Z" 
                  fill="url(#kiraLogoBg)" filter="url(#kiraShadow)" />
            <!-- Soft inner glass highlight -->
            <path d="M22 22C30 14 42 11 58 11C74 11 83 18 83 18" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-opacity="0.35" />
            <!-- AI Multi-point Modern Sparkle in the center -->
            <g transform="translate(50, 46.5)">
                <path d="M0 -22 C0 -8 8 0 22 0 C8 0 0 8 0 22 C0 8 -8 0 -22 0 C-8 0 0 -8 0 -22 Z" 
                      fill="url(#kiraSparkleGrad)" />
                <path d="M15 -14 C15 -8 19 -5 25 -5 C19 -5 15 -1 15 5 C15 -1 11 -5 5 -5 C11 -5 15 -8 15 -14 Z" 
                      fill="#FFFFFF" opacity="0.9" transform="scale(0.55)" />
                <circle cx="0" cy="0" r="3.2" fill="#FF5722" opacity="0.8" />
            </g>
        </g>
    </defs>
</svg>

<div id="aiChatbotContainer" class="fixed bottom-4 right-4 sm:bottom-5 sm:right-5 z-50 flex flex-col items-end print:hidden pointer-events-none">

    {{-- Chat Window (Facebook Messenger Pop-up Style - Compact & Sleek) --}}
    <div id="aiChatWindow" 
         class="hidden flex-col w-[332px] sm:w-[340px] max-w-[calc(100vw-24px)] h-[460px] max-h-[calc(100vh-80px)] bg-white rounded-2xl border border-gray-200/90 shadow-[0_12px_40px_rgba(0,0,0,0.16)] overflow-hidden transition-all duration-300 origin-bottom-right pointer-events-auto">
        
        {{-- Messenger Clean White Header with Status & Action Buttons --}}
        <div id="aiChatHeader" 
             onclick="handleHeaderClick()" 
             class="h-[50px] px-3.5 py-2 bg-white border-b border-gray-100 flex items-center justify-between shrink-0 shadow-[0_1px_3px_rgba(0,0,0,0.04)] select-none cursor-pointer">
            
            {{-- Bot Avatar & Status Info --}}
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="relative w-8 h-8 rounded-full shrink-0 flex items-center justify-center">
                    <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-2xs"><use href="#kira-ai-logo"></use></svg>
                    {{-- Active status green dot --}}
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white ring-1 ring-emerald-500/20"></span>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h4 class="font-bold text-[13px] tracking-tight truncate text-gray-900 leading-tight">Kira AI</h4>
                        <span class="px-1.5 py-0.2 rounded-full bg-primary/10 text-[9px] font-bold text-primary">Trợ lý</span>
                    </div>
                    <p class="text-[10.5px] text-emerald-600 font-medium truncate leading-tight mt-0.5 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Đang hoạt động
                    </p>
                </div>
            </div>

            {{-- Facebook Messenger Window Controls (Refresh, Minimize, Close) --}}
            <div class="flex items-center gap-0.5 shrink-0" onclick="event.stopPropagation()">
                <button type="button" 
                        onclick="clearAiChatHistory()" 
                        class="w-7 h-7 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer" 
                        title="Làm mới cuộc trò chuyện">
                    <span class="material-symbols-outlined text-[17px]">refresh</span>
                </button>
                <button type="button" 
                        id="aiChatMinBtn"
                        onclick="minimizeAiChatbot(event)" 
                        class="w-7 h-7 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer" 
                        title="Thu nhỏ cửa sổ chat">
                    <span id="aiChatMinIcon" class="material-symbols-outlined text-[18px]">remove</span>
                </button>
                <button type="button" 
                        onclick="toggleAiChatbot(false)" 
                        class="w-7 h-7 rounded-full flex items-center justify-center text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" 
                        title="Đóng cửa sổ chat">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </div>

        {{-- Messages Scroll Area (Facebook Messenger Styled) --}}
        <div id="aiChatMessages" class="flex-1 p-3 overflow-y-auto space-y-3 bg-[#f8f9fa] text-[12.5px] custom-scrollbar">
            
            {{-- Initial AI Greeting Message --}}
            <div class="flex items-start gap-2 max-w-[92%]">
                <div class="w-6 h-6 rounded-full shrink-0 mt-0.5 overflow-hidden flex items-center justify-center">
                    <svg viewBox="0 0 100 100" class="w-full h-full"><use href="#kira-ai-logo"></use></svg>
                </div>
                <div class="bg-[#f0f2f5] text-[#050505] p-3 rounded-[18px] rounded-tl-[4px] leading-relaxed space-y-1.5 shadow-2xs">
                    <p>Chào bạn! 👋 Mình là <b>Kira AI</b>. Mình có thể gợi ý quán lẩu nướng, cafe chill, quán ăn đêm hoặc món ngon theo ngân sách của bạn.</p>
                    <p class="text-[11px] text-gray-500 font-medium">Bấm nhanh câu hỏi bên dưới hoặc nhắn tin cho mình nhé:</p>
                </div>
            </div>

            {{-- Quick Suggestion Chips Container --}}
            <div id="aiSuggestionChips" class="flex flex-wrap gap-1.5 pl-8">
                <button type="button" onclick="sendAiQuickMessage(this.innerText)" class="px-2.5 py-1 rounded-full bg-orange-50 border border-orange-200 hover:border-primary hover:text-primary text-primary text-[10.5px] font-semibold transition-all shadow-2xs hover:shadow-xs cursor-pointer flex items-center gap-1">
                    <span>✨</span> Gợi ý quán mới
                </button>
                <button type="button" onclick="sendAiQuickMessage(this.innerText)" class="px-2.5 py-1 rounded-full bg-white border border-gray-200 hover:border-primary hover:text-primary text-gray-700 text-[10.5px] font-medium transition-all shadow-2xs hover:shadow-xs cursor-pointer flex items-center gap-1">
                    <span>🔄</span> Làm mới quán khác
                </button>
                <button type="button" onclick="sendAiQuickMessage(this.innerText)" class="px-2.5 py-1 rounded-full bg-white border border-gray-200 hover:border-primary hover:text-primary text-gray-700 text-[10.5px] font-medium transition-all shadow-2xs hover:shadow-xs cursor-pointer flex items-center gap-1">
                    <span>🔥</span> Lẩu nướng cuối tuần
                </button>
                <button type="button" onclick="sendAiQuickMessage(this.innerText)" class="px-2.5 py-1 rounded-full bg-white border border-gray-200 hover:border-primary hover:text-primary text-gray-700 text-[10.5px] font-medium transition-all shadow-2xs hover:shadow-xs cursor-pointer flex items-center gap-1">
                    <span>☕</span> Cafe chill làm việc Q1
                </button>
                <button type="button" onclick="sendAiQuickMessage(this.innerText)" class="px-2.5 py-1 rounded-full bg-white border border-gray-200 hover:border-primary hover:text-primary text-gray-700 text-[10.5px] font-medium transition-all shadow-2xs hover:shadow-xs cursor-pointer flex items-center gap-1">
                    <span>🍜</span> Món ngon dưới 50k
                </button>
                <button type="button" onclick="sendAiQuickMessage(this.innerText)" class="px-2.5 py-1 rounded-full bg-white border border-gray-200 hover:border-primary hover:text-primary text-gray-700 text-[10.5px] font-medium transition-all shadow-2xs hover:shadow-xs cursor-pointer flex items-center gap-1">
                    <span>🌙</span> Quán ăn đêm 24/7
                </button>
            </div>

            {{-- Dynamic Messages injected here by JavaScript --}}
            <div id="aiDynamicMessagesContainer" class="space-y-3"></div>

            {{-- Loading / Typing Indicator (Facebook Messenger Style) --}}
            <div id="aiChatTyping" class="hidden items-start gap-2 max-w-[85%]">
                <div class="w-6 h-6 rounded-full shrink-0 mt-0.5 overflow-hidden flex items-center justify-center">
                    <svg viewBox="0 0 100 100" class="w-full h-full"><use href="#kira-ai-logo"></use></svg>
                </div>
                <div class="bg-[#f0f2f5] px-3.5 py-2.5 rounded-[18px] rounded-tl-[4px] flex items-center gap-1 shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0ms;"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 150ms;"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 300ms;"></span>
                    <span class="text-[10px] text-gray-400 font-medium ml-1">Kira đang tìm quán...</span>
                </div>
            </div>
        </div>

        {{-- Facebook Messenger Input Bar --}}
        <div id="aiChatFooter" class="p-2.5 bg-white border-t border-gray-100 shrink-0">
            <form id="aiChatForm" onsubmit="handleAiChatSubmit(event)" class="relative flex items-center gap-1.5">
                <div class="flex-1 bg-[#f0f2f5] hover:bg-[#e4e6eb] focus-within:bg-white focus-within:border-primary/50 focus-within:ring-2 focus-within:ring-primary/15 border border-transparent rounded-full px-3.5 py-1.5 flex items-center transition-all">
                    <input type="text" 
                           id="aiChatInput" 
                           placeholder="Hỏi quán ngon, cafe chill..." 
                           autocomplete="off"
                           maxlength="1000"
                           class="w-full bg-transparent outline-none text-[12.5px] text-gray-800 placeholder-gray-400" />
                </div>
                
                <button type="submit" 
                        id="aiChatSendBtn"
                        class="w-8 h-8 rounded-full bg-primary hover:bg-primary/90 text-white flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95 cursor-pointer shrink-0 disabled:opacity-30 disabled:pointer-events-none"
                        title="Gửi câu hỏi (Enter)">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                </button>
            </form>
            <div class="flex items-center justify-between mt-1.5 px-1.5 text-[9.5px] text-gray-400 select-none">
                <span>Dữ liệu thực tế Quán Mới</span>
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
                class="relative w-13 h-13 rounded-full bg-gradient-to-tr from-primary via-[#b94a00] to-orange-500 text-white flex items-center justify-center shadow-[0_8px_25px_rgba(160,65,0,0.35)] hover:shadow-[0_12px_32px_rgba(160,65,0,0.5)] hover:scale-106 active:scale-95 transition-all duration-300 cursor-pointer group"
                title="Mở Trợ lý AI Quán Mới"
                aria-label="Mở Trợ lý AI Quán Mới">
            
            {{-- Breathing ambient glow --}}
            <span class="absolute inset-0 rounded-full bg-primary/40 animate-ping -z-10 opacity-70"></span>

            {{-- Modern AI Logo SVG inside floating button --}}
            <div class="w-8 h-8 transition-transform group-hover:scale-110 group-hover:rotate-6">
                <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-sm"><use href="#kira-ai-logo"></use></svg>
            </div>

            {{-- Unread Sparkle Badge --}}
            <span class="absolute -top-0.5 -right-0.5 w-4.5 h-4.5 rounded-full bg-amber-400 text-slate-950 font-bold text-[9px] flex items-center justify-center border-2 border-white shadow-xs">
                ✨
            </span>
        </button>
    </div>
</div>

{{-- Chatbot Styles & Client-side Script --}}
<style>
    .ai-chat-markdown p { margin-bottom: 0.35rem; line-height: 1.5; }
    .ai-chat-markdown p:last-child { margin-bottom: 0; }
    .ai-chat-markdown ul { padding-left: 1.15rem; margin-top: 0.25rem; margin-bottom: 0.35rem; list-style-type: disc; }
    .ai-chat-markdown ol { padding-left: 1.25rem; margin-top: 0.25rem; margin-bottom: 0.35rem; list-style-type: decimal; }
    .ai-chat-markdown li { margin-bottom: 0.2rem; line-height: 1.45; }
    .ai-chat-markdown strong, .ai-chat-markdown b { font-weight: 700; color: #0f172a; }
    .ai-chat-markdown a { color: #b94a00; font-weight: 600; text-decoration: underline; text-underline-offset: 2px; }
    .ai-chat-markdown a:hover { color: #ea580c; }
    .ai-chat-markdown h4, .ai-chat-markdown h5 { font-weight: 700; color: #1e293b; }
</style>

<script>
    const AI_CHAT_SESSION_KEY = 'quanmoi_ai_chat_history_v1';
    let aiConversationHistory = [];
    let isAiChatMinimized = false;
    let isAiChatLoading = false;

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
                    }
                }
            }
        } catch (e) {
            console.warn('Lỗi đọc session AI chat:', e);
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
            inputEl.addEventListener('focus', () => {
                scrollAiChatToBottom();
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

            // Phục hồi kích thước nếu đang ở trạng thái thu nhỏ
            if (isAiChatMinimized) {
                minimizeAiChatbot();
            }

            if (input) setTimeout(() => input.focus(), 150);
            scrollAiChatToBottom();
        } else {
            chatWindow.classList.remove('flex');
            chatWindow.classList.add('hidden');
            if (launcherBtn) launcherBtn.classList.remove('hidden');
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
            chatWindow.style.height = '50px';
            if (messages) messages.classList.add('hidden');
            if (footer) footer.classList.add('hidden');
            if (minIcon) minIcon.innerText = 'crop_square';
            if (minBtn) minBtn.title = 'Mở rộng cửa sổ chat';
        } else {
            chatWindow.style.height = '';
            if (messages) messages.classList.remove('hidden');
            if (footer) footer.classList.remove('hidden');
            if (minIcon) minIcon.innerText = 'remove';
            if (minBtn) minBtn.title = 'Thu nhỏ cửa sổ chat';
            scrollAiChatToBottom();
            const input = document.getElementById('aiChatInput');
            if (input) setTimeout(() => input.focus(), 100);
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
            input.focus();
        }
        isAiChatLoading = false;
        const sendBtn = document.getElementById('aiChatSendBtn');
        if (sendBtn) sendBtn.disabled = false;
    }

    // Gửi câu hỏi nhanh từ chip gợi ý (chống spam bấm nhiều lần khi đang tải)
    function sendAiQuickMessage(text) {
        if (isAiChatLoading) return;
        const cleanText = text.replace(/^[^\p{L}\p{N}]+/u, '').trim();
        const input = document.getElementById('aiChatInput');
        if (input) {
            input.value = cleanText;
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
        appendUserMessageBubble(message, true);
        input.value = '';

        if (typing) {
            typing.classList.remove('hidden');
            typing.classList.add('flex');
        }
        scrollAiChatToBottom();

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
                input.focus();
            }
            if (sendBtn) sendBtn.disabled = false;

            const reply = data.reply || 'Mình đã tìm thấy một số gợi ý phù hợp cho bạn.';
            const venues = data.venues || [];

            // Hiển thị tin nhắn AI và các card quán đề xuất
            appendAiMessageBubble(reply, venues, true);

            // Cập nhật gợi ý tiếp theo nếu có
            if (data.suggestions && Array.isArray(data.suggestions) && data.suggestions.length > 0) {
                appendSuggestionPills(data.suggestions);
            }

            scrollAiChatToBottom();
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
                input.focus();
            }
            if (sendBtn) sendBtn.disabled = false;

            // Xóa lượt tin nhắn user mồ côi vừa push để tránh mất đồng bộ luồng hội thoại
            if (aiConversationHistory.length > 0 && aiConversationHistory[aiConversationHistory.length - 1].role === 'user') {
                aiConversationHistory.pop();
                saveAiSession();
            }

            appendAiMessageBubble("Xin lỗi bạn, kết nối trợ lý AI hiện đang bận hoặc gián đoạn. Bạn vui lòng thử lại câu hỏi sau giây lát nhé!", [], false);
            scrollAiChatToBottom();
        });
    }

    // Thêm bong bóng chat người dùng (Facebook Messenger Style)
    function appendUserMessageBubble(text, saveHistory) {
        const container = document.getElementById('aiDynamicMessagesContainer');
        if (!container) return;

        const bubble = document.createElement('div');
        bubble.className = 'flex justify-end';
        bubble.innerHTML = `
            <div class="bg-gradient-to-r from-primary via-[#b94a00] to-orange-500 text-white px-3.5 py-2 rounded-[18px] rounded-br-[4px] shadow-2xs max-w-[82%] text-[12.5px] leading-relaxed break-words">
                ${escapeHtml(text)}
            </div>
        `;
        container.appendChild(bubble);

        if (saveHistory) {
            aiConversationHistory.push({ role: 'user', content: text });
            saveAiSession();
        }
    }

    // Thêm bong bóng chat AI (Facebook Messenger Style kèm Avatar Logo hiện đại)
    function appendAiMessageBubble(markdownText, venues, saveHistory) {
        const container = document.getElementById('aiDynamicMessagesContainer');
        if (!container) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-start gap-2 max-w-[95%]';

        let venuesHtml = '';
        if (venues && venues.length > 0) {
            venuesHtml = `
                <div class="mt-2.5 space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px] text-primary">storefront</span>
                            Địa điểm đề xuất (${venues.length})
                        </p>
                        <button type="button" onclick="sendAiQuickMessage('Làm mới gợi ý quán khác')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-orange-50 hover:bg-orange-100 text-primary text-[9.5px] font-semibold border border-orange-200 transition-colors cursor-pointer" title="Làm mới quán khác không trùng lặp">
                            <span class="material-symbols-outlined text-[11px]">refresh</span> Đổi quán khác
                        </button>
                    </div>
                    <div class="space-y-1.5">
                        ${venues.map(v => renderVenueCard(v)).join('')}
                    </div>
                </div>
            `;
        }

        wrapper.innerHTML = `
            <div class="w-6 h-6 rounded-full shrink-0 mt-0.5 overflow-hidden flex items-center justify-center">
                <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-2xs"><use href="#kira-ai-logo"></use></svg>
            </div>
            <div class="bg-[#f0f2f5] text-[#050505] px-3.5 py-2.5 rounded-[18px] rounded-tl-[4px] text-[12.5px] leading-relaxed break-words flex-1 min-w-0 shadow-2xs">
                <div class="ai-chat-markdown space-y-1">${renderSimpleMarkdown(markdownText)}</div>
                ${venuesHtml}
            </div>
        `;

        container.appendChild(wrapper);

        if (saveHistory) {
            aiConversationHistory.push({ role: 'assistant', content: markdownText, venues: venues });
            saveAiSession();
        }
    }

    // Render thẻ quán ăn trực quan thu nhỏ như link preview Facebook Messenger
    function renderVenueCard(v) {
        const hasRating = (v.rating && Number(v.rating) > 0);
        const star = hasRating ? `⭐ ${v.rating}/5` : '✨ Quán mới';
        const reviews = (v.review_count && Number(v.review_count) > 0) ? `(${v.review_count} review)` : '';

        const statusBadge = v.is_open_now 
            ? `<span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-full text-[8.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Đang mở</span>`
            : `<span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-full text-[8.5px] font-medium bg-gray-100 text-gray-500"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Đóng cửa</span>`;
        
        const verifiedBadge = v.is_verified 
            ? `<span class="material-symbols-outlined text-[13px] text-sky-500 shrink-0" title="Quán đã xác thực">verified</span>` 
            : '';

        const featuredBadge = v.is_featured 
            ? `<span class="px-1 py-0.2 rounded text-[8px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">Nổi bật</span>` 
            : '';

        const promoBadge = v.promotion 
            ? `<div class="mt-1 text-[9.5px] text-rose-600 font-semibold truncate flex items-center gap-1 bg-rose-50/70 px-1.5 py-0.5 rounded border border-rose-100">
                    <span class="material-symbols-outlined text-[11px] shrink-0">local_offer</span>
                    <span class="truncate">${escapeHtml(v.promotion)}</span>
               </div>`
            : '';

        const hoursLine = (v.open_hours && v.open_hours !== 'Chưa cập nhật giờ')
            ? `<div class="flex items-center gap-1 text-[9.5px] text-gray-500 truncate mt-0.5">
                    <span class="material-symbols-outlined text-[11px] text-gray-400 shrink-0">schedule</span>
                    <span class="truncate">${escapeHtml(v.open_hours)}</span>
               </div>`
            : '';

        const dishesBadge = (v.highlight_dishes && v.highlight_dishes.length > 0)
            ? `<div class="flex flex-wrap gap-1 mt-1.5">
                    ${v.highlight_dishes.slice(0, 3).map(d => `<span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-700 text-[8.5px] font-medium border border-slate-200/60">${escapeHtml(d)}</span>`).join('')}
               </div>`
            : '';

        return `
            <a href="${v.url}" target="_blank" rel="noopener" class="block p-2.5 rounded-xl border border-gray-200/80 hover:border-primary/50 bg-white hover:bg-orange-50/15 transition-all group shadow-2xs hover:shadow-xs">
                <div class="flex items-start gap-2.5">
                    <div class="relative w-12 h-12 rounded-lg overflow-hidden shrink-0 border border-gray-100">
                        <img src="${v.image}" 
                             alt="${escapeHtml(v.name)}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=200&q=80';" />
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <div class="flex items-center gap-1 min-w-0">
                                <h5 class="font-bold text-[11.5px] text-gray-900 truncate group-hover:text-primary transition-colors">${escapeHtml(v.name)}</h5>
                                ${verifiedBadge}
                                ${featuredBadge}
                            </div>
                            ${statusBadge}
                        </div>
                        
                        <p class="text-[10px] text-gray-500 truncate mt-0.5 flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[10.5px] text-gray-400 shrink-0">location_on</span>
                            <span class="truncate">${escapeHtml(v.address)}</span>
                        </p>

                        ${hoursLine}

                        <div class="flex items-center gap-1.5 mt-1 text-[9.5px]">
                            <span class="font-bold text-amber-600">${star} ${reviews}</span>
                            <span class="text-gray-300">•</span>
                            <span class="font-semibold text-gray-700 truncate">${escapeHtml(v.price_range)}</span>
                        </div>

                        ${promoBadge}
                    </div>

                    <div class="w-5 h-5 rounded-full bg-gray-100 group-hover:bg-primary group-hover:text-white flex items-center justify-center shrink-0 transition-colors text-gray-400 self-center">
                        <span class="material-symbols-outlined text-[13px]">chevron_right</span>
                    </div>
                </div>
                ${dishesBadge}
            </a>
        `;
    }

    // Hiển thị hàng nút gợi ý câu hỏi tiếp theo
    function appendSuggestionPills(suggestions) {
        const container = document.getElementById('aiDynamicMessagesContainer');
        if (!container || !suggestions || suggestions.length === 0) return;

        const row = document.createElement('div');
        row.className = 'flex flex-wrap gap-1.5 pl-8 pt-1';
        row.innerHTML = suggestions.map(s => `
            <button type="button" onclick="sendAiQuickMessage(this.innerText)" class="px-2.5 py-1 rounded-full bg-white border border-gray-200 hover:border-primary hover:text-primary text-gray-600 text-[10px] font-medium transition-all shadow-2xs hover:shadow-xs cursor-pointer flex items-center gap-1">
                <span>👉</span> ${escapeHtml(s)}
            </button>
        `).join('');

        container.appendChild(row);
    }

    // Lưu session chat
    function saveAiSession() {
        try {
            sessionStorage.setItem(AI_CHAT_SESSION_KEY, JSON.stringify(aiConversationHistory.slice(-12)));
        } catch (e) {}
    }

    // Cuộn xuống đáy chat
    function scrollAiChatToBottom() {
        const area = document.getElementById('aiChatMessages');
        if (area) {
            setTimeout(() => {
                area.scrollTop = area.scrollHeight;
            }, 60);
        }
    }

    // Chuyển đổi Markdown có cấu trúc sang HTML an toàn, gọn gàng, chuẩn xác (Hỗ trợ tiêu đề, bảng, danh sách, khối code)
    function renderSimpleMarkdown(md) {
        if (!md) return '';

        const rawLines = md.split(/\r?\n/);
        let html = '';
        let inUl = false;
        let inOl = false;
        let inCodeBlock = false;
        let inTable = false;

        for (let i = 0; i < rawLines.length; i++) {
            let line = rawLines[i];
            let trimmed = line.trim();

            // Khối code đa dòng (```)
            if (/^```/.test(trimmed)) {
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                if (inTable) { html += '</tbody></table></div>'; inTable = false; }

                if (inCodeBlock) {
                    html += '</code></pre>';
                    inCodeBlock = false;
                } else {
                    html += '<pre class="bg-gray-800 text-gray-100 p-2.5 rounded-lg text-[11px] overflow-x-auto my-1.5 font-mono shadow-inner"><code>';
                    inCodeBlock = true;
                }
                continue;
            }

            if (inCodeBlock) {
                html += escapeHtml(line) + '\n';
                continue;
            }

            if (!trimmed) {
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                if (inTable) { html += '</tbody></table></div>'; inTable = false; }
                continue;
            }

            // Headers
            if (/^###\s+(.+)$/.test(trimmed)) {
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                if (inTable) { html += '</tbody></table></div>'; inTable = false; }
                const content = trimmed.replace(/^###\s+/, '');
                html += `<h5 class="font-bold text-[12px] text-gray-900 mt-2 mb-1">${formatInlineMarkdown(content)}</h5>`;
                continue;
            }
            if (/^##\s+(.+)$/.test(trimmed)) {
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                if (inTable) { html += '</tbody></table></div>'; inTable = false; }
                const content = trimmed.replace(/^##\s+/, '');
                html += `<h4 class="font-bold text-[12.5px] text-gray-900 mt-2.5 mb-1">${formatInlineMarkdown(content)}</h4>`;
                continue;
            }
            if (/^#\s+(.+)$/.test(trimmed)) {
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
                if (inTable) { html += '</tbody></table></div>'; inTable = false; }
                const content = trimmed.replace(/^#\s+/, '');
                html += `<h4 class="font-bold text-[13px] text-gray-900 mt-2.5 mb-1.5">${formatInlineMarkdown(content)}</h4>`;
                continue;
            }

            // Tables: Cột phân cách bằng |
            if (trimmed.startsWith('|') && trimmed.endsWith('|')) {
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }

                // Bỏ qua dòng phân cách |---|---|
                if (/^\|[\s\-:|]+\|$/.test(trimmed)) {
                    continue;
                }

                const cells = trimmed.slice(1, -1).split('|').map(c => c.trim());
                if (!inTable) {
                    html += '<div class="overflow-x-auto my-1.5 rounded-lg border border-gray-200"><table class="w-full text-left text-[11px] border-collapse bg-white"><tbody class="divide-y divide-gray-100">';
                    inTable = true;
                }

                html += '<tr class="even:bg-gray-50/50">';
                cells.forEach(cell => {
                    html += `<td class="p-1.5 border-r border-gray-100 last:border-r-0 leading-tight">${formatInlineMarkdown(cell)}</td>`;
                });
                html += '</tr>';
                continue;
            } else if (inTable) {
                html += '</tbody></table></div>';
                inTable = false;
            }

            // Bullet list (- or *)
            if (/^[-*]\s+(.+)$/.test(trimmed)) {
                if (inOl) { html += '</ol>'; inOl = false; }
                if (!inUl) { html += '<ul class="list-disc pl-4 my-1 space-y-0.5 text-gray-700">'; inUl = true; }
                const content = trimmed.replace(/^[-*]\s+/, '');
                html += `<li>${formatInlineMarkdown(content)}</li>`;
                continue;
            }

            // Ordered list (1. 2. 3.)
            if (/^\d+\.\s+(.+)$/.test(trimmed)) {
                if (inUl) { html += '</ul>'; inUl = false; }
                if (!inOl) { html += '<ol class="list-decimal pl-4 my-1 space-y-0.5 text-gray-700">'; inOl = true; }
                const content = trimmed.replace(/^\d+\.\s+/, '');
                html += `<li>${formatInlineMarkdown(content)}</li>`;
                continue;
            }

            // Regular line / paragraph
            if (inUl) { html += '</ul>'; inUl = false; }
            if (inOl) { html += '</ol>'; inOl = false; }

            // Blockquote
            if (/^>\s+(.+)$/.test(trimmed)) {
                const content = trimmed.replace(/^>\s+/, '');
                html += `<div class="border-l-2 border-primary/50 pl-2.5 py-0.5 my-1 text-gray-600 bg-orange-50/40 rounded-r text-[11px] leading-relaxed">${formatInlineMarkdown(content)}</div>`;
                continue;
            }

            html += `<p class="leading-relaxed mb-1">${formatInlineMarkdown(trimmed)}</p>`;
        }

        if (inUl) html += '</ul>';
        if (inOl) html += '</ol>';
        if (inTable) html += '</tbody></table></div>';
        if (inCodeBlock) html += '</code></pre>';

        return html;
    }

    function formatInlineMarkdown(text) {
        let s = escapeHtml(text);
        // Inline code: `code`
        s = s.replace(/`([^`\n]+)`/g, '<code class="px-1 py-0.2 bg-gray-200/80 rounded text-[11px] font-mono text-pink-700">$1</code>');
        // Links: [Text](url)
        s = s.replace(/\[(.*?)\]\((https?:\/\/[^\s)]+|\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener" class="font-bold text-primary hover:underline hover:text-orange-600 transition-colors">$1</a>');
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
