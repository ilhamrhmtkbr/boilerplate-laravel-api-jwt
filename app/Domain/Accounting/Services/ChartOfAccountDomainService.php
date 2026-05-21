<?php

declare(strict_types=1);

namespace App\Domain\Accounting\Services;

use App\Domain\Accounting\Entities\ChartOfAccount;
use App\Domain\Accounting\Repositories\Contracts\ChartOfAccountRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ChartOfAccountDomainService
{
    public function __construct(
        private readonly ChartOfAccountRepositoryInterface $repository,
    ) {}

    public function getList(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function getById(int $id): ChartOfAccount
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): ChartOfAccount
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): ChartOfAccount
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
