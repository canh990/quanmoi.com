<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quan_danh_gia', function (Blueprint $table) {
            $table->id();
            $table->uuid('quan_id');
            $table->uuid('nguoi_dung_id');
            $table->unsignedTinyInteger('so_sao');
            $table->text('binh_luan');
            $table->timestamps();

            $table->foreign('quan_id')->references('id')->on('quan')->cascadeOnDelete();
            $table->foreign('nguoi_dung_id')->references('id')->on('nguoi_dung')->cascadeOnDelete();
            $table->unique(['quan_id', 'nguoi_dung_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quan_danh_gia');
    }
};
