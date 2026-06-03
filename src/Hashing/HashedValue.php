<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Hashing;

/**
 * Result of a "keyed" hashing: the hash + the pepper version used.
 *
 * The `keyVersion` must be stored alongside the hash so that, when the pepper is
 * rotated, you know which version to recompute/verify with (see KeyedHasher).
 */
final readonly class HashedValue
{
    public function __construct(
        public string $hash,
        public int $keyVersion,
    ) {}
}
