<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Padosoft\Rebel\Core\Concerns\BelongsToTenant;
use Padosoft\Rebel\Core\Tenancy\CurrentTenant;

/**
 * Throwaway model just to test the trait.
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

    // As tenant-B I only see my own records.
    expect(TenantTestModel::count())->toBe(1)
        ->and(TenantTestModel::first()?->getAttribute('tenant_id'))->toBe('tenant-B');

    // As tenant-A I only see mine.
    $current->set('tenant-A');
    expect(TenantTestModel::count())->toBe(1)
        ->and(TenantTestModel::first()?->getAttribute('name'))->toBe('a');

    // Without a current tenant: no filter, I see everything.
    $current->set(null);
    expect(TenantTestModel::count())->toBe(2);
});
