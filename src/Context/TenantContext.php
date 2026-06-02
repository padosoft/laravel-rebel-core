<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Context;

/**
 * Il tenant corrente (es. sito/brand/nazione in un ecommerce multi-tenant).
 *
 * `id` è l'identificativo del tenant; `attributes` porta metadati utili a policy
 * e audit (es. ['nazione' => 'IT', 'brand' => 'acme']).
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
