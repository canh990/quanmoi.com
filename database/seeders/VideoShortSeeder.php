<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VideoShortSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quans = \App\Models\Quan::inRandomOrder()->take(3)->get();
        if ($quans->isEmpty()) {
            return;
        }

        $videos = [
            [
                'quan_id' => $quans[0]->id ?? null,
                'tieu_de' => 'Quán Phở ngon nhất Hà Nội đây rồi!',
                'video_id' => '6862153058223197445', // Real video ID (Bella Poarch)
                'video_url' => 'https://www.tiktok.com/@bellapoarch/video/6862153058223197445',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=600&q=80',
                'nguoi_dang' => 'Thánh Review',
                'avatar_nguoi_dang' => 'https://i.pravatar.cc/150?u=1',
                'luot_xem' => 15000,
                'luot_thich' => 1200,
                'trang_thai' => 'da_duyet',
            ],
            [
                'quan_id' => $quans[1]->id ?? ($quans[0]->id ?? null),
                'tieu_de' => 'Trải nghiệm Bún Chả Hương Liên - Quán ruột Obama',
                'video_id' => '6768504823336803589', // Real video ID (Zach King)
                'video_url' => 'https://www.tiktok.com/@zachking/video/6768504823336803589',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
                'nguoi_dang' => 'Cô Gái Mở Đường',
                'avatar_nguoi_dang' => 'https://i.pravatar.cc/150?u=2',
                'luot_xem' => 25000,
                'luot_thich' => 3400,
                'trang_thai' => 'da_duyet',
            ],
            [
                'quan_id' => $quans[2]->id ?? ($quans[0]->id ?? null),
                'tieu_de' => 'Khám phá quán Cafe Cổ giữa lòng Sài Gòn',
                'video_id' => '6965154388481494278', // Real video ID (Khaby Lame)
                'video_url' => 'https://www.tiktok.com/@khaby.lame/video/6965154388481494278',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80',
                'nguoi_dang' => 'Anh Ba Báo',
                'avatar_nguoi_dang' => 'https://i.pravatar.cc/150?u=3',
                'luot_xem' => 8000,
                'luot_thich' => 500,
                'trang_thai' => 'da_duyet',
            ]
        ];

        foreach ($videos as $video) {
            \App\Models\VideoShort::create($video);
        }
    }
}
