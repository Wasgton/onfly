<?php

namespace App\Service;

use App\Exceptions\FailToCreateException;
use App\Models\User;
use App\Repository\Contracts\UserRepository;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class AuthService
{
    private const MULTIPLIER = 60;
    public function __construct(private readonly UserRepository $userRepository) {}

    /**
     * @throws FailToCreateException
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        unset($data['password_confirmation']);
        if (!$user = $this->userRepository->create($data)){
            throw new FailToCreateException('Failed to create User', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $user;
    }

    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);
        if (!$token = auth()->login($user)) {
            throw new UnauthorizedException("Unauthorized", Response::HTTP_UNAUTHORIZED);
        }
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * self::MULTIPLIER
        ];
    }
    
}