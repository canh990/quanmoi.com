<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonTrongMenu;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchSuggestionController extends Controller
{
    /**
     * Trả về kết quả gợi ý tức thì (Live Autocomplete)
     */
    public function suggestions(Request $request)
    {
        $q = trim(strip_tags(is_array($request->input('q')) ? '' : (string) $request->input('q', '')));

        if ($q === '') {
            return response()->json([
                'success' => true,
                'data' => [
                    'quans' => [],
                    'mons' => [],
                ],
            ]);
        }

        $tinhThanhId = trim(strip_tags(is_array($request->input('tinh_thanh_id')) ? '' : (string) $request->input('tinh_thanh_id', '')));
        $quanHuyenId = trim(strip_tags(is_array($request->input('quan_huyen_id')) ? '' : (string) $request->input('quan_huyen_id', '')));
        $lat         = $request->input('lat');
        $lon         = $request->input('lon', $request->input('lng'));
        $banKinh     = $request->input('ban_kinh');
        $hasGps      = !empty($lat) && !empty($lon) && is_numeric($lat) && is_numeric($lon);

        $quans = collect();
        $useSqliteTesting = app()->environment('testing') && config('database.default') === 'sqlite';

        if (!$useSqliteTesting && config('scout.driver') === 'elastic') {
            try {
                $searchQuery = Quan::search($q)->whereIn('trang_thai', ['da_duyet']);

                if ($hasGps && !empty($banKinh) && is_numeric($banKinh)) {
                    $searchQuery->where('_geo_distance', [
                        'lat' => (float) $lat,
                        'lon' => (float) $lon,
                        'distance' => "{$banKinh}km",
                    ]);
                } else {
                    if ($tinhThanhId !== '') {
                        $searchQuery->whereIn('tinh_thanh_id', $this->codeVariants($tinhThanhId));
                    }
                    if ($quanHuyenId !== '') {
                        $searchQuery->whereIn('quan_huyen_id', $this->codeVariants($quanHuyenId));
                    }
                }

                $quans = $searchQuery->take(5)->get();
            } catch (\Throwable $e) {
                report($e);
                $quans = $this->fallbackQuanSuggestions($q, $tinhThanhId, $quanHuyenId);
            }
        } else {
            $quans = $this->fallbackQuanSuggestions($q, $tinhThanhId, $quanHuyenId);
        }

        $quanResults = $quans->map(function (Quan $quan) {
            return [
                'id'                   => $quan->id,
                'ten_quan'             => $quan->ten_quan,
                'dia_chi'              => $quan->dia_chi_chi_tiet,
                'anh_bia'              => $quan->anh_bia_url,
                'slug'                 => $quan->slug,
                'loai_hinh_kinh_doanh' => $quan->loai_hinh_kinh_doanh,
            ];
        })->values();

        // Tìm tối đa 3 món ăn khớp từ khóa (hỗ trợ cả có dấu và không dấu)
        $monsQuery = MonTrongMenu::where('ten_mon', 'like', '%' . $q . '%')
            ->whereHas('danhMuc.quan', function ($qBuilder) use ($tinhThanhId, $quanHuyenId, $hasGps) {
                $qBuilder->where('trang_thai', 'da_duyet');
                if (!$hasGps) {
                    if ($tinhThanhId !== '') {
                        $qBuilder->whereIn('tinh_thanh_id', $this->codeVariants($tinhThanhId));
                    }
                    if ($quanHuyenId !== '') {
                        $qBuilder->whereIn('quan_huyen_id', $this->codeVariants($quanHuyenId));
                    }
                }
            })
            ->with(['danhMuc.quan:id,slug,ten_quan,anh_bia,anh_bia_key'])
            ->take(3)
            ->get();

        // Nếu gõ không dấu (ví dụ: "bun dau", "pho bo") làm SQL LIKE chưa khớp đủ 3 món,
        // lọc bổ sung theo bảng chữ cái ASCII từ các quán đã khớp Elasticsearch.
        if ($monsQuery->count() < 3 && $quans->isNotEmpty()) {
            $qAscii = Str::lower(Str::ascii($q));
            $existingIds = $monsQuery->pluck('id')->all();

            $extraMons = MonTrongMenu::whereHas('danhMuc', function ($qBuilder) use ($quans) {
                $qBuilder->whereIn('quan_id', $quans->pluck('id')->all());
            })
                ->whereNotIn('id', $existingIds)
                ->with(['danhMuc.quan:id,slug,ten_quan,anh_bia,anh_bia_key'])
                ->get()
                ->filter(fn (MonTrongMenu $mon) => str_contains(Str::lower(Str::ascii($mon->ten_mon)), $qAscii))
                ->take(3 - $monsQuery->count());

            $monsQuery = $monsQuery->concat($extraMons);
        }

        $monResults = $monsQuery->map(function (MonTrongMenu $mon) {
            $quan = $mon->danhMuc?->quan;
            return [
                'id'        => $mon->id,
                'ten_mon'   => $mon->ten_mon,
                'gia'       => $mon->gia,
                'hinh_anh'  => $mon->hinh_anh_url ?: $quan?->anh_bia_url,
                'quan_slug' => $quan?->slug,
                'ten_quan'  => $quan?->ten_quan,
            ];
        })->filter(fn (array $item) => !empty($item['quan_slug']))->values();

        return response()->json([
            'success' => true,
            'data' => [
                'quans' => $quanResults,
                'mons' => $monResults,
            ],
        ]);
    }

    protected function fallbackQuanSuggestions(string $q, string $tinhThanhId, string $quanHuyenId)
    {
        return Quan::query()
            ->where('trang_thai', 'da_duyet')
            ->when($tinhThanhId !== '', fn ($b) => $b->whereIn('tinh_thanh_id', $this->codeVariants($tinhThanhId)))
            ->when($quanHuyenId !== '', fn ($b) => $b->whereIn('quan_huyen_id', $this->codeVariants($quanHuyenId)))
            ->where(function ($b) use ($q) {
                $b->where('ten_quan', 'like', "%{$q}%")
                    ->orWhere('dia_chi_chi_tiet', 'like', "%{$q}%")
                    ->orWhere('loai_hinh_kinh_doanh', 'like', "%{$q}%")
                    ->orWhereHas('danhMucMenu.monAn', fn ($m) => $m->where('ten_mon', 'like', "%{$q}%"));
            })
            ->take(5)
            ->get();
    }

    protected function codeVariants(string $code): array
    {
        if ($code === '') {
            return [];
        }
        if (!ctype_digit($code)) {
            return [$code];
        }
        $n = (string) (int) $code;
        return array_values(array_unique([
            $code,
            $n,
            str_pad($n, 2, '0', STR_PAD_LEFT),
            str_pad($n, 3, '0', STR_PAD_LEFT),
            str_pad($n, 5, '0', STR_PAD_LEFT),
        ]));
    }
}
