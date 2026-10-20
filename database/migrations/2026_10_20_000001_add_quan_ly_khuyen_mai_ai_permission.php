<?php

use App\Models\QuyenHan;
use App\Models\VaiTro;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $perm = QuyenHan::firstOrCreate(
            ['ten' => 'quan_ly_khuyen_mai_ai'],
            ['mo_ta' => 'Sử dụng Kira AI tạo khuyến mãi, voucher và quản lý ưu đãi quán']
        );

        $adminRole = VaiTro::where('ten', 'admin')->first();
        if ($adminRole && $perm) {
            DB::table('vai_tro_quyen_han')->updateOrInsert(
                ['vai_tro_id' => $adminRole->id, 'quyen_han_id' => $perm->id],
                ['id' => Str::uuid()->toString(), 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        $perm = QuyenHan::where('ten', 'quan_ly_khuyen_mai_ai')->first();
        if ($perm) {
            DB::table('vai_tro_quyen_han')->where('quyen_han_id', $perm->id)->delete();
            $perm->delete();
        }
    }
};
