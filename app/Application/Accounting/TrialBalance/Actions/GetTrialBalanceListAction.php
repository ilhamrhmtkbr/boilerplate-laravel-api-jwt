<?php

declare(strict_types=1);

namespace App\Application\Accounting\TrialBalance\Actions;

use App\Domain\Accounting\Services\TrialBalanceDomainService;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetTrialBalanceListAction
{
    public function __construct(private readonly TrialBalanceDomainService $domainService) {}

    public function __invoke(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->domainService->getList($filters, $perPage);
    }
}
