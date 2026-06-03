<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Padosoft\Rebel\Core\Context\SecurityContext;

/**
 * Anti-bot/CAPTCHA gate (Turnstile/hCaptcha/Arkose) to invoke BEFORE sending an
 * OTP/SMS on risky requests (new device, non-domestic geo, high velocity).
 */
interface BotProtection
{
    /** True if the request passes the check (valid CAPTCHA token / not a bot). */
    public function passes(SecurityContext $context, ?string $token): bool;
}
