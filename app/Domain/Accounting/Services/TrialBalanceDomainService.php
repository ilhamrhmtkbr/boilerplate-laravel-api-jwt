<?php

declare(strict_types=1);

namespace App\Domain\Accounting\Services;

use App\Domain\Accounting\Entities\TrialBalance;
use App\Domain\Accounting\Repositories\Contracts\TrialBalanceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TrialBalanceDomainService
{
    public function __construct(
        private readonly TrialBalanceRepositoryInterface $repository,
    ) {}

    public function getList(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function getById(int $id): TrialBalance
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): TrialBalance
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): TrialBalance
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
