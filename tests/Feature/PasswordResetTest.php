<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\WithFaker;

class PasswordResetTest extends TestCase
{
    use WithFaker;

    public function testSendResetLink()
    {
        Mail::fake();

        $user = User::factory()->create();

        $response = $this->postJson('api/forgot-password', ['email' => $user->email]);

        $response->assertStatus(200)
                 ->assertJson([
                    'message' => 'Reset Link sent to your email!'
                 ]);

        Mail::assertSent(ResetPasswordMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function testResetPassword()
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $newPassword = $this->faker->password;

        $this->postJson('api/reset-password', [
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'token' => $token,
        ]);

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }
}
