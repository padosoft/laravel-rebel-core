<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Tenancy;

/**
 * Holder del tenant corrente (singleton). Lo imposta il TenantResolver/middleware
 * dell'app; lo legge il global scope BelongsToTenant per isolare i dati per tenant.
 * Null = nessun tenant (single-tenant, CLI, job).
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
     * Azzera il tenant corrente. FONDAMENTALE nei worker di coda long-running:
     * il provider lo invoca all'inizio di ogni job per evitare che un job erediti
     * il tenant di quello precedente (cross-tenant leakage).
     */
    public function reset(): void
    {
        $this->id = null;
    }
}
