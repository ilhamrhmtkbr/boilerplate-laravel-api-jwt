<?php

declare(strict_types=1);

namespace App\Domain\Accounting\Repositories\Contracts;

use App\Domain\Accounting\Entities\ClosingEntry;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClosingEntryRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findAll(array $filters = []): array;
    public function findById(int $id): ?ClosingEntry;
    public function findOrFail(int $id): ClosingEntry;
    public function create(array $data): ClosingEntry;
    public function update(int $id, array $data): ClosingEntry;
    public function delete(int $id): bool;
    public function exists(int $id): bool;
}
