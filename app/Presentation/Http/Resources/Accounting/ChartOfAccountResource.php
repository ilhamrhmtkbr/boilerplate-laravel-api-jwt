<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Accounting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Membungkus Domain Entity (App\Domain\Accounting\ChartOfAccount\Entities\ChartOfAccount) lewat
 * method toArray() bawaan entity — bukan lewat properti Eloquent.
 */
class ChartOfAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
