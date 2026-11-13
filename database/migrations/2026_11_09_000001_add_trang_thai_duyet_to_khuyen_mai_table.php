<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('khuyen_mai', function (Blueprint $table) {
            if (! Schema::hasColumn('khuyen_mai', 'trang_thai_duyet')) {
                $table->string('trang_thai_duyet', 30)->default('da_duyet')->after('trang_thai');
            }
            if (! Schema::hasColumn('khuyen_mai', 'ly_do_tu_choi')) {
                $table->text('ly_do_tu_choi')->nullable()->after('trang_thai_duyet');
            }
            if (! Schema::hasColumn('khuyen_mai', 'nguoi_duyet_id')) {
                $table->string('nguoi_duyet_id', 36)->nullable()->after('ly_do_tu_choi');
            }
            if (! Schema::hasColumn('khuyen_mai', 'ngay_duyet')) {
                $table->timestamp('ngay_duyet')->nullable()->after('nguoi_duyet_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('khuyen_mai', function (Blueprint $table) {
            $columns = ['trang_thai_duyet', 'ly_do_tu_choi', 'nguoi_duyet_id', 'ngay_duyet'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('khuyen_mai', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

