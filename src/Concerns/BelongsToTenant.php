<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Padosoft\Rebel\Core\Tenancy\CurrentTenant;

/**
 * Isolates records per tenant. Rebel models using this trait:
 *  - on read are filtered by the current tenant (global scope);
 *  - on create automatically receive the current tenant_id, if not already set.
 *
 * If there is no current tenant (single-tenant/CLI), no filter is applied.
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
