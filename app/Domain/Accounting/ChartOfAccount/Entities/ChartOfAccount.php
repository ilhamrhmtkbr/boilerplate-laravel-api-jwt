<?php

declare(strict_types=1);

namespace App\Domain\Accounting\ChartOfAccount\Entities;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * ChartOfAccount — Domain Entity.
 *
 * Entity ini menjaga invariant yang SUDAH diketahui saat ini (name tidak
 * boleh kosong) dan mengekspos behavior lewat method (rename), bukan lewat
 * mutasi property publik secara bebas.
 *
 * TIDAK ditambahkan field/rule spekulatif (mis. status, kode, parent, dsb)
 * karena itu adalah domain rule nyata yang harus didefinisikan sesuai
 * kebutuhan bisnis ChartOfAccount, bukan dikarang saat generate skeleton.
 *
 * Extension point: tambahkan property/behavior/invariant baru di sini
 * begitu business rule ChartOfAccount sudah dikonfirmasi. Jangan taruh rule tersebut
 * di Application layer atau di Eloquent Model.
 */
final class ChartOfAccount
{
    private function __construct(
        private readonly ?int $id,
        private string $name,
        private readonly ?DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
    ) {}

    /**
     * Factory untuk entity baru (belum punya identity/persisted).
     */
    public static function create(string $name): self
    {
        self::assertNameIsValid($name);

        return new self(id: null, name: $name, createdAt: null, updatedAt: null);
    }

    /**
     * Rekonstruksi entity dari data persistence. Dipanggil oleh Repository,
     * BUKAN oleh Application layer secara langsung.
     */
    public static function reconstitute(array $data): self
    {
        return new self(
            id:        $data['id'],
            name:      $data['name'] ?? '',
            createdAt: isset($data['created_at']) ? new DateTimeImmutable($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new DateTimeImmutable($data['updated_at']) : null,
        );
    }

    /**
     * Contoh behavior nyata: mengubah nama tetap menjaga invariant,
     * bukan sekadar $entity->name = $x dari luar.
     */
    public function rename(string $newName): void
    {
        self::assertNameIsValid($newName);
        $this->name = $newName;
        $this->updatedAt = new DateTimeImmutable();
    }

    private static function assertNameIsValid(string $name): void
    {
        if (trim($name) === '') {
            throw new InvalidArgumentException('ChartOfAccount name cannot be empty.');
        }
    }

    public function id(): ?int { return $this->id; }

    public function name(): string { return $this->name; }

    public function createdAt(): ?DateTimeImmutable { return $this->createdAt; }

    public function updatedAt(): ?DateTimeImmutable { return $this->updatedAt; }

    /**
     * Proyeksi data untuk kebutuhan serialisasi (Resource/Export/PDF).
     * Ini bukan bagian dari business logic — murni data projection.
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
