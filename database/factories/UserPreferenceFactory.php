<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'preferences' => [
                'source_ids' => [
                    Source::factory()->create()->id,
                    Source::factory()->create()->id
                ],
                'category_ids' => [
                    Category::factory()->create()->id,
                    Category::factory()->create()->id
                ],
                'author_ids' => [
                    Author::factory()->create()->id,
                    Author::factory()->create()->id
                ]
            ]
        ];
    }
}
