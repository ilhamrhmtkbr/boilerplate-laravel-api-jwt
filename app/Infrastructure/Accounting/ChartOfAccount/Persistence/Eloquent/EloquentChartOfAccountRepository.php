<?php

declare(strict_types=1);

namespace App\Infrastructure\Accounting\ChartOfAccount\Persistence\Eloquent;

use App\Domain\Accounting\ChartOfAccount\Entities\ChartOfAccount;
use App\Domain\Accounting\ChartOfAccount\Exceptions\ChartOfAccountNotFoundException;
use App\Domain\Accounting\ChartOfAccount\Repositories\ChartOfAccountRepositoryInterface;
use App\Infrastructure\Accounting\ChartOfAccount\Persistence\Eloquent\Models\ChartOfAccountModel;

/**
 * Write-side repository: bekerja dengan Domain Entity secara utuh,
 * bukan array mentah. Hanya method yang benar-benar dipakai command
 * (findById, findOrFail, save, delete) — pagination/listing ada di
 * ChartOfAccountQueryRepository, bukan di sini (lihat prinsip #5: CRUD Pragmatism
 * untuk read operation).
 */
final class EloquentChartOfAccountRepository implements ChartOfAccountRepositoryInterface
{
    public function __construct(
        private readonly ChartOfAccountModel $model,
    ) {}

    public function findById(int $id): ?ChartOfAccount
    {
        $record = $this->model->find($id);

        return $record ? $this->toEntity($record) : null;
    }

    public function findOrFail(int $id): ChartOfAccount
    {
        $record = $this->model->find($id);

        if ($record === null) {
            throw ChartOfAccountNotFoundException::withId($id);
        }

        return $this->toEntity($record);
    }

    public function save(ChartOfAccount $chartOfAccount): ChartOfAccount
    {
        $record = $chartOfAccount->id() !== null
            ? $this->model->newQuery()->findOrFail($chartOfAccount->id())
            : $this->model->newInstance();

        $record->name = $chartOfAccount->name();
        $record->save();

        return $this->toEntity($record->fresh());
    }

    public function delete(ChartOfAccount $chartOfAccount): bool
    {
        return (bool) $this->model->newQuery()->findOrFail($chartOfAccount->id())->delete();
    }

    private function toEntity(ChartOfAccountModel $record): ChartOfAccount
    {
        return ChartOfAccount::reconstitute($record->toArray());
    }
}
