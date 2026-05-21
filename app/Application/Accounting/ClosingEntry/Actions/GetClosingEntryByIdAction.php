<?php

declare(strict_types=1);

namespace App\Application\Accounting\ClosingEntry\Actions;

use App\Domain\Accounting\Entities\ClosingEntry;
use App\Domain\Accounting\Services\ClosingEntryDomainService;

final class GetClosingEntryByIdAction
{
    public function __construct(private readonly ClosingEntryDomainService $domainService) {}

    public function __invoke(int $id): ClosingEntry
    {
        return $this->domainService->getById($id);
    }
}
