<?php

declare(strict_types=1);

namespace App\Application\Accounting\ChartOfAccount\Queries\GetChartOfAccountList;

final class GetChartOfAccountListQuery
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly string $sortBy = 'created_at',
        public readonly string $sortDirection = 'desc',
        public readonly int $perPage = 15,
        public readonly ?int $page = null,
    ) {}
}
