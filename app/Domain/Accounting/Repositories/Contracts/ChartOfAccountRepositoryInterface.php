<?php

declare(strict_types=1);

namespace App\Domain\Accounting\Repositories\Contracts;

use App\Domain\Accounting\Entities\ChartOfAccount;
use Illuminate\Pagination\LengthAwarePaginator;

interface ChartOfAccountRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findAll(array $filters = []): array;
    public function findById(int $id): ?ChartOfAccount;
    public function findOrFail(int $id): ChartOfAccount;
    public function create(array $data): ChartOfAccount;
    public function update(int $id, array $data): ChartOfAccount;
    public function delete(int $id): bool;
    public function exists(int $id): bool;
}
