<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('nguoi_dung', 'ngay_xoa')) {
            Schema::table('nguoi_dung', function (Blueprint $table) {
                $table->softDeletes('ngay_xoa')->nullable();
            });
        }

        if (!Schema::hasColumn('quan', 'ngay_xoa')) {
            Schema::table('quan', function (Blueprint $table) {
                $table->softDeletes('ngay_xoa')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropSoftDeletes('ngay_xoa');
        });

        Schema::table('quan', function (Blueprint $table) {
            $table->dropSoftDeletes('ngay_xoa');
        });
    }
};
