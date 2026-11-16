<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait HandlesCrudSecurity
{
    /**
     * Kiểm tra ID hợp lệ (UUID hoặc Integer)
     */
    protected function isValidId(mixed $id, string $type = 'uuid'): bool
    {
        if (empty($id)) {
            return false;
        }

        if ($type === 'uuid') {
            return is_string($id) && Str::isUuid($id);
        }

        if ($type === 'int' || $type === 'integer') {
            return is_numeric($id) && (int) $id > 0;
        }

        return true;
    }

    /**
     * Chuẩn hóa khoảng trắng và số Full-width (zenkaku ０-９ -> 0-9)
     */
    protected function sanitizeInput(array $data, array $numericFields = []): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // 1. Chuyển đổi khoảng trắng toàn giác (zenkaku space \x{3000}) thành nửa giác (half-width space)
                $value = preg_replace('/[\x{3000}\s]+/u', ' ', $value);
                $value = trim($value);

                // 2. Chuyển đổi số Full-width (０-９) thành số Half-width (0-9)
                if (in_array($key, $numericFields, true) || preg_match('/^[０-９\s\.\,\-]+$/u', $value)) {
                    $value = mb_convert_kana($value, 'n', 'UTF-8');
                }

                $sanitized[$key] = $value;
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value, $numericFields);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Kiểm tra xung đột cập nhật đồng thời giữa nhiều tab (Optimistic Locking)
     */
    protected function checkConcurrentUpdate(Model $model, Request $request, string $field = 'last_updated_at'): ?JsonResponse
    {
        if (! $request->filled($field)) {
            return null;
        }

        $submittedTimeStr = $request->input($field);
        try {
            $submittedTime = Carbon::parse($submittedTimeStr);
            $modelUpdatedAt = $model->updated_at;

            if ($modelUpdatedAt && $modelUpdatedAt->gt($submittedTime->addSeconds(1))) {
                $message = 'Dữ liệu đã được cập nhật bởi một phiên làm việc khác (hoặc tab khác). Vui lòng tải lại trang để xem dữ liệu mới nhất trước khi chỉnh sửa.';

                return response()->json([
                    'success' => false,
                    'conflict' => true,
                    'message' => $message,
                ], 409);
            }
        } catch (\Throwable $e) {
            // Parse error: bỏ qua để tiếp tục quy trình
        }

        return null;
    }

    /**
     * Trả về phản hồi lỗi khi bản ghi không tồn tại hoặc đã bị xóa
     */
    protected function recordNotFoundResponse(Request $request, string $modelName = 'Bản ghi', ?string $redirectRoute = null): JsonResponse|RedirectResponse
    {
        $message = "{$modelName} không tồn tại hoặc đã được xóa trước đó.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'not_found' => true,
                'message' => $message,
            ], 404);
        }

        if ($redirectRoute) {
            return redirect()->route($redirectRoute)->with('warning', $message);
        }

        return redirect()->back()->with('warning', $message);
    }

    /**
     * Trả về phản hồi lỗi khi ID URL không hợp lệ
     */
    protected function invalidIdResponse(Request $request, ?string $redirectRoute = null): JsonResponse|RedirectResponse
    {
        $message = 'Mã định danh (ID) trong đường dẫn không hợp lệ hoặc sai định dạng.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 400);
        }

        if ($redirectRoute) {
            return redirect()->route($redirectRoute)->with('error', $message);
        }

        return redirect()->back()->with('error', $message);
    }
}
