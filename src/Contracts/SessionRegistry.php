<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Registro di sessioni/dispositivi del subject. Implementato dal package sessions.
 * Qui nel core c'è solo il contratto stabile usato da OTP/step-up.
 */
interface SessionRegistry
{
    /** Revoca tutte le sessioni/token del subject. Ritorna quante ne ha revocate. */
    public function revokeAll(Authenticatable $user): int;

    /**
     * True se questo refresh token risulta GIÀ consumato (reuse detection):
     * un riuso indica furto del token → la catena va revocata.
     */
    public function isRefreshReused(string $refreshTokenId): bool;
}
