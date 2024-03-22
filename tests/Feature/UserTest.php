<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use WithFaker;

    public function testUserCanCreateAccount()
    {
        $userData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password123',
            'bio' => $this->faker->sentence(),
            'city' => $this->faker->city(),
            'contact' => $this->faker->phoneNumber(),
            'terms_of_service' => true,
        ];

        $response = $this->postJson("api/authenticate/register", $userData);

        $response->assertStatus(200);
    }

    public function testUserCanLogin()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson("api/authenticate/login", $credentials);

        $response->assertStatus(200);
    }

    public function testUserAuthenticatedCanLogout()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $token = JWTAuth::fromUser($user);

        $this->assertNotNull($token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("api/authenticate/logout");

        $response->assertStatus(200);
    }

    public function testUserAuthenticatedCanUpdateProfile()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $token = JWTAuth::fromUser($user);

        $this->assertNotNull($token);

        $update = [
            'bio' => $this->faker->sentence,
            'city' => $this->faker->city,
            'contact' => $this->faker->phoneNumber,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("api/profile/update", $update);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'bio' => $update['bio'],
        'city' => $update['city'],
        'contact' => $update['contact'],
        ]);
    }

    public function testUserUnauthenticatedCannotUpdateProfile()
    {
        $update = [
            'bio' => $this->faker->sentence,
            'city' => $this->faker->city,
            'contact' => $this->faker->phoneNumber,
        ];

        $response = $this->putJson("api/profile/update", $update);
        $response->assertStatus(401);
    }
}
