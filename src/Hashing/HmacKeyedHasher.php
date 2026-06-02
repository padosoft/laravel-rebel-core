<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Hashing;

use InvalidArgumentException;
use Padosoft\Rebel\Core\Contracts\KeyedHasher;

/**
 * Implementazione HMAC del KeyedHasher con registro di pepper versionati.
 *
 * Esempio:
 *   $hasher = new HmacKeyedHasher(peppers: [1 => 'segreto-v1'], currentVersion: 1);
 *   $h = $hasher->hash('mario.rossi@example.it');   // HashedValue(hash, keyVersion: 1)
 *   $hasher->matches('mario.rossi@example.it', $h->hash, $h->keyVersion); // true
 *
 * Rotazione: aggiungi una nuova versione e imposta currentVersion al nuovo numero.
 * I nuovi hash usano la nuova versione; i vecchi restano verificabili finché la
 * loro versione resta nel registro.
 */
final class HmacKeyedHasher implements KeyedHasher
{
    /**
     * @param  array<int, string>  $peppers  mappa versione => pepper segreto
     */
    public function __construct(
        private readonly array $peppers,
        private readonly int $currentVersion,
        private readonly string $algo = 'sha256',
    ) {}

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
