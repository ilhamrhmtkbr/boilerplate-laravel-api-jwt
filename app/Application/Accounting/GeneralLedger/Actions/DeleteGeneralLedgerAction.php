<?php

declare(strict_types=1);

namespace App\Application\Accounting\GeneralLedger\Actions;

use App\Domain\Accounting\Services\GeneralLedgerDomainService;

final class DeleteGeneralLedgerAction
{
    public function __construct(private readonly GeneralLedgerDomainService $domainService) {}

    public function __invoke(int $id): bool
    {
        return $this->domainService->delete($id);
    }
}
