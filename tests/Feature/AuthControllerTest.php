<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;


class AuthControllerTest extends TestCase
{

    use RefreshDatabase, WithFaker;
    
    /**
     * Test user registration feature
     */
    public function test_user_can_register(): void
    {        
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
       $this->postJson('/api/register', $userData)
            ->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email']
            ]);
        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }

    public function test_user_cant_register_with_invalid_data()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => 'invalid_email',
            'password' => 'passwo',
            'password_confirmation' => 'passw',
        ];
        $this->postJson('/api/register', $userData)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['password', 'password_confirmation', 'email']);
    }

    public function test_user_can_login()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
        $this->postJson('/api/register', $userData);

        $loginData = [
            'email' => $userData['email'],
            'password' => $userData['password']
        ];
        $this->postJson('/api/login', $loginData)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);
    }
    
    public function test_user_cant_login_with_wrong_data()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
        $this->postJson('/api/register', $userData);

        $loginData = [
            'email' => $this->faker->email,
            'password' => $userData['password']
        ];
        $this->postJson('/api/login', $loginData)
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(['error'=>'Unauthorized']);
    }
    
}
