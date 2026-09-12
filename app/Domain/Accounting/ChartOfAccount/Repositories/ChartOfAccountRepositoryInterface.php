<?php

declare(strict_types=1);

namespace App\Domain\Accounting\ChartOfAccount\Repositories;

use App\Domain\Accounting\ChartOfAccount\Entities\ChartOfAccount;
use App\Domain\Accounting\ChartOfAccount\Exceptions\ChartOfAccountNotFoundException;

/**
 * Interface ini HANYA untuk write-side (command) yang bekerja dengan
 * Domain Entity secara utuh. Read-side (list/search/pagination) sengaja
 * TIDAK lewat sini — lihat Application\...\Queries\GetChartOfAccountList beserta
 * Infrastructure\...${E}QueryRepository. Ini pragmatic split antara
 * write model (via Entity) dan read model (langsung ke Eloquent).
 */
interface ChartOfAccountRepositoryInterface
{
    public function findById(int $id): ?ChartOfAccount;

    /**
     * @throws ChartOfAccountNotFoundException jika ChartOfAccount dengan id tersebut tidak ditemukan.
     */
    public function findOrFail(int $id): ChartOfAccount;

    public function save(ChartOfAccount $chartOfAccount): ChartOfAccount;

    public function delete(ChartOfAccount $chartOfAccount): bool;
}
