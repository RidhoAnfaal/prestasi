<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function save(array $data): User;
    public function findById(int $id): ?User;
}
