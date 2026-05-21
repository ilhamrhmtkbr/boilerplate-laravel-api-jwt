<?php

declare(strict_types=1);

namespace App\Application\Accounting\GeneralLedger\Actions;

use App\Domain\Accounting\Services\GeneralLedgerDomainService;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetGeneralLedgerListAction
{
    public function __construct(private readonly GeneralLedgerDomainService $domainService) {}

    public function __invoke(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->domainService->getList($filters, $perPage);
    }
}
