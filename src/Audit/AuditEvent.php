<?php

declare(strict_types=1);

namespace Padosoft\Rebel\Core\Audit;

use Padosoft\Rebel\Core\Assurance\Aal;

/**
 * A security/auth event to record in the audit trail.
 *
 * Golden rules: NEVER any sensitive cleartext data (the identifier is already an
 * HMAC, the IP is an HMAC, NO OTP/secret in the metadata). `type` is a free-form
 * string (use AuthEventType::value for common types). `metadata` is JSON-safe.
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
        public ?string $country = null,
    ) {}

    /**
     * Return a copy with the (ISO 3166-1 alpha-2) country code set — used by the
     * context enricher to attach geo derived from a request header.
     */
    public function withCountry(?string $country): self
    {
        if ($country === $this->country) {
            return $this;
        }

        return new self(
            type: $this->type,
            guard: $this->guard,
            subjectType: $this->subjectType,
            subjectId: $this->subjectId,
            identifierHmac: $this->identifierHmac,
            keyVersion: $this->keyVersion,
            ipHmac: $this->ipHmac,
            userAgentHash: $this->userAgentHash,
            tenantId: $this->tenantId,
            channel: $this->channel,
            provider: $this->provider,
            purpose: $this->purpose,
            aal: $this->aal,
            amr: $this->amr,
            riskScore: $this->riskScore,
            metadata: $this->metadata,
            country: $country,
        );
    }
}
