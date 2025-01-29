<?php

namespace App\Repository\Contracts;

use App\Models\User;

interface UserRepository
{
    public function create(array $data): User;

    public function findByEmail(string $email) : User;
}