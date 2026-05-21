<?php

declare(strict_types=1);

namespace App\Application\Accounting\TrialBalance\Actions;

use App\Domain\Accounting\Entities\TrialBalance;
use App\Domain\Accounting\Services\TrialBalanceDomainService;

final class GetTrialBalanceByIdAction
{
    public function __construct(private readonly TrialBalanceDomainService $domainService) {}

    public function __invoke(int $id): TrialBalance
    {
        return $this->domainService->getById($id);
    }
}
