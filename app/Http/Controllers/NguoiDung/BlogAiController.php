<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Services\BlogAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogAiController extends Controller
{
    protected BlogAiService $aiService;

    public function __construct(BlogAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Gợi ý tiêu đề hấp dẫn với AI
     */
    public function suggestTitles(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'nullable|string|max:500',
            'current_title' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'style' => 'nullable|string|max:100',
        ]);

        $topic = $request->input('topic') ?: ($request->input('current_title') ?: $request->input('keywords', ''));
        $titles = $this->aiService->suggestTitles(
            $topic, 
            $request->input('current_title'), 
            $request->input('keywords'),
            $request->input('style')
        );

        return response()->json([
            'success' => true,
            'titles' => $titles,
        ]);
    }

    /**
     * Tạo dàn ý bài viết review ẩm thực dựa trên từ khóa đầu vào
     */
    public function generateOutline(Request $request): JsonResponse
    {
        $request->validate([
            'keywords' => 'required|string|max:1000',
            'restaurant_name' => 'nullable|string|max:255',
            'dish_type' => 'nullable|string|max:255',
            'style' => 'nullable|string|max:100',
        ]);

        $outline = $this->aiService->generateReviewOutline(
            $request->input('keywords'),
            $request->input('restaurant_name'),
            $request->input('dish_type'),
            $request->input('style')
        );

        return response()->json([
            'success' => true,
            'outline' => $outline,
        ]);
    }

    /**
     * Tự động sửa lỗi chính tả / chuẩn hóa văn phong
     */
    public function fixSpelling(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'text' => 'nullable|string',
            'field' => 'nullable|string|in:title,excerpt,content,all',
        ]);

        if ($request->has('title') || $request->has('content') || $request->has('excerpt')) {
            $result = $this->aiService->fixStructuredSpelling(
                $request->input('title', ''),
                $request->input('excerpt', ''),
                $request->input('content', '')
            );
        } else {
            $result = $this->aiService->fixSpellingAndGrammar($request->input('text', ''));
        }

        return response()->json([
            'success' => true,
            'result' => $result,
        ]);
    }

    /**
     * Tự động phân tích bài viết và đề xuất Thẻ (Tags) cùng Danh mục phù hợp
     */
    public function suggestTagsCategory(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:1000',
        ]);

        $title = $request->input('title', '');
        $content = $request->input('content', '');
        $excerpt = $request->input('excerpt', '');

        if (trim($title) === '' && trim($content) === '') {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập tiêu đề hoặc nội dung bài viết để AI có dữ liệu phân tích.',
            ], 422);
        }

        $analysis = $this->aiService->suggestTagsAndCategory($title, $content, $excerpt);

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }
}
