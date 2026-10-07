<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        return [
            'user_id' => User::factory(),
            'category_id' => null,
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numerify('####'),
            'excerpt' => $this->faker->sentence(),
            'content' => '<p>' . $this->faker->paragraph() . '</p>',
            'cover_image' => null,
            'status' => 'draft',
            'seo_title' => $title,
            'seo_description' => $this->faker->sentence(),
            'meta_keywords' => 'blog, article',
            'is_hero' => false,
            'published_at' => null,
        ];
    }
}
