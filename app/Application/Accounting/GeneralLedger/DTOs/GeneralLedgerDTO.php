<?php

declare(strict_types=1);

namespace App\Application\Accounting\GeneralLedger\DTOs;

final class GeneralLedgerDTO
{
    public function __construct(
        public readonly string $name,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(name: $data['name'] ?? '');
    }

    public function toArray(): array
    {
        return ['name' => $this->name];
    }
}
