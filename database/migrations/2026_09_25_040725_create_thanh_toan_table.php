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
        Schema::create('thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable();
            $table->uuid('quan_id')->nullable();
            $table->string('ma_giao_dich')->unique()->nullable();
            $table->string('ten_goi_dich_vu')->nullable();
            $table->decimal('so_tien', 15, 2)->default(0);
            $table->string('phuong_thuc')->nullable(); // vnpay, momo, bank_transfer, card
            $table->string('trang_thai')->default('thanh_cong'); // thanh_cong, cho_xu_ly, that_bai
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('nguoi_dung')->onDelete('set null');
            $table->foreign('quan_id')->references('id')->on('quan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanh_toan');
    }
};
