<?php

declare(strict_types=1);

namespace App\Search;

use Elastic\ScoutDriver\Factories\SearchParametersFactory;
use Laravel\Scout\Builder;

class CustomSearchParametersFactory extends SearchParametersFactory
{
    protected function getModelSearchFields(Builder $builder): array
    {
        $model = $builder->model;

        if ($model instanceof \App\Models\VideoShort) {
            return [
                ['tieu_de^4', 'ten_quan^3', 'mon_an^2.5', 'nguoi_dang^1.5', 'video_id^1.5'],
                ['tieu_de^3', 'ten_quan^2.5', 'mon_an^2', 'nguoi_dang^1.5'],
                ['tieu_de^3', 'ten_quan^2', 'mon_an^1.5', 'nguoi_dang'],
                [
                    ['match_phrase' => ['tieu_de' => ['query' => $builder->query, 'boost' => 10]]],
                    ['match_phrase' => ['ten_quan' => ['query' => $builder->query, 'boost' => 8]]],
                    ['match_phrase' => ['mon_an' => ['query' => $builder->query, 'boost' => 6]]],
                ],
            ];
        }

        if ($model instanceof \App\Models\KhuyenMai) {
            return [
                ['ma_voucher^5', 'tieu_de^4', 'ten_quan^3', 'mon_ap_dung^2', 'slogan^1.5', 'noi_dung'],
                ['ma_voucher^4', 'tieu_de^3', 'ten_quan^2.5', 'mon_ap_dung^2'],
                ['ma_voucher^4', 'tieu_de^3', 'ten_quan^2', 'mon_ap_dung'],
                [
                    ['match_phrase' => ['ma_voucher' => ['query' => $builder->query, 'boost' => 10]]],
                    ['match_phrase' => ['tieu_de' => ['query' => $builder->query, 'boost' => 8]]],
                    ['match_phrase' => ['ten_quan' => ['query' => $builder->query, 'boost' => 6]]],
                ],
            ];
        }

        return [
            ['ten_quan^4', 'mon_an^2.5', 'loai_hinh_kinh_doanh^1.5', 'dia_chi', 'mo_ta'],
            ['ten_quan^3', 'mon_an^2', 'loai_hinh_kinh_doanh^1.5', 'dia_chi', 'mo_ta'],
            ['ten_quan^3', 'mon_an^2', 'dia_chi'],
            [
                ['match_phrase' => ['ten_quan' => ['query' => $builder->query, 'boost' => 10]]],
                ['match_phrase' => ['mon_an' => ['query' => $builder->query, 'boost' => 8]]],
            ],
        ];
    }

    protected function makeQuery(Builder $builder): array
    {
        $query = [
            'bool' => [],
        ];

        if (!empty($builder->query)) {
            [$bestFields, $crossFields, $phraseFields, $exactPhrases] = $this->getModelSearchFields($builder);
            $query['bool']['must'] = [
                'bool' => [
                    'should' => [
                        [
                            'multi_match' => [
                                'query' => $builder->query,
                                'fields' => $bestFields,
                                'type' => 'best_fields',
                                'operator' => 'OR',
                                'fuzziness' => 'AUTO',
                                'prefix_length' => 1,
                                'max_expansions' => 50,
                            ],
                        ],
                        [
                            'multi_match' => [
                                'query' => $builder->query,
                                'fields' => $crossFields,
                                'type' => 'cross_fields',
                                'operator' => 'AND',
                                'boost' => 2,
                            ],
                        ],
                        [
                            'multi_match' => [
                                'query' => $builder->query,
                                'fields' => $phraseFields,
                                'type' => 'phrase_prefix',
                                'boost' => 1.5,
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
            $query['bool']['should'] = $exactPhrases;
        } else {
            $query['bool']['must'] = [
                'match_all' => new \stdClass(),
            ];
        }

        if ($filter = $this->makeFilter($builder)) {
            $query['bool']['filter'] = $filter;
        }

        return $query;
    }
    protected function makeFilter(Builder $builder): ?array
    {
        $filter = collect();

        // 1. Xử lý $builder->wheres (Tương thích cả Scout cũ dạng key=>value và Scout mới dạng ['field'=>..., 'value'=>...])
        foreach ($builder->wheres as $key => $item) {
            $field = is_array($item) && isset($item['field']) ? (string) $item['field'] : (string) $key;
            $value = is_array($item) && array_key_exists('value', $item) ? $item['value'] : $item;

            // Xử lý khoảng giá tùy biến (range query)
            if ($field === '_range_gia') {
                if (is_array($value)) {
                    if (isset($value['must']) && is_array($value['must'])) {
                        foreach ($value['must'] as $r) {
                            $filter->push(['range' => $r]);
                        }
                    } elseif (isset($value['range'])) {
                        $filter->push(['range' => $value['range']]);
                    }
                }
                continue;
            }

            // Xử lý định vị GPS (geo_distance filter)
            if ($field === '_geo_distance') {
                if (is_array($value) && !empty($value['lat']) && !empty($value['lon'])) {
                    $filter->push([
                        'geo_distance' => [
                            'distance' => $value['distance'] ?? '10km',
                            'location' => [
                                'lat' => (float) $value['lat'],
                                'lon' => (float) $value['lon'],
                            ],
                        ],
                    ]);
                }
                continue;
            }

            // Xử lý kiểm tra Đang mở cửa theo giờ thực tế so với gio_mo_cua & gio_dong_cua
            if ($field === '_dang_mo_cua' && $value) {
                $now = now('Asia/Ho_Chi_Minh')->format('H:i');
                $filter->push([
                    'bool' => [
                        'should' => [
                            ['term' => ['loai_hinh_kinh_doanh.keyword' => 'Quán Đêm 24/7']],
                            [
                                'bool' => [
                                    'must' => [
                                        ['range' => ['gio_mo_cua' => ['lte' => $now]]],
                                        ['range' => ['gio_dong_cua' => ['gte' => $now]]],
                                    ],
                                ],
                            ],
                            // Quán mở qua đêm (gio_dong_cua buổi sáng sớm)
                            [
                                'bool' => [
                                    'must' => [
                                        ['range' => ['gio_dong_cua' => ['lte' => '12:00']]],
                                        [
                                            'bool' => [
                                                'should' => [
                                                    ['range' => ['gio_mo_cua' => ['lte' => $now]]],
                                                    ['range' => ['gio_dong_cua' => ['gte' => $now]]],
                                                ],
                                                'minimum_should_match' => 1,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ]);
                continue;
            }

            // Bỏ qua các cờ điều khiển khác (ví dụ: _geo_sort)
            if (str_starts_with($field, '_')) {
                continue;
            }

            // Standard term filter
            $filter->push([
                'term' => [$field => $value],
            ]);
        }

        // 2. Xử lý $builder->whereIns
        $whereIns = collect($builder->whereIns)->map(static fn (array $values, string $field) => [
            'terms' => [$field => $values],
        ])->values();

        if ($whereIns->isNotEmpty()) {
            $filter->push([
                'bool' => [
                    'must' => $whereIns->all(),
                ],
            ]);
        }

        // 3. Xử lý $builder->whereNotIns
        $whereNotIns = collect($builder->whereNotIns)->map(static fn (array $values, string $field) => [
            'terms' => [$field => $values],
        ])->values();

        if ($whereNotIns->isNotEmpty()) {
            $filter->push([
                'bool' => [
                    'must_not' => $whereNotIns->all(),
                ],
            ]);
        }

        return $filter->isEmpty() ? null : $filter->all();
    }

    protected function makeSort(Builder $builder): ?array
    {
        $sort = collect();

        // Kiểm tra xem có yêu cầu sắp xếp theo khoảng cách GPS hay không
        foreach ($builder->wheres as $key => $item) {
            $field = is_array($item) && isset($item['field']) ? (string) $item['field'] : (string) $key;
            $value = is_array($item) && array_key_exists('value', $item) ? $item['value'] : $item;

            if ($field === '_geo_sort' && is_array($value) && !empty($value['lat']) && !empty($value['lon'])) {
                $sort->push([
                    '_geo_distance' => [
                        'location' => [
                            'lat' => (float) $value['lat'],
                            'lon' => (float) $value['lon'],
                        ],
                        'order' => $value['order'] ?? 'asc',
                        'unit' => 'km',
                        'mode' => 'min',
                        'distance_type' => 'arc',
                    ],
                ]);
            }
        }

        // Các orders thông thường
        foreach ($builder->orders as $order) {
            $sort->push([
                $order['column'] => $order['direction'],
            ]);
        }

        return $sort->isEmpty() ? null : $sort->all();
    }
}
