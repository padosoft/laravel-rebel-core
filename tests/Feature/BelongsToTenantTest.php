<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Padosoft\Rebel\Core\Concerns\BelongsToTenant;
use Padosoft\Rebel\Core\Tenancy\CurrentTenant;

/**
 * Modello di prova solo per testare il trait.
 *
 * @property string|null $tenant_id
 * @property string $name
 */
class TenantTestModel extends Model
{
    use BelongsToTenant;

    protected $table = 'rebel_tenant_test';

    public $timestamps = false;

    protected $guarded = [];
}

beforeEach(function (): void {
    Schema::create('rebel_tenant_test', function (Blueprint $table): void {
        $table->id();
        $table->string('tenant_id')->nullable();
        $table->string('name');
    });
});

it('scopes reads and stamps tenant_id on create from CurrentTenant', function (): void {
    $current = app(CurrentTenant::class);

    $current->set('tenant-A');
    TenantTestModel::create(['name' => 'a']);

    $current->set('tenant-B');
    TenantTestModel::create(['name' => 'b']);

    // Come tenant-B vedo solo i miei record.
    expect(TenantTestModel::count())->toBe(1)
        ->and(TenantTestModel::first()?->getAttribute('tenant_id'))->toBe('tenant-B');

    // Come tenant-A vedo solo i miei.
    $current->set('tenant-A');
    expect(TenantTestModel::count())->toBe(1)
        ->and(TenantTestModel::first()?->getAttribute('name'))->toBe('a');

    // Senza tenant corrente: nessun filtro, vedo tutto.
    $current->set(null);
    expect(TenantTestModel::count())->toBe(2);
});
