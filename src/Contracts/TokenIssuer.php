<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Padosoft\Rebel\Core\Auth\TokenPair;
use Padosoft\Rebel\Core\Context\SecurityContext;

/**
 * Issues tokens for headless/mobile clients. It is a WRAPPER around the internal
 * Sanctum extension ("session" access token + refresh token): the core does not
 * know the details, it only requests issuance. The token should carry the
 * tenant_id claim.
 */
interface TokenIssuer
{
    public function issue(Authenticatable $user, SecurityContext $context): TokenPair;

    /** Revokes all of the subject's tokens ("logout everywhere"). Returns how many were revoked. */
    public function revokeAll(Authenticatable $user): int;
}
