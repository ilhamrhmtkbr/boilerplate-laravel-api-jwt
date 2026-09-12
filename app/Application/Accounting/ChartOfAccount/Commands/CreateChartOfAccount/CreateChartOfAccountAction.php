<?php

declare(strict_types=1);

namespace App\Application\Accounting\ChartOfAccount\Commands\CreateChartOfAccount;

use App\Application\Accounting\ChartOfAccount\DTOs\ChartOfAccountDTO;
use App\Domain\Accounting\ChartOfAccount\Entities\ChartOfAccount;
use App\Domain\Accounting\ChartOfAccount\Repositories\ChartOfAccountRepositoryInterface;

final class CreateChartOfAccountAction
{
    public function __construct(
        private readonly ChartOfAccountRepositoryInterface $repository,
    ) {}

    public function __invoke(ChartOfAccountDTO $dto): ChartOfAccount
    {
        $chartOfAccount = ChartOfAccount::create(name: $dto->name);

        return $this->repository->save($chartOfAccount);
    }
}
