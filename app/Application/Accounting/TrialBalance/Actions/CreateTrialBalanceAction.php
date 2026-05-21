<?php

declare(strict_types=1);

namespace App\Application\Accounting\TrialBalance\Actions;

use App\Application\Accounting\TrialBalance\DTOs\TrialBalanceDTO;
use App\Domain\Accounting\Entities\TrialBalance;
use App\Domain\Accounting\Services\TrialBalanceDomainService;

final class CreateTrialBalanceAction
{
    public function __construct(private readonly TrialBalanceDomainService $domainService) {}

    public function __invoke(TrialBalanceDTO $dto): TrialBalance
    {
        return $this->domainService->create($dto->toArray());
    }
}
