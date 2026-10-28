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
        Schema::create('blog_da_luu', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 36);
            $table->unsignedBigInteger('blog_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
            $table->unique(['user_id', 'blog_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_da_luu');
    }
};
