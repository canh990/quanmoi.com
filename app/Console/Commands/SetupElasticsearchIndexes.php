<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class SetupElasticsearchIndexes extends Command
{
    protected $signature = 'elastic:setup-indices {--rebuild : Xóa index cũ và tạo mới}';
    protected $description = 'Khởi tạo mapping chuyên sâu (Vietnamese Analyzer & Geo Point) cho Elasticsearch';

    public function handle(): int
    {
        $client = ClientBuilder::create()
            ->setHosts([config('elastic.client.connections.default.hosts.0', config('elastic.client.hosts.0', 'http://elasticsearch:9200'))])
            ->build();

        $analysis = [
            'analysis' => [
                'filter' => [
                    'vietnamese_folding' => [
                        'type' => 'asciifolding',
                        'preserve_original' => true,
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
                ],
            ],
        ];

        $indexes = [
            'quan' => [
                'id' => ['type' => 'keyword'],
                'ten_quan' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                    'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                ],
                'mon_an' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                    'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                ],
                'loai_hinh_kinh_doanh' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                    'fields' => ['keyword' => ['type' => 'keyword']],
                ],
                'mo_ta' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                ],
                'dia_chi' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
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
                'diem_danh_gia' => ['type' => 'double'],
                'trang_thai' => ['type' => 'keyword'],
                'is_noi_bat' => ['type' => 'boolean'],
                'is_xac_thuc' => ['type' => 'boolean'],
                'co_shopeefood' => ['type' => 'boolean'],
                'duoi_50k' => ['type' => 'boolean'],
                'dang_mo_cua' => ['type' => 'boolean'],
                'co_video_review' => ['type' => 'boolean'],
                'created_at' => ['type' => 'long'],
                'updated_at' => ['type' => 'long'],
            ],
            'video_shorts' => [
                'id' => ['type' => 'keyword'],
                'quan_id' => ['type' => 'keyword'],
                'tieu_de' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                    'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                ],
                'nguoi_dang' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                    'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                ],
                'video_id' => [
                    'type' => 'text',
                    'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                ],
                'ten_quan' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                    'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                ],
                'mon_an' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                ],
                'ten_quan_huyen' => ['type' => 'keyword'],
                'loai_hinh_kinh_doanh' => ['type' => 'keyword'],
                'trang_thai' => ['type' => 'keyword'],
                'luot_xem' => ['type' => 'long'],
                'luot_thich' => ['type' => 'long'],
                'created_at' => ['type' => 'long'],
            ],
            'khuyen_mai' => [
                'id' => ['type' => 'keyword'],
                'quan_id' => ['type' => 'keyword'],
                'tieu_de' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                    'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                ],
                'ma_voucher' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                    'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 100]],
                ],
                'ten_quan' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                    'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                ],
                'mon_ap_dung' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                ],
                'slogan' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                ],
                'noi_dung' => [
                    'type' => 'text',
                    'analyzer' => 'vietnamese_analyzer',
                ],
                'loai_khuyen_mai' => ['type' => 'keyword'],
                'gia_tri_giam' => ['type' => 'keyword'],
                'trang_thai_duyet' => ['type' => 'keyword'],
                'trang_thai' => ['type' => 'boolean'],
                'created_at' => ['type' => 'long'],
            ],
        ];

        foreach ($indexes as $index => $properties) {
            $this->ensureIndex($client, $index, $analysis, $properties);
        }

        return Command::SUCCESS;
    }

    protected function ensureIndex($client, string $index, array $analysis, array $properties): void
    {
        $exists = $client->indices()->exists(['index' => $index])->asBool();

        if ($exists) {
            if ($this->option('rebuild')) {
                $this->warn("Đang xóa index cũ [{$index}]...");
                $client->indices()->delete(['index' => $index]);
            } else {
                $this->info("Index [{$index}] đã tồn tại. Dùng --rebuild để xóa và cấu hình lại.");
                return;
            }
        }

        $this->info("Đang tạo index [{$index}] với cấu hình Vietnamese Analyzer...");

        $client->indices()->create([
            'index' => $index,
            'body' => [
                'settings' => $analysis,
                'mappings' => [
                    'properties' => $properties,
                ],
            ],
        ]);

        $this->info("Khởi tạo index [{$index}] thành công!");
    }
}
