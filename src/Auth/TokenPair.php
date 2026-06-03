<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Auth;

/**
 * Token pair for headless/mobile clients (Laravel Sanctum + internal extension):
 * a short-lived ("session") access token + a longer-lived refresh token.
 *
 *  - accessToken:  bearer to use in API calls;
 *  - refreshToken: to obtain a new access token (with rotation on the sessions side);
 *  - expiresIn:    access token validity in seconds;
 *  - tokenType:    scheme, usually "Bearer".
 */
final readonly class TokenPair
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
        public int $expiresIn,
        public string $tokenType = 'Bearer',
    ) {}
}
