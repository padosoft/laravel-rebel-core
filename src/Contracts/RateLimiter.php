<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

/**
 * Rate limiter multidimensionale (per-identifier / per-IP / per-tenant / globale /
 * per-phone / per-prefix). Contratto minimale ispirato a quello di Laravel ma
 * pensato per chiavi composite e backoff. Implementazione concreta (Redis) altrove.
 */
interface RateLimiter
{
    /** True se sono stati superati i tentativi massimi per la chiave. */
    public function tooManyAttempts(string $key, int $maxAttempts): bool;

    /** Registra un tentativo; ritorna il conteggio attuale per la chiave. */
    public function hit(string $key, int $decaySeconds): int;

    /** Azzera i tentativi per la chiave (es. dopo un successo). */
    public function clear(string $key): void;

    /** Secondi mancanti prima che la chiave torni disponibile (0 se già disponibile). */
    public function availableIn(string $key): int;
}
