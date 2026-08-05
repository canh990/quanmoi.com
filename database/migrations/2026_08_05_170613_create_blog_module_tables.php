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
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 36);
            $table->foreign('user_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('cover_image')->nullable();
            
            $table->enum('status', ['draft', 'pending', 'need_revision', 'approved', 'published', 'rejected', 'hidden'])->default('draft');
            
            // SEO
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 255)->nullable();
            $table->string('meta_keywords')->nullable();
            
            $table->boolean('is_hero')->default(false);
            
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_tag_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->foreignId('blog_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->string('user_id', 36);
            $table->foreign('user_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->text('content');
            $table->enum('status', ['active', 'hidden'])->default('active');
            $table->timestamps();
        });

        Schema::create('blog_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('blog_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->string('user_id', 36);
            $table->foreign('user_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->text('reason');
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('blog_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->string('user_id', 36); // The user who made the revision
            $table->foreign('user_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->longText('content_before')->nullable();
            $table->longText('content_after')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_revisions');
        Schema::dropIfExists('blog_reports');
        Schema::dropIfExists('blog_views');
        Schema::dropIfExists('blog_comments');
        Schema::dropIfExists('blog_tag_items');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
    }
};
