<?php

declare(strict_types=1);

namespace App\Application\Accounting\ChartOfAccount\Actions;

use App\Domain\Accounting\Services\ChartOfAccountDomainService;

final class DeleteChartOfAccountAction
{
    public function __construct(private readonly ChartOfAccountDomainService $domainService) {}

    public function __invoke(int $id): bool
    {
        return $this->domainService->delete($id);
    }
}
