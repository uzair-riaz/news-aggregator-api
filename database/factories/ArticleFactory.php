<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'url' => $this->faker->url(),
            'description' => $this->faker->paragraph(),
            'published_at' => $this->faker->date(),
            'source_id' => Source::factory()->create()->id,
            'category_id' => Category::factory()->create()->id,
            'author_id' => Author::factory()->create()->id
        ];
    }
}
