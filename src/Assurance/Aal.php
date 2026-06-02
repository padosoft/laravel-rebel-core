<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Assurance;

/**
 * Authenticator Assurance Level (NIST SP 800-63B-4).
 *
 *  - AAL1: un solo fattore (anche un solo OTP). Es. login email-OTP B2C.
 *  - AAL2: due fattori distinti e deve offrire un'opzione phishing-resistant.
 *  - AAL3: chiave hardware, solo phishing-resistant.
 *
 * Glossario: "phishing-resistant" = non rigiocabile su un sito di phishing
 * (solo passkey/FIDO2; email-OTP e SMS NON lo sono).
 */
enum Aal: string
{
    case Aal1 = 'aal1';
    case Aal2 = 'aal2';
    case Aal3 = 'aal3';

    /** Rango numerico per i confronti (più alto = più forte). */
    public function rank(): int
    {
        return match ($this) {
            self::Aal1 => 1,
            self::Aal2 => 2,
            self::Aal3 => 3,
        };
    }

    /** True se questo livello soddisfa (>=) il livello richiesto. */
    public function satisfies(self $required): bool
    {
        return $this->rank() >= $required->rank();
    }
}
