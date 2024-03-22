<?php

namespace Database\Factories;

use App\Models\Comments;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentsFactory extends Factory
{
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'comment' => $this->faker->sentence(),
            'posts_id' => Post::factory()->create()->id,
            'users_id' => $user->id,
        ];
    }
}
