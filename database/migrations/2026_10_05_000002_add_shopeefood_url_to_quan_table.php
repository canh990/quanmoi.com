<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('quan', 'shopeefood_url')) {
            Schema::table('quan', function (Blueprint $table) {
                $table->string('shopeefood_url', 500)->nullable()->after('tiktok_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('quan', 'shopeefood_url')) {
            Schema::table('quan', function (Blueprint $table) {
                $table->dropColumn('shopeefood_url');
            });
        }
    }
};
