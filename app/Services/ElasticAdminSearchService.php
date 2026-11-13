<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\DanhMucQuan;
use App\Models\KhuyenMai;
use App\Models\Quan;
use App\Models\User;
use App\Models\VideoShort;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;

class ElasticAdminSearchService
{
    private static ?Client $client = null;
    private static ?bool $isAvailable = null;

    /**
     * Lấy instance kết nối Elasticsearch
     */
    public static function getClient(): ?Client
    {
        if (self::$client !== null) {
            return self::$client;
        }

        try {
            $host = config('elastic.client.connections.default.hosts.0') 
                ?: env('ELASTIC_HOST', 'http://elasticsearch:9200');

            self::$client = ClientBuilder::create()
                ->setHosts([$host])
                ->build();

            return self::$client;
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch client initialization failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Kiểm tra nhanh trạng thái hoạt động của Elasticsearch (có timeout an toàn)
     */
    public static function isAvailable(): bool
    {
        if (app()->environment('testing')) {
            return false;
        }

        if (self::$isAvailable !== null) {
            return self::$isAvailable;
        }

        $client = self::getClient();
        if (! $client) {
            return self::$isAvailable = false;
        }

        try {
            self::$isAvailable = $client->ping()->asBool();
        } catch (\Throwable $e) {
            self::$isAvailable = false;
        }

        return self::$isAvailable;
    }

    /**
     * Chuyển đổi chuỗi tiếng Việt có dấu sang không dấu để tăng độ chính xác khi tìm kiếm gần đúng
     */
    public static function removeVietnameseTones(string $str): string
    {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];
        foreach ($unicode as $nonAccent => $accent) {
            $str = preg_replace("/($accent)/u", $nonAccent, $str);
        }
        return $str;
    }

    /**
     * Tìm kiếm Quán ăn chuyên sâu bằng Elasticsearch
     * Hỗ trợ gõ tiếng Việt có dấu, không dấu, sai lỗi chính tả (fuzzy), tìm theo tên món, địa chỉ, chủ sở hữu, SĐT
     * 
     * @return array<string>|null Danh sách ID quán sắp xếp theo độ phù hợp (relevance score), hoặc null nếu ES offline
     */
    public function searchQuan(string $keyword, array $filters = [], int $limit = 100): ?array
    {
        $keyword = trim($keyword);
        if ($keyword === '' || ! self::isAvailable()) {
            return null;
        }

        $client = self::getClient();
        if (! $client) {
            return null;
        }

        try {
            $index = 'quan';
            if (! $client->indices()->exists(['index' => $index])->asBool()) {
                return null;
            }

            $filterQueries = [];
            $unaccent = self::removeVietnameseTones($keyword);
            $cleanKeyword = preg_replace('/[^\p{L}\p{N}]+/u', '', $keyword);
            $cleanUnaccent = preg_replace('/[^\p{L}\p{N}]+/u', '', $unaccent);

            $tokens = preg_split('/\s+/u', $keyword, -1, PREG_SPLIT_NO_EMPTY) ?: [$keyword];
            $tokenCount = count($tokens);
            $minShouldMatch = ($tokenCount <= 2) ? '100%' : '2<66%';

            $phoneticVariants = [];
            if (stripos($unaccent, 'f') !== false) {
                $phoneticVariants[] = str_ireplace('f', 'ph', $unaccent);
            }
            if (stripos($unaccent, 'ph') !== false) {
                $phoneticVariants[] = str_ireplace('ph', 'f', $unaccent);
            }

            $shouldQueries = [
                // 1. Khớp chính xác cụm từ (Tên quán, Địa chỉ, Món ăn - ưu tiên cao nhất)
                [
                    'match_phrase' => [
                        'ten_quan' => ['query' => $keyword, 'boost' => 60],
                    ],
                ],
                [
                    'match_phrase' => [
                        'dia_chi' => ['query' => $keyword, 'boost' => 40],
                    ],
                ],
                [
                    'match_phrase' => [
                        'mon_an' => ['query' => $keyword, 'boost' => 30],
                    ],
                ],
                // 2. Khớp cụm từ bắt đầu (Khi đang gõ dở từ khóa)
                [
                    'match_phrase_prefix' => [
                        'ten_quan' => ['query' => $keyword, 'boost' => 35],
                    ],
                ],
                [
                    'match_phrase_prefix' => [
                        'dia_chi' => ['query' => $keyword, 'boost' => 25],
                    ],
                ],
                [
                    'match_phrase_prefix' => [
                        'mon_an' => ['query' => $keyword, 'boost' => 20],
                    ],
                ],
                // 3. Khớp chính xác toàn bộ từ khóa (operator AND giữa các từ)
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => [
                            'ten_quan^25',
                            'mon_an^12',
                            'dia_chi^8',
                            'chu_quan^6',
                            'loai_hinh_kinh_doanh^4',
                        ],
                        'type' => 'cross_fields',
                        'operator' => 'and',
                        'boost' => 18,
                    ],
                ],
                // 4. Khớp chứa chuỗi chính xác (Wildcard)
                [
                    'wildcard' => [
                        'ten_quan.keyword' => [
                            'value' => '*' . mb_strtolower($keyword) . '*',
                            'case_insensitive' => true,
                            'boost' => 16,
                        ],
                    ],
                ],
                // 5. Khớp sai lỗi chính tả có kiểm soát (Fuzzy với prefix_length = 1 và operator AND để không match nhầm từ khác)
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => [
                            'ten_quan^10',
                            'mon_an^5',
                        ],
                        'type' => 'best_fields',
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 1,
                        'operator' => 'and',
                        'boost' => 4,
                    ],
                ],
            ];

            // 6. Khớp compact (viết liền không dấu cách) trên Tên quán và Món ăn
            if (mb_strlen($cleanKeyword) >= 2) {
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $cleanKeyword,
                        'fields' => ['ten_quan.compact^14', 'mon_an.compact^6'],
                        'type' => 'best_fields',
                        'boost' => 8,
                    ],
                ];
            }

            // 7. Nhánh truy vấn tiếng Việt không dấu
            if ($unaccent !== $keyword) {
                $shouldQueries[] = [
                    'match_phrase' => [
                        'ten_quan' => ['query' => $unaccent, 'boost' => 45],
                    ],
                ];
                $shouldQueries[] = [
                    'match_phrase' => [
                        'dia_chi' => ['query' => $unaccent, 'boost' => 30],
                    ],
                ];
                $shouldQueries[] = [
                    'match_phrase_prefix' => [
                        'ten_quan' => ['query' => $unaccent, 'boost' => 28],
                    ],
                ];
                $shouldQueries[] = [
                    'match_phrase_prefix' => [
                        'dia_chi' => ['query' => $unaccent, 'boost' => 20],
                    ],
                ];
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $unaccent,
                        'fields' => ['ten_quan^20', 'mon_an^10', 'dia_chi^6', 'chu_quan^4'],
                        'type' => 'cross_fields',
                        'operator' => 'and',
                        'boost' => 15,
                    ],
                ];
                $shouldQueries[] = [
                    'wildcard' => [
                        'ten_quan.keyword' => [
                            'value' => '*' . mb_strtolower($unaccent) . '*',
                            'case_insensitive' => true,
                            'boost' => 14,
                        ],
                    ],
                ];
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $unaccent,
                        'fields' => ['ten_quan^8', 'mon_an^4'],
                        'type' => 'best_fields',
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 1,
                        'operator' => 'and',
                        'boost' => 3,
                    ],
                ];
                if (mb_strlen($cleanUnaccent) >= 2) {
                    $shouldQueries[] = [
                        'multi_match' => [
                            'query' => $cleanUnaccent,
                            'fields' => ['ten_quan.compact^10', 'mon_an.compact^5'],
                            'type' => 'best_fields',
                            'boost' => 7,
                        ],
                    ];
                }
            }

            // 8. Biến thể phát âm / gõ tắt tiếng Việt (ví dụ: 'fo' <-> 'pho')
            foreach ($phoneticVariants as $variant) {
                $shouldQueries[] = [
                    'match_phrase_prefix' => [
                        'ten_quan' => ['query' => $variant, 'boost' => 22],
                    ],
                ];
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $variant,
                        'fields' => ['ten_quan^15', 'mon_an^8'],
                        'type' => 'best_fields',
                        'operator' => 'and',
                        'boost' => 12,
                    ],
                ];
            }

            // 7. Số điện thoại
            if (preg_match('/^[0-9]+$/', $cleanKeyword) && strlen($cleanKeyword) >= 3) {
                $shouldQueries[] = [
                    'wildcard' => [
                        'so_dien_thoai' => [
                            'value' => '*' . $cleanKeyword . '*',
                            'boost' => 15,
                        ],
                    ],
                ];
            }

            $mustQueries = [];
            $mustQueries[] = [
                'bool' => [
                    'should' => $shouldQueries,
                    'minimum_should_match' => 1,
                ],
            ];

            // Áp dụng filters bổ sung nếu có
            if (! empty($filters['trang_thai'])) {
                $filterQueries[] = ['term' => ['trang_thai' => $filters['trang_thai']]];
            }
            if (isset($filters['is_noi_bat'])) {
                $filterQueries[] = ['term' => ['is_noi_bat' => (bool) $filters['is_noi_bat']]];
            }
            if (isset($filters['is_xac_thuc'])) {
                $filterQueries[] = ['term' => ['is_xac_thuc' => (bool) $filters['is_xac_thuc']]];
            }
            if (! empty($filters['loai_hinh_kinh_doanh'])) {
                $filterQueries[] = ['term' => ['loai_hinh_kinh_doanh.keyword' => $filters['loai_hinh_kinh_doanh']]];
            }

            $body = [
                'query' => [
                    'bool' => [
                        'must' => $mustQueries,
                        'filter' => $filterQueries,
                    ],
                ],
                'size' => $limit,
            ];

            $response = $client->search([
                'index' => $index,
                'body' => $body,
            ]);

            $hits = $response['hits']['hits'] ?? [];
            $maxScore = $response['hits']['max_score'] ?? 0;
            if ($maxScore > 0) {
                // Lọc bỏ kết quả rác/nhiễu: Giữ lại các tài liệu có độ phù hợp cao với top score
                $ratio = ($maxScore >= 100) ? 0.35 : (($maxScore >= 20) ? 0.30 : 0.25);
                $threshold = $maxScore * $ratio;
                $hits = array_filter($hits, fn ($hit) => ($hit['_score'] ?? 0) >= $threshold);
            }

            return array_values(array_map(fn ($hit) => (string) $hit['_id'], $hits));
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch searchQuan error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm kiếm Người dùng / Tài khoản chuyên sâu bằng Elasticsearch
     * 
     * @return array<string>|null Danh sách ID người dùng hoặc null nếu ES offline
     */
    public function searchUser(string $keyword, array $filters = [], int $limit = 100): ?array
    {
        $keyword = trim($keyword);
        if ($keyword === '' || ! self::isAvailable()) {
            return null;
        }

        $client = self::getClient();
        if (! $client) {
            return null;
        }

        try {
            $index = 'users';
            if (! $client->indices()->exists(['index' => $index])->asBool()) {
                return null;
            }

            $filterQueries = [];
            if (! empty($filters['vai_tro_id'])) {
                $filterQueries[] = ['term' => ['vai_tro_id' => (string) $filters['vai_tro_id']]];
            }
            if (! empty($filters['trang_thai'])) {
                $filterQueries[] = ['term' => ['trang_thai' => (string) $filters['trang_thai']]];
            }
            if (isset($filters['da_xac_thuc'])) {
                $filterQueries[] = ['term' => ['da_xac_thuc' => (bool) $filters['da_xac_thuc']]];
            }

            $unaccent = self::removeVietnameseTones($keyword);
            $cleanKeyword = preg_replace('/[^\p{L}\p{N}]+/u', '', $keyword);
            $cleanUnaccent = preg_replace('/[^\p{L}\p{N}]+/u', '', $unaccent);

            $tokens = preg_split('/\s+/u', $keyword, -1, PREG_SPLIT_NO_EMPTY) ?: [$keyword];
            $tokenCount = count($tokens);
            $minShouldMatch = ($tokenCount <= 2) ? '100%' : '2<66%';

            $shouldQueries = [
                // 1. Khớp chính xác cụm từ họ tên và email (ưu tiên cao nhất)
                [
                    'match_phrase' => [
                        'ho_ten' => ['query' => $keyword, 'boost' => 60],
                    ],
                ],
                [
                    'match_phrase' => [
                        'email' => ['query' => $keyword, 'boost' => 45],
                    ],
                ],
                [
                    'match_phrase_prefix' => [
                        'ho_ten' => ['query' => $keyword, 'boost' => 30],
                    ],
                ],
                [
                    'match_phrase_prefix' => [
                        'email' => ['query' => $keyword, 'boost' => 20],
                    ],
                ],
                // 2. Khớp đa trường chính xác (operator AND)
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['ho_ten^20', 'email^12', 'so_dien_thoai^15'],
                        'type' => 'cross_fields',
                        'operator' => 'and',
                        'boost' => 18,
                    ],
                ],
                // 3. Khớp sai lỗi chính tả có kiểm soát (Fuzzy với prefix_length = 1)
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['ho_ten^10', 'email^5'],
                        'type' => 'best_fields',
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 1,
                        'operator' => 'and',
                        'boost' => 4,
                    ],
                ],
            ];

            // 3. Compact & Wildcard
            if (mb_strlen($cleanKeyword) >= 2) {
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $cleanKeyword,
                        'fields' => ['ho_ten.compact^12', 'ho_ten.ngram^8', 'email.compact^6'],
                        'type' => 'best_fields',
                        'boost' => 8,
                    ],
                ];
                $shouldQueries[] = [
                    'wildcard' => [
                        'ho_ten.keyword' => [
                            'value' => '*' . mb_strtolower($keyword) . '*',
                            'case_insensitive' => true,
                            'boost' => 8,
                        ],
                    ],
                ];
            }

            // 4. Bỏ dấu tiếng Việt
            if ($unaccent !== $keyword) {
                $shouldQueries[] = [
                    'match_phrase' => [
                        'ho_ten' => ['query' => $unaccent, 'boost' => 25],
                    ],
                ];
                $shouldQueries[] = [
                    'match_phrase_prefix' => [
                        'ho_ten' => ['query' => $unaccent, 'boost' => 18],
                    ],
                ];
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $unaccent,
                        'fields' => ['ho_ten^12', 'email^6'],
                        'type' => 'best_fields',
                        'minimum_should_match' => $minShouldMatch,
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 0,
                        'boost' => 10,
                    ],
                ];
                if (mb_strlen($cleanUnaccent) >= 2) {
                    $shouldQueries[] = [
                        'multi_match' => [
                            'query' => $cleanUnaccent,
                            'fields' => ['ho_ten.compact^10', 'ho_ten.ngram^7'],
                            'type' => 'best_fields',
                            'boost' => 7,
                        ],
                    ];
                }
            }

            if (preg_match('/^[0-9]+$/', $cleanKeyword) && strlen($cleanKeyword) >= 3) {
                $shouldQueries[] = [
                    'wildcard' => [
                        'so_dien_thoai' => [
                            'value' => '*' . $cleanKeyword . '*',
                            'boost' => 15,
                        ],
                    ],
                ];
            }

            $body = [
                'query' => [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => $shouldQueries,
                                'minimum_should_match' => 1,
                            ],
                        ],
                        'filter' => $filterQueries,
                    ],
                ],
                'size' => $limit,
            ];

            $response = $client->search([
                'index' => $index,
                'body' => $body,
            ]);

            $hits = $response['hits']['hits'] ?? [];
            $maxScore = $response['hits']['max_score'] ?? 0;
            if ($maxScore > 0) {
                $ratio = ($maxScore >= 100) ? 0.35 : (($maxScore >= 20) ? 0.30 : 0.25);
                $threshold = $maxScore * $ratio;
                $hits = array_filter($hits, fn ($hit) => ($hit['_score'] ?? 0) >= $threshold);
            }

            return array_values(array_map(fn ($hit) => (string) $hit['_id'], $hits));
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch searchUser error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm kiếm Bài viết Blog chuyên sâu bằng Elasticsearch
     * 
     * @return array<int>|null
     */
    public function searchBlog(string $keyword, array $filters = [], int $limit = 100): ?array
    {
        $keyword = trim($keyword);
        if ($keyword === '' || ! self::isAvailable()) {
            return null;
        }

        $client = self::getClient();
        if (! $client) {
            return null;
        }

        try {
            $index = 'blogs';
            if (! $client->indices()->exists(['index' => $index])->asBool()) {
                return null;
            }

            $filterQueries = [];
            if (! empty($filters['status'])) {
                $filterQueries[] = ['term' => ['status' => (string) $filters['status']]];
            }
            if (! empty($filters['category_id'])) {
                $filterQueries[] = ['term' => ['category_id' => (string) $filters['category_id']]];
            }

            $unaccent = self::removeVietnameseTones($keyword);
            $cleanKeyword = preg_replace('/[^\p{L}\p{N}]+/u', '', $keyword);
            $cleanUnaccent = preg_replace('/[^\p{L}\p{N}]+/u', '', $unaccent);

            $tokens = preg_split('/\s+/u', $keyword, -1, PREG_SPLIT_NO_EMPTY) ?: [$keyword];
            $tokenCount = count($tokens);
            $minShouldMatch = ($tokenCount <= 2) ? '100%' : '2<66%';

            $shouldQueries = [
                // 1. Khớp chính xác cụm từ tiêu đề (ưu tiên cao nhất)
                [
                    'match_phrase' => [
                        'title' => ['query' => $keyword, 'boost' => 60],
                    ],
                ],
                [
                    'match_phrase_prefix' => [
                        'title' => ['query' => $keyword, 'boost' => 35],
                    ],
                ],
                // 2. Khớp đa trường chính xác (operator AND)
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['title^20', 'author_name^10', 'category_name^8', 'excerpt^6'],
                        'type' => 'cross_fields',
                        'operator' => 'and',
                        'boost' => 18,
                    ],
                ],
                // 3. Khớp sai lỗi chính tả có kiểm soát (Fuzzy với prefix_length = 1)
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['title^10', 'author_name^5'],
                        'type' => 'best_fields',
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 1,
                        'operator' => 'and',
                        'boost' => 4,
                    ],
                ],
            ];

            if (mb_strlen($cleanKeyword) >= 2) {
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $cleanKeyword,
                        'fields' => ['title.compact^12', 'title.ngram^8'],
                        'type' => 'best_fields',
                        'boost' => 8,
                    ],
                ];
                $shouldQueries[] = [
                    'wildcard' => [
                        'title.keyword' => [
                            'value' => '*' . mb_strtolower($keyword) . '*',
                            'case_insensitive' => true,
                            'boost' => 8,
                        ],
                    ],
                ];
            }

            if ($unaccent !== $keyword) {
                $shouldQueries[] = [
                    'match_phrase' => [
                        'title' => ['query' => $unaccent, 'boost' => 25],
                    ],
                ];
                $shouldQueries[] = [
                    'match_phrase_prefix' => [
                        'title' => ['query' => $unaccent, 'boost' => 18],
                    ],
                ];
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $unaccent,
                        'fields' => ['title^12', 'author_name^5', 'category_name^4'],
                        'type' => 'best_fields',
                        'minimum_should_match' => $minShouldMatch,
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 0,
                        'boost' => 10,
                    ],
                ];
                if (mb_strlen($cleanUnaccent) >= 2) {
                    $shouldQueries[] = [
                        'multi_match' => [
                            'query' => $cleanUnaccent,
                            'fields' => ['title.compact^10', 'title.ngram^7'],
                            'type' => 'best_fields',
                            'boost' => 7,
                        ],
                    ];
                }
            }

            $body = [
                'query' => [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => $shouldQueries,
                                'minimum_should_match' => 1,
                            ],
                        ],
                        'filter' => $filterQueries,
                    ],
                ],
                'size' => $limit,
            ];

            $response = $client->search([
                'index' => $index,
                'body' => $body,
            ]);

            $hits = $response['hits']['hits'] ?? [];
            $maxScore = $response['hits']['max_score'] ?? 0;
            if ($maxScore > 0) {
                $ratio = ($maxScore >= 100) ? 0.35 : (($maxScore >= 20) ? 0.30 : 0.25);
                $threshold = $maxScore * $ratio;
                $hits = array_filter($hits, fn ($hit) => ($hit['_score'] ?? 0) >= $threshold);
            }

            return array_values(array_map(fn ($hit) => (int) $hit['_id'], $hits));
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch searchBlog error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm kiếm Danh mục quán bằng Elasticsearch
     * 
     * @return array<int>|null
     */
    public function searchDanhMuc(string $keyword, int $limit = 50): ?array
    {
        $keyword = trim($keyword);
        if ($keyword === '' || ! self::isAvailable()) {
            return null;
        }

        $client = self::getClient();
        if (! $client) {
            return null;
        }

        try {
            $index = 'danh_muc_quan';
            if (! $client->indices()->exists(['index' => $index])->asBool()) {
                return null;
            }

            $filterQueries = [];
            $unaccent = self::removeVietnameseTones($keyword);
            $cleanKeyword = preg_replace('/[^\p{L}\p{N}]+/u', '', $keyword);
            $cleanUnaccent = preg_replace('/[^\p{L}\p{N}]+/u', '', $unaccent);

            $shouldQueries = [
                // 1. Khớp chính xác tên danh mục (ưu tiên cao nhất)
                [
                    'match_phrase' => [
                        'ten_danh_muc' => ['query' => $keyword, 'boost' => 60],
                    ],
                ],
                // 2. Khớp cụm từ bắt đầu
                [
                    'match_phrase_prefix' => [
                        'ten_danh_muc' => ['query' => $keyword, 'boost' => 35],
                    ],
                ],
                // 3. Khớp chính xác toàn bộ từ khóa
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['ten_danh_muc^25', 'slug^8'],
                        'type' => 'cross_fields',
                        'operator' => 'and',
                        'boost' => 18,
                    ],
                ],
                // 4. Wildcard chứa từ khóa
                [
                    'wildcard' => [
                        'ten_danh_muc.keyword' => [
                            'value' => '*' . mb_strtolower($keyword) . '*',
                            'case_insensitive' => true,
                            'boost' => 16,
                        ],
                    ],
                ],
                // 5. Khớp sai lỗi chính tả có kiểm soát (prefix_length = 1, operator = and)
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['ten_danh_muc^10', 'slug^4'],
                        'type' => 'best_fields',
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 1,
                        'operator' => 'and',
                        'boost' => 4,
                    ],
                ],
            ];

            if ($unaccent !== $keyword) {
                $shouldQueries[] = [
                    'match_phrase' => [
                        'ten_danh_muc' => ['query' => $unaccent, 'boost' => 45],
                    ],
                ];
                $shouldQueries[] = [
                    'match_phrase_prefix' => [
                        'ten_danh_muc' => ['query' => $unaccent, 'boost' => 28],
                    ],
                ];
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $unaccent,
                        'fields' => ['ten_danh_muc^20', 'slug^6'],
                        'type' => 'cross_fields',
                        'operator' => 'and',
                        'boost' => 15,
                    ],
                ];
                $shouldQueries[] = [
                    'wildcard' => [
                        'ten_danh_muc.keyword' => [
                            'value' => '*' . mb_strtolower($unaccent) . '*',
                            'case_insensitive' => true,
                            'boost' => 14,
                        ],
                    ],
                ];
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $unaccent,
                        'fields' => ['ten_danh_muc^8', 'slug^3'],
                        'type' => 'best_fields',
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 1,
                        'operator' => 'and',
                        'boost' => 3,
                    ],
                ];
            }

            if ($cleanKeyword !== '' && $cleanKeyword !== $keyword) {
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $cleanKeyword,
                        'fields' => ['ten_danh_muc.compact^10'],
                        'type' => 'best_fields',
                        'boost' => 6,
                    ],
                ];
            }

            if ($cleanUnaccent !== '' && $cleanUnaccent !== $cleanKeyword) {
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $cleanUnaccent,
                        'fields' => ['ten_danh_muc.compact^8'],
                        'type' => 'best_fields',
                        'boost' => 5,
                    ],
                ];
            }

            $body = [
                'query' => [
                    'bool' => [
                        'should' => $shouldQueries,
                        'minimum_should_match' => 1,
                    ],
                ],
                'size' => $limit,
            ];

            $response = $client->search([
                'index' => $index,
                'body' => $body,
            ]);

            $hits = $response['hits']['hits'] ?? [];
            $maxScore = $response['hits']['max_score'] ?? 0;
            if ($maxScore > 0) {
                $ratio = ($maxScore >= 100) ? 0.35 : (($maxScore >= 20) ? 0.30 : 0.25);
                $threshold = $maxScore * $ratio;
                $hits = array_filter($hits, fn ($hit) => ($hit['_score'] ?? 0) >= $threshold);
            }

            return array_values(array_map(fn ($hit) => (int) $hit['_id'], $hits));
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch searchDanhMuc error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm kiếm Video Shorts / TikTok bằng Elasticsearch
     * 
     * @return array<int>|null
     */
    public function searchVideo(string $keyword, array $filters = [], int $limit = 50): ?array
    {
        $keyword = trim($keyword);
        if ($keyword === '' || ! self::isAvailable()) {
            return null;
        }

        $client = self::getClient();
        if (! $client) {
            return null;
        }

        try {
            $index = 'video_shorts';
            if (! $client->indices()->exists(['index' => $index])->asBool()) {
                return null;
            }

            $filterQueries = [];
            if (! empty($filters['trang_thai'])) {
                $filterQueries[] = ['term' => ['trang_thai' => (string) $filters['trang_thai']]];
            }

            $unaccent = self::removeVietnameseTones($keyword);
            $cleanKeyword = preg_replace('/[^\p{L}\p{N}]+/u', '', $keyword);
            $cleanUnaccent = preg_replace('/[^\p{L}\p{N}]+/u', '', $unaccent);

            $shouldQueries = [
                // 1. Khớp chính xác tiêu đề & tên quán (ưu tiên cao nhất)
                [
                    'match_phrase' => [
                        'tieu_de' => ['query' => $keyword, 'boost' => 60],
                    ],
                ],
                [
                    'match_phrase' => [
                        'ten_quan' => ['query' => $keyword, 'boost' => 40],
                    ],
                ],
                // 2. Khớp cụm từ bắt đầu
                [
                    'match_phrase_prefix' => [
                        'tieu_de' => ['query' => $keyword, 'boost' => 35],
                    ],
                ],
                [
                    'match_phrase_prefix' => [
                        'ten_quan' => ['query' => $keyword, 'boost' => 25],
                    ],
                ],
                // 3. Khớp chính xác toàn bộ từ khóa (cross_fields + and)
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['tieu_de^25', 'nguoi_dang^10', 'ten_quan^12'],
                        'type' => 'cross_fields',
                        'operator' => 'and',
                        'boost' => 18,
                    ],
                ],
                // 4. Wildcard
                [
                    'wildcard' => [
                        'tieu_de.keyword' => [
                            'value' => '*' . mb_strtolower($keyword) . '*',
                            'case_insensitive' => true,
                            'boost' => 16,
                        ],
                    ],
                ],
                [
                    'wildcard' => [
                        'video_id' => [
                            'value' => '*' . $keyword . '*',
                            'boost' => 12,
                        ],
                    ],
                ],
                // 5. Khớp sai lỗi chính tả có kiểm soát (prefix_length = 1, operator = and)
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['tieu_de^10', 'ten_quan^6'],
                        'type' => 'best_fields',
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 1,
                        'operator' => 'and',
                        'boost' => 4,
                    ],
                ],
            ];

            if ($unaccent !== $keyword) {
                $shouldQueries[] = [
                    'match_phrase' => [
                        'tieu_de' => ['query' => $unaccent, 'boost' => 45],
                    ],
                ];
                $shouldQueries[] = [
                    'match_phrase' => [
                        'ten_quan' => ['query' => $unaccent, 'boost' => 30],
                    ],
                ];
                $shouldQueries[] = [
                    'match_phrase_prefix' => [
                        'tieu_de' => ['query' => $unaccent, 'boost' => 28],
                    ],
                ];
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $unaccent,
                        'fields' => ['tieu_de^20', 'nguoi_dang^8', 'ten_quan^10'],
                        'type' => 'cross_fields',
                        'operator' => 'and',
                        'boost' => 15,
                    ],
                ];
                $shouldQueries[] = [
                    'wildcard' => [
                        'tieu_de.keyword' => [
                            'value' => '*' . mb_strtolower($unaccent) . '*',
                            'case_insensitive' => true,
                            'boost' => 14,
                        ],
                    ],
                ];
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $unaccent,
                        'fields' => ['tieu_de^8', 'ten_quan^4'],
                        'type' => 'best_fields',
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 1,
                        'operator' => 'and',
                        'boost' => 3,
                    ],
                ];
            }

            if ($cleanKeyword !== '' && $cleanKeyword !== $keyword) {
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $cleanKeyword,
                        'fields' => ['tieu_de.compact^10', 'ten_quan.compact^6'],
                        'type' => 'best_fields',
                        'boost' => 6,
                    ],
                ];
            }

            if ($cleanUnaccent !== '' && $cleanUnaccent !== $cleanKeyword) {
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $cleanUnaccent,
                        'fields' => ['tieu_de.compact^8'],
                        'type' => 'best_fields',
                        'boost' => 5,
                    ],
                ];
            }

            $body = [
                'query' => [
                    'bool' => [
                        'must' => [
                            'bool' => [
                                'should' => $shouldQueries,
                                'minimum_should_match' => 1,
                            ],
                        ],
                        'filter' => $filterQueries,
                    ],
                ],
                'size' => $limit,
            ];

            $response = $client->search([
                'index' => $index,
                'body' => $body,
            ]);

            $hits = $response['hits']['hits'] ?? [];
            $maxScore = $response['hits']['max_score'] ?? 0;
            if ($maxScore > 0) {
                $ratio = ($maxScore >= 100) ? 0.35 : (($maxScore >= 20) ? 0.30 : 0.25);
                $threshold = $maxScore * $ratio;
                $hits = array_filter($hits, fn ($hit) => ($hit['_score'] ?? 0) >= $threshold);
            }

            return array_values(array_map(fn ($hit) => (int) $hit['_id'], $hits));
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch searchVideo error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm kiếm Khuyến mãi / Voucher bằng Elasticsearch
     * 
     * @return array<string>|null
     */
    public function searchKhuyenMai(string $keyword, int $limit = 50): ?array
    {
        $keyword = trim($keyword);
        if ($keyword === '' || ! self::isAvailable()) {
            return null;
        }

        $client = self::getClient();
        if (! $client) {
            return null;
        }

        try {
            $index = 'khuyen_mai';
            if (! $client->indices()->exists(['index' => $index])->asBool()) {
                return null;
            }

            $filterQueries = [];
            $unaccent = self::removeVietnameseTones($keyword);
            $cleanKeyword = preg_replace('/[^\p{L}\p{N}]+/u', '', $keyword);
            $cleanUnaccent = preg_replace('/[^\p{L}\p{N}]+/u', '', $unaccent);

            $shouldQueries = [
                // 1. Khớp chính xác mã voucher & tiêu đề (ưu tiên cao nhất)
                [
                    'match_phrase' => [
                        'ma_voucher' => ['query' => $keyword, 'boost' => 60],
                    ],
                ],
                [
                    'match_phrase' => [
                        'tieu_de' => ['query' => $keyword, 'boost' => 50],
                    ],
                ],
                [
                    'match_phrase' => [
                        'ten_quan' => ['query' => $keyword, 'boost' => 35],
                    ],
                ],
                // 2. Khớp cụm từ bắt đầu
                [
                    'match_phrase_prefix' => [
                        'ma_voucher' => ['query' => $keyword, 'boost' => 35],
                    ],
                ],
                [
                    'match_phrase_prefix' => [
                        'tieu_de' => ['query' => $keyword, 'boost' => 30],
                    ],
                ],
                // 3. Khớp chính xác toàn bộ từ khóa (cross_fields + and)
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['ma_voucher^25', 'tieu_de^20', 'ten_quan^12'],
                        'type' => 'cross_fields',
                        'operator' => 'and',
                        'boost' => 18,
                    ],
                ],
                // 4. Wildcard
                [
                    'wildcard' => [
                        'ma_voucher.keyword' => [
                            'value' => '*' . mb_strtolower($keyword) . '*',
                            'case_insensitive' => true,
                            'boost' => 16,
                        ],
                    ],
                ],
                [
                    'wildcard' => [
                        'tieu_de.keyword' => [
                            'value' => '*' . mb_strtolower($keyword) . '*',
                            'case_insensitive' => true,
                            'boost' => 14,
                        ],
                    ],
                ],
                // 5. Khớp sai lỗi chính tả có kiểm soát (prefix_length = 1, operator = and)
                [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['ma_voucher^10', 'tieu_de^8'],
                        'type' => 'best_fields',
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 1,
                        'operator' => 'and',
                        'boost' => 4,
                    ],
                ],
            ];

            if ($unaccent !== $keyword) {
                $shouldQueries[] = [
                    'match_phrase' => [
                        'ma_voucher' => ['query' => $unaccent, 'boost' => 45],
                    ],
                ];
                $shouldQueries[] = [
                    'match_phrase' => [
                        'tieu_de' => ['query' => $unaccent, 'boost' => 38],
                    ],
                ];
                $shouldQueries[] = [
                    'match_phrase_prefix' => [
                        'tieu_de' => ['query' => $unaccent, 'boost' => 25],
                    ],
                ];
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $unaccent,
                        'fields' => ['ma_voucher^20', 'tieu_de^15', 'ten_quan^10'],
                        'type' => 'cross_fields',
                        'operator' => 'and',
                        'boost' => 15,
                    ],
                ];
                $shouldQueries[] = [
                    'wildcard' => [
                        'tieu_de.keyword' => [
                            'value' => '*' . mb_strtolower($unaccent) . '*',
                            'case_insensitive' => true,
                            'boost' => 12,
                        ],
                    ],
                ];
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $unaccent,
                        'fields' => ['ma_voucher^8', 'tieu_de^6'],
                        'type' => 'best_fields',
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 1,
                        'operator' => 'and',
                        'boost' => 3,
                    ],
                ];
            }

            if ($cleanKeyword !== '' && $cleanKeyword !== $keyword) {
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $cleanKeyword,
                        'fields' => ['ma_voucher.compact^10', 'tieu_de.compact^8'],
                        'type' => 'best_fields',
                        'boost' => 6,
                    ],
                ];
            }

            if ($cleanUnaccent !== '' && $cleanUnaccent !== $cleanKeyword) {
                $shouldQueries[] = [
                    'multi_match' => [
                        'query' => $cleanUnaccent,
                        'fields' => ['ma_voucher.compact^8', 'tieu_de.compact^6'],
                        'type' => 'best_fields',
                        'boost' => 5,
                    ],
                ];
            }

            $body = [
                'query' => [
                    'bool' => [
                        'should' => $shouldQueries,
                        'minimum_should_match' => 1,
                    ],
                ],
                'size' => $limit,
            ];

            $response = $client->search([
                'index' => $index,
                'body' => $body,
            ]);

            $hits = $response['hits']['hits'] ?? [];
            $maxScore = $response['hits']['max_score'] ?? 0;
            if ($maxScore > 0) {
                $ratio = ($maxScore >= 100) ? 0.35 : (($maxScore >= 20) ? 0.30 : 0.25);
                $threshold = $maxScore * $ratio;
                $hits = array_filter($hits, fn ($hit) => ($hit['_score'] ?? 0) >= $threshold);
            }

            return array_values(array_map(fn ($hit) => (string) $hit['_id'], $hits));
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch searchKhuyenMai error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Global Admin Unified Search (phục vụ Command Palette Ctrl + K và thanh tìm kiếm toàn năng)
     * Tìm kiếm đồng thời Quán, Người dùng, Bài viết blog, Video, Danh mục và Voucher
     */
    public function globalAdminSearch(string $keyword, int $limitPerType = 5): array
    {
        $results = [
            'quans' => [],
            'users' => [],
            'blogs' => [],
            'categories' => [],
            'videos' => [],
            'promotions' => [],
        ];

        if (trim($keyword) === '') {
            return $results;
        }

        // 1. Quán ăn
        $quanIds = $this->searchQuan($keyword, [], $limitPerType);
        if ($quanIds !== null && ! empty($quanIds)) {
            $results['quans'] = Quan::whereIn('id', $quanIds)
                ->select('id', 'ten_quan', 'dia_chi_chi_tiet', 'loai_hinh_kinh_doanh', 'trang_thai', 'anh_bia')
                ->get()
                ->sortBy(fn ($q) => array_search($q->id, $quanIds))
                ->values()
                ->toArray();
        } elseif (empty($results['quans'])) {
            $results['quans'] = Quan::where('ten_quan', 'like', "%{$keyword}%")
                ->orWhere('dia_chi_chi_tiet', 'like', "%{$keyword}%")
                ->select('id', 'ten_quan', 'dia_chi_chi_tiet', 'loai_hinh_kinh_doanh', 'trang_thai', 'anh_bia')
                ->limit($limitPerType)
                ->get()
                ->toArray();
        }

        // 2. Người dùng
        $userIds = $this->searchUser($keyword, [], $limitPerType);
        if ($userIds !== null && ! empty($userIds)) {
            $results['users'] = User::whereIn('id', $userIds)
                ->select('id', 'ho_ten', 'email', 'so_dien_thoai', 'trang_thai', 'da_xac_thuc')
                ->get()
                ->sortBy(fn ($u) => array_search($u->id, $userIds))
                ->values()
                ->toArray();
        } elseif (empty($results['users'])) {
            $results['users'] = User::where('ho_ten', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")
                ->select('id', 'ho_ten', 'email', 'so_dien_thoai', 'trang_thai', 'da_xac_thuc')
                ->limit($limitPerType)
                ->get()
                ->toArray();
        }

        // 3. Blog
        $blogIds = $this->searchBlog($keyword, [], $limitPerType);
        if ($blogIds !== null && ! empty($blogIds)) {
            $results['blogs'] = Blog::whereIn('id', $blogIds)
                ->select('id', 'title', 'slug', 'status', 'created_at')
                ->get()
                ->sortBy(fn ($b) => array_search($b->id, $blogIds))
                ->values()
                ->toArray();
        } elseif (empty($results['blogs'])) {
            $results['blogs'] = Blog::where('title', 'like', "%{$keyword}%")
                ->select('id', 'title', 'slug', 'status', 'created_at')
                ->limit($limitPerType)
                ->get()
                ->toArray();
        }

        // 4. Danh mục
        $catIds = $this->searchDanhMuc($keyword, $limitPerType);
        if ($catIds !== null && ! empty($catIds)) {
            $results['categories'] = DanhMucQuan::whereIn('id', $catIds)
                ->select('id', 'ten_danh_muc', 'slug', 'icon', 'mau_sac')
                ->get()
                ->sortBy(fn ($c) => array_search($c->id, $catIds))
                ->values()
                ->toArray();
        } elseif (empty($results['categories'])) {
            $results['categories'] = DanhMucQuan::where('ten_danh_muc', 'like', "%{$keyword}%")
                ->select('id', 'ten_danh_muc', 'slug', 'icon', 'mau_sac')
                ->limit($limitPerType)
                ->get()
                ->toArray();
        }

        // 5. Video
        $videoIds = $this->searchVideo($keyword, [], $limitPerType);
        if ($videoIds !== null && ! empty($videoIds)) {
            $results['videos'] = VideoShort::whereIn('id', $videoIds)
                ->select('id', 'tieu_de', 'nguoi_dang', 'trang_thai')
                ->get()
                ->sortBy(fn ($v) => array_search($v->id, $videoIds))
                ->values()
                ->toArray();
        } elseif (empty($results['videos'])) {
            $results['videos'] = VideoShort::where('tieu_de', 'like', "%{$keyword}%")
                ->orWhere('nguoi_dang', 'like', "%{$keyword}%")
                ->select('id', 'tieu_de', 'nguoi_dang', 'trang_thai')
                ->limit($limitPerType)
                ->get()
                ->toArray();
        }

        // 6. Khuyến mãi / Voucher
        $kmIds = $this->searchKhuyenMai($keyword, $limitPerType);
        if ($kmIds !== null && ! empty($kmIds)) {
            $results['promotions'] = KhuyenMai::whereIn('id', $kmIds)
                ->with('quan:id,ten_quan')
                ->get()
                ->sortBy(fn ($km) => array_search($km->id, $kmIds))
                ->values()
                ->toArray();
        } elseif (empty($results['promotions'])) {
            $results['promotions'] = KhuyenMai::where('tieu_de', 'like', "%{$keyword}%")
                ->orWhere('ma_voucher', 'like', "%{$keyword}%")
                ->with('quan:id,ten_quan')
                ->limit($limitPerType)
                ->get()
                ->toArray();
        }

        return $results;
    }

    // =========================================================================
    // Realtime Indexing & Document Synchronization
    // =========================================================================

    /**
     * Đồng bộ 1 Quán vào Elasticsearch index
     */
    public function syncQuan(Quan $quan): bool
    {
        $client = self::getClient();
        if (! $client || ! self::isAvailable()) {
            return false;
        }

        try {
            $chuQuan = $quan->relationLoaded('chuQuan') ? $quan->chuQuan : $quan->chuQuan()->first();
            $danhSachMon = [];
            if ($quan->relationLoaded('danhMucMenu')) {
                $danhSachMon = $quan->danhMucMenu->pluck('monAn')->flatten()->pluck('ten_mon')->filter()->unique()->values()->all();
            } else {
                $danhSachMon = $quan->danhMucMenu()->with('monAn:id,danh_muc_id,ten_mon')->get()->pluck('monAn')->flatten()->pluck('ten_mon')->filter()->unique()->values()->all();
            }

            $body = [
                'id' => (string) $quan->id,
                'ten_quan' => (string) $quan->ten_quan,
                'mon_an' => $danhSachMon,
                'loai_hinh_kinh_doanh' => (string) ($quan->loai_hinh_kinh_doanh ?? ''),
                'mo_ta' => (string) ($quan->mo_ta ?? ''),
                'dia_chi' => trim($quan->dia_chi_chi_tiet . ', ' . $quan->ten_phuong_xa . ', ' . $quan->ten_quan_huyen . ', ' . $quan->ten_tinh_thanh),
                'so_dien_thoai' => (string) ($quan->so_dien_thoai ?? ''),
                'email' => (string) ($quan->email ?? ''),
                'chu_quan' => $chuQuan ? (string) $chuQuan->ho_ten : '',
                'trang_thai' => (string) $quan->trang_thai,
                'is_noi_bat' => (bool) $quan->is_noi_bat,
                'is_xac_thuc' => (bool) $quan->is_xac_thuc,
                'co_shopeefood' => ! empty($quan->shopeefood_url),
                'gia_nho_nhat' => (float) ($quan->gia_nho_nhat ?? 0),
                'gia_lon_nhat' => (float) ($quan->gia_lon_nhat ?? 0),
                'gio_mo_cua' => (string) ($quan->gio_mo_cua ?? ''),
                'gio_dong_cua' => (string) ($quan->gio_dong_cua ?? ''),
                'luot_xem' => (int) ($quan->luot_xem ?? 0),
                'created_at' => $quan->created_at ? $quan->created_at->timestamp : null,
                'updated_at' => $quan->updated_at ? $quan->updated_at->timestamp : null,
            ];

            if (! empty($quan->vi_do) && ! empty($quan->kinh_do) && is_numeric($quan->vi_do) && is_numeric($quan->kinh_do)) {
                $body['location'] = [
                    'lat' => (float) $quan->vi_do,
                    'lon' => (float) $quan->kinh_do,
                ];
            }

            $client->index([
                'index' => 'quan',
                'id' => (string) $quan->id,
                'body' => $body,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning("Elasticsearch syncQuan [{$quan->id}] failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đồng bộ 1 Người dùng vào Elasticsearch index
     */
    public function syncUser(User $user): bool
    {
        $client = self::getClient();
        if (! $client || ! self::isAvailable()) {
            return false;
        }

        try {
            $client->index([
                'index' => 'users',
                'id' => (string) $user->id,
                'body' => [
                    'id' => (string) $user->id,
                    'ho_ten' => (string) ($user->ho_ten ?? ''),
                    'email' => (string) ($user->email ?? ''),
                    'so_dien_thoai' => (string) ($user->so_dien_thoai ?? ''),
                    'trang_thai' => (string) ($user->trang_thai ?? ''),
                    'vai_tro_id' => (string) ($user->vai_tro_id ?? ''),
                    'da_xac_thuc' => (bool) $user->da_xac_thuc,
                    'created_at' => $user->created_at ? $user->created_at->timestamp : null,
                    'updated_at' => $user->updated_at ? $user->updated_at->timestamp : null,
                ],
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning("Elasticsearch syncUser [{$user->id}] failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đồng bộ 1 Blog vào Elasticsearch index
     */
    public function syncBlog(Blog $blog): bool
    {
        $client = self::getClient();
        if (! $client || ! self::isAvailable()) {
            return false;
        }

        try {
            $author = $blog->relationLoaded('user') ? $blog->user : $blog->user()->first();
            $category = $blog->relationLoaded('category') ? $blog->category : $blog->category()->first();

            $client->index([
                'index' => 'blogs',
                'id' => (string) $blog->id,
                'body' => [
                    'id' => (string) $blog->id,
                    'title' => (string) $blog->title,
                    'slug' => (string) $blog->slug,
                    'excerpt' => (string) ($blog->excerpt ?? ''),
                    'content' => strip_tags((string) ($blog->content ?? '')),
                    'status' => (string) $blog->status,
                    'author_name' => $author ? (string) $author->ho_ten : '',
                    'category_id' => (string) ($blog->category_id ?? ''),
                    'category_name' => $category ? (string) $category->name : '',
                    'created_at' => $blog->created_at ? $blog->created_at->timestamp : null,
                    'updated_at' => $blog->updated_at ? $blog->updated_at->timestamp : null,
                ],
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning("Elasticsearch syncBlog [{$blog->id}] failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đồng bộ 1 Danh mục vào Elasticsearch index
     */
    public function syncDanhMuc(DanhMucQuan $dm): bool
    {
        $client = self::getClient();
        if (! $client || ! self::isAvailable()) {
            return false;
        }

        try {
            $client->index([
                'index' => 'danh_muc_quan',
                'id' => (string) $dm->id,
                'body' => [
                    'id' => (string) $dm->id,
                    'ten_danh_muc' => (string) $dm->ten_danh_muc,
                    'slug' => (string) $dm->slug,
                    'mo_ta' => (string) ($dm->mo_ta ?? ''),
                    'icon' => (string) ($dm->icon ?? ''),
                    'mau_sac' => (string) ($dm->mau_sac ?? ''),
                    'is_hien_thi' => (bool) $dm->is_hien_thi,
                    'is_noi_bat' => (bool) $dm->is_noi_bat,
                    'thu_tu' => (int) ($dm->thu_tu ?? 0),
                    'created_at' => $dm->created_at ? $dm->created_at->timestamp : null,
                    'updated_at' => $dm->updated_at ? $dm->updated_at->timestamp : null,
                ],
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning("Elasticsearch syncDanhMuc [{$dm->id}] failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đồng bộ 1 Video Short vào Elasticsearch index
     */
    public function syncVideo(VideoShort $v): bool
    {
        $client = self::getClient();
        if (! $client || ! self::isAvailable()) {
            return false;
        }

        try {
            $quan = $v->relationLoaded('quan') ? $v->quan : $v->quan()->first();

            $client->index([
                'index' => 'video_shorts',
                'id' => (string) $v->id,
                'body' => [
                    'id' => (string) $v->id,
                    'tieu_de' => (string) ($v->tieu_de ?? ''),
                    'video_id' => (string) ($v->video_id ?? ''),
                    'nguoi_dang' => (string) ($v->nguoi_dang ?? ''),
                    'ten_quan' => $quan ? (string) $quan->ten_quan : '',
                    'trang_thai' => (string) $v->trang_thai,
                    'created_at' => $v->created_at ? $v->created_at->timestamp : null,
                    'updated_at' => $v->updated_at ? $v->updated_at->timestamp : null,
                ],
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning("Elasticsearch syncVideo [{$v->id}] failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đồng bộ 1 Khuyến mãi vào Elasticsearch index
     */
    public function syncKhuyenMai(KhuyenMai $km): bool
    {
        $client = self::getClient();
        if (! $client || ! self::isAvailable()) {
            return false;
        }

        try {
            $quan = $km->relationLoaded('quan') ? $km->quan : $km->quan()->first();

            $client->index([
                'index' => 'khuyen_mai',
                'id' => (string) $km->id,
                'body' => [
                    'id' => (string) $km->id,
                    'tieu_de' => (string) ($km->tieu_de ?? ''),
                    'ma_voucher' => (string) ($km->ma_voucher ?? ''),
                    'slogan' => (string) ($km->slogan ?? ''),
                    'noi_dung' => (string) ($km->noi_dung ?? ''),
                    'ten_quan' => $quan ? (string) $quan->ten_quan : '',
                    'loai_khuyen_mai' => (string) ($km->loai_khuyen_mai ?? ''),
                    'created_at' => $km->created_at ? $km->created_at->timestamp : null,
                    'updated_at' => $km->updated_at ? $km->updated_at->timestamp : null,
                ],
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning("Elasticsearch syncKhuyenMai [{$km->id}] failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa 1 document khỏi Elasticsearch index
     */
    public function deleteDocument(string $index, string|int $id): bool
    {
        $client = self::getClient();
        if (! $client || ! self::isAvailable()) {
            return false;
        }

        try {
            $client->delete([
                'index' => $index,
                'id' => (string) $id,
            ]);

            return true;
        } catch (\Throwable $e) {
            // Document might not exist in index, ignore safely
            return false;
        }
    }
}
