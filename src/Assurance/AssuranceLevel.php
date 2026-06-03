<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Assurance;

/**
 * The assurance produced by an authentication/step-up driver, or required by a
 * "purpose" (protected action).
 *
 *  - aal:               the NIST level reached/required;
 *  - phishingResistant: true only for passkey/FIDO2;
 *  - amr:               Authentication Methods References, e.g. ['webauthn'] | ['otp','email'] | ['sms'];
 *  - restricted:        true for "restricted" authenticators (e.g. SMS) — see NIST.
 *
 * Example (passkey driver):
 *   new AssuranceLevel(Aal::Aal2, phishingResistant: true, amr: ['webauthn']);
 *
 * The step-up resolver uses satisfies() to REJECT drivers below the purpose threshold.
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
     * Does this level satisfy the purpose requirements?
     *
     * @param  Aal  $requiredAal  minimum required level
     * @param  bool  $requirePhishingResistant  whether the purpose demands phishing-resistance
     * @param  bool  $rejectRestricted  whether the purpose forbids "restricted" authenticators (e.g. SMS)
     */
    public function satisfies(
        Aal $requiredAal,
        bool $requirePhishingResistant = false,
        bool $rejectRestricted = false,
    ): bool {
        if (! $this->aal->satisfies($requiredAal)) {
            return false;
        }

        if ($requirePhishingResistant && ! $this->phishingResistant) {
            return false;
        }

        if ($rejectRestricted && $this->restricted) {
            return false;
        }

        return true;
    }
}
