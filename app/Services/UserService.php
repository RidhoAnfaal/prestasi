<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(string $name, string $email, string $password, int $roleId = 3)
    {
        if (strlen($name) < 3) {
            throw new \InvalidArgumentException('Name must be at least 3 characters long');
        }

        $userData = [
            'user_name' => $name,
            'user_username' => $email,
            'user_password' => bcrypt($password),
            'role_id' => $roleId,
        ];

        return $this->userRepository->save($userData);
    }
}
