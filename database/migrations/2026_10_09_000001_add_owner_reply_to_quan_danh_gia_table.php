<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quan_danh_gia', function (Blueprint $table) {
            $table->text('phan_hoi')->nullable()->after('binh_luan');
            $table->timestamp('phan_hoi_luc')->nullable()->after('phan_hoi');
        });
    }

    public function down(): void
    {
        Schema::table('quan_danh_gia', function (Blueprint $table) {
            $table->dropColumn(['phan_hoi', 'phan_hoi_luc']);
        });
    }
};
