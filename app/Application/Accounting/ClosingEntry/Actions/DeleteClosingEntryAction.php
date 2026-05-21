<?php

declare(strict_types=1);

namespace App\Application\Accounting\ClosingEntry\Actions;

use App\Domain\Accounting\Services\ClosingEntryDomainService;

final class DeleteClosingEntryAction
{
    public function __construct(private readonly ClosingEntryDomainService $domainService) {}

    public function __invoke(int $id): bool
    {
        return $this->domainService->delete($id);
    }
}
