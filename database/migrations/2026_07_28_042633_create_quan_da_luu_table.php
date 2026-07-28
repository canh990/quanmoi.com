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
        Schema::create('quan_da_luu', function (Blueprint $table) {
            $table->id();
            $table->uuid('nguoi_dung_id');
            $table->uuid('quan_id');
            $table->timestamps();

            $table->foreign('nguoi_dung_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('quan_id')->references('id')->on('quan')->onDelete('cascade');
            $table->unique(['nguoi_dung_id', 'quan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quan_da_luu');
    }
};
