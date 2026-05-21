<?php

declare(strict_types=1);

namespace App\Application\Accounting\ChartOfAccount\Actions;

use App\Domain\Accounting\Entities\ChartOfAccount;
use App\Domain\Accounting\Services\ChartOfAccountDomainService;

final class GetChartOfAccountByIdAction
{
    public function __construct(private readonly ChartOfAccountDomainService $domainService) {}

    public function __invoke(int $id): ChartOfAccount
    {
        return $this->domainService->getById($id);
    }
}
