<?php

declare(strict_types=1);

namespace App\Search;

use Elastic\ScoutDriver\Factories\SearchParametersFactory;
use Laravel\Scout\Builder;

class CustomSearchParametersFactory extends SearchParametersFactory
{
    protected function makeQuery(Builder $builder): array
    {
        $query = [
            'bool' => [],
        ];

        if (!empty($builder->query)) {
            $query['bool']['must'] = [
                'multi_match' => [
                    'query' => $builder->query,
                    'fields' => ['ten_quan^3', 'mon_an^2', 'dia_chi', 'loai_hinh_kinh_doanh'],
                    'type' => 'cross_fields',
                    'operator' => 'AND',
                ],
            ];
            $query['bool']['should'] = [
                ['match_phrase' => ['ten_quan' => ['query' => $builder->query, 'boost' => 10]]],
                ['match_phrase' => ['mon_an' => ['query' => $builder->query, 'boost' => 8]]],
            ];
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
