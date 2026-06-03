<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Hashing;

use InvalidArgumentException;
use Padosoft\Rebel\Core\Contracts\KeyedHasher;

/**
 * HMAC implementation of the KeyedHasher with a registry of versioned peppers.
 *
 * Example:
 *   $hasher = new HmacKeyedHasher(peppers: [1 => 'secret-v1'], currentVersion: 1);
 *   $h = $hasher->hash('mario.rossi@example.it');   // HashedValue(hash, keyVersion: 1)
 *   $hasher->matches('mario.rossi@example.it', $h->hash, $h->keyVersion); // true
 *
 * Rotation: add a new version and set currentVersion to the new number. New hashes
 * use the new version; old ones remain verifiable as long as their version stays
 * in the registry.
 */
final class HmacKeyedHasher implements KeyedHasher
{
    /**
     * @param  array<int, string>  $peppers  map of version => secret pepper
     */
    public function __construct(
        private readonly array $peppers,
        private readonly int $currentVersion,
        private readonly string $algo = 'sha256',
    ) {
        if (! in_array($this->algo, hash_hmac_algos(), true)) {
            throw new InvalidArgumentException(
                "Algoritmo HMAC non supportato: '{$this->algo}'. Usa uno tra: ".implode(', ', hash_hmac_algos()).'.'
            );
        }
    }

    public function hash(string $value): HashedValue
    {
        return new HashedValue(
            hash: $this->compute($value, $this->currentVersion),
            keyVersion: $this->currentVersion,
        );
    }

    public function matches(string $value, string $hash, int $keyVersion): bool
    {
        $pepper = $this->peppers[$keyVersion] ?? null;

        // Timing note: the early return here is NOT an oracle. The keyVersion is not
        // secret — it is stored in cleartext next to the hash — so distinguishing
        // "missing version" from "different hash" reveals nothing to an attacker.
        if ($pepper === null || $pepper === '') {
            return false;
        }

        return hash_equals($this->compute($value, $keyVersion), $hash);
    }

    private function compute(string $value, int $version): string
    {
        $pepper = $this->peppers[$version] ?? null;

        if ($pepper === null) {
            throw new InvalidArgumentException("Pepper versione {$version} non configurata.");
        }

        if ($pepper === '') {
            throw new InvalidArgumentException(
                "Pepper versione {$version} vuota: imposta REBEL_PEPPER_V{$version} nel .env."
            );
        }

        return hash_hmac($this->algo, $value, $pepper);
    }
}
