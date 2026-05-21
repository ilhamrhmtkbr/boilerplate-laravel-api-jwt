<?php

declare(strict_types=1);

namespace App\Infrastructure\Accounting\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClosingEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'closing_entries';

    protected $fillable = ['name'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at?->translatedFormat('l, d F Y H:i')
        );
    }

    protected function updatedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->updated_at?->translatedFormat('l, d F Y H:i')
        );
    }
}
