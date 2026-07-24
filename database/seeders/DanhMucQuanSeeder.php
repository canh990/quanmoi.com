<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DanhMucQuanSeeder extends Seeder
{
    public function run(): void
    {
        $quan = DB::table('quan')->first();
        if (!$quan) return;

        $categories = ['Món chính', 'Món ăn kèm', 'Nước uống', 'Tráng miệng'];

        foreach ($categories as $index => $catName) {
            $catId = Str::uuid()->toString();
            DB::table('danh_muc_menu')->insert([
                'id' => $catId,
                'quan_id' => $quan->id,
                'ten_danh_muc' => $catName,
                'thu_tu' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($catName === 'Món chính') {
                DB::table('mon_trong_menu')->insert([
                    [
                        'id' => Str::uuid()->toString(),
                        'danh_muc_id' => $catId,
                        'ten_mon' => 'Phở Bò Tái Lăn Đặc Biệt',
                        'mo_ta' => 'Bò tươi tái lăn xào tỏi dậy mùi thơm nức, bánh phở mềm và nước dùng xương hầm 12 tiếng',
                        'gia' => 65000,
                        'hinh_anh' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=400&q=80',
                        'con_hang' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                        'danh_muc_id' => $catId,
                        'ten_mon' => 'Phở Bò Nạm Gầu',
                        'mo_ta' => 'Nạm gầu giòn sần sật béo ngậy kèm quẩy giòn tôm',
                        'gia' => 55000,
                        'hinh_anh' => 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?auto=format&fit=crop&w=400&q=80',
                        'con_hang' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]);
            }
        }
    }
}
