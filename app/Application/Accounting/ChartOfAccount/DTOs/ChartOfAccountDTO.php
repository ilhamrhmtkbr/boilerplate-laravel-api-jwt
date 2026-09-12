<?php

declare(strict_types=1);

namespace App\Application\Accounting\ChartOfAccount\DTOs;

/**
 * DTO ini murni data transport dari Presentation -> Application.
 * BUKAN tempat validasi domain (itu tugas Entity) dan BUKAN tempat
 * validasi HTTP (itu tugas FormRequest).
 */
final class ChartOfAccountDTO
{
    public function __construct(
        public readonly string $name,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(name: $data['name'] ?? '');
    }
}
