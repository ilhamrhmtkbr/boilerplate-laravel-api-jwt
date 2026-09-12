<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Accounting\ChartOfAccount;

use App\Domain\Accounting\ChartOfAccount\Entities\ChartOfAccount;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Unit test murni untuk Domain Entity ChartOfAccount.
 *
 * SENGAJA extends PHPUnit\Framework\TestCase (bukan Tests\TestCase milik
 * Laravel) dan TIDAK butuh database/HTTP kernel apa pun — ini sekaligus
 * bukti bahwa Domain benar-benar independen dari framework. Kalau suatu
 * saat test ini terpaksa butuh boot Laravel, itu tanda ada kebocoran
 * dependency ke Domain layer.
 */
final class ChartOfAccountTest extends TestCase
{
    public function test_it_can_be_created_with_a_valid_name(): void
    {
        $chartOfAccount = ChartOfAccount::create(name: 'Contoh ChartOfAccount');

        $this->assertNull($chartOfAccount->id());
        $this->assertSame('Contoh ChartOfAccount', $chartOfAccount->name());
    }

    public function test_it_cannot_be_created_with_an_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ChartOfAccount::create(name: '');
    }

    public function test_it_can_be_renamed_with_a_valid_name(): void
    {
        $chartOfAccount = ChartOfAccount::create(name: 'Nama Awal');

        $chartOfAccount->rename('Nama Baru');

        $this->assertSame('Nama Baru', $chartOfAccount->name());
    }

    public function test_it_cannot_be_renamed_with_an_empty_name(): void
    {
        $chartOfAccount = ChartOfAccount::create(name: 'Nama Awal');

        $this->expectException(InvalidArgumentException::class);

        $chartOfAccount->rename('');
    }
}
