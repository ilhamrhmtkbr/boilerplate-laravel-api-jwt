<?php

declare(strict_types=1);

namespace App\Infrastructure\Accounting\ChartOfAccount\Persistence\Eloquent;

use App\Infrastructure\Accounting\ChartOfAccount\Persistence\Eloquent\Models\ChartOfAccountModel;
use Illuminate\Pagination\LengthAwarePaginator;

final class ChartOfAccountQueryRepository
{
    private const ALLOWED_SORT_COLUMNS = ['id', 'name', 'created_at'];

    public function __construct(
        private readonly ChartOfAccountModel $model,
    ) {}

    public function paginate(
        ?string $search,
        string $sortBy,
        string $sortDirection,
        int $perPage,
        ?int $page,
    ): LengthAwarePaginator {
        $builder = $this->model->newQuery();

        if (!empty($search)) {
            $builder->where('name', 'like', '%' . $search . '%');
        }

        $sortColumn = in_array($sortBy, self::ALLOWED_SORT_COLUMNS, true) ? $sortBy : 'created_at';
        $direction  = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        $builder->orderBy($sortColumn, $direction);

        return $builder->paginate(
            perPage:  $perPage,
            pageName: 'page',
            page:     $page,
        );
    }
}
