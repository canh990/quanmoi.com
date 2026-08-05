<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update blogs table
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('approved_by', 36)->nullable();
            $table->foreign('approved_by')->references('id')->on('nguoi_dung')->nullOnDelete();

            $table->string('published_by', 36)->nullable();
            $table->foreign('published_by')->references('id')->on('nguoi_dung')->nullOnDelete();

            $table->integer('reading_time')->nullable();
            $table->boolean('allow_comments')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->string('canonical_url')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('last_submitted_at')->nullable();

            // Drop old enum status and recreate as string for better flexibility
        });

        // SQLite already stores Laravel enum columns as strings; MODIFY is MySQL-only syntax.
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE blogs MODIFY COLUMN status VARCHAR(20) DEFAULT 'draft'");
        }

        // 2. Drop blog_revisions
        Schema::dropIfExists('blog_revisions');

        // 3. Create blog_moderation_logs
        Schema::create('blog_moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->string('admin_id', 36)->nullable();
            $table->foreign('admin_id')->references('id')->on('nguoi_dung')->nullOnDelete();

            $table->string('action'); // submitted, approved, published, requested_revision, rejected, hidden, restored
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_moderation_logs');

        Schema::create('blog_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->onDelete('cascade');
            $table->string('user_id', 36);
            $table->foreign('user_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->longText('content_before')->nullable();
            $table->longText('content_after')->nullable();
            $table->timestamps();
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['published_by']);

            $table->dropColumn([
                'approved_by',
                'published_by',
                'reading_time',
                'allow_comments',
                'is_featured',
                'view_count',
                'comment_count',
                'canonical_url',
                'og_image',
                'scheduled_at',
                'last_submitted_at',
            ]);
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE blogs MODIFY COLUMN status ENUM('draft', 'pending', 'need_revision', 'approved', 'published', 'rejected', 'hidden') DEFAULT 'draft'");
        }
    }
};
