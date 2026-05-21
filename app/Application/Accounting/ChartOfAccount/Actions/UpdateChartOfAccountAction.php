<?php

declare(strict_types=1);

namespace App\Application\Accounting\ChartOfAccount\Actions;

use App\Application\Accounting\ChartOfAccount\DTOs\ChartOfAccountDTO;
use App\Domain\Accounting\Entities\ChartOfAccount;
use App\Domain\Accounting\Services\ChartOfAccountDomainService;

final class UpdateChartOfAccountAction
{
    public function __construct(private readonly ChartOfAccountDomainService $domainService) {}

    public function __invoke(int $id, ChartOfAccountDTO $dto): ChartOfAccount
    {
        return $this->domainService->update($id, $dto->toArray());
    }
}
