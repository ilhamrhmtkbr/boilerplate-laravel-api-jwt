<?php

declare(strict_types=1);

namespace App\Application\Accounting\ChartOfAccount\Queries\GetChartOfAccountDetail;

use App\Domain\Accounting\ChartOfAccount\Entities\ChartOfAccount;
use App\Domain\Accounting\ChartOfAccount\Repositories\ChartOfAccountRepositoryInterface;

final class GetChartOfAccountDetailAction
{
    public function __construct(
        private readonly ChartOfAccountRepositoryInterface $repository,
    ) {}

    public function __invoke(int $id): ChartOfAccount
    {
        return $this->repository->findOrFail($id);
    }
}
