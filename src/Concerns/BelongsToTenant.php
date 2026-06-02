<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Padosoft\Rebel\Core\Tenancy\CurrentTenant;

/**
 * Isola i record per tenant. I modelli Rebel che usano questo trait:
 *  - in lettura vengono filtrati per il tenant corrente (global scope);
 *  - in creazione ricevono automaticamente il tenant_id corrente, se non già impostato.
 *
 * Se non c'è un tenant corrente (single-tenant/CLI), nessun filtro viene applicato.
 *
 * @mixin Model
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('rebel_tenant', function (Builder $builder): void {
            $tenantId = app(CurrentTenant::class)->id();

            if ($tenantId !== null) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', $tenantId);
            }
        });

        static::creating(function (Model $model): void {
            $tenantId = app(CurrentTenant::class)->id();

            if ($tenantId !== null && $model->getAttribute('tenant_id') === null) {
                $model->setAttribute('tenant_id', $tenantId);
            }
        });
    }
}
