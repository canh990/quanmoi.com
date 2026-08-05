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
        Schema::create('video_shorts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('quan_id')->nullable()->constrained('quan')->onDelete('set null');
            $table->string('tieu_de');
            $table->string('video_id')->comment('TikTok Video ID');
            $table->string('video_url')->nullable()->comment('Full URL to video if needed');
            $table->string('thumbnail_url')->nullable();
            $table->string('nguoi_dang');
            $table->string('avatar_nguoi_dang')->nullable();
            $table->integer('luot_xem')->default(0);
            $table->integer('luot_thich')->default(0);
            $table->string('trang_thai')->default('da_duyet');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_shorts');
    }
};
