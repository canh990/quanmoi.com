<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QuanAiChatbotService;
use Illuminate\Http\Request;

class AiChatbotController extends Controller
{
    public function __construct(
        protected QuanAiChatbotService $chatbotService
    ) {}

    /**
     * Endpoint trò chuyện cùng trợ lý AI ẩm thực
     */
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant,bot'],
            'history.*.content' => ['required_with:history', 'string', 'max:15000'],
            'history.*.venues' => ['nullable', 'array'],
        ], [
            'message.required' => 'Vui lòng nhập nội dung câu hỏi để trò chuyện cùng trợ lý AI.',
            'message.max' => 'Nội dung câu hỏi không được vượt quá 2000 ký tự.',
        ]);

        $message = trim(strip_tags($validated['message']));
        $history = $request->input('history', []);

        $response = $this->chatbotService->chat($message, $history);

        return response()->json($response);
    }

    /**
     * Lấy danh sách câu hỏi gợi ý mở đầu tự động bám sát theo nhu cầu
     */
    public function suggestions(Request $request)
    {
        $context = [
            'hour' => (int) $request->input('hour', now()->hour),
            'day_of_week' => (int) $request->input('day_of_week', now()->dayOfWeek),
            'location' => trim((string) $request->input('location', '')),
        ];

        return response()->json([
            'success' => true,
            'suggestions' => $this->chatbotService->getDefaultSuggestions($context),
        ]);
    }
}
