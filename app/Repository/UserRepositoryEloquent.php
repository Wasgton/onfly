<?php

namespace App\Repository;

use App\Models\User;
use App\Repository\Contracts\UserRepository;

class UserRepositoryEloquent implements UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByEmail(string $email): User
    {
        return User::where('email', $email)->firstOrFail(); 
    }
}