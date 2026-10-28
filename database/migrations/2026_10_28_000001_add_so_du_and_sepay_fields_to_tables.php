<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('nguoi_dung') && !Schema::hasColumn('nguoi_dung', 'so_du')) {
            Schema::table('nguoi_dung', function (Blueprint $table) {
                $table->decimal('so_du', 15, 2)->default(0)->after('vai_tro_id');
            });
        }

        if (Schema::hasTable('thanh_toan')) {
            Schema::table('thanh_toan', function (Blueprint $table) {
                if (!Schema::hasColumn('thanh_toan', 'sepay_order_id')) {
                    $table->string('sepay_order_id')->nullable()->after('ma_giao_dich');
                }
                if (!Schema::hasColumn('thanh_toan', 'sepay_transaction_id')) {
                    $table->string('sepay_transaction_id')->nullable()->after('sepay_order_id');
                }
                if (!Schema::hasColumn('thanh_toan', 'du_lieu_ipn')) {
                    $table->json('du_lieu_ipn')->nullable()->after('ghi_chu');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('nguoi_dung') && Schema::hasColumn('nguoi_dung', 'so_du')) {
            Schema::table('nguoi_dung', function (Blueprint $table) {
                $table->dropColumn('so_du');
            });
        }

        if (Schema::hasTable('thanh_toan')) {
            Schema::table('thanh_toan', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('thanh_toan', 'sepay_order_id')) {
                    $columnsToDrop[] = 'sepay_order_id';
                }
                if (Schema::hasColumn('thanh_toan', 'sepay_transaction_id')) {
                    $columnsToDrop[] = 'sepay_transaction_id';
                }
                if (Schema::hasColumn('thanh_toan', 'du_lieu_ipn')) {
                    $columnsToDrop[] = 'du_lieu_ipn';
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};