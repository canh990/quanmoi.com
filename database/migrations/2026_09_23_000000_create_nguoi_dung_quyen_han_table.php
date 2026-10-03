<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nguoi_dung_quyen_han', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('nguoi_dung_id');
            $table->uuid('quyen_han_id');
            $table->boolean('cho_phep')->default(true)->comment('true: cho_phep, false: tu_choi');
            $table->timestamps();

            $table->foreign('nguoi_dung_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('quyen_han_id')->references('id')->on('quyen_han')->onDelete('cascade');
            $table->unique(['nguoi_dung_id', 'quyen_han_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung_quyen_han');
    }
};
