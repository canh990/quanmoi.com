<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Chèn quyền quan_ly_video_tiktok
        $perm = DB::table('quyen_han')->where('ten', 'quan_ly_video_tiktok')->first();
        if (! $perm) {
            $permId = Str::uuid()->toString();
            DB::table('quyen_han')->insert([
                'id' => $permId,
                'ten' => 'quan_ly_video_tiktok',
                'mo_ta' => 'Quản lý, duyệt và đăng video TikTok/Shorts review quán',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $permId = $perm->id;
        }

        // 2. Tự động gán quyền này cho vai trò admin
        $adminRole = DB::table('vai_tro')->where('ten', 'admin')->first();
        if ($adminRole && $permId) {
            $exists = DB::table('vai_tro_quyen_han')
                ->where('vai_tro_id', $adminRole->id)
                ->where('quyen_han_id', $permId)
                ->exists();

            if (! $exists) {
                DB::table('vai_tro_quyen_han')->insert([
                    'id' => Str::uuid()->toString(),
                    'vai_tro_id' => $adminRole->id,
                    'quyen_han_id' => $permId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $perm = DB::table('quyen_han')->where('ten', 'quan_ly_video_tiktok')->first();
        if ($perm) {
            DB::table('vai_tro_quyen_han')->where('quyen_han_id', $perm->id)->delete();
            DB::table('quyen_han')->where('id', $perm->id)->delete();
        }
    }
};
