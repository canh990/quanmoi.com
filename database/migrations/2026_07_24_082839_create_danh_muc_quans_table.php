<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('danh_muc_quans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ten_danh_muc');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::table('quan', function (Blueprint $table) {
            $table->uuid('danh_muc_id')->nullable()->after('ten_quan');
            $table->foreign('danh_muc_id')->references('id')->on('danh_muc_quans')->onDelete('set null');

            // Add indexes for geolocation search & address search
            $table->index(['vi_do', 'kinh_do']);
            $table->index('tinh_thanh_id');
            $table->index('quan_huyen_id');
            $table->index('phuong_xa_id');
        });
    }

    public function down(): void
    {
        Schema::table('quan', function (Blueprint $table) {
            $table->dropForeign(['danh_muc_id']);
            $table->dropColumn('danh_muc_id');
            
            $table->dropIndex(['vi_do', 'kinh_do']);
            $table->dropIndex(['tinh_thanh_id']);
            $table->dropIndex(['quan_huyen_id']);
            $table->dropIndex(['phuong_xa_id']);
        });

        Schema::dropIfExists('danh_muc_quans');
    }
};
