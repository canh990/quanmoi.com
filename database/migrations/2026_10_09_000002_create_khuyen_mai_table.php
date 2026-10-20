<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('khuyen_mai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quan_id');
            $table->string('tieu_de');
            $table->string('ma_voucher')->nullable();
            $table->string('loai_khuyen_mai')->default('giam_phan_tram');
            $table->string('gia_tri_giam')->nullable();
            $table->string('slogan')->nullable();
            $table->text('noi_dung');
            $table->text('the_le')->nullable();
            $table->date('ngay_bat_dau')->nullable();
            $table->date('ngay_ket_thuc')->nullable();
            $table->string('mon_ap_dung')->nullable();
            $table->boolean('trang_thai')->default(true);
            $table->string('ai_provider')->default('kira_ai');
            $table->timestamps();

            $table->foreign('quan_id')->references('id')->on('quan')->onDelete('cascade');
            $table->index(['quan_id', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('khuyen_mai');
    }
};
