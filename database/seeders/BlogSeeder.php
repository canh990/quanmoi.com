<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');

        // Ensure we have at least one user
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'id' => (string) Str::uuid(),
                'ho_ten' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        // Ensure we have at least one category
        $category = BlogCategory::first();
        if (!$category) {
            $category = BlogCategory::create([
                'name' => 'Tin tức tổng hợp',
                'slug' => 'tin-tuc-tong-hop',
                'description' => 'Tin tức tổng hợp',
                'status' => true,
            ]);
        }

        // Create 10 blog posts
        for ($i = 0; $i < 10; $i++) {
            $title = rtrim($faker->sentence(rand(6, 12)), '.');
            
            Blog::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'excerpt' => $faker->paragraph(2),
                'content' => '<p>' . implode('</p><p>', $faker->paragraphs(rand(4, 8))) . '</p>',
                'cover_image' => 'https://picsum.photos/seed/'.rand(1, 1000).'/800/600',
                'status' => 'published',
                'is_hero' => $i === 0, // Make the first one a hero post
                'published_at' => now()->subDays(rand(1, 30)),
                'reading_time' => rand(2, 10),
                'allow_comments' => true,
                'is_featured' => rand(1, 100) <= 30, // 30% chance to be featured
                'view_count' => rand(10, 1000),
                'approved_by' => $user->id,
                'published_by' => $user->id,
            ]);
        }
    }
}
