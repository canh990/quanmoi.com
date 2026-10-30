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
        if (Schema::hasTable('blog_comment_likes') && !Schema::hasColumn('blog_comment_likes', 'reaction')) {
            Schema::table('blog_comment_likes', function (Blueprint $table) {
                $table->string('reaction', 20)->default('like')->after('user_id');
                $table->index(['comment_id', 'reaction']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('blog_comment_likes') && Schema::hasColumn('blog_comment_likes', 'reaction')) {
            Schema::table('blog_comment_likes', function (Blueprint $table) {
                $table->dropIndex(['comment_id', 'reaction']);
                $table->dropColumn('reaction');
            });
        }
    }
};
