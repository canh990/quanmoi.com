<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('trang')->unique(); // slug: home, kham-pha, blog, ...
            $table->string('ten_trang');        // Display name: "Trang Chủ"
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
        });

        // Seed default pages
        DB::table('seo_settings')->insert([
            ['trang' => 'home', 'ten_trang' => 'Trang Chủ', 'meta_title' => 'Quán Mới - Khám phá ẩm thực', 'meta_description' => 'Tìm kiếm và khám phá các nhà hàng, quán ăn, cà phê tuyệt vời gần bạn.', 'meta_keywords' => 'quán ăn, nhà hàng, cà phê, ẩm thực', 'og_title' => null, 'og_description' => null, 'og_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['trang' => 'kham-pha', 'ten_trang' => 'Khám Phá', 'meta_title' => 'Khám Phá Địa Điểm Ăn Uống - Quán Mới', 'meta_description' => 'Khám phá hàng nghìn địa điểm ăn uống, nhà hàng và quán cà phê được đánh giá cao.', 'meta_keywords' => 'khám phá quán ăn, địa điểm ẩm thực, nhà hàng ngon', 'og_title' => null, 'og_description' => null, 'og_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['trang' => 'blog', 'ten_trang' => 'Blog Ẩm Thực', 'meta_title' => 'Blog Ẩm Thực - Quán Mới', 'meta_description' => 'Đọc các bài viết review nhà hàng, quán ăn và các địa điểm ẩm thực hấp dẫn.', 'meta_keywords' => 'blog ẩm thực, review nhà hàng, quán ăn ngon', 'og_title' => null, 'og_description' => null, 'og_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['trang' => 'quan-noi-bat', 'ten_trang' => 'Quán Nổi Bật', 'meta_title' => 'Quán Ăn Nổi Bật - Quán Mới', 'meta_description' => 'Những địa điểm ăn uống được cộng đồng đánh giá cao và ưa thích nhất.', 'meta_keywords' => 'quán ăn nổi bật, nhà hàng ngon, địa điểm ẩm thực được đánh giá cao', 'og_title' => null, 'og_description' => null, 'og_image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['trang' => 'quan-moi', 'ten_trang' => 'Quán Mới Khai Trương', 'meta_title' => 'Quán Mới Khai Trương - Quán Mới', 'meta_description' => 'Khám phá những địa điểm ăn uống mới nhất vừa khai trương, là xu hướng mới nhất.', 'meta_keywords' => 'quán mới, nhà hàng mới khai trương, địa điểm ẩm thực mới', 'og_title' => null, 'og_description' => null, 'og_image' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
