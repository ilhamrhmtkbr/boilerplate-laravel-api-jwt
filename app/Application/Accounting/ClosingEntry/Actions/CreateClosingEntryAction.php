<?php

declare(strict_types=1);

namespace App\Application\Accounting\ClosingEntry\Actions;

use App\Application\Accounting\ClosingEntry\DTOs\ClosingEntryDTO;
use App\Domain\Accounting\Entities\ClosingEntry;
use App\Domain\Accounting\Services\ClosingEntryDomainService;

final class CreateClosingEntryAction
{
    public function __construct(private readonly ClosingEntryDomainService $domainService) {}

    public function __invoke(ClosingEntryDTO $dto): ClosingEntry
    {
        return $this->domainService->create($dto->toArray());
    }
}
