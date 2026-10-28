<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\ThanhToan;
use App\Models\User;
use App\Services\SePayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NapTienController extends Controller
{
    protected SePayService $sePayService;

    public function __construct(SePayService $sePayService)
    {
        $this->sePayService = $sePayService;
    }


    /**
     * Màn hình quản lý Ví tiền và Nạp tiền riêng biệt
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $lichSuNapTien = ThanhToan::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('nguoi-dung.tai-khoan.vi-tien', compact('user', 'lichSuNapTien'));
    }

    /**
     * Khởi tạo giao dịch nạp tiền (Hỗ trợ VietQR on-site và SePay PG)
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'so_tien' => 'required|numeric|min:10000|max:100000000',
            'phuong_thuc' => 'nullable|string|in:ALL,CARD,BANK_TRANSFER,NAPAS_BANK_TRANSFER,VIETQR,SEPAY_GATEWAY',
        ], [
            'so_tien.required' => 'Vui lòng chọn hoặc nhập số tiền muốn nạp.',
            'so_tien.numeric' => 'Số tiền nạp phải là dạng số hợp lệ.',
            'so_tien.min' => 'Số tiền nạp tối thiểu là 10.000 VNĐ.',
            'so_tien.max' => 'Số tiền nạp tối đa mỗi lần là 100.000.000 VNĐ.',
            'phuong_thuc.in' => 'Phương thức thanh toán không hợp lệ.',
        ]);

        $user = Auth::user();
        $amount = (float) $validated['so_tien'];
        $paymentMethod = (!empty($validated['phuong_thuc']) && !in_array($validated['phuong_thuc'], ['ALL', 'SEPAY_GATEWAY', 'VIETQR'], true))
            ? $validated['phuong_thuc']
            : null;

        try {
            $checkoutInfo = $this->sePayService->createDepositCheckout($user, $amount, $paymentMethod);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Khởi tạo đơn nạp tiền thành công.',
                    'code' => $checkoutInfo['code'],
                    'invoice_number' => $checkoutInfo['invoice_number'],
                    'ma_giao_dich' => $checkoutInfo['invoice_number'],
                    'amount' => $checkoutInfo['amount'],
                    'so_tien' => $checkoutInfo['amount'],
                    'amount_formatted' => $checkoutInfo['amount_formatted'],
                    'bank_name' => $checkoutInfo['bank_name'],
                    'acc_number' => $checkoutInfo['acc_number'],
                    'acc_holder' => $checkoutInfo['acc_holder'],
                    'qr_url' => $checkoutInfo['qr_url'],
                    'status_url' => $checkoutInfo['status_url'],
                    'action_url' => $checkoutInfo['action_url'],
                    'fields' => $checkoutInfo['fields'],
                    'vietqr' => [
                        'bank_name' => $checkoutInfo['bank_name'],
                        'account_number' => $checkoutInfo['acc_number'],
                        'account_holder' => $checkoutInfo['acc_holder'],
                        'qr_url' => $checkoutInfo['qr_url'],
                    ],
                ]);
            }

            return view('nguoi-dung.tai-khoan.nap-tien-redirect', compact('checkoutInfo'));
        } catch (\Throwable $e) {
            Log::error('Lỗi khi tạo đơn nạp tiền SePay: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $amount,
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể khởi tạo thanh toán: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('tai-khoan.vi-tien')->with('error', 'Không thể khởi tạo thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Tra cứu trạng thái giao dịch nạp tiền theo mã hóa đơn
     * Dùng cho frontend polling theo thời gian thực (3s/lần)
     */
    public function status(Request $request, string $code): JsonResponse
    {
        $thanhToan = ThanhToan::where('ma_giao_dich', $code)->first();

        if (! $thanhToan) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'message' => 'Không tìm thấy thông tin giao dịch nạp tiền.',
            ], 404);
        }

        // Nếu trạng thái vẫn đang chờ, chủ động đối soát với SePay API
        if ($thanhToan->trang_thai === 'cho_thanh_toan') {
            $this->sePayService->syncOrderStatusWithSePay($code);
            $thanhToan->refresh();
        }

        $user = $thanhToan->user_id ? User::find($thanhToan->user_id) : null;

        return response()->json([
            'success' => true,
            'code' => $code,
            'status' => $thanhToan->trang_thai, // 'cho_thanh_toan' | 'thanh_cong' | 'that_bai' | 'da_huy'
            'amount' => (float) $thanhToan->so_tien,
            'amount_formatted' => number_format($thanhToan->so_tien, 0, ',', '.') . ' VNĐ',
            'so_du' => $user ? (float) $user->so_du : 0,
            'so_du_formatted' => $user ? number_format($user->so_du, 0, ',', '.') . ' VNĐ' : '0 VNĐ',
            'paid_at' => $thanhToan->updated_at ? $thanhToan->updated_at->format('H:i:s d/m/Y') : null,
        ]);
    }

    /**
     * Endpoint nhận SePay Webhooks (Biến động số dư VietQR)
     * Tài liệu: https://developer.sepay.vn/vi/sepay-webhooks
     */
    public function webhook(Request $request): JsonResponse
    {
        if (! $this->sePayService->verifyWebhookAuth($request)) {
            Log::warning('SePay Webhook: Xác thực thất bại.', [
                'ip' => $request->ip(),
                'auth' => $request->header('Authorization'),
                'signature' => $request->header('X-SePay-Signature'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid credentials or signature',
            ], 401);
        }

        $payload = $request->json()->all();
        if (empty($payload)) {
            $payload = $request->all();
        }

        Log::info('SePay Webhook Payload nhận được:', $payload);

        try {
            $this->sePayService->processWebhook($payload);

            // SePay Webhook yêu cầu trả về HTTP 200 kèm đúng {"success": true} trong vòng 30s
            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('SePay Webhook Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $payload,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook IPN nhận kết quả thanh toán từ SePay Gateway
     */
    public function ipn(Request $request): JsonResponse
    {
        $secretKey = $request->header('X-Secret-Key');

        if (! $this->sePayService->verifyIpnSecretKey($secretKey)) {
            Log::warning('SePay IPN: Xác thực X-Secret-Key thất bại.', [
                'ip' => $request->ip(),
                'received_key' => $secretKey ? substr($secretKey, 0, 8) . '...' : 'NULL',
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unauthorized: Invalid or missing X-Secret-Key',
            ], 401);
        }

        $payload = $request->json()->all();
        if (empty($payload)) {
            $payload = $request->all();
        }

        Log::info('SePay IPN Request nhận được:', $payload);

        try {
            $result = $this->sePayService->processIpn($payload);

            return response()->json([
                'success' => $result['success'] ?? true,
                'message' => $result['message'] ?? 'Webhook processed successfully',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('SePay IPN Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $payload,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error while processing IPN: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Callback khi SePay Gateway chuyển hướng về sau khi thanh toán thành công
     */
    public function success(Request $request): RedirectResponse
    {
        $invoiceNumber = $request->query('invoice');
        $thanhToan = null;
        $amountFormatted = '';

        if ($invoiceNumber) {
            $syncResult = $this->sePayService->syncOrderStatusWithSePay($invoiceNumber);
            $thanhToan = ThanhToan::where('ma_giao_dich', $invoiceNumber)->first();
            $amountFormatted = $thanhToan ? number_format($thanhToan->so_tien, 0, ',', '.') . ' VNĐ' : '';

            if (($syncResult['status'] ?? null) === 'thanh_cong' || ($thanhToan && $thanhToan->trang_thai === 'thanh_cong')) {
                return redirect()->route('tai-khoan.vi-tien')
                    ->with('success', "Nạp tiền thành công! Số tiền {$amountFormatted} đã được cộng ngay vào ví của bạn.");
            }
        }

        return redirect()->route('tai-khoan.vi-tien')
            ->with('success', "Giao dịch nạp tiền ({$amountFormatted}) đã được tiếp nhận thành công! Số dư tài khoản sẽ được cập nhật tự động ngay khi ngân hàng xác nhận.");
    }

    /**
     * Callback khi SePay Gateway chuyển hướng về khi thanh toán thất bại
     */
    public function error(Request $request): RedirectResponse
    {
        $invoiceNumber = $request->query('invoice');

        if ($invoiceNumber) {
            $thanhToan = ThanhToan::where('ma_giao_dich', $invoiceNumber)->first();
            if ($thanhToan && $thanhToan->trang_thai === 'cho_thanh_toan') {
                $thanhToan->trang_thai = 'that_bai';
                $thanhToan->ghi_chu = 'Thanh toán không thành công hoặc lỗi từ cổng SePay';
                $thanhToan->save();
            }
        }

        return redirect()->route('tai-khoan.vi-tien')
            ->with('error', 'Quá trình thanh toán nạp tiền đã gặp lỗi hoặc không hoàn tất. Vui lòng thử lại hoặc liên hệ hỗ trợ nếu cần giải đáp.');
    }

    /**
     * Callback khi người dùng hủy bỏ thanh toán tại cổng SePay Gateway
     */
    public function cancel(Request $request): RedirectResponse
    {
        $invoiceNumber = $request->query('invoice');

        if ($invoiceNumber) {
            $thanhToan = ThanhToan::where('ma_giao_dich', $invoiceNumber)->first();
            if ($thanhToan && $thanhToan->trang_thai === 'cho_thanh_toan') {
                $thanhToan->trang_thai = 'da_huy';
                $thanhToan->ghi_chu = 'Khách hàng bấm hủy giao dịch tại cổng SePay';
                $thanhToan->save();
            }
        }

        return redirect()->route('tai-khoan.vi-tien')
            ->with('info', 'Bạn đã hủy bỏ yêu cầu nạp tiền.');
    }
}
