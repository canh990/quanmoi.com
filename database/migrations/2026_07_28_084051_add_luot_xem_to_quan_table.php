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
            $table->unsignedBigInteger('luot_xem')->default(0)->after('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quan', function (Blueprint $table) {
            $table->dropColumn('luot_xem');
        });
    }
};
