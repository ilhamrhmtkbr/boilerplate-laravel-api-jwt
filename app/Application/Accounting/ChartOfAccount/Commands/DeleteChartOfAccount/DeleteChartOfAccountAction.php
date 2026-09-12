<?php

declare(strict_types=1);

namespace App\Application\Accounting\ChartOfAccount\Commands\DeleteChartOfAccount;

use App\Domain\Accounting\ChartOfAccount\Repositories\ChartOfAccountRepositoryInterface;

/**
 * Extension point: kalau nanti ada rule "tidak bisa dihapus jika masih
 * dipakai di tempat lain", cek invariant itu DI SINI (Application
 * orchestration: load entity, load repository/aggregate lain, lempar
 * domain exception jika melanggar) — bukan diam-diam ditaruh di
 * Eloquent Model lewat event 'deleting'.
 */
final class DeleteChartOfAccountAction
{
    public function __construct(
        private readonly ChartOfAccountRepositoryInterface $repository,
    ) {}

    public function __invoke(int $id): bool
    {
        $chartOfAccount = $this->repository->findOrFail($id);

        return $this->repository->delete($chartOfAccount);
    }
}
