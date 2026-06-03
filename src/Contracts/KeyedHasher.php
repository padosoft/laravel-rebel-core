<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Padosoft\Rebel\Core\Hashing\HashedValue;

/**
 * "Keyed" hashing (HMAC with a server-side secret pepper) with key rotation.
 *
 * Why not a plain hash? A "bare" hash of a low-entropy value (IP, 6-digit OTP,
 * email) is easily reversible/brute-forceable. With HMAC + a secret pepper, the
 * value cannot be reconstructed or forged without the pepper. The `keyVersion`
 * allows rotating the pepper without breaking already-stored hashes.
 */
interface KeyedHasher
{
    /** Computes the HMAC of the value with the ACTIVE pepper version. */
    public function hash(string $value): HashedValue;

    /**
     * Constant-time comparison between $value and a $hash produced with $keyVersion.
     * Returns false (without exceptions) if that version is not configured.
     */
    public function matches(string $value, string $hash, int $keyVersion): bool;
}
