<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Padosoft\Rebel\Core\Auth\TokenPair;
use Padosoft\Rebel\Core\Context\SecurityContext;

/**
 * Emette i token per i client headless/mobile. È un WRAPPER attorno all'estensione
 * Sanctum interna (access "session" token + refresh token): il core non conosce i
 * dettagli, chiede solo l'emissione. Il token dovrebbe portare il claim tenant_id.
 */
interface TokenIssuer
{
    public function issue(Authenticatable $user, SecurityContext $context): TokenPair;

    /** Revoca tutti i token del subject ("logout everywhere"). Ritorna quanti revocati. */
    public function revokeAll(Authenticatable $user): int;
}
