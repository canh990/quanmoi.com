<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hinh_anh_quan', function (Blueprint $table) {
            // Lưu R2 object key (e.g. quan/gallery/{quan_id}/uuid.webp)
            // Dùng để xóa file trên R2 khi chủ quán xóa ảnh
            $table->string('object_key')->nullable()->after('duong_dan');
        });
    }

    public function down(): void
    {
        Schema::table('hinh_anh_quan', function (Blueprint $table) {
            $table->dropColumn('object_key');
        });
    }
};
