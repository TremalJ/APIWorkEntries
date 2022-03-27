<?php


declare(strict_types=1);

namespace App\Domain\RepositoryInterface;

use Symfony\Component\HttpFoundation\Response;

interface WorkEntryRepositoryInterface
{
    public function getAllWorksEntryByUserId(string $user_id): array;

    public function getWorkEntryById(string $id): array;

    public function createWorkEntry(array $user, string $user_id): array;

    public function editWorkEntry(string $id, array $user): array;

    public function deleteWorkEntryById(string $id, array $workEntry): ?bool;
}
