<?php

namespace Ogrre\ApiAuth\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Ogrre\ApiAuth\Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRegister()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $expiresAt = config('sanctum.expiration')
            ? Carbon::now()->addMinutes(config('sanctum.expiration'))->toDateTimeString()
            : null;

        $response = $this->postJson('api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'token_type',
                'expires_at',
                'access_token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $user = $this->userClass::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function testCannotRegisterIfUserAlreadyExists()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email')
            ->assertJson([
                'errors' => [
                    'email' => ['The email has already been taken.'],
                ],
            ]);

        $this->assertEquals(1, $this->userClass::count());
    }

    public function testCannotRegisterIfPasswordConfirmationFails()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different_password',
        ];

        $response = $this->postJson('api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password')
            ->assertJson([
                'errors' => [
                    'password' => ['The password confirmation does not match.'],
                ],
            ]);

        $this->assertEquals(0, $this->userClass::count());
    }
}
