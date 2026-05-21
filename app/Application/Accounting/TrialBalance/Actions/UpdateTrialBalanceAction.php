<?php

declare(strict_types=1);

namespace App\Application\Accounting\TrialBalance\Actions;

use App\Application\Accounting\TrialBalance\DTOs\TrialBalanceDTO;
use App\Domain\Accounting\Entities\TrialBalance;
use App\Domain\Accounting\Services\TrialBalanceDomainService;

final class UpdateTrialBalanceAction
{
    public function __construct(private readonly TrialBalanceDomainService $domainService) {}

    public function __invoke(int $id, TrialBalanceDTO $dto): TrialBalance
    {
        return $this->domainService->update($id, $dto->toArray());
    }
}
