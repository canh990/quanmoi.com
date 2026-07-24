<?php

namespace Database\Seeders;

use App\Models\VaiTro;
use Illuminate\Database\Seeder;

class VaiTroSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin',
            'chu_quan',
            'nguoi_dung',
        ];

        foreach ($roles as $roleName) {
            VaiTro::firstOrCreate(['ten' => $roleName]);
        }
    }
}
