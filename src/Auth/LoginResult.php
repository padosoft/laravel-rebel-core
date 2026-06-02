<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Esito di un login andato a buon fine. Due "forme":
 *
 *  - WEB:    sessione + cookie (lo stato è nella sessione Laravel) → tokenPair() è null;
 *  - MOBILE: nessuna sessione, si emette una TokenPair (Sanctum access+refresh).
 *
 * L'azione di verify (es. email-OTP) decide la forma in base al guard/canale e
 * ritorna questo oggetto; il chiamante fa il branch con isWeb()/isMobile().
 *
 *   $result = RebelEmailOtp::verify(...);
 *   if ($result->isMobile()) { return response()->json($result->tokenPair()); }
 */
final readonly class LoginResult
{
    private function __construct(
        public Authenticatable $user,
        public bool $web,
        public ?TokenPair $tokens = null,
    ) {}

    public static function web(Authenticatable $user): self
    {
        return new self($user, true);
    }

    public static function token(Authenticatable $user, TokenPair $tokens): self
    {
        return new self($user, false, $tokens);
    }

    public function isWeb(): bool
    {
        return $this->web;
    }

    public function isMobile(): bool
    {
        return ! $this->web;
    }

    public function tokenPair(): ?TokenPair
    {
        return $this->tokens;
    }
}
