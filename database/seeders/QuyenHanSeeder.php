<?php

namespace Database\Seeders;

use App\Models\QuyenHan;
use App\Models\VaiTro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuyenHanSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['ten' => 'quan_ly_nguoi_dung', 'mo_ta' => 'Quản lý danh sách, chỉnh sửa và xóa người dùng'],
            ['ten' => 'quan_ly_quan', 'mo_ta' => 'Quản lý danh sách, duyệt bài và khóa quán'],
        ];

        foreach ($permissions as $permData) {
            QuyenHan::firstOrCreate(['ten' => $permData['ten']], $permData);
        }

        // Attach all permissions to admin role
        $adminRole = VaiTro::where('ten', 'admin')->first();
        if ($adminRole) {
            $allPerms = QuyenHan::all();
            foreach ($allPerms as $perm) {
                DB::table('vai_tro_quyen_han')->updateOrInsert(
                    ['vai_tro_id' => $adminRole->id, 'quyen_han_id' => $perm->id],
                    ['id' => Str::uuid()->toString(), 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
