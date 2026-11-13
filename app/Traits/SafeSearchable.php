<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Searchable;

trait SafeSearchable
{
    use Searchable {
        syncMakeSearchable as protected baseSyncMakeSearchable;
        syncRemoveFromSearch as protected baseSyncRemoveFromSearch;
    }

    /**
     * Đồng bộ cập nhật chỉ mục Elasticsearch an toàn, không làm sập request nếu ES tạm ngưng.
     */
    public function syncMakeSearchable($models)
    {
        try {
            return $this->baseSyncMakeSearchable($models);
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch index update failed for ' . static::class . ': ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Đồng bộ xóa chỉ mục Elasticsearch an toàn.
     */
    public function syncRemoveFromSearch($models)
    {
        try {
            return $this->baseSyncRemoveFromSearch($models);
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch index removal failed for ' . static::class . ': ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Truy vấn ID khớp trên Elasticsearch (kết hợp index model hiện tại và index Quan).
     * Trả về null nếu Elasticsearch không khả dụng hoặc chưa sync index để caller fallback SQL.
     */
    protected static function queryElasticIds(string $keyword, array $filters = [], int $limit = 500): ?array
    {
        $search = trim($keyword);
        $isElastic = in_array(config('scout.driver'), ['elastic', 'elasticsearch'])
            && ! (app()->environment('testing') && config('database.default') === 'sqlite');

        if (! $isElastic || $search === '') {
            return null;
        }

        try {
            $scout = static::search($search);
            foreach ($filters as $field => $value) {
                if ($value !== null && $value !== '' && $value !== 'all') {
                    $scout->where($field, $value);
                }
            }
            $matchedIds = $scout->take($limit)->keys()->all();
            $quanIds = \App\Models\Quan::search($search)->take(200)->keys()->all();
            $hasIndexedDocs = static::search('')->take(1)->keys()->isNotEmpty();

            if (! empty($matchedIds) || ! empty($quanIds) || $hasIndexedDocs) {
                return [$matchedIds, $quanIds];
            }
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch search failed for ' . static::class . ', falling back to SQL: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Áp dụng lọc theo ID từ Elasticsearch vào Eloquent Builder.
     * Trả về true nếu đã lọc bằng Elasticsearch, false nếu cần fallback SQL.
     */
    protected static function applyElasticIdsToQuery(Builder $query, string $keyword, array $filters = [], int $limit = 500): bool
    {
        $result = static::queryElasticIds($keyword, $filters, $limit);
        if ($result === null) {
            return false;
        }

        [$matchedIds, $quanIds] = $result;
        $query->where(function ($sub) use ($matchedIds, $quanIds) {
            if (! empty($matchedIds)) {
                $sub->whereIn('id', $matchedIds);
            }
            if (! empty($quanIds)) {
                $sub->orWhereIn('quan_id', $quanIds);
            }
            if (empty($matchedIds) && empty($quanIds)) {
                $sub->whereRaw('1 = 0');
            }
        });

        return true;
    }
}
