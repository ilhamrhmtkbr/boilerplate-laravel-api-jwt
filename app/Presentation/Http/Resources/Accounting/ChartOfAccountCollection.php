<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Accounting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Membungkus hasil paginate dari GetChartOfAccountListHandler, yang berisi baris
 * Eloquent (read-model) — karena itu $collects menunjuk ke
 * ChartOfAccountListItemResource, BUKAN ChartOfAccountResource.
 */
class ChartOfAccountCollection extends ResourceCollection
{
    public $collects = ChartOfAccountListItemResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data'  => $this->collection,
            'meta'  => [
                'current_page' => $this->resource->currentPage(),
                'per_page'     => $this->resource->perPage(),
                'total'        => $this->resource->total(),
                'last_page'    => $this->resource->lastPage(),
            ],
            'links' => [
                'first' => $this->resource->url(1),
                'last'  => $this->resource->url($this->resource->lastPage()),
                'prev'  => $this->resource->previousPageUrl(),
                'next'  => $this->resource->nextPageUrl(),
            ],
        ];
    }
}
