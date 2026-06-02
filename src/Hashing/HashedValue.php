<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Hashing;

/**
 * Risultato di un hashing "keyed": l'hash + la versione di pepper usata.
 *
 * La `keyVersion` va salvata accanto all'hash così, quando si ruota il pepper,
 * si sa con quale versione ricalcolare/verificare (vedi KeyedHasher).
 */
final readonly class HashedValue
{
    public function __construct(
        public string $hash,
        public int $keyVersion,
    ) {}
}
