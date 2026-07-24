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
        Schema::create('seo_datas', function (Blueprint $table) {
            $table->id();
            $table->string('page_type'); // type, location, all
            $table->string('name');
            $table->string('loai_hinh_kinh_doanh')->nullable();
            $table->string('province_code')->nullable();
            $table->string('district_code')->nullable();
            $table->string('link');
            $table->string('meta_title');
            $table->text('meta_description')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_datas');
    }
};
