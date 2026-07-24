<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quan', function (Blueprint $table) {
            $table->string('facebook_url')->nullable()->after('email');
            $table->string('website_url')->nullable()->after('facebook_url');
            $table->string('shopee_food_url')->nullable()->after('website_url');
            $table->json('tien_ich')->nullable()->after('gia_lon_nhat');
            $table->string('anh_dai_dien')->nullable()->after('anh_bia');
            $table->boolean('la_nhap')->default(false)->after('trang_thai');
            $table->string('meta_title')->nullable()->after('la_nhap');
            $table->text('meta_description')->nullable()->after('meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('quan', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'website_url',
                'shopee_food_url',
                'tien_ich',
                'anh_dai_dien',
                'la_nhap',
                'meta_title',
                'meta_description',
            ]);
        });
    }
};
