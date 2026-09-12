<?php

declare(strict_types=1);

namespace Tests\Feature\Accounting\ChartOfAccount;

use App\Infrastructure\Accounting\ChartOfAccount\Persistence\Eloquent\Models\ChartOfAccountModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test end-to-end: Route -> Controller -> Application
 * (Action/Query) -> Infrastructure -> Database. Sengaja tidak meng-
 * ge-mock layer mana pun, supaya wiring antar layer benar-benar teruji.
 *
 * Middleware 'auth.jwt' dimatikan di sini karena suite ini fokus
 * menguji CRUD module ChartOfAccount, bukan alur autentikasi (yang punya
 * test tersendiri di Tests\Feature\Auth). Pastikan phpunit.xml
 * memakai koneksi database testing (mis. sqlite in-memory) supaya
 * RefreshDatabase tidak menyentuh database development.
 */
final class ChartOfAccountApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_it_can_list_chart_of_accounts(): void
    {
        ChartOfAccountModel::query()->create(['name' => 'Item Satu']);
        ChartOfAccountModel::query()->create(['name' => 'Item Dua']);

        $response = $this->getJson('/api/v1/accounting/chart-of-accounts');

        $response->assertOk();
        $response->assertJsonCount(2, 'data.data');
    }

    public function test_it_can_create_a_new_record(): void
    {
        $response = $this->postJson('/api/v1/accounting/chart-of-accounts', [
            'name' => 'Contoh ChartOfAccount',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('chart_of_accounts', ['name' => 'Contoh ChartOfAccount']);
    }

    public function test_it_rejects_creation_with_an_empty_name(): void
    {
        $response = $this->postJson('/api/v1/accounting/chart-of-accounts', ['name' => '']);

        $response->assertStatus(422);
    }

    public function test_it_can_show_a_single_record(): void
    {
        $record = ChartOfAccountModel::query()->create(['name' => 'Contoh ChartOfAccount']);

        $response = $this->getJson("/api/v1/accounting/chart-of-accounts/{$record->id}");

        $response->assertOk();
        $response->assertJsonPath('data.name', 'Contoh ChartOfAccount');
    }

    public function test_it_returns_404_when_chart_of_accounts_not_found(): void
    {
        $response = $this->getJson('/api/v1/accounting/chart-of-accounts/999999');

        $response->assertStatus(404);
    }

    public function test_it_can_update_an_existing_record(): void
    {
        $record = ChartOfAccountModel::query()->create(['name' => 'Nama Lama']);

        $response = $this->putJson("/api/v1/accounting/chart-of-accounts/{$record->id}", [
            'name' => 'Nama Baru',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('chart_of_accounts', ['id' => $record->id, 'name' => 'Nama Baru']);
    }

    public function test_it_can_delete_an_existing_record(): void
    {
        $record = ChartOfAccountModel::query()->create(['name' => 'Akan Dihapus']);

        $response = $this->deleteJson("/api/v1/accounting/chart-of-accounts/{$record->id}");

        $response->assertOk();
        $this->assertSoftDeleted('chart_of_accounts', ['id' => $record->id]);
    }
}
