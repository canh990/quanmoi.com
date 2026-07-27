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
            $table->string('anh_bia_key')->nullable()->after('anh_bia');
        });
    }

    public function down(): void
    {
        Schema::table('quan', function (Blueprint $table) {
            $table->dropColumn('anh_bia_key');
        });
    }
};
