<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Audit;

use Padosoft\Rebel\Core\Assurance\Aal;

/**
 * Un evento di sicurezza/auth da registrare nell'audit trail.
 *
 * Regole d'oro: MAI dati in chiaro che siano sensibili (l'identificatore è già
 * un HMAC, l'IP è un HMAC, NIENTE OTP/secret nei metadata). `type` è una stringa
 * libera (usa AuthEventType::value per i tipi comuni). `metadata` è JSON-safe.
 *
 *   new AuditEvent(
 *       type: AuthEventType::EmailOtpVerified->value,
 *       guard: 'customers',
 *       identifierHmac: $hash, keyVersion: 1,
 *       purpose: 'customer-login', aal: Aal::Aal1, amr: ['otp','email'],
 *   );
 */
final readonly class AuditEvent
{
    /**
     * @param  list<string>|null  $amr
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $type,
        public ?string $guard = null,
        public ?string $subjectType = null,
        public ?string $subjectId = null,
        public ?string $identifierHmac = null,
        public ?int $keyVersion = null,
        public ?string $ipHmac = null,
        public ?string $userAgentHash = null,
        public ?string $tenantId = null,
        public ?string $channel = null,
        public ?string $provider = null,
        public ?string $purpose = null,
        public ?Aal $aal = null,
        public ?array $amr = null,
        public ?int $riskScore = null,
        public array $metadata = [],
    ) {}
}
