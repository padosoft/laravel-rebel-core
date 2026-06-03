<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Context;

/**
 * The current tenant (e.g. site/brand/country in a multi-tenant ecommerce).
 *
 * `id` is the tenant identifier; `attributes` carries metadata useful for policies
 * and audit (e.g. ['country' => 'IT', 'brand' => 'acme']).
 */
final readonly class TenantContext
{
    /**
     * @param  array<string, scalar|null>  $attributes
     */
    public function __construct(
        public string $id,
        public array $attributes = [],
    ) {}

    public function attribute(string $key): string|int|float|bool|null
    {
        return $this->attributes[$key] ?? null;
    }
}
