<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources\Accounting;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralLedgerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = is_object($this->resource) && method_exists($this->resource, 'toArray')
            ? $this->resource->toArray()
            : (array) $this->resource;

        return [
            'id'         => $data['id'] ?? null,
            'name'       => $data['name'] ?? null,
            'created_at' => isset($data['created_at'])
                ? Carbon::parse($data['created_at'])->translatedFormat('l, d F Y H:i')
                : null,
            'updated_at' => isset($data['updated_at'])
                ? Carbon::parse($data['updated_at'])->translatedFormat('l, d F Y H:i')
                : null,
        ];
    }
}
