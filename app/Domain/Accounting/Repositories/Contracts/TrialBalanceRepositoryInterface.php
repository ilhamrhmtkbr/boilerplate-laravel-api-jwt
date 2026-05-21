<?php

declare(strict_types=1);

namespace App\Domain\Accounting\Repositories\Contracts;

use App\Domain\Accounting\Entities\TrialBalance;
use Illuminate\Pagination\LengthAwarePaginator;

interface TrialBalanceRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findAll(array $filters = []): array;
    public function findById(int $id): ?TrialBalance;
    public function findOrFail(int $id): TrialBalance;
    public function create(array $data): TrialBalance;
    public function update(int $id, array $data): TrialBalance;
    public function delete(int $id): bool;
    public function exists(int $id): bool;
}
