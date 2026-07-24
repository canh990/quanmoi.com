<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('mat_khau');
            $table->string('anh_dai_dien')->nullable();
            $table->enum('gioi_tinh', ['nam', 'nữ', 'khác'])->default('nam');
            $table->date('ngay_sinh')->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->string('dia_chi')->nullable();
            $table->uuid('vai_tro_id')->nullable();
            $table->boolean('da_xac_thuc')->default(false);
            $table->timestamp('ngay_xac_thuc')->nullable();
            $table->enum('trang_thai', ['hoat_dong', 'bi_khoa'])->default('hoat_dong');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('vai_tro_id')->references('id')->on('vai_tro')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
