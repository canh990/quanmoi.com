<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danh_muc_menu', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quan_id');
            $table->string('ten_danh_muc');
            $table->integer('thu_tu')->default(0);
            $table->timestamps();

            $table->foreign('quan_id')->references('id')->on('quan')->onDelete('cascade');
        });

        Schema::create('mon_trong_menu', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('danh_muc_id');
            $table->string('ten_mon');
            $table->text('mo_ta')->nullable();
            $table->decimal('gia', 12, 2)->default(0);
            $table->string('hinh_anh')->nullable();
            $table->boolean('con_hang')->default(true);
            $table->timestamps();

            $table->foreign('danh_muc_id')->references('id')->on('danh_muc_menu')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mon_trong_menu');
        Schema::dropIfExists('danh_muc_menu');
    }
};
