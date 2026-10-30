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
        if (!Schema::hasTable('blog_reactions')) {
            Schema::create('blog_reactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('blog_id')->constrained('blogs')->onDelete('cascade');
                $table->string('user_id', 36);
                $table->foreign('user_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
                $table->string('reaction', 20)->default('like'); // like, love, drool, fire, cry, angry
                $table->timestamps();

                $table->unique(['blog_id', 'user_id']);
                $table->index(['blog_id', 'reaction']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_reactions');
    }
};
