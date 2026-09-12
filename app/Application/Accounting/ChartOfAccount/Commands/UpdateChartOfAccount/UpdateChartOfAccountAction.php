<?php

declare(strict_types=1);

namespace App\Application\Accounting\ChartOfAccount\Commands\UpdateChartOfAccount;

use App\Application\Accounting\ChartOfAccount\DTOs\ChartOfAccountDTO;
use App\Domain\Accounting\ChartOfAccount\Entities\ChartOfAccount;
use App\Domain\Accounting\ChartOfAccount\Repositories\ChartOfAccountRepositoryInterface;

final class UpdateChartOfAccountAction
{
    public function __construct(
        private readonly ChartOfAccountRepositoryInterface $repository,
    ) {}

    public function __invoke(int $id, ChartOfAccountDTO $dto): ChartOfAccount
    {
        $chartOfAccount = $this->repository->findOrFail($id);

        // Behavior dipanggil lewat method entity (menjaga invariant),
        // bukan lewat overwrite property langsung.
        $chartOfAccount->rename($dto->name);

        return $this->repository->save($chartOfAccount);
    }
}
