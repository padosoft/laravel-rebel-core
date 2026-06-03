<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Tenancy;

/**
 * Holder of the current tenant (singleton). Set by the app's TenantResolver/
 * middleware; read by the BelongsToTenant global scope to isolate data per tenant.
 * Null = no tenant (single-tenant, CLI, job).
 */
final class CurrentTenant
{
    private ?string $id = null;

    public function id(): ?string
    {
        return $this->id;
    }

    public function set(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * Resets the current tenant. ESSENTIAL in long-running queue workers: the
     * provider invokes it at the start of every job to prevent a job from inheriting
     * the previous one's tenant (cross-tenant leakage).
     */
    public function reset(): void
    {
        $this->id = null;
    }
}
