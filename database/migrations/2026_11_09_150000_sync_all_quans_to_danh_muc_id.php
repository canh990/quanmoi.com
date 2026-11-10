<?php

use App\Models\DanhMucQuan;
use App\Models\Quan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categories = DanhMucQuan::all()->keyBy('slug');

        $mapping = [
            'nha-hang'           => ['Nhà hàng'],
            'ca-phe-tra'         => ['Cà phê & Trà', 'Cà phê'],
            'billiards-giai-tri' => ['Billiards & Giải trí', 'Billiards', 'Bida'],
            'do-an-vat'          => ['Đồ ăn vặt', 'Ăn vặt', 'Món ăn vặt'],
            'lau-nuong'          => ['Lẩu & Nướng', 'Lẩu', 'Nướng'],
            'quan-nhau'          => ['Quán nhậu & Bar', 'Quán nhậu', 'Quán Đêm 24/7', 'Bar', 'Pub'],
            'tra-sua-nuoc-ep'    => ['Trà sữa & Nước ép', 'Trà sữa', 'Nước ép'],
            'tiem-banh'          => ['Tiệm bánh', 'Bánh ngọt'],
            'quan-an-binh-dan'   => ['Quán ăn bình dân', 'Cơm bình dân', 'Bình dân'],
        ];

        foreach ($mapping as $slug => $types) {
            if (isset($categories[$slug])) {
                Quan::whereNull('danh_muc_id')
                    ->whereIn('loai_hinh_kinh_doanh', $types)
                    ->update(['danh_muc_id' => $categories[$slug]->id]);
            }
        }

        // Bổ sung khớp theo tên danh mục chính xác cho bất kỳ loại hình nào còn sót
        foreach ($categories as $cat) {
            Quan::whereNull('danh_muc_id')
                ->where('loai_hinh_kinh_doanh', $cat->ten_danh_muc)
                ->update(['danh_muc_id' => $cat->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // An toàn: không gỡ liên kết danh mục khi rollback
    }
};

