<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chu_quan_id');
            $table->string('ten_quan');
            $table->string('slug')->unique();
            $table->text('mo_ta')->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->string('email')->nullable();
            $table->string('dia_chi_chi_tiet');
            $table->string('tinh_thanh_id')->nullable();
            $table->string('ten_tinh_thanh')->nullable();
            $table->string('quan_huyen_id')->nullable();
            $table->string('ten_quan_huyen')->nullable();
            $table->string('phuong_xa_id')->nullable();
            $table->string('ten_phuong_xa')->nullable();
            $table->decimal('kinh_do', 10, 7)->nullable();
            $table->decimal('vi_do', 10, 7)->nullable();
            $table->string('gio_mo_cua')->default('08:00');
            $table->string('gio_dong_cua')->default('22:00');
            $table->decimal('gia_nho_nhat', 12, 2)->default(0);
            $table->decimal('gia_lon_nhat', 12, 2)->default(0);
            $table->string('anh_bia')->nullable();
            $table->enum('trang_thai', ['chua_duyet', 'da_duyet', 'bi_khoa'])->default('da_duyet');
            $table->timestamps();

            $table->foreign('chu_quan_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quan');
    }
};
