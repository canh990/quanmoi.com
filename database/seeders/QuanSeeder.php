<?php

namespace Database\Seeders;

use App\Models\Quan;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Option 1: Attach to a random user if users exist
        $users = User::all();
        
        if ($users->count() > 0) {
            Quan::factory()->count(50)->make()->each(function ($quan) use ($users) {
                $quan->chu_quan_id = $users->random()->id;
                $quan->save();
            });
        } else {
            Quan::factory()->count(50)->create();
        }
    }
}
