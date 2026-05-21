<?php

declare(strict_types=1);

namespace App\Application\Accounting\ClosingEntry\Actions;

use App\Domain\Accounting\Services\ClosingEntryDomainService;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetClosingEntryListAction
{
    public function __construct(private readonly ClosingEntryDomainService $domainService) {}

    public function __invoke(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->domainService->getList($filters, $perPage);
    }
}
