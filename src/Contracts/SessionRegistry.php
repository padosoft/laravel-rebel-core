<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Registry of the subject's sessions/devices. Implemented by the sessions package.
 * Here in the core there is only the stable contract used by OTP/step-up.
 */
interface SessionRegistry
{
    /** Revokes all of the subject's sessions/tokens. Returns how many were revoked. */
    public function revokeAll(Authenticatable $user): int;

    /**
     * True if this refresh token has ALREADY been consumed (reuse detection):
     * a reuse indicates token theft → the chain must be revoked.
     */
    public function isRefreshReused(string $refreshTokenId): bool;
}
