<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

/**
 * Multidimensional rate limiter (per-identifier / per-IP / per-tenant / global /
 * per-phone / per-prefix). A minimal contract inspired by Laravel's but designed
 * for composite keys and backoff. Concrete implementation (Redis) lives elsewhere.
 */
interface RateLimiter
{
    /** True if the maximum attempts for the key have been exceeded. */
    public function tooManyAttempts(string $key, int $maxAttempts): bool;

    /** Records an attempt; returns the current count for the key. */
    public function hit(string $key, int $decaySeconds): int;

    /** Resets the attempts for the key (e.g. after a success). */
    public function clear(string $key): void;

    /** Seconds remaining before the key becomes available again (0 if already available). */
    public function availableIn(string $key): int;
}
