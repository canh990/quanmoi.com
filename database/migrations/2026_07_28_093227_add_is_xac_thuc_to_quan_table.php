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
        Schema::table('quan', function (Blueprint $table) {
            $table->boolean('is_xac_thuc')->default(false)->after('is_noi_bat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quan', function (Blueprint $table) {
            $table->dropColumn('is_xac_thuc');
        });
    }
};
