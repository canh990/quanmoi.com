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
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('blog_id');
            $table->unsignedInteger('likes_count')->default(0)->after('content');

            $table->foreign('parent_id')->references('id')->on('blog_comments')->onDelete('cascade');
        });

        Schema::create('blog_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->string('user_id', 36);
            $table->timestamps();

            $table->foreign('comment_id')->references('id')->on('blog_comments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->unique(['comment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_comment_likes');

        Schema::table('blog_comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'likes_count']);
        });
    }
};
