<?php

declare(strict_types=1);

namespace App\Application\Accounting\GeneralLedger\Actions;

use App\Domain\Accounting\Entities\GeneralLedger;
use App\Domain\Accounting\Services\GeneralLedgerDomainService;

final class GetGeneralLedgerByIdAction
{
    public function __construct(private readonly GeneralLedgerDomainService $domainService) {}

    public function __invoke(int $id): GeneralLedger
    {
        return $this->domainService->getById($id);
    }
}
