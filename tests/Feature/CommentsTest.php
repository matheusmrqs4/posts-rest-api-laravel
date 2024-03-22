<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Comments;
use App\Events\NewNotification;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;

class CommentsTest extends TestCase
{
    use WithFaker;

    private function authenticateUser(User $user)
    {
        $token = JWTAuth::fromUser($user);

        return $token;
    }

    public function testUserAuthenticatedCanCreateComments()
    {
        $user = User::factory()->create();

        $token = $this->authenticateUser($user);

        $post = Post::factory()->for(User::factory())->create();

        $comment = Comments::factory()->make();

        Event::fake();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("api/post/{$post->id}/comments", $comment->toArray());

        $response->assertStatus(200);

        Event::assertDispatched(NewNotification::class);
    }

    public function testUserUnauthenticatedCannotCreateComments()
    {
        $post = Post::factory()->for(User::factory())->create();

        $comment = Comments::factory()->make();

        $post->comments()->create($comment->toArray());

        $response = $this->postJson("api/post/{$post->id}/comments", $comment->toArray());

        $response->assertStatus(401);
    }

    public function testUserAuthenticatedCanUpdateComments()
    {
        $user = User::factory()->create();

        $token = $this->authenticateUser($user);

        $comment = Comments::factory()->create();

        $update = [
            'comment' => $this->faker->sentence,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("api/comments/{$comment->id}", $update);

        $response->assertStatus(200);
    }

    public function testUserUnauthenticatedCannotUpdateComments()
    {
        $comment = Comments::factory()->create();

        $update = [
            'comment' => $this->faker->sentence,
        ];

        $response = $this->putJson("api/comments/{$comment->id}", $update);

        $response->assertStatus(401);
    }

    public function testUserAuthenticatedCanDeleteComments()
    {
        $user = User::factory()->create();

        $token = $this->authenticateUser($user);

        $comment = Comments::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("api/comments/{$comment->id}");

        $response->assertStatus(200);
    }

    public function testUserUnauthenticatedCannotDeleteComments()
    {
        $comment = Comments::factory()->create();

        $response = $this->deleteJson("api/comments/{$comment->id}");

        $response->assertStatus(401);
    }

    public function testUserAuthenticatedCanReplyToComments()
    {
        $user = User::factory()->create();

        $token = $this->authenticateUser($user);

        $comment = Comments::factory()->create();

        $replyData = [
            'comment' => $this->faker->sentence
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("api/post/{$comment->id}/replies", $replyData);

        $response->assertStatus(200);
    }

    public function testUserUnauthenticatedCannotReplyToComments()
    {
        $comment = Comments::factory()->create();

        $replyData = [
            'comment' => $this->faker->sentence
        ];

        $response = $this->postJson("api/post/{$comment->id}/replies", $replyData);

        $response->assertStatus(401);
    }
}
