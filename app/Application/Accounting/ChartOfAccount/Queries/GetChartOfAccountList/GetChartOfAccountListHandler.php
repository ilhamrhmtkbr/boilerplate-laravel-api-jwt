<?php

declare(strict_types=1);

namespace App\Application\Accounting\ChartOfAccount\Queries\GetChartOfAccountList;

use App\Infrastructure\Accounting\ChartOfAccount\Persistence\Eloquent\ChartOfAccountQueryRepository;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Handler ini sengaja bergantung LANGSUNG pada implementasi konkret
 * ChartOfAccountQueryRepository (bukan interface).
 *
 * Alasan: sisi read tidak memiliki domain behavior dan saat ini hanya ada
 * satu sumber data (Eloquent). Menambahkan interface di sini hanya jadi
 * abstraction tanpa tujuan. Interface untuk read-side baru dibuat kalau
 * memang ada kebutuhan sumber data kedua (mis. Elasticsearch, read-replica
 * dengan query berbeda, dsb).
 */
final class GetChartOfAccountListHandler
{
    public function __construct(
        private readonly ChartOfAccountQueryRepository $queryRepository,
    ) {}

    public function __invoke(GetChartOfAccountListQuery $query): LengthAwarePaginator
    {
        return $this->queryRepository->paginate(
            search:        $query->search,
            sortBy:        $query->sortBy,
            sortDirection: $query->sortDirection,
            perPage:       $query->perPage,
            page:          $query->page,
        );
    }
}
