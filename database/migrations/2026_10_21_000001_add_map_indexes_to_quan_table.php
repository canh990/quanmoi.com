<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bổ sung B-Tree Composite Index cho bảng `quan` phục vụ truy vấn Bounding Box trên bản đồ.
     */
    public function up(): void
    {
        if (! Schema::hasTable('quan')) {
            return;
        }

        Schema::table('quan', function (Blueprint $table) {
            $table->index(['trang_thai', 'vi_do', 'kinh_do'], 'quan_map_bbox_idx');
            $table->index(['loai_hinh_kinh_doanh'], 'quan_loai_hinh_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('quan')) {
            return;
        }

        Schema::table('quan', function (Blueprint $table) {
            $table->dropIndex('quan_map_bbox_idx');
            $table->dropIndex('quan_loai_hinh_idx');
        });
    }
};
