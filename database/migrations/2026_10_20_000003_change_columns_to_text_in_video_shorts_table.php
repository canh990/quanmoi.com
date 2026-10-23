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
        Schema::table('video_shorts', function (Blueprint $table) {
            $table->text('tieu_de')->change();
            $table->text('video_url')->nullable()->change();
            $table->text('thumbnail_url')->nullable()->change();
            $table->text('avatar_nguoi_dang')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_shorts', function (Blueprint $table) {
            $table->string('tieu_de', 255)->change();
            $table->string('video_url', 255)->nullable()->change();
            $table->string('thumbnail_url', 255)->nullable()->change();
            $table->string('avatar_nguoi_dang', 255)->nullable()->change();
        });
    }
};
