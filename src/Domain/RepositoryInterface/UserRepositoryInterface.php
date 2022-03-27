<?php

declare(strict_types=1);

namespace App\Domain\RepositoryInterface;

interface UserRepositoryInterface
{
    public function getAllUsers(): array;

    public function getUserById(string $id): ?array;

    public function getUserByEmail(string $email): ?array;

    public function getUserLastId(): array;

    public function createUser(array $user): array;

    public function editUser(string $id, array $user): array;

    public function deleteUserById(string $id, array $user): ?bool;

    public function checkFields(array $user): array;
}
