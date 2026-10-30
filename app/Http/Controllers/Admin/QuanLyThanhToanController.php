<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\ThanhToan;
use App\Models\User;
use App\Notifications\WalletTransactionNotification;
use App\Services\SePayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuanLyThanhToanController extends Controller
{
    public function __construct(protected SePayService $sePayService)
    {
    }

    /**
     * Danh sách giao dịch nạp tiền & Thống kê doanh thu ví
     */
    public function index(Request $request)
    {
        $query = ThanhToan::with(['user', 'quan']);

        // 1. Tìm kiếm theo mã giao dịch, mã SePay hoặc thông tin người dùng
        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('ma_giao_dich', 'like', "%{$search}%")
                  ->orWhere('sepay_transaction_id', 'like', "%{$search}%")
                  ->orWhere('sepay_order_id', 'like', "%{$search}%")
                  ->orWhere('ghi_chu', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('ho_ten', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('so_dien_thoai', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Lọc theo trạng thái
        $trangThai = (string) $request->input('trang_thai', 'all');
        if ($trangThai !== '' && $trangThai !== 'all') {
            if ($trangThai === 'cho_thanh_toan') {
                $query->whereIn('trang_thai', ['cho_thanh_toan', 'cho_xu_ly']);
            } else {
                $query->where('trang_thai', $trangThai);
            }
        }

        // 3. Lọc theo phương thức thanh toán
        $phuongThuc = (string) $request->input('phuong_thuc', 'all');
        if ($phuongThuc !== '' && $phuongThuc !== 'all') {
            $query->where('phuong_thuc', $phuongThuc);
        }

        // 4. Lọc theo khoảng ngày
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');
        if ($tuNgay) {
            try {
                $query->where('created_at', '>=', Carbon::parse($tuNgay)->startOfDay());
            } catch (\Throwable $e) {
            }
        }
        if ($denNgay) {
            try {
                $query->where('created_at', '<=', Carbon::parse($denNgay)->endOfDay());
            } catch (\Throwable $e) {
            }
        }

        // Tính tổng tiền theo bộ lọc hiện tại (chỉ tính đơn thành công)
        $filteredSuccessSum = (clone $query)->where('trang_thai', 'thanh_cong')->sum('so_tien');
        $filteredCount = (clone $query)->count();

        $giaoDichs = $query->latest()->paginate(15)->withQueryString();

        // KPI tổng quan toàn hệ thống
        $stats = [
            'tong_doanh_thu' => (float) ThanhToan::where('trang_thai', 'thanh_cong')->sum('so_tien'),
            'doanh_thu_thang_nay' => (float) ThanhToan::where('trang_thai', 'thanh_cong')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('so_tien'),
            'doanh_thu_hom_nay' => (float) ThanhToan::where('trang_thai', 'thanh_cong')
                ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
                ->sum('so_tien'),
            'tong_don_thanh_cong' => ThanhToan::where('trang_thai', 'thanh_cong')->count(),
            'tong_don_cho' => ThanhToan::whereIn('trang_thai', ['cho_thanh_toan', 'cho_xu_ly'])->count(),
            'tong_don_huy_loi' => ThanhToan::whereIn('trang_thai', ['da_huy', 'that_bai'])->count(),
            'tong_so_du_vi' => (float) User::sum('so_du'),
            'filtered_success_sum' => (float) $filteredSuccessSum,
            'filtered_count' => $filteredCount,
        ];

        return view('admin.thanh-toan.index', compact('giaoDichs', 'stats'));
    }

    /**
     * Duyệt thủ công đơn nạp tiền đang chờ xử lý (Cộng tiền vào ví an toàn trong DB Transaction)
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'ghi_chu_admin' => ['nullable', 'string', 'max:255'],
            'ma_tham_chieu' => ['nullable', 'string', 'max:100'],
        ]);

        $ghiChuAdmin = trim((string) $request->input('ghi_chu_admin', ''));
        $maThamChieu = trim((string) $request->input('ma_tham_chieu', ''));

        $result = DB::transaction(function () use ($id, $ghiChuAdmin, $maThamChieu) {
            $thanhToan = ThanhToan::lockForUpdate()->findOrFail($id);

            if ($thanhToan->trang_thai === 'thanh_cong') {
                return [
                    'success' => false,
                    'message' => "Giao dịch #{$thanhToan->ma_giao_dich} đã được duyệt thành công trước đó.",
                ];
            }

            if (! in_array($thanhToan->trang_thai, ['cho_thanh_toan', 'cho_xu_ly'], true)) {
                return [
                    'success' => false,
                    'message' => "Chỉ có thể duyệt các giao dịch đang ở trạng thái chờ thanh toán.",
                ];
            }

            $amount = (float) $thanhToan->so_tien;
            $adminName = Auth::user()->ho_ten ?? 'Admin';

            $thanhToan->trang_thai = 'thanh_cong';
            if ($maThamChieu !== '') {
                $thanhToan->sepay_transaction_id = $maThamChieu;
            }
            $thanhToan->ghi_chu = $ghiChuAdmin !== ''
                ? "Admin ({$adminName}) xác nhận thủ công: {$ghiChuAdmin}"
                : "Admin ({$adminName}) xác nhận đã nhận chuyển khoản thành công";
            $thanhToan->save();

            if ($thanhToan->user_id) {
                $user = User::lockForUpdate()->find($thanhToan->user_id);
                if ($user) {
                    $oldBalance = (float) $user->so_du;
                    $newBalance = $oldBalance + $amount;
                    $user->so_du = $newBalance;
                    $user->save();

                    try {
                        AdminAuditLog::create([
                            'actor_id' => Auth::id(),
                            'action' => 'admin.payment.approve',
                            'target_type' => ThanhToan::class,
                            'target_id' => (string) $thanhToan->id,
                            'metadata' => [
                                'ma_giao_dich' => $thanhToan->ma_giao_dich,
                                'user_id' => $user->id,
                                'user_email' => $user->email,
                                'so_tien' => $amount,
                                'so_du_truoc' => $oldBalance,
                                'so_du_sau' => $newBalance,
                                'ma_tham_chieu' => $maThamChieu ?: null,
                                'ghi_chu' => $thanhToan->ghi_chu,
                            ],
                            'ip_address' => request()->ip(),
                        ]);
                    } catch (\Throwable $e) {
                        Log::warning('Không thể ghi AdminAuditLog khi duyệt nạp tiền: ' . $e->getMessage());
                    }

                    $this->sePayService->notifyDepositSuccess($user, $thanhToan->ma_giao_dich, $amount, $newBalance);
                }
            }

            return [
                'success' => true,
                'message' => "Đã duyệt giao dịch #{$thanhToan->ma_giao_dich} và cộng +" . number_format($amount, 0, ',', '.') . "đ vào ví khách hàng.",
            ];
        });

        Cache::forget('admin_dashboard_overview_stats');
        Cache::forget('admin_dashboard_charts_6m');

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Hủy giao dịch đang chờ thanh toán
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'ly_do_huy' => ['nullable', 'string', 'max:255'],
        ]);

        $lyDo = trim((string) $request->input('ly_do_huy', ''));

        $thanhToan = ThanhToan::findOrFail($id);

        if ($thanhToan->trang_thai === 'thanh_cong') {
            return back()->with('error', "Không thể hủy giao dịch #{$thanhToan->ma_giao_dich} vì đã hoàn tất thành công.");
        }

        if ($thanhToan->trang_thai === 'da_huy') {
            return back()->with('info', "Giao dịch #{$thanhToan->ma_giao_dich} đã ở trạng thái hủy.");
        }

        $adminName = Auth::user()->ho_ten ?? 'Admin';
        $thanhToan->trang_thai = 'da_huy';
        $thanhToan->ghi_chu = $lyDo !== ''
            ? "Admin ({$adminName}) hủy đơn: {$lyDo}"
            : "Đã hủy bởi Admin ({$adminName})";
        $thanhToan->save();

        try {
            AdminAuditLog::create([
                'actor_id' => Auth::id(),
                'action' => 'admin.payment.cancel',
                'target_type' => ThanhToan::class,
                'target_id' => (string) $thanhToan->id,
                'metadata' => [
                    'ma_giao_dich' => $thanhToan->ma_giao_dich,
                    'so_tien' => (float) $thanhToan->so_tien,
                    'ly_do' => $thanhToan->ghi_chu,
                ],
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Không thể ghi AdminAuditLog khi hủy nạp tiền: ' . $e->getMessage());
        }

        return back()->with('success', "Đã hủy giao dịch #{$thanhToan->ma_giao_dich}.");
    }

    /**
     * Đồng bộ trạng thái đơn hàng trực tiếp từ cổng SePay API
     */
    public function syncSePay($id)
    {
        $thanhToan = ThanhToan::findOrFail($id);

        if ($thanhToan->trang_thai === 'thanh_cong') {
            return back()->with('info', "Giao dịch #{$thanhToan->ma_giao_dich} đã ở trạng thái thành công.");
        }

        $syncResult = $this->sePayService->syncOrderStatusWithSePay($thanhToan->ma_giao_dich);

        Cache::forget('admin_dashboard_overview_stats');
        Cache::forget('admin_dashboard_charts_6m');

        if (($syncResult['status'] ?? '') === 'thanh_cong') {
            return back()->with('success', "Đồng bộ SePay thành công: Đơn #{$thanhToan->ma_giao_dich} đã thanh toán và cộng tiền vào ví!");
        }

        return back()->with('info', "Kết quả đối soát SePay cho #{$thanhToan->ma_giao_dich}: " . ($syncResult['message'] ?? 'Chưa ghi nhận thanh toán mới.'));
    }

    /**
     * Điều chỉnh / Nạp bù số dư ví cho người dùng (Có lưu lịch sử ThanhToan & AuditLog)
     */
    public function adjustBalance(Request $request)
    {
        $validated = $request->validate([
            'email_hoac_id' => ['required', 'string', 'max:255'],
            'loai_dieu_chinh' => ['required', 'in:cong_tien,tru_tien'],
            'so_tien' => ['required', 'numeric', 'min:1000', 'max:500000000'],
            'ghi_chu' => ['required', 'string', 'max:255'],
        ], [
            'email_hoac_id.required' => 'Vui lòng nhập Email hoặc ID tài khoản người dùng.',
            'so_tien.required' => 'Vui lòng nhập số tiền cần điều chỉnh.',
            'so_tien.min' => 'Số tiền điều chỉnh tối thiểu là 1.000đ.',
            'ghi_chu.required' => 'Vui lòng nhập lý do điều chỉnh số dư.',
        ]);

        $identifier = trim($validated['email_hoac_id']);
        $user = User::where('email', $identifier)
            ->orWhere('id', $identifier)
            ->orWhere('so_dien_thoai', $identifier)
            ->first();

        if (! $user) {
            return back()->with('error', "Không tìm thấy người dùng với thông tin '{$identifier}'.");
        }

        $amount = (float) $validated['so_tien'];
        $isCredit = $validated['loai_dieu_chinh'] === 'cong_tien';

        $result = DB::transaction(function () use ($user, $amount, $isCredit, $validated) {
            $lockedUser = User::lockForUpdate()->findOrFail($user->id);
            $oldBalance = (float) $lockedUser->so_du;

            if (! $isCredit && $oldBalance < $amount) {
                return [
                    'success' => false,
                    'message' => "Số dư hiện tại của {$lockedUser->ho_ten} (" . number_format($oldBalance, 0, ',', '.') . "đ) không đủ để trừ " . number_format($amount, 0, ',', '.') . "đ.",
                ];
            }

            $newBalance = $isCredit ? ($oldBalance + $amount) : ($oldBalance - $amount);
            $lockedUser->so_du = $newBalance;
            $lockedUser->save();

            $code = 'ADM' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            $adminName = Auth::user()->ho_ten ?? 'Admin';

            $thanhToan = ThanhToan::create([
                'user_id' => $lockedUser->id,
                'ma_giao_dich' => $code,
                'ten_goi_dich_vu' => $isCredit ? 'Admin cộng số dư ví Quán Mới' : 'Admin trừ số dư ví Quán Mới',
                'so_tien' => $amount,
                'phuong_thuc' => 'admin_adjust',
                'trang_thai' => 'thanh_cong',
                'ghi_chu' => ($isCredit ? '[Cộng số dư] ' : '[Trừ số dư] ') . $validated['ghi_chu'] . " (Bởi {$adminName})",
            ]);

            try {
                AdminAuditLog::create([
                    'actor_id' => Auth::id(),
                    'action' => $isCredit ? 'admin.wallet.credit' : 'admin.wallet.debit',
                    'target_type' => User::class,
                    'target_id' => (string) $lockedUser->id,
                    'metadata' => [
                        'ma_giao_dich' => $code,
                        'thanh_toan_id' => $thanhToan->id,
                        'user_email' => $lockedUser->email,
                        'loai' => $validated['loai_dieu_chinh'],
                        'so_tien' => $amount,
                        'so_du_truoc' => $oldBalance,
                        'so_du_sau' => $newBalance,
                        'ghi_chu' => $validated['ghi_chu'],
                    ],
                    'ip_address' => request()->ip(),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Audit log error: ' . $e->getMessage());
            }

            try {
                $amountFormatted = number_format($amount, 0, ',', '.') . 'đ';
                $balanceFormatted = number_format($newBalance, 0, ',', '.') . 'đ';
                $lockedUser->notify(new WalletTransactionNotification(
                    title: $isCredit ? "Cộng số dư ví +{$amountFormatted}" : "Điều chỉnh số dư ví -{$amountFormatted}",
                    message: "Mã GD #{$code}: {$validated['ghi_chu']}. Số dư hiện tại: {$balanceFormatted}.",
                    action: 'wallet_deposit',
                    statusLabel: 'Ví & Nạp tiền',
                    link: route('tai-khoan.vi-tien'),
                    extra: [
                        'ma_giao_dich' => $code,
                        'so_tien' => $amount,
                        'so_du_moi' => $newBalance,
                    ]
                ));
            } catch (\Throwable $e) {
                Log::warning('Notify wallet adjustment error: ' . $e->getMessage());
            }

            return [
                'success' => true,
                'message' => ($isCredit ? 'Đã cộng +' : 'Đã trừ -') . number_format($amount, 0, ',', '.') . "đ cho tài khoản {$lockedUser->ho_ten} ({$lockedUser->email}). Số dư mới: " . number_format($newBalance, 0, ',', '.') . "đ.",
            ];
        });

        Cache::forget('admin_dashboard_overview_stats');
        Cache::forget('admin_dashboard_charts_6m');

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Xuất báo cáo danh sách giao dịch nạp tiền ra file CSV (Hỗ trợ tiếng Việt UTF-8 BOM)
     */
    public function export(Request $request)
    {
        $query = ThanhToan::with(['user']);

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('ma_giao_dich', 'like', "%{$search}%")
                  ->orWhere('sepay_transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('ho_ten', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $trangThai = (string) $request->input('trang_thai', 'all');
        if ($trangThai !== '' && $trangThai !== 'all') {
            if ($trangThai === 'cho_thanh_toan') {
                $query->whereIn('trang_thai', ['cho_thanh_toan', 'cho_xu_ly']);
            } else {
                $query->where('trang_thai', $trangThai);
            }
        }

        $rows = $query->latest()->limit(5000)->get();

        $filename = 'lich_su_nap_tien_quanmoi_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Mã Giao Dịch',
                'Khách Hàng',
                'Email',
                'Số Tiền (VNĐ)',
                'Phương Thức',
                'Trạng Thái',
                'Mã Tham Chiếu SePay',
                'Ghi Chú',
                'Thời Gian Tạo',
                'Cập Nhật Cuối',
            ]);

            foreach ($rows as $item) {
                $statusLabel = match ($item->trang_thai) {
                    'thanh_cong' => 'Thành công',
                    'cho_thanh_toan', 'cho_xu_ly' => 'Chờ thanh toán',
                    'da_huy' => 'Đã hủy',
                    'that_bai' => 'Thất bại',
                    default => $item->trang_thai,
                };

                fputcsv($handle, [
                    $item->ma_giao_dich,
                    $item->user->ho_ten ?? 'Khách vãng lai',
                    $item->user->email ?? '—',
                    (int) round((float) $item->so_tien),
                    strtoupper((string) $item->phuong_thuc),
                    $statusLabel,
                    $item->sepay_transaction_id ?? $item->sepay_order_id ?? '—',
                    $item->ghi_chu ?? '',
                    optional($item->created_at)->format('d/m/Y H:i:s'),
                    optional($item->updated_at)->format('d/m/Y H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

