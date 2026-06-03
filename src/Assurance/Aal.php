<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Assurance;

/**
 * Authenticator Assurance Level (NIST SP 800-63B-4).
 *
 *  - AAL1: a single factor (even just one OTP). E.g. B2C email-OTP login.
 *  - AAL2: two distinct factors and it must offer a phishing-resistant option.
 *  - AAL3: hardware key, phishing-resistant only.
 *
 * Glossary: "phishing-resistant" = not replayable on a phishing site
 * (only passkey/FIDO2; email-OTP and SMS are NOT).
 */
enum Aal: string
{
    case Aal1 = 'aal1';
    case Aal2 = 'aal2';
    case Aal3 = 'aal3';

    /** Numeric rank for comparisons (higher = stronger). */
    public function rank(): int
    {
        return match ($this) {
            self::Aal1 => 1,
            self::Aal2 => 2,
            self::Aal3 => 3,
        };
    }

    /** True if this level satisfies (>=) the required level. */
    public function satisfies(self $required): bool
    {
        return $this->rank() >= $required->rank();
    }
}
