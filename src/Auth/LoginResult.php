<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Outcome of a successful login. Two "shapes":
 *
 *  - WEB:    session + cookie (state lives in the Laravel session) → tokenPair() is null;
 *  - MOBILE: no session, a TokenPair is issued (Sanctum access+refresh).
 *
 * The verify action (e.g. email-OTP) decides the shape based on the guard/channel
 * and returns this object; the caller branches with isWeb()/isMobile().
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
