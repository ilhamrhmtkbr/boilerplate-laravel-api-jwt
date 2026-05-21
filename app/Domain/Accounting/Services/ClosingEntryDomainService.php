<?php

declare(strict_types=1);

namespace App\Domain\Accounting\Services;

use App\Domain\Accounting\Entities\ClosingEntry;
use App\Domain\Accounting\Repositories\Contracts\ClosingEntryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ClosingEntryDomainService
{
    public function __construct(
        private readonly ClosingEntryRepositoryInterface $repository,
    ) {}

    public function getList(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function getById(int $id): ClosingEntry
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): ClosingEntry
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): ClosingEntry
    {
        $this->repository->findOrFail($id);
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $this->repository->findOrFail($id);
        return $this->repository->delete($id);
    }
}
