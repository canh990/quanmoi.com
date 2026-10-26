<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('danh_muc_quan')) {
            Schema::create('danh_muc_quan', function (Blueprint $table) {
                $table->id();
                $table->string('ten_danh_muc', 100);
                $table->string('slug', 100)->unique();
                $table->string('mo_ta', 255)->nullable();
                $table->string('icon', 50)->nullable()->default('restaurant');
                $table->string('mau_sac', 30)->default('amber');
                $table->string('hinh_anh', 255)->nullable();
                $table->unsignedInteger('thu_tu')->default(0);
                $table->boolean('is_hien_thi')->default(true);
                $table->boolean('is_noi_bat')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('quan') && !Schema::hasColumn('quan', 'danh_muc_id')) {
            Schema::table('quan', function (Blueprint $table) {
                $table->unsignedBigInteger('danh_muc_id')->nullable()->after('chu_quan_id');
                $table->foreign('danh_muc_id')->references('id')->on('danh_muc_quan')->nullOnDelete();
            });
        }

        $initialCategories = [
            [
                'ten_danh_muc' => 'Nhà hàng',
                'slug'         => 'nha-hang',
                'mo_ta'        => 'Bữa chính đậm đà',
                'icon'         => 'restaurant',
                'mau_sac'      => 'rose',
                'thu_tu'       => 1,
                'is_hien_thi'  => true,
                'is_noi_bat'   => true,
            ],
            [
                'ten_danh_muc' => 'Cà phê & Trà',
                'slug'         => 'ca-phe-tra',
                'mo_ta'        => 'Tụ tập & Làm việc',
                'icon'         => 'local_cafe',
                'mau_sac'      => 'amber',
                'thu_tu'       => 2,
                'is_hien_thi'  => true,
                'is_noi_bat'   => true,
            ],
            [
                'ten_danh_muc' => 'Billiards & Giải trí',
                'slug'         => 'billiards-giai-tri',
                'mo_ta'        => 'Vui chơi cuối tuần',
                'icon'         => 'sports_esports',
                'mau_sac'      => 'indigo',
                'thu_tu'       => 3,
                'is_hien_thi'  => true,
                'is_noi_bat'   => true,
            ],
            [
                'ten_danh_muc' => 'Đồ ăn vặt',
                'slug'         => 'do-an-vat',
                'mo_ta'        => 'Ngon rẻ chuẩn gu',
                'icon'         => 'fastfood',
                'mau_sac'      => 'emerald',
                'thu_tu'       => 4,
                'is_hien_thi'  => true,
                'is_noi_bat'   => true,
            ],
            [
                'ten_danh_muc' => 'Lẩu & Nướng',
                'slug'         => 'lau-nuong',
                'mo_ta'        => 'Sum vầy ấm cúng',
                'icon'         => 'ramen_dining',
                'mau_sac'      => 'orange',
                'thu_tu'       => 5,
                'is_hien_thi'  => true,
                'is_noi_bat'   => true,
            ],
            [
                'ten_danh_muc' => 'Quán nhậu & Bar',
                'slug'         => 'quan-nhau',
                'mo_ta'        => 'Chill hết nấc cùng bạn bè',
                'icon'         => 'local_bar',
                'mau_sac'      => 'violet',
                'thu_tu'       => 6,
                'is_hien_thi'  => true,
                'is_noi_bat'   => true,
            ],
            [
                'ten_danh_muc' => 'Trà sữa & Nước ép',
                'slug'         => 'tra-sua-nuoc-ep',
                'mo_ta'        => 'Thanh mát ngọt ngào',
                'icon'         => 'emoji_food_beverage',
                'mau_sac'      => 'pink',
                'thu_tu'       => 7,
                'is_hien_thi'  => true,
                'is_noi_bat'   => false,
            ],
            [
                'ten_danh_muc' => 'Tiệm bánh',
                'slug'         => 'tiem-banh',
                'mo_ta'        => 'Bánh ngọt thơm lừng',
                'icon'         => 'bakery_dining',
                'mau_sac'      => 'yellow',
                'thu_tu'       => 8,
                'is_hien_thi'  => true,
                'is_noi_bat'   => false,
            ],
            [
                'ten_danh_muc' => 'Quán ăn bình dân',
                'slug'         => 'quan-an-binh-dan',
                'mo_ta'        => 'Bữa ăn thân thuộc giá sinh viên',
                'icon'         => 'lunch_dining',
                'mau_sac'      => 'teal',
                'thu_tu'       => 9,
                'is_hien_thi'  => true,
                'is_noi_bat'   => false,
            ],
        ];

        foreach ($initialCategories as $cat) {
            $cat['created_at'] = now();
            $cat['updated_at'] = now();
            DB::table('danh_muc_quan')->updateOrInsert(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        $allCats = DB::table('danh_muc_quan')->get();
        foreach ($allCats as $cat) {
            $searchTerms = [$cat->ten_danh_muc];
            if ($cat->slug === 'ca-phe-tra') {
                $searchTerms[] = 'Cà phê';
            } elseif ($cat->slug === 'do-an-vat') {
                $searchTerms[] = 'Ăn vặt';
            }

            DB::table('quan')
                ->whereNull('danh_muc_id')
                ->whereIn('loai_hinh_kinh_doanh', $searchTerms)
                ->update(['danh_muc_id' => $cat->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('quan') && Schema::hasColumn('quan', 'danh_muc_id')) {
            Schema::table('quan', function (Blueprint $table) {
                $table->dropForeign(['danh_muc_id']);
                $table->dropColumn('danh_muc_id');
            });
        }

        Schema::dropIfExists('danh_muc_quan');
    }
};
