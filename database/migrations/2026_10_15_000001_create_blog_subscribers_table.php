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
        Schema::create('blog_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->char('user_id', 36)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('source', 50)->default('blog_sidebar');
            $table->string('status', 20)->default('active'); // active, unsubscribed
            $table->string('token', 64)->nullable()->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('nguoi_dung')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_subscribers');
    }
};
