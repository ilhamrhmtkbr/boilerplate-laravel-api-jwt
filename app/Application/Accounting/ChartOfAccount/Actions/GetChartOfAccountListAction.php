<?php

declare(strict_types=1);

namespace App\Application\Accounting\ChartOfAccount\Actions;

use App\Domain\Accounting\Services\ChartOfAccountDomainService;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetChartOfAccountListAction
{
    public function __construct(private readonly ChartOfAccountDomainService $domainService) {}

    public function __invoke(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->domainService->getList($filters, $perPage);
    }
}
