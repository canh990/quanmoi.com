<?php

namespace Database\Seeders;

use App\Models\VaiTro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NguoiDungSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            VaiTroSeeder::class,
            QuyenHanSeeder::class,
        ]);

        $roleUser = DB::table('vai_tro')->where('ten', 'nguoi_dung')->first();
        $roleAdmin = DB::table('vai_tro')->where('ten', 'admin')->first();
        $roleChuQuan = DB::table('vai_tro')->where('ten', 'chu_quan')->first();

        // 1. Test User Account
        DB::table('nguoi_dung')->updateOrInsert(
            ['email' => 'user@quanmoi.com'],
            [
                'id' => Str::uuid()->toString(),
                'ho_ten' => 'Nguyễn Văn Minh',
                'mat_khau' => Hash::make('123456'),
                'anh_dai_dien' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0901234567',
                'dia_chi' => 'Quận 1, TP. Hồ Chí Minh',
                'vai_tro_id' => $roleUser ? $roleUser->id : null,
                'da_xac_thuc' => true,
                'ngay_xac_thuc' => now(),
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Admin Test Account
        DB::table('nguoi_dung')->updateOrInsert(
            ['email' => 'admin@quanmoi.com'],
            [
                'id' => Str::uuid()->toString(),
                'ho_ten' => 'Quản Trị Viên (Admin)',
                'mat_khau' => Hash::make('123456'),
                'anh_dai_dien' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=200&q=80',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0988888888',
                'dia_chi' => 'TP. Hồ Chí Minh',
                'vai_tro_id' => $roleAdmin ? $roleAdmin->id : null,
                'da_xac_thuc' => true,
                'ngay_xac_thuc' => now(),
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. Chu Quan Test Account
        DB::table('nguoi_dung')->updateOrInsert(
            ['email' => 'chuquan@quanmoi.com'],
            [
                'id' => Str::uuid()->toString(),
                'ho_ten' => 'Trần Văn Chủ Quán',
                'mat_khau' => Hash::make('123456'),
                'anh_dai_dien' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=200&q=80',
                'gioi_tinh' => 'nam',
                'so_dien_thoai' => '0912345678',
                'dia_chi' => 'Quận 3, TP. Hồ Chí Minh',
                'vai_tro_id' => $roleChuQuan ? $roleChuQuan->id : null,
                'da_xac_thuc' => true,
                'ngay_xac_thuc' => now(),
                'trang_thai' => 'hoat_dong',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
