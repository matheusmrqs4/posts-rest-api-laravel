<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $userId = User::factory()->create()->id;

        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'users_id' => $userId,
        ];
    }
}
