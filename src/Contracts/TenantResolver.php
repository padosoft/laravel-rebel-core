<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Illuminate\Http\Request;
use Padosoft\Rebel\Core\Context\TenantContext;

/**
 * Determines the current tenant from the request (e.g. domain/site/brand).
 * Returns null in single-tenant contexts or in CLI/jobs without a tenant.
 */
interface TenantResolver
{
    public function resolve(Request $request): ?TenantContext;
}
