<?php

namespace Ogrre\ApiAuth\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ogrre\ApiAuth\Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

//    /** @test */
//    public function it_allows_user_login()
//    {
//        $this->testUser->password = bcrypt('password');
//        $this->testUser->save();
//
//        $response = $this->postJson('/api/auth/login', [
//            'email' => $this->testUser->email,
//            'password' => 'password',
//        ]);
//
//        $response->assertStatus(200);
//        $response->assertJsonStructure(['token']);
//    }
//
//    /** @test */
//    public function it_allows_user_logout()
//    {
//        $response = $this->actingAs($this->testUser, 'api')->postJson('/api/auth/logout');
//
//        $response->assertStatus(200);
//    }
//
//    /** @test */
//    public function it_sends_forgot_password_email()
//    {
//        $response = $this->postJson('/api/auth/forgot-password', [
//            'email' => $this->testUser->email,
//        ]);
//
//        $response->assertStatus(200);
//        // Assuming you have a way to check that the email was sent
//    }
//
//    /** @test */
//    public function it_resets_password()
//    {
//        $token = \Illuminate\Support\Facades\Password::createToken($this->testUser);
//
//        $response = $this->postJson('/api/auth/reset-password', [
//            'email' => $this->testUser->email,
//            'token' => $token,
//            'password' => 'new-password',
//            'password_confirmation' => 'new-password',
//        ]);
//
//        $response->assertStatus(200);
//        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-password', $this->testUser->fresh()->password));
//    }
//
//    /** @test */
//    public function it_allows_custom_reset_password_notification()
//    {
//        $token = \Illuminate\Support\Facades\Password::createToken($this->testUser);
//
//        $response = $this->postJson('/api/auth/reset-password', [
//            'email' => $this->testUser->email,
//            'token' => $token,
//            'password' => 'new-password',
//            'password_confirmation' => 'new-password',
//        ]);
//
//        $response->assertStatus(200);
//    }
}
