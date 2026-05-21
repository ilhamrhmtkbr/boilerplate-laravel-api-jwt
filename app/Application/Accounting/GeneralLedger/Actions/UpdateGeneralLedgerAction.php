<?php

declare(strict_types=1);

namespace App\Application\Accounting\GeneralLedger\Actions;

use App\Application\Accounting\GeneralLedger\DTOs\GeneralLedgerDTO;
use App\Domain\Accounting\Entities\GeneralLedger;
use App\Domain\Accounting\Services\GeneralLedgerDomainService;

final class UpdateGeneralLedgerAction
{
    public function __construct(private readonly GeneralLedgerDomainService $domainService) {}

    public function __invoke(int $id, GeneralLedgerDTO $dto): GeneralLedger
    {
        return $this->domainService->update($id, $dto->toArray());
    }
}
