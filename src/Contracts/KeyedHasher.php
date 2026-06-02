<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Padosoft\Rebel\Core\Hashing\HashedValue;

/**
 * Hashing "keyed" (HMAC con pepper segreto lato server) con rotazione delle chiavi.
 *
 * Perché non un semplice hash? Un hash "nudo" di un valore a bassa entropia
 * (IP, OTP a 6 cifre, email) è facilmente reversibile/brute-forzabile. Con
 * l'HMAC + pepper segreto, senza il pepper non si può ricostruire né forgiare
 * il valore. La `keyVersion` permette di ruotare il pepper senza rompere gli
 * hash già salvati.
 */
interface KeyedHasher
{
    /** Calcola l'HMAC del valore con la versione di pepper ATTIVA. */
    public function hash(string $value): HashedValue;

    /**
     * Confronto a tempo costante tra $value e un $hash prodotto con $keyVersion.
     * Ritorna false (senza eccezioni) se quella versione non è configurata.
     */
    public function matches(string $value, string $hash, int $keyVersion): bool;
}
