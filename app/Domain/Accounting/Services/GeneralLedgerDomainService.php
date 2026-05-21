<?php

declare(strict_types=1);

namespace App\Domain\Accounting\Services;

use App\Domain\Accounting\Entities\GeneralLedger;
use App\Domain\Accounting\Repositories\Contracts\GeneralLedgerRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class GeneralLedgerDomainService
{
    public function __construct(
        private readonly GeneralLedgerRepositoryInterface $repository,
    ) {}

    public function getList(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function getById(int $id): GeneralLedger
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): GeneralLedger
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): GeneralLedger
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
