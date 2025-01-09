<?php

namespace Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\FeatureTestCase;

class AuthTest extends FeatureTestCase
{
    public function test_register_validation_errors()
    {
        $response = $this->post('/api/register', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(
            [
                "password" => [
                    "The password field confirmation does not match."
                ],
                "password_confirmation" => [
                    "The password confirmation field is required."
                ]
            ]
        );
    }

    public function test_register_successful()
    {
        $response = $this->post('/api/register', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['name', 'access_token']);
    }

    public function test_login_validation_errors()
    {
        $response = $this->post('/api/login', [
            'email' => 'test@test.com'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(
            [
                "password" => [
                    "The password field is required."
                ]
            ]
        );
    }

    public function test_login_successful()
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => 'password'
        ]);
        $response = $this->post('/api/login', [
            'email' => 'test@test.com',
            'password' => 'password'
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure(['access_token']);
    }

    public function test_logout_unathenticated()
    {
        $response = $this->post('/api/logout');

        $response->assertUnauthorized();
    }

    public function test_logout_successful()
    {
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => 'password'
        ]);
        $response = $this->actingAs($user)->post('/api/logout');

        $response->assertSuccessful();
    }

    public function test_reset_password_validation_errors()
    {
        $response = $this->post('/api/reset-password', [
            'password' => 'password'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(
            [
                "email" => [
                    "The email field is required."
                ]
            ]
        );
    }

    public function test_reset_password_successful()
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => 'password'
        ]);
        $response = $this->post('/api/reset-password', [
            'email' => 'test@test.com',
            'password' => 'new_password'
        ]);

        $response->assertSuccessful();
    }
}
