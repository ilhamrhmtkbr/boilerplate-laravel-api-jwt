<?php

declare(strict_types=1);

namespace App\Domain\Accounting\Repositories\Contracts;

use App\Domain\Accounting\Entities\GeneralLedger;
use Illuminate\Pagination\LengthAwarePaginator;

interface GeneralLedgerRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findAll(array $filters = []): array;
    public function findById(int $id): ?GeneralLedger;
    public function findOrFail(int $id): GeneralLedger;
    public function create(array $data): GeneralLedger;
    public function update(int $id, array $data): GeneralLedger;
    public function delete(int $id): bool;
    public function exists(int $id): bool;
}
