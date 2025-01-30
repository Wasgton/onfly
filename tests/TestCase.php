<?php

namespace Tests;

use App\Exceptions\FailToCreateException;
use App\Service\AuthService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;
    /**
     * @throws FailToCreateException
     */
    protected function createUser(): array
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
        $user = app(AuthService::class)->createUser($userData);
        return array_merge($userData, ['user' => $user]);
    }
}
