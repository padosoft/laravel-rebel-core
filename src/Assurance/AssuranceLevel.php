<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Assurance;

/**
 * L'assurance prodotta da un driver di autenticazione/step-up, oppure richiesta
 * da un "purpose" (azione protetta).
 *
 *  - aal:               il livello NIST raggiunto/richiesto;
 *  - phishingResistant: true solo per passkey/FIDO2;
 *  - amr:               Authentication Methods References, es. ['webauthn'] | ['otp','email'] | ['sms'];
 *  - restricted:        true per autenticatori "restricted" (es. SMS) — vedi NIST.
 *
 * Esempio (driver passkey):
 *   new AssuranceLevel(Aal::Aal2, phishingResistant: true, amr: ['webauthn']);
 *
 * Il resolver step-up usa satisfies() per RIFIUTARE driver sotto la soglia del purpose.
 */
final readonly class AssuranceLevel
{
    /**
     * @param  list<string>  $amr
     */
    public function __construct(
        public Aal $aal,
        public bool $phishingResistant,
        public array $amr,
        public bool $restricted = false,
    ) {}

    /**
     * Questo livello soddisfa i requisiti del purpose?
     *
     * @param  Aal  $requiredAal  livello minimo richiesto
     * @param  bool  $requirePhishingResistant  se il purpose esige phishing-resistance
     */
    public function satisfies(Aal $requiredAal, bool $requirePhishingResistant = false): bool
    {
        if (! $this->aal->satisfies($requiredAal)) {
            return false;
        }

        if ($requirePhishingResistant && ! $this->phishingResistant) {
            return false;
        }

        return true;
    }
}
