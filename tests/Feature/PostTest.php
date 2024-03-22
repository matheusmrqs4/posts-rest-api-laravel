<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    private function authenticateUser(User $user)
    {
        $token = JWTAuth::fromUser($user);

        return $token;
    }

    public function testUserAuthenticatedCanCreatePost()
    {
        $user = User::factory()->create();

        $token = $this->authenticateUser($user);

        $post = Post::factory()->for(User::factory())->make();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/post', $post->toArray());

        $response->assertStatus(200);
    }

    public function testUserUnauthenticatedCannotCreatePosts()
    {
        $post = Post::factory()->make();

        $response = $this->postJson('api/post', $post->toArray());

        $response->assertStatus(401);
    }

    public function testUserAuthenticatedCanUpdatePost()
    {
        $user = User::factory()->create();
        $token = $this->authenticateUser($user);

        $post = Post::factory()->for($user)->create();

        $postData = [
        'title' => 'Post Update',
        'description' => 'Test Post Update'
        ];

        $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        ])->putJson("api/post/{$post->id}", $postData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Post Update',
        'description' => 'Test Post Update'
        ]);
    }

    public function testUserUnauthenticatedCannotUpdatePost()
    {
        $post = Post::factory()->create();

        $postData = [
        'title' => 'Post Update',
        'description' => 'Test Post Update',
        ];

        $response = $this->putJson("api/post/{$post->id}", $postData);

        $response->assertStatus(401);
    }

    public function testUserAuthenticatedCanSearchPost()
    {
        $user = User::factory()->create();
        $token = $this->authenticateUser($user);

        $post = Post::factory(3)->for($user)->create();

        $searchQuery = 'Test';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            ])->postJson("api/post/search", ['query' => $searchQuery]);

        $response->assertStatus(200);
    }

    public function testUserUnauthenticatedCannotSearchPost()
    {
        $post = Post::factory(3)->create();

        $searchQuery = 'Test';

        $response = $this->postJson("api/post/search", ['query' => $searchQuery]);

        $response->assertStatus(401);
    }

    public function testUserAuthenticatedCanDeletePost()
    {
        $user = User::factory()->create();
        $token = $this->authenticateUser($user);

        $post = Post::factory()->for($user)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("api/post/{$post->id}");

        $response->assertStatus(200);
    }

    public function testUserUnauthenticatedCannotDeletePost()
    {
        $post = Post::factory()->create();

        $response = $this->deleteJson("api/post/{$post->id}");

        $response->assertStatus(401);
    }
}
