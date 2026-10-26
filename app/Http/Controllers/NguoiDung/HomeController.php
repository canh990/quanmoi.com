<?php

namespace App\Http\Controllers\NguoiDung;

use App\Http\Controllers\Controller;
use App\Models\KhuyenMai;
use App\Models\Quan;
use App\Models\User;
use App\Models\VideoShort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $hasFeaturedColumn = Schema::hasColumn('quan', 'is_noi_bat');

        $quanNoiBat = Quan::query()->where('trang_thai', 'da_duyet');
        if ($hasFeaturedColumn) {
            $quanNoiBat = $quanNoiBat->where('is_noi_bat', true);
        }
        $quanNoiBat = $quanNoiBat->orderBy('updated_at', 'desc')->take(9)->get();

        $quanMoi = Quan::with('danhMuc')->where('trang_thai', 'da_duyet')
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();
        $totalQuanMoi = Quan::where('trang_thai', 'da_duyet')->count();
        $totalQuanNoiBat = $hasFeaturedColumn
            ? Quan::where('trang_thai', 'da_duyet')->where('is_noi_bat', true)->count()
            : 0;

        // Lấy video shorts
        $videoShorts = VideoShort::with('quan')
            ->where('trang_thai', 'da_duyet')
            ->whereHas('quan', fn ($query) => $query->where('trang_thai', 'da_duyet'))
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Lấy tin tức / bài viết mới nhất
        $latestBlogs = \App\Models\Blog::where('status', 'published')
            ->latest('published_at')
            ->take(4)
            ->get();

        $savedQuanIds = [];
        $user = Auth::user();
        if ($user instanceof User) {
            $savedQuanIds = $user->savedQuan()->pluck('quan_id')->toArray();
        }

        // Xử lý vị trí người dùng (hỗ trợ cả request param, plain cookie từ JS, và encrypted cookie)
        $rawLat = $request->input('lat') ?? $request->cookie('user_lat') ?? $request->cookies->get('user_lat');
        $rawLng = $request->input('lng') ?? $request->cookie('user_lng') ?? $request->cookies->get('user_lng');

        foreach (['lat' => &$rawLat, 'lng' => &$rawLng] as $k => &$v) {
            if ($v !== null && !is_numeric($v) && is_string($v)) {
                try {
                    $decrypted = \Illuminate\Support\Facades\Crypt::decrypt($v, false);
                    if (is_string($decrypted) && str_contains($decrypted, '|')) {
                        $decrypted = explode('|', $decrypted, 2)[1];
                    }
                    if (is_numeric($decrypted)) {
                        $v = $decrypted;
                    }
                } catch (\Throwable $e) {
                    // Bỏ qua lỗi giải mã nếu cookie không hợp lệ
                }
            }
        }
        unset($v);

        $userLat = $rawLat;
        $userLng = $rawLng;

        $quanGanBan = collect();
        $hasGps = false;

        if ($userLat !== null && $userLng !== null && is_numeric($userLat) && is_numeric($userLng)) {
            $lat = (float) $userLat;
            $lng = (float) $userLng;

            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                $hasGps = true;
                $radiusKm = 20.0; // Bán kính 20km bao phủ trọn vẹn khu vực đô thị (Thủ Đức -> Q.1 / Q.3 ~ 10-12km)
                $earthKmPerDegLat = 111.045;
                $deltaLat = $radiusKm / $earthKmPerDegLat;
                $cosLat = max(abs(cos(deg2rad($lat))), 0.01);
                $deltaLng = $radiusKm / ($earthKmPerDegLat * $cosLat);

                $minLat = max(-90.0, $lat - $deltaLat);
                $maxLat = min(90.0, $lat + $deltaLat);
                $minLng = max(-180.0, $lng - $deltaLng);
                $maxLng = min(180.0, $lng + $deltaLng);

                $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();

                $baseNearbyQuery = Quan::query()
                    ->where('trang_thai', 'da_duyet')
                    ->whereNotNull('vi_do')
                    ->whereNotNull('kinh_do')
                    ->whereBetween('vi_do', [$minLat, $maxLat])
                    ->whereBetween('kinh_do', [$minLng, $maxLng]);

                if (in_array($driver, ['mysql', 'mariadb', 'pgsql'], true)) {
                    $haversineSql = '(6371 * ACOS(LEAST(1.0, GREATEST(-1.0, COS(RADIANS(?)) * COS(RADIANS(vi_do)) * COS(RADIANS(kinh_do) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(vi_do))))))';

                    $quanGanBan = (clone $baseNearbyQuery)
                        ->selectRaw("quan.*, {$haversineSql} AS khoang_cach_km", [$lat, $lng, $lat])
                        ->whereRaw("{$haversineSql} <= ?", [$lat, $lng, $lat, $radiusKm])
                        ->orderBy('khoang_cach_km', 'asc')
                        ->take(9)
                        ->get()
                        ->map(function ($q) {
                            $q->khoang_cach_km = round((float) $q->khoang_cach_km, 2);
                            return $q;
                        });

                    // Nếu trong 20km chưa có quán, tự động mở rộng quét trong 50km
                    if ($quanGanBan->isEmpty()) {
                        $expandRadius = 50.0;
                        $expandDeltaLat = $expandRadius / $earthKmPerDegLat;
                        $expandDeltaLng = $expandRadius / ($earthKmPerDegLat * $cosLat);
                        $quanGanBan = Quan::query()
                            ->where('trang_thai', 'da_duyet')
                            ->whereNotNull('vi_do')
                            ->whereNotNull('kinh_do')
                            ->whereBetween('vi_do', [max(-90.0, $lat - $expandDeltaLat), min(90.0, $lat + $expandDeltaLat)])
                            ->whereBetween('kinh_do', [max(-180.0, $lng - $expandDeltaLng), min(180.0, $lng + $expandDeltaLng)])
                            ->selectRaw("quan.*, {$haversineSql} AS khoang_cach_km", [$lat, $lng, $lat])
                            ->whereRaw("{$haversineSql} <= ?", [$lat, $lng, $lat, $expandRadius])
                            ->orderBy('khoang_cach_km', 'asc')
                            ->take(9)
                            ->get()
                            ->map(function ($q) {
                                $q->khoang_cach_km = round((float) $q->khoang_cach_km, 2);
                                return $q;
                            });
                    }
                } else {
                    $quanGanBan = $baseNearbyQuery->get()
                        ->map(function ($q) use ($lat, $lng) {
                            $earthRadius = 6371.0;
                            $dLat = deg2rad((float) $q->vi_do - $lat);
                            $dLon = deg2rad((float) $q->kinh_do - $lng);
                            $a = sin($dLat / 2) * sin($dLat / 2)
                                + cos(deg2rad($lat)) * cos(deg2rad((float) $q->vi_do))
                                * sin($dLon / 2) * sin($dLon / 2);
                            $c = 2 * atan2(sqrt($a), sqrt(max(0.0, 1 - $a)));
                            $q->khoang_cach_km = round($earthRadius * $c, 2);
                            return $q;
                        })
                        ->filter(fn ($q) => $q->khoang_cach_km <= $radiusKm)
                        ->sortBy('khoang_cach_km')
                        ->take(9)
                        ->values();
                }
            }
        }

                $danhMucList = \App\Models\DanhMucQuan::hienThi()->noiBat()->orderBy('thu_tu')->orderBy('id')->get();

        return view('welcome', compact(
            'danhMucList',
            'quanNoiBat',
            'quanMoi',
            'totalQuanMoi',
            'totalQuanNoiBat',
            'savedQuanIds',
            'videoShorts',
            'latestBlogs',
            'quanGanBan',
            'hasGps',
            'userLat',
            'userLng'
        ));
    }

    public function show($slug)
    {
        $quan = Quan::with([
            'danhMucMenu.monAn',
            'hinhAnh',
            'danhGia.nguoiDung',
            'khuyenMaiDangChay',
            'videos' => fn ($q) => $q->where('trang_thai', 'da_duyet')->orderBy('created_at', 'desc'),
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Nếu quán chưa được duyệt hoặc bị khóa, chỉ cho phép Admin hoặc Chủ quán xem trước
        if ($quan->trang_thai !== 'da_duyet') {
            $user = Auth::user();
            $canPreview = $user instanceof User && ($user->isAdmin() || $user->id === $quan->chu_quan_id);
            if (! $canPreview) {
                abort(404);
            }
        }

        // Chống Spam View: Lưu ID quán vào Session trong vòng 2 tiếng (chỉ tính view khi quán đã được duyệt)
        if ($quan->trang_thai === 'da_duyet') {
            $sessionKey = 'viewed_quan_'.$quan->id;
            if (! session()->has($sessionKey) && Schema::hasColumn('quan', 'luot_xem')) {
                $quan->increment('luot_xem');
                session()->put($sessionKey, true);
            }
        }

        $user = Auth::user();
        $danhGiaCuaToi = $user instanceof User
            ? $quan->danhGia->firstWhere('nguoi_dung_id', $user->id)
            : null;

        $isSaved = $user instanceof User
            ? $user->savedQuan()->where('quan_id', $quan->id)->exists()
            : false;

        return view('nguoi-dung.chi-tiet', compact('quan', 'danhGiaCuaToi', 'isSaved'));
    }

    /**
     * Giả lập & kiểm tra tính hợp lệ của mã voucher khuyến mãi
     */
    public function kiemTraVoucher(Request $request, $slug)
    {
        $quan = Quan::where('slug', $slug)->firstOrFail();
        $code = trim(strtoupper((string) $request->input('ma_voucher')));
        $tamTinh = max(0, (float) $request->input('tam_tinh', 0));

        if (empty($code)) {
            return response()->json([
                'valid' => false,
                'message' => 'Vui lòng nhập mã voucher cần kiểm tra!',
            ], 422);
        }

        $km = KhuyenMai::where('quan_id', $quan->id)
            ->where('ma_voucher', $code)
            ->first();

        if (! $km) {
            return response()->json([
                'valid' => false,
                'message' => "Mã voucher [{$code}] không tồn tại tại quán này.",
            ], 404);
        }

        if (! $km->trang_thai) {
            return response()->json([
                'valid' => false,
                'message' => "Mã [{$code}] hiện đang tạm ngưng áp dụng.",
            ], 400);
        }

        if ($km->ngay_ket_thuc && $km->ngay_ket_thuc->endOfDay()->isPast()) {
            return response()->json([
                'valid' => false,
                'message' => "Mã [{$code}] đã hết hạn sử dụng vào ngày ".$km->ngay_ket_thuc->format('d/m/Y').".",
            ], 400);
        }

        // Tính toán mức giảm giá
        $giaTriStr = trim((string) ($km->gia_tri_khuyen_mai ?: ''));
        if (empty($giaTriStr)) {
            $giaTriStr = (string) $km->tieu_de;
        }
        $discountAmount = 0;
        $discountType = 'other';
        $discountLabel = $km->gia_tri_khuyen_mai ?: 'Ưu đãi đặc biệt';

        if (preg_match('/(\d+)\s*%/i', $giaTriStr, $matches)) {
            $percent = (int) $matches[1];
            $discountType = 'percent';
            $discountLabel = "Giảm {$percent}%";
            if ($tamTinh > 0) {
                $discountAmount = round($tamTinh * ($percent / 100));
            }
        } elseif (preg_match('/(\d+(?:\.\d+)?)\s*(?:k|nghìn|nghin)/i', strtolower($giaTriStr), $matches)) {
            $fixed = (float) $matches[1] * 1000;
            $discountType = 'fixed';
            $discountLabel = 'Giảm '.number_format($fixed, 0, ',', '.').'đ';
            $discountAmount = min($tamTinh, $fixed);
        } elseif (preg_match('/(\d[\d\.\,]*)/i', $giaTriStr, $matches)) {
            $cleanNum = (float) str_replace(['.', ','], '', $matches[1]);
            if ($cleanNum > 0 && $cleanNum <= 100) {
                $discountType = 'percent';
                $discountLabel = "Giảm {$cleanNum}%";
                if ($tamTinh > 0) {
                    $discountAmount = round($tamTinh * ($cleanNum / 100));
                }
            } elseif ($cleanNum > 1000) {
                $discountType = 'fixed';
                $discountLabel = 'Giảm '.number_format($cleanNum, 0, ',', '.').'đ';
                $discountAmount = min($tamTinh, $cleanNum);
            }
        } else {
            $discountType = 'gift';
            $discountLabel = $giaTriStr ?: 'Tặng quà / Ưu đãi đặc biệt';
        }

        $finalTotal = max(0, $tamTinh - $discountAmount);

        return response()->json([
            'valid' => true,
            'message' => "Áp dụng thành công mã ưu đãi [{$km->ma_voucher}]!",
            'khuyen_mai' => [
                'id' => $km->id,
                'ma_voucher' => $km->ma_voucher,
                'tieu_de' => $km->tieu_de,
                'gia_tri_khuyen_mai' => $km->gia_tri_khuyen_mai,
                'slogan' => $km->slogan,
                'dieu_kien_ap_dung' => $km->dieu_kien_ap_dung,
                'ngay_ket_thuc' => $km->ngay_ket_thuc ? $km->ngay_ket_thuc->format('d/m/Y') : 'Không thời hạn',
            ],
            'discount_type' => $discountType,
            'discount_label' => $discountLabel,
            'discount_amount' => $discountAmount,
            'tam_tinh' => $tamTinh,
            'final_total' => $finalTotal,
        ]);
    }
}

