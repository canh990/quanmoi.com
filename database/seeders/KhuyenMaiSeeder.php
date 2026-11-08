<?php

namespace Database\Seeders;

use App\Models\KhuyenMai;
use App\Models\Quan;
use Illuminate\Database\Seeder;

class KhuyenMaiSeeder extends Seeder
{
    public function run(): void
    {
        $quans = Quan::where('trang_thai', 'da_duyet')->take(5)->get();
        if ($quans->isEmpty()) {
            $quans = Quan::take(5)->get();
        }

        if ($quans->isEmpty()) {
            return;
        }

        $promotions = [
            [
                'tieu_de' => 'Đại tiệc ẩm thực - Giảm 20% toàn bộ thực đơn',
                'ma_voucher' => 'QUANMOI20',
                'loai_khuyen_mai' => 'giam_phan_tram',
                'gia_tri_giam' => '20%',
                'slogan' => 'Ăn ngon thả ga, không lo về giá!',
                'noi_dung' => 'Ưu đãi cực sốc dành riêng cho tín đồ ẩm thực. Áp dụng cho mọi hóa đơn khi đặt bàn hoặc gọi món trực tiếp.',
                'the_le' => 'Mỗi khách hàng được áp dụng 1 lần trong ngày. Không áp dụng đồng thời với các chương trình khuyến mãi khác.',
                'ngay_bat_dau' => now()->toDateString(),
                'ngay_ket_thuc' => now()->addDays(30)->toDateString(),
                'mon_ap_dung' => 'Toàn bộ menu',
                'trang_thai' => true,
                'ai_provider' => 'kira_ai',
            ],
            [
                'tieu_de' => 'Mua 1 Tặng 1 Đồ Uống Mùa Hè',
                'ma_voucher' => 'MUA1TANG1',
                'loai_khuyen_mai' => 'tang_mon',
                'gia_tri_giam' => 'Mua 1 Tặng 1',
                'slogan' => 'Giải nhiệt sảng khoái cùng bạn bè',
                'noi_dung' => 'Mua 1 ly nước bất kỳ được tặng ngay 1 ly trà hoa quả hoặc trà sữa cùng kích cỡ.',
                'the_le' => 'Áp dụng cho khung giờ vàng từ 13h00 đến 17h00 từ Thứ 2 đến Thứ 6.',
                'ngay_bat_dau' => now()->toDateString(),
                'ngay_ket_thuc' => now()->addDays(45)->toDateString(),
                'mon_ap_dung' => 'Trà đào, Trà sữa, Nước ép',
                'trang_thai' => true,
                'ai_provider' => 'kira_ai',
            ],
            [
                'tieu_de' => 'Voucher 50K cho đơn từ 200K',
                'ma_voucher' => 'GIAM50K',
                'loai_khuyen_mai' => 'giam_tien',
                'gia_tri_giam' => '50.000đ',
                'slogan' => 'Tiết kiệm thêm, niềm vui nhân đôi!',
                'noi_dung' => 'Giảm trực tiếp 50.000 VNĐ vào tổng hóa đơn thanh toán cho nhóm khách từ 2 người trở lên.',
                'the_le' => 'Áp dụng cho hóa đơn có giá trị từ 200.000 VNĐ trở lên.',
                'ngay_bat_dau' => now()->toDateString(),
                'ngay_ket_thuc' => now()->addDays(20)->toDateString(),
                'mon_ap_dung' => 'Món chính, Lẩu & Nướng',
                'trang_thai' => true,
                'ai_provider' => 'kira_ai',
            ],
            [
                'tieu_de' => 'Freeship Đơn Hàng Giao Tận Nơi',
                'ma_voucher' => 'FREESHIP',
                'loai_khuyen_mai' => 'giam_tien',
                'gia_tri_giam' => 'Miễn phí giao hàng',
                'slogan' => 'Giao hàng tận cửa, chuẩn vị thơm ngon',
                'noi_dung' => 'Miễn phí giao hàng trong bán kính 5km khi đặt món online qua hệ thống Quán Mới.',
                'the_le' => 'Áp dụng cho đơn hàng từ 150.000đ.',
                'ngay_bat_dau' => now()->toDateString(),
                'ngay_ket_thuc' => now()->addDays(60)->toDateString(),
                'mon_ap_dung' => 'Toàn bộ menu',
                'trang_thai' => true,
                'ai_provider' => 'kira_ai',
            ],
        ];

        foreach ($promotions as $index => $data) {
            $quan = $quans[$index % $quans->count()];
            $data['quan_id'] = $quan->id;
            KhuyenMai::create($data);
        }
    }
}

