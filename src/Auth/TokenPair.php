<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Auth;

/**
 * Coppia di token per i client headless/mobile (Laravel Sanctum + estensione interna):
 * un access token ("session") a vita breve + un refresh token a vita più lunga.
 *
 *  - accessToken:  bearer da usare nelle chiamate API;
 *  - refreshToken: per ottenere un nuovo access token (con rotation lato sessions);
 *  - expiresIn:    secondi di validità dell'access token;
 *  - tokenType:    schema, di norma "Bearer".
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
