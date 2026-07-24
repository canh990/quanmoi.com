<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danh_gia_quan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quan_id');
            $table->uuid('nguoi_dung_id');
            $table->unsignedTinyInteger('so_sao')->default(5);
            $table->text('noi_dung')->nullable();
            $table->timestamps();

            $table->foreign('quan_id')->references('id')->on('quan')->onDelete('cascade');
            $table->foreign('nguoi_dung_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->unique(['quan_id', 'nguoi_dung_id']);
        });

        Schema::create('quan_yeu_thich', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quan_id');
            $table->uuid('nguoi_dung_id');
            $table->timestamps();

            $table->foreign('quan_id')->references('id')->on('quan')->onDelete('cascade');
            $table->foreign('nguoi_dung_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->unique(['quan_id', 'nguoi_dung_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quan_yeu_thich');
        Schema::dropIfExists('danh_gia_quan');
    }
};
