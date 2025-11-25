<?php

namespace Tests\Fakes;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;

class FakeUserRepository implements UserRepositoryInterface
{
    private $users = [];
    private $nextId = 1;

    public function save(array $data): User
    {
        $user = new User($data);
        $user->user_id = $this->nextId++;
        $this->users[$user->user_id] = $user;
        return $user;
    }

    public function findById(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }
}
