<?php

namespace Database\Seeders;

use App\Models\Quan;
use App\Models\QuanDanhGia;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuanDanhGiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $quans = Quan::all();

        if ($users->isEmpty() || $quans->isEmpty()) {
            return;
        }

        $comments = [
            'Quán ăn rất ngon, không gian ấm cúng và sạch sẽ. Chắc chắn sẽ quay lại!',
            'Đồ ăn tươi ngon, phục vụ nhiệt tình nhanh nhẹn, giá cả rất hợp lý.',
            'Món ăn chuẩn vị, decor quán đẹp lung linh chụp ảnh check-in cực mê.',
            'Quán ruột của mình mỗi khi tụ tập bạn bè cuối tuần, 10 điểm không có nhưng!',
            'Rất hài lòng về chất lượng đồ ăn và thái độ chu đáo của các bạn nhân viên.',
            'Hương vị đậm đà, bài trí món đẹp mắt, gia đình mình ai cũng khen ngon.',
            'Giá cả phải chăng, khẩu phần đầy đặn, đồ uống cũng ngon xuất sắc.',
            'Không gian thoáng mát, view đẹp, đồ ăn lên nhanh và nóng hổi.'
        ];

        foreach ($quans as $quan) {
            // Chọn ngẫu nhiên 2 - 5 user để đánh giá mỗi quán
            $reviewers = $users->random(min($users->count(), rand(2, 5)));

            foreach ($reviewers as $user) {
                // Với quán nổi bật thì ưu tiên sao 4-5
                $soSao = $quan->is_noi_bat ? rand(4, 5) : rand(3, 5);

                QuanDanhGia::updateOrCreate(
                    [
                        'quan_id' => $quan->id,
                        'nguoi_dung_id' => $user->id,
                    ],
                    [
                        'so_sao' => $soSao,
                        'binh_luan' => $comments[array_rand($comments)],
                    ]
                );
            }
        }
    }
}
