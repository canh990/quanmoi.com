<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\DanhMucQuan;
use App\Models\KhuyenMai;
use App\Models\Quan;
use App\Models\User;
use App\Models\VideoShort;
use App\Services\ElasticAdminSearchService;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class SetupElasticsearchIndexes extends Command
{
    protected $signature = 'elastic:setup-indices {--rebuild : Xóa index cũ và tạo mới}';
    protected $description = 'Khởi tạo mapping chuyên sâu (Vietnamese Compact N-Gram, Edge N-Gram & Fuzzy Search) cho toàn bộ chức năng trong Admin';

    public function handle(): int
    {
        $host = config('elastic.client.connections.default.hosts.0') ?: env('ELASTIC_HOST', 'http://elasticsearch:9200');
        $client = ClientBuilder::create()->setHosts([$host])->build();

        $analysisConfig = [
            'char_filter' => [
                'strip_space_punct' => [
                    'type' => 'pattern_replace',
                    'pattern' => '[\\s\\p{Punct}]+',
                    'replacement' => '',
                ],
            ],
            'filter' => [
                'vietnamese_folding' => [
                    'type' => 'asciifolding',
                    'preserve_original' => true,
                ],
                'full_ngram_filter' => [
                    'type' => 'ngram',
                    'min_gram' => 2,
                    'max_gram' => 20,
                ],
                'edge_ngram_filter' => [
                    'type' => 'edge_ngram',
                    'min_gram' => 1,
                    'max_gram' => 20,
                ],
            ],
            'analyzer' => [
                'vietnamese_analyzer' => [
                    'tokenizer' => 'standard',
                    'filter' => [
                        'lowercase',
                        'vietnamese_folding',
                    ],
                ],
                'vietnamese_compact_ngram' => [
                    'tokenizer' => 'standard',
                    'char_filter' => ['strip_space_punct'],
                    'filter' => [
                        'lowercase',
                        'vietnamese_folding',
                        'full_ngram_filter',
                    ],
                ],
                'vietnamese_compact_search' => [
                    'tokenizer' => 'standard',
                    'char_filter' => ['strip_space_punct'],
                    'filter' => [
                        'lowercase',
                        'vietnamese_folding',
                    ],
                ],
                'vietnamese_edge_ngram' => [
                    'tokenizer' => 'standard',
                    'filter' => [
                        'lowercase',
                        'vietnamese_folding',
                        'edge_ngram_filter',
                    ],
                ],
            ],
        ];

        $indices = [
            'quan' => [
                'properties' => [
                    'id' => ['type' => 'keyword'],
                    'ten_quan' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'prefix' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                        ],
                    ],
                    'mon_an' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'prefix' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                        ],
                    ],
                    'loai_hinh_kinh_doanh' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'mo_ta' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                        ],
                    ],
                    'dia_chi' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                        ],
                    ],
                    'chu_quan' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                        ],
                    ],
                    'so_dien_thoai' => [
                        'type' => 'keyword',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                        ],
                    ],
                    'email' => [
                        'type' => 'keyword',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                        ],
                    ],
                    'location' => ['type' => 'geo_point'],
                    'tinh_thanh_id' => ['type' => 'keyword'],
                    'quan_huyen_id' => ['type' => 'keyword'],
                    'phuong_xa_id' => ['type' => 'keyword'],
                    'gia_nho_nhat' => ['type' => 'double'],
                    'gia_lon_nhat' => ['type' => 'double'],
                    'gio_mo_cua' => ['type' => 'keyword'],
                    'gio_dong_cua' => ['type' => 'keyword'],
                    'luot_xem' => ['type' => 'long'],
                    'trang_thai' => ['type' => 'keyword'],
                    'is_noi_bat' => ['type' => 'boolean'],
                    'is_xac_thuc' => ['type' => 'boolean'],
                    'co_shopeefood' => ['type' => 'boolean'],
                    'created_at' => ['type' => 'long'],
                    'updated_at' => ['type' => 'long'],
                ],
            ],
            'users' => [
                'properties' => [
                    'id' => ['type' => 'keyword'],
                    'ho_ten' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'prefix' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                        ],
                    ],
                    'email' => [
                        'type' => 'text',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                        ],
                    ],
                    'so_dien_thoai' => [
                        'type' => 'keyword',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                        ],
                    ],
                    'trang_thai' => ['type' => 'keyword'],
                    'vai_tro_id' => ['type' => 'keyword'],
                    'da_xac_thuc' => ['type' => 'boolean'],
                    'created_at' => ['type' => 'long'],
                    'updated_at' => ['type' => 'long'],
                ],
            ],
            'blogs' => [
                'properties' => [
                    'id' => ['type' => 'keyword'],
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'prefix' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                        ],
                    ],
                    'slug' => ['type' => 'keyword'],
                    'excerpt' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                        ],
                    ],
                    'content' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                        ],
                    ],
                    'status' => ['type' => 'keyword'],
                    'author_name' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'category_id' => ['type' => 'keyword'],
                    'category_name' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'created_at' => ['type' => 'long'],
                    'updated_at' => ['type' => 'long'],
                ],
            ],
            'danh_muc_quan' => [
                'properties' => [
                    'id' => ['type' => 'keyword'],
                    'ten_danh_muc' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'slug' => [
                        'type' => 'keyword',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                        ],
                    ],
                    'mo_ta' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                        ],
                    ],
                    'icon' => ['type' => 'keyword'],
                    'mau_sac' => ['type' => 'keyword'],
                    'is_hien_thi' => ['type' => 'boolean'],
                    'is_noi_bat' => ['type' => 'boolean'],
                    'thu_tu' => ['type' => 'integer'],
                    'created_at' => ['type' => 'long'],
                    'updated_at' => ['type' => 'long'],
                ],
            ],
            'video_shorts' => [
                'properties' => [
                    'id' => ['type' => 'keyword'],
                    'tieu_de' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'prefix' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                        ],
                    ],
                    'video_id' => [
                        'type' => 'keyword',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                        ],
                    ],
                    'nguoi_dang' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'ten_quan' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                        ],
                    ],
                    'trang_thai' => ['type' => 'keyword'],
                    'created_at' => ['type' => 'long'],
                    'updated_at' => ['type' => 'long'],
                ],
            ],
            'khuyen_mai' => [
                'properties' => [
                    'id' => ['type' => 'keyword'],
                    'ma_voucher' => [
                        'type' => 'text',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'tieu_de' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'prefix' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                            'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                        ],
                    ],
                    'slogan' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                        ],
                    ],
                    'noi_dung' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                        ],
                    ],
                    'ten_quan' => [
                        'type' => 'text',
                        'analyzer' => 'vietnamese_analyzer',
                        'fields' => [
                            'compact' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_compact_ngram',
                                'search_analyzer' => 'vietnamese_compact_search',
                            ],
                            'ngram' => [
                                'type' => 'text',
                                'analyzer' => 'vietnamese_edge_ngram',
                                'search_analyzer' => 'vietnamese_analyzer',
                            ],
                        ],
                    ],
                    'trang_thai' => ['type' => 'keyword'],
                    'created_at' => ['type' => 'long'],
                    'updated_at' => ['type' => 'long'],
                ],
            ],
        ];

        foreach ($indices as $indexName => $def) {
            $exists = $client->indices()->exists(['index' => $indexName])->asBool();
            if ($exists) {
                if ($this->option('rebuild')) {
                    $this->warn("Đang xóa index cũ [{$indexName}]...");
                    $client->indices()->delete(['index' => $indexName]);
                } else {
                    $this->info("Index [{$indexName}] đã tồn tại.");
                    continue;
                }
            }

            $this->info("Đang tạo index [{$indexName}] với Vietnamese Compact & N-Gram Analyzer...");
            $client->indices()->create([
                'index' => $indexName,
                'body' => [
                    'settings' => [
                        'number_of_replicas' => 0,
                        'index.max_ngram_diff' => 20,
                        'analysis' => $analysisConfig,
                    ],
                    'mappings' => $def,
                ],
            ]);
        }

        // Đồng bộ dữ liệu hiện có trong Database vào Elasticsearch
        $searchService = app(ElasticAdminSearchService::class);

        $this->info("Đang nạp toàn bộ Quán vào Elasticsearch...");
        $quanCount = 0;
        Quan::withTrashed()->chunk(100, function ($quans) use ($searchService, &$quanCount) {
            foreach ($quans as $q) {
                $searchService->syncQuan($q);
                $quanCount++;
            }
        });
        $this->info("✓ Đã nạp {$quanCount} quán.");

        $this->info("Đang nạp toàn bộ Người dùng vào Elasticsearch...");
        $userCount = 0;
        User::withTrashed()->chunk(100, function ($users) use ($searchService, &$userCount) {
            foreach ($users as $u) {
                $searchService->syncUser($u);
                $userCount++;
            }
        });
        $this->info("✓ Đã nạp {$userCount} người dùng.");

        $this->info("Đang nạp toàn bộ Blog vào Elasticsearch...");
        $blogCount = 0;
        Blog::withTrashed()->chunk(100, function ($blogs) use ($searchService, &$blogCount) {
            foreach ($blogs as $b) {
                $searchService->syncBlog($b);
                $blogCount++;
            }
        });
        $this->info("✓ Đã nạp {$blogCount} bài viết.");

        $this->info("Đang nạp Danh mục vào Elasticsearch...");
        $dmCount = 0;
        foreach (DanhMucQuan::all() as $dm) {
            $searchService->syncDanhMuc($dm);
            $dmCount++;
        }
        $this->info("✓ Đã nạp {$dmCount} danh mục.");

        $this->info("Đang nạp Video Shorts vào Elasticsearch...");
        $videoCount = 0;
        foreach (VideoShort::all() as $video) {
            $searchService->syncVideo($video);
            $videoCount++;
        }
        $this->info("✓ Đã nạp {$videoCount} video.");

        $this->info("Đang nạp Khuyến mãi vào Elasticsearch...");
        $kmCount = 0;
        foreach (KhuyenMai::all() as $km) {
            $searchService->syncKhuyenMai($km);
            $kmCount++;
        }
        $this->info("✓ Đã nạp {$kmCount} khuyến mãi.");

        $this->info("🎉 TOÀN BỘ DỮ LIỆU ĐÃ ĐƯỢC ĐỒNG BỘ VÀO ELASTICSEARCH THÀNH CÔNG VỚI FULL N-GRAM & COMPACT MATCHING!");

        return self::SUCCESS;
    }
}
