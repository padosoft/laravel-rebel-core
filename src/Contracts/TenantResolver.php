<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Illuminate\Http\Request;
use Padosoft\Rebel\Core\Context\TenantContext;

/**
 * Determina il tenant corrente dalla richiesta (es. dominio/sito/brand).
 * Ritorna null in contesti single-tenant o CLI/job senza tenant.
 */
interface TenantResolver
{
    public function resolve(Request $request): ?TenantContext;
}
