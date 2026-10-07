<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('mon_trong_menu', 'shopeefood_url')) {
            Schema::table('mon_trong_menu', function (Blueprint $table) {
                $table->string('shopeefood_url', 2048)->nullable()->after('hinh_anh');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('mon_trong_menu', 'shopeefood_url')) {
            Schema::table('mon_trong_menu', function (Blueprint $table) {
                $table->dropColumn('shopeefood_url');
            });
        }
    }
};
