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
        Schema::table('blog_da_luu', function (Blueprint $table) {
            if (!Schema::hasColumn('blog_da_luu', 'da_doc')) {
                $table->boolean('da_doc')->default(false)->after('blog_id');
            }
            if (!Schema::hasColumn('blog_da_luu', 'doc_luc')) {
                $table->timestamp('doc_luc')->nullable()->after('da_doc');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_da_luu', function (Blueprint $table) {
            if (Schema::hasColumn('blog_da_luu', 'doc_luc')) {
                $table->dropColumn('doc_luc');
            }
            if (Schema::hasColumn('blog_da_luu', 'da_doc')) {
                $table->dropColumn('da_doc');
            }
        });
    }
};
