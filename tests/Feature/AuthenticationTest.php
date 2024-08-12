<?php

namespace Ogrre\ApiAuth\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Artisan;
use Ogrre\ApiAuth\Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('vendor:publish', ['--tag' => 'laravel-api-auth', '--force' => true]);
    }

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type']);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => __('laravel-api-auth::messages.logout_success')]);
    }

    public function test_forgot_password_sends_email()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/auth/forgot-password', ['email' => $user->email]);

        $response->assertStatus(200)
            ->assertJson(['message' => __('laravel-api-auth::messages.reset_link_sent')]);

        Notification::assertSentTo([$user], \Illuminate\Auth\Notifications\ResetPassword::class);
    }

    public function test_reset_password()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => __('laravel-api-auth::messages.password_reset_success')]);
    }

    public function test_custom_reset_password_notification_can_be_published()
    {
        Artisan::call('vendor:publish', ['--tag' => 'laravel-api-auth-custom-reset', '--force' => true]);

        $this->assertFileExists(app_path('Notifications/ApiResetPasswordNotification.php'));
    }
}
