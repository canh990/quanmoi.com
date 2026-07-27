<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            // Store R2 object key (e.g. avatars/{user_id}/uuid.webp)
            // Used to delete old file from R2 when user updates avatar
            $table->string('anh_dai_dien_key')->nullable()->after('anh_dai_dien');
        });
    }

    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn('anh_dai_dien_key');
        });
    }
};
