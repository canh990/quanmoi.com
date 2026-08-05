<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('xac_thuc_otp', function (Blueprint $table) {
            $table->string('otp_hash')->nullable()->after('otp');
            $table->unsignedTinyInteger('attempts')->default(0)->after('expires_at');
            $table->timestamp('locked_until')->nullable()->after('attempts');
            $table->timestamp('last_sent_at')->nullable()->after('locked_until');
            $table->index(['email', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('xac_thuc_otp', function (Blueprint $table) {
            $table->dropIndex(['email', 'expires_at']);
            $table->dropColumn(['otp_hash', 'attempts', 'locked_until', 'last_sent_at']);
        });
    }
};
