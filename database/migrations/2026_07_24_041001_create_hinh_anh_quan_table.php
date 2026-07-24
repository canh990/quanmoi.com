<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hinh_anh_quan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quan_id');
            $table->string('duong_dan');
            $table->string('tieu_de')->nullable();
            $table->timestamps();

            $table->foreign('quan_id')->references('id')->on('quan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hinh_anh_quan');
    }
};
