<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Contracts;

use Padosoft\Rebel\Core\Context\SecurityContext;

/**
 * Gate anti-bot/CAPTCHA (Turnstile/hCaptcha/Arkose) da invocare PRIMA di inviare
 * un OTP/SMS su richieste a rischio (nuovo device, geo non domestica, alta velocità).
 */
interface BotProtection
{
    /** True se la richiesta supera il controllo (token CAPTCHA valido / non bot). */
    public function passes(SecurityContext $context, ?string $token): bool;
}
