<?php

declare(strict_types=1);

namespace App\Application\Accounting\TrialBalance\Actions;

use App\Domain\Accounting\Services\TrialBalanceDomainService;

final class DeleteTrialBalanceAction
{
    public function __construct(private readonly TrialBalanceDomainService $domainService) {}

    public function __invoke(int $id): bool
    {
        return $this->domainService->delete($id);
    }
}
