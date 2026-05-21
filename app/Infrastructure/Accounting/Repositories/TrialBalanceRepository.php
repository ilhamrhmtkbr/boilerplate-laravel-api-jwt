<?php

declare(strict_types=1);

namespace App\Infrastructure\Accounting\Repositories;

use App\Domain\Accounting\Entities\TrialBalance as TrialBalanceEntity;
use App\Domain\Accounting\Repositories\Contracts\TrialBalanceRepositoryInterface;
use App\Infrastructure\Accounting\Eloquent\TrialBalance as TrialBalanceModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class TrialBalanceRepository implements TrialBalanceRepositoryInterface
{
    public function __construct(
        private readonly TrialBalanceModel $model,
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['sort']) && !empty($filters['order'])) {
            $allowedSorts = ['id', 'name', 'created_at'];
            $sort  = in_array($filters['sort'], $allowedSorts, true) ? $filters['sort'] : 'created_at';
            $order = strtolower($filters['order']) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }

        return $query->paginate(
            perPage:  $perPage,
            columns:  ['*'],
            pageName: 'page',
            page:     $filters['page'] ?? null,
        );
    }

    public function findAll(array $filters = []): array
    {
        return $this->model->newQuery()->get()->all();
    }

    public function findById(int $id): ?TrialBalanceEntity
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findOrFail(int $id): TrialBalanceEntity
    {
        try {
            $record = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new \DomainException("TrialBalance with ID {$id} not found.", 404, $e);
        }
        return $this->toEntity($record);
    }

    public function create(array $data): TrialBalanceEntity
    {
        $record = $this->model->create($data);
        return $this->toEntity($record);
    }

    public function update(int $id, array $data): TrialBalanceEntity
    {
        $record = $this->model->newQuery()->findOrFail($id);
        $record->update($data);
        return $this->toEntity($record->fresh());
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->newQuery()->findOrFail($id)->delete();
    }

    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    private function toEntity(TrialBalanceModel $model): TrialBalanceEntity
    {
        return TrialBalanceEntity::reconstitute($model->toArray());
    }
}
